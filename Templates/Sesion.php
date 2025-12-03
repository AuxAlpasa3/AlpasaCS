<?php
include '../../api/db/conexion.php';
$VERSION = getenv('VERSION');
session_start();
if (!isset($_SESSION['current_user' . $VERSION])) {
  header("location: ../api/login/logout.php");
  exit;
}

$session_duration = 30 * 60;
$current_time = time();
if (isset($_SESSION['login_time' . $VERSION]) && ($current_time - $_SESSION['login_time' . $VERSION] > $session_duration)) {
  session_unset();
  session_destroy();
  header('location: ../api/login/logout.php');
  exit;
} else {
  $_SESSION['login_time' . $VERSION] = $current_time;
}

$user = $_SESSION['current_user' . $VERSION];
$IdUsuario = $_SESSION['idusuario' . $VERSION];
?>