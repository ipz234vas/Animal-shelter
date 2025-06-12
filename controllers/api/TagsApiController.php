<?php

namespace controllers\api;

use attributes\routing\Delete;
use attributes\routing\Get;
use attributes\routing\Post;
use attributes\routing\Put;
use classes\ApiController;
use dto\tags\TagCreateRequest;
use dto\tags\TagUpdateRequest;
use enums\database\SQLOperator;
use models\Tag;

class TagsApiController extends ApiController
{
    #[Get('index')]
    public function index(int $page = 1, int $per_page = 20): never
    {
        $base = Tag::asQuery()->select();
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
        $q = Tag::asQuery()->select();
        if ($query)
            $q->where('name', SQLOperator::Like, "%$query%");
        $this->respondSuccess($q->orderBy("name")->limit(20)->fetch());
    }

    #[Get('get')]
    public function get(int $id): never
    {
        $row = Tag::getById($id);
        $row
            ? $this->respondSuccess($row)
            : $this->respondError('Не знайдено', 404, 'NOT_FOUND');
    }

    #[Post('create')]
    public function create(TagCreateRequest $dto): never
    {
        if (Tag::existsByName($dto->name))
            $this->modelState->add('name', 'Такий тег уже існує.');

        if (!$this->modelState->isValid())
            $this->respondError($this->modelState->all(), 422);

        $tag = new Tag();
        $tag->name = trim($dto->name);
        $id = $tag->save();

        $this->respondSuccess($id, [], 201);
    }

    #[Put('update')]
    public function update(TagUpdateRequest $dto): never
    {
        $row = Tag::getById($dto->id) ??
            $this->respondError('Не знайдено', 404, 'NOT_FOUND');

        if ($row['name'] !== $dto->name &&
            Tag::existsByName($dto->name))
            $this->modelState->add('name', 'Такий тег уже існує.');

        if (!$this->modelState->isValid())
            $this->respondError($this->modelState->all(), 422);

        $tag = new Tag();
        $tag->id = $dto->id;
        $tag->name = trim($dto->name);
        $tag->save();

        $this->respondSuccess($tag);
    }

    #[Delete('destroy')]
    public function delete(int $id): never
    {
        Tag::deleteById($id)
            ? $this->respondSuccess(['id' => $id])
            : $this->respondError('Не знайдено', 404, 'NOT_FOUND');
    }
}
