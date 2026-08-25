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
$passwordSQL = mysqli_real_escape_string($cn, $password);

$sqlSuper = "select * from tbsuperusuario
             where usuario='$nickSQL'
             and password='$passwordSQL'";

$fSuper = mysqli_query($cn, $sqlSuper);

if (mysqli_num_rows($fSuper) > 0) {

    $super = mysqli_fetch_assoc($fSuper);

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
               where nick='$nickSQL'
               and password='$passwordSQL'";

$fUsuario = mysqli_query($cn, $sqlUsuario);

if (mysqli_num_rows($fUsuario) == 0) {
    header("location: login.php?error=datos");
    exit();
}

$r = mysqli_fetch_assoc($fUsuario);

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
