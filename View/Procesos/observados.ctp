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

$rol = AuthComponent::user('rol');
?>
<?php echo $this->element('menu_' . $rol); ?>
<div class="procesos view">
    <h2><?php echo __('Listado de Procesos de Pago'); ?></h2>
    
    <?php echo $this->element('menu_listado'); ?>
    
    <div class="busqueda">
        <?php
        echo $this->Form->create('Borrar', array('id' => 'frm_borrar'));
        echo $this->Form->end();
        ?>

        <?php 
        echo $this->Form->create('Procesos', array('action' => 'index', 'onsubmit' => 'return false;'));
        $buscar = $this->Form->input('buscar', array('label' => false, 'div' => false, 'value' => $params['buscar'], 'size' => 15, 'maxlength' => 20));
        $options = array('cite' => 'Cite', 'nro_proceso' => 'Nro Proceso', 'nro_preventivo' => 'Nro Preventivo', 'monto' => 'Monto', 'referencia' => 'Referencia', 'beneficiario_documento' => 'CI/NIT Beneficiario', 'beneficiario_nombre' => 'Beneficiario');
        echo $this->Form->input('filtro', array('options' => $options, 'label' => 'Búsqueda', 'after' => '&nbsp;' . $buscar, 'default' => $params['filtro']));
        echo $this->Form->end(__('Buscar', true)); 
        ?>
    </div>
    <div class="filtros right">
        <?php
        echo $this->Form->create('Filtros', array('action' => 'index', 'onsubmit' => 'return false;'));
        echo $this->Form->input('dependencia', array('options' => $dependencias, 'label' => 'Dependencia', 'default' => $params['dependencia'], 'empty' => 'Todas'));
        echo $this->Form->input('motivo', array('options' => $motivos, 'label' => 'Motivo', 'default' => $params['motivo'], 'empty' => 'Todos'));
        echo $this->Form->input('estado', array('type' => 'hidden', 'value' => $params['estado']));
        echo $this->Form->end(); 
        ?>
    </div>
    
    <div class="clear"></div>
    
    <div class="buttons left">
        <?php $url = $this->Html->url(array('controller' => 'procesos', 'action' => 'index'), true); ?>
        <?php echo $this->Html->link($this->Html->image('botones/ver.png') . ' Ver', 'javascript:void(0);', array('onclick' => 'javascript:action(\'' . $url . '\', \'ver\', id_registro, null)', 'escape' => false)); ?>
        <?php if (($params['estado'] == '3') && (AuthComponent::user('rol') == 'analistas')) : ?>
        <?php echo $this->Html->link($this->Html->image('botones/nota.png') . ' Modificar Observación', 'javascript:void(0);', array('onclick' => 'javascript:action(\'' . $url . '\', \'editar_obs\', id_registro, null)', 'escape' => false)); ?>
        <?php echo $this->Html->link($this->Html->image('botones/borrar.png') . ' Borrar Observación', 'javascript:void(0);', array('onclick' => 'javascript:action(\'' . $url . '\', \'borrar_obs\', id_registro, \'¿Está seguro de borrar la Observación del Proceso seleccionado?\nNota.- El Proceso pasará a sus Recepcionados\');', 'escape' => false)); ?>
        <?php endif; ?>
    </div>
    <div class="buttons right">
        <?php echo $this->Html->link($this->Html->image('botones/estado.png') . __(' Pasar con Obs. [Administradores]'), 'javascript:void(0);', array('escape' => false, 'onclick' => 'enviar_procesos(4);')) ;?>
    </div>
    
    <?php echo $this->Form->create('Procesos', array('action' => 'enviar_procesos')); ?>
    <?php echo $this->Form->input('estado', array('type' => 'hidden')); ?>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th width="2%"><?php echo $this->Form->checkbox('marcar'); ?></th>
            <th width="40"><?php echo $this->Paginator->sort('nro_proceso', 'Nro'); ?></th>
            <th><?php echo $this->Paginator->sort('cite'); ?></th>
            <th><?php echo __('Fecha envío'); ?></th>
            <th><?php echo __('Fecha recepción'); ?></th>
            <th><?php echo $this->Paginator->sort('motivo_id'); ?></th>
            <th><?php echo $this->Paginator->sort('beneficiario_nombre', 'Beneficiario'); ?></th>
            <th><?php echo $this->Paginator->sort('monto'); ?></th>
        </tr>
        <?php
        $i = (intval($params['page']) - 1) * 20;
        foreach ($procesos as $proceso):
            $i++;

            $reingreso = '';
            if (!empty($proceso['ProcesosEstado']['reingreso'])) {
                $reingreso = 'reingreso';
            }
            ?>
            <tr onclick="javascript:seleccionar(this, <?php echo $proceso['Proceso']['id']; ?>);" class="<?php echo $reingreso; ?>">
                <td><?php echo $this->Form->checkbox('publicar', array('name' => 'procesos[]', 'value' => $proceso['Proceso']['id'])); ?></td>
                <td><?php echo h($proceso['Proceso']['nro_proceso']); ?>&nbsp;</td>
                <td><?php echo h($proceso['Proceso']['cite']); ?>&nbsp;</td>
                <td><?php echo empty($proceso['ProcesosEstado']['fecha_envio'])?'No recepcionado':fecha($proceso['ProcesosEstado']['fecha_envio'], true); ?>&nbsp;</td>
                <td><?php echo empty($proceso['ProcesosEstado']['fecha_recepcion'])?'No recepcionado':fecha($proceso['ProcesosEstado']['fecha_recepcion'], true); ?>&nbsp;</td>
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
    <br />
    <div style="float: left;background: #FFA; width: 15px;border: 1px solid #ccc;">&nbsp;</div>
    <div>&nbsp;&nbsp;Estos procesos son denonimados reingresados debido a que fueron enviados por los administradores</div>
</div>
<script type="text/javascript">
    //<![CDATA[
    function getURL () {
        url = $('#ProcesosIndexForm').attr('action') + '/observados'
            + '/mayor:<?php echo $params['mayor']; ?>'
            + '/filtro:' + $('#ProcesosFiltro').val()
            + '/buscar:' + $('#ProcesosBuscar').val()
            + '/dependencia:' + $('#FiltrosDependencia').val()
            + '/motivo:' + $('#FiltrosMotivo').val()
            + '/estado:' + $('#FiltrosEstado').val();
        return url;
    }

    $('#ProcesosIndexForm').submit(function() {
        window.location = getURL();
    });
    $('#FiltrosDependencia').change(function() {
        window.location = getURL();
    });
    $('#FiltrosMotivo').change(function() {
        window.location = getURL();
    });
    //]]>
</script>

<script type="text/javascript">
    //<![CDATA[
    $('#ProcesosMarcar').click(function() {
        marcar_checks('ProcesosForm');
    });

    function enviar_procesos (estado) {
        if (validar_checks()) {
            $('#ProcesosEnviarProcesosForm').attr('action', '<?php echo $this->Html->url(array('controller' => 'procesos', 'action' => 'enviar_procesos'), false) ?>/' + estado);
            $('#ProcesosEstado').attr('value', <?php echo $params['estado']; ?>);
            $('#ProcesosEnviarProcesosForm').submit();
        }
    }
    
    function recibir_procesos (estado) {
        if (validar_checks()) {
            $('#ProcesosEnviarProcesosForm').attr('action', '<?php echo $this->Html->url(array('controller' => 'procesos', 'action' => 'recibir_procesos'), false) ?>');
            $('#ProcesosEstado').attr('value', estado);
            $('#ProcesosEnviarProcesosForm').submit();
        }
    }
    //]]>
</script>