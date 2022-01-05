<?php

class Application_Model_DbTable_Comuna extends Zend_Db_Table_Abstract{
   protected $_name = 'comuna';
   protected $_primary = 'idComuna';

   public function listar()
    {
      $select = $this->select();
      $select->setIntegrityCheck(false) ;
      $select->from(array('c' => 'comuna'),'*');
      $select->order("c.nombreComuna");
      return $this->fetchAll($select);
    }
 
   public function getAsKeyValue($marca){
      $select = $this->select();
                     $select->setIntegrityCheck(false) ;
        
        $select->from(array('c' => 'comuna'),'*');
                     $select->where('c.idProvincia = ?', $marca);
                     $select->order("c.nombreComuna");
      $rowset = $this->fetchAll($select);
      $data = array();
      foreach($rowset as $row){
         $data[$row->idComuna] = $row->nombreComuna;
      }
      return $data;
   }
 
   public function getAsKeyValueJSON($marca){
      $select = $this->select()
                     ->where('idProvincia = ?', $marca);
      $rowset = $this->fetchAll($select)->toArray();
      return $rowset;
   }
   
    public function getcomuna($marca){
      $select = $this->select();
                     $select->setIntegrityCheck(false) ;
        
        $select->from(array('c' => 'comuna'),'*');
                     $select->joinLeft(array('pr' => 'provincia'), 'pr.idProvincia = c.idProvincia');
                     $select->joinLeft(array('rg' => 'region'), 'rg.idRegion = pr.idRegion');
                     $select->where('c.idComuna = ?', $marca);
                     
      $rowset = $this->fetchAll($select);
      return $rowset;
      
   }

    public function getcomunasolo($marca){
        $select = $this->select();
        $select->setIntegrityCheck(false) ;

        $select->from(array('c' => 'comuna'),'*');
        $select->joinLeft(array('pr' => 'provincia'), 'pr.idProvincia = c.idProvincia');
        $select->joinLeft(array('rg' => 'region'), 'rg.idRegion = pr.idRegion');
        $select->where('c.idComuna = ?', $marca);
        return $this->fetchAll($select);


    }
}
?>
