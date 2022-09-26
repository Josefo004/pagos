<div class="dependencias view">
    <h2><?php echo __('Dependencia'); ?></h2>
    <dl>
        <dt><?php echo __('Id'); ?></dt>
        <dd>
            <?php echo h($dependencia['Dependencia']['id']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Nombre'); ?></dt>
        <dd>
            <?php echo h($dependencia['Dependencia']['nombre']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Sigla'); ?></dt>
        <dd>
            <?php echo h($dependencia['Dependencia']['sigla']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Descripción'); ?></dt>
        <dd>
            <?php echo h($dependencia['Dependencia']['descripcion']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Teléfono'); ?></dt>
        <dd>
            <?php echo h($dependencia['Dependencia']['telefono']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Dirección'); ?></dt>
        <dd>
            <?php echo h($dependencia['Dependencia']['direccion']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Correo Electrónico'); ?></dt>
        <dd>
            <?php echo h($dependencia['Dependencia']['correo_electronico']); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Dependencia padre'); ?></dt>
        <dd>
            <?php echo $this->Html->link($dependencia['SupDependencia']['sigla'], array('controller' => 'dependencias', 'action' => 'view', $dependencia['SupDependencia']['id'])); ?>
            &nbsp;
        </dd>
        <dt><?php echo __('Tipo de Dependencia'); ?></dt>
        <dd>
            <?php echo h($dependencia['TipoDependencia']['nombre']); ?>
            &nbsp;
        </dd>
    </dl>
</div>
<div class="actions">
    <h3><?php echo __('Acciones'); ?></h3>
    <ul>
        <li><?php echo $this->Html->link(__('Editar Dependencia'), array('action' => 'editar', $dependencia['Dependencia']['id'])); ?> </li>
        <li><?php echo $this->Form->postLink(__('Eliminar Dependencia'), array('action' => 'eliminar', $dependencia['Dependencia']['id']), null, __('¿Está seguro de eliminar la Dependencia con ID # %s?', $dependencia['Dependencia']['id'])); ?> </li>
        <li><?php echo $this->Html->link(__('Listar Dependencias'), array('action' => 'index')); ?> </li>
        <li><?php echo $this->Html->link(__('Nueva Dependencia'), array('action' => 'nuevo')); ?> </li>
    </ul>
</div>
<div class="related">
    <h3><?php echo __('Dependencias hijas'); ?></h3>
    
    <?php
    echo $this->Form->create('Borrar', array('id' => 'frm_borrar'));
    echo $this->Form->end();
    ?>
    <div class="buttons left">
        <?php $url = $this->Html->url(array('controller' => 'dependencias', 'action' => 'index'), true); ?>
        <?php echo $this->Html->link($this->Html->image('botones/ver.png') . ' Ver', 'javascript:void(0);', array('onclick' => 'javascript:action(\'' . $url . '\', \'ver\', id_registro, null)', 'escape' => false)); ?>
    </div>
    
    <div class="buttons right">
        <?php echo $this->Html->link($this->Html->image('botones/devolver.png') . ' Volver al listado', array('controller' => 'dependencias', 'action' => 'index'), array('escape' => false)); ?>
    </div>
    
    <?php if (!empty($dependencia['SubDependencia'])): ?>
        <table cellpadding = "0" cellspacing = "0">
            <tr>
                <th>#</th>
                <th><?php echo __('Nombre'); ?></th>
                <th><?php echo __('Sigla'); ?></th>
                <th><?php echo __('Teléfono'); ?></th>
                <th><?php echo __('Dirección'); ?></th>
            </tr>
            <?php
            $i = 0;
            foreach ($dependencia['SubDependencia'] as $subDependencia):
                $i++;
                ?>
                <tr>
                    <td><?php echo $i; ?></td>
                    <td><?php echo $subDependencia['nombre']; ?></td>
                    <td><?php echo $subDependencia['sigla']; ?></td>
                    <td><?php echo $subDependencia['telefono']; ?></td>
                    <td><?php echo $subDependencia['direccion']; ?></td>
                </tr>
        <?php endforeach; ?>
        </table>
<?php endif; ?>
</div>
