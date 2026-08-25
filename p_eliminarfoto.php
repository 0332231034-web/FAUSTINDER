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

$sqlFoto = "select * from tbfoto where codigo='$codigoFoto' and codigo_persona='$codigoSQL'";
$fFoto = mysqli_query($cn, $sqlFoto);

if (mysqli_num_rows($fFoto) == 0) {
    header("location: imagenperfil.php");
    exit();
}

$foto = mysqli_fetch_assoc($fFoto);
$eraPrincipal = ($foto["principal"] == "S");

/* Borrar el archivo físico si existe */
if (file_exists($foto["ruta"])) {
    unlink($foto["ruta"]);
}

$sqlEliminar = "delete from tbfoto where codigo='$codigoFoto' and codigo_persona='$codigoSQL'";
mysqli_query($cn, $sqlEliminar);

/* Si la foto eliminada era la principal, se asigna otra automáticamente */
if ($eraPrincipal) {

    $sqlSiguiente = "select codigo from tbfoto where codigo_persona='$codigoSQL' order by codigo asc limit 1";
    $fSiguiente = mysqli_query($cn, $sqlSiguiente);

    if (mysqli_num_rows($fSiguiente) > 0) {

        $siguiente = mysqli_fetch_assoc($fSiguiente);
        $codigoSiguienteSQL = mysqli_real_escape_string($cn, $siguiente["codigo"]);

        $sqlNuevaPrincipal = "update tbfoto set principal='S' where codigo='$codigoSiguienteSQL' and codigo_persona='$codigoSQL'";
        mysqli_query($cn, $sqlNuevaPrincipal);
    }
}

header("location: imagenperfil.php?ok=1");
exit();
?>
