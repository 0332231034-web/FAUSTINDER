<?php

require('fpdf/fpdf.php');

include("authsuper.php");
include("conexion.php");


$tipo = "TODOS";


if(isset($_GET["tipo"])){

    $tipo = $_GET["tipo"];

}



$condicion = " WHERE 1=1 ";



if($tipo != "TODOS"){

    $tipoSQL = mysqli_real_escape_string($cn,$tipo);

    $condicion .= " AND tipo LIKE '%$tipoSQL%' ";

}



$sql = "SELECT * FROM tbpersona
        $condicion
        ORDER BY nombre, apaterno";



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

        $this->Cell(0,8,'REPORTE POR TIPO',0,1,'C');


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


    // Calcula cuántas líneas ocupará un texto dentro de un ancho $w,
    // replicando el mismo wrap que usa MultiCell
    function NbLines($w, $txt){
        $cw = &$this->CurrentFont['cw'];
        if($w==0) $w = $this->w - $this->rMargin - $this->x;
        $wmax = ($w - 2*$this->cMargin) * 1000 / $this->FontSize;
        $s = str_replace("\r",'',$txt);
        $nb = strlen($s);
        if($nb>0 and $s[$nb-1]=="\n") $nb--;
        $sep=-1; $i=0; $j=0; $l=0; $nl=1;
        while($i<$nb){
            $c=$s[$i];
            if($c=="\n"){
                $i++; $sep=-1; $j=$i; $l=0; $nl++;
                continue;
            }
            if($c==' ') $sep=$i;
            $l+=$cw[$c] ?? 0;
            if($l>$wmax){
                if($sep==-1){
                    if($i==$j) $i++;
                }else{
                    $i=$sep+1;
                }
                $sep=-1; $j=$i; $l=0; $nl++;
            }else{
                $i++;
            }
        }
        return $nl;
    }


}





$pdf = new PDF('L','mm','A4');


$pdf->AddPage();





/******** RESUMEN ********/


if($tipo=="TODOS"){

    $tipoTexto="Todos los tipos";

}else{

    $tipoTexto=$tipo;

}



$pdf->SetFont('Arial','B',10);

$pdf->SetFillColor(240,244,250);



$pdf->Cell(60,8,"Fecha generacion:",1,0,'L',true);


$pdf->SetFont('Arial','',10);


$pdf->Cell(70,8,date("d/m/Y H:i"),1,1);



$pdf->SetFont('Arial','B',10);


$pdf->Cell(60,8,"Tipo seleccionado:",1,0,'L',true);



$pdf->SetFont('Arial','',10);


$pdf->Cell(70,8,utf8_decode($tipoTexto),1,1);




$pdf->Ln(8);





/******** TABLA ********/


$pdf->SetFillColor(25,60,120);

$pdf->SetTextColor(255);


$pdf->SetFont('Arial','B',9);



$pdf->Cell(35,9,"Nick",1,0,'C',true);


$pdf->Cell(60,9,"Nombre completo",1,0,'C',true);


$pdf->Cell(35,9,"Celular",1,0,'C',true);


$pdf->Cell(60,9,"Escuela",1,0,'C',true);


$pdf->Cell(45,9,"Tipo",1,0,'C',true);


$pdf->Cell(40,9,"Estado",1,1,'C',true);



$pdf->SetFont('Arial','',9);
$pdf->SetTextColor(0);

$total=0;

$anchoNick=35; $anchoNombre=60; $anchoCelular=35; $anchoEscuela=60; $anchoTipo=45; $anchoEstado=40;
$anchoTotal=$anchoNick+$anchoNombre+$anchoCelular+$anchoEscuela+$anchoTipo+$anchoEstado;

while($r=mysqli_fetch_assoc($f)){

    $tipoPersona=$r["tipo"];
    if($tipoPersona=="" || $tipoPersona==NULL) $tipoPersona="No registrado";

    if($r["estado"]=="I"){
        if($r["fechafin_inactivo"]!="" && $r["fechafin_inactivo"]!=NULL){
            $estado="Inactivo temporal";
        }else{
            $estado="Baja definitiva";
        }
    }else{
        $estado="Activo";
    }

    $nombre=$r["nombre"]." ".$r["apaterno"]." ".$r["amaterno"];

    $nickDec    = utf8_decode($r["nick"]);
    $nombreDec  = utf8_decode($nombre);
    $celularDec = $r["celular"];
    $escuelaDec = utf8_decode($r["escuela"]);
    $tipoDec    = utf8_decode($tipoPersona);
    $estadoDec  = utf8_decode($estado);

    // Líneas reales que necesita cada columna
    $lNick    = $pdf->NbLines($anchoNick, $nickDec);
    $lNombre  = $pdf->NbLines($anchoNombre, $nombreDec);
    $lCelular = $pdf->NbLines($anchoCelular, $celularDec);
    $lEscuela = $pdf->NbLines($anchoEscuela, $escuelaDec);
    $lTipo    = $pdf->NbLines($anchoTipo, $tipoDec);
    $lEstado  = $pdf->NbLines($anchoEstado, $estadoDec);

    $maxLineas = max($lNick,$lNombre,$lCelular,$lEscuela,$lTipo,$lEstado);
    $lineaAlto = 5;
    $alturaFila = $maxLineas * $lineaAlto;

    // Salto de página si la fila no cabe
    if($pdf->GetY() + $alturaFila > 190){
        $pdf->AddPage();
    }

    $x=$pdf->GetX();
    $y=$pdf->GetY();

    // 1) Marco único de toda la fila
    $pdf->Rect($x, $y, $anchoTotal, $alturaFila);

    // 2) Líneas verticales divisorias entre columnas
    $xLinea = $x;
    $anchos = [$anchoNick,$anchoNombre,$anchoCelular,$anchoEscuela,$anchoTipo,$anchoEstado];
    foreach($anchos as $idx=>$ancho){
        if($idx>0){
            $pdf->Line($xLinea, $y, $xLinea, $y+$alturaFila);
        }
        $xLinea += $ancho;
    }

    // 3) Texto de cada columna, sin borde propio, alineado arriba
    $pdf->SetXY($x,$y);
    $pdf->MultiCell($anchoNick,$lineaAlto,$nickDec,0,'L');

    $pdf->SetXY($x+$anchoNick,$y);
    $pdf->MultiCell($anchoNombre,$lineaAlto,$nombreDec,0,'L');

    $pdf->SetXY($x+$anchoNick+$anchoNombre,$y);
    $pdf->MultiCell($anchoCelular,$lineaAlto,$celularDec,0,'L');

    $pdf->SetXY($x+$anchoNick+$anchoNombre+$anchoCelular,$y);
    $pdf->MultiCell($anchoEscuela,$lineaAlto,$escuelaDec,0,'L');

    $pdf->SetXY($x+$anchoNick+$anchoNombre+$anchoCelular+$anchoEscuela,$y);
    $pdf->MultiCell($anchoTipo,$lineaAlto,$tipoDec,0,'L');

    $pdf->SetXY($x+$anchoNick+$anchoNombre+$anchoCelular+$anchoEscuela+$anchoTipo,$y);
    $pdf->MultiCell($anchoEstado,$lineaAlto,$estadoDec,0,'C');

    // 4) Avanzar el cursor a la siguiente fila
    $pdf->SetXY($x, $y+$alturaFila);

    $total++;

}





$pdf->Ln(8);



$pdf->SetFont('Arial','B',11);


$pdf->SetFillColor(230,235,245);



$pdf->Cell(275,10,

"Total de usuarios encontrados: ".$total,

1,

1,

'C',

true);





$pdf->Output();


?>