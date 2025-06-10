<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вхід</title>
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

        .error-message {
            color: red;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>

<h1>Вхід до системи</h1>

<?php if (!empty($Test)): ?>
    <p class="error-message"><?= htmlspecialchars($Test) ?></p>
<?php endif; ?>

<form action="/users/login">
    <input name="_method" type="hidden" value="delete"/>
    <div>
        <label for="email">Електронна пошта:</label>
        <input type="email" id="email" name="email" required>
    </div>
    <div>
        <label for="password">Пароль:</label>
        <input type="password" id="password" name="password" required>
    </div>
    <button type="submit">Увійти</button>
</form>

</body>
</html>