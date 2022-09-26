<?php
function fecha($fecha, $completo = false) {
    $fecha = explode(' ', $fecha);
    
    $fecha_dia = explode('-', $fecha[0]);
    $dia = $fecha_dia[2] . '-' . $fecha_dia[1] . '-' . $fecha_dia[0];

    if ($completo) {
        $fecha_hora = explode(':', $fecha[1]);
        $hora = $fecha_hora[0] . ':' . $fecha_hora[1];
        return $dia . ' ' . $hora;
    } else {
        return $dia;
    }
}


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
    <div class="buttons">
        <?php echo $this->Html->link('Por Secretarías', array('controller' => 'reportes', 'action' => 'por_secretarias')); ?>
        <?php echo $this->Html->link('Por Estados', array('controller' => 'reportes', 'action' => 'por_estados'), array('class' => 'seleccionado')); ?>
        <?php echo $this->Html->link('Por Funcionarios', array('controller' => 'reportes', 'action' => 'por_funcionarios')); ?>
    </div>
     
    <?php 
    echo $this->Form->create('Reportes', array('action' => 'por_estados', 'onsubmit' => 'return false;'));
    ?>
    <table cellspacing="0" cellpadding="0">
        <?php 
        $montos = array('menores' => 'Menor o igual a 10000', 'mayores' => 'Mayor a 10000');
        ?>
        <tr>
            <td width="25%"><?php echo $this->Form->input('monto', array('label' => 'Monto', 'type' => 'select', 'options' => $montos, 'default' => $params['monto'])); ?></td>
            <td width="25%">
                <div id="filtro-menores" <?php echo ($params['monto'] == 'menores')?'':'style="display: none;"'; ?>>
                    <?php echo $this->Form->input('estado_men', array('label' => 'Estados', 'type' => 'select', 'options' => $estados_men, 'default' => $params['estado'])); ?>
                </div>
                <div id="filtro-mayores" <?php echo ($params['monto'] == 'mayores')?'':'style="display: none;"'; ?>>
                    <?php echo $this->Form->input('estado_may', array('label' => 'Estados', 'type' => 'select', 'options' => $estados_may, 'default' => $params['estado'])); ?>
                </div>
            </td>
            <td width="25%">
                <div id="filtro-estado">
                    <?php echo $this->Form->input('estado_envio', array('label' => '&nbsp;', 'type' => 'select', 'options' => array('P' => 'Pendientes y Recibidos', 'H' => 'Histórico'), 'default' => $params['estado_envio'])); ?>
                </div>
            </td>
            <td width="25%">
                <div id="filtro-analistas" <?php echo ($params['estado'] == '3')?'':'style="display: none;"'; ?>>
                    <?php echo $this->Form->input('analista', array('label' => 'Analista', 'type' => 'select', 'options' => $analistas, 'default' => $params['analista'], 'empty' => 'Todos')); ?>
                </div>
            </td>
        </tr>
        <tr>
            <td>
                <?php echo $this->Form->input('dependencia', array('label' => 'Dependencia', 'type' => 'select', 'options' => $dependencias, 'default' => $params['dependencia'], 'empty' => 'Todas')); ?>
            </td>
            <td colspan="2">
                <div id="filtro-fechas" <?php echo ($params['estado_envio'] == 'P')?'style="display: none;"':''; ?>>
                <?php 
                    $fecha_fin = $this->Form->input('fecha_fin', array('label' => false, 'div' => false, 'size' => 8, 'after' => '<input id="BtnFechaFin" type="button" value="..." />', 'value' => $params['fecha_fin'], 'readonly' => true));
                    echo $this->Form->input('fecha_ini', array('label' => 'Fecha de Envio', 'size' => 8, 'after' => '<input id="BtnFechaIni" type="button" value="..." /> a ' . $fecha_fin, 'value' => $params['fecha_ini'], 'readonly' => true)); 
                ?>
                </div>
            </td>
            <td>
                <div class="right">
                <?php
                echo $this->Form->end(__('Generar', true)); 
                ?>
                </div>
            </td>
        </tr>
    </table>
    
    <?php if (!empty($params['buscar'])): ?>
    <div class="procesos related">
        <table cellpadding="0" cellspacing="0">
            <tr>
                <th width="40"><?php echo $this->Paginator->sort('nro_proceso', 'Nº Proceso'); ?></th>
                <th><?php echo $this->Paginator->sort('nro_proeventivo', 'Nº Preventivo'); ?></th>
                <th><?php echo $this->Paginator->sort('cite'); ?></th>
                <th><?php echo $this->Paginator->sort('Dependencia.nombre', 'Dependencia'); ?></th>
                <th><?php echo $this->Paginator->sort('ProcesosEstado.fecha_envio', 'Fecha envío'); ?></th>
                <th><?php echo $this->Paginator->sort('ProcesosEstado.fecha_recepcion', 'Fecha recepción'); ?></th>
                <!--<th><?php echo $this->Paginator->sort('motivo_id'); ?></th>-->
                <th><?php echo $this->Paginator->sort('beneficiario_nombre', 'Beneficiario'); ?></th>
                <th><?php echo $this->Paginator->sort('monto'); ?></th>
            </tr>
            <?php
            $i = (intval($params['page']) - 1) * 20;
            foreach ($procesos as $proceso):
                $i++;
                ?>
                <tr>
                    <td><?php echo h($proceso['Proceso']['nro_proceso']); ?>&nbsp;</td>
                    <td><?php echo h($proceso['Proceso']['nro_preventivo']); ?>&nbsp;</td>
                    <td><?php echo h($proceso['Proceso']['cite']); ?>&nbsp;</td>
                    <td><?php echo $proceso['Dependencia']['sigla']; ?></td>
                    <td><?php echo fecha($proceso['ProcesosEstado']['fecha_envio'], true); ?></td>
                    <td><?php echo empty($proceso['ProcesosEstado']['fecha_recepcion'])?'No recepcionado':fecha($proceso['ProcesosEstado']['fecha_recepcion'], true); ?></td>
                    <!--<td><?php echo $proceso['Motivo']['nombre']; ?></td>-->
                    <td><?php echo $proceso['Proceso']['beneficiario_nombre']; ?><br /><?php echo $proceso['Proceso']['beneficiario_documento']; ?></td>
                    <td align="right"><?php echo h($proceso['Proceso']['monto']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <p>
            <?php
            echo $this->Paginator->counter(array(
                'format' => __('Página {:page} de {:pages}, mostrando {:current} registros de un total de {:count}')
            ));
            ?>
        </p>
        <div class="paging">
            <?php
            echo $this->Paginator->prev('< ' . __('Anterior'), array(), null, array('class' => 'prev disabled'));
            echo $this->Paginator->numbers(array('separator' => ''));
            echo $this->Paginator->next(__('Siguiente') . ' >', array(), null, array('class' => 'next disabled'));
            ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script type="text/javascript">
    //<![CDATA[
    var cal_1 = Calendar.setup({
        weekNumbers : false,
        onSelect: function() {
            var date = cal_1.selection.get();
            if (date) {
                date = Calendar.intToDate(date);
                $('#ReportesFechaIni').val(Calendar.printDate(date, "%Y-%m-%d"));
            } 
            this.hide();
        }
    });
    cal_1.manageFields("BtnFechaIni", "ReportesFechaIni", "%Y-%m-%d");
    
    var cal_2 = Calendar.setup({
        weekNumbers : false,
        onSelect: function() {
            var date = cal_2.selection.get();
            if (date) {
                date = Calendar.intToDate(date);
                $('#ReportesFechaFin').val(Calendar.printDate(date, "%Y-%m-%d"));
            } 
            this.hide();
        }
    });
    cal_2.manageFields("BtnFechaFin", "ReportesFechaFin", "%Y-%m-%d");

    function getURL () {
        var estado = 0;
        var url = '<?php echo $this->Html->url(array('controller' => 'reportes', 'action' => 'por_estados')); ?>';
        url += '/buscar:1';
        url += '/monto:' + $('#ReportesMonto').val();
        
        if ($('#ReportesMonto').val() == 'menores') {
            estado = $('#ReportesEstadoMen').val();
        } else if ($('#ReportesMonto').val() == 'mayores') {
            estado = $('#ReportesEstadoMay').val();
        } 
        url += '/estado:' + estado;

        if (estado == '3') {
            url += '/analista:' + $('#ReportesAnalista').val();
        }
        
        if ($('#ReportesEstadoEnvio').val() != '') {
            url += '/estado_envio:' + $('#ReportesEstadoEnvio').val();
        }
        
        if ($('#ReportesDependencia').val() != '') {
            url += '/dependencia:' + $('#ReportesDependencia').val();
        }
        
        if ($('#ReportesEstadoEnvio').val() == 'H') {
            if ($('#ReportesFechaIni').val() != '') {
                url += '/fecha_ini:' + $('#ReportesFechaIni').val();
            }
            if ($('#ReportesFechaFin').val() != '') {
                url += '/fecha_fin:' + $('#ReportesFechaFin').val();
            }
        }

        return url;
    }
    
    $('#ReportesPorEstadosForm').submit(function() {
        var url = getURL();
        window.location = url;
    });
    
    $('#ReportesMonto').change(function() {
        if ($('#ReportesMonto').val() == 'menores') {
            $('#filtro-menores').show();
            $('#filtro-mayores').hide();
        } else if ($('#ReportesMonto').val() == 'mayores') {
            $('#filtro-mayores').show();
            $('#filtro-menores').hide();
        }
    });

    $('#ReportesEstadoMay').change(function() {
        if ($('#ReportesEstadoMay').val() == '3') {
            $('#filtro-analistas').show();
        } else {
            $('#filtro-analistas').hide();
        }
    });
    
    $('#ReportesEstadoMen').change(function() {
        if ($('#ReportesEstadoMen').val() == '3') {
            $('#filtro-analistas').show();
        } else {
            $('#filtro-analistas').hide();
        }
    });
    
    $('#ReportesEstadoEnvio').change(function() {
        if ($('#ReportesEstadoEnvio').val() == 'H') {
            $('#filtro-fechas').show();
        } else {
            $('#filtro-fechas').hide();
        }
    });
    
    //]]>
</script>

<?php if ($params['buscar']): ?>
<div class="buttons right">
    <?php $url = $this->Html->url(null, true); ?>
    <?php echo $this->Html->link($this->Html->image('botones/imprimir.png') . ' Imprimir', 'javascript:void(0);', array('onclick' => 'javascript:ventana(\'' . $url . '/pdf:imprimir\', 960, 640)', 'escape' => false)); ?>
    <?php echo $this->Html->link($this->Html->image('botones/pdf.png') . ' Descargar', 'javascript:void(0);', array('onclick' => 'javascript:ventana(\'' . $url . '/pdf:descargar\', 960, 640)', 'escape' => false)); ?>
</div>
<?php endif; ?>