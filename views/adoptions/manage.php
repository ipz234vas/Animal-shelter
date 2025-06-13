<?php
/** @var array[] $apps */

use enums\applications\AdoptionStatus;

require_once 'app/helpers/forms.php';

/* ── бейджі ───────────────────────────────────────────── */
function status_badge_admin(AdoptionStatus $st): string
{
    return match ($st) {
        AdoptionStatus::Pending  => '<span class="badge bg-warning">Очікує</span>',
        AdoptionStatus::Accepted => '<span class="badge bg-success">Схвалена</span>',
        AdoptionStatus::Rejected => '<span class="badge bg-danger">Відхилена</span>',
        default                  => '<span class="badge bg-secondary">Чернетка</span>',
    };
}
?>
<div class="container mt-4" style="max-width:1080px">
    <h3 class="mb-4">Заявки&nbsp;на&nbsp;усиновлення — адміністрування</h3>

    <?php if (!$apps): ?>
        <div class="alert alert-info">Немає жодної активної заявки.</div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-hover align-middle small">
                <thead class="table-light">
                <tr>
                    <th style="width:70px">ID</th>
                    <th>Тваринка</th>
                    <th>Користувач</th>
                    <th style="width:140px">Статус</th>
                    <th>Коментар</th>
                    <th style="width:180px" class="text-end">Дії</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($apps as $app): ?>
                    <?php $st = AdoptionStatus::from($app['status']); ?>
                    <tr>
                        <td>#<?= $app['id'] ?></td>

                        <td>
                            <a href="/animals/show?id=<?= $app['animal_id'] ?>"
                               class="link-secondary text-decoration-none">
                                <?= htmlspecialchars($app['animal_name'] ?? '—') ?>
                            </a>
                        </td>

                        <td><?= htmlspecialchars($app['user_name'] ?? '—') ?></td>

                        <td><?= status_badge_admin($st) ?></td>

                        <td class="text-truncate" style="max-width:280px">
                            <?= $app['comment'] ? htmlspecialchars($app['comment']) : '—' ?>
                        </td>

                        <!-- ======== ACTIONS ======== -->
                        <td class="text-end">
                            <div class="d-inline-flex gap-1 align-items-center">
                                <?php if ($st !== AdoptionStatus::Accepted): ?>
                                    <!-- зміна статусу -->
                                    <form action="/adoptions/status" method="post" class="d-flex gap-1 m-0">
                                        <input type="hidden" name="id" value="<?= $app['id'] ?>">
                                        <select name="status"
                                                class="form-select form-select-sm"
                                                style="width:auto">
                                            <?php foreach ([AdoptionStatus::Pending,
                                                            AdoptionStatus::Accepted,
                                                            AdoptionStatus::Rejected] as $opt): ?>
                                                <option value="<?= $opt->value ?>"
                                                    <?= $opt === $st ? 'selected' : '' ?>>
                                                    <?= $opt->label() ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <button class="btn btn-sm btn-outline-primary"
                                                title="Зберегти">
                                            <i class="bi bi-check-lg"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <!-- delete -->
                                <button type="button"
                                        class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteConfirmationModal"
                                        data-id="<?= $app['id'] ?>"
                                        data-name="заявку&nbsp;№<?= $app['id'] ?>"
                                        data-action="/adoptions/admin/delete">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php require dirname(__DIR__,2).'/shared/delete_confirmation_modal.php'; ?>