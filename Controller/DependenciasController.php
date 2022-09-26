<?php

App::uses('AppController', 'Controller');

/**
 * Dependencias Controller
 *
 * @property Dependencia $Dependencia
 */
class DependenciasController extends AppController {
    
    /**
     * This controller does not use a model
     *
     * @var array
     */
    public $uses = array('Dependencia', 'Proceso', 'ServidoresPublico');

    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $params = $this->params['named'];
        $params['page'] = empty($params['page']) ? 1 : $params['page'];
        $this->set('params', $params);

        $this->Dependencia->recursive = 0;
        $this->paginate = array(
            'Dependencia' => array('limit' => 20,
                'order' => array('Dependencia.nombre' => 'asc'),
                'fields' => array(
                    'id', 'nombre', 'sigla', 'tipo_dependencia_id', 
                    'TipoDependencia.id', 'TipoDependencia.nombre'
                ),
            )
        );
        $this->set('dependencias', $this->paginate());
    }

    /**
     * view method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function ver($id = null) {
        $this->Dependencia->id = $id;
        if (!$this->Dependencia->exists()) {
            throw new NotFoundException(__('Dependencia inválida'));
        }
        $this->set('dependencia', $this->Dependencia->read(null, $id));
    }

    /**
     * add method
     *
     * @return void
     */
    public function nuevo() {
        if ($this->request->is('post')) {
            $this->Dependencia->create();
            if ($this->Dependencia->save($this->request->data)) {
                $this->Session->setFlash(__('La Dependencia ha sido guardada'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('La Dependencia no pudo ser guardad. Por favor, inténtelo nuevamente.'));
            }
        }
        $dependencias = $this->Dependencia->SupDependencia->find('list', array('order' => array('SupDependencia.nombre' => 'ASC')));
        $tipoDependencias = $this->Dependencia->TipoDependencia->find('list');
        $this->set(compact('dependencias', 'tipoDependencias'));
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function editar($id = null) {
        $this->Dependencia->id = $id;
        if (!$this->Dependencia->exists()) {
            throw new NotFoundException(__('Dependencia inválida'));
        }
        
        $this->Dependencia->unbindModel(
            array('hasMany' => array('ServidoresPublico', 'SubDependencia'))
        );
        
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Dependencia->save($this->request->data)) {
                $this->Session->setFlash(__('La Dependencia ha sido guardada'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('La Dependencia no pudo ser guardada. Por favor, inténtelo nuevamente.'));
            }
        } else {
            $this->request->data = $this->Dependencia->read(null, $id);
        }
        $dependencias = $this->Dependencia->SupDependencia->find('list');
        $tipoDependencias = $this->Dependencia->TipoDependencia->find('list');
        $this->set(compact('dependencias', 'tipoDependencias'));
    }

    /**
     * delete method
     *
     * @throws MethodNotAllowedException
     * @throws NotFoundException
     * @param string $id
     * @return void
     */
    public function eliminar($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->Dependencia->id = $id;
        if (!$this->Dependencia->exists()) {
            throw new NotFoundException(__('Dependencia inválida'));
        }
        
        $eliminar = true;
        $existe1 = $this->Dependencia->find('count', array('conditions' => array('Dependencia.dependencia_id' => $id)));
        if ($existe1 == 0) {
            $existe2 = $this->Proceso->find('count', array('conditions' => array('Proceso.dependencia_id' => $id)));
            if ($existe2 == 0) {
                $existe3 = $this->ServidoresPublico->find('count', array('conditions' => array('ServidoresPublico.dependencia_id' => $id)));
                if ($existe3 != 0) {
                    $eliminar = false;
                }
            } else {
                $eliminar = false;
            }
        } else {
            $eliminar = false;
        }
        
        if ($eliminar) {
            if ($this->Dependencia->delete()) {
                $this->Session->setFlash(__('La Dependencia ha sido eliminada'));
                $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(__('La Dependencia no pudo ser eliminada'));
            $this->redirect(array('action' => 'index'));
        } else {
            $this->Session->setFlash(__('La Dependencia no puede ser eliminada debido a que esta tiene registros relacionados'));
            $this->redirect(array('action' => 'index'));
        }
    }

}
