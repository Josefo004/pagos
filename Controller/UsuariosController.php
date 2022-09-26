<?php

App::uses('AppController', 'Controller');

/**
 * Usuarios Controller
 *
 * @property Usuario $Usuario
 */
class UsuariosController extends AppController {
    
    /**
     * beforeFilter method
     * 
     * @return void
     */
    /*public function beforeFilter() {
        parent::beforeFilter();
        $this->Auth->allow('*');
    }*/
    
    /**
     * This controller does not use a model
     *
     * @var array
     */
    public $uses = array('Usuario', 'ServidoresPublico');

    /**
     * login method
     * 
     * @return void
     */
    public function login() {
        if ($this->Auth->user('id')) {
            $this->redirect($this->Auth->redirect());
        }
        
        if ($this->request->is('post')) {
            if ($this->Auth->login()) {
                $this->Usuario->updateAll(
                        array(
                            'Usuario.ultimo_acceso' => "'" . date('Y-m-d H:i:s') . "'",
                            'Usuario.ultima_aplicacion' => "'PAGOS'",
                            'Usuario.ultima_ip' => "'" . $_SERVER['REMOTE_ADDR'] . "'"
                        ),
                        array(
                            'Usuario.id' => $this->Auth->user('id')
                        )
                );
                
                $this->Session->write('Usuario.nick', $this->data['Usuario']['nick']);
                $this->Session->write('Usuario.contrasenia', $this->data['Usuario']['contrasenia']);
                
                $this->redirect($this->Auth->redirect());
            } else {
                $this->Session->setFlash('Sus datos de acceso son incorrectos. Por favor, inténtelo nuevamente.');
            }
        }
    }

    /**
     * logout method
     * 
     * @return void
     */
    public function logout(){
        $this->layout = 'login';
        $this->Session->destroy();
        $this->Session->setFlash('Salio del sistema', 'default');
        $this->redirect($this->Auth->logout());
    }
    
    /** 
     * Método datos
     * 
     * @return void
     */
    public function datos() {
        if (isset($this->params['requested'])) {
            $usuario = $this->ServidoresPublico->find('first', array(
                'conditions' => array('Usuario.id' => $this->Auth->user('id')), 
                'fields' => array('ServidoresPublico.nombres', 'ServidoresPublico.apellidos'),
            ));
            
            return $usuario;
        }
    }
    
    /**
     * Método contrasenia
     * 
     * @return void
     */
    public function contrasenia() {
        if ($this->request->is('post')) {
            $this->Usuario->id = $this->Auth->user('id');
            $this->request->data['Usuario']['contrasenia'] = $this->data['Usuario']['contrasenia_nueva2'];
            if ($this->Usuario->save($this->request->data)) {
                $this->Session->setFlash(__('La contraseña fue cambiada correctamente.'));
                $this->redirect(array('controller' => 'pages', 'action' => 'admin'));
            } else {
                $this->Session->setFlash(__('La contraseña no pudo ser modificada. Por favor, verifique e intentelo nuevamente.'));
            }
        }
    }
    
    /**
     * admin_index method
     *
     * @return void
     */
    public function index() {
        $params = $this->params['named'];
        $params['page'] = empty($params['page']) ? 1 : $params['page'];
        $params['filtro'] = empty($params['filtro']) ? 'ci' : $params['filtro'];
        $params['texto'] = empty($params['texto']) ? '' : $params['texto'];
        $params['rol'] = empty($params['rol']) ? '' : $params['rol'];
        $this->set('params', $params);
        
        $conditions['ServidoresPublico.usuario_id <>'] = $this->Auth->user('id');
        if (!empty($params['filtro']) && !empty($params['texto'])) {
            $conditions['ServidoresPublico.' .  $params['filtro'] . ' ILIKE'] = '%' . $params['texto'] . '%';
        }
        
        if (!empty($params['rol'])) {
            $conditions['Usuario.rol'] = $params['rol'];
        }
        
        $this->ServidoresPublico->recursive = 0;
        $this->paginate = array(
            'ServidoresPublico' => array(
                'limit' => 20,
                'fields' => array('id', 'nombres', 'apellidos', 'ci', 'usuario_id', 'Usuario.nick', 'Usuario.rol', 'Dependencia.nombre'),
                'conditions' => $conditions,
                'order' => array('Usuario.rol' => 'ASC')
            )
        );
        $this->set('servidores_publicos', $this->paginate('ServidoresPublico'));
    }

    /**
     * nuevo method
     *
     * @return void
     */
    public function nuevo() {
        if ($this->request->is('post')) {
            $this->Usuario->create();
            if ($this->Usuario->save($this->request->data)) {
                $this->Session->setFlash(__('El Usuario ha sido guardado'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('El Usuario no pudo ser guardado. Por favor, inténtelo nuevamente.'));
            }
        }
        $dependencias = $this->ServidoresPublico->Dependencia->find('list', array('order' => array('Dependencia.nombre' => 'ASC')));
        $this->set(compact('dependencias'));
    }

    /**
     * editar method
     *
     * @param string $id
     * @return void
     */
    public function editar($id = null) {
        $this->Usuario->id = $id;
        if (!$this->Usuario->exists()) {
            throw new NotFoundException(__('Usuario inválido'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Usuario->save($this->request->data)) {
                $this->Session->setFlash(__('El Usuario ha sido guardado'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('El Usuario no pudo ser guardado. Por favor, inténtelo nuevamente.'));
            }
        } else {
            $this->Usuario->recursive = 0;
            $this->request->data = $this->Usuario->read(null, $id);
        }
        
        $dependencias = $this->ServidoresPublico->Dependencia->find('list', array('order' => array('Dependencia.nombre' => 'ASC'), 'recursive' => 0));
        $this->set(compact('dependencias'));
    }

    /**
     * eliminar method
     *
     * @param string $id
     * @return void
     */
    public function eliminar($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->Usuario->id = $id;
        if (!$this->Usuario->exists()) {
            throw new NotFoundException(__('Usuario inválido'));
        }
        if ($this->Usuario->delete()) {
            $this->Session->setFlash(__('El Usuario fue eliminado'));
            $this->redirect($this->referer());
        }
        $this->Session->setFlash(__('El Usuario no pudo ser eliminado'));
        $this->redirect(array('action' => 'index'));
    }
    
    /**
     *
     * @return boolean 
     */
    public function isAuthorized() {
        if ($this->Auth->user('rol') == 'admin') {
            return true;
        }
        
        if (in_array($this->action, array('contrasenia', 'datos', 'login', 'logout'))) {
            return true;
        }
        
        $this->Session->setFlash(__('No tiene los suficientes permisos para ingresar a este módulo'));
        return false;
    }

}