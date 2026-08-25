<?php
include("authsuper.php");
include("conexion.php");

$sexo = "TODOS";

if (isset($_GET["sexo"])) {
    $sexo = $_GET["sexo"];
}

$cantidad = 20;

if (isset($_GET["lim"])) {
    $limite = (int)$_GET["lim"];
} else {
    $limite = 0;
}

$condicion = " where 1=1 ";

if ($sexo == "M") {
    $condicion = $condicion . " and sexo='M' ";
}

if ($sexo == "F") {
    $condicion = $condicion . " and sexo='F' ";
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
    <title>Reporte por sexo - Superusuario</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<div class="panel-superusuario">

    <div class="contenedor-superusuario">

        <div class="encabezado-superusuario">
            <div>
                <h1>FAUSTINder</h1>
                <p>Reporte de usuarios registrados por sexo</p>
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

            <form action="reporte-sexo-super.php" method="get" class="formulario-reporte-super">

                <h2>Reporte por sexo</h2>

                <div class="opciones-radio-reporte">

                    <label>
                        <input type="radio" name="sexo" value="TODOS" <?php if ($sexo == "TODOS") echo "checked"; ?>>
                        Todos
                    </label>

                    <label>
                        <input type="radio" name="sexo" value="M" <?php if ($sexo == "M") echo "checked"; ?>>
                        Masculino
                    </label>

                    <label>
                        <input type="radio" name="sexo" value="F" <?php if ($sexo == "F") echo "checked"; ?>>
                        Femenino
                    </label>

                </div>

<div class="fila-botones-reporte">

    <button type="submit" class="btn-datos-faustinder">
        Buscar
    </button>

    <a href="pdf-reporte-sexo-super.php?sexo=<?php echo urlencode($sexo); ?>"
        class="btn-reporte-pdf-super"
        target="_blank">
        GENERAR PDF
    </a>

</div>

</div>
</form>



<div class="resumen-reporte-super">
    Total de registros encontrados:
    <b><?php echo $totalRegistros; ?></b>
</div>
            <div class="tabla-usuarios-super">

                <div class="fila-reporte-sexo encabezado-tabla-super">
                    <div>Nick</div>
                    <div>Nombre completo</div>
                    <div>Celular</div>
                    <div>Escuela</div>
                    <div>Sexo</div>
                    <div>Estado</div>
                </div>

                <?php if (mysqli_num_rows($f) == 0) { ?>

                    <div class="sin-resultados-amigos">
                        No se encontraron usuarios registrados.
                    </div>

                <?php } else { ?>

                    <?php while ($r = mysqli_fetch_assoc($f)) { ?>

                        <?php
                        if ($r["sexo"] == "M") {
                            $sexoTexto = "Masculino";
                        } else if ($r["sexo"] == "F") {
                            $sexoTexto = "Femenino";
                        } else {
                            $sexoTexto = "No registrado";
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

                        <div class="fila-reporte-sexo">

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
                                <?php echo $sexoTexto; ?>
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
                    $urlSexo = urlencode($sexo);
                    if ($limite == $parametro) {
                         echo "<a class='pagina-activa-super' href='reporte-sexo-super.php?sexo=$urlSexo&lim=$parametro'>$numero</a>";
                         } else {
                            echo "<a href='reporte-sexo-super.php?sexo=$urlSexo&lim=$parametro'>$numero</a>";
                            }
                            }
                            ?>
                            </div>

        </div>

    </div>

</div>

</body>
</html>