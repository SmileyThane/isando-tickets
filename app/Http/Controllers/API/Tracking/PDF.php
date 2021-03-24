<?php

namespace App\Http\Controllers\API\Tracking;

use Codedge\Fpdf\Fpdf\Fpdf;

class PDF extends FPDF {

    protected $options = [
        'title'     => 'Title',
        'user'      => 'User',
        'period'    => 'Period'
    ];

    public function SetOptions($options) {
        $this->options = $options;
    }

    public function showHeader() {
        $this->InHeader = true;
    }

    public function hideHeader() {
        $this->InHeader = false;
    }

    public function showFooter() {
        $this->InFooter = true;
    }

    public function hideFooter() {
        $this->InFooter = false;
    }

    public function Header()
    {
        if ($this->PageNo() > 1) {
            // Select Arial bold 15
            $this->SetFont('Arial','',12);
            // Framed title
            $this->Cell(30,5,$this->options['title'],'L',1,'L');

            $this->SetFont('Arial','B',12);
            $this->Cell(30,5,$this->options['user'],'L',1,'L');

            $this->SetFont('Arial','',12);
            $this->Cell(30,5,$this->options['period'],'L',1,'L');
            // Line break
            $this->Ln(10);
        }
    }

    public function Footer()
    {
        if ($this->PageNo() > 1) {
            // Go to 1.5 cm from bottom
            $this->SetY(-20);
            // Select Arial italic 8
            $this->SetFont('Arial','',8);
            // Print right page number
            $this->SetX(-50);
            $this->Cell(0,10,'Page '.$this->PageNo() . ' of {nb}','L',0,'L');
        }
    }

    public function GetCenterX($text, $fullWidth = 210) {
        return Round($fullWidth / 2 - ($this->GetStringWidth($text) / 2));
    }

    public function GetCountLines($text, $fullWidth = 210) {
        return Ceil($this->GetStringWidth($text) / $fullWidth);
    }

    // Load data
    public function LoadData($file)
    {
        // Read file lines
        $lines = file($file);
        $data = array();
        foreach($lines as $line)
            $data[] = explode(';',trim($line));
        return $data;
    }

    // Simple table
    public function BasicTable($header, $data)
    {
        // Header
        foreach($header as $col)
            $this->Cell(40,7,$col,1);
        $this->Ln();
        // Data
        foreach($data as $row)
        {
            foreach($row as $col)
                $this->Cell(40,6,$col,1);
            $this->Ln();
        }
    }

    // Better table
    public function ImprovedTable($header, $data)
    {
        // Column widths
        $w = array(25, 15, 15, 25, 40, 40, 30, 40, 20, 20);
        // Header
        $this->SetFont('Arial', 'b', 10);
        $this->SetLineWidth(.2);
        for($i=0;$i<count($header);$i++)
            $this->Cell($w[$i],7,$header[$i],'TB',0,'C');
        $this->Ln();
        $this->SetFont('Arial', '', 10);
        // Data
        $this->SetLineWidth(.1);
        foreach($data as $keyRow => $row) {
            $i = 0;
            foreach ($row as $keyColumn => $column) {
                $this->Cell($w[$i],6, $column,'B', '');
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

}
