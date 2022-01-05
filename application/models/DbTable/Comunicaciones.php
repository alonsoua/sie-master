<?php

class Application_Model_DbTable_Comunicaciones extends Zend_Db_Table_Abstract
{

    protected $_name = 'comunicacion';
   
    
    public function ultimo()
    {
        return $this->fetchRow(
                $this->select()
                ->from($this, array(new Zend_Db_Expr('max(id) as id')))
            )
    ;
        
    }

    /**
     *  agrega una nueba citacion a la base de datos
     * @param  $id
     * @param  $rut
     * @param  $idcurso
     * @param  $idestablecimiento
     * @param  $fecha
     * @param  $sub
     * @param  $comentarios
     */
    public function agregar($comentarios,$profesor,$periodo,$tipo)
    {
        $data = array('contenido'=>$comentarios,'idCuenta'=>$profesor,'idPeriodo'=>$periodo,'idTipo'=>$tipo
            );
        //$this->insert inserta nueva Observacion
       $this->insert($data);
       
       
    
        return $this;
            
    
    }
    /**
     * modifica los datos de Alumnos Rut= $rut
     * @param  $RBD
     * @param  $periodo
     * @param  $nombre_establecimiento
     */
    public function cambiar($id,$idcurso,$rut,$comentarios,$fecha,$profesor,$periodo)
    {
        $data = array('idCursos'=>$idcurso,'idAlumnos'=>$rut,'contenido'=>$comentarios,'fechaComunicacion'=>$fecha,'idCuenta'=>$profesor,'idPeriodo'=>$periodo
            );
        //$this->update cambia datos de Alumno con Rut= $rut
        $this->update($data, 'idComunicacion = ' . (int) $id);
    }

    /**
     * borra el Alumno Rut= $rut
     * @param  $rut
     */
    
    public function borrar($id)
    {
        //$this->delete borrar album donde RBD=$rbd
        $this->delete('idComunicacion =' . (int) $id);
       
       
    }
    public function listar()
    {
      
          //devuelve todos los registros de la tabla
       $select = $this->select(); 

               
                $select->setIntegrityCheck(false) ;
        
        $select->from(array('o' => 'observaciones'),'*');
 
        

	$stmt = $select->query();
 
	$result = $stmt->fetchAll();
 
	return $result;

    }
    public function listarcitaciones($id)
    {
        $Id = (int) $id;
          //devuelve todos los registros de la tabla
       $select = $this->select(); 

               
                $select->setIntegrityCheck(false) ;
        
        $select->from(array('o' => 'comunicacion'),'*');
 
        
        
        $select->where('o.idCuenta = ' . $Id );

	$stmt = $select->query();
 
	return $stmt->fetchAll();

    }
    
     public function listarcitacionesid($id)
    {
        $Id = (int) $id;
          //devuelve todos los registros de la tabla
       $select = $this->select(); 

               
                $select->setIntegrityCheck(false) ;
        
        $select->from(array('o' => 'comunicacion'),'*');
 
        
        
        $select->where('o.idComunicacion = ' . $Id );

	$stmt = $select->query();
 
	return $stmt->fetchAll();

    }
}
?>
