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

echo $this->Html->script(array('jscalendar/jscal2', 'jscalendar/unicode-letter', 'jscalendar/lang/es'), false)
?>
<div class="procesos form">
    <?php echo $this->Form->create('Proceso'); ?>
    <fieldset>
        <legend><?php echo __("Nuevo Proceso"); ?> <small style="color: #000;"><?php echo "Último Proceso: $ultimo";?></small></legend>
        <div class="left"><?php echo $this->Form->input('cite', array('label' => 'CITE')); ?></div>
        <div class="left"><?php echo $this->Form->input('nro_proceso', array('label' => 'N° de Proceso')); ?></div>
        <div class="left"><?php echo $this->Form->input('fecha_emision', array('label' => 'Fecha de Emisión', 'type' => 'text', 'after' => '&nbsp;<input id="BtnFechaEmision" type="button" value="..." />', 'readonly' => 'true', 'size' => 10, 'value' => date('Y-m-d'))); ?></div>
        <div class="clear"></div>
        <?php echo $this->Form->input('dependencia_id', array('empty' => 'Seleccione')); ?>
        <div class="left"><?php echo $this->Form->input('motivo_id'); ?></div>
        <div class="left"><?php echo $this->Form->input('nro_preventivo'); ?></div>
        <div class="left"><?php echo $this->Form->input('monto', array('type' => 'number')); ?></div>
        <div class="clear"></div>
        <div class="left"><?php echo $this->Form->input('beneficiario_documento', array('label' => 'CI/NIT del Beneficiario')); ?></div>
        <div class="left"><?php echo $this->Form->input('beneficiario_nombre', array('label' => 'Nombre del Beneficiario')); ?></div>
        <div class="clear"></div>
        <?php
        echo $this->Form->input('referencia', array('class' => 'full', 'rows' => 2));
        echo $this->Form->input('descripcion_doc', array('class' => 'full', 'rows' => 2, 'label' => 'Descripción de documentos'));
        ?>
    <?php echo $this->Form->end(__('Guardar')); ?>
    </fieldset>
</div>
<div class="actions">
    <h3><?php echo __('Acciones'); ?></h3>
    <ul>
        <li><?php echo $this->Html->link(__('Listar Procesos'), array('action' => 'index', 'mayor' => 'si', 'estado' => 1)); ?></li>
    </ul>
</div>

<script type="text/javascript">
    //<![CDATA[
    var cal_1 = Calendar.setup({
        weekNumbers : false,
        onSelect: function() {
            var date = cal_1.selection.get();
            if (date) {
                date = Calendar.intToDate(date);
                $('#ProcesoFechaEmision').val(Calendar.printDate(date, "%Y-%m-%d"));
            } 
            this.hide();
        }
    });
    cal_1.manageFields("BtnFechaEmision", "ProcesoFechaEmision", "%Y-%m-%d");
    //]]>
</script>