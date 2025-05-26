<?php
session_start();
session_unset();
session_destroy();
header('Location: /'); // перенаправление на главную страницу
exit;
?>