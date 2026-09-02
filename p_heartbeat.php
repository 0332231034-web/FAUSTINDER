<?php
// auth.php ya actualiza ultima_conexion=NOW() en cada llamada.
// Este endpoint permite refrescarla periódicamente sin recargar la página.
include("auth.php");
echo "OK";
?>
