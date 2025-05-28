<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit;
}

// Подключение к базе данных
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

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Получение данных из формы
    $login = trim($_POST['login'] ?? '');
    $password = $_POST['password'] ?? '';

    // Валидация логина
    if (empty($login)) {
        $error_message = 'Пожалуйста, введите логин.';
    } elseif (mb_strlen($login) < 3 || mb_strlen($login) > 20) {
        $error_message = 'Логин должен быть от 3 до 20 символов.';
    } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $login)) {
        $error_message = 'Логин может содержать только буквы, цифры и подчеркивания.';
    }

    // Валидация пароля
    if (!$error_message) { // если ошибок еще нет
        if (empty($password)) {
            $error_message = 'Пожалуйста, введите пароль.';
        } elseif (mb_strlen($password) < 6 || mb_strlen($password) > 50) {
            $error_message = 'Пароль должен быть от 6 до 50 символов.';
        }
        // Можно добавить дополнительные проверки сложности пароля
    }

    if (!$error_message) {
        // Поиск пользователя по логину
        $stmt = $pdo->prepare("SELECT * FROM users WHERE login = ?");
        $stmt->execute([$login]);
        $user_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user_data) {
            $error_message = 'Пользователь с таким логином не найден.';
        } else {
            // Проверка пароля
            if (password_verify($password, $user_data['password'])) {
                // Успешный вход — установить сессию
                $_SESSION['user_id'] = $user_data['id'];
                $_SESSION['login'] = $user_data['login'];
                $_SESSION['full_name'] = $user_data['full_name']; // если есть

                // Установка роли
                if (isset($user_data['role']) && $user_data['role'] === 'admin') {
                    $_SESSION['role'] = 'admin';
                } else {
                    $_SESSION['role'] = 'user'; // или оставить пустым
                }

                // Перенаправление в зависимости от роли
                if ($_SESSION['role'] === 'admin') {
                    header('Location: admin.php');
                } else {
                    header('Location: index.php');
                }
                exit;
            } else {
                $error_message = 'Неверный пароль.';
            }
        }
    }
}

// Обработка удаления пользователя
if (isset($_POST['delete_user_id'])) {
    $delete_id = intval($_POST['delete_user_id']);
    if ($delete_id !== $_SESSION['user_id']) { // нельзя удалять себя
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$delete_id]);
    }
}

// Обработка редактирования пользователя
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_user'])) {
    $id = intval($_POST['id']);
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $role = $_POST['role'];
    $password_input = trim($_POST['password']); // новое значение пароля

    // Начинаем подготовку запроса
    if ($password_input !== '') {
        // Хешируем новый пароль
        $password_hash = password_hash($password_input, PASSWORD_DEFAULT);
        // Обновляем все поля включая пароль
        $stmt = $pdo->prepare("UPDATE users SET full_name=?, email=?, phone=?, role=?, password=? WHERE id=?");
        $stmt->execute([$full_name, $email, $phone, $role, $password_hash, $id]);
    } else {
        // Обновляем только остальные поля без пароля
        $stmt = $pdo->prepare("UPDATE users SET full_name=?, email=?, phone=?, role=? WHERE id=?");
        $stmt->execute([$full_name, $email, $phone, $role, $id]);
    }
}

// Обработка создания нового пользователя
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['create_user'])) {
    $login = $_POST['login'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password_plain = $_POST['password'];
    $role = $_POST['role'];

    // Хешируем пароль
    $password_hash = password_hash($password_plain, PASSWORD_DEFAULT);

    // Вставляем нового пользователя
    try {
        $stmt = $pdo->prepare("INSERT INTO users (login, full_name, email, phone, password, role) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$login, $full_name, $email, $phone, $password_hash, $role]);
        echo "Пользователь успешно создан.";
    } catch (PDOException $e) {
        echo "Ошибка при создании пользователя: " . htmlspecialchars($e->getMessage());
    }
}

// Получение всех пользователей
$stmt = $pdo->query("SELECT * FROM users ORDER BY id");
$users = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
<meta charset="UTF-8" />
<title>Управление пользователями</title>
<link rel="stylesheet" href="style/style.css" />
</head>
<body>
<h1>Управление пользователями</h1>

    <section class="content">
<table border="1" cellpadding="5" cellspacing="0">
<tr>
<th>ID</th>
<th>Логин</th>
<th>ФИО</th>
<th>Email</th>
<th>Телефон</th>
<th>Роль</th>
<th>Действия</th>
</tr>
<?php foreach ($users as $user): ?>
<tr>
<td><?= htmlspecialchars($user['id']) ?></td>
<td><?= htmlspecialchars($user['login']) ?></td>
<td><?= htmlspecialchars($user['full_name']) ?></td>
<td><?= htmlspecialchars($user['email']) ?></td>
<td><?= htmlspecialchars($user['phone']) ?></td>
<td><?= htmlspecialchars($user['role']) ?></td>
<td>
<!-- Форма редактирования -->
<form method="post" style="display:inline-block;">
<input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
<input type="hidden" name="edit_user" value="1">
<label>ФИО: <input type="text" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required></label><br/>
<label>Email: <input type="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required></label><br/>
<label>Телефон: <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']) ?>"></label><br/>
<label>Роль:
<select name="role">
<option value='user' <?= ($user['role']=='user') ? 'selected' : '' ?>>Пользователь</option>
<option value='admin' <?= ($user['role']=='admin') ? 'selected' : '' ?>>Админ</option>
</select></label><br/>
<label>Пароль: <input type='password' name='password' placeholder='Оставьте пустым для сохранения текущего'></label><br/>
<button type="submit">Обновить</button>
</form>

<!-- Форма удаления -->
<form method="post" style="display:inline-block; margin-left:10px;" onsubmit="return confirm('Удалить этого пользователя?');">
<input type="hidden" name="delete_user_id" value="<?= htmlspecialchars($user['id']) ?>">
<button type="submit">Удалить</button>
</form>

</td>
</tr>
<?php endforeach; ?>
</table>

<h2>Создать нового пользователя</h2>
<form method="post">
<input type='hidden' name='create_user' value='1'>
<label>Логин: <input type='text' name='login' required></label><br/>
<label>ФИО: <input type='text' name='full_name' required></label><br/>
<label>Email: <input type='email' name='email' required></label><br/>
<label>Телефон: <input type='text' name='phone'></label><br/>
<label>Пароль: <input type='password' name='password' required></label><br/>
<label>Роль:
<select name='role'>
<option value='user'>Пользователь</option>
<option value='admin'>Админ</option>
</select></label><br/>
<button type='submit'>Создать пользователя</button>
</form>

<p><a href="/admin.php">Вернуться в панель администратора</a></p>
</section>

</body>
</html>