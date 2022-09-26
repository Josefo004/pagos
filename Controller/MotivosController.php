<?php

App::uses('AppController', 'Controller');

/**
 * Motivos Controller
 *
 * @property Motivo $Motivo
 */
class MotivosController extends AppController {

    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $params = $this->params['named'];
        $params['page'] = empty($params['page']) ? 1 : $params['page'];
        $this->set('params', $params);

        $this->Motivo->recursive = 0;
        $this->set('motivos', $this->paginate());
    }

    /**
     * add method
     *
     * @return void
     */
    public function nuevo() {
        if ($this->request->is('post')) {
            $this->Motivo->create();
            if ($this->Motivo->save($this->request->data)) {
                $this->Session->setFlash(__('El Motivo ha sido guardado'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('El Motivo no pudo ser guardado. Por favor, inténtelo nuevamente.'));
            }
        }
    }

    /**
     * edit method
     *
     * @param string $id
     * @return void
     */
    public function editar($id = null) {
        $this->Motivo->id = $id;
        if (!$this->Motivo->exists()) {
            throw new NotFoundException(__('Motivo inválido'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Motivo->save($this->request->data)) {
                $this->Session->setFlash(__('El Motivo ha sido guardado'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('El Motivo no pudo ser guardado. Por favor, inténtelo nuevamente.'));
            }
        } else {
            $this->request->data = $this->Motivo->read(null, $id);
        }
    }

    /**
     * delete method
     *
     * @param string $id
     * @return void
     */
    public function eliminar($id = null) {
        if (!$this->request->is('post')) {
            throw new MethodNotAllowedException();
        }
        $this->Motivo->id = $id;
        if (!$this->Motivo->exists()) {
            throw new NotFoundException(__('Motivo inválido'));
        }
        $count = $this->Motivo->Proceso->find('count', array(
            'conditions' => array('Proceso.motivo_id' => $id)
        ));
        if ($count == 0) {
            if ($this->Motivo->delete()) {
                $this->Session->setFlash(__('El Motivo ha sido eliminado'));
                $this->redirect(array('action' => 'index'));
            }
            $this->Session->setFlash(__('El Motivo no pudo ser eliminado'));
        } else {
            $this->Session->setFlash(__('El Motivo no pudo ser eliminado debido a que existen Procesos registrados con el mismo'));
        }
        $this->redirect(array('action' => 'index'));
    }

}
