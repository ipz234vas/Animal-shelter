<?php

namespace controllers\api;

use classes\ApiController;
use dto\species\SpeciesCreateRequest;
use dto\species\SpeciesUpdateRequest;
use enums\database\SQLOperator;
use attributes\routing\{Get, Post, Put, Delete};
use models\Species;

class SpeciesApiController extends ApiController
{
    #[Get('index')]
    public function index(int $page = 1, int $per_page = 20): never
    {
        $base = Species::asQuery()->select();
        $total = (clone $base)->count();
        $rows = $base->limit($per_page)
            ->offset(($page - 1) * $per_page)
            ->fetch();

        $this->respondSuccess($rows, [
            'total' => $total, 'page' => $page, 'per_page' => $per_page
        ]);
    }

    #[Get('list')]
    public function list(?string $query = null): never
    {
        $q = Species::asQuery()->select();
        if ($query)
            $q->where('name', SQLOperator::Like, "%$query%");
        $this->respondSuccess($q->limit(20)->fetch());
    }

    #[Get('get')]
    public function get(int $id): never
    {
        $row = Species::getById($id);
        $row
            ? $this->respondSuccess($row)
            : $this->respondError('Не знайдено', 404, 'NOT_FOUND');
    }

    #[Post('create')]
    public function create(SpeciesCreateRequest $dto): never
    {
        if (Species::existsByName($dto->name))
            $this->modelState->add('name', 'Такий вид уже існує.');

        if (!$this->modelState->isValid())
            $this->respondError($this->modelState->all(), 422);

        $s = new Species();
        $s->name = trim($dto->name);
        $s->save();

        $this->respondSuccess($s, [], 201);     // 201 Created
    }

    #[Put('update')]
    public function update(SpeciesUpdateRequest $dto): never
    {
        $row = Species::getById($dto->id) ??
            $this->respondError('Не знайдено', 404, 'NOT_FOUND');

        if ($row['name'] !== $dto->name &&
            Species::existsByName($dto->name))
            $this->modelState->add('name', 'Такий вид уже існує.');

        if (!$this->modelState->isValid())
            $this->respondError($this->modelState->all(), 422);

        $sp = new Species();
        $sp->id = $dto->id;
        $sp->name = trim($dto->name);
        $sp->save();

        $this->respondSuccess($sp);
    }

    #[Delete('destroy')]
    public function delete(int $id): never
    {
        Species::deleteById($id)
            ? $this->respondSuccess(['id' => $id])
            : $this->respondError('Не знайдено', 404, 'NOT_FOUND');
    }
}
