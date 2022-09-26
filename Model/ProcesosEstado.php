<?php

App::uses('AppModel', 'Model');

/**
 * ProcesosEstado Model
 *
 * @property Proceso $Proceso
 * @property Estado $Estado
 */
class ProcesosEstado extends AppModel {
    
    public $useTable = 'pag_procesos_estados';

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'proceso_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
        'estado_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
            //'message' => 'Your custom message here',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
    );

    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'Proceso' => array(
            'className' => 'Proceso',
            'foreignKey' => 'proceso_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Estado' => array(
            'className' => 'Estado',
            'foreignKey' => 'estado_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
    );

}
