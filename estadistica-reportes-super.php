<?php
include("authsuper.php");
include("conexion.php");

$sqlTotalUsuarios = "select count(*) as total from tbpersona";
$fTotalUsuarios = mysqli_query($cn, $sqlTotalUsuarios);
$rTotalUsuarios = mysqli_fetch_assoc($fTotalUsuarios);
$totalUsuarios = $rTotalUsuarios["total"];


$sqlReportados = "select count(distinct reportado) as total from tbreporte";
$fReportados = mysqli_query($cn, $sqlReportados);
$rReportados = mysqli_fetch_assoc($fReportados);
$totalReportados = $rReportados["total"];


$totalNoReportados = $totalUsuarios - $totalReportados;


if ($totalNoReportados < 0) {
    $totalNoReportados = 0;
}

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Estadística de reportes - Superusuario</title>

<link rel="stylesheet" href="css/estilo.css">

</head>


<body>


<div class="panel-superusuario">


<div class="contenedor-superusuario">



<div class="encabezado-superusuario">

<div>

<h1>FAUSTINder</h1>

<p>Estadística de usuarios reportados y no reportados</p>

</div>


<a href="cerrarsesion-super.php" class="btn-salir-faustinder">

Cerrar sesión

</a>


</div>





<div class="menu-superusuario">



<a href="principal-super.php">
Principal
</a>




<div class="dropdown-super">

<a href="#" class="btn-dropdown-super">
Mis Datos
</a>


<div class="contenido-dropdown-super">

<a href="actualizardatos-super.php">
Actualizar datos
</a>

<a href="imagenperfil-super.php">
Subir foto
</a>

<a href="cambiarpassword-super.php">
Cambiar contraseña
</a>


</div>


</div>





<div class="dropdown-super">

<a href="#" class="btn-dropdown-super">
Usuarios ▾
</a>


<div class="contenido-dropdown-super">


<a href="usuarios-super.php">
Buscar / Editar usuarios
</a>


</div>


</div>





<div class="dropdown-super">


<a href="#" class="btn-dropdown-super">
Reportes
</a>



<div class="contenido-dropdown-super">


<a href="reporte-sexo-super.php">
Por sexo
</a>


<a href="reporte-escuela-super.php">
Por escuela
</a>


<a href="reporte-tipo-super.php">
Por tipo
</a>


<a href="reporte-fecha-super.php">
Por rango de fechas
</a>


<a href="reporte-estado-super.php">
Activos / Inactivos
</a>


<a href="reportados-super.php">
Personas reportadas
</a>



</div>


</div>






<div class="dropdown-super">


<a href="#" class="btn-dropdown-super activo-super">
Estadísticas
</a>



<div class="contenido-dropdown-super">


<a href="estadistica-sexo-super.php">
Usuarios por sexo
</a>


<a href="estadistica-estado-super.php">
Activos e inactivos
</a>


<a href="estadistica-reportes-super.php">
Reportados y no reportados
</a>



</div>



</div>



</div>







<div class="cuerpo-superusuario">





<div class="tarjeta-estadistica-super">


<h2>
Usuarios reportados y no reportados
</h2>




<div class="contenedor-grafico-super">


<canvas id="graficoReportes"></canvas>


</div>




</div>





</div>






</div>


</div>





<script src="chartjs/chart.umd.min.js"></script>



<script>


const ctxReportes = document.getElementById('graficoReportes');



new Chart(ctxReportes, {



type: 'doughnut',




data: {



labels: [

'Reportados: <?php echo $totalReportados; ?>',

'No reportados: <?php echo $totalNoReportados; ?>'

],



datasets: [{



label: 'Usuarios',



data: [


<?php echo $totalReportados; ?>,


<?php echo $totalNoReportados; ?>


],



backgroundColor: [

'#e83e8c',

'#1e5aa0'

],



borderWidth: 1



}]



},






options: {



responsive: true,


maintainAspectRatio: false,



plugins: {



legend: {



position: 'bottom',



labels: {



padding: 20,



font: {


size: 14


}



}



},




tooltip: {



callbacks: {



label: function(context){



return context.label.split(':')[0] + ': ' + context.raw + ' usuarios';



}



}



}



}



}



});



</script>



</body>

</html>