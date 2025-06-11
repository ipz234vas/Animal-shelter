<?php
$this->Title = "Мій профіль";
$errs = $errors ?? [];
require_once dirname(__DIR__, 2) . '/app/helpers/forms.php';
?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="mb-4 text-center">Профіль</h2>
                    <form method="post" novalidate>
                        <div class="mb-3">
                            <label for="full_name" class="form-label">ПІБ</label>
                            <input type="text"
                                   class="form-control<?= !empty($errs['full_name']) ? ' is-invalid' : '' ?>"
                                   id="full_name"
                                   name="full_name"
                                   value="<?= htmlspecialchars($model->full_name ?? '') ?>"
                                   required>
                            <?= form_error($errs, 'full_name') ?>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email"
                                   class="form-control"
                                   id="email"
                                   name="email"
                                   value="<?= htmlspecialchars($model->email ?? '') ?>"
                                   readonly style="background: #f9f9f9;">
                        </div>

                        <button type="submit" class="btn btn-primary w-100">Зберегти зміни</button>
                    </form>
                    <div class="mt-5">
                        <a href="/account/password" class="btn btn-outline-secondary w-100 mb-2">Змінити пароль</a>
                        <a href="/account/delete" class="btn btn-outline-danger w-100">Видалити акаунт</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>