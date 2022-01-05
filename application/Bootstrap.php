<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{




    protected function _initNavigationConfig()
    {
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();
        $navigation = new Zend_Navigation($this->getOption('navigation'));
        $view->navigation($navigation);
    }

    protected function _initViewNavigation()
    {
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();
        $config = new Zend_Config_Xml(APPLICATION_PATH . '/configs/navegacion.xml', 'nav');
        $navigation = new Zend_Navigation($config);
        $view->navigation($navigation);

    }

    protected function _initConfig()
    {


        Zend_Session::start();
        require('plugin/checksession.php');

        $plugin = Plugin_CheckAccess::getInstance();
        Zend_Controller_Front::getInstance()->registerPlugin($plugin);


        /**
         * below 2 lines are added so that, I can fetch the db values in helper file
         * at /zend_tutorial/library/My/Controller/Helper\Acl.php
         * :::: zend_tutorial is directory name of my local project.
         */
        //$this->bootstrap('db'); // Bootstrap the db resource from configuration
        // $db = $this->getResource('db'); // get the db object here, if necessary
        /** ******************** */


        /*$helper= new My_Controller_Helper_Acl();
          $helper->setRole();
          $helper->setResource();
          $helper->setPrivilage();
          $helper->setAcl();*/

        // comment above 5 lines to remove ACL functionality

    }

    protected function _initPlugins()
    {
        /*$objFront = Zend_Controller_Front::getInstance();
         $objFront->registerPlugin(new My_Controller_Plugin_Acl());
         return $objFront;*/

        // comment above 3 lines to remove ACL functionality
    }

    protected function _initSession()
    {

        Zend_Session::start();
        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;

        $usuariorol = new Zend_Session_Namespace('idRol');
        $userrol = $usuariorol->idRol;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $this->bootstrap('multidb');
        $multidb = $this->getPluginResource('multidb');
        $db2 = $multidb->getDb('db2');
        $db2->setFetchMode(Zend_Db::FETCH_OBJ);
        Zend_Registry::set('db2', $db2);

        //indivdual
        //$this->bootstrap('db');
        //$multidb = $this->getPluginResource('multidb');

        //$resource = $this->bootstrap->getPluginResource('multidb');

        //$db1 = $resource->getDb('db1');
        //$db2 = $resource->getDb('db2');


        //$this->bootstrap->getPluginResource('multidb')->getDb();
        //$this->bootstrap('autoload');
        $modelmensaje = new Application_Model_DbTable_Mensaje();
        $mensajes= new Zend_Session_Namespace('mensaje');

        if(!empty($user) && !empty($idperiodo)){
            $mensajesnoleidos = $modelmensaje->getmensajes($userrol, $idperiodo, array('1'));
            $totalmensajesnoleidos = count($mensajesnoleidos);

            $mensajes->mensaje = $totalmensajesnoleidos;
        }else{
            $mensajes->mensaje = 0;
        }
        Zend_Registry::set('mensaje', $mensajes);



    }


    public function _initRouter()
    {

        $frontController = Zend_Controller_Front::getInstance();
        $config = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini');
        $router = $frontController->getRouter();
        $router->addConfig($config, 'routes');


    }


}


