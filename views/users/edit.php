<?php
/* @var array|object $user */

/* @var Permission[] $perms */

/* @var array $errors */

/* @var string $next */

use enums\auth\Permission;

$errs = $errors ?? [];
$nextUrl = $next ? base64_decode($next) : '/';
$this->Title = 'Редагувати користувача';

require_once dirname(__DIR__, 2) . '/app/helpers/forms.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="mb-4 text-center">Редагування</h2>

                    <form method="POST" novalidate>
                        <div class="mb-3">
                            <label class="form-label">ПІБ</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($user->full_name) ?>"
                                   readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" value="<?= htmlspecialchars($user->email) ?>"
                                   readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label d-block">Дозволи</label>
                            <?php $assigned = explode(' ', (string)($user->permissions ?? '')); ?>
                            <?php foreach ($perms as $perm): ?>
                                <label class="me-3 small">
                                    <input type="checkbox" name="permissions[]" value="<?= $perm->value ?>"
                                        <?= in_array($perm->value, $assigned, true) ? 'checked' : '' ?>>
                                    <?= htmlspecialchars($perm->label()) ?>
                                </label>
                            <?php endforeach; ?>
                            <?= form_error($errs, 'permissions') ?>
                        </div>

                        <button class="btn btn-primary w-100">Зберегти дозволи</button>
                    </form>

                    <div class="mt-4 text-center small">
                        <a href="<?= htmlspecialchars($nextUrl) ?>" class="text-decoration-none">← повернутись</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
