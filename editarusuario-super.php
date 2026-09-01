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

$r = mysqli_fetch_assoc($f);
$tipoActual = $r["tipo"];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar usuario - Superusuario</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<div class="panel-superusuario">

    <div class="contenedor-superusuario">

        <div class="encabezado-superusuario">
            <div>
                <h1>FAUSTINder</h1>
            </div>

            <a href="cerrarsesion-super.php" class="btn-salir-faustinder">
                Cerrar sesión
            </a>
        </div>

        <div class="cuerpo-superusuario">

            <form action="p_editarusuario-super.php" method="post" class="formulario-superusuario">

                <h2>Editar usuario</h2>

                <?php
                if (isset($_GET["error"])) {
                    if ($_GET["error"] == "vacio") {
                        echo "<div class='mensaje-error'>Complete todos los campos.</div>";
                    }

                    if ($_GET["error"] == "nick") {
                        echo "<div class='mensaje-error'>El nick ya está siendo usado por otro usuario.</div>";
                    }

                    if ($_GET["error"] == "correo") {
                        echo "<div class='mensaje-error'>El correo ya está siendo usado por otro usuario.</div>";
                    }

                    if ($_GET["error"] == "password") {
                        echo "<div class='mensaje-error'>La nueva contraseña debe tener exactamente 8 caracteres.</div>";
                    }
                }

                if (isset($_GET["ok"])) {
                    echo "<div class='mensaje-correcto'>Usuario actualizado correctamente.</div>";
                }
                ?>

                <input type="hidden" name="codigo" value="<?php echo htmlspecialchars($r["codigo"]); ?>">

                <div class="fila-formulario-super">
                    <div class="grupo-datos-faustinder">
                        <label>Nick</label>
                        <input type="text" name="txtnick" value="<?php echo htmlspecialchars($r["nick"]); ?>" required>
                    </div>

                    <div class="grupo-datos-faustinder">
                        <label>Correo</label>
                        <input type="email" name="txtcorreo" value="<?php echo htmlspecialchars($r["correo"]); ?>" required>
                    </div>
                </div>

                <div class="fila-formulario-super">
                    <div class="grupo-datos-faustinder">
                        <label>Nombre</label>
                        <input type="text" name="txtnombre" value="<?php echo htmlspecialchars($r["nombre"]); ?>" required>
                    </div>

                    <div class="grupo-datos-faustinder">
                        <label>Apellido paterno</label>
                        <input type="text" name="txtapaterno" value="<?php echo htmlspecialchars($r["apaterno"]); ?>" required>
                    </div>

                    <div class="grupo-datos-faustinder">
                        <label>Apellido materno</label>
                        <input type="text" name="txtamaterno" value="<?php echo htmlspecialchars($r["amaterno"]); ?>" required>
                    </div>
                </div>

                <div class="fila-formulario-super">
                    <div class="grupo-datos-faustinder">
                        <label>Celular</label>
                        <input type="text" name="txtcelular" maxlength="9" value="<?php echo htmlspecialchars($r["celular"]); ?>" required>
                    </div>

                    <div class="grupo-datos-faustinder">
                        <label>Escuela</label>
                        <select name="cboescuela" required>
                            <option value="">Seleccione</option>
                            <option value="ADMINISTRACION" <?php if ($r["escuela"] == "ADMINISTRACION") echo "selected"; ?>>Administración</option>
                            <option value="CONTABILIDAD" <?php if ($r["escuela"] == "CONTABILIDAD") echo "selected"; ?>>Contabilidad</option>
                            <option value="ECONOMIA" <?php if ($r["escuela"] == "ECONOMIA") echo "selected"; ?>>Economía</option>
                            <option value="DERECHO" <?php if ($r["escuela"] == "DERECHO") echo "selected"; ?>>Derecho</option>
                            <option value="EDUCACION" <?php if ($r["escuela"] == "EDUCACION") echo "selected"; ?>>Educación</option>
                            <option value="ENFERMERIA" <?php if ($r["escuela"] == "ENFERMERIA") echo "selected"; ?>>Enfermería</option>
                            <option value="INGENIERIA DE SISTEMAS" <?php if ($r["escuela"] == "INGENIERIA DE SISTEMAS") echo "selected"; ?>>Ingeniería de Sistemas</option>
                            <option value="INGENIERIA INFORMATICA" <?php if ($r["escuela"] == "INGENIERIA INFORMATICA") echo "selected"; ?>>Ingeniería Informática</option>
                            <option value="INGENIERIA INDUSTRIAL" <?php if ($r["escuela"] == "INGENIERIA INDUSTRIAL") echo "selected"; ?>>Ingeniería Industrial</option>
                            <option value="INGENIERIA CIVIL" <?php if ($r["escuela"] == "INGENIERIA CIVIL") echo "selected"; ?>>Ingeniería Civil</option>
                            <option value="MEDICINA HUMANA" <?php if ($r["escuela"] == "MEDICINA HUMANA") echo "selected"; ?>>Medicina Humana</option>
                            <option value="TRABAJO SOCIAL" <?php if ($r["escuela"] == "TRABAJO SOCIAL") echo "selected"; ?>>Trabajo Social</option>
                            <option value="TURISMO Y HOTELERIA" <?php if ($r["escuela"] == "TURISMO Y HOTELERIA") echo "selected"; ?>>Turismo y Hotelería</option>
                            <option value="ZOOTECNIA" <?php if ($r["escuela"] == "ZOOTECNIA") echo "selected"; ?>>Zootecnia</option>
                            <option value="INDUSTRIAS ALIMENTARIAS" <?php if ($r["escuela"] == "INDUSTRIAS ALIMENTARIAS") echo "selected"; ?>>Industrias Alimentarias</option>
                        </select>
                    </div>

                    <div class="grupo-datos-faustinder">
                        <label>Sexo</label>
                        <select name="cbosexo" required>
                            <option value="">Seleccione</option>
                            <option value="M" <?php if ($r["sexo"] == "M") echo "selected"; ?>>Masculino</option>
                            <option value="F" <?php if ($r["sexo"] == "F") echo "selected"; ?>>Femenino</option>
                        </select>
                    </div>
                </div>

                <div class="grupo-datos-faustinder">
                    <label>Tipo de interés</label>

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
                    <textarea name="txtdescripcion" maxlength="250" required><?php echo htmlspecialchars($r["descripcion"]); ?></textarea>
                </div>

                <div class="grupo-datos-faustinder">
                    <label>Nueva contraseña (opcional)</label>
                    <input type="password" name="txtnuevapassword" maxlength="8" placeholder="Déjalo vacío para no cambiarla">
                    <small style="color:#52697a; font-size:12px;">Si el usuario olvidó su contraseña, escribe una nueva de exactamente 8 caracteres. Si lo dejas vacío, su contraseña actual no se toca.</small>
                </div>

                <button type="submit" class="btn-datos-faustinder">
                    Actualizar usuario
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
