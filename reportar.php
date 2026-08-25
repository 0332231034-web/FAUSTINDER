<?php
include("auth.php");
include("conexion.php");

$codigoUsuario = $_SESSION["codigo"];

if (!isset($_GET["codigo"])) {
    header("location: vermatch.php");
    exit();
}

$codigoPersona = mysqli_real_escape_string($cn, $_GET["codigo"]);

if ($codigoPersona == $codigoUsuario) {
    header("location: vermatch.php");
    exit();
}

$sql = "select * from tbpersona where codigo='$codigoPersona' and estado='A'";
$f = mysqli_query($cn, $sql);

if (mysqli_num_rows($f) == 0) {
    header("location: vermatch.php");
    exit();
}

$p = mysqli_fetch_assoc($f);

$nombrePersona = $p["nick"];

$sqlFotoPersona = "select ruta from tbfoto where codigo_persona='$codigoPersona' and principal='S' limit 1";
$fFotoPersona = mysqli_query($cn, $sqlFotoPersona);
$tieneFotoPersona = mysqli_num_rows($fFotoPersona) > 0;

if ($tieneFotoPersona) {
    $fotoPersonaData = mysqli_fetch_assoc($fFotoPersona);
    $fotoWeb = $fotoPersonaData["ruta"];
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reportar usuario - FAUSTINder</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>

<div class="panel-formulario-faustinder">

    <form action="p_reportar.php" method="post" class="formulario-datos-faustinder">

        <h1>FAUSTINder</h1>
        <h2>Reportar usuario</h2>

        <p class="texto-formulario-faustinder">
            Completa el tipo de reporte y describe el motivo.
        </p>

        <?php
        if (isset($_GET["error"])) {
            if ($_GET["error"] == "vacio") {
                echo "<div class='mensaje-error'>Complete todos los campos.</div>";
            }
        }
        ?>

        <input type="hidden" name="codigoreportado" value="<?php echo htmlspecialchars($codigoPersona); ?>">

        <div class="vista-foto-perfil">
            <?php if ($tieneFotoPersona) { ?>
                <img src="<?php echo $fotoWeb . '?v=' . time(); ?>" alt="Foto">
            <?php } else { ?>
                <div class="sin-foto-perfil">
                    Sin foto
                </div>
            <?php } ?>
        </div>

        <div class="grupo-datos-faustinder">
            <label>Usuario reportado</label>
            <input type="text" value="<?php echo htmlspecialchars($nombrePersona); ?>" disabled>
        </div>

        <div class="grupo-datos-faustinder">
            <label>Tipo de reporte</label>
            <select name="cbotipo" required>
                <option value="">Seleccione tipo de reporte</option>
                <option value="SPAM">Spam</option>
                <option value="ACOSO">Acoso</option>
                <option value="CUENTA FALSA">Cuenta falsa</option>
                <option value="CONTENIDO INAPROPIADO">Contenido inapropiado</option>
                <option value="INSULTOS">Insultos</option>
                <option value="SUPLANTACION">Suplantación</option>
                <option value="OTRO">Otro</option>
            </select>
        </div>

        <div class="grupo-datos-faustinder">
            <label>Motivo específico</label>
            <textarea name="txtmotivo" maxlength="250" required placeholder="Escribe aquí el motivo del reporte..."></textarea>
        </div>

        <button type="submit" class="btn-datos-faustinder">
            Enviar reporte
        </button>

        <a href="vermatch.php" class="volver-inicio">
            Cancelar
        </a>

    </form>

</div>

</body>
</html>
