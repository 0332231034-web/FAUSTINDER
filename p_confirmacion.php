<?php
include("auth.php");
include("conexion.php");

$codigoUsuario = $_SESSION["codigo"];

if (!isset($_GET["codigo"])) {
    header("location: buscaramigos.php");
    exit();
}

$codigoPersona = $_GET["codigo"];

if ($codigoPersona == $codigoUsuario) {
    header("location: buscaramigos.php");
    exit();
}

$codigoUsuarioSQL = mysqli_real_escape_string($cn, $codigoUsuario);
$codigoPersonaSQL = mysqli_real_escape_string($cn, $codigoPersona);

$sqlPersona = "select * from tbpersona where codigo='$codigoPersonaSQL'";
$fPersona = mysqli_query($cn, $sqlPersona);

if (mysqli_num_rows($fPersona) == 0) {
    header("location: buscaramigos.php");
    exit();
}

$fecha = date("Y-m-d");

$sqlExiste = "select * from tbmatch
              where (solicitante='$codigoUsuarioSQL' and receptor='$codigoPersonaSQL')
              or (solicitante='$codigoPersonaSQL' and receptor='$codigoUsuarioSQL')";

$fExiste = mysqli_query($cn, $sqlExiste);

if (mysqli_num_rows($fExiste) > 0) {

    $match = mysqli_fetch_assoc($fExiste);

    if ($match["estado"] == "RECHAZADO") {

        $sqlActualizar = "update tbmatch
                          set solicitante='$codigoUsuarioSQL',
                              receptor='$codigoPersonaSQL',
                              estado='PENDIENTE',
                              fecha_solicitud='$fecha',
                              fecha_respuesta=NULL
                          where codigo='" . $match["codigo"] . "'";

        mysqli_query($cn, $sqlActualizar);

        header("location: vermatch.php");
        exit();
    }

    if ($match["estado"] == "PENDIENTE" && $match["solicitante"] == $codigoPersona) {

        $sqlAceptar = "update tbmatch
                       set estado='ACEPTADO',
                           fecha_respuesta='$fecha'
                       where codigo='" . $match["codigo"] . "'";

        mysqli_query($cn, $sqlAceptar);

        $sqlNick = "select nick from tbpersona where codigo='$codigoUsuarioSQL'";
        $fNick = mysqli_query($cn, $sqlNick);
        $rNick = mysqli_fetch_assoc($fNick);
        $nickQueAcepta = $rNick["nick"];

        $mensajeNotif = mysqli_real_escape_string($cn, "¡" . $nickQueAcepta . " aceptó tu solicitud de match! Ya pueden chatear.");

        $sqlNotif = "insert into tbnotificacion (codigo_persona, tipo, mensaje, codigo_relacionado, leida)
                     values ('$codigoPersonaSQL', 'MATCH_NUEVO', '$mensajeNotif', '$codigoUsuarioSQL', 'N')";

        mysqli_query($cn, $sqlNotif);

        header("location: vermatch.php");
        exit();
    }

    header("location: vermatch.php");
    exit();
}

$sqlNuevo = "insert into tbmatch
             (solicitante, receptor, estado, fecha_solicitud)
             values
             ('$codigoUsuarioSQL', '$codigoPersonaSQL', 'PENDIENTE', '$fecha')";

mysqli_query($cn, $sqlNuevo);

// Notificación de solicitud pendiente para el receptor
$sqlNickSolicitante = "select nick from tbpersona where codigo='$codigoUsuarioSQL'";
$fNickSolicitante = mysqli_query($cn, $sqlNickSolicitante);
$rNickSolicitante = mysqli_fetch_assoc($fNickSolicitante);
$nickSolicitante = $rNickSolicitante["nick"];

$mensajeNotif = mysqli_real_escape_string($cn, $nickSolicitante . " te envió una solicitud de match.");

$sqlNotif = "insert into tbnotificacion (codigo_persona, tipo, mensaje, codigo_relacionado, leida)
             values ('$codigoPersonaSQL', 'SOLICITUD_MATCH', '$mensajeNotif', '$codigoUsuarioSQL', 'N')";

mysqli_query($cn, $sqlNotif);

header("location: vermatch.php");
exit();
?>