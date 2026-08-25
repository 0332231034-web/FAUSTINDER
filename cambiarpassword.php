<?php
include("auth.php");
include("conexion.php");

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Cambiar contraseña - FAUSTINDER</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<div class="panel-formulario-faustinder">

    <form action="p_cambiarpassword.php" method="post" class="formulario-datos-faustinder">

        <h1>FAUSTINDER</h1>
        <h2>Cambiar contraseña</h2>

        <p class="texto-formulario-faustinder">
            Primero ingresa tu contraseña actual y luego registra una nueva contraseña.
        </p>

        <?php
        if (isset($_GET["error"])) {

            if ($_GET["error"] == "vacio") {
                echo "<div class='mensaje-error'>Complete todos los campos.</div>";
            }

            if ($_GET["error"] == "actual") {
                echo "<div class='mensaje-error'>La contraseña actual es incorrecta.</div>";
            }

            if ($_GET["error"] == "diferente") {
                echo "<div class='mensaje-error'>Las nuevas contraseñas no coinciden.</div>";
            }

            if ($_GET["error"] == "longitud") {
                echo "<div class='mensaje-error'>La contraseña debe tener exactamente 8 caracteres.</div>";
            }
        }

        if (isset($_GET["ok"])) {
            echo "<div class='mensaje-correcto'>Contraseña cambiada correctamente.</div>";
        }
        ?>

        <div class="grupo-datos-faustinder">
            <label>Contraseña actual</label>
            <input type="password" name="pwdactual" maxlength="8" required>
        </div>

        <div class="grupo-datos-faustinder">
            <label>Nueva contraseña</label>
            <input type="password" name="pwdpass" maxlength="8" required>
        </div>

        <div class="grupo-datos-faustinder">
            <label>Repetir nueva contraseña</label>
            <input type="password" name="pwdrepass" maxlength="8" required>
        </div>

        <button type="submit" class="btn-datos-faustinder">
            Cambiar contraseña
        </button>

        <a href="principal.php" class="volver-inicio">
            Volver al principal
        </a>

    </form>

</div>

</body>
</html>