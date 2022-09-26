<div id="panel">
    <div class="opcion" onclick="window.location = '<?php echo $this->Html->url(array('controller' => 'usuarios', 'action' => 'index'), true); ?>';">
        <?php echo $this->Html->image('panel/usuarios_64x64.png', array('alt' => __('Usuarios'), 'border' => '0')); ?>
        <p>Usuarios</p>
    </div>
    <div class="opcion" onclick="window.location = '<?php echo $this->Html->url(array('controller' => 'dependencias', 'action' => 'index'), true); ?>';">
        <?php echo $this->Html->image('panel/dependencias_64x64.png', array('alt' => __('Dependencias'), 'border' => '0')); ?>
        <p>Dependencias</p>
    </div>
    <div class="opcion" onclick="window.location = '<?php echo $this->Html->url(array('controller' => 'motivos', 'action' => 'index'), true); ?>';">
        <?php echo $this->Html->image('panel/motivos_64x64.png', array('alt' => __('Motivos'), 'border' => '0')); ?>
        <p>Motivos</p>
    </div>
    <div class="opcion" onclick="window.location = '<?php echo $this->Html->url(array('controller' => 'usuarios', 'action' => 'contrasenia'), true); ?>';">
        <?php echo $this->Html->image('panel/contrasenia_64x64.png', array('alt' => __('Cambio de Contraseña'), 'border' => '0')); ?>
        <p>Contraseña</p>
    </div>
    <div class="clear"></div>
</div>
<div id="perfil">
    <h3>Datos de Usuario</h3>
    <?php $persona = $this->requestAction('usuarios/datos'); ?>
    <table>
        <tr>
            <td>Nombre:</td>
            <td><?php echo $persona['ServidoresPublico']['apellidos']; ?> <?php echo $persona['ServidoresPublico']['nombres']; ?></td>
        </tr>
        <tr>
            <td>&Uacute;ltimo acceso:</td>
            <td><?php echo (AuthComponent::user('ultimo_acceso'))?fecha(AuthComponent::user('ultimo_acceso')):'Ninguno'; ?></td>
        </tr>
        <tr>
            <td>&Uacute;ltima IP:</td>
            <td><?php echo (AuthComponent::user('ultima_ip'))?AuthComponent::user('ultima_ip'):'Ninguna'; ?></td>
        </tr>
    </table>
</div>
<?php
function fecha($fecha) {
    $fecha = explode(' ', $fecha);
    $fecha_dia = explode('-', $fecha[0]);
    $fecha_hora = explode('-', $fecha[1]);

    return $fecha_dia[2] . '-' . $fecha_dia[1] . '-' . $fecha_dia[0] . ' a las ' . $fecha_hora[0];
}
?>