<?php
include("authsuper.php");
include("conexion.php");

$codigoSuper = $_SESSION["codigosuper"];

$actual = trim($_POST["pwdactual"]);
$nueva = trim($_POST["pwdnueva"]);
$repetir = trim($_POST["pwdrepetir"]);

if ($actual == "" || $nueva == "" || $repetir == "") {
    header("location: cambiarpassword-super.php?error=vacio");
    exit();
}

if ($nueva != $repetir) {
    header("location: cambiarpassword-super.php?error=diferente");
    exit();
}

if (strlen($nueva) != 8) {
    header("location: cambiarpassword-super.php?error=longitud");
    exit();
}

if ($actual == $nueva) {
    header("location: cambiarpassword-super.php?error=igual");
    exit();
}

$actualSQL = mysqli_real_escape_string($cn, $actual);
$nuevaSQL = mysqli_real_escape_string($cn, $nueva);

$sql = "select * from tbsuperusuario
        where codigo='$codigoSuper'
        and password='$actualSQL'";

$f = mysqli_query($cn, $sql);

if (mysqli_num_rows($f) == 0) {
    header("location: cambiarpassword-super.php?error=actual");
    exit();
}

$sqlActualizar = "update tbsuperusuario
                  set password='$nuevaSQL'
                  where codigo='$codigoSuper'";

mysqli_query($cn, $sqlActualizar);

header("location: cambiarpassword-super.php?ok=1");
exit();
?>