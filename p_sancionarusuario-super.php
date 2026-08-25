<?php
include("authsuper.php");
include("conexion.php");

$codigoPersona = mysqli_real_escape_string($cn, $_POST["codigo"]);
$accion = trim($_POST["cboaccion"]);
$motivo = trim($_POST["txtmotivo"]);

if ($codigoPersona == "" || $accion == "" || $motivo == "") {
    header("location: sancionarusuario-super.php?codigo=$codigoPersona&error=vacio");
    exit();
}

$motivoSQL = mysqli_real_escape_string($cn, $motivo);
$hoy = date("Y-m-d");

if ($accion == "ACTIVAR") {

    $sql = "update tbpersona
            set estado='A',
                fechafin_inactivo=NULL,
                motivo_inactivo=''
            where codigo='$codigoPersona'";

    mysqli_query($cn, $sql);

} else if ($accion == "BAJA_INDEFINIDA") {

    $sql = "update tbpersona
            set estado='I',
                fechafin_inactivo=NULL,
                motivo_inactivo='$motivoSQL'
            where codigo='$codigoPersona'";

    mysqli_query($cn, $sql);

} else {

    $dias = 0;

    if ($accion == "INACTIVAR_1") {
        $dias = 1;
    } else if ($accion == "INACTIVAR_2") {
        $dias = 2;
    } else if ($accion == "INACTIVAR_3") {
        $dias = 3;
    } else if ($accion == "INACTIVAR_7") {
        $dias = 7;
    } else if ($accion == "INACTIVAR_15") {
        $dias = 15;
    } else if ($accion == "INACTIVAR_30") {
        $dias = 30;
    }

    if ($dias == 0) {
        header("location: sancionarusuario-super.php?codigo=$codigoPersona&error=vacio");
        exit();
    }

    $fechaFin = date("Y-m-d", strtotime("+$dias days"));

    $sql = "update tbpersona
            set estado='I',
                fechafin_inactivo='$fechaFin',
                motivo_inactivo='$motivoSQL'
            where codigo='$codigoPersona'";

    mysqli_query($cn, $sql);
}

$fechaAtencion = date("Y-m-d");

$sqlReportes = "update tbreporte
                set estado='ATENDIDO',
                    respuesta='$motivoSQL',
                    fecha_atencion='$fechaAtencion'
                where reportado='$codigoPersona'
                and estado='PENDIENTE'";

mysqli_query($cn, $sqlReportes);

header("location: sancionarusuario-super.php?codigo=$codigoPersona&ok=1");
exit();
?>
