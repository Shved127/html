<?php
session_start();
session_unset();
session_destroy();
header('Location: ../naryshenia/index.php'); // перенаправление на главную страницу
exit;
?>