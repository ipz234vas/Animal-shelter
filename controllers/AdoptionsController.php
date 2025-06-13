<?php

namespace controllers;

use dto\adoptions\CreateAdoptionRequest;
use dto\adoptions\UpdateStatusRequest;
use enums\applications\AdoptionStatus;
use enums\database\SQLOrderDirection;
use attributes\routing\{Get, Post};
use attributes\auth\Authorize;
use classes\Controller;
use enums\auth\Permission;
use enums\database\SQLOperator;
use models\{Adoption, Animal};

#[Authorize]
class AdoptionsController extends Controller
{
    #[Get('index')]
    public function myIndex(): array
    {
        $uid = \models\User::getCurrentUser()["id"];

        $rows = Adoption::asQuery()
            ->select(['adoption_applications.*', 'ani.name AS animal_name'])
            ->join('animals ani', 'ani.id = adoption_applications.animal_id', 'LEFT')
            ->where('adoption_applications.user_id', SQLOperator::Equal, $uid)
            ->orderBy('id', SQLOrderDirection::Descending)
            ->fetch();

        usort(
            $rows,
            static function (array $x, array $y) {
                $wx = $x['status'] == AdoptionStatus::Draft->value ? 0 : 1;
                $wy = $y['status'] == AdoptionStatus::Draft->value ? 0 : 1;

                return $wx <=> $wy ?: $y['id'] <=> $x['id'];
            }
        );

        return $this->view(['apps' => $rows]);
    }

    #[Get('create')]
    public function create(int $animal_id): array
    {
        $uid = \models\User::getCurrentUser()['id'];
        $animal = Animal::getById($animal_id);

        if (!$animal || $animal['is_adopted'])
            throw new \classes\exceptions\HttpException(404);

        $acceptedExists = Adoption::asQuery()
            ->select(['id'])
            ->where('animal_id', SQLOperator::Equal, $animal_id)
            ->where('status', SQLOperator::Equal, AdoptionStatus::Accepted->value)
            ->first();

        if ($acceptedExists) {
            $this->redirect('adoptions', 'index');
        }

        $exist = Adoption::asQuery()
            ->select()
            ->where('user_id', SQLOperator::Equal, $uid)
            ->where('animal_id', SQLOperator::Equal, $animal_id)
            ->first();

        if ($exist) {
            if ($exist['status'] == AdoptionStatus::Draft->value) {
                $this->redirectToPath("/adoptions/edit?id={$exist['id']}");
            }
            $this->redirect('adoptions', 'index');
        }

        return $this->view([
            'dto' => new CreateAdoptionRequest(),
            'animal' => $animal,
            'state' => $this->modelState
        ]);
    }

    #[Post('create')]
    public function store(CreateAdoptionRequest $dto): array
    {
        $uid = \models\User::getCurrentUser()["id"];

        $animal = Animal::getById($dto->animal_id);
        if (!$animal || $animal['is_adopted']) {
            $this->modelState->add('animal_id', 'Тваринку не знайдено або вже всиновлено');
        }

        if (!$this->modelState->isValid()) {
            return $this->view(['state' => $this->modelState, 'dto' => $dto]);
        }

        $ad = new Adoption();
        $ad->user_id = $uid;
        $ad->animal_id = $dto->animal_id;
        $ad->comment = trim((string)$dto->comment);
        $ad->status = $dto->toStatus()->value;
        $ad->save();

        $this->redirect('adoptions', 'index');
    }

    #[Post('submit')]
    public function submitDraft(int $id): never
    {
        $uid = \models\User::getCurrentUser()["id"];

        $row = Adoption::getById($id);
        if (!$row || $row['user_id'] != $uid || $row['status'] != AdoptionStatus::Draft->value) {
            throw new \classes\exceptions\HttpException(404);
        }

        Adoption::asQuery()
            ->update(['status' => AdoptionStatus::Pending->value])
            ->where('id', SQLOperator::Equal, $id)
            ->execute();

        $this->redirect('adoptions');
    }

    #[Post('delete')]
    public function deleteOwn(int $id): never
    {
        $uid = \models\User::getCurrentUser()["id"];

        $row = Adoption::getById($id);
        if (!$row || $row['user_id'] != $uid) {
            throw new \classes\exceptions\HttpException(404);
        }

        if ($row['status'] === AdoptionStatus::Accepted->value) {
            throw new \classes\exceptions\HttpException(403);
        }

        Adoption::deleteById($id);

        $this->redirect('adoptions');
    }


    #[Get('edit')]
    public function edit(int $id): array
    {
        $uid = \models\User::getCurrentUser()['id'];

        $row = Adoption::asQuery()
            ->select(['adoption_applications.*', 'ani.name AS animal_name'])
            ->join('animals ani', 'ani.id = adoption_applications.animal_id', 'LEFT')
            ->where('adoption_applications.id', SQLOperator::Equal, $id)
            ->first();

        if (!$row || $row['user_id'] != $uid || $row['status'] != AdoptionStatus::Draft->value) {
            throw new \classes\exceptions\HttpException(404);
        }

        $dto = new CreateAdoptionRequest();
        $dto->animal_id = $row['animal_id'];
        $dto->comment = $row['comment'];

        return $this->view([
            'draftId' => $id,
            'animal' => $row,
            'dto' => $dto,
            'state' => $this->modelState
        ]);
    }


    #[Post('update')]
    public function updateDraft(int $id, CreateAdoptionRequest $dto): array
    {
        $uid = \models\User::getCurrentUser()['id'];

        $row = Adoption::getById($id);
        if (!$row || $row['user_id'] != $uid || $row['status'] != AdoptionStatus::Draft->value) {
            throw new \classes\exceptions\HttpException(404);
        }

        $animal = Animal::getById($dto->animal_id);
        if (!$animal || $animal['is_adopted'])
            $this->modelState->add('animal_id', 'Тваринку не знайдено або вже всиновлено');

        if (!$this->modelState->isValid()) {
            return $this->view([
                'draftId' => $id,
                'animal' => $animal,
                'dto' => $dto,
                'state' => $this->modelState
            ]);
        }

        Adoption::asQuery()
            ->update([
                'comment' => trim((string)$dto->comment),
                'status' => $dto->toStatus()->value
            ])
            ->where('id', SQLOperator::Equal, $id)
            ->execute();

        $this->redirect('adoptions', 'index');
    }

    #[Authorize(Permission::ManageApplications)]
    #[Get('manage')]
    public function adminIndex(): array
    {
        $rows = Adoption::asQuery()
            ->select([
                'adoption_applications.*',
                'ani.name  AS animal_name',
                'u.full_name AS user_name'
            ])
            ->join('animals ani', 'ani.id = adoption_applications.animal_id', 'LEFT')
            ->join('users   u', 'u.id  = adoption_applications.user_id', 'LEFT')
            ->where('adoption_applications.status', SQLOperator::NotEqual, AdoptionStatus::Draft->value)
            ->orderBy('adoption_applications.id', \enums\database\SQLOrderDirection::Descending)
            ->fetch();

        return $this->view(['apps' => $rows]);
    }

    #[Authorize(Permission::ManageApplications)]
    #[Post('status')]
    public function adminSetStatus(UpdateStatusRequest $dto): never
    {
        if ($dto->status === AdoptionStatus::Draft)
            throw new \classes\exceptions\HttpException(403);

        Adoption::asQuery()
            ->update(['status' => $dto->status->value])
            ->where('id', SQLOperator::Equal, $dto->id)
            ->execute();
        if ($dto->status === AdoptionStatus::Accepted) {
            $animal_id = Adoption::getById($dto->id)["animal_id"];

            Adoption::asQuery()
                ->update(['status' => AdoptionStatus::Rejected->value])
                ->where('animal_id', SQLOperator::Equal, $animal_id)
                ->where('status', SQLOperator::Equal, AdoptionStatus::Pending->value)
                ->execute();
        }

        $this->redirect('adoptions', 'manage');
    }

    #[Authorize(Permission::ManageApplications)]
    #[Post('admin/delete')]
    public function adminDelete(int $id): never
    {
        Adoption::deleteById($id);

        $this->redirect('adoptions', 'manage');
    }
}
