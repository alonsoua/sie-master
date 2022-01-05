<?php

class Application_Model_DbTable_Resources extends Zend_Db_Table_Abstract
{



    protected $_name = 'refcountry';


    public function listarpais()
    {

        $this->_db = Zend_Registry::get('db2');

        return $this->fetchAll();


    }


}
