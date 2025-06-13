<?php
/** @var int $id
 * @var dto\animals\UpdateAnimalRequest $dto
 * @var classes\ModelState $state
 * @var string|null $cover
 * @var string|null $video
 */

use enums\animals\Sex;

/* 1 місяць ≈ 2 592 000 с (60 × 60 × 24 × 30).                */
$monthsDelta = 0;
if (!empty($dto->updated_at)) {
    $monthsDelta = max(
        0,
        (int)floor((time() - strtotime($dto->updated_at)) / 2_592_000)
    );
}

$minTotal = $dto->age_min_months > 0 ? $dto->age_min_months + $monthsDelta : 0;
$maxTotal = $dto->age_max_months > 0 ? $dto->age_max_months + $monthsDelta : 0;

$minYears = $minTotal ? intdiv($minTotal, 12) : '';
$minMonths = $minTotal ? $minTotal % 12 : '';
$maxYears = $maxTotal ? intdiv($maxTotal, 12) : '';
$maxMonths = $maxTotal ? $maxTotal % 12 : '';

require_once dirname(__DIR__, 2) . '/app/helpers/forms.php';
?>
<div class="container mt-2">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="card-body p-4">
                    <h2 class="mb-4 text-center">Редагувати тваринку</h2>

                    <form method="post" enctype="multipart/form-data" novalidate>
                        <input type="hidden" name="id" value="<?= $id ?>">

                        <div class="mb-3">
                            <label class="form-label">Імʼя</label>
                            <input type="text"
                                   name="name"
                                   class="form-control<?= $state->first('name') ? ' is-invalid' : '' ?>"
                                   value="<?= htmlspecialchars($dto->name ?? "") ?>"
                                   required>
                            <?= form_error($state->all(), 'name') ?>
                        </div>

                        <div class="mb-4">
                            <label for="speciesSelect" class="form-label">Вид</label>
                            <select id="speciesSelect"
                                    name="species_id"
                                    data-api="/species"
                                    data-current="<?= (int)$dto->species_id ?>">
                            </select>
                            <?= form_error($state->all(), 'species_id') ?>
                        </div>

                        <div class="mb-4">
                            <label class="form-label">Особливості</label>
                            <select id="tagsSelect"
                                    name="tag_ids[]"
                                    multiple
                                    data-api="/tags"
                                    data-selected="<?= implode(', ', array_map('intval', $dto->tag_ids ?? [])) ?>">
                            </select>

                            <?= form_error($state->all(), 'tag_ids') ?>
                        </div>

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

                        <div class="mb-3">
                            <label class="form-label">Вік (роки / місяці)</label>

                            <div class="row g-2 align-items-center">
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

                        <div class="mb-3">
                            <label class="form-label">Опис</label>
                            <textarea name="description" rows="3"
                                      class="form-control"><?= htmlspecialchars($dto->description ?? "") ?></textarea>
                        </div>
                        <div class="form-check form-switch mb-4">
                            <input class="form-check-input"
                                   type="checkbox"
                                   role="switch"
                                   id="isAdoptedSwitch"
                                   name="is_adopted"
                                <?= $dto->is_adopted ? 'checked' : '' ?>>
                            <label class="form-check-label" for="isAdoptedSwitch">
                                Тваринка вже усиновлена
                            </label>
                        </div>
                        <?php if ($cover): ?>
                            <div class="mb-3">
                                <img src="<?= htmlspecialchars($cover) ?>" class="img-fluid rounded">
                            </div>
                        <?php endif; ?>

                        <?php if ($video): ?>
                            <div class="mb-3">
                                <video src="<?= htmlspecialchars($video) ?>"
                                       poster="<?= htmlspecialchars($cover) ?>"
                                       controls style="max-width:100%; max-height: 500px; border-radius:.5rem"></video>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label class="form-label">Нова обкладинка (JPEG/PNG ≤5 МБ)&nbsp;
                                <small class="text-muted">(залиште порожнім, щоб не змінювати)</small></label>
                            <input type="file" name="cover_image"
                                   class="form-control<?= $state->first('cover_image') ? ' is-invalid' : '' ?>"
                                   accept="image/jpeg,image/png">
                            <?= form_error($state->all(), 'cover_image') ?>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Нове відео (MP4/WEBM ≤16 МБ)&nbsp;
                                <small class="text-muted">(необов’язково)</small></label>
                            <input type="file" name="intro_video"
                                   class="form-control<?= $state->first('intro_video') ? ' is-invalid' : '' ?>"
                                   accept="video/mp4,video/webm">
                            <?= form_error($state->all(), 'intro_video') ?>
                        </div>

                        <button class="btn btn-primary w-100">Оновити дані</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="module">
    import initAgeRange from '/public/js/age-range-picker.js';

    initAgeRange('minYears', 'minMonths', 'maxYears', 'maxMonths', 'age_min_months', 'age_max_months');
</script>

<script type="module" src="/public/js/useCustomSelect.js"></script>