<?php
include("auth.php");
include("conexion.php");

$codigo = $_SESSION["codigo"];

$sql = "select * from tbpersona where codigo='$codigo'";
$f = mysqli_query($cn, $sql);
$r = mysqli_fetch_assoc($f);

$nombreCompleto = $r["nombre"] . " " . $r["apaterno"] . " " . $r["amaterno"];

$datosCompletos = true;

if ($r["correo"] == "" || $r["correo"] == NULL) { $datosCompletos = false; }
if ($r["celular"] == "" || $r["celular"] == NULL) { $datosCompletos = false; }
if ($r["escuela"] == "" || $r["escuela"] == NULL) { $datosCompletos = false; }
if ($r["sexo"] == "" || $r["sexo"] == NULL) { $datosCompletos = false; }
if ($r["tipo"] == "" || $r["tipo"] == NULL) { $datosCompletos = false; }
if ($r["descripcion"] == "" || $r["descripcion"] == NULL) { $datosCompletos = false; }

// Foto principal desde tbfoto
$sqlFotoPrincipal = "select ruta from tbfoto where codigo_persona='$codigo' and principal='S' limit 1";
$fFotoPrincipal = mysqli_query($cn, $sqlFotoPrincipal);
$tieneFoto = false;
$rutaFotoWeb = "";

if (mysqli_num_rows($fFotoPrincipal) > 0) {
    $fotoPrincipal = mysqli_fetch_assoc($fFotoPrincipal);
    $rutaFotoWeb = $fotoPrincipal["ruta"];
    $tieneFoto = true;
}

$rutaPasswordFisica = __DIR__ . "/password/cambio_" . $codigo . ".txt";
$passwordCambiado = false;

if (file_exists($rutaPasswordFisica)) {
    $passwordCambiado = true;
}

$servicioActivo = false;

if ($datosCompletos == true && $tieneFoto == true && $passwordCambiado == true) {
    $servicioActivo = true;
}

$sexoTexto = "";

if ($r["sexo"] == "M") {
    $sexoTexto = "Masculino";
} else if ($r["sexo"] == "F") {
    $sexoTexto = "Femenino";
} else {
    $sexoTexto = $r["sexo"];
}

// Notificaciones no leídas
$sqlNotif = "select count(*) as total from tbnotificacion where codigo_persona='$codigo' and leida='N'";
$fNotif = mysqli_query($cn, $sqlNotif);
$rNotif = mysqli_fetch_assoc($fNotif);
$totalNotifNoLeidas = $rNotif["total"];

$sqlListaNotif = "select * from tbnotificacion where codigo_persona='$codigo' order by fecha desc limit 10";
$fListaNotif = mysqli_query($cn, $sqlListaNotif);

// Modo oscuro
$modoOscuro = isset($_COOKIE["modo_oscuro"]) && $_COOKIE["modo_oscuro"] == "1";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Principal - FAUSTINDER</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="css/modo-oscuro.css">
</head>
<body class="<?php echo $modoOscuro ? 'tema-oscuro' : ''; ?>">

<div class="panel-principal-faustinder">

    <div class="tarjeta-principal-faustinder">

        <div class="encabezado-faustinder">
            <div>
                <h1>FAUSTINDER</h1>
                <p>Te acercamos al amor de tu vida o a tu peor pesadilla</p>
            </div>

            <div class="acciones-encabezado-faustinder">

                <div class="campana-notificaciones">
                    <a href="#" id="btnNotificaciones" class="btn-campana">
                        🔔
                        <?php if ($totalNotifNoLeidas > 0) { ?>
                            <span class="badge-notificacion"><?php echo $totalNotifNoLeidas; ?></span>
                        <?php } ?>
                    </a>

                    <div id="listaNotificaciones" class="dropdown-notificaciones">

                        <?php if (mysqli_num_rows($fListaNotif) == 0) { ?>

                            <div class="notif-vacia">No tienes notificaciones.</div>

                        <?php } else { ?>

                            <?php while ($n = mysqli_fetch_assoc($fListaNotif)) { ?>

                                <?php if ($n["tipo"] == "MENSAJE" && $n["codigo_relacionado"] != NULL) { ?>

                                    <a href="chat.php?match=<?php echo urlencode($n["codigo_relacionado"]); ?>" class="item-notificacion <?php echo $n["leida"] == "N" ? 'notif-no-leida' : ''; ?>" style="display:block; text-decoration:none; color:inherit;">
                                        <p><?php echo htmlspecialchars($n["mensaje"]); ?></p>
                                        <span class="fecha-notif"><?php echo $n["fecha"]; ?></span>
                                    </a>

                                <?php } else { ?>

                                    <div class="item-notificacion <?php echo $n["leida"] == "N" ? 'notif-no-leida' : ''; ?>">
                                        <p><?php echo htmlspecialchars($n["mensaje"]); ?></p>
                                        <span class="fecha-notif"><?php echo $n["fecha"]; ?></span>
                                    </div>

                                <?php } ?>

                            <?php } ?>

                        <?php } ?>

                    </div>
                </div>

                <a href="#" id="btnModoOscuro" class="btn-modo-oscuro" title="Cambiar tema">
                    <?php echo $modoOscuro ? '☀️' : '🌙'; ?>
                </a>

                <a href="cerrarsesion.php" class="btn-salir-faustinder">
                    Cerrar sesión
                </a>

            </div>
        </div>

        <div class="menu-faustinder-moderno">

            <a href="principal.php" class="activo-faustinder">
                Principal
            </a>

            <div class="dropdown-datos">
                <a href="#" class="btn-dropdown">
                    Tus Datos ▾
                </a>

                <div class="contenido-dropdown">
                    <a href="actualizardatos.php">Actualizar datos</a>
                    <a href="imagenperfil.php">Insertar foto de perfil</a>
                    <a href="cambiarpassword.php">Cambiar password</a>
                </div>
            </div>

            <?php if ($servicioActivo == true) { ?>

                <a href="buscaramigos.php">
                    Buscar Amigos
                </a>

            <?php } else { ?>

                <a href="#" class="bloqueado-faustinder" onclick="alert('Primero debes completar tus datos, insertar tu foto de perfil y cambiar tu contraseña.');">
                    Buscar Amigos
                </a>

            <?php } ?>

            <?php if ($servicioActivo == true) { ?>

                <a href="vermatch.php">
                    Ver Match
                </a>

                <a href="chats.php">
                    Mensajes
                </a>

            <?php } else { ?>

                <a href="#" class="bloqueado-faustinder" onclick="alert('Primero debes completar tus datos, insertar tu foto de perfil y cambiar tu contraseña.');">
                    Ver Match
                </a>

                <a href="#" class="bloqueado-faustinder" onclick="alert('Primero debes completar tus datos, insertar tu foto de perfil y cambiar tu contraseña.');">
                    Mensajes
                </a>

            <?php } ?>

        </div>

        <div class="cuerpo-principal-faustinder">

            <h2>Bienvenido, <?php echo htmlspecialchars($nombreCompleto); ?></h2>

            <?php if ($datosCompletos == false) { ?>

                <p>
                    Para usar FAUSTINDER, primero debes completar tus datos personales desde el menú
                    <b>Tus Datos</b>.
                </p>

                <div class="lista-pasos-faustinder">

                    <div class="paso-pendiente">
                        Falta completar tus datos personales
                    </div>

                    <?php if ($tieneFoto == true) { ?>
                        <div class="paso-listo">
                            Foto de perfil registrada
                        </div>
                    <?php } else { ?>
                        <div class="paso-pendiente">
                            Falta insertar tu foto de perfil
                        </div>
                    <?php } ?>

                    <?php if ($passwordCambiado == true) { ?>
                        <div class="paso-listo">
                            Contraseña cambiada
                        </div>
                    <?php } else { ?>
                        <div class="paso-pendiente">
                            Falta cambiar tu contraseña inicial
                        </div>
                    <?php } ?>

                </div>

            <?php } else { ?>

                <?php if ($servicioActivo == false) { ?>

                    <p>
                        Tus datos personales ya están completos. Para activar Buscar Amigos y Ver Match,
                        todavía debes completar los requisitos pendientes.
                    </p>

                <?php } else { ?>

                    <p>
                        Perfil completo
                    </p>

                <?php } ?>

                <div class="tarjeta-perfil-completo">

                    <div class="foto-perfil-completo">

                        <?php if ($tieneFoto == true) { ?>

                            <img src="<?php echo $rutaFotoWeb . '?v=' . time(); ?>" alt="Foto de perfil">

                        <?php } else { ?>

                            <div class="sin-foto-perfil">
                                Sin foto
                            </div>

                        <?php } ?>

                    </div>

                    <div class="datos-perfil-completo">

                        <table class="tabla-perfil-completo">

                            <tr>
                                <th>Nombres</th>
                                <td><?php echo htmlspecialchars($nombreCompleto); ?></td>
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
                                <th>Correo</th>
                                <td><?php echo htmlspecialchars($r["correo"]); ?></td>
                            </tr>

                            <tr>
                                <th>Celular</th>
                                <td><?php echo htmlspecialchars($r["celular"]); ?></td>
                            </tr>

                        </table>

                    </div>

                </div>

                <?php if ($servicioActivo == false) { ?>

                    <div class="lista-pasos-faustinder">

                        <?php if ($tieneFoto == true) { ?>
                            <div class="paso-listo">
                                Foto de perfil registrada
                            </div>
                        <?php } else { ?>
                            <div class="paso-pendiente">
                                Falta insertar tu foto de perfil
                            </div>
                        <?php } ?>

                        <?php if ($passwordCambiado == true) { ?>
                            <div class="paso-listo">
                                Contraseña cambiada
                            </div>
                        <?php } else { ?>
                            <div class="paso-pendiente">
                                Falta cambiar tu contraseña inicial
                            </div>
                        <?php } ?>

                    </div>

                <?php } ?>

            <?php } ?>

        </div>

    </div>

</div>

<script src="js/modo-oscuro.js"></script>
<script>
document.getElementById('btnNotificaciones').addEventListener('click', function(e) {
    e.preventDefault();
    var lista = document.getElementById('listaNotificaciones');
    lista.style.display = (lista.style.display === 'block') ? 'none' : 'block';
});

document.addEventListener('click', function(e) {
    if (!e.target.closest('.campana-notificaciones')) {
        document.getElementById('listaNotificaciones').style.display = 'none';
    }
});

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
</script>

</body>
</html>
