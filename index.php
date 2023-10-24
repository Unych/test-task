<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Форма для заявок</title>
</head>
<body>
<h1>Заполните форму для заявки</h1>
<form action="AmoCRMActions.php" method="post">
    <label for="name">Имя:</label>
    <input type="text" id="name" name="name" required>
    <br>

    <label for="email">Email:</label>
    <input type="email" id="email" name="email" required>
    <br>

    <label for="phone">Телефон:</label>
    <input type="tel" id="phone" name="phone" required>
    <br>

    <label for="price">Цена:</label>
    <input type="number" id="price" name="price">
    <br>

    <button type="submit">Отправить заявку</button>
</form>
</body>
</html>
