<?php
include("authsuper.php");
include("conexion.php");

$sqlActivos = "select count(*) as total 
               from tbpersona 
               where estado='A' or estado is null or estado=''";

$fActivos = mysqli_query($cn, $sqlActivos);
$rActivos = mysqli_fetch_assoc($fActivos);
$totalActivos = $rActivos["total"];


$sqlTemporal = "select count(*) as total 
                from tbpersona 
                where estado='I'
                and fechafin_inactivo is not null
                and fechafin_inactivo <> ''";

$fTemporal = mysqli_query($cn, $sqlTemporal);
$rTemporal = mysqli_fetch_assoc($fTemporal);
$totalTemporal = $rTemporal["total"];


$sqlBaja = "select count(*) as total 
            from tbpersona 
            where estado='I'
            and (fechafin_inactivo is null or fechafin_inactivo='')";

$fBaja = mysqli_query($cn, $sqlBaja);
$rBaja = mysqli_fetch_assoc($fBaja);
$totalBaja = $rBaja["total"];
?>

<!DOCTYPE html>
<html lang="es">

<head>

<meta charset="UTF-8">

<title>Estadística por estado - Superusuario</title>

<link rel="stylesheet" href="css/estilo.css">

</head>


<body>


<div class="panel-superusuario">


<div class="contenedor-superusuario">


<div class="encabezado-superusuario">


<div>

<h1>FAUSTINder</h1>

<p>Estadística de usuarios activos e inactivos</p>

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
Usuarios activos e inactivos
</h2>



<div class="contenedor-grafico-super">


<canvas id="graficoEstado"></canvas>


</div>



</div>



</div>



</div>


</div>
<script src="chartjs/chart.umd.min.js"></script>

<script>

const ctxEstado = document.getElementById('graficoEstado');

new Chart(ctxEstado, {

    type: 'pie',

    data: {

        labels: [
            'Activos <?php echo $totalActivos; ?>',
            'Inactivos temporales <?php echo $totalTemporal; ?>',
            'Baja definitiva <?php echo $totalBaja; ?>'
        ],

        datasets: [{

            label: 'Usuarios',

            data: [

                <?php echo $totalActivos; ?>,
                <?php echo $totalTemporal; ?>,
                <?php echo $totalBaja; ?>

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

                    label: function(context) {

                        return context.label;

                    }

                }

            }

        }

    }

});

</script>



</body>

</html>