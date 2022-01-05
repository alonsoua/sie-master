<?php

class SostenedorController extends Zend_Controller_Action
{

    public function init()
    {
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();
    }

    public function indexAction()
    {
        $table = new Application_Model_DbTable_Sostenedor();
        $this->view->dato = $table->listar();
    }

    public function agregarAction()
    {

        $this->view->title = "Agregar Sostenedor";
        $this->view->headTitle($this->view->title);
        $form = new Application_Form_Sostenedor();
        $form->submit->setLabel('Agregar Sostenedor');
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $rutsostenedor = $form->getValue('rutSostenedor');
                $nombre = $form->getValue('nombreSostenedor');
                $direccion = $form->getValue('direccion');
                $telefono = $form->getValue('telefono');
                $comuna = $form->getValue('comuna');
                $correo = $form->getValue('correo');
                $sostenedor = new Application_Model_DbTable_Sostenedor();

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                // Iniciamos la transaccion
                $db->beginTransaction();
                try {

                    $sostenedor->agregar($rutsostenedor, $nombre, $direccion, $telefono, $comuna, $correo);
                    $db->commit();
                    $this->_helper->redirector('index');

                } catch (Exception $e) {
                    // Si hubo problemas. Enviamos  marcha atras
                    $db->rollBack();

                }
            } else {

                $comuna = new Application_Model_DbTable_Comuna();
                $rowset = $comuna->getAsKeyValue($this->_request->getParam('provincia'));
                $provincia = new Application_Model_DbTable_Provincia();
                $rowsetprovincia = $provincia->getAsKeyValue($this->_request->getParam('region'));
                $form->provincia->clearMultiOptions();
                $form->provincia->addMultiOptions($rowsetprovincia);
                $form->comuna->clearMultiOptions();
                $form->comuna->addMultiOptions($rowset);
                $this->view->form = $form;
                $form->populate($formData);
            }
        }
    }

    public function editarAction()
    {

        $this->view->title = "Modificar Sostenedor";
        $this->view->headTitle($this->view->title);
        $form = new Application_Form_Sostenedor();

        $form->submit->setLabel('Modificar Sostenedor');
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $idsostenedor = $form->getValue('idSostenedor');
                $rutsostenedor = $form->getValue('rutSostenedor');
                $nombre = $form->getValue('nombreSostenedor');
                $direccion = $form->getValue('direccion');
                $telefono = $form->getValue('telefono');
                $comuna = $form->getValue('comuna');
                $correo = $form->getValue('correo');
                $sostenedor = new Application_Model_DbTable_Sostenedor();

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                // Iniciamos la transaccion
                $db->beginTransaction();
                try {

                    $sostenedor->cambiar($idsostenedor, $rutsostenedor, $nombre, $direccion, $telefono, $comuna, $correo);
                    $db->commit();
                    $this->_helper->redirector('index');

                } catch (Exception $e) {
                    // Si hubo problemas. Enviamos marcha atras
                    $db->rollBack();
                    echo $e;
                }

            } else {
                $comuna = new Application_Model_DbTable_Comuna();
                $rowset = $comuna->getAsKeyValue($this->_request->getParam('provincia'));
                $form->comuna->clearMultiOptions();
                $form->comuna->addMultiOptions($rowset);
                $this->view->form = $form;
                $form->populate($formData);
            }
        } else {

            $rutsostenedor = $this->_getParam('idSostenedor', 0);
            if ($rutsostenedor > 0) {

                $fsostenedor = new Application_Model_DbTable_Sostenedor();
                $fsostenedores = $fsostenedor->get($rutsostenedor);
                $comuna = new Application_Model_DbTable_Comuna();
                $rowset = $comuna->getcomuna($fsostenedores['comuna']);

                $rowsetcom = $comuna->getAsKeyValue($rowset[0][idProvincia]);
                $provincia = new Application_Model_DbTable_Provincia();
                $rowsetprovincia = $provincia->getAsKeyValue($rowset[0][idRegion]);

                $form->provincia->clearMultiOptions();
                $form->provincia->addMultiOptions($rowsetprovincia);
                $form->provincia->setValue($rowset[0][idProvincia]);
                $form->comuna->clearMultiOptions();
                $form->comuna->addMultiOptions($rowsetcom);
                $form->region->setValue($rowset[0][idRegion]);


                $this->view->form = $form;

                $form->populate($fsostenedores);

            }
        }
    }

    public function eliminarAction()
    {

        $rutsostenedor = $this->_getParam('idSostenedor', 0);
        $tabla = new Application_Model_DbTable_Sostenedor();

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $tabla->borrar($rutsostenedor);
            $db->commit();
            $this->_helper->redirector('index');
        } catch (Exception $e) {
            // Si hubo problemas. Enviamos marcha atras
            $db->rollBack();
            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No se puede eliminar este registro, posee datos asociados');

            $this->view->assign('messages', $messages);
        }
    }


    public function getprovinciaAction()
    {
        $modelModelo = new Application_Model_DbTable_Provincia();
        $results = $modelModelo->getAsKeyValueJSON($this->_getParam('id'));
        $this->_helper->json($results);
    }

    public function getcomunaAction()
    {
        $modelModelo = new Application_Model_DbTable_Comuna();
        $results = $modelModelo->getAsKeyValueJSON($this->_getParam('id'));
        $this->_helper->json($results);
    }

}







