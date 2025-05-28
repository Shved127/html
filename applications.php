<?php
session_start();

require 'db_connection.php'; // подключение к базе данных

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Обработка изменения статуса
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['application_id']) && isset($_POST['new_status'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        $application_id = intval($_POST['application_id']);
        $new_status = $_POST['new_status'];
        if (in_array($new_status, ['Одобрено', 'Отклонено'])) {
            $stmt = $pdo->prepare("UPDATE applications SET status = ? WHERE id = ?");
            $stmt->execute([$new_status, $application_id]);
        }
    }
}

// Обработка удаления заявления
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_application_id'])) {
    if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
        $delete_id = intval($_POST['delete_application_id']);
        $stmt = $pdo->prepare("DELETE FROM applications WHERE id = ?");
        $stmt->execute([$delete_id]);
    }
}

// Получение заявлений
if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin') {
    // Админ видит все заявления
    $stmt = $pdo->prepare("SELECT * FROM applications ORDER BY created_at DESC");
    $stmt->execute();
} else {
    // Обычный пользователь — только свои
    $stmt = $pdo->prepare("SELECT * FROM applications WHERE user_id = ? ORDER BY created_at DESC");
    $stmt->execute([$_SESSION['user_id']]);
}
$applications = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8" />
<link rel="stylesheet" href="style/style.css" />
<title>Заявления</title>
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
    <th>ФИО заявителя</th>
    <th>Заявление</th>
    <th>Статус</th>
    <th>Дата отправки</th>
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <th>Действия</th>
    <?php endif; ?>
</tr>
<?php foreach ($applications as $app): ?>
<tr>
    <td><?= htmlspecialchars($app['applicant_name']) ?></td>
    <td><?= htmlspecialchars($app['statement']) ?></td>
    <td><?= htmlspecialchars($app['status']) ?></td>
    <td><?= htmlspecialchars($app['created_at']) ?></td>
    
    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
        <td style="white-space: nowrap;">
            <!-- Формы для изменения статуса -->
            <form method="post" style="display:inline-block;">
                <input type="hidden" name="application_id" value="<?= $app['id'] ?>">
                <button type="submit" name="new_status" value="Одобрено">Одобрить</button>
                <button type="submit" name="new_status" value="Отклонено">Отклонить</button>
            </form>

            <!-- Форма для удаления -->
            <form method="post" style="display:inline-block; margin-left:10px;" onsubmit="return confirm('Удалить это заявление?');">
                <input type="hidden" name="delete_application_id" value="<?= $app['id'] ?>">
                <button type="submit" style="background-color:#d9534f; color:#fff;">Удалить</button>
            </form>
        </td>
    <?php endif; ?>
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