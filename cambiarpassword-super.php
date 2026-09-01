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
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar contraseña - Superusuario</title>
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

            <form action="p_cambiarpassword-super.php" method="post" class="formulario-superusuario">

                <h2>Cambiar contraseña</h2>

                <p class="texto-formulario-faustinder">
                    Por seguridad, ingresa tu contraseña actual y luego la nueva contraseña.
                </p>

                <?php
                if (isset($_GET["error"])) {
                    if ($_GET["error"] == "vacio") {
                        echo "<div class='mensaje-error'>Complete todos los campos.</div>";
                    }

                    if ($_GET["error"] == "actual") {
                        echo "<div class='mensaje-error'>La contraseña actual es incorrecta.</div>";
                    }

                    if ($_GET["error"] == "diferente") {
                        echo "<div class='mensaje-error'>Las nuevas contraseñas no coinciden.</div>";
                    }

                    if ($_GET["error"] == "longitud") {
                        echo "<div class='mensaje-error'>La contraseña debe tener exactamente 8 caracteres.</div>";
                    }

                    if ($_GET["error"] == "igual") {
                        echo "<div class='mensaje-error'>La nueva contraseña no puede ser igual a la actual.</div>";
                    }
                }

                if (isset($_GET["ok"])) {
                    echo "<div class='mensaje-correcto'>Contraseña actualizada correctamente.</div>";
                }
                ?>

                <div class="grupo-datos-faustinder">
                    <label>Usuario</label>
                    <input type="text" value="<?php echo $r["usuario"]; ?>" disabled>
                </div>

                <div class="grupo-datos-faustinder">
                    <label>Contraseña actual</label>
                    <input type="password" name="pwdactual" maxlength="8" required>
                </div>

                <div class="fila-formulario-super">

                    <div class="grupo-datos-faustinder">
                        <label>Nueva contraseña</label>
                        <input type="password" name="pwdnueva" maxlength="8" required>
                    </div>

                    <div class="grupo-datos-faustinder">
                        <label>Repetir nueva contraseña</label>
                        <input type="password" name="pwdrepetir" maxlength="8" required>
                    </div>

                </div>

                <button type="submit" class="btn-datos-faustinder">
                    Cambiar contraseña
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