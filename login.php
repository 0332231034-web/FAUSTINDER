<?php
session_start();

if (isset($_SESSION["auth"]) && $_SESSION["auth"] == "1") {
    header("location: principal.php");
    exit();
}

if (isset($_SESSION["authsuper"]) && $_SESSION["authsuper"] == "1") {
    header("location: principal-super.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - FAUSTINder</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<div class="contenedor-login">

    <form action="p_login.php" method="post" class="formulario-login">

        <h1>FAUSTINder</h1>
        <h2>Iniciar sesión</h2>

        <?php
        if (isset($_GET["error"])) {

            if ($_GET["error"] == "datos") {
                echo "<div class='mensaje-error'>Usuario o contraseña incorrectos.</div>";
            }

            if ($_GET["error"] == "vacio") {
                echo "<div class='mensaje-error'>Complete todos los campos.</div>";
            }

            if ($_GET["error"] == "temporal") {
                $hasta = $_GET["hasta"];
                echo "<div class='mensaje-error'>Usuario reportado. Cuenta inactivada temporalmente hasta el " . htmlspecialchars($hasta) . ".</div>";
            }

            if ($_GET["error"] == "indefinido") {
                echo "<div class='mensaje-error'>Usuario reportado. Cuenta inactivada indefinidamente.</div>";
            }
        }
        ?>

        <div class="grupo-login">
            <label>Nick</label>
            <input type="text" name="txtnick" required>
        </div>

        <div class="grupo-login">
            <label>Contraseña</label>
            <input type="password" name="txtpassword" maxlength="8" required>
        </div>

        <button type="submit" class="btn-login">
            INGRESAR
        </button>

        <a href="register.php" class="link-login">
            Crear cuenta nueva
        </a>

        <a href="index.php" class="link-login">
            Volver al inicio
        </a>

    </form>

</div>

</body>
</html>
