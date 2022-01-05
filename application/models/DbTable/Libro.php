<?php

class Application_Model_DbTable_Libro extends Zend_Db_Table_Abstract
{

    protected $_name = 'libro_clases';
    protected $_primary = 'idlibro_clases';

    /**
     * devuelve un arreglo con los datos de alumnos con Rut=$rut
     * @param  $id id del establecimiento
     * @return  arreglo asociativo
     */
    public function get($id)
    {
        $ID = (int)$id;
        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false);

        $select->from(array('a' => 'libro_clases'), '*');

        /* $select->join(array('obs' => 'observaciones'),
                             'obs.idObservaciones = a.Alumno_detalle_academico_idObservaciones');

     $select->join(array('asist' => 'asistencia'),
                             'asist.idAsistencia = a.Asistencia_idAsistencia');
         $select->join(array('inas' => 'inasistencia'),
                             'inas.idInasistencia = a.Inasistencia_idInasistencia');*/
        $select->join(array('cur' => 'cursos'),
            'cur.idCursos = a.Cursos_idCursos');
        //$select->join(array('lis' => 'lista'),
        //'lis.idLista = a.Lista_idLista');

        $select->where('a.idlibro_clases = ' . $ID);

        $stmt = $select->query();

        $result = $stmt->fetchAll();

        return $result;

    }

    /**
     *  agrega un nuevo alumno a la base de datos
     * @param  $RBD
     * @param  $periodo
     * @param  $nombre_establecimiento
     */
    public function agregar($idlibro, $curso)
    {
        $data = array('idLibro_clases' => $idlibro, 'Cursos_idCursos' => $curso);
        //$this->insert inserta nuevo Personal
        $this->insert($data);


    }

    /**
     * modifica los datos de Alumnos Rut= $rut
     * @param  $RBD
     * @param  $periodo
     * @param  $nombre_establecimiento
     */
    public function cambiar($idlibro, $observaciones, $inasistencia, $asistencia, $curso, $lista)
    {
        $data = array('idLibro_clases' => $idlibro, 'Alumno_detalle_academico_idObservaciones' => $observaciones, 'Inasistencia_idInasistencia' => $inasistencia, 'Asistencia_idAsistencia' => $asistencia, 'Cursos_idCursos' => $curso, 'Lista_idLista' => $lista
        );
        //$this->update cambia datos de Alumno con Rut= $rut
        $this->update($data, 'idLibro_clases = ' . (int)$idlibro);
    }

    /**
     * borra el Alumno Rut= $rut
     * @param  $rut
     */
    public function borrar($idlibro)
    {
        //$this->delete borrar alumno donde Rut=$rut
        $this->delete('idLibro_clases =' . (int)$idlibro);
    }

    public function listar()
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();

        //Add next line
        $select->setIntegrityCheck(false)
            ->from(array('a' => 'libro_clases'), '*');

        /*$select->join(array('obs' => 'observaciones'),
                            'obs.idObservaciones = a.Alumno_detalle_academico_idObservaciones');

	$select->join(array('asist' => 'asistencia'),
                            'asist.idAsistencia = a.Asistencia_idAsistencia');
        $select->join(array('inas' => 'inasistencia'),
                            'inas.idInasistencia = a.Inasistencia_idInasistencia');*/
        $select->join(array('cur' => 'cursos'),
            'cur.idCursos = a.Cursos_idCursos');
        // $select->join(array('lis' => 'lista'),
        // 'lis.idLista = a.Lista_idLista');

        //$select->where('a.idlibro_clases = ' . $ID );

        return $this->fetchAll($select);

        //return $this->fetchAll($select);


    }
}
