<?php
include("authsuper.php");
include("conexion.php");

if (!isset($_GET["codigo"])) {
    header("location: usuarios-super.php");
    exit();
}

$codigoPersona = mysqli_real_escape_string($cn, $_GET["codigo"]);

$sqlPersona = "select * from tbpersona where codigo='$codigoPersona'";
$fPersona = mysqli_query($cn, $sqlPersona);

if (mysqli_num_rows($fPersona) == 0) {
    header("location: usuarios-super.php");
    exit();
}

$sql = "update tbpersona
        set estado='A',
            fechafin_inactivo=NULL,
            motivo_inactivo=''
        where codigo='$codigoPersona'";

mysqli_query($cn, $sql);

header("location: usuarios-super.php");
exit();
?>
