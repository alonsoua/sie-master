<?php

class Application_Model_DbTable_Detallenivelcuenta extends Zend_Db_Table_Abstract
{

    protected $_name = 'cuentasnivel';
    
    

    /**
     *  agrega un nuevo curso a la base de datos
     * @param  $idcurso
     * @param  $user
     */
    public function agregar( $idnivel,$user,$periodo)
    {
        $data = array('idNiveles' => $idnivel,'idCuenta'=>$user,'idPeriodo'=>$periodo);
        //$this->insert inserta nuevo Establecimiento
        $this->insert($data);
       
    }
    
   public function borrar($id,$pass)
    {
        //$this->delete borrar cuenta usuario donde id=$id
        $this->delete('idCuenta =' .  $id);
    }
    
    public function borrarporid($id)
    {
        //$this->delete borrar cuenta usuario donde id=$id
        $row=$this->delete('idCuentaNivel =' .  $id);
        
        if ($row)
        {
            return TRUE;
        }else{
            
            return FALSE;
        }
    }
    
    public function listar($marca,$periodo){
      $select = $this->select();
                     $select->setIntegrityCheck(false) ;
        
        $select->from(array('c' => 'cuentasnivel'),'*');
        $select->join(array('n' => 'niveles'), 
                            'n.idNiveles = c.idNiveles');
                     $select->where('c.idCuenta = ?', $marca);
                     $select->where('c.idPeriodo = ?', $periodo);
                      
                     $select->order("c.idCuenta");
     return $this->fetchAll($select);
   }
   
 

   
   
   // verificar niveles
   
   
   
    public function listarv($marca,$periodo){
      $select = $this->select();
                     $select->setIntegrityCheck(false) ;
        
        $select->from(array('c' => 'detalle_nivel_cuenta'),'*');
        
        
                     $select->where('c.user_cuenta = ?', $marca);
                     $select->where('c.periodo = ?', $periodo);
                     
                     
      $a=$this->fetchAll($select);
      return count($a);
   }
   
  
   
   
   
     public function validar($id,$periodo)
    {
        //devuelve todos los registros de la tabla
       $select = $this->select(); 

               
                $select->setIntegrityCheck(false) ;
        
        $select->from(array('o' => 'detalle_nivel_cuenta'),'*');
 
        $select->where('o.idCuenta = ?',$id );
        $select->where('o.idPeriodo = ?',$periodo );
        

	$stmt = $select->query();
 
	 $row=$stmt->fetchAll();
        
        
        if (!$row)
        {
            return TRUE;
        }else{
            
            return FALSE;
        }
    
        
    }
    
    
    public function validar_nivel($id,$periodo,$nivel)
    {
        //devuelve todos los registros de la tabla
       $select = $this->select(); 

               
                $select->setIntegrityCheck(false) ;
        
        $select->from(array('o' => 'cuentasnivel'),'*');
 
        $select->where('o.idCuenta = ?',$id );
        $select->where('o.idPeriodo = ?' ,$periodo );
        $select->where('o.idNiveles = ' . $nivel );
        

	$stmt = $select->query();
 
	 $row=$stmt->fetchAll();
        
        $ok='0';
        if (!$row)
        {
            return $ok;
        }else{
            
            return $row;
        }
    
        
    }
    
    public function validar_nivel_docente($id,$periodo,$nivel)
    {
        //devuelve todos los registros de la tabla
       $select = $this->select(); 

               
                $select->setIntegrityCheck(false) ;
        
        $select->from(array('o' => 'cuentasnivel'),'*');
 
        $select->where('o.idCuenta = ?',$id );
        $select->where('o.idPeriodo = ?' ,$periodo );
        $select->where('o.idCuentaNivel= ?' ,$nivel );
        

	$stmt = $select->query();
 
	 $row=$stmt->fetchAll();
        
        
        if ($row==NULL)
        {
           return 0;
        }else{
            
            return $row;
        }
       
    
        
    }
    
    
    public function getnivelusuario($marca,$periodo){
      $select = $this->select();
                     $select->setIntegrityCheck(false) ;
        
        $select->from(array('c' => 'detalle_nivel_cuenta'),'*');
        $select->join(array('n' => 'nivel'), 
                            'n.NIVEL_ID = c.id_nivel_cuenta');
                     $select->where('c.user_cuenta = ?', $marca);
                     $select->where('c.periodo = ?', $periodo);
                     $select->order("c.id_nivel_cuenta");
     return $this->fetchAll($select);
     
   }

    public function getnivelusuarioeditar($marca,$periodo){
      $select = $this->select();
                     $select->setIntegrityCheck(false) ;
        
        $select->from(array('c' => 'cuentasnivel'),'*');
        $select->join(array('n' => 'niveles'), 
                            'n.idNiveles = c.idNiveles');
                     $select->where('c.idCuenta = ?', $marca);
                     $select->where('c.idPeriodo = ?', $periodo);
                     $select->order("c.idNiveles");
                     
    $rowset = $this->fetchAll($select)->toArray();
      return $rowset;
    
   }
    
}

?>