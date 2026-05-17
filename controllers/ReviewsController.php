<?php

namespace controllers;

use attributes\auth\Authorize;
use dto\reviews\CreateReviewRequest;
use dto\reviews\UpdateStatusRequest;
use enums\applications\AdoptionStatus;
use enums\database\SQLOrderDirection;
use enums\reviews\ReviewStatus;
use attributes\routing\{Get, Post};
use classes\Controller;
use enums\auth\Permission;
use enums\database\SQLOperator;
use app\helpers\FileValidator;
use models\Adoption;
use models\Review;
use models\ReviewImage;

class ReviewsController extends Controller
{
    #[Authorize]
    #[Get('create')]
    public function create(int $application_id): array
    {
        $app = Adoption::getById($application_id);
        $uid = \models\User::getCurrentUser()['id'];

        if (!$app
            || $app['user_id'] != $uid
            || $app['status'] != AdoptionStatus::Accepted->value) {
            throw new \classes\exceptions\HttpException(404);
        }

        if (Review::asQuery()->select()
            ->where('application_id', SQLOperator::Equal, $application_id)
            ->first()) {
            $this->redirect('reviews');
        }

        $animal = \models\Animal::getById($app['animal_id']);
        $app['animal_name'] = $animal['name'] ?? '-';

        return $this->view([
            'dto' => new CreateReviewRequest(),
            'app' => $app,
            'state' => $this->modelState,
        ]);
    }

    #[Authorize]
    #[Post('create')]
    public function store(CreateReviewRequest $dto): array
    {
        $uid = \models\User::getCurrentUser()['id'];

        $app = Adoption::getById($dto->application_id);
        if (!$app || $app['user_id'] != $uid || $app['status'] != AdoptionStatus::Accepted->value)
            $this->modelState->add('application_id', 'Заявку не знайдено або стан не «Accepted».');

        if (!$dto->text) $this->modelState->add('text', 'Текст обовʼязковий.');

        $files = $_FILES['images'] ?? null;
        $paths = [];
        if ($files && is_array($files['name'])) {
            foreach ($files['name'] as $i => $n) {
                $tmp = [
                    'name' => $files['name'][$i],
                    'type' => $files['type'][$i],
                    'tmp_name' => $files['tmp_name'][$i],
                    'error' => $files['error'][$i],
                    'size' => $files['size'][$i],
                ];
                $name = FileValidator::saveImage($tmp, $this->modelState, "images");
                if ($name)
                    $paths[] = "/uploads/images/$name";
            }
        }

        if (!$this->modelState->isValid())
            return $this->view(['dto' => $dto, 'state' => $this->modelState, 'app' => $app]);

        $rev = new Review();
        $rev->application_id = $dto->application_id;
        $rev->text = trim($dto->text);
        $rev->status = ReviewStatus::Pending->value;
        $rid = $rev->save();

        foreach ($paths as $pos => $p) {
            $img = new ReviewImage();
            $img->review_id = $rid;
            $img->file_path = $p;
            $img->position = $pos;
            $img->save();
        }

        $this->redirect('reviews');
    }

    #[Get('public')]
    public function publicIndex(): array
    {
        $rows = Review::asQuery()
            ->select([
                'reviews.*',
                'animals.name        AS animal_name',
                'animals.id    AS animal_id',
                'users.full_name     AS user_name'
            ])
            ->join('adoption_applications',
                'adoption_applications.id = reviews.application_id',
                'LEFT')
            ->join('animals',
                'animals.id = adoption_applications.animal_id',
                'LEFT')
            ->join('users',
                'users.id = adoption_applications.user_id',
                'LEFT')
            ->where('reviews.status', SQLOperator::Equal,
                ReviewStatus::Accepted->value)
            ->orderBy('reviews.id', SQLOrderDirection::Descending)
            ->fetch();

        $ids = array_column($rows, 'id');
        $imgs = [];
        if ($ids) {
            $allImg = ReviewImage::asQuery()
                ->select()
                ->whereIn('review_id', $ids)
                ->orderBy('position')
                ->fetch();
            foreach ($allImg as $i) {
                $imgs[$i['review_id']][] = $i;
            }
        }

        foreach ($rows as &$r) {
            $r['images'] = $imgs[$r['id']] ?? [];
        }

        return $this->view(['reviews' => $rows]);
    }

    #[Authorize]
    #[Get('index')]
    public function myIndex(): array
    {
        $uid = \models\User::getCurrentUser()['id'];

        $rows = Review::asQuery()
            ->select([
                'reviews.*',
                'animals.name AS animal_name',
            ])
            ->join(
                'adoption_applications',
                'adoption_applications.id = reviews.application_id',
                'LEFT'
            )
            ->join(
                'animals',
                'animals.id = adoption_applications.animal_id',
                'LEFT'
            )
            ->where('adoption_applications.user_id', SQLOperator::Equal, $uid)
            ->orderBy('reviews.id', SQLOrderDirection::Descending)
            ->fetch();

        return $this->view(['reviews' => $rows]);
    }

    #[Authorize(Permission::ManageReviews)]
    #[Get('manage')]
    public function adminIndex(): array
    {
        $rows = Review::asQuery()
            ->select([
                'reviews.*',
                'animals.name  AS animal_name',
                'users.full_name AS user_name',
            ])
            ->join(
                'adoption_applications',
                'adoption_applications.id = reviews.application_id',
                'LEFT'
            )
            ->join(
                'animals',
                'animals.id = adoption_applications.animal_id',
                'LEFT'
            )
            ->join(
                'users',
                'users.id = adoption_applications.user_id',
                'LEFT'
            )
            ->orderBy('reviews.id', SQLOrderDirection::Descending)
            ->fetch();

        return $this->view(['reviews' => $rows]);
    }

    #[Authorize(Permission::ManageReviews)]
    #[Post('status')]
    public function adminSetStatus(UpdateStatusRequest $dto): never
    {
        Review::asQuery()
            ->update(['status' => $dto->status->value])
            ->where('id', SQLOperator::Equal, $dto->id)
            ->execute();

        $this->redirect('reviews', 'manage');
    }

    #[Authorize(Permission::ManageReviews)]
    #[Post('delete')]
    public function adminDelete(int $id): never
    {
        Review::deleteById($id);
        $this->redirect('reviews', 'manage');
    }
}
