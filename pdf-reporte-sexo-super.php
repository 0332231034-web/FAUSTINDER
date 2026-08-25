<?php
require('fpdf/fpdf.php');

include("authsuper.php");
include("conexion.php");


class PDF extends FPDF{


function Header(){

    // Logo si tienes
    // $this->Image('img/logo.png',10,8,25);


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

    $this->Cell(0,8,'REPORTE POR SEXO',0,1,'C');


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



$sexo="TODOS";


if(isset($_GET["sexo"])){
    $sexo=$_GET["sexo"];
}


$condicion=" WHERE 1=1 ";


if($sexo=="M"){
    $condicion.=" AND sexo='M'";
}


if($sexo=="F"){
    $condicion.=" AND sexo='F'";
}



$sql="SELECT * FROM tbpersona
      $condicion
      ORDER BY nombre,apaterno";


$f=mysqli_query($cn,$sql);



$pdf=new PDF('L','mm','A4');

$pdf->AddPage();



/******** RESUMEN ********/


if($sexo=="TODOS"){

    $texto="Todos";

}elseif($sexo=="M"){

    $texto="Masculino";

}else{

    $texto="Femenino";

}



$pdf->SetFillColor(240,244,250);


$pdf->SetFont('Arial','B',10);


$pdf->Cell(60,8,"Fecha de generacion:",1,0,'L',true);

$pdf->SetFont('Arial','',10);

$pdf->Cell(70,8,date("d/m/Y H:i"),1,1);


$pdf->SetFont('Arial','B',10);

$pdf->Cell(60,8,"Filtro aplicado:",1,0,'L',true);

$pdf->SetFont('Arial','',10);

$pdf->Cell(70,8,$texto,1,1);



/******** TABLA ********/


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

$color=false;



while($r=mysqli_fetch_assoc($f)){


    if($color){

        $pdf->SetFillColor(245,245,245);

    }else{

        $pdf->SetFillColor(255,255,255);

    }



    $color=!$color;



    if($r["sexo"]=="M"){

        $sexoTexto="Masculino";

    }elseif($r["sexo"]=="F"){

        $sexoTexto="Femenino";

    }else{

        $sexoTexto="-";

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



    $pdf->Cell(35,8,utf8_decode($r["nick"]),1,0,'L',true);

    $pdf->Cell(60,8,utf8_decode($nombre),1,0,'L',true);

    $pdf->Cell(35,8,$r["celular"],1,0,'C',true);

    $pdf->Cell(65,8,utf8_decode($r["escuela"]),1,0,'L',true);

    $pdf->Cell(30,8,$sexoTexto,1,0,'C',true);


    // color estado

    if($estado=="Activo"){

        $pdf->SetTextColor(0,120,0);

    }

    elseif($estado=="Inactivo temporal"){

        $pdf->SetTextColor(180,130,0);

    }

    else{

        $pdf->SetTextColor(200,0,0);

    }


    $pdf->Cell(40,8,utf8_decode($estado),1,1,'C',true);


    $pdf->SetTextColor(0);



    $total++;

}




$pdf->Ln(8);



$pdf->SetFillColor(230,235,245);

$pdf->SetFont('Arial','B',11);


$pdf->Cell(265,10,

"Total de usuarios encontrados: ".$total,

1,

1,

'C',

true);



$pdf->Output();

?>