<?php

App::uses('AppController', 'Controller');

/**
 * ServidoresPublicos Controller
 *
 * @property ServidoresPublico $ServidoresPublico
 */
class ServidoresPublicosController extends AppController {
    
    public function dependencia($id = null) {
        $this->layout = 'ajax';
        $servidores = $this->ServidoresPublico->findByDependenciaId($id, array(
//            'fields' => array('ServidoresPublico.id')
        ));
        
        $servidores = $this->ServidoresPublico->find('all', array(
            'conditions' => array(
                'ServidoresPublico.dependencia_id' => $id,
                'ServidoresPublico.estado' => 1,
                'ServidoresPublico.cargo_actual' => 'SECRETARIO DEPARTAMENTAL'
            ),
            'fields' => array('ServidoresPublico.id', 'ServidoresPublico.nombre'),
            'order' => array('ServidoresPublico.nombre' => 'asc')
        ));
        echo json_encode($servidores);
        
        
//        echo '{ metaData: { "root": "data"}';	
//echo ',"success":true, "data":' . json_encode($servidores) . '}';
        exit;
    } 

}
