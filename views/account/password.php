<?php
$this->Title = "Зміна пароля";
$errs = $errors ?? [];
require_once dirname(__DIR__, 2) . '/app/helpers/forms.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="mb-4 text-center">Змінити пароль</h2>
                    <form method="post" novalidate>
                        <div class="mb-3">
                            <label for="old_password" class="form-label">Старий пароль</label>
                            <input type="password"
                                   class="form-control<?= !empty($errs['old_password']) ? ' is-invalid' : '' ?>"
                                   id="old_password"
                                   name="old_password"
                                   required>
                            <?= form_error($errs, 'old_password') ?>
                        </div>

                        <div class="mb-3">
                            <label for="new_password" class="form-label">Новий пароль</label>
                            <input type="password"
                                   class="form-control<?= !empty($errs['new_password']) ? ' is-invalid' : '' ?>"
                                   id="new_password"
                                   name="new_password"
                                   required>
                            <?= form_error($errs, 'new_password') ?>
                        </div>

                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Підтвердження нового пароля</label>
                            <input type="password"
                                   class="form-control<?= !empty($errs['confirm_password']) ? ' is-invalid' : '' ?>"
                                   id="confirm_password"
                                   name="confirm_password"
                                   required>
                            <?= form_error($errs, 'confirm_password') ?>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Змінити пароль</button>
                    </form>
                    <a href="/account/profile" class="btn btn-outline-secondary w-100 mt-2">Повернутись до профілю</a>
                </div>
            </div>
        </div>
    </div>
</div>
