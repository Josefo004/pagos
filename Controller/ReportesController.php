<?php

App::uses('AppController', 'Controller');

/**
 * Reportes Controller
 *
 * @property Reporte $Reporte
 */
class ReportesController extends AppController {

    public $uses = array('Proceso', 'Motivo', 'ServidoresPublico', 'Dependencia');
    
    /**
     * por_secretarias method
     * Método para por_secretarias reportes
     * 
     * @return void
     */
    public function por_secretarias() {
        $params = $this->params['named'];
        $params['ciclo'] = empty($params['ciclo']) ? 'mensual' : $params['ciclo'];
        $params['anio'] = empty($params['anio']) ? date('Y') : $params['anio'];
        $params['mes'] = empty($params['mes']) ? date('m') : $params['mes'];
        $params['dia'] = empty($params['dia']) ? date('Y-m-d') : $params['dia'];
        $params['monto'] = empty($params['monto']) ? '' : $params['monto'];
        $params['detalle'] = empty($params['detalle']) ? '' : $params['detalle'];
        $params['pdf'] = empty($params['pdf']) ? '' : $params['pdf'];
        $params['controller'] = $this->params['controller'];
        $this->set('params', $params);
        
        switch ($params['monto']) {
            case 'menores':
                $monto = 'AND "Proceso"."monto" <= 10000';
                $txt_monto = ' - Monto: Menor o igual a 10000';
                break;
            case 'mayores':
                $monto = 'AND "Proceso"."monto" > 10000';
                $txt_monto = ' - Monto: Mayor a 10000';
                break;
            default:
                $monto = '';
                $txt_monto = ' - Monto: Todos';
        }
        
        $motivos = $this->Motivo->find('all', array(
            'fields' => array('Motivo.nombre'),
            'order' => array('Motivo.id' => 'ASC'),
            'recursive' => 0
        ));
        
        $analista = '';
        if ($this->Auth->user('rol') == 'analistas') {
            $analista = ' AND "Proceso"."usuario_analista_id" = ' . $this->Auth->user('id');
        } 
        
        switch ($params['ciclo']) {
            case 'anual':
                $fechas = 'AND "Proceso"."created" >= \'' . $params['anio'] . '-01-01 00:00:01\' AND "Proceso"."created" <= \'' . $params['anio'] . '-12-31 23:59:59\'';
                break;
            case 'mensual':
                if ($params['mes'] == '12') {
                    $fecha = (date('Y') + 1) . '-01';
                } else {
                    $fecha = date('Y') . '-' . ($params['mes'] + 1);
                }
                $fechas = 'AND "Proceso"."created" >= \'' . date('Y') . '-' . $params['mes'] . '-01 00:00:01\' AND "Proceso"."created" < \'' . $fecha . '-01 00:00:00\'';
                break;
            case 'diario':
                $fechas = 'AND "Proceso"."created" >= \'' . $params['dia'] . ' 00:00:01\' AND "Proceso"."created" <= \'' . $params['dia'] . ' 23:59:59\'';
                break;
        }
        
        $reporte_total = $this->Proceso->query(
                'SELECT "Dependencia"."nombre" AS "Dependencia__nombre", "Dependencia"."sigla" AS "Dependencia__sigla", 
                    COUNT("Proceso".*) AS "Proceso__total"
                FROM "per_dependencias" AS "Dependencia"
                    LEFT JOIN "pag_procesos" AS "Proceso" ON ("Proceso"."dependencia_id" = "Dependencia"."id"
                        ' . $fechas . '
                        ' . $monto . '
                        ' . $analista . '    
                        )
                WHERE ("Dependencia"."tipo_dependencia_id" = 2 OR "Dependencia"."tipo_dependencia_id" = 4) AND "Dependencia"."sigla" IS NOT NULL 
                GROUP BY "Dependencia"."id", "Dependencia"."nombre", "Dependencia"."sigla" 
                ORDER BY "Dependencia"."nombre"'
        );

        if ($params['detalle'] == 'por_estados') {
            $reporte_estado[1] = $this->Proceso->query(
                    'SELECT "Dependencia"."nombre" AS "Dependencia__nombre", "Dependencia"."sigla" AS "Dependencia__sigla", 
                        COUNT("Proceso".*) AS "Proceso__total"
                    FROM "per_dependencias" AS "Dependencia"
                        LEFT JOIN "pag_procesos" AS "Proceso" ON ("Proceso"."dependencia_id" = "Dependencia"."id"
                            ' . $fechas . '
                            ' . $monto . ' AND "Proceso"."estado_id" = 1 
                            ' . $analista . '
                            )
                    WHERE ("Dependencia"."tipo_dependencia_id" = 2 OR "Dependencia"."tipo_dependencia_id" = 4) AND "Dependencia"."sigla" IS NOT NULL 
                    GROUP BY "Dependencia"."id", "Dependencia"."nombre", "Dependencia"."sigla" 
                    ORDER BY "Dependencia"."nombre"'
            );
            $reporte_estado[2] = $this->Proceso->query(
                    'SELECT "Dependencia"."nombre" AS "Dependencia__nombre", "Dependencia"."sigla" AS "Dependencia__sigla", 
                        COUNT("Proceso".*) AS "Proceso__total"
                    FROM "per_dependencias" AS "Dependencia"
                        LEFT JOIN "pag_procesos" AS "Proceso" ON ("Proceso"."dependencia_id" = "Dependencia"."id"
                            ' . $fechas . '
                            ' . $monto . ' AND "Proceso"."estado_id" = 2
                            ' . $analista . '
                            )
                    WHERE ("Dependencia"."tipo_dependencia_id" = 2 OR "Dependencia"."tipo_dependencia_id" = 4) AND "Dependencia"."sigla" IS NOT NULL 
                    GROUP BY "Dependencia"."id", "Dependencia"."nombre", "Dependencia"."sigla" 
                    ORDER BY "Dependencia"."nombre"'
            );
            $reporte_estado[3] = $this->Proceso->query(
                    'SELECT "Dependencia"."nombre" AS "Dependencia__nombre", "Dependencia"."sigla" AS "Dependencia__sigla", 
                        COUNT("Proceso".*) AS "Proceso__total"
                    FROM "per_dependencias" AS "Dependencia"
                        LEFT JOIN "pag_procesos" AS "Proceso" ON ("Proceso"."dependencia_id" = "Dependencia"."id"
                            ' . $fechas . '
                            ' . $monto . ' AND "Proceso"."estado_id" = 3
                            ' . $analista . '
                            )
                    WHERE ("Dependencia"."tipo_dependencia_id" = 2 OR "Dependencia"."tipo_dependencia_id" = 4) AND "Dependencia"."sigla" IS NOT NULL 
                    GROUP BY "Dependencia"."id", "Dependencia"."nombre", "Dependencia"."sigla" 
                    ORDER BY "Dependencia"."nombre"'
            );
            $reporte_estado[4] = $this->Proceso->query(
                    'SELECT "Dependencia"."nombre" AS "Dependencia__nombre", "Dependencia"."sigla" AS "Dependencia__sigla", 
                        COUNT("Proceso".*) AS "Proceso__total"
                    FROM "per_dependencias" AS "Dependencia"
                        LEFT JOIN "pag_procesos" AS "Proceso" ON ("Proceso"."dependencia_id" = "Dependencia"."id"
                            ' . $fechas . '
                            ' . $monto . ' AND "Proceso"."estado_id" = 4
                            ' . $analista . '
                            )
                    WHERE ("Dependencia"."tipo_dependencia_id" = 2 OR "Dependencia"."tipo_dependencia_id" = 4) AND "Dependencia"."sigla" IS NOT NULL 
                    GROUP BY "Dependencia"."id", "Dependencia"."nombre", "Dependencia"."sigla" 
                    ORDER BY "Dependencia"."nombre"'
            );
            $reporte_estado[5] = $this->Proceso->query(
                    'SELECT "Dependencia"."nombre" AS "Dependencia__nombre", "Dependencia"."sigla" AS "Dependencia__sigla", 
                        COUNT("Proceso".*) AS "Proceso__total"
                    FROM "per_dependencias" AS "Dependencia"
                        LEFT JOIN "pag_procesos" AS "Proceso" ON ("Proceso"."dependencia_id" = "Dependencia"."id"
                            ' . $fechas . '
                            ' . $monto . ' AND "Proceso"."estado_id" = 5
                            ' . $analista . '
                            )
                    WHERE ("Dependencia"."tipo_dependencia_id" = 2 OR "Dependencia"."tipo_dependencia_id" = 4) AND "Dependencia"."sigla" IS NOT NULL 
                    GROUP BY "Dependencia"."id", "Dependencia"."nombre", "Dependencia"."sigla" 
                    ORDER BY "Dependencia"."nombre"'
            );

            $reporte_estado[6] = $this->Proceso->query(
                    'SELECT "Dependencia"."nombre" AS "Dependencia__nombre", "Dependencia"."sigla" AS "Dependencia__sigla", 
                        COUNT("Proceso".*) AS "Proceso__total"
                    FROM "per_dependencias" AS "Dependencia"
                        LEFT JOIN "pag_procesos" AS "Proceso" ON ("Proceso"."dependencia_id" = "Dependencia"."id"
                            ' . $fechas . '
                            ' . $monto . ' AND "Proceso"."estado_id" = 6
                            ' . $analista . '
                            )
                    WHERE ("Dependencia"."tipo_dependencia_id" = 2 OR "Dependencia"."tipo_dependencia_id" = 4) AND "Dependencia"."sigla" IS NOT NULL 
                    GROUP BY "Dependencia"."id", "Dependencia"."nombre", "Dependencia"."sigla" 
                    ORDER BY "Dependencia"."nombre"'
            );
            $reporte_estado[7] = $this->Proceso->query(
                    'SELECT "Dependencia"."nombre" AS "Dependencia__nombre", "Dependencia"."sigla" AS "Dependencia__sigla", 
                        COUNT("Proceso".*) AS "Proceso__total"
                    FROM "per_dependencias" AS "Dependencia"
                        LEFT JOIN "pag_procesos" AS "Proceso" ON ("Proceso"."dependencia_id" = "Dependencia"."id"
                            ' . $fechas . '
                            ' . $monto . ' AND "Proceso"."estado_id" = 7
                            ' . $analista . '
                            )
                    WHERE ("Dependencia"."tipo_dependencia_id" = 2 OR "Dependencia"."tipo_dependencia_id" = 4) AND "Dependencia"."sigla" IS NOT NULL 
                    GROUP BY "Dependencia"."id", "Dependencia"."nombre", "Dependencia"."sigla" 
                    ORDER BY "Dependencia"."nombre"'
            );

            if ($params['monto'] == 'menores') {
                $reporte_estado[8] = $this->Proceso->query(
                        'SELECT "Dependencia"."nombre" AS "Dependencia__nombre", "Dependencia"."sigla" AS "Dependencia__sigla", 
                            COUNT("Proceso".*) AS "Proceso__total"
                        FROM "per_dependencias" AS "Dependencia"
                            LEFT JOIN "pag_procesos" AS "Proceso" ON ("Proceso"."dependencia_id" = "Dependencia"."id"
                                ' . $fechas . '
                                ' . $monto . ' AND "Proceso"."estado_id" = 8
                                ' . $analista . '
                                )
                        WHERE ("Dependencia"."tipo_dependencia_id" = 2 OR "Dependencia"."tipo_dependencia_id" = 4) AND "Dependencia"."sigla" IS NOT NULL 
                        GROUP BY "Dependencia"."id", "Dependencia"."nombre", "Dependencia"."sigla" 
                        ORDER BY "Dependencia"."nombre"'
                );
            } elseif ($params['monto'] == 'mayores') {
                $reporte_estado[9] = $this->Proceso->query(
                        'SELECT "Dependencia"."nombre" AS "Dependencia__nombre", "Dependencia"."sigla" AS "Dependencia__sigla", 
                            COUNT("Proceso".*) AS "Proceso__total"
                        FROM "per_dependencias" AS "Dependencia"
                            LEFT JOIN "pag_procesos" AS "Proceso" ON ("Proceso"."dependencia_id" = "Dependencia"."id"
                                ' . $fechas . '
                                ' . $monto . ' AND "Proceso"."estado_id" = 9
                                ' . $analista . '
                                )
                        WHERE ("Dependencia"."tipo_dependencia_id" = 2 OR "Dependencia"."tipo_dependencia_id" = 4) AND "Dependencia"."sigla" IS NOT NULL 
                        GROUP BY "Dependencia"."id", "Dependencia"."nombre", "Dependencia"."sigla" 
                        ORDER BY "Dependencia"."nombre"'
                );
                $reporte_estado[10] = $this->Proceso->query(
                        'SELECT "Dependencia"."nombre" AS "Dependencia__nombre", "Dependencia"."sigla" AS "Dependencia__sigla", 
                            COUNT("Proceso".*) AS "Proceso__total"
                        FROM "per_dependencias" AS "Dependencia"
                            LEFT JOIN "pag_procesos" AS "Proceso" ON ("Proceso"."dependencia_id" = "Dependencia"."id"
                                ' . $fechas . '
                                ' . $monto . ' AND "Proceso"."estado_id" = 10
                                ' . $analista . '
                                )
                        WHERE ("Dependencia"."tipo_dependencia_id" = 2 OR "Dependencia"."tipo_dependencia_id" = 4) AND "Dependencia"."sigla" IS NOT NULL 
                        GROUP BY "Dependencia"."id", "Dependencia"."nombre", "Dependencia"."sigla" 
                        ORDER BY "Dependencia"."nombre"'
                );
                $reporte_estado[11] = $this->Proceso->query(
                        'SELECT "Dependencia"."nombre" AS "Dependencia__nombre", "Dependencia"."sigla" AS "Dependencia__sigla", 
                            COUNT("Proceso".*) AS "Proceso__total"
                        FROM "per_dependencias" AS "Dependencia"
                            LEFT JOIN "pag_procesos" AS "Proceso" ON ("Proceso"."dependencia_id" = "Dependencia"."id"
                                ' . $fechas . '
                                ' . $monto . ' AND "Proceso"."estado_id" = 11
                                ' . $analista . '
                                )
                        WHERE ("Dependencia"."tipo_dependencia_id" = 2 OR "Dependencia"."tipo_dependencia_id" = 4) AND "Dependencia"."sigla" IS NOT NULL 
                        GROUP BY "Dependencia"."id", "Dependencia"."nombre", "Dependencia"."sigla" 
                        ORDER BY "Dependencia"."nombre"'
                );
            }

            $reporte_estado[12] = $this->Proceso->query(
                    'SELECT "Dependencia"."nombre" AS "Dependencia__nombre", "Dependencia"."sigla" AS "Dependencia__sigla", 
                        COUNT("Proceso".*) AS "Proceso__total"
                    FROM "per_dependencias" AS "Dependencia"
                        LEFT JOIN "pag_procesos" AS "Proceso" ON ("Proceso"."dependencia_id" = "Dependencia"."id"
                            ' . $fechas . '
                            ' . $monto . ' AND "Proceso"."estado_id" = 12
                            ' . $analista . '
                            )
                    WHERE ("Dependencia"."tipo_dependencia_id" = 2 OR "Dependencia"."tipo_dependencia_id" = 4) AND "Dependencia"."sigla" IS NOT NULL 
                    GROUP BY "Dependencia"."id", "Dependencia"."nombre", "Dependencia"."sigla" 
                    ORDER BY "Dependencia"."nombre"'
            );
            $reporte_estado[13] = $this->Proceso->query(
                    'SELECT "Dependencia"."nombre" AS "Dependencia__nombre", "Dependencia"."sigla" AS "Dependencia__sigla", 
                        COUNT("Proceso".*) AS "Proceso__total"
                    FROM "per_dependencias" AS "Dependencia"
                        LEFT JOIN "pag_procesos" AS "Proceso" ON ("Proceso"."dependencia_id" = "Dependencia"."id"
                            ' . $fechas . '
                            ' . $monto . ' AND "Proceso"."estado_id" = 13
                            ' . $analista . '
                            )
                    WHERE ("Dependencia"."tipo_dependencia_id" = 2 OR "Dependencia"."tipo_dependencia_id" = 4) AND "Dependencia"."sigla" IS NOT NULL 
                    GROUP BY "Dependencia"."id", "Dependencia"."nombre", "Dependencia"."sigla" 
                    ORDER BY "Dependencia"."nombre"'
            );
        } elseif ($params['detalle'] == 'por_motivos') {
            foreach ($motivos as $motivo) {
                $reporte_motivo[$motivo['Motivo']['id']] = $this->Proceso->query(
                        'SELECT "Dependencia"."nombre" AS "Dependencia__nombre", "Dependencia"."sigla" AS "Dependencia__sigla", 
                            COUNT("Proceso".*) AS "Proceso__total"
                        FROM "per_dependencias" AS "Dependencia"
                            LEFT JOIN "pag_procesos" AS "Proceso" ON ("Proceso"."dependencia_id" = "Dependencia"."id"
                                ' . $fechas . '
                                ' . $monto . ' AND "Proceso"."motivo_id" = ' . $motivo['Motivo']['id'] . '
                                ' . $analista . '
                                )
                        WHERE ("Dependencia"."tipo_dependencia_id" = 2 OR "Dependencia"."tipo_dependencia_id" = 4) AND "Dependencia"."sigla" IS NOT NULL 
                        GROUP BY "Dependencia"."id", "Dependencia"."nombre", "Dependencia"."sigla" 
                        ORDER BY "Dependencia"."nombre"'
                );
            }
        }
        
        /**
         * 
         */
        if (!empty($params['pdf'])) {
            $this->autoRender = false;
            App::import('Vendor', 'tcpdf/xtcpdf');

            // crea el documento PDF
            $pdf = new XTCPDF('L', 'mm', array(215, 330));

            // Modificar información del PDF
            $pdf->setTituloPDF('Reporte');

            // set font
            $pdf->SetFont('freesans', '', 12);
            
            $pdf->setAutoPageBreak(false);
            
            // add a page
            $pdf->AddPage();
            
            $pdf->SetY(14);
            $pdf->Image(WWW_ROOT . '/img/logo_gach_pdf.jpg');
            $pdf->SetY(16);
            $pdf->SetFont('freesans', 'B', 14);
            $pdf->SetTextColor(22, 102, 152);
            $pdf->Cell(310, 6, 'Gobierno Autónomo Departamental de Chuquisaca', 0, 0, 'C');
            $pdf->Ln();
            $pdf->SetFont('freesans', 'B', 12);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(310, 6, 'Sistema de Seguimiento de Proceso de Pago', 0, 0, 'C');
            $pdf->Ln();
            
            $meses = array(
                '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo',
                '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
                '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre',
                '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre',
            );
            
            $pdf->SetFont('freesans', 'B', 13);
            $pdf->SetTextColor(221, 0, 0);
            switch ($params['ciclo']) {
                case 'anual':
                    $pdf->Cell(310, 6, 'Reporte Anual (' . $params['anio'] . ')' . $txt_monto , 0, 0, 'C');
                    break;
                case 'mensual':
                    $pdf->Cell(310, 6, 'Reporte Mensual (' . $meses[$params['mes']] . ')' . $txt_monto , 0, 0, 'C');
                    break;
                case 'diario':
                    $dia = explode('-', $params['dia']);
                    $pdf->Cell(310, 6, 'Reporte Diario (' . $dia[2] . '/' . $dia[1] . '/' . $dia[0] . ')' . $txt_monto, 0, 0, 'C');
                    break;
            }
            
            $pdf->Ln();

            $pdf->SetTextColor(0, 0, 0);
            
            $pdf->SetFillColor(245, 245, 245);
            $pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
            $pdf->RoundedRect(266, 18, 54, 14, 2.50, '1111', 'DF');
            
            $pdf->SetY(20);
            $pdf->SetX(269);
            $pdf->SetFont('freesans', '', 8);
            $pdf->Cell(40, 6, 'Usuario: ' . $this->Auth->user('nick') , 0, 0, 'L');
            $pdf->Ln();
            $pdf->SetY(25);
            $pdf->SetX(269);
            $pdf->Cell(40, 6, 'Fecha de emisión: ' . date('d/m/Y H:i') , 0, 0, 'L');
            
            $pdf->SetY(40);
            
            if ($params['detalle'] == 'por_estados') {
                if ($params['monto'] == 'menores') {
                    $pdf->SetFont('freesans', 'B', 8);
                    $pdf->SetFillColor(190, 190, 190);
                    $pdf->SetLineStyle(array('width' => 0.3, 'color' => array(145, 145, 145)));
                    $pdf->MultiCell(90, 29, 'Dependencia', 1, 'C', true, 0, '', '', true, 0, false, true, 29, 'M');
                    $pdf->MultiCell(200, 8, 'Procesos por Estado', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 29, 'Total', 1, 'C', true, 0, '', '', true, 0, false, true, 29, 'M');
                    $pdf->Ln();
                    
                    $pdf->SetY(48);
                    
                    $pdf->SetFont('freesans', 'B', 7);
                    $pdf->SetFillColor(215, 215, 215);
                    $pdf->MultiCell(20, 16, '[1] Recepción', 1, 'C', true, 0, 100, '', true, 0, false, true, 16, 'M');
                    $pdf->MultiCell(80, 8, '[2] Análisis y Proceso', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(40, 8, '[3] Elaboración de Cheque', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 16, '[4] Firma de Cheque', 1, 'C', true, 0, '', '', true, 0, false, true, 16, 'M');
                    $pdf->MultiCell(20, 16, '[5] Entrega de Cheque', 1, 'C', true, 0, '', '', true, 0, false, true, 16, 'M');
                    $pdf->MultiCell(20, 16, '[6] Archivo', 1, 'C', true, 0, '', '', true, 0, false, true, 16, 'M');
                    
                    $pdf->Ln();
                    
                    $pdf->SetY(56);
                    
                    $pdf->SetFillColor(230, 230, 230);
                    $pdf->MultiCell(20, 8, '[2.1] Recepción', 1, 'C', true, 0, 120, '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, '[2.2] Revisión', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, '[2.3] Con observación', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, '[2.4] Sin observación', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, '[3.1] Recepción', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, '[3.2] Impresión', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->Ln();
                    
                    $pdf->SetY(64);
                    
                    $pdf->SetFont('freesans', '', 7);
                    $pdf->SetFillColor(245, 245, 245);
                    $pdf->MultiCell(20, 5, 'Dir. Finanzas', 1, 'C', true, 0, 100, '', true, 0, false, true, 5, 'M');
                    $pdf->MultiCell(20, 5, 'Contabilidad', 1, 'C', true, 0, '', '', true, 0, false, true, 5, 'M');
                    $pdf->MultiCell(20, 5, 'Analistas', 1, 'C', true, 0, '', '', true, 0, false, true, 5, 'M');
                    $pdf->MultiCell(20, 5, 'Administradores', 1, 'C', true, 0, '', '', true, 0, false, true, 5, 'M');
                    $pdf->MultiCell(20, 5, 'Contabilidad', 1, 'C', true, 0, '', '', true, 0, false, true, 5, 'M');
                    $pdf->MultiCell(20, 5, 'Tesorería', 1, 'C', true, 0, '', '', true, 0, false, true, 5, 'M');
                    $pdf->MultiCell(20, 5, 'Tesorería', 1, 'C', true, 0, '', '', true, 0, false, true, 5, 'M');
                    $pdf->MultiCell(20, 5, 'Contabilidad', 1, 'C', true, 0, '', '', true, 0, false, true, 5, 'M');
                    $pdf->MultiCell(20, 5, 'Caja', 1, 'C', true, 0, '', '', true, 0, false, true, 5, 'M');
                    $pdf->MultiCell(20, 5, 'Archivo', 1, 'C', true, 0, '', '', true, 0, false, true, 5, 'M');
                    $pdf->Ln();
                    
                    $totales[0] = 0;
                    $totales[1] = 0; $totales[2] = 0; $totales[3] = 0; $totales[4] = 0; 
                    $totales[5] = 0; $totales[6] = 0; $totales[7] = 0; $totales[8] = 0;
                    $totales[12] = 0; $totales[13] = 0;
                    
                    $i = 0;
                    foreach ($reporte_total as $dependencia) {
                        $i++;
                        if ($i % 2) {
                            $pdf->SetFillColor(255, 255, 255);
                        } else {
                            $pdf->SetFillColor(240, 240, 240);
                        }
                        
                        $pdf->SetFont('freesans', '', 7);
                        $pdf->MultiCell(90, 8, $dependencia['Dependencia']['nombre'], 1, 'L', true, 0, '', '', true, 0, false, true, 8, 'M');
                        $pdf->SetFont('freesans', '', 7);
                        list(, $estado) = each($reporte_estado[1]);
                        $totales[1] += $estado['Proceso']['total'];
                        $pdf->MultiCell(20, 8, $estado['Proceso']['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                        list(, $estado) = each($reporte_estado[2]);
                        $totales[2] += $estado['Proceso']['total'];
                        $pdf->MultiCell(20, 8, $estado['Proceso']['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                        list(, $estado) = each($reporte_estado[3]);
                        $totales[3] += $estado['Proceso']['total'];
                        $pdf->MultiCell(20, 8, $estado['Proceso']['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                        list(, $estado) = each($reporte_estado[4]);
                        $totales[4] += $estado['Proceso']['total'];
                        $pdf->MultiCell(20, 8, $estado['Proceso']['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                        list(, $estado) = each($reporte_estado[5]);
                        $totales[5] += $estado['Proceso']['total'];
                        $pdf->MultiCell(20, 8, $estado['Proceso']['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                        list(, $estado) = each($reporte_estado[6]);
                        $totales[6] += $estado['Proceso']['total'];
                        $pdf->MultiCell(20, 8, $estado['Proceso']['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                        list(, $estado) = each($reporte_estado[7]);
                        $totales[7] += $estado['Proceso']['total'];
                        $pdf->MultiCell(20, 8, $estado['Proceso']['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                        list(, $estado) = each($reporte_estado[8]);
                        $totales[8] += $estado['Proceso']['total'];
                        $pdf->MultiCell(20, 8, $estado['Proceso']['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                        list(, $estado) = each($reporte_estado[12]);
                        $totales[12] += $estado['Proceso']['total'];
                        $pdf->MultiCell(20, 8, $estado['Proceso']['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                        list(, $estado) = each($reporte_estado[13]);
                        $totales[13] += $estado['Proceso']['total'];
                        $pdf->MultiCell(20, 8, $estado['Proceso']['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                        $totales[0] += $dependencia['Proceso']['total'];
                        $pdf->SetFont('freesans', 'B', 7);
                        $pdf->MultiCell(20, 8, $dependencia['Proceso']['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                        
                        $pdf->Ln();
                    }
                    
                    $pdf->SetFont('freesans', 'B', 7);
                    $pdf->SetFillColor(240, 240, 240);
                    $pdf->MultiCell(90, 8, '', 0, 'C', false, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, $totales[1], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, $totales[2], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, $totales[3], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, $totales[4], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, $totales[5], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, $totales[6], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, $totales[7], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, $totales[8], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, $totales[12], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, $totales[13], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, $totales[0], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');

                    $pdf->Ln();
                } else {
                    $pdf->SetFont('freesans', 'B', 8);
                    $pdf->SetFillColor(190, 190, 190);
                    $pdf->SetLineStyle(array('width' => 0.3, 'color' => array(145, 145, 145)));
                    $pdf->MultiCell(50, 29, 'Dependencia', 1, 'C', true, 0, '', '', true, 0, false, true, 29, 'M');
                    $pdf->MultiCell(240, 8, 'Procesos por Estado', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 29, 'Total', 1, 'C', true, 0, '', '', true, 0, false, true, 29, 'M');
                    $pdf->Ln();
                    
                    $pdf->SetY(48);
                    
                    $pdf->SetFont('freesans', 'B', 7);
                    $pdf->SetFillColor(215, 215, 215);
                    $pdf->MultiCell(20, 16, '[1] Recepción', 1, 'C', true, 0, 60, '', true, 0, false, true, 16, 'M');
                    $pdf->MultiCell(80, 8, '[2] Análisis y Proceso', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(40, 8, '[3] Elaboración de Cheque', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 16, '[4] Firma de Cheque', 1, 'C', true, 0, '', '', true, 0, false, true, 16, 'M');
                    $pdf->MultiCell(20, 16, '[5] Firma de Cheque', 1, 'C', true, 0, '', '', true, 0, false, true, 16, 'M');
                    $pdf->MultiCell(20, 16, '[6] Cheque concluido', 1, 'C', true, 0, '', '', true, 0, false, true, 16, 'M');
                    $pdf->MultiCell(20, 16, '[7] Entrega de Cheque', 1, 'C', true, 0, '', '', true, 0, false, true, 16, 'M');
                    $pdf->MultiCell(20, 16, '[8] Archivo', 1, 'C', true, 0, '', '', true, 0, false, true, 16, 'M');
                    
                    $pdf->Ln();
                    
                    $pdf->SetY(56);
                    
                    $pdf->SetFillColor(230, 230, 230);
                    $pdf->MultiCell(20, 8, '[2.1] Recepción', 1, 'C', true, 0, 80, '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, '[2.2] Revisión', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, '[2.3] Con observación', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, '[2.4] Sin observación', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, '[3.1] Recepción', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, '[3.2] Impresión', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->Ln();
                    
                    $pdf->SetY(64);
                    
                    $pdf->SetFont('freesans', '', 7);
                    $pdf->SetFillColor(245, 245, 245);
                    $pdf->MultiCell(20, 5, 'Dir. Finanzas', 1, 'C', true, 0, 60, '', true, 0, false, true, 5, 'M');
                    $pdf->MultiCell(20, 5, 'Contabilidad', 1, 'C', true, 0, '', '', true, 0, false, true, 5, 'M');
                    $pdf->MultiCell(20, 5, 'Analistas', 1, 'C', true, 0, '', '', true, 0, false, true, 5, 'M');
                    $pdf->MultiCell(20, 5, 'Administradores', 1, 'C', true, 0, '', '', true, 0, false, true, 5, 'M');
                    $pdf->MultiCell(20, 5, 'Contabilidad', 1, 'C', true, 0, '', '', true, 0, false, true, 5, 'M');
                    $pdf->MultiCell(20, 5, 'Tesorería', 1, 'C', true, 0, '', '', true, 0, false, true, 5, 'M');
                    $pdf->MultiCell(20, 5, 'Tesorería', 1, 'C', true, 0, '', '', true, 0, false, true, 5, 'M');
                    $pdf->MultiCell(20, 5, 'Dir. Finanzas', 1, 'C', true, 0, '', '', true, 0, false, true, 5, 'M');
                    $pdf->MultiCell(20, 5, 'Stria. Economía', 1, 'C', true, 0, '', '', true, 0, false, true, 5, 'M');
                    $pdf->MultiCell(20, 5, 'Dir. Finanzas', 1, 'C', true, 0, '', '', true, 0, false, true, 5, 'M');
                    $pdf->MultiCell(20, 5, 'Caja', 1, 'C', true, 0, '', '', true, 0, false, true, 5, 'M');
                    $pdf->MultiCell(20, 5, 'Archivo', 1, 'C', true, 0, '', '', true, 0, false, true, 5, 'M');
                    $pdf->Ln();
                    
                    $totales[0] = 0;
                    $totales[1] = 0; $totales[2] = 0; $totales[3] = 0; $totales[4] = 0; 
                    $totales[5] = 0; $totales[6] = 0; $totales[7] = 0; $totales[9] = 0;
                    $totales[10] = 0; $totales[11] = 0;
                    $totales[12] = 0; $totales[13] = 0;
                    
                    $i = 0;
                    foreach ($reporte_total as $dependencia) {
                        $i++;
                        if ($i % 2) {
                            $pdf->SetFillColor(255, 255, 255);
                        } else {
                            $pdf->SetFillColor(240, 240, 240);
                        }
                        
                        $pdf->SetFont('freesans', '', 7);
                        $pdf->MultiCell(50, 8, $dependencia['Dependencia']['nombre'], 1, 'L', true, 0, '', '', true, 0, false, true, 8, 'M');
                        $pdf->SetFont('freesans', '', 7);
                        list(, $estado) = each($reporte_estado[1]);
                        $totales[1] += $estado['Proceso']['total'];
                        $pdf->MultiCell(20, 8, $estado['Proceso']['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                        list(, $estado) = each($reporte_estado[2]);
                        $totales[2] += $estado['Proceso']['total'];
                        $pdf->MultiCell(20, 8, $estado['Proceso']['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                        list(, $estado) = each($reporte_estado[3]);
                        $totales[3] += $estado['Proceso']['total'];
                        $pdf->MultiCell(20, 8, $estado['Proceso']['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                        list(, $estado) = each($reporte_estado[4]);
                        $totales[4] += $estado['Proceso']['total'];
                        $pdf->MultiCell(20, 8, $estado['Proceso']['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                        list(, $estado) = each($reporte_estado[5]);
                        $totales[5] += $estado['Proceso']['total'];
                        $pdf->MultiCell(20, 8, $estado['Proceso']['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                        list(, $estado) = each($reporte_estado[6]);
                        $totales[6] += $estado['Proceso']['total'];
                        $pdf->MultiCell(20, 8, $estado['Proceso']['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                        list(, $estado) = each($reporte_estado[7]);
                        $totales[7] += $estado['Proceso']['total'];
                        $pdf->MultiCell(20, 8, $estado['Proceso']['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                        list(, $estado) = each($reporte_estado[9]);
                        $totales[9] += $estado['Proceso']['total'];
                        $pdf->MultiCell(20, 8, $estado['Proceso']['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                        list(, $estado) = each($reporte_estado[10]);
                        $totales[10] += $estado['Proceso']['total'];
                        $pdf->MultiCell(20, 8, $estado['Proceso']['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                        list(, $estado) = each($reporte_estado[11]);
                        $totales[11] += $estado['Proceso']['total'];
                        $pdf->MultiCell(20, 8, $estado['Proceso']['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                        list(, $estado) = each($reporte_estado[12]);
                        $totales[12] += $estado['Proceso']['total'];
                        $pdf->MultiCell(20, 8, $estado['Proceso']['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                        list(, $estado) = each($reporte_estado[13]);
                        $totales[13] += $estado['Proceso']['total'];
                        $pdf->MultiCell(20, 8, $estado['Proceso']['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                        $totales[0] += $dependencia['Proceso']['total'];
                        $pdf->SetFont('freesans', 'B', 7);
                        $pdf->MultiCell(20, 8, $dependencia['Proceso']['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                        
                        $pdf->Ln();
                    }
                    
                    $pdf->SetFont('freesans', 'B', 7);
                    $pdf->SetFillColor(240, 240, 240);
                    $pdf->MultiCell(50, 8, '', 0, 'C', false, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, $totales[1], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, $totales[2], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, $totales[3], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, $totales[4], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, $totales[5], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, $totales[6], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, $totales[7], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, $totales[9], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, $totales[10], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, $totales[11], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, $totales[12], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, $totales[13], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, $totales[0], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');

                    $pdf->Ln();
                }
            } elseif ($params['detalle'] == 'por_motivos') {
                $pdf->SetFont('freesans', 'B', 8);
                $pdf->SetFillColor(190, 190, 190);
                $pdf->SetLineStyle(array('width' => 0.3, 'color' => array(145, 145, 145)));
                $pdf->MultiCell(90, 20, 'Dependencia', 1, 'C', true, 0, '', '', true, 0, false, true, 20, 'M');
                $pdf->MultiCell(198, 8, 'Procesos por Motivo', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(22, 20, 'Total', 1, 'C', true, 0, '', '', true, 0, false, true, 20, 'M');
                $pdf->Ln();

                $pdf->SetY(48);
                
                $totales[0] = 0;
                foreach ($motivos as $motivo) {
                    $totales[$motivo['Motivo']['id']] = 0;
                }

                $pdf->SetX(100);
                $pdf->SetFont('freesans', 'B', 7);
                $pdf->SetFillColor(215, 215, 215);
                foreach ($motivos as $motivo) {
                    $pdf->MultiCell(22, 12, $motivo['Motivo']['nombre'], 1, 'C', true, 0, '', '', true, 0, false, true, 12, 'M');
                }

                $pdf->SetY(60);

                $i = 0;
                foreach ($reporte_total as $dependencia) {
                    $i++;
                    if ($i % 2) {
                        $pdf->SetFillColor(255, 255, 255);
                    } else {
                        $pdf->SetFillColor(240, 240, 240);
                    }
                    
                    $pdf->SetFont('freesans', '', 8);
                    $pdf->MultiCell(90, 8, $dependencia['Dependencia']['nombre'], 1, 'L', true, 0, '', '', true, 0, false, true, 8, 'M');
                    
                    $pdf->SetFont('freesans', '', 7);
                    foreach ($motivos as $motivo) {
                        list(, $motivo_id) = each($reporte_motivo[$motivo['Motivo']['id']]);
                        $pdf->MultiCell(22, 8, $motivo_id['Proceso']['total'], 1, 'L', true, 0, '', '', true, 0, false, true, 8, 'M');
                    }
                    
                    $totales[0] += $dependencia['Proceso']['total'];
                    
                    $pdf->SetFont('freesans', 'B', 7);
                    $pdf->MultiCell(22, 8, $dependencia['Proceso']['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');

                    $pdf->Ln();
                }
                
                $pdf->SetX(100);
                $pdf->SetFont('freesans', 'B', 7);
                $pdf->SetFillColor(240, 240, 240);
                foreach($motivos as $motivo) {
                    $pdf->MultiCell(22, 8, $totales[$motivo['Motivo']['id']], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                }
                $pdf->MultiCell(22, 8, $totales[0], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');

                $pdf->Ln();
            } elseif (empty($params['detalle'])) {
                $pdf->SetFont('freesans', 'B', 8);
                $pdf->SetFillColor(234, 234, 234);
                $pdf->SetLineStyle(array('width' => 0.4, 'color' => array(187, 187, 187)));
                $pdf->MultiCell(120, 8, 'Dependencia', 1, 'C', true, 0, 90, '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(30, 8, 'Total Procesos', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->Ln();

                $i = 0;
                $total = 0;
                $pdf->SetFont('freesans', '', 8);
                foreach ($reporte_total as $dependencia) {
                    $i++;
                    if ($i % 2) {
                        $pdf->SetFillColor(255, 255, 255);
                    } else {
                        $pdf->SetFillColor(249, 249, 249);
                    }

                    $pdf->MultiCell(120, 8, $dependencia['Dependencia']['nombre'], 1, 'L', true, 0, 90, '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(30, 8, $dependencia['Proceso']['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->Ln();
                    
                    $total += $dependencia['Proceso']['total'];
                }
                $pdf->SetFont('freesans', 'B', 8);
                $pdf->MultiCell(120, 8, 'Total', 1, 'L', true, 0, 90, '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(30, 8, $total, 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->Ln();
            }
            
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

            //Close and output PDF document
            if ($params['pdf'] == 'descargar') {
                $pdf->Output('reporte.pdf', 'D');
            } elseif ($params['pdf'] == 'imprimir') { 
                $pdf->Output('reporte.pdf', 'I');
            }
        }
        /***/
        
        $this->set(compact('reporte_total', 'reporte_estado', 'reporte_motivo'));
        $this->set('motivos', $motivos);
    }
    
    /**
     * por_estados method
     * Método para por_estados reportes
     * 
     * @return void
     */
    public function por_estados() {
        $params = $this->params['named'];
        $params['page'] = empty($params['page']) ? 1 : $params['page'];
        $params['buscar'] = empty($params['buscar']) ? '' : $params['buscar'];
        $params['monto'] = empty($params['monto']) ? 'menores' : $params['monto'];
        $params['estado'] = empty($params['estado']) ? '1' : $params['estado'];
        $params['estado_envio'] = empty($params['estado_envio']) ? 'P' : $params['estado_envio'];
        $params['analista'] = empty($params['analista']) ? '' : $params['analista'];
        $params['dependencia'] = empty($params['dependencia']) ? '' : $params['dependencia'];
        $params['fecha_ini'] = empty($params['fecha_ini']) ? date('Y-m-d') : $params['fecha_ini'];
        $params['fecha_fin'] = empty($params['fecha_fin']) ? date('Y-m-d') : $params['fecha_fin'];
        $params['pdf'] = empty($params['pdf']) ? '' : $params['pdf'];
        $params['controller'] = $this->params['controller'];
        $this->set('params', $params);
        
        $estados = array(
            '1' => '[1] Recepcionados (Dir. Finanzas)',
            '2' => '[2.1] Recepción (Contabilidad)',
            '3' => '[2.2] Revisión (Analistas)',
            '4' => '[2.3] Con observación (Administradores)',
            '5' => '[2.4] Sin observación (Contabilidad)',
            '6' => '[3.1] Recepción (Tesorería)',
            '7' => '[3.2] Impresión (Tesorería)',
            '8' => '[4] Firma de Cheque (Contabilidad)',
            '9' => '[4] Firma de Cheque (Dir. Finanzas)',
            '10' => '[5] Firma de Cheque (Stria. Economía)',
            '11' => '[6] Cheque concluído (Dir. Finanzas)',
            '12' => '[5] Entrega de Cheque (Caja)',
            '13' => '[6] Archivo (Archivo)'
        );
        $estados_men = array(
            '1' => '[1] Recepcionados (Dir. Finanzas)',
            '[2] Análisis y Proceso' => array(
                '2' => '[2.1] Recepción (Contabilidad)',
                '3' => '[2.2] Revisión (Analistas)',
                '4' => '[2.3] Con observación (Administradores)',
                '5' => '[2.4] Sin observación (Contabilidad)'
            ),
            '[3] Elaboración de Cheque' => array(
                '6' => '[3.1] Recepción (Tesorería)',
                '7' => '[3.2] Impresión (Tesorería)',
            ),
            '8' => '[4] Firma de Cheque (Contabilidad)',
            '12' => '[5] Entrega de Cheque (Caja)',
            '13' => '[6] Archivo (Archivo)',
        );
        $this->set('estados_men', $estados_men);
        
        $estados_may = array(
            '1' => '[1] Recepcionados (Dir. Finanzas)',
            '[2] Análisis y Proceso' => array(
                '2' => '[2.1] Recepción (Contabilidad)',
                '3' => '[2.2] Revisión (Analistas)',
                '4' => '[2.3] Con observación (Administradores)',
                '5' => '[2.4] Sin observación (Contabilidad)'
            ),
            '[3] Elaboración de Cheque' => array(
                '6' => '[3.1] Recepción (Tesorería)',
                '7' => '[3.2] Impresión (Tesorería)',
            ),
            '9' => '[4] Firma de Cheque (Dir. Finanzas)',
            '10' => '[5] Firma de Cheque (Stria. Economía)',
            '11' => '[6] Cheque concluído (Dir. Finanzas)',
            '12' => '[7] Entrega de Cheque (Caja)',
            '13' => '[8] Archivo (Archivo)',
        );
        $this->set('estados_may', $estados_may);
        
        /** Analistas */
        $analistas = $this->ServidoresPublico->find('all', array(
            'conditions' => array(
                'Usuario.rol' => 'analistas'
            ),
            'fields' => array(
                'Usuario.id',
                'ServidoresPublico.nombre'
            ),
            'order' => 'ServidoresPublico.nombre',
            'recursive' => 0
        ));
        $this->set('analistas', Set::combine($analistas, '{n}.Usuario.id', '{n}.ServidoresPublico.nombre'));

        $dependencias = $this->Proceso->Dependencia->find('list', array(
            'conditions' => array(
                'Dependencia.tipo_dependencia_id' => array(2, 4),
                'Dependencia.sigla <>' => NULL
            ),
            'fields' => array('Dependencia.id', 'Dependencia.sigla'),
            'recursive' => 0
        ));
        $this->set('dependencias', $dependencias);
        
        if (!empty($params['buscar'])) {
            /** Condiciones de filtrado */
            $conditions = array('AND' => array(
                'Proceso.fecha_emision >=' => Configure::read('App.year_act') . '-01-01',
                'Proceso.fecha_emision <=' => Configure::read('App.year_act') . '-12-31',
            ));
            
            if ($params['monto'] == 'menores') {
                $conditions['Proceso.monto <='] = 10000;
            } else {
                $conditions['Proceso.monto >'] = 10000;
            }
            
            if (($params['estado'] == 3) && !empty($params['analista'])) {
                $conditions['Proceso.usuario_analista_id'] = $params['analista'];
            }
            if (!empty($params['dependencia'])) {
                $conditions['Proceso.dependencia_id'] = $params['dependencia'];
            }
            
            if ($params['estado_envio'] == 'P') {
                $conditions['Proceso.estado_id'] = $params['estado'];
                
                $conditions_joins = array(
                    'ProcesosEstado.id = Proceso.ultimo_estado_id',
                );
            } elseif ($params['estado_envio'] == 'H') {
                $conditions['ProcesosEstado.estado_id'] = $params['estado'];
                
                $conditions_joins = array(
                    'ProcesosEstado.proceso_id = Proceso.id',
                );

                if (!empty($params['fecha_ini'])) {
                    $conditions_joins[] = "ProcesosEstado.fecha_envio >='" . $params['fecha_ini'] . " 00:00'";
                }
                if (!empty($params['fecha_fin'])) {
                    $conditions_joins[] = "ProcesosEstado.fecha_envio <='" . $params['fecha_fin'] . " 23:59'";
                }
            }

            $this->Proceso->recursive = 0;
            if (empty($params['pdf'])) {
                $this->paginate = array(
                    'Proceso' => array(
                        'limit' => 20,
                        'conditions' => $conditions,
                        'order' => array('Proceso.nro_proceso' => 'desc'),
                        'fields' => array(
                            'id', 'cite', 'nro_proceso', 'nro_preventivo',
                            'beneficiario_documento', 'beneficiario_nombre', 'monto', 'usuario_analista_id', 
                            'Motivo.id', 'Motivo.nombre', 
                            'ProcesosEstado.fecha_envio', 'ProcesosEstado.fecha_recepcion',
                            'Dependencia.sigla',
                            'ServidoresPublico.nombres', 'ServidoresPublico.apellidos'
                        ),
                        'joins' => array(
                            array(
                                'table' => 'pag_procesos_estados',
                                'alias' => 'ProcesosEstado',
                                'type' => 'LEFT',
                                'conditions' => $conditions_joins
                            ),
                            array(
                                'table' => 'per_servidores_publicos',
                                'alias' => 'ServidoresPublico',
                                'type' => 'LEFT',
                                'conditions' => array(
                                    'ServidoresPublico.usuario_id = Proceso.usuario_analista_id'
                                )
                            )
                        )
                    )
                );
                $procesos = $this->paginate('Proceso');
                $this->set('procesos', $procesos);
            } else {
                $procesos = $this->Proceso->find('all', array(
                    'conditions' => $conditions,
                    'order' => array('Proceso.nro_proceso' => 'desc'),
                    'fields' => array(
                        'id', 'cite', 'nro_proceso', 'nro_preventivo',
                        'beneficiario_documento', 'beneficiario_nombre', 'monto', 'usuario_analista_id', 
                        'Motivo.id', 'Motivo.nombre', 
                        'ProcesosEstado.fecha_envio', 'ProcesosEstado.fecha_recepcion',
                        'Dependencia.sigla',
                        'ServidoresPublico.nombres', 'ServidoresPublico.apellidos'
                    ),
                    'joins' => array(
                        array(
                            'table' => 'pag_procesos_estados',
                            'alias' => 'ProcesosEstado',
                            'type' => 'LEFT',
                            'conditions' => $conditions_joins
                        ),
                        array(
                            'table' => 'per_servidores_publicos',
                            'alias' => 'ServidoresPublico',
                            'type' => 'LEFT',
                            'conditions' => array(
                                'ServidoresPublico.usuario_id = Proceso.usuario_analista_id'
                            )
                        )
                    ),
                    'recursive' => 0
                ));
            }
        
            if (!empty($params['pdf'])) {
                $this->autoRender = false;
                App::import('Vendor', 'tcpdf/xtcpdf');

                // crea el documento PDF
                $pdf = new XTCPDF('L', 'mm', array(215, 330));

                // Modificar información del PDF
                $pdf->setTituloPDF('Reportes por estado');

                // set font
                $pdf->SetFont('freesans', '', 12);

                $pdf->setAutoPageBreak(false);

                // add a page
                $pdf->AddPage();
            
                $pdf->SetY(13);
                $pdf->Image(WWW_ROOT . '/img/logo_gach_pdf.jpg');
                $pdf->SetY(16);
                $pdf->SetFont('freesans', 'B', 14);
                $pdf->SetTextColor(22, 102, 152);
                $pdf->Cell(310, 6, 'Gobierno Autónomo Departamental de Chuquisaca', 0, 0, 'C');
                $pdf->Ln();
                $pdf->SetFont('freesans', 'B', 12);
                $pdf->SetTextColor(0, 0, 0);
                $txt_monto = ($params['monto'] == 'menores')?'Menor o igual a 10000':'Mayor a 10000';
                $pdf->Cell(310, 6, 'Reporte por Estados - Monto: ' . $txt_monto, 0, 0, 'C');
                $pdf->Ln();
                $pdf->SetFont('freesans', 'B', 11);
                $pdf->SetTextColor(221, 0, 0);
                $pdf->Cell(310, 6, $estados[$params['estado']], 0, 0, 'C');
                $pdf->Ln();

                $pdf->SetY(38);
                $pdf->SetTextColor(0, 0, 0);
            
                $pdf->SetFillColor(245, 245, 245);
                $pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
                $pdf->RoundedRect(266, 18, 54, 14, 2.50, '1111', 'DF');

                $pdf->SetY(20);
                $pdf->SetX(269);
                $pdf->SetFont('freesans', '', 8);
                $pdf->Cell(40, 6, 'Usuario: ' . $this->Auth->user('nick') , 0, 0, 'L');
                $pdf->Ln();
                $pdf->SetY(25);
                $pdf->SetX(269);
                $pdf->Cell(40, 6, 'Fecha de emisión: ' . date('d/m/Y H:i') , 0, 0, 'L');
            
                $pdf->SetY(40);

                $pdf->SetFont('freesans', 'B', 7);
                $pdf->SetFillColor(234, 234, 234);
                $pdf->SetLineStyle(array('width' => 0.2, 'color' => array(187, 187, 187)));
                
                if (in_array($params['estado'], array('3', '4'))) {
                    $pdf->MultiCell(8, 8, '#', 1, 'C', true, '', '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(21, 8, 'Nº Proceso', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(21, 8, 'Nº Preventivo', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(30, 8, 'Cite', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(25, 8, 'Dependencia', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(25, 8, "Fecha envío", 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(25, 8, 'Fecha recepción', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(90, 4, 'Beneficiario', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                    $pdf->MultiCell(45, 8, 'Analista', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, 'Monto', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->SetY(44);$pdf->SetX(165);
                    $pdf->MultiCell(30, 4, 'Documento', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                    $pdf->MultiCell(60, 4, 'Nombre', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                } else {
                    $pdf->MultiCell(8, 8, '#', 1, 'C', true, '', '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(21, 8, 'Nº Proceso', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(21, 8, 'Nº Preventivo', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(45, 8, 'Cite', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(35, 8, 'Dependencia', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(25, 8, "Fecha envío", 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(25, 8, 'Fecha recepción', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(110, 4, 'Beneficiario', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                    $pdf->MultiCell(20, 8, 'Monto', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->SetY(44);$pdf->SetX(190);
                    $pdf->MultiCell(30, 4, 'Documento', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                    $pdf->MultiCell(80, 4, 'Nombre', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                }
                
                $pdf->Ln();

                $i = 0;
                $total = 0;
                $pdf->SetFont('freesans', '', 7);
                foreach ($procesos as $proceso) {
                    $y_cite = ceil($pdf->getStringHeight(30, $proceso['Proceso']['cite'], $reseth = true, $autopadding = true, $border = 1)) + 1;
                    $y_beneficiario = ceil($pdf->getStringHeight(45, $proceso['ServidoresPublico']['nombres'] . ' ' . $proceso['ServidoresPublico']['apellidos'], $reseth = true, $autopadding = true, $border = 1)) + 1;
                    $y = max(array($y_cite, $y_beneficiario));
                    if ($pdf->getY() + $y > $pdf->getPageHeight() - 15) {
                        $pdf->AddPage();
                        $pdf->SetY(16);
                        $pdf->SetFont('freesans', 'B', 7);
                        $pdf->SetFillColor(234, 234, 234);
                        $h = $pdf->GetY();
                        
                        
                        if (in_array($params['estado'], array('3', '4'))) {
                            $pdf->MultiCell(8, 8, '#', 1, 'C', true, '', '', '', true, 0, false, true, 8, 'M');
                            $pdf->MultiCell(21, 8, 'Nº Proceso', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                            $pdf->MultiCell(21, 8, 'Nº Preventivo', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                            $pdf->MultiCell(30, 8, 'Cite', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                            $pdf->MultiCell(25, 8, 'Dependencia', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                            $pdf->MultiCell(25, 8, "Fecha envío", 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                            $pdf->MultiCell(25, 8, 'Fecha recepción', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                            $pdf->MultiCell(90, 4, 'Beneficiario', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                            $pdf->MultiCell(45, 8, 'Analista', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                            $pdf->MultiCell(20, 8, 'Monto', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                            $pdf->SetY($h + 4); $pdf->SetX(165);
                            $pdf->MultiCell(30, 4, 'Documento', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                            $pdf->MultiCell(60, 4, 'Nombre', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                        } else {
                            $pdf->MultiCell(8, 8, '#', 1, 'C', true, '', '', '', true, 0, false, true, 8, 'M');
                            $pdf->MultiCell(21, 8, 'Nº Proceso', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                            $pdf->MultiCell(21, 8, 'Nº Preventivo', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                            $pdf->MultiCell(45, 8, 'Cite', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                            $pdf->MultiCell(35, 8, 'Dependencia', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                            $pdf->MultiCell(25, 8, "Fecha envío", 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                            $pdf->MultiCell(25, 8, 'Fecha recepción', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                            $pdf->MultiCell(110, 4, 'Beneficiario', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                            $pdf->MultiCell(20, 8, 'Monto', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                            $pdf->SetY($h + 4); $pdf->SetX(190);
                            $pdf->MultiCell(30, 4, 'Documento', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                            $pdf->MultiCell(80, 4, 'Nombre', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                        }
                        
                        $pdf->Ln();
                        $pdf->SetFont('freesans', '', 7);
                    } 

                    $i++;
                    if ($i % 2) {
                        $pdf->SetFillColor(255, 255, 255);
                    } else {
                        $pdf->SetFillColor(249, 249, 249);
                    }
                    
                    if (in_array($params['estado'], array('3', '4'))) {
                        $analista = 'Sin asignar';
                        if (!empty($proceso['ServidoresPublico']['nombres']) && !empty($proceso['ServidoresPublico']['apellidos'])) {
                            $analista = $proceso['ServidoresPublico']['nombres'] . ' ' . $proceso['ServidoresPublico']['apellidos'];
                        }
                    }
                    
                    $fecha_recepcion = empty($proceso['ProcesosEstado']['fecha_recepcion'])?'No recepcionado':$this->fecha($proceso['ProcesosEstado']['fecha_recepcion'], true);
                    if (in_array($params['estado'], array('3', '4'))) {
                        $pdf->MultiCell(8, $y, $i, 1, 'C', true, '', '', '', true, 0, false, true, $y, 'M');
                        $pdf->MultiCell(21, $y, $proceso['Proceso']['nro_proceso'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                        $pdf->MultiCell(21, $y, $proceso['Proceso']['nro_preventivo'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                        $pdf->MultiCell(30, $y, $proceso['Proceso']['cite'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                        $pdf->MultiCell(25, $y, $proceso['Dependencia']['sigla'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                        $pdf->MultiCell(25, $y, $this->fecha($proceso['ProcesosEstado']['fecha_envio'], true), 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                        $pdf->MultiCell(25, $y, $fecha_recepcion, 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                        $pdf->MultiCell(30, $y, $proceso['Proceso']['beneficiario_documento'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                        $pdf->MultiCell(60, $y, $proceso['Proceso']['beneficiario_nombre'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                        $pdf->MultiCell(45, $y, $analista, 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                        $pdf->MultiCell(20, $y, $proceso['Proceso']['monto'], 1, 'R', true, 0, '', '', true, 0, false, true, $y, 'M');
                    } else {
                        $pdf->MultiCell(8, $y, $i, 1, 'C', true, '', '', '', true, 0, false, true, $y, 'M');
                        $pdf->MultiCell(21, $y, $proceso['Proceso']['nro_proceso'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                        $pdf->MultiCell(21, $y, $proceso['Proceso']['nro_preventivo'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                        $pdf->MultiCell(45, $y, $proceso['Proceso']['cite'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                        $pdf->MultiCell(35, $y, $proceso['Dependencia']['sigla'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                        $pdf->MultiCell(25, $y, $this->fecha($proceso['ProcesosEstado']['fecha_envio'], true), 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                        $pdf->MultiCell(25, $y, $fecha_recepcion, 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                        $pdf->MultiCell(30, $y, $proceso['Proceso']['beneficiario_documento'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                        $pdf->MultiCell(80, $y, $proceso['Proceso']['beneficiario_nombre'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                        $pdf->MultiCell(20, $y, $proceso['Proceso']['monto'], 1, 'R', true, 0, '', '', true, 0, false, true, $y, 'M');
                    }
                    $pdf->Ln();
                }

                $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

                //Close and output PDF document
                if ($params['pdf'] == 'descargar') {
                    $pdf->Output('reporte.pdf', 'D');
                } elseif ($params['pdf'] == 'imprimir') { 
                    $pdf->Output('reporte.pdf', 'I');
                }
            }
        /***/
        } else {
            $this->set('procesos', null);
        }
        
    }
    
    /**
     * por_funcionarios method
     * Método para por_funcionarios reportes
     * 
     * @return void
     */
    public function por_funcionarios() {
        $params = $this->params['named'];
        $params['rol'] = empty($params['rol']) ? 'analistas' : $params['rol'];
        $params['pdf'] = empty($params['pdf']) ? '' : $params['pdf'];
        $params['controller'] = $this->params['controller'];
        $this->set('params', $params);
        
        if ($params['rol'] == 'analistas') {
            $funcionarios = $this->ServidoresPublico->find('all', array( 
                'fields' => array('nombres', 'apellidos', 'Usuario.id'),
                'conditions' => array('ServidoresPublico.estado' => '1', 'Usuario.rol' => 'analistas'),
                'order' => array('ServidoresPublico.apellidos' => 'ASC', 'ServidoresPublico.nombres' => 'ASC'),
                'recursive' => 0
            ));

            $menores = $this->Proceso->query('
                SELECT U.id, 
                    COUNT(PGE1.id) AS total, COUNT(PGE2.id) AS pendientes, COUNT(PGE3.id) AS recibidos
                FROM int_usuarios U
                INNER JOIN per_servidores_publicos P ON (P.usuario_id = U.id)
                    LEFT JOIN pag_procesos PG ON (PG.usuario_analista_id = U.id AND PG.estado_id = 3 AND PG.monto <= 10000)
                    LEFT JOIN pag_procesos_estados PGE1 ON (PGE1.id = PG.ultimo_estado_id)
                    LEFT JOIN pag_procesos_estados PGE2 ON (PGE2.id = PG.ultimo_estado_id AND PGE2.fecha_recepcion IS NULL)
                    LEFT JOIN pag_procesos_estados PGE3 ON (PGE3 .id = PG.ultimo_estado_id AND PGE3.fecha_recepcion IS NOT NULL)
                WHERE U.rol = \'analistas\' AND P.estado = \'1\'
                GROUP BY U.id, P.apellidos, P.nombres
                ORDER BY P.apellidos ASC, P.nombres ASC
            ');

            $mayores = $this->Proceso->query('
                SELECT U.id, 
                    COUNT(PGE1.id) AS total, COUNT(PGE2.id) AS pendientes, COUNT(PGE3.id) AS recibidos
                FROM int_usuarios U
                INNER JOIN per_servidores_publicos P ON (P.usuario_id = U.id)
                    LEFT JOIN pag_procesos PG ON (PG.usuario_analista_id = U.id AND PG.estado_id = 3 AND PG.monto > 10000)
                    LEFT JOIN pag_procesos_estados PGE1 ON (PGE1.id = PG.ultimo_estado_id)
                    LEFT JOIN pag_procesos_estados PGE2 ON (PGE2.id = PG.ultimo_estado_id AND PGE2.fecha_recepcion IS NULL)
                    LEFT JOIN pag_procesos_estados PGE3 ON (PGE3.id = PG.ultimo_estado_id AND PGE3.fecha_recepcion IS NOT NULL)
                WHERE U.rol = \'analistas\' AND P.estado = \'1\'
                GROUP BY U.id, P.apellidos, P.nombres
                ORDER BY P.apellidos ASC, P.nombres ASC
            ');
        } elseif ($params['rol'] == 'administradores') {
            $menores = $this->ServidoresPublico->Dependencia->query(
                'SELECT D.id, COUNT(PE1.id) AS men_total, COUNT(PE2.id) AS men_pendientes, COUNT(PE3.id) AS men_recibidos
                    FROM per_dependencias D
                    LEFT JOIN pag_procesos P ON (P.dependencia_id = D.id AND P.estado_id = 4 AND P.monto <= 10000)
                    LEFT JOIN pag_procesos_estados PE1 ON (PE1.id = P.ultimo_estado_id)
                    LEFT JOIN pag_procesos_estados PE2 ON (PE2.id = P.ultimo_estado_id AND PE2.fecha_recepcion IS NULL)
                    LEFT JOIN pag_procesos_estados PE3 ON (PE3.id = P.ultimo_estado_id AND PE3.fecha_recepcion IS NOT NULL)
                    WHERE D.tipo_dependencia_id IN (2,4) AND D.sigla IS NOT NULL
                GROUP BY D.id, D.nombre
                ORDER BY D.nombre'
            );
            $mayores = $this->ServidoresPublico->Dependencia->query(
                'SELECT D.nombre, COUNT(PE1.id) AS may_total, COUNT(PE2.id) AS may_pendientes, COUNT(PE3.id) AS may_recibidos
                    FROM per_dependencias D
                    LEFT JOIN pag_procesos P ON (P.dependencia_id = D.id AND P.estado_id = 4 AND P.monto > 10000)
                    LEFT JOIN pag_procesos_estados PE1 ON (PE1.id = P.ultimo_estado_id)
                    LEFT JOIN pag_procesos_estados PE2 ON (PE2.id = P.ultimo_estado_id AND PE2.fecha_recepcion IS NULL)
                    LEFT JOIN pag_procesos_estados PE3 ON (PE3.id = P.ultimo_estado_id AND PE3.fecha_recepcion IS NOT NULL)
                    WHERE D.tipo_dependencia_id IN (2,4) AND D.sigla IS NOT NULL
                GROUP BY D.nombre
                ORDER BY D.nombre'
            );
            $dependencias = array(); 
            for ($i = 0; $i < count($menores); $i++) {
                $servidores = $this->ServidoresPublico->find('all', array( 
                    'fields' => array('nombres', 'apellidos', 'Usuario.id'),
                    'conditions' => array('ServidoresPublico.estado' => '1', 'Usuario.rol' => 'administradores', 'ServidoresPublico.dependencia_id' => $menores[$i][0]['id']),
                    'order' => array('ServidoresPublico.apellidos' => 'ASC', 'ServidoresPublico.nombres' => 'ASC'),
                    'recursive' => 0
                ));
                $dependencias[] = array_merge($menores[$i][0], $mayores[$i][0], array('ServidoresPublicos' => $servidores));
            }
            $this->set('dependencias', $dependencias);
        }
        
        /**
         * 
         */
        if (!empty($params['pdf'])) {
            $this->autoRender = false;
            App::import('Vendor', 'tcpdf/xtcpdf');

            // crea el documento PDF
            $pdf = new XTCPDF('L', 'mm', array(215, 330));

            // Modificar información del PDF
            $pdf->setTituloPDF('Reporte');

            // set font
            $pdf->SetFont('freesans', '', 12);
            
            $pdf->setAutoPageBreak(false);
            
            // add a page
            $pdf->AddPage();
            
            $pdf->SetY(14);
            $pdf->Image(WWW_ROOT . '/img/logo_gach_pdf.jpg');
            $pdf->SetY(16);
            $pdf->SetFont('freesans', 'B', 14);
            $pdf->SetTextColor(22, 102, 152);
            $pdf->Cell(310, 6, 'Gobierno Autónomo Departamental de Chuquisaca', 0, 0, 'C');
            $pdf->Ln();
            $pdf->SetFont('freesans', 'B', 12);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(310, 6, 'Sistema de Seguimiento de Proceso de Pago', 0, 0, 'C');
            $pdf->Ln();
            
            $meses = array(
                '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo',
                '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio',
                '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre',
                '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre',
            );
            
            $pdf->SetFont('freesans', 'B', 13);
            $pdf->SetTextColor(221, 0, 0);
            if ($params['rol'] == 'administradores') {
                $pdf->Cell(310, 6, 'Reporte de Procesos Pendientes por Administradores' , 0, 0, 'C');
            } else {
                $pdf->Cell(310, 6, 'Reporte de Procesos Pendientes por Analistas' , 0, 0, 'C');
            }
            
            $pdf->Ln();

            $pdf->SetTextColor(0, 0, 0);
            
            $pdf->SetFillColor(245, 245, 245);
            $pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
            $pdf->RoundedRect(266, 18, 54, 14, 2.50, '1111', 'DF');
            
            $pdf->SetY(20);
            $pdf->SetX(269);
            $pdf->SetFont('freesans', '', 8);
            $pdf->Cell(40, 6, 'Usuario: ' . $this->Auth->user('nick') , 0, 0, 'L');
            $pdf->Ln();
            $pdf->SetY(25);
            $pdf->SetX(269);
            $pdf->Cell(40, 6, 'Fecha de emisión: ' . date('d/m/Y H:i') , 0, 0, 'L');
            
            $pdf->SetY(40);
            if ($params['rol'] == 'analistas') {
                $pdf->SetFont('freesans', 'B', 8);
                $pdf->SetFillColor(190, 190, 190);
                $pdf->SetLineStyle(array('width' => 0.3, 'color' => array(145, 145, 145)));
                $pdf->MultiCell(90, 16, 'Funcionario', 1, 'C', true, 0, '', '', true, 0, false, true, 16, 'M');
                $pdf->MultiCell(100, 8, 'Montos menores o iguales a 10000', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(100, 8, 'Montos mayores a 10000', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(20, 16, 'Total', 1, 'C', true, 0, '', '', true, 0, false, true, 16, 'M');
                $pdf->Ln();
                $pdf->SetY(48);

                $pdf->SetFont('freesans', 'B', 7);
                $pdf->SetFillColor(215, 215, 215);
                $pdf->MultiCell(35, 8, 'Pendientes', 1, 'C', true, 0, 100, '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(35, 8, 'Recepcionados', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(30, 8, 'Total', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(35, 8, 'Pendientes', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(35, 8, 'Recepcionados', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(30, 8, 'Total', 1, 'C', true, 1, '', '', true, 0, false, true, 8, 'M');
                
                $total = array(
                    'men_pendientes' => 0, 'men_recibidos' => 0, 'men_total' => 0,
                    'may_pendientes' => 0, 'may_recibidos' => 0, 'may_total' => 0,
                    'total' => 0
                );
                for ($i = 0; $i < count($funcionarios); $i++) {
                    if ($pdf->getY() + 20 > $pdf->getPageHeight() - 15) {
                        $pdf->AddPage();
                        $pdf->SetY(15);
                    }
                    
                    $total_parcial = $menores[$i][0]['total'] + $mayores[$i][0]['total'];
                    $total['men_pendientes'] += $menores[$i][0]['pendientes'];
                    $total['men_recibidos'] += $menores[$i][0]['recibidos'];
                    $total['men_total'] += $menores[$i][0]['total'];
                    $total['may_pendientes'] += $mayores[$i][0]['pendientes'];
                    $total['may_recibidos'] += $mayores[$i][0]['recibidos'];
                    $total['may_total'] += $mayores[$i][0]['total'];

                    $total['total'] += $total_parcial;
                    
                    if ($i % 2) {
                        $pdf->SetFillColor(240, 240, 240);
                    } else {
                        $pdf->SetFillColor(255, 255, 255);
                    }

                    $pdf->SetFont('freesans', '', 7);
                    $pdf->MultiCell(90, 8, strtoupper($funcionarios[$i]['ServidoresPublico']['apellidos'] . ', ' . $funcionarios[$i]['ServidoresPublico']['nombres']), 1, 'L', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(35, 8, $menores[$i][0]['pendientes'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(35, 8, $menores[$i][0]['recibidos'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->SetFont('freesans', 'B', 7);
                    $pdf->MultiCell(30, 8, $menores[$i][0]['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->SetFont('freesans', '', 7);
                    $pdf->MultiCell(35, 8, $mayores[$i][0]['pendientes'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(35, 8, $mayores[$i][0]['recibidos'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->SetFont('freesans', 'B', 7);
                    $pdf->MultiCell(30, 8, $mayores[$i][0]['total'], 1, 'R', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->SetFont('freesans', 'B', 7);
                    $pdf->MultiCell(20, 8, $total_parcial, 1, 'R', true, 1, '', '', true, 0, false, true, 8, 'M');
                }
                $pdf->MultiCell(35, 8, $total['men_pendientes'], 1, 'R', false, 0, 100, '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(35, 8, $total['men_recibidos'], 1, 'R', false, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(30, 8, $total['men_total'], 1, 'R', false, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(35, 8, $total['may_pendientes'], 1, 'R', false, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(35, 8, $total['may_recibidos'], 1, 'R', false, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(30, 8, $total['may_total'], 1, 'R', false, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(20, 8, $total['total'], 1, 'R', false, 1, '', '', true, 0, false, true, 8, 'M');
            } elseif ($params['rol'] == 'administradores') {
                $pdf->SetFont('freesans', 'B', 8);
                $pdf->SetFillColor(190, 190, 190);
                $pdf->SetLineStyle(array('width' => 0.3, 'color' => array(145, 145, 145)));
                $pdf->MultiCell(65, 16, 'Dependencia', 1, 'C', true, 0, '', '', true, 0, false, true, 16, 'M');
                $pdf->MultiCell(65, 16, 'Funcionarios', 1, 'C', true, 0, '', '', true, 0, false, true, 16, 'M');
                $pdf->MultiCell(80, 8, 'Montos menores o iguales a 10000', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(80, 8, 'Montos mayores a 10000', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(20, 16, 'Total', 1, 'C', true, 0, '', '', true, 0, false, true, 16, 'M');
                $pdf->Ln();
                $pdf->SetY(48);

                $pdf->SetFont('freesans', 'B', 7);
                $pdf->SetFillColor(215, 215, 215);
                $pdf->MultiCell(30, 8, 'Pendientes', 1, 'C', true, 0, 140, '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(30, 8, 'Recepcionados', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(20, 8, 'Total', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(30, 8, 'Pendientes', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(30, 8, 'Recepcionados', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(20, 8, 'Total', 1, 'C', true, 1, '', '', true, 0, false, true, 8, 'M');
                
                $total = array(
                    'men_pendientes' => 0, 'men_recibidos' => 0, 'men_total' => 0,
                    'may_pendientes' => 0, 'may_recibidos' => 0, 'may_total' => 0,
                    'total' => 0
                );
                $i = 0;
                foreach ($dependencias as $dependencia) { 
                    if ($pdf->getY() + 20 > $pdf->getPageHeight() - 15) {
                        $pdf->AddPage();
                        $pdf->SetY(15);
                    }
                    
                    $i++;
                    $total_parcial = $dependencia['men_total'] + $dependencia['may_total'];
                    $total['men_pendientes'] += $dependencia['men_pendientes'];
                    $total['men_recibidos'] += $dependencia['men_recibidos'];
                    $total['men_total'] += $dependencia['men_total'];
                    $total['may_pendientes'] += $dependencia['may_pendientes'];
                    $total['may_recibidos'] += $dependencia['may_recibidos'];
                    $total['may_total'] += $dependencia['may_total'];
                    $total['total'] += $total_parcial;
                    
                    if ($i % 2) {
                        $pdf->SetFillColor(255, 255, 255);
                    } else {
                        $pdf->SetFillColor(240, 240, 240);
                    }
                    
                    $pdf->SetFont('freesans', '', 7);
                    $y = $pdf->GetY();
                    $h = count($dependencia['ServidoresPublicos']) * 8;
                    $pdf->MultiCell(65, $h, $dependencia['nombre'], 1, 'L', true, 0, '', '', true, 0, false, true, $h, 'M');
                    $x = $pdf->GetX();
                    foreach ($dependencia['ServidoresPublicos'] as $servidor) {
                        $pdf->SetX($x);
                        $pdf->MultiCell(65, 8, strtoupper($servidor['ServidoresPublico']['apellidos'] . ', ' . $servidor['ServidoresPublico']['nombres']), 1, 'L', true, 1, '', '', true, 0, false, true, 8, 'M');
                    }
                    $pdf->MultiCell(30, $h, $dependencia['men_pendientes'], 1, 'R', true, 0, $x + 65, $y, true, 0, false, true, $h, 'M');
                    $pdf->MultiCell(30, $h, $dependencia['men_recibidos'], 1, 'R', true, 0, '', '', true, 0, false, true, $h, 'M');
                    $pdf->SetFont('freesans', 'B', 7);
                    $pdf->MultiCell(20, $h, $dependencia['men_total'], 1, 'R', true, 0, '', '', true, 0, false, true, $h, 'M');
                    $pdf->SetFont('freesans', '', 7);
                    $pdf->MultiCell(30, $h, $dependencia['may_pendientes'], 1, 'R', true, 0, '', '', true, 0, false, true, $h, 'M');
                    $pdf->MultiCell(30, $h, $dependencia['may_recibidos'], 1, 'R', true, 0, '', '', true, 0, false, true, $h, 'M');
                    $pdf->SetFont('freesans', 'B', 7);
                    $pdf->MultiCell(20, $h, $dependencia['may_total'], 1, 'R', true, 0, '', '', true, 0, false, true, $h, 'M');
                    $pdf->MultiCell(20, $h, $total_parcial, 1, 'R', true, 1, '', '', true, 0, false, true, $h, 'M');
                }
                $pdf->SetFont('freesans', 'B', 7);
                $pdf->MultiCell(30, 8, $total['men_pendientes'], 1, 'R', false, 0, $x + 65, '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(30, 8, $total['men_recibidos'], 1, 'R', false, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(20, 8, $total['men_total'], 1, 'R', false, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(30, 8, $total['may_pendientes'], 1, 'R', false, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(30, 8, $total['may_recibidos'], 1, 'R', false, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(20, 8, $total['may_total'], 1, 'R', false, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(20, 8, $total['total'], 1, 'R', false, 1, '', '', true, 0, false, true, 8, 'M');
            }
            
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

            //Close and output PDF document
            if ($params['pdf'] == 'descargar') {
                $pdf->Output('reporte_funcionarios_por_' . $params['rol'] . '.pdf', 'D');
            } elseif ($params['pdf'] == 'imprimir') { 
                $pdf->Output('reporte_funcionarios_por_' . $params['rol'] . '.pdf', 'I');
            }
        }
        /***/
        
        $this->set(compact('funcionarios', 'menores', 'mayores'));
    }
    
    /**
     * analistas method
     *
     * @return void
     */
    public function analistas() {
        $params = $this->params['named'];
        $params['monto'] = empty($params['monto']) ? '' : $params['monto'];
        $params['estado'] = empty($params['estado']) ? '' : $params['estado'];
//        $params['observaciones'] = empty($params['observaciones']) ? '' : $params['observaciones'];
        $params['fecha_ini'] = empty($params['fecha_ini']) ? date('Y-m-d') : $params['fecha_ini'];
        $params['fecha_fin'] = empty($params['fecha_fin']) ? date('Y-m-d') : $params['fecha_fin'];
        $params['pdf'] = empty($params['pdf']) ? '' : $params['pdf'];
        $params['page'] = empty($params['page']) ? 1 : $params['page'];
        $params['controller'] = $this->params['controller'];
        $this->set('params', $params);

        if (!empty($params['estado'])) {
            $estado = '';
            $fecha = '';
            switch ($params['estado']) {
                case 'enviados':
                    $fecha = 'ProcesosEstado.fecha_envio >= \'' . $params['fecha_ini'] . ' 00:00:00\' AND ProcesosEstado.fecha_envio <= \'' . $params['fecha_fin'] . ' 23:59:59\'';
                    $estado = 'ProcesosEstado.estado_id IN (4, 5) AND ProcesosEstado.usuario_envio_id = ' . $this->Auth->user('id');
                    break;
                case 'recibidos':
                    $fecha = 'ProcesosEstado.fecha_recepcion >= \'' . $params['fecha_ini'] . ' 00:00:00\' AND ProcesosEstado.fecha_recepcion <= \'' . $params['fecha_fin'] . ' 23:59:59\'';
                    $estado = 'ProcesosEstado.estado_id = 3 AND ProcesosEstado.usuario_recepcion_id = ' . $this->Auth->user('id');
                    break;
            }

            switch ($params['monto']) {
                case 'menores':
                    $monto = '"Proceso"."monto" <= 10000';
                    break;
                case 'mayores':
                    $monto = '"Proceso"."monto" > 10000';
                    break;
                default:
                    $monto = '';
            }

            if (empty($params['pdf'])) {
                $this->Proceso->recursive = 0;
                $this->paginate = array(
                    'Proceso' => array(
                        'conditions' => array(
                            $monto,
                            'Proceso.usuario_analista_id' => $this->Auth->user('id')
                        ),
                        'fields' => array(
                            'id', 'cite', 'nro_proceso', 
                            'beneficiario_documento', 'beneficiario_nombre', 'monto', 
                            'Motivo.id', 'Motivo.nombre', 
                            'Dependencia.id', 'Dependencia.sigla',
                            'COUNT(ProcesosEstado.id) AS total'
                        ),
                        'order' => array('Proceso.nro_proceso' => 'asc'),
                        'joins' => array(
                            array(
                                'table' => 'pag_procesos_estados',
                                'alias' => 'ProcesosEstado',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'ProcesosEstado.proceso_id = Proceso.id',
                                    $fecha, 
                                    $estado
                                )
                            )
                        ),
                        'group' => 'Proceso.id, Proceso.cite, Proceso.nro_proceso, 
                            Proceso.beneficiario_documento, Proceso.beneficiario_nombre, 
                            Proceso.monto, Motivo.id, Motivo.nombre, Dependencia.id, Dependencia.sigla'
                    )
                );
                $procesos = $this->paginate('Proceso');
            } else {
                $procesos = $this->Proceso->find('all', array(
                    'conditions' => array(
                        $monto,
                        'Proceso.usuario_analista_id' => $this->Auth->user('id')
                    ),
                    'fields' => array(
                        'id', 'cite', 'nro_proceso', 
                        'beneficiario_documento', 'beneficiario_nombre', 'monto', 
                        'Motivo.id', 'Motivo.nombre', 
                        'Dependencia.id', 'Dependencia.sigla',
                        'COUNT(ProcesosEstado.id) AS total'
                    ),
                    'order' => array('Proceso.nro_proceso' => 'asc'),
                    'joins' => array(
                        array(
                            'table' => 'pag_procesos_estados',
                            'alias' => 'ProcesosEstado',
                            'type' => 'INNER',
                            'conditions' => array(
                                'ProcesosEstado.proceso_id = Proceso.id',
                                $fecha, 
                                $estado
                            )
                        )
                    ),
                    'group' => 'Proceso.id, Proceso.cite, Proceso.nro_proceso, 
                        Proceso.beneficiario_documento, Proceso.beneficiario_nombre, 
                        Proceso.monto, Motivo.id, Motivo.nombre, Dependencia.id, Dependencia.sigla',
                    'recursive' => 0
                ));
            }
        } else {
            $procesos = 0;
        }
        
        /**
         * 
         */
        if (!empty($params['pdf'])) {
            App::import('Vendor', 'tcpdf/xtcpdf');

            // crea el documento PDF
            $pdf = new XTCPDF('P', 'mm', array(215, 279));

            // Modificar información del PDF
            $pdf->setTituloPDF('Reporte');

            // set font
            $pdf->SetFont('freesans', '', 12);
            
            $pdf->setAutoPageBreak(false);
            
            // add a page
            $pdf->AddPage();
            $pdf->SetY(14);
            $pdf->Image(WWW_ROOT . '/img/logo_gach_pdf.jpg');
            $pdf->SetY(16);
            $pdf->SetFont('freesans', 'B', 14);
            $pdf->SetTextColor(22, 102, 152);
            $pdf->Cell(185, 6, 'Gobierno Autónomo Departamental de Chuquisaca', 0, 0, 'C');
            $pdf->Ln();
            $pdf->SetFont('freesans', 'B', 12);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(185, 6, 'Sistema de Seguimiento de Proceso de Pago', 0, 0, 'C');
            $pdf->Ln();
            $pdf->SetFont('freesans', 'B', 12);
            $pdf->SetTextColor(221, 0, 0);
            if ($params['estado'] == 'recibidos') {
                $pdf->Cell(185, 6, 'Reporte de Procesos Recibidos', 0, 0, 'C');
                $pdf->Ln();
                $pdf->SetFont('freesans', '', 9);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell(185, 4, 'De ' . $this->fecha($params['fecha_ini']) . ' a ' . $this->fecha($params['fecha_fin']), 0, 0, 'C');
            } else {
                $pdf->Cell(185, 6, 'Reporte de Procesos Enviados' , 0, 0, 'C');
            }
            
            
            $pdf->Ln();

            $pdf->SetTextColor(0, 0, 0);
            
            $pdf->SetFillColor(245, 245, 245);
            $pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
            $pdf->RoundedRect(168, 19, 32, 16, 2.50, '1111', 'DF');
            
            $pdf->SetY(20);
            $pdf->SetX(169);
            $pdf->SetFont('freesans', 'B', 8);
            $pdf->Cell(30, 3, 'Usuario: ', 0, 0, 'L');
            $pdf->Ln();
            $pdf->SetX(169);
            $pdf->SetFont('freesans', '', 8);
            $pdf->Cell(30, 3, $this->Auth->user('nick') , 0, 0, 'C');
            $pdf->Ln();
            $pdf->SetX(169);
            $pdf->SetFont('freesans', 'B', 8);
            $pdf->Cell(30, 3, 'Fecha de emisión: ', 0, 0, 'L');
            $pdf->Ln();
            $pdf->SetX(169);
            $pdf->SetFont('freesans', '', 8);
            $pdf->Cell(30, 3, date('d/m/Y H:i') , 0, 0, 'C');
            $pdf->Ln();
            
            $pdf->SetY(40);
            
            $pdf->SetFont('freesans', 'B', 8);
            $pdf->SetFillColor(190, 190, 190);
            $pdf->SetLineStyle(array('width' => 0.3, 'color' => array(145, 145, 145)));
            $pdf->MultiCell(14, 8, 'Nro', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(40, 8, 'Cite', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(26, 8, 'Dependencia', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(40, 8, 'Motivo', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(45, 8, 'Beneficiario', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(20, 8, 'Monto', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->Ln();

            $pdf->SetFont('freesans', '', 7);
            $i = 0;
            foreach ($procesos as $proceso) {
                $y_1 = ceil($pdf->getStringHeight(40, $proceso['Proceso']['cite'], $reseth = false, $autopadding = true, $cellpadding = '', $border = 1));
                $y_2 = ceil($pdf->getStringHeight(40, $proceso['Motivo']['nombre'], $reseth = false, $autopadding = true, $cellpadding = '', $border = 1));
                $y_3 = ceil($pdf->getStringHeight(45, $proceso['Proceso']['beneficiario_nombre'], $reseth = false, $autopadding = true, $cellpadding = '', $border = 1));
                $y = max($y_1, $y_2, $y_3) + 1;
                
                if ($pdf->getY() + $y > $pdf->getPageHeight() - 18) {
                    $pdf->AddPage();
                    $pdf->SetY(16);
                    $pdf->SetFont('freesans', 'B', 7);
                    $pdf->SetFillColor(190, 190, 190);
                    $pdf->SetLineStyle(array('width' => 0.3, 'color' => array(145, 145, 145)));
                    $pdf->MultiCell(14, 8, 'Nro', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(40, 8, 'Cite', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(26, 8, 'Dependencia', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(40, 8, 'Motivo', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(45, 8, 'Beneficiario', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, 'Monto', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->Ln();
                }

                $pdf->SetFont('freesans', '', 7);
                
                $i++;
                if ($i % 2) {
                    $pdf->SetFillColor(255, 255, 255);
                } else {
                    $pdf->SetFillColor(240, 240, 240);
                }
                $pdf->MultiCell(14, 7, $proceso['Proceso']['nro_proceso'], 1, 'C', true, 0, '', '', true, 0, false, true, 7, 'M');
                $pdf->MultiCell(40, 7, $proceso['Proceso']['cite'], 1, 'C', true, 0, '', '', true, 0, false, true, 7, 'M');
                $pdf->MultiCell(26, 7, $proceso['Dependencia']['sigla'], 1, 'C', true, 0, '', '', true, 0, false, true, 7, 'M');
                $pdf->MultiCell(40, 7, $proceso['Motivo']['nombre'], 1, 'C', true, 0, '', '', true, 0, false, true, 7, 'M');
                $pdf->MultiCell(45, 7, $proceso['Proceso']['beneficiario_nombre'], 1, 'C', true, 0, '', '', true, 0, false, true, 7, 'M');
                $pdf->MultiCell(20, 7, $proceso['Proceso']['monto'], 1, 'C', true, 0, '', '', true, 0, false, true, 7, 'M');
                $pdf->Ln();
            }
            
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

            //Close and output PDF document
            if ($params['pdf'] == 'descargar') {
                $pdf->Output('reporte.pdf', 'D');
            } elseif ($params['pdf'] == 'imprimir') {
                $pdf->Output('reporte.pdf', 'I');
            }
        }
        /***/
        
        $this->set('procesos', $procesos);
    }
    
    /**
     * finanzas method
     *
     * @return void
     */
    public function finanzas() {
        $params = $this->params['named'];
        $params['monto'] = empty($params['monto']) ? '' : $params['monto'];
        $params['estado'] = empty($params['estado']) ? '1' : $params['estado'];
        $params['tipo'] = empty($params['tipo']) ? 'recepcionados' : $params['tipo'];
        $params['fecha_ini'] = empty($params['fecha_ini']) ? date('Y-m-d') : $params['fecha_ini'];
        $params['fecha_fin'] = empty($params['fecha_fin']) ? date('Y-m-d') : $params['fecha_fin'];
        $params['pdf'] = empty($params['pdf']) ? '' : $params['pdf'];
        $params['page'] = empty($params['page']) ? 1 : $params['page'];
        $params['controller'] = $this->params['controller'];
        $this->set('params', $params);

        if (!empty($params['estado'])) {
            $estado = '';
            $fecha = '';
            switch ($params['estado']) {
                case '1':
                    $estado = 'ProcesosEstado.estado_id = 1 AND ProcesosEstado.usuario_recepcion_id = ' . $this->Auth->user('id');
                    break;
                case '9':
                    $estado = 'ProcesosEstado.estado_id = 9 AND ProcesosEstado.usuario_recepcion_id = ' . $this->Auth->user('id');
                    break;
                case '11':
                    $estado = 'ProcesosEstado.estado_id = 11 AND ProcesosEstado.usuario_recepcion_id = ' . $this->Auth->user('id');
                    break;
            }
            switch ($params['tipo']) {
                case 'recepcionados':
                    $fecha = 'ProcesosEstado.fecha_recepcion >= \'' . $params['fecha_ini'] . ' 00:00:00\' AND ProcesosEstado.fecha_recepcion <= \'' . $params['fecha_fin'] . ' 23:59:59\'';
                    break;
                case 'enviados':
                    $fecha = 'ProcesosEstado.fecha_envio >= \'' . $params['fecha_ini'] . ' 00:00:00\' AND ProcesosEstado.fecha_envio <= \'' . $params['fecha_fin'] . ' 23:59:59\'';
                    break;
            }

            switch ($params['monto']) {
                case 'menores':
                    $monto = '"Proceso"."monto" <= 10000';
                    break;
                case 'mayores':
                    $monto = '"Proceso"."monto" > 10000';
                    break;
                default:
                    $monto = '';
            }

            if (empty($params['pdf'])) {
                $this->Proceso->recursive = 0;
                $this->paginate = array(
                    'Proceso' => array(
                        'conditions' => array(
                            $monto
                        ),
                        'fields' => array(
                            'id', 'cite', 'nro_proceso',
                            'beneficiario_documento', 'beneficiario_nombre', 'monto', 
                            'Motivo.id', 'Motivo.nombre', 
                            'Dependencia.id', 'Dependencia.sigla',
                            'COUNT(ProcesosEstado.id) AS total'
                        ),
                        'order' => array('Proceso.nro_proceso' => 'asc'),
                        'joins' => array(
                            array(
                                'table' => 'pag_procesos_estados',
                                'alias' => 'ProcesosEstado',
                                'type' => 'INNER',
                                'conditions' => array(
                                    'ProcesosEstado.proceso_id = Proceso.id',
                                    $fecha, 
                                    $estado
                                )
                            )
                        ),
                        'group' => 'Proceso.id, Proceso.cite, Proceso.nro_proceso, 
                            Proceso.beneficiario_documento, Proceso.beneficiario_nombre, 
                            Proceso.monto, Proceso.fecha_emision, Proceso.nro_preventivo, Proceso.referencia, 
                            Motivo.id, Motivo.nombre, Dependencia.id, Dependencia.sigla'
                    )
                );
                $procesos = $this->paginate('Proceso');
            } else {
                $procesos = $this->Proceso->find('all', array(
                    'conditions' => array(
                        $monto,
                    ),
                    'fields' => array(
                        'id', 'cite', 'nro_proceso', 'fecha_emision', 'nro_preventivo', 'referencia',
                        'beneficiario_documento', 'beneficiario_nombre', 'monto', 
                        'Motivo.id', 'Motivo.nombre', 
                        'Dependencia.id', 'Dependencia.sigla',
                        'COUNT(ProcesosEstado.id) AS total'
                    ),
                    'order' => array('Proceso.nro_proceso' => 'asc'),
                    'joins' => array(
                        array(
                            'table' => 'pag_procesos_estados',
                            'alias' => 'ProcesosEstado',
                            'type' => 'INNER',
                            'conditions' => array(
                                'ProcesosEstado.proceso_id = Proceso.id',
                                $fecha, 
                                $estado
                            )
                        )
                    ),
                    'group' => 'Proceso.id, Proceso.cite, Proceso.nro_proceso, 
                        Proceso.beneficiario_documento, Proceso.beneficiario_nombre, 
                        Proceso.monto, Proceso.fecha_emision, Proceso.nro_preventivo, Proceso.referencia, 
                        Motivo.id, Motivo.nombre, Dependencia.id, Dependencia.sigla',
                    'recursive' => 0
                ));
            }
        } else {
            $procesos = 0;
        }
        
        /**
         * 
         */
        if (!empty($params['pdf'])) {
            set_time_limit(3600);
            App::import('Vendor', 'tcpdf/xtcpdf');

            // crea el documento PDF
            if ($params['pdf'] == 'imprimir') {
                $pdf = new XTCPDF('L', 'mm', array(215, 330));
            } else {
                $pdf = new XTCPDF('P', 'mm', array(330, 215));
            }
            
            // Modificar información del PDF
            $pdf->setTituloPDF('Reporte');

            // set font
            $pdf->SetFont('freesans', '', 12);
            
            $pdf->setAutoPageBreak(false);
            
            // add a page
            $pdf->AddPage();
            $pdf->SetY(14);
            $pdf->Image(WWW_ROOT . '/img/logo_gach_pdf.jpg');
            $pdf->SetY(16);
            $pdf->SetFont('freesans', 'B', 14);
            $pdf->SetTextColor(22, 102, 152);
            $pdf->Cell('', 6, 'Gobierno Autónomo Departamental de Chuquisaca', 0, 0, 'C');
            $pdf->Ln();
            $pdf->SetFont('freesans', 'B', 12);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell('', 6, 'Sistema de Seguimiento de Proceso de Pago', 0, 0, 'C');
            $pdf->Ln();
            $pdf->SetFont('freesans', 'B', 12);
            $pdf->SetTextColor(221, 0, 0);
            if ($params['tipo'] == 'recepcionados') {
                $pdf->Cell('', 6, 'Reporte de Procesos Recepcionados', 0, 0, 'C');
                $pdf->Ln();
                $pdf->SetFont('freesans', '', 9);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Cell('', 4, 'De ' . $this->fecha($params['fecha_ini']) . ' a ' . $this->fecha($params['fecha_fin']), 0, 0, 'C');
            } else {
                $pdf->Cell('', 6, 'Reporte de Procesos Enviados' , 0, 0, 'C');
            }
            
            $pdf->Ln();

            $pdf->SetTextColor(0, 0, 0);
            
            $pdf->SetFillColor(245, 245, 245);
            $pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
            if ($params['pdf'] == 'imprimir') {
                $pdf->RoundedRect(266, 18, 54, 14, 2.50, '1111', 'DF');

                $pdf->SetY(20);
                $pdf->SetX(269);
                $pdf->SetFont('freesans', '', 8);
                $pdf->Cell(40, 6, 'Usuario: ' . $this->Auth->user('nick') , 0, 0, 'L');
                $pdf->Ln();
                $pdf->SetY(25);
                $pdf->SetX(269);
                $pdf->Cell(40, 6, 'Fecha de emisión: ' . date('d/m/Y H:i') , 0, 0, 'L');
            } else {
                $pdf->RoundedRect(165, 18, 35, 18, 2.50, '1111', 'DF');

                $pdf->SetY(19);
                $pdf->SetX(167);
                $pdf->SetFont('freesans', '', 8);
                $pdf->Cell(31, 4, 'Usuario:', 0, 1, 'L');
                $pdf->SetX(167);
                $pdf->Cell(31, 4, $this->Auth->user('nick') , 0, 1, 'C');
                $pdf->SetX(167);
                $pdf->Cell(31, 4, 'Fecha de emisión:', 0, 1, 'L');
                $pdf->SetX(167);
                $pdf->Cell(31, 4, date('d/m/Y H:i') , 0, 1, 'C');
            }
            $pdf->SetY(40);
            $pdf->SetFillColor(190, 190, 190);
            $pdf->SetLineStyle(array('width' => 0.3, 'color' => array(145, 145, 145)));
                
            if ($params['pdf'] == 'imprimir') {
                $pdf->SetFont('freesans', 'B', 8);
                $pdf->MultiCell(16, 8, 'Fecha', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(15, 8, 'Nro', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(20, 8, 'Preventivo', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(26, 8, 'Dependencia', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(37, 8, 'Motivo', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(38, 8, 'Beneficiario', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(38, 8, 'Referencia', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(18, 8, 'Monto', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(34, 4, 'Contabilidad', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                $pdf->MultiCell(34, 4, 'Tesoreria', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                $pdf->MultiCell(34, 4, 'Caja', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                $pdf->Ln();
                $pdf->MultiCell(17, 4, 'Sello', 1, 'C', true, 0, 218, '', true, 0, false, true, 4, 'M');
                $pdf->MultiCell(17, 4, 'Firma', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                $pdf->MultiCell(17, 4, 'Sello', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                $pdf->MultiCell(17, 4, 'Firma', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                $pdf->MultiCell(17, 4, 'Sello', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                $pdf->MultiCell(17, 4, 'Firma', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                $pdf->Ln();
                $pdf->SetFont('freesans', '', 7);
            } else {
                $pdf->SetFont('freesans', 'B', 7);
                $pdf->MultiCell(15, 8, 'Fecha', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(15, 8, 'Nro', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(20, 8, 'Preventivo', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(20, 8, 'Dependencia', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(30, 8, 'Motivo', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(35, 8, 'Beneficiario', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(35, 8, 'Referencia', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(15, 8, 'Monto', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->Ln();
                $pdf->SetFont('freesans', '', 6);
            }
            $i = 0;
            foreach ($procesos as $proceso) {
                $y = 20;
                if ($pdf->getY() + $y > $pdf->getPageHeight() - 15) {
                    $pdf->AddPage();
                    $pdf->SetY(15);
                    $pdf->SetFillColor(190, 190, 190);
                    $pdf->SetLineStyle(array('width' => 0.3, 'color' => array(145, 145, 145)));
                    
                    if ($params['pdf'] == 'imprimir') {
                        $pdf->SetFont('freesans', 'B', 7);
                        $pdf->MultiCell(16, 8, 'Fecha', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                        $pdf->MultiCell(15, 8, 'Nro', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                        $pdf->MultiCell(20, 8, 'Preventivo', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                        $pdf->MultiCell(26, 8, 'Dependencia', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                        $pdf->MultiCell(37, 8, 'Motivo', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                        $pdf->MultiCell(38, 8, 'Beneficiario', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                        $pdf->MultiCell(38, 8, 'Referencia', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                        $pdf->MultiCell(18, 8, 'Monto', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                        $pdf->MultiCell(34, 4, 'Contabilidad', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                        $pdf->MultiCell(34, 4, 'Tesoreria', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                        $pdf->MultiCell(34, 4, 'Caja', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                        $pdf->Ln();
                        $pdf->MultiCell(17, 4, 'Sello', 1, 'C', true, 0, 218, '', true, 0, false, true, 4, 'M');
                        $pdf->MultiCell(17, 4, 'Firma', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                        $pdf->MultiCell(17, 4, 'Sello', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                        $pdf->MultiCell(17, 4, 'Firma', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                        $pdf->MultiCell(17, 4, 'Sello', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                        $pdf->MultiCell(17, 4, 'Firma', 1, 'C', true, 0, '', '', true, 0, false, true, 4, 'M');
                        $pdf->Ln();
                        $pdf->SetFont('freesans', '', 7);
                    } else {
                        $pdf->SetFont('freesans', 'B', 7);
                        $pdf->MultiCell(15, 8, 'Fecha', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                        $pdf->MultiCell(15, 8, 'Nro', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                        $pdf->MultiCell(20, 8, 'Preventivo', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                        $pdf->MultiCell(20, 8, 'Dependencia', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                        $pdf->MultiCell(30, 8, 'Motivo', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                        $pdf->MultiCell(35, 8, 'Beneficiario', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                        $pdf->MultiCell(35, 8, 'Referencia', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                        $pdf->MultiCell(15, 8, 'Monto', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                        $pdf->Ln();
                        $pdf->SetFont('freesans', '', 6);
                    }
                }
                
                $i++;
                if ($i % 2) {
                    $pdf->SetFillColor(255, 255, 255);
                } else {
                    $pdf->SetFillColor(240, 240, 240);
                }
                if ($params['pdf'] == 'imprimir') {
                    $pdf->MultiCell(16, $y, $proceso['Proceso']['fecha_emision'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                    $pdf->MultiCell(15, $y, $proceso['Proceso']['nro_proceso'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                    $pdf->MultiCell(20, $y, $proceso['Proceso']['nro_preventivo'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                    $pdf->MultiCell(26, $y, $proceso['Dependencia']['sigla'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                    $pdf->MultiCell(37, $y, $proceso['Motivo']['nombre'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                    $pdf->MultiCell(38, $y, $proceso['Proceso']['beneficiario_nombre'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                    $pdf->MultiCell(38, $y, $proceso['Proceso']['referencia'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                    $pdf->MultiCell(18, $y, $proceso['Proceso']['monto'], 1, 'R', true, 0, '', '', true, 0, false, true, $y, 'M');
                    $pdf->MultiCell(34, $y, '', 1, 'C', true, 0, '', '', true, 0, false, true, $y);
                    $pdf->MultiCell(34, $y, '', 1, 'C', true, 0, '', '', true, 0, false, true, $y);
                    $pdf->MultiCell(34, $y, '', 1, 'C', true, 1, '', '', true, 0, false, true, $y);
                } else {
                    $y_motivo = ceil($pdf->getStringHeight(30, $proceso['Motivo']['nombre'], $reseth = false, $autopadding = true, $cellpadding = '', $border = 1));
                    $y_beneficiario = ceil($pdf->getStringHeight(35, $proceso['Proceso']['beneficiario_nombre'], $reseth = false, $autopadding = true, $cellpadding = '', $border = 1));
                    $y_referencia = ceil($pdf->getStringHeight(35, $proceso['Proceso']['referencia'], $reseth = false, $autopadding = true, $cellpadding = '', $border = 1));
                    $y = max($y_motivo, $y_beneficiario, $y_referencia) + 1;
                    $pdf->MultiCell(15, $y, $proceso['Proceso']['fecha_emision'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                    $pdf->MultiCell(15, $y, $proceso['Proceso']['nro_proceso'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                    $pdf->MultiCell(20, $y, $proceso['Proceso']['nro_preventivo'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                    $pdf->MultiCell(20, $y, $proceso['Dependencia']['sigla'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                    $pdf->MultiCell(30, $y, $proceso['Motivo']['nombre'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                    $pdf->MultiCell(35, $y, $proceso['Proceso']['beneficiario_nombre'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                    $pdf->MultiCell(35, $y, $proceso['Proceso']['referencia'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                    $pdf->MultiCell(15, $y, $proceso['Proceso']['monto'], 1, 'R', true, 1, '', '', true, 0, false, true, $y, 'M');
                }
            }
            
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

            //Close and output PDF document
            if (in_array($params['pdf'], array('descargar', 'descargar_historico'))) {
                $pdf->Output('reporte_finanzas_' . date('Y-m-d') . '.pdf', 'D');
            } elseif (in_array($params['pdf'], array('imprimir', 'imprimir_historico'))) {
                $pdf->Output('reporte_finanzas_' . date('Y-m-d') . '.pdf', 'I');
            }
        }
        /***/
        
        $this->set('procesos', $procesos);
    }
    
    /**
     * finanzas method
     *
     * @return void
     */
    public function caja() {
        $params = $this->params['named'];
        $params['monto'] = empty($params['monto']) ? 'menores' : $params['monto'];
        $params['estado'] = empty($params['estado']) ? 'pendientes' : $params['estado'];
        $params['fecha_ini'] = empty($params['fecha_ini']) ? '' : $params['fecha_ini'];
        $params['fecha_fin'] = empty($params['fecha_fin']) ? '' : $params['fecha_fin'];
        $params['pdf'] = empty($params['pdf']) ? '' : $params['pdf'];
        $params['page'] = empty($params['page']) ? 1 : $params['page'];
        $params['controller'] = $this->params['controller'];
        $this->set('params', $params);

        $estado = 'ProcesosEstado.estado_id = 12';
        $fecha = '';

        switch ($params['estado']) {
            case 'pendientes':
                $fecha = 'ProcesosEstado.fecha_recepcion IS NULL ';
                if (!empty($params['fecha_ini'])) {
                    $fecha .= ' AND ProcesosEstado.fecha_envio >= \'' . $params['fecha_ini'] . ' 00:00:00\'';
                }
                if (!empty($params['fecha_fin'])) {
                    $fecha .= ' AND ProcesosEstado.fecha_envio <= \'' . $params['fecha_fin'] . ' 23:59:59\'';
                }
                break;
            case 'recepcionados':
                $fecha = 'ProcesosEstado.fecha_recepcion IS NOT NULL ';
                if (!empty($params['fecha_ini'])) {
                    $fecha .= ' AND ProcesosEstado.fecha_recepcion >= \'' . $params['fecha_ini'] . ' 00:00:00\'';
                }
                if (!empty($params['fecha_fin'])) {
                    $fecha .= ' AND ProcesosEstado.fecha_recepcion <= \'' . $params['fecha_fin'] . ' 23:59:59\'';
                }
                break;
        }

        switch ($params['monto']) {
            case 'menores':
                $monto = '"Proceso"."monto" <= 10000';
                break;
            case 'mayores':
                $monto = '"Proceso"."monto" > 10000';
                break;
            default:
                $monto = '';
        }

        if (empty($params['pdf'])) {
            $this->Proceso->recursive = 0;
            $this->paginate = array(
                'Proceso' => array(
                    'conditions' => array(
                        $monto,
                        $estado
                    ),
                    'fields' => array(
                        'id', 'cite', 'nro_proceso',
                        'beneficiario_documento', 'beneficiario_nombre', 'monto', 
                        'Motivo.id', 'Motivo.nombre', 
                        'Dependencia.id', 'Dependencia.sigla',
                        'ProcesosEstado.fecha_envio', 'ProcesosEstado.fecha_recepcion'
                    ),
                    'order' => array('Proceso.nro_proceso' => 'asc'),
                    'joins' => array(
                        array(
                            'table' => 'pag_procesos_estados',
                            'alias' => 'ProcesosEstado',
                            'type' => 'INNER',
                            'conditions' => array(
                                'ProcesosEstado.proceso_id = Proceso.id',
                                'ProcesosEstado.estado_id = Proceso.estado_id',
                                'ProcesosEstado.id = Proceso.ultimo_estado_id',
                                $fecha
                            )
                        )
                    )
                )
            );
            $procesos = $this->paginate('Proceso');
        } else {
            $procesos = $this->Proceso->find('all', array(
                'conditions' => array(
                    $monto,
                ),
                'fields' => array(
                    'id', 'cite', 'nro_proceso', 'fecha_emision', 'nro_preventivo', 'referencia',
                    'beneficiario_documento', 'beneficiario_nombre', 'monto', 
                    'Motivo.id', 'Motivo.nombre', 
                    'Dependencia.id', 'Dependencia.sigla',
                    'COUNT(ProcesosEstado.id) AS total'
                ),
                'order' => array('Proceso.nro_proceso' => 'asc'),
                'joins' => array(
                    array(
                        'table' => 'pag_procesos_estados',
                        'alias' => 'ProcesosEstado',
                        'type' => 'INNER',
                        'conditions' => array(
                            'ProcesosEstado.proceso_id = Proceso.id',
                            $fecha, 
                            $estado
                        )
                    )
                ),
                'group' => 'Proceso.id, Proceso.cite, Proceso.nro_proceso, 
                    Proceso.beneficiario_documento, Proceso.beneficiario_nombre, 
                    Proceso.monto, Proceso.fecha_emision, Proceso.nro_preventivo, Proceso.referencia, 
                    Motivo.id, Motivo.nombre, Dependencia.id, Dependencia.sigla',
                'recursive' => 0
            ));
        }
        
        /**
         * 
         */
        if (!empty($params['pdf'])) {
            App::import('Vendor', 'tcpdf/xtcpdf');

            // crea el documento PDF
            $pdf = new XTCPDF('P', 'mm', array(215, 330));

            // Modificar información del PDF
            $pdf->setTituloPDF('Reporte');

            // set font
            $pdf->SetFont('freesans', '', 12);
            
            $pdf->setAutoPageBreak(false);
            
            // add a page
            $pdf->AddPage();
            $pdf->SetY(14);
            $pdf->Image(WWW_ROOT . '/img/logo_gach_pdf.jpg');
            $pdf->SetY(16);
            $pdf->SetFont('freesans', 'B', 14);
            $pdf->SetTextColor(22, 102, 152);
            $pdf->Cell('', 6, 'Gobierno Autónomo Departamental de Chuquisaca', 0, 0, 'C');
            $pdf->Ln();
            $pdf->SetFont('freesans', 'B', 12);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell('', 6, 'Sistema de Seguimiento de Proceso de Pago', 0, 0, 'C');
            $pdf->Ln();
            $pdf->SetFont('freesans', 'B', 12);
            $pdf->SetTextColor(221, 0, 0);
            if ($params['estado'] == 'pendientes') {
                $pdf->Cell('', 6, 'Reporte de Procesos Pendientes', 0, 0, 'C');
            } else {
                $pdf->Cell('', 6, 'Reporte de Procesos Recepcionados' , 0, 0, 'C');
            }
            
            $pdf->Ln();

            $pdf->SetTextColor(0, 0, 0);
            
            $pdf->SetFillColor(245, 245, 245);
            $pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
            $pdf->RoundedRect(156, 18, 44, 14, 2.50, '1111', 'DF');
            
            $pdf->SetY(20);
            $pdf->SetX(157);
            $pdf->SetFont('freesans', '', 7);
            $pdf->Cell(40, 6, 'Usuario: ' . $this->Auth->user('nick') , 0, 0, 'L');
            $pdf->Ln();
            $pdf->SetY(25);
            $pdf->SetX(157);
            $pdf->Cell(40, 6, 'Fecha de emisión: ' . date('d/m/Y H:i') , 0, 0, 'L');
            
            $pdf->SetY(40);
            
            $pdf->SetFont('freesans', 'B', 8);
            $pdf->SetFillColor(190, 190, 190);
            $pdf->SetLineStyle(array('width' => 0.3, 'color' => array(145, 145, 145)));
            $pdf->MultiCell(17, 8, 'Nro', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(18, 8, 'Preventivo', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(25, 8, 'Dependencia', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(35, 8, 'Motivo', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(35, 8, 'Beneficiario', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(35, 8, 'Referencia', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(20, 8, 'Monto', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->Ln();
            $pdf->SetFont('freesans', '', 7);
            $i = 0;
            foreach ($procesos as $proceso) {
                $y_motivo = ceil($pdf->getStringHeight(35, $proceso['Motivo']['nombre'], $reseth = false, $autopadding = true, $cellpadding = '', $border = 1));
                $y_beneficiario = ceil($pdf->getStringHeight(35, $proceso['Proceso']['beneficiario_nombre'], $reseth = false, $autopadding = true, $cellpadding = '', $border = 1));
                $y_referencia = ceil($pdf->getStringHeight(35, $proceso['Proceso']['referencia'], $reseth = false, $autopadding = true, $cellpadding = '', $border = 1));
                $y = max($y_motivo, $y_beneficiario, $y_referencia) + 1;
                if ($pdf->getY() + $y > $pdf->getPageHeight() - 15) {
                    $pdf->AddPage();
                    $pdf->SetY(15);
                    $pdf->SetFont('freesans', 'B', 7);
                    $pdf->SetFillColor(190, 190, 190);
                    $pdf->SetLineStyle(array('width' => 0.3, 'color' => array(145, 145, 145)));
                    $pdf->MultiCell(17, 8, 'Nro', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(18, 8, 'Preventivo', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(25, 8, 'Dependencia', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(35, 8, 'Motivo', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(35, 8, 'Beneficiario', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(35, 8, 'Referencia', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->MultiCell(20, 8, 'Monto', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                    $pdf->Ln();
                }

                $pdf->SetFont('freesans', '', 7);
                
                $i++;
                if ($i % 2) {
                    $pdf->SetFillColor(255, 255, 255);
                } else {
                    $pdf->SetFillColor(240, 240, 240);
                }
                $pdf->MultiCell(17, $y, $proceso['Proceso']['nro_proceso'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                $pdf->MultiCell(18, $y, $proceso['Proceso']['nro_preventivo'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                $pdf->MultiCell(25, $y, $proceso['Dependencia']['sigla'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                $pdf->MultiCell(35, $y, $proceso['Motivo']['nombre'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                $pdf->MultiCell(35, $y, $proceso['Proceso']['beneficiario_nombre'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                $pdf->MultiCell(35, $y, $proceso['Proceso']['referencia'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
                $pdf->MultiCell(20, $y, $proceso['Proceso']['monto'], 1, 'R', true, 0, '', '', true, 0, false, true, $y, 'M');
                $pdf->Ln();
            }
            
            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

            //Close and output PDF document
            if ($params['pdf'] == 'descargar') {
                $pdf->Output('reporte_' . date('Y-m-d') . '.pdf', 'D');
            } elseif ($params['pdf'] == 'imprimir') {
                $pdf->Output('reporte_' . date('Y-m-d') . '.pdf', 'I');
            }
        }
        
        $this->set('procesos', $procesos);
    }
    
    private function fecha($fecha, $completo = false) {
        $fecha = explode(' ', $fecha);

        $fecha_dia = explode('-', $fecha[0]);
        $dia = $fecha_dia[2] . '/' . $fecha_dia[1] . '/' . $fecha_dia[0];

        if ($completo) {
            $fecha_hora = explode(':', $fecha[1]);
            $hora = $fecha_hora[0] . ':' . $fecha_hora[1];
            return $dia . ' ' . $hora;
        } else {
            return $dia;
        }
    }
    
    public function isAuthorized() {        
        if (in_array($this->action, array('por_secretarias', 'por_estados', 'por_funcionarios')) && in_array($this->Auth->user('rol'), array('admin', 'reportes'))) {
            return true;
        }
        
        if (($this->action == 'finanzas') && ($this->Auth->user('rol') == 'finanzas')) {
            return true;
        }
        
        if (($this->action == 'analistas') && ($this->Auth->user('rol') == 'analistas')) {
            return true;
        }
        
        if (($this->action == 'caja') && ($this->Auth->user('rol') == 'caja')) {
            return true;
        }

        $this->Session->setFlash(__('No tiene los suficientes permisos para ingresar a este módulo'));
        return false;
    }

}