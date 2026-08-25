<?php
include("authsuper.php");
include("conexion.php");

$codigoSuper = $_SESSION["codigosuper"];

$sql = "select * from tbsuperusuario where codigo='$codigoSuper'";
$f = mysqli_query($cn, $sql);

if (mysqli_num_rows($f) == 0) {
    header("location: login.php");
    exit();
}

$r = mysqli_fetch_assoc($f);

$nombreCompleto = $r["nombre"] . " " . $r["apaterno"] . " " . $r["amaterno"];

$fotoFisica = __DIR__ . "/img/super_" . $codigoSuper . ".png";
$fotoWeb = "img/super_" . $codigoSuper . ".png";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Principal Superusuario - FAUSTINder</title>
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

            <a href="principal-super.php" class="activo-super">
                Principal
            </a>

            <div class="dropdown-super">
                <a href="#" class="btn-dropdown-super">
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

            <div class="tarjeta-bienvenida-super tarjeta-super-limpia">

                <div class="foto-superusuario">

                    <?php if (file_exists($fotoFisica)) { ?>

                        <img src="<?php echo $fotoWeb . '?v=' . time(); ?>" alt="Foto superusuario">

                    <?php } else { ?>

                        <div class="sin-foto-super">
                            X
                        </div>

                    <?php } ?>

                </div>

                <div class="datos-superusuario">

                    <h2>Bienvenido, <?php echo $nombreCompleto; ?></h2>

                    <table>
                        <tr>
                            <th>Usuario</th>
                            <td><?php echo $r["usuario"]; ?></td>
                        </tr>

                        <tr>
                            <th>Celular</th>
                            <td><?php echo $r["celular"]; ?></td>
                        </tr>

                        <tr>
                            <th>Nivel</th>
                            <td>Superadministrador</td>
                        </tr>
                    </table>

                </div>

            </div>

        </div>

    </div>

</div>

</body>
</html>