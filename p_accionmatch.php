<?php
include("auth.php");
include("conexion.php");

$codigoUsuario = $_SESSION["codigo"];

if (!isset($_GET["codigo"]) || !isset($_GET["accion"])) {
    header("location: vermatch.php");
    exit();
}

$codigoPersona = $_GET["codigo"];
$accion = $_GET["accion"];

if ($codigoPersona == $codigoUsuario) {
    header("location: vermatch.php");
    exit();
}

$codigoUsuarioSQL = mysqli_real_escape_string($cn, $codigoUsuario);
$codigoPersonaSQL = mysqli_real_escape_string($cn, $codigoPersona);

$sql = "select * from tbmatch
        where solicitante='$codigoPersonaSQL'
        and receptor='$codigoUsuarioSQL'
        and estado='PENDIENTE'";

$f = mysqli_query($cn, $sql);

if (mysqli_num_rows($f) == 0) {
    header("location: vermatch.php");
    exit();
}

$match = mysqli_fetch_assoc($f);
$fecha = date("Y-m-d");

if ($accion == "aceptar") {

    $sqlAceptar = "update tbmatch
                   set estado='ACEPTADO',
                       fecha_respuesta='$fecha'
                   where codigo='" . $match["codigo"] . "'";

    mysqli_query($cn, $sqlAceptar);

    // Obtener nick de quien acepta, para la notificación
    $sqlNick = "select nick from tbpersona where codigo='$codigoUsuarioSQL'";
    $fNick = mysqli_query($cn, $sqlNick);
    $rNick = mysqli_fetch_assoc($fNick);
    $nickQueAcepta = $rNick["nick"];

    // Notificación para quien solicitó originalmente
    $mensajeNotif = mysqli_real_escape_string($cn, "¡" . $nickQueAcepta . " aceptó tu solicitud de match! Ya pueden chatear.");

    $sqlNotif = "insert into tbnotificacion (codigo_persona, tipo, mensaje, codigo_relacionado, leida)
                 values ('$codigoPersonaSQL', 'MATCH_NUEVO', '$mensajeNotif', '$codigoUsuarioSQL', 'N')";

    mysqli_query($cn, $sqlNotif);

    header("location: vermatch.php");
    exit();
}

if ($accion == "rechazar") {

    $sqlRechazar = "update tbmatch
                    set estado='RECHAZADO',
                        fecha_respuesta='$fecha'
                    where codigo='" . $match["codigo"] . "'";

    mysqli_query($cn, $sqlRechazar);

    header("location: vermatch.php");
    exit();
}

if ($accion == "reportar") {
    header("location: reportar.php?codigo=$codigoPersonaSQL");
    exit();
}

header("location: vermatch.php");
exit();
?>