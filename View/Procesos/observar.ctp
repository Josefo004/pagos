<div class="procesos form">
    <?php echo $this->Form->create('Observacione'); ?>
    <fieldset>
        <legend><?php echo __('Nueva Observación'); ?></legend>
        <?php echo $this->Form->input('descripcion', array('label' => 'Descripción', 'class' => 'full')); ?>
    <?php echo $this->Form->end(__('Guardar')); ?>
    </fieldset>
</div>
<div class="actions">
    <h3><?php echo __('Acciones'); ?></h3>
    <ul>
        <li><?php echo $this->Html->link(__('Listar Procesos'), array('action' => 'index', 'mayor' => 'si', 'estado' => 3)); ?></li>
    </ul>
</div>