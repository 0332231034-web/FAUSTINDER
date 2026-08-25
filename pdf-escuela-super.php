<?php

require('fpdf/fpdf.php');

include("authsuper.php");
include("conexion.php");


$escuela="TODAS";


if(isset($_GET["escuela"])){

    $escuela=$_GET["escuela"];

}



$condicion=" WHERE 1=1 ";



if($escuela!="TODAS"){

    $escuelaSQL=mysqli_real_escape_string($cn,$escuela);

    $condicion.=" AND escuela='$escuelaSQL' ";

}



$sql="SELECT * FROM tbpersona
      $condicion
      ORDER BY nombre,apaterno";



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


$this->Line(10,$this->GetY(),287,$this->GetY());


$this->Ln(5);



$this->SetFont('Arial','B',14);

$this->SetTextColor(0);


$this->Cell(0,8,'REPORTE POR ESCUELA',0,1,'C');


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



$pdf->SetFont('Arial','B',10);


$pdf->SetFillColor(240,244,250);



$pdf->Cell(60,8,"Fecha generacion:",1,0,'L',true);

$pdf->SetFont('Arial','',10);

$pdf->Cell(70,8,date("d/m/Y H:i"),1,1);



$pdf->SetFont('Arial','B',10);

$pdf->Cell(60,8,"Escuela:",1,0,'L',true);


$pdf->SetFont('Arial','',10);


if($escuela=="TODAS"){

$escuelaTexto="Todas las escuelas";

}else{

$escuelaTexto=$escuela;

}


$pdf->Cell(70,8,utf8_decode($escuelaTexto),1,1);



$pdf->Ln(8);




$pdf->SetFillColor(25,60,120);

$pdf->SetTextColor(255);

$pdf->SetFont('Arial','B',9);



$pdf->Cell(35,9,"Nick",1,0,'C',true);

$pdf->Cell(60,9,"Nombre completo",1,0,'C',true);

$pdf->Cell(35,9,"Celular",1,0,'C',true);

$pdf->Cell(65,9,"Escuela",1,0,'C',true);

$pdf->Cell(30,9,"Sexo",1,0,'C',true);

$pdf->Cell(40,9,"Estado",1,1,'C',true);



$pdf->SetFont('Arial','',9);

$pdf->SetTextColor(0);



$total=0;



while($r=mysqli_fetch_assoc($f)){



if($r["sexo"]=="M"){

$sexo="Masculino";

}else{

$sexo="Femenino";

}



if($r["estado"]=="I"){


if($r["fechafin_inactivo"]!=""){

$estado="Inactivo temporal";

}else{

$estado="Baja definitiva";

}


}else{

$estado="Activo";

}




$nombre=$r["nombre"]." ".$r["apaterno"]." ".$r["amaterno"];



$pdf->Cell(35,8,utf8_decode($r["nick"]),1);

$pdf->Cell(60,8,utf8_decode($nombre),1);

$pdf->Cell(35,8,$r["celular"],1);

$pdf->Cell(65,8,utf8_decode($r["escuela"]),1);

$pdf->Cell(30,8,$sexo,1);

$pdf->Cell(40,8,utf8_decode($estado),1);



$pdf->Ln();



$total++;


}




$pdf->Ln(8);

$pdf->SetFont('Arial','B',11);

$pdf->Cell(265,10,"Total de usuarios encontrados: ".$total,1,1,'C');



$pdf->Output();


?>