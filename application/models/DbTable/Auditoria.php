<?php

class Application_Model_DbTable_Auditoria extends Zend_Db_Table_Abstract
{

    protected $_name = 'auditoria';
    protected $_primary = 'idAuditoria';


    public function agregar($idcuenta,$zona,$hora_zona,$token)
    {
        $data = array('idCuenta'=>$idcuenta,'zone'=>$zona,'timezone'=>$hora_zona,'token'=>$token);
        $this->insert($data);
    }


    public function agregardetalle($tabla,$campo,$operacion,$old,$new,$idauditoria)
    {
        $db = new Zend_Db_Table('auditoriadetalle');
        $data = array('TableName'=>$tabla,'FieldName'=>$campo,'OperationType'=>$operacion,'OldData'=>$old,'NewData'=>$new,'idAuditoria'=>$idauditoria);
        $db->insert($data);
    }


}
