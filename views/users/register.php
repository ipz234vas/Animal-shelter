<?php
$this->Title = "Реєстрація";
$errs = $errors ?? [];
require_once dirname(__DIR__, 2) . '/app/helpers/forms.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="mb-4 text-center">Реєстрація</h2>
                    <form action="/users/register" method="post" novalidate>
                        <div class="mb-3">
                            <label for="full_name" class="form-label">ПІБ</label>
                            <input type="text"
                                   class="form-control<?= !empty($errs['full_name']) ? ' is-invalid' : '' ?>"
                                   id="full_name"
                                   name="full_name"
                                   required
                                   value="<?= htmlspecialchars($model->full_name ?? "") ?>">
                            <?= form_error($errs, 'full_name') ?>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Електронна пошта</label>
                            <input type="email"
                                   class="form-control<?= !empty($errs['email']) ? ' is-invalid' : '' ?>"
                                   id="email"
                                   name="email"
                                   required
                                   value="<?= htmlspecialchars($model->email ?? "") ?>">
                            <?= form_error($errs, 'email') ?>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Пароль</label>
                            <input type="password"
                                   class="form-control<?= !empty($errs['password']) ? ' is-invalid' : '' ?>"
                                   id="password"
                                   name="password"
                                   required>
                            <?= form_error($errs, 'password') ?>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Підтвердьте пароль</label>
                            <input type="password"
                                   class="form-control<?= !empty($errs['confirm_password']) ? ' is-invalid' : '' ?>"
                                   id="confirm_password"
                                   name="confirm_password"
                                   required>
                            <?= form_error($errs, 'confirm_password') ?>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Зареєструватися</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>