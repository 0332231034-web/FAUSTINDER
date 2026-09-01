<?php
include("authsuper.php");
include("conexion.php");

$codigoSuper = $_SESSION["codigosuper"];

$sql = "select * from tbsuperusuario where codigo='$codigoSuper'";
$f = mysqli_query($cn, $sql);

if (mysqli_num_rows($f) == 0) {
    session_destroy();
    header("location: login.php");
    exit();
}

$r = mysqli_fetch_assoc($f);

$fotoFisica = __DIR__ . "/img/super_" . $codigoSuper . ".png";
$fotoWeb = "img/super_" . $codigoSuper . ".png";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Foto de perfil - Superusuario</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<div class="panel-superusuario">

    <div class="contenedor-superusuario">

        <div class="encabezado-superusuario">

            <div>
                <h1>FAUSTINder</h1>
            </div>

            <a href="cerrarsesion-super.php" class="btn-salir-faustinder">
                Cerrar sesión
            </a>

        </div>

        <div class="menu-superusuario">

            <a href="principal-super.php">
                Principal
            </a>

            <div class="dropdown-super">
                <a href="#" class="btn-dropdown-super activo-super">
                    Mis Datos
                </a>

                <div class="contenido-dropdown-super">
                    <a href="actualizardatos-super.php">Actualizar datos</a>
                    <a href="imagenperfil-super.php">Subir foto</a>
                    <a href="cambiarpassword-super.php">Cambiar contraseña</a>
                </div>
            </div>

            <div class="dropdown-super">
                <a href="#" class="btn-dropdown-super">
                    Usuarios
                </a>

                <div class="contenido-dropdown-super">
                    <a href="usuarios-super.php">Buscar / Editar usuarios</a>
                </div>
            </div>

            <div class="dropdown-super">
                <a href="#" class="btn-dropdown-super">
                    Reportes
                </a>

                <div class="contenido-dropdown-super">
                    <a href="reporte-sexo-super.php">Por sexo</a>
                    <a href="reporte-escuela-super.php">Por escuela</a>
                    <a href="reporte-tipo-super.php">Por tipo</a>
                    <a href="reporte-fecha-super.php">Por rango de fechas</a>
                    <a href="reporte-estado-super.php">Activos / Inactivos</a>
                    <a href="reportados-super.php">Personas reportadas</a>
                </div>
            </div>

            <div class="dropdown-super">
                <a href="#" class="btn-dropdown-super">
                    Estadísticas
                </a>

                <div class="contenido-dropdown-super">
                    <a href="estadistica-sexo-super.php">Usuarios por sexo</a>
                    <a href="estadistica-estado-super.php">Activos e inactivos</a>
                    <a href="estadistica-reportes-super.php">Reportados y no reportados</a>
                </div>
            </div>

        </div>

        <div class="cuerpo-superusuario">

            <form action="p_imagenperfil-super.php" method="post" enctype="multipart/form-data" class="formulario-superusuario">

                <h2>Foto de perfil</h2>
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
                }

                if (isset($_GET["ok"])) {
                    echo "<div class='mensaje-correcto'>Foto actualizada correctamente.</div>";
                }
                ?>

                <div class="vista-foto-perfil">

                    <?php if (file_exists($fotoFisica)) { ?>

                        <img src="<?php echo $fotoWeb . '?v=' . time(); ?>" alt="Foto de perfil">

                    <?php } else { ?>

                        <div class="sin-foto-super">
                            X
                        </div>

                    <?php } ?>

                </div>

                <div class="grupo-datos-faustinder">
                    <label>Seleccionar foto PNG</label>
                    <input type="file" name="archivo" accept=".png" required>
                </div>

                <button type="submit" class="btn-datos-faustinder">
                    Subir foto
                </button>

                <a href="principal-super.php" class="volver-inicio">
                    Volver al principal
                </a>

            </form>

        </div>

    </div>

</div>

</body>
</html>