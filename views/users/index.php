<?php
$this->Title = "Користувачі";
require_once dirname(__DIR__, 2) . '/app/helpers/forms.php';
?>

    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="h4 mb-0">Користувачі</h1>
            <a href="/users/create" class="btn btn-primary btn-sm">+ Додати</a>
        </div>

        <div class="card shadow-sm border-0 rounded-3">
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="bg-white border-bottom small text-muted">
                    <tr>
                        <th>П.І.Б.</th>
                        <th>Пошта</th>
                        <th class="text-center">Дозволи</th>
                        <th class="text-center">Дії</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr class="border-bottom">
                            <td class="py-3"><?= htmlspecialchars($user['full_name']) ?></td>
                            <td class="py-3"><?= htmlspecialchars($user['email']) ?></td>
                            <td class="text-center py-3">
                                <?php if (!empty($user['permissions'])): ?>
                                    <?php $permissions = \classes\PermissionParser::fromString($user['permissions']) ?>
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-light text-dark border rounded px-3 py-2 "
                                                type="button"
                                                id="permDropdown-<?= $user['id'] ?>"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                            <i class="bi bi-shield-lock"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end"
                                            aria-labelledby="permDropdown-<?= $user['id'] ?>">
                                            <?php foreach ($permissions as $perm): ?>
                                                <li class="dropdown-item disabled"><?= htmlspecialchars($perm->label()) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted small">Відсутні</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center py-3">
                                <div class="d-flex justify-content-center gap-2">
                                    <a href="/users/edit?id=<?= $user['id'] ?>"
                                       class="btn btn-sm btn-light text-dark border rounded px-2 py-2"
                                       title="Редагувати">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button"
                                            class="btn btn-sm btn-light text-dark border rounded px-2 py-2"
                                            title="Видалити"
                                            data-bs-toggle="modal"
                                            data-bs-target="#deleteConfirmationModal"
                                            data-id="<?= $user['id'] ?>"
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
    </div>

<?php require dirname(__DIR__, 2) . '/shared/delete_confirmation_modal.php'; ?>