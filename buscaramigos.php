<?php
include("auth.php");
include("conexion.php");
/** @var mysqli $cn */

$codigo = $_SESSION["codigo"];

$sql = "select * from tbpersona where codigo='$codigo'";
$f = mysqli_query($cn, $sql);
$r = mysqli_fetch_assoc($f);

$nombreCompleto = $r["nombre"] . " " . $r["apaterno"] . " " . $r["amaterno"];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Buscar Amigos - FAUSTINDER</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<div class="panel-principal-faustinder">

    <div class="tarjeta-principal-faustinder">

        <div class="encabezado-faustinder">
            <div>
                <h1>FAUSTINDER</h1>
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

        </div>

        <div class="cuerpo-principal-faustinder">

            <h2>Bienvenido, <?php echo $nombreCompleto; ?></h2>

            <form action="encontrar.php" method="get" class="formulario-buscar-amigos">

                <div class="fila-filtros-amigos">

                    <div class="grupo-buscar-amigos">
                        <label>Escuela</label>
                        <select name="escuela">
                            <option value="TODAS">Todas las escuelas</option>
                            <option value="ADMINISTRACION">Administración</option>
                            <option value="CONTABILIDAD">Contabilidad</option>
                            <option value="ECONOMIA">Economía</option>
                            <option value="DERECHO">Derecho</option>
                            <option value="EDUCACION">Educación</option>
                            <option value="ENFERMERIA">Enfermería</option>
                            <option value="INGENIERIA DE SISTEMAS">Ingeniería de Sistemas</option>
                            <option value="INGENIERIA INFORMATICA">Ingeniería Informática</option>
                            <option value="INGENIERIA INDUSTRIAL">Ingeniería Industrial</option>
                            <option value="INGENIERIA CIVIL">Ingeniería Civil</option>
                            <option value="MEDICINA HUMANA">Medicina Humana</option>
                            <option value="TRABAJO SOCIAL">Trabajo Social</option>
                            <option value="TURISMO Y HOTELERIA">Turismo y Hotelería</option>
                            <option value="ZOOTECNIA">Zootecnia</option>
                            <option value="INDUSTRIAS ALIMENTARIAS">Industrias Alimentarias</option>
                        </select>
                    </div>

                    <div class="grupo-buscar-amigos">
                        <label>Sexo</label>
                        <select name="sexo">
                            <option value="TODOS">Todos los géneros</option>
                            <option value="M">Masculino</option>
                            <option value="F">Femenino</option>
                        </select>
                    </div>

                    <div class="grupo-buscar-amigos">
                        <label>Tipo</label>
                        <select name="tipo">
                            <option value="TODOS">Todo tipo</option>
                            <option value="AMISTAD">Amistad</option>
                            <option value="RELACION OCASIONAL">Relación ocasional</option>
                            <option value="RELACION FORMAL">Relación formal</option>
                        </select>
                    </div>

                </div>

                <button type="submit" class="btn-buscar-amigos">
                    Buscar
                </button>

            </form>

        </div>

    </div>

</div>

</body>
</html>