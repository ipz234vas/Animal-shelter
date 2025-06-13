<?php
/** @var dto\animals\CreateAnimalRequest $dto */
/** @var array $animal */           /* name + id  */
/** @var int $draftId */         /* ідентифікатор чернетки */
/** @var classes\ModelState $state */

require_once dirname(__DIR__, 2) . '/app/helpers/forms.php';
?>
<div class="container mt-2" style="max-width:680px">
    <h3 class="text-center mb-3">
        Редагування чернетки заявки<br>
        <small class="text-muted">(тваринка: <?= htmlspecialchars($animal['animal_name']) ?>)</small>
    </h3>

    <form method="post" action="/adoptions/update?id=<?= $draftId ?>" novalidate>
        <input type="hidden" name="animal_id" value="<?= $dto->animal_id ?>">

        <div class="mb-3">
            <label class="form-label">Коментар</label>
            <textarea name="comment" rows="4"
                      class="form-control<?= $state->first('comment') ? ' is-invalid' : '' ?>"
            ><?= htmlspecialchars($dto->comment ?? '') ?></textarea>
            <?= form_error($state->all(), 'comment') ?>
        </div>

        <div class="d-flex gap-2">
            <button name="action" value="draft" class="btn btn-outline-secondary flex-fill">
                Зберегти чернетку
            </button>

            <button name="action" value="submit" class="btn btn-primary flex-fill">
                Надіслати
            </button>
        </div>
    </form>
</div>
