<?php

App::uses('AppModel', 'Model');

/**
 * Proceso Model
 *
 * @property Dependencia $Dependencia
 * @property ServidoresPublico $ServidoresPublico
 * @property Motivo $Motivo
 * @property Beneficiario $Beneficiario
 * @property Usuario $Usuario
 * @property Observacione $Observacione
 * @property Estado $Estado
 */
class Proceso extends AppModel {

    public $useTable = 'pag_procesos';

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'cite';

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'cite' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Introduzca el CITE',
            //'allowEmpty' => false,
            //'required' => false,
            //'last' => false, // Stop validation after this rule
            //'on' => 'create', // Limit validation to 'create' or 'update' operations
            ),
        ),
        'nro_proceso' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Introduzca el Nro de Proceso de Pago',
            ),
            'validarNroProceso' => array (
                'rule' => array ('validarNroProceso'),
                'message' => 'El Nro de Proceso ya existe',
                'on' => 'create',
            ),
        ),
        'fecha_emision' => array(
            'date' => array(
                'rule' => array('date'),
                'message' => 'Seleccione la Fecha de Emisión',
            ),
        ),
        'nro_preventivo' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Introduzca el Nro de Preventivo',
            ),
        ),
        'monto' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Introduzca el Monto',
            ),
            'money' => array(
                'rule' => array('money'),
                'message' => 'Debe introducir un valor correcto',
            ),
        ),
        'dependencia_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Seleccione una Dependencia',
            ),
        ),
        'motivo_id' => array(
            'numeric' => array(
                'rule' => array('numeric'),
                'message' => 'Seleccione un motivo',
            ),
        ),
        'beneficiario_documento' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Seleccione un Beneficiario',
            ),
        ),
        'beneficiario_nombre' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Seleccione un Beneficiario',
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
        'Dependencia' => array(
            'className' => 'Dependencia',
            'foreignKey' => 'dependencia_id',
            'conditions' => '',
            'fields' => '',
            'order' => ''
        ),
        'Motivo' => array(
            'className' => 'Motivo',
            'foreignKey' => 'motivo_id',
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
        )
//        'Usuario' => array(
//            'className' => 'Usuario',
//            'foreignKey' => 'usuario_id',
//            'conditions' => '',
//            'fields' => '',
//            'order' => ''
//        )
    );

    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = array(
        'Observacione' => array(
            'className' => 'Observacione',
            'foreignKey' => 'proceso_id',
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

    /**
     * hasAndBelongsToMany associations
     *
     * @var array
     */
    public $hasAndBelongsToMany = array(
        'Estado' => array(
            'className' => 'Estado',
            'joinTable' => 'pag_procesos_estados',
            'foreignKey' => 'proceso_id',
            'associationForeignKey' => 'estado_id',
            'unique' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'finderQuery' => '',
            'deleteQuery' => '',
            'insertQuery' => ''
        )
    );
    
    /**
     * Método para validar el nro_proceso
     */
    public function validarNroProceso() {
        if (empty($this->data['Proceso']['fecha_emision'])) {
            $anio = date('Y');
        } else {
            $anio_1 = explode('-', $this->data['Proceso']['fecha_emision']);
            $anio = $anio_1[0];
        }
        $count = $this->find('count', array(
            'conditions' => array(
                'Proceso.nro_proceso' => $this->data['Proceso']['nro_proceso'],
                'Proceso.fecha_emision >=' => $anio . '-01-01',
                'Proceso.fecha_emision <=' => $anio . '-12-31'
            )
        ));
        if ($count == 0) {
            return true;
        } else {
            return false;
        }
    }
    
}
