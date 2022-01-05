<?php

class EstablecimientoController extends Zend_Controller_Action
{

    public function init()
    {
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();
    }

    public function indexAction()
    {

        $establecimiento = new Zend_Session_NameSpace("establecimiento");
        $id = $establecimiento->establecimiento;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $table = new Application_Model_DbTable_Establecimiento();
        $cargo = new Zend_Session_NameSpace("cargo");
        if ($cargo->cargo == '1') {
            $result = $table->listar();
        }

        if ($cargo->cargo == '3' || $cargo->cargo == '4' || $cargo->cargo == '6') {
            $result = $table->listarestablecimientorol($id, $idperiodo);
        }


        $this->view->dato = $result;

    }

    public function editarAction()
    {
        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;
        if ($rol == '1' || $rol == '3') {

            $this->view->title = "Modificar Establecimiento";
            $this->view->headTitle($this->view->title);
            $form = new Application_Form_Establecimiento();
            $form->rbd->setAttrib('readonly', 'true');
            $form->submit->setLabel('Modificar Establecimiento');
            $this->view->form = $form;


            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {
                    $id = $form->getValue('idEstablecimiento');
                    $RBD = $form->getValue('rbd');
                    $sostenedor = $form->getValue('idSostenedor');

                    $Nombre = $form->getValue('nombreEstablecimiento');
                    $dependencia = $form->getValue('dependencia');
                    $comuna = $form->getValue('comuna');
                    $direccion = $form->getValue('direccion');
                    $telefono = $form->getValue('telefono');
                    $correo = $form->getValue('correo');
                    $nombresub = $form->getValue('subvencion');
                    $matricula = $form->getValue('matricula');

                    $concentracion = $form->getValue('concentracion');
                    $calificacion = $form->getValue('calificacion');
                    $matriculapie = $form->getValue('matriculapie');
                    $tipo = $form->getValue('tipoEvaluacion');

                    $establecimiento = new Application_Model_DbTable_Establecimiento();

                    try {

                        $originalFilename = pathinfo($form->logo->getFileName());
                        if ($originalFilename == null) {
                            $newFilename = '0';

                        } else {
                            $newFilename = 'logo-' . uniqid() . '.' . $originalFilename['extension'];
                        }


                        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                        // Iniciamos la transaccion
                        $db->beginTransaction();
                        try {

                            $establecimiento->cambiar($id, $RBD, $Nombre, $dependencia, $comuna, $direccion, $telefono, $correo, $newFilename, $sostenedor, $tipo, $nombresub, $matricula, $concentracion, $calificacion, $matriculapie);
                            $upload = $form->logo->getTransferAdapter();

                            $form->logo->addFilter('Rename', $newFilename);

                            $upload = $form->logo->receive();
                            if ($upload) {
                                $db->commit();
                                $this->_helper->redirector('index');
                            } else {
                                $db->rollBack();
                                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Ha ocurido un error, al ingresar los datos, intente nuevamente' . $upload);

                                $this->view->assign('messages', $messages);
                            }

                        } catch (Exception $e) {
                            // Si hubo problemas. Enviamos  marcha atras
                            $db->rollBack();
                            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Ha ocurido un error, al ingresar los datos, intente nuevamente' . $e);
                            $this->view->assign('messages', $messages);
                        }

                    } catch (Exception $e) {
                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Ha ocurido un error, revise los datos');
                        $this->view->assign('messages', $messages);

                    }

                } else {
                    $form->populate($formData);
                }
            } else {

                $RBD = $this->_getParam('id', 0);
                if ($RBD > 0) {
                    $establecimiento = new Application_Model_DbTable_Establecimiento();
                    $establecimientos = $establecimiento->get($RBD);

                    if (empty($establecimientos)) {
                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Establecimiento no existe');
                        $this->view->assign('messages', $messages);
                    } else {

                        $this->view->form = $form;
                        $form->populate($establecimientos[0]);
                    }
                }
            }
        }
    }

    public function eliminarAction()
    {
        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;
        if ($rol == '1' || $rol == '3') {

            $RBD = $this->_getParam('id', 0);
            $tabla = new Application_Model_DbTable_Establecimiento();
            $tabla->borrar($RBD);
            $this->_helper->redirector('index');
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

    public function agregarAction()
    {
        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;
        if ($rol == '1' || $rol == '3') {

            $this->view->title = "Agregar Establecimiento";
            $this->view->headTitle($this->view->title);

            $form = new Application_Form_Establecimiento();
            $this->view->form = $form;

            if ($this->getRequest()->isPost()) {

                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {
                    $RBD = $form->getValue('rbd');
                    $sostenedor = $form->getValue('idSostenedor');
                    $Nombre = $form->getValue('nombreEstablecimiento');
                    $dependencia = $form->getValue('dependencia');
                    $comuna = $form->getValue('comuna');
                    $direccion = $form->getValue('direccion');
                    $telefono = $form->getValue('telefono');
                    $correo = $form->getValue('correo');
                    $nombresub = $form->getValue('subvencion');
                    $matricula = $form->getValue('matricula');

                    $concentracion = $form->getValue('concentracion');
                    $calificacion = $form->getValue('calificacion');
                    $matriculapie = $form->getValue('matriculapie');
                    $tipo = $form->getValue('tipoEvaluacion');

                    $establecimiento = new Application_Model_DbTable_Establecimiento();

                    $respuesta = $establecimiento->validar($RBD);
                    if ($respuesta == true) {

                        try {

                            $originalFilename = pathinfo($form->logo->getFileName());
                            if ($originalFilename == null) {
                                $newFilename = '0';

                            } else {
                                $newFilename = 'logo-' . uniqid() . '.' . $originalFilename['extension'];
                            }

                            $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                            // Iniciamos la transaccion
                            $db->beginTransaction();
                            try {

                                $establecimiento->agregar($RBD, $Nombre, $dependencia, $comuna, $direccion, $telefono, $correo, $newFilename, $sostenedor, $tipo, $nombresub, $matricula, $concentracion, $calificacion, $matriculapie);
                                $upload = $form->logo->getTransferAdapter();

                                $form->logo->addFilter('Rename', $newFilename);

                                $upload = $form->logo->receive();
                                if ($upload) {
                                    $db->commit();
                                    $this->_helper->redirector('index');
                                } else {
                                    $db->rollBack();
                                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Ha ocurido un error, al ingresar los datos, intente nuevamente' . $upload);

                                    $this->view->assign('messages', $messages);
                                }

                            } catch (Exception $e) {
                                // Si hubo problemas. Enviamos marcha atras
                                $db->rollBack();
                                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Ha ocurido un error, al ingresar los datos, intente nuevamente' . $e);
                                $this->view->assign('messages', $messages);
                            }

                        } catch (Exception $e) {
                            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Ha ocurido un error, revise los datos');
                            $this->view->assign('messages', $messages);
                            echo $e;
                        }


                    } else {

                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('El RBD que intenta ingresar, ya existe');
                        $this->view->assign('messages', $messages);

                    }
                } else {
                    $form->populate($formData);

                }
            }
        }
    }

    public function configAction()
    {
        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;
        if ($rol == '1' || $rol == '3' || $rol =='6') {

            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;

            $this->view->title = "Configuración Establecimiento";
            $this->view->headTitle($this->view->title);
            $form = new Application_Form_configEstablecimiento();
            $form->setDecorators(
                array(
                    'FormElements',
                    'Form',

                )
            );
            $establecimiento = new Application_Model_DbTable_Establecimiento();
            $this->view->form = $form;

            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {
                    $id = $form->getValue('idConfiguracion');
                    $idestablecimiento = $form->getValue('idEstablecimiento');
                    $tipomodalidad=$form->getValue('tipoModalidad');
                    $numero = $form->getValue('numeroDecreto');
                    $year = $form->getValue('yeardecreto');
                    $aproxasignatura = $form->getValue('aproxAsignatura');
                    $aproxperiodo = $form->getValue('aproxPeriodo');
                    $aproxanual = $form->getValue('aproxAnual');
                    $aproxfinal = $form->getValue('aproxFinal');
                    $examen= $form->getValue('examen');
                    $aproxexamen= $form->getValue('aproxExamen');

                    $monitoreos = $form->getValue('monitoreo');

                    $profesorparcial=$form->getValue('profesorParcial');
                    $apoderadoparcial=$form->getValue('apoderadoParcial');
                    $directorparcial=$form->getValue('directorParcial');

                    $profesorperiodo=$form->getValue('profesorPeriodo');
                    $apoderadoperiodo=$form->getValue('apoderadoPeriodo');
                    $directorperiodo=$form->getValue('directorPeriodo');
                    $mostrarprimero=$form->getValue('primerPeriodo');
                    $mostrarsegundo=$form->getValue('segundoPeriodo');

                    $profesoranual=$form->getValue('profesorAnual');
                    $apoderadoanual=$form->getValue('apoderadoAnual');
                    $directoranual=$form->getValue('directorAnual');

                    $decimas=$form->getValue('decimas');

                    $profesorranking=$form->getValue('profesorRanking');
                    $directorranking=$form->getValue('directorRanking');

                    //APP


                    $activarapp=$form->getValue('activarapp');

                    $dataprofesor=array($profesorparcial,$profesorperiodo,$profesoranual,$profesorranking);
                    $dataapoderado=array($apoderadoparcial,$apoderadoperiodo,$apoderadoanual);
                    $datadirector=array($directorparcial,$directorperiodo,$directoranual,$directorranking);
                    $datamostrar=array($mostrarprimero,$mostrarsegundo);

                    $listaprofesor = serialize($dataprofesor);
                    $listaapoderado = serialize($dataapoderado);
                    $listadirector = serialize($datadirector);
                    $listamostrar = serialize($datamostrar);

                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                    // Iniciamos la transaccion
                    $db->beginTransaction();
                    try {
                        if ($idestablecimiento > 0) {
                            $validar = $establecimiento->getconfig($idestablecimiento, $idperiodo);
                        } else {
                            $idestablecimiento = $this->_getParam('id', 0);
                            $validar = false;
                        }

                        if ($validar) {
                            $establecimiento->cambiarconfig($id,$tipomodalidad, $numero, $year, $aproxasignatura, $aproxperiodo, $aproxanual, $aproxfinal,$examen,$aproxexamen, $monitoreos,$listaprofesor,$listaapoderado,$listadirector,$listamostrar,$decimas,$activarapp);
                        } else {
                            $establecimiento->agregarconfig($tipomodalidad,$numero, $year, $aproxasignatura, $aproxperiodo, $aproxanual, $aproxfinal, $examen,$aproxexamen, $monitoreos, $idperiodo, $idestablecimiento,$listaprofesor,$listaapoderado,$listadirector,$listamostrar,$decimas,$activarapp);
                        }

                        $db->commit();
                        $this->_helper->redirector('index');

                    } catch (Exception $e) {
                        // Si hubo problemas. Enviamos  marcha atras
                        $db->rollBack();
                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Ha ocurido un error, al ingresar los datos, intente nuevamente' . $e);
                        $this->view->assign('messages', $messages);
                    }

                } else {
                    $form->populate($formData);
                }
            } else {
                $id = $this->_getParam('id', 0);
                if ($id > 0) {
                    $establecimientos = $establecimiento->getconfig($id, $idperiodo);


                    $form = new Application_Form_configEstablecimiento(array('params' => $establecimientos[0]['tipoEvaluacion']));

                    if (empty($establecimientos)) {
                        $this->view->form = $form;
                    } else {


                        if(!empty($establecimientos[0]['profesor']) || $establecimientos[0]['profesor']>0){
                            $valoresp=unserialize($establecimientos[0]['profesor']);
                            //Profesor
                            $form->profesorParcial->setValue($valoresp[0]);
                            $form->profesorPeriodo->setValue($valoresp[1]);
                            $form->profesorAnual->setValue($valoresp[2]);
                        }

                        if(!empty($establecimientos[0]['apoderado']) ||  $establecimientos[0]['apoderado']>0){
                            $valoresa=unserialize($establecimientos[0]['apoderado']);
                            //Apoderado
                            $form->apoderadoParcial->setValue($valoresa[0]);
                            $form->apoderadoPeriodo->setValue($valoresa[1]);
                            $form->apoderadoAnual->setValue($valoresa[2]);
                        }
                        if(!empty($establecimientos[0]['director']) || $establecimientos[0]['director']>0){
                            $valoresd=unserialize($establecimientos[0]['director']);
                            //Director
                            $form->directorParcial->setValue($valoresd[0]);
                            $form->directorPeriodo->setValue($valoresd[1]);
                            $form->directorAnual->setValue($valoresd[2]);
                        }

                        if(!empty($establecimientos[0]['mostrarPromedio']) || $establecimientos[0]['mostrarPromedio']>0){
                            $valoresm=unserialize($establecimientos[0]['mostrarPromedio']);
                            //Mostrar Promedio General Semestre
                            $form->primerPeriodo->setValue($valoresm[0]);
                            $form->segundoPeriodo->setValue($valoresm[1]);

                        }

                        $this->view->form = $form;
                        $form->populate($establecimientos[0]);
                    }
                }
            }
        }
    }

    public function configdecretoAction()
    {
        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        if ($rol == '1' || $rol == '3') {

            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;

            $this->view->title = "Configuración Decretos";
            $this->view->headTitle($this->view->title);
            $idest = $this->_getParam('id', 0);
            $this->view->esta = $idest;
            $form = new Application_Form_Decretos(array('params' => $idest));
            $form->setDecorators(
                array(
                    'FormElements',
                    'Form',

                )
            );
            $this->view->form = $form;

            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {
                    $modeloestablecimiento = new Application_Model_DbTable_Establecimiento();
                    $modelocurso = new Application_Model_DbTable_Cursos();
                    $id = $form->getValue('idDecreto');
                    $numero = $form->getValue('numeroDecreto');
                    $year = $form->getValue('yeardecreto');
                    $idcurso = $form->getValue('idCursos');


                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                    // Iniciamos la transaccion
                    $db->beginTransaction();
                    try {

                        $modeloestablecimiento->agregardecreto($numero, $year, $idest, $idperiodo);
                        $iddecreto = $modeloestablecimiento->getAdapter()->lastInsertId();
                        $largocurso = count($idcurso);
                        if ($largocurso > 0) {
                            for ($i = 0; $i < $largocurso; $i++) {
                                $modelocurso->cambiardecreto($idcurso[$i], $iddecreto);
                            }

                        }

                        $db->commit();
                        $this->_helper->redirector('index');

                    } catch (Exception $e) {
                        // Si hubo problemas. Enviamos  marcha atras
                        $db->rollBack();
                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Ha ocurido un error, al ingresar los datos, intente nuevamente' . $e);
                        $this->view->assign('messages', $messages);
                    }

                } else {
                    $form->populate($formData);
                }
            }
        }
    }

    public function editardecretoAction()
    {
        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;
        $modeloestablecimiento = new Application_Model_DbTable_Establecimiento();
        $modelocurso = new Application_Model_DbTable_Cursos();

        if ($rol == '1' || $rol == '3') {

            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;

            $this->view->title = "Editar Decretos";
            $this->view->headTitle($this->view->title);
            $idest = $this->_getParam('id', 0);
            $this->view->esta = $idest;
            $form = new Application_Form_Decretos(array('params' => $idest));
            $form->setDecorators(
                array(
                    'FormElements',
                    'Form',

                )
            );
            $this->view->form = $form;

            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {

                    $id = $form->getValue('idDecreto');
                    $numero = $form->getValue('numeroDecreto');
                    $year = $form->getValue('yearDecreto');
                    $numeroplan=$form->getValue('numeroPlan');
                    $yearplan=$form->getValue('yearPlan');
                    $idcurso = $form->getValue('idCursos');

                    //Obtenemos la lista de cursos guardados
                    $listacursos=$modelocurso->getcursodecretoid($id);
                    //Zend_Debug::dump($idcurso);


                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                    // Iniciamos la transaccion
                    $db->beginTransaction();
//                    try {
//
//                        $modeloestablecimiento->agregardecreto($numero, $year, $idest, $idperiodo);
//                        $iddecreto = $modeloestablecimiento->getAdapter()->lastInsertId();
//                        $largocurso = count($idcurso);
//                        if ($largocurso > 0) {
//                            for ($i = 0; $i < $largocurso; $i++) {
//                                $modelocurso->cambiardecreto($idcurso[$i], $iddecreto);
//                            }
//
//                        }
//
//                        $db->commit();
//                        $this->_helper->redirector('index');
//
//                    } catch (Exception $e) {
//                        // Si hubo problemas. Enviamos  marcha atras
//                        $db->rollBack();
//                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Ha ocurido un error, al ingresar los datos, intente nuevamente' . $e);
//                        $this->view->assign('messages', $messages);
//                    }

                } else {
                    $form->populate($formData);
                }
            } else {

                $id = $this->_getParam('idd', 0);
                if ($id > 0) {

                    $decretos= $modeloestablecimiento->getdecreto($id, $idperiodo);
                    $listacursos=$modelocurso->getcursodecretoid($decretos[0]['idDecreto']);

                   foreach ($listacursos as $row) {
                        $form->idCursos->addMultiOption($row['idCursos'], $row['nombreGrado'].' '.$row['letra']);
                       $decretos[0]['idCursos'][] = $row['idCursos'];
                   }

                    $form->populate($decretos[0]);
                    $this->view->form = $form;


                }

            }
        }
    }

    public function decretosAction()
    {

        $idest = $this->_getParam('id', 0);

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $table = new Application_Model_DbTable_Establecimiento();
        $cargo = new Zend_Session_NameSpace("cargo");
        if ($cargo->cargo == '1') {
            $result = $table->listardecreto($idperiodo);
        }

        if ($cargo->cargo == '3' || $cargo->cargo == '4' || $cargo->cargo == '6') {
            $result = $table->listardecretoestablecimiento($idest, $idperiodo);
        }


        $this->view->dato = $result;


    }

    public function agregardirectorAction()
    {

        $this->view->title = "Agregar Director";
        $this->view->headTitle($this->view->title);
        $form = new Application_Form_Director();
        $form->submit->setLabel('Agregar Director');
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                $iddirector = $form->getValue('idDirector');
                $idest = $form->getValue('idEstablecimiento');
                $curso = new Application_Model_DbTable_Establecimiento();

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $db->beginTransaction();

                try {
                    $curso->actualizardirector($iddirector, $idest);


                    $db->commit();
                    $this->_helper->redirector('index');
                } catch (Exception $e) {
                    // Si hubo problemas. Enviamos todo marcha atras
                    $db->rollBack();
                    echo $e;
                }
            } else {
                $form->populate($formData);
            }

        } else {
            $idest = $this->_getParam('id', 0);
            if ($idest > 0) {

                $cuenta = new Application_Model_DbTable_Cuentas();
                $cuentas = $cuenta->listardirector($idest);

                $form->idEstablecimiento->setValue($idest);
                $form->idDirector->clearMultiOptions();
                $form->idDirector->addMultiOption('Null', 'Seleccione Director');
                $form->idDirector->addMultiOptions($cuentas);
                $this->view->form = $form;

            }
        }
    }
}
