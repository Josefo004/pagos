<div class="motivos related">
    <h2><?php echo __('Motivos'); ?></h2>
    <?php
    echo $this->Form->create('Borrar', array('id' => 'frm_borrar'));
    echo $this->Form->end();
    ?>
    <div class="buttons left">
        <?php $url = $this->Html->url(array('controller' => 'motivos', 'action' => 'index'), true); ?>
        <?php echo $this->Html->link($this->Html->image('botones/nuevo.png') . ' Nuevo', array('action' => 'nuevo'), array('escape' => false)); ?>
        <?php echo $this->Html->link($this->Html->image('botones/editar.png') . ' Editar', 'javascript:void(0);', array('onclick' => 'javascript:action(\'' . $url . '\', \'editar\', id_registro, null)', 'escape' => false)); ?>
        <?php echo $this->Html->link($this->Html->image('botones/borrar.png') . ' Borrar', 'javascript:void(0);', array('onclick' => 'javascript:action(\'' . $url . '\', \'eliminar\', id_registro, \'¿Está seguro de borrar el Motivo seleccionado?\');', 'escape' => false)); ?>
    </div>
    
    <div class="buttons right">
        <?php echo $this->Html->link($this->Html->image('botones/devolver.png') . ' Volver al Panel', array('controller' => 'pages', 'action' => 'admin'), array('escape' => false)); ?>
    </div>
    
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th>#</th>
            <th><?php echo $this->Paginator->sort('nombre'); ?></th>
            <th><?php echo $this->Paginator->sort('descripcion'); ?></th>
        </tr>
        <?php
        $i = (intval($params['page']) - 1) * 20;
        foreach ($motivos as $motivo):
            $i++;
            ?>
            <tr onclick="javascript:seleccionar(this, <?php echo $motivo['Motivo']['id']; ?>);">
                <td><?php echo $i; ?>&nbsp;</td>
                <td><?php echo h($motivo['Motivo']['nombre']); ?>&nbsp;</td>
                <td><?php echo h($motivo['Motivo']['descripcion']); ?>&nbsp;</td>
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
