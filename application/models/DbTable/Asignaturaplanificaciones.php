<?php

class Application_Model_DbTable_Asignaturaplanificaciones extends Zend_Db_Table_Abstract{
   protected $_name = 'asignaturas';
   protected $_primary = 'idAsignatura';
 
   public function listarasignaturasp(){
        $select = $this->select();
                     $select->setIntegrityCheck(false) ;
        
        $select->from(array('p' => 'asignaturas'),'*');
                     
                     $select->order("p.nombreAsignatura");
      return $this->fetchAll($select);
   }
   
   public function getAsKeyValueAsignaturap($marca){
      $select = $this->select();
                     $select->setIntegrityCheck(false) ;
        
        $select->from(array('c' => 'asignaturas'),'*');
                     $select->where('c.idNiveles = ?', $marca);
                     //$select->order("c.ASIGNATURAP_NOMBRE");
      $rowset = $this->fetchAll($select);
      $data = array();
      foreach($rowset as $row){
         $data[$row->idAsignatura] = $row->nombreAsignatura;
      }
      return $data;
   }
 
   public function getAsKeyValueJSONAsignaturap($marca){
      $select = $this->select()
                     ->where('idNiveles= ?', $marca);
      $rowset = $this->fetchAll($select)->toArray();
      return $rowset;
   }
}
