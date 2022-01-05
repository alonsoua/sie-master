<?php

class Application_Model_DbTable_Detalleasignaturacuenta extends Zend_Db_Table_Abstract
{

    protected $_name = 'cuentasasignatura';
    
    

    /**
     *  agrega un nuevo detalle_asignatura a la base de datos
     * @param  $idcurso
     * @param  $user
     */
    public function agregar( $idasignatura,$id)
    {
        $data = array('idAsignatura' => $idasignatura,'idCuentacurso'=>$id);
        //$this->insert inserta nuevo Establecimiento
        $this->insert($data);
    }
    
  
    
    public function listar($marca){
      $select = $this->select();
                     $select->setIntegrityCheck(false) ;
        
        $select->from(array('c' => 'cuentasasignatura'),'*');
        $select->join(array('cu' => 'asignaturas'), 
                            'cu.idAsignatura = c.idAsignatura');
        $select->join(array('cur' => 'cuentascurso'), 
                            'cur.idCuentaCurso = c.idCuentacurso',array('idCursos'));
                     $select->where('c.idCuentacurso = ?', $marca);
                     $select->order("cu.idAsignatura");
     return $this->fetchAll($select);
   }
   
    public function listarmedia($marca){
      $select = $this->select();
                     $select->setIntegrityCheck(false) ;
        
        $select->from(array('c' => 'detalle_asignatura_cuenta'),'*');
        $select->join(array('cu' => 'asignaturamedia'), 
                            'cu.ASIGNATURA_ID = c.id_asignatura');
        $select->join(array('cur' => 'detalle_curso_cuenta'), 
                          'cur.id = c.id_detalle_curso',array('id_curso'));
                     $select->where('c.id_detalle_curso = ?', $marca);
                     $select->order("cu.ASIGNATURA_ID");
     return $this->fetchAll($select);
   }
   
    public function listarmt($marca){
      $select = $this->select();
                     $select->setIntegrityCheck(false) ;
        
        $select->from(array('c' => 'detalle_asignatura_cuenta'),'*');
        $select->join(array('cu' => 'especialidades'), 
                            'cu.ASIGNATURA_ID = c.id_asignatura');
                     $select->where('c.id_detalle_curso = ?', $marca);
                     $select->order("cu.ASIGNATURA_ID");
     return $this->fetchAll($select);
   }

    public function listarsep($marca){
      $select = $this->select();
                     $select->setIntegrityCheck(false) ;
        
        $select->from(array('c' => 'cuentasasignatura'),'*');
        $select->join(array('cu' => 'asignaturassep'), 
                            'cu.idAsignatura = c.idAsignatura');
        $select->join(array('cur' => 'cuentascurso'), 
                            'cur.idCuentaCurso = c.idCuentacurso',array('idCursos'));
                     $select->where('c.idCuentacurso = ?', $marca);
                     $select->order("cu.idAsignatura");
     return $this->fetchAll($select);
   }
   
   
   
    public function borrar($id_detalle)
    {
        $re = $this->fetchRow('idCuentaasignatura = ' .(int) $id_detalle);
        
        $row=$this->delete('idCuentaasignatura =' . (int) $id_detalle);
        //return $id_detalle;
        if($row)
        {
           return $re->toArray();
        }else{
            
            return FALSE;
        }
    }
    
    public function borrarporcurso($id)
    {
        
        $row=$this->delete('idCuentacurso =' . (int) $id);
        //return $id_detalle;
        if ($row)
        {
            return TRUE;
        }else{
            
            return FALSE;
        }
    }


      public function listarasignaturausuario($marca){
      $select = $this->select();
                     $select->setIntegrityCheck(false) ;
        
        $select->from(array('c' => 'cuentasasignatura'),'*');
        
                     $select->where('c.idCuentacurso = ?', $marca);
                     
    $rowset = $this->fetchAll($select)->toArray();
     return $rowset;
   }

     public function get($id)
    {
 
        $row=$this->fetchRow('idCuentacurso =' . (int) $id);
        if ($row)
        {
            return TRUE;
        }else{
            return FALSE;
        }
    
        
    }
    
    
    
      
    
}

?>