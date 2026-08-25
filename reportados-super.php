<?php
include("authsuper.php");
include("conexion.php");

$cantidad = 20;

if (isset($_GET["lim"])) {
    $limite = (int)$_GET["lim"];
} else {
    $limite = 0;
}

$sqlTotal = "select count(*) as total from tbreporte";
$fTotal = mysqli_query($cn, $sqlTotal);
$rTotal = mysqli_fetch_assoc($fTotal);
$totalRegistros = $rTotal["total"];

$sql = "select * from tbreporte
        order by codigo desc
        limit $limite, $cantidad";

$f = mysqli_query($cn, $sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Personas reportadas - Superusuario</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<div class="panel-superusuario">

    <div class="contenedor-superusuario">

        <div class="encabezado-superusuario">
            <div>
                <h1>FAUSTINder</h1>
                <p>Lista de personas reportadas por los usuarios</p>
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

            <div class="formulario-reporte-super">

                <h2>Personas reportadas</h2>
    <a href="pdf-reportados-super.php" target="_blank" class="btn-reporte-pdf-super">
        Generar PDF
    </a>
            </div>

            <div class="resumen-reporte-super">
                Total de reportes registrados: <b><?php echo $totalRegistros; ?></b>
            </div>

            <div class="tabla-usuarios-super">

                <div class="fila-reportados-super encabezado-tabla-super">
                    <div>Reportado</div>
                    <div>Reportante</div>
                    <div>Tipo</div>
                    <div>Motivo</div>
                    <div>Fecha</div>
                    <div>Estado</div>
                    <div>Acción</div>
                </div>

                <?php if (mysqli_num_rows($f) == 0) { ?>

                    <div class="sin-resultados-amigos">
                        No hay personas reportadas.
                    </div>

                <?php } else { ?>

                    <?php while ($r = mysqli_fetch_assoc($f)) { ?>

                        <?php
                        $codigoReportado = $r["reportado"];
                        $codigoReportante = $r["reportante"];

                        $sqlReportado = "select * from tbpersona where codigo='$codigoReportado'";
                        $fReportado = mysqli_query($cn, $sqlReportado);

                        if (mysqli_num_rows($fReportado) > 0) {
                            $reportado = mysqli_fetch_assoc($fReportado);
                            $nombreReportado = $reportado["nick"];
                        } else {
                            $nombreReportado = "Usuario eliminado";
                        }

                        $sqlReportante = "select * from tbpersona where codigo='$codigoReportante'";
                        $fReportante = mysqli_query($cn, $sqlReportante);

                        if (mysqli_num_rows($fReportante) > 0) {
                            $reportante = mysqli_fetch_assoc($fReportante);
                            $nombreReportante = $reportante["nick"];
                        } else {
                            $nombreReportante = "Usuario eliminado";
                        }

                        if ($r["estado"] == "PENDIENTE") {
                            $claseEstadoReporte = "estado-pendiente-reporte";
                            $textoEstadoReporte = "Pendiente";
                        } else {
                            $claseEstadoReporte = "estado-atendido-reporte";
                            $textoEstadoReporte = "Atendido";
                        }
                        ?>

                        <div class="fila-reportados-super">

                            <div>
                                <?php echo $nombreReportado; ?>
                            </div>

                            <div>
                                <?php echo $nombreReportante; ?>
                            </div>

                            <div>
                                <?php echo $r["tipo_reporte"]; ?>
                            </div>

                            <div>
                                <?php echo $r["motivo"]; ?>
                            </div>

                            <div>
                                <?php echo $r["fecha_reporte"]; ?>
                            </div>

                            <div>
                                <span class="<?php echo $claseEstadoReporte; ?>">
                                    <?php echo $textoEstadoReporte; ?>
                                </span>
                            </div>

                            <div>
                                <?php if ($r["estado"] == "PENDIENTE") { ?>

                                    <a href="sancionarusuario-super.php?codigo=<?php echo $codigoReportado; ?>" class="btn-reportado-super">
                                        Atender
                                    </a>

                                <?php } else { ?>

                                    <a href="sancionarusuario-super.php?codigo=<?php echo $codigoReportado; ?>" class="btn-editar-super">
                                        Ver
                                    </a>

                                <?php } ?>
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
                    if ($limite == $parametro) {
                        echo "<a class='pagina-activa-super' href='reportados-super.php?lim=$parametro'>$numero</a>";
                        } else {
                            echo "<a href='reportados-super.php?lim=$parametro'>$numero</a>";
                            }
                            }
                            ?>
                            </div>

        </div>

    </div>

</div>

</body>
</html>