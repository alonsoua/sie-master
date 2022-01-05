<?php

class Application_Model_DbTable_Sostenedor extends Zend_Db_Table_Abstract
{

    protected $_name = 'sostenedor';
        protected $_primary='idSostenedor';
        
        
    public function get($idsostenedor)
    {

        
        
        $row = $this->fetchRow('idSostenedor = ' . $idsostenedor);
        if (!$row)
        {
            throw new Exception("No se Encuentra el dato $idSostenedor");
        }
    
        return $row->toArray();
    }

    /**
     *  agrega un nuevo Sostenedor a la base de datos
     * @param  $rutsostenedor
     * @param  $nombre_establecimiento
     */
    public function agregar($rutsostenedor, $nombre, $direccion,$telefono,$comuna,$correo)
    {
        $data = array('rutSostenedor' => $rutsostenedor, 'nombreSostenedor' => $nombre, 'direccion' => $direccion,'telefono'=>$telefono,'comuna'=>$comuna,'correo'=>$correo);
        //$this->insert inserta nuevo Establecimiento
        $this->insert($data);
    }

    /**
     * modifica los datos del Periodo idPeriodo= $idperiodo
     * @param  $idPeriodo
     * @param  $fecha
     */
    public function cambiar($id,$rutsostenedor,$nombre,$direccion,$telefono,$comuna,$correo)
    {
        $data = array('rutSostenedor' => $rutsostenedor, 'nombreSostenedor' => $nombre, 'direccion' => $direccion,'telefono'=>$telefono,'comuna'=>$comuna,'correo'=>$correo);
        //$this->update cambia datos de Establecimiento con RBD= $RBD
        $this->update($data, 'idSostenedor = ' . (int) $id);
    }

    /**
     * borra el Sostenedor con Rut_sostenedor= $rutsostenedor
     * @param  $rutsostenedor
     */
    public function borrar($id)
    {
        //$this->delete borrar album donde RBD=$rbd
        $this->delete('idSostenedor =' . (int) $id);
       
       
    }

public function listar()
    {
        //devuelve todos los registros de la tabla
        
        return $this->fetchAll();
    }


}

