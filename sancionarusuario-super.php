<?php
include("authsuper.php");
include("conexion.php");

if (!isset($_GET["codigo"])) {
    header("location: usuarios-super.php");
    exit();
}

$codigoPersona = mysqli_real_escape_string($cn, $_GET["codigo"]);

$sql = "select * from tbpersona where codigo='$codigoPersona'";
$f = mysqli_query($cn, $sql);

if (mysqli_num_rows($f) == 0) {
    header("location: usuarios-super.php");
    exit();
}

$p = mysqli_fetch_assoc($f);

$sqlReportes = "select * from tbreporte
                where reportado='$codigoPersona'
                order by codigo desc";

$fReportes = mysqli_query($cn, $sqlReportes);

$nombreCompleto = $p["nombre"] . " " . $p["apaterno"] . " " . $p["amaterno"];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Usuario reportado - Superusuario</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<div class="panel-superusuario">

    <div class="contenedor-superusuario">

        <div class="encabezado-superusuario">
            <div>
                <h1>FAUSTINder</h1>
                <p>Atención de usuario reportado</p>
            </div>

            <a href="cerrarsesion-super.php" class="btn-salir-faustinder">
                Cerrar sesión
            </a>
        </div>

        <div class="cuerpo-superusuario">

            <form action="p_sancionarusuario-super.php" method="post" class="formulario-superusuario">

                <h2>Usuario reportado</h2>

                <?php
                if (isset($_GET["error"])) {
                    echo "<div class='mensaje-error'>Complete los datos de la sanción.</div>";
                }

                if (isset($_GET["ok"])) {
                    echo "<div class='mensaje-correcto'>Sanción aplicada correctamente.</div>";
                }
                ?>

                <input type="hidden" name="codigo" value="<?php echo htmlspecialchars($codigoPersona); ?>">

                <div class="tabla-detalle-reportado">

                    <table>
                        <tr>
                            <th>Nick</th>
                            <td><?php echo htmlspecialchars($p["nick"]); ?></td>
                        </tr>

                        <tr>
                            <th>Nombre</th>
                            <td><?php echo htmlspecialchars($nombreCompleto); ?></td>
                        </tr>

                        <tr>
                            <th>Celular</th>
                            <td><?php echo htmlspecialchars($p["celular"]); ?></td>
                        </tr>

                        <tr>
                            <th>Estado actual</th>
                            <td><?php echo htmlspecialchars($p["estado"]); ?></td>
                        </tr>
                    </table>

                </div>

                <h3 class="titulo-reportes-super">Reportes recibidos</h3>

                <div class="lista-reportes-super">

                    <?php if (mysqli_num_rows($fReportes) == 0) { ?>

                        <div class="sin-resultados-amigos">
                            Este usuario no tiene reportes registrados.
                        </div>

                    <?php } else { ?>

                        <?php while ($rep = mysqli_fetch_assoc($fReportes)) { ?>

                            <?php
                            $codigoReportante = mysqli_real_escape_string($cn, $rep["reportante"]);
                            $sqlReportante = "select * from tbpersona where codigo='$codigoReportante'";
                            $fReportante = mysqli_query($cn, $sqlReportante);
                            $reportante = mysqli_fetch_assoc($fReportante);
                            ?>

                            <div class="card-reporte-super">
                                <p><b>Reportante:</b> <?php echo htmlspecialchars($reportante["nick"]); ?></p>
                                <p><b>Tipo:</b> <?php echo htmlspecialchars($rep["tipo_reporte"]); ?></p>
                                <p><b>Motivo:</b> <?php echo htmlspecialchars($rep["motivo"]); ?></p>
                                <p><b>Fecha:</b> <?php echo htmlspecialchars($rep["fecha_reporte"]); ?></p>
                                <p><b>Estado:</b> <?php echo htmlspecialchars($rep["estado"]); ?></p>
                            </div>

                        <?php } ?>

                    <?php } ?>

                </div>

                <h3 class="titulo-reportes-super">Aplicar sanción</h3>

                <div class="grupo-datos-faustinder">
                    <label>Acción</label>
                    <select name="cboaccion" required>
                        <option value="">Seleccione acción</option>
                        <option value="ACTIVAR">Activar cuenta</option>
                        <option value="INACTIVAR_1">Inactivar por 1 día</option>
                        <option value="INACTIVAR_2">Inactivar por 2 días</option>
                        <option value="INACTIVAR_3">Inactivar por 3 días</option>
                        <option value="INACTIVAR_7">Inactivar por 7 días</option>
                        <option value="INACTIVAR_15">Inactivar por 15 días</option>
                        <option value="INACTIVAR_30">Inactivar por 30 días</option>
                        <option value="BAJA_INDEFINIDA">Dar de baja indefinidamente</option>
                    </select>
                </div>

                <div class="grupo-datos-faustinder">
                    <label>Mensaje para el usuario</label>
                    <textarea name="txtmotivo" maxlength="250" required placeholder="Cuenta inactivada por reportes recibidos."></textarea>
                </div>

                <button type="submit" class="btn-datos-faustinder">
                    Aplicar sanción
                </button>

                <a href="usuarios-super.php" class="volver-inicio">
                    Volver a usuarios
                </a>

            </form>

        </div>

    </div>

</div>

</body>
</html>
