<?php
include("authsuper.php");
include("conexion.php");

$fechainicio = "";
$fechafin = "";

if (isset($_GET["fechainicio"])) {
    $fechainicio = trim($_GET["fechainicio"]);
}

if (isset($_GET["fechafin"])) {
    $fechafin = trim($_GET["fechafin"]);
}

$cantidad = 20;

if (isset($_GET["lim"])) {
    $limite = (int)$_GET["lim"];
} else {
    $limite = 0;
}

$condicion = " where 1=1 ";

if ($fechainicio != "" && $fechafin != "") {
    $fechainicioSQL = mysqli_real_escape_string($cn, $fechainicio);
    $fechafinSQL = mysqli_real_escape_string($cn, $fechafin);

    $condicion = $condicion . " and fecharegistro between '$fechainicioSQL' and '$fechafinSQL' ";
}

if ($fechainicio != "" && $fechafin == "") {
    $fechainicioSQL = mysqli_real_escape_string($cn, $fechainicio);

    $condicion = $condicion . " and fecharegistro >= '$fechainicioSQL' ";
}

if ($fechainicio == "" && $fechafin != "") {
    $fechafinSQL = mysqli_real_escape_string($cn, $fechafin);

    $condicion = $condicion . " and fecharegistro <= '$fechafinSQL' ";
}

$sqlTotal = "select count(*) as total from tbpersona $condicion";
$fTotal = mysqli_query($cn, $sqlTotal);
$rTotal = mysqli_fetch_assoc($fTotal);
$totalRegistros = $rTotal["total"];

$sql = "select * from tbpersona
        $condicion
        order by fecharegistro desc, codigo desc
        limit $limite, $cantidad";

$f = mysqli_query($cn, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte por fechas - Superusuario</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<div class="panel-superusuario">

    <div class="contenedor-superusuario">

        <div class="encabezado-superusuario">
            <div>
                <h1>FAUSTINder</h1>
                <p>Reporte de personas registradas por rango de fechas</p>
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
                <a href="#" class="btn-dropdown-super activo-super">
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

            <form action="reporte-fecha-super.php" method="get" class="formulario-reporte-super">

                <h2>Reporte por rango de fechas</h2>

                <div class="fila-fechas-reporte">

                    <div class="grupo-datos-faustinder">
                        <label>Fecha inicio</label>
                        <input type="date" name="fechainicio" value="<?php echo $fechainicio; ?>">
                    </div>

                    <div class="grupo-datos-faustinder">
                        <label>Fecha fin</label>
                        <input type="date" name="fechafin" value="<?php echo $fechafin; ?>">
                    </div>

                </div>

              <div class="fila-botones-reporte">

    <button type="submit" class="btn-datos-faustinder">
        Buscar
    </button>

    <a href="pdf-fecha-super.php?fechainicio=<?php echo urlencode($fechainicio); ?>&fechafin=<?php echo urlencode($fechafin); ?>"
       class="btn-reporte-pdf-super"
        target="_blank">
        GENERAR PDF
    </a>

</div>

</div>
            </form>

            <div class="resumen-reporte-super">
                Total de registros encontrados: <b><?php echo $totalRegistros; ?></b>
            </div>

            <div class="tabla-usuarios-super">

                <div class="fila-reporte-fecha encabezado-tabla-super">
                    <div>Nick</div>
                    <div>Nombre completo</div>
                    <div>Celular</div>
                    <div>Escuela</div>
                    <div>Fecha registro</div>
                    <div>Estado</div>
                </div>

                <?php if (mysqli_num_rows($f) == 0) { ?>

                    <div class="sin-resultados-amigos">
                        No se encontraron usuarios registrados en ese rango de fechas.
                    </div>

                <?php } else { ?>

                    <?php while ($r = mysqli_fetch_assoc($f)) { ?>

                        <?php
                        $fechaRegistro = $r["fecharegistro"];

                        if ($fechaRegistro == "" || $fechaRegistro == NULL) {
                            $fechaRegistro = "No registrada";
                        }

                        $estado = "";

                        if (isset($r["estado"])) {
                            $estado = $r["estado"];
                        }

                        if ($estado == "I") {
                            if ($r["fechafin_inactivo"] != "" && $r["fechafin_inactivo"] != NULL) {
                                $estadoTexto = "Inactivo temporal";
                                $claseEstado = "estado-temporal-super";
                            } else {
                                $estadoTexto = "Baja definitiva";
                                $claseEstado = "estado-baja-super";
                            }
                        } else {
                            $estadoTexto = "Activo";
                            $claseEstado = "estado-activo-super";
                        }
                        ?>

                        <div class="fila-reporte-fecha">

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
                                <?php echo $r["escuela"]; ?>
                            </div>

                            <div>
                                <?php echo $fechaRegistro; ?>
                            </div>

                            <div>
                                <span class="<?php echo $claseEstado; ?>">
                                    <?php echo $estadoTexto; ?>
                                </span>
                            </div>

                        </div>

                    <?php } ?>

                <?php } ?>

            </div>

            <div class="paginacion-amigos">
                <?php
                $numpaginas = ceil($totalRegistros / $cantidad);
                for ($i = 0; $i < $numpaginas; $i++) {
                    $parametro = $i * $cantidad;
                    $numero = $i + 1;
                    $urlFechaInicio = urlencode($fechainicio);
                    $urlFechaFin = urlencode($fechafin);
                    if ($limite == $parametro) {
                        echo "<a class='pagina-activa-super' href='reporte-fecha-super.php?fechainicio=$urlFechaInicio&fechafin=$urlFechaFin&lim=$parametro'>$numero</a>";
                        } else {
                            echo "<a href='reporte-fecha-super.php?fechainicio=$urlFechaInicio&fechafin=$urlFechaFin&lim=$parametro'>$numero</a>";
                            }
                            }
                            ?>
                            </div>

        </div>

    </div>

</div>

</body>
</html>