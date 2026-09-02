<?php
include("auth.php");
include("conexion.php");

$codigoUsuario = $_SESSION["codigo"];
$codigoUsuarioSQL = mysqli_real_escape_string($cn, $codigoUsuario);

if (!isset($_POST["tipo"]) || !isset($_POST["relacionado"]) || !isset($_POST["fecha"])) {
    exit();
}

$tipoSQL = mysqli_real_escape_string($cn, $_POST["tipo"]);
$relacionadoSQL = mysqli_real_escape_string($cn, $_POST["relacionado"]);
$fechaSQL = mysqli_real_escape_string($cn, $_POST["fecha"]);

// Se identifica la notificación por tipo + relacionado + fecha (igual que el resto
// del sistema, que ya marca notificaciones sin depender de un id propio)
$sqlMarcar = "update tbnotificacion set leida='S'
              where codigo_persona='$codigoUsuarioSQL' and tipo='$tipoSQL'
              and codigo_relacionado='$relacionadoSQL' and fecha='$fechaSQL'";
mysqli_query($cn, $sqlMarcar);

echo "OK";
?>
