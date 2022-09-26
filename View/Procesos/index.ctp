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
        if (($rol == 'finanzas') && ($params['estado'] == 1)){
            echo $this->Form->create('Borrar', array('id' => 'frm_borrar'));
            echo $this->Form->end();
        }
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
        if (AuthComponent::user('rol') != 'administradores') {
            echo $this->Form->input('dependencia', array('options' => $dependencias, 'label' => 'Dependencia', 'default' => $params['dependencia'], 'empty' => 'Todas'));
        }
        echo $this->Form->input('motivo', array('options' => $motivos, 'label' => 'Motivo', 'default' => $params['motivo'], 'empty' => 'Todos'));
        echo $this->Form->input('estado', array('type' => 'hidden', 'value' => $params['estado']));
        echo $this->Form->end(); 
        ?>
    </div>
    
    <div class="clear"></div>
    
    <div class="buttons left">
        <?php $url = $this->Html->url(array('controller' => 'procesos', 'action' => 'index'), true); ?>
        <?php if (($rol == 'finanzas') && ($params['estado'] == 1)): ?>
        <?php echo $this->Html->link($this->Html->image('botones/nuevo.png') . ' Nuevo', array('action' => 'nuevo'), array('escape' => false)); ?>
        <?php echo $this->Html->link($this->Html->image('botones/editar.png') . ' Editar', 'javascript:void(0);', array('onclick' => 'javascript:action(\'' . $url . '\', \'editar\', id_registro, null)', 'escape' => false)); ?>
        <?php echo $this->Html->link($this->Html->image('botones/borrar.png') . ' Borrar', 'javascript:void(0);', array('onclick' => 'javascript:action(\'' . $url . '\', \'eliminar\', id_registro, \'¿Está seguro de borrar el Proceso seleccionado?\');', 'escape' => false)); ?>
        <?php endif; ?>
        <?php echo $this->Html->link($this->Html->image('botones/ver.png') . ' Ver', 'javascript:void(0);', array('onclick' => 'javascript:action(\'' . $url . '\', \'ver\', id_registro, null)', 'escape' => false)); ?>
        <?php if (($rol == 'finanzas') && ($params['estado'] == 1)): ?>
        <?php echo $this->Html->link($this->Html->image('botones/imprimir.png') . ' Imprimir', 'javascript:void(0);', array('onclick' => 'javascript:imprimir(\'' . $url . '/pdf/\', id_registro);', 'escape' => false)); ?>
        <?php endif; ?>
    </div>
    <div class="buttons right">
        <?php 
        switch ($rol) {
            case 'finanzas':
                switch ($params['estado']) {
                    case '1':
                        echo $this->Html->link($this->Html->image('botones/estado.png') . __(' Pasar a Revisión [Contabilidad]'), 'javascript:void(0);', array('escape' => false, 'onclick' => 'enviar_procesos(2);'));
                        break;
                    case '9':
                        echo $this->Html->link($this->Html->image('botones/recibir.png') . __(' Recepcionar'), 'javascript:void(0);', array('escape' => false, 'onclick' => 'recibir_procesos(9);'));
                        echo $this->Html->link($this->Html->image('botones/estado.png') . __(' Pasar a Firmas [Stria. Economía]'), 'javascript:void(0);', array('escape' => false, 'onclick' => 'enviar_procesos(10);'));
                        break;
                    case '11':
                        echo $this->Html->link($this->Html->image('botones/recibir.png') . __(' Recepcionar'), 'javascript:void(0);', array('escape' => false, 'onclick' => 'recibir_procesos(11);'));
                        echo $this->Html->link($this->Html->image('botones/estado.png') . __(' Pasar a Entregar [Caja]'), 'javascript:void(0);', array('escape' => false, 'onclick' => 'enviar_procesos(12);'));
                        break;
                }
                break;
            case 'contabilidad':
                if ($params['estado'] == '3') {
                    echo $this->Html->link($this->Html->image('botones/devolver.png') . __(' Devolver a Recepcionados [Contabilidad]'), 'javascript:void(0);', array('escape' => false, 'onclick' => 'devolver_contabilidad(2);'));
                }
                break;
        }
        ?>
    </div>
    
    <?php echo $this->Form->create('Procesos', array('action' => 'enviar_procesos')); ?>
    <?php echo $this->Form->input('estado', array('type' => 'hidden')); ?>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <?php 
            $checkbox = false;
            if (($rol == 'finanzas') && ($params['estado'] == 1)) {
                $checkbox = true;
            } 
            if (($rol == 'contabilidad') && ($params['estado'] == 3)) {
                $checkbox = true;
            } 
            ?>
            <?php if ($checkbox) : ?>
            <th width="2%"><?php echo $this->Form->checkbox('marcar'); ?></th>
            <?php endif; ?>
            <th width="40"><?php echo $this->Paginator->sort('nro_proceso', 'Nro'); ?></th>
            <th><?php echo $this->Paginator->sort('cite'); ?></th>
            <?php if($params['estado'] != 1) : ?>
            <th><?php echo $this->Paginator->sort('ProcesosEstado.fecha_envio', 'Fecha envío'); ?></th>
            <?php endif; ?>
            <th><?php echo $this->Paginator->sort('ProcesosEstado.fecha_recepcion', 'Fecha recepción'); ?></th>
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
                <?php 
                if ($checkbox) : 
                ?>
                <td><?php echo $this->Form->checkbox('publicar', array('name' => 'procesos[]', 'value' => $proceso['Proceso']['id'], 'hiddenField' => false)); ?></td>
                <?php endif; ?>
                <td><?php echo h($proceso['Proceso']['nro_proceso']); ?>&nbsp;</td>
                <td><?php echo h($proceso['Proceso']['cite']); ?>&nbsp;</td>
                <?php if($params['estado'] != 1) : ?>
                <td><?php echo fecha($proceso['ProcesosEstado']['fecha_envio'], true); ?></td>
                <?php endif; ?>
                <td><?php echo empty($proceso['ProcesosEstado']['fecha_recepcion'])?'No recepcionado':fecha($proceso['ProcesosEstado']['fecha_recepcion'], true); ?></td>
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
</div>
<script type="text/javascript">
    //<![CDATA[
    function getURL () {
        url = $('#ProcesosIndexForm').attr('action') + '/index'
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
    function publicar () {
        if(validar_checks()) {
             if (confirm('Los procesos seleccionados procederán a ser cambiados de estado. \n¿Está seguro de continuar?.')) {
                $('#SolicitudesPublicarForm').submit();
             }
        }
    }

    function enviar_procesos (estado) {
        if (validar_checks()) {
            $('#ProcesosEnviarProcesosForm').attr('action', '<?php echo $this->Html->url(array('controller' => 'procesos', 'action' => 'enviar_procesos'), false) ?>/' + estado);
            $('#ProcesosEstado').attr('value', <?php echo $params['estado']; ?>);
            $('#ProcesosEnviarProcesosForm').submit();
        }
    }
    
    function devolver_contabilidad () {
        if (validar_checks()) {
            $('#ProcesosEnviarProcesosForm').attr('action', '<?php echo $this->Html->url(array('controller' => 'procesos', 'action' => 'devolver_contabilidad'), false) ?>');
            $('#ProcesosEnviarProcesosForm').submit();
        }
    }
    
    function imprimir(url, id) {
        if (id) {
            ventana(url + '/' + id, 800, 600);
        } else {
            alert('Debe seleccionar al menos un registro');
        }
    }
    <?php if (!empty($params['imprimir'])): ?>
    ventana('<?php echo $this->Html->url(array('controller' => 'procesos', 'action' => 'pdf', $params['imprimir'])); ?>', 800, 600, false);
    <?php endif; ?>
    //]]>
</script>