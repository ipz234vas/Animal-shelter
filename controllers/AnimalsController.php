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

#[Authorize(Permission::ManageAnimals)]
class AnimalsController extends Controller
{
    #[Get('create')]
    public function create(ModelState $state): array
    {
        return $this->view([
            'species' => [],
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

        if (!$this->modelState->isValid()) {
            return $this->view([
                'species' => [],
                'state' => $this->modelState,
                'dto' => $dto,
            ]);
        }

        $a = new Animal();
        $a->name = $dto->name;
        $a->species_id = $dto->species_id;
        $a->sex = $dto->sex->value;
        $a->age_min_months = $dto->age_min_months;
        $a->age_max_months = $dto->age_max_months;
        $a->description = $dto->description;
        $a->cover_image_url = "/uploads/images/$imgName";
        $a->intro_video_url = $vidName ? "/uploads/videos/$vidName" : null;
        $a->save();

        $this->redirect('animals', 'index');
    }
}