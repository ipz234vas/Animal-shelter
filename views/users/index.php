<?php
/* @var UsersListRequest $request */
/* @var Permission[] $perms */

/* @var PaginatedResult $users */

use dto\listRequests\UsersListRequest;
use dto\pagination\PaginatedResult;
use enums\auth\Permission;

$this->Title = "Користувачі";

$next = base64_encode($_SERVER['REQUEST_URI'] ?? '/');

require_once dirname(__DIR__, 2) . '/app/helpers/forms.php';
require_once dirname(__DIR__, 2) . '/app/helpers/pagination.php';
require_once dirname(__DIR__, 2) . '/app/helpers/sort_link.php';
require_once dirname(__DIR__, 2) . '/app/helpers/permission_dropdown_renderer.php';
?>

    <style>
        .table-fixed {
            table-layout: fixed;
        }

        .table-users td, .table-users th {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .table-users th:nth-child(1), .table-users td:nth-child(1) {
            width: 40%;
        }

        .table-users th:nth-child(2), .table-users td:nth-child(2) {
            width: 30%;
        }
    </style>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">Користувачі</h1>
        </div>

        <form method="GET" class="card shadow-sm border-0 rounded-3 p-3 mb-3">
            <div class="row gy-2">
                <div class="col-xl-4 d-flex gap-2">
                    <input type="text" name="query" value="<?= htmlspecialchars($request->query ?? '') ?>"
                           class="form-control" placeholder="Пошук…">

                    <select name="perPage" class="form-select w-auto">
                        <?php foreach (dto\listRequests\BaseListRequest::PER_PAGE_CHOICES as $size): ?>
                            <option value="<?= $size ?>" <?= $request->perPage == $size ? 'selected' : '' ?>>
                                <?= $size ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-xl-6">
                    <?php foreach ($perms as $perm): ?>
                        <label class="me-2 small">
                            <input type="checkbox" name="permissions[]" value="<?= $perm->value ?>"
                                <?= in_array($perm->value, (array)$request->permissions, true) ? 'checked' : '' ?>>
                            <?= htmlspecialchars($perm->label()) ?>
                        </label>
                    <?php endforeach; ?>
                </div>

                <div class="col-xl-2 text-end d-flex align-items-center justify-content-end">
                    <button class="btn btn-outline-primary btn-sm">Застосувати</button>
                </div>
            </div>
        </form>

        <div class="card shadow-sm border-0 rounded-3">
            <div class="table-responsive">
                <table class="table table-fixed table-users align-middle mb-0">
                    <thead class="bg-white border-bottom small text-muted">
                    <tr>
                        <th><?= sort_link('П.І.Б.', 'full_name', $request) ?></th>
                        <th><?= sort_link('Пошта', 'email', $request) ?></th>
                        <th class="text-center" style="width:15%">Дозволи</th>
                        <th class="text-center" style="width:15%">Дії</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr class="border-bottom">
                            <td class="py-3 text-truncate"><?= htmlspecialchars($user['full_name']) ?></td>
                            <td class="py-3 text-truncate"><?= htmlspecialchars($user['email']) ?></td>
                            <td class="text-center py-3">
                                <?= render_permission_dropdown($user['permissions'] ?? '', (int)$user['id']) ?>
                            </td>
                            <td class="text-center py-3">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="/users/edit?id=<?= $user['id'] ?>&next=<?= $next ?>"
                                       class="btn btn-sm btn-light text-dark border rounded px-2 py-2"
                                       title="Редагувати"><i class="bi bi-pencil"></i></a>

                                    <button type="button"
                                            class="btn btn-sm btn-light text-dark border rounded px-2 py-2"
                                            title="Видалити"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteConfirmationModal"
                                            data-id="<?= $user['id'] ?>"
                                            data-next="<?= $next ?>"
                                            data-name="<?= htmlspecialchars($user['full_name']) ?>"
                                            data-action="/users/delete">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-3">
            <?= paginationLinks($users) ?>
        </div>
    </div>

<?php require dirname(__DIR__, 2) . '/shared/delete_confirmation_modal.php'; ?>