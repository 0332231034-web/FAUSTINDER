<?php
include("auth.php");
include("conexion.php");

$codigo = $_SESSION["codigo"];
$codigoSQL = mysqli_real_escape_string($cn, $codigo);

$sqlFotos = "select * from tbfoto where codigo_persona='$codigoSQL' order by principal desc, codigo asc";
$fFotos = mysqli_query($cn, $sqlFotos);
$totalFotos = mysqli_num_rows($fFotos);

$puedeSubir = $totalFotos < 5;

$modoOscuro = isset($_COOKIE["modo_oscuro"]) && $_COOKIE["modo_oscuro"] == "1";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Fotos de perfil - FAUSTINDER</title>
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

            <a href="cerrarsesion.php" class="btn-salir-faustinder">
                Cerrar sesión
            </a>
        </div>

        <div class="menu-faustinder-moderno">

            <a href="principal.php">
                Principal
            </a>

            <div class="dropdown-datos">
                <a href="#" class="btn-dropdown">
                    Tus Datos
                </a>

                <div class="contenido-dropdown">
                    <a href="actualizardatos.php">Actualizar datos</a>
                    <a href="imagenperfil.php" class="activo-faustinder">Insertar foto de perfil</a>
                    <a href="cambiarpassword.php">Cambiar password</a>
                </div>
            </div>

            <a href="buscaramigos.php">
                Buscar Amigos
            </a>

            <a href="vermatch.php">
                Ver Match
            </a>

            <a href="chats.php">
                Mensajes
            </a>

        </div>

        <div class="cuerpo-principal-faustinder">

            <h2>Fotos de perfil</h2>

            <p class="texto-formulario-faustinder">
                Puedes subir hasta 5 fotos. Elige cuál es tu foto principal, la que verán los demás.
            </p>

            <?php
            if (isset($_GET["error"])) {

                if ($_GET["error"] == "vacio") {
                    echo "<div class='mensaje-error'>Seleccione una imagen.</div>";
                }

                if ($_GET["error"] == "formato") {
                    echo "<div class='mensaje-error'>Solo se permite imagen en formato PNG.</div>";
                }

                if ($_GET["error"] == "subida") {
                    echo "<div class='mensaje-error'>No se pudo subir la imagen.</div>";
                }

                if ($_GET["error"] == "limite") {
                    echo "<div class='mensaje-error'>Ya tienes el máximo de 5 fotos. Elimina alguna para subir otra.</div>";
                }
            }

            if (isset($_GET["ok"])) {
                echo "<div class='mensaje-correcto'>Listo, tus fotos se actualizaron correctamente.</div>";
            }
            ?>

            <div class="galeria-fotos-perfil">

                <?php if ($totalFotos == 0) { ?>

                    <div class="sin-foto-perfil">
                        Todavía no has subido ninguna foto.
                    </div>

                <?php } else { ?>

                    <?php while ($foto = mysqli_fetch_assoc($fFotos)) { ?>

                        <div class="tarjeta-foto-perfil">

                            <img src="<?php echo htmlspecialchars($foto["ruta"]) . '?v=' . time(); ?>" alt="Foto de perfil">

                            <?php if ($foto["principal"] == "S") { ?>

                                <span class="badge-foto-principal">Principal</span>

                            <?php } else { ?>

                                <a href="p_fotoprincipal.php?foto=<?php echo urlencode($foto["codigo"]); ?>" class="btn-foto-accion">
                                    Hacer principal
                                </a>

                            <?php } ?>

                            <a href="p_eliminarfoto.php?foto=<?php echo urlencode($foto["codigo"]); ?>"
                               class="btn-foto-eliminar"
                               onclick="return confirm('¿Eliminar esta foto?');">
                                Eliminar
                            </a>

                        </div>

                    <?php } ?>

                <?php } ?>

            </div>

            <?php if ($puedeSubir) { ?>

                <form action="p_imagenperfil.php" method="post" enctype="multipart/form-data">

                    <div class="grupo-datos-faustinder">
                        <label>Archivo PNG (<?php echo $totalFotos; ?>/5)</label>
                        <input type="file" name="archivo" accept=".png" required>
                    </div>

                    <button type="submit" class="btn-datos-faustinder">
                        Subir foto
                    </button>

                </form>

            <?php } else { ?>

                <div class="mensaje-error">
                    Alcanzaste el máximo de 5 fotos. Elimina una para subir otra.
                </div>

            <?php } ?>

            <a href="principal.php" class="volver-inicio">
                Volver al principal
            </a>

        </div>

    </div>

</div>

<script src="js/modo-oscuro.js"></script>

</body>
</html>
