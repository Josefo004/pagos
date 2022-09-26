<?php

App::uses('AppModel', 'Model');

/**
 * TipoDependencia Model
 *
 */
class TipoDependencia extends AppModel {

    public $useTable = 'per_tipo_dependencias';

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'nombre';
    public $hasMany = array(
        'Dependencia' => array(
            'className' => 'Dependencia',
            'foreignKey' => 'tipo_dependencia_id',
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