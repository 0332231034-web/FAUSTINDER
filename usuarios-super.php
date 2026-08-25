<?php
include("authsuper.php");
include("conexion.php");

$busqueda = "";

if (isset($_GET["busqueda"])) {
    $busqueda = trim($_GET["busqueda"]);
}

$cantidad = 20;

if (isset($_GET["lim"])) {
    $limite = (int)$_GET["lim"];
} else {
    $limite = 0;
}

$condicion = " where 1=1 ";

if ($busqueda != "") {

    $busquedaLimpia = preg_replace('/\s+/', ' ', $busqueda);
    $busquedaSQL = mysqli_real_escape_string($cn, $busquedaLimpia);

    if (is_numeric($busquedaLimpia)) {

        $condicion = $condicion . "
        and celular like '$busquedaSQL%' ";

    } else {

        $palabras = explode(" ", $busquedaLimpia);

        $condicionPalabras = "";

        for ($i = 0; $i < count($palabras); $i++) {

            $palabra = trim($palabras[$i]);

            if ($palabra != "") {

                $palabraSQL = mysqli_real_escape_string($cn, $palabra);

                if ($condicionPalabras != "") {
                    $condicionPalabras = $condicionPalabras . " and ";
                }

                $condicionPalabras = $condicionPalabras . "
                (
                    lower(nick) = lower('$palabraSQL')
                    or lower(nombre) = lower('$palabraSQL')
                    or lower(apaterno) = lower('$palabraSQL')
                    or lower(amaterno) = lower('$palabraSQL')
                )";
            }
        }

        $condicion = $condicion . "
        and
        (
            lower(nick) = lower('$busquedaSQL')
            or lower(concat(nombre, ' ', apaterno, ' ', amaterno)) = lower('$busquedaSQL')
            or ($condicionPalabras)
        )";
    }
}

$sqlTotal = "select count(*) as total from tbpersona $condicion";
$fTotal = mysqli_query($cn, $sqlTotal);
$rTotal = mysqli_fetch_assoc($fTotal);
$totalRegistros = $rTotal["total"];

$sql = "select * from tbpersona
        $condicion
        order by codigo desc
        limit $limite, $cantidad";

$f = mysqli_query($cn, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuarios - Superusuario</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<div class="panel-superusuario">

    <div class="contenedor-superusuario">

        <div class="encabezado-superusuario">
            <div>
                <h1>FAUSTINder</h1>
                <p>Administración de usuarios registrados</p>
            </div>

            <a href="cerrarsesion-super.php" class="btn-salir-faustinder">
                Cerrar sesión
            </a>
        </div>

        <div class="menu-superusuario">

            <a href="principal-super.php">Principal</a>

            <div class="dropdown-super">
                <a href="#" class="btn-dropdown-super"> Mis Datos</a>
                <div class="contenido-dropdown-super">
                    <a href="actualizardatos-super.php">Actualizar datos</a>
                    <a href="imagenperfil-super.php">Subir foto</a>
                    <a href="cambiarpassword-super.php">Cambiar contraseña</a>
                </div>
            </div>

            <div class="dropdown-super">
                <a href="#" class="btn-dropdown-super activo-super"> Usuarios</a>

                <div class="contenido-dropdown-super">
                    <a href="usuarios-super.php">Buscar / Editar usuarios</a>
                </div>
            </div>

            <div class="dropdown-super">
                <a href="#" class="btn-dropdown-super">Reportes</a>
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
                <a href="#" class="btn-dropdown-super">Estadísticas</a>
                <div class="contenido-dropdown-super">
                    <a href="estadistica-sexo-super.php">Usuarios por sexo</a>
                    <a href="estadistica-estado-super.php">Activos e inactivos</a>
                    <a href="estadistica-reportes-super.php">Reportados y no reportados</a>
                </div>
            </div>

        </div>

        <div class="cuerpo-superusuario">

            <form action="usuarios-super.php" method="get" class="formulario-busqueda-super">

                <h2>Usuarios registrados</h2>

                <div class="fila-busqueda-super">
                    <input type="text" name="busqueda" placeholder="Buscar por nick, celular o nombre completo" value="<?php echo $busqueda; ?>">
                    <button type="submit">Buscar</button>
                </div>

            </form>

            <div class="tabla-usuarios-super">

                <div class="fila-usuario-super encabezado-tabla-super">
                    <div>Nick</div>
                    <div>Nombre</div>
                    <div>Celular</div>
                    <div>Estado</div>
                    <div>Acciones</div>
                </div>

                <?php if (mysqli_num_rows($f) == 0) { ?>

                    <div class="sin-resultados-amigos">
                        No se encontraron usuarios.
                    </div>

                <?php } else { ?>

                    <?php while ($r = mysqli_fetch_assoc($f)) { ?>

                        <?php
                        $codigoPersona = $r["codigo"];

                        $sqlReporte = "select count(*) as total
                                       from tbreporte
                                       where reportado='$codigoPersona'
                                       and estado='PENDIENTE'";

                        $fReporte = mysqli_query($cn, $sqlReporte);
                        $rReporte = mysqli_fetch_assoc($fReporte);
                        $tieneReportePendiente = $rReporte["total"];

                        $estado = "";

                        if (isset($r["estado"])) {
                            $estado = $r["estado"];
                        }

                        $esActivo = false;
                        $esInactivoTemporal = false;
                        $esBajaDefinitiva = false;

                        if ($estado == "I") {

                            if ($r["fechafin_inactivo"] != "" && $r["fechafin_inactivo"] != NULL) {
                                $esInactivoTemporal = true;
                            } else {
                                $esBajaDefinitiva = true;
                            }

                        } else {

                            $esActivo = true;

                        }
                        ?>

                        <div class="fila-usuario-super">

                            <div>
                                <?php echo $r["nick"]; ?>
                            </div>

                            <div>
                                <?php echo $r["nombre"] . " " . $r["apaterno"] . " " . $r["amaterno"]; ?>
                            </div>

                            <div>
                                <?php echo $r["celular"]; ?>
                            </div>

                            <div>
                                <?php if ($esActivo == true) { ?>

                                    <span class="estado-activo-super">
                                        Activo
                                    </span>

                                <?php } else if ($esInactivoTemporal == true) { ?>

                                    <span class="estado-temporal-super">
                                        Inactivo hasta <?php echo $r["fechafin_inactivo"]; ?>
                                    </span>

                                <?php } else if ($esBajaDefinitiva == true) { ?>

                                    <span class="estado-baja-super">
                                        Baja definitiva
                                    </span>

                                <?php } ?>
                            </div>

                            <div class="acciones-usuario-super">

                                <?php if ($esActivo == true) { ?>

                                    <a href="editarusuario-super.php?codigo=<?php echo $codigoPersona; ?>" class="btn-editar-super">
                                        Editar
                                    </a>

                                    <?php if ($tieneReportePendiente > 0) { ?>
                                        <a href="sancionarusuario-super.php?codigo=<?php echo $codigoPersona; ?>" class="btn-reportado-super">
                                            Reportado
                                        </a>
                                    <?php } ?>

                                <?php } else if ($esInactivoTemporal == true) { ?>

                                    <a href="editarusuario-super.php?codigo=<?php echo $codigoPersona; ?>" class="btn-editar-super">
                                        Editar
                                    </a>

                                    <a href="p_reactivarusuario-super.php?codigo=<?php echo $codigoPersona; ?>" class="btn-reactivar-super">
                                        Reactivar
                                    </a>

                                <?php } else if ($esBajaDefinitiva == true) { ?>

                                    <span class="btn-baja-super">
                                        Desactivada definitiva
                                    </span>

                                <?php } ?>

                            </div>

                        </div>

                    <?php } ?>

                <?php } ?>

            </div>

            <div class="paginacion-amigos">

                <?php if ($limite > 0) { ?>
                    <a href="usuarios-super.php?busqueda=<?php echo urlencode($busqueda); ?>&lim=<?php echo $limite - $cantidad; ?>">
                        Anterior
                    </a>
                <?php } ?>

                <?php if ($limite + $cantidad < $totalRegistros) { ?>
                    <a href="usuarios-super.php?busqueda=<?php echo urlencode($busqueda); ?>&lim=<?php echo $limite + $cantidad; ?>">
                        Siguiente
                    </a>
                <?php } ?>

            </div>

        </div>

    </div>

</div>

</body>
</html>