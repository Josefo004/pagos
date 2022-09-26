<?php

App::uses('AppModel', 'Model');

/**
 * Dependencia Model
 *
 * @property Dependencia $Dependencia
 * @property Dependencia $Dependencia
 * @property Empleado $Empleado
 */
class Dependencia extends AppModel {

    public $useTable = 'per_dependencias';

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'nombre';
    
//    public $virtualFields = array(
//        'nombre_sigla' => 'Dependencia.nombre || \' \' || \'[\' || Dependencia.sigla || \']\''
//    );

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
        'sigla' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Introduzca la sigla',
            ),
            'alphanumeric' => array(
                'rule' => array('alphanumeric'),
                'message' => 'Introduzca una sigla válida (alfanumérico)',
            ),
        ),
        'telefono' => array(
            'maxLength' => array(
                'rule' => array('maxLength', 8),
                'message' => 'El Teléfono no debe superar los 8 dígitos',
                'allowEmpty' => true
            ),
            'minLength' => array(
                'rule' => array('minLength', 7),
                'message' => 'El Teléfono no debe ser menos a 7 dígitos',
                'allowEmpty' => true
            )
        ),
        'correo' => array(
            'email' => array(
                'rule' => array('email'),
                'message' => 'Introduzca un correo electrónico válido'
            ),
        ),
        'dependencia_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Seleccione la Dependencia padre',
            ),
        ),
        'tipo_dependencia_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Seleccione el Tipo de Dependencia',
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
        'SupDependencia' => array(
            'className' => 'Dependencia',
            'foreignKey' => 'dependencia_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'TipoDependencia' => array(
            'className' => 'TipoDependencia',
            'foreignKey' => 'tipo_dependencia_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = array(
        'SubDependencia' => array(
            'className' => 'Dependencia',
            'foreignKey' => 'dependencia_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'ServidoresPublico' => array(
            'className' => 'ServidoresPublico',
            'foreignKey' => 'dependencia_id',
            'dependent' => false,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'Proceso' => array(
            'className' => 'Proceso',
            'foreignKey' => 'dependencia_id',
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
