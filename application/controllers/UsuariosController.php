<?php
require_once 'Zend/Controller/Action.php';
require_once 'Zend/Auth.php';
require_once 'Zend/Auth/Adapter/DbTable.php';

class UsuariosController extends Zend_Controller_Action
{


    public function init()
    {
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();
    }

    public function indexAction()
    {

        $this->_helper->layout->disableLayout();
        $auth = Zend_Auth::getInstance();

        $request = $this->getRequest();
        $user = $auth->getIdentity();
        $usuario = $user->usuario;


        $nombre_real = $user->nombrescuenta;
        $paterno = $user->paternocuenta;
        $materno = $user->maternocuenta;
        $id = $user->idCuenta;
        $nombreusuario = new Zend_Session_Namespace('usuario');
        $nombreusuario->mivalor = $nombre_real . ' ' . $paterno . ' ' . $materno;


        $iduser = new Zend_Session_Namespace('id');
        $iduser->id = $id;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;



        $cuenta = new Application_Model_DbTable_Cuentas();
        if(empty($id)){
            $this->_helper->redirector('loginform');
            exit();
        }
        $resultadoCuenta = $cuenta->getusuarioid($id, $idperiodo);
        $datos_cuenta_principal=$cuenta->get($id);


        $cargo = new Zend_Session_Namespace('cargo');
        $cargo->cargo = $resultadoCuenta[0]->idRoles;

        $cargorol = new Zend_Session_Namespace('idRol');
        $cargorol->idRol = $resultadoCuenta[0]->id;

        $rol = $resultadoCuenta[0]->idRoles;

        $idest = $resultadoCuenta[0]->idEstablecimiento;
        $rbd = $resultadoCuenta[0]->rbd;

        if($datos_cuenta_principal['update_password']==1){
            $this->_helper->redirector('cambiarpass');

        }
        $resultadoidentificador = $cuenta->getidentificador();
        $identificadors = new Zend_Session_Namespace('identificador');
        $identificadors->identificador = $resultadoidentificador[0]['identificador'];

        if ($resultadoCuenta[0]['estadoCuenta'] != 1 || count($resultadoidentificador) == 0) {
            $this->_helper->redirector('loginform');
        }

        if (!$auth->hasIdentity()) {
            $this->_helper->redirector('loginform');
        } else {

            $iduser = new Zend_Session_Namespace('id');
            $iduser->id = $id;
            $cuenta = new Application_Model_DbTable_Cuentas();
            $modelrol = new Application_Model_DbTable_Detallerolcuenta();
            $resultadoCuenta = $cuenta->getusuarioid($id, $idperiodo);
            $cuenta->actualizafecha(date("Y-m-d H:i:s"), $id);
            if (count($resultadoCuenta) > 1) {
                $resultadoest = $cuenta->getestablecimientocuenta($id,$idperiodo);
                //mostramos la vista para que seleccione el establecmiento
                $this->view->assign('action', $request->getBaseURL() . "/usuarios/guardaconf");
                $this->view->assign('title', 'Seleccione Establecimiento y Cargo');
                $this->view->dato = $resultadoest;

            } else {
                $rol = $resultadoCuenta[0]->idRoles;
                $rbd = $resultadoCuenta[0]->idEstablecimiento;

                if ($rol > '1') {
                    if (!empty($rbd) || $rbd != '0' || $rbd != 'NULL' || $rbd = !null) {

                        $establecimiento = new Zend_Session_Namespace('establecimiento');
                        $establecimiento->establecimiento = $idest;

                        $rbdest = new Zend_Session_Namespace('establecimientorbd');
                        $rbdest->establecimientorbd = $rbd;

                        $resultadorol = $modelrol->getnombrerol($resultadoCuenta[0]->idRoles);

                        $establecimiento = new Zend_Session_Namespace('establecimiento');
                        $establecimiento->establecimiento = $rbd;

                        $periodo = new Zend_Session_Namespace('periodo');
                        $idperiodo = $periodo->periodo;

                        $nomb = new Application_Model_DbTable_Establecimiento();
                        $estab = $nomb->listarestablecimiento($rbd, $idperiodo);

                        $nombrestablecimiento = $estab[0]['nombreEstablecimiento'];
                        $nombrefinal = new Zend_Session_Namespace('nomb_establecimiento');
                        $nombrefinal->nom_establecimiento = $nombrestablecimiento;

                        $nombrerbd = new Zend_Session_Namespace('nombrerbd');
                        $nombrerbd->nombrerbd = $estab[0]['rbd'];

                        $tipoev = $estab[0]['tipoModalidad'];
                        $ingreso = $estab[0]['ingresonota'];
                        $tipoevaluacion = new Zend_Session_Namespace('tipoevaluacion');
                        $tipoevaluacion->tipoevaluacion = $tipoev;

                        $ingresonota = new Zend_Session_Namespace('ingresonota');
                        $ingresonota->ingresonota = $ingreso;

                        $cambio = new Zend_Session_Namespace('cambio');
                        $cambio->cambio = 0;

                        $nombrerol = new Zend_Session_Namespace('nombrerol');
                        $nombrerol->nombrerol = $resultadorol[0]['nombreRol'];

                        $cargos = new Zend_Session_Namespace('cargo');
                        $cargos->cargo = $resultadoCuenta[0]['idRoles'];

                        $cargorol = new Zend_Session_Namespace('idRol');
                        $cargorol->idRol = $resultadoCuenta[0]->id;

                        $app = new Zend_Session_Namespace('activarapp');
                        $app->activarapp = $estab[0]['activarapp'];

                        $this->_redirect('index');

                    }

                }
                if ($rol == '1') {

                    $establecimiento = new Zend_Session_Namespace('establecimiento');
                    $establecimiento->establecimiento = '0';

                    $nombrerbd = new Zend_Session_Namespace('nombrerbd');
                    $nombrerbd->nombrerbd = 'Todos';

                    $nombrefinal = new Zend_Session_Namespace('nomb_establecimiento');
                    $nombrefinal->nom_establecimiento = 'Todos los Establecimientos';

                    $cambio = new Zend_Session_Namespace('cambio');
                    $cambio->cambio = 0;

                    $nombrerol = new Zend_Session_Namespace('nombrerol');
                    $nombrerol->nombrerol = 'Administrador Daem';

                    $cargos = new Zend_Session_Namespace('cargo');
                    $cargos->cargo = '1';

                    $cargorol = new Zend_Session_Namespace('idRol');
                    $cargorol->cargorol = $resultadoCuenta[0]->id;

                    $this->_redirect('index');

                }
            }

        }
    }


    public function loginformAction()
    {
        $this->_helper->layout->disableLayout();
        $request = $this->getRequest();
        $this->view->assign('action', $request->getBaseURL() . "/usuarios/auth");
        $table = new Application_Model_DbTable_Periodo();
        $this->view->dato = $table->listar();

    }

    public function authAction()
    {
        $request = $this->getRequest();
        $uname = $request->getParam('username');
        $paswd = $request->getParam('password');
        $periodo = $request->getParam('periodo');
        if (empty($uname) || empty($paswd) || empty($periodo)) {
            $this->_helper->redirector('loginform');
            die();
        }
        $registry = Zend_Registry::getInstance();
        $auth = Zend_Auth::getInstance();

        $authAdapter = new Zend_Auth_Adapter_DbTable();
        $authAdapter->setTableName('cuentasUsuario')
            ->setIdentityColumn('usuario')
            ->setCredentialColumn('contrasena');

        $uname = $request->getParam('username');
        $paswd = $request->getParam('password');
        $periodo = $request->getParam('periodo');

        $modelperiodo = new Application_Model_DbTable_Periodo();
        $periodoresultado = $modelperiodo->nombreperiodo($periodo);

        $periodosnombre = new Zend_Session_Namespace('nombreperiodo');
        $periodosnombre->nombreperiodo = $periodoresultado[0]['nombrePeriodo'];

        $periodos = new Zend_Session_Namespace('periodo');
        $periodos->periodo = $periodo;

        $authAdapter->setIdentity($uname);
        $authAdapter->setCredential(0);

        $result = $auth->authenticate($authAdapter);

        if ($result->isValid()) {
            $data = $authAdapter->getResultRowObject();
            if ($data) {
                if ($data->update_password == 1) {
                    if (md5($paswd) === $data->contrasena) {
                        $data->contrasena = null;
                        $auth->getStorage()->write($data);
                        $authSession = new Zend_Session_Namespace('Zend_Auth');
                        $authSession->setExpirationSeconds(86400);
                        $this->_helper->redirector('index');
                    } else {
                        $auth->clearIdentity();
                        $this->_helper->redirector('loginform');
                    }

                } elseif ($data->update_password == 2) {
                    if (password_verify($paswd, $data->contrasena)) {
                        $data->contrasena = null;
                        $auth->getStorage()->write($data);
                        $authSession = new Zend_Session_Namespace('Zend_Auth');
                        $authSession->setExpirationSeconds(86400);
                        $this->_helper->redirector('index');

                    } else {
                        $auth->clearIdentity();
                        $this->_helper->redirector('loginform');
                    }
                }

            } else {
                $this->_helper->redirector('loginform');
            }

        } else {

            $this->_redirect('/usuarios/loginform');

        }

    }


    public function logoutAction()
    {
        $auth = Zend_Auth::getInstance();

        if (!$auth->hasIdentity()) {
            $this->_redirect('/usuarios/loginform');
        }

        $auth->clearIdentity();
        $this->_redirect('/usuarios');
    }


    public function cargarrolAction()
    {
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $auth = Zend_Auth::getInstance();
        $user = $auth->getIdentity();
        $idusuario = $user->idCuenta;
        $modelModelo = new Application_Model_DbTable_Detallerolcuenta();
        $idestablecimiento = $this->_getParam('id');
        $results = $modelModelo->getrolselect($idusuario, $idestablecimiento, $idperiodo);
        $this->_helper->json($results);
    }

    public function guardaconfAction()
    {

        $auth = Zend_Auth::getInstance();
        $user = $auth->getIdentity();

        if (!$auth->hasIdentity()) {
            $this->_redirect('/usuarios/loginform');
        } else {
            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;

            $request = $this->getRequest();
            $idestablecimiento = $request->getParam('establecimiento');
            $idrol = $request->getParam('cargo');
            $modelrol = new Application_Model_DbTable_Detallerolcuenta();
            $modeloest = new Application_Model_DbTable_Establecimiento();
            if (!empty($idestablecimiento) && !empty($idrol)) {
                $datoscuenta = $modelrol->get($idrol);

                $cargo = new Zend_Session_Namespace('cargo');
                $cargo->cargo = $datoscuenta[0]['idRol'];

                $cargorol = new Zend_Session_Namespace('idRol');
                $cargorol->idRol = $idrol;


                if ($idrol > '1') {
                    if (!empty($idestablecimiento) || $idestablecimiento != '0' || $idestablecimiento != 'NULL' || $idestablecimiento = !null) {

                        $establecimiento = new Zend_Session_Namespace('establecimiento');
                        $establecimiento->establecimiento = $idestablecimiento;

                        $resultadorol = $modelrol->getnombrerol($datoscuenta[0]['idRol']);
                        $rol = new Zend_Session_Namespace('nombrerol');
                        $rol->nombrerol = $resultadorol[0]['nombreRol'];

                        $nomb = new Application_Model_DbTable_Establecimiento();

                        $estab = $nomb->listarestablecimiento($idestablecimiento, $idperiodo);
                        $nombrestablecimiento = $estab[0]['nombreEstablecimiento'];

                        $nombrerbd = new Zend_Session_Namespace('nombrerbd');
                        $nombrerbd->nombrerbd = $estab[0]['rbd'];

                        $tipoev = $estab[0]['tipoModalidad'];
                        $ingreso = $estab[0]['ingresonota'];
                        $tipoevaluacion = new Zend_Session_Namespace('tipoevaluacion');
                        $tipoevaluacion->tipoevaluacion = $tipoev;

                        $ingresonota = new Zend_Session_Namespace('ingresonota');
                        $ingresonota->ingresonota = $ingreso;

                        $nombrefinal = new Zend_Session_Namespace('nomb_establecimiento');
                        $nombrefinal->nom_establecimiento = $nombrestablecimiento;

                        $cambio = new Zend_Session_Namespace('cambio');
                        $cambio->cambio = 1;

                        $cargos = new Zend_Session_Namespace('cargo');
                        $cargos->cargo = $datoscuenta[0]['idRol'];

                        $cargorol = new Zend_Session_Namespace('idRol');
                        $cargorol->idRol = $datoscuenta[0]['id'];

                        $app = new Zend_Session_Namespace('activarapp');
                        $app->activarapp = $estab[0]['activarapp'];

                        $this->_redirect('index');
                    }

                }
                if ($idrol == '1') {

                    $establecimiento = new Zend_Session_Namespace('establecimiento');
                    $establecimiento->establecimiento = '0';

                    $nombrerbd = new Zend_Session_Namespace('nombrerbd');
                    $nombrerbd->nombrerbd = 'Todos';

                    $nombrefinal = new Zend_Session_Namespace('nomb_establecimiento');
                    $nombrefinal->nom_establecimiento = 'Todos los Establecimientos';

                    $cambio = new Zend_Session_Namespace('cambio');
                    $cambio->cambio = 1;

                    $rol = new Zend_Session_Namespace('nombrerol');
                    $rol->nombrerol = 'Administrador Daem';

                    $cargos = new Zend_Session_Namespace('cargo');
                    $cargos->cargo = '1';

                    $this->_redirect('index');

                }

            } else {

                $this->_redirect('/usuarios');
            }

        }

    }




    public function cambiarpassAction()
    {
        $this->_helper->layout->disableLayout();

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;
        $form = new Application_Form_Cambiarcontrasena();
        $form->removeElement('verifica');
        $form->removeElement('idCuenta');
        $form->removeElement('usuario');
        $form->submit->setLabel('Actualizar');
        $this->view->form = $form;

        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;

        $modelocuenta = new Application_Model_DbTable_Cuentas();

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
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
                        $modelocuenta->actualizar($user, password_hash($password,PASSWORD_DEFAULT));
                        $db->commit();
                        $auth = Zend_Auth::getInstance();
                        $auth->clearIdentity();
                        $this->_redirect('/usuarios');
                    } catch (Exception $e) {

                        $db->rollBack();
                        $err='Error al actualizar, contacte al soporte técnico.';
                        $this->view->message=$err;
                    }
                }

            }
            else {
                $form->populate($formData);
            }
        } else {

            $cargo = new Zend_Session_Namespace('cargo');
            $rol = $cargo->cargo;

            if ($rol == '2' || $rol == '3' || $rol == '6' || $rol == '4' || $rol == '1') {

                $datos_usuario = $modelocuenta->getusuariorol($user, $idperiodo,$rol);
                $form->populate($datos_usuario);

            } else {
                $err='Sin Autorización';
                $this->view->message=$err;

            }

        }

    }


}

