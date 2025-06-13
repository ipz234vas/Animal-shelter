<?php

namespace controllers;

use app\helpers\FileValidator;
use attributes\auth\Authorize;
use attributes\routing\Get;
use attributes\routing\Post;
use classes\Controller;
use dto\animals\CreateAnimalRequest;
use dto\listRequests\AnimalsListRequest;
use enums\database\SQLOperator;
use models\Animal;
use enums\auth\Permission;
use models\AnimalTag;
use models\Species;
use models\Tag;

#[Authorize(Permission::ManageAnimals)]
class AnimalsController extends Controller
{
    #[Get('index')]
    public function index(AnimalsListRequest $req): array
    {
        $req->sanitize();

        $base = Animal::asQuery()
            ->select(['animals.*'])
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

        if ($req->age_min !== null)
            $base->where('age_min_months', SQLOperator::GreaterEqual, $req->age_min);
        if ($req->age_max !== null)
            $base->where('age_max_months', SQLOperator::LessEqual, $req->age_max);

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

    #[Get('create')]
    public function create(): array
    {
        return $this->view([
            'state' => $this->modelState,
            'dto' => new CreateAnimalRequest(),
        ]);
    }

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
}