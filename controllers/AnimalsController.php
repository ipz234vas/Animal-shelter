<?php

namespace controllers;

use app\helpers\FileValidator;
use attributes\auth\Authorize;
use attributes\routing\Get;
use attributes\routing\Post;
use classes\Controller;
use classes\ModelState;
use dto\animals\CreateAnimalRequest;
use models\Animal;
use enums\auth\Permission;
use models\AnimalTag;

#[Authorize(Permission::ManageAnimals)]
class AnimalsController extends Controller
{
    #[Get('create')]
    public function create(ModelState $state): array
    {
        return $this->view([
            'state' => $state,
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