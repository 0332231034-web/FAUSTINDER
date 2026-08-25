<?php
include("conexion.php");

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro - FAUSTINDER</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<div class="contenedor-registro">

    <form action="p_register.php" method="post" class="formulario-registro">

        <h1>FAUSTINDER</h1>
        <h2>Registro</h2>

        <p class="texto-registro">
            Regístrate y conoce al amor de tu vida 
        </p>

        <?php
        if (isset($_GET["error"])) {

            if ($_GET["error"] == "vacio") {
                echo "<div class='mensaje-error'>Complete todos los campos.</div>";
            }

            if ($_GET["error"] == "nick") {
                echo "<div class='mensaje-error'>El nick ya está registrado.</div>";
            }

            if ($_GET["error"] == "correo") {
                echo "<div class='mensaje-error'>El correo ya está registrado.</div>";
            }
        }
        ?>

        <div class="grupo-registro">
            <label>Nick</label>
            <input type="text" name="txtnick" maxlength="8" required>
        </div>

        <div class="grupo-registro">
            <label>Nombre</label>
            <input type="text" name="txtnombre" maxlength="250" required>
        </div>

        <div class="grupo-registro">
            <label>Apellido paterno</label>
            <input type="text" name="txtapaterno" maxlength="250" required>
        </div>

        <div class="grupo-registro">
            <label>Apellido materno</label>
            <input type="text" name="txtamaterno" maxlength="250" required>
        </div>

        <div class="grupo-registro">
            <label>Correo</label>
            <input type="email" name="txtcorreo" maxlength="250" required>
        </div>

        <button type="submit" class="btn-registro">
            Registrarse
        </button>

        <a href="index.php" class="volver-inicio">
            Volver al inicio
        </a>

    </form>

</div>

</body>
</html>