<?php

require('fpdf/fpdf.php');

include("authsuper.php");
include("conexion.php");



$sql = "SELECT * FROM tbreporte
        ORDER BY codigo DESC";


$f = mysqli_query($cn,$sql);



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


    $this->Cell(0,8,'REPORTE DE PERSONAS REPORTADAS',0,1,'C');


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




$pdf = new PDF('L','mm','A4');


$pdf->AddPage();





/******** RESUMEN ********/


$pdf->SetFont('Arial','B',10);

$pdf->SetFillColor(240,244,250);



$pdf->Cell(60,8,"Fecha generacion:",1,0,'L',true);


$pdf->SetFont('Arial','',10);


$pdf->Cell(70,8,date("d/m/Y H:i"),1,1);




$pdf->SetFont('Arial','B',10);


$pdf->Cell(60,8,"Tipo de reporte:",1,0,'L',true);


$pdf->SetFont('Arial','',10);


$pdf->Cell(70,8,"Personas reportadas",1,1);




$pdf->Ln(8);





/******** TABLA ********/


$pdf->SetFillColor(25,60,120);

$pdf->SetTextColor(255);


$pdf->SetFont('Arial','B',9);



$pdf->Cell(35,9,"Reportado",1,0,'C',true);

$pdf->Cell(35,9,"Reportante",1,0,'C',true);

$pdf->Cell(35,9,"Tipo",1,0,'C',true);

$pdf->Cell(80,9,"Motivo",1,0,'C',true);

$pdf->Cell(35,9,"Fecha",1,0,'C',true);

$pdf->Cell(35,9,"Estado",1,1,'C',true);





$pdf->SetFont('Arial','',9);

$pdf->SetTextColor(0);



$total=0;



while($r=mysqli_fetch_assoc($f)){



    $codigoReportado=$r["reportado"];

    $codigoReportante=$r["reportante"];



    $sql1="select nick from tbpersona where codigo='$codigoReportado'";

    $fr=mysqli_query($cn,$sql1);



    if(mysqli_num_rows($fr)>0){

        $persona=mysqli_fetch_assoc($fr);

        $nombreReportado=$persona["nick"];

    }else{

        $nombreReportado="Usuario eliminado";

    }





    $sql2="select nick from tbpersona where codigo='$codigoReportante'";

    $fr2=mysqli_query($cn,$sql2);



    if(mysqli_num_rows($fr2)>0){

        $persona2=mysqli_fetch_assoc($fr2);

        $nombreReportante=$persona2["nick"];

    }else{

        $nombreReportante="Usuario eliminado";

    }





    if($r["estado"]=="PENDIENTE"){

        $estado="Pendiente";

    }else{

        $estado="Atendido";

    }





    $pdf->Cell(35,8,utf8_decode($nombreReportado),1);


    $pdf->Cell(35,8,utf8_decode($nombreReportante),1);


    $pdf->Cell(35,8,utf8_decode($r["tipo_reporte"]),1);


    $pdf->Cell(80,8,utf8_decode($r["motivo"]),1);


    $pdf->Cell(35,8,$r["fecha_reporte"],1);


    $pdf->Cell(35,8,$estado,1);



    $pdf->Ln();


    $total++;


}





$pdf->Ln(8);



$pdf->SetFont('Arial','B',11);


$pdf->SetFillColor(230,235,245);



$pdf->Cell(255,10,

"Total de reportes encontrados: ".$total,

1,

1,

'C',

true);





$pdf->Output();


?>