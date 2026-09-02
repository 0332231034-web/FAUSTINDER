<?php
include("auth.php");
include("conexion.php");

$codigoUsuario = $_SESSION["codigo"];
$codigoUsuarioSQL = mysqli_real_escape_string($cn, $codigoUsuario);

if (!isset($_POST["match"])) {
    exit();
}

$codigoMatch = mysqli_real_escape_string($cn, $_POST["match"]);

// Validar que el match existe, está ACEPTADO, y que el usuario actual participa en él
$sqlMatch = "select * from tbmatch where codigo='$codigoMatch' and estado='ACEPTADO'
             and (solicitante='$codigoUsuarioSQL' or receptor='$codigoUsuarioSQL')";
$fMatch = mysqli_query($cn, $sqlMatch);

if (mysqli_num_rows($fMatch) == 0) {
    exit();
}

try {
    $sqlActualizar = "update tbpersona set escribiendo_match='$codigoMatch', escribiendo_fecha=NOW()
                       where codigo='$codigoUsuarioSQL'";
    mysqli_query($cn, $sqlActualizar);
} catch (Throwable $e) {
    // La migración de escribiendo_match / escribiendo_fecha todavía no se corrió; se ignora.
}

echo "OK";
?>
