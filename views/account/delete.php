<?php
$this->Title = "Видалення акаунта";
$errs = $errors ?? [];
require_once dirname(__DIR__, 2) . '/app/helpers/forms.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-danger shadow-sm">
                <div class="card-body p-4">
                    <h2 class="mb-4 text-center text-danger">Видалення акаунта</h2>
                    <p class="text-muted text-center">
                        Цю дію неможливо скасувати. <br> Щоб підтвердити — введіть свій пароль:
                    </p>
                    <form method="post" novalidate>
                        <div class="mb-3">
                            <label for="password" class="form-label">Пароль</label>
                            <input type="password"
                                   class="form-control<?= !empty($errs['password']) ? ' is-invalid' : '' ?>"
                                   id="password"
                                   name="password"
                                   required>
                            <?= form_error($errs, 'password') ?>
                        </div>

                        <button type="submit" class="btn btn-danger w-100">Видалити акаунт</button>
                    </form>
                    <a href="/account/profile" class="btn btn-outline-secondary w-100 mt-2">Повернутись до профілю</a>
                </div>
            </div>
        </div>
    </div>
</div>
