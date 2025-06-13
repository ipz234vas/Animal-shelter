<?php
/** @var string $Title */
/** @var string $Content */

$Title = $Title ?? "Кам'янський притулок";
$Content = $Content ?? '';
$isAuth = \models\User::isUserLoggedIn();
$permissionsStr = $isAuth
    ? \models\User::getPermissionsById(\models\User::getCurrentUser()['id'])
    : "";
$permissions = \classes\PermissionParser::fromString($permissionsStr);
$can = static fn(\enums\auth\Permission $p) => in_array($p, $permissions);
?>
<!doctype html>
<html lang="uk">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($Title) ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/css/tom-select.css" rel="stylesheet">


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
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 pt-1">
                    <li class="nav-item">
                        <a class="nav-link" href="/animals">Тваринки</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/reviews/public">Ми вдома</a>
                    </li>
                    <?php if ($isAuth): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/adoptions">Мої заявки</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="/reviews">Мої відгуки</a>
                        </li>
                    <?php endif; ?>
                    <?php if ($can(\enums\auth\Permission::ManageUsers)
                        || $can(\enums\auth\Permission::ManageApplications)
                        || $can(\enums\auth\Permission::ManageReviews)): ?>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button"
                               data-bs-toggle="dropdown" aria-expanded="false">
                                Адмін&nbsp;панель
                            </a>

                            <ul class="dropdown-menu">
                                <?php if ($can(\enums\auth\Permission::ManageUsers)): ?>
                                    <li><a class="dropdown-item" href="/users">
                                            <i class="bi bi-people me-1"></i> Користувачі</a></li>
                                <?php endif; ?>

                                <?php if ($can(\enums\auth\Permission::ManageApplications)): ?>
                                    <li><a class="dropdown-item" href="/adoptions/manage">
                                            <i class="bi bi-inboxes me-1"></i> Заявки</a></li>
                                <?php endif; ?>

                                <?php if ($can(\enums\auth\Permission::ManageReviews)): ?>
                                    <li><a class="dropdown-item" href="/reviews/manage">
                                            <i class="bi bi-chat-left-text me-1"></i> Відгуки</a></li>
                                <?php endif; ?>
                            </ul>
                        </li>
                    <?php endif; ?>
                </ul>

                <div class="d-flex gap-2">
                    <?php if ($isAuth): ?>
                        <a class="btn btn-outline-primary"
                           href="/account/profile">Профіль</a>
                        <form action="/auth/logout" method="get" class="m-0">
                            <button class="btn btn-primary">Вийти</button>
                        </form>
                    <?php else: ?>
                        <a class="btn btn-outline-primary" href="/auth/login">Увійти</a>
                        <a class="btn btn-primary" href="/auth/register">Реєстрація</a>
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
    <div class="container">
        <div>&copy; <?= date('Y') ?> Притулок для тварин Кам'янської Міської Ради — усі права захищено</div>
        <div class="mt-1">
            📞 <a href="tel:+380636312879" class="text-muted text-decoration-none">0 (63) 631 28 79</a> |
            📞 <a href="tel:+380632026858" class="text-muted text-decoration-none">0 (63) 202 68 58</a>
        </div>
    </div>
</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" defer></script>
<script src="https://cdn.jsdelivr.net/npm/tom-select@2.4.3/dist/js/tom-select.complete.min.js"></script>
<script src="/public/js/useFormValidation.js"></script>
</body>
</html>
