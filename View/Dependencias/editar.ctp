<div class="dependencias form">
    <?php echo $this->Form->create('Dependencia'); ?>
    <fieldset>
        <legend><?php echo __('Editar Dependencia'); ?></legend>
        <?php echo $this->Form->input('id'); ?>
        <div class="left" style="width: 64% !important;"><?php echo $this->Form->input('nombre', array('size' => 46)); ?></div>
        <div class="left"><?php echo $this->Form->input('sigla'); ?></div>
        <?php
        echo $this->Form->input('descripcion', array('label' => 'Descripción', 'rows' => 3, 'cols' => 70));
        ?>
        <div class="left"><?php echo $this->Form->input('telefono', array('label' => 'Teléfono')); ?></div>
        <div class="left"><?php echo $this->Form->input('direccion', array('label' => 'Dirección')); ?></div>
        <div class="left"><?php echo $this->Form->input('correo_electronico', array('label' => 'Correo electrónico')); ?></div>
        <?php echo $this->Form->input('dependencia_id', array('label' => 'Dependencia')); ?>
        <?php echo $this->Form->input('tipo_dependencia_id', array('label' => 'Tipo de Dependencia')); ?>
        <?php echo $this->Form->end(__('Guardar')); ?>
    </fieldset>
</div>
<div class="actions">
    <h3><?php echo __('Acciones'); ?></h3>
    <ul>
        <li><?php echo $this->Form->postLink(__('Eliminar'), array('action' => 'eliminar', $this->Form->value('Dependencia.id')), null, __('¿Está seguro de eliminar la Dependencia con ID # %s?', $this->Form->value('Dependencia.id'))); ?></li>
        <li><?php echo $this->Html->link(__('Listar Dependencias'), array('action' => 'index')); ?></li>
    </ul>
</div>
