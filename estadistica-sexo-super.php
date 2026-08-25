<?php
include("authsuper.php");
include("conexion.php");


$sqlM = "select count(*) as total from tbpersona where sexo='M'";
$fM = mysqli_query($cn, $sqlM);
$rM = mysqli_fetch_assoc($fM);
$totalM = $rM["total"];


$sqlF = "select count(*) as total from tbpersona where sexo='F'";
$fF = mysqli_query($cn, $sqlF);
$rF = mysqli_fetch_assoc($fF);
$totalF = $rF["total"];

?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Estadística por sexo - Superusuario</title>

<link rel="stylesheet" href="css/estilo.css">

</head>


<body>


<div class="panel-superusuario">


<div class="contenedor-superusuario">



<div class="encabezado-superusuario">


<div>

<h1>FAUSTINder</h1>

<p>Estadística de usuarios por sexo</p>

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
Usuarios
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
Usuarios por sexo
</h2>



<div class="contenedor-grafico-super">

<canvas id="graficoSexo"></canvas>

</div>



</div>



</div>




</div>


</div>






<script src="chartjs/chart.umd.min.js"></script>



<script>


const ctxSexo = document.getElementById('graficoSexo');



new Chart(ctxSexo, {



type:'bar',



data:{


labels:[

'Masculino',

'Femenino'

],



datasets:[{

label:'Cantidad de usuarios',



data:[

<?php echo $totalM; ?>,

<?php echo $totalF; ?>

],



backgroundColor:[

'#1e5aa0',

'#e83e8c'

],



borderWidth:1



}]



},





options:{



responsive:true,


maintainAspectRatio:false,



plugins:{



legend:{


position:'bottom',



labels:{


padding:20,



font:{


size:14


},




generateLabels:function(chart){



let datos = chart.data.datasets[0].data;



return [


{


text:'Masculino: '+datos[0],


fillStyle:'#1e5aa0',


strokeStyle:'#1e5aa0',


lineWidth:1


},



{


text:'Femenino: '+datos[1],


fillStyle:'#e83e8c',


strokeStyle:'#e83e8c',


lineWidth:1


}



];



}



}



},





tooltip:{



callbacks:{



label:function(context){



return context.raw;


}



}



}



},





scales:{


y:{


beginAtZero:true,


ticks:{


precision:0


}



}



}





}



});



</script>



</body>

</html>