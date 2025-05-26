<?php
session_start();
require 'db_connection.php'; // подключение к базе данных

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // перенаправление на страницу входа, если не авторизован
    exit;
}

// Обработка отправки нового заявления
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['statement'])) {
    $statement = trim($_POST['statement']);
    $user_id = $_SESSION['user_id'];

    // Вставка нового заявления в базу
    $stmt = $pdo->prepare("INSERT INTO applications (user_id, statement) VALUES (?, ?)");
    $stmt->execute([$user_id, $statement]);
}

// Получение заявлений текущего пользователя
$stmt = $pdo->prepare("SELECT * FROM applications WHERE user_id = ? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$applications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8" />
<link rel="stylesheet" href="style/style.css" />
<title>Мои заявления</title>
</head>
<body>

<section class="content">
<h1>Заявления</h1>

<!-- Форма для добавления нового заявления -->
<h2>Оставить новое заявление</h2>
<a href="zayavlenie.php" class="btn btn-orange">Новое заявление</a>

<!-- Таблица с заявками -->
<h2>История заявлений</h2>
<?php if (count($applications) > 0): ?>
<table border="1" cellpadding="5" cellspacing="0">
<tr>
    <th>Заявление</th>
    <th>Статус</th>
    <th>Дата отправки</th>
</tr>
<?php foreach ($applications as $app): ?>
<tr>
    <td><?= htmlspecialchars($app['statement']) ?></td>
    <td><?= htmlspecialchars($app['status']) ?></td>
    <td><?= htmlspecialchars($app['created_at']) ?></td>
</tr>
<?php endforeach; ?>
</table>
<?php else: ?>
<p>У вас пока нет заявлений.</p>
<?php endif; ?>
<p><a href="index.php">Вернуться на главную</a></p>
<a href="logout.php">Выйти из аккаунта</a>
</section>

</body>
</html>