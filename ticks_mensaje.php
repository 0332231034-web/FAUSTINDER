<?php
// Devuelve el HTML de los "ticks" de estado de un mensaje propio.
// - 1 flecha gris: enviado (siempre, ya está guardado en la BD)
// - 2 flechas grises: entregado (el receptor estuvo conectado después del envío)
// - 2 flechas azules: leído (columna leido='S')
function renderTicks($leido, $fechaEnvioMsj, $otroUltimaConexion) {
    if ($leido == 'S') {
        $clase = 'tick-leido';
    } else if ($otroUltimaConexion != NULL && strtotime($otroUltimaConexion) >= strtotime($fechaEnvioMsj)) {
        $clase = 'tick-entregado';
    } else {
        $clase = 'tick-enviado';
    }

    if ($clase == 'tick-enviado') {
        return '<span class="ticks-mensaje ' . $clase . '">&#10003;</span>';
    } else {
        return '<span class="ticks-mensaje ' . $clase . '">&#10003;&#10003;</span>';
    }
}
?>
