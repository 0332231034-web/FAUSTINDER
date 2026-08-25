<?php
include("auth.php");
include("conexion.php");

$codigo = $_SESSION["codigo"];

$sql = "select * from tbpersona where codigo='$codigo'";
$f = mysqli_query($cn, $sql);
$r = mysqli_fetch_assoc($f);

$tipoActual = $r["tipo"];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Actualizar datos - FAUSTINDER</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<div class="panel-formulario-faustinder">

    <form action="p_actualizardatos.php" method="post" class="formulario-datos-faustinder formulario-compacto">

        <h1>FAUSTINDER</h1>
        <h2>Actualizar datos</h2>

        <p class="texto-formulario-faustinder">
            Completa tu perfil para usar Buscar Amigos y Ver Match.
        </p>

        <?php
        if (isset($_GET["error"])) {
            if ($_GET["error"] == "vacio") {
                echo "<div class='mensaje-error'>Complete todos los campos.</div>";
            }

            if ($_GET["error"] == "tipo") {
                echo "<div class='mensaje-error'>Seleccione al menos un tipo de interés.</div>";
            }
        }
        ?>

        <div class="grupo-datos-faustinder">
            <label>Correo</label>
            <input type="email" name="txtcorreo" maxlength="250" value="<?php echo $r["correo"]; ?>" required>
        </div>

        <div class="fila-formulario-datos">

            <div class="grupo-datos-faustinder">
                <label>Celular</label>
                <input type="text" name="txtcelular" maxlength="9" value="<?php echo $r["celular"]; ?>" required>
            </div>

            <div class="grupo-datos-faustinder">
                <label>Escuela</label>
                <select name="cboescuela" required>
                    <option value="">Seleccione</option>

                    <option value="INGENIERIA DE SISTEMAS" <?php if ($r["escuela"] == "INGENIERIA DE SISTEMAS") echo "selected"; ?>>
                        Ing. Sistemas
                    </option>

                    <option value="INGENIERIA INDUSTRIAL" <?php if ($r["escuela"] == "INGENIERIA INDUSTRIAL") echo "selected"; ?>>
                        Ing. Industrial
                    </option>

                    <option value="INGENIERIA INFORMATICA" <?php if ($r["escuela"] == "INGENIERIA INFORMATICA") echo "selected"; ?>>
                        Ing. Informática
                    </option>

                    <option value="ADMINISTRACION" <?php if ($r["escuela"] == "ADMINISTRACION") echo "selected"; ?>>
                        Administración
                    </option>

                    <option value="CONTABILIDAD" <?php if ($r["escuela"] == "CONTABILIDAD") echo "selected"; ?>>
                        Contabilidad
                    </option>

                    <option value="ENFERMERIA" <?php if ($r["escuela"] == "ENFERMERIA") echo "selected"; ?>>
                        Enfermería
                    </option>

                    <option value="EDUCACION" <?php if ($r["escuela"] == "EDUCACION") echo "selected"; ?>>
                        Educación
                    </option>

                    <option value="DERECHO" <?php if ($r["escuela"] == "DERECHO") echo "selected"; ?>>
                        Derecho
                    </option>
                </select>
            </div>

            <div class="grupo-datos-faustinder">
                <label>Sexo</label>
                <select name="cbosexo" required>
                    <option value="">Seleccione</option>

                    <option value="M" <?php if ($r["sexo"] == "M") echo "selected"; ?>>
                        Masculino
                    </option>

                    <option value="F" <?php if ($r["sexo"] == "F") echo "selected"; ?>>
                        Femenino
                    </option>
                </select>
            </div>

        </div>

        <div class="grupo-datos-faustinder">
            <label>¿Qué buscas?</label>

            <div class="opciones-interes-horizontal">

                <label>
                    <input type="checkbox" name="cbotipo[]" value="AMISTAD"
                    <?php if (strpos($tipoActual, "AMISTAD") !== false) echo "checked"; ?>>
                    Amistad
                </label>

                <label>
                    <input type="checkbox" name="cbotipo[]" value="RELACION OCASIONAL"
                    <?php if (strpos($tipoActual, "RELACION OCASIONAL") !== false) echo "checked"; ?>>
                    Relación ocasional
                </label>

                <label>
                    <input type="checkbox" name="cbotipo[]" value="RELACION FORMAL"
                    <?php if (strpos($tipoActual, "RELACION FORMAL") !== false) echo "checked"; ?>>
                    Relación formal
                </label>

            </div>
        </div>

        <div class="grupo-datos-faustinder">
            <label>Descripción</label>
            <textarea name="txtdescripcion" maxlength="250" required><?php echo $r["descripcion"]; ?></textarea>
        </div>

        <button type="submit" class="btn-datos-faustinder">
            Actualizar datos
        </button>

        <a href="principal.php" class="volver-inicio">
            Volver al principal
        </a>

    </form>

</div>

</body>
</html>