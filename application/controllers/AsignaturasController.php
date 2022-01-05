<?php

class AsignaturasController extends Zend_Controller_Action
{

    public function init()
    {
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();
    }

    public function indexAction()
    {
        //creo objeto que maneja la tabla Asignaturas
        $table = new Application_Model_DbTable_Nivel();
        $this->view->dato = $table->listar();
    }

    public function asignaturasAction()
    {
        $idnivel = $this->_getParam('id', 0);

        $table = new Application_Model_DbTable_Asignaturas();
        $datos = $table->listar($idnivel);

        $this->view->title = $datos[0]['nombreNiveles'];

        $this->view->dato = $datos;
    }

    public function agregarAction()
    {

        $idnivel = $this->_getParam('id', 0);
        $asignaturas = new Application_Model_DbTable_Asignaturas();
        $datos = $asignaturas->ultimo($idnivel);
        $this->view->title = "Agregar Asignatura";
        $this->view->headTitle($this->view->title);
        $form = new Application_Form_Asignaturas(array('params' => $idnivel));

        if ($idnivel == 14 || $idnivel == 15) {

            $form->addElement('hidden', 'idNiveles');
            $form->addElement('Text', 'orden', array('order' => 3));
            $form->orden->setLabel('Orden: ')->setRequired(true);
            $form->orden->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));

            $form->idNiveles->setValue($idnivel);
            $form->orden->setValue($datos[0]['max'] + 1);
            $form->orden->setAttrib('readonly', 'readonly');

            $form->submit->setLabel('Agregar Asignatura');

        } else {

            $form->addElement('hidden', 'idNiveles');
            $form->addElement('Text', 'orden', array('order' => 2));
            $form->orden->setLabel('Orden: ')->setRequired(true);
            $form->orden->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));
            $form->addElement('radio', 'promedio', array('order' => 3));
            $form->promedio->setLabel('Incide en Promedio: ')->setRequired(true);
            $form->promedio->addMultiOptions(array(1 => 'Si', 0 => 'No'));
            $form->promedio->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));


            $form->idNiveles->setValue($idnivel);
            $form->orden->setValue($datos[0]['max'] + 1);
            $form->orden->setAttrib('readonly', 'readonly');
            $form->submit->setLabel('Agregar Asignatura');
        }

        $this->view->form = $form;


        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {


                $nombre_asignatura = $form->getValue('nombreAsignatura');
                $nivel = $form->getValue('idNiveles');
                $nucleo = $form->getValue('idNucleo');
                $orden = $form->getValue('orden');
                $promedio = $form->getValue('promedio');

                $ultimoid = $asignaturas->ultimoid();
                $idasignatura = $ultimoid[0]['max'] + 1;

                $asignaturas = new Application_Model_DbTable_Asignaturas();

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                // Iniciamos la transaccion
                $db->beginTransaction();
                try {
                    if ($nivel == 14 || $nivel == 15) {
                        $asignaturas->agregarpre($idasignatura, $nombre_asignatura, $orden, $promedio, $nivel, $nucleo);

                    } else {
                        $asignaturas->agregar($idasignatura, $nombre_asignatura, $orden, $promedio, $nivel);
                    }



                    $db->commit();
                    $this->_helper->redirector('asignaturas', 'Asignaturas', null, array('id' => $nivel));
                } catch (Exception $e) {
                    // Si hubo problemas. Enviamos marcha atras
                    $db->rollBack();

                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al ingresar los datos, intente nuevamente');
                    /// Assign the messages
                    $this->view->assign('messages', $messages);
                }
            }
            else {
                $form->populate($formData);
            }
        }
    }

    public function editarAction()
    {

        $this->view->title = "Modificar Asignatura";
        $this->view->headTitle($this->view->title);
        $form = new Application_Form_Asignaturas();
        $form->addElement('hidden', 'idNiveles');

        $form->submit->setLabel('Modificar Asignatura');
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($form->isValid($formData)) {

                $idasignaturas = $form->getValue('idAsignatura');
                $nombre_asignatura = $form->getValue('nombreAsignatura');
                $nivel = $form->getValue('idNiveles');
                $asignatura = new Application_Model_DbTable_Asignaturas();
                $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                // Iniciamos la transaccion
                $db->beginTransaction();
                try {
                    $asignatura->cambiar($idasignaturas, $nombre_asignatura);
                    $db->commit();
                    $this->_helper->redirector('asignaturas', 'Asignaturas', null, array('id' => $nivel));

                } catch (Exception $e) {
                    // Si hubo problemas. Enviamos  marcha atras
                    $db->rollBack();
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al ingresar los datos, intente nuevamente');
                    $this->view->assign('messages', $messages);
                }

            }

            else {
                $form->populate($formData);
            }
        }

        else {

            $idAsignaturas = $this->_getParam('id', 0);
            if ($idAsignaturas > 0) {
                $fasignatura = new Application_Model_DbTable_Asignaturas();
                $fasignaturas = $fasignatura->get($idAsignaturas);

                $nombre = $fasignaturas['nombreAsignatura'];
                $length = strlen(utf8_decode($nombre));
                $form->nombreAsignatura->setAttrib('size', $length + 2);
                $form->populate($fasignaturas);
            }
        }
    }

    public function eliminarAction()
    {

        $idasignatura = $this->_getParam('idAsignaturas', 0);
        $tabla = new Application_Model_DbTable_Asignaturas();
        $tabla->borrar($idasignatura);
        $this->_helper->redirector('index');
    }


    public function guardaasignaturaAction()
    {


        if ($this->getRequest()->isXmlHttpRequest()) {

            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();
            $json = file_get_contents('php://input');

            $data = json_decode($json, true);
            $tabla = new Application_Model_DbTable_Asignaturas();
            $db = Zend_Db_Table_Abstract::getDefaultAdapter();

            // Iniciamos la transaccion
            $db->beginTransaction();
            try {
                for ($i = 0; $i < count($data); $i++) {
                    $tabla->cambiarestadoorden($data[$i]['idasignatura'], $i + 1, $data[$i]['check'], $data[$i]['mostrar']);
                }

                $db->commit();
                echo Zend_Json::encode(array('redirect' => '/Asignaturas/asignaturas/id/' . $data[0]['n']));

            } catch (Exception $e) {

                // Si hubo problemas. Enviamos marcha atras
                $db->rollBack();
                echo Zend_Json::encode(array('response' => 'error'));


            }

        }

    }

}






