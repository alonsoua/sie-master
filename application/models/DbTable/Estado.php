<?php

class Application_Model_DbTable_Estado extends Zend_Db_Table_Abstract
{

    protected $_name = 'estadoAlumno';
    protected $_primary = 'idEstado';
    
  

public function listar()
    {
        //devuelve todos los registros de la tabla
    
        return $this->fetchAll();
    }
    
   

    
}

