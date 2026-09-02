<?php
include("auth.php");
include("conexion.php");
header('Content-Type: application/json');

$codigoUsuario = $_SESSION["codigo"];
$codigoUsuarioSQL = mysqli_real_escape_string($cn, $codigoUsuario);

if (!isset($_GET["match"])) {
    echo json_encode(["error" => true]);
    exit();
}

$codigoMatch = mysqli_real_escape_string($cn, $_GET["match"]);

// Validar que el match existe, está ACEPTADO, y que el usuario actual participa en él
$sqlMatch = "select * from tbmatch where codigo='$codigoMatch' and estado='ACEPTADO'
             and (solicitante='$codigoUsuarioSQL' or receptor='$codigoUsuarioSQL')";
$fMatch = mysqli_query($cn, $sqlMatch);

if (!$fMatch || mysqli_num_rows($fMatch) == 0) {
    echo json_encode(["error" => true]);
    exit();
}

$match = mysqli_fetch_assoc($fMatch);

if ($match["solicitante"] == $codigoUsuario) {
    $codigoOtro = $match["receptor"];
} else {
    $codigoOtro = $match["solicitante"];
}

$codigoOtroSQL = mysqli_real_escape_string($cn, $codigoOtro);

// Estado "en línea" — no depende de columnas nuevas, siempre funciona.
// El cálculo ocurre dentro de MySQL (TIMESTAMPDIFF) para no mezclar
// el reloj de PHP con el de MySQL.
$sqlOtro = "select ultima_conexion, TIMESTAMPDIFF(SECOND, ultima_conexion, NOW()) as segundos_conexion
            from tbpersona where codigo='$codigoOtroSQL'";
$fOtro = mysqli_query($cn, $sqlOtro);

$enLinea = false;
$texto = "Sin conexión reciente";

if ($fOtro && mysqli_num_rows($fOtro) > 0) {
    $otro = mysqli_fetch_assoc($fOtro);
    if ($otro["ultima_conexion"] != NULL) {
        $segundos = (int)$otro["segundos_conexion"];
        $minutos = intdiv($segundos, 60);

        if ($segundos < 120) {
            $enLinea = true;
            $texto = "En línea";
        } else if ($minutos < 60) {
            $texto = "Activo hace " . $minutos . " min";
        } else if ($minutos < 1440) {
            $texto = "Activo hace " . intdiv($minutos, 60) . " h";
        } else {
            $texto = "Activo hace " . intdiv($minutos, 1440) . " días";
        }
    }
}

// Estado "escribiendo" — requiere las columnas escribiendo_match / escribiendo_fecha
// (ver migracion_escribiendo.sql). Si todavía no se corrió esa migración, esta
// consulta simplemente falla y se ignora, sin romper el resto del endpoint.
// (PHP 8.1+ hace que mysqli lance excepción en vez de solo devolver false,
// por eso el try/catch en lugar de solo revisar el valor de retorno)
$escribiendo = false;
$debugEscribiendoMatch = null;
$debugSegundos = null;
try {
    $sqlEscribiendo = "select escribiendo_match, TIMESTAMPDIFF(SECOND, escribiendo_fecha, NOW()) as segundos_escribiendo
                        from tbpersona where codigo='$codigoOtroSQL'";
    $fEscribiendo = mysqli_query($cn, $sqlEscribiendo);

    if ($fEscribiendo && mysqli_num_rows($fEscribiendo) > 0) {
        $rEscribiendo = mysqli_fetch_assoc($fEscribiendo);
        $debugEscribiendoMatch = $rEscribiendo["escribiendo_match"];
        $debugSegundos = $rEscribiendo["segundos_escribiendo"];

        if ($rEscribiendo["escribiendo_match"] == $codigoMatch
            && $rEscribiendo["segundos_escribiendo"] !== NULL
            && (int)$rEscribiendo["segundos_escribiendo"] < 6) {
            $escribiendo = true;
        }
    }
} catch (Throwable $e) {
    $escribiendo = false;
}

echo json_encode([
    "en_linea" => $enLinea,
    "texto" => $texto,
    "escribiendo" => $escribiendo,
    "debug_codigo_otro" => $codigoOtro,
    "debug_match_actual" => $codigoMatch,
    "debug_escribiendo_match_guardado" => $debugEscribiendoMatch,
    "debug_segundos_desde_ultima_tecla" => $debugSegundos
]);
?>
