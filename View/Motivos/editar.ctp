<div class="motivos form">
    <?php echo $this->Form->create('Motivo'); ?>
    <fieldset>
        <legend><?php echo __('Editar Motivo'); ?></legend>
        <?php
        echo $this->Form->input('id');
        echo $this->Form->input('nombre', array('size' => 30));
        echo $this->Form->input('descripcion', array('label' => 'Descripción'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<div class="actions">
    <h3><?php echo __('Acciones'); ?></h3>
    <ul>
        <li><?php echo $this->Form->postLink(__('Eliminar'), array('action' => 'delete', $this->Form->value('Motivo.id')), null, __('¿Está seguro de eliminar el Motivo con ID # %s?', $this->Form->value('Motivo.id'))); ?></li>
        <li><?php echo $this->Html->link(__('Listar Motivos'), array('action' => 'index')); ?></li>
    </ul>
</div>
