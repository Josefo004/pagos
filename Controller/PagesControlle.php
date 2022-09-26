<?php

/**
 * Static content controller.
 *
 * This file will render views from views/pages/
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
 * @package       app.Controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       app.Controller
 */
class PagesController extends AppController {

    /**
     * Controller name
     *
     * @var string
     */
    public $name = 'Pages';

    /**
     * Default helper
     *
     * @var array
     */
    public $helpers = array('Html');

    /**
     * This controller does not use a model
     *
     * @var array
     */
    public $uses = array();

    /**
     * Displays a view
     *
     * @param mixed What page to display
     */
    public function display() {
        $path = func_get_args();

        $count = count($path);
        if (!$count) {
            $this->redirect('/');
        }
        $page = $subpage = $title_for_layout = null;

        if (!empty($path[0])) {
            $page = $path[0];
        }
        if (!empty($path[1])) {
            $subpage = $path[1];
        }
        if (!empty($path[$count - 1])) {
            $title_for_layout = Inflector::humanize($path[$count - 1]);
        }
        $this->set(compact('page', 'subpage', 'title_for_layout'));
        
  //      $this->Session->setFlash(__('Atención: Hemos realizado algunas mejoras al Sistema de Procesos de Pagos, por lo que le rogamos en caso de sufrir algunas dificultades se comunique a la brevedad posible con el Ing. Iván Garnica, Jefe de la Unidad de Sistemas'), 'default');
        
        switch ($this->Auth->user('rol')) {
            case 'finanzas':
                $this->redirect(array('controller' => 'procesos', 'action' => 'index', 'mayor' => 'no', 'estado' => 1));
                break;
            case 'contabilidad':
                $this->redirect(array('controller' => 'procesos', 'action' => 'pendientes', 'mayor' => 'no', 'estado' => 2));
                break;
            case 'analistas':
                $this->redirect(array('controller' => 'procesos', 'action' => 'pendientes', 'mayor' => 'no', 'estado' => 3));
                break;
            case 'administradores':
                $this->redirect(array('controller' => 'procesos', 'action' => 'pendientes', 'mayor' => 'no', 'estado' => 4));
                break;
            case 'tesoreria1':
                $this->redirect(array('controller' => 'procesos', 'action' => 'pendientes', 'mayor' => 'no', 'estado' => 6));
                break;
            case 'tesoreria2':
                $this->redirect(array('controller' => 'procesos', 'action' => 'pendientes', 'mayor' => 'no', 'estado' => 7));
                break;
            case 'economia':
                $this->redirect(array('controller' => 'procesos', 'action' => 'pendientes', 'mayor' => 'no', 'estado' => 10));
                break;
            case 'caja':
                $this->redirect(array('controller' => 'procesos', 'action' => 'pendientes', 'mayor' => 'no', 'estado' => 12));
                break;
            case 'archivo':
                $this->redirect(array('controller' => 'procesos', 'action' => 'pendientes', 'mayor' => 'no', 'estado' => 13));
                break;
            case 'reportes':
                $this->redirect(array('controller' => 'reportes', 'action' => 'por_secretarias'));
                break;
            case 'admin':
                if ($page != 'admin') {
                    $this->redirect(array('controller' => 'pages', 'action' => 'admin'));
                }
                break;
        }

        $this->render(implode('/', $path));
    }
    
    public function admin() {
        $this->set('page', 'admin');
    }
}