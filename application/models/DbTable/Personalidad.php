<?php

class Application_Model_DbTable_Personalidad extends Zend_Db_Table_Abstract
{

    protected $_name = 'personalidad';
    protected $_primary = 'idPersonalidad';


    public function get($idcurso)
    {
        $idcurso = (int)$idcurso;
        $row = $this->fetchAll('idPersonalidad = ' . $idcurso);
        if (!$row) {
            throw new Exception("Could not find row $idcurso");
        }
        return $row->toArray();
    }

    public function getpersonalidad($idalumno, $idperiodo,$segmento=null)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'personalidad'))
            ->where('p.idAlumnos=?', $idalumno);
            if(!empty($segmento)){
                $select->where('p.segmento=?', $segmento);
            }
            $select->where('p.idPeriodo=?', $idperiodo);

        $row = $this->fetchAll($select);
        if (!$row) {
            return 0;
        }

        return $row->toArray();
    }


    public function validapersonalidad($idalumno, $idperiodo, $idsegmento, $tipo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'personalidad'))
            ->where('p.idAlumnos=?', $idalumno)
            ->where('p.idPeriodo=?', $idperiodo)
            ->where('p.segmento=?', $idsegmento)
            ->where('p.tipo=?', $tipo);
        $row = $this->fetchAll($select);
        return $row->toArray();


    }


    public function agregar($r1, $r2, $r3, $r4, $r5, $r6, $r7, $r8, $r9, $r10, $r11, $r12, $r13, $r14, $r15, $r16, $r17, $r18, $r19, $tipo, $segmento, $idalumno, $curso, $idperiodo, $comentario)
    {
        $data = array('R1' => $r1, 'R2' => $r2, 'R3' => $r3, 'R4' => $r4, 'R5' => $r5, 'R6' => $r6, 'R7' => $r7, 'R8' => $r8, 'R9' => $r9, 'R10' => $r10, 'R11' => $r11, 'R12' => $r12, 'R13' => $r13, 'R14' => $r14, 'R15' => $r15, 'R16' => $r16, 'R17' => $r17, 'R18' => $r18, 'R19' => $r19, 'tipo' => $tipo, 'segmento' => $segmento, 'idAlumnos' => $idalumno, 'idCursos' => $curso, 'idPeriodo' => $idperiodo, 'comentario' => $comentario
        );
        $this->insert($data);
    }

    public function agregart($r1, $r2, $r3, $r4, $r5, $r6, $r7, $r8, $r9, $r10, $r11, $r12, $r13, $r14, $r15, $r16, $r17, $r18, $r19, $r20, $tipo, $segmento, $idalumno, $curso, $idperiodo, $comentario)
    {
        $data = array('R1' => $r1, 'R2' => $r2, 'R3' => $r3, 'R4' => $r4, 'R5' => $r5, 'R6' => $r6, 'R7' => $r7, 'R8' => $r8, 'R9' => $r9, 'R10' => $r10, 'R11' => $r11, 'R12' => $r12, 'R13' => $r13, 'R14' => $r14, 'R15' => $r15, 'R16' => $r16, 'R17' => $r17, 'R18' => $r18, 'R19' => $r19, 'R20' => $r20, 'tipo' => $tipo, 'segmento' => $segmento, 'idAlumnos' => $idalumno, 'idCursos' => $curso, 'idPeriodo' => $idperiodo, 'comentario' => $comentario
        );
        $this->insert($data);
    }


    public function cambiar($id, $r1, $r2, $r3, $r4, $r5, $r6, $r7, $r8, $r9, $r10, $r11, $r12, $r13, $r14, $r15, $r16, $r17, $r18, $r19, $tipo, $comentario)
    {
        $data = array('R1' => $r1, 'R2' => $r2, 'R3' => $r3, 'R4' => $r4, 'R5' => $r5, 'R6' => $r6, 'R7' => $r7, 'R8' => $r8, 'R9' => $r9, 'R10' => $r10, 'R11' => $r11, 'R12' => $r12, 'R13' => $r13, 'R14' => $r14, 'R15' => $r15, 'R16' => $r16, 'R17' => $r17, 'R18' => $r18, 'R19' => $r19, 'tipo' => $tipo, 'comentario' => $comentario
        );
        $this->update($data, 'idPersonalidad = ' . (int)$id);
    }

    public function cambiarmediat($id, $r1, $r2, $r3, $r4, $r5, $r6, $r7, $r8, $r9, $r10, $r11, $r12, $r13, $r14, $r15, $r16, $r17, $r18, $r19, $r20, $comentario)
    {
        $data = array('R1' => $r1, 'R2' => $r2, 'R3' => $r3, 'R4' => $r4, 'R5' => $r5, 'R6' => $r6, 'R7' => $r7, 'R8' => $r8, 'R9' => $r9, 'R10' => $r10, 'R11' => $r11, 'R12' => $r12, 'R13' => $r13, 'R14' => $r14, 'R15' => $r15, 'R16' => $r16, 'R17' => $r17, 'R18' => $r18, 'R19' => $r19, 'R20' => $r20, 'comentario' => $comentario
        );
        $this->update($data, 'idPersonalidad = ' . (int)$id);
    }


    public function borrar($id)
    {
        $this->delete('idPersonalidad =' . (int)$id);
    }


}

