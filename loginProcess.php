<?php
error_reporting(E_ALL); ini_set('display_errors', 1);

$user = $_POST["user"];
$password = $_POST["password"];
$center = $_POST["center"];
if (in_array($center, ["neru","sacca"])){
  if (in_array($user, ["neru","sacca"]) && $password=="behappy") {
    header("Location: init.php?center=".$center);
  }else {
    header("Location: login.php?message=Usuario o contraseña incorrectos.");  
  }
} else {
  header("Location: login.php?message=Por favor, selecciona centro antes de continuar.");
}


?>