<?php
include("authsuper.php");

$codigoSuper = $_SESSION["codigosuper"];

if (!isset($_FILES["archivo"]) || $_FILES["archivo"]["name"] == "") {
    header("location: imagenperfil-super.php?error=vacio");
    exit();
}

$nombreArchivo = $_FILES["archivo"]["name"];
$rutaTemporal = $_FILES["archivo"]["tmp_name"];

$partes = explode(".", $nombreArchivo);
$extension = strtolower(end($partes));

if ($extension != "png") {
    header("location: imagenperfil-super.php?error=formato");
    exit();
}

if (!file_exists("img")) {
    mkdir("img");
}

$rutaDestino = "img/super_" . $codigoSuper . ".png";

if (!move_uploaded_file($rutaTemporal, $rutaDestino)) {
    header("location: imagenperfil-super.php?error=subida");
    exit();
}

header("location: imagenperfil-super.php?ok=1");
exit();
?>