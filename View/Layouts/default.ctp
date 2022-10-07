<?php
/**
 *
 * PHP 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2011, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.View.Layouts
 * @since         CakePHP(tm) v 0.10.0.1076
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <?php echo $this->Html->charset(); ?>
        <title>
            <?php echo Configure::read('App.name'); ?>: 
            <?php echo $title_for_layout; ?>
        </title>
        <?php
        echo $this->Html->meta('icon');

        echo $this->Html->css('gach.pagos');
        
        echo $this->Html->script('jquery-1.7.2');
        
        echo $this->Html->script('funciones');

        echo $scripts_for_layout;
        ?>
    </head>
    <body style="">
        <div id="container">
            <div style="background-color: #ffee00;">
                <fieldset>
                    <legend><p></p>SISTEMA EN ETAPA DE PRUEBA !!! SISTEMA DE PRUEBAS TEMPORAL !!!</legend>
                    <strong>Pruebas Temporales !!! </strong> 
                </fieldset>
            </div>
            <?php if (AuthComponent::user('id')) : ?>
            <div class="menu_superior">
                <?php $usuario = $this->requestAction('usuarios/datos'); ?>
                <div class="left">
                    <b>Bienvenid@ <?php echo $usuario['ServidoresPublico']['nombres'] . ' ' . $usuario['ServidoresPublico']['apellidos']; ?></b>
                </div>
                <div class="right">
                    <?php
                    echo $this->Html->link(
                            'Cambio de contraseña ' . $this->Html->image('botones/contrasenia.png', array('border' => '0')), array('controller' => 'usuarios', 'action' => 'contrasenia'), array('escape' => false)
                    );
                    echo '&nbsp;';
                    echo $this->Html->link(
                            'Cerrar Sesión ' . $this->Html->image('botones/cerrar.png', array('border' => '0')), array('controller' => 'usuarios', 'action' => 'logout'), array('escape' => false)
                    );
                    ?>
                </div>
                <div class="clear"></div>
            </div>
            <?php endif; ?>
            <div id="header">
                <div style="height: 82px">&nbsp;</div>
                <?php if (AuthComponent::user('id')) : ?>
                <div class="menu_principal">
                    <?php // echo $this->Html->link('Inicio', array('controller' => 'procesos', 'action' => 'index', 'mayor' => 'si')); ?>
                    <?php
                    $mayor = empty($params['mayor'])?'':$params['mayor'];
                    $avanzada = empty($params['avanzada'])?'':$params['avanzada'];
                    $class_mayor = 'seleccionado';
                    $class_menor = '';
                    $class_busqueda = '';
                    $class_admin = '';
                    $class_reportes = '';
                    
                    if ($mayor == 'no') {
                        $class_mayor = '';
                        $class_menor = 'seleccionado';
                    } elseif (!empty($avanzada)) {
                        $class_mayor = '';
                        $class_menor = '';
                        $class_busqueda = 'seleccionado';
                    } elseif (in_array($this->params['controller'], array('pages', 'motivos', 'dependencias', 'usuarios'))) {
                        $class_mayor = '';
                        $class_admin = 'seleccionado';
                    } elseif ($this->params['controller'] == 'reportes') {
                        $class_mayor = '';
                        $class_reportes = 'seleccionado';
                    }
                    $action = 'pendientes';
                    switch (AuthComponent::user('rol')) {
                        case 'finanzas':
                            $action = 'index';
                            $estado_mayor = 1;
                            $estado_menor = 1;
                            break;
                        case 'contabilidad':
                            $estado_mayor = 2;
                            $estado_menor = 2;
                            break;
                        case 'analistas':
                            $estado_mayor = 3;
                            $estado_menor = 3;
                            break;
                        case 'administradores':
                            $estado_mayor = 4;
                            $estado_menor = 4;
                            break;
                        case 'tesoreria1':
                            $estado_mayor = 6;
                            $estado_menor = 6;
                            break;
                        case 'tesoreria2':
                            $estado_mayor = 7;
                            $estado_menor = 7;
                            break;
                        case 'economia':
                            $estado_mayor = 10;
                            $estado_menor = 1;
                            break;
                        case 'caja':
                            $estado_mayor = 12;
                            $estado_menor = 12;
                            break;
                        case 'archivo':
                            $estado_mayor = 13;
                            $estado_menor = 13;
                            break;
                        case 'reportes':
                            $action = 'index';
                            $estado_mayor = 1;
                            $estado_menor = 1;
                            break;
                        case 'admin':
                            $action = 'index';
                            $estado_mayor = 1;
                            $estado_menor = 1;
                            break;
                    }
                    
                    echo $this->Html->link('Procesos', array('controller' => 'procesos', 'action' => $action, 'mayor' => 'si', 'estado' => $estado_mayor), array('escape' => false, 'class' => $class_mayor));
                    //echo $this->Html->link('Procesos <span><= Bs. 10.000</span>', array('controller' => 'procesos', 'action' => $action, 'mayor' => 'no', 'estado' => $estado_menor), array('escape' => false, 'class' => $class_menor));
                    ?>
                    
                    <div class="right">
                        <?php 
                        switch (AuthComponent::user('rol')) {
                            case 'finanzas':
                                echo $this->Html->link('Reportes', array('controller' => 'reportes', 'action' => 'finanzas'), array('escape' => false, 'class' => $class_reportes));
                                break;
                            case 'analistas':
                                echo $this->Html->link('Reportes', array('controller' => 'reportes', 'action' => 'analistas'), array('escape' => false, 'class' => $class_reportes));
                                break;
                            case 'caja':
                                echo $this->Html->link('Reportes', array('controller' => 'reportes', 'action' => 'caja'), array('escape' => false, 'class' => $class_reportes));
                                break;
                            case 'admin':
                                echo $this->Html->link('Administración', array('controller' => 'pages', 'action' => 'admin'), array('escape' => false, 'class' => $class_admin));
                                echo $this->Html->link('Reportes', array('controller' => 'reportes', 'action' => 'por_secretarias'), array('escape' => false, 'class' => $class_reportes));
                                break;
                            case 'reportes':
                                echo $this->Html->link('Reportes', array('controller' => 'reportes', 'action' => 'por_secretarias'), array('escape' => false, 'class' => $class_reportes));
                                break;
                        }
                        ?>
                        <?php echo $this->Html->link('Búsqueda avanzada', array('controller' => 'procesos', 'action' => 'busqueda', 'avanzada' => 'si'), array('escape' => false, 'class' => $class_busqueda)); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div id="content">

                <?php echo $this->Session->flash(); ?>

                <?php echo $content_for_layout; ?>

            </div>
            <?php echo $this->element('footer'); ?>
        </div>
        <?php echo $this->element('sql_dump'); ?>
    </body>
</html>