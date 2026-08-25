<?php
include("auth.php");
include("conexion.php");

$codigoUsuario = $_SESSION["codigo"];
$codigoUsuarioSQL = mysqli_real_escape_string($cn, $codigoUsuario);

$sqlUsuario = "select * from tbpersona where codigo='$codigoUsuarioSQL'";
$fUsuario = mysqli_query($cn, $sqlUsuario);
$usuario = mysqli_fetch_assoc($fUsuario);
$nombreCompleto = $usuario["nombre"] . " " . $usuario["apaterno"] . " " . $usuario["amaterno"];

$sqlMatches = "select * from tbmatch
               where (solicitante='$codigoUsuarioSQL' or receptor='$codigoUsuarioSQL')
               and estado='ACEPTADO'";

$fMatches = mysqli_query($cn, $sqlMatches);

$modoOscuro = isset($_COOKIE["modo_oscuro"]) && $_COOKIE["modo_oscuro"] == "1";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mensajes - FAUSTINder</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="css/modo-oscuro.css">
</head>
<body class="<?php echo $modoOscuro ? 'tema-oscuro' : ''; ?>">

<div class="panel-principal-faustinder">
    <div class="tarjeta-principal-faustinder">

        <div class="encabezado-faustinder">
            <div><h1>FAUSTINder</h1></div>
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
            <a href="buscaramigos.php">Buscar Amigos</a>
            <a href="vermatch.php">Ver Match</a>
            <a href="chats.php" class="activo-faustinder">Mensajes</a>
        </div>

        <div class="cuerpo-principal-faustinder">

            <h2>Bienvenido, <?php echo htmlspecialchars($nombreCompleto); ?></h2>
            <p>Tus conversaciones activas.</p>

            <?php if (mysqli_num_rows($fMatches) == 0) { ?>

                <div class="sin-resultados-amigos">
                    Todavía no tienes matches para chatear.
                </div>

            <?php } else { ?>

                <div class="lista-chats">

                    <?php while ($m = mysqli_fetch_assoc($fMatches)) { ?>

                        <?php
                        if ($m["solicitante"] == $codigoUsuario) {
                            $codigoOtro = $m["receptor"];
                        } else {
                            $codigoOtro = $m["solicitante"];
                        }

                        $codigoOtroSQL = mysqli_real_escape_string($cn, $codigoOtro);

                        $sqlOtro = "select * from tbpersona where codigo='$codigoOtroSQL'";
                        $fOtro = mysqli_query($cn, $sqlOtro);
                        $otro = mysqli_fetch_assoc($fOtro);

                        $sqlFotoOtro = "select ruta from tbfoto where codigo_persona='$codigoOtroSQL' and principal='S' limit 1";
                        $fFotoOtro = mysqli_query($cn, $sqlFotoOtro);
                        $tieneFotoOtro = mysqli_num_rows($fFotoOtro) > 0;
                        if ($tieneFotoOtro) {
                            $fotoOtroData = mysqli_fetch_assoc($fFotoOtro);
                            $fotoOtroWeb = $fotoOtroData["ruta"];
                        }

                        // Último mensaje entre ambos
                        $sqlUltimo = "select * from tbmensaje
                                      where codigo_match='" . $m["codigo"] . "'
                                      order by fecha_envio desc limit 1";
                        $fUltimo = mysqli_query($cn, $sqlUltimo);
                        $ultimoMensaje = mysqli_num_rows($fUltimo) > 0 ? mysqli_fetch_assoc($fUltimo) : null;

                        // No leídos para mí
                        $sqlNoLeidos = "select count(*) as total from tbmensaje
                                        where codigo_match='" . $m["codigo"] . "'
                                        and receptor='$codigoUsuarioSQL' and leido='N'";
                        $fNoLeidos = mysqli_query($cn, $sqlNoLeidos);
                        $rNoLeidos = mysqli_fetch_assoc($fNoLeidos);

                        // Última conexión del otro usuario
                        $ultimaConexionTexto = "Sin conexión reciente";
                        if ($otro["ultima_conexion"] != NULL) {
                            $minutos = (strtotime("now") - strtotime($otro["ultima_conexion"])) / 60;
                            if ($minutos < 5) {
                                $ultimaConexionTexto = "En línea";
                            } else if ($minutos < 60) {
                                $ultimaConexionTexto = "Activo hace " . floor($minutos) . " min";
                            } else if ($minutos < 1440) {
                                $ultimaConexionTexto = "Activo hace " . floor($minutos / 60) . " h";
                            } else {
                                $ultimaConexionTexto = "Activo hace " . floor($minutos / 1440) . " días";
                            }
                        }
                        ?>

                        <a href="chat.php?match=<?php echo $m["codigo"]; ?>" class="tarjeta-chat">

                            <div class="foto-chat-lista">
                                <?php if ($tieneFotoOtro) { ?>
                                    <img src="<?php echo $fotoOtroWeb . '?v=' . time(); ?>" alt="Foto">
                                <?php } else { ?>
                                    <div class="sin-foto-match">❤</div>
                                <?php } ?>
                            </div>

                            <div class="info-chat-lista">
                                <div class="nombre-chat-lista">
                                    <?php echo htmlspecialchars($otro["nick"]); ?>
                                    <?php if ($rNoLeidos["total"] > 0) { ?>
                                        <span class="badge-notificacion"><?php echo $rNoLeidos["total"]; ?></span>
                                    <?php } ?>
                                </div>

                                <div class="ultimo-mensaje-chat-lista">
                                    <?php
                                    if ($ultimoMensaje != null) {
                                        echo htmlspecialchars(mb_strimwidth($ultimoMensaje["mensaje"], 0, 50, "..."));
                                    } else {
                                        echo "Inicien la conversación";
                                    }
                                    ?>
                                </div>

                                <div class="ultima-conexion-chat-lista">
                                    <?php echo $ultimaConexionTexto; ?>
                                </div>
                            </div>

                        </a>

                    <?php } ?>

                </div>

            <?php } ?>

        </div>

    </div>
</div>

<script src="js/modo-oscuro.js"></script>

</body>
</html>