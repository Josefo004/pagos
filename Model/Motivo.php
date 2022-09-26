<?php

App::uses('AppModel', 'Model');

/**
 * Motivo Model
 *
 * @property Proceso $Proceso
 */
class Motivo extends AppModel {
    
    public $useTable = 'pag_motivos';

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'nombre';

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'nombre' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Introduzca el Nombre',
            ),
        ),
    );

    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = array(
        'Proceso' => array(
            'className' => 'Proceso',
            'foreignKey' => 'motivo_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        )
    );

}
