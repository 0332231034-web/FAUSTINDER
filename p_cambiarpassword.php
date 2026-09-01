<?php
include("auth.php");
include("conexion.php");
/** @var mysqli $cn */

$codigo = $_SESSION["codigo"];

$actual = trim($_POST["pwdactual"]);
$nueva = trim($_POST["pwdpass"]);
$repetir = trim($_POST["pwdrepass"]);

if ($actual == "" || $nueva == "" || $repetir == "") {
    header("location: cambiarpassword.php?error=vacio");
    exit();
}

if ($nueva != $repetir) {
    header("location: cambiarpassword.php?error=diferente");
    exit();
}

if (strlen($nueva) != 8) {
    header("location: cambiarpassword.php?error=longitud");
    exit();
}

/* Validar contraseña actual */
$sql = "select * from tbpersona
        where codigo='$codigo'";

$f = mysqli_query($cn, $sql);
$r = mysqli_num_rows($f) > 0 ? mysqli_fetch_assoc($f) : null;

if ($r === null || !password_verify($actual, $r["password"])) {
    header("location: cambiarpassword.php?error=actual");
    exit();
}

$nuevaHash = password_hash($nueva, PASSWORD_DEFAULT);
$nuevaSQL = mysqli_real_escape_string($cn, $nuevaHash);

$sqlActualizar = "update tbpersona
                  set password='$nuevaSQL'
                  where codigo='$codigo'";

mysqli_query($cn, $sqlActualizar);


if (!file_exists("password")) {
    mkdir("password");
}

$archivo = "password/cambio_" . $codigo . ".txt";
file_put_contents($archivo, "SI");

header("location: principal.php");
exit();
?>