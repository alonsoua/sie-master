<?php

class Application_Model_DbTable_Cuentas extends Zend_Db_Table_Abstract
{

    protected $_name = 'cuentasUsuario';


    public function get($id)
    {
        $idusuario = (int)$id;
        $row = $this->fetchRow('idCuenta= ' . $idusuario);
        return $row->toArray();
    }

    public function getusuariocon($id)
    {
        $idusuario = (int)$id;
        $row = $this->fetchRow('idCuenta = ' . $idusuario);
        if (!$row) {
            return 0;
        }

        return $row->toArray();
    }

    public function getusuario($id, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'cuentasUsuario'), array('idCuenta', 'usuario', 'nombrescuenta', 'paternocuenta', 'maternocuenta', 'correo', 'contrasena'))
            ->joinLeft(array('rol' => 'cuentaRoles'), 'p.idCuenta = rol.idCuenta', array('idEstablecimiento', 'idRol', 'idPeriodo'))
            ->joinLeft(array('roles' => 'roles'), 'roles.idRoles = rol.idRol')
            ->where('p.idCuenta = ?', $id)
            ->where('rol.estadoCuenta = 1')
            ->where('rol.idPeriodo = ?', $idperiodo);
        $row = $this->fetchRow($select);
        if (!$row) {
            return 0;
        }

        return $row->toArray();
    }

    public function getusuariorol($id, $idperiodo, $idrol)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'cuentasUsuario'), array('idCuenta', 'usuario', 'nombrescuenta', 'paternocuenta', 'maternocuenta', 'correo', 'contrasena'))
            ->joinLeft(array('rol' => 'cuentaRoles'), 'p.idCuenta = rol.idCuenta', array('idEstablecimiento', 'idRol', 'idPeriodo'))
            ->joinLeft(array('roles' => 'roles'), 'roles.idRoles = rol.idRol')
            ->where('p.idCuenta = ?', $id)
            ->where('rol.id = ?', $idrol)
            ->where('rol.estadoCuenta = 1')
            ->where('rol.idPeriodo = ?', $idperiodo);
        $row = $this->fetchAll($select);
        if (!$row) {
            return 0;
        }

        return $row->toArray();
    }

    public function gettipousuario($id, $est)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'cuentasUsuario'))
            ->join(array('rol' => 'cuentaRoles'), 'p.idCuenta = rol.idCuenta')
            ->join(array('roles' => 'roles'), 'roles.idRoles = rol.idRol')
            ->where('rol.idEstablecimiento = ?', $est)
            ->where('rol.estadoCuenta = 1')
            ->where('rol.idRol = ?', $id);


        return $this->fetchAll($select)->toArray();
    }

    public function valida($id)
    {

        $select = $this->select()
            ->from(array('p' => 'cuentasUsuario'))
            ->where('p.usuario = ?', $id);
        $row = $this->fetchRow($select);
        if (empty($row)) {
            return true;
        } else {
            return false;
        }


    }

    public function valida_usuario_pass($id, $nombre)
    {

        $select = $this->select()
            ->from(array('p' => 'cuentasUsuario'))
            ->where('p.usuario= ?', $nombre)
            ->where('p.id = ?', $id);
        $row = $this->fetchRow($select);
        if (empty($row)) //if row exist
        {
            return $row = 'error';
        } else {
            return $row = 'ok';
        }


    }


    public function agregar($username, $pass, $correo, $nombre, $paterno, $materno)
    {
        $data = array('usuario' => $username, 'contrasena' => $pass, 'correo' => $correo, 'nombrescuenta' => $nombre, 'paternocuenta' => $paterno, 'maternocuenta' => $materno, 'update_password' => '2');
        $this->insert($data);
    }


    public function listar($idperiodo, $idestablecimiento, $estado)
    {

        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('p' => 'cuentasUsuario'), array('idCuenta', 'usuario', 'nombrescuenta', 'paternocuenta', 'maternocuenta', 'fechaIngreso'))
            ->joinLeft(array('rol' => 'cuentaRoles'), 'p.idCuenta = rol.idCuenta')
            ->joinLeft(array('roles' => 'roles'), 'roles.idRoles = rol.idRol')
            ->joinLeft(array('e' => 'establecimiento'), 'rol.idEstablecimiento = e.idEstablecimiento')
            ->joinLeft(array('cu' => 'cursosactual'), 'p.idCuenta= cu.idCuentaJefe', array('idPeriodo', 'idCodigoTipo', 'idCodigoGrado', 'letra', 'idCuentaJefe'))
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'cu.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'cu.idCodigoGrado= g.idCodigoGrado');
        $select->where('rol.idPeriodo=?', $idperiodo);
        $select->where('rol.idEstablecimiento = ?', $idestablecimiento);
        $select->where('rol.estadoCuenta= ?', $estado);

        $select->order("p.nombrescuenta");
        $select->order("p.paternocuenta");
        $select->order("p.maternocuenta");
        $select->group('p.idCuenta');

        return $this->fetchAll($select)->toArray();

    }

    public function listartodo($idperiodo, $estado)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'cuentasUsuario'), array('idCuenta', 'usuario', 'nombrescuenta', 'paternocuenta', 'maternocuenta', 'fechaIngreso'))
            ->joinLeft(array('rol' => 'cuentaRoles'), 'p.idCuenta = rol.idCuenta')
            ->joinLeft(array('roles' => 'roles'), 'roles.idRoles = rol.idRol')
            ->joinLeft(array('e' => 'establecimiento'), 'rol.idEstablecimiento = e.idEstablecimiento', array('rbd', 'nombreEstablecimiento'));
        $select->where('rol.idPeriodo=?', $idperiodo);

        $select->where('rol.estadoCuenta= ?', $estado);

        $select->order("e.nombreEstablecimiento");
        $select->order("roles.nombreRol");
        $select->order("p.paternocuenta");
        $select->order("p.maternocuenta");
        $select->order("p.nombrescuenta");


        return $this->fetchAll($select)->toArray();

    }

    public function borrar($id)
    {
        $this->delete('idCuenta =' . (int)$id);
    }

    public function actualizar($id, $pass)
    {
        $idusuario = (int)$id;
        $data = array('contrasena' => $pass, 'update_password' => '2');
        $this->update($data, 'idCuenta = ' . $idusuario);
    }

    public function actualizafecha($fecha, $id)
    {
        $data = array('fechaIngreso' => $fecha);
        $this->update($data, 'idCuenta = ' . $id);
    }

    public function actualizarrbd($id, $rbd)
    {
        $data = array('idEstable' => $rbd);
        $this->update($data, 'id = ' . $id);
    }

    public function actualizardatos($idcuenta, $nombre, $paterno, $materno, $correo)
    {
        $data = array('nombrescuenta' => $nombre, 'paternocuenta' => $paterno, 'maternocuenta' => $materno, 'correo' => $correo);
        $this->update($data, 'idCuenta = ' . $idcuenta);
    }


    public function getusuarioid($id, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'cuentaRoles'))
            ->joinLeft(array('rol' => 'roles'), 'rol.idRoles = p.idRol')
            ->joinLeft(array('es' => 'establecimiento'), 'es.idEstablecimiento = p.idEstablecimiento')
            ->where('p.idCuenta=?', $id)
            ->where('p.idPeriodo=?', $idperiodo)
            ->where('p.estadoCuenta=?',1);
        return $this->fetchAll($select);
    }


    public function listarjefe($marca)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'cuentasUsuario'))
            ->join(array('rol' => 'cuentaRoles'), 'rol.idCuenta = p.idCuenta')
            ->join(array('e' => 'establecimiento'), 'rol.idEstablecimiento = e.idEstablecimiento')
            ->where('rol.idEstablecimiento = ?', $marca)
            ->where('rol.estadoCuenta = 1')
            ->where('rol.idRol = 2');
        $select->order("p.nombrescuenta");

        $rowset = $this->fetchAll($select);
        $data = array();
        foreach ($rowset as $row) {
            $data[$row->idCuenta] = $row->nombrescuenta . ' ' . $row->paternocuenta . ' ' . $row->maternocuenta;
        }
        return $data;

    }

    public function listarsolodocentes($idestablecimiento)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'cuentasUsuario'), array('idCuenta', 'usuario', 'nombrescuenta', 'paternocuenta', 'maternocuenta'))
            ->join(array('rol' => 'cuentaRoles'), 'p.idCuenta = rol.idCuenta')
            ->join(array('roles' => 'roles'), 'roles.idRoles = rol.idRol')
            ->join(array('e' => 'establecimiento'), 'rol.idEstablecimiento = e.idEstablecimiento')
            ->where('rol.idEstablecimiento = ?', $idestablecimiento)
            ->where('rol.estadoCuenta = 1')
            ->where('rol.idRol = 2');
        $select->order("p.nombrescuenta");
        $select->order("p.paternocuenta");
        $select->order("p.maternocuenta");
        $select->order("p.maternocuenta");
        $select->group('p.idCuenta');

        return $this->fetchAll($select);

    }

    public function listaderoles($idperiodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'cuentaRoles'))
            ->where('p.idPeriodo= ?', $idperiodo);

        return $this->fetchAll($select)->toArray();
    }

    public function agregarrol($idestablecimiento, $rol, $idperiodo, $idcuenta)
    {
        $db = new Zend_Db_Table('cuentaRoles');
        $data = array('idestablecimiento' => $idestablecimiento, 'idRol' => $rol, 'idPeriodo' => $idperiodo, 'idCuenta' => $idcuenta);
        $db->insert($data);
    }

    public function listardirector($marca)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'cuentasUsuario'))
            ->join(array('rol' => 'cuentaRoles'), 'rol.idCuenta = p.idCuenta')
            ->join(array('e' => 'establecimiento'), 'rol.idEstablecimiento = e.idEstablecimiento')
            ->where('rol.idEstablecimiento = ?', $marca)
            ->where('rol.estadoCuenta = 1')
            ->where('rol.idRol = 3');
        $select->order("p.nombrescuenta");

        $rowset = $this->fetchAll($select);
        $data = array();
        foreach ($rowset as $row) {
            $data[$row->idCuenta] = $row->nombrescuenta . ' ' . $row->paternocuenta . ' ' . $row->maternocuenta;
        }
        return $data;

    }

    public function actualizarestadocuenta($idcuenta, $estado, $idperiodo, $establecimiento, $idrol)
    {

        $db = new Zend_Db_Table('cuentaRoles');
        $data = array('estadoCuenta' => $estado);
        $where = array();
        $where[] = $db->getAdapter()->quoteInto('idEstablecimiento= ?', $establecimiento);
        $where[] = $db->getAdapter()->quoteInto('idRol = ?', $idrol);
        $where[] = $db->getAdapter()->quoteInto('idPeriodo = ?', $idperiodo);
        $where[] = $db->getAdapter()->quoteInto('idCuenta = ?', $idcuenta);
        $db->update($data, $where);
    }

    public function getestablecimientocuenta($id,$idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'cuentaRoles'))
            ->joinLeft(array('rol' => 'roles'), 'rol.idRoles = p.idRol')
            ->joinLeft(array('es' => 'establecimiento'), 'es.idEstablecimiento = p.idEstablecimiento')
            ->where('p.idCuenta=?', $id)
            ->where('p.idPeriodo=?', $idperiodo)
            ->where('p.estadoCuenta=?', '1')
            ->group('p.idEstablecimiento');

        return $this->fetchAll($select);
    }


    public function getidentificador()
    {
        $db = new Zend_Db_Table('identificador');
        $row = $db->fetchAll();
        return $row->toArray();
    }


    public function listarjefeperiodo($marca, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('p' => 'cuentasUsuario'));
        $select->join(array('rol' => 'cuentaRoles'), 'rol.idCuenta = p.idCuenta');
        $select->join(array('e' => 'establecimiento'), 'rol.idEstablecimiento = e.idEstablecimiento');
        $select->where('rol.idEstablecimiento = ?', $marca);
        $select->where('rol.idPeriodo = ?', $idperiodo);
        $select->where('rol.estadoCuenta = 1');
        $select->where('rol.idRol = 2');
        $select->order("p.paternocuenta");
        $select->order("p.maternocuenta");
        $select->order("p.nombrescuenta");

        $rowset = $this->fetchAll($select);
        $data = array();
        foreach ($rowset as $row) {
            $data[$row->idCuenta] = $row->nombrescuenta . ' ' . $row->paternocuenta . ' ' . $row->maternocuenta;
        }
        return $data;

    }

    public function listardocentesperiodo($idestablecimiento, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('p' => 'cuentasUsuario'), array('nombrescuenta', 'paternocuenta', 'maternocuenta', 'idCuenta'));
        $select->join(array('rol' => 'cuentaRoles'), 'rol.idCuenta = p.idCuenta', array());
        $select->where('rol.idEstablecimiento = ?', $idestablecimiento);
        $select->where('rol.idPeriodo = ?', $idperiodo);
        $select->where('rol.estadoCuenta = 1');
        $select->where('rol.idRol = 2');
        $select->order("p.paternocuenta");
        $select->order("p.maternocuenta");
        $select->order("p.nombrescuenta");
        return $this->fetchAll($select)->toArray();


    }


}
