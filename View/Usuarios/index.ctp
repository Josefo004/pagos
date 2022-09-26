<div class="usuarios related">
    <h2><?php echo __('Usuarios'); ?></h2>
    
    <?php
    echo $this->Form->create('Borrar', array('id' => 'frm_borrar'));
    echo $this->Form->end();
    ?>
    <div class="buttons left">
        <?php $url = $this->Html->url(array('controller' => 'usuarios', 'action' => 'index'), true); ?>
        <?php echo $this->Html->link($this->Html->image('botones/nuevo.png') . ' Nuevo', array('action' => 'nuevo'), array('escape' => false)); ?>
        <?php echo $this->Html->link($this->Html->image('botones/editar.png') . ' Editar', 'javascript:void(0);', array('onclick' => 'javascript:action(\'' . $url . '\', \'editar\', id_registro, null)', 'escape' => false)); ?>
        <?php echo $this->Html->link($this->Html->image('botones/borrar.png') . ' Borrar', 'javascript:void(0);', array('onclick' => 'javascript:action(\'' . $url . '\', \'eliminar\', id_registro, \'¿Está seguro de borrar el Usuario seleccionado?\');', 'escape' => false)); ?>
    </div>
    
    <div class="buttons right">
        <?php echo $this->Html->link($this->Html->image('botones/devolver.png') . ' Volver al Panel', array('controller' => 'pages', 'action' => 'admin'), array('escape' => false)); ?>
    </div>
    <div class="clear"></div>
    <?php echo $this->Form->create('Buscar', array('onsubmit' => 'return false;')); ?>
    <div class="left">
        <?php
        $filtro = $this->Form->select('Filtro', array('ci' => 'por CI', 'nombres' => 'por nombres', 'apellidos' => 'por apellidos'), array('empty' => false, 'value' => $params['filtro']));
        echo $this->Form->input('texto', array('before' => 'Buscar: ' . $filtro, 'label' => false, 'value' => $params['texto']));
        ?>
    </div>
    </div>
    <div class="right">
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
            'label' => false,
            'before' => 'Rol: ',
            'default' => $params['rol']
        )); 
        ?>
    </div>
    <?php echo $this->Form->end(); ?>
    <table cellpadding="0" cellspacing="0">
        <tr>
            <th width="2%">#</th>
            <th width="20%"><?php echo $this->Paginator->sort('nick'); ?></th>
            <th width="20%"><?php echo $this->Paginator->sort('rol'); ?></th>
            <th width="20%"><?php echo $this->Paginator->sort('ServidoresPublico.nombres', 'Nombres'); ?></th>
            <th width="20%"><?php echo $this->Paginator->sort('ServidoresPublico.apellidos', 'Apellidos'); ?></th>
            <th width="20%"><?php echo $this->Paginator->sort('ServidoresPublico.ci', 'Documento'); ?></th>
        </tr>
        <?php 
        $i = (intval($params['page']) - 1) * 20;
        foreach ($servidores_publicos as $servidor): 
            $i++;
            ?>
            <tr onclick="javascript:seleccionar(this, <?php echo $servidor['ServidoresPublico']['usuario_id']; ?>);">
                <td><?php echo $i; ?>&nbsp;</td>
                <td><?php echo h($servidor['Usuario']['nick']); ?>&nbsp;</td>
                <td>
                    <?php 
                        switch ($servidor['Usuario']['rol']) {
                            case 'admin':
                                echo 'Administrador';
                                break;
                            case 'finanzas':
                                echo 'Finanzas';
                                break;
                            case 'contabilidad':
                                echo 'Contabilidad';
                                break;
                            case 'analistas':
                                echo 'Analistas';
                                break;
                            case 'administradores':
                                echo 'Administradores';
                                break;
                            case 'tesoreria1':
                                echo 'Tesorería Recepción';
                                break;
                            case 'tesoreria2':
                                echo 'Tesorería Impresión';
                                break;
                            case 'economia':
                                echo 'Stria. Economía';
                                break;
                            case 'caja':
                                echo 'Caja';
                                break;
                            case 'archivo':
                                echo 'Archivo';
                                break;
                        }
                    ?>
                    &nbsp;
                </td>
                <td><?php echo h($servidor['ServidoresPublico']['nombres']); ?>&nbsp;</td>
                <td><?php echo h($servidor['ServidoresPublico']['apellidos']); ?>&nbsp;</td>
                <td><?php echo $servidor['ServidoresPublico']['ci']; ?>&nbsp;</td>
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
<script type="text/javascript">
    //<![CDATA[
    $('#BuscarIndexForm').submit(function() {
        url = '<?php echo $this->Html->url(array('controller' => 'usuarios', 'action' => 'index')); ?>/index'
            + '/filtro:' + $('#BuscarFiltro').val()
            + '/texto:' + $('#BuscarTexto').val()
            + '/rol:' + $('#BuscarRol').val();
        
        window.location = url;
    });
    $('#BuscarRol').change(function() {
        url = '<?php echo $this->Html->url(array('controller' => 'usuarios', 'action' => 'index')); ?>/index'
            + '/filtro:' + $('#BuscarFiltro').val()
            + '/texto:' + $('#BuscarTexto').val()
            + '/rol:' + $('#BuscarRol').val();
        
        window.location = url;
    });
    //]]>
</script>