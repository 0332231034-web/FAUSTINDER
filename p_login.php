<?php
session_start();
include("conexion.php");

$nick = trim($_POST["txtnick"]);
$password = trim($_POST["txtpassword"]);

if ($nick == "" || $password == "") {
    header("location: login.php?error=vacio");
    exit();
}

$nickSQL = mysqli_real_escape_string($cn, $nick);

$sqlSuper = "select * from tbsuperusuario
             where usuario='$nickSQL'";

$fSuper = mysqli_query($cn, $sqlSuper);
$super = mysqli_num_rows($fSuper) > 0 ? mysqli_fetch_assoc($fSuper) : null;

if ($super !== null && password_verify($password, $super["password"])) {

    $_SESSION["authsuper"] = "1";
    $_SESSION["codigosuper"] = $super["codigo"];
    $_SESSION["usuariosuper"] = $super["usuario"];
    $_SESSION["nombresuper"] = $super["nombre"];
    $_SESSION["apaternosuper"] = $super["apaterno"];
    $_SESSION["amaternosuper"] = $super["amaterno"];

    header("location: principal-super.php");
    exit();
}


$sqlUsuario = "select * from tbpersona
               where nick='$nickSQL'";

$fUsuario = mysqli_query($cn, $sqlUsuario);
$r = mysqli_num_rows($fUsuario) > 0 ? mysqli_fetch_assoc($fUsuario) : null;

if ($r === null || !password_verify($password, $r["password"])) {
    header("location: login.php?error=datos");
    exit();
}

$codigoUsuario = $r["codigo"];
$hoy = date("Y-m-d");

if ($r["estado"] == "I") {

    if ($r["fechafin_inactivo"] != "" && $r["fechafin_inactivo"] != NULL) {

        if ($r["fechafin_inactivo"] < $hoy) {

            $sqlActivar = "update tbpersona
                           set estado='A',
                               fechafin_inactivo=NULL,
                               motivo_inactivo=''
                           where codigo='$codigoUsuario'";

            mysqli_query($cn, $sqlActivar);

        } else {

            $hasta = $r["fechafin_inactivo"];
            header("location: login.php?error=temporal&hasta=$hasta");
            exit();
        }

    } else {

        header("location: login.php?error=indefinido");
        exit();
    }
}

$_SESSION["auth"] = "1";
$_SESSION["codigo"] = $r["codigo"];
$_SESSION["nick"] = $r["nick"];
$_SESSION["nombre"] = $r["nombre"];
$_SESSION["apaterno"] = $r["apaterno"];
$_SESSION["amaterno"] = $r["amaterno"];

$sqlActualizarConexion = "update tbpersona set ultima_conexion=NOW() where codigo='$codigoUsuario'";
mysqli_query($cn, $sqlActualizarConexion);

header("location: principal.php");
exit();
?>
