<?php
/**
 * @var dto\reviews\CreateReviewRequest $dto
 * @var classes\ModelState $state
 * @var array $app
 */
require_once dirname(__DIR__, 2) . '/app/helpers/forms.php';
?>
<h3 class="mb-3">Відгук про <?= htmlspecialchars($app['animal_name']) ?></h3>

<form method="post" enctype="multipart/form-data" class="mb-5">
    <input type="hidden" name="application_id" value="<?= $app['id'] ?>">

    <div class="mb-3">
        <label class="form-label">Текст відгуку</label>
        <textarea name="text" rows="4"
                  class="form-control<?= $state->first('text') ? ' is-invalid' : '' ?>"
                  required><?= htmlspecialchars($dto->text ?? '') ?></textarea>
        <?= form_error($state->all(), 'text') ?>
    </div>

    <div class="mb-3">
        <label class="form-label">Фото (до 5&nbsp;шт, JPEG/PNG ≤2 МБ)</label>
        <input type="file" name="images[]" multiple accept="image/jpeg,image/png" class="form-control">
        <?= form_error($state->all(), 'images') ?>
    </div>

    <button class="btn btn-primary">Надіслати на модерацію</button>
</form>
