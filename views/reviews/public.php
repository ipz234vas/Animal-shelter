<?php
/* @var array[] $reviews */
require_once 'app/helpers/forms.php';
?>
<div class="container mt-4" style="max-width:1024px">
    <h3 class="mb-4">Відгуки наших опікунів</h3>

    <?php if (!$reviews): ?>
        <div class="alert alert-info">Наразі опублікованих відгуків немає.</div>
    <?php endif; ?>

    <?php foreach ($reviews as $r): ?>
        <?php
        $firstImg = $r['images'][0]['file_path'] ?? '/public/images/no_photo.png';
        ?>
        <div class="card mb-4 shadow-sm">
            <div class="row g-0">
                <div class="col-md-4">
                    <img src="<?= htmlspecialchars($firstImg) ?>"
                         class="img-fluid rounded-start w-100 h-100"
                         style="object-fit:cover" alt="Фото відгуку">
                </div>

                <div class="col-md-8">
                    <div class="card-body d-flex flex-column h-100">
                        <h5 class="card-title mb-1">
                            <a href="/animals/show?id=<?= $r['animal_id'] ?? 0 ?>"
                               class="link-secondary text-decoration-none">
                                <?= htmlspecialchars($r['animal_name'] ?? 'Тваринка') ?>
                            </a>
                        </h5>

                        <p class="text-muted mb-2 small">
                            Автор: <?= htmlspecialchars($r['user_name'] ?? '—') ?>
                        </p>

                        <p class="card-text" style="white-space:pre-line">
                            <?= htmlspecialchars($r['text']) ?>
                        </p>

                        <?php if (count($r['images']) > 1): ?>
                            <div class="mt-auto pt-2 d-flex gap-2 flex-wrap">
                                <?php foreach (array_slice($r['images'], 1) as $img): ?>
                                    <img src="<?= htmlspecialchars($img['file_path']) ?>"
                                         style="width:72px;height:72px;object-fit:cover"
                                         class="rounded border" alt="">
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>