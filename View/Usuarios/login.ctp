<div class="usuarios index">
    <?php echo $this->Session->flash('auth'); ?>
    <?php echo $this->Form->create('Usuario'); ?>
    <fieldset>
        <legend><?php echo __('Acceso a usuarios'); ?></legend>
        <div class="izquierdo">
            <?php
            echo $this->Form->input('nick');
            echo $this->Form->input('contrasenia', array('label' => 'Contraseña', 'type' => 'password'));
            ?>
        </div>
<!--        <div class="izquierdo">-->
            <?php
//            if ($intentos > 3){
//                $captcha = '<img id="captcha" src="' . $this->Html->url('/usuarios/captcha') . '?random=' . rand(0, 10000) .'" alt="" border="1" style="cursor: pointer;" title="Haga clic para cambiar imagen" />';
//                echo $this->Form->input('captcha', array('label' => 'Introduzca las letras y numeros', 'before' => $captcha, 'size' => 6, 'maxlength' => 6));
//            }
            ?>
<!--        </div>-->
        <?php echo $this->Form->end(__('Ingresar')); ?>
    </fieldset>
    
    <span style="font-size: 110%">Para ingresar a la B&uacute;squeda avanzada <?php echo $this->Html->link('click aquí', array('controller' => 'procesos', 'action' => 'busqueda', 'avanzada' => 'si')) ?></span>
</div>
<div class="actions">
    <?php // echo $this->Html->image('logo.jpg', array('alt' => __('ORION: Sistema de Gestión de Compras Menores'), 'width' => 180)); ?>
</div>