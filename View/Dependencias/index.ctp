<div class="dependencias related">
    <h2><?php echo __('Dependencias'); ?></h2>
    
    <?php
    echo $this->Form->create('Borrar', array('id' => 'frm_borrar'));
    echo $this->Form->end();
    ?>
    <div class="buttons left">
        <?php $url = $this->Html->url(array('controller' => 'dependencias', 'action' => 'index'), true); ?>
        <?php echo $this->Html->link($this->Html->image('botones/nuevo.png') . ' Nuevo', array('action' => 'nuevo'), array('escape' => false)); ?>
        <?php echo $this->Html->link($this->Html->image('botones/ver.png') . ' Ver', 'javascript:void(0);', array('onclick' => 'javascript:action(\'' . $url . '\', \'ver\', id_registro, null)', 'escape' => false)); ?>
        <?php echo $this->Html->link($this->Html->image('botones/editar.png') . ' Editar', 'javascript:void(0);', array('onclick' => 'javascript:action(\'' . $url . '\', \'editar\', id_registro, null)', 'escape' => false)); ?>
        <?php echo $this->Html->link($this->Html->image('botones/borrar.png') . ' Borrar', 'javascript:void(0);', array('onclick' => 'javascript:action(\'' . $url . '\', \'eliminar\', id_registro, \'¿Está seguro de borrar la Dependencia seleccionada?\');', 'escape' => false)); ?>
    </div>
    
    <div class="buttons right">
        <?php echo $this->Html->link($this->Html->image('botones/devolver.png') . ' Volver al Panel', array('controller' => 'pages', 'action' => 'admin'), array('escape' => false)); ?>
    </div>
    
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th>#</th>
            <th><?php echo $this->Paginator->sort('nombre'); ?></th>
            <th><?php echo $this->Paginator->sort('sigla'); ?></th>
            <th><?php echo $this->Paginator->sort('TipoDependencia.nombre', 'Tipo'); ?></th>
        </tr>
        <?php 
            $i = (intval($params['page']) - 1) * 20;
            foreach ($dependencias as $dependencia): 
                $i++;
            ?>
            <tr onclick="javascript:seleccionar(this, <?php echo $dependencia['Dependencia']['id']; ?>);">
                <td><?php echo $i; ?>&nbsp;</td>
                <td><?php echo h($dependencia['Dependencia']['nombre']); ?>&nbsp;</td>
                <td><?php echo h($dependencia['Dependencia']['sigla']); ?>&nbsp;</td>
                <td><?php echo h($dependencia['TipoDependencia']['nombre']); ?>&nbsp;</td>
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