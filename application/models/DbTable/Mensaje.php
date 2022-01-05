<?php

class Application_Model_DbTable_Mensaje extends Zend_Db_Table_Abstract
{

    protected $_name = 'mensajes';


    public function get($id)
    {
        $db = new Zend_Db_Table('mensajesConfiguracion');
        $id = (int)$id;
        $row = $db->fetchRow('id = ' . $id);
        if (!$row) {
            throw new Exception("Could not find row $id");
        }

        return $row->toArray();
    }

    public function agregarmensaje($emisor, $asunto, $mensaje, $fecha, $estado, $idperiodo)
    {

        $data = array('emisor' => $emisor, 'asunto' => $asunto, 'mensaje' => $mensaje, 'fecha' => $fecha, 'estado' => $estado, 'idPeriodo' => $idperiodo);
        $this->insert($data);
    }

    public function agregarreceptores($receptor, $leido, $fecha, $idmensaje)
    {

        $db = new Zend_Db_Table('mensajesReceptor');
        $data = array('receptor' => $receptor, 'leido' => $leido, 'fechaleido' => $fecha, 'idMensaje' => $idmensaje);
        $db->insert($data);
    }

    public function getmensaje($id)
    {
        $id = (int)$id;
        $row = $this->fetchRow('idMensaje = ' . $id);
        if (!$row) {
            throw new Exception("Could not find row $id");
        }

        return $row->toArray();
    }


    public function getmensajes($id, $idperiodo, $opciones)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('m' => 'mensajes'), '*');
        $select->join(array('mr' => 'mensajesReceptor'), 'm.idMensaje = mr.idMensaje');
        $select->joinLeft(array('rol' => 'cuentaRoles'), 'm.emisor = rol.id', array('idEstablecimiento', 'idRol', 'idPeriodo','idCuenta','estadoCuenta'));
        $select->joinLeft(array('c' => 'cuentasUsuario'), 'rol.idCuenta = c.idCuenta', array('nombrescuenta', 'paternocuenta', 'maternocuenta'));

        $select->joinLeft(array('roles' => 'roles'), 'roles.idRoles = rol.idRol');
        $select->joinLeft(array('e' => 'establecimiento'), 'rol.idEstablecimiento = e.idEstablecimiento', array('rbd', 'nombreEstablecimiento'));
        $select->where('mr.receptor=?' , $id);
        $select->where('m.idPeriodo=?' , $idperiodo);
        $select->where('rol.idPeriodo=?' , $idperiodo);
        $select->where('mr.leido IN(?)', $opciones);
        $select->order('m.fecha DESC');
        return $this->fetchAll($select)->toArray();
    }

    public function getmensajeusuario($idusuario, $idmensaje)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('m' => 'mensajes'), '*');
        $select->join(array('mr' => 'mensajesReceptor'), 'm.idMensaje = mr.idMensaje');
        $select->joinLeft(array('rol' => 'cuentaRoles'), 'm.emisor = rol.id', array('idEstablecimiento', 'idRol', 'idPeriodo','idCuenta','estadoCuenta'));
        $select->join(array('c' => 'cuentasUsuario'), 'rol.idCuenta = c.idCuenta', array('nombrescuenta', 'paternocuenta', 'maternocuenta'));
        $select->where('mr.receptor=' . $idusuario);
        $select->where('m.idMensaje=' . $idmensaje);
        return $this->fetchAll($select)->toArray();
    }

    public function getmensajeusuarioenviado($idusuario, $idmensaje)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('m' => 'mensajes'), '*');
        $select->join(array('c' => 'cuentasUsuario'), 'm.emisor = c.idCuenta', array('nombrescuenta', 'paternocuenta', 'maternocuenta'));
        $select->where('emisor=' . $idusuario);
        $select->where('idMensaje=' . $idmensaje);
        return $this->fetchAll($select)->toArray();
    }

    public function getmensajesenviados($id, $idperiodo)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('m' => 'mensajes'), '*');
        $select->joinLeft(array('rol' => 'cuentaRoles'), 'm.emisor = rol.id', array('idEstablecimiento', 'idRol', 'idPeriodo','idCuenta','estadoCuenta'));
        $select->join(array('c' => 'cuentasUsuario'), 'rol.idCuenta = c.idCuenta', array('nombrescuenta', 'paternocuenta', 'maternocuenta'));
        $select->joinLeft(array('roles' => 'roles'), 'roles.idRoles = rol.idRol');
        //$select->joinLeft(array('e' => 'establecimiento'), 'rol.idEstablecimiento = e.idEstablecimiento',array('rbd','nombreEstablecimiento'));
        $select->where('emisor=' . $id);
        $select->where('m.idPeriodo=' . $idperiodo);
        $select->where('rol.idPeriodo=' . $idperiodo);
        $select->order('m.fecha DESC');
        return $this->fetchAll($select)->toArray();
    }

    public function getreceptores($idmensaje, $idperiodo)
    {
        $select = $this->select();
        $select->setIntegrityCheck(false);
        $select->from(array('m' => 'mensajesReceptor'), '*');
        $select->joinLeft(array('rol' => 'cuentaRoles'), 'm.receptor = rol.id', array('idEstablecimiento', 'idRol', 'idPeriodo'));
        $select->join(array('c' => 'cuentasUsuario'), 'rol.idCuenta = c.idCuenta', array('nombrescuenta', 'paternocuenta', 'maternocuenta'));
        $select->joinLeft(array('roles' => 'roles'), 'roles.idRoles = rol.idRol');
        $select->joinLeft(array('e' => 'establecimiento'), 'rol.idEstablecimiento = e.idEstablecimiento', array('rbd', 'nombreEstablecimiento'));
        $select->where('m.idMensaje=' . $idmensaje);
        $select->where('rol.idPeriodo=' . $idperiodo);
        return $this->fetchAll($select)->toArray();
    }

    public function actualizarleido($id, $fecha, $idmensaje)
    {
        $db = new Zend_Db_Table('mensajesReceptor');
        $data = array('leido' => 2, 'fechaleido' => $fecha);
        $where = array();
        $where[] = $db->getAdapter()->quoteInto('idReceptor = ?', $id);
        $where[] = $db->getAdapter()->quoteInto('idmensaje = ?', $idmensaje);
        $db->update($data, $where);
    }


    public function listar($idestablecimiento, $idperiodo)
    {

        $db = new Zend_Db_Table('mensajesConfiguracion');
        $select = $db->select();
        $select->where('idEstablecimiento=' . $idestablecimiento);
        $select->where('idPeriodo=' . $idperiodo);
        return $this->fetchAll($select)->toArray();
    }


    public function agregarconfig($datos, $idperiodo, $idestablecimiento)
    {
        $db = new Zend_Db_Table('mensajesConfiguracion');

        $data = array('config' => $datos, 'idPeriodo' => $idperiodo, 'idEstablecimiento' => $idestablecimiento);
        //$this->insert inserta nuevo
        $db->insert($data);
    }

    public function cambiarconfig($id, $datos)
    {
        $db = new Zend_Db_Table('mensajesConfiguracion');
        $data = array('config' => $datos);
        //$this->update cambia datos de Establecimiento con RBD= $RBD
        $db->update($data, 'id = ' . (int)$id);
    }

    //Usuarios

    public function listarusuarioestablecimientos($idperiodo, $idestablecimiento, $idusuario, $roles)
    {

        $select = $this->select();

        $select->setIntegrityCheck(false)
            ->from(array('p' => 'cuentasUsuario'), array('idCuenta', 'usuario', 'nombrescuenta', 'paternocuenta', 'maternocuenta'))
            ->joinLeft(array('rol' => 'cuentaRoles'), 'p.idCuenta = rol.idCuenta')
            ->joinLeft(array('roles' => 'roles'), 'roles.idRoles = rol.idRol')
            ->joinLeft(array('e' => 'establecimiento'), 'rol.idEstablecimiento = e.idEstablecimiento', array('rbd', 'nombreEstablecimiento'))
            ->joinLeft(array('cu' => 'cursosactual'), 'p.idCuenta= cu.idCuentaJefe', array('idPeriodo', 'idCodigoTipo', 'idCodigoGrado', 'letra'))
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'cu.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'cu.idCodigoGrado= g.idCodigoGrado');
        $select->where('rol.idPeriodo=?', $idperiodo);
        $select->where('rol.estadoCuenta=1');
        $select->where('rol.idEstablecimiento  IN(?)', $idestablecimiento);
        $select->where('rol.idRol  IN(?)', $roles);
        $select->where('rol.id NOT IN (?)', $idusuario);
        $select->order("e.nombreEstablecimiento");
        $select->order("roles.nombreRol");
        $select->order("p.nombrescuenta");
        $select->order("p.paternocuenta");
        $select->order("p.maternocuenta");
        $select->group('rol.id');

        return $this->fetchAll($select)->toArray();

    }

    public function listarusuariotodo($idperiodo, $idusuario, $roles)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('p' => 'cuentasUsuario'), array('idCuenta', 'usuario', 'nombrescuenta', 'paternocuenta', 'maternocuenta'))
            ->joinLeft(array('rol' => 'cuentaRoles'), 'p.idCuenta = rol.idCuenta')
            ->joinLeft(array('roles' => 'roles'), 'roles.idRoles = rol.idRol')
            ->joinLeft(array('e' => 'establecimiento'), 'rol.idEstablecimiento = e.idEstablecimiento', array('rbd', 'nombreEstablecimiento'))
            ->joinLeft(array('cu' => 'cursosactual'), 'p.idCuenta= cu.idCuentaJefe', array('idPeriodo', 'idCodigoTipo', 'idCodigoGrado', 'letra'))
            ->joinLeft(array('ce' => 'codigotipoensenanza'), 'cu.idCodigoTipo= ce.idCodigoTipo')
            ->joinLeft(array('g' => 'codigogrados'), 'cu.idCodigoGrado= g.idCodigoGrado');
        $select->where('rol.idPeriodo=?', $idperiodo);
        $select->where('rol.estadoCuenta=1');
        $select->where('rol.id NOT IN (?)', $idusuario);
        $select->where('rol.idRol  IN(?)', $roles);
        $select->order("e.nombreEstablecimiento");
        $select->order("roles.nombreRol");
        $select->order("p.nombrescuenta");
        $select->order("p.paternocuenta");
        $select->order("p.maternocuenta");
        $select->group('rol.id');
        //$select->group('p.idCuenta');

        return $this->fetchAll($select)->toArray();


    }

    public function getnombreusuario($idcuenta)
    {

        $select = $this->select();
        $select->setIntegrityCheck(false)
            ->from(array('rol' => 'cuentaRoles'), array('idEstablecimiento', 'idRol', 'idPeriodo','idCuenta','estadoCuenta'));
        //$select->joinLeft(array('rol' => 'cuentaRoles'), 'm.emisor = rol.id', array('idEstablecimiento', 'idRol', 'idPeriodo','idCuenta','estadoCuenta'));
        $select->join(array('c' => 'cuentasUsuario'), 'rol.idCuenta = c.idCuenta', array('nombrescuenta', 'paternocuenta', 'maternocuenta'));

        $select->where('rol.id=?', $idcuenta);
        return $this->fetchAll($select)->toArray();


    }


}

