<?php
include("conexion.php");

$nick = trim($_POST["txtnick"]);
$nombre = trim($_POST["txtnombre"]);
$apaterno = trim($_POST["txtapaterno"]);
$amaterno = trim($_POST["txtamaterno"]);
$correo = trim($_POST["txtcorreo"]);

if ($nick == "" || $nombre == "" || $apaterno == "" || $amaterno == "" || $correo == "") {
    header("location: register.php?error=vacio");
    exit();
}

$caracteres = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789";
$password = "";

for ($i = 0; $i < 8; $i++) {
    $posicion = rand(0, strlen($caracteres) - 1);
    $password = $password . substr($caracteres, $posicion, 1);
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);

$nickSQL = mysqli_real_escape_string($cn, $nick);
$nombreSQL = mysqli_real_escape_string($cn, $nombre);
$apaternoSQL = mysqli_real_escape_string($cn, $apaterno);
$amaternoSQL = mysqli_real_escape_string($cn, $amaterno);
$correoSQL = mysqli_real_escape_string($cn, $correo);
$passwordSQL = mysqli_real_escape_string($cn, $passwordHash);

$sqlNick = "select * from tbpersona
            where nick = '$nickSQL'";

$fNick = mysqli_query($cn, $sqlNick);

if (mysqli_num_rows($fNick) > 0) {
    header("location: register.php?error=nick");
    exit();
}

$sqlCorreo = "select * from tbpersona
              where correo = '$correoSQL'";

$fCorreo = mysqli_query($cn, $sqlCorreo);

if (mysqli_num_rows($fCorreo) > 0) {
    header("location: register.php?error=correo");
    exit();
}

$fecha = date("Y-m-d");
$estado = "A";

/* REGISTRAR PERSONA */
$sql = "insert into tbpersona
        (nick, nombre, apaterno, amaterno, correo, password, fecharegistro, estado)
        values
        ('$nickSQL', '$nombreSQL', '$apaternoSQL', '$amaternoSQL', '$correoSQL', '$passwordSQL', '$fecha', '$estado')";

mysqli_query($cn, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro exitoso</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<div class="contenedor-registro">

    <div class="tarjeta-password">

        <h1>Registro exitoso</h1>

        <p>
            Tu contraseña generada es:
        </p>

        <div class="password-generado">
            <?php echo $password; ?>
        </div>

        <a href="login.php" class="btn-registro">
            IR AL LOGIN
        </a>

    </div>

</div>

</body>
</html>