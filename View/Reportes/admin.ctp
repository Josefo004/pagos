<?php 
$this->Html->css(array(
        'jscalendar/jscal2',
        'jscalendar/border-radius',
        'jscalendar/steel/steel',
        'jscalendar/reduce-spacing'
    ), 
    'stylesheet', 
    array('inline' => false)
);

echo $this->Html->script(array('jscalendar/jscal2', 'jscalendar/unicode-letter', 'jscalendar/lang/es'), false)
?>

<div class="reportes related">
    <h2><?php echo __('Generación de Reportes'); ?></h2>
    <?php 
    echo $this->Form->create('Reportes', array('action' => 'generar', 'onsubmit' => 'return false;'));
    ?>
    <table cellspacing="0" cellpadding="0">
        <?php 
        $anios = array();
        for ($i = 2012; $i <= date('Y'); $i++) {
            $anios[$i] = $i;
        }

        $meses = array(
            '01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril',
            '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto',
            '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'
        );

        if ($params['detalle'] == 'por_estados') {
            $montos = array('menores' => 'Menor o igual a 10000', 'mayores' => 'Mayor a 10000');
        } else {
            $montos = array('' => 'Todos', 'menores' => 'Menor o igual a 10000', 'mayores' => 'Mayor a 10000');
        }

        $detalles = array('' => 'Ninguno', 'por_estados' => 'Por estados', 'por_motivos' => 'Por motivos');
        
        ?>
        <tr>
            <td width="25%"><?php echo $this->Form->input('ciclo', array('type' => 'select', 'options' => array('anual' => 'Anual', 'mensual' => 'Mensual', 'diario' => 'Diario'), 'label' => 'Ciclo', 'default' => $params['ciclo'])); ?></td>
            <td width="25%">
                <div id="filtro-anio" <?php echo ($params['ciclo'] == 'anual')?'':'style="display: none;"'; ?>>
                    <?php echo $this->Form->input('anio', array('label' => 'Gestión', 'type' => 'select', 'options' => $anios, 'default' => $params['anio'])); ?>
                </div>
                <div id="filtro-mes" <?php echo ($params['ciclo'] == 'mensual')?'':'style="display: none;"'; ?>>
                    <?php echo $this->Form->input('mes', array('label' => 'Mes', 'type' => 'select', 'options' => $meses, 'default' => $params['mes'])); ?>
                </div>
                <div id="filtro-dia" <?php echo ($params['ciclo'] == 'diario')?'':'style="display: none;"'; ?>>
                    <?php echo $this->Form->input('dia', array('label' => 'Gestión', 'size' => 8, 'after' => '<input id="BtnDia" type="button" value="..." />', 'value' => $params['dia'], 'readonly' => true)); ?>
                </div>
            </td>
            <td width="25%"><?php echo $this->Form->input('monto', array('label' => 'Monto', 'type' => 'select', 'options' => $montos, 'default' => $params['monto'])); ?></td>
            <td width="25%"><?php echo $this->Form->input('detalle', array('label' => 'Más detalle', 'type' => 'select', 'options' => $detalles, 'default' => $params['detalle'])); ?></td>
            <td>
                <div class="right">
                <?php
                echo $this->Form->end(__('Generar', true)); 
                ?>
                </div>
            </td>
        </tr>
    </table>
    <div style="overflow: auto;">
    <?php if ($params['detalle'] == 'por_estados'): ?>
        <?php if ($params['monto'] == 'menores') : ?>
        <table cellspacing="0" class="reporte" style="width: 1720px">
            <tr>
                <th rowspan="3" width="400">Dependencia</th>
                <th colspan="10" style="text-align: center;">Procesos por Estado</th>
                <th rowspan="3">&nbsp;&nbsp;Total&nbsp;&nbsp;</th>
            </tr>
            <tr>
                <th rowspan="2">[1] Recepcionados<br /><span>[Dir. Finanzas]</span></th>
                <th colspan="4">[2] Análisis y Proceso</th>
                <th colspan="2">[3] Elaboración de Cheque</th>
                <th rowspan="2">[4] Firma de Cheque<br /><span>[Contabilidad]</span></th>
                <th rowspan="2">[5] Entrega de Cheque<br /><span>[Caja]</span></th>
                <th rowspan="2">[6] Archivo<br /><span>[Archivo]</span></th>
            </tr>
            <tr>
                <th>[2.1] Recepción<br /><span>[Contabilidad]</span></th>
                <th>[2.2] Revisión<br /><span>[Analistas]</span></th>
                <th>[2.3] Con observación<br /><span>[Administradores]</span></th>
                <th>[2.4] Sin observación<br /><span>[Contabilidad]</span></th>
                <th>[3.1] Recepción<br /><span>[Tesorería]</span></th>
                <th>[3.2] Impresión<br /><span>[Tesorería]</span></th>
            </tr>
            <?php 
            $totales[0] = 0;
            $totales[1] = 0; $totales[2] = 0; $totales[3] = 0; $totales[4] = 0; 
            $totales[5] = 0; $totales[6] = 0; $totales[7] = 0; $totales[8] = 0;
            $totales[12] = 0; $totales[13] = 0;
            ?>
            <?php foreach ($reporte_total as $dependencia) : ?>
            <tr>
                <td><?php echo $dependencia['Dependencia']['nombre']; ?></td>
                <td class="numerico"><?php list(, $estado) = each($reporte_estado[1]); echo $estado['Proceso']['total']; $totales[1] += $estado['Proceso']['total']; ?></td>
                <td class="numerico"><?php list(, $estado) = each($reporte_estado[2]); echo $estado['Proceso']['total']; $totales[2] += $estado['Proceso']['total']; ?></td>
                <td class="numerico"><?php list(, $estado) = each($reporte_estado[3]); echo $estado['Proceso']['total']; $totales[3] += $estado['Proceso']['total']; ?></td>
                <td class="numerico"><?php list(, $estado) = each($reporte_estado[4]); echo $estado['Proceso']['total']; $totales[4] += $estado['Proceso']['total']; ?></td>
                <td class="numerico"><?php list(, $estado) = each($reporte_estado[5]); echo $estado['Proceso']['total']; $totales[5] += $estado['Proceso']['total']; ?></td>
                <td class="numerico"><?php list(, $estado) = each($reporte_estado[6]); echo $estado['Proceso']['total']; $totales[6] += $estado['Proceso']['total']; ?></td>
                <td class="numerico"><?php list(, $estado) = each($reporte_estado[7]); echo $estado['Proceso']['total']; $totales[7] += $estado['Proceso']['total']; ?></td>
                <td class="numerico"><?php list(, $estado) = each($reporte_estado[8]); echo $estado['Proceso']['total']; $totales[8] += $estado['Proceso']['total']; ?></td>
                <td class="numerico"><?php list(, $estado) = each($reporte_estado[12]); echo $estado['Proceso']['total']; $totales[12] += $estado['Proceso']['total']; ?></td>
                <td class="numerico"><?php list(, $estado) = each($reporte_estado[13]); echo $estado['Proceso']['total']; $totales[13] += $estado['Proceso']['total']; ?></td>
                <td class="numerico"><b><?php echo $dependencia['Proceso']['total']; $totales[0] += $dependencia['Proceso']['total']; ?></b></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td></td>
                <td class="numerico"><b><?php echo $totales[1]; ?></b></td>
                <td class="numerico"><b><?php echo $totales[2]; ?></b></td>
                <td class="numerico"><b><?php echo $totales[3]; ?></b></td>
                <td class="numerico"><b><?php echo $totales[4]; ?></b></td>
                <td class="numerico"><b><?php echo $totales[5]; ?></b></td>
                <td class="numerico"><b><?php echo $totales[6]; ?></b></td>
                <td class="numerico"><b><?php echo $totales[7]; ?></b></td>
                <td class="numerico"><b><?php echo $totales[8]; ?></b></td>
                <td class="numerico"><b><?php echo $totales[12]; ?></b></td>
                <td class="numerico"><b><?php echo $totales[13]; ?></b></td>
                <td class="numerico"><b><?php echo $totales[0]; ?></b></td>
            </tr>
        </table>
        <?php else : ?>
        <table cellspacing="0" class="reporte" style="width: 1960px">
            <tr>
                <th rowspan="3" width="400">Dependencia</th>
                <th colspan="12" style="text-align: center;">Procesos por Estado</th>
                <th rowspan="3">&nbsp;&nbsp;Total&nbsp;&nbsp;</th>
            </tr>
            <tr>
                <th rowspan="2">[1] Recepcionados<br /><span>[Dir. Finanzas]</span></th>
                <th colspan="4">[2] Análisis y Proceso</th>
                <th colspan="2">[3] Elaboración de Cheque</th>
                <th rowspan="2">[4] Firma de Cheque<br /><span>[Dir. Finanzas]</span></th>
                <th rowspan="2">[5] Firma de Cheque<br /><span>[Stria. Economía]</span></th>
                <th rowspan="2">[6] Cheque concluido<br /><span>[Dir. Finanzas]</span></th>
                <th rowspan="2">[7] Entrega de Cheque<br /><span>[Caja]</span></th>
                <th rowspan="2">[8] Archivo<br /><span>[Archivo]</span></th>
            </tr>
            <tr>
                <th>[2.1] Recepción<br /><span>[Contabilidad]</span></th>
                <th>[2.2] Revisión<br /><span>[Analistas]</span></th>
                <th>[2.3] Con observación<br /><span>[Administradores]</span></th>
                <th>[2.4] Sin observación<br /><span>[Contabilidad]</span></th>
                <th>[3.1] Recepción<br /><span>[Tesorería]</span></th>
                <th>[3.2] Impresión<br /><span>[Tesorería]</span></th>
            </tr>
            <?php 
            $totales[0] = 0;
            $totales[1] = 0; $totales[2] = 0; $totales[3] = 0; $totales[4] = 0; 
            $totales[5] = 0; $totales[6] = 0; $totales[7] = 0; $totales[9] = 0;
            $totales[10] = 0; $totales[11] = 0;$totales[12] = 0; $totales[13] = 0;
            ?>
            <?php foreach ($reporte_total as $dependencia) : ?>
            <tr>
                <td><?php echo $dependencia['Dependencia']['nombre']; ?></td>
                <td class="numerico"><?php list(, $estado) = each($reporte_estado[1]); echo $estado['Proceso']['total']; $totales[1] += $estado['Proceso']['total']; ?></td>
                <td class="numerico"><?php list(, $estado) = each($reporte_estado[2]); echo $estado['Proceso']['total']; $totales[2] += $estado['Proceso']['total']; ?></td>
                <td class="numerico"><?php list(, $estado) = each($reporte_estado[3]); echo $estado['Proceso']['total']; $totales[3] += $estado['Proceso']['total']; ?></td>
                <td class="numerico"><?php list(, $estado) = each($reporte_estado[4]); echo $estado['Proceso']['total']; $totales[4] += $estado['Proceso']['total']; ?></td>
                <td class="numerico"><?php list(, $estado) = each($reporte_estado[5]); echo $estado['Proceso']['total']; $totales[5] += $estado['Proceso']['total']; ?></td>
                <td class="numerico"><?php list(, $estado) = each($reporte_estado[6]); echo $estado['Proceso']['total']; $totales[6] += $estado['Proceso']['total']; ?></td>
                <td class="numerico"><?php list(, $estado) = each($reporte_estado[7]); echo $estado['Proceso']['total']; $totales[7] += $estado['Proceso']['total']; ?></td>
                <td class="numerico"><?php list(, $estado) = each($reporte_estado[9]); echo $estado['Proceso']['total']; $totales[9] += $estado['Proceso']['total']; ?></td>
                <td class="numerico"><?php list(, $estado) = each($reporte_estado[10]); echo $estado['Proceso']['total']; $totales[10] += $estado['Proceso']['total']; ?></td>
                <td class="numerico"><?php list(, $estado) = each($reporte_estado[11]); echo $estado['Proceso']['total']; $totales[11] += $estado['Proceso']['total']; ?></td>
                <td class="numerico"><?php list(, $estado) = each($reporte_estado[12]); echo $estado['Proceso']['total']; $totales[12] += $estado['Proceso']['total']; ?></td>
                <td class="numerico"><?php list(, $estado) = each($reporte_estado[13]); echo $estado['Proceso']['total']; $totales[13] += $estado['Proceso']['total']; ?></td>
                <td class="numerico"><b><?php echo $dependencia['Proceso']['total']; $totales[0] += $dependencia['Proceso']['total']; ?></b></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td></td>
                <td class="numerico"><b><?php echo $totales[1]; ?></b></td>
                <td class="numerico"><b><?php echo $totales[2]; ?></b></td>
                <td class="numerico"><b><?php echo $totales[3]; ?></b></td>
                <td class="numerico"><b><?php echo $totales[4]; ?></b></td>
                <td class="numerico"><b><?php echo $totales[5]; ?></b></td>
                <td class="numerico"><b><?php echo $totales[6]; ?></b></td>
                <td class="numerico"><b><?php echo $totales[7]; ?></b></td>
                <td class="numerico"><b><?php echo $totales[9]; ?></b></td>
                <td class="numerico"><b><?php echo $totales[10]; ?></b></td>
                <td class="numerico"><b><?php echo $totales[11]; ?></b></td>
                <td class="numerico"><b><?php echo $totales[12]; ?></b></td>
                <td class="numerico"><b><?php echo $totales[13]; ?></b></td>
                <td class="numerico"><b><?php echo $totales[0]; ?></b></td>
            </tr>
        </table>
        <?php endif; ?>
    <?php elseif ($params['detalle'] == 'por_motivos'): ?>
        <table cellspacing="0" class="reporte" style="width: 1600px;">
            <tr>
                <th rowspan="2" width="400">Dependencia</th>
                <th colspan="<?php echo count($motivos); ?>">Procesos por Motivo</th>
                <th rowspan="2" width="120">Total</th>
            </tr>
            <tr>
                <?php foreach ($motivos as $motivo) : ?>
                <th width="120"><?php echo $motivo['Motivo']['nombre']; ?></th>
                <?php endforeach; ?>
            </tr>
            <?php 
            $totales[0] = 0;
            foreach ($motivos as $motivo) {
                $totales[$motivo['Motivo']['id']] = 0;
            }
            ?>
            <?php foreach ($reporte_total as $dependencia) : ?>
            <tr>
                <td><?php echo $dependencia['Dependencia']['nombre']; ?></td>
                <?php 
                foreach ($motivos as $motivo) : 
                    list(, $motivo_id) = each($reporte_motivo[$motivo['Motivo']['id']]);
                    $totales[$motivo['Motivo']['id']] += $motivo_id['Proceso']['total'];
                    ?>
                <td class="numerico"><?php echo $motivo_id['Proceso']['total']; ?></td>
                <?php endforeach; ?>
                <td class="numerico"><b><?php echo $dependencia['Proceso']['total']; $totales[0] += $dependencia['Proceso']['total']; ?></b></td>
            </tr>
            <?php endforeach; ?>
            <tr>
                <td></td>
                <?php foreach($motivos as $motivo) : ?>
                <td class="numerico"><b><?php echo $totales[$motivo['Motivo']['id']]; ?></b></td>
                <?php endforeach; ?>
                <td class="numerico"><b><?php echo $totales[0]; ?></b></td>
            </tr>
        </table>
    <?php else : ?>
        <table cellspacing="0" class="reporte" style="width: 520px;" align="center">
            <tr>
                <th width="400">Dependencia</th>
                <th width="120">Total<br />Procesos</th>
            </tr>
            <?php $total = 0; ?>
            <?php foreach ($reporte_total as $dependencia) : ?>
            <tr>
                <td><?php echo $dependencia['Dependencia']['nombre']; ?></td>
                <td class="numerico"><b><?php echo $dependencia['Proceso']['total']; ?></b></td>
            </tr>
            <?php $total += $dependencia['Proceso']['total']; ?>
            <?php endforeach; ?>
            <tr>
                <td ><b>Total</b></td>
                <td class="numerico"><b><?php echo $total; ?></b></td>
            </tr>
        </table>
    <?php endif; ?>
    </div>
</div>

<script type="text/javascript">
    //<![CDATA[
    var cal = Calendar.setup({
        weekNumbers : false,
        onSelect: function() {
            var date = cal.selection.get();
            if (date) {
                date = Calendar.intToDate(date);
                $('#ReportesDia').val(Calendar.printDate(date, "%Y-%m-%d"));
            } 
            this.hide();
        }
    });
    cal.manageFields("BtnDia", "ReportesDia", "%Y-%m-%d");

    function getURL () {
        url = '<?php echo $this->Html->url(array('controller' => 'reportes', 'action' => 'generar')); ?>';
        url += '/ciclo:' + $('#ReportesCiclo').val();
        
        if ($('#ReportesCiclo').val() == 'anual') {
            url += '/anio:' + $('#ReportesAnio').val();
        } else if ($('#ReportesCiclo').val() == 'mensual') {
            url += '/mes:' + $('#ReportesMes').val();
        } else if ($('#ReportesCiclo').val() == 'diario') {
            url += '/dia:' + $('#ReportesDia').val();
        }
        
        if ($('#ReportesMonto').val() != '') {
            url += '/monto:' + $('#ReportesMonto').val();
        }
        if ($('#ReportesDetalle').val() != '') {
            url += '/detalle:' + $('#ReportesDetalle').val();
        }

        return url;
    }

    $('#ReportesGenerarForm').submit(function() {
        var url = getURL();
        window.location = url;
    });
    
    $('#ReportesCiclo').change(function() {
        if ($('#ReportesCiclo').val() == 'anual') {
            $('#filtro-anio').show();
            $('#filtro-mes').hide();
            $('#filtro-dia').hide();
        } else if ($('#ReportesCiclo').val() == 'mensual') {
            $('#filtro-anio').hide();
            $('#filtro-mes').show();
            $('#filtro-dia').hide();
        } else if ($('#ReportesCiclo').val() == 'diario') {
            $('#filtro-anio').hide();
            $('#filtro-mes').hide();
            $('#filtro-dia').show();
        }
    });
    
    $('#ReportesDetalle').change(function() {
        if ($('#ReportesDetalle').val() == 'por_estados') {
            $('#ReportesMonto').html(
                '<option selected="selected" value="menores">Menor o igual a 10000</option>' +
                '<option value="mayores">Mayor a 10000</option>'
            );
        } else {
            $('#ReportesMonto').html(
                '<option selected="selected" value="">Todos</option>' +
                '<option value="menores">Menor o igual a 10000</option>' +
                '<option value="mayores">Mayor a 10000</option>'
            );
        }
    });
    
    //]]>
</script>

<div class="buttons right">
    <?php $url = $this->Html->url(null, true); ?>
    <?php echo $this->Html->link($this->Html->image('botones/imprimir.png') . ' Imprimir', 'javascript:void(0);', array('onclick' => 'javascript:ventana(\'' . $url . '/pdf:imprimir\', 960, 640)', 'escape' => false)); ?>
    <?php echo $this->Html->link($this->Html->image('botones/pdf.png') . ' Descargar', 'javascript:void(0);', array('onclick' => 'javascript:ventana(\'' . $url . '/pdf:descargar\', 960, 640)', 'escape' => false)); ?>
</div>