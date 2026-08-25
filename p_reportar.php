<?php
include("auth.php");
include("conexion.php");

$codigoUsuario = $_SESSION["codigo"];

if (!isset($_POST["codigoreportado"])) {
    header("location: vermatch.php");
    exit();
}

$codigoReportado = mysqli_real_escape_string($cn, $_POST["codigoreportado"]);
$tipoReporte = trim($_POST["cbotipo"]);
$motivo = trim($_POST["txtmotivo"]);

if ($codigoReportado == "" || $tipoReporte == "" || $motivo == "") {
    header("location: reportar.php?codigo=$codigoReportado&error=vacio");
    exit();
}

if ($codigoReportado == $codigoUsuario) {
    header("location: vermatch.php");
    exit();
}

$sqlPersona = "select * from tbpersona where codigo='$codigoReportado'";
$fPersona = mysqli_query($cn, $sqlPersona);

if (mysqli_num_rows($fPersona) == 0) {
    header("location: vermatch.php");
    exit();
}

$tipoReporteSQL = mysqli_real_escape_string($cn, $tipoReporte);
$motivoSQL = mysqli_real_escape_string($cn, $motivo);

$fecha = date("Y-m-d");
$estado = "PENDIENTE";

$codigoUsuarioSQL = mysqli_real_escape_string($cn, $codigoUsuario);

$sql = "insert into tbreporte
        (reportante, reportado, tipo_reporte, motivo, fecha_reporte, estado, respuesta, fecha_atencion)
        values
        ('$codigoUsuarioSQL', '$codigoReportado', '$tipoReporteSQL', '$motivoSQL', '$fecha', '$estado', '', NULL)";

mysqli_query($cn, $sql);

header("location: vermatch.php");
exit();
?>
