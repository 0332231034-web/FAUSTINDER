<?php
include("authsuper.php");
include("conexion.php");

$tipo = "TODOS";

if (isset($_GET["tipo"])) {
    $tipo = $_GET["tipo"];
}

$cantidad = 20;

if (isset($_GET["lim"])) {
    $limite = (int)$_GET["lim"];
} else {
    $limite = 0;
}

$condicion = " where 1=1 ";

if ($tipo != "TODOS") {
    $tipoSQL = mysqli_real_escape_string($cn, $tipo);


    $condicion = $condicion . " and tipo like '%$tipoSQL%' ";
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
    <title>Reporte por tipo - Superusuario</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<div class="panel-superusuario">

    <div class="contenedor-superusuario">

        <div class="encabezado-superusuario">
            <div>
                <h1>FAUSTINder</h1>
                <p>Reporte de usuarios registrados por tipo de interés</p>
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

            <form action="reporte-tipo-super.php" method="get" class="formulario-reporte-super">

                <h2>Reporte por tipo</h2>
                <div class="grupo-datos-faustinder">
                    <label>Tipo de interés</label>

                    <select name="tipo" required>
                        <option value="TODOS" <?php if ($tipo == "TODOS") echo "selected"; ?>>
                            Todos los tipos
                        </option>

                        <option value="AMISTAD" <?php if ($tipo == "AMISTAD") echo "selected"; ?>>
                            Amistad
                        </option>

                        <option value="RELACION OCASIONAL" <?php if ($tipo == "RELACION OCASIONAL") echo "selected"; ?>>
                            Relación ocasional
                        </option>

                        <option value="RELACION FORMAL" <?php if ($tipo == "RELACION FORMAL") echo "selected"; ?>>
                            Relación formal
                        </option>
                    </select>
                </div>

                <div class="fila-botones-reporte">

    <button type="submit" class="btn-datos-faustinder">
        Buscar
    </button>

    <a href="pdf-tipo-super.php?tipo=<?php echo urlencode($tipo); ?>"
        class="btn-reporte-pdf-super"
        target="_blank">
        GENERAR PDF
    </a>

</div>
            </form>

            <div class="resumen-reporte-super">
                Total de registros encontrados: <b><?php echo $totalRegistros; ?></b>
            </div>

            <div class="tabla-usuarios-super">

                <div class="fila-reporte-tipo encabezado-tabla-super">
                    <div>Nick</div>
                    <div>Nombre completo</div>
                    <div>Celular</div>
                    <div>Escuela</div>
                    <div>Tipo</div>
                    <div>Estado</div>
                </div>

                <?php if (mysqli_num_rows($f) == 0) { ?>

                    <div class="sin-resultados-amigos">
                        No se encontraron usuarios registrados.
                    </div>

                <?php } else { ?>

                    <?php while ($r = mysqli_fetch_assoc($f)) { ?>

                        <?php
                        $tipoTexto = $r["tipo"];

                        if ($tipoTexto == "" || $tipoTexto == NULL) {
                            $tipoTexto = "No registrado";
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

                        <div class="fila-reporte-tipo">

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
                                <?php echo $tipoTexto; ?>
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
                     $urlTipo = urlencode($tipo);
                     if ($limite == $parametro) {
                        echo "<a class='pagina-activa-super' href='reporte-tipo-super.php?tipo=$urlTipo&lim=$parametro'>$numero</a>";
                        } else {
                            echo "<a href='reporte-tipo-super.php?tipo=$urlTipo&lim=$parametro'>$numero</a>";
                            }
                            }
                            ?>
                            </div>

        </div>

    </div>

</div>

</body>
</html>