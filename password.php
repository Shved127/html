<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    if ($password !== '') {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        echo "<p>Хеш пароля: <b>$hash</b></p>";
    } else {
        echo "<p style='color:red;'>Пожалуйста, введите пароль.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8" />
<title>Хеширование пароля</title>
</head>
<body>
<h2>Генератор хеша пароля</h2>
<form method="post" action="">
    <label for="password">Введите пароль:</label><br />
    <input type="text" id="password" name="password" required /><br /><br />
    <button type="submit">Получить хеш</button>

<?php
// Подключение к базе данных (используйте ваши параметры)
$host = 'localhost';
$db   = 'mydb';
$user = 'shved';
$pass = 'DeadDemon6:6';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения: " . htmlspecialchars($e->getMessage()));
}

// Генерируем новый хеш для пароля 'password'
$new_hash = password_hash('password', PASSWORD_DEFAULT);

// Обновляем пароль для пользователя 'comm'
$stmt = $pdo->prepare("UPDATE users SET password = ? WHERE login = ?");
$stmt->execute([$new_hash, 'comm']);

echo "Пароль обновлен.";
?>
</form>
</body>
</html>