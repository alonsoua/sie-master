<?php

class Application_Model_DbTable_Detallerolcuenta extends Zend_Db_Table_Abstract
{

    protected $_name = 'cuentaRoles';


    public function agregar($ides, $idrol, $idperiodo, $idcuenta)
    {
        $data = array('idEstablecimiento' => $ides, 'idRol' => $idrol, 'idPeriodo' => $idperiodo, 'idCuenta' => $idcuenta);
        $this->insert($data);

    }

    public function actualizar($id)
    {
        $data = array('estadoCuenta' => 1);
        $this->update($data,'id = ' . (int)$id);

    }

    public function borrar($id)
    {
        $this->delete('id =' . $id);
    }

    public function validarRol($id)
    {

        $select = $this->select();


        $select->setIntegrityCheck(false);

        $select->from(array('o' => 'cuentaRoles'), '*');

        $select->where('o.idCuenta = ' . $id);


        $stmt = $select->query();

        $row = $stmt->fetchAll();

        $ok = '0';
        if (!$row) {
            return $ok;
        } else {

            return $row;
        }


    }

    public function validarrolusuario($idcuenta,$periodo,$id)
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();


        $select->setIntegrityCheck(false) ;

        $select->from(array('o' => 'cuentaRoles'),'*');

        $select->where('o.idCuenta = ?',$idcuenta );
        $select->where('o.idPeriodo = ?' ,$periodo );
        $select->where('o.id= ?' ,$id );


        $stmt = $select->query();

        $row=$stmt->fetchAll();


        if ($row==NULL)
        {
            return 0;
        }else{

            return $row;
        }



    }


    public function getrolusuario($idcuenta,$idperiodo,$idestablecimiento)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('c' => 'cuentaRoles'), '*');

        $select->where('c.idCuenta = ?', $idcuenta);
        $select->where('c.idPeriodo = ?', $idperiodo);
        $select->where('c.idEstablecimiento = ?', $idestablecimiento);


        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;

    }

    public function listar($idcuenta, $periodo,$idestablecimiento)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('c' => 'cuentaRoles'), '*');
        $select->where('c.idCuenta = ?', $idcuenta);
        $select->where('c.idPeriodo = ?', $periodo);
        $select->where('c.idEstablecimiento = ?', $idestablecimiento);
        $select->order("c.idCuenta");
        return $this->fetchAll($select);
    }

    public function validauserrol($idcuenta,$periodo,$id,$idest,$estado)
    {
        //devuelve todos los registros de la tabla
        $select = $this->select();


        $select->setIntegrityCheck(false) ;

        $select->from(array('o' => 'cuentaRoles'),'*');

        $select->where('o.idEstablecimiento = ?',$idest);
        $select->where('o.idRol= ?' ,$id );
        $select->where('o.idPeriodo = ?' ,$periodo );
        $select->where('o.idCuenta = ?',$idcuenta );
        $select->where('o.estadoCuenta = ?',$estado);




        $stmt = $select->query();

        $row=$stmt->fetchAll();


        if ($row==NULL)
        {
            return 0;
        }else{

            return $row;
        }



    }

    public function getrolselect($idcuenta, $idestablecimiento,$idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('c' => 'cuentaRoles'), '*');
        $select->join(array('r' => 'roles'), 'c.idRol = r.idRoles');
        $select->where('c.idCuenta = ?', $idcuenta);
        $select->where('c.idEstablecimiento = ?', $idestablecimiento);
        $select->where('c.idPeriodo = ?', $idperiodo);

        $rowset = $this->fetchAll($select)->toArray();
        return $rowset;

    }

    public function getnombrerol($idrol)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('r' => 'roles'), '*');
        $select->where('r.idRoles = ?', $idrol);

        return $this->fetchAll($select)->toArray();
    }

    public function get($id)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);

        $select->from(array('c' => 'cuentaRoles'), '*');
        $select->where('c.id = ?', $id);
        $select->where('c.estadoCuenta = ?', 1);

        return $this->fetchAll($select)->toArray();
    }


}
