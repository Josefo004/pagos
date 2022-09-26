<?php

App::uses('AppController', 'Controller');

/**
 * Procesos Controller
 *
 * @property Proceso $Proceso
 */
class ProcesosController extends AppController {

    /**
     * This controller does not use a model
     *
     * @var array
     */
    public $uses = array('Proceso', 'ProcesosEstado', 'ServidoresPublico', 'Observacione');

    /**
     * index method
     *
     * @return void
     */
    public function index() {
        $params = $this->params['named'];
        $params['filtro'] = empty($params['filtro']) ? 'nro_proceso' : $params['filtro'];
        $params['buscar'] = empty($params['buscar']) ? '' : strtoupper($params['buscar']);
        $params['dependencia'] = empty($params['dependencia']) ? '' : $params['dependencia'];
        $params['motivo'] = empty($params['motivo']) ? '' : $params['motivo'];
        $params['estado'] = empty($params['estado']) ? '' : $params['estado'];
        $params['page'] = empty($params['page']) ? 1 : $params['page'];
        $this->set('params', $params);

        $dependencias = $this->Proceso->Dependencia->find('list', array(
            'conditions' => array(
                'Dependencia.tipo_dependencia_id' => array(2, 4),
                'Dependencia.sigla <>' => NULL
            ),
            'fields' => array('Dependencia.id', 'Dependencia.sigla'),
        ));
        $motivos = $this->Proceso->Motivo->find('list', array(
            'fields' => array('Motivo.id', 'Motivo.nombre'),
        ));

        $this->set(compact('dependencias', 'motivos', 'estados'));

        /** Cantidades de procesos por estado [inicio] */
        $monto = ($params['mayor'] == 'si')?'Proceso.monto >':'Proceso.monto <=';
        $pendientes = array();
        switch ($this->Auth->user('rol')) {
            case 'finanzas':
                $pendientes['9'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 9,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));
                $pendientes['11'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 11,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));
                break;
            case 'contabilidad':
                $pendientes['2'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 2,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));
                $pendientes['5'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 5,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));
                $pendientes['8'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 8,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));

                /** Analistas */
                if ($params['estado'] == '2') {
                    $usuarios = $this->ServidoresPublico->find('all', array(
                        'conditions' => array(
                            'Usuario.rol' => 'analistas'
                        ),
                        'fields' => array(
                            'Usuario.id',
                            'ServidoresPublico.nombre'
                        ),
                        'order' => 'ServidoresPublico.nombre'
                    ));
                    $this->set('usuarios', Set::combine($usuarios, '{n}.Usuario.id', '{n}.ServidoresPublico.nombre'));
                }
                break;
            case 'analistas':
                $pendientes['3'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 3,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000,
                        'Proceso.usuario_analista_id' => $this->Auth->user('id')
                    )
                ));
                break;
            case 'administradores':
                $dependencia = $this->ServidoresPublico->find('first', array(
                    'conditions' => array(
                        'ServidoresPublico.usuario_id' => $this->Auth->user('id')
                    )
                ));
                $pendientes['4'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 4,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000,
                        'Proceso.dependencia_id' => $dependencia['ServidoresPublico']['dependencia_id']
                    )
                ));
                break;
            case 'tesoreria1':
                $pendientes['6'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 6,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));
                break;
            case 'tesoreria2':
                $pendientes['7'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 7,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));
                break;
            case 'economia':
                $pendientes['10'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 10,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));
                break;
            case 'caja':
                $pendientes['12'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 12,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));
                break;
            case 'archivo':
                $pendientes['13'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 13,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));
                break;
        }
        $this->set('pendientes', $pendientes);
        /** Cantidades de procesos por estado [fin] */

        /** Condiciones de filtrado */
        $conditions = array('AND' => array(
            'Proceso.fecha_emision >=' => Configure::read('App.year_act') . '-01-01',
            'Proceso.fecha_emision <=' => Configure::read('App.year_act') . '-12-31',
        ));
        if ($params['mayor'] == 'si') {
            $conditions['Proceso.monto >'] = 10000;
        } else {
            $conditions['Proceso.monto <='] = 10000;
        }
        switch ($params['filtro']) {
            case 'cite':
            case 'beneficiario_documento':
            case 'beneficiario_nombre':
            case 'referencia':
                $conditions['Proceso.' . $params['filtro'] .' ILIKE '] = '%' . $params['buscar'] . '%';
                break;
            case 'nro_proceso':
            case 'nro_preventivo':
            case 'monto':
                if (!empty($params['buscar'])) {
                    $conditions['Proceso.' . $params['filtro']] = $params['buscar'];
                }
                break;
        }
        if (!empty($params['dependencia'])) {
            $conditions['Proceso.dependencia_id'] = $params['dependencia'];
        }
        if (!empty($params['motivo'])) {
            $conditions['Proceso.motivo_id'] = $params['motivo'];
        }
        if (!empty($params['estado'])) {
            $conditions['Proceso.estado_id'] = $params['estado'];
        }

        if ($this->Auth->user('rol') == 'analistas') {
            $conditions['Proceso.usuario_analista_id'] = $this->Auth->user('id');
        }
        if ($this->Auth->user('rol') == 'administradores') {
            $dependencia = $this->ServidoresPublico->find('first', array(
                'conditions' => array(
                    'ServidoresPublico.usuario_id' => $this->Auth->user('id')
                )
            ));
            $conditions['Proceso.dependencia_id'] = $dependencia['ServidoresPublico']['dependencia_id'];
        }

        $sort = empty($sort)?'nro_proceso':$sort;
        $direction = empty($direction)?'DESC':$direction;

        $this->Proceso->recursive = 0;
        $this->paginate = array(
            'Proceso' => array('limit' => 20,
                'conditions' => $conditions,
                'fields' => array(
                    'id', 'cite', 'nro_proceso',
                    'beneficiario_documento', 'beneficiario_nombre', 'monto', 'usuario_analista_id',
                    'Motivo.id', 'Motivo.nombre',
                    'ProcesosEstado.fecha_envio', 'ProcesosEstado.fecha_recepcion'
                ),
                'joins' => array(
                    array(
                        'table' => 'pag_procesos_estados',
                        'alias' => 'ProcesosEstado',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'ProcesosEstado.proceso_id = Proceso.id',
                            'ProcesosEstado.estado_id = Proceso.estado_id',
                            'ProcesosEstado.id = Proceso.ultimo_estado_id'
                        )
                    )
                ),
                'order' => array($sort => $direction)
            )
        );
        $this->set('procesos', $this->paginate());
    }

    /**
     * pendientes method
     *
     * @return void
     */
    public function pendientes() {
        $params = $this->params['named'];
        $params['filtro'] = empty($params['filtro']) ? 'nro_proceso' : $params['filtro'];
        $params['buscar'] = empty($params['buscar']) ? '' : strtoupper($params['buscar']);
        $params['dependencia'] = empty($params['dependencia']) ? '' : $params['dependencia'];
        $params['motivo'] = empty($params['motivo']) ? '' : $params['motivo'];
        $params['estado'] = empty($params['estado']) ? '' : $params['estado'];
        $params['page'] = empty($params['page']) ? 1 : $params['page'];
        $this->set('params', $params);

        $dependencias = $this->Proceso->Dependencia->find('list', array(
            'conditions' => array(
                'Dependencia.tipo_dependencia_id' => array(2, 4),
                'Dependencia.sigla <>' => NULL
            ),
            'fields' => array('Dependencia.id', 'Dependencia.sigla'),
        ));
        $motivos = $this->Proceso->Motivo->find('list', array(
            'fields' => array('Motivo.id', 'Motivo.nombre'),
        ));

        $this->set(compact('dependencias', 'motivos', 'estados'));

        /** Cantidades de procesos por estado [inicio] */
        $monto = ($params['mayor'] == 'si')?'Proceso.monto >':'Proceso.monto <=';
        switch ($this->Auth->user('rol')) {
            case 'finanzas':
                $pendientes['9'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 9,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));
                $pendientes['11'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 11,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));
                break;
            case 'contabilidad':
                $pendientes['2'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 2,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));
                $pendientes['5'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 5,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));
                $pendientes['8'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 8,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));

                /** Analistas */
                if ($params['estado'] == '2') {
                    $usuarios = $this->ServidoresPublico->find('all', array(
                        'conditions' => array(
                            'Usuario.rol' => 'analistas'
                        ),
                        'fields' => array(
                            'Usuario.id',
                            'ServidoresPublico.nombre'
                        ),
                        'order' => 'ServidoresPublico.nombre'
                    ));
                    $this->set('usuarios', Set::combine($usuarios, '{n}.Usuario.id', '{n}.ServidoresPublico.nombre'));
                }
                break;
            case 'analistas':
                $pendientes['3'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 3,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000,
                        'Proceso.usuario_analista_id' => $this->Auth->user('id')
                    )
                ));
                break;
            case 'administradores':
                $dependencia = $this->ServidoresPublico->find('first', array(
                    'conditions' => array(
                        'ServidoresPublico.usuario_id' => $this->Auth->user('id')
                    )
                ));
                $pendientes['4'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 4,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000,
                        'Proceso.dependencia_id' => $dependencia['ServidoresPublico']['dependencia_id']
                    )
                ));
                break;
            case 'tesoreria1':
                $pendientes['6'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 6,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));
                break;
            case 'tesoreria2':
                $pendientes['7'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 7,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));
                break;
            case 'economia':
                $pendientes['10'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 10,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));
                break;
            case 'caja':
                $pendientes['12'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 12,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));
                break;
            case 'archivo':
                $pendientes['13'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 13,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));
                break;
        }
        $this->set('pendientes', $pendientes);
        /** Cantidades de procesos por estado [fin] */

        /** Condiciones de filtrado */
        $conditions = array();
        if ($params['mayor'] == 'si') {
            $conditions['Proceso.monto >'] = 10000;
        } else {
            $conditions['Proceso.monto <='] = 10000;
        }
        switch ($params['filtro']) {
            case 'cite':
            case 'beneficiario_documento':
            case 'beneficiario_nombre':
            case 'referencia':
                $conditions['Proceso.' . $params['filtro'] .' ILIKE '] = '%' . $params['buscar'] . '%';
                break;
            case 'nro_proceso':
            case 'nro_preventivo':
            case 'monto':
                if (!empty($params['buscar'])) {
                    $conditions['Proceso.' . $params['filtro']] = $params['buscar'];
                }
                break;
        }
        if (!empty($params['dependencia'])) {
            $conditions['Proceso.dependencia_id'] = $params['dependencia'];
        }
        if (!empty($params['motivo'])) {
            $conditions['Proceso.motivo_id'] = $params['motivo'];
        }
        if (!empty($params['estado'])) {
            $conditions['Proceso.estado_id'] = $params['estado'];
        }

        if (AuthComponent::user('rol') == 'analistas') {
            $conditions['Proceso.usuario_analista_id'] = $this->Auth->user('id');
        }
        if (AuthComponent::user('rol') == 'administradores') {
            $dependencia = $this->ServidoresPublico->find('first', array(
                'conditions' => array(
                    'ServidoresPublico.usuario_id' => $this->Auth->user('id')
                )
            ));
            $conditions['Proceso.dependencia_id'] = $dependencia['ServidoresPublico']['dependencia_id'];
        }

        $sort = empty($sort)?'nro_proceso':$sort;
        $direction = empty($direction)?'DESC':$direction;

        $this->Proceso->recursive = 0;
        $this->paginate = array(
            'Proceso' => array('limit' => 20,
                'conditions' => $conditions,
                'order' => 'ProcesosEstado.fecha_envio DESC',
                'fields' => array(
                    'id', 'cite', 'nro_proceso', 'nro_preventivo',
                    'beneficiario_documento', 'beneficiario_nombre', 'monto', 'usuario_analista_id',
                    'Motivo.id', 'Motivo.nombre',
                    'ProcesosEstado.fecha_envio', 'ProcesosEstado.fecha_recepcion',
                    'ProcesosEstado.reingreso'
                ),
                'joins' => array(
                    array(
                        'table' => 'pag_procesos_estados',
                        'alias' => 'ProcesosEstado',
                        'type' => 'INNER',
                        'conditions' => array(
                            'ProcesosEstado.proceso_id = Proceso.id',
                            'ProcesosEstado.estado_id = Proceso.estado_id',
                            'ProcesosEstado.id = Proceso.ultimo_estado_id',
                            'ProcesosEstado.fecha_recepcion IS NULL'
                        )
                    )
                ),
                'order' => array($sort => $direction)
            )
        );

        $this->set('procesos', $this->paginate());
    }

    /**
     * observados method
     *
     * @return void
     */
    public function observados() {
        if ($this->Auth->user('rol') != 'analistas') {
            $this->Session->setFlash(__('No tiene los suficientes permisos para ingresar a esta URL'));
            $this->redirect($this->referer());
        }

        $params = $this->params['named'];
        $params['filtro'] = empty($params['filtro']) ? 'nro_proceso' : $params['filtro'];
        $params['buscar'] = empty($params['buscar']) ? '' : strtoupper($params['buscar']);
        $params['dependencia'] = empty($params['dependencia']) ? '' : $params['dependencia'];
        $params['motivo'] = empty($params['motivo']) ? '' : $params['motivo'];
        $params['estado'] = empty($params['estado']) ? '' : $params['estado'];
        $params['page'] = empty($params['page']) ? 1 : $params['page'];
        $this->set('params', $params);

        $dependencias = $this->Proceso->Dependencia->find('list', array(
            'conditions' => array(
                'Dependencia.tipo_dependencia_id' => array(2, 4),
                'Dependencia.sigla <>' => NULL
            ),
            'fields' => array('Dependencia.id', 'Dependencia.sigla'),
        ));
        $motivos = $this->Proceso->Motivo->find('list', array(
            'fields' => array('Motivo.id', 'Motivo.nombre'),
        ));

        $this->set(compact('dependencias', 'motivos', 'estados'));

        /** Cantidades de procesos por estado [inicio] */
        $monto = ($params['mayor'] == 'si')?'Proceso.monto >':'Proceso.monto <=';

        $pendientes['3'] = $this->ProcesosEstado->find('count', array(
            'conditions' => array(
                'ProcesosEstado.estado_id' => 3,
                'ProcesosEstado.fecha_recepcion' => NULL,
                $monto => 10000,
                'Proceso.usuario_analista_id' => $this->Auth->user('id')
            )
        ));

        $this->set('pendientes', $pendientes);
        /** Cantidades de procesos por estado [fin] */

        /** Condiciones de filtrado */
        $conditions = array();
        if ($params['mayor'] == 'si') {
            $conditions['Proceso.monto >'] = 10000;
        } else {
            $conditions['Proceso.monto <='] = 10000;
        }
        switch ($params['filtro']) {
            case 'cite':
            case 'beneficiario_documento':
            case 'beneficiario_nombre':
            case 'referencia':
                $conditions['Proceso.' . $params['filtro'] .' ILIKE '] = '%' . $params['buscar'] . '%';
                break;
            case 'nro_proceso':
            case 'nro_preventivo':
            case 'monto':
                if (!empty($params['buscar'])) {
                    $conditions['Proceso.' . $params['filtro']] = $params['buscar'];
                }
                break;
        }
        if (!empty($params['dependencia'])) {
            $conditions['Proceso.dependencia_id'] = $params['dependencia'];
        }
        if (!empty($params['motivo'])) {
            $conditions['Proceso.motivo_id'] = $params['motivo'];
        }
        if (!empty($params['estado'])) {
            $conditions['Proceso.estado_id'] = $params['estado'];
        }

        $conditions['Proceso.usuario_analista_id'] = $this->Auth->user('id');


        if ($params['estado'] == 3) {
            $conditions['Observacione.id <>'] = NULL;
        }

        $sort = empty($sort)?'nro_proceso':$sort;
        $direction = empty($direction)?'DESC':$direction;

        $this->Proceso->recursive = 0;
        $this->paginate = array(
            'Proceso' => array('limit' => 20,
                'conditions' => $conditions,
                'order' => array('Proceso.nro_proceso' => 'desc'),
                'fields' => array(
                    'id', 'cite', 'nro_proceso',
                    'beneficiario_documento', 'beneficiario_nombre', 'monto', 'usuario_analista_id',
                    'Motivo.id', 'Motivo.nombre',
                    'ProcesosEstado.fecha_envio', 'ProcesosEstado.fecha_recepcion',
                    'ProcesosEstado.reingreso'
                ),
                'joins' => array(
                    array(
                        'table' => 'pag_procesos_estados',
                        'alias' => 'ProcesosEstado',
                        'type' => 'INNER',
                        'conditions' => array(
                            'ProcesosEstado.proceso_id = Proceso.id',
                            'ProcesosEstado.estado_id = Proceso.estado_id',
                            'ProcesosEstado.id = Proceso.ultimo_estado_id',
                            'ProcesosEstado.fecha_recepcion IS NOT NULL'
                        )
                    ),
                    array(
                        'table' => 'pag_observaciones',
                        'alias' => 'Observacione',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Observacione.procesos_estados_id = Proceso.ultimo_estado_id'
                        )
                    )
                ),
                'order' => array($sort => $direction)
            )
        );
        $this->set('procesos', $this->paginate());
    }

    /**
     * recepcionados method
     *
     * @return void
     */
    public function recepcionados() {
        $params = $this->params['named'];
        $params['filtro'] = empty($params['filtro']) ? 'nro_proceso' : $params['filtro'];
        $params['buscar'] = empty($params['buscar']) ? '' : strtoupper($params['buscar']);
        $params['dependencia'] = empty($params['dependencia']) ? '' : $params['dependencia'];
        $params['motivo'] = empty($params['motivo']) ? '' : $params['motivo'];
        $params['estado'] = empty($params['estado']) ? '' : $params['estado'];
        $params['page'] = empty($params['page']) ? 1 : $params['page'];
        $this->set('params', $params);

        $dependencias = $this->Proceso->Dependencia->find('list', array(
            'conditions' => array(
                'Dependencia.tipo_dependencia_id' => array(2, 4),
                'Dependencia.sigla <>' => NULL
            ),
            'fields' => array('Dependencia.id', 'Dependencia.sigla'),
        ));
        $motivos = $this->Proceso->Motivo->find('list', array(
            'fields' => array('Motivo.id', 'Motivo.nombre'),
        ));

        $this->set(compact('dependencias', 'motivos', 'estados'));

        /** Cantidades de procesos por estado [inicio] */
        $monto = ($params['mayor'] == 'si')?'Proceso.monto >':'Proceso.monto <=';
        switch ($this->Auth->user('rol')) {
            case 'finanzas':
                $pendientes['9'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 9,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));
                $pendientes['11'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 11,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));
                break;
            case 'contabilidad':
                $pendientes['2'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 2,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));
                $pendientes['5'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 5,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));
                $pendientes['8'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 8,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));

                /** Analistas */
                if ($params['estado'] == '2') {
                    $usuarios = $this->ServidoresPublico->find('all', array(
                        'conditions' => array(
                            'Usuario.rol' => 'analistas'
                        ),
                        'fields' => array(
                            'Usuario.id',
                            'ServidoresPublico.nombre'
                        ),
                        'order' => 'ServidoresPublico.nombre'
                    ));
                    $this->set('usuarios', Set::combine($usuarios, '{n}.Usuario.id', '{n}.ServidoresPublico.nombre'));
                }
                break;
            case 'analistas':
                $pendientes['3'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 3,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000,
                        'Proceso.usuario_analista_id' => $this->Auth->user('id')
                    )
                ));
                break;
            case 'administradores':
                $dependencia = $this->ServidoresPublico->find('first', array(
                    'conditions' => array(
                        'ServidoresPublico.usuario_id' => $this->Auth->user('id')
                    )
                ));
                $pendientes['4'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 4,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000,
                        'Proceso.dependencia_id' => $dependencia['ServidoresPublico']['dependencia_id']
                    )
                ));
                break;
            case 'tesoreria1':
                $pendientes['6'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 6,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));
                break;
            case 'tesoreria2':
                $pendientes['7'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 7,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));
                break;
            case 'economia':
                $pendientes['10'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 10,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));
                break;
            case 'caja':
                $pendientes['12'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 12,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));
                break;
            case 'archivo':
                $pendientes['13'] = $this->ProcesosEstado->find('count', array(
                    'conditions' => array(
                        'ProcesosEstado.estado_id' => 13,
                        'ProcesosEstado.fecha_recepcion' => NULL,
                        $monto => 10000
                    )
                ));
                break;
        }
        $this->set('pendientes', $pendientes);
        /** Cantidades de procesos por estado [fin] */

        /** Condiciones de filtrado */
        $conditions = array();
        if ($params['mayor'] == 'si') {
            $conditions['Proceso.monto >'] = 10000;
        } else {
            $conditions['Proceso.monto <='] = 10000;
        }
        switch ($params['filtro']) {
            case 'cite':
            case 'beneficiario_documento':
            case 'beneficiario_nombre':
            case 'referencia':
                $conditions['Proceso.' . $params['filtro'] .' ILIKE '] = '%' . $params['buscar'] . '%';
                break;
            case 'nro_proceso':
            case 'nro_preventivo':
            case 'monto':
                if (!empty($params['buscar'])) {
                    $conditions['Proceso.' . $params['filtro']] = $params['buscar'];
                }
                break;
        }
        if (!empty($params['dependencia'])) {
            $conditions['Proceso.dependencia_id'] = $params['dependencia'];
        }
        if (!empty($params['motivo'])) {
            $conditions['Proceso.motivo_id'] = $params['motivo'];
        }
        if (!empty($params['estado'])) {
            $conditions['Proceso.estado_id'] = $params['estado'];
        }

        if (AuthComponent::user('rol') == 'analistas') {
            $conditions['Proceso.usuario_analista_id'] = $this->Auth->user('id');
        }
        if (AuthComponent::user('rol') == 'administradores') {
            $dependencia = $this->ServidoresPublico->find('first', array(
                'conditions' => array(
                    'ServidoresPublico.usuario_id' => $this->Auth->user('id')
                )
            ));
            $conditions['Proceso.dependencia_id'] = $dependencia['ServidoresPublico']['dependencia_id'];
        }
        if (($this->Auth->user('rol') == 'analistas') && ($params['estado'] == 3)) {
            $conditions['Observacione.id'] = NULL;
        }

        $sort = empty($sort)?'nro_proceso':$sort;
        $direction = empty($direction)?'DESC':$direction;

        $this->Proceso->recursive = 0;
        $this->paginate = array(
            'Proceso' => array('limit' => 20,
                'conditions' => $conditions,
                'order' => 'ProcesosEstado.fecha_recepcion desc',
                'fields' => array(
                    'id', 'cite', 'nro_proceso', 'nro_preventivo',
                    'beneficiario_documento', 'beneficiario_nombre', 'monto', 'usuario_analista_id',
                    'Motivo.id', 'Motivo.nombre',
                    'ProcesosEstado.fecha_envio', 'ProcesosEstado.fecha_recepcion',
                    'ProcesosEstado.reingreso'
                ),
                'joins' => array(
                    array(
                        'table' => 'pag_procesos_estados',
                        'alias' => 'ProcesosEstado',
                        'type' => 'INNER',
                        'conditions' => array(
                            'ProcesosEstado.proceso_id = Proceso.id',
                            'ProcesosEstado.estado_id = Proceso.estado_id',
                            'ProcesosEstado.id = Proceso.ultimo_estado_id',
                            'ProcesosEstado.fecha_recepcion IS NOT NULL'
                        )
                    ),
                    array(
                        'table' => 'pag_observaciones',
                        'alias' => 'Observacione',
                        'type' => 'LEFT',
                        'conditions' => array(
                            'Observacione.procesos_estados_id = Proceso.ultimo_estado_id'
                        )
                    )
                ),
                'order' => array($sort => $direction)
            )
        );
        $this->set('procesos', $this->paginate());
    }

    /**
     * ver method
     *
     * @param string $id
     * @return void
     */
    public function ver($id = null) {
        $this->Proceso->id = $id;
        if (!$this->Proceso->exists()) {
            throw new NotFoundException(__('Proceso inválido'));
        }

        $this->set('referer', $this->referer());

        $this->set('proceso', $this->Proceso->read(null, $id));

        $this->set('estados', $this->ProcesosEstado->find('all', array(
            'conditions' => array('ProcesosEstado.proceso_id' => $id),
            'joins' => array(
                array(
                    'table' => 'per_servidores_publicos',
                    'alias' => 'UsuarioEnvio',
                    'type' => 'INNER',
                    'conditions' => array(
                        'UsuarioEnvio.usuario_id = ProcesosEstado.usuario_envio_id',
                    )
                ),
                array(
                    'table' => 'per_servidores_publicos',
                    'alias' => 'UsuarioRecepcion',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'UsuarioRecepcion.usuario_id = ProcesosEstado.usuario_recepcion_id',
                    )
                ),
                array(
                    'table' => 'pag_observaciones',
                    'alias' => 'Observacione',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Observacione.procesos_estados_id = ProcesosEstado.id',
                    )
                )
            ),
            'fields' => array(
                'Estado.id', 'Estado.nombre', 'Estado.descripcion', 'ProcesosEstado.id', 'ProcesosEstado.fecha_envio',
                'ProcesosEstado.fecha_recepcion',
                'UsuarioEnvio.nombres', 'UsuarioEnvio.apellidos',
                'UsuarioRecepcion.nombres', 'UsuarioRecepcion.apellidos',
                'Observacione.id'
            ),
            'order' => array('ProcesosEstado.fecha_envio' => 'ASC')
        )));

        $this->set('observaciones', $this->Observacione->find('all', array(
            'conditions' => array('Observacione.proceso_id' => $id),
            'joins' => array(
                array(
                    'table' => 'per_servidores_publicos',
                    'alias' => 'ServidoresPublico',
                    'type' => 'INNER',
                    'conditions' => array(
                        'ServidoresPublico.usuario_id = Observacione.usuario_analista_id',
                    )
                )
            ),
            'fields' => array(
                'Observacione.id', 'Observacione.descripcion', 'Observacione.modified',
                'ServidoresPublico.nombres', 'ServidoresPublico.apellidos'
            ),
            'order' => array('Observacione.modified' => 'ASC')
        )));
    }

    public function borrar() {
        $estado = 2;
        $procesos = $this->Proceso->find('all', array(
            'conditions' => array('Proceso.estado_id' => $estado),
            'fields' => array('Proceso.id')
        ));

        foreach ($procesos as $proceso) {
            $proceso_estado = $this->ProcesosEstado->find('first', array(
                'conditions' => array(
                    'ProcesosEstado.proceso_id' => $proceso['Proceso']['id'],
                    'ProcesosEstado.estado_id' => $estado
                ),
                'fields' => array('ProcesosEstado.id'),
                'order' => array('ProcesosEstado.id' => 'DESC')
            ));

            $this->ProcesosEstado->deleteAll(array(
                'ProcesosEstado.id <>' => $proceso_estado['ProcesosEstado']['id'],
                'ProcesosEstado.proceso_id' => $proceso['Proceso']['id'],
                'ProcesosEstado.estado_id' => $estado
            ));
        }


    }

    /**
     * observar method
     *
     * @param string $id
     * @return void
     */
    public function observar($id = null) {
        $this->Proceso->id = $id;
        if (!$this->Proceso->exists()) {
            throw new NotFoundException(__('Proceso inválido'));
        }
        $procesoEstado = $this->ProcesosEstado->find('first', array(
            'conditions' => array(
                'ProcesosEstado.estado_id' => 3,
                'ProcesosEstado.proceso_id' => $id,
                'ProcesosEstado.fecha_recepcion <>' => NULL
            )
        ));
        if (!$procesoEstado) {
            $this->Session->setFlash(__('El Proceso de Pago debe ser antes recepcionado'));
            $this->redirect($this->referer());
        }

        $proceso = $this->Proceso->read(null, $id);
        $this->set('proceso', $proceso);

        if ($this->request->is('post')) {
            $this->Observacione->create();
            $this->request->data['Observacione']['estado_id'] = 3;
            $this->request->data['Observacione']['proceso_id'] = $id;
            $this->request->data['Observacione']['verificado'] = '0';
            $this->request->data['Observacione']['user_created'] = $this->Auth->user('id');
            $this->request->data['Observacione']['user_modified'] = $this->Auth->user('id');
            $this->request->data['Observacione']['usuario_analista_id'] = $this->Auth->user('id');
            $this->request->data['Observacione']['procesos_estados_id'] = $proceso['Proceso']['ultimo_estado_id'];
            if ($this->Observacione->save($this->request->data)) {
                $this->Session->setFlash(__('La Observación ha sido guardada correctamente'));

                if ($proceso['Proceso']['monto'] > 10000) {
                    $this->redirect(array('action' => 'recepcionados', 'estado' => 3, 'mayor' => 'si'));
                } else {
                    $this->redirect(array('action' => 'recepcionados', 'estado' => 3, 'mayor' => 'no'));
                }
            } else {
                $this->Session->setFlash(__('La Observación no pudo ser guardado. Por favor, inténtelo nuevamente.'));
            }
        }
    }

    /**
     * editar_obs method
     *
     * @param string $id
     * @return void
     */
    public function editar_obs($id = null) {
        $params = $this->params['named'];

        $this->Proceso->id = $id;
        if (!$this->Proceso->exists()) {
            throw new NotFoundException(__('Proceso inválido'));
        }

        $observacion = $this->Observacione->find('first', array(
            'conditions' => array(
                'Observacione.proceso_id' => $id,
                'Observacione.verificado' => '0'
            ),
            'order' => array('Observacione.modified' => 'DESC')
        ));

        if (!$observacion) {
            throw new NotFoundException(__('Observación inválida'));
        }

        $proceso = $this->Proceso->find('first', array(
            'conditions' => array(
                'Proceso.id' => $id,
                'Proceso.estado_id' => 3
            )
        ));

        if ($proceso) {
            if ($this->request->is('post') || $this->request->is('put')) {
                $this->request->data['Observacione']['user_modified'] = $this->Auth->user('id');
                if ($this->Observacione->save($this->request->data)) {
                    $this->Session->setFlash(__('La Observación ha sido correctamente guardada.'));
                    if ($proceso['Proceso']['monto'] > 10000) {
                        $this->redirect(array('action' => 'observados', 'estado' => 3, 'mayor' => 'si'));
                    } else {
                        $this->redirect(array('action' => 'observados', 'estado' => 3, 'mayor' => 'no'));
                    }
                } else {
                    $this->Session->setFlash(__('La Observación no pudo ser guardada. Por favor, inténtelo nuevamente.'));
                }
            } else {
                $this->request->data = $observacion;
            }
        } else {
            $this->Session->setFlash(__('El Proceso de Pago no puede ser modificado'));
            $this->redirect($this->referer());
        }

        $this->set('referer', $this->referer());
    }

    /**
     * borrar_obs method
     *
     * @param string $id
     * @return void
     */
    public function borrar_obs($id = null) {
        $params = $this->params['named'];

        $this->Proceso->id = $id;
        if (!$this->Proceso->exists()) {
            throw new NotFoundException(__('Proceso inválido'));
        }

        $observacion = $this->Observacione->find('first', array(
            'conditions' => array(
                'Observacione.proceso_id' => $id,
                'Observacione.verificado' => '0'
            ),
            'order' => array('Observacione.modified' => 'DESC')
        ));

        if (!$observacion) {
            throw new NotFoundException(__('Observación inválida'));
        }

        if ($this->request->is('post') || $this->request->is('put')) {
            $this->Observacione->deleteAll(array('Observacione.id' => $observacion['Observacione']['id']), false);
            $this->Session->setFlash(__('La Observación fue borrada correctamente.'));
        } else {
            throw new NotFoundException(__('Petición inválida'));
        }

        $this->redirect($this->referer());
    }

    /**
     * ver method
     *
     * @param string $id
     * @return void
     */
    public function imprimir($id = null) {
        $this->layout = 'impresion';

        $this->Proceso->id = $id;
        if (!$this->Proceso->exists()) {
            throw new NotFoundException(__('Proceso inválido'));
        }
        $this->set('proceso', $this->Proceso->read(null, $id));
    }

    /**
     * pdf method
     *
     * @param string $id
     * @return void
     */
    public function pdf($id = null, $opcion = '') {
        $this->layout = 'pdf';

        $this->Proceso->id = $id;
        if (!$this->Proceso->exists()) {
            throw new NotFoundException(__('Solicitud inválida'));
        }
        $proceso = $this->Proceso->read(null, $id);

        if ($proceso) {
            App::import('Vendor', 'tcpdf/xtcpdf');

            // crea el documento PDF
            $pdf = new XTCPDF();

            // Modificar información del PDF
            $pdf->setTituloPDF('Proceso de Pago N°' . $proceso['Proceso']['nro_proceso']);

            // set font
            $pdf->SetFont('freesans', '', 12);

            $pdf->setAutoPageBreak(false);

            // add a page
            $pdf->AddPage();

            $pdf->SetY(13);
            $pdf->Image(WWW_ROOT . '/img/logo_gach_pdf.jpg');
            $pdf->SetY(16);
            $pdf->SetFont('freesans', 'B', 14);
            $pdf->SetTextColor(22, 102, 152);
            $pdf->Cell(190, 6, 'Gobierno Autónomo de Chuquisaca', 0, 0, 'C');
            $pdf->Ln();
            $pdf->SetFont('freesans', 'B', 12);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(190, 6, 'Proceso de Pago', 0, 0, 'C');
            $pdf->Ln();
            $pdf->SetFont('freesans', 'B', 14);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Cell(190, 6, 'N° ' . $proceso['Proceso']['nro_proceso'], 0, 0, 'C');
            $pdf->Ln();

            $pdf->SetY(38);

            $pdf->SetFont('freesans', 'B', 8);
            $pdf->SetFillColor(234, 234, 234);
            $pdf->SetLineStyle(array('width' => 0.4, 'color' => array(187, 187, 187)));
            $pdf->MultiCell(62, 6, 'Cite', 1, 'C', true, 0, '', '', true, 0, false, true, 6, 'M');
            $pdf->MultiCell(62, 6, 'Fecha de Emisión', 1, 'C', true, 0, '', '', true, 0, false, true, 6, 'M');
            $pdf->MultiCell(62, 6, 'Autorizado por', 1, 'C', true, 0, '', '', true, 0, false, true, 6, 'M');
            $pdf->Ln();

            $pdf->SetFont('freesans', '', 8);
            $pdf->MultiCell(62, 8, $proceso['Proceso']['cite'], 1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(62, 8, $this->fecha($proceso['Proceso']['fecha_emision']), 1, 'C', 0, 0, '', '', true, 0, false, true, 8, 'M');
            $pdf->MultiCell(62, 8, $proceso['Dependencia']['nombre'], 1, 'C', 0, 0, '', '', true, 0, true, true, 8, 'M');
            $pdf->Ln();

            $pdf->SetFont('freesans', 'B', 8);
            $pdf->SetFillColor(234, 234, 234);
            $pdf->SetLineStyle(array('color' => array(187, 187, 187)));
            $pdf->MultiCell(62, 6, 'Motivo', 1, 'C', true, 0, '', '', true, 0, false, true, 6, 'M');
            $pdf->MultiCell(62, 6, 'Beneficiario', 1, 'C', true, 0, '', '', true, 0, false, true, 6, 'M');
            $pdf->MultiCell(62, 6, 'Monto', 1, 'C', true, 0, '', '', true, 0, false, true, 6, 'M');
            $pdf->Ln();

            $pdf->SetFont('freesans', '', 8);
            $pdf->MultiCell(62, 6, $proceso['Motivo']['nombre'], 1, 'C', 0, 0, '', '', true, 0, false, true, 6, 'M');
            $pdf->MultiCell(62, 6, $proceso['Proceso']['beneficiario_nombre'], 1, 'C', 0, 0, '', '', true, 0, false, true, 6, 'M');
            $pdf->MultiCell(62, 6, $proceso['Proceso']['monto'], 1, 'C', 0, 0, '', '', true, 0, false, true, 6, 'M');
            $pdf->Ln();

            $pdf->SetFont('freesans', 'B', 10);
            $pdf->SetTextColor(44, 104, 119);
            $pdf->Cell(190, 12, 'Seguimiento', 0, 0, 'L');
            $pdf->Ln();

            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('freesans', '', 8);
            $pdf->SetFillColor(255, 255, 255);
            /** Cabecera de la tabla de Ítems */
            for ($i = 0; $i < 4; $i++) {
                $proveido = ($i == 0)?"Contabilidad: Autorizado para su análisis y proceder conforme a normas en vigencia":'';
                $pdf->SetFont('freesans', '', 9);
                $pdf->MultiCell(62, 40, $proveido, 'LT', 'C', true, 0, '', '', true, 0, false, true, 40, 'T');
                $pdf->MultiCell(62, 40, '', 'LT', 'C', true, 0, '', '', true, 0, false, true, 40, 'M');
                $pdf->MultiCell(62, 40, '', 'LTR', 'C', true, 0, '', '', true, 0, false, true, 40, 'M');
                $pdf->Ln();
                $pdf->SetFont('freesans', '', 8);
                $fecha = ($i == 0)?date('d-m-Y'):'';
                $pdf->MultiCell(31, 6, 'Firma o sello', 'LB', 'C', true, 0, '', '', true, 0, false, true, 6, 'M');
                $pdf->MultiCell(31, 6, 'Fecha: ' . $fecha, 'LTB', 'L', true, 0, '', '', true, 0, false, true, 6, 'M');
                $pdf->MultiCell(31, 6, 'Firma o sello', 'LB', 'C', true, 0, '', '', true, 0, false, true, 6, 'M');
                $pdf->MultiCell(31, 6, 'Fecha:', 'LTB', 'L', true, 0, '', '', true, 0, false, true, 6, 'M');
                $pdf->MultiCell(31, 6, 'Firma o sello', 'LB', 'C', true, 0, '', '', true, 0, false, true, 6, 'M');
                $pdf->MultiCell(31, 6, 'Fecha:', 'LTRB', 'L', true, 0, '', '', true, 0, false, true, 6, 'M');
                $pdf->Ln();
            }

            $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

            //Close and output PDF document
            if ($opcion == 'descargar') {
                $pdf->Output('proceso_' . $proceso['Proceso']['nro_proceso'] . '.pdf', 'D');
            } else {
                $pdf->Output('proceso_' . $proceso['Proceso']['nro_proceso'] . '.pdf', 'I');
            }
        }

    }

    /**
     * imprimir_caja method
     *
     * @param string $id
     * @return void
     */
    public function imprimir_caja($ids = null) {
        $this->layout = 'pdf';

        if (empty($ids)) {
            throw new NotFoundException(__('Solicitud inválida'));
        }

        App::import('Vendor', 'tcpdf/xtcpdf');

        // crea el documento PDF
        $pdf = new XTCPDF('P', 'mm', array(215, 330));

        // Modificar información del PDF
        $pdf->setTituloPDF('Reporte');

        // set font
        $pdf->SetFont('freesans', '', 12);

        $pdf->setAutoPageBreak(false);

        // add a page
        $pdf->AddPage();
        $pdf->SetY(14);
        $pdf->Image(WWW_ROOT . '/img/logo_gach_pdf.jpg');
        $pdf->SetY(16);
        $pdf->SetFont('freesans', 'B', 14);
        $pdf->SetTextColor(22, 102, 152);
        $pdf->Cell('', 6, 'Gobierno Autónomo de Chuquisaca', 0, 0, 'C');
        $pdf->Ln();
        $pdf->SetFont('freesans', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Cell('', 6, 'Sistema de Seguimiento de Proceso de Pago', 0, 0, 'C');
        $pdf->Ln();
        $pdf->SetFont('freesans', 'B', 12);
        $pdf->SetTextColor(221, 0, 0);
        $pdf->Cell('', 6, 'Reporte de Procesos por Archivar', 0, 0, 'C');

        $pdf->Ln();

        $pdf->SetTextColor(0, 0, 0);

        $pdf->SetFillColor(245, 245, 245);
        $pdf->SetLineStyle(array('width' => 0.3, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
        $pdf->RoundedRect(156, 18, 44, 14, 2.50, '1111', 'DF');

        $pdf->SetY(20);
        $pdf->SetX(157);
        $pdf->SetFont('freesans', '', 7);
        $pdf->Cell(40, 6, 'Usuario: ' . $this->Auth->user('nick') , 0, 0, 'L');
        $pdf->Ln();
        $pdf->SetY(25);
        $pdf->SetX(157);
        $pdf->Cell(40, 6, 'Fecha de emisión: ' . date('d/m/Y H:i') , 0, 0, 'L');

        $ids = explode(',', $ids);
        $procesos = $this->Proceso->find('all', array(
            'conditions' => array(
                'Proceso.id' => $ids
            ),
            'fields' => array(
                'id', 'cite', 'nro_proceso', 'fecha_emision', 'nro_preventivo', 'referencia',
                'beneficiario_documento', 'beneficiario_nombre', 'monto',
                'Motivo.id', 'Motivo.nombre',
                'Dependencia.id', 'Dependencia.sigla',
                'COUNT(ProcesosEstado.id) AS total'
            ),
            'order' => array('Proceso.nro_proceso' => 'asc'),
            'joins' => array(
                array(
                    'table' => 'pag_procesos_estados',
                    'alias' => 'ProcesosEstado',
                    'type' => 'INNER',
                    'conditions' => array(
                        'ProcesosEstado.proceso_id = Proceso.id'
                    )
                )
            ),
            'group' => 'Proceso.id, Proceso.cite, Proceso.nro_proceso,
                Proceso.beneficiario_documento, Proceso.beneficiario_nombre,
                Proceso.monto, Proceso.fecha_emision, Proceso.nro_preventivo, Proceso.referencia,
                Motivo.id, Motivo.nombre, Dependencia.id, Dependencia.sigla'
        ));


        $pdf->SetY(40);

        $pdf->SetFont('freesans', 'B', 8);
        $pdf->SetFillColor(190, 190, 190);
        $pdf->SetLineStyle(array('width' => 0.3, 'color' => array(145, 145, 145)));
        $pdf->MultiCell(17, 8, 'Nro', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
        $pdf->MultiCell(18, 8, 'Preventivo', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
        $pdf->MultiCell(25, 8, 'Dependencia', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
        $pdf->MultiCell(35, 8, 'Motivo', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
        $pdf->MultiCell(35, 8, 'Beneficiario', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
        $pdf->MultiCell(35, 8, 'Referencia', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
        $pdf->MultiCell(20, 8, 'Monto', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
        $pdf->Ln();
        $pdf->SetFont('freesans', '', 7);
        $i = 0;
        foreach ($procesos as $proceso) {
            $y_motivo = ceil($pdf->getStringHeight(35, $proceso['Motivo']['nombre'], $reseth = false, $autopadding = true, $cellpadding = '', $border = 1));
            $y_beneficiario = ceil($pdf->getStringHeight(35, $proceso['Proceso']['beneficiario_nombre'], $reseth = false, $autopadding = true, $cellpadding = '', $border = 1));
            $y_referencia = ceil($pdf->getStringHeight(35, $proceso['Proceso']['referencia'], $reseth = false, $autopadding = true, $cellpadding = '', $border = 1));
            $y = max($y_motivo, $y_beneficiario, $y_referencia) + 1;
            if ($pdf->getY() + $y > $pdf->getPageHeight() - 15) {
                $pdf->AddPage();
                $pdf->SetY(15);
                $pdf->SetFont('freesans', 'B', 7);
                $pdf->SetFillColor(190, 190, 190);
                $pdf->SetLineStyle(array('width' => 0.3, 'color' => array(145, 145, 145)));
                $pdf->MultiCell(17, 8, 'Nro', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(18, 8, 'Preventivo', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(25, 8, 'Dependencia', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(35, 8, 'Motivo', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(35, 8, 'Beneficiario', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(35, 8, 'Referencia', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->MultiCell(20, 8, 'Monto', 1, 'C', true, 0, '', '', true, 0, false, true, 8, 'M');
                $pdf->Ln();
            }

            $pdf->SetFont('freesans', '', 7);

            $i++;
            if ($i % 2) {
                $pdf->SetFillColor(255, 255, 255);
            } else {
                $pdf->SetFillColor(240, 240, 240);
            }
            $pdf->MultiCell(17, $y, $proceso['Proceso']['nro_proceso'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
            $pdf->MultiCell(18, $y, $proceso['Proceso']['nro_preventivo'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
            $pdf->MultiCell(25, $y, $proceso['Dependencia']['sigla'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
            $pdf->MultiCell(35, $y, $proceso['Motivo']['nombre'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
            $pdf->MultiCell(35, $y, $proceso['Proceso']['beneficiario_nombre'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
            $pdf->MultiCell(35, $y, $proceso['Proceso']['referencia'], 1, 'C', true, 0, '', '', true, 0, false, true, $y, 'M');
            $pdf->MultiCell(20, $y, $proceso['Proceso']['monto'], 1, 'R', true, 0, '', '', true, 0, false, true, $y, 'M');
            $pdf->Ln();
        }

        if ($pdf->getY() > $pdf->getPageHeight() - 48) {
            $pdf->AddPage();
        }

        $pdf->SetY($pdf->GetY() + 40);
        $pdf->SetFont('freesans', 'B', 7);
        $pdf->SetFillColor(190, 190, 190);
        $pdf->SetLineStyle(array('width' => 0.3, 'color' => array(145, 145, 145)));
        $pdf->MultiCell(50, 8, 'ENTREGUE CONFORME', 'T', 'C', 0, 0, 40, '', true, 0, false, true, 8, 'M');
        $pdf->MultiCell(50, 8, 'RECIBI CONFORME', 'T', 'C', 0, 0, 125, '', true, 0, false, true, 8, 'M');
        $pdf->Ln();

        $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

        $pdf->Output('procesos_para_archivar_' . date('Y-m-d') . '.pdf', 'I');
    }

    /**
     * Método nuevo
     *
     * @return void
     */
    public function nuevo() {
        if ($this->request->is('post')) {
            $this->Proceso->create();
            $this->request->data['Proceso']['estado_id'] = 1;
            $this->request->data['Proceso']['beneficiario_nombre'] = strtoupper($this->request->data['Proceso']['beneficiario_nombre']);
            $this->request->data['Proceso']['user_created'] = $this->Auth->user('id');
            $this->request->data['Proceso']['user_modified'] = $this->Auth->user('id');
            if ($this->Proceso->save($this->request->data)) {
                $this->Session->setFlash(__('El Proceso de Pago ha sido guardado correctamente'));

                if ($this->request->data['Proceso']['monto'] > 10000) {
                    $this->redirect(array('action' => 'index', 'estado' => 1, 'mayor' => 'si', 'imprimir' => $this->Proceso->id));
                } else {
                    $this->redirect(array('action' => 'index', 'estado' => 1, 'mayor' => 'no', 'imprimir' => $this->Proceso->id));
                }
            } else {
                $this->Session->setFlash(__('El Proceso de Pago no pudo ser guardado. Por favor, inténtelo nuevamente.'));
            }
        }
        $dependencias = $this->Proceso->Dependencia->find('list', array(
            'conditions' => array(
                'Dependencia.tipo_dependencia_id' => array(2, 4),
                'Dependencia.sigla <>' => NULL
            ),
            'fields' => array('Dependencia.id', 'Dependencia.nombre'),
            'order' => array('Dependencia.nombre' => 'ASC')
        ));
        $motivos = $this->Proceso->Motivo->find('list', array(
            'order' => array('Motivo.nombre' => 'asc')
        ));
        $estados = $this->Proceso->Estado->find('list');
        $this->set(compact('dependencias', 'motivos', 'beneficiarios', 'estados'));
    }

    /**
     * edit method
     *
     * @param string $id
     * @return void
     */
    public function editar($id = null) {
        $this->Proceso->id = $id;
        if (!$this->Proceso->exists()) {
            throw new NotFoundException(__('Proceso inválido'));
        }
        if ($this->request->is('post') || $this->request->is('put')) {
            $this->request->data['Proceso']['beneficiario_nombre'] = strtoupper($this->request->data['Proceso']['beneficiario_nombre']);
            if ($this->Proceso->save($this->request->data)) {
                $this->Session->setFlash(__('El Proceso ha sido correctamente guardado.'));
                if ($this->request->data['Proceso']['monto'] > 10000) {
                    $this->redirect(array('action' => 'index', 'estado' => 1, 'mayor' => 'si'));
                } else {
                    $this->redirect(array('action' => 'index', 'estado' => 1, 'mayor' => 'no'));
                }
            } else {
                $this->Session->setFlash(__('El Proceso no pudo ser guardado. Por favor, inténtelo nuevamente.'));
            }
        } else {
            $this->request->data = $this->Proceso->read(null, $id);
        }

        $dependencias = $this->Proceso->Dependencia->find('list', array(
            'conditions' => array(
                'Dependencia.tipo_dependencia_id' => array(2, 4),
                'Dependencia.sigla <>' => NULL
            ),
            'fields' => array('Dependencia.id', 'Dependencia.nombre'),
            'order' => array('Dependencia.nombre' => 'ASC')
        ));
        $motivos = $this->Proceso->Motivo->find('list', array(
            'order' => array('Motivo.nombre' => 'asc')
        ));
        $estados = $this->Proceso->Estado->find('list');
        $this->set(compact('dependencias', 'motivos', 'beneficiarios', 'estados'));
    }

    /**
     * delete method
     *
     * @param string $id
     * @return void
     */
    public function eliminar($id = null) {
        if ($this->Auth->user('rol') != 'finanzas') {
            throw new MethodNotAllowedException();
        }

        $this->Proceso->id = $id;
        if (!$this->Proceso->exists()) {
            throw new NotFoundException(__('Proceso inválido'));
        }
        if ($this->Proceso->delete()) {
            $this->ProcesosEstado->deleteAll(array('ProcesosEstado.proceso_id' => $id), false);
            $this->Session->setFlash(__('El Proceso ha sido eliminado correctamente'));
            $this->redirect($this->referer());
        }
        $this->Session->setFlash(__('El Proceso no pudo ser eliminado. Por favor inténtelo nuevamente'));
        $this->redirect($this->referer());
    }

    /**
     * enviar_procesos method
     *
     * @param string $id
     * @return void
     */
    public function enviar_procesos($estado = null) {
        if ($this->request->is('post')) {
            $procesos = array_unique($this->request->data['procesos']);
            $estado_actual = $this->request->data['Procesos']['estado'];
            $error = 0;
            $fondos_avance = empty($this->params['named']['fondos_avance'])?false:true;
            $procesos_ok = array();
            foreach ($procesos as $proceso) {
                if ($proceso != '0') {
                    if ($fondos_avance) {
                        $count = $this->ProcesosEstado->find('count', array(
                            'conditions' => array(
                                'ProcesosEstado.fecha_recepcion <>' => NULL,
                                'ProcesosEstado.proceso_id' => $proceso,
                                'ProcesosEstado.estado_id' => $estado_actual,
                                'Proceso.motivo_id' => 9
                            )
                        ));
                    } else {
                        $count = $this->ProcesosEstado->find('count', array(
                            'conditions' => array(
                                'ProcesosEstado.fecha_recepcion <>' => NULL,
                                'ProcesosEstado.proceso_id' => $proceso,
                                'ProcesosEstado.estado_id' => $estado_actual
                            )
                        ));
                    }
//
                    if ($count) {
                        $procesos_ok[] = $proceso;
                    } else {
                        if ($fondos_avance) {
                            $error = 3;
                        } else {
                            $error = 1;
                        }
                    }

                }
            }
            if (($estado == 3) && ($this->Auth->user('rol') == 'contabilidad')) {
                foreach ($procesos_ok as $proceso) {
                    $this->Proceso->updateAll(
                        array('Proceso.usuario_analista_id' => $this->request->data['Procesos']['usuario_id_' . $proceso]),
                        array('Proceso.id' => $proceso)
                    );
                }
            }

            if (($estado == 4) && ($this->Auth->user('rol') == 'analistas')) {
                $procesos_ok2 = array();
                foreach ($procesos_ok as $proceso) {
                    $count = $this->Observacione->find('count', array(
                        'conditions' => array(
                            'Observacione.proceso_id' => $proceso
                        )
                    ));
                    if ($count > 0) {
                        $procesos_ok2[] = $proceso;
                    } else {
                        $error = 2;
                    }
                }

                $procesos_ok = $procesos_ok2;
            }

            if (($estado == 5) && ($this->Auth->user('rol') == 'analistas')) {
                $this->Observacione->updateAll(
                    array('Observacione.verificado' => '1'),
                    array('Observacione.proceso_id' => $proceso)
                );
            }

            $this->Proceso->updateAll(
                array(
                    'Proceso.estado_id' => $estado,
                    'Proceso.user_modified' => $this->Auth->user('id')
                ),
                array('Proceso.id' => $procesos_ok)
            );
            if ($error == 1) {
                $this->Session->setFlash(__('Algunos procesos no pudieron ser cambiados de estado debido a que no fueron recepcionados o porque son de Cierre de Fondos en Avance'));
            } elseif ($error == 2) {
                $this->Session->setFlash(__('Algunos procesos no pudieron ser enviados a los administradores debido a que no se registraron observaciones'));
            } elseif ($error == 3) {
                $this->Session->setFlash(__('Algunos procesos no pudieron ser enviados a archivo debido a que éstos no son de Cierre de Fondos en Avance'));
            } else {
                $this->Session->setFlash(__('Los Procesos fueron correctamente cambiados de estado'));
            }

            if (($estado == 13) && ($this->Auth->user('rol') == 'caja')) {
                $imprimir_redirect = '';
                foreach ($procesos_ok as $procesos_ok_i) {
                    $imprimir_redirect .= empty($imprimir_redirect)?$procesos_ok_i:','.$procesos_ok_i;
                }
                $this->redirect(array('action' => 'recepcionados', 'mayor' => 'no', 'estado' => 12, 'imprimir' => $imprimir_redirect));
            } else {
                $this->redirect($this->referer());
            }
        }
    }

    /**
     * recibir_procesos method
     *
     * @param string $id
     * @return void
     */
    public function recibir_procesos() {
        if ($this->request->is('post')) {
            $procesos = array_unique($this->request->data['procesos']);
            $estado = $this->request->data['Procesos']['estado'];
            $error = false;
            $procesos_ok = array();
            if (($estado == 3) && ($this->Auth->user('rol') == 'analistas')) {
                $analistas_condicion['Proceso.usuario_analista_id'] = $this->Auth->user('id');
            } else {
                $analistas_condicion = null;
            }

            foreach ($procesos as $proceso) {
                if ($proceso != '0') {
                    $count = $this->ProcesosEstado->find('count', array(
                        'conditions' => array(
                            'ProcesosEstado.fecha_recepcion' => NULL,
                            'ProcesosEstado.proceso_id' => $proceso,
                            'ProcesosEstado.estado_id' => $estado,
                            $analistas_condicion
                        )
                    ));
                    if ($count > 0) {
                        $procesos_ok[] = $proceso;
                    } else {
                        $error = true;
                    }
                }
            }

            $this->ProcesosEstado->updateAll(
                array(
                    'ProcesosEstado.fecha_recepcion' => "'" . date('Y-m-d H:i:s') . "'",
                    'ProcesosEstado.usuario_recepcion_id' => $this->Auth->user('id')
                ),
                array(
                    'ProcesosEstado.proceso_id' => $procesos_ok,
                    'ProcesosEstado.estado_id' => $estado,
                )
            );
            if ($error) {
                $this->Session->setFlash(__('Algunos procesos no pudieron ser cambiados de estado. Por favor, verifique su estado e intentelo nuevamente'));
            } else {
                $this->Session->setFlash(__('Los Procesos fueron correctamente cambiados de estado'));
            }
            $this->redirect($this->referer());
        }
    }

    public function busqueda() {
        $params = $this->params['named'];
        $params['envio'] = empty($params['envio']) ? '' : 'si';
        if ($params['envio'] == 'si') {
            $params['chk_nro_proceso'] = empty($params['chk_nro_proceso']) ? false : (bool)$params['chk_nro_proceso'];
        } else {
            $params['chk_nro_proceso'] = true;
        }
        $params['gestion'] = empty($params['gestion']) ? Configure::read('App.year_act') : $params['gestion'];
        $params['nro_proceso'] = empty($params['nro_proceso']) ? '' : $params['nro_proceso'];
        $params['chk_nro_preventivo'] = (empty($params['chk_nro_preventivo'])) ? false : (bool)$params['chk_nro_preventivo'];
        $params['nro_preventivo'] = empty($params['nro_preventivo']) ? '' : $params['nro_preventivo'];
        $params['chk_cite'] = (empty($params['chk_cite'])) ? false : (bool)$params['chk_cite'];
        $params['cite'] = empty($params['cite']) ? '' : $params['cite'];
        $params['chk_ci_nit'] = (empty($params['chk_ci_nit'])) ? false : (bool)$params['chk_ci_nit'];
        $params['ci_nit'] = empty($params['ci_nit']) ? '' : $params['ci_nit'];
        $params['chk_beneficiario'] = (empty($params['chk_beneficiario'])) ? false : (bool)$params['chk_beneficiario'];
        $params['beneficiario'] = empty($params['beneficiario']) ? '' : $params['beneficiario'];
        $params['chk_monto'] = (empty($params['chk_monto'])) ? false : (bool)$params['chk_monto'];
        $params['comparador'] = empty($params['comparador']) ? '=' : $params['comparador'];
        $params['monto'] = empty($params['monto']) ? '' : $params['monto'];
        $params['chk_dependencia'] = (empty($params['chk_dependencia'])) ? false : (bool)$params['chk_dependencia'];
        $params['dependencia'] = empty($params['dependencia']) ? '' : $params['dependencia'];
        $params['chk_motivo'] = (empty($params['chk_motivo'])) ? false : (bool)$params['chk_motivo'];
        $params['motivo'] = empty($params['motivo']) ? '' : $params['motivo'];
        $params['chk_estado'] = (empty($params['chk_estado'])) ? false : (bool)$params['chk_estado'];
        $params['estado'] = empty($params['estado']) ? 'V' : $params['estado'];
        $params['chk_fecha_ini'] = (empty($params['chk_fecha_ini'])) ? false : (bool)$params['chk_fecha_ini'];
        $params['fecha_ini'] = empty($params['fecha_ini']) ? '' : $params['fecha_ini'];
        $params['chk_fecha_fin'] = (empty($params['chk_fecha_fin'])) ? false : (bool)$params['chk_fecha_fin'];
        $params['fecha_fin'] = empty($params['fecha_fin']) ? '' : $params['fecha_fin'];
        $params['page'] = empty($params['page']) ? 1 : $params['page'];
        $params['action'] = 'busqueda';
        $this->set('params', $params);

        $gestiones = array();
        for ($i = Configure::read('App.year_ini'); $i <= Configure::read('App.year_act'); $i++) {
            $gestiones["$i"] = $i;
        }
        $dependencias = $this->Proceso->Dependencia->find('list', array(
            'conditions' => array(
                'Dependencia.tipo_dependencia_id' => array(2, 4),
                'Dependencia.sigla <>' => NULL
            ),
            'fields' => array('Dependencia.id', 'Dependencia.sigla'),
        ));
        $motivos = $this->Proceso->Motivo->find('list', array(
            'fields' => array('Motivo.id', 'Motivo.nombre'),
        ));
        $estados = array('V' => 'Vigentes', 'A' => 'Archivados');

        $this->set(compact('gestiones', 'dependencias', 'motivos', 'estados'));

        $conditions = array(
            'AND' => array(
                'Proceso.fecha_emision >=' => $params['gestion'] . '-01-01',
                'Proceso.fecha_emision <=' => $params['gestion'] . '-12-31'
            )
        );

        if (!empty($params['chk_nro_proceso'])) {
            $conditions['Proceso.nro_proceso'] = $params['nro_proceso'];
        }
        if (!empty($params['chk_cite'])) {
            $conditions['Proceso.cite'] = $params['cite'];
        }
        if (!empty($params['chk_nro_preventivo'])) {
            $conditions['Proceso.nro_preventivo'] = $params['nro_preventivo'];
        }
        if (!empty($params['chk_ci_nit'])) {
            $conditions['Proceso.beneficiario_documento'] = $params['ci_nit'];
        }
        if (!empty($params['chk_beneficiario'])) {
            $conditions['Proceso.beneficiario_nombre ILIKE '] = '%' . strtoupper($params['beneficiario']) . '%';
        }
        if (!empty($params['chk_monto'])) {
            $conditions['Proceso.monto ' . $params['comparador']] = $params['monto'];
        }
        if (!empty($params['chk_dependencia']) && ($params['dependencia'] != 0)) {
            $conditions['Proceso.dependencia_id'] = $params['dependencia'];
        }
        if (!empty($params['chk_motivo']) && ($params['motivo'] != 0)) {
            $conditions['Proceso.motivo_id'] = $params['motivo'];
        }
        if (!empty($params['chk_estado'])) {
            if ($params['estado'] == 'V') {
                $conditions['Proceso.estado_id <'] = 13;
            } elseif ($params['estado'] == 'A') {
                $conditions['Proceso.estado_id'] = 13;
            }
        }
        if (!empty($params['chk_fecha_ini'])) {
            $conditions['Proceso.fecha_emision >='] = $params['fecha_ini'];
        }
        if (!empty($params['chk_fecha_fin'])) {
            $conditions['Proceso.fecha_emision <='] = $params['fecha_fin'];
        }

        if (!empty($params['envio'])) {
            $this->Proceso->recursive = 0;
            $this->paginate = array(
                'Proceso' => array('limit' => 20,
                    'conditions' => $conditions,
                    'order' => array('Proceso.nro_procesos' => 'desc'),
                    'fields' => array(
                        'id', 'cite', 'nro_proceso', 'nro_preventivo',
                        'beneficiario_documento', 'beneficiario_nombre', 'monto', 'usuario_analista_id',
                        'Dependencia.id', 'Dependencia.sigla',
                        'Motivo.id', 'Motivo.nombre',
                        'Estado.nombre'
                    )
                )
            );
            $this->set('params', $params);
            $this->set('procesos', $this->paginate());
        } else {
            $this->set('procesos', null);
        }

        if ($this->Auth->user('nick') != null) {
            $this->layout = 'default';
        } else {
            $this->layout = 'visitante';
        }
    }

    /**
     * devolver method
     *
     * @param string $id
     * @return void
     */
    public function devolver_contabilidad() {
        if ($this->request->is('post')) {
            $procesos = array_unique($this->request->data['procesos']);
            $procesos_ok = $this->Proceso->find('all', array(
                'fields' => array('Proceso.id'),
                'conditions' => array('Proceso.id' => $procesos, 'Proceso.estado_id' => 3),
                'joins' => array(
                    array(
                        'table' => 'pag_procesos_estados',
                        'alias' => 'ProcesosEstado',
                        'type' => 'INNER',
                        'conditions' => array(
                            'ProcesosEstado.proceso_id = Proceso.id',
                            'ProcesosEstado.estado_id = Proceso.estado_id',
                            'ProcesosEstado.fecha_recepcion IS NULL'
                        )
                    )
                )
            ));

            foreach ($procesos_ok as $proceso) {
                $this->ProcesosEstado->deleteAll(array(
                    'ProcesosEstado.proceso_id' => $proceso['Proceso']['id'],
                    'ProcesosEstado.estado_id >' => 2
                ), false);

                $estado = $this->ProcesosEstado->find('first', array(
                    'fields' => array('ProcesosEstado.id'),
                    'conditions' => array(
                        'ProcesosEstado.proceso_id' => $proceso['Proceso']['id'],
                        'ProcesosEstado.estado_id' => 2
                    ),
                ));

                $this->ProcesosEstado->updateAll(
                    array(
                        'ProcesosEstado.fecha_recepcion' => null,
                        'ProcesosEstado.usuario_recepcion_id' => null
                    ),
                    array('ProcesosEstado.id' => $estado['ProcesosEstado']['id'])
                );


                $this->Proceso->updateAll(
                    array(
                        'Proceso.estado_id' => 2,
                        'Proceso.usuario_analista_id' => null,
                        'Proceso.user_modified' => 0,
                        'Proceso.ultimo_estado_id' => $estado['ProcesosEstado']['id']
                    ),
                    array('Proceso.id' => $proceso['Proceso']['id'])
                );
            }

            $this->Session->setFlash(__(count($procesos_ok) . ' de ' . count($procesos) . ' de los Procesos seleccionados fueron devueltos a Contabilidad.'));
        } else {
            throw new NotFoundException(__('Acceso incorrecto'));
        }
        $this->redirect($this->referer());
    }

    /**
     * eliminar_estado method
     *
     * @param string $id
     * @return void
     */
    public function eliminar_estado($id ) {
        if ($this->request->is('post')) {

            $this->ProcesosEstado->id = $id;
            if (!$this->ProcesosEstado->exists()) {
                throw new NotFoundException(__('Estado del Proceso inválido'));
            }
            $estado_1 = $this->ProcesosEstado->read(null, $id);
            if ($this->ProcesosEstado->delete()) {
                $this->Observacione->deleteAll(array('Observacione.procesos_estados_id' => $id), false);
                $estado_2 = $this->ProcesosEstado->find('first', array(
                    'fields' => array('ProcesosEstado.id', 'ProcesosEstado.estado_id', 'ProcesosEstado.proceso_id'),
                    'conditions' => array(
                        'ProcesosEstado.proceso_id' => $estado_1['ProcesosEstado']['proceso_id'],
                    ),
                    'order' => array('ProcesosEstado.fecha_envio' => 'DESC')
                ));
                $this->Proceso->updateAll(
                    array(
                        'Proceso.estado_id' => $estado_2['ProcesosEstado']['estado_id'],
                        'Proceso.ultimo_estado_id' => $estado_2['ProcesosEstado']['id'],
                        'Proceso.user_modified' => 0,
                    ),
                    array('Proceso.id' => $estado_2['ProcesosEstado']['proceso_id'])
                );
                $this->Session->setFlash(__('El Estado del Proceso fue eliminado correctamente'));
            } else {
                $this->Session->setFlash(__('El Estado del Proceso no pudo ser eliminado. Por favor inténtelo nuevamente'));
            }


        } else {
            throw new NotFoundException(__('Acceso incorrecto'));
        }
        $this->redirect($this->referer());
    }

    public function busqueda_web() {
        $params = $this->params['named'];
        $params['envio'] = empty($params['envio']) ? '' : 'si';
        if ($params['envio'] == 'si') {
            $params['chk_nro_proceso'] = empty($params['chk_nro_proceso']) ? false : (bool)$params['chk_nro_proceso'];
        } else {
            $params['chk_nro_proceso'] = true;
        }
        $params['nro_proceso'] = empty($params['nro_proceso']) ? '' : $params['nro_proceso'];
        $params['chk_nro_preventivo'] = (empty($params['chk_nro_preventivo'])) ? false : (bool)$params['chk_nro_preventivo'];
        $params['nro_preventivo'] = empty($params['nro_preventivo']) ? '' : $params['nro_preventivo'];
        $params['chk_cite'] = (empty($params['chk_cite'])) ? false : (bool)$params['chk_cite'];
        $params['cite'] = empty($params['cite']) ? '' : $params['cite'];
        $params['chk_ci_nit'] = (empty($params['chk_ci_nit'])) ? false : (bool)$params['chk_ci_nit'];
        $params['ci_nit'] = empty($params['ci_nit']) ? '' : $params['ci_nit'];
        $params['chk_beneficiario'] = (empty($params['chk_beneficiario'])) ? false : (bool)$params['chk_beneficiario'];
        $params['beneficiario'] = empty($params['beneficiario']) ? '' : $params['beneficiario'];
        $params['chk_monto'] = (empty($params['chk_monto'])) ? false : (bool)$params['chk_monto'];
        $params['comparador'] = empty($params['comparador']) ? '=' : $params['comparador'];
        $params['monto'] = empty($params['monto']) ? '' : $params['monto'];
        $params['chk_dependencia'] = (empty($params['chk_dependencia'])) ? false : (bool)$params['chk_dependencia'];
        $params['dependencia'] = empty($params['dependencia']) ? '' : $params['dependencia'];
        $params['chk_motivo'] = (empty($params['chk_motivo'])) ? false : (bool)$params['chk_motivo'];
        $params['motivo'] = empty($params['motivo']) ? '' : $params['motivo'];
        $params['chk_estado'] = (empty($params['chk_estado'])) ? false : (bool)$params['chk_estado'];
        $params['chk_fecha_ini'] = (empty($params['chk_fecha_ini'])) ? false : (bool)$params['chk_fecha_ini'];
        $params['fecha_ini'] = empty($params['fecha_ini']) ? '' : $params['fecha_ini'];
        $params['chk_fecha_fin'] = (empty($params['chk_fecha_fin'])) ? false : (bool)$params['chk_fecha_fin'];
        $params['fecha_fin'] = empty($params['fecha_fin']) ? '' : $params['fecha_fin'];
        $params['page'] = empty($params['page']) ? 1 : $params['page'];
        $params['action'] = 'busqueda';
        $this->set('params', $params);

        $dependencias = $this->Proceso->Dependencia->find('list', array(
            'conditions' => array(
                'Dependencia.tipo_dependencia_id' => array(2, 4),
                'Dependencia.sigla <>' => NULL
            ),
            'fields' => array('Dependencia.id', 'Dependencia.sigla'),
        ));
        $motivos = $this->Proceso->Motivo->find('list', array(
            'fields' => array('Motivo.id', 'Motivo.nombre'),
        ));

        $this->set(compact('dependencias', 'motivos'));

        $conditions = array(
            'AND' => array(
                'Proceso.fecha_emision >=' => Configure::Read('App.year_act') . '-01-01',
                'Proceso.fecha_emision <=' => Configure::Read('App.year_act') . '-12-31',
                'Proceso.estado_id <' => 13
            )
        );

        if (!empty($params['chk_nro_proceso'])) {
            $conditions['Proceso.nro_proceso'] = $params['nro_proceso'];
        }
        if (!empty($params['chk_cite'])) {
            $conditions['Proceso.cite'] = $params['cite'];
        }
        if (!empty($params['chk_nro_preventivo'])) {
            $conditions['Proceso.nro_preventivo'] = $params['nro_preventivo'];
        }
        if (!empty($params['chk_ci_nit'])) {
            $conditions['Proceso.beneficiario_documento'] = $params['ci_nit'];
        }
        if (!empty($params['chk_beneficiario'])) {
            $conditions['Proceso.beneficiario_nombre ILIKE '] = '%' . strtoupper($params['beneficiario']) . '%';
        }
        if (!empty($params['chk_monto'])) {
            $conditions['Proceso.monto ' . $params['comparador']] = $params['monto'];
        }
        if (!empty($params['chk_dependencia']) && ($params['dependencia'] != 0)) {
            $conditions['Proceso.dependencia_id'] = $params['dependencia'];
        }
        if (!empty($params['chk_motivo']) && ($params['motivo'] != 0)) {
            $conditions['Proceso.motivo_id'] = $params['motivo'];
        }
        if (!empty($params['chk_fecha_ini'])) {
            $conditions['Proceso.fecha_emision >='] = $params['fecha_ini'];
        }
        if (!empty($params['chk_fecha_fin'])) {
            $conditions['Proceso.fecha_emision <='] = $params['fecha_fin'];
        }

        if (!empty($params['envio'])) {
            $this->Proceso->recursive = 0;
            $this->paginate = array(
                'Proceso' => array('limit' => 20,
                    'conditions' => $conditions,
                    'order' => array('Proceso.nro_procesos' => 'desc'),
                    'fields' => array(
                        'id', 'nro_proceso', 'fecha_emision',
                        'beneficiario_documento', 'beneficiario_nombre', 'monto', 'usuario_analista_id',
                        'Dependencia.id', 'Dependencia.sigla',
                        'Motivo.id', 'Motivo.nombre',
                        'Estado.nombre'
                    )
                )
            );
            $this->set('params', $params);
            $this->set('procesos', $this->paginate());
        } else {
            $this->set('procesos', null);
        }

        $this->layout = 'web';
    }

    /**
     * ver method
     *
     * @param string $id
     * @return void
     */
    public function ver_web ($id = null) {
        $this->Proceso->id = $id;
        if (!$this->Proceso->exists()) {
            throw new NotFoundException(__('Proceso inválido'));
        }

        $this->set('referer', $this->referer());

        $this->set('proceso', $this->Proceso->read(null, $id));

        $this->set('estados', $this->ProcesosEstado->find('all', array(
            'conditions' => array('ProcesosEstado.proceso_id' => $id),
            'joins' => array(
                array(
                    'table' => 'per_servidores_publicos',
                    'alias' => 'UsuarioEnvio',
                    'type' => 'INNER',
                    'conditions' => array(
                        'UsuarioEnvio.usuario_id = ProcesosEstado.usuario_envio_id',
                    )
                ),
                array(
                    'table' => 'per_servidores_publicos',
                    'alias' => 'UsuarioRecepcion',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'UsuarioRecepcion.usuario_id = ProcesosEstado.usuario_recepcion_id',
                    )
                ),
                array(
                    'table' => 'pag_observaciones',
                    'alias' => 'Observacione',
                    'type' => 'LEFT',
                    'conditions' => array(
                        'Observacione.procesos_estados_id = ProcesosEstado.id',
                    )
                )
            ),
            'fields' => array(
                'Estado.id', 'Estado.nombre', 'Estado.descripcion', 'ProcesosEstado.id', 'ProcesosEstado.fecha_envio',
                'ProcesosEstado.fecha_recepcion',
                'UsuarioEnvio.nombres', 'UsuarioEnvio.apellidos',
                'UsuarioRecepcion.nombres', 'UsuarioRecepcion.apellidos'
            ),
            'order' => array('ProcesosEstado.fecha_envio' => 'ASC')
        )));

        $this->layout = 'web';
    }

    public function isAuthorized() {
        if (parent::isAuthorized()) {
            return true;
        } elseif (in_array ( $this->action, array('busqueda', 'busqueda_web', 'ver', 'ver_web') )) {
            return true;
        }

        return false;
    }

    private function fecha($fecha, $completo = false) {
        $fecha = explode(' ', $fecha);

        $fecha_dia = explode('-', $fecha[0]);
        $dia = $fecha_dia[2] . '-' . $fecha_dia[1] . '-' . $fecha_dia[0];

        if ($completo) {
            $fecha_hora = explode(':', $fecha[1]);
            $hora = $fecha_hora[0] . ':' . $fecha_hora[1];
            return $dia . ' ' . $hora;
        } else {
            return $dia;
        }
    }

}
