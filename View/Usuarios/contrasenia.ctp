<div class="usuarios form">
    <?php echo $this->Form->create('Usuario'); ?>
    <fieldset>
        <legend><?php echo __('Cambiar Contraseña'); ?></legend>
        <div class="izquierdo">
            <?php echo $this->Form->input('contrasenia_actual', array('type' => 'password', 'label' => 'Contraseña actual', 'size' => 30)); ?>
        </div>
        <div class="clear"></div>
        <div class="izquierdo">
            <?php echo $this->Form->input('contrasenia_nueva1', array('type' => 'password', 'label' => 'Nueva Contraseña', 'size' => 30)); ?>
        </div>
        <div class="izquierdo">
            <?php echo $this->Form->input('contrasenia_nueva2', array('type' => 'password', 'label' => 'Confirmar Contraseña', 'size' => 30)); ?>
        </div>
        <div class="clear"></div>
        <?php echo $this->Form->end(__('Guardar')); ?>
    </fieldset>
    
</div>
<div class="actions" style="width: 146px;">
    <h3><?php echo __('Acciones'); ?></h3>
    <ul>
        <li><?php echo $this->Html->link('Volver al Panel', array('controller' => 'pages', 'action' => 'admin'), array('escape' => false)); ?></li>
    </ul>
</div>