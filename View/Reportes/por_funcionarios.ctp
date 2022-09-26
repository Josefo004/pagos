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
    <div class="buttons">
        <?php echo $this->Html->link('Por Secretarías', array('controller' => 'reportes', 'action' => 'por_secretarias')); ?>
        <?php echo $this->Html->link('Por Estados', array('controller' => 'reportes', 'action' => 'por_estados')); ?>
        <?php echo $this->Html->link('Por Funcionarios', array('controller' => 'reportes', 'action' => 'por_funcionarios'), array('class' => 'seleccionado')); ?>
    </div>
     
    <?php 
    echo $this->Form->create('Reportes', array('action' => 'por_funcionarios', 'onsubmit' => 'return false;'));
    ?>
    <table cellspacing="0" cellpadding="0">
        <tr>
            <td width="25%"><?php echo $this->Form->input('rol', array('type' => 'select', 'options' => array('analistas' => 'Analistas', 'administradores' => 'Administradores'), 'label' => 'Rol', 'default' => $params['rol'])); ?></td>
            <td width="75%">&nbsp;</td>
            <td>
                <div class="right">
                <?php
                echo $this->Form->end(__('Generar', true)); 
                ?>
                </div>
            </td>
        </tr>
    </table>
    <?php if ($params['rol'] == 'analistas') { ?>
    <div style="overflow: auto;">
        <table cellspacing="0" class="reporte">
            <tr>
                <th rowspan="2">Funcionario</th>
                <th colspan="3">Montos menores o iguales a 10000</th>
                <th colspan="3">Montos mayores a 10000</th>
                <th rowspan="2">Total</th>
            </tr>
            <tr>
                <th>Pendientes</th>
                <th>Recibidos</th>
                <th>Total</th>
                <th>Pendientes</th>
                <th>Recibidos</th>
                <th>Total</th>
            </tr>
        <?php
        $i = -1;
        $total = array(
            'men_pendientes' => 0, 'men_recibidos' => 0, 'men_total' => 0,
            'may_pendientes' => 0, 'may_recibidos' => 0, 'may_total' => 0,
            'total' => 0
        );
        foreach ($funcionarios as $funcionario) {
            $i++;
            $total_parcial = $menores[$i][0]['total'] + $mayores[$i][0]['total'];
            $total['men_pendientes'] += $menores[$i][0]['pendientes'];
            $total['men_recibidos'] += $menores[$i][0]['recibidos'];
            $total['men_total'] += $menores[$i][0]['total'];
            $total['may_pendientes'] += $mayores[$i][0]['pendientes'];
            $total['may_recibidos'] += $mayores[$i][0]['recibidos'];
            $total['may_total'] += $mayores[$i][0]['total'];
            
            $total['total'] += $total_parcial;
            ?>
            <tr>
                <td><?php echo strtoupper($funcionario['ServidoresPublico']['apellidos'] . ', ' . $funcionario['ServidoresPublico']['nombres']); ?></td>
                <td class="numerico"><?php echo $menores[$i][0]['pendientes']; ?></td>
                <td class="numerico"><?php echo $menores[$i][0]['recibidos']; ?></td>
                <td class="numerico"><b><?php echo $this->Html->link($menores[$i][0]['total'], array('controller' => 'reportes', 'action' => 'por_estados', 'buscar' => '1', 'monto' => 'menores', 'estado' => '3', 'analista' => $funcionario['Usuario']['id'], 'estado_envio' => 'P')); ?></b></td>
                <td class="numerico"><?php echo $mayores[$i][0]['pendientes']; ?></td>
                <td class="numerico"><?php echo $mayores[$i][0]['recibidos']; ?></td>
                <td class="numerico"><b><?php echo $this->Html->link($mayores[$i][0]['total'], array('controller' => 'reportes', 'action' => 'por_estados', 'buscar' => '1', 'monto' => 'mayores', 'estado' => '3', 'analista' => $funcionario['Usuario']['id'], 'estado_envio' => 'P')); ?></b></td>
                <td class="numerico"><b><?php echo $total_parcial; ?></b></td>
            </tr>
        <?php } ?>
            <tr>
                <td></td>
                <td class="numerico"><b><?php echo $total['men_pendientes']; ?></b></td>
                <td class="numerico"><b><?php echo $total['men_recibidos']; ?></b></td>
                <td class="numerico"><b><?php echo $this->Html->link($total['men_total'], array('controller' => 'reportes', 'action' => 'por_estados', 'buscar' => '1', 'monto' => 'menores', 'estado' => '3', 'estado_envio' => 'P')); ?></b></td>
                <td class="numerico"><b><?php echo $total['may_pendientes']; ?></b></td>
                <td class="numerico"><b><?php echo $total['may_recibidos']; ?></b></td>
                <td class="numerico"><b><?php echo $this->Html->link($total['may_total'], array('controller' => 'reportes', 'action' => 'por_estados', 'buscar' => '1', 'monto' => 'mayores', 'estado' => '3', 'estado_envio' => 'P')); ?></b></td>
                <td class="numerico"><b><?php echo $total['total']; ?></b></td>
            </tr>
        </table>
    </div>
    <?php } elseif ($params['rol'] == 'administradores') { ?>      
    <div style="overflow: auto;">
        <table cellspacing="0" class="reporte">
            <tr>
                <th rowspan="2" width="20%">Dependencia</th>
                <th rowspan="2" width="20%">Funciorarios</th>
                <th colspan="3" width="27%" style="text-align: center;">Procesos menores o iguales a 10000</th>
                <th colspan="3" width="27%" style="text-align: center;">Procesos mayores a 10000</th>
                <th rowspan="2" width="6%">Total</th>
            </tr>
            <tr>
                <th>Pendientes</th>
                <th>Recibidos</th>
                <th>Total</th>
                <th>Pendientes</th>
                <th>Recibidos</th>
                <th>Total</th>
            </tr>
            <?php 
            $total = array(
                'men_pendientes' => 0, 'men_recibidos' => 0, 'men_total' => 0,
                'may_pendientes' => 0, 'may_recibidos' => 0, 'may_total' => 0,
                'total' => 0
            );
            $total_parcial = 0;
            foreach ($dependencias as $dependencia) { 
                $total_parcial = $dependencia['men_total'] + $dependencia['may_total'];
                $total['men_pendientes'] += $dependencia['men_pendientes'];
                $total['men_recibidos'] += $dependencia['men_recibidos'];
                $total['men_total'] += $dependencia['men_total'];
                $total['may_pendientes'] += $dependencia['may_pendientes'];
                $total['may_recibidos'] += $dependencia['may_recibidos'];
                $total['may_total'] += $dependencia['may_total'];
                $total['total'] += $total_parcial;
                ?>
            <tr>
                <td><?php echo $dependencia['nombre']; ?></td>
                <td>
                    <table style="border: none !important;margin-bottom: 0 !important;">
                    <?php foreach ($dependencia['ServidoresPublicos'] as $servidor) { ?>
                        <tr>
                            <td><?php echo strtoupper($servidor['ServidoresPublico']['apellidos'] . ', ' . $servidor['ServidoresPublico']['nombres']); ?></td>
                        </tr>
                    <?php } ?>
                    </table>
                </td>
                <td width="9%" class="numerico"><?php echo $dependencia['men_pendientes']; ?></td>
                <td width="9%" class="numerico"><?php echo $dependencia['men_recibidos']; ?></td>
                <td width="9%" class="numerico"><b><?php echo $this->Html->link($dependencia['men_total'], array('controller' => 'reportes', 'action' => 'por_estados', 'buscar' => '1', 'monto' => 'menores', 'estado' => '4', 'dependencia' => $dependencia['id'], 'estado_envio' => 'P')); ?></b></td>
                <td width="9%" class="numerico"><?php echo $dependencia['may_pendientes']; ?></td>
                <td width="9%" class="numerico"><?php echo $dependencia['may_recibidos']; ?></td>
                <td width="9%" class="numerico"><b><?php echo $this->Html->link($dependencia['may_total'], array('controller' => 'reportes', 'action' => 'por_estados', 'buscar' => '1', 'monto' => 'mayores', 'estado' => '4', 'dependencia' => $dependencia['id'], 'estado_envio' => 'P')); ?></b></td>
                <td width="9%" class="numerico"><b><?php echo $total_parcial; ?></b></td>
            </tr>
            <?php } ?>
            <tr>
                <td align="right" colspan="2"></td>
                <td class="numerico"><b><?php echo $total['men_pendientes']; ?></b></td>
                <td class="numerico"><b><?php echo $total['men_recibidos']; ?></b></td>
                <td class="numerico"><b><?php echo $this->Html->link($total['men_total'], array('controller' => 'reportes', 'action' => 'por_estados', 'buscar' => '1', 'monto' => 'menores', 'estado' => '4', 'estado_envio' => 'P')); ?></b></td>
                <td class="numerico"><b><?php echo $total['may_pendientes']; ?></b></td>
                <td class="numerico"><b><?php echo $total['may_recibidos']; ?></b></td>
                <td class="numerico"><b><?php echo $this->Html->link($total['may_total'], array('controller' => 'reportes', 'action' => 'por_estados', 'buscar' => '1', 'monto' => 'mayores', 'estado' => '4', 'estado_envio' => 'P')); ?></b></td>
                <td class="numerico"><b><?php echo $total['total']; ?></b></td>
            </tr>
        </table>
    </div>
    <?php } ?>
</div>

<script type="text/javascript">
    //<![CDATA[
    function getURL () {
        url = '<?php echo $this->Html->url(array('controller' => 'reportes', 'action' => 'por_funcionarios')); ?>';
        url += '/rol:' + $('#ReportesRol').val();
        
        return url;
    }

    $('#ReportesPorFuncionariosForm').submit(function() {
        var url = getURL();
        window.location = url;
    });
    
    //]]>
</script>

<div class="buttons right">
    <?php $url = $this->Html->url(null, true); ?>
    <?php echo $this->Html->link($this->Html->image('botones/imprimir.png') . ' Imprimir', 'javascript:void(0);', array('onclick' => 'javascript:ventana(\'' . $url . '/pdf:imprimir\', 960, 640)', 'escape' => false)); ?>
    <?php echo $this->Html->link($this->Html->image('botones/pdf.png') . ' Descargar', 'javascript:void(0);', array('onclick' => 'javascript:ventana(\'' . $url . '/pdf:descargar\', 960, 640)', 'escape' => false)); ?>
</div>