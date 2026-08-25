<?php
include("auth.php");
include("conexion.php");

$codigo = $_SESSION["codigo"];

$correo = trim($_POST["txtcorreo"]);
$celular = trim($_POST["txtcelular"]);
$escuela = trim($_POST["cboescuela"]);
$sexo = trim($_POST["cbosexo"]);
$descripcion = trim($_POST["txtdescripcion"]);

if (!isset($_POST["cbotipo"])) {
    header("location: actualizardatos.php?error=tipo");
    exit();
}

$tiposSeleccionados = $_POST["cbotipo"];
$tipo = implode(", ", $tiposSeleccionados);

if ($correo == "" || $celular == "" || $escuela == "" || $sexo == "" || $tipo == "" || $descripcion == "") {
    header("location: actualizardatos.php?error=vacio");
    exit();
}

$correoSQL = mysqli_real_escape_string($cn, $correo);
$celularSQL = mysqli_real_escape_string($cn, $celular);
$escuelaSQL = mysqli_real_escape_string($cn, $escuela);
$sexoSQL = mysqli_real_escape_string($cn, $sexo);
$tipoSQL = mysqli_real_escape_string($cn, $tipo);
$descripcionSQL = mysqli_real_escape_string($cn, $descripcion);

$sql = "update tbpersona
        set correo='$correoSQL',
            celular='$celularSQL',
            escuela='$escuelaSQL',
            sexo='$sexoSQL',
            tipo='$tipoSQL',
            descripcion='$descripcionSQL'
        where codigo='$codigo'";

mysqli_query($cn, $sql);

header("location: principal.php");
exit();
?>