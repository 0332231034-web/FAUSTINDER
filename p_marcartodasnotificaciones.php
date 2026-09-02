<?php
include("auth.php");
include("conexion.php");

$codigoUsuario = $_SESSION["codigo"];
$codigoUsuarioSQL = mysqli_real_escape_string($cn, $codigoUsuario);

$sqlMarcarTodo = "update tbnotificacion set leida='S'
                  where codigo_persona='$codigoUsuarioSQL' and leida='N'";
mysqli_query($cn, $sqlMarcarTodo);

echo "OK";
?>
