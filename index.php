<?php
session_start();

$host = 'localhost';
$db   = 'mydb';
$user = 'shved';
$pass = 'DeadDemon6:6';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="style/style.css" />
    <title>Главная</title>
</head>
<body>
    <!-- Навигация -->
    <nav class="shap">
        <ul>
            <li><a href="#" class="salka">Главная</a></li>
            <li><a href="/applications.php" class="salka">Заявления</a></li> 
            <li><a href="#" class="salka">Лицензия</a></li>
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
        <h1>Наруешниям.нет</h1>
       <!-- Внутри PHP блока -->
<?php if (isset($_SESSION['user_id'])): ?>
    <p>Здравствуйте, <?= htmlspecialchars($_SESSION['full_name']) ?>!</p>
    <p>Если вы стали свидетелем нарушений правил дорожного движения, можете оставить заявление и мы его рассмотрим в ближайшее время.</p>
    <a href="zayavlenie.php" class="btn btn-orange">Оставить заявление</a>
<?php else: ?>
    <p>Пожалуйста, войдите в систему или зарегистрируйтесь для доступа к заявкам.</p>
    <div style="margin-top: 20px;">
        <a href="/login.php" class="btn btn-green">Вход</a>
        <a href="/register.php" class="btn btn-blue">Регистрация</a>
    </div>
<?php endif; ?>
    </section>

    <!-- Подвал -->
    <footer></footer>
</body>
</html>