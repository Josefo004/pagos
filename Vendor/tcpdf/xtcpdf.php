<?php 
App::import('Vendor', 'tcpdf/tcpdf');
App::import('Vendor', 'tcpdf/tcpdf/config/lang/spa');

class XTCPDF extends TCPDF 
{ 
    private $autor  = 'Gobierno Autónomo Departamental de Chuquisaca'; 
    private $copyright  = '© 2022 Gobierno Autónomo de Chuquisaca. Todos los derechos reservados';
    private $titulo = 'Gobierno Autónomo Departamental de Chuquisaca';
    private $subtitulo = '';
    private $palabras_claves = 'Sucre, GACH, Procesos de pagos';
    private $orientacion = 'P';

    public function __construct($orientation = 'P', $unit = 'mm', $format = array(215, 279), $unicode = true, $encoding = 'UTF-8', $diskcache = false) {
        parent::__construct($orientation, $unit, $format, $unicode, $encoding, $diskcache);
        
        $this->orientacion = $orientation;
        $this->subtitulo = Configure::read('App.name');
        
        $this->SetCreator(PDF_CREATOR);
        $this->SetAuthor($this->autor);
        $this->SetTitle($this->titulo);
        $this->SetSubject($this->autor);
        $this->SetKeywords($this->palabras_claves);
        
        //cambiar margenes
        if ($orientation == 'P') {
            $this->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT, 10);
        } else {
            $this->SetMargins(10, 10, 10, 10);
        }
        $this->SetHeaderMargin(PDF_MARGIN_HEADER);
        $this->SetFooterMargin(PDF_MARGIN_FOOTER);

        //set auto page breaks
        $this->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
        
        //set image scale factor
        $this->setImageScale(PDF_IMAGE_SCALE_RATIO);
        
        $this->setJPEGQuality(100);
    }

    public function Header() {
        $this->SetFont('freesans', '', 9);
        $this->Cell(93, 10, $this->titulo, 0, 0, 'L');
        $this->SetFont('freesans', '', 8);
        if ($this->orientacion == 'P') {
            $this->Cell(92, 10, $this->subtitulo, 0, 0, 'R');
        } else {
            $this->Cell(217, 10, $this->subtitulo, 0, 0, 'R');
        }
        $this->Ln();
        $this->SetY(12);
        $this->Cell(0, 0, null, 'T', 0);
    }

    public function Footer() {
        $this->SetY(-14);
        $this->SetFont('freesans', '', 8);
        $this->Cell(100, 0, $this->copyright, 0, 0, 'L');
        if ($this->orientacion == 'P') {
            $this->Cell(100, 0, 'Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, 0, 'R');
        } else {
            $this->Cell(225, 0, 'Página ' . $this->getAliasNumPage() . ' de ' . $this->getAliasNbPages(), 0, 0, 'R');
        }
        
        $this->Ln();

        $this->SetY(-14);
        $this->Cell(0, 5, '', 'T', 0);
    }

    // Colored table
    public function ColoredTable($header,$data) {
        // Colors, line width and bold font
        $this->SetFillColor(255, 0, 0);
        $this->SetTextColor(255);
        $this->SetDrawColor(128, 0, 0);
        $this->SetLineWidth(0.3);
        $this->SetFont('', 'B');
        // Header
        $w = array(50, 80, 50);
        $num_headers = count($header);
        for($i = 0; $i < $num_headers; ++$i) {             $this->Cell($w[$i], 7, $header[$i], 1, 0, 'C', 1);
        }
        $this->Ln();
        // Color and font restoration
        $this->SetFillColor(224, 235, 255);
        $this->SetTextColor(0);
        $this->SetFont('');
        // Data
        $fill = 0;
        foreach($data as $row) {
            $this->Cell($w[0], 6, $row[0], 'LR', 0, 'L', $fill);
            $this->Cell($w[1], 6, $row[1], 'LR', 0, 'L', $fill);
            $this->Cell($w[2], 6, $row[2], 'LR', 0, 'L', $fill);
            $this->Ln();
            $fill=!$fill;
        }
        $this->Cell(array_sum($w), 0, '', 'T');
    }
    
    public function setTitulo($titulo) {
        $this->titulo = $titulo;
    }
    
    public function setSubTitulo($subtitulo) {
        $this->subtitulo = $subtitulo;
    }
    
    public function setCopyright($copyright) {
        $this->copyright = $copyright;
    }
    
    public function setSistema($sistema) {
        $this->sistema = $sistema;
    }
    
    public function setTituloPDF($titulo) {
        $this->SetTitle($titulo);
    }
} 
