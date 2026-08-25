<?php

require('fpdf/fpdf.php');

include("authsuper.php");
include("conexion.php");


$estadoFiltro="TODOS";


if(isset($_GET["estado"])){

    $estadoFiltro=$_GET["estado"];

}



$condicion=" WHERE 1=1 ";



if($estadoFiltro=="ACTIVO"){

    $condicion.=" AND (estado='A' OR estado IS NULL OR estado='') ";

}



if($estadoFiltro=="INACTIVO"){

    $condicion.=" AND estado='I' ";

}



$sql="SELECT * FROM tbpersona
      $condicion
      ORDER BY codigo DESC";


$f=mysqli_query($cn,$sql);





class PDF extends FPDF{


function Header(){


    $this->SetFont('Arial','B',18);

    $this->SetTextColor(20,50,90);


    $this->Cell(0,10,'FAUSTINder',0,1,'C');



    $this->SetFont('Arial','',10);

    $this->SetTextColor(100);


    $this->Cell(0,6,'Sistema de Gestion de Usuarios',0,1,'C');



    $this->Ln(3);



    $this->SetDrawColor(30,90,160);


    $this->Line(10,$this->GetY(),287,$this->GetY());



    $this->Ln(5);



    $this->SetFont('Arial','B',14);

    $this->SetTextColor(0);


    $this->Cell(0,8,'REPORTE POR ESTADO',0,1,'C');



    $this->Ln(3);


}




function Footer(){


    $this->SetY(-15);


    $this->SetFont('Arial','I',8);


    $this->SetTextColor(120);



    $this->Cell(90,10,'FAUSTINder © 2026',0,0);


    $this->Cell(90,10,'Reporte generado automaticamente',0,0,'C');


    $this->Cell(90,10,'Pagina '.$this->PageNo(),0,0,'R');

}


}




$pdf=new PDF('L','mm','A4');


$pdf->AddPage();





/******** RESUMEN ********/


if($estadoFiltro=="TODOS"){

    $estadoTexto="Todos los estados";

}

elseif($estadoFiltro=="ACTIVO"){

    $estadoTexto="Usuarios activos";

}

else{

    $estadoTexto="Usuarios inactivos";

}




$pdf->SetFont('Arial','B',10);

$pdf->SetFillColor(240,244,250);



$pdf->Cell(60,8,"Fecha generacion:",1,0,'L',true);



$pdf->SetFont('Arial','',10);


$pdf->Cell(70,8,date("d/m/Y H:i"),1,1);




$pdf->SetFont('Arial','B',10);


$pdf->Cell(60,8,"Estado seleccionado:",1,0,'L',true);



$pdf->SetFont('Arial','',10);


$pdf->Cell(70,8,utf8_decode($estadoTexto),1,1);





$pdf->Ln(8);






/******** TABLA ********/


$pdf->SetFillColor(25,60,120);

$pdf->SetTextColor(255);


$pdf->SetFont('Arial','B',9);



$pdf->Cell(33,9,"Nick",1,0,'C',true);


$pdf->Cell(58,9,"Nombre completo",1,0,'C',true);


$pdf->Cell(33,9,"Celular",1,0,'C',true);


$pdf->Cell(58,9,"Escuela",1,0,'C',true);


$pdf->Cell(38,9,"Estado",1,0,'C',true);


$pdf->Cell(57,9,"Detalle",1,1,'C',true);





$pdf->SetFont('Arial','',9);

$pdf->SetTextColor(0);



$total=0;



while($r=mysqli_fetch_assoc($f)){



    if($r["estado"]=="I"){


        if($r["fechafin_inactivo"]!="" && $r["fechafin_inactivo"]!=NULL){

            $estado="Inactivo temporal";

            $detalle="Hasta ".$r["fechafin_inactivo"];


        }else{

            $estado="Baja definitiva";

            $detalle="Cuenta desactivada";

        }



    }else{


        $estado="Activo";

        $detalle="Cuenta habilitada";


    }




    $nombre=$r["nombre"]." ".$r["apaterno"]." ".$r["amaterno"];




    $pdf->Cell(33,8,utf8_decode($r["nick"]),1);
    $pdf->Cell(58,8,utf8_decode($nombre),1);
    $pdf->Cell(33,8,$r["celular"],1);
    $pdf->Cell(58,8,utf8_decode($r["escuela"]),1);
    $pdf->Cell(38,8,utf8_decode($estado),1);
    $pdf->Cell(57,8,utf8_decode($detalle),1);



    $pdf->Ln();



    $total++;


}





$pdf->Ln(8);



$pdf->SetFont('Arial','B',11);


$pdf->SetFillColor(230,235,245);



$pdf->Cell(0,10,

"Total de usuarios encontrados: ".$total,

1,

1,

'C',

true);





$pdf->Output();



?>