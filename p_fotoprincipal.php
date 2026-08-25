<?php
include("auth.php");
include("conexion.php");

$codigo = $_SESSION["codigo"];
$codigoSQL = mysqli_real_escape_string($cn, $codigo);

if (!isset($_GET["foto"])) {
    header("location: imagenperfil.php");
    exit();
}

$codigoFoto = mysqli_real_escape_string($cn, $_GET["foto"]);

/* Verificar que la foto pertenece al usuario logueado */
$sqlVerificar = "select * from tbfoto where codigo='$codigoFoto' and codigo_persona='$codigoSQL'";
$fVerificar = mysqli_query($cn, $sqlVerificar);

if (mysqli_num_rows($fVerificar) == 0) {
    header("location: imagenperfil.php");
    exit();
}

$sqlQuitarPrincipal = "update tbfoto set principal='N' where codigo_persona='$codigoSQL'";
mysqli_query($cn, $sqlQuitarPrincipal);

$sqlPonerPrincipal = "update tbfoto set principal='S' where codigo='$codigoFoto' and codigo_persona='$codigoSQL'";
mysqli_query($cn, $sqlPonerPrincipal);

header("location: imagenperfil.php?ok=1");
exit();
?>
