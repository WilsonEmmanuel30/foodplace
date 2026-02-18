<?php
// Adding pagetitle and header
$pagetitle = "Welcome to foodplace";
require_once "assets/header.php";
$password = "Qwertyuiop@1";
echo $password . "<br/>";
echo sha1($password) . "<br/>";
echo md5($password) . "<br/>";
echo password_hash($password, PASSWORD_DEFAULT) . "<br/>";
?>
<h1 class="text-3xl font-bold underline">Hello World</h1>
