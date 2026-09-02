<?php
include("auth.php");
include("conexion.php");
include("ticks_mensaje.php");

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

// Todo el cálculo de tiempo ocurre dentro de MySQL (TIMESTAMPDIFF), para no mezclar
// el reloj de PHP con el de MySQL y evitar desfaces de zona horaria.
$sqlOtro = "select *, TIMESTAMPDIFF(SECOND, ultima_conexion, NOW()) as segundos_conexion
            from tbpersona where codigo='$codigoOtroSQL'";
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

// Estado de conexión del otro usuario (calculado en MySQL, ver arriba)
$enLinea = false;
$ultimaConexionTexto = "Sin conexión reciente";
if ($otro["ultima_conexion"] != NULL) {
    $segundos = (int)$otro["segundos_conexion"];
    $minutos = intdiv($segundos, 60);
    if ($segundos < 120) {
        $enLinea = true;
        $ultimaConexionTexto = "En línea";
    } else if ($minutos < 60) {
        $ultimaConexionTexto = "Activo hace " . $minutos . " min";
    } else if ($minutos < 1440) {
        $ultimaConexionTexto = "Activo hace " . intdiv($minutos, 60) . " h";
    } else {
        $ultimaConexionTexto = "Activo hace " . intdiv($minutos, 1440) . " días";
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
                <span class="punto-en-linea <?php echo $enLinea ? 'punto-activo' : ''; ?>" id="puntoEnLinea"></span>
            </div>

            <div class="info-encabezado-chat">
                <div class="nombre-encabezado-chat"><?php echo htmlspecialchars($otro["nick"]); ?></div>
                <div class="estado-encabezado-chat" id="estadoConexion"><?php echo $ultimaConexionTexto; ?></div>
                <div id="lineaDebug" style="font-size:10px; color:#c0392b;"></div>
            </div>

        </div>

        <div class="cuerpo-chat" id="cuerpoChat">

            <?php while ($msj = mysqli_fetch_assoc($fMensajes)) { ?>

                <?php if ($msj["emisor"] == $codigoUsuario) { ?>

                    <div class="burbuja-mensaje burbuja-propia">
                        <p><?php echo nl2br(htmlspecialchars($msj["mensaje"])); ?></p>
                        <span class="hora-mensaje">
                            <?php echo date("H:i", strtotime($msj["fecha_envio"])); ?>
                            <?php echo renderTicks($msj["leido"], $msj["fecha_envio"], $otro["ultima_conexion"]); ?>
                        </span>
                    </div>

                <?php } else { ?>

                    <div class="burbuja-mensaje burbuja-otro">
                        <p><?php echo nl2br(htmlspecialchars($msj["mensaje"])); ?></p>
                        <span class="hora-mensaje"><?php echo date("H:i", strtotime($msj["fecha_envio"])); ?></span>
                    </div>

                <?php } ?>

            <?php } ?>

            <div class="burbuja-escribiendo" id="burbujaEscribiendo" style="display:none;">
                <span></span><span></span><span></span>
            </div>


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
const estadoConexion = document.getElementById('estadoConexion');
const puntoEnLinea = document.getElementById('puntoEnLinea');
const burbujaEscribiendo = document.getElementById('burbujaEscribiendo');

cuerpoChat.scrollTop = cuerpoChat.scrollHeight;

// --- Envío instantáneo (optimista): el mensaje aparece de inmediato en pantalla,
// sin esperar la respuesta del servidor ni el siguiente polling ---
formEnviar.addEventListener('submit', function(e) {
    e.preventDefault();
    const texto = inputMensaje.value.trim();
    if (texto === '') return;

    const burbuja = document.createElement('div');
    burbuja.className = 'burbuja-mensaje burbuja-propia burbuja-pendiente';
    burbuja.innerHTML = '<p></p><span class="hora-mensaje"></span>';
    burbuja.querySelector('p').textContent = texto;
    const ahora = new Date();
    const horaTxt = String(ahora.getHours()).padStart(2, '0') + ':' + String(ahora.getMinutes()).padStart(2, '0');
    burbuja.querySelector('.hora-mensaje').textContent = horaTxt + ' ';
    burbujaEscribiendo.insertAdjacentElement('beforebegin', burbuja);
    cuerpoChat.scrollTop = cuerpoChat.scrollHeight;

    inputMensaje.value = '';

    fetch('p_enviarmensaje.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'match=' + encodeURIComponent(codigoMatch) + '&mensaje=' + encodeURIComponent(texto)
    })
    .then(res => res.text())
    .then(() => {
        cargarMensajes();
    });
});

let estaEscribiendo = false;

function cargarMensajes() {
    fetch('p_obtenermensajes.php?match=' + encodeURIComponent(codigoMatch))
        .then(res => res.text())
        .then(html => {
            const scrollAbajo = (cuerpoChat.scrollTop + cuerpoChat.clientHeight) >= (cuerpoChat.scrollHeight - 40);
            cuerpoChat.innerHTML = html;
            burbujaEscribiendo.style.display = estaEscribiendo ? 'flex' : 'none';
            cuerpoChat.appendChild(burbujaEscribiendo);
            if (scrollAbajo) {
                cuerpoChat.scrollTop = cuerpoChat.scrollHeight;
            }
        });
}

// --- Estado en vivo del otro usuario: en línea / activo hace X / escribiendo... ---
const lineaDebug = document.getElementById('lineaDebug');

function actualizarEstado() {
    fetch('p_estadochat.php?match=' + encodeURIComponent(codigoMatch))
        .then(res => res.json())
        .then(data => {
            if (data.error) return;

            puntoEnLinea.classList.toggle('punto-activo', data.en_linea);
            estadoConexion.textContent = data.texto;

            // LÍNEA TEMPORAL DE DEPURACIÓN — quitar cuando "escribiendo..." funcione bien
            lineaDebug.textContent = 'DEBUG otro=' + data.debug_codigo_otro
                + ' match_actual=' + data.debug_match_actual
                + ' guardado=' + data.debug_escribiendo_match_guardado
                + ' segs=' + data.debug_segundos_desde_ultima_tecla
                + ' escribiendo=' + data.escribiendo;

            estaEscribiendo = !!data.escribiendo;
            if (estaEscribiendo) {
                burbujaEscribiendo.style.display = 'flex';
                cuerpoChat.scrollTop = cuerpoChat.scrollHeight;
            } else {
                burbujaEscribiendo.style.display = 'none';
            }
        })
        .catch((err) => {
            lineaDebug.textContent = 'DEBUG error de red/JSON: ' + err;
        });
}

// --- Avisar al otro usuario que estoy escribiendo ---
let timeoutEscribiendo = null;
inputMensaje.addEventListener('input', function() {
    if (timeoutEscribiendo) return; // evita mandar la señal en cada tecla
    fetch('p_escribiendo.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'match=' + encodeURIComponent(codigoMatch)
    });
    timeoutEscribiendo = setTimeout(function() {
        timeoutEscribiendo = null;
    }, 2000);
});

// Heartbeat: mantiene mi ultima_conexion actualizada mientras tengo el chat abierto
setInterval(function() {
    fetch('p_heartbeat.php');
}, 20000);

setInterval(cargarMensajes, 1500);
setInterval(actualizarEstado, 2000);
actualizarEstado();
</script>

</body>
</html>
