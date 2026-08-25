<?php
include("auth.php");
include("conexion.php");

$codigoUsuario = $_SESSION["codigo"];
$codigoUsuarioSQL = mysqli_real_escape_string($cn, $codigoUsuario);

if (!isset($_GET["match"])) {
    exit();
}

$codigoMatch = mysqli_real_escape_string($cn, $_GET["match"]);

$sqlMatch = "select * from tbmatch where codigo='$codigoMatch' and estado='ACEPTADO'
             and (solicitante='$codigoUsuarioSQL' or receptor='$codigoUsuarioSQL')";
$fMatch = mysqli_query($cn, $sqlMatch);

if (mysqli_num_rows($fMatch) == 0) {
    exit();
}

$sqlMarcarLeido = "update tbmensaje set leido='S'
                   where codigo_match='$codigoMatch' and receptor='$codigoUsuarioSQL'";
mysqli_query($cn, $sqlMarcarLeido);

$sqlMarcarNotifLeida = "update tbnotificacion set leida='S'
                        where tipo='MENSAJE' and codigo_relacionado='$codigoMatch'
                        and codigo_persona='$codigoUsuarioSQL'";
mysqli_query($cn, $sqlMarcarNotifLeida);

$sqlMensajes = "select * from tbmensaje where codigo_match='$codigoMatch' order by fecha_envio asc";
$fMensajes = mysqli_query($cn, $sqlMensajes);

while ($msj = mysqli_fetch_assoc($fMensajes)) {

    if ($msj["emisor"] == $codigoUsuario) {
        echo '<div class="burbuja-mensaje burbuja-propia">';
        echo '<p>' . nl2br(htmlspecialchars($msj["mensaje"])) . '</p>';
        echo '<span class="hora-mensaje">' . date("H:i", strtotime($msj["fecha_envio"])) . '</span>';
        echo '</div>';
    } else {
        echo '<div class="burbuja-mensaje burbuja-otro">';
        echo '<p>' . nl2br(htmlspecialchars($msj["mensaje"])) . '</p>';
        echo '<span class="hora-mensaje">' . date("H:i", strtotime($msj["fecha_envio"])) . '</span>';
        echo '</div>';
    }

}
?>