<div class="motivos form">
    <?php echo $this->Form->create('Motivo'); ?>
    <fieldset>
        <legend><?php echo __('Nuevo Motivo'); ?></legend>
        <?php
        echo $this->Form->input('nombre', array('size' => 30));
        echo $this->Form->input('descripcion', array('label' => 'Descripción'));
        ?>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<div class="actions">
    <h3><?php echo __('Acciones'); ?></h3>
    <ul>
        <li><?php echo $this->Html->link(__('Listar Motivos'), array('action' => 'index')); ?></li>
    </ul>
</div>
