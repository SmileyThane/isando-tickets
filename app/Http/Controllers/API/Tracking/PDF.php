<?php

namespace App\Http\Controllers\API\Tracking;

use Codedge\Fpdf\Fpdf\Fpdf;
use easyTable;
use Exception;

class PDF extends FPDF
{

    protected $options = [
        'title' => 'Title',
        'user' => 'User',
        'period' => 'Period'
    ];

    private $firstPage = 1;

    public function setFirstPage(int $number)
    {
        $this->firstPage = $number;
    }

    public function SetOptions($options)
    {
        $this->options = $options;
    }

    public function showHeader()
    {
        $this->InHeader = true;
    }

    public function hideHeader()
    {
        $this->InHeader = false;
    }

    public function showFooter()
    {
        $this->InFooter = true;
    }

    public function hideFooter()
    {
        $this->InFooter = false;
    }

    public function Header()
    {
        if ($this->PageNo() > $this->firstPage) {
            // Select Arial bold 15
            $this->SetFont('Arial', '', 10);
            // Framed title
            foreach ($this->options as $key => $option) {
                $this->Cell(30, 5, $option, 'L', 1, 'L');
            }
            // Line break
            $this->Ln(10);
        }
    }

    public function Footer()
    {
        if ($this->PageNo() > $this->firstPage) {
            // Go to 1.5 cm from bottom
            $this->SetY(-20);
            // Select Arial italic 8
            $this->SetFont('Arial', '', 8);
            // Print right page number
            $this->SetX(-50);
            $this->Cell(0, 10, 'Page ' . $this->PageNo() . ' of {nb}', 'L', 0, 'L');
        }
    }

    public function GetCenterX($text, $fullWidth = 210)
    {
        return Round($fullWidth / 2 - ($this->GetStringWidth($text) / 2));
    }

    public function LoadData($file)
    {
        // Read file lines
        $lines = file($file);
        $data = array();
        foreach ($lines as $line)
            $data[] = explode(';', trim($line));
        return $data;
    }

    public function BasicTable($header, $data)
    {
        // Header
        foreach ($header as $col)
            $this->Cell(40, 7, $col, 1);
        $this->Ln();
        // Data
        foreach ($data as $row) {
            foreach ($row as $col)
                $this->Cell(40, 6, $col, 1);
            $this->Ln();
        }
    }

    // Better table
    public function ImprovedTable($header, $data)
    {
        // Column widths
        $w = array(20, 10, 10, 35, 70, 70, 30, 15, 15);
        // Header
        $this->SetFont('Arial', 'b', 8);
        $this->SetLineWidth(.2);
        for($i=0;$i<count($header);$i++)
            $this->Cell($w[$i],7,$header[$i],'TB',0,'C');
        $this->Ln();
        $this->SetFont('Arial', '', 8);
        // Data
        $this->SetLineWidth(.1);
        foreach($data as $keyRow => $row) {
            $i = 0;    $currentY = $this->GetY();
            foreach ($row as $keyColumn => $column) {
                $currentX = $this->GetX();
                $this->MultiCell($w[$i],6, $this->AutoLineBreak($column, $w[$i]));
                $this->SetXY($currentX + $w[$i], $currentY);
                $i++;
            }
            $this->Ln();
        }
        // Closing line
        $this->Cell(array_sum($w),0,'','T');
    }

    // Colored table
    public function FancyTable($header, $data)
    {
        // Colors, line width and bold font
        $this->SetFillColor(255,0,0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128,0,0);
        $this->SetLineWidth(.3);
        $this->SetFont('','B');
        // Header
        $w = array(40, 35, 40, 45);
        for($i=0;$i<count($header);$i++)
            $this->Cell($w[$i],7,$header[$i],1,0,'C',true);
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = false;
        foreach($data as $row)
        {
            $this->Cell($w[0],6,$row[0],'LR',0,'L',$fill);
            $this->Cell($w[1],6,$row[1],'LR',0,'L',$fill);
            $this->Cell($w[2],6,number_format($row[2]),'LR',0,'R',$fill);
            $this->Cell($w[3],6,number_format($row[3]),'LR',0,'R',$fill);
            $this->Ln();
            $fill = !$fill;
        }
        // Closing line
        $this->Cell(array_sum($w),0,'','T');
    }

    // Rubber table
    public function EasyTable(Array $headers, Array $data) {
        foreach ($headers as $key => $header) {
            $headers[$key]['text'] = $this->fixCharacters($header['text']);
        }
        $columnWidths = '%{' . implode(',', collect($headers)->map(function($item) {return $item['width'];})->toArray()) . '}';
        try {
            $table = new \easyTable($this, $columnWidths, 'width:100%;border:0;font-size:8');

//            for($i=0;$i<count($headers);$i++)
            foreach ($headers as $i => $header)
                $table->easyCell($headers[$i]['text'], $headers[$i]['style']);
            $table->printRow(true);

            foreach($data as $keyRow => $row) {
                foreach ($row as $keyColumn => $column) {
                    $style = 'border:B;border-width:0.1';
                    if (in_array($keyColumn, ['start', 'end', 'total'])) {
                        $style .= ';align:R';
                    }
                    $table->easyCell($this->fixCharacters($column), $style);
                }
                $table->printRow(false);
            }

            $table->endTable();
        } catch (\Exception $exception) {
            $this->Error($exception->getMessage());
        }
    }

    public function PageBreak(){
        return $this->PageBreakTrigger;
    }

    public function current_font($c){
        if($c=='family'){
            return $this->FontFamily;
        }
        elseif($c=='style'){
            return $this->FontStyle;
        }
        elseif($c=='size'){
            return $this->FontSizePt;
        }
    }

    public function get_color($c){
        if($c=='fill'){
            return $this->FillColor;
        }
        elseif($c=='text'){
            return $this->TextColor;
        }
    }

    public function get_page_width(){
        return $this->w;
    }

    public function get_margin($c){
        if($c=='l'){
            return $this->lMargin;
        }
        elseif($c=='r'){
            return $this->rMargin;
        }
        elseif($c=='t'){
            return $this->tMargin;
        }
    }

    public function get_linewidth(){
        return $this->LineWidth;
    }

    public function get_orientation(){
        return $this->CurOrientation;
    }
    /***********************************************************************
     *
     * Based on FPDF method SetFont
     *
     ************************************************************************/

    private function &FontData($family, $style, $size){
        if($family=='')
            $family = $this->FontFamily;
        else
            $family = strtolower($family);
        $style = strtoupper($style);
        if(strpos($style,'U')!==false){
            $style = str_replace('U','',$style);
        }
        if($style=='IB')
            $style = 'BI';
        $fontkey = $family.$style;
        if(!isset($this->fonts[$fontkey])){
            if($family=='arial')
                $family = 'helvetica';
            if(in_array($family,$this->CoreFonts)){
                if($family=='symbol' || $family=='zapfdingbats')
                    $style = '';
                $fontkey = $family.$style;
                if(!isset($this->fonts[$fontkey]))
                    $this->AddFont($family,$style);
            }
            else
                $this->Error('Undefined font: '.$family.' '.$style);
        }
        $result['FontSize'] = $size/$this->k;
        $result['CurrentFont']=&$this->fonts[$fontkey];
        return $result;
    }

    /***********************************************************************
     *
     * Based on FPDF method MultiCell
     *
     ************************************************************************/


    public function extMultiCell($font_family, $font_style, $font_size, $w, $txt){
        $result=array();
        $font=$this->FontData($font_family, $font_style, $font_size);
        $cw = $font['CurrentFont']['cw'];
        if($w==0){
            return $result;
        }
        $wmax = ($w-2*$this->cMargin)*1000/($font['FontSize']);

        $s = trim(str_replace("\r",'',$txt));
        $chs = strlen($s);
        $sep = -1;
        $i = 0;
        $j = 0;
        $l = 0;
        while($i<$chs){
            $c = $s[$i];
            if($c=="\n"){
                $result[]=substr($s,$j,$i-$j);
                $i++;
                $sep = -1;
                $j = $i;
                $l = 0;
                continue;
            }
            if($c==' '){
                if($l==0){
                    while($s[$i]==' '){
                        $i++;
                    }
                    $j=$i;
                }
                $sep = $i;
            }
            $l += $cw[$c];
            if($l>$wmax){
                if($sep==-1){
                    if($i==$j)
                        $i++;
                    $result[]=substr($s,$j,$i-$j);
                }
                else{
                    $result[]=substr($s,$j,$sep-$j);
                    $i = $sep+1;
                }
                $sep = -1;
                $j = $i;
                $l = 0;
            }
            else{
                $i++;
            }
        }
        $s=trim(substr($s,$j,$i-$j));
        if($s!=''){
            $result[]=$s;
        }
        return $result;
    }
    /***********************************************************************
     *
     * Based on FPDF method Cell
     *
     ************************************************************************/


    public function CellBlock($w, $h, &$array_txt, $align='J',$link=''){
        if(!isset($this->CurrentFont))
            $this->Error('No font has been set');
        foreach($array_txt as $ti=>$txt){
            $k = $this->k;
            if($this->y+$h>$this->PageBreakTrigger){
                break;
            }
            if($w==0){
                return;
            }
            $s = '';
            if($txt!==''){
                $stringWidth=$this->GetStringWidth($txt);
                if($align=='R'){
                    $dx = $w-$this->cMargin-$stringWidth;
                }
                elseif($align=='C'){
                    $dx = ($w-$stringWidth)/2;
                }
                else{
                    $dx = $this->cMargin;
                }
                if($align=='J'){
                    $ns=count(explode(' ', $txt));
                    $wx = $w-2*$this->cMargin;
                    $this->ws = ($ns>1) ? (($wx-$stringWidth)*(1/($ns-1))) : 0;
                    $this->_out(sprintf('%.3F Tw',$this->ws*$this->k));
                }
                if($this->ColorFlag)
                    $s .= 'q '.$this->TextColor.' ';
                $s .= sprintf('BT %.2F %.2F Td (%s) Tj ET',($this->x+$dx)*$k,($this->h-($this->y+.5*$h+.3*$this->FontSize))*$k,$this->_escape($txt));
                if($this->underline)
                    $s .= ' '.$this->_dounderline($this->x+$dx,$this->y+.5*$h+.3*$this->FontSize,$txt);
                if($this->ColorFlag)
                    $s .= ' Q';
                if($link)
                    $this->Link($this->x+$dx,$this->y+.5*$h-.5*$this->FontSize,$stringWidth,$this->FontSize,$link);
                unset($array_txt[$ti]);
            }
            if($s)
                $this->_out($s);
            $this->lasth = $h;
            $this->y += $h;
        }
        if($this->ws>0){
            $this->ws = 0;
            $this->_out('0 Tw');
        }
    }

    private function fixCharacters($text) {
        return iconv(mb_detect_encoding($text, 'auto'), 'CP1252//IGNORE', $text);
    }

    function Write($h, $txt, $link='') {
        $txt = $this->fixCharacters($txt);
        parent::Write($h, $txt, $link);
    }
}
