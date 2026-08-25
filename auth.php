<?php
session_start();
include("conexion.php");

if (!isset($_SESSION["auth"]) || $_SESSION["auth"] != "1") {
    header("location: login.php");
    exit();
}

$codigo = $_SESSION["codigo"];
$codigoSQL = mysqli_real_escape_string($cn, $codigo);

$sqlEstado = "select estado from tbpersona where codigo='$codigoSQL'";
$fEstado = mysqli_query($cn, $sqlEstado);

if (mysqli_num_rows($fEstado) == 0) {
    session_destroy();
    header("location: login.php");
    exit();
}

$rEstado = mysqli_fetch_assoc($fEstado);

if ($rEstado["estado"] == "I") {
    session_destroy();
    header("location: login.php?error=indefinido");
    exit();
}

$sqlActualizar = "update tbpersona set ultima_conexion=NOW() where codigo='$codigoSQL'";
mysqli_query($cn, $sqlActualizar);
?>