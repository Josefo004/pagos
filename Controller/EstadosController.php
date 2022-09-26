<?php

App::uses('AppController', 'Controller');

/**
 * Estados Controller
 *
 * @property Estado $Estado
 */
class EstadosController extends AppController {

    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $this->Estado->recursive = 0;
        $this->set('estados', $this->paginate());
    }

    /**
     * add method
     *
     * @return void
     */
    public function nuevo() {
        if ($this->request->is('post')) {
            $this->Estado->create();
            if ($this->Estado->save($this->request->data)) {
                $this->Session->setFlash(__('The estado has been saved'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The estado could not be saved. Please, try again.'));
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
        $this->Estado->id = $id;
        if (!$this->Estado->exists()) {
            throw new NotFoundException(__('Invalid estado'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            if ($this->Estado->save($this->request->data)) {
                $this->Session->setFlash(__('The estado has been saved'));
                $this->redirect(array('action' => 'index'));
            } else {
                $this->Session->setFlash(__('The estado could not be saved. Please, try again.'));
            }
        } else {
            $this->request->data = $this->Estado->read(null, $id);
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
        $this->Estado->id = $id;
        if (!$this->Estado->exists()) {
            throw new NotFoundException(__('Invalid estado'));
        }
        if ($this->Estado->delete()) {
            $this->Session->setFlash(__('Estado deleted'));
            $this->redirect(array('action' => 'index'));
        }
        $this->Session->setFlash(__('Estado was not deleted'));
        $this->redirect(array('action' => 'index'));
    }

}
