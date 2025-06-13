<?php
/** @var array $a */
/** @var bool $accepted_any */
/** @var int|null $pending_cnt */

require_once 'app/helpers/animals.php';

use enums\animals\Sex;

?>
<style>
    .media-wrapper {
        position: relative;
        aspect-ratio: 9/16;
        max-width: 340px;
        max-height: 600px;
        margin: auto;
        background: #000;
        border-radius: .5rem;
        overflow: hidden;

        display: flex;
        align-items: center;
        justify-content: center;
    }

    .media-wrapper video {
        width: 100%;
        height: 100%;
        object-fit: contain;
    }

    .media-wrapper img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .media-wrapper button {
        position: absolute;
        right: .5rem;
        bottom: .5rem;
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: none;
        background: #fff8;
        backdrop-filter: blur(5px);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .media-wrapper button:hover {
        background: #fff;
    }

    .media-wrapper svg {
        width: 20px;
        height: 20px;
    }

    .card-animal {
        max-width: 880px;
        margin: auto
    }
</style>

<div class="card shadow-sm card-animal">
    <div class="row g-0">
        <div class="col-md-6">
            <?php if ($a['intro_video_url']): ?>
                <div class="media-wrapper">
                    <video muted autoplay loop playsinline
                           poster="<?= htmlspecialchars($a['cover_image_url']) ?>">
                        <source src="<?= htmlspecialchars($a['intro_video_url']) ?>">
                    </video>
                    <button id="volBtn">
                        <svg id="volOff" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                             class="bi bi-volume-mute" viewBox="0 0 16 16">
                            <path d="M9.717 3.55 6.825 6H4.333A.333.333 0 0 0 4 6.333v3.334c0 .184.149.333.333.333h2.492l2.892 2.45A.333.333 0 0 0 10 12.117V3.883a.333.333 0 0 0-.283-.333z"/>
                            <path d="M13.002 9.595 11.404 8l1.598-1.595-.708-.708L10.707 7.293 9.11 5.697l-.708.708L10.586 8l-2.184 2.184.708.708L10.707 8.707l1.588 1.588.707-.707z"/>
                        </svg>
                        <svg id="volOn" class="d-none" xmlns="http://www.w3.org/2000/svg" fill="currentColor"
                             viewBox="0 0 16 16">
                            <path d="M9.717 3.55 6.825 6H4.333A.333.333 0 0 0 4 6.333v3.334c0 .184.149.333.333.333h2.492l2.892 2.45A.333.333 0 0 0 10 12.117V3.883a.333.333 0 0 0-.283-.333z"/>
                            <path d="M11.536 14.01a.5.5 0 0 1-.03-.706 6.58 6.58 0 0 0 0-9.609.5.5 0 0 1 .737-.676 7.58 7.58 0 0 1 0 10.96.5.5 0 0 1-.707.03z"/>
                            <path d="M12.93 12.646a.5.5 0 0 1-.38-.926 4.578 4.578 0 0 0 0-7.44.5.5 0 0 1 .759-.65 5.578 5.578 0 0 1 0 8.741.5.5 0 0 1-.38.275z"/>
                        </svg>
                    </button>
                </div>
            <?php else: ?>
                <div class="media-wrapper">
                    <img src="<?= htmlspecialchars($a['cover_image_url']) ?>"
                         alt="<?= htmlspecialchars($a['name']) ?>">
                </div>
            <?php endif; ?>
        </div>

        <div class="col-md-6">
            <div class="card-body h-100 d-flex flex-column">
                <h3 class="card-title"><?= htmlspecialchars($a['name']) ?>
                    - <?= htmlspecialchars($a['species_name'] ?? 'невідомий вид') ?></h3>

                <p class="text-muted mb-2">
                    <?= ucfirst(Sex::tryFrom($a['sex'])->label()) ?>,&nbsp;
                    <?= animal_age($a['age_min_months'] ?? 0, $a['age_max_months'], $a['updated_at']) ?>
                </p>

                <?php if ($a['tags']): ?>
                    <p>
                        <?php foreach ($a['tags'] as $t): ?>
                            <span class="badge bg-primary-subtle text-dark me-1">
                                <?= htmlspecialchars($t['name']) ?>
                            </span>
                        <?php endforeach; ?>
                    </p>
                <?php endif; ?>

                <?php if ($a['description']): ?>
                    <p class="mb-4" style="white-space:pre-line">
                        <?= htmlspecialchars($a['description']) ?>
                    </p>
                <?php endif; ?>

                <div class="mt-auto">
                    <?php if ($accepted_any): ?>
                        <div class="alert alert-warning text-center mb-0">
                            Чиясь заявка на цю тварину вже <strong>прийнята</strong>.
                        </div>

                    <?php else: ?>
                        <?php if ($pending_cnt > 0): ?>
                            <p class="small text-muted text-center mb-2">
                                Уже подано заявок:&nbsp;<strong><?= $pending_cnt ?></strong>
                            </p>
                        <?php endif; ?>

                        <a href="/adoptions/create?animal_id=<?= $a['id'] ?>"
                           class="btn btn-primary w-100">
                            Подарувати дім
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    (() => {
        const video = document.querySelector('video');
        if (!video)
            return;

        const btn = document.getElementById('volBtn'),
            off = document.getElementById('volOff'),
            on = document.getElementById('volOn');

        btn.addEventListener('click', () => {
            video.muted = !video.muted;
            off.classList.toggle('d-none', !video.muted);
            on.classList.toggle('d-none', video.muted);
        });
    })();
</script>
