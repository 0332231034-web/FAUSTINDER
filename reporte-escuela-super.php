<?php
include("authsuper.php");
include("conexion.php");

$escuela = "TODAS";

if (isset($_GET["escuela"])) {
    $escuela = $_GET["escuela"];
}

$cantidad = 20;

if (isset($_GET["lim"])) {
    $limite = (int)$_GET["lim"];
} else {
    $limite = 0;
}

$condicion = " where 1=1 ";

if ($escuela != "TODAS") {
    $escuelaSQL = mysqli_real_escape_string($cn, $escuela);
    $condicion = $condicion . " and escuela='$escuelaSQL' ";
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
    <title>Reporte por escuela - Superusuario</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<div class="panel-superusuario">

    <div class="contenedor-superusuario">

        <div class="encabezado-superusuario">
            <div>
                <h1>FAUSTINder</h1>
                <p>Reporte de usuarios registrados por escuela</p>
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

            <form action="reporte-escuela-super.php" method="get" class="formulario-reporte-super">

                <h2>Reporte por escuela</h2>

                <div class="grupo-datos-faustinder">
                    <label>Escuela</label>

                    <select name="escuela" required>
                        <option value="TODAS" <?php if ($escuela == "TODAS") echo "selected"; ?>>
                            Todas las escuelas
                        </option>

                        <option value="ADMINISTRACION" <?php if ($escuela == "ADMINISTRACION") echo "selected"; ?>>
                            Administración
                        </option>

                        <option value="CONTABILIDAD" <?php if ($escuela == "CONTABILIDAD") echo "selected"; ?>>
                            Contabilidad
                        </option>

                        <option value="ECONOMIA" <?php if ($escuela == "ECONOMIA") echo "selected"; ?>>
                            Economía
                        </option>

                        <option value="DERECHO" <?php if ($escuela == "DERECHO") echo "selected"; ?>>
                            Derecho
                        </option>

                        <option value="EDUCACION" <?php if ($escuela == "EDUCACION") echo "selected"; ?>>
                            Educación
                        </option>

                        <option value="ENFERMERIA" <?php if ($escuela == "ENFERMERIA") echo "selected"; ?>>
                            Enfermería
                        </option>

                        <option value="INGENIERIA DE SISTEMAS" <?php if ($escuela == "INGENIERIA DE SISTEMAS") echo "selected"; ?>>
                            Ingeniería de Sistemas
                        </option>

                        <option value="INGENIERIA INFORMATICA" <?php if ($escuela == "INGENIERIA INFORMATICA") echo "selected"; ?>>
                            Ingeniería Informática
                        </option>

                        <option value="INGENIERIA INDUSTRIAL" <?php if ($escuela == "INGENIERIA INDUSTRIAL") echo "selected"; ?>>
                            Ingeniería Industrial
                        </option>

                        <option value="INGENIERIA CIVIL" <?php if ($escuela == "INGENIERIA CIVIL") echo "selected"; ?>>
                            Ingeniería Civil
                        </option>

                        <option value="MEDICINA HUMANA" <?php if ($escuela == "MEDICINA HUMANA") echo "selected"; ?>>
                            Medicina Humana
                        </option>

                        <option value="TRABAJO SOCIAL" <?php if ($escuela == "TRABAJO SOCIAL") echo "selected"; ?>>
                            Trabajo Social
                        </option>

                        <option value="TURISMO Y HOTELERIA" <?php if ($escuela == "TURISMO Y HOTELERIA") echo "selected"; ?>>
                            Turismo y Hotelería
                        </option>

                        <option value="ZOOTECNIA" <?php if ($escuela == "ZOOTECNIA") echo "selected"; ?>>
                            Zootecnia
                        </option>

                        <option value="INDUSTRIAS ALIMENTARIAS" <?php if ($escuela == "INDUSTRIAS ALIMENTARIAS") echo "selected"; ?>>
                            Industrias Alimentarias
                        </option>
                    </select>
                </div>

               <div class="fila-botones-reporte">

    <button type="submit" class="btn-datos-faustinder">
        Buscar
    </button>

    <a href="pdf-escuela-super.php?escuela=<?php echo urlencode($escuela); ?>"
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

                <div class="fila-reporte-escuela encabezado-tabla-super">
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

                        <div class="fila-reporte-escuela">

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
                $urlEscuela = urlencode($escuela);
                if ($limite == $parametro) {
                    echo "<a class='pagina-activa-super' href='reporte-escuela-super.php?escuela=$urlEscuela&lim=$parametro'>$numero</a>";
                    } else {
                        echo "<a href='reporte-escuela-super.php?escuela=$urlEscuela&lim=$parametro'>$numero</a>";
                        }
                        }
                        ?>
                        </div>

        </div>

    </div>

</div>

</body>
</html>