<?php

namespace controllers\api;

use attributes\auth\Authorize;
use attributes\routing\Get;
use attributes\routing\Post;
use classes\ApiController;
use enums\auth\Permission;

class TestApiController extends ApiController
{
    #[Get('index')]
    public function index($number = 10): never
    {
        $this->json(["status" => "error", "number" => $number]);
    }

    #[Authorize(Permission::ManageAnimals)]
    #[POST('index')]
    public function indexPost($number = 5): never
    {
        $this->json(["status" => "success", "number" => $number]);
    }
}