<?php
/** @var array[] $apps */

use enums\applications\AdoptionStatus;
use enums\database\SQLOperator;

require_once 'app/helpers/pagination.php';
require_once 'app/helpers/forms.php';

function status_badge(AdoptionStatus $st): string
{
    return match ($st) {
        AdoptionStatus::Draft => "<span class=\"badge bg-secondary\">{$st->label()}</span>",
        AdoptionStatus::Pending => "<span class=\"badge bg-warning\">{$st->label()}</span>",
        AdoptionStatus::Accepted => "<span class=\"badge bg-success\">{$st->label()}</span>",
        AdoptionStatus::Rejected => "<span class=\"badge bg-danger\">{$st->label()}</span>",
    };
}

?>
    <div class="container mt-4" style="max-width:980px">
        <h3 class="mb-4">Мої заявки&nbsp;на&nbsp;усиновлення</h3>

        <?php if (!$apps): ?>
            <div class="alert alert-info">Поки що ви не створили жодної заявки.</div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                    <tr>
                        <th style="width:80px">ID</th>
                        <th>Тваринка</th>
                        <th style="width:140px">Статус</th>
                        <th>Коментар</th>
                        <th style="width:240px" class="text-end">Дії</th>
                    </tr>
                    </thead>

                    <tbody>
                    <?php foreach ($apps as $app): ?>
                        <?php
                        $st = AdoptionStatus::from($app['status']);
                        $isDraft = $st === AdoptionStatus::Draft;
                        $isAccepted = $st === AdoptionStatus::Accepted;

                        $hasReview = false;
                        if ($isAccepted) {
                            $hasReview = \models\Review::asQuery()
                                    ->select(['id'])
                                    ->where('application_id', SQLOperator::Equal, $app['id'])
                                    ->first() !== null;
                        }
                        ?>
                        <tr>
                            <td>#<?= $app['id'] ?></td>

                            <td>
                                <a href="/animals/show?id=<?= $app['animal_id'] ?>"
                                   class="link-secondary text-decoration-none">
                                    <?= htmlspecialchars($app['animal_name'] ?? '—') ?>
                                </a>
                            </td>

                            <td><?= status_badge($st) ?></td>

                            <td class="text-truncate" style="max-width:260px">
                                <?= $app['comment'] ? htmlspecialchars($app['comment']) : '—' ?>
                            </td>

                            <td class="text-end">
                                <div class="d-inline-flex gap-1">

                                    <?php if ($isDraft): ?>
                                        <a href="/adoptions/edit?id=<?= $app['id'] ?>"
                                           class="btn btn-sm btn-outline-secondary"
                                           title="Редагувати">
                                            <i class="bi bi-pencil"></i>
                                        </a>

                                        <form action="/adoptions/submit" method="post" class="m-0">
                                            <input type="hidden" name="id" value="<?= $app['id'] ?>">
                                            <button class="btn btn-sm btn-outline-primary" title="Надіслати">
                                                <i class="bi bi-send"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>

                                    <?php if ($isAccepted && !$hasReview): ?>
                                        <!-- залишити відгук -->
                                        <a href="/reviews/create?application_id=<?= $app['id'] ?>"
                                           class="btn btn-sm btn-outline-success"
                                           title="Залишити відгук">
                                            <i class="bi bi-chat-square-text"></i>
                                        </a>
                                    <?php endif; ?>


                                    <?php if (!$isAccepted): ?>
                                        <button type="button"
                                                class="btn btn-sm btn-outline-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteConfirmationModal"
                                                data-id="<?= $app['id'] ?>"
                                                data-name="заявку&nbsp;№<?= $app['id'] ?>"
                                                data-action="/adoptions/delete">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

<?php require dirname(__DIR__, 2) . '/shared/delete_confirmation_modal.php'; ?>