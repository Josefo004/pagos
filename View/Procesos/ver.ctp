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
?>
<div class="procesos view">
    <h2><?php echo __('Proceso'); ?></h2>
    <dl>
        <dt><?php echo __('ID'); ?></dt>
        <dd>
            <b><?php echo h($proceso['Proceso']['id']); ?></b>
            &nbsp;
        </dd>
        <dt><?php echo __('N° de Proceso'); ?></dt>
        <dd>
            <b><?php echo h($proceso['Proceso']['nro_proceso']); ?></b>
            &nbsp;
        </dd>
        <dt><?php echo __('Cite'); ?></dt>
        <dd>
            <?php echo h($proceso['Proceso']['cite']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('N° de Preventivo'); ?></dt>
        <dd>
            <?php echo h($proceso['Proceso']['nro_preventivo']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Fecha de Ingreso'); ?></dt>
        <dd>
            <?php echo fecha($proceso['Proceso']['fecha_emision']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Referencia'); ?></dt>
        <dd>
            <?php echo empty($proceso['Proceso']['referencia'])?'<i>Sin especificar</i>':$proceso['Proceso']['referencia']; ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Descripción de documentos'); ?></dt>
        <dd>
            <?php echo empty($proceso['Proceso']['descripcion_doc'])?'<i>Sin especificar</i>':$proceso['Proceso']['descripcion_doc']; ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Autorizado por'); ?></dt>
        <dd>
            <?php echo h($proceso['Dependencia']['nombre']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('CI/NIT Beneficiario'); ?></dt>
        <dd>
            <?php echo h($proceso['Proceso']['beneficiario_documento']) ; ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Beneficiario'); ?></dt>
        <dd>
            <?php echo h($proceso['Proceso']['beneficiario_nombre']) ; ?>
            &nbsp;
            (<?php echo h($proceso['Proceso']['beneficiario_documento']) ; ?>)
        </dd>
        <dt><?php echo __('Motivo'); ?></dt>
        <dd>
            <?php echo h($proceso['Motivo']['nombre']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Monto'); ?></dt>
        <dd>
            <?php echo h($proceso['Proceso']['monto']); ?>
            &nbsp;
        </dd>
    </dl>
</div>
<div class="actions">
    <h3><?php echo __('Acciones'); ?></h3>
    <ul>
        <?php if (AuthComponent::user('nick') != null) : ?>
        <li><?php echo $this->Html->link(__('Listar Procesos'), $referer); ?></li>
        <?php else: ?>
        <li><?php echo $this->Html->link(__('Volver a resultados'), $referer); ?></li>
        <?php endif; ?>
        <?php if (AuthComponent::user('rol') == 'finanzas') : ?>
        <li><?php echo $this->Html->link(__('Nuevo Proceso'), array('action' => 'nuevo')); ?></li>
        <?php endif; ?>
    </ul>
</div>
<?php echo $this->Form->create('Proceso', array('id' => 'frm_borrar')); ?>
<?php echo $this->Form->end(); ?>
<div class="related">
    <h3><?php echo __('Seguimiento del Proceso'); ?></h3>
    <?php if (!empty($proceso['Estado'])): ?>
        <table cellpadding = "0" cellspacing = "0" class="normal">
            <tr>
                <th width="2%">#</th>
                <th width="15%"><?php echo __('Estado'); ?></th>
                <th width="35%"><?php echo __('Descripción'); ?></th>
                <th width="25%"><?php echo __('Envío'); ?></th>
                <th width="25%"><?php echo __('Recepción'); ?></th>
                <th>&nbsp;</th>
            </tr>
            <?php
            $i = 0; $j = 0;
            $total = count($estados);
            foreach ($estados as $estado):
                $i++;
                ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td>
                        <?php 
                        echo $estado['Estado']['nombre']; 
                        if (!empty($estado['Observacione']['id'])) {
                            $j++;
                            echo " <sup><a href=\"#obs_$j\">$j</a></sup>";
                        }
                        ?>
                    </td>
                    <td><?php echo $estado['Estado']['descripcion']; ?></td>
                    <td>
                        <?php if ($estado['Estado']['id'] != 1) : ?>
                        <?php echo fecha($estado['ProcesosEstado']['fecha_envio'], true); ?><br />
                        <?php echo $estado['UsuarioEnvio']['nombres'] . ' ' . $estado['UsuarioEnvio']['apellidos']; ?>
                        <?php else: ?>
                        -------------------
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if(!empty($estado['ProcesosEstado']['fecha_recepcion'])): ?>
                        <?php echo fecha($estado['ProcesosEstado']['fecha_recepcion'], true); ?><br />
                        <?php echo $estado['UsuarioRecepcion']['nombres'] . ' ' . $estado['UsuarioRecepcion']['apellidos']; ?>
                        <?php else: ?>
                        No recepcionado a&uacute;n
                        <?php endif; ?>
                    </td>
                    <td class="buttons">
                        <?php 
                        if ((AuthComponent::user('rol') == 'admin') && ($total == $i) && ($i != 1)) {
//                            echo $this->Html->link($this->Html->image('botones/borrar.png'), array('controller' => 'procesos', 'action' => 'eliminar_estado', $estado['ProcesosEstado']['id'])); 
                            echo $this->Html->link($this->Html->image('botones/borrar.png'), 'javascript:void(0);', array('onclick' => 'javascript:action(\'' . $this->Html->url(array('controller' => 'procesos', 'action' => 'index'), true) . '\', \'eliminar_estado\', ' . $estado['ProcesosEstado']['id'] . ', \'¿Está seguro de borrar el Estado del Proceso seleccionado?\');', 'escape' => false));
                        }
                        ?>&nbsp;&nbsp;
                    </td>
                </tr>
        <?php endforeach; ?>
        </table>
    <?php endif; ?>
</div>

<div class="related">
    <h3><?php echo __('Observaciones'); ?></h3>
    <?php if (!empty($proceso['Observacione'])): ?>
        <table cellpadding = "0" cellspacing = "0">
            <tr>
                <th width="2%">#</th>
                <th width="10%"><?php echo __('Fecha'); ?></th>
                <th width="60%"><?php echo __('Descripción'); ?></th>
                <th width="30%"><?php echo __('Analista'); ?></th>
            </tr>
            <?php
            $i = 0;
            foreach ($observaciones as $observacion):
                $i++;
                ?>
            <tr>
                <td><?php echo $i; ?></td>
                <td><?php echo fecha($observacion['Observacione']['modified'], true); ?></td>
                <td><a name="obs_<?php echo $i; ?>"></a><?php echo $observacion['Observacione']['descripcion']; ?></td>
                <td><?php echo $observacion['ServidoresPublico']['nombres'] . ' ' . $observacion['ServidoresPublico']['apellidos']; ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
    No existen observaciones registradas
    <?php endif; ?>
</div>