<?php
include("auth.php");
include("conexion.php");

$codigo = $_SESSION["codigo"];

$sqlUsuario = "select * from tbpersona where codigo='$codigo'";
$fUsuario = mysqli_query($cn, $sqlUsuario);
$usuario = mysqli_fetch_assoc($fUsuario);

$nombreCompleto = $usuario["nombre"] . " " . $usuario["apaterno"] . " " . $usuario["amaterno"];

$escuela = "TODAS";
$sexo = "TODOS";
$tipo = "TODOS";

if (isset($_GET["escuela"])) {
    $escuela = $_GET["escuela"];
}

if (isset($_GET["sexo"])) {
    $sexo = $_GET["sexo"];
}

if (isset($_GET["tipo"])) {
    $tipo = $_GET["tipo"];
}

$cantidad = 4;
$limite = 0;

if (isset($_GET["lim"])) {
    $limite = (int)$_GET["lim"];
}

$condicion = " where codigo <> '$codigo'
               and estado = 'A'
               and celular is not null and celular <> ''
               and escuela is not null and escuela <> ''
               and sexo is not null and sexo <> ''
               and tipo is not null and tipo <> ''
               and descripcion is not null and descripcion <> '' ";

if ($escuela != "TODAS") {
    $escuelaSQL = mysqli_real_escape_string($cn, $escuela);
    $condicion = $condicion . " and escuela='$escuelaSQL' ";
}

if ($sexo != "TODOS") {
    $sexoSQL = mysqli_real_escape_string($cn, $sexo);
    $condicion = $condicion . " and sexo='$sexoSQL' ";
}

if ($tipo != "TODOS") {
    $tipoSQL = mysqli_real_escape_string($cn, $tipo);
    $condicion = $condicion . " and tipo like '%$tipoSQL%' ";
}

$sqlTotal = "select count(*) as total from tbpersona $condicion";
$fTotal = mysqli_query($cn, $sqlTotal);
$rTotal = mysqli_fetch_assoc($fTotal);
$total = $rTotal["total"];

$sql = "select * from tbpersona
        $condicion
        order by nombre asc
        limit $limite, $cantidad";

$f = mysqli_query($cn, $sql);

$numpaginas = ceil($total / $cantidad);

$modoOscuro = isset($_COOKIE["modo_oscuro"]) && $_COOKIE["modo_oscuro"] == "1";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Resultados - FAUSTINDER</title>
    <link rel="stylesheet" href="css/estilo.css">
    <link rel="stylesheet" href="css/modo-oscuro.css">
</head>
<body class="<?php echo $modoOscuro ? 'tema-oscuro' : ''; ?>">

<div class="panel-principal-faustinder">

    <div class="tarjeta-principal-faustinder">

        <div class="encabezado-faustinder">
            <div>
                <h1>FAUSTINDER</h1>
                <p>Resultados de búsqueda</p>
            </div>

            <a href="cerrarsesion.php" class="btn-salir-faustinder">
                Cerrar sesión
            </a>
        </div>

        <div class="menu-faustinder-moderno">

            <a href="principal.php">
                Principal
            </a>

            <div class="dropdown-datos">
                <a href="#" class="btn-dropdown">
                    Tus Datos
                </a>

                <div class="contenido-dropdown">
                    <a href="actualizardatos.php">Actualizar datos</a>
                    <a href="imagenperfil.php">Insertar foto de perfil</a>
                    <a href="cambiarpassword.php">Cambiar password</a>
                </div>
            </div>

            <a href="buscaramigos.php" class="activo-faustinder">
                Buscar Amigos
            </a>

            <a href="vermatch.php">
                Ver Match
            </a>

            <a href="chats.php">
                Mensajes
            </a>

        </div>

        <div class="cuerpo-principal-faustinder">

            <h2>Bienvenido, <?php echo htmlspecialchars($nombreCompleto); ?></h2>

            <p>
                Personas encontradas según los filtros seleccionados.
            </p>

            <?php if ($total == 0) { ?>

                <div class="sin-resultados-amigos">
                    No hay alumnos registrados con esos filtros.
                </div>

                <a href="buscaramigos.php" class="btn-nueva-busqueda">
                    Nueva búsqueda
                </a>

            <?php } else { ?>

                <div class="tabla-resultados-amigos">

                    <?php while ($r = mysqli_fetch_assoc($f)) { ?>

                        <?php
                        $codigoAmigo = $r["codigo"];
                        $nombreAmigo = $r["nick"];

                        $codigoAmigoSQL = mysqli_real_escape_string($cn, $codigoAmigo);
                        $sqlFotoAmigo = "select ruta from tbfoto where codigo_persona='$codigoAmigoSQL' and principal='S' limit 1";
                        $fFotoAmigo = mysqli_query($cn, $sqlFotoAmigo);
                        $tieneFotoAmigo = mysqli_num_rows($fFotoAmigo) > 0;

                        if ($tieneFotoAmigo) {
                            $fotoAmigoData = mysqli_fetch_assoc($fFotoAmigo);
                            $fotoAmigoWeb = $fotoAmigoData["ruta"];
                        }
                        ?>

                        <div class="fila-resultado-amigo">

                            <div class="col-foto-amigo">
                                <?php if ($tieneFotoAmigo) { ?>
                                    <img src="<?php echo $fotoAmigoWeb . '?v=' . time(); ?>" alt="Foto">
                                <?php } else { ?>
                                    <div class="sin-foto-amigo">❤</div>
                                <?php } ?>
                            </div>

                            <div class="col-nombre-amigo">
                                <?php echo htmlspecialchars($nombreAmigo); ?>
                            </div>

                            <div class="col-escuela-amigo">
                                <?php echo htmlspecialchars($r["escuela"]); ?>
                            </div>

                            <div class="col-sexo-amigo">
                                <?php echo htmlspecialchars($r["sexo"]); ?>
                            </div>

                            <div class="col-match-amigo">
                                <a href="confirmacion.php?codigo=<?php echo $codigoAmigo; ?>">
                                    Solicitar match
                                </a>
                            </div>

                        </div>

                    <?php } ?>

                </div>

                <div class="paginacion-amigos">

                    <?php
                    for ($i = 0; $i < $numpaginas; $i++) {

                        $parametro = $i * $cantidad;
                        $numero = $i + 1;

                        $urlEscuela = urlencode($escuela);
                        $urlSexo = urlencode($sexo);
                        $urlTipo = urlencode($tipo);

                        echo "<a href='encontrar.php?escuela=$urlEscuela&sexo=$urlSexo&tipo=$urlTipo&lim=$parametro'>$numero</a>";
                    }
                    ?>

                </div>

                <a href="buscaramigos.php" class="btn-nueva-busqueda">
                    Nueva búsqueda
                </a>

            <?php } ?>

        </div>

    </div>

</div>

<script src="js/modo-oscuro.js"></script>

</body>
</html>
