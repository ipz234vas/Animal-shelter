<?php

use enums\animals\Sex;

/** @var array $animal */
/** @var dto\adoptions\CreateAdoptionRequest $dto */
/** @var classes\ModelState $state */

require_once dirname(__DIR__, 2) . '/app/helpers/forms.php';
?>

<div class="container mt-4" style="max-width: 820px">
    <div class="card shadow-sm">
        <div class="row g-0">

            <div class="col-md-5 text-center bg-light">
                <img src="<?= htmlspecialchars($animal['cover_image_url']) ?>"
                     alt="<?= htmlspecialchars($animal['name']) ?>"
                     class="img-fluid p-2 rounded"
                     style="object-fit:cover;max-height:380px;width:auto">
            </div>

            <div class="col-md-7">
                <div class="card-body h-100 d-flex flex-column">

                    <h4 class="card-title">
                        Заявка на&nbsp;«<?= htmlspecialchars($animal['name']) ?>»
                    </h4>
                    <p class="text-muted mb-3 small">
                        Стать: <?= ucfirst(Sex::tryFrom($animal['sex'] ?? '')->label() ?? '—') ?>
                    </p>

                    <form method="post" class="d-flex flex-column flex-grow-1">
                        <input type="hidden" name="animal_id" value="<?= $animal['id'] ?>">

                        <div class="mb-3 flex-grow-1">
                            <label class="form-label" for="comment">Коментар&nbsp;(необов&rsquo;язково)</label>
                            <textarea name="comment" id="comment" rows="6"
                                      class="form-control<?= $state->first('comment') ? ' is-invalid' : '' ?>"
                                      placeholder="Розкажіть чому саме ця тваринка&nbsp;— ваш вибір…"><?= htmlspecialchars($dto->comment ?? '') ?></textarea>
                            <?= form_error($state->all(), 'comment') ?>
                        </div>

                        <div class="d-grid gap-2 mt-auto">
                            <button class="btn btn-primary"
                                    type="submit"
                                    name="action"
                                    value="submit">
                                Надіслати заявку
                            </button>
                            <button class="btn btn-outline-secondary"
                                    type="submit"
                                    name="action"
                                    value="draft">
                                Зберегти як чернетку
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
</div>