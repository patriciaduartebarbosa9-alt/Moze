<?php
$host = "sql109.infinityfree.com";
$user = "if0_40439565_Moze";
$pass = "Mozept123";
$db   = "if0_40439565_Moze";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Erro na ligação: " . mysqli_connect_error());
}
?>