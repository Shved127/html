<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Не админ — перенаправляем или показываем ошибку
    header('Location: login.php');
    exit;
}

// Подключение к базе данных (если нужно)
$host = 'localhost';
$db   = 'mydb';
$user = 'shved';
$pass = 'DeadDemon6:6';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

// Можно подключиться к базе данных, если потребуется
try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style/style.css" />
    <title>Панель администратора</title>
</head>
<body>
    <!-- Навигация -->
    <nav class="shap">
        <ul>
            <li><a href="#" class="salka">Главная</a></li>
            <li><a href="/applications.php" class="salka">Заявления</a></li> 
            <li><a href="/users_admin.php">Управление пользователями</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="logout.php">Выход</a></li>
            <?php else: ?>
                <li><a href="/login.php">Войти</a></li>
                <li><a href="register.php">Регистрация</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <!-- Основной контент -->
    <section class="content">
        <h1>Добро пожаловать в панель администратора Наруешниям.нет</h1>

        <!-- Приветствие -->
        <?php if (isset($_SESSION['full_name'])): ?>
            <p>Привет, <?= htmlspecialchars($_SESSION['full_name']) ?>!</p>
        <?php else: ?>
            <p>Добро пожаловать!</p>
        <?php endif; ?>

        <!-- Здесь можно разместить административные функции -->
        <!-- Например, список пользователей, управление заявками и т.д. -->

        <!-- Пример кнопки для выхода -->
        <a href="/applications.php" class="btn btn-orange">Посмотреть заявления</a>
        <a href="logout.php" class="btn btn-orange">Выйти из панели</a>
    </section>

    <!-- Подвал -->
    <footer></footer>
</body>
</html>