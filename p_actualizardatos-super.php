<?php
include("authsuper.php");
include("conexion.php");

$codigoSuper = $_SESSION["codigosuper"];

$usuario = trim($_POST["txtusuario"]);
$celular = trim($_POST["txtcelular"]);
$nombre = trim($_POST["txtnombre"]);
$apaterno = trim($_POST["txtapaterno"]);
$amaterno = trim($_POST["txtamaterno"]);

if ($usuario == "" || $celular == "" || $nombre == "" || $apaterno == "" || $amaterno == "") {
    header("location: actualizardatos-super.php?error=vacio");
    exit();
}

$usuarioSQL = mysqli_real_escape_string($cn, $usuario);
$celularSQL = mysqli_real_escape_string($cn, $celular);
$nombreSQL = mysqli_real_escape_string($cn, $nombre);
$apaternoSQL = mysqli_real_escape_string($cn, $apaterno);
$amaternoSQL = mysqli_real_escape_string($cn, $amaterno);

$sqlUsuario = "select * from tbsuperusuario
               where usuario='$usuarioSQL'
               and codigo <> '$codigoSuper'";

$fUsuario = mysqli_query($cn, $sqlUsuario);

if (mysqli_num_rows($fUsuario) > 0) {
    header("location: actualizardatos-super.php?error=usuario");
    exit();
}

$sql = "update tbsuperusuario
        set usuario='$usuarioSQL',
            celular='$celularSQL',
            nombre='$nombreSQL',
            apaterno='$apaternoSQL',
            amaterno='$amaternoSQL'
        where codigo='$codigoSuper'";

mysqli_query($cn, $sql);

$_SESSION["usuariosuper"] = $usuario;
$_SESSION["nombresuper"] = $nombre;
$_SESSION["apaternosuper"] = $apaterno;
$_SESSION["amaternosuper"] = $amaterno;

header("location: actualizardatos-super.php?ok=1");
exit();
?>