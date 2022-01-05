<?php
require('fpdf.php');

class PDF extends FPDF { var $widths; var $aligns; function SetWidths($w) {    $this->widths=$w;
}
 
function SetAligns($a)
{
    $this->aligns=$a;
}
 
function Row($data)
{
    $nb=0;
    for($i=0;$i<count($data);$i++)
        $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
    $h=5*$nb;
    //Issue a page break first if needed
    $this->CheckPageBreak($h);
    //Draw the cells of the row
    for($i=0;$i<count($data);$i++)
    {
        $w=$this->widths[$i];
        $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
        //Save the current position
        $x=$this->GetX();
        $y=$this->GetY();
        //Draw the border
        $this->Rect($x,$y,$w,$h);
        //Print the text
        $this->MultiCell($w,5,$data[$i],0,$a);
        //Put the position to the right of the cell
        $this->SetXY($x+$w,$y);
    
    }
    $this->Ln($h);
}
 
function CheckPageBreak($h)
{
    if($this->GetY()+$h>$this->PageBreakTrigger)
        $this->AddPage($this->CurOrientation);
}
 
function NbLines($w,$txt)
{
   $cw=&$this->CurrentFont['cw'];
    if($w==0)
        $w=$this->w-$this->rMargin-$this->x;
    $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
    $s=str_replace("\r",'',$txt);
    $nb=strlen($s);
    if($nb>0 and $s[$nb-1]=="\n")
        $nb--;
    $sep=-1;
    $i=0;
    $j=0;
    $l=0;
    $nl=1;
    while($i<$nb)
    {
        $c=$s[$i];
        if($c=="\n")
        {
            $i++;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
            continue;
        }
        if($c==' ')
            $sep=$i;
        $l+=$cw[$c];
        if($l>$wmax)
        {
            if($sep==-1)
            {
                if($i==$j)
                    $i++;
            }
            else
                $i=$sep+1;
            $sep=-1;
            $j=$i;
            $l=0;
            $nl++;
        }
        else
            $i++;
    }
    return $nl;
}
 
function Header()
{
    if ($this->header == 0)
        {
           
    
    $this->SetFont('Arial','',13);
    $this->Cell(160,16,utf8_decode('Planificacion Anual Educación Básica'),0,'C', 0);
    $this->Ln(15);
        }
        
         if ($this->header == 1)
        {
           
    
    $this->SetFont('Arial','',10);
    $this->Cell(100,14,utf8_decode('Planificacion Anual Educación Media',0,'C', 0));
    $this->Ln(15);
        }
}
 
function Footer()
{
    $this->SetY(-10);
    $this->SetFont('Arial','B',8);
    $this->Cell(80,5,'Firma Profesor',0,0,'L');
}
}
?>