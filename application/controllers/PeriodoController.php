<?php


class PeriodoController extends Zend_Controller_Action
{
    const mensaje = 'Hubo un problema al ingresar los datos, intente nuevamente';

    public function init()
    {
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();
    }

    public function indexAction()
    {

        $table = new Application_Model_DbTable_Periodo();
        $this->view->dato = $table->listar();


    }

    public function agregarAction()
    {
        $this->view->title = "Agregar Periodo";
        $this->view->headTitle($this->view->title);
        $form = new Application_Form_Periodo();
        $form->submit->setLabel('Agregar Periodo');
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                try {
                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                    // Iniciamos la transaccion
                    $db->beginTransaction();
                    $periodo = new Application_Model_DbTable_Periodo();
                    $modelocurso = new Application_Model_DbTable_Cursos();
                    $modeloasignatura = new Application_Model_DbTable_Asignaturas();
                    $modeloasignaturas = new Application_Model_DbTable_Asignaturascursos();
                    $modelousuarios = new Application_Model_DbTable_Cuentas();
                    $modeloestablecimiento = new Application_Model_DbTable_Establecimiento();

                    $fecha = $form->getValue('nombrePeriodo');
                    $validar = $periodo->getname($fecha);

                    if ($validar) {
                        $db->rollBack();
                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('El Periodo ya existe');
                        $this->view->assign('messages', $messages);
                    } else {

                        $periodo->agregar($fecha);
                        $fecha = $fecha - 1;
                        $idperiodo = $periodo->getAdapter()->lastInsertId();

                        //obtenemos el periodo anterior
                        $periodoanterior = $periodo->getname($fecha);
                        //si Existe un periodo anterior creamos los cursos y las cuentas de usuario.
                        if ($periodoanterior) {
                            $cuenta = 0;
                            $idperiodoanterior = $periodoanterior[0]['idPeriodo'];

                            //Obtenemos los cursos
                            $cursos = $modelocurso->listacursos($idperiodoanterior);
                            for ($i = 0; $i < count($cursos); $i++) {

                                //Agregamos el curso
                                $modelocurso->agregaractual($cursos[$i]['idEstablecimiento'], $idperiodo, $cursos[$i]['idCodigoTipo'], $cursos[$i]['idCodigoGrado'], $cursos[$i]['letra'], $cuenta, $cursos[$i]['combinado'], $cursos[$i]['numeroCurso'], $cursos[$i]['tipoJornada'], $cursos[$i]['codigoSector'], $cursos[$i]['codigoEspecialidad'], $cursos[$i]['codigoAlternativa'], $cursos[$i]['infraestructura']);
                                $idcurso = $modelocurso->getAdapter()->lastInsertId();


                                //Obtenemos las Asignaturas
                                $asignaturas = $modeloasignaturas->listarasignaturacurso($cursos[$i]['idCursos']);
                                //Agregamos las asignaturas correspondientes
                                for ($j = 0; $j < count($asignaturas); $j++) {
                                    $ultimoid = $modeloasignatura->ultimoid();
                                    $idasignatura = $ultimoid[0]['max'] + 1;
                                    $modeloasignatura->agregar($idasignatura, $asignaturas[$j]['nombreAsignatura'], $i + 1, $asignaturas[$j]['promedio'], $asignaturas[$j]['idNucleo']);
                                    $idasignaturacurso = $modeloasignatura->ultimoid();
                                    $modeloasignaturas->agregar($idasignaturacurso[0]['max'], $asignaturas[$j]['tipoAsignatura'], $j + 1, $asignaturas[$j]['promedio'], $asignaturas[$j]['idNucleo'], $idperiodo, $idcurso);


                                }

                            }

                            //Agregamos las Cuentas de usuario al nuevo Periodo

                            $listaroles = $modelousuarios->listaderoles($idperiodoanterior);
                            for ($i = 0; $i < count($listaroles); $i++) {

                                $modelousuarios->agregarrol($listaroles[$i]['idEstablecimiento'], $listaroles[$i]['idRol'], $idperiodo, $listaroles[$i]['idCuenta']);
                            }

                            //Agregamos las configuraciones de los establecimeintos.

                            $listaconfiguracion = $modeloestablecimiento->listaconfiguracion($idperiodoanterior);

                            for ($i = 0; $i < count($listaconfiguracion); $i++) {
                                $modeloestablecimiento->agregarconfig($listaconfiguracion[$i]['numeroDecreto'], $listaconfiguracion[$i]['yeardecreto'], $listaconfiguracion[$i]['aproxAsignatura'], $listaconfiguracion[$i]['aproxPeriodo'], $listaconfiguracion[$i]['aproxAnual'], $listaconfiguracion[$i]['aproxFinal'], $listaconfiguracion[$i]['examen'], $listaconfiguracion[$i]['aproxExamen'], $listaconfiguracion[$i]['ingresonota'], $idperiodo, $listaconfiguracion[$i]['idEstablecimiento']);

                            }


                        } else { // si no existe un periodo anterior, solo creamos el periodo
                            $periodo->agregar($fecha);
                        }

                        $db->commit();
                        $this->_helper->redirector('index');
                    }


                } catch (Exception $e) {
                    $db->rollBack();
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage(self::mensaje);
                    $this->view->assign('messages', $messages);
                }


            } else {

                $form->populate($formData);
            }
        }
    }

    public function editarAction()
    {

        $this->view->title = "Modificar Periodo";
        $this->view->headTitle($this->view->title);
        $form = new Application_Form_Periodo();

        $form->submit->setLabel('Modificar Periodo');
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                $idPeriodo = $form->getValue('idPeriodo');
                $fecha = $form->getValue('nombrePeriodo');

                $Tperiodo = new Application_Model_DbTable_Periodo();
                $Tperiodo->cambiar($idPeriodo, $fecha);
                $this->_helper->redirector('index');
            } else {
                $form->populate($formData);
            }
        } else {

            $idPeriodo = $this->_getParam('id', 0);
            if ($idPeriodo > 0) {

                $Tperiodo = new Application_Model_DbTable_Periodo();
                $Tperiodo2 = $Tperiodo->get($idPeriodo);
                $form->populate($Tperiodo2);
            }
        }
    }

    public function eliminarAction()
    {


        $this->_helper->redirector('index');
    }

    //Calendario


    public function calendarioAction()
    {
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $modeloperiodo = new Application_Model_DbTable_Periodo();

        if ($rol == 1) {
            $this->view->dato = $modeloperiodo->listarcalendario($idperiodo, 1, null);

        } elseif ($rol == 3 || $rol == 6) {

            $est = new Zend_Session_Namespace('establecimiento');
            $establecimiento = $est->establecimiento;
            $this->view->dato = $modeloperiodo->listarcalendario($idperiodo, 1, $establecimiento);

        }


    }

    public function calendariocursoAction()
    {

        $id = $this->_getparam('id', 0);
        if ($id > 0) {

            $cargo = new Zend_Session_Namespace('cargo');
            $rol = $cargo->cargo;

            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;

            $modeloperiodo = new Application_Model_DbTable_Periodo();

            if ($rol == 1) {
                $this->view->dato = $modeloperiodo->listarcalendario($idperiodo, 2, $id);
                $this->view->idCalendarioEstablecimiento = $id;
            } elseif ($rol == 3 || $rol == 6) {
                $est = new Zend_Session_Namespace('establecimiento');
                $establecimiento = $est->establecimiento;

                $this->view->dato = $modeloperiodo->listarcalendario($idperiodo, 2, $establecimiento);
                $this->view->idCalendarioEstablecimiento = $id;
            } else {
                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage(self::mensaje);
                $this->view->assign('messages', $messages);
            }


        } else {
            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage(self::mensaje);
            $this->view->assign('messages', $messages);
        }


    }


    public function agregarcalendarioAction()
    {
        $form = new Application_Form_Calendario();
        $form->submit->setLabel('Agregar');
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                try {
                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                    $db->beginTransaction();
                    $modeloperiodo = new Application_Model_DbTable_Periodo();

                    $periodo = new Zend_Session_Namespace('periodo');
                    $idperiodo = $periodo->periodo;

                    $idestablecimiento = $form->getValue('idEstablecimiento');
                    $codigo = $form->getValue('codigoCalendario');
                    $descripcion = $form->getValue('descripcionCalendario');
                    $fechainicio = $form->getValue('fechaInicioClase');
                    $fechatermino = $form->getValue('fechaTerminoClase');
                    $fechainicio = date("Y-m-d", strtotime($fechainicio));
                    $fechatermino = date("Y-m-d", strtotime($fechatermino));


                    $valida = $modeloperiodo->validarcalendario($idestablecimiento, $idperiodo, null, 1);
                    if (!$valida) {
                        $modeloperiodo->agregarcalendario($idestablecimiento, $codigo, $descripcion, $fechainicio, $fechatermino, $idperiodo, null, 1);
                        //Obtenemos la lista de Cursos del establecimiento
                        $modelocurso = new Application_Model_DbTable_Cursos();
                        $listacurso = $modelocurso->listarcursoestablecimiento($idestablecimiento, $idperiodo);
                        for ($i = 0; $i < count($listacurso); $i++) {
                            $validacalendariocurso = $modeloperiodo->validarcalendario($idestablecimiento, $idperiodo, $listacurso[$i]['idCursos'], 2);
                            if (!$validacalendariocurso) {

                                $modeloperiodo->agregarcalendario($idestablecimiento, $codigo, $descripcion, $fechainicio, $fechatermino, $idperiodo, $listacurso[$i]['idCursos'], 2);
                            }

                        }

                        $db->commit();
                        $this->_helper->redirector('calendario');
                    } else {

                        $db->rollBack();
                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Ya Existe un Calendario en el periodo escolar');
                        $this->view->assign('messages', $messages);
                    }


                } catch (Exception $e) {
                    $db->rollBack();
                    echo $e;
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage(self::mensaje);
                    $this->view->assign('messages', $messages);
                }


            } else {

                $form->populate($formData);
            }
        }
    }

    public function editarcalendarioAction()
    {

        $form = new Application_Form_Calendario();
        $form->idEstablecimiento->setAttrib('disabled', 'true');
        $form->idEstablecimiento->setRequired(false);
        $form->submit->setLabel('Editar');
        $this->view->form = $form;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $modeloperiodo = new Application_Model_DbTable_Periodo();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                try {
                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                    $db->beginTransaction();

                    $periodo = new Zend_Session_Namespace('periodo');
                    $idperiodo = $periodo->periodo;

                    $idcalendario = $form->getValue('idCalendario');
                    $codigo = $form->getValue('codigoCalendario');
                    $descripcion = $form->getValue('descripcionCalendario');
                    $fechainicio = $form->getValue('fechaInicioClase');
                    $fechatermino = $form->getValue('fechaTerminoClase');

                    $fechainicio = date("Y-m-d", strtotime($fechainicio));
                    $fechatermino = date("Y-m-d", strtotime($fechatermino));

                    $modeloperiodo->actualizarcalendario($idcalendario, $codigo, $descripcion, $fechainicio, $fechatermino);

                    $db->commit();
                    $this->_helper->redirector('calendario');

                } catch (Exception $e) {
                    $db->rollBack();
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage(self::mensaje);
                    $this->view->assign('messages', $messages);
                }


            } else {

                $form->populate($formData);
            }
        } else {

            $idcalendario = $this->_getParam('id', 0);


            if ($idcalendario > 0) {
                $datos = $modeloperiodo->getcalendario($idcalendario, $idperiodo);

                if ($datos) {
                    $datos[0]['fechaInicioClase'] = date("d-m-Y", strtotime($datos[0]['fechaInicioClase']));
                    $datos[0]['fechaTerminoClase'] = date("d-m-Y", strtotime($datos[0]['fechaTerminoClase']));
                    $form->populate($datos[0]);

                } else {
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No Existe');
                    $this->view->assign('messages', $messages);
                }

            } else {
                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Error');
                $this->view->assign('messages', $messages);
            }


        }

    }

    public function agregarcalendariocursoAction()
    {

        $idcalendario = $this->_getParam('id', 0);

        if ($idcalendario > 0) {
            $activePage = $this->view->navigation()->findOneByLabel('Calendarios Cursos');
            $activePage->setparam('id', $idcalendario);

            $form = new Application_Form_Calendario(array('params' => $idcalendario));
            $form->submit->setLabel('Agregar');
            $this->view->form = $form;

            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {

                    try {
                        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                        $db->beginTransaction();
                        $modeloperiodo = new Application_Model_DbTable_Periodo();

                        $periodo = new Zend_Session_Namespace('periodo');
                        $idperiodo = $periodo->periodo;

                        $idcurso = $form->getValue('idCursos');
                        $codigo = $form->getValue('codigoCalendario');
                        $descripcion = $form->getValue('descripcionCalendario');
                        $fechainicio = $form->getValue('fechaInicioClase');
                        $fechatermino = $form->getValue('fechaTerminoClase');

                        $fechainicio = date("Y-m-d", strtotime($fechainicio));
                        $fechatermino = date("Y-m-d", strtotime($fechatermino));


                        $valida = $modeloperiodo->validarcalendario($idcalendario, $idperiodo, $idcurso, 2);
                        if (!$valida) {
                            $modeloperiodo->agregarcalendario($idcalendario, $codigo, $descripcion, $fechainicio, $fechatermino, $idperiodo, $idcurso, 2);
                            $db->commit();
                            $this->_helper->redirector('calendariocurso', 'Periodo', null, array('id' => $idcalendario));
                        } else {

                            $db->rollBack();
                            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Ya Existe un Calendario en el periodo escolar');
                            $this->view->assign('messages', $messages);
                        }


                    } catch (Exception $e) {
                        $db->rollBack();
                        echo $e;
                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage(self::mensaje);
                        $this->view->assign('messages', $messages);
                    }


                } else {

                    $form->populate($formData);
                }
            }

        } else {
            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Error');
            $this->view->assign('messages', $messages);
        }


    }


    public function eventoAction()
    {
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcalendario = $this->_getParam('id', 0);

        $table = new Application_Model_DbTable_Periodo();
        $datos = $table->listarcalendarioevento($idcalendario, $idperiodo);
        foreach ($datos as $d => $i) {

            $datos[$d]['fechaEvento'] = date("d-m-Y", strtotime($i['fechaEvento']));

        }
        $this->view->dato = $datos;

    }

    public function eventocursoAction()
    {

        $idcurso = $this->_getParam('idcu', 0);
        if ($idcurso > 0) {
            $modelocurso = new Application_Model_DbTable_Cursos();
            $nombre_curs = $modelocurso->getnombreactual($idcurso);

            $activePage = $this->view->navigation()->findOneByLabel('Calendarios Cursos');
            $activePage->setparam('id', $this->_getParam('idc', 0));
            $activePage = $this->view->navigation()->findOneBy('active', true);
            $activePage->setlabel("Eventos Curso " . $nombre_curs[0]['nombreGrado'] . ' ' . $nombre_curs[0]['letra']);

        }


        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcalendarioestablecimiento = $this->_getParam('idc', 0);
        $idcalendario = $this->_getParam('id', 0);

        $table = new Application_Model_DbTable_Periodo();
        $datos = $table->listarcalendarioeventocurso($idcalendario, $idperiodo);
        foreach ($datos as $d => $i) {

            $datos[$d]['fechaEvento'] = date("d-m-Y", strtotime($i['fechaEvento']));

        }
        $this->view->dato = $datos;
        $this->view->idCalendarioEstablecimiento = $idcalendarioestablecimiento;
        $this->view->idCalendario = $idcalendario;

    }


    public function agregareventoAction()
    {

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcalendario = $this->_getParam('id', 0);
        $this->view->id = $idcalendario;

        $modeloperiodo = new Application_Model_DbTable_Periodo();
        $datoscalendario = $modeloperiodo->getcalendario($idcalendario, $idperiodo);
        $datoscalendarioestablecimiento = $modeloperiodo->getcalendarioestablecimiento($datoscalendario[0]['idEstablecimiento'], $idperiodo);

        $form = new Application_Form_Evento();
        $form->submit->setLabel('Agregar');
        if ($datoscalendario[0]['tipoCalendario'] == 2) {

            $this->view->navigation()->removePages();
            $form->volver->setAttrib('onclick', 'window.location =\'' . $this->_request->getBaseUrl() . '/Periodo/eventocurso/id/' . $idcalendario . '/idcu/' . $datoscalendarioestablecimiento[0]['idCursos'] . '/idc/' . $datoscalendarioestablecimiento[0]['idEstablecimiento'] . '\'');
        }else{

            $activePage = $this->view->navigation()->findOneByLabel('Eventos Calendario');
            $activePage->setparam('id', $idcalendario);

        }
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                try {
                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                    $db->beginTransaction();

                    $nombre = $form->getValue('nombreEvento');
                    $fecha = $form->getValue('fechaEvento');
                    $tipo = $form->getValue('idTipoEvento');
                    $fecha = date("Y-m-d", strtotime($fecha));

                    $modeloperiodo->agregarevento($idcalendario, $nombre, $fecha, $tipo);
                    $db->commit();

                    if ($datoscalendario[0]['tipoCalendario'] == 1) {
                        $this->redirect('/Periodo/evento/id/' . $idcalendario);
                    } else {

                        $this->redirect('/Periodo/eventocurso/id/' . $idcalendario . '/idcu/' . $datoscalendarioestablecimiento[0]['idCursos'] . '/idc/' . $datoscalendarioestablecimiento[0]['idEstablecimiento']);
                    }

                } catch (Exception $e) {
                    $db->rollBack();
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage(self::mensaje);
                    $this->view->assign('messages', $messages);
                }


            } else {

                $form->populate($formData);
            }
        }
    }

    public function editareventoAction()
    {

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcalendario = $this->_getParam('id', 0);
        $idcurso = $this->_getParam('idcu', 0);
        $idest = $this->_getParam('idc', 0);
        $this->view->id = $idcalendario;

        $modeloperiodo = new Application_Model_DbTable_Periodo();
        $datoscalendario = $modeloperiodo->getcalendario($idcalendario, $idperiodo);
        $datoscalendarioestablecimiento = $modeloperiodo->getcalendarioestablecimiento($datoscalendario[0]['idEstablecimiento'], $idperiodo);


        $form = new Application_Form_Evento();
        $form->submit->setLabel('Actualizar');

        if ($datoscalendario[0]['tipoCalendario'] == 2) {

            $this->view->navigation()->removePages();
            $form->volver->setAttrib('onclick', 'window.location =\'' . $this->_request->getBaseUrl() . '/Periodo/eventocurso/id/' . $idcalendario . '/idcu/' . $idcurso . '/idc/' . $idest . '\'');
        }else{

            $activePage = $this->view->navigation()->findOneByLabel('Eventos Calendario');
            $activePage->setparam('id', $idcalendario);

        }

        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                try {
                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                    $db->beginTransaction();

                    $idevento = $form->getValue('idEvento');
                    $nombre = $form->getValue('nombreEvento');
                    $tipo = $form->getValue('idTipoEvento');
                    $fecha = $form->getValue('fechaEvento');
                    $fecha = date("Y-m-d", strtotime($fecha));

                    $modeloperiodo->actualizarevento($idevento, $nombre, $fecha, $tipo);
                    $db->commit();
                    if ($datoscalendario[0]['tipoCalendario'] == 1) {
                        $this->redirect('/Periodo/evento/id/' . $idcalendario);
                    } else {

                        $this->redirect('/Periodo/eventocurso/id/' . $idcalendario . '/idc/' . $datoscalendarioestablecimiento[0]['idCalendario']);
                    }

                } catch (Exception $e) {
                    $db->rollBack();
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage(self::mensaje);
                    $this->view->assign('messages', $messages);
                }


            } else {

                $form->populate($formData);
            }
        } else {

            $idevento = $this->_getParam('ide', 0);
            if ($idevento > 0) {
                $datos = $modeloperiodo->getevento($idevento);

                if ($datos) {
                    $datos[0]['fechaEvento'] = date("d-m-Y", strtotime($datos[0]['fechaEvento']));
                    $form->idTipoEvento->setValue($datos[0]['tipoEvento']);
                    $form->populate($datos[0]);

                } else {
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No Existe');
                    $this->view->assign('messages', $messages);
                }

            } else {
                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Error');
                $this->view->assign('messages', $messages);
            }


        }

    }

    public function eliminareventoAction()
    {


        $id = $this->_getParam('id', 0);
        $idcalendario = $this->_getParam('idc', 0);
        $idcalendarioestablecimiento = $this->_getParam('idce', 0);
        $tipo = $this->_getParam('t', 0);
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $db->beginTransaction();
        try {

            $modelo = new Application_Model_DbTable_Periodo();

            $modelo->eliminarevento($id);
            $db->commit();
            if ($tipo == 1) {
                $this->redirect('/Periodo/evento/id/' . $idcalendario);

            } elseif ($tipo == 2) {

                $this->redirect('/Periodo/eventocurso/id/' . $idcalendario . '/idc/' . $idcalendarioestablecimiento);
            } else {
                $db->rollBack();
            }

        } catch (Exception $e) {
            // Si hubo problemas. Enviamos  marcha atras
            $db->rollBack();

        }
    }


    public function getdiaseventosAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $modeloperiodo = new Application_Model_DbTable_Periodo();
        $idcalendario = $this->_getParam('id', 0);

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;


        if ($rol == 1 || $rol == 3 || $rol == 6) {

            $datoscalendario = $modeloperiodo->getcalendario($idcalendario, $idperiodo);
            $results[0] = $modeloperiodo->getfecha($idcalendario, $datoscalendario[0]['tipoCalendario']);
            if (!empty($results[0])) {
                //Si existe cambimos el formato de la fecha de inicio y termino del periodo escolar
                $results[0][0]['fechaInicioClase'] = date("d-m-Y", strtotime($results[0][0]['fechaInicioClase']));
                $results[0][0]['fechaTerminoClase'] = date("d-m-Y", strtotime($results[0][0]['fechaTerminoClase']));

            }

            if ($datoscalendario[0]['tipoCalendario'] == 1) {
                $results[1] = $modeloperiodo->getdiaseventos($idcalendario);
            } else {

                //Obtenemos los Datos del Calendario del Establecimiento
                $datoscaslendarioestablecimiento = $modeloperiodo->getcalendarioestablecimiento($datoscalendario[0]['idEstablecimiento'], $idperiodo);
                $results[1] = $modeloperiodo->getdiaseventoscurso(array($idcalendario, $datoscaslendarioestablecimiento[0]['idCalendario']));
            }

            $this->_helper->json($results);
        }


    }

    public function generarferiadosAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $modeloperiodo = new Application_Model_DbTable_Periodo();
        $idcalendarioestablecimiento = $this->_getParam('id', 0);

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;


        if ($rol == 1 || $rol == 3 || $rol == 6) {

            $datos = $modeloperiodo->getferiados($idperiodo);

            if (count($datos) == 0) {
                //Hacemos una llamada a la funcion getferiadosapi
                $datos = $this->getferiadoapi();

            }

            //Ingresamos los días a la tabla eventos del calendario del establecimiento
            foreach ($datos as $a => $value) {

                //Validamos que el dia no este ingresado para el calendario
                $valida = $modeloperiodo->validarferiado($idcalendarioestablecimiento, $value['fechaFeriado']);
                if (!$valida) { //Si no existe se agrega el evento
                    $modeloperiodo->agregarevento($idcalendarioestablecimiento, $value['nombreFeriado'], $value['fechaFeriado'], 2);
                }

            }

            $this->redirect('/Periodo/evento/id/' . $idcalendarioestablecimiento);

        }


    }


    public function getferiadoapi()
    {


        $modeloperiodo = new Application_Model_DbTable_Periodo();

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $periodos = new Zend_Session_Namespace('nombreperiodo');
        $nombreperiodo = $periodos->nombreperiodo;

        if ($rol == 1 || $rol == 3 || $rol == 6) {

            //API URL Obtenemos los dias feriados desde la api
            $url = 'https://apis.digital.gob.cl/fl/feriados/' . $nombreperiodo . '/';
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            //execute the POST request
            $result = curl_exec($ch);
            //close cURL resource
            curl_close($ch);
            $resultadodeco = json_decode($result);
            if (!$resultadodeco->error) {
                //Poblamos la Tabla de Feriados correspondientes al año desde la api y devolvemos los datos de la tabvla feriados

                foreach ($resultadodeco as $a => $value) {
                    if ($a > 0) {
                        //Agreamos los datos a la tabla feriados Legales de Chile
                        $modeloperiodo->agregarferiados($value->nombre, $value->fecha, $idperiodo);

                    }
                }

                return $modeloperiodo->getferiados($idperiodo);

            } else {
                return false;
            }


        }


    }

    //Bloque

    public function bloqueAction()
    {
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idestablecimiento = $this->_getParam('id', 0);

        if ($idestablecimiento > 0) {
            $table = new Application_Model_DbTable_Periodo();
            $this->view->dato = $table->listarbloque($idperiodo, $idestablecimiento);
        }


    }

    public function agregarbloqueAction()
    {
        $this->view->title = "Agregar Bloque";
        $this->view->headTitle($this->view->title);
        $form = new Application_Form_Bloque();
        $idestablecimiento = $this->_getParam('id', 0);
        $form->idEstablecimiento->setValue($idestablecimiento);
        $form->submit->setLabel('Agregar');
        $this->view->id = $idestablecimiento;
        $this->view->form = $form;


        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                try {
                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                    $db->beginTransaction();
                    $modeloperiodo = new Application_Model_DbTable_Periodo();

                    $periodo = new Zend_Session_Namespace('periodo');
                    $idperiodo = $periodo->periodo;

                    $idestablecimiento = $form->getValue('idEstablecimiento');
                    $nombre = $form->getValue('nombreBloque');
                    $tiempoinicio = $form->getValue('tiempoInicio');
                    $tiempotermino = $form->getValue('tiempoTermino');
                    $tipo = $form->getValue('tipoBloque');

                    $modeloperiodo->agregarbloque($idestablecimiento, $nombre, $tiempoinicio, $tiempotermino, $tipo, $idperiodo);

                    $db->commit();
                    $this->redirect('/Periodo/bloque/id/' . $idestablecimiento);

                } catch (Exception $e) {
                    $db->rollBack();
                    echo $e;
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage(self::mensaje);
                    $this->view->assign('messages', $messages);
                }


            } else {

                $form->populate($formData);
            }
        }
    }

    public function editarbloqueAction()
    {

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $this->view->title = "Editar Bloque";
        $this->view->headTitle($this->view->title);
        $form = new Application_Form_Bloque();
        $form->tiempoInicio->setAttrib('readonly', true);
        $form->tiempoTermino->setAttrib('readonly', true);
        $modeloperiodo = new Application_Model_DbTable_Periodo();
        $form->submit->setLabel('Actualizar');
        $idbloque = $this->_getParam('id', 0);
        $idestablecimiento = $this->_getParam('ide', 0);


        $this->view->id = $idestablecimiento;
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                try {
                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                    $db->beginTransaction();

                    $idestablecimiento = $form->getValue('idEstablecimiento');
                    $idbloque = $form->getValue('idBloque');
                    $nombre = $form->getValue('nombreBloque');

                    $modeloperiodo->actualizarbloque($idbloque, $nombre);

                    $db->commit();
                    $this->redirect('/Periodo/bloque/id/' . $idestablecimiento);

                } catch (Exception $e) {
                    $db->rollBack();
                    echo $e;
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage(self::mensaje);
                    $this->view->assign('messages', $messages);
                }


            } else {

                $form->populate($formData);
            }
        } else {

            $idbloque = $this->_getParam('id', 0);

            if ($idbloque > 0) {

                $datosbloque = $modeloperiodo->listarbloqueid($idbloque, $idperiodo, $idestablecimiento);
                $datosbloque = $datosbloque->toArray();
                $form->populate($datosbloque[0]);
            }

        }
    }

    public function eliminarbloqueAction()
    {

        $idbloque = $this->_getParam('id', 0);
        $idestablecimiento = $this->_getParam('ide', 0);
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $db->beginTransaction();
        try {

            $modelocurso = new Application_Model_DbTable_Cursos();
            $datosbloque = $modelocurso->listarbloquesid($idbloque);
            if (count($datosbloque) > 0) {
                $db->rollBack();
                $this->view->dato = $idestablecimiento;
                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No se puede eliminar este registro, posee datos asociados');
                $this->view->assign('messages', $messages);

            } else {
                $modelocurso->eliminarbloqueid($idbloque);
                $db->commit();
                $this->redirect('/Periodo/bloque/id/' . $idestablecimiento);
            }

        } catch (Exception $e) {
            // Si hubo problemas. Enviamos marcha atras
            $db->rollBack();
            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No se puede eliminar este registro, posee datos asociados');
            $this->view->assign('messages', $messages);
        }
    }


}







