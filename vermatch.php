<?php
include("auth.php");
include("conexion.php");

$codigoUsuario = $_SESSION["codigo"];

$sqlUsuario = "select * from tbpersona where codigo='$codigoUsuario'";
$fUsuario = mysqli_query($cn, $sqlUsuario);
$usuario = mysqli_fetch_assoc($fUsuario);

$nombreCompleto = $usuario["nombre"] . " " . $usuario["apaterno"] . " " . $usuario["amaterno"];

$listaPersonas = array();
$listaCodigoMatch = array();

$codigoSQL = mysqli_real_escape_string($cn, $codigoUsuario);

$sqlMatches = "select * from tbmatch
               where solicitante='$codigoSQL' or receptor='$codigoSQL'";

$fMatches = mysqli_query($cn, $sqlMatches);

while ($m = mysqli_fetch_assoc($fMatches)) {

    if ($m["solicitante"] == $codigoUsuario) {
        $codigoOtraPersona = $m["receptor"];
        $soyElSolicitante = true;
    } else {
        $codigoOtraPersona = $m["solicitante"];
        $soyElSolicitante = false;
    }

    $codigoOtraPersonaSQL = mysqli_real_escape_string($cn, $codigoOtraPersona);

    $sqlReporte = "select * from tbreporte
                   where (reportante='$codigoSQL' and reportado='$codigoOtraPersonaSQL')
                   or (reportante='$codigoOtraPersonaSQL' and reportado='$codigoSQL')";

    $fReporte = mysqli_query($cn, $sqlReporte);

    if (mysqli_num_rows($fReporte) > 0) {
        continue;
    }

    if ($m["estado"] == "ACEPTADO") {
        $listaPersonas[$codigoOtraPersona] = "CHAT";
        $listaCodigoMatch[$codigoOtraPersona] = $m["codigo"];
    } else if ($m["estado"] == "PENDIENTE") {
        if ($soyElSolicitante) {
            $listaPersonas[$codigoOtraPersona] = "EN ESPERA";
        } else {
            $listaPersonas[$codigoOtraPersona] = "PENDIENTE_RESPUESTA";
        }
    } else if ($m["estado"] == "RECHAZADO") {
        if ($soyElSolicitante) {
            $listaPersonas[$codigoOtraPersona] = "RECHAZADO";
        }
    }
}

$modoOscuro = isset($_COOKIE["modo_oscuro"]) && $_COOKIE["modo_oscuro"] == "1";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ver Match - FAUSTINder</title>
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
            <a href="vermatch.php" class="activo-faustinder">Ver Match</a>
            <a href="chats.php">Mensajes</a>
        </div>

        <div class="cuerpo-principal-faustinder">

            <h2>Bienvenido, <?php echo htmlspecialchars($nombreCompleto); ?></h2>
            <p>Aquí puedes ver tus solicitudes de match.</p>

            <?php if (count($listaPersonas) == 0) { ?>

                <div class="sin-resultados-amigos">
                    No hay match registrados.
                </div>

            <?php } else { ?>

                <div class="tabla-vermatch">

                    <?php foreach ($listaPersonas as $codigoPersona => $estadoMatch) { ?>

                        <?php
                        $codigoPersonaSQL = mysqli_real_escape_string($cn, $codigoPersona);
                        $sqlPersona = "select *, TIMESTAMPDIFF(SECOND, ultima_conexion, NOW()) as segundos_conexion
                                       from tbpersona where codigo='$codigoPersonaSQL'";
                        $fPersona = mysqli_query($cn, $sqlPersona);

                        if (mysqli_num_rows($fPersona) > 0) {

                            $p = mysqli_fetch_assoc($fPersona);
                            $nombrePersona = $p["nick"];

                            $sqlFotoPersona = "select ruta from tbfoto where codigo_persona='$codigoPersonaSQL' and principal='S' limit 1";
                            $fFotoPersona = mysqli_query($cn, $sqlFotoPersona);
                            $tieneFotoPersona = mysqli_num_rows($fFotoPersona) > 0;

                            if ($tieneFotoPersona) {
                                $fotoPersonaData = mysqli_fetch_assoc($fFotoPersona);
                                $fotoWeb = $fotoPersonaData["ruta"];
                            }

                            if ($p["sexo"] == "M") {
                                $sexoTexto = "Masculino";
                            } else if ($p["sexo"] == "F") {
                                $sexoTexto = "Femenino";
                            } else {
                                $sexoTexto = $p["sexo"];
                            }

                            // Última conexión (solo se muestra cuando ya es match/chat habilitado)
                            // El cálculo ocurre dentro de MySQL (TIMESTAMPDIFF) para no mezclar
                            // el reloj de PHP con el de MySQL y evitar desfaces de zona horaria.
                            $enLineaPersona = false;
                            $ultimaConexionTexto = "";
                            if ($estadoMatch == "CHAT" && $p["ultima_conexion"] != NULL) {
                                $segundos = (int)$p["segundos_conexion"];
                                $minutos = intdiv($segundos, 60);
                                if ($segundos < 120) {
                                    $enLineaPersona = true;
                                    $ultimaConexionTexto = "En línea";
                                } else if ($minutos < 60) {
                                    $ultimaConexionTexto = "Activo hace " . $minutos . " min";
                                } else if ($minutos < 1440) {
                                    $ultimaConexionTexto = "Activo hace " . intdiv($minutos, 60) . " h";
                                } else {
                                    $ultimaConexionTexto = "Activo hace " . intdiv($minutos, 1440) . " días";
                                }
                            }
                        ?>

                        <!-- FILA PRINCIPAL -->
                        <div class="fila-vermatch">

                            <div class="col-foto-match">
                                <?php if ($tieneFotoPersona) { ?>
                                    <img src="<?php echo $fotoWeb . '?v=' . time(); ?>" alt="Foto">
                                <?php } else { ?>
                                    <div class="sin-foto-match">❤</div>
                                <?php } ?>
                                <?php if ($estadoMatch == "CHAT") { ?>
                                    <span class="punto-en-linea <?php echo $enLineaPersona ? 'punto-activo' : ''; ?>"></span>
                                <?php } ?>
                            </div>

                            <div class="col-nombre-match">
                                <?php echo htmlspecialchars($nombrePersona); ?>
                                <?php if ($estadoMatch == "CHAT" && $ultimaConexionTexto != "") { ?>
                                    <div class="ultima-conexion-chat-lista"><?php echo $ultimaConexionTexto; ?></div>
                                <?php } ?>
                            </div>

                            <div class="col-escuela-match"><?php echo htmlspecialchars($p["escuela"]); ?></div>
                            <div class="col-sexo-match"><?php echo htmlspecialchars($sexoTexto); ?></div>

                            <div class="col-accion-match">

                                <?php if ($estadoMatch == "EN ESPERA") { ?>

                                    <span class="estado-espera-match">En espera</span>

                                <?php } else if ($estadoMatch == "PENDIENTE_RESPUESTA") { ?>

                                    <a href="#" class="btn-aceptar-match"
                                       onclick="togglePerfil('perfil_<?php echo $codigoPersona; ?>'); return false;">
                                        Ver Perfil
                                    </a>

                                <?php } else if ($estadoMatch == "CHAT") { ?>

                                    <a href="chat.php?match=<?php echo $listaCodigoMatch[$codigoPersona]; ?>" class="btn-whatsapp-match">
                                        Ir al chat
                                    </a>

                                <?php } else if ($estadoMatch == "RECHAZADO") { ?>

                                    <div class="acciones-match">
                                        <span class="estado-rechazado-match">Rechazado</span>
                                        <a href="confirmacion.php?codigo=<?php echo $codigoPersona; ?>" class="btn-volver-solicitar">
                                            Volver a solicitar
                                        </a>
                                    </div>

                                <?php } ?>

                            </div>

                        </div>

                        <!-- TARJETA EXPANDIBLE (solo para PENDIENTE_RESPUESTA) -->
                        <?php if ($estadoMatch == "PENDIENTE_RESPUESTA") { ?>

                        <div id="perfil_<?php echo $codigoPersona; ?>" style="display:none; margin-bottom:20px;">

                            <div class="tarjeta-confirmacion-match" style="margin-top:12px;">

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
                                            <td><?php echo htmlspecialchars($p["nick"]); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Nombre</th>
                                            <td><?php echo htmlspecialchars($p["nombre"] . " " . $p["apaterno"] . " " . $p["amaterno"]); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Escuela</th>
                                            <td><?php echo htmlspecialchars($p["escuela"]); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Sexo</th>
                                            <td><?php echo htmlspecialchars($sexoTexto); ?></td>
                                        </tr>
                                        <tr>
                                            <th>Tipo</th>
                                            <td><?php echo htmlspecialchars($p["tipo"]); ?></td>
                                        </tr>
                                    </table>
                                </div>

                            </div>

                            <?php if ($p["descripcion"]) { ?>
                            <div class="descripcion-confirmacion" style="margin-top:14px;">
                                <?php echo htmlspecialchars($p["descripcion"]); ?>
                            </div>
                            <?php } ?>

                            <div class="botones-confirmacion-match" style="margin-top:16px;">
                                <a href="p_accionmatch.php?codigo=<?php echo $codigoPersona; ?>&accion=aceptar" class="btn-hacer-match">
                                    Aceptar
                                </a>
                                <a href="p_accionmatch.php?codigo=<?php echo $codigoPersona; ?>&accion=rechazar" class="btn-cancelar-match">
                                    Rechazar
                                </a>
                                <a href="reportar.php?codigo=<?php echo $codigoPersona; ?>" class="btn-cancelar-match" style="background:#fff7df; color:#9b6b00;">
                                    Reportar
                                </a>
                            </div>

                        </div>

                        <?php } ?>

                        <?php }  ?>

                    <?php }  ?>

                </div>

            <?php } ?>

        </div>

    </div>
</div>

<script src="js/modo-oscuro.js"></script>
<script>
function togglePerfil(id) {
    var div = document.getElementById(id);
    if (div.style.display === 'none') {
        div.style.display = 'block';
    } else {
        div.style.display = 'none';
    }
}

// Dropdown con click
document.querySelectorAll('.btn-dropdown').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();
        var dropdown = this.nextElementSibling;
        var estaAbierto = dropdown.style.display === 'block';
        document.querySelectorAll('.contenido-dropdown').forEach(function(d) {
            d.style.display = 'none';
        });
        if (!estaAbierto) dropdown.style.display = 'block';
    });
});

document.addEventListener('click', function(e) {
    if (!e.target.classList.contains('btn-dropdown')) {
        document.querySelectorAll('.contenido-dropdown').forEach(function(d) {
            d.style.display = 'none';
        });
    }
});

setInterval(function() {
    fetch('p_heartbeat.php');
}, 20000);
</script>

</body>
</html>
