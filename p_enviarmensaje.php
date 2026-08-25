<?php
include("auth.php");
include("conexion.php");

$codigoUsuario = $_SESSION["codigo"];
$codigoUsuarioSQL = mysqli_real_escape_string($cn, $codigoUsuario);

if (!isset($_POST["match"]) || !isset($_POST["mensaje"])) {
    exit();
}

$codigoMatch = mysqli_real_escape_string($cn, $_POST["match"]);
$mensajeTexto = trim($_POST["mensaje"]);

if ($mensajeTexto === "") {
    exit();
}

// Validar que el match existe, está ACEPTADO, y que el usuario actual participa en él
$sqlMatch = "select * from tbmatch where codigo='$codigoMatch' and estado='ACEPTADO'
             and (solicitante='$codigoUsuarioSQL' or receptor='$codigoUsuarioSQL')";
$fMatch = mysqli_query($cn, $sqlMatch);

if (mysqli_num_rows($fMatch) == 0) {
    exit();
}

$match = mysqli_fetch_assoc($fMatch);

// Determinar quién es el receptor del mensaje (el otro usuario del match)
if ($match["solicitante"] == $codigoUsuario) {
    $codigoReceptor = $match["receptor"];
} else {
    $codigoReceptor = $match["solicitante"];
}

$codigoReceptorSQL = mysqli_real_escape_string($cn, $codigoReceptor);
$mensajeSQL = mysqli_real_escape_string($cn, $mensajeTexto);

$sqlInsertar = "insert into tbmensaje (codigo_match, emisor, receptor, mensaje, fecha_envio, leido)
                values ('$codigoMatch', '$codigoUsuarioSQL', '$codigoReceptorSQL', '$mensajeSQL', now(), 'N')";

mysqli_query($cn, $sqlInsertar);

// Obtener el nick de quien envía, para armar el texto de la notificación
$sqlEmisor = "select nick from tbpersona where codigo='$codigoUsuarioSQL'";
$fEmisor = mysqli_query($cn, $sqlEmisor);
$emisorData = mysqli_fetch_assoc($fEmisor);
$nickEmisor = $emisorData["nick"];

$textoNotificacion = "Tienes un nuevo mensaje de " . $nickEmisor;
$textoNotificacionSQL = mysqli_real_escape_string($cn, $textoNotificacion);

$sqlNotificacion = "insert into tbnotificacion (codigo_persona, tipo, mensaje, codigo_relacionado, leida, fecha)
                    values ('$codigoReceptorSQL', 'MENSAJE', '$textoNotificacionSQL', '$codigoMatch', 'N', now())";

mysqli_query($cn, $sqlNotificacion);

echo "OK";
?>
