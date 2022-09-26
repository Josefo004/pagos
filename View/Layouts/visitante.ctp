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
    <body>
        <div id="container">
            <div class="menu_superior">
                <?php
                echo $this->Html->link(
                        'Iniciar Sesión ' . $this->Html->image('botones/login.png', array('border' => '0')), array('controller' => 'usuarios', 'action' => 'logout'), array('escape' => false)
                );
                ?>
            </div>
            <div id="header">
                <div style="height: 82px">&nbsp;</div>
                <div class="menu_principal">
                    <?php
                    echo $this->Html->link('Búsqueda avanzada', array('controller' => 'procesos', 'action' => 'busqueda', 'avanzada' => 'si'), array('escape' => false, 'class' => 'seleccionado'));
                    ?>
                </div>
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