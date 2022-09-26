<?php

App::uses('AppModel', 'Model');

/**
 * Usuario Model
 *
 * @property Empleado $Empleado
 * @property Sistema $Sistema
 */
class Usuario extends AppModel {
    
    public $useTable = 'int_usuarios';

    /**
     * Display field
     *
     * @var string
     */
    public $displayField = 'nick';
    
    //The Associations below have been created with all possible keys, those that are not needed can be removed

    /**
     * Validation rules
     *
     * @var array
     */
    public $validate = array(
        'nick' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Debe introducir su ID de usuario',
            ),
        ),
        'contrasenia' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Debe introducir su Contraseña',
                'on' => 'create',
            ),
            'minlenght' => array(
                'rule' => array('minLength', '6'),
                'message' => 'La nueva contraseña debe tener al menos 6 caracteres.',
            ),
        ),
        'contrasenia2' => array(
            'minlenght' => array(
                'rule' => array('minLength', '6'),
                'message' => 'La nueva contraseña debe tener al menos 6 caracteres.',
                'allowEmpty' => true,
                'on' => 'update'
            ),
        ),
        'contrasenia_actual' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Debe introducir la contraseña actual',
            ),
            'validar' => array (
                'rule' => array ('validarContrasenia'),
                'message' => 'La contraseña es incorrecta'
            ),
        ),
        'contrasenia_nueva1' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Debe introducir la nueva contraseña',
            ),
            'minlenght' => array(
                'rule' => array('minLength', '6'),
                'message' => 'La nueva contraseña debe tener al menos 6 caracteres.',
            ),
            'confirmar' => array (
                'rule' => array ('confirmarContrasenia'),
                'message' => 'La nueva contraseña debe coincidir con la confirmación'
            ),
        ),
        'contrasenia_nueva2' => array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Debe introducir la confirmación de su nueva contraseña',
            ),
            'confirmar' => array (
                'rule' => array ('confirmarContrasenia'),
                'message' => 'La nueva contraseña debe coincidir con la confirmación'
            ),
        ),
        'rol' =>  array(
            'notempty' => array(
                'rule' => array('notempty'),
                'message' => 'Debe seleccionar el Rol de Usuario',
            ),
        )
    );
    
    /**
     * hasOne associations
     *
     * @var array
     */
    public $hasOne = array(
        'ServidoresPublico' => array(
            'className' => 'ServidoresPublico',
            'foreignKey' => 'usuario_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
    );
    
    /**
     * hasMany associations
     *
     * @var array
     */
    public $hasMany = array(
        'UsuarioEnvio' => array(
            'className' => 'ProcesosEstado',
            'foreignKey' => 'usuario_envio_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
        'UsuarioRecepcion' => array(
            'className' => 'ProcesosEstado',
            'foreignKey' => 'usuario_recepcion_id',
            'dependent' => true,
            'conditions' => '',
            'fields' => '',
            'order' => '',
            'limit' => '',
            'offset' => '',
            'exclusive' => '',
            'finderQuery' => '',
            'counterQuery' => ''
        ),
    );
       
    public function beforeSave($options = array()) {
        if (!empty($this->data['Usuario']['contrasenia'])) {
            $this->data['Usuario']['contrasenia'] = AuthComponent::password($this->data['Usuario']['contrasenia']);
        } elseif (!empty($this->data['Usuario']['contrasenia2'])) {
            $this->data['Usuario']['contrasenia'] = AuthComponent::password($this->data['Usuario']['contrasenia2']);
        }
        return true;
    }
    
    /**
     * Método para validar la contraseña actual
     */
    public function validarContrasenia() {
        $usuario = $this->find('first', array(
                'conditions' => array('Usuario.id' => AuthComponent::user('id')), 
                'fields' => array('contrasenia')
                )
        );
        if ($usuario['Usuario']['contrasenia'] == AuthComponent::password($this->data['Usuario']['contrasenia_actual'])) {
            return true;
        }
        return false;
    }
    
    /**
     * Método para validar la nueva contraseña y su confirmación son iguales
     */
    public function confirmarContrasenia() {
        if ($this->data['Usuario']['contrasenia_nueva1'] == $this->data['Usuario']['contrasenia_nueva2']) {
            return true;
        }
        return false;
    }
    
    /**
     *
     * @return boolean 
     */
    public function beforeDelete($cascade = true) {
        $count1 = $this->UsuarioEnvio->find('count', array(
            'conditions' => array('UsuarioEnvio.usuario_envio_id' => $this->id)
        ));
        if ($count1 == 0) {
            $count2 = $this->UsuarioRecepcion->find('count', array(
                'conditions' => array('UsuarioRecepcion.usuario_recepcion_id' => $this->id)
            ));
            if ($count2 == 0) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
    
    /**
     *
     * @param type $created
     * @return boolean 
     */
    public function afterSave($created) {
        if ($created) {
            $this->ServidoresPublico->create();
            $this->data['ServidoresPublico']['usuario_id'] = $this->id;
            if ($this->ServidoresPublico->save($this->data)) {
                return true;
            } 
            return false;
        } else {
            $this->ServidoresPublico->id = $this->data['ServidoresPublico']['id'];
            if (!$this->ServidoresPublico->exists()) {
                return false;
            }
            $this->ServidoresPublico->save($this->data);
            return true;
        }
    }
           
}
