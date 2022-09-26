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
        <?php echo $this->Html->link($this->Html->image('botones/borrar.png') . ' Borrar', 'javascript:void(0);', array('onclick' => 'javascript:action(\'' . $url . '\', \'eliminar\', id_registro, \'¿Está seguro de eliminar el Proceso seleccionada?\');', 'escape' => false)); ?>
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
                        break;
                    case '11':
                        echo $this->Html->link($this->Html->image('botones/recibir.png') . __(' Recepcionar'), 'javascript:void(0);', array('escape' => false, 'onclick' => 'recibir_procesos(11);'));
                        break;
                }
                break;
            case 'analistas':
                switch ($params['estado']) {
                    case '3':
                        echo $this->Html->link($this->Html->image('botones/recibir.png') . __(' Recepcionar'), 'javascript:void(0);', array('escape' => false, 'onclick' => 'recibir_procesos(3);'));
                        break;
                }
                break;
            case 'administradores':
                switch ($params['estado']) {
                    case '4':
                        echo $this->Html->link($this->Html->image('botones/recibir.png') . __(' Recepcionar'), 'javascript:void(0);', array('escape' => false, 'onclick' => 'recibir_procesos(4);'));
                        break;
                }
                break;
            case 'contabilidad':
                switch ($params['estado']) {
                    case '2':
                        echo $this->Html->link($this->Html->image('botones/recibir.png') . __(' Recepcionar'), 'javascript:void(0);', array('escape' => false, 'onclick' => 'recibir_procesos(2);'));
                        break;
                    case '5':
                        echo $this->Html->link($this->Html->image('botones/recibir.png') . __(' Recepcionar'), 'javascript:void(0);', array('escape' => false, 'onclick' => 'recibir_procesos(5);'));
                        break;
                    case '8':
                        if ($params['mayor'] == 'no') {
                            echo $this->Html->link($this->Html->image('botones/recibir.png') . __(' Recepcionar'), 'javascript:void(0);', array('escape' => false, 'onclick' => 'recibir_procesos(8);'));
                        }
                        break;
                }
                break;
            case 'tesoreria1':
                switch ($params['estado']) {
                    case '6':
                        if ($params['mayor'] == 'si') {
                            echo $this->Html->link($this->Html->image('botones/recibir.png') . __(' Recepcionar'), 'javascript:void(0);', array('escape' => false, 'onclick' => 'recibir_procesos(6);'));
                        } else {
                            echo $this->Html->link($this->Html->image('botones/recibir.png') . __(' Recepcionar'), 'javascript:void(0);', array('escape' => false, 'onclick' => 'recibir_procesos(6);'));
                        }
                        break;
                }
                break;
            case 'tesoreria2':
                switch ($params['estado']) {
                    case '14':
                        if ($params['mayor'] == 'si') {
                            echo $this->Html->link($this->Html->image('botones/recibir.png') . __(' Recepcionar'), 'javascript:void(0);', array('escape' => false, 'onclick' => 'recibir_procesos(14);'));
                        } else {
                            echo $this->Html->link($this->Html->image('botones/recibir.png') . __(' Recepcionar'), 'javascript:void(0);', array('escape' => false, 'onclick' => 'recibir_procesos(14);'));
                        }
                        break;
                    case '7':
                        echo $this->Html->link($this->Html->image('botones/recibir.png') . __(' Recepcionar'), 'javascript:void(0);', array('escape' => false, 'onclick' => 'recibir_procesos(7);'));
                        break;
                }
                break;
            case 'economia':
                switch ($params['estado']) {
                    case '10':
                        echo $this->Html->link($this->Html->image('botones/recibir.png') . __(' Recepcionar'), 'javascript:void(0);', array('escape' => false, 'onclick' => 'recibir_procesos(10);'));
                        break;
                }
                break;
            case 'caja':
                switch ($params['estado']) {
                    case '12':
                        echo $this->Html->link($this->Html->image('botones/recibir.png') . __(' Recepcionar'), 'javascript:void(0);', array('escape' => false, 'onclick' => 'recibir_procesos(12);'));
                        break;
                }
                break;
            case 'archivo':
                switch ($params['estado']) {
                    case '13':
                        echo $this->Html->link($this->Html->image('botones/archivar.png') . __(' Archivar'), 'javascript:void(0);', array('escape' => false, 'onclick' => 'recibir_procesos(13);'));
                        break;
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
            if (($rol == 'finanzas') && (($params['estado'] == 1) || ($params['estado'] == 9) || ($params['estado'] == 11))) {
                $checkbox = true;
            } elseif (($rol == 'analistas') && ($params['estado'] == 3)) {
                $checkbox = true;
            } elseif (($rol == 'administradores') && ($params['estado'] == 4)) {
                $checkbox = true;
            } elseif (($rol == 'contabilidad') && (($params['estado'] == 2) || ($params['estado'] == 5) || ($params['estado'] == 8))) {
                $checkbox = true;
            } elseif (($rol == 'tesoreria1') && ($params['estado'] == 6)) {
                $checkbox = true;
            } elseif (($rol == 'tesoreria2') && ($params['estado'] == 7)) {
                $checkbox = true;
            } elseif (($rol == 'economia') && ($params['estado'] == 10)) {
                $checkbox = true;
            } elseif (($rol == 'caja') && ($params['estado'] == 12)) {
                $checkbox = true;
            } elseif (($rol == 'archivo') && ($params['estado'] == 13)) {
                $checkbox = true;
            }
            ?>
            <?php if ($checkbox) : ?>
            <th width="2%"><?php echo $this->Form->checkbox('marcar'); ?></th>
            <?php endif; ?>
            <th width="40"><?php echo $this->Paginator->sort('nro_proceso', 'Nro'); ?></th>
            <?php if ((($rol == 'caja') && ($params['estado'] == 12)) || (($rol == 'archivo') && ($params['estado'] == 13))) : ?>
            <th width="40"><?php echo $this->Paginator->sort('nro_preventivo', 'Preventivo'); ?></th>
            <?php endif; ?>
            <th><?php echo $this->Paginator->sort('cite'); ?></th>
            <?php if($params['estado'] != 1) : ?>
            <th><?php echo __('Fecha envío'); ?></th>
            <?php endif; ?>
            <th><?php echo $this->Paginator->sort('motivo_id'); ?></th>
            <th><?php echo $this->Paginator->sort('beneficiario_nombre', 'Beneficiario'); ?></th>
            <th><?php echo $this->Paginator->sort('monto'); ?></th>
        </tr>
        <?php
        $i = (intval($params['page']) - 1) * 20;
        foreach ($procesos as $proceso):
            $i++;
        
            /** Calcular los retrasados */
            $retrasado = '';
            if (!empty($proceso['ProcesosEstado']['fecha_envio'])) {
                $fecha_envio = substr($proceso['ProcesosEstado']['fecha_envio'], 0, 19);
                $fecha_envio = explode('.', $fecha_envio);
                $fecha_envio = explode(' ', $fecha_envio[0]);
                $fecha = explode('-', $fecha_envio[0]);
                $hora = explode(':', $fecha_envio[1]);
                $fecha_envio = mktime($hora[0], $hora[1], $hora[2], $fecha[1], $fecha[2], $fecha[0]);
                if ($fecha_envio + Configure::Read('App.dias_retraso') * 24 * 60 * 60 < time()) {
                    $retrasado = 'retrasado';
                }
            }
        
            $reingreso = '';
            if (!empty($proceso['ProcesosEstado']['reingreso'])) {
                $reingreso = 'reingreso';
            }
            ?>
            <tr onclick="javascript:seleccionar(this, <?php echo $proceso['Proceso']['id']; ?>);" class="<?php echo $reingreso . ' ' . $retrasado; ?>">
                <?php 
                if ($checkbox) : 
                ?>
                <td><?php echo $this->Form->checkbox('publicar', array('name' => 'procesos[]', 'value' => $proceso['Proceso']['id'])); ?></td>
                <?php endif; ?>
                <td><?php echo h($proceso['Proceso']['nro_proceso']); ?></td>
                <?php if ((($rol == 'caja') && ($params['estado'] == 12)) || (($rol == 'archivo') && ($params['estado'] == 13))) : ?>
                <td><?php echo h($proceso['Proceso']['nro_preventivo']); ?></td>
                <?php endif; ?>
                <td><?php echo h($proceso['Proceso']['cite']); ?></td>
                <?php if($params['estado'] != 1) : ?>
                <td><?php echo fecha($proceso['ProcesosEstado']['fecha_envio'], true); ?>&nbsp;</td>
                <?php endif; ?>
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
    <?php if (($params['estado'] == 3) && (AuthComponent::user('rol') == 'analistas')) : ?>
    <br />
    <div style="float: left;background: #FFA; width: 15px;border: 1px solid #ccc;">&nbsp;</div>
    <div>&nbsp;&nbsp;Estos procesos son denonimados reingresados debido a que fueron enviados por los administradores</div>
    <?php endif; ?>
    <br />
    <div style="float: left;width: 15px;border: 1px solid #ccc;border: 1px dashed #EE3322;">&nbsp;</div>
    <div>&nbsp;&nbsp;Estos procesos son considerados retrasados por llevar m&aacute;s de <b>5 d&iacute;as en espera</b></div>
</div>
<script type="text/javascript">
    //<![CDATA[
    function getURL () {
        url = $('#ProcesosIndexForm').attr('action') + '/pendientes'
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
    <?php if (($rol == 'finanzas') && ($params['estado'] == 1)): ?>
    function imprimir(url, id) {
        if (id) {
            ventana(url + '/' + id, 800, 600);
        } else {
            alert('Debe seleccionar al menos un registro');
        }
    }
    <?php endif; ?>
    
    <?php if (!empty($params['imprimir'])): ?>
    ventana('<?php echo $this->Html->url(array('controller' => 'procesos', 'action' => 'pdf', $params['imprimir']), false); ?>', 800, 600);
    <?php endif; ?>
    //]]>
</script>
