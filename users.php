<?php
// Настройки подключения к базе данных (замените на свои параметры)
$host = 'localhost';
$db   = 'mydb';
$user = 'shved';
$pass = 'DeadDemon6:6';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";

try {
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Ошибка подключения к базе данных: " . $e->getMessage());
}

// Получение всех пользователей
$stmt = $pdo->query("SELECT id, login, full_name, phone, email FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8" />
<title>Список зарегистрированных пользователей</title>
</head>
<body>

<h2>Зарегистрированные пользователи</h2>

<table border="1" cellpadding="5" cellspacing="0">
<tr>
  <th>ID</th>
  <th>Логин</th>
  <th>ФИО</th>
  <th>Телефон</th>
  <th>Email</th>
</tr>

<?php foreach ($users as $user): ?>
<tr>
  <td><?= htmlspecialchars($user['id']) ?></td>
  <td><?= htmlspecialchars($user['login']) ?></td>
  <td><?= htmlspecialchars($user['full_name']) ?></td>
  <td><?= htmlspecialchars($user['phone']) ?></td>
  <td><?= htmlspecialchars($user['email']) ?></td>
</tr>
<?php endforeach; ?>

</table>

</body>
</html>