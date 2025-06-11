<?php

use enums\animals\Sex;

/**
 * @var dto\animals\CreateAnimalRequest $dto
 * @var classes\ModelState $state
 * @var array $species
 */

$minTotal = $dto->age_min_months;
$maxTotal = $dto->age_max_months;

$minYears = $minTotal !== null ? intdiv($minTotal, 12) : '';
$minMonths = $minTotal !== null ? $minTotal % 12 : '';
$maxYears = $maxTotal !== null ? intdiv($maxTotal, 12) : '';
$maxMonths = $maxTotal !== null ? $maxTotal % 12 : '';

require_once dirname(__DIR__, 2) . '/app/helpers/forms.php';
?>
<div class="container mt-2">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="mb-4 text-center">Нова тваринка</h2>

                    <form method="post" enctype="multipart/form-data" novalidate>
                        <!-- Імʼя -->
                        <div class="mb-3">
                            <label class="form-label">Імʼя</label>
                            <input type="text"
                                   name="name"
                                   class="form-control<?= $state->first('name') ? ' is-invalid' : '' ?>"
                                   value="<?= htmlspecialchars($dto->name ?? "") ?>"
                                   required>
                            <?= form_error($state->all(), 'name') ?>
                        </div>

                        <!-- Вид -->
                        <div class="mb-3">
                            <label class="form-label">Вид</label>
                            <select name="species_id"
                                    class="form-select<?= $state->first('species_id') ? ' is-invalid' : '' ?>"
                                    required>
                                <option value="1">— обрати —</option>
                                <?php foreach ($species as $s): ?>
                                    <option value="<?= $s['id'] ?>"
                                        <?= $dto->species_id == $s['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($s['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <?= form_error($state->all(), 'species_id') ?>
                        </div>

                        <!-- ======= 1. Стать як кнопки ======= -->
                        <div class="mb-3">
                            <label class="form-label d-block">Стать</label>
                            <div class="btn-group" role="group" aria-label="Стать">
                                <?php foreach (Sex::cases() as $sex): ?>
                                    <input type="radio"
                                           class="btn-check"
                                           name="sex"
                                           id="sex-<?= $sex->value ?>"
                                           value="<?= $sex->value ?>"
                                        <?= ($dto->sex->value ?? Sex::Unknown->value) === $sex->value ? 'checked' : '' ?>>
                                    <label class="btn btn-outline-secondary" for="sex-<?= $sex->value ?>">
                                        <?= $sex->label() ?>
                                    </label>
                                <?php endforeach; ?>
                            </div>
                            <?= form_error($state->all(), 'sex') ?>
                        </div>


                        <!-- ======= 2. Вік (одна секція) ======= -->
                        <div class="mb-3">
                            <label class="form-label">Вік (роки / місяці)</label>

                            <div class="row g-2 align-items-center">
                                <!-- Від -->
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text">від</span>
                                        <input type="number" min="0" max="50" id="minYears"
                                               class="form-control" placeholder="0" value="<?= $minYears ?>">
                                        <span class="input-group-text">р</span>
                                        <input type="number" min="0" max="11" id="minMonths"
                                               class="form-control" placeholder="0" value="<?= $minMonths ?>">
                                        <span class="input-group-text">м</span>
                                    </div>
                                </div>

                                <!-- До -->
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <span class="input-group-text">до</span>
                                        <input type="number" min="0" max="50" id="maxYears"
                                               class="form-control"
                                               placeholder="0"
                                               value="<?= $maxYears ?>">
                                        <span class="input-group-text">р</span>
                                        <input type="number" min="0" max="11" id="maxMonths"
                                               class="form-control"
                                               placeholder="0"
                                               value="<?= $maxMonths ?>">
                                        <span class="input-group-text">м</span>
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="age_min_months" id="age_min_months">
                            <input type="hidden" name="age_max_months" id="age_max_months">
                            <div>
                                <?= form_error($state->all(), 'age_min_months') ?>
                                <?= form_error($state->all(), 'age_max_months') ?>
                            </div>
                        </div>

                        <!-- Опис -->
                        <div class="mb-3">
                            <label class="form-label">Опис</label>
                            <textarea name="description" rows="3"
                                      class="form-control"><?= htmlspecialchars($dto->description ?? "") ?></textarea>
                        </div>

                        <!-- Файли -->
                        <div class="mb-3">
                            <label class="form-label">Обкладинка (JPEG/PNG ≤ 5 МБ)</label>
                            <input type="file"
                                   name="cover_image"
                                   class="form-control<?= $state->first('cover_image') ? ' is-invalid' : '' ?>"
                                   accept="image/jpeg,image/png" required>
                            <?= form_error($state->all(), 'cover_image') ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Відео (MP4/WEBM ≤ 16 МБ)</label>
                            <input type="file"
                                   name="intro_video"
                                   class="form-control<?= $state->first('intro_video') ? ' is-invalid' : '' ?>"
                                   accept="video/mp4,video/webm">
                            <?= form_error($state->all(), 'intro_video') ?>
                        </div>

                        <button class="btn btn-primary w-100">Зберегти</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function toMonths(y, m) {
        return (parseInt(y, 10) || 0) * 12 + (parseInt(m, 10) || 0);
    }

    function syncAge() {
        document.getElementById('age_min_months').value =
            toMonths(document.getElementById('minYears').value,
                document.getElementById('minMonths').value);

        document.getElementById('age_max_months').value =
            toMonths(document.getElementById('maxYears').value,
                document.getElementById('maxMonths').value);
    }

    ['minYears', 'minMonths', 'maxYears', 'maxMonths'].forEach(id =>
        document.getElementById(id).addEventListener('input', syncAge)
    );

    window.addEventListener('DOMContentLoaded', () => syncAge());
</script>