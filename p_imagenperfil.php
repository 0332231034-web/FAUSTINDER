<?php
include("auth.php");
include("conexion.php");

$codigo = $_SESSION["codigo"];
$codigoSQL = mysqli_real_escape_string($cn, $codigo);

if (!isset($_FILES["archivo"]) || $_FILES["archivo"]["name"] == "") {
    header("location: imagenperfil.php?error=vacio");
    exit();
}

$sqlContar = "select count(*) as total from tbfoto where codigo_persona='$codigoSQL'";
$fContar = mysqli_query($cn, $sqlContar);
$rContar = mysqli_fetch_assoc($fContar);

if ($rContar["total"] >= 5) {
    header("location: imagenperfil.php?error=limite");
    exit();
}

$nombreArchivo = $_FILES["archivo"]["name"];
$rutaTemporal = $_FILES["archivo"]["tmp_name"];

$partes = explode(".", $nombreArchivo);
$extension = strtolower(end($partes));

if ($extension != "png") {
    header("location: imagenperfil.php?error=formato");
    exit();
}

if (!file_exists("img")) {
    mkdir("img");
}

$nombreUnico = "perfil_" . $codigo . "_" . uniqid() . ".png";
$rutaDestino = "img/" . $nombreUnico;

if (!move_uploaded_file($rutaTemporal, $rutaDestino)) {
    header("location: imagenperfil.php?error=subida");
    exit();
}

/* La primera foto que sube el usuario queda como principal automáticamente */
$esPrincipal = ($rContar["total"] == 0) ? "S" : "N";

$rutaDestinoSQL = mysqli_real_escape_string($cn, $rutaDestino);

$sqlInsertar = "insert into tbfoto (codigo_persona, ruta, principal)
                values ('$codigoSQL', '$rutaDestinoSQL', '$esPrincipal')";

mysqli_query($cn, $sqlInsertar);

header("location: imagenperfil.php?ok=1");
exit();
?>
