<?php
/**
 * @var dto\pagination\PaginatedResult $animals
 * @var AnimalsListRequest $request
 * @var array $species | $tags
 */

use dto\listRequests\AnimalsListRequest;
use enums\animals\Sex;

require_once 'app/helpers/animals.php';
require_once 'app/helpers/forms.php';
require_once 'app/helpers/pagination.php';
?>

<div class="container-fluid mt-3">
    <div class="row">
        <aside class="col-md-4 col-lg-3 mb-3">
            <form class="bg-light p-3 rounded shadow-sm">
                <input name="query" class="form-control mb-2"
                       placeholder="Кличка / ID"
                       value="<?= htmlspecialchars($request->query ?? '') ?>">

                <div class="mb-2">
                    <label for="perPage" class="form-label small mb-1">Показувати по</label>
                    <select id="perPage" name="perPage" class="form-select">
                        <?php foreach (dto\listRequests\AnimalsListRequest::PER_PAGE_CHOICES as $size): ?>
                            <option value="<?= $size ?>" <?= $request->perPage == $size ? 'selected' : '' ?>>
                                <?= $size ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>


                <select id="speciesSelect" class="mb-2"
                        name="species_id"
                        data-api="/species"
                        data-current="<?= (int)$request->species_id ?>"
                        data-create="false"
                        data-placeholder="Вид...">
                </select>

                <select name="sex" class="form-select mb-2">
                    <option value="">Стать (усі)</option>
                    <?php foreach ($sexes as $sx): ?>
                        <option value="<?= $sx->value ?>"
                            <?= $request->sex === $sx->value ? 'selected' : '' ?>>
                            <?= $sx->label() ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select multiple name="tag_ids[]" class="mb-2"
                        data-api="/tags"
                        data-create="false"
                        data-placeholder="Особливості..."
                        data-selected="<?= implode(', ', array_map('intval', $request->tag_ids ?? [])) ?>">
                </select>
                <?php
                $amin = $request->age_min;
                $amax = $request->age_max;
                $aminY = $amin !== null ? intdiv($amin, 12) : '';
                $aminM = $amin !== null ? $amin % 12 : '';
                $amaxY = $amax !== null ? intdiv($amax, 12) : '';
                $amaxM = $amax !== null ? $amax % 12 : '';
                ?>
                <label class="form-label mb-1">Вік тваринки</label>
                <div class="col g-2 mb-3">
                    <div class="col-12">
                        <div class="input-group">
                            <span class="input-group-text px-2">від</span>
                            <input type="number" min="0" max="50" step="1"
                                   class="form-control" id="ageMinY" placeholder="0" value="<?= $aminY ?>">
                            <span class="input-group-text">р</span>
                            <input type="number" min="0" max="11" step="1"
                                   class="form-control" id="ageMinM" placeholder="0" value="<?= $aminM ?>">
                            <span class="input-group-text px-2">м</span>
                        </div>
                    </div>

                    <div class="col-12 mt-2">
                        <div class="input-group">
                            <span class="input-group-text px-2">до</span>
                            <input type="number" min="0" max="50" step="1"
                                   class="form-control" id="ageMaxY" placeholder="0" value="<?= $amaxY ?>">
                            <span class="input-group-text">р</span>
                            <input type="number" min="0" max="11" step="1"
                                   class="form-control" id="ageMaxM" placeholder="0" value="<?= $amaxM ?>">
                            <span class="input-group-text px-2">м</span>
                        </div>
                    </div>
                </div>

                <input type="hidden" name="age_min" id="age_min"
                       value="<?= $request->age_min ?? '' ?>">
                <input type="hidden" name="age_max" id="age_max"
                       value="<?= $request->age_max ?? '' ?>">

                <label class="form-label mt-2" for="sortBy">Сортувати за</label>
                <select class="form-select mb-2" id="sortBy" name="sortBy">
                    <?php
                    $options = [
                        'id' => 'ID',
                        'name' => 'Ім’я',
                        'updated_at' => 'Оновлено',
                        'age_min_months' => 'Вік (мін)'
                    ];
                    foreach ($options as $v => $lbl): ?>
                        <option value="<?= $v ?>" <?= $request->sortBy === $v ? 'selected' : '' ?>>
                            <?= $lbl ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label class="form-label" for="direction">Напрямок</label>
                <select class="form-select mb-3" id="direction" name="direction">
                    <option value="asc" <?= $request->direction == 'ASC' ? 'selected' : '' ?>>За зростанням ↑</option>
                    <option value="desc" <?= $request->direction == 'DESC' ? 'selected' : '' ?>>За спаданням ↓</option>
                </select>

                <button class="btn btn-primary w-100">Застосувати</button>
                <a href="/animals" class="d-block text-center small mt-2">Скинути</a>
            </form>
        </aside>

        <section class="col-md-8 col-lg-9">
            <div class="row row-cols-1 row-cols-md-2 row-cols-xl-3 g-4">
                <?php foreach ($animals->items as $a): ?>
                    <div class="col">
                        <div class="card h-100 shadow-sm">
                            <img src="<?= htmlspecialchars($a['cover_image_url']) ?>"
                                 class="card-img-top" style="object-fit:cover;height:220px">
                            <div class="card-body">
                                <h6 class="card-title mb-1"><?= htmlspecialchars($a['name']) ?></h6>
                                <div class="text-muted small">
                                    <?= ucfirst(Sex::tryFrom($a['sex'])->label()) ?>,
                                    <?= animal_age($a['age_min_months'] ?? 0,
                                        $a['age_max_months'],
                                        $a['updated_at']) ?>
                                </div>
                            </div>
                            <div class="card-footer small text-end">
                                ID <?= $a['id'] ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <div class="mt-4">
                <?= paginationLinks($animals) ?>
            </div>
        </section>
    </div>
</div>
<script>
    (function () {
        const byId = id => document.getElementById(id);
        const [y1, m1, y2, m2] =
            ['ageMinY', 'ageMinM', 'ageMaxY', 'ageMaxM'].map(byId);

        const hMin = byId('age_min'), hMax = byId('age_max');

        function toMonths(y, m) {
            return (parseInt(y) || 0) * 12 + (parseInt(m) || 0);
        }

        function sync() {
            hMin.value = (y1.value || m1.value) ? toMonths(y1.value, m1.value) : '';
            hMax.value = (y2.value || m2.value) ? toMonths(y2.value, m2.value) : '';
        }

        [y1, m1, y2, m2].forEach(i => i.addEventListener('input', sync));
        sync();
    })();
</script>

<script type="module" src="../../public/js/useCustomSelect.js"></script>