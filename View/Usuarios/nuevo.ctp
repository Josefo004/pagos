<?php
$this->Html->css(array(
        'jscalendar/jscal2',
        'jscalendar/border-radius',
        'jscalendar/steel/steel',
        'jscalendar/reduce-spacing'
    ), 
    'stylesheet', 
    array('inline' => false)
);

echo $this->Html->script(array('jscalendar/jscal2', 'jscalendar/unicode-letter', 'jscalendar/lang/es'), false);
?>
<div class="usuarios form">
    <?php echo $this->Form->create('Usuario'); ?>
    <fieldset>
        <legend><?php echo __('Nuevo Usuario'); ?></legend>
        <fieldset>
            <legend>Datos de Usuario</legend>
            <div class="left"><?php echo $this->Form->input('nick', array('size' => 20)); ?></div>
            <div class="left"><?php echo $this->Form->input('contrasenia', array('type' => 'password', 'size' => 20, 'label' => 'Contraseña')); ?></div>
            <div class="clear"></div>
            <div class="left">
                <?php 
                echo $this->Form->input('rol', array(
                    'type' => 'select', 
                    'options' => array(
                        'admin' => 'Admin Sistemas',
                        'reportes' => 'Reportes Gerenciales',
                        'finanzas' => 'Dir. Finanzas', 
                        'contabilidad' => 'Contabilidad', 
                        'analistas' => 'Analistas', 
                        'administradores' => 'Administradores', 
                        'tesoreria1' => 'Tesorería Recepción', 
                        'tesoreria2' => 'Tesorería Impresión', 
                        'economia' => 'Stria. Economía', 
                        'caja' => 'Caja', 
                        'archivo' => 'Archivo'
                    ), 
                    'empty' => 'Seleccione',
                    'label' => 'Rol',
                    'before' => '<br>'
                )); 
                ?>
            </div>
            <div class="left"><br /><?php echo $this->Form->input('estado', array('type' => 'checkbox', 'label' => 'Habilitado')); ?></div>
        </fieldset><br />
        <fieldset>
            <legend>Datos Personales</legend>
            <div class="left"><?php echo $this->Form->input('ServidoresPublico.ci', array('label' => 'Documento')); ?></div>
            <div class="left"><?php echo $this->Form->input('ServidoresPublico.nombres', array('label' => 'Nombres')); ?></div>
            <div class="left"><?php echo $this->Form->input('ServidoresPublico.apellidos', array('label' => 'Apellidos')); ?></div>
            <div class="clear"></div>
            <div class="left"><?php echo $this->Form->input('ServidoresPublico.sexo', array('type' => 'radio', 'options' => array('1' => 'Femenino', '2' => 'Masculino'))); ?></div>
            <div class="left"><?php 
            echo $this->Form->input('ServidoresPublico.fecha_nacimiento', array('type' => 'text', 'label' => 'Fecha de Nacimiento', 'size' => 8, 'after' => '<input id="BtnFechaNac" type="button" value="..." />', 'readonly' => true)); ?>
            </div>
            <div class="left"><?php echo $this->Form->input('ServidoresPublico.lugar_nacimiento'); ?></div>
            <div class="clear"></div>
            <div class="left"><?php echo $this->Form->input('ServidoresPublico.profesion', array('label' => 'Profesión')); ?></div>
            <div class="left"><?php echo $this->Form->input('ServidoresPublico.domicilio'); ?></div>
            <div class="clear"></div>
            <div class="left"><?php echo $this->Form->input('ServidoresPublico.telefono_domicilio', array('label' => 'Telf. Domicilio')); ?></div>
            <div class="left"><?php echo $this->Form->input('ServidoresPublico.telefono_celular', array('label' => 'Telf. Celular')); ?></div>
            <div class="left"><?php echo $this->Form->input('ServidoresPublico.telefono_interno', array('label' => 'Telf. Interno')); ?></div>
            <div class="clear"></div>
            <div class="left"><?php echo $this->Form->input('ServidoresPublico.correo_electronico', array('label' => 'Correo electrónico')); ?></div>
        </fieldset><br />
        <fieldset>
            <legend>Datos Laborales</legend>
            <?php echo $this->Form->input('ServidoresPublico.estado', array('label' => 'Habilitado', 'type' => 'checkbox')); ?>
            <?php echo $this->Form->input('ServidoresPublico.dependencia_id', array('label' => 'Dependencia')); ?>
            <?php echo $this->Form->input('ServidoresPublico.cargo_actual', array('label' => 'Cargo actual')); ?>
        </fieldset>
    </fieldset>
    <?php echo $this->Form->end(__('Guardar')); ?>
</div>
<div class="actions">
    <h3><?php echo __('Acciones'); ?></h3>
    <ul>
        <li><?php echo $this->Html->link('Volver al Listado', array('action' => 'index'), array('escape' => false)); ?></li>
        <li><?php echo $this->Html->link('Ir al Panel', array('controller' => 'pages', 'action' => 'admin'), array('escape' => false)); ?></li>
    </ul>
</div>
<script type="text/javascript">
    //<![CDATA[
    var cal_fnac = Calendar.setup({
        weekNumbers : false,
        onSelect: function() {
            var date = cal_fnac.selection.get();
            if (date) {
                date = Calendar.intToDate(date);
                $('#ServidoresPublicoFechaNacimiento').val(Calendar.printDate(date, "%Y-%m-%d"));
            } 
            this.hide();
        }
    });
    cal_fnac.manageFields("BtnFechaNac", "ServidoresPublicoFechaNacimiento", "%Y-%m-%d");
    //]]>
</script>