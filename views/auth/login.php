<?php
$this->Title = "Login";
$errs = $errors ?? [];
require_once dirname(__DIR__, 2) . '/app/helpers/forms.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="mb-4 text-center">Вхід до системи</h2>
                    <form action="/auth/login" method="post" novalidate>
                        <input type="hidden" name="next" value="<?= htmlspecialchars($next ?? "") ?>"/>
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
                                   required
                                   value="">
                            <?= form_error($errs, 'password') ?>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Увійти</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>