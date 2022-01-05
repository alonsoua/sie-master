<?php
require 'vendor/autoload.php';
require 'Cryptor.php';

class MensajeController extends Zend_Controller_Action
{

    public function init()
    {
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();
    }

    public function indexAction()
    {
        $table = new Application_Model_DbTable_Establecimiento();
        $this->view->dato = $table->listar();


    }

    public function entradaAction()
    {

        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;

        $usuariorol = new Zend_Session_Namespace('idRol');
        $userrol = $usuariorol->idRol;

        $est = new Zend_Session_Namespace('establecimiento');
        $idestablecimiento = $est->establecimiento;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $modelmensaje = new Application_Model_DbTable_Mensaje();


        if ($rol == 1) {
            $form = new Application_Form_Mensajes();
            $this->view->form = $form;
        } else {
            $resultadovalidacion = false;
            //1=nuevo 2=respuesta
            $resultadovalidacion = $this->validarconfiguracionAction($rol, $idestablecimiento, 1, 0);
            if (!$resultadovalidacion) {
                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No Tiene Permisos para esta accion');
                $this->view->assign('messagesnuevo', $messages);
            }
        }


        $mensajes = $modelmensaje->getmensajes($userrol, $idperiodo, array('1', '2'));
        $mensajesnoleidos = $modelmensaje->getmensajes($userrol, $idperiodo, array('1'));
        $totalmensajes = count($mensajes);
        $totalmensajesnoleidos = count($mensajesnoleidos);


        $cryptor = new \Chirp\Cryptor();

        for ($i = 0; $i < $totalmensajes; $i++) {
            $mensajes[$i]['fecha'] = strtotime($mensajes[$i]['fecha']);

            $mensajes[$i]['idMensaje'] = $cryptor->encrypt($mensajes[$i]['idMensaje']);
        }

        $this->view->mensajes = $mensajes;
        $this->view->total = $totalmensajes;
        $this->view->totalnoleido = $totalmensajesnoleidos;


    }

    public function enviadoAction()
    {
        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;

        $usuariorol = new Zend_Session_Namespace('idRol');
        $userrol = $usuariorol->idRol;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $est = new Zend_Session_Namespace('establecimiento');
        $idestablecimiento = $est->establecimiento;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        try {

            $modelmensaje = new Application_Model_DbTable_Mensaje();

            if ($rol == 1) {
                $form = new Application_Form_Mensajes();
                $this->view->form = $form;
            } else {
                //Obtenemos las configurciones de los mensajes
                $resultadovalidacion = false;
                //1=nuevo 2=respuesta
                $resultadovalidacion = $this->validarconfiguracionAction($rol, $idestablecimiento, 1, 0);
                if (!$resultadovalidacion) {
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No Tiene Permisos para esta accion');
                    $this->view->assign('messagesnuevo', $messages);
                }
            }

            $mensajes = $modelmensaje->getmensajesenviados($userrol, $idperiodo);
            $totalmensajes = count($mensajes);


            $cryptor = new \Chirp\Cryptor();

            for ($i = 0; $i < $totalmensajes; $i++) {
                $mensajes[$i]['fecha'] = strtotime($mensajes[$i]['fecha']);
                $mensajes[$i]['receptores'] = $modelmensaje->getreceptores($mensajes[$i]['idMensaje'], $idperiodo);
                //encriptamos el id del mensaje
                $mensajes[$i]['idMensaje'] = $cryptor->encrypt($mensajes[$i]['idMensaje']);

            }


            $mensajesnoleidos = $modelmensaje->getmensajes($userrol, $idperiodo, array('1'));
            $totalmensajesnoleidos = count($mensajesnoleidos);
            $this->view->totalnoleido = $totalmensajesnoleidos;


            $this->view->mensajes = $mensajes;
            $this->view->total = $totalmensajes;


        } catch (Exception $e) {
            echo $e;

            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Error');
            $this->view->assign('messages', $messages);
        }


    }

    public function nuevoAction()
    {

        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;

        $usuariorol = new Zend_Session_Namespace('idRol');
        $userrol = $usuariorol->idRol;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $est = new Zend_Session_Namespace('establecimiento');
        $idestablecimiento = $est->establecimiento;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $modelmensaje = new Application_Model_DbTable_Mensaje();

        if ($rol == 1) {
            $form = new Application_Form_Mensajes();
            $this->view->form = $form;
        } else {
            //Obtenemos las configurciones de los mensajes
            $resultadovalidacion = false;
            //1=nuevo 2=respuesta
            $resultadovalidacion = $this->validarconfiguracionAction($rol, $idestablecimiento, 1, 0);
            if ($resultadovalidacion) {
                $form = new Application_Form_Mensajes();
                $this->view->form = $form;
            } else {
                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No Tiene Permisos para esta accion');
                $this->view->assign('messagesnuevo', $messages);
            }
        }


        $mensajesnoleidos = $modelmensaje->getmensajes($userrol, $idperiodo, array('1'));
        $totalmensajesnoleidos = count($mensajesnoleidos);
        $this->view->totalnoleido = $totalmensajesnoleidos;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                $establecimientos = $form->getValue('idEstablecimiento');
                $roles = $form->getValue('idRol');
                $destinatarios = $form->getValue('idCuenta');
                $asunto = $form->getValue('asunto');
                $mensaje = $form->getValue('mensaje');

                if ($destinatarios == null) {
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No Existen Destinatarios');
                    $this->view->assign('messages', $messages);
                } else {
                    if ($roles == null) {
                        $roles[0] = 'allr';
                    }

                    if ($establecimientos == null) {
                        $establecimientos[0] = 'all';
                    }


                    $fecha_creacion = date_create()->format('Y-m-d H:i:s');
                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                    // Iniciamos la transaccion
                    $db->beginTransaction();
                    $fecha_leido = '0000-00-00 00:00:00';
                    $cryptor = new \Chirp\Cryptor();

                    try {

                        //Agreamos el cuerpo del mensaje
                        $modelmensaje->agregarmensaje($userrol, $asunto, $mensaje, $fecha_creacion, 1, $idperiodo);
                        $idmensaje = $modelmensaje->getAdapter()->lastInsertId();


                        //Todos los Usuarios
                        if ($establecimientos[0] == 'all' && (count($destinatarios) > 0 && $destinatarios[0] == 'alle') && $roles[0] == 'allr') {
                            //buscamos todas las cuentas activas
                            $datosusuarios = $modelmensaje->listarusuariotodo($idperiodo, $userrol, array(1, 2, 3, 4, 5, 6));
                            $largo = count($datosusuarios);
                            //agregamos a la tabla de receptores

                            if ($largo > 0) {
                                for ($i = 0; $i < $largo; $i++) {

                                    $modelmensaje->agregarreceptores($datosusuarios[$i]['id'], 1, $fecha_leido, $idmensaje);

                                }
                            } else {
                                $db->rollBack();
                                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No Existen Destinatarios');
                                $this->view->assign('messages', $messages);
                            }


                        } //Solo Destinatarios
                        elseif (count($destinatarios) > 0 && $destinatarios[0] != 'alle') {
                            $largo = count($destinatarios);
                            //agregamos a la tabla de receptores
                            if ($largo > 0) {


                                for ($i = 0; $i < $largo; $i++) {


                                    $destinatarios[$i] = $cryptor->decrypt($destinatarios[$i]);
                                    $modelmensaje->agregarreceptores($destinatarios[$i], 1, $fecha_leido, $idmensaje);

                                }
                            } else {
                                $db->rollBack();
                                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No Existen Destinatarios');
                                $this->view->assign('messages', $messages);
                            }

                        } //Envia a mensaje a todos los usuarios de todos los establecimiento seleccionado el rol(ejemplo envia un mensaje a todos los directores de los establecimientos)
                        elseif ($establecimientos[0] == 'all' && (count($destinatarios) > 0 && $destinatarios[0] == 'alle') && $roles[0] != 'allr') {
                            //buscamos todas las cuentas activas

                            $largorol = count($roles);
                            if ($largorol > 0) {
                                for ($i = 0; $i < $largorol; $i++) {
                                    $datosrol[$i] = $cryptor->decrypt($roles[$i]);
                                }
                            }

                            $datosusuarios = $modelmensaje->listarusuariotodo($idperiodo, $userrol, $datosrol);
                            $largo = count($datosusuarios);


                            //agregamos a la tabla de receptores

                            if ($largo > 0) {
                                for ($i = 0; $i < $largo; $i++) {

                                    $modelmensaje->agregarreceptores($datosusuarios[$i]['id'], 1, $fecha_leido, $idmensaje);

                                }
                            } else {
                                $db->rollBack();
                                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No Existen Destinatarios');
                                $this->view->assign('messages', $messages);
                            }


                        } elseif (count($establecimientos) > 0 && $establecimientos[0] != 'all') {

                            if ($destinatarios[0] == 'alle' && $roles[0] == 'allr') {
                                //buscamos todas las cuentas activas
                                $datosusuarios = $modelmensaje->listarusuarioestablecimientos($idperiodo, $establecimientos, $userrol, array(1, 2, 3, 4, 5, 6));
                                $largo = count($datosusuarios);


                            } else {

                                if ($roles[0] == 'allr') {

                                    $datosusuarios = $modelmensaje->listarusuarioestablecimientos($idperiodo, $establecimientos, $userrol, array(1, 2, 3, 4, 5, 6));
                                    $largo = count($datosusuarios);

                                } else {
                                    $largorol = count($roles);
                                    if ($largorol > 0) {
                                        for ($i = 0; $i < $largorol; $i++) {
                                            $datosrol[] = $cryptor->decrypt($roles[$i]);
                                        }
                                    }

                                    $datosusuarios = $modelmensaje->listarusuarioestablecimientos($idperiodo, $establecimientos, $userrol, $datosrol);

                                    $largo = count($datosusuarios);
                                }

                            }

                            //agregamos a la tabla de receptores

                            if ($largo > 0) {
                                for ($i = 0; $i < $largo; $i++) {
                                    $modelmensaje->agregarreceptores($datosusuarios[$i]['id'], 1, $fecha_leido, $idmensaje);

                                }
                            } else {
                                $db->rollBack();
                                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No Existen Destinatarios');
                                $this->view->assign('messages', $messages);
                            }


                        }


                        $db->commit();
                        $this->_helper->redirector('nuevo');

                    } catch (Exception $e) {
                        $db->rollBack();
                        echo $e;
                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Error');
                        $this->view->assign('messages', $messages);
                    }
                }


            } else {
                $form->populate($formData);
                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Error, existen campos vacíos');
                $this->view->assign('messages', $messages);
            }
        }


    }

    public function readAction()
    {

        $id = $this->_getParam('id', 0);
        if (!empty($id)) {


            $usuario = new Zend_Session_Namespace('id');
            $user = $usuario->id;

            $usuariorol = new Zend_Session_Namespace('idRol');
            $userrol = $usuariorol->idRol;

            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;

            $est = new Zend_Session_Namespace('establecimiento');
            $idestablecimiento = $est->establecimiento;

            $cargo = new Zend_Session_Namespace('cargo');
            $rol = $cargo->cargo;

            //Desencritamos el id
            $cryptor = new \Chirp\Cryptor();

            $idmensaje = $cryptor->decrypt($id);
            if ($idmensaje) {

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                // Iniciamos la transaccion
                $db->beginTransaction();
                try {
                    if ($rol == 1) {
                        $form = new Application_Form_Mensajes();
                        $this->view->form = $form;
                    } else {
                        //Obtenemos las configurciones de los mensajes
                        $validacionnuevo = false;
                        $validacionresp = false;
                        //1=nuevo 2=respuesta
                        $validacionnuevo = $this->validarconfiguracionAction($rol, $idestablecimiento, 1, 0);

                        if (!$validacionnuevo) {
                            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No Tiene Permisos para esta accion');
                            $this->view->assign('messagesnuevo', $messages);
                        }


                    }
                    $modelmensaje = new Application_Model_DbTable_Mensaje();
                    $datos = $modelmensaje->getmensajeusuario($userrol, $idmensaje);

                    $validacionresp = $this->validarconfiguracionAction($rol, $idestablecimiento, 2,$datos[0]['idRol']);

                    if (!$validacionresp) {
                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No Tiene Permisos para Responder');
                        $this->view->assign('validaresp', $messages);
                    }

                    if (count($datos) > 0) {
                        if ($datos[0]['leido'] == 1) {
                            $fecha_ahora = date_create()->format('Y-m-d H:i:s');
                            //Actualizamos y cambiamos a leido
                            $modelmensaje->actualizarleido($datos[0]['idReceptor'], $fecha_ahora, $datos[0]['idMensaje']);

                        }
                        //Formato Fecha
                        setlocale(LC_TIME, "es_ES");

                        $date = date_create($datos[0]['fecha']);

                        $datos[0]['fechaformato'] = strftime("%d %b %r", $date->getTimestamp());

                    } else {
                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Mensaje no encontrado1');
                        $this->view->assign('messages', $messages);
                    }

                    $mensajesnoleidos = $modelmensaje->getmensajes($userrol, $idperiodo, array('1'));
                    $totalmensajesnoleidos = count($mensajesnoleidos);


                    $db->commit();

                    $datos[0]['idMensaje'] = $cryptor->encrypt($datos[0]['idMensaje']);
                    $this->view->datos = $datos;
                    $this->view->totalnoleido = $totalmensajesnoleidos;


                } catch (Exception $e) {
                    // Si hubo problemas. Enviamos todo marcha atras
                    $db->rollBack();
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Mensaje no encontrado2');
                    $this->view->assign('messages', $messages);
                }


            } else {
                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Error');
                $this->view->assign('messages', $messages);
            }


        }

    }

    public function readsendAction()
    {
        $id = $this->_getParam('id', 0);
        if (!empty($id)) {

            $usuario = new Zend_Session_Namespace('id');
            $user = $usuario->id;

            $usuariorol = new Zend_Session_Namespace('idRol');
            $userrol = $usuariorol->idRol;

            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;

            $est = new Zend_Session_Namespace('establecimiento');
            $idestablecimiento = $est->establecimiento;

            $cargo = new Zend_Session_Namespace('cargo');
            $rol = $cargo->cargo;


            //Desencritamos el id
            $cryptor = new \Chirp\Cryptor();

            $idmensaje = $cryptor->decrypt($id);
            if ($idmensaje) {

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                // Iniciamos la transaccion
                $db->beginTransaction();
                try {

                    if ($rol == 1) {
                        $form = new Application_Form_Mensajes();
                        $this->view->form = $form;
                    } else {
                        //Obtenemos las configurciones de los mensajes
                        $validacionnuevo = false;
                        $validacionresp = false;
                        //1=nuevo 2=respuesta
                        $validacionnuevo = $this->validarconfiguracionAction($rol, $idestablecimiento, 1, 0);
                        $validacionresp = $this->validarconfiguracionAction($rol, $idestablecimiento, 2);
                        if (!$validacionnuevo) {
                            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No Tiene Permisos Crear Nuevo Mensaje');
                            $this->view->assign('messagesnuevo', $messages);
                        }

                    }


                    $modelmensaje = new Application_Model_DbTable_Mensaje();
                    $datos = $modelmensaje->getmensajeusuarioenviado($userrol, $idmensaje);


                    //buscamos los receptores del mensaje
                    $datosreceptores = $modelmensaje->getreceptores($idmensaje, $idperiodo);

                    if (count($datos) > 0) {

                        //Formato Fecha
                        setlocale(LC_TIME, "es_ES");

                        $date = date_create($datos[0]['fecha']);

                        $datos[0]['fechaformato'] = strftime("%d %b %r", $date->getTimestamp());

                    } else {
                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Mensaje no encontrado');
                        $this->view->assign('messages', $messages);
                    }

                    $mensajesnoleidos = $modelmensaje->getmensajes($userrol, $idperiodo, array('1'));
                    $totalmensajesnoleidos = count($mensajesnoleidos);
                    $this->view->totalnoleido = $totalmensajesnoleidos;
                    $this->view->receptores = $datosreceptores;

                    $db->commit();
                    $this->view->datos = $datos;


                } catch (Exception $e) {
                    echo $e;
                    // Si hubo problemas. Enviamos todo marcha atras
                    $db->rollBack();
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Mensaje no encontrado');
                    $this->view->assign('messages', $messages);
                }


            } else {
                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Error');
                $this->view->assign('messages', $messages);
            }


        }

    }

    public function responderAction()
    {

        $id = $this->_getParam('id', 0);

        //Desencritamos el id
        $cryptor = new \Chirp\Cryptor();

        $idmensaje = $cryptor->decrypt($id);
        if ($idmensaje) {

            $usuario = new Zend_Session_Namespace('id');
            $user = $usuario->id;

            $usuariorol = new Zend_Session_Namespace('idRol');
            $userrol = $usuariorol->idRol;

            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;

            $est = new Zend_Session_Namespace('establecimiento');
            $idestablecimiento = $est->establecimiento;

            $cargo = new Zend_Session_Namespace('cargo');
            $rol = $cargo->cargo;


            if ($rol == 1) {
                $form = new Application_Form_Mensajes();
                $this->view->form = $form;
            } else {
                //Obtenemos las configurciones de los mensajes
                $resultadovalidacion = false;
                //1=nuevo 2=respuesta
                $resultadovalidacion = $this->validarconfiguracionAction($rol, $idestablecimiento, 1, 0);
                if ($resultadovalidacion) {
                    $form = new Application_Form_Mensajes();
                    $this->view->form = $form;
                } else {
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No Tiene Permisos para esta accion');
                    $this->view->assign('messagesnuevo', $messages);
                }
            }

            $form = new Application_Form_Mensajes();
            $modelmensaje = new Application_Model_DbTable_Mensaje();
            $datos = $modelmensaje->getmensaje($idmensaje);

            setlocale(LC_TIME, "es_ES");

            $date = date_create($datos['fecha']);

            $fechaformato = strftime("%A, %d de %B del %Y a las %r", $date->getTimestamp());

            //Agregamos un campo del emisor del mensaje

            $form->addElement('text', 'para');
            $form->para->setAttrib("class", "form-control");
            $form->para->setAttrib("placeholder", "Para:");

            //Obtenemos el nombre del emisor
            $datosusuario = $modelmensaje->getnombreusuario($datos['emisor']);
            $validacionresp = $this->validarconfiguracionAction($rol, $idestablecimiento, 2, $datosusuario[0]['idRol']);


            if ($validacionresp) {
                $form->para->setValue("Para: " . $datosusuario[0]['nombrescuenta'] . " " . $datosusuario[0]['paternocuenta'] . " " . $datosusuario[0]['maternocuenta']);
                $form->para->setAttrib('disabled', 'disabled');


                //agregamos una separacion y datos del mensaje enviado a la variable mensaje
                $mensaje = "<p><br><br></p><p>---------------------------------------------------------------------------------------</p><p><b>De:</b> " . $datosusuario[0]['nombrescuenta'] . " " . $datosusuario[0]['paternocuenta'] . " " . $datosusuario[0]['maternocuenta'] . "<br><b>Enviado:</b> " . $fechaformato . "<br><b>Asunto:</b> " . $datos['asunto'] . "</p>" . $datos['mensaje'];
                $form->asunto->setValue("RE:" . $datos['asunto']);
                $form->mensaje->setValue($mensaje);
                //deshabilitamos que el campo idCuenta no sea requerido ya que no se utiliza
                if ($form->idCuenta) {
                    $form->idCuenta->setRequired(false);
                }

                $this->view->form = $form;
            } else {
                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No Tiene Permisos para Responder');
                $this->view->assign('validaresp', $messages);
            }


            $mensajesnoleidos = $modelmensaje->getmensajes($userrol, $idperiodo, array('1'));
            $totalmensajesnoleidos = count($mensajesnoleidos);
            $this->view->totalnoleido = $totalmensajesnoleidos;

            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {

                    //Obetenemos los datos del usuario emisor para responder el mensaje
                    $datosusuario = $modelmensaje->getmensaje($idmensaje);

                    if ($datosusuario['emisor'] > 0) {

                        $fecha_creacion = date_create()->format('Y-m-d H:i:s');
                        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                        // Iniciamos la transaccion
                        $db->beginTransaction();
                        $fecha_leido = '0000-00-00 00:00:00';

                        $asunto = $form->getValue('asunto');
                        $mensaje = $form->getValue('mensaje');

                        try {

                            //Agreamos el cuerpo del mensaje
                            $modelmensaje->agregarmensaje($userrol, $asunto, $mensaje, $fecha_creacion, 1, $idperiodo);
                            $idmensajenuevo = $modelmensaje->getAdapter()->lastInsertId();

                            //agregamos el receptor

                            $modelmensaje->agregarreceptores($datosusuario['emisor'], 1, $fecha_leido, $idmensajenuevo);


                            $db->commit();
                            $this->_helper->redirector('entrada');

                        } catch (Exception $e) {
                            $db->rollBack();
                            //echo $e;
                            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Error');
                            $this->view->assign('messages', $messages);
                        }

                    } else {

                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Error');
                        $this->view->assign('messages', $messages);

                    }


                } else {
                    $form->para->setValue("Para: " . $datosusuario[0]['nombrescuenta'] . " " . $datosusuario[0]['paternocuenta'] . " " . $datosusuario[0]['maternocuenta']);
                    $form->para->setAttrib('disabled', 'disabled');
                    $form->populate($formData);
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Error, existen campos vacíos');
                    $this->view->assign('messages', $messages);
                }
            }

        }


    }

    public function agregarconfiguracionAction()
    {
        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;
        if ($rol == '1' || $rol == '3' || $rol == '6') {

            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;

            $this->view->title = "Configuración Mensajes";
            $this->view->headTitle($this->view->title);
            $form = new Application_Form_configuracionMensajes();
            $form->setDecorators(
                array(
                    'FormElements',
                    'Form',

                )
            );
            $modelmensaje = new Application_Model_DbTable_Mensaje();
            $this->view->form = $form;

            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {
                    $idconf = $form->getValue('id');
                    $idestablecimiento = $form->getValue('idEstablecimiento');

                    //Nuevos mensajes
                    $nuevodirector = $form->getValue('nuevoDirector');
                    $nuevoutp = $form->getValue('nuevoUtp');
                    $nuevodocente = $form->getValue('nuevoDocente');

                    //Respuestas

                    $daemdirector = $form->getValue('daemDirector');
                    $daemutp = $form->getValue('daemUtp');
                    $daemdocente = $form->getValue('daemDocente');

                    //Director
                    $directorutp = $form->getValue('directorUtp');
                    $directordocente = $form->getValue('directorDocente');

                    //UTP
                    $docenteutp = $form->getValue('utpDocente');

                    //Si el Rol es Administrador Daem
                    if ($rol == 1) {
                        $listadatos = array($nuevodirector, $nuevoutp, $nuevodocente, $daemdirector, $daemutp, $daemdocente, $directorutp, $directordocente, $docenteutp);

                    }
                    if ($rol == 3 || $rol == 6) {
                        $id = $this->_getParam('id', 0);
                        //si viene algun id
                        if ($id > 0) {
                            $establecimientos = $modelmensaje->listar($id, $idperiodo);

                            $form = new Application_Form_configuracionMensajes();

                            if (empty($establecimientos)) {
                                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No existe configuración, contacte al Administrador ');
                                //Asignamos el mensaje
                                $this->view->assign('messages', $messages);
                            } else {
                                if (!empty($establecimientos[0]['config'])) {
                                    $datosconfiguracion = unserialize($establecimientos[0]['config']);

                                    if ($rol == 3) {

                                        $nuevodirector = $datosconfiguracion[0];
                                        $nuevoutp = $datosconfiguracion[1];
                                        $nuevodocente = $datosconfiguracion[2];
                                        $daemdirector = $datosconfiguracion[3];
                                        $daemutp = $datosconfiguracion[4];
                                        $daemdocente = $datosconfiguracion[5];

                                        $listadatos = array($nuevodirector, $nuevoutp, $nuevodocente, $daemdirector, $daemutp, $daemdocente, $directorutp, $directordocente, $docenteutp);


                                    }
                                    if ($rol == 6) {

                                        $nuevodirector = $datosconfiguracion[0];
                                        $nuevoutp = $datosconfiguracion[1];
                                        $nuevodocente = $datosconfiguracion[2];
                                        $daemdirector = $datosconfiguracion[3];
                                        $daemutp = $datosconfiguracion[4];
                                        $daemdocente = $datosconfiguracion[5];
                                        $directorutp = $datosconfiguracion[6];
                                        $directordocente = $datosconfiguracion[7];

                                        $listadatos = array($nuevodirector, $nuevoutp, $nuevodocente, $daemdirector, $daemutp, $daemdocente, $directorutp, $directordocente, $docenteutp);


                                    }

                                }

                            }
                        }

                    }

                    $datos = serialize($listadatos);

                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                    // Iniciamos la transaccion
                    $db->beginTransaction();
                    try {
                        if ($idestablecimiento > 0) {
                            $validar = $modelmensaje->listar($idestablecimiento, $idperiodo);
                        } else {
                            $idestablecimiento = $this->_getParam('id', 0);
                            $validar = false;
                        }


                        if ($validar) {

                            $modelmensaje->cambiarconfig($idconf, $datos);
                        } else {
                            $modelmensaje->agregarconfig($datos, $idperiodo, $idestablecimiento);
                        }

                        $db->commit();
                        $this->_helper->redirector('index');

                    } catch (Exception $e) {
                        // Si hubo problemas. Enviamos todo marcha atras
                        $db->rollBack();
                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Ha ocurido un error, al ingresar los datos, intente nuevamente' . $e);
                        /// Asignamos el mensaje
                        $this->view->assign('messages', $messages);
                    }

                } else {
                    $form->populate($formData);
                }
            } else {


                $id = $this->_getParam('id', 0);
                //si viene algun id
                if ($id > 0) {
                    $establecimientos = $modelmensaje->listar($id, $idperiodo);

                    $form = new Application_Form_configuracionMensajes();

                    if (empty($establecimientos)) {
                        $this->view->form = $form;
                    } else {
                        if (!empty($establecimientos[0]['config'])) {
                            $datosconfiguracion = unserialize($establecimientos[0]['config']);

                            //nuevo
                            $form->nuevoDirector->setValue($datosconfiguracion[0]);
                            $form->nuevoUtp->setValue($datosconfiguracion[1]);
                            $form->nuevoDocente->setValue($datosconfiguracion[2]);

                            //Respuestas


                            $form->daemDirector->setValue($datosconfiguracion[3]);
                            $form->daemUtp->setValue($datosconfiguracion[4]);
                            $form->daemDocente->setValue($datosconfiguracion[5]);
                            $form->directorUtp->setValue($datosconfiguracion[6]);
                            $form->directorDocente->setValue($datosconfiguracion[7]);
                            $form->utpDocente->setValue($datosconfiguracion[8]);

                        }
                        $this->view->form = $form;
                        $form->populate($establecimientos[0]);
                    }
                }
            }
        }
    }

    public function getusuariosAction()
    {
        $this->_helper->viewRenderer->setNeverRender(true);
        $this->_helper->ViewRenderer->setNoRender(true);
        $this->_helper->Layout->disableLayout();

        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;

        $usuariorol = new Zend_Session_Namespace('idRol');
        $userrol = $usuariorol->idRol;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;
        //Desencritamos el id
        $cryptor = new \Chirp\Cryptor();

        $modelmensaje = new Application_Model_DbTable_Mensaje();
        $establecimiento = $this->_getParam('id');
        $roles = $this->_getParam('r');

        if (strlen($establecimiento) > 1) {
            $establecimiento = explode(',', $establecimiento);
        }


        if ($roles != 'allr') {
            if (strlen($roles) > 1) {
                $roles = explode(',', $roles);
            }
            $largorol = count($roles);
            if ($largorol > 0) {

                for ($i = 0; $i < $largorol; $i++) {

                    $datosroles[$i] = $cryptor->decrypt($roles[$i]);

                }

            }
        } else {
            $datosroles = array(1, 2, 3, 4, 5, 6);
        }


        if (!empty($establecimiento) && count($establecimiento) > 0 && $establecimiento[0] != 'all') {
            $datos = $modelmensaje->listarusuarioestablecimientos($idperiodo, $establecimiento, $userrol, $datosroles);



        } else {
            //var_dump($datosroles);
            $datos = $modelmensaje->listarusuariotodo($idperiodo, $userrol, $datosroles);

        }

        for ($i = 0; $i < count($datos); $i++) {

            $datos[$i]['idCuenta'] = $cryptor->encrypt($datos[$i]['idCuenta']);
            $datos[$i]['id'] = $cryptor->encrypt($datos[$i]['id']);

        }

        $this->_helper->json($datos);
    }

    public function validarconfiguracionAction($rol, $establecimiento, $tipo, $rolt)
    {
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $modelmensaje = new Application_Model_DbTable_Mensaje();
        // si la validacion es para nuevos mensajes
        if ($tipo == 1) {

            if ($rol == 2 || $rol == 3 || $rol == 6) {

                $datosest = $modelmensaje->listar($establecimiento, $idperiodo);
                $datosconfiguracion = unserialize($datosest[0]['config']);

                if ($rol == 2) {
                    if ($datosconfiguracion[2]) {
                        return true;
                    }
                }
                if ($rol == 3) {
                    if ($datosconfiguracion[0]) {
                        return true;
                    }
                }

                if ($rol == 6) {
                    if ($datosconfiguracion[1]) {
                        return true;
                    }
                }

            }
        }
        if ($tipo == 2) {

            if ($rol == 2 || $rol == 3 || $rol == 6) {


                $datosest = $modelmensaje->listar($establecimiento, $idperiodo);
                $datosconfiguracion = unserialize($datosest[0]['config']);
                //Asociamos el rol a la configuracion que corresponde

                //Administrador Daem

                if ($rolt == 1 && $rol == 3) {
                    return $datosconfiguracion[3];
                }
                if ($rolt == 1 && $rol == 6) {
                    return $datosconfiguracion[4];
                }

                if ($rolt == 1 && $rol == 2) {

                    return $datosconfiguracion[5];
                }

                //Director
                if ($rolt == 3 && $rol == 6) {
                    return $datosconfiguracion[6];
                }

                if ($rolt == 3 && $rol == 2) {

                    return $datosconfiguracion[7];
                }

                //Jefe UTP
                if ($rolt == 6 && $rol == 2) {
                    return $datosconfiguracion[8];
                }

            }

        }


    }


}







