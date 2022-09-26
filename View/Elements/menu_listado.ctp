<div class="menu_listado">
<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

$class_index = '';
$class_pendientes = '';
$class_recepcionados = '';
$class_observados = '';
switch ($this->params['action']) {
    case 'index':
        $class_index = 'activo';
        break;
    case 'pendientes':
        $class_pendientes = 'activo';
        break;
    case 'recepcionados':
        $class_recepcionados = 'activo';
        break;
    case 'observados':
        $class_observados = 'activo';
        break;
}

$rol = AuthComponent::user('rol');
$menu = false;
if (($rol == 'finanzas') && (($params['estado'] == 9) || ($params['estado'] == 11))) {
    $menu = true;
} elseif (($rol == 'analistas') && ($params['estado'] == 3)) {
    $menu = true;
} elseif (($rol == 'administradores') && ($params['estado'] == 4)) {
    $menu = true;
} elseif (($rol == 'contabilidad') && (($params['estado'] == 2) || ($params['estado'] == 5) || ($params['estado'] == 8))) {
    $menu = true;
} elseif (($rol == 'tesoreria1') && ($params['estado'] == 6)) {
    $menu = true;
} elseif (($rol == 'tesoreria2') && ($params['estado'] == 7)) {
    $menu = true;
} elseif (($rol == 'economia') && ($params['estado'] == 10)) {
    $menu = true;
} elseif (($rol == 'caja') && ($params['estado'] == 12)) {
    $menu = true;
} elseif (($rol == 'archivo') && ($params['estado'] == 13)) {
    $menu = true;
}

if ($menu) {
    if ($params['estado'] != '1') {
//        echo $this->Html->link('Todos', array('action' => 'index', 'mayor' => $params['mayor'], 'estado' => $params['estado']), array('class' => $class_index));
        echo $this->Html->link('Pendientes', array('action' => 'pendientes', 'mayor' => $params['mayor'], 'estado' => $params['estado']), array('class' => $class_pendientes));
        if ($rol == 'archivo') {
            echo $this->Html->link('Archivados', array('action' => 'recepcionados', 'mayor' => $params['mayor'], 'estado' => $params['estado']), array('class' => $class_recepcionados));
        } else {
            echo $this->Html->link('Recepcionados', array('action' => 'recepcionados', 'mayor' => $params['mayor'], 'estado' => $params['estado']), array('class' => $class_recepcionados));
        }
    }
    if ($rol == 'analistas') {
        echo $this->Html->link('Observados', array('action' => 'observados', 'mayor' => $params['mayor'], 'estado' => $params['estado']), array('class' => $class_observados));
    }
} 
?>

</div>