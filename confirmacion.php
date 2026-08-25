<?php
include("auth.php");
include("conexion.php");

$codigoUsuario = $_SESSION["codigo"];

if (!isset($_GET["codigo"])) {
    header("location: buscaramigos.php");
    exit();
}

$codigoPersona = mysqli_real_escape_string($cn, $_GET["codigo"]);

if ($codigoPersona == $codigoUsuario) {
    header("location: buscaramigos.php");
    exit();
}

$sqlUsuario = "select * from tbpersona where codigo='$codigoUsuario'";
$fUsuario = mysqli_query($cn, $sqlUsuario);
$usuario = mysqli_fetch_assoc($fUsuario);

$nombreUsuario = $usuario["nombre"] . " " . $usuario["apaterno"] . " " . $usuario["amaterno"];

$sql = "select * from tbpersona where codigo='$codigoPersona' and estado='A'";
$f = mysqli_query($cn, $sql);

if (mysqli_num_rows($f) == 0) {
    header("location: buscaramigos.php");
    exit();
}

$r = mysqli_fetch_assoc($f);

$nombrePersona = $r["nombre"] . " " . $r["apaterno"] . " " . $r["amaterno"];

$sqlFotoPersona = "select ruta from tbfoto where codigo_persona='$codigoPersona' and principal='S' limit 1";
$fFotoPersona = mysqli_query($cn, $sqlFotoPersona);
$tieneFotoPersona = mysqli_num_rows($fFotoPersona) > 0;

if ($tieneFotoPersona) {
    $fotoPersonaData = mysqli_fetch_assoc($fFotoPersona);
    $fotoWeb = $fotoPersonaData["ruta"];
}

if ($r["sexo"] == "M") {
    $sexoTexto = "Masculino";
} else if ($r["sexo"] == "F") {
    $sexoTexto = "Femenino";
} else {
    $sexoTexto = $r["sexo"];
}

/* Verificar repetido con archivo */
$codigoUsuarioSQL = mysqli_real_escape_string($cn, $codigoUsuario);
$codigoPersonaSQL = mysqli_real_escape_string($cn, $codigoPersona);

$sqlRelacion = "select * from tbmatch
                where (solicitante='$codigoUsuarioSQL' and receptor='$codigoPersonaSQL')
                or (solicitante='$codigoPersonaSQL' and receptor='$codigoUsuarioSQL')";

$fRelacion = mysqli_query($cn, $sqlRelacion);

$yaSolicito = false;
$yaEsMatch = false;

if (mysqli_num_rows($fRelacion) > 0) {

    $relacion = mysqli_fetch_assoc($fRelacion);

    if ($relacion["estado"] == "ACEPTADO") {
        $yaEsMatch = true;
    } else if ($relacion["estado"] == "PENDIENTE" && $relacion["solicitante"] == $codigoUsuario) {
        $yaSolicito = true;
    }
}

$modoOscuro = isset($_COOKIE["modo_oscuro"]) && $_COOKIE["modo_oscuro"] == "1";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmación - FAUSTINDER</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="css/modo-oscuro.css">
</head>
<body class="<?php echo $modoOscuro ? 'tema-oscuro' : ''; ?>">

<div class="panel-principal-faustinder">

    <div class="tarjeta-principal-faustinder">

        <div class="encabezado-faustinder">
            <div>
                <h1>FAUSTINDER</h1>
            </div>

            <a href="cerrarsesion.php" class="btn-salir-faustinder">Cerrar sesión</a>
        </div>

        <div class="menu-faustinder-moderno">

            <a href="principal.php">Principal</a>

            <div class="dropdown-datos">
                <a href="#" class="btn-dropdown">Tus Datos ▾</a>

                <div class="contenido-dropdown">
                    <a href="actualizardatos.php">Actualizar datos</a>
                    <a href="imagenperfil.php">Insertar foto de perfil</a>
                    <a href="cambiarpassword.php">Cambiar password</a>
                </div>
            </div>

            <a href="buscaramigos.php" class="activo-faustinder">Buscar Amigos</a>
            <a href="vermatch.php">Ver Match</a>
            <a href="chats.php">Mensajes</a>

        </div>

        <div class="cuerpo-principal-faustinder">

            <h2>Bienvenido, <?php echo htmlspecialchars($nombreUsuario); ?></h2>

            <div class="tarjeta-confirmacion-match">

                <div class="foto-confirmacion-match">

                    <?php if ($tieneFotoPersona) { ?>
                        <img src="<?php echo $fotoWeb . '?v=' . time(); ?>" alt="Foto">
                    <?php } else { ?>
                        <div class="sin-foto-confirmacion">❤</div>
                    <?php } ?>

                </div>

                <div class="datos-confirmacion-match">

                    <table>
                        <tr>
                            <th>Nick</th>
                            <td><?php echo htmlspecialchars($r["nick"]); ?></td>
                        </tr>

                        <tr>
                            <th>Nombre</th>
                            <td><?php echo htmlspecialchars($nombrePersona); ?></td>
                        </tr>

                        <tr>
                            <th>Escuela</th>
                            <td><?php echo htmlspecialchars($r["escuela"]); ?></td>
                        </tr>

                        <tr>
                            <th>Sexo</th>
                            <td><?php echo htmlspecialchars($sexoTexto); ?></td>
                        </tr>

                        <tr>
                            <th>Tipo</th>
                            <td><?php echo htmlspecialchars($r["tipo"]); ?></td>
                        </tr>
                    </table>

                </div>

            </div>

            <div class="descripcion-confirmacion">
                <?php echo htmlspecialchars($r["descripcion"]); ?>
            </div>

            <div class="botones-confirmacion-match">

                <?php if ($yaEsMatch == true) { ?>

                    <span class="btn-match-realizado">
                        Ya tienen match, revisa Ver Match
                    </span>

                <?php } else if ($yaSolicito == false) { ?>

                    <a href="p_confirmacion.php?codigo=<?php echo $codigoPersona; ?>" class="btn-hacer-match">
                        Hacer Match
                    </a>

                <?php } else { ?>

                    <span class="btn-match-realizado">
                        Match ya solicitado
                    </span>

                <?php } ?>

                <a href="buscaramigos.php" class="btn-cancelar-match">
                    Cancelar
                </a>

            </div>

        </div>

    </div>

</div>

<script src="js/modo-oscuro.js"></script>

</body>
</html>
