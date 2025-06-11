<?php
/** @var string $Title */
/** @var string $Content */

$Title = $Title ?? '';
$Content = $Content ?? '';
$isAuth = \models\User::isUserLoggedIn();
?>
<!doctype html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($Title) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <style>
        :root {
            --bs-primary: #7ac7c4;
            --bs-primary-rgb: 122, 199, 196;
            --bs-navbar-brand-font-size: 1.25rem;
            --bs-body-bg: #fcfcfc;
        }

        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1 0 auto;
        }

        footer {
            flex-shrink: 0;
        }
    </style>
</head>

<body>
<header>
    <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="/">
                <img src="/public/images/shelter_icon.jpg" alt="Логотип притулку" width="64" height="64" class="me-2">
                Кам'янський притулок
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                    aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="/pets">Тваринки</a></li>
                </ul>

                <div class="d-flex gap-2">
                    <?php if ($isAuth): ?>
                        <a class="btn btn-outline-primary"
                           href="/account/profile">Профіль</a>
                        <form action="/users/logout" method="get" class="m-0">
                            <button class="btn btn-primary">Вийти</button>
                        </form>
                    <?php else: ?>
                        <a class="btn btn-outline-primary" href="/users/login">Увійти</a>
                        <a class="btn btn-primary" href="/users/register">Реєстрація</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>
</header>

<main class="container py-4">
    <?= $Content ?>
</main>

<footer class="bg-light text-center py-3 small text-muted">
    &copy; <?= date('Y') ?> Притулок для тварин Кам'янської Міської Ради - усі права захищено
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="/public/js/useFormValidation.js"></script>
</body>
</html>
