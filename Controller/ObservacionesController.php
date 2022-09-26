<?php
App::uses('AppController', 'Controller');
/**
 * Observaciones Controller
 *
 * @property Observacione $Observacione
 */
class ObservacionesController extends AppController {


/**
 * index method
 *
 * @return void
 */
	public function index() {
		$this->Observacione->recursive = 0;
		$this->set('observaciones', $this->paginate());
	}

/**
 * view method
 *
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		$this->Observacione->id = $id;
		if (!$this->Observacione->exists()) {
			throw new NotFoundException(__('Invalid observacione'));
		}
		$this->set('observacione', $this->Observacione->read(null, $id));
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
			$this->Observacione->create();
			if ($this->Observacione->save($this->request->data)) {
				$this->Session->setFlash(__('The observacione has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The observacione could not be saved. Please, try again.'));
			}
		}
		$procesos = $this->Observacione->Proceso->find('list');
		$usuarios = $this->Observacione->Usuario->find('list');
		$this->set(compact('procesos', 'usuarios'));
	}

/**
 * edit method
 *
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		$this->Observacione->id = $id;
		if (!$this->Observacione->exists()) {
			throw new NotFoundException(__('Invalid observacione'));
		}
		if ($this->request->is('post') || $this->request->is('put')) {
			if ($this->Observacione->save($this->request->data)) {
				$this->Session->setFlash(__('The observacione has been saved'));
				$this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The observacione could not be saved. Please, try again.'));
			}
		} else {
			$this->request->data = $this->Observacione->read(null, $id);
		}
		$procesos = $this->Observacione->Proceso->find('list');
		$usuarios = $this->Observacione->Usuario->find('list');
		$this->set(compact('procesos', 'usuarios'));
	}

/**
 * delete method
 *
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		if (!$this->request->is('post')) {
			throw new MethodNotAllowedException();
		}
		$this->Observacione->id = $id;
		if (!$this->Observacione->exists()) {
			throw new NotFoundException(__('Invalid observacione'));
		}
		if ($this->Observacione->delete()) {
			$this->Session->setFlash(__('Observacione deleted'));
			$this->redirect(array('action'=>'index'));
		}
		$this->Session->setFlash(__('Observacione was not deleted'));
		$this->redirect(array('action' => 'index'));
	}
}
