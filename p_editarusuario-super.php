<?php
include("authsuper.php");
include("conexion.php");

$codigoPersona = mysqli_real_escape_string($cn, $_POST["codigo"]);

$nick = trim($_POST["txtnick"]);
$correo = trim($_POST["txtcorreo"]);
$nombre = trim($_POST["txtnombre"]);
$apaterno = trim($_POST["txtapaterno"]);
$amaterno = trim($_POST["txtamaterno"]);
$celular = trim($_POST["txtcelular"]);
$escuela = trim($_POST["cboescuela"]);
$sexo = trim($_POST["cbosexo"]);
$descripcion = trim($_POST["txtdescripcion"]);
$nuevaPassword = trim($_POST["txtnuevapassword"]);

if (!isset($_POST["cbotipo"])) {
    header("location: editarusuario-super.php?codigo=$codigoPersona&error=vacio");
    exit();
}

$tipo = implode(", ", $_POST["cbotipo"]);

if ($nick == "" || $correo == "" || $nombre == "" || $apaterno == "" || $amaterno == "" || $celular == "" || $escuela == "" || $sexo == "" || $tipo == "" || $descripcion == "") {
    header("location: editarusuario-super.php?codigo=$codigoPersona&error=vacio");
    exit();
}

/* La contraseña es opcional: si el admin escribió algo, debe medir exactamente 8 caracteres */
if ($nuevaPassword != "" && strlen($nuevaPassword) != 8) {
    header("location: editarusuario-super.php?codigo=$codigoPersona&error=password");
    exit();
}

$nickSQL = mysqli_real_escape_string($cn, $nick);
$correoSQL = mysqli_real_escape_string($cn, $correo);
$nombreSQL = mysqli_real_escape_string($cn, $nombre);
$apaternoSQL = mysqli_real_escape_string($cn, $apaterno);
$amaternoSQL = mysqli_real_escape_string($cn, $amaterno);
$celularSQL = mysqli_real_escape_string($cn, $celular);
$escuelaSQL = mysqli_real_escape_string($cn, $escuela);
$sexoSQL = mysqli_real_escape_string($cn, $sexo);
$tipoSQL = mysqli_real_escape_string($cn, $tipo);
$descripcionSQL = mysqli_real_escape_string($cn, $descripcion);

$sqlNick = "select * from tbpersona
            where nick='$nickSQL'
            and codigo <> '$codigoPersona'";

$fNick = mysqli_query($cn, $sqlNick);

if (mysqli_num_rows($fNick) > 0) {
    header("location: editarusuario-super.php?codigo=$codigoPersona&error=nick");
    exit();
}

$sqlCorreo = "select * from tbpersona
              where correo='$correoSQL'
              and codigo <> '$codigoPersona'";

$fCorreo = mysqli_query($cn, $sqlCorreo);

if (mysqli_num_rows($fCorreo) > 0) {
    header("location: editarusuario-super.php?codigo=$codigoPersona&error=correo");
    exit();
}

$sql = "update tbpersona
        set nick='$nickSQL',
            correo='$correoSQL',
            nombre='$nombreSQL',
            apaterno='$apaternoSQL',
            amaterno='$amaternoSQL',
            celular='$celularSQL',
            escuela='$escuelaSQL',
            sexo='$sexoSQL',
            tipo='$tipoSQL',
            descripcion='$descripcionSQL'
        where codigo='$codigoPersona'";

mysqli_query($cn, $sql);

/* Si el admin escribió una nueva contraseña, se actualiza aparte (con hash) */
if ($nuevaPassword != "") {

    $nuevaPasswordHash = password_hash($nuevaPassword, PASSWORD_DEFAULT);
    $nuevaPasswordHashSQL = mysqli_real_escape_string($cn, $nuevaPasswordHash);

    $sqlPassword = "update tbpersona
                    set password='$nuevaPasswordHashSQL'
                    where codigo='$codigoPersona'";

    mysqli_query($cn, $sqlPassword);

    /* Como el admin le puso una contraseña nueva "de fábrica", se le
       vuelve a pedir que la cambie por una propia al iniciar sesión. */
    $archivoFlag = "password/cambio_" . $codigoPersona . ".txt";
    if (file_exists($archivoFlag)) {
        unlink($archivoFlag);
    }
}

header("location: editarusuario-super.php?codigo=$codigoPersona&ok=1");
exit();
?>
