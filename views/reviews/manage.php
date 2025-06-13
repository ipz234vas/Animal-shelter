<?php
/**
 * @var array[] $reviews
 */

use enums\reviews\ReviewStatus;

?>
<h3 class="mb-4">Модерація відгуків</h3>

<table class="table align-middle">
    <thead class="table-light">
    <tr>
        <th>ID</th>
        <th>Тваринка</th>
        <th>Автор</th>
        <th>Статус</th>
        <th>Текст</th>
        <th></th>
    </tr>
    </thead>
    <?php foreach ($reviews as $r): ?>
        <tr>
            <td>#<?= $r['id'] ?></td>
            <td><?= htmlspecialchars($r['animal_name']) ?></td>
            <td><?= htmlspecialchars($r['user_name']) ?></td>
            <td><?= ReviewStatus::from($r['status'])->label() ?></td>
            <td style="max-width:260px" class="text-truncate"><?= nl2br(htmlspecialchars($r['text'])) ?></td>
            <td class="text-end">
                <form action="/reviews/status" method="post" class="d-inline">
                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                    <select name="status" class="form-select form-select-sm d-inline w-auto">
                        <?php foreach ([ReviewStatus::Pending, ReviewStatus::Accepted, ReviewStatus::Rejected] as $st): ?>
                            <option value="<?= $st->value ?>" <?= $st->value == $r['status'] ? 'selected' : '' ?>>
                                <?= $st->label() ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <button class="btn btn-sm btn-outline-primary">OK</button>
                </form>

                <form action="/reviews/delete" method="post" class="d-inline"
                      onsubmit="return confirm('Видалити відгук #<?= $r['id'] ?>?')">
                    <input type="hidden" name="id" value="<?= $r['id'] ?>">
                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>
