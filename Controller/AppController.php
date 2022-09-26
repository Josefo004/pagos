<?php
/**
 * Application level Controller
 *
 * This file is application-wide controller file. You can put all
 * application-wide controller-related methods here.
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

App::uses('Controller', 'Controller');

/**
 * Application Controller
 *
 * Add your application-wide methods in the class below, your controllers
 * will inherit them.
 *
 * @package       app.Controller
 */
class AppController extends Controller {
    public $uses = array('Usuario');
    
    public $components = array(
        'Session',
        'Auth'
    );
    
    public $helpers = array('Html', 'Form', 'Session');

    public function beforeFilter() {
        /** Configure AuthComponent */     
        $this->Auth->loginAction = array('controller' => 'usuarios', 'action' => 'login');
        $this->Auth->loginRedirect = array('controller' => 'pages', 'action' => 'admin');
        $this->Auth->logoutRedirect = array('controller' => 'usuarios', 'action' => 'login');
        $this->Auth->loginError = 'El nombre de usuario y/o la contraseña no son correctos. Por favor, inténtale de nuevo';
        $this->Auth->authError = 'Para entrar a la zona de administración debe autenticarse';
        $this->Auth->allowedActions = array('login', 'form', 'busqueda', 'busqueda_web', 'ver', 'ver_web');
        $this->Auth->authenticate = array(
            'Form' => array(
                'userModel' => 'Usuario',
                'fields' => array('username' => 'nick', 'password' => 'contrasenia')
            )
        );
        $this->Auth->authorize = array('Controller');
        $this->Session->write('Auth.redirect', null);
        
    }
    
    public function isAuthorized() {
        if ($this->Auth->user('estado') == '1') {
            return true;
        }

        $this->layout = 'login';
        $this->Session->destroy();
        $this->Session->setFlash(__('Su cuenta está dada de baja, comuníquese con el administrador para mayor información'), 'default');
        $this->redirect($this->Auth->logout());

        return true;
    }
}
