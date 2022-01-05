<?php

class Application_Model_DbTable_Subvencion extends Zend_Db_Table_Abstract
{

    protected $_name = 'subvencion_escolar';
        protected $_primary='idSubvencion';
        
        
    public function get($rbd)
    {

    $RBD = (int)$rbd;
        //$this->fetchRow devuelve fila donde Rut_sostenedor = $rutsostenedor
        $row = $this->fetchRow('Establecimiento_RBD = ' . $RBD);
        if (!$row)
        {
            throw new Exception("No se Encuentra el dato $RBD");
        }
    
        return $row->toArray();
    }

    /**
     *  agrega un nuevo Sostenedor a la base de datos
     * @param  $rutsostenedor
     * @param  $nombre_establecimiento
     */
    public function agregar($idsubvencion,$RBD, $nombre, $matricula,$prioritarios,$concentracion,$calificacion,$matriculapie)
    {
        $data = array('idSubvencion' => $idsubvencion, 'Establecimiento_RBD' => $RBD, 'nombre' => $nombre,'matricula'=>$matricula,'alumnos_prioritarios'=>$prioritarios,'concentracion'=>$concentracion,'calificacion'=>$calificacion,'matricula_pie'=>$matriculapie);
        //$this->insert inserta nuevo Establecimiento
        $this->insert($data);
    }

    /**
     * modifica los datos del Periodo idPeriodo= $idperiodo
     * @param  $idPeriodo
     * @param  $fecha
     */
    public function cambiar($idsubvencion,$RBD, $nombre, $matricula,$prioritarios,$concentracion,$calificacion,$matriculapie)
    {
        $data = array('idSubvencion' => $idsubvencion, 'Establecimiento_RBD' => $RBD, 'nombre' => $nombre,'matricula'=>$matricula,'alumnos_prioritarios'=>$prioritarios,'concentracion'=>$concentracion,'calificacion'=>$calificacion,'matricula_pie'=>$matriculapie);
        //$this->update cambia datos de Establecimiento con RBD= $RBD
        $this->update($data, 'idSubvencion = ' . (int) $idsubvencion);
    }

    /**
     * borra el Sostenedor con Rut_sostenedor= $rutsostenedor
     * @param  $rutsostenedor
     */
    public function borrar($rbd)
    {
        //$this->delete borrar album donde RBD=$rbd
        $this->delete('Establecimiento_RBD =' . (int) $rbd);
       
       
    }

public function listar()
    {
        //devuelve todos los registros de la tabla
        
        return $this->fetchAll();
    }


}


?>
