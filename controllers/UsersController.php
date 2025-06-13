<?php

namespace controllers;

use attributes\auth\Authorize;
use attributes\routing\Get;
use attributes\routing\Post;
use classes\Controller;
use classes\database\builder\BaseBuilder;
use classes\exceptions\HttpException;
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

    /**
     * @throws HttpException
     */
    #[Get('edit')]
    public function edit(int $id, ?string $next = null): array
    {
        $user = User::getCurrentUser();
        if ($user["id"] === $id)
            throw new HttpException(403);
        $userRow = User::getById($id);
        if (!$userRow) {
            throw new HttpException(404);
        }

        return $this->view([
            'user' => (object)$userRow,
            'perms' => Permission::cases(),
            'next' => $next ?? $_SERVER['HTTP_REFERER'] ?? '/users',
        ]);
    }

    /**
     * @throws HttpException
     */
    #[Post('edit')]
    public function updatePermissions(int $id, ?array $permissions = [], ?string $next = null): array
    {
        $user = User::getCurrentUser();
        if ($user["id"] === $id)
            throw new HttpException(403);
        $rows = User::getById($id);
        if (!$rows) {
            throw new HttpException(404);
        }
        $permissions_str = implode(' ', $permissions);
        $user = new User();
        $user->id = $rows["id"];
        $user->permissions = $permissions_str;
        $user->save();

        $target = $next ?? "/users/";
        $this->redirect("users", "edit", [
            "next" => $target,
            "id" => $id
        ]);
    }

    #[Post('delete')]
    public function delete(string $next, $id): array
    {
        if (!str_starts_with($next, '/')) {
            $next = '/users';
        }
        User::deleteById($id);

        $nextUrl = $next ? base64_decode($next) : '/';
        $this->redirectToPath($nextUrl);
    }
}