<?php

namespace controllers;

use attributes\auth\Authorize;
use attributes\routing\Get;
use classes\Controller;
use classes\database\builder\BaseBuilder;
use dto\listRequests\UsersListRequest;
use dto\pagination\PaginatedResult;
use enums\auth\Permission;
use enums\database\SQLOperator;
use models\User;

#[Authorize(Permission::ManageUsers)]
class UsersController extends Controller
{
    #[Get('index')]
    public function index(UsersListRequest $req): array
    {
        $req->sanitize();

        $base = User::asQuery()->select();

        if ($req->query) {
            $base->orWhereGroup(fn($b) => $b
                ->where('full_name', SQLOperator::Like, "%{$req->query}%")
                ->orWhere('email', SQLOperator::Like, "%{$req->query}%"));
        }

        if ($req->permissions) {
            $base->andWhereGroup(function (BaseBuilder $builder) use ($req) {
                foreach ($req->permissions as $permission) {
                    $builder->where('permissions', SQLOperator::Like, "%{$permission}%");
                }
            });
        }

        $total = (clone $base)->count();

        $perPage = $req->perPage;
        $offset = ($req->page - 1) * $perPage;

        $rows = (clone $base)
            ->orderBy($req->sortBy, $req->convertedDirection)
            ->limit($perPage)
            ->offset($offset)
            ->fetch();

        $result = new PaginatedResult($rows, $total, $perPage, $offset);

        return $this->view([
            'users' => $result,
            'request' => $req,
            'perms' => Permission::cases(),
        ]);
    }
}