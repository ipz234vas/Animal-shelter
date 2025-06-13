<?php
/**
 * @var array[] $reviews
 */

use enums\reviews\ReviewStatus;

?>
<h3 class="mb-4">Мої відгуки</h3>

<?php if (!$reviews): ?>
    <div class="alert alert-info">Ви ще не залишали відгуків.</div>
<?php else: ?>
    <table class="table align-middle">
        <thead class="table-light">
        <tr>
            <th>ID</th>
            <th>Тваринка</th>
            <th>Статус</th>
            <th>Текст</th>
        </tr>
        </thead>
        <?php foreach ($reviews as $r): $st = ReviewStatus::from($r['status']); ?>
            <tr>
                <td>#<?= $r['id'] ?></td>
                <td><?= htmlspecialchars($r['animal_name'] ?? '—') ?></td>
                <td><?= $st->label() ?></td>
                <td><?= nl2br(htmlspecialchars($r['text'])) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>
