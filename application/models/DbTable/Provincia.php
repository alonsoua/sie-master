<?php

class Application_Model_DbTable_Provincia extends Zend_Db_Table_Abstract{
   protected $_name = 'provincia';
   protected $_primary = 'idProvincia';
 
   public function listarprovincias(){
        $select = $this->select();
                     $select->setIntegrityCheck(false) ;
        
        $select->from(array('p' => 'provincia'),'*');
                     
                     $select->order("p.nombreProvincia");
      return $this->fetchAll($select);
   }
   
   public function getAsKeyValue($marca){
      $select = $this->select();
                     $select->setIntegrityCheck(false) ;
        
        $select->from(array('c' => 'provincia'),'*');
                     $select->where('c.idRegion = ?', $marca);
                     $select->order("c.nombreProvincia");
      $rowset = $this->fetchAll($select);
      $data = array();
      foreach($rowset as $row){
         $data[$row->idProvincia] = $row->nombreProvincia;
      }
      return $data;
   }
 
   public function getAsKeyValueJSON($marca){
      $select = $this->select()
                     ->where('idRegion = ?', $marca);
      $rowset = $this->fetchAll($select)->toArray();
      return $rowset;
   }
}

