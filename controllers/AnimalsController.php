<?php

namespace controllers;

use app\helpers\FileValidator;
use attributes\auth\Authorize;
use attributes\routing\Get;
use attributes\routing\Post;
use classes\Controller;
use dto\animals\CreateAnimalRequest;
use dto\listRequests\AnimalsListRequest;
use enums\auth\Permission;
use enums\database\SQLOperator;
use models\Animal;
use models\AnimalTag;
use models\Species;
use models\Tag;

class AnimalsController extends Controller
{
    #[Get('index')]
    public function index(AnimalsListRequest $req): array
    {
        $req->sanitize();

        $base = Animal::asQuery()
            ->select(['animals.*'])
            ->where("animals.is_adopted", SQLOperator::Equal, false)
            ->where("animals.is_deleted", SQLOperator::Equal, false)
            ->join('species', 'species.id = animals.species_id', "LEFT");

        if ($req->query) {
            $q = trim($req->query);
            $base->andWhereGroup(fn($b) => $b
                ->where('animals.name', SQLOperator::Like, "%$q%")
                ->orWhere('animals.id', SQLOperator::Equal, $q)
            );
        }

        if ($id = $req->speciesId()) {
            $base->where('species_id', SQLOperator::Equal, $id);
        }

        if ($sex = $req->sexEnum()) {
            $base->where('sex', SQLOperator::Equal, $sex->value);
        }

        if ($req->adopted !== null) {
            $base->where('is_adopted', SQLOperator::Equal, $req->adopted);
        }

        $amin = $req->age_min ?: null;
        $amax = $req->age_max ?: null;

        if ($amin !== null) {
            $base->andWhereGroup(fn($g) => $g
                ->andWhereGroup(fn($b) => $b
                    ->where('age_min_months', SQLOperator::NotEqual, 0)
                    ->where('age_min_months', SQLOperator::GreaterEqual, $amin)
                )
                ->orWhereGroup(fn($b) => $b
                    ->where('age_max_months', SQLOperator::NotEqual, 0)
                    ->where('age_max_months', SQLOperator::GreaterEqual, $amin)
                )
            );
        }

        if ($amax !== null) {
            $base->andWhereGroup(fn($g) => $g
                ->andWhereGroup(fn($b) => $b
                    ->where('age_min_months', SQLOperator::Equal, 0)
                    ->orWhere('age_min_months', SQLOperator::LessEqual, $amax)
                )
                ->andWhereGroup(fn($b) => $b
                    ->where('age_max_months', SQLOperator::Equal, 0)
                    ->orWhere('age_max_months', SQLOperator::LessEqual, $amax)
                )
            );
        }

        if ($req->tag_ids) {
            $base->join('animal_tags', 'animal_tags.animal_id = animals.id', 'INNER')
                ->whereIn('animal_tags.tag_id', $req->tag_ids)
                ->groupBy('animals.id');
        }

        $total = (clone $base)->count();
        $offset = ($req->page - 1) * $req->perPage;

        $rows = (clone $base)
            ->orderBy($req->sortBy, $req->convertedDirection)
            ->limit($req->perPage)
            ->offset($offset)
            ->fetch();

        return $this->view([
            'animals' => new \dto\pagination\PaginatedResult(
                $rows, $total, $req->perPage, $offset),
            'request' => $req,
            'species' => Species::getAll(['id', 'name']),
            'tags' => Tag::getAll(['id', 'name']),
            'sexes' => \enums\animals\Sex::cases(),
        ]);
    }

    #[Get('show')]
    public function show(int $id): array
    {
        /* ---- 1. тваринка + назва виду ----------------------------------- */
        $row = Animal::asQuery()
            ->select([
                'animals.*',
                's.name   AS species_name'     // ← одразу дістаємо назву виду
            ])
            ->join('species s', 's.id = animals.species_id', "LEFT")
            ->where('animals.id', \enums\database\SQLOperator::Equal, $id)
            ->first();

        if (!$row || $row['is_deleted']) {
            throw new \classes\exceptions\HttpException(404);
        }

        $tags = AnimalTag::asQuery()
            ->select(['t.id', 't.name'])
            ->join('tags t', 't.id = animal_tags.tag_id')
            ->where('animal_tags.animal_id', \enums\database\SQLOperator::Equal, $id)
            ->fetch();

        $row['tags'] = $tags;

        return $this->view(['a' => $row]);
    }

    #[Authorize(Permission::ManageAnimals)]
    #[Get('create')]
    public function create(): array
    {
        return $this->view([
            'state' => $this->modelState,
            'dto' => new CreateAnimalRequest(),
        ]);
    }

    #[Authorize(Permission::ManageAnimals)]
    #[Post('create')]
    public function store(CreateAnimalRequest $dto): array
    {
        $imgName = FileValidator::saveImage($_FILES['cover_image'] ?? null, $this->modelState);
        if (!$imgName) {
            $this->modelState->add('cover_image', 'Зображення обовʼязкове.');
        }

        $vidName = FileValidator::saveVideo($_FILES['intro_video'] ?? null, $this->modelState);

        $min = $dto->age_min_months;
        $max = $dto->age_max_months;

        if ($min !== null && $max !== null && $max < $min) {
            $this->modelState->add(
                'age_max_months',
                'Максимальний вік не може бути меншим за мінімальний.'
            );
        }

        foreach ($dto->tag_ids as $tid) {
            if (!ctype_digit((string)$tid)) {
                $this->modelState->add('tag_ids', "Недійсний ID тегу: $tid");
            }
        }

        if (!$this->modelState->isValid()) {
            return $this->view([
                'state' => $this->modelState,
                'dto' => $dto,
            ]);
        }
        $db = \classes\Core::getInstance()->db;
        $db->pdo->beginTransaction();
        try {
            $animal = new Animal();
            $animal->name = $dto->name;
            $animal->species_id = $dto->species_id;
            $animal->sex = $dto->sex->value;
            $animal->age_min_months = $dto->age_min_months;
            $animal->age_max_months = $dto->age_max_months;
            $animal->description = $dto->description;
            $animal->cover_image_url = "/uploads/images/$imgName";
            $animal->intro_video_url = $vidName ? "/uploads/videos/$vidName" : null;
            $id = $animal->save();

            foreach ($dto->tag_ids as $tid) {
                AnimalTag::attach($id, $tid);
            }

            $db->pdo->commit();
        } catch (\Throwable $e) {
            $db->pdo->rollBack();
            throw $e;
        }

        $this->redirect('animals', 'index');
    }

    #[Authorize(Permission::ManageAnimals)]
    #[Post('archive')]
    public function archive(int $id, ?string $next = null): never
    {
        $row = Animal::getById($id);
        if (!$row || $row['is_deleted']) {
            throw new \classes\exceptions\HttpException(404);
        }

        Animal::asQuery()
            ->update([
                'is_deleted' => true
            ])
            ->where('id', SQLOperator::Equal, $id)
            ->execute();

        $back = $next ? base64_decode($next) : '/animals';
        $this->redirectToPath($back);
    }
}