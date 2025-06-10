<?php
$this->Title = "Login";
$errs = $errors ?? [];
?>

<style>
    body {
        font-family: sans-serif;
        margin: 20px;
    }

    form {
        background: #f4f4f4;
        padding: 20px;
        border-radius: 8px;
        max-width: 400px;
        margin: auto;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    input[type="email"],
    input[type="password"] {
        width: calc(100% - 22px); /* Залишаємо місце для padding та border */
        padding: 10px;
        margin-bottom: 15px;
        border: 1px solid #ccc;
        border-radius: 4px;
    }

    button {
        background-color: #007bff;
        color: white;
        padding: 10px 15px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }

    button:hover {
        background-color: #0056b3;
    }

    .error {
        color: red;
        margin-bottom: 15px;
    }
</style>
<body>

<h1>Вхід до системи</h1>

<form action="/users/login" method="post">
    <input name="next" type="hidden" value="<?= $next ?? "" ?>"/>
    <div>
        <label for="email">Електронна пошта:</label>
        <input type="email" id="email" name="email" required value="<?= $model->email ?? "" ?>">
        <?php if (!empty($errs['email'])): ?>
            <div class="error"><?= implode(', ', $errs['email']) ?></div>
        <?php endif; ?>
    </div>
    <div>
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required value="<?= $model->password ?? "" ?>">
        <?php if (!empty($errs['password'])): ?>
            <div class="error"><?= implode(', ', $errs['password']) ?></div>
        <?php endif; ?>
    </div>
    <button type="submit">Увійти</button>
</form>

</body>