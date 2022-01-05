<?php

ini_set("default_socket_timeout", 15);
ini_set('soap.wsdl_cache_enabled', 0);
ini_set('soap.wsdl_cache_ttl', 0);

/**
 * Created by PhpStorm.
 * User: Raul Retamal
 * Date: 02-07-17
 * Time: 8:46 PM
 */
class SigeController extends Zend_Controller_Action
{

    public function init()
    {
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();

    }

    public function indexAction()
    {
        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;
        $modelosige = new Application_Model_DbTable_Sige();
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;
        if ($rol == '1' || $rol=='3') {
            $this->view->dato = $modelosige->listar($idperiodo);
        }


    }

    public function indextipoAction()
    {
        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;
        $modelosige = new Application_Model_DbTable_Sige();
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;
        if ($rol == '1') {
            $this->view->dato = $modelosige->listartipo($idperiodo);
        }


    }

    public function agregarAction()
    {
        $this->view->title = "Agregar Datos Sige";
        $this->view->headTitle($this->view->title);
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;
        $form = new Application_Form_Sige();
        $form->setDecorators(array('FormElements', 'Form'));
        $form->submit->setLabel('Guardar');
        $form->volver->setLabel('Volver');
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                $clienteid = $form->getValue('ClienteId');
                $convenio = $form->getValue('ConvenioId');
                $token = $form->getValue('ConvenioToken');
                $idestablecimiento = $form->getValue('idEstablecimiento');
                $modelosige = new Application_Model_DbTable_Sige();

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $db->beginTransaction();
                try {
                    $modelosige->agregar($clienteid, $convenio, $token, $idperiodo, $idestablecimiento);
                    $db->commit();
                    $this->_helper->redirector('index');

                } catch (Exception $e) {
                    // Si hubo problemas. Enviamos todo marcha atras
                    $db->rollBack();
                    echo $e;
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al ingresar los datos, intente nuevamente');
                    /// Assign the messages
                    $this->view->assign('messages', $messages);
                }
            } else {
                $form->populate($formData);
            }

        }

    }


    public function editarAction()
    {
        $this->view->title = "Editar Datos Sige";
        $this->view->headTitle($this->view->title);
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;
        $form = new Application_Form_Sige();
        $form->setDecorators(array('FormElements', 'Form'));
        $form->submit->setLabel('Guardar');
        $form->volver->setLabel('Volver');
        $form->addElement('Text', 'ValorSemilla', array('order' => 5));
        $form->ValorSemilla->setLabel('Semilla: ')->setRequired(true);
        $this->view->form = $form;
        $modelosige = new Application_Model_DbTable_Sige();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $idsige = $form->getValue('idSige');
                $clienteid = $form->getValue('ClienteId');
                $convenio = $form->getValue('ConvenioId');
                $token = $form->getValue('ConvenioToken');
                $semilla = $form->getValue('ValorSemilla');


                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $db->beginTransaction();
                try {
                    $modelosige->cambiar($idsige, $clienteid, $convenio, $token, $semilla);
                    $db->commit();
                    $this->_helper->redirector('index');

                } catch (Exception $e) {
                    // Si hubo problemas. Enviamos todo marcha atras
                    $db->rollBack();
                    echo $e;
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al ingresar los datos, intente nuevamente');
                    /// Assign the messages
                    $this->view->assign('messages', $messages);
                }
            } else {
                $form->populate($formData);
            }

        } else {


            $id = $this->_getParam('id', 0);

            if ($id > 0) {
                $resultado = $modelosige->get($id);
                $form->idEstablecimiento->setValue($resultado[0]['idEstablecimiento']);
                $form->populate($resultado[0]);


            }
        }

    }

    private function getsemillaonly($est, $per)
    {
        $modelosige = new Application_Model_DbTable_Sige();
        $valores = $modelosige->get2($est, $per);

        $wsdl = "http://w7app.mineduc.cl/WsApiAutorizacion/wsdl/SemillaServiciosSoapPort.wsdl";

        $parametros = new stdClass();
        $parametros->ClienteId = $valores[0]['ClienteId'];
        $parametros->ConvenioId = $valores[0]['ConvenioId'];
        $parametros->ConvenioToken = $valores[0]['ConvenioToken'];


        try {
            $client = new SoapClient($wsdl);
            $response = $client->__soapCall("getSemillaServicios", array($parametros), ['location' => 'http://w7app.mineduc.cl/WsApiAutorizacion/services/SemillaServiciosSoapPort']);
            if (!empty($response->ValorSemilla)) {
                return (object)["semilla" => $response->ValorSemilla, "params" => $parametros];
            } else {
                return null;
            }
        } catch (SoapFault $e) {
            return null;
        } catch (Exception $e) {
            return null;
        }
    }

    public function validaralumnoAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $json = json_decode(file_get_contents('php://input'));
            if (is_null($json)) {
                die(json_encode(["response" => 0]));
            }
            $sem = $this->getsemillaonly($json->est, $json->per);

            $modelosige = new Application_Model_DbTable_Sige();
            //$resultado = $modelosige->get2($json->est, $json->per);

            //$sem = $resultado[0]['ValorSemilla'];
            if (is_null($sem)) {
                die(json_encode(["response" => 3]));
            }

            $wsdl = "http://w7app.mineduc.cl/WsApiMineduc/wsdl/ValidaAlumnoSigeSoapPort.wsdl";
            $parametros = new stdClass();
            $parametros->Run = new stdClass();
            $parametros->Run->numero = $json->rut;
            $parametros->Run->dv = $json->dv;
            $parametros->Nombres = $json->nombres;
            $parametros->ApellidoPaterno = $json->appaterno;
            $parametros->ApellidoMaterno = $json->apmaterno;
            $parametros->Semilla = $sem->semilla;


            try {
                $client = new SoapClient($wsdl);
                $response = $client->__soapCall("getValidacion", array($parametros), ['location' => 'http://w7app.mineduc.cl/WsApiMineduc/services/ValidaAlumnoSigeSoapPort']);

                if (!empty($response->ExisteFichaAlumno)) {
                    //Guardamos en el historial
                    $modelosige = new Application_Model_DbTable_Sige();
                    $date = new DateTime;
                    $fecha = $date->format('Y-m-d H:i:s');
                    $modelosige->agregarhistorialalumno($sem, $response->ExisteFichaAlumno, $fecha, $json->id);
                    die(json_encode(["response" => 1, "status" => $response->ExisteFichaAlumno]));
                } else {
                    die(json_encode(["response" => 5]));
                }
            } catch (SoapFault $e) {

                die(json_encode(["response" => 6]));
            } catch (Exception $e) {

                die(json_encode(["response" => 6]));
            }
        } else {
            die(json_encode(["response" => 6]));
        }
    }

    public function obtenerAction()
    {
        $id = $this->_getParam('id', 0);
        $modelosige = new Application_Model_DbTable_Sige();
        $valores = $modelosige->get($id);

        $wsdl = "http://w7app.mineduc.cl/WsApiAutorizacion/wsdl/SemillaServiciosSoapPort.wsdl";

        $parametros = new stdClass();
        $parametros->ClienteId = $valores[0][ClienteId];
        $parametros->ConvenioId = $valores[0][ConvenioId];
        $parametros->ConvenioToken = $valores[0][ConvenioToken];


        try {
            $client = new SoapClient($wsdl);
            $response = $client->__soapCall("getSemillaServicios", array($parametros), ['location' => 'http://w7app.mineduc.cl/WsApiAutorizacion/services/SemillaServiciosSoapPort']);
            if (!empty($response->ValorSemilla)) {

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $db->beginTransaction();
                try {
                    $date = new DateTime;
                    $fecha = $date->format('Y-m-d H:i:s');
                    $modelosige->actualizarsemilla($id, $response->ValorSemilla, $fecha);
                    $db->commit();
                    $this->_helper->redirector('index');

                } catch (Exception $e) {
                    // Si hubo problemas. Enviamos todo marcha atras
                    $db->rollBack();
                    echo $e;
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al actualizar la semilla, intente nuevamente');
                    /// Assign the messages
                    $this->view->assign('messages', $messages);
                }

            } else {
                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('La Semilla Es nula');
                /// Assign the messages
                $this->view->assign('messages', $messages);
            }
        } catch (SoapFault $e) {
            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Error de Conexión' . $e . '');
            /// Assign the messages
            $this->view->assign('messages', $messages);
        }


    }


    public function alumnosAction()
    {

        //recuperamos el establecimiento guardado en sesion
        $establecimiento = new Zend_Session_NameSpace("establecimiento");
        $id = $establecimiento->establecimiento;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;
        //creo objeto que maneja la tabla Periodo
        $table = new Application_Model_DbTable_Alumnos();
        $cargo = new Zend_Session_NameSpace("cargo");
        if ($cargo->cargo == '1' || $cargo->cargo == '4') {
            $result = $table->listar($idperiodo);
        }
        if ($cargo->cargo == '3') {
            $result = $table->listarestablecimiento($id, $idperiodo);
        }

        $this->view->paginator = $result;

    }


    public function agregartipoAction()
    {
        $this->view->title = "Agregar Tipo de Enseñanza";
        $this->view->headTitle($this->view->title);
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;
        $form = new Application_Form_Tipo();
        $form->setDecorators(array('FormElements', 'Form'));
        $form->submit->setLabel('Guardar');
        $form->volver->setLabel('Volver');
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                $idestablecimiento = $form->getValue('idEstablecimiento');
                $periodo = $form->getValue('idPeriodo');
                $codigo = $form->getValue('idCodigoTipo');
                $estado = $form->getValue('estadoTipo');
                $autorizacion = $form->getValue('autorizacion');
                $fechaautorizacion = date('Y-m-d', strtotime($form->getValue('fechaAutorizacion')));
                $centro = $form->getValue('centro');
                $juridica = $form->getValue('juridica');
                $numero = $form->getValue('numeroGrupo');
                $iniciom = $form->getValue('inicioManana');
                $terminom = $form->getValue('terminoManana');
                $iniciot = $form->getValue('inicioTarde');
                $terminot = $form->getValue('terminoTarde');
                $iniciomt = $form->getValue('inicioMananaTarde');
                $terminomt = $form->getValue('terminoMananaTarde');
                $iniciov = $form->getValue('inicioVespertino');
                $terminov = $form->getValue('terminoVespertino');


                $modelosige = new Application_Model_DbTable_Sige();

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $db->beginTransaction();
                try {
                    $modelosige->agregartipo($periodo, $idestablecimiento, $codigo, $estado, $autorizacion, $fechaautorizacion, $centro, $juridica, $numero, $iniciom, $terminom, $iniciot, $terminot, $iniciomt, $terminomt, $iniciov, $terminov);
                    $db->commit();
                    $this->_helper->redirector('indextipo');

                } catch (Exception $e) {
                    // Si hubo problemas. Enviamos todo marcha atras
                    $db->rollBack();
                    echo $e;
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al ingresar los datos, intente nuevamente');
                    /// Assign the messages
                    $this->view->assign('messages', $messages);
                }
            } else {
                $form->populate($formData);
            }

        }

    }


    public function validartipoAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $json = json_decode(file_get_contents('php://input'));
            if (is_null($json)) {
                die(json_encode(["response" => 0]));
            }
            $sem = $this->getsemillaonly($json->est, $json->per);
            $modelosige = new Application_Model_DbTable_Sige();
            //$resultado = $modelosige->get2($json->est, $json->per);

            //$sem = $resultado[0]['ValorSemilla'];
            if (is_null($sem)) {
                die(json_encode(["response" => 3]));
            }

            $wsdl = "http://w7app.mineduc.cl/WsApiMineduc/wsdl/TipoEnsenanzaSigeSoapPort.wsdl";
            $parametros = new stdClass();
            $parametros->RecordTipoEnsenanzaSige = new stdClass();
            $parametros->RecordTipoEnsenanzaSige->PKTipoEnsenanzaSige = new stdClass();
            $parametros->RecordTipoEnsenanzaSige->PKTipoEnsenanzaSige->AnioEscolar = intval($json->idperiodo);
            $parametros->RecordTipoEnsenanzaSige->PKTipoEnsenanzaSige->RBD = intval($json->rbd);
            $parametros->RecordTipoEnsenanzaSige->PKTipoEnsenanzaSige->CodigoTipoEnsenanza = intval($json->idcodigo);
            $parametros->RecordTipoEnsenanzaSige->EstadoTipoEnsenanza = intval($json->estado);
            $parametros->RecordTipoEnsenanzaSige->NumeroAutorizacion = intval($json->autorizacion);
            $parametros->RecordTipoEnsenanzaSige->FechaAutorizacion = $json->fecha;
            $parametros->RecordTipoEnsenanzaSige->TieneCentroPadres = (bool)$json->centro;
            $parametros->RecordTipoEnsenanzaSige->TienePersonalidadJuridica = (bool)$json->juridica;
            $parametros->RecordTipoEnsenanzaSige->NumeroGruposDiferenciales = $json->numero;
            $parametros->RecordTipoEnsenanzaSige->HorarioInicioManana = $json->iniciom;
            $parametros->RecordTipoEnsenanzaSige->HorarioTerminoManana = $json->terminom;
            $parametros->RecordTipoEnsenanzaSige->HorarioInicioTarde = $json->iniciot;
            $parametros->RecordTipoEnsenanzaSige->HorarioTerminoTarde = $json->terminot;
            $parametros->RecordTipoEnsenanzaSige->HorarioInicioMananaTarde = $json->iniciomt;
            $parametros->RecordTipoEnsenanzaSige->HorarioTerminoMananaTarde = $json->terminomt;
            $parametros->RecordTipoEnsenanzaSige->HorarioInicioVespertino = $json->iniciov;
            $parametros->RecordTipoEnsenanzaSige->HorarioTerminoVespertino = $json->terminov;
            $parametros->Semilla = $sem->semilla;

            try {
                $client = new SoapClient($wsdl);

                $response = $client->__soapCall("addTipoEnsenanza", array($parametros), ['location' => 'http://w7app.mineduc.cl/WsApiMineduc/services/TipoEnsenanzaSigeSoapPort']);

                if (!empty($response->CodigoRespuestaTipoEnsenanza)) {
                    //Guardamos en el historial
                    $modelosige = new Application_Model_DbTable_Sige();
                    $date = new DateTime;
                    $fecha = $date->format('Y-m-d H:i:s');
                    $modelosige->agregarhistorialtipo($sem, $response->CodigoRespuestaTipoEnsenanza, $fecha, $json->idtipo);
                    die(json_encode(["response" => 1, "status" => $response->CodigoRespuestaTipoEnsenanza]));
                } else {
                    die(json_encode(["response" => 2]));
                }
            } catch (SoapFault $e) {

                die(json_encode(["response" => 6]));
            } catch (Exception $e) {

                die(json_encode(["response" => 6]));
            }
        } else {
            die(json_encode(["response" => 6]));
        }


    }


    public function cursosAction()
    {

        $table = new Application_Model_DbTable_Cursos();
        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;
        if ($rol == '1') {
            $this->view->dato = $table->listaractual($idperiodo);

        }

    }


    public function agregarcursoAction()
    {

        $this->view->title = "Agregar Curso";
        $this->view->headTitle($this->view->title);
        $form = new Application_Form_CursosActual();
        $form->submit->setLabel('Guardar');
        $this->view->form = $form;


        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                $idestablecimiento = $form->getValue('idEstablecimiento');
                $idperiodo = $form->getValue('idPeriodo');
                $idcodigo = $form->getValue('idCodigoTipo');
                $idnivel = $form->getValue('idNiveles');
                $letra = $form->getValue('letra');
                $idcuentajefe = $form->getValue('idCuentaJefe');
                $combinado = $form->getValue('combinado');
                $numero = $form->getValue('numeroCurso');
                $jornada = $form->getValue('tipoJornada');
                $codigosector = $form->getValue('codigoSector');
                $codigoespecialidad = $form->getValue('codigoEspecialidad');
                $codigoalternativa = $form->getValue('codigoAlternativa');
                $infraestructura = $form->getValue('infraestructura');


                $curso = new Application_Model_DbTable_Cursos();

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                // Iniciamos la transaccion
                $db->beginTransaction();
                try {
                    $curso->agregaractual($idestablecimiento, $idperiodo, $idcodigo, $idnivel, $letra, $idcuentajefe, $combinado, $numero, $jornada, $codigosector, $codigoespecialidad, $codigoalternativa, $infraestructura);
                    $db->commit();
                    $this->_helper->redirector('cursos');
                } catch (Exception $e) {
                    // Si hubo problemas. Enviamos todo marcha atras
                    $db->rollBack();
                    echo $e;
                }
            } else {
                //Si el Formulario no es válido
                $idest = $this->_getParam('idEstablecimiento', 0);
                $idcod = $this->_getParam('idCodigoTipo', 0);
                $idgrado = $this->_getParam('idGrado', 0);
                $idcuenta = $this->_getParam('idCuentaJefe', 0);

                $modelo = new Application_Model_DbTable_Nivel();
                $modeloc = new Application_Model_DbTable_Cuentas();
                $resultado = $modelo->getcodigos($idcod);
                $resultadoc = $modeloc->listarsolodocentes($idest);
                foreach ($resultado as $row) {
                    $data[$row['idNiveles']] = $row['nombreNiveles'];
                }

                foreach ($resultadoc as $rowc) {
                    $datac[$rowc['idCuenta']] = $rowc['usuario'] . '-' . $rowc['nombrescuenta'] . '' . $rowc['paternocuenta'] . '' . $rowc['maternocuenta'];
                }

                $form->idNiveles->clearMultiOptions();
                $form->idNiveles->addMultiOptions($data);
                $form->idNiveles->setValue($idgrado);

                $form->idCuentaJefe->clearMultiOptions();
                $form->idCuentaJefe->addMultiOptions($datac);
                $form->idCuentaJefe->setValue($idcuenta);

                $form->populate($formData);
            }
        }

    }


    public function editarcursoAction()
    {

        $this->view->title = "Modificar Curso";
        $this->view->headTitle($this->view->title);
        $form = new Application_Form_CursosActual();
        $form->submit->setLabel('Guardar');
        $this->view->form = $form;


        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $idcurso = $form->getValue('idCursos');
                $idestablecimiento = $form->getValue('idEstablecimiento');
                $idperiodo = $form->getValue('idPeriodo');
                $idnivel = $form->getValue('idNiveles');
                $letra = $form->getValue('letra');
                $idcuentajefe = $form->getValue('idCuentaJefe');
                $combinado = $form->getValue('combinado');
                $numero = $form->getValue('numeroCurso');
                $jornada = $form->getValue('tipoJornada');
                $codigosector = $form->getValue('codigoSector');
                $codigoespecialidad = $form->getValue('codigoEspecialidad');
                $codigoalternativa = $form->getValue('codigoAlternativa');
                $infraestructura = $form->getValue('infraestructura');


                $curso = new Application_Model_DbTable_Cursos();

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                // Iniciamos la transaccion
                $db->beginTransaction();
                try {

                    $curso->cambiaractual($idcurso, $idestablecimiento, $idperiodo, $idnivel, $letra, $idcuentajefe, $combinado, $numero, $jornada, $codigosector, $codigoespecialidad, $codigoalternativa, $infraestructura);
                    $db->commit();
                    $this->_helper->redirector('cursos');

                } catch (Exception $e) {
                    // Si hubo problemas. Enviamos todo marcha atras
                    $db->rollBack();
                    echo $e;
                }
            } else {
                //Si el Formulario no es válido
                $idest = $this->_getParam('idEstablecimiento', 0);
                $idcod = $this->_getParam('idCodigoTipo', 0);
                $idgrado = $this->_getParam('idGrado', 0);
                $idcuenta = $this->_getParam('idCuentaJefe', 0);

                $modelo = new Application_Model_DbTable_Nivel();
                $modeloc = new Application_Model_DbTable_Cuentas();
                $resultado = $modelo->getcodigos($idcod);
                $resultadoc = $modeloc->listarsolodocentes($idest);
                foreach ($resultado as $row) {
                    $data[$row['idNiveles']] = $row['nombreNiveles'];
                }

                foreach ($resultadoc as $rowc) {
                    $datac[$rowc['idCuenta']] = $rowc['usuario'] . '-' . $rowc['nombrescuenta'] . '' . $rowc['paternocuenta'] . '' . $rowc['maternocuenta'];
                }

                $form->idNiveles->clearMultiOptions();
                $form->idNiveles->addMultiOptions($data);
                $form->idNiveles->setValue($idgrado);

                $form->idCuentaJefe->clearMultiOptions();
                $form->idCuentaJefe->addMultiOptions($datac);
                $form->idCuentaJefe->setValue($idcuenta);

                $form->populate($formData);

            }
        } else {

            $idcurso = $this->_getParam('id', 0);
            //si viene algun id
            if ($idcurso > 0) {
                $curso = new Application_Model_DbTable_Cursos();
                $cursos = $curso->getactual($idcurso);
                $idest = $cursos[0]['idEstablecimiento'];

                $modelo = new Application_Model_DbTable_Nivel();
                $modeloc = new Application_Model_DbTable_Cuentas();

                $resultadoc = $modeloc->listarsolodocentes($idest);


                foreach ($resultadoc as $rowc) {
                    $datac[$rowc['idCuenta']] = $rowc['usuario'] . '-' . $rowc['nombrescuenta'] . '' . $rowc['paternocuenta'] . '' . $rowc['maternocuenta'];
                }


                $form->idCuentaJefe->clearMultiOptions();
                $form->idCuentaJefe->addMultiOptions($datac);
                $form->idCuentaJefe->setValue($cursos[0]['idCuentaJefe']);

                $form->populate($cursos[0]);
            }
        }
    }


    public function getnivelAction()
    {
        $modelo = new Application_Model_DbTable_Nivel();
        $resultado = $modelo->getcodigos($this->_getParam('id'));
        $this->_helper->json($resultado);
    }

    public function getespecialidadAction()
    {
        $modelo = new Application_Model_DbTable_Sige();
        $resultado = $modelo->getespecialidad($this->_getParam('id'));
        $this->_helper->json($resultado);
    }

    public function getdocenteAction()
    {
        $modelo = new Application_Model_DbTable_Cuentas();
        $resultado = $modelo->listarsolodocentes($this->_getParam('id'));
        $this->_helper->json($resultado);
    }


    public function validarcursoAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $json = json_decode(file_get_contents('php://input'));
            if (is_null($json)) {
                die(json_encode(["response" => 0]));
            }
            $modelosige = new Application_Model_DbTable_Sige();
            //$resultado = $modelosige->get2($json->est, $json->per);

            //$sem = $resultado[0]['ValorSemilla'];
            $sem = $this->getsemillaonly($json->est, $json->per);
            if (is_null($sem)) {
                die(json_encode(["response" => 3]));
            }

            $wsdl = "http://w7app.mineduc.cl/WsApiMineduc/wsdl/CursoSigeSoapPort.wsdl";
            $parametros = new stdClass();
            $parametros->RecordCursoSige = new stdClass();
            $parametros->RecordCursoSige->PKCursoSige = new stdClass();
            $parametros->RecordCursoSige->PKCursoSige->AnioEscolar = intval($json->idperiodo);
            $parametros->RecordCursoSige->PKCursoSige->RBD = intval($json->rbd);
            //$parametros->RecordCursoSige->PKCursoSige->RBD =188888;
            $parametros->RecordCursoSige->PKCursoSige->CodigoTipoEnsenanza = intval($json->idcodigo);
            $parametros->RecordCursoSige->PKCursoSige->CodigoGrado = intval($json->grado);
            $parametros->RecordCursoSige->PKCursoSige->LetraCurso = $json->letra;
            $parametros->RecordCursoSige->Run = new stdClass();
            $parametros->RecordCursoSige->Run->numero = intval($json->run);
            $parametros->RecordCursoSige->Run->dv = intval($json->dv);
            $parametros->RecordCursoSige->CursoCombinado = (bool)$json->combinado;
            $parametros->RecordCursoSige->NumeroCursoCombinado = $json->numero;
            $parametros->RecordCursoSige->CodigoTipoJornada = intval($json->jornada);
            $parametros->RecordCursoSige->CodigoSectorEconomico = intval($json->sector);
            $parametros->RecordCursoSige->CodigoEspecialidad = intval($json->especialidad);
            $parametros->RecordCursoSige->CodigoAlternativaDesarrolloCurricular = intval($json->alternativa);
            $parametros->RecordCursoSige->TieneInfraestructuraEspecialidad = (bool)$json->infraestructura;
            $parametros->Semilla = $sem->semilla;


            try {
                $client = new SoapClient($wsdl);

                $response = $client->__soapCall("addCurso", array($parametros), ['location' => 'http://w7app.mineduc.cl/WsApiMineduc/services/CursoSigeSoapPort']);

                if (!empty($response->CodigoRespuestaCurso)) {
                    //Guardamos en el historial
                    $modelosige = new Application_Model_DbTable_Sige();
                    $date = new DateTime;
                    $fecha = $date->format('Y-m-d H:i:s');
                    $modelosige->agregarhistorialcurso($sem, $response->CodigoRespuestaCurso, $fecha, $json->idcurso);
                    die(json_encode(["response" => 1, "status" => $response->CodigoRespuestaCurso]));
                } else {
                    die(json_encode(["response" => 2]));
                }
            } catch (SoapFault $e) {

                die(json_encode(["response" => 6]));
            } catch (Exception $e) {

                die(json_encode(["response" => 6]));
            }
        } else {
            die(json_encode(["response" => 8]));
        }


    }

    public function verhistorialcursoAction()
    {
        $this->_helper->layout->disableLayout();
        $id = $this->_getParam('id');

        if ($id > 0) {

            $modelosige = new Application_Model_DbTable_Sige();
            $resultado = $modelosige->listarhistorialcurso($id);
            $this->view->datos = $resultado;

        }

    }

    public function verhistorialtipoAction()
    {
        $this->_helper->layout->disableLayout();
        $id = $this->_getParam('id');

        if ($id > 0) {

            $modelosige = new Application_Model_DbTable_Sige();
            $resultado = $modelosige->listarhistorialtipo($id);
            $this->view->datos = $resultado;

        }

    }

    public function verhistorialalumnoAction()
    {
        $this->_helper->layout->disableLayout();
        $id = $this->_getParam('id');

        if ($id > 0) {

            $modelosige = new Application_Model_DbTable_Sige();
            $resultado = $modelosige->listarhistorialalumno($id);
            $this->view->datos = $resultado;

        }

    }

    public function verhistorialasistenciaAction()
    {
        $this->_helper->layout->disableLayout();
        $id = $this->_getParam('id');

        if ($id > 0) {

            $modelosige = new Application_Model_DbTable_Sige();
            $resultado = $modelosige->listarhistorialasistencia($id);
            $this->view->datos = $resultado;

        }

    }

    public function verhistorialasistenciareporteAction()
    {
        $this->_helper->layout->disableLayout();
        $id = $this->_getParam('id');

        if ($id > 0) {

            $modelosige = new Application_Model_DbTable_Sige();
            $resultado = $modelosige->listarhistorialasistenciareporte($id);
            $this->view->datos = $resultado;

        }

    }

    public function asistenciaAction()
    {
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        if ($rol == '5' || $rol == '1' || $rol=='3') {

            $modelas = new Application_Model_DbTable_Asistencia();


            $listanotas = $modelas->listarall($idperiodo);

            for ($i = 0; $i < count($listanotas); $i++) {

                if ($listanotas[$i]['idAsistencia']) {

                    $listanotas[$i]['Codigos'] = $modelas->ultimocodigo($listanotas[$i]['idAsistencia']);


                }
            }

            $page = $this->_getParam('page', 1);
            $paginator = Zend_Paginator::factory($listanotas);
            $paginator->setItemCountPerPage(20);
            $paginator->setCurrentPageNumber($page);
            $this->view->paginator = $paginator;

    }


    }

    public function agregarasistenciaAction()
    {

        $formas = new Application_Form_AsistenciaSige();
        $this->view->form = $formas;
    }

    public function getdiasAction()
    {
        $modelModelo = new Application_Model_DbTable_Asistencia();
        $idcurso = $this->_getParam('id');

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;


        $results = $modelModelo->getdias($idcurso, $idperiodo);
        $this->_helper->json($results);
    }

    public function getalumnosAction()
    {

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;


        $idcurso = $this->_getParam('id');

        $modelModelo = new Application_Model_DbTable_Alumnosactual();

        $results = $modelModelo->listaralumnoscurso($idcurso, $idperiodo);
        $this->_helper->json($results);
    }

    public function guardaasistenciaAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            //Detectamos si es una llamada AJAX

            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();

            //guardamos los datos en $json recibidos de la funcion ajax
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $asistencia = new Application_Model_DbTable_Asistencia();

            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;

            $rutusuario = new Zend_Session_Namespace('id');
            $usuario = $rutusuario->id;
            if (empty($usuario)) {
                echo Zend_Json::encode(array('response' => 'errorsesion'));
            } else {

                $fecha = date("Y-m-d", strtotime($data['0']['fecha']));
                $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                // Iniciamos la transaccion
                $db->beginTransaction();
                try {

                    $asistencia->agregar($fecha, $idperiodo, $data['0']['curso'], $usuario);
                    $idasistencia = $asistencia->getAdapter()->lastInsertId();

                    //recorremos el arreglo con los datos recibidos del formulario
                    for ($i = 0; $i < count($data[0]['valores']); $i++) {

                        $asistencia->agregarvalores($data[0]['valores'][$i]['ausencia'], $data[0]['valores'][$i]['alumno'], $idasistencia);
                    }
                    $db->commit();
                    echo Zend_Json::encode(array('redirect' => '/Sige/asistencia'));
                } catch (Exception $e) {
                    //echo $e;
                    // Si hubo problemas. Enviamos todo marcha atras
                    $db->rollBack();
                    echo Zend_Json::encode(array('response' => 'errorinserta'));
                }

            }

        }
    }

    public function editarasistenciaAction()
    {

        $form = new Application_Form_AsistenciaSige();
        $this->view->form = $form;


        $id = base64_decode($this->_getParam('id'));


        if ($id > 0) {

            $modelnotas = new Application_Model_DbTable_Asistencia();

            $listaas = $modelnotas->listaralumnosbasica($id);

            $form->fecha->setValue(date("d-m-Y", strtotime($listaas[0]['fechaAsistencia'])));

            $form->idCursos->setValue($listaas[0]['idCursos']);
            $form->idCursos->setAttribs(array('disable' => 'disable'));

            $this->view->datos = $listaas;


        }

    }

    public function guardaasistenciaeditarAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            //Detectamos si es una llamada AJAX

            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();

            //guardamos los datos en $json recibidos de la funcion ajax
            $json = file_get_contents('php://input');
            //decodificamos los datos en un array($data) php

            $data = json_decode($json, true);
            $idasistencia = base64_decode($data['ida']);

            $asistencia = new Application_Model_DbTable_Asistencia();
            $listaalumnos = $asistencia->listaralumnosbasica($idasistencia);

            //extreamos el rut del usuario que ingresa
            $rutusuario = new Zend_Session_Namespace('id');
            $usuario = $rutusuario->id;
            if (empty($usuario)) {
                echo Zend_Json::encode(array('response' => 'errorsesion'));
            } else {
                if (count($listaalumnos) == count($data['asistencia'])) {

                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                    // Iniciamos la transaccion
                    $db->beginTransaction();
                    try {

                        for ($i = 0; $i < count($data['asistencia']); $i++) {
                            $asistencia->cambiar($listaalumnos[$i]['idAsistenciaValores'], $data['asistencia'][$i]['valor']);
                        }
                        $db->commit();
                        echo Zend_Json::encode(array('redirect' => '/Sige/asistencia'));
                    } catch (Exception $e) {
                        // Si hubo problemas. Enviamos todo marcha atras
                        $db->rollBack();
                        echo Zend_Json::encode(array('response' => 'errorinserta'));
                    }
                } else {
                    echo Zend_Json::encode(array('response' => 'errorinserta2'));
                }


            }

        }
    }

    public function verasistenciaAction()
    {
        $this->_helper->layout->disableLayout();
        $id = base64_decode($this->_getParam('id'));

        if ($id > 0) {

            $modelnotas = new Application_Model_DbTable_Asistencia();
            $modelcurso= new Application_Model_DbTable_Cursos();




            $lista = $modelnotas->listaralumnosbasica($id);

            $datoscurso=$modelcurso->getnombreactual($lista[0]['idCursos']);
            $this->view->listaasistencia = $lista;
            $this->view->nombrecurso= $datoscurso[0]['nombreGrado'].' '.$datoscurso[0]['letra'];


        }

    }

    public function enviaasistenciaAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $json = json_decode(file_get_contents('php://input'));
            if (is_null($json)) {
                die(json_encode(["response" => 0]));
            }
            $sem = $this->getsemillaonly($json->est, $json->periodo);
            $modelosige = new Application_Model_DbTable_Sige();
            //$resultado = $modelosige->get2($json->est, $json->periodo);
            //$sem = $resultado[0]['ValorSemilla'];
            if (is_null($sem)) {
                die(json_encode(["response" => 3]));
            }
            $curs = new Application_Model_DbTable_Cursos();
            $resultadocurso = $curs->getcursoactualtotal($json->idcurso);


            $as = new Application_Model_DbTable_Asistencia();
            $asist = $as->listaralumnosbasica($json->idasistencia);

            $wsdl = "http://w7app.mineduc.cl/WsApiMineduc/wsdl/AsistenciaSigeSoapPort.wsdl";
            $parametros = new stdClass();
            $parametros->RecordAsistenciaSige = new stdClass();
            $parametros->RecordAsistenciaSige->AnioEscolar = intval($json->nombreperiodo);
            $parametros->RecordAsistenciaSige->RBD = intval($resultadocurso[0]['rbd']);
            $parametros->RecordAsistenciaSige->CodigoTipoEnsenanza = $resultadocurso[0]['idCodigoTipo'];
            $parametros->RecordAsistenciaSige->CodigoGrado = $resultadocurso[0]['idGrado'];
            $parametros->RecordAsistenciaSige->FechaAsistencia = $asist[0]['fechaAsistencia'];
            $parametros->RecordAsistenciaSige->Cursos = new stdClass();
            $parametros->RecordAsistenciaSige->Cursos->Curso = new stdClass();
            $parametros->RecordAsistenciaSige->Cursos->Curso->LetraCurso = $resultadocurso[0]['letra'];

            for ($i = 0; $i < count($asist); $i++) {
                $a = $asist[$i];
                $o = new stdClass();
                $o->Run = new stdClass();
                $o->numero = intval(substr($a['rutAlumno'], 0, -1));
                $o->dv = substr($a['rutAlumno'], -1);

                if ($a['valor'] == 1) {
                    $ausentes[] = $o;
                } else {
                    $presentes[] = $o;
                }
            }

            $parametros->RecordAsistenciaSige->Cursos->Curso->Presentes = $presentes;
            $parametros->RecordAsistenciaSige->Cursos->Curso->Ausentes = $ausentes;
            $parametros->Semilla = $sem->semilla;


            try {
                $client = new SoapClient($wsdl);

                $response = $client->__soapCall("addAsistencia", array($parametros), ['location' => 'http://w7app.mineduc.cl/WsApiMineduc/services/AsistenciaSigeSoapPort']);
                if (!empty($response->CodigoRespuestaAsistencia)) {
                    $date = new DateTime;
                    $fecharespuesta = $date->format('Y-m-d H:i:s');

                    $modelosige->agregarhistorialasistencia($sem->semilla, $response->CodigoRespuestaAsistencia, $fecharespuesta, $json->idasistencia);

                    if (!empty($response->CodigoEnvioAsistencia)) {
                        //Guardamos el Código de Asistencia
                        $usuario = new Zend_Session_Namespace('id');
                        $user = $usuario->id;
                        $modelosige->guardarcodigo($asist[0]['idAsistencia'], $fecharespuesta, $response->CodigoEnvioAsistencia, $user);
                        die(json_encode(["response" => 1, "status" => $response->CodigoRespuestaAsistencia]));
                    } else {
                        //Guardamos el Código de Asistencia
                        $usuario = new Zend_Session_Namespace('id');
                        $user = $usuario->id;
                        //$modelosige->guardarcodigo($asist[0]['idAsistencia'],$fecharespuesta,1234,$user);
                        die(json_encode(["response" => 1, "status" => $response->CodigoRespuestaAsistencia]));
                    }

                } else {
                    die(json_encode(["response" => 5]));
                }
            } catch (SoapFault $e) {
                print_r($e);
                die(json_encode(["response" => 6]));
            } catch (Exception $e) {
                print_r($e);
                die(json_encode(["response" => 6]));
            }
        } else {
            die(json_encode(["response" => 6]));
        }

    }


    public function reporteasistenciaAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $json = json_decode(file_get_contents('php://input'));
            if (is_null($json)) {
                die(json_encode(["response" => 0]));
            }
            $sem = $this->getsemillaonly($json->est, $json->periodo);
            $modelosige = new Application_Model_DbTable_Sige();
            //$resultado = $modelosige->get2($json->est, $json->periodo);

            if (is_null($json->codigo)) {
                die(json_encode(["response" => 3]));
            }
            //$sem = $resultado[0]['ValorSemilla'];
            if (is_null($sem)) {
                die(json_encode(["response" => 3]));
            }

            $curs = new Application_Model_DbTable_Cursos();
            $resultadocurso = $curs->getcursoactualtotal($json->idcurso);

            $wsdl = "http://w7app.mineduc.cl/WsApiMineduc/wsdl/AsistenciaSigeSoapPort.wsdl";
            $parametros = new stdClass();
            $parametros->RBD = intval($resultadocurso[0]['rbd']);
            $parametros->CodigoEnvioAsistencia = $json->codigo;
            $parametros->Semilla = $sem->semilla;


            try {
                $client = new SoapClient($wsdl);

                $response = $client->__soapCall("getReporteEnvioAsistencia", array($parametros), ['location' => 'http://w7app.mineduc.cl/WsApiMineduc/services/AsistenciaSigeSoapPort']);

                if (!empty($response->CodigoRespuestaReporteEnvioAsistencia)) {
                    $date = new DateTime;
                    $fecharespuesta = $date->format('Y-m-d H:i:s');

                    $modelosige->agregarhistorialasistenciareporte($sem->semilla, $response->CodigoRespuestaReporteEnvioAsistencia, $fecharespuesta, $json->idasistencia);
                    $modelosige->actualizarcodigo($json->idcodigo, $response->CodigoRespuestaReporteEnvioAsistencia);

                    die(json_encode(["response" => 1, "status" => $response->CodigoRespuestaReporteEnvioAsistencia]));

                } else {
                    die(json_encode(["response" => 5]));
                }
            } catch (SoapFault $e) {
                print_r($e);
                die(json_encode(["response" => 6]));
            } catch (Exception $e) {
                print_r($e);
                die(json_encode(["response" => 6]));
            }
        } else {
            die(json_encode(["response" => 6]));
        }

    }


}