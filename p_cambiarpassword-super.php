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

$sql = "select * from tbsuperusuario
        where codigo='$codigoSuper'";

$f = mysqli_query($cn, $sql);
$r = mysqli_num_rows($f) > 0 ? mysqli_fetch_assoc($f) : null;

if ($r === null || !password_verify($actual, $r["password"])) {
    header("location: cambiarpassword-super.php?error=actual");
    exit();
}

$nuevaHash = password_hash($nueva, PASSWORD_DEFAULT);
$nuevaSQL = mysqli_real_escape_string($cn, $nuevaHash);

$sqlActualizar = "update tbsuperusuario
                  set password='$nuevaSQL'
                  where codigo='$codigoSuper'";

mysqli_query($cn, $sqlActualizar);

header("location: cambiarpassword-super.php?ok=1");
exit();
?>