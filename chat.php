<?php
include("auth.php");
include("conexion.php");

$codigoUsuario = $_SESSION["codigo"];
$codigoUsuarioSQL = mysqli_real_escape_string($cn, $codigoUsuario);

if (!isset($_GET["match"])) {
    header("location: chats.php");
    exit();
}

$codigoMatch = mysqli_real_escape_string($cn, $_GET["match"]);

$sqlMatch = "select * from tbmatch where codigo='$codigoMatch' and estado='ACEPTADO'
             and (solicitante='$codigoUsuarioSQL' or receptor='$codigoUsuarioSQL')";
$fMatch = mysqli_query($cn, $sqlMatch);

if (mysqli_num_rows($fMatch) == 0) {
    header("location: chats.php");
    exit();
}

$match = mysqli_fetch_assoc($fMatch);

if ($match["solicitante"] == $codigoUsuario) {
    $codigoOtro = $match["receptor"];
} else {
    $codigoOtro = $match["solicitante"];
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

// Marcar como leídos los mensajes que me enviaron
$sqlMarcarLeido = "update tbmensaje set leido='S'
                   where codigo_match='$codigoMatch' and receptor='$codigoUsuarioSQL'";
mysqli_query($cn, $sqlMarcarLeido);

// Marcar como leídas las notificaciones de mensaje de este match
$sqlMarcarNotifLeida = "update tbnotificacion set leida='S'
                        where tipo='MENSAJE' and codigo_relacionado='$codigoMatch'
                        and codigo_persona='$codigoUsuarioSQL'";
mysqli_query($cn, $sqlMarcarNotifLeida);

$sqlMensajes = "select * from tbmensaje where codigo_match='$codigoMatch' order by fecha_envio asc";
$fMensajes = mysqli_query($cn, $sqlMensajes);

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

$modoOscuro = isset($_COOKIE["modo_oscuro"]) && $_COOKIE["modo_oscuro"] == "1";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Chat con <?php echo htmlspecialchars($otro["nick"]); ?> - FAUSTINder</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="css/modo-oscuro.css">
</head>
<body class="<?php echo $modoOscuro ? 'tema-oscuro' : ''; ?>">

<div class="panel-principal-faustinder">
    <div class="tarjeta-principal-faustinder">

        <div class="encabezado-chat">

            <a href="chats.php" class="btn-volver-chat">← Volver</a>

            <div class="foto-chat-lista">
                <?php if ($tieneFotoOtro) { ?>
                    <img src="<?php echo $fotoOtroWeb . '?v=' . time(); ?>" alt="Foto">
                <?php } else { ?>
                    <div class="sin-foto-match">❤</div>
                <?php } ?>
            </div>

            <div class="info-encabezado-chat">
                <div class="nombre-encabezado-chat"><?php echo htmlspecialchars($otro["nick"]); ?></div>
                <div class="estado-encabezado-chat" id="estadoConexion"><?php echo $ultimaConexionTexto; ?></div>
            </div>

        </div>

        <div class="cuerpo-chat" id="cuerpoChat">

            <?php while ($msj = mysqli_fetch_assoc($fMensajes)) { ?>

                <?php if ($msj["emisor"] == $codigoUsuario) { ?>

                    <div class="burbuja-mensaje burbuja-propia">
                        <p><?php echo nl2br(htmlspecialchars($msj["mensaje"])); ?></p>
                        <span class="hora-mensaje"><?php echo date("H:i", strtotime($msj["fecha_envio"])); ?></span>
                    </div>

                <?php } else { ?>

                    <div class="burbuja-mensaje burbuja-otro">
                        <p><?php echo nl2br(htmlspecialchars($msj["mensaje"])); ?></p>
                        <span class="hora-mensaje"><?php echo date("H:i", strtotime($msj["fecha_envio"])); ?></span>
                    </div>

                <?php } ?>

            <?php } ?>

        </div>

        <form id="formEnviarMensaje" class="pie-chat">
            <input type="hidden" name="match" value="<?php echo $codigoMatch; ?>">
            <input type="text" name="mensaje" id="inputMensaje" placeholder="Escribe un mensaje..." autocomplete="off" required maxlength="500">
            <button type="submit">Enviar</button>
        </form>

    </div>
</div>

<script src="js/modo-oscuro.js"></script>
<script>
const codigoMatch = <?php echo json_encode($codigoMatch); ?>;
const cuerpoChat = document.getElementById('cuerpoChat');
const formEnviar = document.getElementById('formEnviarMensaje');
const inputMensaje = document.getElementById('inputMensaje');

cuerpoChat.scrollTop = cuerpoChat.scrollHeight;

formEnviar.addEventListener('submit', function(e) {
    e.preventDefault();
    const texto = inputMensaje.value.trim();
    if (texto === '') return;

    fetch('p_enviarmensaje.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'match=' + encodeURIComponent(codigoMatch) + '&mensaje=' + encodeURIComponent(texto)
    })
    .then(res => res.text())
    .then(() => {
        inputMensaje.value = '';
        cargarMensajes();
    });
});

function cargarMensajes() {
    fetch('p_obtenermensajes.php?match=' + encodeURIComponent(codigoMatch))
        .then(res => res.text())
        .then(html => {
            cuerpoChat.innerHTML = html;
            cuerpoChat.scrollTop = cuerpoChat.scrollHeight;
        });
}

setInterval(cargarMensajes, 3000);
</script>

</body>
</html>