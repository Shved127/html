<?php
session_start();

// Подключение к базе данных (замените параметры на свои)
$host = 'localhost';
$db   = 'mydb';
$user = 'shved';
$pass = 'DeadDemon6:6';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе: " . $e->getMessage());
}

// Проверка авторизации
if (!isset($_SESSION['user_id'])) {
    die("Пожалуйста, войдите в систему.");
}

$message = ''; // Инициализация переменной для сообщений

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение данных из формы
    $reg_number = trim($_POST['reg_number'] ?? '');
    $violation_description = trim($_POST['violation_description'] ?? '');

    // Проверка обязательных полей
    if (empty($reg_number) || empty($violation_description)) {
        $message = "Пожалуйста, заполните все обязательные поля.";
    } else {
        // Создаем текст заявления
        $statement_text = "Госномер: " . htmlspecialchars($reg_number) . "\nОписание нарушения: " . htmlspecialchars($violation_description);

        // Вставляем данные в базу без фото
        try {
            $sql = "INSERT INTO applications (user_id, statement, reg_number, violation_description)
                    VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $_SESSION['user_id'],
                $statement_text,
                $reg_number,
                $violation_description
            ]);
            $message = "Заявление успешно отправлено!";
        } catch (PDOException $e) {
            $message = "Ошибка при сохранении заявления: " . htmlspecialchars($e->getMessage());
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Формирование заявления</title>
<link rel="stylesheet" href="style/style.css" />
</head>
<body>
<h1>Формирование заявления</h1>

<?php if ($message): ?>
    <p class="<?= strpos($message, 'ошибка') !== false ? 'error-message' : 'success-message' ?>"><?= htmlspecialchars($message) ?></p>
<?php endif; ?>

<section class="content">
<form method="post" action="">
    <label for="reg_number">Государственный регистрационный номер автомобиля:</label><br>
    <input type="text" id="reg_number" name="reg_number" required><br><br>

    <label for="violation_description">Описание нарушения:</label><br>
    <textarea id="violation_description" name="violation_description" rows="5" cols="50" required></textarea><br><br>

<!-- Удалена секция для загрузки фотографии -->
<!--
<div class="file-upload">
    <label for="photo">Прикрепить фотографию:</label>
    <input type="file" id="photo" name="photo" accept="image/*">
</div>
-->

    <button type="submit">Отправить заявление</button>
</form>

<a href="/applications.php">Посмотреть заявления</a>

<p><a href="index.php">Вернуться на главную</a></p>
</section>

</body>
</html>