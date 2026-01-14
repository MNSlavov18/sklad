<?php
session_start();
session_destroy();
setcookie('remember_user', '', time() - 3600, "/"); // Трием бисквитката
header("Location: login.php");
exit;