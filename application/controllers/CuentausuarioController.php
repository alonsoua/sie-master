<?php

class CuentausuarioController extends Zend_Controller_Action
{

    public function init()
    {
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();
    }

    public function indexAction()
    {

        $table = new Application_Model_DbTable_Cuentas();
        $establecimiento = new Zend_Session_Namespace('establecimiento');
        $envia = $establecimiento->establecimiento;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;


        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;
        if ($rol == '4' || $rol == '3' || $rol == '6') {

            $lista = $table->listar($idperiodo, $envia, 1);
            for ($i = 0; $i < count($lista); $i++) {
                $lista[$i]['ultima_conexion'] = $this->tiempo_fecha($lista[$i]['fechaIngreso']);

            }
            $this->view->dato = $lista;
        }
        if ($rol == '1') {
            $lista = $table->listartodo($idperiodo, 1);
            for ($i = 0; $i < count($lista); $i++) {
                $lista[$i]['ultima_conexion'] = $this->tiempo_fecha($lista[$i]['fechaIngreso']);

            }
            $this->view->dato = $lista;
        }

    }

    public function agregarAction()
    {

        $this->view->title = "Agregar Cuenta";
        $this->view->headTitle($this->view->title);
        $form = new Application_Form_Cuentas();
        $this->view->form = $form;

    }


    public function eliminarAction()
    {

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;


        if ($rol == 1 || $rol == 3) {


            $idcuenta = $this->_getParam('id', 0);
            $idest = $this->_getParam('ide', 0);
            $idrol = $this->_getParam('idr', 0);
            $modelcuenta = new Application_Model_DbTable_Cuentas();

            $modelcuenta->actualizarestadocuenta($idcuenta, 2, $idperiodo, $idest, $idrol);

        }

        $this->_helper->redirector('index');
    }

    public function getcursoAction()
    {
        $modelcurso = new Application_Model_DbTable_Cursos();
        $modelasignatura = new Application_Model_DbTable_Asignaturascursos();

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $results = $modelcurso->getcursojson($this->_getParam('id'), $this->_getParam('idp'),$idperiodo);
        //Tipos de Asignaturas 1=normal 2=taller 4=Pertenece a una combinada 5= Concepto
        $tipos = array('1', '2', '4', '5');

        $this->_helper->json($results);
    }

    public function getasignaturaAction()
    {
        $modelModelo = new Application_Model_DbTable_Asignaturaplanificaciones();
        $results = $modelModelo->getAsKeyValueJSONAsignaturap($this->_getParam('id'));
        $this->_helper->json($results);
    }


    public function guardausuarioAction()
    {

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();

            $cargo = new Zend_Session_Namespace('cargo');
            $rol = $cargo->cargo;

            if ($rol == 1 || $rol == 3) {
                $json = file_get_contents('php://input');
                $data = json_decode($json, true);

                $rut = trim($data[0]['nombre']);
                $caracteres = array(",", ".", "-");
                $rut = str_replace($caracteres, "", $rut);
                $usuario = new Application_Model_DbTable_Cuentas();
                $roles = new Application_Model_DbTable_Detallerolcuenta();
                $valida = $usuario->valida($rut);


                if (!$valida) {
                    echo Zend_Json::encode(array('response' => 'usuarioexiste'));
                } else {

                    $pass = base64_decode($data[0]['pass']);
                    $nombrereal = $data[0]['nombrereal'];
                    $paterno = $data[0]['paterno'];
                    $materno = $data[0]['materno'];
                    $correo = $data[0]['correo'];
                    $rbd = $data[0]['rbd'];
                    $cargo = $data[0]['cargo'];
                    $tipo = $data[0]['tipo'];
                    $periodo = $data[0]['periodo'];

                    $hash = password_hash($pass, PASSWORD_DEFAULT);

                    if ($cargo == '1' || $cargo == '2' || $cargo == '3' || $cargo == '4' || $cargo == '5' || $cargo == '6') {

                        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                        $db->beginTransaction();
                        try {

                            $usuario->agregar($rut, $hash, $correo, $nombrereal, $paterno, $materno);
                            $idcuenta = $usuario->getAdapter()->lastInsertId();

                            $roles->agregar($rbd, $cargo, $periodo, $idcuenta);
                            $idrol = $roles->getAdapter()->lastInsertId();

                            $db->commit();
                            echo Zend_Json::encode(array('redirect' => '/Cuentausuario'));
                        } catch (Exception $e) {
                            echo $e;
                            // Si hubo problemas. Enviamos  marcha atras
                            $db->rollBack();
                            echo Zend_Json::encode(array('response' => 'error'));
                        }
                    } else {
                        echo Zend_Json::encode(array('response' => 'error'));
                    }

                }
            }

        }
    }


    public function guardaeditarusuarioAction()
    {

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();

            $cargo = new Zend_Session_Namespace('cargo');
            $rol = $cargo->cargo;

            if ($rol == 1 || $rol == 3 || $rol == 6) {
                //guardamos los datos en $json recibidos de la funcion ajax
                $json = file_get_contents('php://input');
                $data = json_decode($json, true);


                $rut = trim($data[0]['nombre']);
                $caracteres = array(",", ".", "-");
                $rut = str_replace($caracteres, "", $rut);

                $idperiodo = $data[0]['periodo'];
                $idestablecimiento = $data[0]['rbd'];
                $cargo = $data[0]['cargo'];
                $tipo = $data[0]['tipo'];

                $detallecursocuenta = new Application_Model_DbTable_Detallecursocuenta();
                $detallerolcuenta = new Application_Model_DbTable_Detallerolcuenta();
                $modelcuenta = new Application_Model_DbTable_Cuentas();
                $datosrol = $detallerolcuenta->validauserrol($rut, $idperiodo, $cargo, $idestablecimiento, 1);

                if ($cargo == '1' || $cargo == '2' || $cargo == '3' || $cargo == '4' || $cargo == '5' || $cargo == '6') {

                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                    // Iniciamos la transaccion
                    $db->beginTransaction();

                    try {
                        if (!$datosrol) {
                            $detallerolcuenta->agregar($idestablecimiento, $cargo, $idperiodo, $rut);
                            $idrol = $detallerolcuenta->getAdapter()->lastInsertId();

                        } else {

                            $idrol = $datosrol[0]['id'];
                        }
                        if ($cargo == '2' && $tipo == '2') {
                            $detallecursocuenta->borrar($idrol);

                            if (count($data) > 0) {
                                for ($i = 0; $i < count($data); $i++) {
                                    $detallecursocuenta->agregar($data[$i]['curso'], serialize($data[$i]['asignaturas']), $idrol);
                                }
                            }

                        }

                        $nombres = $data[0]['nombrereal'];
                        $paterno = $data[0]['paterno'];
                        $materno = $data[0]['materno'];
                        $correo = $data[0]['correo'];

                        $modelcuenta->actualizardatos($rut, $nombres, $paterno, $materno, $correo);


                        $db->commit();
                        echo Zend_Json::encode(array('redirect' => '/Cuentausuario'));


                    } catch (Exception $e) {

                        // Si hubo problemas. Enviamos marcha atras
                        $db->rollBack();
                        //echo $e;
                        echo Zend_Json::encode(array('response' => 'error'));

                    }
                } else {
                    echo Zend_Json::encode(array('response' => 'error'));
                }
            }


        }
    }

    public function cambiarpassAction()
    {

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;
        $form = new Application_Form_Cambiarcontrasena();
        $form->removeElement('usuario');
        $modelocuenta = new Application_Model_DbTable_Cuentas();
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $id = $form->getValue('idCuenta');
                $password = $form->getValue('password');
                $verifica = $form->getValue('verifica');

                $err='';
                if (!empty($password)) {
                    if (strlen($password) < '8') {
                        $err .= "La Contraseña debe contener minimo 8 caractéres"."<br>";
                    } elseif (!preg_match("#[0-9]+#", $password)) {
                        $err .= "La Contraseña debe contener al menos un número"."<br>";
                    } elseif (!preg_match("#[A-Z]+#", $password)) {
                        $err .= "La Contraseña debe contener al menos una letra Mayúscula"."<br>";
                    } elseif (!preg_match("#[a-z]+#", $password)) {
                        $err .= "La Contraseña debe contener al menos una letra Minúscula"."<br>";
                    }
                }  else {
                    $err .= "Ingrese una Contraseña"."<br>";
                }
                if(strlen($err)>0){

                    $this->view->message=$err;

                }else{
                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                    $db->beginTransaction();

                    try {
                        $datosusuario = $modelocuenta->getusuario($id, $idperiodo);
                        if (password_verify($verifica, $datosusuario['contrasena'])) {
                            $modelocuenta->actualizar($id, password_hash($password, PASSWORD_DEFAULT));
                            $db->commit();
                            $this->_helper->redirector('logout', 'usuarios');

                        } else {
                            $err='Contraseña antigua incorrecta, vuelva a intentar';
                            $this->view->message=$err;
                        }
                    } catch (Exception $e) {

                        $db->rollBack();
                        $err='Error al actualizar, contacte al soporte técnico.';
                        $this->view->message=$err;


                    }
                }


            } else {

                var_dump('a1qqq');
                $form->populate($formData);
            }
        } else {

            $idcurso = $this->_getParam('id');
            $usuario = new Zend_Session_Namespace('id');
            $user = $usuario->id;

            $cargo = new Zend_Session_Namespace('cargo');
            $rol = $cargo->cargo;
            if ($idcurso > 0 && ($idcurso == $user) && ($rol == '2' || $rol == '3' || $rol == '6' || $rol == '4' || $rol == '1')) {

                $datos = $modelocuenta->getusuario($idcurso, $idperiodo);
                $form->populate($datos);

            } else {
                $err='Sin Autorización';
                $this->view->message=$err;

            }
        }

    }

    public function cambiarperiodoAction()
    {

        $form = new Application_Form_Cambiarperiodo();
        $recuperando2 = new Zend_Session_Namespace('nombreperiodo');
        $nombreperiodo = $recuperando2->nombreperiodo;
        $this->view->title = "Periodo Actual " . $nombreperiodo;
        $this->view->headTitle($this->view->title);
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $id = $form->getValue('periodo');


                $modelperiodo = new Application_Model_DbTable_Periodo();
                $periodoresultado = $modelperiodo->nombreperiodo($id);

                $periodos = new Zend_Session_Namespace('periodo');
                $periodos->periodo = $id;

                $recuperando2->nombreperiodo = $periodoresultado[0]['nombrePeriodo'];
                try {
                    $usuario = new Zend_Session_Namespace('id');
                    $user = $usuario->id;

                    $cuenta = new Application_Model_DbTable_Cuentas();
                    $resultadoCuenta = $cuenta->getusuarioid($user, $id);

                    $cargo = new Zend_Session_Namespace('cargo');
                    $cargo->cargo = $resultadoCuenta[0]->idRoles;

                    $cargorol = new Zend_Session_Namespace('idRol');
                    $cargorol->idRol = $resultadoCuenta[0]->id;

                    $rol = $resultadoCuenta[0]->idRoles;
                    $rbd = $resultadoCuenta[0]->idEstablecimiento;

                    if ($rol != '1') {
                        if (!empty($rbd) || $rbd != '0' || $rbd != 'NULL' || $rbd = !NULL) {
                            $establecimiento = new Zend_Session_Namespace('establecimiento');
                            $establecimiento->establecimiento = $rbd;


                            $nomb = new Application_Model_DbTable_Establecimiento();
                            $estab = $nomb->listarestablecimiento($rbd, $id);

                            $nombrestablecimiento = $estab[0]['nombreEstablecimiento'];
                            $nombrefinal = new Zend_Session_Namespace('nomb_establecimiento');
                            $nombrefinal->nom_establecimiento = $nombrestablecimiento;

                            $tipoev = $estab[0]['tipoEvaluacion'];
                            $ingreso = $estab[0]['ingresonota'];
                            $tipoevaluacion = new Zend_Session_Namespace('tipoevaluacion');
                            $tipoevaluacion->tipoevaluacion = $tipoev;

                            $ingresonota = new Zend_Session_Namespace('ingresonota');
                            $ingresonota->ingresonota = $ingreso;

                            $this->_helper->redirector('index', 'index');
                        }


                    }
                    if ($rol == '1') {

                        $establecimiento = new Zend_Session_Namespace('establecimiento');
                        $establecimiento->establecimiento = '0';

                        $nombrefinal = new Zend_Session_Namespace('nomb_establecimiento');
                        $nombrefinal->nom_establecimiento = 'Todos los Establecimientos';
                        $this->_helper->redirector('index', 'index');

                    }

                } catch (Exception $e) {
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al cambiar el periodo');
                    $this->view->assign('messages', $messages);
                }


            }

            else {
                $form->populate($formData);
            }
        }

    }

    public function agregaperiodoAction()
    {


        $this->view->title = "Agregar Periodo a Cuenta";
        $this->view->headTitle($this->view->title);

        $form = new Application_Form_Cuentasperiodo();
        $form->username->setAttrib('readonly', 'true');
        $form->nombre_real->setAttrib('readonly', 'true');
        $this->view->form = $form;

        $idcurso = $this->_getParam('idCuenta');
        $cortado = explode("-", $idcurso);

        if ($idcurso > 0) {
            $curso = new Application_Model_DbTable_Cuentas();
            $cursos = $curso->get($cortado[0]);

            $rbd = $cursos['Rbd_cuenta'];

            $rbds = new Application_Model_DbTable_Establecimiento();
            $rowset = $rbds->getestablecimiento($rbd);
            $form->rbd->clearMultiOptions();
            $form->rbd->addMultiOptions($rowset);
            $this->view->form = $form;
            $form->populate($cursos);

        }

    }

    public function getambitosAction()
    {
        $modelModelo = new Application_Model_DbTable_Ambito();
        $results = $modelModelo->getAmbitos($this->_getParam('id'));
        $this->_helper->json($results);
    }

    public function editarcuentaAction()
    {
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $this->view->title = "Editar Cuenta";

        $this->view->headTitle($this->view->title);
        //creo el formulario
        $form = new Application_Form_Cuentas();
        $form->removeElement('password');
        $form->usuario->setAttrib('readonly', 'true');

        $this->view->form = $form;
        $idcuenta = $this->_getParam('idCuenta');
        $idrol = $this->_getParam('idRol');
        if ($idcuenta > 0) {
            $modelcuenta = new Application_Model_DbTable_Cuentas();
            $datoscuenta = $modelcuenta->getusuariorol($idcuenta, $idperiodo, $idrol);


            $rbd = $datoscuenta[0]['idEstablecimiento'];
            if (empty($rbd)) {


                $rbds = new Application_Model_DbTable_Establecimiento();
                $rowset = $rbds->getestablecimientos();
                $form->idEstablecimiento->clearMultiOptions();
                $form->idEstablecimiento->addMultiOptions($rowset);
                $form->periodo->setValue($idperiodo);
                $this->view->form = $form;

            } else {

                $rbds = new Application_Model_DbTable_Establecimiento();
                $rowset = $rbds->getestablecimiento($rbd);
                $form->idEstablecimiento->clearMultiOptions();
                $form->idEstablecimiento->addMultiOptions($rowset);
                $form->periodo->setValue($idperiodo);
                $form->cargo->setValue($datoscuenta[0]['idRoles']);
                $this->view->form = $form;
            }

            $form->populate($datoscuenta[0]);

        }

    }

    public function getnivelusuarioAction()
    {
        $modelorol = new Application_Model_DbTable_Detallerolcuenta();
        $modelocurso = new Application_Model_DbTable_Detallecursocuenta();

        $idusuario = $this->_getParam('id');
        $idperiodo = $this->_getParam('idp');
        $idestablecimiento = $this->_getParam('ide');

        $resultadorol = $modelorol->getrolusuario($idusuario, $idperiodo, $idestablecimiento);
        if ($resultadorol) {
            $resultadocurso = $modelocurso->listarcursousuariojson($resultadorol[0]['id']);
            for ($i = 0; $i < count($resultadocurso); $i++) {
                $resultadocurso[$i]['asignaturas'] = unserialize($resultadocurso[$i]['asignaturasLista']);

            }
            $this->_helper->json($resultadocurso);
        } else {

            die(json_encode(["response" => 0]));

        }


    }

    public function getcursousuarioAction()
    {
        $modelModelo = new Application_Model_DbTable_Detallecursocuenta();
        $id = $this->_getParam('id');
        $results = $modelModelo->listar($id);
        $this->_helper->json($results);
    }

    public function getasignaturausuarioAction()
    {
        $modelModelo = new Application_Model_DbTable_Detalleasignaturacuenta();
        $id = $this->_getParam('id');
        $results = $modelModelo->listar($id);
        $this->_helper->json($results);
    }

    public function getasignaturausuariosepAction()
    {
        $modelModelo = new Application_Model_DbTable_Detalleasignaturacuenta();
        $id = $this->_getParam('id');
        $results = $modelModelo->listarsep($id);
        $this->_helper->json($results);
    }

    public function getasignaturausuariomediaAction()
    {
        $modelModelo = new Application_Model_DbTable_Detalleasignaturacuenta();
        $id = $this->_getParam('id');
        $results = $modelModelo->listarmedia($id);
        $this->_helper->json($results);
    }

    public function eliminadetalleasignaturaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $modelModelo = new Application_Model_DbTable_Detalleasignaturacuenta();
        $results = $modelModelo->borrar($this->_getParam('id'));
        if ($results) {
            echo Zend_Json::encode(array('response' => 'ok', 'response2' => $this->_getParam('id')));
        } else {
            echo Zend_Json::encode(array('response' => 'error'));

        }

    }

    public function eliminadetallecursoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $id_borrar = $this->_getParam('id');
        $modelModelo2 = new Application_Model_DbTable_Detalleasignaturacuenta();
        $results2 = $modelModelo2->borrarporcurso($id_borrar);
        if ($results2) {
            $modelModelo = new Application_Model_DbTable_Detallecursocuenta();
            $results = $modelModelo->borrar($id_borrar);
            if ($results) {
                echo Zend_Json::encode(array('response' => 'ok', 'response2' => $this->_getParam('id')));
            } else {
                echo Zend_Json::encode(array('response' => 'error'));

            }
        } else {
            echo Zend_Json::encode(array('response' => 'error'));
        }

    }

    public function eliminadetallecursoinAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $id_borrar = $this->_getParam('id');

        $modelModelo = new Application_Model_DbTable_Detallecursocuenta();
        $results = $modelModelo->borrar($id_borrar);
        if ($results) {
            echo Zend_Json::encode(array('response' => 'ok', 'response2' => $this->_getParam('id')));
        } else {
            echo Zend_Json::encode(array('response' => 'error'));

        }

    }

    public function eliminadetallenivelAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $modelModelo = new Application_Model_DbTable_Detallenivelcuenta();
        $results = $modelModelo->borrarporid($this->_getParam('id'));
        if ($results) {
            echo Zend_Json::encode(array('response' => 'ok', 'response2' => $this->_getParam('id')));
        } else {
            echo Zend_Json::encode(array('response' => 'error'));

        }

    }


    public function restablecerAction()
    {

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;
        if ($rol == '1' || $rol == '3' || $rol == '6' || $rol == '4') {

            $modelocuenta = new Application_Model_DbTable_Cuentas ();
            $form = new Application_Form_Cambiarcontrasena();
            $form->removeElement('verifica');
            $form->removeElement('usuario');
            $form->submit->setLabel('Actualizar');
            $this->view->form = $form;

            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {
                    $id = $form->getValue('idCuenta');
                    $password = $form->getValue('password');

                    $err = '';
                    if (!empty($password)) {
                        if (strlen($password) < '8') {
                            $err .= "La Contraseña debe contener minimo 8 caractéres" . "<br>";
                        } elseif (!preg_match("#[0-9]+#", $password)) {
                            $err .= "La Contraseña debe contener al menos un número" . "<br>";
                        } elseif (!preg_match("#[A-Z]+#", $password)) {
                            $err .= "La Contraseña debe contener al menos una letra Mayúscula" . "<br>";
                        } elseif (!preg_match("#[a-z]+#", $password)) {
                            $err .= "La Contraseña debe contener al menos una letra Minúscula" . "<br>";
                        }
                    } else {
                        $err .= "Ingrese una Contraseña" . "<br>";
                    }
                    if (strlen($err) > 0) {

                        $this->view->message = $err;

                    } else {
                        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                        $db->beginTransaction();

                        try {
                            $modelocuenta->actualizar($id, password_hash($password, PASSWORD_DEFAULT));
                            $db->commit();
                            $this->_helper->redirector('index');
                        } catch (Exception $e) {
                            $db->rollBack();
                            $err = 'Error al actualizar, contacte al soporte técnico.';
                            $this->view->message = $err;

                        }
                    }


                } else {

                    $form->populate($formData);
                }
            } else {

                $idcurso = $this->_getParam('idCuenta');
                if ($idcurso > 0 && ($rol == '1' || $rol == '3' || $rol == '4' || $rol == '6')) {
                    $datos = $modelocuenta->getusuariocon($idcurso);
                    $form->populate($datos);

                } else {
                    $err = 'Sin Autorización.';
                    $this->view->message = $err;
                }
            }
        } else {
            $err = 'Sin Autorización.';
            $this->view->message = $err;
        }
    }


    public function agregarrolAction()
    {

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $this->view->title = "Agregar Rol";
        $this->view->headTitle($this->view->title);
        $form = new Application_Form_Cuentasrol();
        $form->usuario->setAttrib('readonly', 'true');
        $form->nombrescuenta->setAttrib('readonly', 'true');
        $form->paternocuenta->setAttrib('readonly', 'true');
        $form->maternocuenta->setAttrib('readonly', 'true');

        $this->view->form = $form;

        $id = $this->_getParam('idCuenta');
        if ($id > 0) {

            $modelocuenta = new Application_Model_DbTable_Cuentas();
            $datoscuenta = $modelocuenta->getusuario($id, $idperiodo);
            $form->populate($datoscuenta);

        }

    }

    public function getrolAction()
    {
        $modelModelo = new Application_Model_DbTable_Detallerolcuenta();
        $id = $this->_getParam('id');
        $detalle = explode('-', $id);
        $idcuenta = $detalle[0];
        $idrol = $detalle[1];
        $idestablecimiento = $detalle[2];
        $results = $modelModelo->validarrolusuario($idcuenta, $idrol, $idestablecimiento);
        $this->_helper->json($results);
    }

    public function guardarolusuarioAction()
    {

        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();

            $cargo = new Zend_Session_Namespace('cargo');
            $rol = $cargo->cargo;

            if ($rol == 1 || $rol == 3) {
                $json = file_get_contents('php://input');
                $data = json_decode($json, true);

                $nombreusuario = $data[0]['nombre'];
                $usuario = new Application_Model_DbTable_Cuentas();
                $roles = new Application_Model_DbTable_Detallerolcuenta();
                $valida = $usuario->valida($nombreusuario);

                if (!$valida) {
                    echo Zend_Json::encode(array('response' => 'usuarioexiste'));
                } else {

                    $idusuario = $data[0]['nombre'];

                    $rbd = $data[0]['rbd'];
                    $cargo = $data[0]['cargo'];
                    $tipo = $data[0]['tipo'];
                    $periodo = $data[0]['periodo'];
                    $detallecurso = new Application_Model_DbTable_Detallecursocuenta();

                    if ($cargo == '1' || $cargo == '2' || $cargo == '3' || $cargo == '4' || $cargo == '5' || $cargo == '6') {
                        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                        // Iniciamos la transaccion
                        $db->beginTransaction();
                        try {

                            //Validamos que si el rol existe y se encuentra activo
                            $datosrolactivo = $roles->validauserrol($idusuario, $periodo, $cargo, $rbd, 1);

                            if ($datosrolactivo) {

                                echo Zend_Json::encode(array('response' => 'error'));
                            } else {


                                //Validamos si el rol existe y esta eliminado
                                $datosrol = $roles->validauserrol($idusuario, $periodo, $cargo, $rbd, 2);

                                if ($datosrol) {

                                    $roles->actualizar($datosrol[0]['id']);
                                    $idrol = $datosrol[0]['id'];
                                } else {

                                    $roles->agregar($rbd, $cargo, $periodo, $idusuario);
                                    $idrol = $roles->getAdapter()->lastInsertId();
                                }


                                if ($cargo == '2' && $tipo == '2') {

                                    for ($i = 0; $i < count($data); $i++) {

                                        $detallecurso->agregar($data[$i]['curso'], serialize($data[$i]['asignaturas']), $idrol);

                                    }

                                }
                                // Sino hubo ningun inconveniente hacemos un commit
                                $db->commit();
                                echo Zend_Json::encode(array('redirect' => '/Cuentausuario'));

                            }


                        } catch (Exception $e) {
                            // Si hubo problemas. Enviamos marcha atras

                            $db->rollBack();
                            echo Zend_Json::encode(array('response' => 'error'));
                        }
                    } else {
                        echo Zend_Json::encode(array('response' => 'error'));
                    }

                }
            }

        }
    }

    function tiempo_fecha($fecha)
    {
        $timestamp = strtotime($fecha);
        $diff = time() - (int)$timestamp;

        if ($diff == 0)
            return 'Ahora';

        if ($diff > 604800)
            return date("d-m-Y (H:m:s)", $timestamp);

        $intervals = array
        (
            $diff < 604800 => array('día', 86400),
            $diff < 86400 => array('hora', 3600),
            $diff < 3600 => array('minuto', 60),
            $diff < 60 => array('segundo', 1)
        );

        $value = floor($diff / $intervals[1][1]);
        return 'hace ' . $value . ' ' . $intervals[1][0] . ($value > 1 ? 's' : '');
    }

}
