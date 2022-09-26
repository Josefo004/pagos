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

$montos = array('menores' => 'Menor o igual a 10000', 'mayores' => 'Mayor a 10000');
if ($params['monto'] == 'mayores') {
    $estados = array(
        '1' => 'Ingreso de Documentos', 
        '9' => 'Firma de Cheque',
        '11' => 'Cheque concluido'
    );
} else {
    $estados = array(
        '1' => 'Ingreso de Documentos'
    );
}

$tipos = array('recepcionados' => 'Recepcionados', 'enviados' => 'Enviados');

$this->Html->css(array(
        'jscalendar/jscal2',
        'jscalendar/border-radius',
        'jscalendar/steel/steel',
        'jscalendar/reduce-spacing'
    ), 
    'stylesheet', 
    array('inline' => false)
);

echo $this->Html->script(array('jscalendar/jscal2', 'jscalendar/unicode-letter', 'jscalendar/lang/es'), false);
?>

<div class="reportes related">
    <h2><?php echo __('Generación de Reportes'); ?></h2>
    <?php 
    echo $this->Form->create('Reportes', array('action' => 'finanzas', 'onsubmit' => 'return false;'));
    $tipo = $this->Form->input('tipo', array('label' => false, 'type' => 'select', 'options' => $tipos, 'default' => $params['tipo'], 'div' => false));
    ?>
    <table cellspacing="0" cellpadding="0">
        <tr>
            <td width="20%"><?php echo $this->Form->input('monto', array('label' => 'Monto', 'type' => 'select', 'options' => $montos, 'default' => $params['monto'])); ?></td>
            <td width="40%"><?php echo $this->Form->input('estado', array('label' => 'Estado', 'type' => 'select', 'options' => $estados, 'default' => $params['estado'], 'after' => $tipo)); ?></td>
            <td width="20%"><?php echo $this->Form->input('fecha_ini', array('label' => 'Desde', 'size' => 8, 'after' => '<input id="BtnFechaIni" type="button" value="..." />', 'value' => $params['fecha_ini'], 'readonly' => true)); ?></td>
            <td width="20%"><?php echo $this->Form->input('fecha_fin', array('label' => 'Hasta', 'size' => 8, 'after' => '<input id="BtnFechaFin" type="button" value="..." />', 'value' => $params['fecha_fin'], 'readonly' => true)); ?></td>
            <td>
                <div class="right">
                <?php
                echo $this->Form->end(__('Generar', true)); 
                ?>
                </div>
            </td>
        </tr>
    </table>    
    <?php if (!empty($params['estado'])) : ?>
    <div class="buttons left">
        <?php $url = $this->Html->url(array('controller' => 'procesos', 'action' => 'index'), true); ?>
        <?php echo $this->Html->link($this->Html->image('botones/ver.png') . ' Ver', 'javascript:action(\'' . $url . '\', \'ver\', id_registro, null)', array('escape' => false)); ?>
    </div>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th width="40"><?php echo $this->Paginator->sort('nro_proceso', 'Nro'); ?></th>
            <th><?php echo $this->Paginator->sort('cite'); ?></th>
            <th><?php echo $this->Paginator->sort('dependencia_id'); ?></th>
            <th><?php echo $this->Paginator->sort('motivo_id'); ?></th>
            <th><?php echo $this->Paginator->sort('beneficiario_nombre', 'Beneficiario'); ?></th>
            <th><?php echo $this->Paginator->sort('monto'); ?></th>
        </tr>
        <?php
        $i = (intval($params['page']) - 1) * 20;
        foreach ($procesos as $proceso):
            $i++;
            ?>
            <tr onclick="javascript:seleccionar(this, <?php echo $proceso['Proceso']['id']; ?>);">
                <td><?php echo h($proceso['Proceso']['nro_proceso']); ?>&nbsp;</td>
                <td><?php echo h($proceso['Proceso']['cite']); ?>&nbsp;</td>
                <td><?php echo $proceso['Dependencia']['sigla']; ?></td>
                <td><?php echo $proceso['Motivo']['nombre']; ?></td>
                <td><?php echo $proceso['Proceso']['beneficiario_nombre']; ?></td>
                <td align="right"><?php echo h($proceso['Proceso']['monto']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
    <?php echo $this->Form->end(); ?>
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
        url = '<?php echo $this->Html->url(array('controller' => 'reportes', 'action' => 'finanzas')); ?>';
        url += '/monto:' + $('#ReportesMonto').val();
        url += '/fecha_ini:' + $('#ReportesFechaIni').val();
        url += '/fecha_fin:' + $('#ReportesFechaFin').val();
        url += '/estado:' + $('#ReportesEstado').val();
        url += '/tipo:' + $('#ReportesTipo').val();

        return url;
    }

    $('#ReportesFinanzasForm').submit(function() {
        var url = getURL();
        window.location = url;
    });
    
    $('#ReportesMonto').change(function() {
        if ($('#ReportesMonto').val() == 'menores') {
            $('#ReportesEstado').html(
                '<option selected="selected" value="1">Ingreso de Documentos</option>'
            );
        } else if ($('#ReportesMonto').val() == 'mayores') {
            $('#ReportesEstado').html(
                '<option selected="selected" value="1">Ingreso de Documentos</option>' +
                '<option value="9">Firma de Cheque o igual a 10000</option>' +
                '<option value="11">Cheque concluido</option>'
            );
        }
    });

    //]]>
</script>

<div class="buttons right">
    <?php $url = $this->Html->url(null, true); ?>
    <?php echo $this->Html->link($this->Html->image('botones/imprimir.png') . ' Imprimir', 'javascript:void(0);', array('onclick' => 'javascript:ventana(\'' . $url . '/pdf:imprimir\', 960, 640)', 'escape' => false)); ?>
    <?php echo $this->Html->link($this->Html->image('botones/pdf.png') . ' Descargar', 'javascript:void(0);', array('onclick' => 'javascript:ventana(\'' . $url . '/pdf:descargar\', 960, 640)', 'escape' => false)); ?>
    |
    <?php echo $this->Html->link($this->Html->image('botones/imprimir.png') . ' Imprimir Histórico', 'javascript:void(0);', array('onclick' => 'javascript:ventana(\'' . $url . '/pdf:imprimir_historico\', 960, 640)', 'escape' => false)); ?>
    <?php echo $this->Html->link($this->Html->image('botones/pdf.png') . ' Descargar Histórico', 'javascript:void(0);', array('onclick' => 'javascript:ventana(\'' . $url . '/pdf:descargar_historico\', 960, 640)', 'escape' => false)); ?>
</div>