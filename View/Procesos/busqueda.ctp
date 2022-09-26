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
echo $this->Html->script('scripts', false);
?>

<?php
function fecha($fecha, $completo = false) {
    $fecha = explode(' ', $fecha);
    
    $fecha_dia = explode('-', $fecha[0]);
    $dia = $fecha_dia[2] . '-' . $fecha_dia[1] . '-' . $fecha_dia[0];

    if ($completo) {
        $fecha_hora = explode(':', $fecha[1]);
        $hora = $fecha_hora[0] . ':' . $fecha_hora[1];
        return $dia . ' ' . $hora;
    } else {
        return $dia;
    }
}

?>
<div class="procesos related">
    <h2><?php echo __('Búsqueda Avanzada de Procesos de Pago'); ?></h2>
    
    <div class="busqueda_avanzada">
        <?php 
        echo $this->Form->create('Procesos', array('action' => 'busqueda', 'onsubmit' => 'return false;'));
        $size = 14;
        $maxlength = 20;
        ?>
        <table cellspacing="0" cellpadding="0">
            <tr>
                <td width="14%"><span style="font-size: 110%; margin-left: 6px;">Gesti&oacute;n</span></td>
                <td width="22%"><?php echo $this->Form->input('gestione', array('label' => '', 'type' => 'select', 'default' => $params['gestion'])); ?></td>
                <td width="64%" colspan="4">&nbsp;</td>
            </tr>
            <?php 
            $chk_nro_proceso = ($params['chk_nro_proceso'])?'checked = "checked"':''; 
            $chk_cite = ($params['chk_cite'] == 'true')?'checked = "checked"':'';
            $chk_nro_preventivo = ($params['chk_nro_preventivo'] == 'true')?'checked = "checked"':'';
            ?>
            <tr>
                <td width="14%"><?php echo $this->Form->input('chk_nro_proceso', array('type' => 'checkbox', 'value' => '1', 'label' => 'N° Proceso', $chk_nro_proceso)); ?></td>
                <td width="22%"><?php echo $this->Form->input('nro_proceso', array('label' => '', 'size' => $size, 'maxlength' => $maxlength, 'onkeypress' => 'return validar_num(event)', 'disabled' => !$params['chk_nro_proceso'], 'default' => $params['nro_proceso'])); ?></td>
                <td width="14%"><?php echo $this->Form->input('chk_cite', array('type' => 'checkbox', 'value' => '1', 'label' => 'Cite', $chk_cite)); ?></td>
                <td width="22%"><?php echo $this->Form->input('cite', array('label' => '', 'size' => $size, 'maxlength' => $maxlength, 'disabled' => !$params['chk_cite'], 'default' => $params['cite'])); ?></td>
                <td width="14%"><?php echo $this->Form->input('chk_nro_preventivo', array('type' => 'checkbox', 'value' => '1', 'label' => 'N° Preventivo', $chk_nro_preventivo)); ?></td>
                <td width="22%"><?php echo $this->Form->input('nro_preventivo', array('label' => '', 'size' => $size, 'maxlength' => $maxlength, 'disabled' => !$params['chk_nro_preventivo'], 'default' => $params['nro_preventivo'])); ?></td>
            </tr>
            <?php 
            $chk_ci_nit = ($params['chk_ci_nit'] == 'true')?'checked = "checked"':'';
            $chk_beneficiario = ($params['chk_beneficiario'] == 'true')?'checked = "checked"':'';
            $chk_monto = ($params['chk_monto'])?'checked = "checked"':'';
            $monto = $this->Form->input('monto', array('label' => null, 'size' => $size, 'maxlength' => $maxlength, 'disabled' => !$params['chk_monto'], 'default' => $params['monto']));
            ?>
            <tr>
                <td><?php echo $this->Form->input('chk_ci_nit', array('type' => 'checkbox', 'value' => '1', 'label' => 'CI/NIT', $chk_ci_nit)); ?></td>
                <td><?php echo $this->Form->input('ci_nit', array('label' => '', 'size' => $size, 'maxlength' => $maxlength, 'disabled' => !$params['chk_ci_nit'], 'default' => $params['ci_nit'])); ?></td>
                <td><?php echo $this->Form->input('chk_beneficiario', array('type' => 'checkbox', 'value' => '1', 'label' => 'Beneficiario', $chk_beneficiario)); ?></td>
                <td><?php echo $this->Form->input('beneficiario', array('label' => '', 'size' => $size, 'maxlength' => $maxlength, 'disabled' => !$params['chk_beneficiario'], 'default' => $params['beneficiario'])); ?></td>
                <td><?php echo $this->Form->input('chk_monto', array('type' => 'checkbox', 'value' => '1', 'label' => 'Monto', $chk_monto)); ?></td>
                <td>
                    <?php 
                    echo $this->Form->select('comparador', array('=' => '=', '>' => '>', '>=' => '>=', '<' => '<', '<=' => '<='), array('empty' => null, 'disabled' => !$params['chk_monto'], 'default' => $params['comparador'])); 
                    echo $this->Form->input('monto', array('label' => false, 'div' => false, 'size' => 8, 'maxlength' => $maxlength, 'disabled' => !$params['chk_monto'], 'default' => $params['monto']));
                    ?>
                </td>
            </tr>
            <?php 
            $chk_dependencia = ($params['chk_dependencia'])?'checked = "checked"':''; 
            $chk_motivo = ($params['chk_motivo'] == 'true')?'checked = "checked"':''; 
            $chk_estado = ($params['chk_estado'] == 'true')?'checked = "checked"':''; 
            ?>
            <tr>
                <td><?php echo $this->Form->input('chk_dependencia', array('type' => 'checkbox', 'value' => 'true', 'label' => 'Dependencia', $chk_dependencia)); ?></td>
                <td><?php echo $this->Form->input('dependencia', array('label' => '', 'type' => 'select', 'empty' => 'Todas', 'disabled' => !$params['chk_dependencia'], 'default' => $params['dependencia'])); ?></td>
                <td><?php echo $this->Form->input('chk_motivo', array('type' => 'checkbox', 'value' => '1', 'label' => 'Motivo', $chk_motivo)); ?></td>
                <td><?php echo $this->Form->input('motivo', array('label' => '', 'type' => 'select', 'empty' => 'Todos', 'disabled' => !$params['chk_motivo'], 'default' => $params['motivo'])); ?></td>
                <td><?php echo $this->Form->input('chk_estado', array('type' => 'checkbox', 'value' => '1', 'label' => 'Estado', $chk_estado)); ?></td>
                <td><?php echo $this->Form->input('estado', array('label' => '', 'type' => 'select', 'empty' => 'Todos', 'disabled' => !$params['chk_estado'], 'default' => $params['estado'])); ?></td>
            </tr>
            <?php 
            $chk_fecha_ini = ($params['chk_fecha_ini'] == 'true')?'checked = "checked"':''; 
            $chk_fecha_fin = ($params['chk_fecha_fin'] == 'true')?'checked = "checked"':''; 
            
            $fecha_ini = (!$params['chk_fecha_ini'])?'disabled="disabled"':'';
            $fecha_fin = (!$params['chk_fecha_fin'])?'disabled="disabled"':'';
            ?>
            <tr>
                <td><span style="font-size: 12px;">&nbsp;Fecha de ingreso</span>
                    <br /><?php echo $this->Form->input('chk_fecha_ini', array('type' => 'checkbox', 'value' => '1', 'label' => 'De ', $chk_fecha_ini)); ?></td>
                <td><br /><?php echo $this->Form->input('fecha_ini', array('label' => '', 'size' => 8, 'readonly' => true, 'after' => '&nbsp;<input id="BtnFechaIni" type="button" ' . $fecha_ini . ' value="..." />', 'disabled' => !$params['chk_fecha_ini'], 'default' => $params['fecha_ini'])); ?></td>
                <td><br /><?php echo $this->Form->input('chk_fecha_fin', array('type' => 'checkbox', 'value' => '1', 'label' => 'a ', $chk_fecha_fin)); ?></td>
                <td><br /><?php echo $this->Form->input('fecha_fin', array('label' => '', 'size' => 8, 'readonly' => true, 'after' => '&nbsp;<input id="BtnFechaFin" type="button" ' . $fecha_fin . ' value="..." />', 'disabled' => !$params['chk_fecha_fin'], 'default' => $params['fecha_fin'])); ?></td>
                <td colspan="2"><br />
                    <div class="right">
                    <?php
                    echo $this->Form->end(__('Buscar', true)); 
                    ?>
                    </div>
                </td>
            </tr>
        </table>
    </div>
    
    <?php if (!empty($params['envio'])) : ?>
    
    <div class="buttons left">
        <?php $url = $this->Html->url(array('controller' => 'procesos', 'action' => 'index'), true); ?>
        <?php echo $this->Html->link($this->Html->image('botones/ver.png') . ' Ver', 'javascript:void(0);', array('onclick' => 'javascript:action(\'' . $url . '\', \'ver\', id_registro, null)', 'escape' => false)); ?>
    </div>
    
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th width="40"><?php echo $this->Paginator->sort('nro_proceso', 'Nro'); ?></th>
            <th><?php echo $this->Paginator->sort('cite'); ?></th>
            <th><?php echo $this->Paginator->sort('nro_preventivo', 'N°Preventivo'); ?></th>
            <th><?php echo $this->Paginator->sort('beneficiario_documento', 'CI/NIT'); ?></th>
            <th><?php echo $this->Paginator->sort('beneficiario_nombre', 'Beneficiario'); ?></th>
            <th><?php echo $this->Paginator->sort('dependencia_id'); ?></th>
            <th><?php echo $this->Paginator->sort('motivo_id'); ?></th>
            <th><?php echo $this->Paginator->sort('estado_id', 'Estado'); ?></th>
            <th><?php echo $this->Paginator->sort('monto'); ?></th>
        </tr>
        <?php
        foreach ($procesos as $proceso):
            ?>
            <tr onclick="javascript:seleccionar(this, <?php echo $proceso['Proceso']['id']; ?>);">
                <td><?php echo h($proceso['Proceso']['nro_proceso']); ?>&nbsp;</td>
                <td><?php echo h($proceso['Proceso']['cite']); ?>&nbsp;</td>
                <td><?php echo h($proceso['Proceso']['nro_preventivo']); ?>&nbsp;</td>
                <td><?php echo $proceso['Proceso']['beneficiario_documento']; ?></td>
                <td><?php echo $proceso['Proceso']['beneficiario_nombre']; ?></td>
                <td><?php echo $proceso['Dependencia']['sigla']; ?></td>
                <td><?php echo $proceso['Motivo']['nombre']; ?></td>
                <td><?php echo h($proceso['Estado']['nombre']); ?></td>
                <td align="right"><?php echo h($proceso['Proceso']['monto']); ?></td>
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
    <?php endif; ?>
</div>
<script type="text/javascript">
    //<![CDATA[
    var cal_1 = Calendar.setup({
        weekNumbers : false,
        onSelect: function() {
            var date = cal_1.selection.get();
            if (date) {
                date = Calendar.intToDate(date);
                $('#ProcesosFechaIni').val(Calendar.printDate(date, "%Y-%m-%d"));
            } 
            this.hide();
        }
    });
    cal_1.manageFields("BtnFechaIni", "ProcesosFechaIni", "%Y-%m-%d");
    var cal_2 = Calendar.setup({
        weekNumbers : false,
        onSelect: function() {
            var date = cal_2.selection.get();
            if (date) {
                date = Calendar.intToDate(date);
                $('#ProcesosFechaFin').val(Calendar.printDate(date, "%Y-%m-%d"));
            } 
            this.hide();
        }
    });
    cal_2.manageFields("BtnFechaFin", "ProcesosFechaFin", "%Y-%m-%d");

    var query_ok;
    function getURL () {
        query_ok = false;
        url = $('#ProcesosBusquedaForm').attr('action')
            + '/envio:si'
            + '/gestion:' + $('#ProcesosGestione').val();
    
        if ($('#ProcesosChkNroProceso').is(':checked') && ($('#ProcesosNroProceso').val() != '')) {
            query_ok = true;
            url += '/chk_nro_proceso:' + $('#ProcesosChkNroProceso').is(':checked')
                + '/nro_proceso:' + $('#ProcesosNroProceso').val();
        }
        if ($('#ProcesosChkCite').is(':checked') && ($('#ProcesosCite').val() != '')) {
            query_ok = true;
            url += '/chk_cite:' + $('#ProcesosChkCite').is(':checked')
                + '/cite:' + $('#ProcesosCite').val();
        }
        if ($('#ProcesosChkNroPreventivo').is(':checked') && ($('#ProcesosNroPreventivo').val() != '')) {
            query_ok = true;
            url += '/chk_nro_preventivo:' + $('#ProcesosChkNroPreventivo').is(':checked')
                + '/nro_preventivo:' + $('#ProcesosNroPreventivo').val();
        }
        if ($('#ProcesosChkCiNit').is(':checked') && ($('#ProcesosCiNit').val() != '')) {
            query_ok = true;
            url += '/chk_ci_nit:' + $('#ProcesosChkCiNit').is(':checked')
                + '/ci_nit:' + $('#ProcesosCiNit').val();
        }
        if ($('#ProcesosChkBeneficiario').is(':checked') && ($('#ProcesosBeneficiario').val() != '')) {
            query_ok = true;
            url += '/chk_beneficiario:' + $('#ProcesosChkBeneficiario').is(':checked')
                + '/beneficiario:' + $('#ProcesosBeneficiario').val();
        }
        if ($('#ProcesosChkMonto').is(':checked') && ($('#ProcesosMonto').val() != '')) {
            query_ok = true;
            url += '/chk_monto:' + $('#ProcesosChkMonto').is(':checked')
                + '/comparador:' + $('#ProcesosComparador').val()
                + '/monto:' + $('#ProcesosMonto').val();
        }
        if ($('#ProcesosChkDependencia').is(':checked')) {
            query_ok = true;
            url += '/chk_dependencia:' + $('#ProcesosChkDependencia').is(':checked')
                + '/dependencia:' + $('#ProcesosDependencia').val();
        }
        if ($('#ProcesosChkMotivo').is(':checked')) {
            query_ok = true;
            url += '/chk_motivo:' + $('#ProcesosChkMotivo').is(':checked')
                + '/motivo:' + $('#ProcesosMotivo').val();
        }
        if ($('#ProcesosChkEstado').is(':checked')) {
            query_ok = true;
            url += '/chk_estado:' + $('#ProcesosChkEstado').is(':checked')
                + '/estado:' + $('#ProcesosEstado').val();
        }
        if ($('#ProcesosChkFechaIni').is(':checked') && ($('#ProcesosFechaIni').val() != '')) {
            query_ok = true;
            url += '/chk_fecha_ini:' + $('#ProcesosChkFechaIni').is(':checked')
                + '/fecha_ini:' + $('#ProcesosFechaIni').val();
        }
        if ($('#ProcesosChkFechaFin').is(':checked') && ($('#ProcesosFechaFin').val() != '')) {
            query_ok = true;
            url += '/chk_fecha_fin:' + $('#ProcesosChkFechaFin').is(':checked')
                + '/fecha_fin:' + $('#ProcesosFechaFin').val();
        }

        return url;
    }

    $('#ProcesosBusquedaForm').submit(function() {
        var url = getURL();
        if (query_ok) {
            window.location = url;
        } else {
            alert ('Por favor, debe seleccionar e introducir un valor en al menos una opción de la búsqueda');
        }
    });
    
    /** Filtros de búsqueda */
    $('#ProcesosChkNroProceso').click(function() {
        if ($('#ProcesosChkNroProceso').is(':checked')) {
            $('#ProcesosNroProceso').attr('disabled', false);
            $('#ProcesosNroProceso').focus();
        } else {
            $('#ProcesosNroProceso').attr('disabled', true);
        }
    });
    $('#ProcesosChkCite').click(function() {
        if ($('#ProcesosChkCite').is(':checked')) {
            $('#ProcesosCite').attr('disabled', false);
            $('#ProcesosCite').focus();
        } else {
            $('#ProcesosCite').attr('disabled', true);
        }
    });
    $('#ProcesosChkNroPreventivo').click(function() {
        if ($('#ProcesosChkNroPreventivo').is(':checked')) {
            $('#ProcesosNroPreventivo').attr('disabled', false);
            $('#ProcesosNroPreventivo').focus();
        } else {
            $('#ProcesosNroPreventivo').attr('disabled', true);
        }
    });
    $('#ProcesosChkCiNit').click(function() {
        if ($('#ProcesosChkCiNit').is(':checked')) {
            $('#ProcesosCiNit').attr('disabled', false);
            $('#ProcesosCiNit').focus();
        } else {
            $('#ProcesosCiNit').attr('disabled', true);
        }
    });
    $('#ProcesosChkBeneficiario').click(function() {
        if ($('#ProcesosChkBeneficiario').is(':checked')) {
            $('#ProcesosBeneficiario').attr('disabled', false);
            $('#ProcesosBeneficiario').focus();
        } else {
            $('#ProcesosBeneficiario').attr('disabled', true);
        }
    });
    $('#ProcesosChkMonto').click(function() {
        if ($('#ProcesosChkMonto').is(':checked')) {
            $('#ProcesosComparador').attr('disabled', false);
            $('#ProcesosMonto').attr('disabled', false);
            $('#ProcesosMonto').focus();
        } else {
            $('#ProcesosComparador').attr('disabled', true);
            $('#ProcesosMonto').attr('disabled', true);
        }
    });
    $('#ProcesosChkDependencia').click(function() {
        if ($('#ProcesosChkDependencia').is(':checked')) {
            $('#ProcesosDependencia').attr('disabled', false);
            $('#ProcesosDependencia').focus();
        } else {
            $('#ProcesosDependencia').attr('disabled', true);
        }
    });
    $('#ProcesosChkMotivo').click(function() {
        if ($('#ProcesosChkMotivo').is(':checked')) {
            $('#ProcesosMotivo').attr('disabled', false);
            $('#ProcesosMotivo').focus();
        } else {
            $('#ProcesosMotivo').attr('disabled', true);
        }
    });
    $('#ProcesosChkEstado').click(function() {
        if ($('#ProcesosChkEstado').is(':checked')) {
            $('#ProcesosEstado').removeAttr('disabled');
            $('#ProcesosEstado').focus();
        } else {
            $('#ProcesosEstado').attr('disabled', true);
        }
    });
    $('#ProcesosChkFechaIni').click(function() {
        if ($('#ProcesosChkFechaIni').is(':checked')) {
            $('#ProcesosFechaIni').attr('disabled', false);
            $('#BtnFechaIni').attr('disabled', false);
            $('#BtnFechaIni').focus();
        } else {
            $('#ProcesosFechaIni').attr('disabled', true);
            $('#BtnFechaIni').attr('disabled', true);
        }
    });
    $('#ProcesosChkFechaFin').click(function() {
        if ($('#ProcesosChkFechaFin').is(':checked')) {
            $('#ProcesosFechaFin').attr('disabled', false);
            $('#BtnFechaFin').attr('disabled', false);
            $('#BtnFechaFin').focus();
        } else {
            $('#ProcesosFechaFin').attr('disabled', true);
            $('#BtnFechaFin').attr('disabled', true);
        }
    });
    //]]>
</script>