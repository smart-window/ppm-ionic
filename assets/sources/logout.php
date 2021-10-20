<?php
if (isset($_SESSION['user'])) {
    unset($_SESSION['user']);
    setcookie("user", 0, time() - 3600); 
}
if (isset($_SESSION['new_user'])) {
    unset($_SESSION['new_user']);
}
$domain = $_SERVER["SERVER_NAME"];
header('Location: http://'.$domain);