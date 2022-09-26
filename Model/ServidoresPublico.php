<?php

App::uses('AppModel', 'Model');

/**
 * ServidoresPublico Model
 *
 * @property Dependencia $Dependencia
 */
class ServidoresPublico extends AppModel {

    public $useTable = 'per_servidores_publicos';
    
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
        'ci' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Introduzca el Documento',
            ),
        ),
        'nombres' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Introduzca el Nombre',
            ),
        ),
        'apellidos' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Introduzca el(los) Apellidos',
            ),
        ),
//        'nro_proceso' => array(
//            'numeric' => array(
//                'rule' => array('numeric'),
//                'message' => 'Introduzca el Nro de Proceso de Pago',
//            ),
//            'validarNroProceso' => array (
//                'rule' => array ('validarNroProceso'),
//                'message' => 'El Nro de Proceso ya existe',
//                'on' => 'create',
//            ),
//        )
    );
    
    public $virtualFields = array(
        'nombre' => 'ServidoresPublico.apellidos || \' \' || ServidoresPublico.nombres'
    );
    
    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * belongsTo associations
     *
     * @var array
     */
    public $belongsTo = array(
        'Dependencia' => array(
            'className' => 'Dependencia',
            'foreignKey' => 'dependencia_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Usuario' => array(
            'className' => 'Usuario',
            'foreignKey' => 'usuario_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        )
    );

}
