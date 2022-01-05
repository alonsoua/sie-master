<?php
require 'Cryptor.php';
setlocale(LC_TIME, 'es_CL.UTF-8');

class LibroController extends Zend_Controller_Action
{

    const MENSAJE = "Hubo un problema al ingresar los datos, intente nuevamente";

    public function init()
    {
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();

    }

    public function indexAction()
    {

        $table = new Application_Model_DbTable_Cursos();

        $est = new Zend_Session_Namespace('establecimiento');
        $establecimiento = $est->establecimiento;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;


        if ($rol == '3' || $rol == '4' || $rol == '6') {
            $this->view->datos = $table->listartodasactual($establecimiento, $idperiodo);

        }
        if ($rol == '1') {
            $this->view->datos = $table->listaractual($idperiodo);

        }

        if ($rol == '2') {


            $datoscurso = $table->listarcursodocente($user, $idperiodo, $establecimiento)->toArray();
            $this->view->datos = $datoscurso;


        }


    }

    public function abrirAction()
    {
        $rutusuario = new Zend_Session_Namespace('id');
        $idcuenta = $rutusuario->id;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;


        $est = new Zend_Session_Namespace('establecimiento');
        $idestablecimiento = $est->establecimiento;

        $modelocurso = new Application_Model_DbTable_Cursos();


        // Si el Rol es Docente
        if ($rol == '2') {
            $id = $this->_request->getParam('id');


            if ($id > 0) {
                $datosusuario = $modelocurso->getcursohorario($idcuenta, $idperiodo, $id, $idestablecimiento);
                $datospre = $modelocurso->listarcursodocentepreid($idcuenta, $idperiodo, $idestablecimiento, $id)->toArray();

                if (count($datospre) > 0) {
                    if ($datospre[0]['idCuentaJefe'] == $idcuenta) {
                        $pase = true;
                    } else {
                        $pase = false;
                    }
                }

                if ($datosusuario || $pase) {

                    $detalleest = $modelocurso->listarcursoid($id, $idperiodo);

                    $monitoreo = new Zend_Session_Namespace('monitoreo');
                    $monitoreo->monitoreo = $detalleest[0]['monitoreo'];

                    $examenes = new Zend_Session_Namespace('examen');
                    $examenes->examen = $detalleest[0]['examen'];


                    $nombre_curs = $modelocurso->getnombreactual($id);
                    $id_curso = $id;


                    $nombrecurso = new Zend_Session_Namespace('nombre_curso');
                    $nombrecurso->nombre_curso = $nombre_curs[0]['nombreGrado'] . ' ' . $nombre_curs[0]['letra'];


                    $codigos = new Zend_Session_Namespace('codigo');
                    $codigos->codigo = $nombre_curs[0]['idCodigoTipo'];


                    $idcurso = new Zend_Session_Namespace('id_curso');
                    $idcurso->id_curso = $id;

                    $idtablacurso = new Zend_Session_Namespace('idtablacurso');
                    $idtablacurso->idtablacurso = $nombre_curs[0]['idCursos'];

                    $datosdocente = new Zend_Session_Namespace('nombredocente');
                    $datosdocente->docente = $datosusuario[0]['nombrescuenta'] . ' ' . $datosusuario[0]['paternocuenta'] . ' ' . $datosusuario[0]['maternocuenta'];

                    $activePage = $this->view->navigation()->findOneBy('active', true);
                    $activePage->setlabel($nombre_curs[0]['nombreGrado'] . ' ' . $nombre_curs[0]['letra']);

                    $modelalumnosactual = new Application_Model_DbTable_Alumnosactual();
                    $this->view->datosalumnos = $modelalumnosactual->listaralumnoscursoactual($id_curso, $idperiodo);

                    //si el usuario en sesion  es profesor jefe se habilita el orden de alumno

                    if ($idcuenta == $nombre_curs[0]['idCuentaJefe'] || $rol == 1 || $rol == 3 || $rol == 6) {
                        $this->view->jefe = 1;
                    } else {
                        $this->view->jefe = 0;

                    }

                } else {
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No se encontro el libro que desea abrir');
                    $this->view->assign('messages', $messages);
                }
            } else {
                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No se encontro el libro que desea abrir');
                $this->view->assign('messages', $messages);

            }

        }

        // Si el rol es Administrador Daem ,Director,UTP
        if ($rol == '1' || $rol == '3' || $rol == '6') {

            $id = $this->_request->getParam('id');
            if ($id > 0) {


                $modelcurso = new Application_Model_DbTable_Cursos();
                $detalleest = $modelcurso->listarcursoid($id, $idperiodo);

                $monitoreo = new Zend_Session_Namespace('monitoreo');
                $monitoreo->monitoreo = $detalleest[0]['monitoreo'];

                $examenes = new Zend_Session_Namespace('examen');
                $examenes->examen = $detalleest[0]['examen'];

                $nombre_curs = $modelcurso->getnombreactual($id);
                $valida = true;
                if ($rol == '3' || $rol == '6') {
                    $est = new Zend_Session_Namespace('establecimiento');
                    $establecimiento = $est->establecimiento;
                    if ($establecimiento != $nombre_curs[0]['idEstablecimiento']) {
                        $valida = false;
                    }

                }

                if ($rol == '2') {
                    $usuario = new Zend_Session_Namespace('id');
                    $user = $usuario->id;

                    if ($user != $nombre_curs[0]['idCuentaJefe']) {
                        $valida = false;
                    }

                }
                if ($valida) {

                    $periodo = new Zend_Session_Namespace('periodo');
                    $idperiodo = $periodo->periodo;

                    $nombrecurso = new Zend_Session_Namespace('nombre_curso');
                    $nombrecurso->nombre_curso = $nombre_curs[0]['nombreGrado'] . ' ' . $nombre_curs[0]['letra'];


                    $codigos = new Zend_Session_Namespace('codigo');
                    $codigos->codigo = $nombre_curs[0]['idCodigoTipo'];

                    $idcurso = new Zend_Session_Namespace('id_curso');
                    $idcurso->id_curso = $id;

                    $idtablacurso = new Zend_Session_Namespace('idtablacurso');
                    $idtablacurso->idtablacurso = $nombre_curs[0]['idCursos'];

                    $activePage = $this->view->navigation()->findOneBy('active', true);

                    $activePage->setlabel($nombre_curs[0]['nombreGrado'] . ' ' . $nombre_curs[0]['letra']);

                    $modelalumnosactual = new Application_Model_DbTable_Alumnosactual();
                    $this->view->datosalumnos = $modelalumnosactual->listaralumnoscursoactual($id, $idperiodo);

                    //si el usuario en sesion  es profesor jefe se habilita el orden de alumnos
                    if ($idcuenta == $nombre_curs[0]['idCuentaJefe'] || $rol == 1 || $rol == 3 || $rol == 6) {
                        $this->view->jefe = 1;
                    } else {
                        $this->view->jefe = 0;
                    }

                } else {
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No se encontro el libro que desea abrir ');
                    $this->view->assign('messages', $messages);
                }

            } else {
                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No se encontro el libro que desea abrir');
                $this->view->assign('messages', $messages);
            }
        }

    }

    public function agregapruebasAction()
    {

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $nivelcurso = new Zend_Session_Namespace('nivel_curso');
        $nivel_curso = $nivelcurso->nivel_curso;

        if ($rol == '2') {
            $formpruebas = new Application_Form_Pruebas();
            $this->view->form = $formpruebas;

            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if ($formpruebas->isValid($formData)) {

                    $asignatura = $formpruebas->getValue('idAsignatura');
                    $contenido = $formpruebas->getValue('conte');
                    $tipoev = $formpruebas->getValue('tipoEvaluacionPrueba');
                    $fecha = $formpruebas->getValue('fecha');
                    $curso = $formpruebas->getvalue('idCursos');
                    $coef = $formpruebas->getvalue('coef');
                    if ($nivel_curso == 14 || $nivel_curso == 15) {
                        $coef = 1;
                    }

                    $usuario = new Zend_Session_Namespace('id');

                    $user = $usuario->id;

                    $periodo = new Zend_Session_Namespace('periodo');
                    $idperiodo = $periodo->periodo;

                    $pruebas = new Application_Model_DbTable_Pruebas();
                    $fecha2 = date("Y-m-d", strtotime($fecha));
                    $fecha = $fecha2;
                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                    // Iniciamos la transaccion
                    $db->beginTransaction();
                    try {
                        $pruebas->agregar($contenido, $fecha, $curso, $asignatura, $idperiodo, $user, $tipoev, $coef);
                        $db->commit();
                        $this->_helper->redirector('pruebas');

                    } catch (Exception $e) {
                        // Si hubo problemas. Enviamos  marcha atras
                        $db->rollBack();
                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage(self::MENSAJE);
                        $this->view->assign('messages', $messages);
                    }


                } else {

                    $formpruebas->populate($formData);

                }
            }
        }
    }

    public function pruebaeditarAction()
    {

        $id = $this->_getParam('id', 0);
        if ($id > 0) {

            $pruebas = new Application_Model_DbTable_Pruebas();
            $tabla = $pruebas->get($id);

            if ($tabla['estadoev'] == 1) {
                $formpruebas = new Application_Form_Pruebas();
                $formpruebas->removeElement('idAsignatura');
                $formpruebas->removeElement('tipoEvaluacionPrueba');
                $formpruebas->removeElement('fecha');
                $this->view->form = $formpruebas;
            }
            if ($tabla['estadoev'] == 0) {
                $formpruebas = new Application_Form_Pruebas();
                $this->view->form = $formpruebas;
            }

        }

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($formpruebas->isValid($formData)) {
                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $id = $formpruebas->getValue('idEvaluacion');

                $pruebas = new Application_Model_DbTable_Pruebas();
                $tabla = $pruebas->get($id);
                if ($tabla['estadoev'] == 1) {
                    $contenido = $formpruebas->getValue('conte');
                    $coef = $formpruebas->getvalue('coef');

                    // Iniciamos la transaccion
                    $db->beginTransaction();

                    try {
                        $pruebas->cambiarcoef($id, $contenido, $coef);
                        $db->commit();
                        $this->_helper->redirector('pruebas');

                    } catch (Exception $e) {
                        // Si hubo problemas. Enviamos  marcha atras
                        $db->rollBack();
                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage(self::MENSAJE);
                        $this->view->assign('messages', $messages);
                    }

                }

                if ($tabla['estadoev'] == 0) {

                    $asignatura = $formpruebas->getValue('idAsignatura');
                    $contenido = $formpruebas->getValue('conte');
                    $tipoev = $formpruebas->getValue('tipoEvaluacionPrueba');
                    $fecha = $formpruebas->getValue('fecha');
                    $curso = $formpruebas->getvalue('idCursos');
                    $coef = $formpruebas->getvalue('coef');

                    $usuario = new Zend_Session_Namespace('id');
                    $user = $usuario->id;

                    $periodo = new Zend_Session_Namespace('periodo');
                    $idperiodo = $periodo->periodo;

                    $fecha2 = date("Y-m-d", strtotime($fecha));
                    $fecha = $fecha2;

                    // Iniciamos la transaccion
                    $db->beginTransaction();
                    try {
                        $pruebas->cambiar($id, $contenido, $fecha, $curso, $asignatura, $idperiodo, $user, $tipoev, $coef);
                        $db->commit();
                        $this->_helper->redirector('pruebas');
                    } catch (Exception $e) {
                        // Si hubo problemas. Enviamos  marcha atras
                        $db->rollBack();
                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage(self::MENSAJE);
                        $this->view->assign('messages', $messages);
                    }
                }

            }
            else {


                $formpruebas->populate($formData);

            }
        } else {

            $id = $this->_getParam('id', 0);
            if ($id > 0) {
                $personal = new Application_Model_DbTable_Pruebas();
                $tabla = $personal->get($id);

                $tabla['fechaEvaluacion'] = date("d-m-Y", strtotime($tabla['fechaEvaluacion']));

                if ($tabla['estadoev'] == '1') {

                    $formpruebas->removeElement('idAsignatura');
                    $formpruebas->removeElement('tipoEvaluacionPrueba');
                    $formpruebas->removeElement('fecha');
                    $formpruebas->conte->setValue($tabla['contenido']);

                    $formpruebas->populate($tabla);

                }
                if ($tabla['estadoev'] == 0) {

                    $formpruebas->conte->setValue($tabla['contenido']);
                    $formpruebas->tipoEvaluacionPrueba->setValue($tabla['tiempo']);
                    $formpruebas->fecha->setValue($tabla['fechaEvaluacion']);

                    $formpruebas->populate($tabla);
                }
            }
        }

    }

    public function pruebaeliminarAction()
    {

        $id = $this->_getParam('id', 0);
        $tabla = new Application_Model_DbTable_Pruebas();
        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        // Iniciamos la transaccion
        $db->beginTransaction();

        try {
            $tabla->borrar($id);
            $db->commit();
            $this->_helper->redirector('pruebas');
        } catch (Exception $e) {
            // Si hubo problemas. Enviamos  marcha atras
            $db->rollBack();
            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al eliminar lod datos, esto puede ser que tenga datos asociados');
            $this->view->assign('messages', $messages);
        }
    }

    public function pruebasAction()
    {

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $roles = new Zend_Session_Namespace('cargo');
        $rol = $roles->cargo;

        $nivelcurso = new Zend_Session_Namespace('nivel_curso');
        $nivel_curso = $nivelcurso->nivel_curso;

        $nombrecurso = new Zend_Session_Namespace('nombre_curso');
        $nombre_curso = $nombrecurso->nombre_curso;

        $idcurso = new Zend_Session_Namespace('id_curso');
        $id_curso = $idcurso->id_curso;

        $usuario = new Zend_Session_Namespace('id');

        $user = $usuario->id;

        $modelpruebas = new Application_Model_DbTable_Pruebas();
        $iddetallecurso = new Zend_Session_Namespace('id_detalle_curso');
        $detallecurso = $iddetallecurso->id_detalle_curso;

        //Si el es Docente
        if ($rol == '2') {

            $activePage = $this->view->navigation()->findOneByLabel('Abierto');
            $activePage->setlabel($nombre_curso);
            $activePage->setparam('id', $id_curso);

            $listapruebas = $modelpruebas->listaradmin($id_curso, $idperiodo);
            $this->view->datospruebas = $listapruebas;

        }

        //si el Rol es Administrador dame o Director
        if ($rol == '1' || $rol == '3' || $rol == '6') {

            $activePage = $this->view->navigation()->findOneByLabel('Abierto');
            $activePage->setlabel($nombre_curso);
            $activePage->setparam('id', $id_curso);
            $listapruebas = $modelpruebas->listaradmin($id_curso, $idperiodo);
            $this->view->datospruebas = $listapruebas;
        }


    }

    public function agregarnotasAction()
    {

        $formnotas = new Application_Form_Notas();
        $this->view->form = $formnotas;

    }


    public function comunicacionesAction()
    {

        $idcurso = new Zend_Session_Namespace('id_curso');
        $id_curso = $idcurso->id_curso;

        $rutusuario = new Zend_Session_Namespace('id');
        $rutusuario = $rutusuario->id;

        $nombrecurso = new Zend_Session_Namespace('nombre_curso');
        $nombre_curso = $nombrecurso->nombre_curso;

        $iddetallecurso = new Zend_Session_Namespace('id_detalle_curso');
        $detallecurso = $iddetallecurso->id_detalle_curso;

        $ingreson = new Zend_Session_Namespace('ingresonota');
        $ingresonota = $ingreson->ingresonota;

        $activePage = $this->view->navigation()->findOneByLabel('Abierto');
        $activePage->setlabel($nombre_curso);
        if ($ingresonota == 1) {
            $activePage->setparam('id', $id_curso);
        } else {
            $activePage->setparam('id', $id_curso . '-' . $detallecurso);
        }

        $tablec = new Application_Model_DbTable_Comunicaciones();
        $listacita = $tablec->listarcitaciones($rutusuario);
        $this->view->listacitaciones = $listacita;
    }

    public function detallecomunicacionAction()
    {
        $this->_helper->layout->disableLayout();
        $id = $this->_getParam('id');

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;


        if ($id > 0) {

            $modelocomunicacion = new Application_Model_DbTable_Comunicaciones();
            $detalle = $modelocomunicacion->listardetalle($user, $id, $idperiodo);
            $this->view->detalle = $detalle;

        }

    }

    function searchs($array, $key, $value)
    {
        $results = array();

        if (is_array($array)) {
            if (isset($array[$key]) && $array[$key] == $value) {
                $results[] = $array;
            }

            foreach ($array as $subarray) {
                $results = array_merge($results, $this->searchs($subarray, $key, $value));
            }
        }

        return $results;
    }

    public function agregacomunicacionesAction()
    {

        $idcurso = new Zend_Session_Namespace('id_curso');
        $id_curso = $idcurso->id_curso;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $identificadors = new Zend_Session_Namespace('identificador');
        $ididentificador = $identificadors->identificador;

        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;

        //Logeamos al usuario master y obtenemos el token

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://api.softinnova.cl/login?");
        curl_setopt($ch, CURLOPT_POST, 1);

        curl_setopt($ch, CURLOPT_POSTFIELDS,
            "user=76189207k&password=rino711633");


        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute the POST request
        $result = curl_exec($ch);
        //close cURL resource
        curl_close($ch);
        $resultadodeco = json_decode($result);
        $token = $resultadodeco->token;

        //Obtenemos los alumnos habilitados

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => "http://api.softinnova.cl/getalumnos/76189207k/" . $ididentificador . "/" . $id_curso . "/" . $idperiodo,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array('Acceso: ' . $token),
        ));

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        $response = json_decode($response, true);


        if ($response) {
            if (count($response) > 0) {


                //titulo para la pagina
                $this->view->title = "Comunicación";
                $this->view->headTitle($this->view->title);

                $form = new Application_Form_Comunicaciones(array('params' => $response));
                $this->view->form = $form;

                if ($this->getRequest()->isPost()) {

                    $formData = $this->getRequest()->getPost();

                    if ($form->isValid($formData)) {


                        $cryptor = new \Chirp\Cryptor();


                        $destinatarios = $form->getValue('idAlumnosActual');
                        $titulo = $form->getValue('titulo');
                        $mensaje = $form->getValue('mensajeComunicacion');


                        if (count($destinatarios) > 0) {


                            for ($i = 0; $i < count($destinatarios); $i++) {


                                $destinatarios[$i] = $cryptor->decrypt($destinatarios[$i]);
                                //Buscamos los datos de los alumnos selecciuonados
                                $data = $this->searchs($response, 'idAlumno', $destinatarios[$i]);


                                if ($destinatarios[$i] == $data[0]['idAlumno']) {

                                    $registrationIds = $data[0]['id'];
                                    #prep the bundle
                                    $msg = array
                                    (
                                        'body' => $mensaje,
                                        'title' => $titulo,
                                        'icon' => 'myicon',/*Default Icon*/
                                        'sound' => 'mySound'/*Default sound*/
                                    );
                                    $fields = array
                                    (
                                        'to' => $registrationIds,
                                        'notification' => $msg
                                    );
                                    $headers = array
                                    (
                                        'Authorization: key=AAAANpVKfXQ:APA91bG_GTQcHsiuHMGx-v_k0TQSCOXidcSgSX48tPAlzAzkYtDX620adLOdm04d4ck7Clu3BV1DL-Qj0gKWH-0njt9DLcIzKoPws7PXtNzVhC8e9nLFBDZiKkxhzAaNwIfTVKkEw5rT',
                                        'Content-Type: application/json'
                                    );
                                    #Send Reponse To FireBase Server
                                    $ch = curl_init();
                                    curl_setopt($ch, CURLOPT_URL, 'https://fcm.googleapis.com/fcm/send');
                                    curl_setopt($ch, CURLOPT_POST, true);
                                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

                                    $result = curl_exec($ch);
                                    $respuesta = json_decode($result, true);


                                    if ($respuesta['success']) {
                                        $res[] = array("success" => true, "idAlumno" => $data[0]['idAlumno'], "multicast_id" => $respuesta['multicast_id'], "message_id" => $respuesta['results'][0]['message_id']);

                                    } else {
                                        $res[] = array("success" => false, "idAlumno" => $data[0]['idAlumno']);

                                    }
                                    curl_close($ch);

                                }


                            }


                            if (count($res) > 0) {
                                //creo objeto Sostenedor que controla la talba sostenedor de la base de datos
                                $comunicacion = new Application_Model_DbTable_Comunicaciones();
                                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                                $db->beginTransaction();

                                //Ingresamos la comunicacion y obtenemos el id
                                $date = new DateTime;
                                $fecha = $date->format('Y-m-d H:i:s');
                                $comunicacion->agregar($titulo, $mensaje, $user, $id_curso, $idperiodo, $fecha);
                                $idcomunicacion = $comunicacion->getAdapter()->lastInsertId();


                                for ($j = 0; $j < count($res); $j++) {
                                    // Iniciamos la transaccion
                                    try {

                                        if ($res[$j]['success']) {
                                            $comunicacion->agregardetalle($idcomunicacion, $res[$j]['idAlumno'], $res[$j]['multicast_id'], $res[$j]['message_id'], 1);

                                        } else {
                                            $comunicacion->agregardetalle($idcomunicacion, $res[$j]['idAlumno'], null, null, 0);
                                        }
                                        // Sino hubo ningun inconveniente hacemos un commit
                                        $db->commit();
                                        $this->_helper->redirector('comunicaciones');
                                    } catch (Exception $e) {
                                        // Si hubo problemas. Enviamos  marcha atras
                                        $db->rollBack();
                                        echo $e;
                                        exit();
                                    }


                                }

                            }


                        }


                    }
                    else {

                        $form->populate($formData);
                    }
                }

            } else {
                var_dump("Vacio");
            }
        } else {
            var_dump('Error');
        }


    }

    public function editacomunicacionesAction()
    {

        $this->view->title = "Editar Comunicación";
        $this->view->headTitle($this->view->title);
        $form = new Application_Form_Comunicaciones();
        $form->submit->setLabel('Modificar Comunicación');
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $id = $form->getValue('idComunicacion');
                $contenido = $form->getValue('contenido');

                $tipo = $form->getValue('idTipo');
                $comunicacion = new Application_Model_DbTable_Comunicaciones();

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                // Iniciamos la transaccion
                $db->beginTransaction();
                try {

                    $comunicacion->cambiar($id, $contenido, $tipo);

                    // Sino hubo ningun inconveniente hacemos un commit
                    $db->commit();
                    $this->_helper->redirector('comunicaciones');
                } catch (Exception $e) {
                    // Si hubo problemas. Enviamos  marcha atras
                    $db->rollBack();
                    echo $e;
                }

            }
            else {
                $form->populate($formData);
            }
        }
        else {
            $id = $this->_getParam('id', 0);
            if ($id > 0) {
                $personal = new Application_Model_DbTable_Comunicaciones();
                $tabla = $personal->listarcitacionesid($id);
                $form->populate($tabla[0]);
            }
        }

    }

    public function verobservacionesAction()
    {
        $id = $this->_getParam('id');
        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;

        $usuariorol = new Zend_Session_Namespace('idRol');
        $userrol = $usuariorol->idRol;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcursos = new Zend_Session_Namespace('id_curso');
        $idcurso = $idcursos->id_curso;

        $est = new Zend_Session_Namespace('establecimiento');
        $idestablecimiento = $est->establecimiento;

        $nombrecurso = new Zend_Session_Namespace('nombre_curso');
        $nombre_curso = $nombrecurso->nombre_curso;

        $iddetallecurso = new Zend_Session_Namespace('id_detalle_curso');
        $detallecurso = $iddetallecurso->id_detalle_curso;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $ingreson = new Zend_Session_Namespace('ingresonota');
        $ingresonota = $ingreson->ingresonota;
        $cryptor = new \Chirp\Cryptor();

        $modelocurso = new Application_Model_DbTable_Cursos();
        $modelodetallecurso = new Application_Model_DbTable_Detallecursocuenta();
        $modelobservacion = new Application_Model_DbTable_Observacion();
        $modeloasignatura = new Application_Model_DbTable_Asignaturascursos();


        if ($rol == '2') {


            $activePage = $this->view->navigation()->findOneByLabel('Abierto');
            $activePage->setlabel($nombre_curso);

            $datosdocente = $modelocurso->getcursohorario($user, $idperiodo, $idcurso, $idestablecimiento);
            if (!$datosdocente) {
                $datosdocente = $datosdocente->toArray();
            }
            $resultado = array_search($idcurso, array_column($datosdocente, 'idCursos'));
            if (!is_bool($resultado) && $id > 0) {

                $lista = $modelobservacion->listar($id, $idperiodo);
                if (count($lista) > 0) {

                    if ($lista[0]['idCursos'] == $idcurso) {

                        for ($i = 0; $i < count($lista); $i++) {
                            if ($lista[$i]['idAsignatura'] == "D") {

                                $lista[$i]['Asignatura'][0]['nombreAsignatura'] = "Dirección";

                            } elseif ($lista[$i]['idAsignatura'] == "I") {
                                $lista[$i]['Asignatura'][0]['nombreAsignatura'] = "Inspectoria";
                            } elseif ($lista[$i]['idAsignatura'] == "R") {
                                $lista[$i]['Asignatura'][0]['nombreAsignatura'] = "Recreo";
                            } else {

                                $lista[$i]['Asignatura'] = $modeloasignatura->get($lista[$i]['idAsignatura'], $lista[$i]['idCursos'], $idperiodo);

                            }

                            $lista[$i]['idObservaciones'] = $cryptor->encrypt($lista[$i]['idObservaciones']);
                            $lista[$i]['idAlumnos'] = $cryptor->encrypt($lista[$i]['idAlumnos']);

                        }
                        $this->view->lista = $lista;

                    } else {

                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No posee permisos');
                        $this->view->assign('messages', $messages);

                    }

                } else {
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No posee permisos');
                    $this->view->assign('messages', $messages);
                }


            }

            $activePage->setparam('id', $idcurso . '-' . $detallecurso);
            //}

        }

        //Si el Rol es Adminitrador daem o Director

        if ($rol == '3' || $rol == '1' || $rol == '6') {

            $activePage = $this->view->navigation()->findOneByLabel('Abierto');
            $activePage->setlabel($nombre_curso);
            $activePage->setparam('id', $idcurso);

            if ($id > 0) {

                $lista = $modelobservacion->listar($id, $idperiodo);

                for ($i = 0; $i < count($lista); $i++) {
                    if ($lista[$i]['idAsignatura'] == "D") {

                        $lista[$i]['Asignatura'][0]['nombreAsignatura'] = "Dirección";

                    } elseif ($lista[$i]['idAsignatura'] == "I") {
                        $lista[$i]['Asignatura'][0]['nombreAsignatura'] = "Inspectoria";
                    } elseif ($lista[$i]['idAsignatura'] == "R") {
                        $lista[$i]['Asignatura'][0]['nombreAsignatura'] = "Recreo";
                    } else {

                        $lista[$i]['Asignatura'] = $modeloasignatura->get($lista[$i]['idAsignatura'], $lista[$i]['idCursos'], $idperiodo);

                    }

                    $lista[$i]['idObservaciones'] = $cryptor->encrypt($lista[$i]['idObservaciones']);
                    $lista[$i]['idAlumnos'] = $cryptor->encrypt($lista[$i]['idAlumnos']);

                }

                $this->view->lista = $lista;

            }
        }


    }

    public function agregaobservacionesAction()
    {

        $id_detalle_cursos = new Zend_Session_Namespace('id_detalle_curso');
        $id_detalle_curso = $id_detalle_cursos->id_detalle_curso;

        $id_cursos = new Zend_Session_Namespace('id_curso');
        $id_curso = $id_cursos->id_curso;

        $formobservaciones = new Application_Form_Observaciones();

        $this->view->form = $formobservaciones;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($formobservaciones->isValid($formData)) {

                $c = new Application_Model_DbTable_Observacion();

                $date = new DateTime;

                $usuario = new Zend_Session_Namespace('id');
                $user = $usuario->id;

                $periodo = new Zend_Session_Namespace('periodo');
                $idperiodo = $periodo->periodo;

                $asignatura = $formobservaciones->getValue('idAsignatura');
                $observacion = $formobservaciones->getValue('observacion');
                $rut = $formobservaciones->getValue('idAlumnos');
                $curso = $formobservaciones->getvalue('idCursos');
                $tipo = $formobservaciones->getvalue('idTipo');
                $fecha = $date->format('Y-m-d');

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                // Iniciamos la transaccion
                $db->beginTransaction();
                try {

                    $c->agregar($rut, $fecha, $asignatura, $observacion, $user, $idperiodo, $curso, $tipo);
                    $db->commit();
                    $this->_helper->redirector('observaciones');

                } catch (Exception $e) {
                    // Si hubo problemas. Enviamos  marcha atras
                    $db->rollBack();
                    echo $user;
                    echo $e;
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage(self::MENSAJE);
                    /// Assign the messages
                    $this->view->assign('messages', $messages);
                }

            } else {
                $formobservaciones->populate($formData);

            }

        } else {
            $idalumno = $this->_getParam('id', 0);
            if ($idalumno > 0) {
                $alumno = new Application_Model_DbTable_Alumnos();
                $alumnos = $alumno->getcomunaalumno($idalumno);

                $formobservaciones->populate($alumnos[0]);

            }
        }

    }

    public function observacionesAction()
    {

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcurso = new Zend_Session_Namespace('id_curso');
        $id_curso = $idcurso->id_curso;

        $nombrecurso = new Zend_Session_Namespace('nombre_curso');
        $nombre_curso = $nombrecurso->nombre_curso;

        $iddetallecurso = new Zend_Session_Namespace('id_detalle_curso');
        $detallecurso = $iddetallecurso->id_detalle_curso;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $ingreson = new Zend_Session_Namespace('ingresonota');
        $ingresonota = $ingreson->ingresonota;

        if ($rol == '2') {
            $activePage = $this->view->navigation()->findOneByLabel('Abierto');
            $activePage->setlabel($nombre_curso);
            $activePage->setparam('id', $id_curso);


        }

        //Si el Rol es Adminitrador daem o Director

        if ($rol == '3' || $rol == '1' || $rol == '6') {

            $activePage = $this->view->navigation()->findOneByLabel('Abierto');
            $activePage->setlabel($nombre_curso);
            $activePage->setparam('id', $id_curso);
        }

        $table = new Application_Model_DbTable_Alumnosactual();
        $lista = $table->listaralumnoscurso($id_curso, $idperiodo);
        $this->view->datos = $lista;
    }

    public function agregarasistenciaAction()
    {

        $formas = new Application_Form_Asistencia();
        $this->view->form = $formas;
    }

    public function editarasistenciaAction()
    {

        $formas = new Application_Form_Asistencia();
        $this->view->form = $formas;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcurso = new Zend_Session_Namespace('id_curso');
        $id_curso = $idcurso->id_curso;

        $id = base64_decode($this->_getParam('id'));

        if ($id > 0) {

            $modeloalumnos = new Application_Model_DbTable_Alumnosactual();
            $modeloasistencia = new Application_Model_DbTable_Asistencia();


            $listaalumnos = $modeloalumnos->listaralumnoscursoactual($id_curso, $idperiodo);
            for ($i = 0; $i < count($listaalumnos); $i++) {

                $listaalumnos[$i]['asistencia'] = $modeloasistencia->getasistencia($id, $listaalumnos[$i]['idAlumnos']);

            }
            $formas->fecha->setValue(date("d-m-Y", strtotime($listaalumnos[0]['asistencia'][0]['fechaAsistencia'])));
            $this->view->datos = $listaalumnos;
            $this->view->idAsistencia = $id;

        }

    }

    public function asistenciaAction()
    {
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $nivelcurso = new Zend_Session_Namespace('nivel_curso');
        $nivel_curso = $nivelcurso->nivel_curso;

        $idcurso = new Zend_Session_Namespace('id_curso');
        $id_curso = $idcurso->id_curso;

        $nombrecurso = new Zend_Session_Namespace('nombre_curso');
        $nombre_curso = $nombrecurso->nombre_curso;

        $ingreson = new Zend_Session_Namespace('ingresonota');
        $ingresonota = $ingreson->ingresonota;

        $modelas = new Application_Model_DbTable_Asistencia();

        $iddetallecurso = new Zend_Session_Namespace('id_detalle_curso');
        $detallecurso = $iddetallecurso->id_detalle_curso;

        //Si el es Docente
        if ($rol == '2') {
            $activePage = $this->view->navigation()->findOneByLabel('Abierto');
            $activePage->setlabel($nombre_curso);

            if ($ingresonota == 1) {
                $activePage->setparam('id', $id_curso);
            } else {
                $activePage->setparam('id', $id_curso . '-' . $detallecurso);
            }

            $listanotas = $modelas->listaradmin($id_curso, $idperiodo);
            for ($i = 0; $i < count($listanotas); $i++) {

                if ($listanotas[$i]['idAsistencia']) {

                    $listanotas[$i]['codigos'] = $modelas->ultimocodigo($listanotas[$i]['idAsistencia']);


                }
            }
            $this->view->datosnotas = $listanotas;

        }

        //Si el Rol es Adminitrador daem o Director

        if ($rol == '3' || $rol == '1' || $rol == '6') {

            $activePage = $this->view->navigation()->findOneByLabel('Abierto');
            $activePage->setlabel($nombre_curso);
            $activePage->setparam('id', $id_curso);

            $listanotas = $modelas->listaradmin($id_curso, $idperiodo);
            for ($i = 0; $i < count($listanotas); $i++) {

                if ($listanotas[$i]['idAsistencia']) {

                    $listanotas[$i]['codigos'] = $modelas->ultimocodigo($listanotas[$i]['idAsistencia']);


                }
            }
            $this->view->datosnotas = $listanotas;


        }
    }


    public function getpruebasAction()
    {
        $modelModelo = new Application_Model_DbTable_Pruebas();
        $dato = $this->_getParam('id');
        $datos = explode('-', $dato);
        $results = $modelModelo->getpruebas($datos[1], $datos[2], $datos[3]);
        $this->_helper->json($results);
    }

    public function getalumnosAction()
    {

        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcurso = new Zend_Session_Namespace('id_curso');
        $idcursos = $idcurso->id_curso;

        $modeloalumnos = new Application_Model_DbTable_Alumnosactual();
        $modeloasistencia = new Application_Model_DbTable_Asistencia();
        $results = array();

        $results[0] = $modeloalumnos->listaralumnoscurso($idcursos, $idperiodo)->toArray();
        $results[1] = $modeloasistencia->listarcategoriaasistencia(2);
        $this->_helper->json($results);
    }

    public function guardanotasAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();
            $json = file_get_contents('php://input');

            $data = json_decode($json, true);
            $modelonotas = new Application_Model_DbTable_Notas();
            $pruebas = new Application_Model_DbTable_Pruebas();

            $date = new DateTime;
            $fecha = $date->format('Y-m-d H:i:s');

            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;

            $idcurso = new Zend_Session_Namespace('id_curso');
            $idcursos = $idcurso->id_curso;

            $rutusuario = new Zend_Session_Namespace('id');
            $usuario = $rutusuario->id;
            if (empty($usuario)) {
                echo Zend_Json::encode(array('response' => 'errorsesion'));
            } else {

                //ahora los extraemos como se ve abajo
                $asignatura = $data['0']['asignatura'];
                $contenido = $data['0']['contenido'];
                $tipoev = $data['0']['tipoevaluacion'];
                $fecha = $data['0']['fecha'];
                $coef = $data['0']['coef'];
                $tipo = $data['0']['tipo'];
                $fecha = date("Y-m-d", strtotime($fecha));
                if ($tipo == 5) {
                    $asignaturamodelo = new Application_Model_DbTable_Asignaturascursos();
                    $resultado = $asignaturamodelo->getasignaturaconcepto($asignatura, $idcursos, $idperiodo);
                    for ($y = 0; $y < count($data); $y++) {

                        for ($i = 0; $i < count($resultado); $i++) {
                            if ($resultado[$i]['concepto'] == trim($data[$y]['nota'])) {
                                $data[$y]['nota'] = $resultado[$i]['notaconcepto'];
                            }
                        }
                    }

                }


                $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                // Iniciamos la transaccion
                $db->beginTransaction();
                try {

                    $pruebas->agregar($contenido, $fecha, $idcursos, $asignatura, $idperiodo, $usuario, $tipoev, $coef);
                    $idprueba = $pruebas->getAdapter()->lastInsertId();

                    //recorremos el arreglo con los datos recibidos del formulario
                    for ($i = 0; $i < count($data); $i++) {

                        $modelonotas->agregar($data[$i]['alumno'], $data['0']['asignatura'], $idcursos, $data[$i]['nota'], $usuario, $idprueba, $fecha, $idperiodo);

                    }

                    $db->commit();
                    echo Zend_Json::encode(array('redirect' => '/Libro/notas'));
                } catch (Exception $e) {
                    // Si hubo problemas. Enviamos marcha atras
                    $db->rollBack();
                    echo Zend_Json::encode(array('response' => 'errorinserta'));
                }
            }
        }
    }

    public function guardanotaseditarAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $modelonotas = new Application_Model_DbTable_Notas();
            $asignaturamodelo = new Application_Model_DbTable_Asignaturascursos();

            $rutusuario = new Zend_Session_Namespace('id');
            $usuario = $rutusuario->id;

            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;

            $idcurso = new Zend_Session_Namespace('id_curso');
            $idcursos = $idcurso->id_curso;
            if (empty($usuario)) {
                echo Zend_Json::encode(array('response' => 'errorsesion'));
            } else {

                if ($data['0']['id'] == 0 || empty($data['0']['id'])) {
                    echo Zend_Json::encode(array('response' => 'errorinserta'));
                    die();
                }
                $resultadonota = $modelonotas->get($data['0']['id']);
                $datosasignatura = $asignaturamodelo->get($resultadonota['idAsignatura'], $idcursos, $idperiodo);

                if ($datosasignatura[0]['tipoAsignatura'] == 5) {

                    $resultado = $asignaturamodelo->getasignaturaconcepto($resultadonota['idAsignatura'], $idcursos, $idperiodo);


                    for ($i = 0; $i < count($resultado); $i++) {
                        if ($resultado[$i]['concepto'] == trim($data[0]['valor'])) {
                            $data[0]['valor'] = $resultado[$i]['notaconcepto'];
                        }
                    }


                }

                if (intval($data['0']['valor']) || $data['0']['valor'] == 0) {
                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                    // Iniciamos la transaccion
                    $db->beginTransaction();
                    try {
                        $modelonotas->cambiar($data['0']['id'], $data['0']['valor']);

                        $db->commit();
                        echo Zend_Json::encode(array('response' => 'exito'));

                    } catch (Exception $e) {
                        // Si hubo problemas. Enviamos  marcha atras
                        $db->rollBack();
                        echo Zend_Json::encode(array('response' => 'errorinserta'));
                    }
                } else {

                    echo Zend_Json::encode(array('response' => 'errorinserta'));
                }


            }
        }
    }

    public function guardanotaseditarnuevoAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {

            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $modelonotas = new Application_Model_DbTable_Notas();

            $rutusuario = new Zend_Session_Namespace('id');
            $usuario = $rutusuario->id;

            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;

            $date = new DateTime;
            $fecha = $date->format('Y-m-d H:i:s');

            if (empty($usuario)) {
                echo Zend_Json::encode(array('response' => 'errorsesion'));
            } else {

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                // Iniciamos la transaccion
                $db->beginTransaction();
                try {
                    $modelonotas->agregar($data['0']['al'], $data['0']['as'], $data['0']['cu'], $data['0']['valor'], $usuario, $data['0']['ev'], $fecha, $idperiodo);

                    $db->commit();
                    echo Zend_Json::encode(array('redirect' => '/Libro/notaeditar/id/' . $data['0']['ev']));
                } catch (Exception $e) {
                    // Si hubo problemas. Enviamos marcha atras
                    $db->rollBack();
                    echo Zend_Json::encode(array('response' => 'errorinserta'));
                }
            }
        }
    }

    public function vernotasAction()
    {
        $this->_helper->layout->disableLayout();
        $id = $this->_getParam('id');

        if ($id > 0) {

            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;

            $modelnotas = new Application_Model_DbTable_Notas();
            $modelasignatura = new Application_Model_DbTable_Asignaturascursos();
            $modelocurso = new Application_Model_DbTable_Cursos();
            $idcursos = new Zend_Session_Namespace('id_curso');
            $idcurso = $idcursos->id_curso;
            $datoscurso = $modelocurso->get($idcurso);
            $this->view->tipo = $datoscurso[0]['idCodigoGrado'];


            $listanotas = $modelnotas->listarnotasbasica($id, $idperiodo);

            if ($listanotas[0]['tipoAsignatura'] == 5) {
                $resultadoconcepto = $modelasignatura->getasignaturaconcepto($listanotas[0]['idAsignatura'], $idcurso, $idperiodo);
                for ($y = 0; $y < count($listanotas); $y++) {

                    for ($i = 0; $i < count($resultadoconcepto); $i++) {
                        if ($resultadoconcepto[$i]['notaconcepto'] == $listanotas[$y]['nota']) {
                            $listanotas[$y]['nota'] = $resultadoconcepto[$i]['concepto'];
                        }
                    }
                }
            }
            $this->view->listanotas = $listanotas;

        }

    }

    public function verasistenciaAction()
    {
        $this->_helper->layout->disableLayout();
        $id = base64_decode($this->_getParam('id'));

        if ($id > 0) {

            $nivel = new Zend_Session_Namespace('nivel_curso');
            $idnivel = $nivel->nivel_cuso;

            $modelasistencia = new Application_Model_DbTable_Asistencia();
            $listaas = $modelasistencia->listaralumnosbasica($id);
            $this->view->listaasistencia = $listaas;

        }

    }

    public function verpruebasAction()
    {
        $this->_helper->layout->disableLayout();
        $id = $this->_getParam('id');

        if ($id > 0) {

            $modelprueba = new Application_Model_DbTable_Pruebas();

            $listapruebas = $modelprueba->get($id);

            $this->view->listapruebas = $listapruebas;

        }

    }

    public function citacioneliminarAction()
    {
        $id = $this->_getParam('id', 0);
        $tabla = new Application_Model_DbTable_Comunicaciones();

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        // Iniciamos la transaccion
        $db->beginTransaction();
        try {

            $tabla->borrar($id);
            $db->commit();
            $this->_helper->redirector('comunicaciones');
        } catch (Exception $e) {
            // Si hubo problemas. Enviamos marcha atras
            $db->rollBack();
            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No se puede eliminar este registro, posee datos asociados');
            $this->view->assign('messages', $messages);
        }
    }

    public function downloadAction()
    {
        defined('APPLICATION_UPLOADS_DIR')
        || define('APPLICATION_UPLOADS_DIR', realpath(dirname(__FILE__) . '/../documentos/guias'));

        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        try {
            $this->getResponse()->setHeader('Content-Type: foo/bar');
            $filename = $this->getRequest()->getParam('filename');
            $this->getResponse()->appendBody(file_get_contents(APPLICATION_UPLOADS_DIR . '/' . $filename));
        } catch (Exception $e) {
            echo $e;
        }

    }


    public function getdiasAction()
    {
        $modelModelo = new Application_Model_DbTable_Asistencia();
        $datos = $this->_getParam('id');

        $id_cursos = new Zend_Session_Namespace('id_curso');
        $id_curso = $id_cursos->id_curso;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $rutusuario = new Zend_Session_Namespace('id');
        $usuario = $rutusuario->id;

        $results = $modelModelo->getdias($id_curso, $idperiodo, $usuario);
        $this->_helper->json($results);
    }


    public function guardarordenAction()
    {

        if ($this->getRequest()->isXmlHttpRequest()) {

            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();

            $idcurso = new Zend_Session_Namespace('id_curso');
            $idcursos = $idcurso->id_curso;
            $idcuentacursos = new Zend_Session_Namespace('idcuentacurso');
            $cuentacurso = $idcuentacursos->idcuentacurso;

            $ingreson = new Zend_Session_Namespace('ingresonota');
            $ingresonota = $ingreson->ingresonota;

            $cargo = new Zend_Session_Namespace('cargo');
            $rol = $cargo->cargo;
            if ($rol == 2 || $rol == 1 || $rol == 3 || $rol == 6) {

                $json = file_get_contents('php://input');
                $data = json_decode($json, true);

                $tabla = new Application_Model_DbTable_Alumnosactual();

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $db->beginTransaction();

                try {
                    for ($i = 0; $i < count($data); $i++) {
                        $tabla->cambiarorden($data[$i]['idalumno'], $data[$i]['orden']);
                    }

                    $db->commit();
                    echo Zend_Json::encode(array('redirect' => '/Libro/abrir/id/' . $idcursos));


                } catch (Exception $e) {
                    $db->rollBack();
                    echo Zend_Json::encode(array('response' => 'error'));

                }
            }
        }
    }


    public function getnucleoAction()
    {

        $modelModelo = new Application_Model_DbTable_Asignaturas();
        $dato = $this->_getParam('id');
        if ($dato == 'todo') {
            $results = $modelModelo->listarnucleo();
        } else {
            $results = $modelModelo->listarnucleoporambito($dato);
        }

        $this->_helper->json($results);
    }

    public function getasignaturaspreAction()
    {
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $id_cursos = new Zend_Session_Namespace('id_curso');
        $id_curso = $id_cursos->id_curso;

        $asignaturamodelo = new Application_Model_DbTable_Asignaturascursos();
        $dato = $this->_getParam('id');
        $tipos = array('1');

        if ($dato == 'todo') {
            $results = $asignaturamodelo->listarniveltipos($id_curso, $idperiodo, $tipos);
        } else {
            $datoset = str_replace("''", "", $dato);
            $results = $asignaturamodelo->listarasignaturapornucleo($dato, $id_curso, $idperiodo);
        }

        $this->_helper->json($results);
    }

    public function getalumnospreAction()
    {

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcurso = new Zend_Session_Namespace('id_curso');
        $idcursos = $idcurso->id_curso;

        $modelModelo = new Application_Model_DbTable_Alumnosactual();

        $results = $modelModelo->listaralumnoscurso($idcursos, $idperiodo);
        $this->_helper->json($results);
    }


    public function getasignaturasAction()
    {
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcursos = new Zend_Session_Namespace('id_curso');
        $idcurso = $idcursos->id_curso;

        $asignaturamodelo = new Application_Model_DbTable_Asignaturascursos();
        $dato = $this->_getParam('id');
        $resultado = $asignaturamodelo->getasignaturaconcepto($dato, $idcurso, $idperiodo);

        $this->_helper->json($resultado);
    }


    public function notasnuevoAction()
    {
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;


        $idcurso = new Zend_Session_Namespace('id_curso');
        $id_curso = $idcurso->id_curso;

        $nombrecurso = new Zend_Session_Namespace('nombre_curso');
        $nombre_curso = $nombrecurso->nombre_curso;

        $roles = new Zend_Session_Namespace('cargo');
        $rol = $roles->cargo;

        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;


        $modelnotas = new Application_Model_DbTable_Notas();
        $asignaturamodel = new Application_Model_DbTable_Asignaturascursos();
        $modelcurso = new Application_Model_DbTable_Cursos();

        $detalleest = $modelcurso->listarcursoid($id_curso, $idperiodo);


        //Tipos de asignaturas 1=normal 2=Taller 4=asignatura que compone a otra 5= conceptos
        $tipos = array('1', '2', '3', '4', '5');

        //Si el es Docente
        if ($rol == '2') {

            $activePage = $this->view->navigation()->findOneByLabel('Abierto');
            $activePage->setlabel($nombre_curso);
            $activePage->setparam('id', $id_curso);
            $row = $modelcurso->getasignaturashorario($id_curso, $idperiodo, $user);
            $this->view->listaasignatura = $row;

            $modelalumnos = new Application_Model_DbTable_Alumnosactual();
            $listaalumnos = $modelalumnos->listaralumnoscursoactual($id_curso, $idperiodo);
            $this->view->alumnos = $listaalumnos;
            $this->view->aprox = $detalleest[0]['aproxPeriodo'];

        }

        // Si el Rol es Administrador daem o Director
        if ($rol == '1' || $rol == '3' || $rol == '6') {

            $activePage = $this->view->navigation()->findOneByLabel('Abierto');
            $activePage->setlabel($nombre_curso);
            $activePage->setparam('id', $id_curso);

            $rowsetasignatura = $asignaturamodel->listarniveltipos($id_curso, $idperiodo, $tipos);
            $this->view->listaasignatura = $rowsetasignatura;

            $listanotas = $modelnotas->listaradmin($id_curso, $idperiodo);
            $this->view->datosnotas = $listanotas;

            $modelalumnos = new Application_Model_DbTable_Alumnosactual();
            $listaalumnos = $modelalumnos->listaralumnoscursoactual($id_curso, $idperiodo);
            $this->view->alumnos = $listaalumnos;
            $this->view->aprox = $detalleest[0]['aproxPeriodo'];

        }
    }


    public function getnotasasignaturaAction()

    {
        $dias = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");
        $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");


        $this->_helper->layout->disableLayout();

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcursos = new Zend_Session_Namespace('id_curso');
        $idcurso = $idcursos->id_curso;

        $modelonotas = new Application_Model_DbTable_Notas();
        $modeloprueba = new Application_Model_DbTable_Pruebas();
        $modeloalumnos = new Application_Model_DbTable_Alumnosactual();
        $modeloasignatura = new Application_Model_DbTable_Asignaturascursos();
        $modelcurso = new Application_Model_DbTable_Cursos();
        $idasignatura = $this->_getParam('id');

        $asignaturas = new Zend_Session_Namespace('idAsignatura');
        $asignaturas->idAsignatura = $idasignatura;

        $resultadoasignatura = $modeloasignatura->get($idasignatura, $idcurso, $idperiodo);

        $datoscurso = $modelcurso->listarcursoid($idcurso, $idperiodo);

        $examenes = new Zend_Session_Namespace('examen');
        $examenes->examen = $datoscurso[0]['examen'];

        $listadealumnos = $modeloalumnos->listaralumnoscursoactual($idcurso, $idperiodo);
        $largoalumnos = count($listadealumnos);
        $datostaller = array();


        //Taller por alumnos
        if (count($resultadoasignatura) > 0) {
            //Si la Asignatura es normal, buscamos si algun taller corresponde a la asignatura
            if ($resultadoasignatura[0]['tipoAsignatura'] == 1) {
                $datostalleres = $modeloasignatura->gettallerasignaturaalumnos($idasignatura);
                $modelotallerdetalle = new Application_Model_DbTable_Asignaturascursos();

                $promediotalleralumnos = array();
                $promediotalleralumnoss = array();

                if ($datostalleres) {

                    for ($i = 0; $i < count($datostalleres); $i++) {
                        for ($j = 0; $j < $largoalumnos; $j++) {
                            $detalletaller = $modelotallerdetalle->gettallersegmento($datostalleres[$i]['idConfiguracionTaller'], 1, $listadealumnos[$j]['idAlumnos']);
                            $detalletallers = $modelotallerdetalle->gettallersegmento($datostalleres[$i]['idConfiguracionTaller'], 2, $listadealumnos[$j]['idAlumnos']);

                            //Primer Semestre
                            if ($datostalleres[$i]['tiempoOpcion'] == 1) {
                                $promtalleralumnos[$i]['idAsignatura'] = $datostalleres[$i]['idAsignaturaTaller'];
                                $promtalleralumnos[$i]['coef'] = 1;
                                $promtalleralumnos[$i]['tiempo'] = 1;
                                $promtalleralumnos[$i]['forma'] = $datostalleres[$i]['forma'];
                                $promtalleralumnos[$i]['taller'] = 1;
                                if ($datostalleres[$i]['forma'] == 2) {
                                    $promtalleralumnos[$i]['fechaEvaluacion'] = $datostalleres[$i]['nombreAsignatura'] . ' ' . $datostalleres[$i]['porcentaje'] . '%';
                                } else {
                                    $promtalleralumnos[$i]['fechaEvaluacion'] = $datostalleres[$i]['nombreAsignatura'];
                                }
                                $promtalleralumnos[$i]['idEvaluacion'] = "taller" . $datostalleres[$i]['idConfiguracionTaller'];


                                if ($detalletaller) {
                                    $datosalumnostaller = $modelonotas->listarpromedioalumnotaller($listadealumnos[$j]['idAlumnos'], $idperiodo, 1, $idcurso, $detalletaller[0]['idAsignatura']);

                                    if ($datosalumnostaller) {
                                        for ($k = 0; $k < count($datosalumnostaller); $k++) {

                                            if ($datostalleres[$i]['idAsignatura'] == $datosalumnostaller[$k]['idAsignatura']) {
                                                if ($datosalumnostaller[$k]['coef'] == 2) {
                                                    if ($datosalumnostaller[$k]['nota'] > 0) {
                                                        $promediotalleralumnos[$j][$i][] = $datosalumnostaller[$k]['nota'];
                                                        $promediotalleralumnos[$j][$i][] = $datosalumnostaller[$k]['nota'];

                                                    } else {
                                                        $promediotalleralumnos[$j][$i][] = 0;
                                                        $promediotalleralumnos[$j][$i][] = 0;
                                                    }

                                                } else {
                                                    if ($datosalumnostaller[$k]['nota'] > 0) {
                                                        $promediotalleralumnos[$j][$i][] = $datosalumnostaller[$k]['nota'];
                                                    } else {
                                                        $promediotalleralumnos[$j][$i][] = 0;
                                                    }

                                                }


                                            }


                                        }
                                    }

                                    if (!empty($promediotalleralumnos[$j][$i])) {
                                        $promsetalumnos = array_diff($promediotalleralumnos[$j][$i], array('0'));
                                    } else {
                                        $promsetalumnos = 0;
                                    }


                                    if (is_array($promsetalumnos)) {
                                        if (count($promsetalumnos) > 0) {

                                            if ($datoscurso[0]['aproxAsignatura'] == 1) {
                                                $promtalleralumnos[$i]['alumnos'][$j]['nota'] = round(array_sum($promsetalumnos) / count($promsetalumnos));
                                                $promtalleralumnos[$i]['alumnos'][$j]['nota'] = intval($promtalleralumnos[$i]['alumnos'][$j]['nota']);

                                            } else {
                                                $promtalleralumnos[$i]['alumnos'][$j]['nota'] = intval(array_sum($promsetalumnos) / count($promsetalumnos));

                                            }
                                        } else {
                                            $promtalleralumnos[$i]['alumnos'][$j]['nota'] = 0;
                                        }
                                    } else {
                                        $promtalleralumnos[$i]['alumnos'][$j]['nota'] = 0;
                                    }


                                } else {
                                    $promtalleralumnos[$i]['alumnos'][$j]['nota'] = 0;

                                }
                            } else {
                                //$promtalleralumnos[$i]['alumnos'][$j]['nota']= 0;
                                //$promtalleralumnos[$i]['alumnos'][$j]['auth'] = 0;

                            }

                            //Segundo Semestre
                            if ($datostalleres[$i]['tiempoOpcion'] == 2) {
                                $promtalleralumnoss[$i]['idAsignatura'] = $datostalleres[$i]['idAsignaturaTaller'];
                                $promtalleralumnoss[$i]['coef'] = 1;
                                $promtalleralumnoss[$i]['tiempo'] = 2;
                                $promtalleralumnoss[$i]['forma'] = $datostalleres[$i]['forma'];
                                $promtalleralumnoss[$i]['taller'] = 1;
                                if ($datostalleres[$i]['forma'] == 2) {
                                    $promtalleralumnoss[$i]['fechaEvaluacion'] = $datostalleres[$i]['nombreAsignatura'] . ' ' . $datostalleres[$i]['porcentaje'] . '%';
                                } else {
                                    $promtalleralumnoss[$i]['fechaEvaluacion'] = $datostalleres[$i]['nombreAsignatura'];
                                }
                                $promtalleralumnoss[$i]['idEvaluacion'] = "taller" . $datostalleres[$i]['idConfiguracionTaller'];
                                //$promtalleralumnoss[$i]['fechaEvaluacion'] = $datostalleres[$i]['nombreAsignatura'] . ' ' . $datostalleres[$i]['porcentaje'] . '%';
                                $promtalleralumnoss[$i]['idEvaluacion'] = "taller" . $datostalleres[$i]['idConfiguracionTaller'];
                                if ($detalletallers) {
                                    $datosalumnostallers = $modelonotas->listarpromedioalumnotaller($listadealumnos[$j]['idAlumnos'], $idperiodo, 2, $idcurso, $detalletallers[0]['idAsignatura']);


                                    if ($datosalumnostallers) {
                                        for ($k = 0; $k < count($datosalumnostallers); $k++) {

                                            if ($datostalleres[$i]['idAsignatura'] == $datosalumnostallers[$k]['idAsignatura']) {

                                                if ($datosalumnostallers[$k]['coef'] == 2) {
                                                    if ($datosalumnostallers[$k]['nota'] > 0) {
                                                        $promediotalleralumnoss[$j][$i][] = $datosalumnostallers[$k]['nota'];
                                                        $promediotalleralumnoss[$j][$i][] = $datosalumnostallers[$k]['nota'];

                                                    } else {
                                                        $promediotalleralumnoss[$j][$i][] = 0;
                                                        $promediotalleralumnoss[$j][$i][] = 0;
                                                    }

                                                } else {
                                                    if ($datosalumnostallers[$k]['nota'] > 0) {
                                                        $promediotalleralumnoss[$j][$i][] = $datosalumnostallers[$k]['nota'];
                                                    } else {
                                                        $promediotalleralumnoss[$j][$i][] = 0;
                                                    }

                                                }


                                            }


                                        }
                                    }

                                    if (!empty($promediotalleralumnoss[$j][$i])) {
                                        $promsetalumnoss = array_diff($promediotalleralumnoss[$j][$i], array('0'));
                                    } else {
                                        $promsetalumnoss = 0;
                                    }


                                    if (is_array($promsetalumnoss)) {
                                        if (count($promsetalumnoss) > 0) {

                                            if ($datoscurso[0]['aproxAsignatura'] == 1) {
                                                $promtalleralumnoss[$i]['alumnos'][$j]['nota'] = round(array_sum($promsetalumnoss) / count($promsetalumnoss));
                                                $promtalleralumnoss[$i]['alumnos'][$j]['nota'] = intval($promtalleralumnoss[$i]['alumnos'][$j]['nota']);

                                            } else {
                                                $promtalleralumnoss[$i]['alumnos'][$j]['nota'] = intval(array_sum($promsetalumnoss) / count($promsetalumnoss));
                                            }
                                        } else {
                                            $promtalleralumnoss[$i]['alumnos'][$j]['nota'] = 0;
                                        }
                                    } else {
                                        $promtalleralumnoss[$i]['alumnos'][$j]['nota'] = 0;
                                    }


                                } else {
                                    $promtalleralumnoss[$i]['alumnos'][$j]['nota'] = 0;
                                }
                            } else {
                                //$promtalleralumnoss[$j][$i] = array();
                            }


                        }//Fin if talleres


                    }//Fin for Alumnos


                }


            }
        }


        //Fin Taller por alumnos

        //Taller Por Segmento Curso Completo
        $datostallersem = $modeloasignatura->gettaller2($idasignatura, $idperiodo, array(1, 2), 1, array(1));
        $datostallersemseg = $modeloasignatura->gettaller2($idasignatura, $idperiodo, array(1, 2), 1, array(2));
        $largotallerprimero = count($datostallersem);
        $largotallersegundo = count($datostallersemseg);

        $promediotaller = array();
        if ($largotallerprimero > 0) {

            for ($i = 0; $i < $largotallerprimero; $i++) {
                if (!empty($datostallersem[$i]['idConfiguracionTaller'])) {
                    $validataller[$i] = $datostallersem[$i]['idAsignaturaTaller'];
                    for ($j = 0; $j < $largoalumnos; $j++) {

                        $datosalumnostaller = $modelonotas->listarpromedioalumnotaller($listadealumnos[$j]['idAlumnos'], $idperiodo, 1, $idcurso, $datostallersem[$i]['idAsignatura']);

                        for ($k = 0; $k < count($datosalumnostaller); $k++) {
                            if ($datostallersem[$i]['idAsignatura'] == $datosalumnostaller[$k]['idAsignatura']) {
                                if ($datosalumnostaller[$k]['coef'] == 2) {
                                    if ($datosalumnostaller[$k]['nota'] > 0) {
                                        $promediotaller[$j][$i][] = $datosalumnostaller[$k]['nota'];
                                        $promediotaller[$j][$i][] = $datosalumnostaller[$k]['nota'];

                                    } else {
                                        $promediotaller[$j][$i][] = 0;
                                        $promediotaller[$j][$i][] = 0;
                                    }

                                } else {
                                    if ($datosalumnostaller[$k]['nota'] > 0) {
                                        $promediotaller[$j][$i][] = $datosalumnostaller[$k]['nota'];
                                    } else {
                                        $promediotaller[$j][$i][] = 0;
                                    }

                                }


                            }

                        }


                        //Primer Semestre
                        $promtaller[$i]['idAsignatura'] = $datostallersem[$i]['idAsignaturaTaller'];
                        $promtaller[$i]['coef'] = 1;
                        $promtaller[$i]['tiempo'] = 1;
                        $promtaller[$i]['forma'] = $datostallersem[$i]['forma'];
                        $promtaller[$i]['taller'] = 1;
                        $promtaller[$i]['fechaEvaluacion'] = $datostallersem[$i]['nombreAsignatura'];
                        $promtaller[$i]['idEvaluacion'] = "taller" . $datostallersem[$i]['idConfiguracionTaller'];


                        $promtaller[$i]['porcentaje'] = $datostallersem[$i]['porcentaje'];

                        if (!empty($promediotaller[$j][$i])) {
                            $promset = array_diff($promediotaller[$j][$i], array('0'));
                        } else {
                            $promset = 0;
                        }

                        if (is_array($promset)) {
                            if (count($promset) > 0) {
                                if ($datoscurso[0]['aproxAsignatura'] == 1) {
                                    $promtaller[$i]['alumnos'][$j]['nota'] = round(array_sum($promset) / count($promset));

                                } else {
                                    $promtaller[$i]['alumnos'][$j]['nota'] = intval(array_sum($promset) / count($promset));
                                }
                            } else {
                                $promtaller[$i]['alumnos'][$j]['nota'] = 0;
                            }

                        } else {
                            $promtaller[$i]['alumnos'][$j]['nota'] = 0;
                        }


                    }

                }


            }
        }

        //Segundo Semestre
        if ($largotallersegundo > 0) {

            for ($i = 0; $i < $largotallersegundo; $i++) {
                if (!empty($datostallersemseg[$i]['idConfiguracionTaller'])) {
                    $validatallers[$i] = $datostallersemseg[$i]['idAsignaturaTaller'];
                    for ($j = 0; $j < $largoalumnos; $j++) {
                        $promediofinal = 0;
                        $datosalumnostallers = $modelonotas->listarpromedioalumnotaller($listadealumnos[$j]['idAlumnos'], $idperiodo, 2, $idcurso, $datostallersemseg[$i]['idAsignatura']);

                        for ($k = 0; $k < count($datosalumnostallers); $k++) {
                            if ($datostallersemseg[$i]['idAsignatura'] == $datosalumnostallers[$k]['idAsignatura']) {
                                if ($datosalumnostallers[$k]['coef'] == 2) {
                                    if ($datosalumnostallers[$k]['nota'] > 0) {
                                        $promediotallers[$j][$i][] = $datosalumnostallers[$k]['nota'];
                                        $promediotallers[$j][$i][] = $datosalumnostallers[$k]['nota'];

                                    } else {
                                        $promediotallers[$j][$i][] = 0;
                                        $promediotallers[$j][$i][] = 0;
                                    }

                                } else {
                                    if ($datosalumnostallers[$k]['nota'] > 0) {
                                        $promediotallers[$j][$i][] = $datosalumnostallers[$k]['nota'];
                                    } else {
                                        $promediotallers[$j][$i][] = 0;
                                    }

                                }


                            }


                        }
                        //Segundo Semestre
                        $promtallers[$i]['idAsignatura'] = $datostallersemseg[$i]['idAsignaturaTaller'];
                        $promtallers[$i]['coef'] = 1;
                        $promtallers[$i]['tiempo'] = 2;
                        $promtallers[$i]['forma'] = $datostallersemseg[$i]['forma'];
                        $promtallers[$i]['taller'] = 1;
                        $promtallers[$i]['fechaEvaluacion'] = $datostallersemseg[$i]['nombreAsignatura'];
                        $promtallers[$i]['idEvaluacion'] = "taller" . $datostallersemseg[$i]['idConfiguracionTaller'];


                        $promtallers[$i]['porcentaje'] = $datostallersemseg[$i]['porcentaje'];

                        if (!empty($promediotallers[$j][$i])) {
                            $promsets = array_diff($promediotallers[$j][$i], array('0'));
                        } else {
                            $promsets = 0;
                        }

                        if (is_array($promsets)) {
                            if (count($promsets) > 0) {
                                if ($datoscurso[0]['aproxAsignatura'] == 1) {
                                    $promtallers[$i]['alumnos'][$j]['nota'] = round(array_sum($promsets) / count($promsets));

                                } else {
                                    $promtallers[$i]['alumnos'][$j]['nota'] = intval(array_sum($promsets) / count($promsets));
                                }
                            } else {
                                $promtallers[$i]['alumnos'][$j]['nota'] = 0;
                            }

                        } else {
                            $promtallers[$i]['alumnos'][$j]['nota'] = 0;
                        }

                    }

                }


            }
        }

        $resultado['notas'] = $modeloprueba->listapruebasasignatura($idasignatura, $idcurso, $idperiodo);
        //var_dump($resultado);

        $largoresultado = count($resultado['notas']);


        //Si la Asignatura es Taller
        if ($resultadoasignatura[0]['tipoAsignatura'] == 2) {

            $datostalleres = $modeloasignatura->gettallerconfiguracion($resultadoasignatura[0]['idAsignaturaCurso']);
            $modelotallerdetalle = new Application_Model_DbTable_Asignaturascursos();
            if ($datostalleres) {
                for ($j = 0; $j < count($datostalleres); $j++) {
                    if ($datostalleres[$j]['tipoAjuste'] == 1) {
                        //El Taller es para todos los alumnos
                        for ($i = 0; $i < $largoresultado; $i++) {

                            //$resultado['notas'][$i]['fechaEvaluacion'] = $this->datetranslate($resultado['notas'][$i]['fechaEvaluacion']);
                            $resultado['notas'][$i]['alumnos'] = $modelonotas->getnotasasignatura($resultado['notas'][$i]['idEvaluacion'], $idcurso, $idperiodo);
                            //si el resultado de las notas no coinciden con la cantidad de alumnos se realiza una validacion de las notas de esa evaluacion
                            if (count($resultado[$i]['alumnos']) != count($listadealumnos)) {
                                $date = new DateTime;
                                $fechaactual = $date->format('Y-m-d');

                                for ($j = 0; $j < count($listadealumnos); $j++) {
                                    if ($modelonotas->validarnotaalumno($listadealumnos[$j]['idAlumnos'], $resultado['notas'][$i]['idEvaluacion'], $idasignatura, $idcurso, $idperiodo)) {
                                        //Creamos las notas a los alumnos correspondientes
                                        $modelonotas->agregar($listadealumnos[$j]['idAlumnos'], $idasignatura, $idcurso, 0, $resultado['notas'][$i]['idCuenta'], $resultado['notas'][$i]['idEvaluacion'], $fechaactual, $idperiodo);

                                    }


                                }

                                //recargamos las notas
                                $resultado['notas'][$i]['alumnos'] = $modelonotas->getnotasasignatura($resultado['notas'][$i]['idEvaluacion'], $idcurso, $idperiodo);
                            }


                        }
                    } elseif ($datostalleres[$j]['tipoAjuste'] == 2) {


                        for ($l = 0; $l < count($listadealumnos); $l++) {

                            $resultadotaller = $modeloasignatura->gettallerdetalles($datostalleres[$j]['idConfiguracionTaller'], $listadealumnos[$l]['idAlumnos']);

                            if ($resultadotaller) { //Se crean los datos del alumno al que corresponden las notas
                                $listadealumnos[$l]['auth'] = 1;
                                if ($resultadotaller[0]['tiempoOpcion'] == 1) {
                                    $listadealumnos[$l]['tiempop'] = 1;
                                } elseif ($resultadotaller[0]['tiempoOpcion'] == 2) {
                                    $listadealumnos[$l]['tiempos'] = 2;
                                }


                            }
                        }
                        $resultado['notas'][0]['authalumnos'] = true;

                    }

                }

            }


            //Asignamos las notas que corresponden a los alumnos.
            for ($i = 0; $i < count($resultado['notas']); $i++) {
                $resultado['notas'][$i]['fechaEvaluacion'] = $this->datetranslate($resultado['notas'][$i]['fechaEvaluacion']);
                $resultado['notas'][$i]['alumnos'] = $modelonotas->getnotasasignatura($resultado['notas'][$i]['idEvaluacion'], $idcurso, $idperiodo);

                //si el resultado de las notas no coinciden con la cantidad de alumnos se realiza una validacion de las notas de esa evaluacion
                if (count($resultado['notas'][$i]['alumnos']) != count($listadealumnos)) {
                    $date = new DateTime;
                    $fechaactual = $date->format('Y-m-d');

                    for ($j = 0; $j < count($listadealumnos); $j++) {

                        if ($modelonotas->validarnotaalumno($listadealumnos[$j]['idAlumnos'], $resultado['notas'][$i]['idEvaluacion'], $idasignatura, $idcurso, $idperiodo)) {
                            $modelonotas->agregar($listadealumnos[$j]['idAlumnos'], $idasignatura, $idcurso, 0, $resultado[$i]['idCuenta'], $resultado['notas'][$i]['idEvaluacion'], $fechaactual, $idperiodo);

                        }

                    }

                    //recargamos las notas
                    $resultado['notas'][$i]['alumnos'] = $modelonotas->getnotasasignatura($resultado['notas'][$i]['idEvaluacion'], $idcurso, $idperiodo);
                }

                //Asignamos la autorizacion a los alumnos correspondientes
                for ($j = 0; $j < count($listadealumnos); $j++) {
                    //si tiene autorizacion para el primer semestre
                    if ($listadealumnos[$j]['auth'] == 1 && $resultado[$i]['tiempo'] == $listadealumnos[$j]['tiempop']) {
                        $resultado['notas'][$i]['alumnos'][$j]['auth'] = true;

                    }

                    //si tiene autorizacion para el Segundo semestre
                    if ($listadealumnos[$j]['auth'] == 1 && $resultado['notas'][$i]['tiempo'] == $listadealumnos[$j]['tiempos']) {
                        $resultado['notas'][$i]['alumnos'][$j]['auth'] = true;

                    }


                }


            }


        } elseif ($resultadoasignatura[0]['tipoAsignatura'] == 1 || $resultadoasignatura[0]['tipoAsignatura'] == 4 || $resultadoasignatura[0]['tipoAsignatura'] == 5) {
            for ($i = 0; $i < count($resultado['notas']); $i++) {
                $resultado['notas'][$i]['fechaEvaluacion'] = $this->datetranslate($resultado['notas'][$i]['fechaEvaluacion']);
                $resultado['notas'][$i]['alumnos'] = $modelonotas->getnotasasignatura($resultado['notas'][$i]['idEvaluacion'], $idcurso, $idperiodo);
                //si el resultado de las notas no coinciden con la cantidad de alumnos se realiza una validacion de las notas de esa evaluacion
                if (count($resultado['notas'][$i]['alumnos']) != count($listadealumnos)) {
                    $date = new DateTime;
                    $fechaactual = $date->format('Y-m-d');

                    for ($j = 0; $j < count($listadealumnos); $j++) {


                        if ($modelonotas->validarnotaalumno($listadealumnos[$j]['idAlumnos'], $resultado['notas'][$i]['idEvaluacion'], $idasignatura, $idcurso, $idperiodo)) {
                            //Creamos las notas a los alumnos correspondientes
                            $modelonotas->agregar($listadealumnos[$j]['idAlumnos'], $idasignatura, $idcurso, 0, $resultado['notas'][$i]['idCuenta'], $resultado['notas'][$i]['idEvaluacion'], $fechaactual, $idperiodo);

                        }


                    }

                    //recargamos las notas
                    $resultado['notas'][$i]['alumnos'] = $modelonotas->getnotasasignatura($resultado['notas'][$i]['idEvaluacion'], $idcurso, $idperiodo);
                }


            }

        }


        //Realizamos la Llamada a la funcion getpromedios y ontenemos los promedios
        $resultadopromedios = $this->getpromedioperiodoAction();


        //Examenes
        $examenes = new Zend_Session_Namespace('examen');
        $examen = $examenes->examen;
        $largoresultado = count($resultado['notas']);
        if ($examen == 1) {

            for ($i = 0; $i < $largoresultado; $i++) {
                if ($resultado['notas'][$i]['tipoNota'] == 2 && $resultado['notas'][$i]['tiempo'] == 6) {
                    $criterio = $resultado['notas'][$i]['criterio'];


                    if ($resultadopromedios) {
                        for ($j = 0; $j < count($resultado['notas'][$i]['alumnos']); $j++) {
                            if ($resultadopromedios[$j]['final'] < $criterio) {
                                $resultado['notas'][$i]['alumnos'][$j]['auth'] = true;
                                if ($resultado['notas'][$i]['alumnos'][$j]['nota'] > 0) {


                                    $totalex = 100 - $resultado['notas'][$i]['porcentajeExamen'];
                                    $sumaex = $resultado['notas'][$i]['alumnos'][$j]['nota'] * ($resultado['notas'][$i]['porcentajeExamen'] / 100);
                                    if ($datoscurso[0]['aproxExamen'] == 1) {
                                        $resultadopromedios[$j]['finalex'] = round(($resultadopromedios[$j]['final'] * ($totalex / 100)) + $sumaex);
                                    } else {
                                        $resultadopromedios[$j]['finalex'] = intval(($resultadopromedios[$j]['final'] * ($totalex / 100)) + $sumaex);
                                    }


                                } else {
                                    $resultadopromedios[$j]['finalex'] = $resultadopromedios[$j]['final'];
                                }


                            } else {
                                $resultado['notas'][$i]['alumnos'][$j]['auth'] = false;
                                $resultadopromedios[$j]['finalex'] = $resultadopromedios[$j]['final'];
                            }


                        }

                    }


                }

            }

        }


        //lista de alumnos


        $asignaturamodelo = new Application_Model_DbTable_Asignaturascursos();
        $dato = $this->_getParam('id');
        $resultadoconcepto = $asignaturamodelo->getasignaturaconcepto($dato, $idcurso, $idperiodo);

        for ($i = 0; $i < count($resultado['notas']); $i++) {
            for ($j = 0; $j < count($resultado['notas'][$i]['alumnos']); $j++) {

                for ($k = 0; $k < count($resultadoconcepto); $k++) {
                    if ($resultadoconcepto[$k]['desde'] <= $resultado['notas'][$i]['alumnos'][$j]['nota'] && $resultadoconcepto[$k]['hasta'] >= $resultado['notas'][$i]['alumnos'][$j]['nota']) {
                        //if ($resultadoconcepto[$k]['notaconcepto'] == $resultado['notas'][$i]['alumnos'][$j]['nota']) {
                        $resultado['notas'][$i]['alumnos'][$j]['notaconconcepto'] = $resultadoconcepto[$k]['concepto'];
                    }


                }

            }

        }


        //Pasamos los Resultados del taller a los datos de notas
        //Primer Semestre
        $largopromtaller = count($promtaller);
        if ($largopromtaller > 0) {
            for ($i = 0; $i < $largopromtaller; $i++) {
                $resultado['notas'][] = $promtaller[$i];
            }
        }
        //Segundo Semesre
        $largopromtallers = count($promtallers);
        if ($largopromtallers > 0) {
            for ($i = 0; $i < $largopromtallers; $i++) {
                $resultado['notas'][] = $promtallers[$i];
            }
        }

        if (count($promtalleralumnos) > 0) {
            foreach ($promtalleralumnos as $a => $j) {
                if (!is_null($promtalleralumnos[$a])) {
                    $resultado['notas'][] = $promtalleralumnos[$a];
                }

            }
        }


        if (count($promtalleralumnoss) > 0) {
            foreach ($promtalleralumnoss as $a => $j) {
                if (!is_null($promtalleralumnoss[$a])) {
                    $resultado['notas'][] = $promtalleralumnoss[$a];
                }

            }
        }


        //Asignamos los Promedios al Resultado
        if ($resultadopromedios) {
            $resultado['promedios'] = $resultadopromedios;
        }


        $this->_helper->json($resultado);


    }


    public
    function getpromedioperiodoAction($opc, $tip, $idal, $idas)
    {
        $this->_helper->viewRenderer->setNeverRender(true);
        $this->_helper->ViewRenderer->setNoRender(true);
        $this->_helper->Layout->disableLayout();

        $idcursos = new Zend_Session_Namespace('id_curso');
        $id = $idcursos->id_curso;
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $modelcurso = new Application_Model_DbTable_Cursos();
        $modelnotas = new Application_Model_DbTable_Notas();
        $modelasignatura = new Application_Model_DbTable_Asignaturascursos();
        $modeloprueba = new Application_Model_DbTable_Pruebas();


        if ($opc) {

            $agregadecimas = false;
            if ($tip == 0) {
                $asignaturas = new Zend_Session_Namespace('idAsignatura');
                $idasignatura = $asignaturas->idAsignatura;
                $listaalumnos = $modelnotas->listaralumnoscurso($id, $idperiodo);

            } elseif ($tip == 2) {
                $idasignatura = $idas;
                $idalumno = $idal;
                $listaalumnos = $modelnotas->listaralumno($idalumno, $id, $idperiodo);
            }

        } else {
            $tipo = $this->_getParam('t', 0);
            $agregadecimas = false;
            if ($tipo == 0 || $tipo == null) {
                $asignaturas = new Zend_Session_Namespace('idAsignatura');
                $idasignatura = $asignaturas->idAsignatura;
                $listaalumnos = $modelnotas->listaralumnoscurso($id, $idperiodo);

            } elseif ($tipo == 2) {
                $idasignatura = $this->_getParam('as', 0);
                $idalumno = $this->_getParam('id', 0);
                $listaalumnos = $modelnotas->listaralumno($idalumno, $id, $idperiodo);
            }
        }


        $datoscurso = $modelcurso->listarcursoid($id, $idperiodo);

        $examenes = new Zend_Session_Namespace('examen');
        $examenes->examen = $datoscurso[0]['examen'];

        //Configuracion de agregar 2 decimas a curso desde 5 a 4to medio cuando el promedio final de asignatura es mayor o igual a 6
        if (($datoscurso[0]['rbd'] == 1863) && (($datoscurso[0]['idCodigo'] == 110 && $datoscurso[0]['idGrado'] > 4) || $datoscurso[0]['idCodigo'] == 310 || $datoscurso[0]['idCodigo'] == 363 || $datoscurso[0]['idCodigo'] == 410 || $datoscurso[0]['idCodigo'] == 510 || $datoscurso[0]['idCodigo'] == 610 || $datoscurso[0]['idCodigo'] == 165)) {
            $agregadecimas = true;
        }


        $listanotas = $modelnotas->listarnotascursoperiodo2($id, $idperiodo, 1);

        $datosasignaturas = $modelasignatura->listarporasignatura($idasignatura, $idperiodo);
        if ($datosasignaturas[0]['tipoAsignatura'] == 4) {
            $agregadecimas = false;

        }

        //Datos Asignatura Combinadas
        $datosasignaturascombinada = $modelasignatura->getcombinada($id, $idperiodo);
        $largoalumnos = count($listaalumnos);
        $largocombinada = count($datosasignaturascombinada);
        $m = 0;


        //Talleres para Alumnos Separados Primer Semestre
        //COnsultar solo los talleres por asignatura ******************

//        $datostallersem = $modelasignatura->gettaller($id, $idperiodo, array(1, 2), 1, array(1));
//        $datostallersemseg = $modelasignatura->gettaller($id, $idperiodo, array(1, 2), 1, array(2));
//        $datostalleranual = $modelasignatura->gettalleranual($id, $idperiodo, array(1), 1);

        $datostallersem = $modelasignatura->gettaller2($idasignatura, $idperiodo, array(1, 2), 1, array(1));
        $datostallersemseg = $modelasignatura->gettaller2($idasignatura, $idperiodo, array(1, 2), 1, array(2));
        $datostalleranual = $modelasignatura->gettalleranual2($idasignatura, $idperiodo, array(1), 1);
        $datostaller = array_merge($datostallersem, $datostallersemseg, $datostalleranual);
        $largotaller = count($datostaller);


        //Configuracion de Taller Buscamos si existen configuraciones por alumnos

        $modelotallerdetalle = new Application_Model_DbTable_Asignaturascursos();
        //Obtiene los Talleres que son Semestrales Tiempo=2.
        $datostalleres = $modelasignatura->gettaller2($idasignatura, $idperiodo, array(2), 2, array(1, 2));


        $promediotalleralumnos = array();
        $promediotalleralumnoss = array();
        if ($datostalleres) {
            for ($j = 0; $j < $largoalumnos; $j++) {
                for ($i = 0; $i < count($datostalleres); $i++) {
                    $detalletaller = $modelotallerdetalle->gettallersegmento($datostalleres[$i]['idConfiguracionTaller'], 1, $listaalumnos[$j]['idAlumnos']);
                    $detalletallers = $modelotallerdetalle->gettallersegmento($datostalleres[$i]['idConfiguracionTaller'], 2, $listaalumnos[$j]['idAlumnos']);


                    //Primer Semestre
                    if ($datostalleres[$i]['tiempoOpcion'] == 1) {
                        if ($detalletaller) {
                            $datosalumnostaller = $modelnotas->listarpromedioalumnotaller($listaalumnos[$j]['idAlumnos'], $idperiodo, 1, $id, $detalletaller[0]['idAsignatura']);

                            if ($datosalumnostaller) {
                                for ($k = 0; $k < count($datosalumnostaller); $k++) {

                                    if ($datostalleres[$i]['idAsignatura'] == $datosalumnostaller[$k]['idAsignatura']) {
                                        if ($datosalumnostaller[$k]['coef'] == 2) {
                                            if ($datosalumnostaller[$k]['nota'] > 0) {
                                                $promediotalleralumnos[$j][$i][] = $datosalumnostaller[$k]['nota'];
                                                $promediotalleralumnos[$j][$i][] = $datosalumnostaller[$k]['nota'];

                                            } else {
                                                $promediotalleralumnos[$j][$i][] = 0;
                                                $promediotalleralumnos[$j][$i][] = 0;
                                            }

                                        } else {
                                            if ($datosalumnostaller[$k]['nota'] > 0) {
                                                $promediotalleralumnos[$j][$i][] = $datosalumnostaller[$k]['nota'];
                                            } else {
                                                $promediotalleralumnos[$j][$i][] = 0;
                                            }

                                        }


                                    }


                                }
                            }
                            if (!empty($promediotalleralumnos[$j][$i])) {
                                $promsetalumnos = array_diff($promediotalleralumnos[$j][$i], array('0'));
                            } else {
                                $promsetalumnos = 0;
                            }

                            if (is_array($promsetalumnos)) {
                                if (count($promsetalumnos) > 0) {

                                    if ($datoscurso[0]['aproxAsignatura'] == 1) {
                                        $promtalleralumnos[$j][$i]['nota'] = round(array_sum($promsetalumnos) / count($promsetalumnos));

                                    } else {
                                        $promtalleralumnos[$j][$i]['nota'] = intval(array_sum($promsetalumnos) / count($promsetalumnos));
                                    }
                                } else {
                                    $promtalleralumnos[$j][$i]['nota'] = 0;
                                }
                            } else {
                                $promtalleralumnos[$j][$i]['nota'] = 0;
                            }


                            $promtalleralumnos[$j][$i]['idAlumnos'] = $listaalumnos[$j]['idAlumnos'];
                            $promtalleralumnos[$j][$i]['idAsignatura'] = $datostalleres[$i]['idAsignaturaTaller'];
                            $promtalleralumnos[$j][$i]['coef'] = 1;
                            $promtalleralumnos[$j][$i]['tiempo'] = 1;
                            $promtalleralumnos[$j][$i]['forma'] = $datostalleres[$i]['forma'];
                            $promtalleralumnos[$j][$i]['porcentaje'] = $datostalleres[$i]['porcentaje'];

                        } else {
                            $promtalleralumnoss[$j][$i]['nota'] = 0;
                            $promtalleralumnos[$j][$i]['idAlumnos'] = $listaalumnos[$j]['idAlumnos'];
                            $promtalleralumnos[$j][$i]['idAsignatura'] = $datostalleres[$i]['idAsignaturaTaller'];
                            $promtalleralumnos[$j][$i]['coef'] = 1;
                            $promtalleralumnos[$j][$i]['tiempo'] = 1;
                            $promtalleralumnos[$j][$i]['forma'] = $datostalleres[$i]['forma'];
                            $promtalleralumnos[$j][$i]['porcentaje'] = $datostalleres[$i]['porcentaje'];
                        }
                    } else {
                        $promtalleralumnos[$j][$i] = array();
                    }

                    //Segundo Semestre
                    if ($datostalleres[$i]['tiempoOpcion'] == 2) {
                        if ($detalletallers) {
                            $datosalumnostallers = $modelnotas->listarpromedioalumnotaller($listaalumnos[$j]['idAlumnos'], $idperiodo, 2, $id, $detalletallers[0]['idAsignatura']);

                            if ($datosalumnostallers) {
                                for ($k = 0; $k < count($datosalumnostallers); $k++) {

                                    if ($datostalleres[$i]['idAsignatura'] == $datosalumnostallers[$k]['idAsignatura']) {

                                        if ($datosalumnostallers[$k]['coef'] == 2) {
                                            if ($datosalumnostallers[$k]['nota'] > 0) {
                                                $promediotalleralumnoss[$j][$i][] = $datosalumnostallers[$k]['nota'];
                                                $promediotalleralumnoss[$j][$i][] = $datosalumnostallers[$k]['nota'];

                                            } else {
                                                $promediotalleralumnoss[$j][$i][] = 0;
                                                $promediotalleralumnoss[$j][$i][] = 0;
                                            }

                                        } else {
                                            if ($datosalumnostallers[$k]['nota'] > 0) {
                                                $promediotalleralumnoss[$j][$i][] = $datosalumnostallers[$k]['nota'];
                                            } else {
                                                $promediotalleralumnoss[$j][$i][] = 0;
                                            }

                                        }


                                    }


                                }
                            }
                            if (!empty($promediotalleralumnoss[$j][$i])) {
                                $promsetalumnoss = array_diff($promediotalleralumnoss[$j][$i], array('0'));
                            } else {
                                $promsetalumnoss = 0;
                            }

                            if (is_array($promsetalumnoss)) {
                                if (count($promsetalumnoss) > 0) {

                                    if ($datoscurso[0]['aproxAsignatura'] == 1) {
                                        $promtalleralumnoss[$j][$i]['nota'] = round(array_sum($promsetalumnoss) / count($promsetalumnoss));
                                        $promtalleralumnoss[$j][$i]['nota'] = intval($promtalleralumnoss[$j][$i]['nota']);

                                    } else {
                                        $promtalleralumnoss[$j][$i]['nota'] = intval(array_sum($promsetalumnoss) / count($promsetalumnoss));
                                    }
                                } else {
                                    $promtalleralumnoss[$j][$i]['nota'] = 0;
                                }
                            } else {
                                $promtalleralumnoss[$j][$i]['nota'] = 0;
                            }


                            $promtalleralumnoss[$j][$i]['idAlumnos'] = $listaalumnos[$j]['idAlumnos'];
                            $promtalleralumnoss[$j][$i]['idAsignatura'] = $datostalleres[$i]['idAsignaturaTaller'];
                            $promtalleralumnoss[$j][$i]['coef'] = 1;
                            $promtalleralumnoss[$j][$i]['tiempo'] = 2;
                            $promtalleralumnoss[$j][$i]['forma'] = $datostalleres[$i]['forma'];
                            $promtalleralumnoss[$j][$i]['porcentaje'] = $datostalleres[$i]['porcentaje'];

                        } else {
                            $promtalleralumnoss[$j][$i]['nota'] = 0;
                            $promtalleralumnoss[$j][$i]['idAlumnos'] = $listaalumnos[$j]['idAlumnos'];
                            $promtalleralumnoss[$j][$i]['idAsignatura'] = $datostalleres[$i]['idAsignaturaTaller'];
                            $promtalleralumnoss[$j][$i]['coef'] = 1;
                            $promtalleralumnoss[$j][$i]['tiempo'] = 2;
                            $promtalleralumnoss[$j][$i]['forma'] = $datostalleres[$i]['forma'];
                            $promtalleralumnoss[$j][$i]['porcentaje'] = $datostalleres[$i]['porcentaje'];
                        }
                    } else {
                        $promtalleralumnoss[$j][$i] = array();
                    }


                }//Fin if talleres

                $largopromedio = count($promtalleralumnos[$j]);
                $largopromedios = count($promtalleralumnoss[$j]);


                for ($l = 0; $l < $largopromedio; $l++) {
                    if (!empty($promtalleralumnos[$j][$l]["idAsignatura"])) {
                        $datosdestino = $modelotallerdetalle->getdestino($promtalleralumnos[$j][$l]["idAsignatura"]);
                        if ($datosdestino[0]['tipoAsignatura'] != 4) {
                            $listapromediostaller[$j][] = $promtalleralumnos[$j][$l];
                        } else {
                            $listapromediostallercom[$j][$promtalleralumnos[$j][$l]['idAsignatura']] = $promtalleralumnos[$j][$l];

                        }
                    }

                }


                for ($l = 0; $l < $largopromedios; $l++) {
                    if (!empty($promtalleralumnoss[$j][$l]["idAsignatura"])) {
                        $datosdestino = $modelotallerdetalle->getdestino($promtalleralumnoss[$j][$l]["idAsignatura"]);
                        if ($datosdestino[0]['tipoAsignatura'] != 4) {
                            $listapromediostallers[$j][] = $promtalleralumnoss[$j][$l];
                        } else {
                            $listapromediostallercoms[$j][$promtalleralumnoss[$j][$l]['idAsignatura']] = $promtalleralumnoss[$j][$l];

                        }
                    }

                }


            }//Fin for Alumnos


        }


        //Fin Talleres

        //Asignaturas Combinadas


        $datosasignaturascombinada = $modelasignatura->getcombinada($id, $idperiodo, $idasignatura);

        $largoalumnos = count($listaalumnos);
        $largotaller = count($datostaller);
        $largocombinada = count($datosasignaturascombinada);
        $m = 0;


        if ($largocombinada > 0) {
            for ($i = 0; $i < $largocombinada; $i++) {
                $listaasignatura[$i] = unserialize($datosasignaturascombinada[$i]['asignaturas']);
                $m = 0;
                for ($h = 0; $h < count($listaasignatura[$i]); $h++) {
                    $datocombinada = $modelasignatura->getnombre($listaasignatura[$i][$h]);

                    $validacom[$i][$m] = $datosasignaturascombinada[$i]['idAsignatura'];
                    $m++;
                    $validacom[$i][$m] = $datosasignaturascombinada[$i]['idAsignatura'];
                    //$m++;
                    for ($j = 0; $j < $largoalumnos; $j++) {
                        $datosalumnoscom = $modelnotas->listarpromedioalumnotaller($listaalumnos[$j]['idAlumnos'], $idperiodo, 1, $id, $datocombinada[0]['idAsignatura']);
                        $datosalumnoscoms = $modelnotas->listarpromedioalumnotaller($listaalumnos[$j]['idAlumnos'], $idperiodo, 2, $id, $datocombinada[0]['idAsignatura']);
                        //Primer Semestre
                        if ($listapromediostallercom) {
                            if ($datocombinada[0]['idAsignatura'] == $listapromediostallercom[$j][$datocombinada[0]['idAsignatura']]['idAsignatura']) {
                                $datosalumnoscom[] = $listapromediostallercom[$j][$datocombinada[0]['idAsignatura']];
                            }

                        }
                        //Segundo Semestre
                        if ($listapromediostallercoms) {
                            if ($datocombinada[0]['idAsignatura'] == $listapromediostallercoms[$j][$datocombinada[0]['idAsignatura']]['idAsignatura']) {
                                $datosalumnoscoms[] = $listapromediostallercoms[$j][$datocombinada[0]['idAsignatura']];
                            }

                        }


                        for ($k = 0; $k < count($datosalumnoscom); $k++) {
                            if ($datocombinada[0]['idAsignatura'] == $datosalumnoscom[$k]['idAsignatura']) {
                                if ($datosalumnoscom[$k]['coef'] == 2) {
                                    if ($datosalumnoscom[$k]['nota'] > 0) {
                                        $promediocom[$j][$m][] = $datosalumnoscom[$k]['nota'];
                                        $promediocom[$j][$m][] = $datosalumnoscom[$k]['nota'];

                                    } else {
                                        $promediocom[$j][$m][] = 0;
                                        $promediocom[$j][$m][] = 0;
                                    }

                                } else {

                                    if ($datosalumnoscom[$k]['nota'] == 0 || empty($datosalumnoscom[$k]['nota']) || $datosalumnoscom[$k]['nota'] == NULL || $datosalumnoscom[$k]['nota'] == '') {
                                        if (!empty($datosalumnoscom[$k]["forma"])) {
                                            if ($datosalumnoscom[$k]['nota'] == 0) {
                                                $promediocom[$j][$m][] = 0;
                                            }

                                        } else {
                                            $promediocom[$j][$m][] = 0;
                                        }


                                    } else {
                                        if (!empty($datosalumnoscom[$k]["forma"]) && $datosalumnoscom[$k]["forma"] == 2) {
                                            $promtalleraux = $datosalumnoscom[$k]['nota'];
                                            $porcentajetaller = $datosalumnoscom[$k]["porcentaje"];


                                        } elseif (!empty($datosalumnoscom[$k]["forma"]) && $datosalumnoscom[$k]["forma"] == 1) {
                                            $promediocom[$j][$m][] = $datosalumnoscom[$k]['nota'];
                                            $promtalleraux = array();
                                            $porcentajetaller = array();
                                        } else {
                                            $promediocom[$j][$m][] = $datosalumnoscom[$k]['nota'];
                                            $promtalleraux = array();
                                            $porcentajetaller = array();
                                        }


                                    }

                                }//fin else


                            }

                        }


                        for ($k = 0; $k < count($datosalumnoscoms); $k++) {
                            if ($datocombinada[0]['idAsignatura'] == $datosalumnoscoms[$k]['idAsignatura']) {
                                if ($datosalumnoscoms[$k]['coef'] == 2) {
                                    if ($datosalumnoscoms[$k]['nota'] > 0) {
                                        $promediocoms[$j][$m][] = $datosalumnoscoms[$k]['nota'];
                                        $promediocoms[$j][$m][] = $datosalumnoscoms[$k]['nota'];

                                    } else {
                                        $promediocoms[$j][$m][] = 0;
                                        $promediocoms[$j][$m][] = 0;
                                    }

                                } else {

                                    if ($datosalumnoscoms[$k]['nota'] == 0 || empty($datosalumnoscoms[$k]['nota']) || $datosalumnoscoms[$k]['nota'] == NULL || $datosalumnoscoms[$k]['nota'] == '') {
                                        if (!empty($datosalumnoscoms[$k]["forma"])) {
                                            if ($datosalumnoscoms[$k]['nota'] == 0) {
                                                $promediocoms[$j][$m][] = 0;
                                            }

                                        } else {
                                            $promediocoms[$j][$m][] = 0;
                                        }


                                    } else {
                                        if (!empty($datosalumnoscoms[$k]["forma"]) && $datosalumnoscoms[$k]["forma"] == 2) {
                                            $promtallerauxs = $datosalumnoscoms[$k]['nota'];
                                            $porcentajetallers = $datosalumnoscoms[$k]["porcentaje"];


                                        } elseif (!empty($datosalumnoscoms[$k]["forma"]) && $datosalumnoscoms[$k]["forma"] == 1) {
                                            $promediocoms[$j][$m][] = $datosalumnoscoms[$k]['nota'];
                                            $promtallerauxs = array();
                                            $porcentajetallers = array();
                                        } else {
                                            $promediocoms[$j][$m][] = $datosalumnoscoms[$k]['nota'];
                                            $promtallerauxs = array();
                                            $porcentajetallers = array();
                                        }


                                    }

                                }//fin else


                            }

                        }
                        //Primer Semestre Promedio
                        if (!empty($promediocom[$j][$m])) {
                            $promset = array_diff($promediocom[$j][$m], array('0'));
                        } else {
                            $promset = 0;
                        }

                        if (is_array($promset)) {
                            if (count($promset) > 0) {
                                if ($datoscurso[0]['aproxAsignatura'] == 1) {
                                    if ($porcentajetaller) {
                                        $totalporcentaje = 100;
                                        $porcentajeasig = $totalporcentaje - $porcentajetaller;
                                        $promedioauxalum = array_sum($promset) / count($promset);
                                        //$promcom[$j][$m]['nota'] = round(array_sum($promset) / count($promset));
                                        $promcom[$j][$m]['nota'] = round(($promedioauxalum * ($porcentajeasig / 100)) + ($promtalleraux * ($porcentajetaller / 100)));

                                    } else {
                                        $promcom[$j][$m]['nota'] = round(array_sum($promset) / count($promset));
                                    }

                                } else {
                                    if ($porcentajetaller) {
                                        $totalporcentaje = 100;
                                        $porcentajeasig = $totalporcentaje - $porcentajetaller;
                                        $promedioauxalum = array_sum($promset) / count($promset);
                                        //$promcom[$j][$m]['nota'] = round(array_sum($promset) / count($promset));
                                        $promcom[$j][$m]['nota'] = round(($promedioauxalum * ($porcentajeasig / 100)) + ($promtalleraux * ($porcentajetaller / 100)));
                                        // $promcom[$j][$m]['nota'] = intval(($promedioauxalum * ($porcentajeasig / 100)) + ($promtalleraux * ($porcentajetaller / 100)));

                                    } else {
                                        $promcom[$j][$m]['nota'] = intval(array_sum($promset) / count($promset));
                                    }
                                }
                            } else {
                                $promcom[$j][$m]['nota'] = 0;
                            }

                        } else {

                            $promcom[$j] = array();
                        }
                        $promcom[$j][$m]['idAsignatura'] = $datosasignaturascombinada[$i]['idAsignatura'];
                        $promcom[$j][$m]['coef'] = 1;
                        $promcom[$j][$m]['tiempo'] = 1;

                        //Segundo Semestre
                        if (!empty($promediocoms[$j][$m])) {
                            $promsets = array_diff($promediocoms[$j][$m], array('0'));
                        } else {
                            $promsets = 0;
                        }


                        if (is_array($promsets)) {
                            if (count($promsets) > 0) {
                                if ($datoscurso[0]['aproxAsignatura'] == 1) {
                                    if ($porcentajetallers) {
                                        $totalporcentajes = 100;
                                        $porcentajeasigs = $totalporcentajes - $porcentajetallers;
                                        $promedioauxalums = array_sum($promsets) / count($promsets);
                                        $promcoms[$j][$m]['nota'] = round(($promedioauxalums * ($porcentajeasigs / 100)) + ($promtallerauxs * ($porcentajetallers / 100)));


                                    } else {
                                        $promcoms[$j][$m]['nota'] = round(array_sum($promsets) / count($promsets));

                                    }

                                } else {
                                    if ($porcentajetallers) {
                                        $totalporcentajes = 100;
                                        $porcentajeasigs = $totalporcentajes - $porcentajetallers;
                                        $promedioauxalums = array_sum($promsets) / count($promsets);
                                        $promcoms[$j][$m]['nota'] = round(($promedioauxalums * ($porcentajeasigs / 100)) + ($promtallerauxs * ($porcentajetallers / 100)));


                                    } else {
                                        $promcoms[$j][$m]['nota'] = intval(array_sum($promsets) / count($promsets));

                                    }
                                }
                            } else {
                                $promcoms[$j][$m]['nota'] = 0;
                            }

                        } else {


                            $promcoms[$j][$m]['nota'] = 0;
                        }
                        $promcoms[$j][$m]['idAsignatura'] = $datosasignaturascombinada[$i]['idAsignatura'];
                        $promcoms[$j][$m]['coef'] = 1;
                        $promcoms[$j][$m]['tiempo'] = 2;


                    }
                    $m++;
                }

            }
        }


        //Taller Por Segmento Curso Completo
        $datostallersem = $modelasignatura->gettaller2($idasignatura, $idperiodo, array(1, 2), 1, array(1));
        $datostallersemseg = $modelasignatura->gettaller2($idasignatura, $idperiodo, array(1, 2), 1, array(2));
        $largotallerprimero = count($datostallersem);
        $largotallersegundo = count($datostallersemseg);


        $promediotaller = array();
        if ($largotallerprimero > 0) {

            for ($i = 0; $i < $largotallerprimero; $i++) {
                if (!empty($datostallersem[$i]['idConfiguracionTaller'])) {
                    $validataller[$i] = $datostallersem[$i]['idAsignaturaTaller'];
                    for ($j = 0; $j < $largoalumnos; $j++) {
                        $promediofinal = 0;
                        $datosalumnostaller = $modelnotas->listarpromedioalumnotaller($listaalumnos[$j]['idAlumnos'], $idperiodo, 1, $id, $datostallersem[$i]['idAsignatura']);
                        $largodatos = count($datosalumnostaller);

                        for ($k = 0; $k < $largodatos; $k++) {
                            if ($datostaller[$i]['idAsignatura'] == $datosalumnostaller[$k]['idAsignatura']) {
                                if ($datosalumnostaller[$k]['coef'] == 2) {
                                    if ($datosalumnostaller[$k]['nota'] > 0) {
                                        $promediotaller[$j][$i][] = $datosalumnostaller[$k]['nota'];
                                        $promediotaller[$j][$i][] = $datosalumnostaller[$k]['nota'];

                                    } else {
                                        $promediotaller[$j][$i][] = 0;
                                        $promediotaller[$j][$i][] = 0;
                                    }

                                } else {
                                    if ($datosalumnostaller[$k]['nota'] > 0) {
                                        $promediotaller[$j][$i][] = $datosalumnostaller[$k]['nota'];
                                    } else {
                                        $promediotaller[$j][$i][] = 0;
                                    }

                                }


                            }

                        }


                        //Primer Semestre
                        if (!empty($promediotaller[$j][$i])) {
                            $promset = array_diff($promediotaller[$j][$i], array('0'));
                        } else {
                            $promset = 0;
                        }

                        if (is_array($promset)) {
                            if (count($promset) > 0) {
                                if ($datoscurso[0]['aproxAsignatura'] == 1) {
                                    $promtaller[$j][$i]['nota'] = round(array_sum($promset) / count($promset));

                                } else {
                                    $promtaller[$j][$i]['nota'] = intval(array_sum($promset) / count($promset));
                                }
                            } else {
                                $promtaller[$j][$i]['nota'] = 0;
                            }

                        } else {
                            $promtaller[$j][$i]['nota'] = 0;
                        }


                        $promtaller[$j][$i]['idAsignatura'] = $datostallersem[$i]['idAsignaturaTaller'];
                        $promtaller[$j][$i]['coef'] = 1;
                        $promtaller[$j][$i]['tiempo'] = 1;
                        $promtaller[$j][$i]['forma'] = $datostallersem[$i]['forma'];
                        $promtaller[$j][$i]['porcentaje'] = $datostallersem[$i]['porcentaje'];


                    }

                }


            }
        }

        //Segundo Semestre
        if ($largotallersegundo > 0) {

            for ($i = 0; $i < $largotallersegundo; $i++) {
                if (!empty($datostallersemseg[$i]['idConfiguracionTaller'])) {
                    $validatallers[$i] = $datostallersemseg[$i]['idAsignaturaTaller'];
                    for ($j = 0; $j < $largoalumnos; $j++) {
                        $promediofinal = 0;
                        $datosalumnostallers = $modelnotas->listarpromedioalumnotaller($listaalumnos[$j]['idAlumnos'], $idperiodo, 2, $id, $datostallersemseg[$i]['idAsignatura']);
                        $largodatoss = count($datosalumnostallers);
                        for ($k = 0; $k < $largodatoss; $k++) {
                            if ($datostallersemseg[$i]['idAsignatura'] == $datosalumnostallers[$k]['idAsignatura']) {
                                if ($datosalumnostallers[$k]['coef'] == 2) {
                                    if ($datosalumnostallers[$k]['nota'] > 0) {
                                        $promediotallers[$j][$i][] = $datosalumnostallers[$k]['nota'];
                                        $promediotallers[$j][$i][] = $datosalumnostallers[$k]['nota'];

                                    } else {
                                        $promediotallers[$j][$i][] = 0;
                                        $promediotallers[$j][$i][] = 0;
                                    }

                                } else {
                                    if ($datosalumnostallers[$k]['nota'] > 0) {
                                        $promediotallers[$j][$i][] = $datosalumnostallers[$k]['nota'];
                                    } else {
                                        $promediotallers[$j][$i][] = 0;
                                    }

                                }


                            }


                        }
                        if (!empty($promediotallers[$j][$i])) {
                            $promsets = array_diff($promediotallers[$j][$i], array('0'));
                        } else {
                            $promsets = 0;
                        }

                        if (is_array($promsets)) {
                            if (count($promsets) > 0) {
                                if ($datoscurso[0]['aproxAsignatura'] == 1) {
                                    $auxtallers = round(array_sum($promsets) / count($promsets));
                                    $promtallers[$j][$i]['nota'] = intval($auxtallers);

                                } else {
                                    $promtallers[$j][$i]['nota'] = intval(array_sum($promsets) / count($promsets));
                                }
                            } else {
                                $promtallers[$j][$i]['nota'] = 0;
                            }

                        } else {
                            $promtallers[$j][$i]['nota'] = 0;
                        }


                        $promtallers[$j][$i]['idAsignatura'] = $datostallersemseg[$i]['idAsignaturaTaller'];
                        $promtallers[$j][$i]['coef'] = 1;
                        $promtallers[$j][$i]['tiempo'] = 2;
                        $promtallers[$j][$i]['forma'] = $datostallersemseg[$i]['forma'];
                        $promtallers[$j][$i]['porcentaje'] = $datostallersemseg[$i]['porcentaje'];


                    }

                }


            }
        }


        if ($largoalumnos == 0 || count($listanotas) == 0) {
            echo "<script type=\"text/javascript\">alert(\"Sin Notas\");</script>";
            echo "<script>parent.$.fancybox.close();</script>";
            exit;

        }

        for ($i = 0; $i < $largoalumnos; $i++) {

            $valores = $modelnotas->listarnotasporalumnoasignatura($listaalumnos[$i]['idAlumnos'], $idperiodo, $id, $idasignatura);
            if ($valores != '' || !empty($valores)) {
                $listaalumnos[$i]['listanotas'] = $valores;
            } else {
                $listaalumnos[$i]['listanotas'] = 0;

            }
            for ($j = 0; $j < count($promtaller[$i]); $j++) {
                $listaalumnos[$i]['listanotas'][] = $promtaller[$i][$j];
            }


            foreach ($promcom[$i] as $a => $j) {
                $listaalumnos[$i]['listanotas'][] = $promcom[$i][$a];
            }
            //Segundo Semestre
            for ($j = 0; $j < count($promtallers[$i]); $j++) {
                $listaalumnos[$i]['listanotas'][] = $promtallers[$i][$j];
            }

            foreach ($promcoms[$i] as $a => $j) {
                $listaalumnos[$i]['listanotas'][] = $promcoms[$i][$a];
            }

            if ($listapromediostaller[$i]) {
                for ($j = 0; $j < count($listapromediostaller[$i]); $j++) {
                    $listaalumnos[$i]['listanotas'][] = $listapromediostaller[$i][$j];
                }
            }

            if ($listapromediostallers[$i]) {
                for ($j = 0; $j < count($listapromediostallers[$i]); $j++) {
                    $listaalumnos[$i]['listanotas'][] = $listapromediostallers[$i][$j];
                }
            }


        }


        //Agregamos cantidad de notas por alumno y la asignamos a cada aisgnatura

        if (!empty($datosasignaturas)) {
            $largoasignatura = count($datosasignaturas);
            for ($i = 0; $i < $largoasignatura; $i++) {

                $datoscuenta[$i] = $modelnotas->listarcantidadnotasanual($id, $idperiodo, $datosasignaturas[$i]['idAsignatura']);
                if (empty($datoscuenta[$i]) && $datosasignaturas[$i]['tipoAsignatura'] == 1) {
                    $datosasig[$i] = 0;
                } else {
                    $largonotas = count($datoscuenta[$i]);
                    $datosasig[$i] = 0;
                    //Primer Semestre
                    for ($j = 0; $j < count($validataller); $j++) {
                        if ($datosasignaturas[$i]['idAsignatura'] == $validataller[$j]) {
                            $datosasig[$i] += 1;
                        }
                    }
                    //Segundo Semestre

                    for ($j = 0; $j < count($validatallers); $j++) {
                        if ($datosasignaturas[$i]['idAsignatura'] == $validatallers[$j]) {
                            $datosasig[$i] += 1;
                        }
                    }
                    for ($j = 0; $j < count($validacom); $j++) {
                        for ($k = 0; $k < count($validacom[$j]); $k++) {
                            if ($datosasignaturas[$i]['idAsignatura'] == $validacom[$j][$k]) {

                                $datosasig[$i] += 1;
                            }
                        }

                    }

                    for ($j = 0; $j < count($datostalleres); $j++) {
                        if ($datosasignaturas[$i]['idAsignatura'] == $datostalleres[$j]['idAsignaturaTaller']) {
                            $datosasig[$i] += 1;
                        }
                    }

                    for ($j = 0; $j < $largonotas; $j++) {

                        if ($datoscuenta[$i][$j]['coef'] == 2) {
                            $datosasig[$i] += 2;
                        } else {
                            $datosasig[$i] += 1;
                        }

                    }


                }


            }
        } else {
            echo "<script type=\"text/javascript\">alert(\"El Curso no Posee Asignaturas en el periodo\");</script>";
            echo "<script>parent.$.fancybox.close();</script>";
            exit;

        }


        if (!empty($listaalumnos)) {
            $r = 0;

            for ($i = 0; $i < $largoalumnos; $i++) {

                $r = 0;

                for ($j = 0; $j < $largoasignatura; $j++) {

                    $promtalleraux = array();
                    $porcentajetaller = array();
                    $sumataller = 0;
                    $promtallerauxs = array();
                    $porcentajetallers = array();
                    $sumatallers = 0;
                    $row = $datosasig[$j];


                    $promedio = 0;
                    $contador = 0;
                    $contadorpromedio = 0;
                    $promedios = 0;
                    $contadorpromedios = 0;
                    if ($row != 0) {
                        $largolistanota = count($listaalumnos[$i]['listanotas']);
                        for ($z = 0; $z < $largolistanota; $z++) {
                            $contadoraux = 0;

                            if ($datosasignaturas[$j]['idAsignatura'] == $listaalumnos[$i]['listanotas'][$z]['idAsignatura']) {


                                //Primer Semesre
                                if ($listaalumnos[$i]['listanotas'][$z]['tiempo'] == 1) {

                                    if ($listaalumnos[$i]['listanotas'][$z]['coef'] == 2) {
                                        $contador += 2;
                                        if ($listaalumnos[$i]['listanotas'][$z]['nota'] == 0 || empty($listaalumnos[$i]['listanotas'][$z]['nota']) || $listaalumnos[$i]['listanotas'][$z]['nota'] == NULL || $listaalumnos[$i]['listanotas'][$z]['nota'] == '') {
                                            $promedio += 0;
                                            $contadorpromedio += 0;


                                        } else {
                                            $promedio += ($listaalumnos[$i]['listanotas'][$z]['nota'] + $listaalumnos[$i]['listanotas'][$z]['nota']);
                                            $contadorpromedio += 2;

                                        }

                                    } else {
                                        $contador += 1;

                                        if ($listaalumnos[$i]['listanotas'][$z]['nota'] == 0 || empty($listaalumnos[$i]['listanotas'][$z]['nota']) || $listaalumnos[$i]['listanotas'][$z]['nota'] == NULL || $listaalumnos[$i]['listanotas'][$z]['nota'] == '') {
                                            if (!empty($listaalumnos[$i]["listanotas"][$z]["forma"])) {
                                                if ($listaalumnos[$i]["listanotas"][$z]["nota"] == 0) {

                                                    $promedio += 0;
                                                    $contadorpromedio += 0;

                                                }

                                            } else {
                                                $promedio += 0;
                                                $contadorpromedio += 0;


                                            }


                                        } else {
                                            if (!empty($listaalumnos[$i]["listanotas"][$z]["forma"]) && $listaalumnos[$i]["listanotas"][$z]["forma"] == 2) {

                                                $contadorauxtaller = true;
                                                $promtalleraux[] = $listaalumnos[$i]['listanotas'][$z]['nota'];
                                                $porcentajetaller[] = $listaalumnos[$i]["listanotas"][$z]["porcentaje"];


                                            } else {

                                                $promedio += $listaalumnos[$i]['listanotas'][$z]['nota'];
                                                $contadorpromedio += 1;


                                            }


                                        }


                                    }
                                }
                                if ($listaalumnos[$i]['listanotas'][$z]['tiempo'] == 2) {


                                    if ($listaalumnos[$i]['listanotas'][$z]['coef'] == 2) {
                                        $contador += 2;
                                        if ($listaalumnos[$i]['listanotas'][$z]['nota'] == 0 || empty($listaalumnos[$i]['listanotas'][$z]['nota']) || $listaalumnos[$i]['listanotas'][$z]['nota'] == NULL || $listaalumnos[$i]['listanotas'][$z]['nota'] == '') {
                                            $promedios += 0;
                                            $contadorpromedios += 0;


                                        } else {
                                            $promedios += ($listaalumnos[$i]['listanotas'][$z]['nota'] + $listaalumnos[$i]['listanotas'][$z]['nota']);
                                            $contadorpromedios += 2;

                                        }

                                    } else {


                                        $contador += 1;

                                        if ($listaalumnos[$i]['listanotas'][$z]['nota'] == 0 || empty($listaalumnos[$i]['listanotas'][$z]['nota']) || $listaalumnos[$i]['listanotas'][$z]['nota'] == NULL || $listaalumnos[$i]['listanotas'][$z]['nota'] == '') {
                                            if (!empty($listaalumnos[$i]["listanotas"][$z]["forma"])) {
                                                if ($listaalumnos[$i]["listanotas"][$z]["nota"] == 0) {

                                                    $promedios += 0;
                                                    $contadorpromedios += 0;

                                                }

                                            } else {
                                                $promedios += 0;
                                                $contadorpromedios += 0;


                                            }


                                        } else {
                                            if (!empty($listaalumnos[$i]["listanotas"][$z]["forma"]) && $listaalumnos[$i]["listanotas"][$z]["forma"] == 2) {

                                                $contadorauxtallers = true;
                                                $promtallerauxs[] = $listaalumnos[$i]['listanotas'][$z]['nota'];
                                                $porcentajetallers[] = $listaalumnos[$i]["listanotas"][$z]["porcentaje"];


                                            } else {

                                                $promedios += $listaalumnos[$i]['listanotas'][$z]['nota'];
                                                $contadorpromedios += 1;


                                            }


                                        }


                                    }
                                }


                                //Promedios por notas

                                if ($contador == $row) {
                                    $promedioaux = 0;
                                    $promedioauxs = 0;

                                    if ($contadorpromedio != 0 && $promedio != 0) {
                                        $promedioaux = 0;
                                        //SI el Taller es por porcentaje
                                        if ($contadorauxtaller) {
                                            $totalporcentaje = 100;
                                            if (count($promtalleraux) > 1) {
                                                for ($t = 0; $t < count($promtalleraux); $t++) {
                                                    $totalporcentaje = $totalporcentaje - $porcentajetaller[$t];
                                                    $sumataller += $promtalleraux[$t] * ($porcentajetaller[$t] / 100);
                                                }

                                            } else {
                                                $totalporcentaje = 100 - $porcentajetaller[0];
                                                $sumataller = $promtalleraux[0] * ($porcentajetaller[0] / 100);

                                            }


                                            if ($datoscurso[0]['aproxAsignatura'] == 1) {//Aproxima
                                                $promedioaux = round($promedio / $contadorpromedio);
                                                $promedioaux = ($promedioaux * ($totalporcentaje / 100)) + $sumataller;
                                                $promedioaux = round($promedioaux);


                                            } else {
                                                $promedioaux = round($promedio / $contadorpromedio);
                                                $promedioaux = ($promedioaux * ($totalporcentaje / 100)) + $sumataller;
                                                $promedioaux = intval($promedioaux);
                                            }

                                        } else {

                                            if ($datoscurso[0]['aproxAsignatura'] == 1) {//Aproxima
                                                $promedioaux = round($promedio / $contadorpromedio);
                                                $promfinal[$i][] = $promedioaux;

                                            } else {
                                                $promedioaux = intval($promedio / $contadorpromedio);
                                                $promfinal[$i][] = $promedioaux;
                                            }

                                        }


                                        $promedioalumnos[$i]['primero'] = $promedioaux;


                                    } else {
                                        $promedioalumnos[$i]['primero'] = 0;
                                        $promfinal[$i][] = 0;


                                    }


                                    //Segundo Semestre
                                    if ($contadorpromedios != 0 && $promedios != 0) {
                                        $promedioauxs = 0;
                                        //SI el Taller es por porcentaje
                                        if ($contadorauxtallers) {
                                            $totalporcentajes = 100;
                                            if (count($promtallerauxs) > 1) {
                                                for ($t = 0; $t < count($promtallerauxs); $t++) {
                                                    $totalporcentajes = $totalporcentajes - $porcentajetallers[$t];
                                                    $sumatallers += $promtallerauxs[$t] * ($porcentajetallers[$t] / 100);
                                                }

                                            } else {
                                                $totalporcentajes = 100 - $porcentajetallers[0];
                                                $sumatallers = $promtallerauxs[0] * ($porcentajetallers[0] / 100);

                                            }


                                            if ($datoscurso[0]['aproxAsignatura'] == 1) {//Aproxima
                                                $promedioauxs = round($promedios / $contadorpromedios);
                                                $promedioauxs = ($promedioauxs * ($totalporcentajes / 100)) + $sumatallers;
                                                $promedioauxs = round($promedioauxs);


                                            } else {
                                                $promedioauxs = round($promedios / $contadorpromedios);
                                                $promedioauxs = ($promedioauxs * ($totalporcentajes / 100)) + $sumatallers;
                                                $promedioauxs = intval($promedioauxs);

                                            }

                                        } else {

                                            if ($datoscurso[0]['aproxAsignatura'] == 1) {//Aproxima
                                                $promedioauxs = round($promedios / $contadorpromedios);
                                                $promfinal[$i][] = $promedioauxs;


                                            } else {
                                                $promedioauxs = intval($promedios / $contadorpromedios);
                                                $promfinal[$i][] = $promedioauxs;

                                            }
                                        }


                                        $promedioalumnos[$i]['segundo'] = $promedioauxs;


                                    } else {
                                        $promedioalumnos[$i]['segundo'] = 0;
                                        $promfinal[$i][] = 0;


                                    }


                                    //Promedio Final De Asignatura
                                    $finalasignatura = 0;

                                    if ($promedioaux != 0 && $promedioauxs != 0) {

                                        if ($datoscurso[0]['aproxAnual'] == 1) {
                                            $finalasignatura = round(($promedioaux + $promedioauxs) / 2);
                                            if ($finalasignatura == 39 && $datoscurso[0]['rbd'] == '1864') {
                                                $finalasignatura = 40;
                                            }
                                        } else {
                                            $finalasignatura = intval(($promedioaux + $promedioauxs) / 2);
                                            if ($finalasignatura == 39 && $datoscurso[0]['rbd'] == '1864') {
                                                $finalasignatura = 40;
                                            }

                                        }
                                        if ($agregadecimas) {
                                            if ($finalasignatura >= 60) {
                                                $finalasignatura += 2;
                                                if ($finalasignatura > 70) {
                                                    $finalasignatura = 70;

                                                }

                                            }
                                        }
                                        $promediototal[$i][] = $finalasignatura;

                                        $notamatriz[$r][] = $finalasignatura;


                                    }

                                    if ($promedioaux == 0 && $promedioauxs == 0) {

                                        $finalasignatura = 0;


                                    }

                                    if ($promedioaux == 0 && $promedioauxs != 0) {


                                        $finalasignatura = $promedioauxs;

                                        if ($agregadecimas) {
                                            if ($finalasignatura >= 60) {
                                                $finalasignatura += 2;
                                                if ($finalasignatura > 70) {
                                                    $finalasignatura = 70;

                                                }

                                            }
                                        }
                                        $promediototal[$i][] = $finalasignatura;
                                        $notamatriz[$r][] = $finalasignatura;


                                    }
                                    if ($promedioaux != 0 && $promedioauxs == 0) {

                                        $finalasignatura = $promedioaux;
                                        if ($agregadecimas) {
                                            if ($finalasignatura >= 60) {
                                                $finalasignatura += 2;
                                                if ($finalasignatura > 70) {
                                                    $finalasignatura = 70;

                                                }

                                            }
                                        }

                                        $promediototal[$i][] = $finalasignatura;
                                        $notamatriz[$r][] = $finalasignatura;


                                    }

                                }// fin promedio por notas

                            }

                        }// fin for


                    }  //fin if row


                } //fin for Asignaturas


                if ($promediototal[$i] != '' || $promediototal[$i] != null) {

                    if ($datoscurso[0]['aproxFinal'] == 1) {//Aproxima
                        $promedioalumnos[$i]['final'] = round(array_sum($promediototal[$i]) / count($promediototal[$i]));


                    } else {
                        $promedioalumnos[$i]['final'] = intval(array_sum($promediototal[$i]) / count($promediototal[$i]));


                    }


                } else {
                    $promedioalumnos[$i]['final'] = 0;
                }


            }// Fin for lista Alumnos


        }


        $resultadoconcepto = $modelasignatura->getasignaturaconcepto($idasignatura, $id, $idperiodo);
        if ($resultadoconcepto) {
            for ($i = 0; $i < count($promedioalumnos); $i++) {

                for ($k = 0; $k < count($resultadoconcepto); $k++) {

                    if ($promedioalumnos[$i]['primero'] == 0) {
                        $promedioalumnos[$i]['primeroconcepto'] = 0;
                    } else {
                        if ($resultadoconcepto[$k]['desde'] <= $promedioalumnos[$i]['primero'] && $resultadoconcepto[$k]['hasta'] >= $promedioalumnos[$i]['primero']) {
                            //if ($resultadoconcepto[$k]['notaconcepto'] == $resultado['notas'][$i]['alumnos'][$j]['nota']) {
                            $promedioalumnos[$i]['primeroconcepto'] = $resultadoconcepto[$k]['concepto'];
                        }
                    }

                    if ($promedioalumnos[$i]['segundo'] == 0) {
                        $promedioalumnos[$i]['segundoconcepto'] = 0;

                    } else {
                        if ($resultadoconcepto[$k]['desde'] <= $promedioalumnos[$i]['segundo'] && $resultadoconcepto[$k]['hasta'] >= $promedioalumnos[$i]['segundo']) {
                            //if ($resultadoconcepto[$k]['notaconcepto'] == $resultado['notas'][$i]['alumnos'][$j]['nota']) {
                            $promedioalumnos[$i]['segundoconcepto'] = $resultadoconcepto[$k]['concepto'];
                        }
                    }

                    if ($promedioalumnos[$i]['final'] == 0) {
                        $promedioalumnos[$i]['finalconcepto'] = 0;

                    } else {
                        if ($resultadoconcepto[$k]['desde'] <= $promedioalumnos[$i]['final'] && $resultadoconcepto[$k]['hasta'] >= $promedioalumnos[$i]['final']) {
                            //if ($resultadoconcepto[$k]['notaconcepto'] == $resultado['notas'][$i]['alumnos'][$j]['nota']) {
                            $promedioalumnos[$i]['finalconcepto'] = $resultadoconcepto[$k]['concepto'];
                        }
                    }


                }


            }
        }


        if ($tipo == 2) {
            if ($datoscurso[0]['examen'] == 1) {
                //Examen a final de asignatura
                $datosexamenes = $modeloprueba->getexamen($id, $idperiodo, $idasignatura, 6);
                if ($datosexamenes[0]) {
                    $datosalumnosexamen = $modelnotas->getnotasexamenalumno($datosexamenes[0]['idEvaluacion'], $id, $idasignatura, $idperiodo, 6, $listaalumnos[0]['idAlumnos']);

                }

                if ($datosalumnosexamen) {


                    if ($promedioalumnos[0]['final'] > 0) {


                        $totalex = 100 - $datosexamenes[0]['porcentajeExamen'];

                        //nota d examen

                        $sumaex = $datosalumnosexamen[0]['nota'] * ($datosexamenes[0]['porcentajeExamen'] / 100);


                        if ($datoscurso[0]['aproxExamen'] == 1) {
                            $promedioalumnos[0]['finalex'] = round(($promedioalumnos[0]['final'] * ($totalex / 100)) + $sumaex);
                        } else {
                            $promedioalumnos[0]['finalex'] = intval(($promedioalumnos[0]['final'] * ($totalex / 100)) + $sumaex);
                        }


                    } else {
                        $promedioalumnos[0]['finalex'] = $promedioalumnos[0]['final'];
                    }


                }


            }
            $this->_helper->json($promedioalumnos);
        } else {
            return $promedioalumnos;
        }


    }

    public function agregarnotasnuevoAction()
    {

        $this->_helper->layout->disableLayout();
        $idasignatura = $this->_getParam('id', 0);

        $idasignatura = $this->_getParam('id', 0);

        if ($idasignatura > 0) {

            $modeloasignatura=new Application_Model_DbTable_Asignaturascursos();
            $dato_asignatura=$modeloasignatura->getasignaturasid($idasignatura)->toArray();


        }

        $monitoreos = new Zend_Session_Namespace('monitoreo');
        $monitoreo = $monitoreos->monitoreo;

        if ($monitoreo == 0 || ($monitoreo == 1 && $dato_asignatura[0]['tipoAsignatura']==5)) {

            $form = new Application_Form_Pruebas();
            $form->idAsignatura->setValue($idasignatura);
            $form->removeElement('publicar');
            $this->view->form = $form;

            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {

                    $modelonotas = new Application_Model_DbTable_Notas();
                    $pruebas = new Application_Model_DbTable_Pruebas();
                    $alumnos = new Application_Model_DbTable_Alumnosactual();

                    $periodo = new Zend_Session_Namespace('periodo');
                    $idperiodo = $periodo->periodo;

                    $idcurso = new Zend_Session_Namespace('id_curso');
                    $idcursos = $idcurso->id_curso;

                    $examenes = new Zend_Session_Namespace('examen');
                    $examen = $examenes->examen;

                    $rutusuario = new Zend_Session_Namespace('id');
                    $usuario = $rutusuario->id;
                    if (empty($usuario)) {
                        echo "<script type=\"text/javascript\">alert(\"Error de Sesión\");</script>";
                        echo "<script>parent.$.fancybox.close();</script>";
                        exit;
                    } else {

                        //ahora los extraemos como se ve abajo
                        $asignatura = $form->getValue('idAsignatura');
                        $contenido = $form->getValue('conte');
                        $tipoev = $form->getValue('tipoEvaluacionPrueba');
                        $fecha = $form->getValue('fecha');
                        $coef = $form->getValue('coef');


                        //Nuevos Campos
                        $tiponota = $form->getValue('tipoNota');
                        $criterio = $form->getValue('criterio');
                        $porcentaje = $form->getValue('porcentajeExamen');

                        $publicar = 0;


                        $listaalumnos = $alumnos->listaralumnoscurso($idcursos, $idperiodo);
                        $fecha = date("Y-m-d", strtotime($fecha));


                        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                        // Iniciamos la transaccion
                        $db->beginTransaction();

                        try {
                            $date = new DateTime;
                            $fechacreacion = $date->format('Y-m-d H:i:s');


                            $valida = $pruebas->validar($contenido, $tipoev, $fecha, $idcursos, $idasignatura, $idperiodo);
                            if ($valida) {
                                if ($tiponota == 2) {
                                    $fecha = date("Y-m-d");
                                    $coef = 1;
                                }
                                if ($examen != 1) {
                                    $tiponota = 1;
                                }


                                $pruebas->agregar($contenido, $fecha, $idcursos, $asignatura, $idperiodo, $usuario, $tipoev, $coef, $tiponota, $criterio, $porcentaje, $publicar, null);
                                $idprueba = $pruebas->getAdapter()->lastInsertId();

                                //recorremos el arreglo con los datos recibidos del formulario
                                for ($i = 0; $i < count($listaalumnos); $i++) {

                                    $modelonotas->agregar($listaalumnos[$i]['idAlumnos'], $asignatura, $idcursos, 0, $usuario, $idprueba, $fechacreacion, $idperiodo);

                                }

                                $db->commit();
                                echo "<script type=\"text/javascript\">window.parent.Nuevo($idprueba);</script>";
                                echo "<script>parent.$.fancybox.close();</script>";
                                exit;
                            } else {
                                echo "<script type=\"text/javascript\">alert(\"Está Intentando agregar una evaluacion existente \");</script>";
                                $form->populate($formData);
                            }


                        } catch (Exception $e) {
                            // Si hubo problemas. Enviamos marcha atras
                            $db->rollBack();
                        }
                    }

                } else {
                    $form->populate($formData);
                }
            } else {

                $idasignatura = $this->_getParam('id', 0);
                if ($idasignatura == 0) {
                    echo "<script type=\"text/javascript\">alert(\"Debe seleccionar una asignatura\");</script>";
                    echo "<script>parent.$.fancybox.close();</script>";
                    exit;


                }
            }

        } else {

            echo "<script type=\"text/javascript\">alert(\"Monitoreo Activo, No Puede Agregar Notas\");</script>";
            echo "<script>parent.$.fancybox.close();</script>";
            exit;
        }


    }

    public function accionesAction()
    {

        $this->_helper->layout->disableLayout();
        $idasignatura = $this->_getParam('id', 0);
        $cargo = new Zend_Session_NameSpace("cargo");
        $rol = $cargo->cargo;

        $form = new Application_Form_Pruebas();
        $form->removeElement('publicar');

        $form->idAsignatura->setValue($idasignatura);
        $form->submit->setLabel('Modificar');
        $form->addElement('submit', 'eliminar');
        $form->eliminar->setLabel('Eliminar');
        $form->eliminar->setAttrib('class', 'red');
        $form->eliminar->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $form->eliminar->setOrder(11);
        if ($rol == 1 || $rol == 3 || $rol == 6) {
            $form->submit->setAttrib("disabled", "disabled");
            $form->eliminar->setAttrib("disabled", "disabled");
        }
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($rol == 2) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {


                    $modelonota = new Application_Model_DbTable_Notas();
                    $modeloprueba = new Application_Model_DbTable_Pruebas();


                    $rutusuario = new Zend_Session_Namespace('id');
                    $usuario = $rutusuario->id;
                    if (empty($usuario)) {
                        echo "<script type=\"text/javascript\">alert(\"Error de Sesión\");</script>";
                        echo "<script>parent.$.fancybox.close();</script>";
                        exit;
                    } else {

                        //ahora los extraemos como se ve abajo
                        $idevaluacion = $form->getValue('idEvaluacion');
                        $contenido = $form->getValue('conte');
                        $tipoev = $form->getValue('tipoEvaluacionPrueba');
                        $fecha = $form->getValue('fecha');
                        $coef = $form->getValue('coef');
                        $modificar = $form->getValue('submit');
                        $eliminar = $form->getValue('eliminar');
                        $fecha = date("Y-m-d", strtotime($fecha));

                        //Nuevos Campos
                        $tiponota = $form->getValue('tipoNota');
                        $criterio = $form->getValue('criterio');
                        $porcentaje = $form->getValue('porcentajeExamen');


                        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                        if ($tiponota == null) {
                            $tiponota = 1;
                        }

                        // Iniciamos la transaccion
                        $db->beginTransaction();

                        try {

                            if ($modificar) {
                                if ($tiponota == 1) {
                                    $modeloprueba->cambiar($idevaluacion, $contenido, $fecha, $tipoev, $coef);

                                } elseif ($tiponota == 2) {

                                    $modeloprueba->cambiarexamen($idevaluacion, $contenido, $tipoev, $criterio, $porcentaje);

                                }


                                $db->commit();
                                echo "<script type=\"text/javascript\">window.parent.Cambiar();</script>";
                                echo "<script>parent.$.fancybox.close();</script>";
                                exit;

                            } elseif ($eliminar) {

                                //Cambiamos de Estado las Evaluacion a estado 9=eliminado Evaluacion
                                $modelonota->actualizarborrar($idevaluacion, 9);
                                $modeloprueba->actualizaestadoeliminar($idevaluacion, 9);
                                $db->commit();
                                echo "<script type=\"text/javascript\">window.parent.Eliminar();</script>";
                                echo "<script>parent.$.fancybox.close();</script>";
                                exit;
                            }


                        } catch (Exception $e) {
                            // Si hubo problemas. Enviamos marcha atras
                            $db->rollBack();
                            echo "<script type=\"text/javascript\">alert(\"Error Intentelo nuevamente\");</script>";
                            echo "<script>parent.$.fancybox.close();</script>";
                            exit;
                        }
                    }

                } else {
                    $form->populate($formData);
                }
            } else {
                echo "<script type=\"text/javascript\">alert(\"No posee los Permisos para ésta acción\");</script>";
                echo "<script>parent.$.fancybox.close();</script>";
                exit;

            }
        } else {

            $idevaluacion = $this->_getParam('id', 0);
            if ($idasignatura > 0) {

                $modeloprueba = new Application_Model_DbTable_Pruebas();

                $datosprueba = $modeloprueba->get($idevaluacion);
                $form->conte->setValue($datosprueba['contenido']);
                $form->tipoEvaluacionPrueba->setValue($datosprueba['tiempo']);
                $fechaa = date("d-m-Y", strtotime($datosprueba['fechaEvaluacion']));
                $form->fecha->setValue($fechaa);

                $form->populate($datosprueba);
                $this->view->fechaset = $fechaa;


            }
        }

    }

    public function getindicadorespreAction()
    {
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcursos = new Zend_Session_Namespace('id_curso');
        $idcurso = $idcursos->id_curso;

        $asignaturamodelo = new Application_Model_DbTable_Asignaturascursos();
        $results = $asignaturamodelo->listarniveltipospre($idcurso, $idperiodo, array(1));
        $this->_helper->json($results);
    }

    public function guardanotasprenuevoAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();

            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            $modelonotas = new Application_Model_DbTable_Notas();
            $pruebas = new Application_Model_DbTable_Pruebas();


            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;

            $idcursos = new Zend_Session_Namespace('id_curso');
            $idcurso = $idcursos->id_curso;

            $rutusuario = new Zend_Session_Namespace('id');
            $usuario = $rutusuario->id;
            if (empty($usuario)) {
                echo Zend_Json::encode(array('response' => 'errorsesion'));
            } else {

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $db->beginTransaction();
                try {

                    $date = new DateTime;
                    $fecha = $date->format('Y-m-d H:i:s');
                    $asignaturamodelo = new Application_Model_DbTable_Asignaturascursos();
                    $resultado = $asignaturamodelo->getconceptosparvularia($idcurso);
                    $largoresultado = count($resultado);
                    $nota = 0;


                    //recorremos el arreglo con los datos recibidos del formulario
                    for ($i = 0; $i < count($data); $i++) {
                        $pruebas->agregar('', '', $idcurso, $data[$i]['asignatura'], $idperiodo, $usuario, $data[$i]['tipoevaluacion'], 1, 1, null, null, 0, null);
                        $idprueba = $pruebas->getAdapter()->lastInsertId();

                        if (!empty($data[$i]['nota'])) {
                            for ($j = 0; $j < $largoresultado; $j++) {
                                if ($resultado[$j]['concepto'] == $data[$i]['nota']) {
                                    $modelonotas->agregar($data[$i]['alumno'], $data[$i]['asignatura'], $idcurso, $resultado[$j]['notaConcepto'], $usuario, $idprueba, $fecha, $idperiodo);

                                }

                            }

                        } else {
                            $db->rollBack();
                            echo Zend_Json::encode(array('response' => 'errorinserta'));
                            exit();
                        }


                    }

                    $db->commit();
                    echo Zend_Json::encode(array('redirect' => '/Libro/notas'));
                } catch (Exception $e) {
                    // Si hubo problemas. Enviamos  marcha atras
                    $db->rollBack();
                    echo Zend_Json::encode(array('response' => 'errorinserta'));
                }
            }
        }
    }

    public function notasnuevopreAction()
    {

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcurso = new Zend_Session_Namespace('id_curso');
        $idcursos = $idcurso->id_curso;

        $nombrecurso = new Zend_Session_Namespace('nombre_curso');
        $nombre_curso = $nombrecurso->nombre_curso;


        $modelnotas = new Application_Model_DbTable_Notas();
        $roles = new Zend_Session_Namespace('cargo');
        $rol = $roles->cargo;

        $modelalumnosactual = new Application_Model_DbTable_Alumnosactual();

        $monitoreos = new Zend_Session_Namespace('monitoreo');
        $monitoreo = $monitoreos->monitoreo;

        if ($rol == '2') {

            if ($monitoreo == 0 ) {

                $activePage = $this->view->navigation()->findOneByLabel('Abierto');
                $activePage->setlabel($nombre_curso);
                $activePage->setparam('id', $idcursos);

                $listanotas = $modelnotas->listaradmin($idcursos, $idperiodo);

                $this->view->datosnotas = $listanotas;
                $this->view->datosalumnos = $modelalumnosactual->listaralumnoscursoactual($idcursos, $idperiodo);
            }else{
                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage("No se Puede Ingresar notas, Monitoreo Activo");
                $this->view->assign('messages', $messages);
            }

        }

        if ($rol == '1' || $rol == '3' || $rol == '6') {

            $activePage = $this->view->navigation()->findOneByLabel('Abierto');
            $activePage->setlabel($nombre_curso);
            $activePage->setparam('id', $idcursos);

            $listanotas = $modelnotas->listaradmin($idcursos, $idperiodo);

            $this->view->datosnotas = $listanotas;
            $this->view->datosalumnos = $modelalumnosactual->listaralumnoscursoactual($idcursos, $idperiodo);

        }
    }

    public function nuevopreAction()
    {

        $monitoreos = new Zend_Session_Namespace('monitoreo');
        $monitoreo = $monitoreos->monitoreo;

        $formnotas = new Application_Form_Notaspre();
        $modelocurso = new Application_Model_DbTable_Cursos();
        $modelonotas = new Application_Model_DbTable_Notas();
        $modeloalumnodetalle = new Application_Model_DbTable_AlumnosDetalle();
        $modeloalumnoactual = new Application_Model_DbTable_Alumnosactual();

        $idcursos = new Zend_Session_Namespace('id_curso');
        $idcurso = $idcursos->id_curso;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $codigos = new Zend_Session_Namespace('codigo');
        $codigo = $codigos->codigo;

        $datoscurso = $modelocurso->get($idcurso);

        if($monitoreo==1){

            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage("No se Puede Ingresar notas, Monitoreo Activo");
            $this->view->assign('messages', $messages);

        }



        $idalumno = $this->_request->getParam('id');
        $segmento = $this->_request->getParam('s');
        if (!empty($idalumno) && !empty($segmento)) {
            $detallealumnoactual = $modeloalumnoactual->getactual($idalumno, $idperiodo);
            $validadetalle = $modeloalumnodetalle->get($detallealumnoactual[0]['idAlumnosActual'], $segmento, $idperiodo);

            $detallealumno = $modelonotas->generanotasalumnobasicapresegmento($idalumno, $idcurso, $idperiodo, $segmento);
            $formnotas->idAlumnos->setValue($idalumno)->setAttrib('disabled', 'disabled');
            $formnotas->tipoEvaluacionPrueba->setValue($segmento)->setAttrib('disabled', 'disabled');
            if ($validadetalle) {
                $formnotas->diasTrabajado->setValue($validadetalle[0]['diasTrabajado']);
                $formnotas->diasInasistencia->setValue($validadetalle[0]['diasInasistencia']);
                $formnotas->observaciones->setValue($validadetalle[0]['observaciones']);

            }


            $this->view->form = $formnotas;
            $this->view->tipo = $datoscurso[0]['idCodigoGrado'];


            $asignaturamodelo = new Application_Model_DbTable_Asignaturascursos();
            $resultado = $asignaturamodelo->getconceptosparvularia($idcurso);
            $largoresultado = count($resultado);
            $largoalumno = count($detallealumno);

            if ($largoalumno > 0) {
                for ($i = 0; $i < $largoalumno; $i++) {
                    for ($j = 0; $j < $largoresultado; $j++) {
                        if ($resultado[$j]['notaConcepto'] == $detallealumno[$i]['nota']) {
                            $detallealumno[$i]['nota'] = $resultado[$j]['concepto'];

                        }
                    }

                }
                $this->view->notas = $detallealumno;
                $this->view->check = 0;
                $this->view->id = $idalumno;
                $this->view->s = $segmento;
            } else {

                $detallealumno = $this->getindicadorespre2Action();
                $this->view->notas = $detallealumno;
                $this->view->check = 1;
                $this->view->id = $idalumno;
                $this->view->s = $segmento;
            }


        }


    }

    public function getindicadorespre2Action()
    {
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcursos = new Zend_Session_Namespace('id_curso');
        $idcurso = $idcursos->id_curso;

        $asignaturamodelo = new Application_Model_DbTable_Asignaturascursos();
        $results = $asignaturamodelo->listarniveltipospre($idcurso, $idperiodo, array(1));
        return $results->toArray();
    }

    public function guardanotasprenuevoeditarAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();

            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            $modelonotas = new Application_Model_DbTable_Notas();
            $pruebas = new Application_Model_DbTable_Pruebas();
            $asignaturamodelo = new Application_Model_DbTable_Asignaturascursos();
            $modeloalumnodetalle = new Application_Model_DbTable_AlumnosDetalle();
            $modeloalumnoactual = new Application_Model_DbTable_Alumnosactual();

            //extreamos el rut del usuario que ingresa
            $rutusuario = new Zend_Session_Namespace('id');
            $usuario = $rutusuario->id;

            $idcursos = new Zend_Session_Namespace('id_curso');
            $idcurso = $idcursos->id_curso;

            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;

            if (empty($usuario)) {
                echo Zend_Json::encode(array('response' => 'errorsesion'));
            } else {

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $db->beginTransaction();
                try {

                    $detallealumnoactual = $modeloalumnoactual->getactual($data[0]['ida'], $idperiodo);

                    $validadetalle = $modeloalumnodetalle->get($detallealumnoactual[0]['idAlumnosActual'], $data[0]['s'], $idperiodo);
                    if ($validadetalle) {
                        $modeloalumnodetalle->cambiar($data[0]['diast'], $data[0]['diasi'], null, 0, $data[0]['obs'], 0, $data[0]['s'], $validadetalle[0]["idDetalle"]);
                    } else {

                        $modeloalumnodetalle->agregar($data[0]['diast'], $data[0]['diasi'], null, 0, $data[0]['obs'], 0, $data[0]['s'], $detallealumnoactual[0]['idAlumnosActual'], $idperiodo);
                    }

                    $resultado = $asignaturamodelo->getconceptosparvularia($idcurso);
                    $largoresultado = count($resultado);

                    $detallealumno = $modelonotas->generanotasalumnobasicapresegmento($data[0]['ida'], $idcurso, $idperiodo, $data[0]['s']);
                    $largoalumnos = count($detallealumno);

                    if ($largoalumnos > 0) {
                        for ($i = 0; $i < count($data); $i++) {

                            $nota = null;
                            for ($j = 0; $j < $largoresultado; $j++) {
                                if ($resultado[$j]['concepto'] == $data[$i]['nota']) {
                                    $nota = $resultado[$j]['notaConcepto'];

                                }

                            }
                            $modelonotas->cambiar($data[$i]['id'], $nota);

                        }
                    } else {

                        $date = new DateTime;
                        $fecha = $date->format('Y-m-d H:i:s');

                        //recorremos el arreglo con los datos recibidos del formulario
                        for ($i = 0; $i < count($data); $i++) {
                            $pruebas->agregar('', '', $idcurso, $data[$i]['asignatura'], $idperiodo, $usuario, $data[$i]['s'], 1, 1, null, null, 0, null);
                            $idprueba = $pruebas->getAdapter()->lastInsertId();

                            //Ingresamos los conceptos vacios
                            if ($data[$i]['nota'] == '' || $data[$i]['nota'] == null || empty($data[$i]['nota'])) {
                                $modelonotas->agregar($data[$i]['alumno'], $data[$i]['asignatura'], $idcurso, null, $usuario, $idprueba, $fecha, $idperiodo);

                            } else {
                                for ($j = 0; $j < $largoresultado; $j++) {
                                    if ($resultado[$j]['concepto'] == $data[$i]['nota']) {
                                        $modelonotas->agregar($data[$i]['alumno'], $data[$i]['asignatura'], $idcurso, $resultado[$j]['notaConcepto'], $usuario, $idprueba, $fecha, $idperiodo);

                                    }


                                }
                            }
                        }

                    }

                    $db->commit();
                    echo Zend_Json::encode(array('redirect' => '/Libro/notasnuevopre'));
                } catch (Exception $e) {
                    // Si hubo problemas. Enviamos marcha atras
                    echo $e;
                    $db->rollBack();
                    echo Zend_Json::encode(array('response' => 'errorinserta'));
                }
            }
        }
    }

    public function getconceptosparvulariaAction()
    {
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcursos = new Zend_Session_Namespace('id_curso');
        $idcurso = $idcursos->id_curso;
        $asignaturamodelo = new Application_Model_DbTable_Asignaturascursos();
        $resultado = $asignaturamodelo->getconceptosparvularia($idcurso);
        $this->_helper->json($resultado);
    }

    public function nuevaobservacionAction()
    {


        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;

        $idcurso = new Zend_Session_Namespace('id_curso');
        $id_curso = $idcurso->id_curso;

        $nombrecurso = new Zend_Session_Namespace('nombre_curso');
        $nombre_curso = $nombrecurso->nombre_curso;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        if ($rol == 2) {

            $activePage = $this->view->navigation()->findOneByLabel('Abierto');
            $activePage->setlabel($nombre_curso);
            $activePage->setparam('id', $id_curso);
        }

        $cryptor = new \Chirp\Cryptor();

        $form = new Application_Form_NuevaObservacion();

        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($form->isValid($formData)) {


                $modelo = new Application_Model_DbTable_Observacion();


                $idalumnos = $form->getValue('idAlumnos');
                $asignatura = $form->getValue('idAsignatura');
                $tipo = $form->getvalue('idTipo');
                $observacion = $form->getValue('observacion');
                $fecha = $form->getValue('fechaObservacion');

                $fecha = date("Y-m-d", strtotime($fecha));

                if ($idalumnos[0] == "allc") {
                    $modeloalumnos = new Application_Model_DbTable_Alumnosactual();
                    $listaalumnos = $modeloalumnos->listaralumnoscursoactual($id_curso, $idperiodo);
                    for ($i = 0; $i < count($listaalumnos); $i++) {
                        $idalumnos[$i] = $listaalumnos[$i]['idAlumnos'];
                    }

                } else {
                    for ($i = 0; $i < count($idalumnos); $i++) {
                        $idalumnos[$i] = $cryptor->decrypt($idalumnos[$i]);
                    }
                }

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $db->beginTransaction();
                try {

                    for ($i = 0; $i < count($idalumnos); $i++) {
                        $modelo->agregar($idalumnos[$i], $fecha, $asignatura, $observacion, $user, $idperiodo, $id_curso, null);
                    }


                    $db->commit();
                    $this->_helper->redirector('observaciones');

                } catch (Exception $e) {
                    // Si hubo problemas. Enviamos todos marcha atras
                    $db->rollBack();
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage(self::MENSAJE);
                    $this->view->assign('messages', $messages);
                }

            } else {

                $form->populate($formData);

            }

        }

    }


    public function editarAction()
    {

        $id_detalle_cursos = new Zend_Session_Namespace('id_detalle_curso');
        $id_detalle_curso = $id_detalle_cursos->id_detalle_curso;

        //recuperamos el nivel del curso que esta en sesion
        $idcursos = new Zend_Session_Namespace('id_curso');
        $idcurso = $idcursos->id_curso;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;

        $cryptor = new \Chirp\Cryptor();

        $form = new Application_Form_NuevaObservacion();
        $form->idAlumnos->setRequired(false);
        $modelo = new Application_Model_DbTable_Observacion();
        $modeloalumnos = new Application_Model_DbTable_Alumnosactual();


        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($form->isValid($formData)) {


                $date = new DateTime;
                $fecha = $date->format('Y-m-d');

                $idobservacion = $cryptor->decrypt($form->getValue('idObservaciones'));
                $observacion = $form->getValue('observacion');
                $fecha = $form->getValue('fechaObservacion');
                $ida = $form->getValue('ida');


                $fecha = date("Y-m-d", strtotime($fecha));

                if (!$idobservacion) {
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Error Id');
                    $this->view->assign('messages', $messages);
                }


                $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                // Iniciamos la transaccion
                $db->beginTransaction();
                try {

                    $modelo->editar($idobservacion, $fecha, $observacion, $user, $idperiodo, $idcurso, 1);
                    $ida = $cryptor->decrypt($ida);
                    if ($ida) {

                        $db->commit();
                        $this->redirect('/Libro/verobservaciones/id/' . $ida);
                    } else {
                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Error ');
                        $this->view->assign('messages', $messages);
                    }


                } catch (Exception $e) {
                    // Si hubo problemas. Enviamos todos marcha atras
                    $db->rollBack();
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage(self::MENSAJE);
                    $this->view->assign('messages', $messages);
                }

            } else {

                $form->populate($formData);

            }

        } else {

            $id = $this->_getParam('id', 0);
            $ids = $cryptor->decrypt($id);

            $ida = $this->_getParam('ida', 0);
            $idas = $cryptor->decrypt($ida);

            if ($ids && $idas) {
                $datosobservacion = $modelo->get($ids, $idperiodo, $idcurso);

                if ($datosobservacion) {

                    $datoalumno = $modeloalumnos->getactual($datosobservacion[0]['idAlumnos'], $idperiodo);
                    $idalumnosencrypt = $cryptor->encrypt($datoalumno[0]['idAlumnos']);
                    $rowset[$idalumnosencrypt] = $datoalumno[0]['nombres'] . ' ' . $datoalumno[0]['apaterno'] . ' ' . $datoalumno[0]['amaterno'];


                    $form->idAlumnos->clearMultiOptions();
                    $form->idAlumnos->addMultiOptions($rowset);
                    $form->populate($datosobservacion[0]);
                    $form->idAlumnos->setValue($idalumnosencrypt);
                    $form->idAlumnos->setAttrib('disabled', 'disabled');
                    $fecha = date("d-m-Y", strtotime($datosobservacion[0]['fechaObservacion']));
                    $form->fechaObservacion->setValue($fecha);

                    $form->idObservaciones->setValue($id);
                    $form->ida->setValue($cryptor->encrypt($datoalumno[0]['idAlumnos']));
                    $this->view->alumno = $idas;


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


    public function eliminarAction()
    {


        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;

        if ($rol == '3' || $rol == '2' || $rol == '1') {

            $cryptor = new \Chirp\Cryptor();
            $modelo = new Application_Model_DbTable_Observacion();
            $id = $this->_getParam('id', 0);
            $ida = $this->_getParam('ida', 0);
            $ids = $cryptor->decrypt($id);
            $idas = $cryptor->decrypt($ida);
            if ($ids && $idas) {

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                // Iniciamos la transaccion
                $db->beginTransaction();
                try {
                    $modelo->eliminar($ids, $user, $idperiodo);
                    if ($idas) {
                        $db->commit();
                        $this->redirect('/Libro/verobservaciones/id/' . $idas);
                    } else {

                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Error1 ');
                        $this->view->assign('messages', $messages);
                    }


                } catch (Exception $e) {
                    // Si hubo problemas. Enviamos todos marcha atras
                    $db->rollBack();
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage(self::MENSAJE);
                    $this->view->assign('messages', $messages);
                }

            } else {
                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Error2');
                $this->view->assign('messages', $messages);
            }

        }


    }

    public function publicarnotasAction()
    {


        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;


        $idcurso = new Zend_Session_Namespace('id_curso');
        $id_curso = $idcurso->id_curso;

        $nombrecurso = new Zend_Session_Namespace('nombre_curso');
        $nombre_curso = $nombrecurso->nombre_curso;

        $roles = new Zend_Session_Namespace('cargo');
        $rol = $roles->cargo;

        $iddetallecurso = new Zend_Session_Namespace('id_detalle_curso');
        $detallecurso = $iddetallecurso->id_detalle_curso;

        $ingreson = new Zend_Session_Namespace('ingresonota');
        $ingresonota = $ingreson->ingresonota;

        $app = new Zend_Session_Namespace('activarapp');
        $activarapp = $app->activarapp;


        $asignaturamodel = new Application_Model_DbTable_Asignaturascursos();
        $modelcurso = new Application_Model_DbTable_Cursos();
        $modelopruebas = new Application_Model_DbTable_Pruebas();

        $detalleest = $modelcurso->listarcursoid($id_curso, $idperiodo);


        //Tipos de asignaturas 1=normal 2=Taller 4=asignatura que compone a otra 5= conceptos
        $tipos = array('1', '3');

        if ($activarapp == 1) {
            //Si el es Docente
            if ($rol == '2') {
                if ($ingresonota == 1) {
                    $activePage = $this->view->navigation()->findOneByLabel('Abierto');
                    $activePage->setlabel($nombre_curso);
                    $activePage->setparam('id', $id_curso);


                    $row = $asignaturamodel->listarniveltipos($id_curso, $idperiodo, $tipos);

                    for ($i = 0; $i < count($row); $i++) {
                        $evaluaciones = $modelopruebas->gettotalpruebascursocuenta($id_curso, $idperiodo, $user, $row[$i]['idAsignatura']);
                        for ($j = 0; $j < count($evaluaciones); $j++) {
                            $evaluaciones[$j]['fechaEvaluacion'] = $this->datetranslate($evaluaciones[$j]['fechaEvaluacion']);

                        }
                        $listaasigaturas[$i] = array('idAsigantura' => $row[$i]['idAsignatura'], 'nombreAsignatura' => $row[$i]['nombreAsignatura'], 'evaluaciones' => $evaluaciones);

                    }

                    $this->view->listaasignatura = $listaasigaturas;
                } else {
                    $activePage = $this->view->navigation()->findOneByLabel('Abierto');
                    $activePage->setlabel($nombre_curso);
                    $activePage->setparam('id', $id_curso . '-' . $detallecurso);


                    //si no lo ingresan por asignaturas.
                    $cursomodel = new Application_Model_DbTable_Detallecursocuenta();
                    $asignaturamodel = new Application_Model_DbTable_Asignaturascursos();

                    $rowcurso = $cursomodel->listarcursoasignatura($detallecurso);
                    $listaasigatura = unserialize($rowcurso[0]['asignaturasLista']);
                    $row = $asignaturamodel->getasignaturastipos($listaasigatura, $tipos);


                    for ($i = 0; $i < count($row); $i++) {
                        $evaluaciones = $modelopruebas->gettotalpruebascursocuenta($id_curso, $idperiodo, $user, $row[$i]['idAsignatura']);
                        for ($j = 0; $j < count($evaluaciones); $j++) {
                            $evaluaciones[$j]['fechaEvaluacion'] = $this->datetranslate($evaluaciones[$j]['fechaEvaluacion']);

                        }
                        $listaasigaturas[$i] = array('idAsigantura' => $row[$i]['idAsignatura'], 'nombreAsignatura' => $row[$i]['nombreAsignatura'], 'evaluaciones' => $evaluaciones);

                    }

                    $this->view->listaasignatura = $listaasigaturas;
                }

            }
        }

    }


    public function guardapublicarAction()
    {

        if ($this->getRequest()->isXmlHttpRequest()) {

            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();

            $app = new Zend_Session_Namespace('activarapp');
            $activarapp = $app->activarapp;

            $cargo = new Zend_Session_Namespace('cargo');
            $rol = $cargo->cargo;

            if ($activarapp == 1) {

                if ($rol == 2 || $rol == 1 || $rol == 3 || $rol == 6) {

                    $json = file_get_contents('php://input');
                    $data = json_decode($json, true);

                    $modelopruebas = new Application_Model_DbTable_Pruebas();

                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                    // Iniciamos la transaccion
                    $db->beginTransaction();
                    $lista = $data[0]['valores'];

                    try {
                        for ($i = 0; $i < count($lista); $i++) {

                            $modelopruebas->actualizarpublicar($lista[$i]['ev'], $lista[$i]['valor']);

                        }
                        $db->commit();
                        die(json_encode(["response" => 1]));


                    } catch (Exception $e) {
                        $db->rollBack();
                        die(json_encode(["response" => 0, "status" => "2"]));

                    }
                }

            }
            //fin si el rol es docente
        }
    }

    function datetranslate($fecha)
    {
        $fecha = substr($fecha, 0, 10);
        $numeroDia = date('d', strtotime($fecha));
        $mes = date('F', strtotime($fecha));
        $meses_ES = array("Ene", "Feb", "Mar", "Abr", "May", "Jun", "Jul", "Ago", "Sep", "Oct", "Nov", "Dic");
        $meses_EN = array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
        $nombreMes = str_replace($meses_EN, $meses_ES, $mes);
        return $numeroDia . " " . $nombreMes;
    }


    //*Requerimiento Nº1 26-08-2020*//


    public function monitoreoAction()
    {

        $monitoreos = new Zend_Session_Namespace('monitoreo');
        $monitoreo = $monitoreos->monitoreo;

        if ($monitoreo == 1) {


            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;


            $idcurso = new Zend_Session_Namespace('id_curso');
            $id_curso = $idcurso->id_curso;

            $nombrecurso = new Zend_Session_Namespace('nombre_curso');
            $nombre_curso = $nombrecurso->nombre_curso;

            $roles = new Zend_Session_Namespace('cargo');
            $rol = $roles->cargo;


            $usuario = new Zend_Session_Namespace('id');
            $user = $usuario->id;


            $modelnotas = new Application_Model_DbTable_Notas();
            $asignaturamodel = new Application_Model_DbTable_Asignaturascursos();
            $modelcurso = new Application_Model_DbTable_Cursos();

            $detalleest = $modelcurso->listarcursoid($id_curso, $idperiodo);


            //Tipos de asignaturas 1=normal 2=Taller 4=asignatura que compone a otra 5= conceptos
            $tipos = array('1', '2', '3', '4', '5');

            //Si el es Docente
            if ($rol == '2') {


                $activePage = $this->view->navigation()->findOneByLabel('Abierto');
                $activePage->setlabel($nombre_curso);
                $activePage->setparam('id', $id_curso);

                if ($detalleest[0]['idCodigo'] == 10) {
                    $rowsetasignatura = $asignaturamodel->getasignaturajson($id_curso, $idperiodo, $tipos);

                    foreach ($rowsetasignatura as $item) {
                        $listaasigatura[] = $item['idAsignatura'];

                    }
                    if ($rowsetasignatura[0]['idNucleo'] > 1) {
                        $rowsetnucleos = $asignaturamodel->getasignaturaspornucleo($listaasigatura);
                        $rowsetambitos = $asignaturamodel->getasignaturasporambito($listaasigatura);

                        $this->view->listaasignatura = $rowsetnucleos;
                        $this->view->listaambitos = $rowsetambitos;
                    }


                } else {
                    $row = $modelcurso->getasignaturashorario($id_curso, $idperiodo, $user);
                    $this->view->listaasignatura = $row;
                }


                //obtenemos la lista de alumnos actual
                $modelalumnos = new Application_Model_DbTable_Alumnosactual();
                $listaalumnos = $modelalumnos->listaralumnoscursoactual($id_curso, $idperiodo);
                $this->view->alumnos = $listaalumnos;
                $this->view->aprox = $detalleest[0]['aproxPeriodo'];

            }

            // Si el Rol es Administrador daem o Director
            if ($rol == '1' || $rol == '3' || $rol == '6') {

                $activePage = $this->view->navigation()->findOneByLabel('Abierto');
                $activePage->setlabel($nombre_curso);
                $activePage->setparam('id', $id_curso);

                $rowsetasignatura = $asignaturamodel->listarniveltipos($id_curso, $idperiodo, $tipos);
                $this->view->listaasignatura = $rowsetasignatura;

                $listanotas = $modelnotas->listaradmin($id_curso, $idperiodo);
                $this->view->datosnotas = $listanotas;

                //obtenemos la lista de alumnos actual
                $modelalumnos = new Application_Model_DbTable_Alumnosactual();
                $listaalumnos = $modelalumnos->listaralumnoscursoactual($id_curso, $idperiodo);
                $this->view->alumnos = $listaalumnos;
                $this->view->aprox = $detalleest[0]['aproxPeriodo'];

            }


        } else {

            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Error');
            $this->view->assign('messages', $messages);
        }


    }


    public function agregarguiaAction()
    {

        $this->_helper->layout->disableLayout();
        $idasignatura = $this->_getParam('id', 0);

        $form = new Application_Form_Pruebas();
        $form->idAsignatura->setValue($idasignatura);
        $form->conte->setLabel("Nombre Guia:");
        $form->fecha->setLabel("Fecha:");
        $form->removeElement('coef');
        $form->removeElement('publicar');

        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                $modelonotas = new Application_Model_DbTable_Notas();
                $pruebas = new Application_Model_DbTable_Pruebas();
                $alumnos = new Application_Model_DbTable_Alumnosactual();

                $periodo = new Zend_Session_Namespace('periodo');
                $idperiodo = $periodo->periodo;

                $idcurso = new Zend_Session_Namespace('id_curso');
                $idcursos = $idcurso->id_curso;

                $examenes = new Zend_Session_Namespace('examen');
                $examen = $examenes->examen;

                $rutusuario = new Zend_Session_Namespace('id');
                $usuario = $rutusuario->id;

                $est = new Zend_Session_Namespace('establecimiento');
                $idestablecimiento = $est->establecimiento;


                if (empty($usuario)) {
                    echo "<script type=\"text/javascript\">alert(\"Error de Sesión\");</script>";
                    echo "<script>parent.$.fancybox.close();</script>";
                    exit;
                } else {

                    $asignatura = $form->getValue('idAsignatura');
                    $contenido = $form->getValue('conte');
                    $tipoev = $form->getValue('tipoEvaluacionPrueba');
                    $fecha = $form->getValue('fecha');
                    $coef = 1;

                    $tiponota = $form->getValue('tipoNota');
                    $criterio = $form->getValue('criterio');
                    $porcentaje = $form->getValue('porcentajeExamen');


                    $listaalumnos = $alumnos->listaralumnoscurso($idcursos, $idperiodo);
                    $fecha = date("Y-m-d", strtotime($fecha));


                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                    // Iniciamos la transaccion
                    $db->beginTransaction();

                    try {
                        $date = new DateTime;
                        $fechacreacion = $date->format('Y-m-d H:i:s');


                        $valida = $pruebas->validar($contenido, $tipoev, $fecha, $idcursos, $idasignatura, $idperiodo);
                        if ($valida) {
                            if ($tiponota == 2) {
                                $fecha = date("Y-m-d");
                                $coef = 1;
                            }
                            if ($examen != 1) {
                                $tiponota = 1;
                            }


                            $pruebas->agregarguias($contenido, $fecha, $idcursos, $asignatura, $idperiodo, $usuario, $tipoev, 0);
                            $idguia = $pruebas->getAdapter()->lastInsertId();


                            $pruebas->agregar($contenido, $fecha, $idcursos, $asignatura, $idperiodo, $usuario, $tipoev, $coef, $tiponota, $criterio, $porcentaje, 0, $idguia);
                            $idprueba = $pruebas->getAdapter()->lastInsertId();

                            if ($idestablecimiento == 11) {

                                //recorremos el arreglo con los datos recibidos del formulario
                                for ($i = 0; $i < count($listaalumnos); $i++) {

                                    $modelonotas->agregarnotaguia($listaalumnos[$i]['idAlumnos'], $asignatura, $idcursos, 0, $usuario, $idprueba, $fechacreacion, $idperiodo, 1, 0);
                                    $idnota = $modelonotas->getAdapter()->lastInsertId();

                                    //Agregamos los Datos de Ponderacion

                                    $modelonotas->agregarponderacion($idnota, 0, 0, 0, 0, 0, 0);


                                }

                            } else {

                                if ($idestablecimiento == 14) {

                                    for ($i = 0; $i < count($listaalumnos); $i++) {

                                        $modelonotas->agregarnotaguia($listaalumnos[$i]['idAlumnos'], $asignatura, $idcursos, 0, $usuario, $idprueba, $fechacreacion, $idperiodo, 0, 0);

                                    }

                                } else {
                                    for ($i = 0; $i < count($listaalumnos); $i++) {

                                        $modelonotas->agregarnotaguia($listaalumnos[$i]['idAlumnos'], $asignatura, $idcursos, 0, $usuario, $idprueba, $fechacreacion, $idperiodo, 1, 0);

                                    }
                                }


                            }


                            $db->commit();
                            echo "<script type=\"text/javascript\">window.parent.Nuevo($idprueba);</script>";
                            echo "<script>parent.$.fancybox.close();</script>";
                            exit;
                        } else {
                            echo "<script type=\"text/javascript\">alert(\"Está Intentando agregar una evaluacion existente \");</script>";
                            $form->populate($formData);
                        }


                    } catch (Exception $e) {
                        $db->rollBack();
                        echo "<script type=\"text/javascript\">alert(\"Error Intentelo nuevamente\" +$e);</script>";
                        echo "<script>parent.$.fancybox.close();</script>";
                        exit;
                    }
                }

            } else {
                $form->populate($formData);
            }
        } else {

            $idasignatura = $this->_getParam('id', 0);
            if ($idasignatura == 0) {
                echo "<script type=\"text/javascript\">alert(\"Debe seleccionar una asignatura\");</script>";
                echo "<script>parent.$.fancybox.close();</script>";
                exit;


            }
        }

    }

    public function getnotasguiaAction()

    {

        $this->_helper->layout->disableLayout();

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcursos = new Zend_Session_Namespace('id_curso');
        $idcurso = $idcursos->id_curso;

        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;

        $est = new Zend_Session_Namespace('establecimiento');
        $idestablecimiento = $est->establecimiento;

        $modelonotas = new Application_Model_DbTable_Notas();
        $modeloprueba = new Application_Model_DbTable_Pruebas();
        $modeloalumnos = new Application_Model_DbTable_Alumnosactual();
        $modeloasignatura = new Application_Model_DbTable_Asignaturascursos();
        $modelcurso = new Application_Model_DbTable_Cursos();
        $idasignatura = $this->_getParam('id');


        // consultar si el curso es de codigo grado 4 o 5 (prebasica)
        //  rqm 42
        $curso = $modelcurso->get($idcurso);

        $asignaturas = new Zend_Session_Namespace('idAsignatura');
        $asignaturas->idAsignatura = $idasignatura;

        if ($curso[0]['idCodigoGrado'] == 4 || $curso[0]['idCodigoGrado'] == 5) { // prebasica

            $resultadoasignatura[0]['tipoAsignatura'] = 6;

        } else {

            $resultadoasignatura = $modeloasignatura->get($idasignatura, $idcurso, $idperiodo);

        }


        $datoscurso = $modelcurso->listarcursoid($idcurso, $idperiodo);

        $examenes = new Zend_Session_Namespace('examen');
        $examenes->examen = $datoscurso[0]['examen'];

        $listadealumnos = $modeloalumnos->listaralumnoscursoactual($idcurso, $idperiodo);
        $largoalumnos = count($listadealumnos);
        $datostaller = array();


        $resultado['notas'] = $modeloprueba->listarguias($idasignatura, $idcurso, $idperiodo);

        $largoresultado = count($resultado['notas']);


        //Si la Asignatura es Taller
        if ($resultadoasignatura[0]['tipoAsignatura'] == 2) {

            $datostalleres = $modeloasignatura->gettallerconfiguracion($resultadoasignatura[0]['idAsignaturaCurso']);
            $modelotallerdetalle = new Application_Model_DbTable_Asignaturascursos();
            if ($datostalleres) {
                for ($j = 0; $j < count($datostalleres); $j++) {
                    if ($datostalleres[$j]['tipoAjuste'] == 1) {
                        //El Taller es para todos los alumnos
                        for ($i = 0; $i < $largoresultado; $i++) {

                            //$resultado['notas'][$i]['fechaEvaluacion'] = $this->datetranslate($resultado['notas'][$i]['fechaEvaluacion']);
                            $resultado['notas'][$i]['alumnos'] = $modelonotas->getnotasasignatura($resultado['notas'][$i]['idEvaluacion'], $idcurso, $idperiodo);
                            //si el resultado de las notas no coinciden con la cantidad de alumnos se realiza una validacion de las notas de esa evaluacion
                            if (count($resultado[$i]['alumnos']) != count($listadealumnos)) {
                                $date = new DateTime;
                                $fechaactual = $date->format('Y-m-d');

                                for ($j = 0; $j < count($listadealumnos); $j++) {
                                    if ($modelonotas->validarnotaalumno($listadealumnos[$j]['idAlumnos'], $resultado['notas'][$i]['idEvaluacion'], $idasignatura, $idcurso, $idperiodo)) {
                                        //Creamos las notas a los alumnos correspondientes
                                        $modelonotas->agregar($listadealumnos[$j]['idAlumnos'], $idasignatura, $idcurso, 0, $resultado['notas'][$i]['idCuenta'], $resultado['notas'][$i]['idEvaluacion'], $fechaactual, $idperiodo);

                                    }


                                }

                                //recargamos las notas
                                $resultado['notas'][$i]['alumnos'] = $modelonotas->getnotasasignatura($resultado['notas'][$i]['idEvaluacion'], $idcurso, $idperiodo);
                            }


                        }
                    } elseif ($datostalleres[$j]['tipoAjuste'] == 2) {


                        for ($l = 0; $l < count($listadealumnos); $l++) {

                            $resultadotaller = $modeloasignatura->gettallerdetalles($datostalleres[$j]['idConfiguracionTaller'], $listadealumnos[$l]['idAlumnos']);

                            if ($resultadotaller) { //Se crean los datos del alumno al que corresponden las notas
                                $listadealumnos[$l]['auth'] = 1;
                                if ($resultadotaller[0]['tiempoOpcion'] == 1) {
                                    $listadealumnos[$l]['tiempop'] = 1;
                                } elseif ($resultadotaller[0]['tiempoOpcion'] == 2) {
                                    $listadealumnos[$l]['tiempos'] = 2;
                                }


                            }
                        }
                        $resultado['notas'][0]['authalumnos'] = true;

                    }

                }

            }


            //Asignamos las notas que corresponden a los alumnos.
            for ($i = 0; $i < count($resultado['notas']); $i++) {
                $resultado['notas'][$i]['fechaEvaluacion'] = $this->datetranslate($resultado['notas'][$i]['fechaEvaluacion']);
                $resultado['notas'][$i]['alumnos'] = $modelonotas->getnotasasignatura($resultado['notas'][$i]['idEvaluacion'], $idcurso, $idperiodo);

                //si el resultado de las notas no coinciden con la cantidad de alumnos se realiza una validacion de las notas de esa evaluacion
                if (count($resultado['notas'][$i]['alumnos']) != count($listadealumnos)) {
                    $date = new DateTime;
                    $fechacreacion = $date->format('Y-m-d H:i:s');

                    for ($j = 0; $j < count($listadealumnos); $j++) {


                        if ($modelonotas->validarnotaalumno($listadealumnos[$j]['idAlumnos'], $resultado['notas'][$i]['idEvaluacion'], $idasignatura, $idcurso, $idperiodo)) {
                            if ($idestablecimiento == 11) {

                                //Creamos las notas a los alumnos correspondientes
                                $modelonotas->agregarnotaguia($listadealumnos[$j]['idAlumnos'], $idasignatura, $idcurso, 0, $resultado['notas'][$i]['idCuenta'], $resultado['notas'][$i]['idEvaluacion'], $fechacreacion, $idperiodo, 1, 0);
                                //$modelonotas->agregar($listadealumnos[$j]['idAlumnos'], $idasignatura, $idcurso, 0, $resultado['notas'][$i]['idCuenta'], $resultado['notas'][$i]['idEvaluacion'], $fechaactual, $idperiodo);
                                $idnota = $modelonotas->getAdapter()->lastInsertId();

                                //Agregamos los Datos de Ponderacion

                                $modelonotas->agregarponderacion($idnota, 0, 0, 0, 0, 0, 0);


                            } else {
                                //Creamos las notas a los alumnos correspondientes
                                $modelonotas->agregar($listadealumnos[$j]['idAlumnos'], $idasignatura, $idcurso, 0, $resultado['notas'][$i]['idCuenta'], $resultado['notas'][$i]['idEvaluacion'], $fechacreacion, $idperiodo);

                            }
                        }


                    }

                    //recargamos las notas
                    $resultado['notas'][$i]['alumnos'] = $modelonotas->getnotasasignatura($resultado['notas'][$i]['idEvaluacion'], $idcurso, $idperiodo);
                }

                //Asignamos la autorizacion a los alumnos correspondientes
                for ($j = 0; $j < count($listadealumnos); $j++) {
                    //si tiene autorizacion para el primer semestre
                    if ($listadealumnos[$j]['auth'] == 1 && $resultado[$i]['tiempo'] == $listadealumnos[$j]['tiempop']) {
                        $resultado['notas'][$i]['alumnos'][$j]['auth'] = true;

                    }

                    //si tiene autorizacion para el Segundo semestre
                    if ($listadealumnos[$j]['auth'] == 1 && $resultado['notas'][$i]['tiempo'] == $listadealumnos[$j]['tiempos']) {
                        $resultado['notas'][$i]['alumnos'][$j]['auth'] = true;

                    }


                }


            }


        } elseif ($resultadoasignatura[0]['tipoAsignatura'] == 6 || $resultadoasignatura[0]['tipoAsignatura'] == 1 || $resultadoasignatura[0]['tipoAsignatura'] == 4 || $resultadoasignatura[0]['tipoAsignatura'] == 5) {

            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
            $db->beginTransaction();
            try {

                for ($i = 0; $i < count($resultado['notas']); $i++) {
                    $resultado['notas'][$i]['fechaEvaluacion'] = $this->datetranslate($resultado['notas'][$i]['fechaEvaluacion']);
                    $resultado['notas'][$i]['alumnos'] = $modelonotas->getnotasasignatura($resultado['notas'][$i]['idEvaluacion'], $idcurso, $idperiodo);
                    //si el resultado de las notas no coinciden con la cantidad de alumnos se realiza una validacion de las notas de esa evaluacion
                    if (count($resultado['notas'][$i]['alumnos']) != count($listadealumnos)) {
                        $date = new DateTime;
                        $fechacreacion = $date->format('Y-m-d H:i:s');


                        for ($j = 0; $j < count($listadealumnos); $j++) {


                            if ($modelonotas->validarnotaalumno($listadealumnos[$j]['idAlumnos'], $resultado['notas'][$i]['idEvaluacion'], $idasignatura, $idcurso, $idperiodo)) {

                                if ($idestablecimiento == 11) {

                                    //Creamos las notas a los alumnos correspondientes
                                    $modelonotas->agregarnotaguia($listadealumnos[$j]['idAlumnos'], $idasignatura, $idcurso, 0, $resultado['notas'][$i]['idCuenta'], $resultado['notas'][$i]['idEvaluacion'], $fechacreacion, $idperiodo, 1, 0);
                                    //$modelonotas->agregar($listadealumnos[$j]['idAlumnos'], $idasignatura, $idcurso, 0, $resultado['notas'][$i]['idCuenta'], $resultado['notas'][$i]['idEvaluacion'], $fechaactual, $idperiodo);
                                    $idnota = $modelonotas->getAdapter()->lastInsertId();

                                    //Agregamos los Datos de Ponderacion

                                    $modelonotas->agregarponderacion($idnota, 0, 0, 0, 0, 0, 0);


                                } else {
                                    //Creamos las notas a los alumnos correspondientes
                                    $modelonotas->agregar($listadealumnos[$j]['idAlumnos'], $idasignatura, $idcurso, 0, $resultado['notas'][$i]['idCuenta'], $resultado['notas'][$i]['idEvaluacion'], $fechacreacion, $idperiodo);

                                }

                            }


                        }

                        //recargamos las notas
                        $resultado['notas'][$i]['alumnos'] = $modelonotas->getnotasasignatura($resultado['notas'][$i]['idEvaluacion'], $idcurso, $idperiodo);
                    }

                    if ($idestablecimiento == 11) {

                        for ($j = 0; $j < count($listadealumnos); $j++) {

                            if ($resultado['notas'][$i]['alumnos'][$j]['idNotas'] != null) {
                                if ($modelonotas->validarnotaalumnoponderacion($resultado['notas'][$i]['alumnos'][$j]['idNotas'])) {
                                    $modelonotas->actualizarnotaponderacion($resultado['notas'][$i]['alumnos'][$j]['idNotas'], 1);
                                    $modelonotas->agregarponderacion($resultado['notas'][$i]['alumnos'][$j]['idNotas'], 0, 0, 0, 0, 0, 0);

                                }
                            }


                        }

                        $resultado['notas'][$i]['alumnos'] = $modelonotas->getnotasasignatura($resultado['notas'][$i]['idEvaluacion'], $idcurso, $idperiodo);


                    }


                }

                $db->commit();
                $this->_helper->json($resultado);

            } catch (Exception $e) {
                // Si hubo problemas. Enviamos marcha atras
                $db->rollBack();
                $this->_helper->json(array());
            }

        }

    }


    public function getnotasguialagAction()

    {

        $this->_helper->layout->disableLayout();

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcursos = new Zend_Session_Namespace('id_curso');
        $idcurso = $idcursos->id_curso;

        $idtipoev = new Zend_Session_Namespace('tipoevaluacion');
        $idtevalucacion = $idtipoev->tipoevaluacion;

        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;

        $est = new Zend_Session_Namespace('establecimiento');
        $idestablecimiento = $est->establecimiento;

        $modelonotas = new Application_Model_DbTable_Notas();
        $modeloprueba = new Application_Model_DbTable_Pruebas();
        $modeloalumnos = new Application_Model_DbTable_Alumnosactual();
        $modeloasignatura = new Application_Model_DbTable_Asignaturascursos();
        $modelcurso = new Application_Model_DbTable_Cursos();
        $modeloequivalencia = new Application_Model_DbTable_Equivalencia();
        $idasignatura = $this->_getParam('id');

        $asignaturas = new Zend_Session_Namespace('idAsignatura');
        $asignaturas->idAsignatura = $idasignatura;

        $resultadoasignatura = $modeloasignatura->get($idasignatura, $idcurso, $idperiodo);

        $datoscurso = $modelcurso->listarcursoid($idcurso, $idperiodo);

        $examenes = new Zend_Session_Namespace('examen');
        $examenes->examen = $datoscurso[0]['examen'];

        $listadealumnos = $modeloalumnos->listaralumnoscursoactual($idcurso, $idperiodo);
        $largoalumnos = count($listadealumnos);
        $datostaller = array();
        $tiempo = 1;

        if ($idtevalucacion == 2) {
            $tiempo = 3;
        }
        $lista_equivalencia = $modeloequivalencia->listarperiodo($idestablecimiento, $idperiodo, $tiempo);



        $resultado['notas'] = $modeloprueba->listarguias($idasignatura, $idcurso, $idperiodo);

        $largoresultado = count($resultado['notas']);


        if ($resultadoasignatura[0]['tipoAsignatura'] == 1 || $resultadoasignatura[0]['tipoAsignatura'] == 4 || $resultadoasignatura[0]['tipoAsignatura'] == 5) {

            $db = Zend_Db_Table_Abstract::getDefaultAdapter();

            // Iniciamos la transaccion
            $db->beginTransaction();
            try {

                for ($i = 0; $i < $largoresultado; $i++) {
                    $resultado['notas'][$i]['fechaEvaluacion'] = $this->datetranslate($resultado['notas'][$i]['fechaEvaluacion']);
                    $resultado['notas'][$i]['alumnos'] = $modelonotas->getnotasasignaturalag($resultado['notas'][$i]['idEvaluacion'], $idcurso, $idperiodo);
                    $date = new DateTime;
                    $fechacreacion = $date->format('Y-m-d H:i:s');


                    for ($j = 0; $j < count($listadealumnos); $j++) {


                        if ($modelonotas->validarnotaalumno($listadealumnos[$j]['idAlumnos'], $resultado['notas'][$i]['idEvaluacion'], $idasignatura, $idcurso, $idperiodo)) {

                            //Agregamos la Nota final
                            $modelonotas->agregarnotaguia($listadealumnos[$j]['idAlumnos'], $idasignatura, $idcurso, 0, $resultado['notas'][$i]['idCuenta'], $resultado['notas'][$i]['idEvaluacion'], $fechacreacion, $idperiodo, 1, 0);
                            $idnota = $modelonotas->getAdapter()->lastInsertId();

                            //Agregamos la notaGuia

                            $modelonotas->agregarnotaguialag($listadealumnos[$j]['idAlumnos'], $idasignatura, $idcurso, $resultado['notas'][$i]['idCuenta'], $fechacreacion, $idperiodo, 0, 0, 0, $resultado['notas'][$i]['idGuia'], $idnota);

                        }


                    }


                    //recargamos las notas
                    $resultado['notas'][$i]['alumnos'] = $modelonotas->getnotasasignaturalag($resultado['notas'][$i]['idEvaluacion'], $idcurso, $idperiodo);

                    for ($j = 0; $j < $largoalumnos; $j++) {


                        //Obtenemos la Equivalencia de Porcentaje
                        $resultado['notas'][$i]['alumnos'][$j]['porcentaje'] = $this->equivalenciaslag($lista_equivalencia, $resultado['notas'][$i]['alumnos'][$j]['nota']);

                    }


                }


                if ($idtevalucacion == 1) {
                    $resultadopromedios = $this->getpromedioperiodoAction(true, 0);
                    $resultadopromedios[0]['t'] = false;
                } elseif ($idtevalucacion == 2) {
                    $resultadopromedios = $this->getpromedioperiodotrimAction(true, 0);
                    $resultadopromedios[0]['t'] = true;
                }

                //Asignamos los Promedios al Resultado
                if ($resultadopromedios) {
                    $resultado['promedios'] = $resultadopromedios;
                }

                $db->commit();
                $this->_helper->json($resultado);

            } catch (Exception $e) {
                // Si hubo problemas. Enviamos  marcha atras
                $db->rollBack();
                $this->_helper->json(array());
            }


        }


    }

    public function guardaestadoguiaAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {

            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            $modelonotas = new Application_Model_DbTable_Notas();
            $rutusuario = new Zend_Session_Namespace('id');
            $usuario = $rutusuario->id;

            if (empty($usuario)) {
                echo Zend_Json::encode(array('response' => 'errorsesion1'));
            } else {

                if ($data['0']['id'] == 0 || empty($data['0']['id'])) {
                    echo Zend_Json::encode(array('response' => 'errorinserta2'));
                    die();
                }

                if (is_numeric($data['0']['valor'])) {
                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                    // Iniciamos la transaccion
                    $db->beginTransaction();
                    try {
                        $modelonotas->cambiarguia($data['0']['id'], $data['0']['valor']);
                        $db->commit();
                        echo Zend_Json::encode(array('response' => 'exito'));

                    } catch (Exception $e) {
                        // Si hubo problemas. Enviamos marcha atras
                        $db->rollBack();
                        echo Zend_Json::encode(array('response' => 'errorinserta3'));
                    }
                } else {

                    echo Zend_Json::encode(array('response' => 'errorinserta4'));
                }


            }
        }
    }

    public function accionesguiaAction()
    {

        $this->_helper->layout->disableLayout();
        $idasignatura = $this->_getParam('id', 0);
        $cargo = new Zend_Session_NameSpace("cargo");
        $rol = $cargo->cargo;

        $idcursos = new Zend_Session_Namespace('id_curso');
        $idcurso = $idcursos->id_curso;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;


        $est = new Zend_Session_Namespace('establecimiento');
        $idestablecimiento = $est->establecimiento;

        $form = new Application_Form_Pruebas();

        if ($idestablecimiento == 12) {
            $form->addElement('hidden', 'idGuia');

        }

        $form->conte->setLabel("Nombre Guia:");
        $form->fecha->setLabel("Fecha:");
        $form->removeElement('coef');
        $form->removeElement('publicar');

        $form->idAsignatura->setValue($idasignatura);
        $form->submit->setLabel('Modificar');
        $form->addElement('submit', 'eliminar');
        $form->eliminar->setLabel('Eliminar');
        $form->eliminar->setAttrib('class', 'red');
        $form->eliminar->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $form->eliminar->setOrder(11);
        if ($rol == 1 || $rol == 3 || $rol == 6) {
            $form->submit->setAttrib("disabled", "disabled");
            $form->eliminar->setAttrib("disabled", "disabled");
        }
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($rol == 2) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {


                    $modelonota = new Application_Model_DbTable_Notas();
                    $modeloprueba = new Application_Model_DbTable_Pruebas();


                    $rutusuario = new Zend_Session_Namespace('id');
                    $usuario = $rutusuario->id;
                    if (empty($usuario)) {
                        echo "<script type=\"text/javascript\">alert(\"Error de Sesión\");</script>";
                        echo "<script>parent.$.fancybox.close();</script>";
                        exit;
                    } else {

                        //ahora los extraemos como se ve abajo
                        $idevaluacion = $form->getValue('idEvaluacion');
                        $idguia = $form->getValue('idGuia');
                        $contenido = $form->getValue('conte');
                        $tipoev = $form->getValue('tipoEvaluacionPrueba');
                        $fecha = $form->getValue('fecha');
                        $modificar = $form->getValue('submit');
                        $eliminar = $form->getValue('eliminar');
                        $fecha = date("Y-m-d", strtotime($fecha));

                        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                        // Iniciamos la transaccion
                        $db->beginTransaction();

                        try {

                            if ($idestablecimiento != 12) {
                                $datosguia = $modeloprueba->getguia($idevaluacion);
                                if (count($datosguia) > 0) {
                                    $idguia = $datosguia[0]['idGuia'];
                                }
                            }


                            if ($modificar) {

                                if ($idestablecimiento == 12) {//Si es CESA

                                    $modeloprueba->cambiarguia($idguia, $contenido);
                                } else {
                                    $modeloprueba->cambiar($idevaluacion, $contenido, $fecha, $tipoev, 1);
                                    $modeloprueba->cambiarguia($idguia, $contenido);
                                }

                                $db->commit();
                                echo "<script type=\"text/javascript\">window.parent.Cambiar();</script>";
                                echo "<script>parent.$.fancybox.close();</script>";
                                exit;

                            } elseif ($eliminar) {

                                if ($idestablecimiento == 12) {//Si es CESA

                                    $resuldadoguia = $modeloprueba->getguiaid($idguia);
                                    $modeloequivalencia = new Application_Model_DbTable_Equivalencia();
                                    $alumnos = new Application_Model_DbTable_Alumnosactual();
                                    $listaalumnos = $alumnos->listaralumnoscurso($idcurso, $idperiodo);

                                    if ($resuldadoguia[0]['tiempoGuia'] == 1) {

                                        $modeloprueba->borrarguia($idguia);
                                        $modelonota->borrarvaloresguia($idguia);

                                        //Actualizamos las notas de los alumnos


                                        $lista_equivalencia = $modeloequivalencia->listarperiodo($idestablecimiento, $idperiodo, 1);
                                        //recorremos el arreglo con los datos recibidos del formulario
                                        for ($i = 0; $i < count($listaalumnos); $i++) {


                                            //Actualizamos el nuevo valor de la nota

                                            $lista_guias = array();
                                            //Obtenemos todas las Guias del Alumnos correspondientes a la evaluacion
                                            $lista_guias = $modeloprueba->getguiaalumno($idevaluacion, $listaalumnos[$i]['idAlumnos'], $idperiodo);
                                            $total = count($lista_guias);
                                            $valores = 0;
                                            $valores_d = 0;
                                            $porcentaje = 0;
                                            foreach ($lista_guias as $k => $valor) {

                                                if ($valor['valorGuias'] == 1) {
                                                    $valores += 1;
                                                }
                                                if ($valor['valorGuias'] == 2) {
                                                    $valores_d += 1;
                                                }


                                            }

                                            if ($valores > 0) {

                                                $porcentaje = round(($valores * 100) / $total);


                                            }

                                            //Obtenemos la lista de Equivalencia de Porcentajes a Nota

                                            $valor_nota = 0;
                                            if (count($lista_equivalencia) > 0) {

                                                foreach ($lista_equivalencia as $h => $valor) {
                                                    if (($porcentaje >= $valor['porcentaje_inicio']) && ($porcentaje <= $valor['porcentaje_final'])) {
                                                        $valor_nota = $valor['equivalencia_nota'];
                                                        break;

                                                    }
                                                }

                                            }


                                            //Actualizamos la Nota del Alumnos de Acuerdo a la Equivalencia del Pocentaje a  Nota

                                            if ($porcentaje != 0) {
                                                $modelonota->cambiarnotaalumno($idevaluacion, $valor_nota, $porcentaje, $listaalumnos[$i]['idAlumnos']);

                                            } else if ($porcentaje == 0 && $valores_d > 0) {

                                                $modelonota->cambiarnotaalumno($idevaluacion, $valor_nota, $porcentaje, $listaalumnos[$i]['idAlumnos']);
                                            } else if ($porcentaje == 0 && $valores_d == 0) {
                                                $valor_nota = null;
                                                $porcentaje = null;
                                                $modelonota->cambiarnotaalumno($idevaluacion, $valor_nota, $porcentaje, $listaalumnos[$i]['idAlumnos']);


                                            }

                                        }

                                    } elseif ($resuldadoguia[0]['tiempoGuia'] == 2) {

                                        $modeloprueba->borrarguia($idguia);
                                        $modelonota->borrarvaloresguia($idguia);


                                        $lista_equivalencia = $modeloequivalencia->listarperiodo($idestablecimiento, $idperiodo, 2);

                                        for ($i = 0; $i < count($listaalumnos); $i++) {

                                            $lista_guias = array();
                                            //Obtenemos todas las Guias del Alumnos correspondientes al semestre
                                            $lista_guias = $modeloprueba->getguiaalumnosegsem($resuldadoguia[0]['idAsignatura'], $listaalumnos[$i]['idAlumnos'], $idperiodo);
                                            $porcentaje = 0;
                                            $promedioNota = 0;
                                            $total = 0;

                                            foreach ($lista_guias as $h => $valor) {

                                                if ($valor['valorFormativa'] != null) {
                                                    if ($valor['porcentajeGuiaFinal'] != null) {
                                                        $porcentaje += $valor['porcentajeGuiaFinal'];

                                                    } else {
                                                        $porcentaje += 0;
                                                    }
                                                    $total++;
                                                }

                                            }


                                            if ($total > 0) {
                                                $promedioPorcentaje = round($porcentaje / $total);
                                            } else {
                                                $promedioPorcentaje = 0;
                                            }


                                            if (count($lista_equivalencia) > 0 && $porcentaje != null) {
                                                foreach ($lista_equivalencia as $j => $valor) {
                                                    if (($promedioPorcentaje >= $valor['porcentaje_inicio']) && ($promedioPorcentaje <= $valor['porcentaje_final'])) {
                                                        $promedioNota = $valor['equivalencia_nota'];
                                                        break;
                                                    }
                                                }
                                            }

                                            $modelonota->cambiarnotaalumno($resuldadoguia[0]['idEvaluacionGuia'], $promedioNota, $promedioPorcentaje, $listaalumnos[$i]['idAlumnos']);

                                        }


                                    }


                                } else {
                                    //eliminamos las notas y luego la evaluacion
                                    $modelonota->borrar($idevaluacion);
                                    $modeloprueba->borrar($idevaluacion);
                                    $modeloprueba->borrarguia($idguia);
                                }


                                $db->commit();
                                echo "<script type=\"text/javascript\">window.parent.Eliminar();</script>";
                                echo "<script>parent.$.fancybox.close();</script>";
                                exit;
                            }


                        } catch (Exception $e) {
                            // Si hubo problemas. Enviamos todo marcha atras
                            $db->rollBack();
                            echo "<script type=\"text/javascript\">alert(\"Error Intentelo nuevamente\");</script>";
                            echo "<script>parent.$.fancybox.close();</script>";
                            exit;
                        }
                    }

                } else {
                    $form->populate($formData);
                }
            } else {
                echo "<script type=\"text/javascript\">alert(\"No posee los Permisos para ésta acción\");</script>";
                echo "<script>parent.$.fancybox.close();</script>";
                exit;

            }
        } else {

            $idevaluacion = $this->_getParam('id', 0);
            if ($idasignatura > 0) {
                $modeloprueba = new Application_Model_DbTable_Pruebas();

                if ($idestablecimiento == 12) {

                    $datosprueba = $modeloprueba->getguiaid($idevaluacion);
                    $form->idEvaluacion->setValue($datosprueba[0]['idEvaluacionGuia']);
                    $form->idGuia->setValue($datosprueba[0]['idGuia']);
                    $form->conte->setValue($datosprueba[0]['nombreguia']);
                    $form->tipoEvaluacionPrueba->setValue($datosprueba[0]['tiempoGuia']);
                    $fechaa = date("d-m-Y", strtotime($datosprueba[0]['fechaGuia']));
                    $form->fecha->setValue($fechaa);
                    $form->populate($datosprueba);
                    $this->view->fechaset = $fechaa;

                } else {
                    $datosprueba = $modeloprueba->get($idevaluacion);
                    $form->conte->setValue($datosprueba['contenido']);
                    $form->tipoEvaluacionPrueba->setValue($datosprueba['tiempo']);
                    $fechaa = date("d-m-Y", strtotime($datosprueba['fechaEvaluacion']));
                    $form->fecha->setValue($fechaa);
                    $form->populate($datosprueba);
                    $this->view->fechaset = $fechaa;
                }


            }
        }

    }

    public function getlogrosAction()
    {

        $modelo = new Application_Model_DbTable_Logros();
        $resultado = $modelo->listar();
        $this->_helper->json($resultado);
    }


    public function getporcentajesAction()
    {

        $id_cursos = new Zend_Session_Namespace('id_curso');
        $id_curso = $id_cursos->id_curso;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        //Obtenemos los datos del Establecimiento
        $modelocurso = new Application_Model_DbTable_Cursos();
        $datos = $modelocurso->get($id_curso);

        $modelo = new Application_Model_DbTable_Equivalencia();
        $resultado = $modelo->listar($datos[0]['idEstablecimiento'], $idperiodo);

        $this->_helper->json($resultado);
    }


    public function guardaporcentajenotaAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            //Detectamos si es una llamada AJAX

            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();

            //guardamos los datos en $json recibidos de la funcion ajax
            $json = file_get_contents('php://input');
            //decodificamos los datos en un array($data) php

            $data = json_decode($json, true);

            $modelonotas = new Application_Model_DbTable_Notas();
            $asignaturamodelo = new Application_Model_DbTable_Asignaturascursos();

            //extreamos el rut del usuario que ingresa
            $rutusuario = new Zend_Session_Namespace('id');
            $usuario = $rutusuario->id;

            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;

            $idcurso = new Zend_Session_Namespace('id_curso');
            $idcursos = $idcurso->id_curso;

            if (empty($usuario)) {
                echo Zend_Json::encode(array('response' => 'errorsesion'));
            } else {

                if ($data['id'] == 0 || empty($data['id'])) {
                    echo Zend_Json::encode(array('response' => 'errorinserta1'));
                    die();
                }
                $resultadonota = $modelonotas->get($data['id']);
                $datosasignatura = $asignaturamodelo->get($resultadonota['idAsignatura'], $idcursos, $idperiodo);

                //Validamos que la nota sea un integer
                if (intval($data['valor']) || $data['valor'] == 0) {

                    //Validamoe el rango del porcentaje

                    if ($data['valor'] >= 0 && $data['valor'] <= 100) {
                        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                        // Iniciamos la transaccion
                        $db->beginTransaction();
                        try {
                            $modeloequivalencia = new Application_Model_DbTable_Equivalencia();
                            $datosnotas = $modeloequivalencia->get($data['ideq']);

                            if ($datosnotas[0] == 0) {

                                echo Zend_Json::encode(array('response' => 'errorinserta2'));
                                die();

                            } else {

                                $modelonotas->cambiarporcentaje($data['id'], $datosnotas[0]['equivalencia_nota'], $data['valor']);
                                $db->commit();
                                echo Zend_Json::encode(array('response' => 'exito'));

                            }

                        } catch (Exception $e) {
                            // Si hubo problemas. Enviamosmarcha atras
                            $db->rollBack();
                            echo Zend_Json::encode(array('response' => 'errorinserta3'));
                        }
                    } else {
                        echo Zend_Json::encode(array('response' => 'errorinserta4'));
                    }

                } else {

                    echo Zend_Json::encode(array('response' => 'errorinserta5'));
                }


            }
        }
    }


    public function guardaestadoguiaponderacionAction()
    {

        $monitoreos = new Zend_Session_Namespace('monitoreo');
        $monitoreo = $monitoreos->monitoreo;

        $est = new Zend_Session_Namespace('establecimiento');
        $idestablecimiento = $est->establecimiento;

        if ($monitoreo == 1) {
            if ($idestablecimiento == 11) {

                if ($this->getRequest()->isXmlHttpRequest()) {
                    $this->_helper->viewRenderer->setNoRender();
                    $this->_helper->layout->disableLayout();

                    $json = file_get_contents('php://input');
                    $data = json_decode($json, true);

                    $modelonotas = new Application_Model_DbTable_Notas();

                    //extreamos el rut del usuario que ingresa
                    $rutusuario = new Zend_Session_Namespace('id');
                    $usuario = $rutusuario->id;

                    if (empty($usuario)) {
                        echo Zend_Json::encode(array('response' => 'errorsesion'));
                    } else {

                        if ($data['id'] == 0 || empty($data['id'])) {
                            echo Zend_Json::encode(array('response' => 'errorinserta1'));
                            die();
                        }
                        //Validamos que el valor sea un integer y mayor a 0
                        if ($data['valor_u_n'] >= 0 && $data['valor_d_n'] >= 0 && $data['valor_t_n'] >= 0) {
                            $total_nota = 0;

                            if ($data['valor_u_n'] > 0 && $data['valor_d_n'] > 0 && $data['valor_t_n'] > 0) {

                                //Calculo de la nota

                                if ($data['valor_u'] == 3) {

                                    $total_u = round(30 * 0.1);
                                    $total_d = round(30 * 0.3);
                                    $total_t = round(30 * 0.6);

                                } else {
                                    $total_u = round((int)$data['valor_u_n'] * 0.1);
                                    $total_d = round((int)$data['valor_d_n'] * 0.3);
                                    $total_t = round((int)$data['valor_t_n'] * 0.6);
                                }


                                $total_nota = $total_u + $total_d + $total_t;

                            }


                            $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                            // Iniciamos la transaccion
                            $db->beginTransaction();


                            try {
                                //Actualizamos la nota
                                $modelonotas->cambiar($data['id'], $total_nota);
                                //Actualizamos los datos en ponderacion
                                $modelonotas->cambiarponderacion($data['valor_u'], $data['valor_u_n'], $data['valor_d'], $data['valor_d_n'], $data['valor_t'], $data['valor_t_n'], $data['id']);

                                $db->commit();
                                echo Zend_Json::encode(array('response' => 'exito'));

                            } catch (Exception $e) {
                                // Si hubo problemas. Enviamos marcha atras
                                $db->rollBack();
                                echo Zend_Json::encode(array('response' => 'errorinserta2' . $e));
                            }
                        } else {

                            echo Zend_Json::encode(array('response' => 'errorinserta22'));
                        }


                    }
                }
            }
        }
    }

    function equivalencias($lista_equivalencia, $nota)
    {


        $valor_nota = 0;
        $numeros = array();
        $escala = array();
        $largo = count($lista_equivalencia);
        if ($largo > 0) {
            for ($i = 0; $i < $largo; $i++) {
                $numeros[$i] = range($lista_equivalencia[$i]['porcentaje_final'], $lista_equivalencia[$i]['porcentaje_inicio']);

                if (($i + 1) == $largo) {
                    $step = (count(range($lista_equivalencia[$i]['equivalencia_nota'], $lista_equivalencia[$i]['equivalencia_nota'] + 1)) / count($numeros[$i]));
                } else {
                    $step = (count(range($lista_equivalencia[$i]['equivalencia_nota'], $lista_equivalencia[$i + 1]['equivalencia_nota'] + 1)) / count($numeros[$i]));
                }
                for ($j = 0; $j < count($numeros[$i]); $j++) {
                    if ($j == 0) {
                        if ($lista_equivalencia[$i]['equivalencia_nota'] == $nota) {
                            $valor_nota = array($numeros[$i][$j], $lista_equivalencia[$i]['nivelLogro']);
                            break;
                        }
                        $escala[$i][$j] = $lista_equivalencia[$i]['equivalencia_nota'];
                    } else {
                        $aux_nota = $escala[$i][$j - 1];

                        if (($aux_nota - $step) == $nota) {
                            $valor_nota = array($numeros[$i][$j], $lista_equivalencia[$i]['nivelLogro']);
                            break;
                        } elseif (round($aux_nota - $step) == $nota) {
                            $valor_nota = array($numeros[$i][$j], $lista_equivalencia[$i]['nivelLogro']);
                            break;
                        }
                        $escala[$i][$j] = $aux_nota - $step;
                    }

                }

            }

        }

        return $valor_nota;


    }

    function equivalenciaslag($lista_equivalencia, $nota)
    {


        $valor_nota = array();
        $numeros = array();
        $escala = array();
        $largo = count($lista_equivalencia);
        if ($largo > 0) {
            for ($i = 0; $i < $largo; $i++) {
                $numeros[$i] = range($lista_equivalencia[$i]['porcentaje_final'], $lista_equivalencia[$i]['porcentaje_inicio']);

                if (($i + 1) == $largo) {
                    $step = (count(range($lista_equivalencia[$i]['equivalencia_nota'], $lista_equivalencia[$i]['equivalencia_nota'] + 1)) / count($numeros[$i]));
                } else {
                    $step = (count(range($lista_equivalencia[$i]['equivalencia_nota'], $lista_equivalencia[$i + 1]['equivalencia_nota'] + 1)) / count($numeros[$i]));
                }


                for ($j = 0; $j < count($numeros[$i]); $j++) {
                    if ($j == 0) {
                        if ($lista_equivalencia[$i]['equivalencia_nota'] == $nota) {
                            $valor_nota = array($numeros[$i][$j], $lista_equivalencia[$i]['nivelLogro']);
                            break;
                        }
                        $escala[$i][$j] = $lista_equivalencia[$i]['equivalencia_nota'];
                    } else {
                        $aux_nota = $escala[$i][$j - 1];
                        $escala[$i][$j] = $aux_nota - $step;
                        if ($escala[$i][$j] == $nota) {
                            $valor_nota = array($numeros[$i][$j], $lista_equivalencia[$i]['nivelLogro']);
                            break;
                        } elseif (round($aux_nota - $step) == $nota) {
                            $valor_nota = array($numeros[$i][$j], $lista_equivalencia[$i]['nivelLogro']);
                            break;
                        }
                    }

                }

            }

        }

        return $valor_nota;


    }

    public function guardaguianotasegsemAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();

            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $modelonotas = new Application_Model_DbTable_Notas();
            $modeloprueba = new Application_Model_DbTable_Pruebas();

            $modeloequivalencia = new Application_Model_DbTable_Equivalencia();

            $rutusuario = new Zend_Session_Namespace('id');
            $usuario = $rutusuario->id;

            $est = new Zend_Session_Namespace('establecimiento');
            $idestablecimiento = $est->establecimiento;

            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;

            if (empty($usuario)) {
                echo Zend_Json::encode(array('response' => 'errorsesion1'));
            } else {

                if ($data['id'] == 0 || empty($data['id'])) {
                    echo Zend_Json::encode(array('response' => 'errorinserta2'));
                    die();
                }

                if (is_numeric($data['valor_f'])) {
                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                    // Iniciamos la transaccion
                    $db->beginTransaction();
                    try {

                        $lista_equivalencia = $modeloequivalencia->listarperiodo($idestablecimiento, $idperiodo, 2);
                        $porcentajeInput = $data['porcentaje'];
                        $valor_nota = 0;
                        $valor_notafinal = 0;
                        $porcentaje_final = 0;

                        if (count($lista_equivalencia) > 0 && $porcentajeInput != null) {
                            foreach ($lista_equivalencia as $i => $valor) {
                                if (($porcentajeInput >= $valor['porcentaje_inicio']) && ($porcentajeInput <= $valor['porcentaje_final'])) {
                                    $valor_nota = $valor['equivalencia_nota'];
                                    break;
                                }
                            }
                        }

                        if ($data['valor_f'] == 1) {

                            if ($valor_nota >= 60) {
                                $valor_nota = 70;
                            } else if ($valor_nota < 60) {
                                $valor_nota += 10;
                            }
                            $resultado = $this->equivalencias($lista_equivalencia, $valor_nota);
                            $porcentaje_final = $resultado[0];

                        } elseif ($data['valor_f'] == 2) {
                            $porcentaje_final = $data['porcentaje'];
                        }

                        if (count($lista_equivalencia) > 0 && $porcentaje_final != null) {
                            foreach ($lista_equivalencia as $i => $valor) {
                                if (($porcentaje_final >= $valor['porcentaje_inicio']) && ($porcentaje_final <= $valor['porcentaje_final'])) {
                                    $valor_notafinal = $valor['equivalencia_nota'];
                                    break;
                                }
                            }
                        }

                        $modelonotas->cambiarguianota($data['id'], 0, $data['valor_f'], $data['porcentaje'], $porcentaje_final);

                        // Consulta los valores de la guia
                        $datosvalor = $modelonotas->getvaloresguias($data['id']);

                        $id_evaluacion = $datosvalor[0]['idEvaluacionGuia'];
                        $id_alumno = $datosvalor[0]['idAlumnos'];


                        if ($porcentajeInput == '') {
                            $porcentajeInput = 0;
                        }

                        //Obtenemos todas las Guias del Alumnos correspondientes al semestre
                        $lista_guias = $modeloprueba->getguiaalumnosegsem($datosvalor[0]['idAsignatura'], $id_alumno, $idperiodo);
                        $porcentaje = 0;
                        $promedioNota = 0;
                        $total = 0;

                        foreach ($lista_guias as $i => $valor) {

                            if ($valor['valorFormativa'] != null) {
                                if ($valor['porcentajeGuiaFinal'] != null) {
                                    $porcentaje += $valor['porcentajeGuiaFinal'];
                                    $total++;

                                } else {
                                    $porcentaje += 0;
                                }
                            }

                        }


                        if ($total > 0) {
                            $promedioPorcentaje = round($porcentaje / $total);
                        } else {
                            $promedioPorcentaje = 0;
                        }


                        if (count($lista_equivalencia) > 0 && $porcentaje != null) {
                            foreach ($lista_equivalencia as $i => $valor) {
                                if (($promedioPorcentaje >= $valor['porcentaje_inicio']) && ($promedioPorcentaje <= $valor['porcentaje_final'])) {
                                    $promedioNota = $valor['equivalencia_nota'];
                                    break;
                                }
                            }
                        }

                        $modelonotas->cambiarnotaalumno($id_evaluacion, $promedioNota, $porcentajeInput, $id_alumno);
                        $db->commit();
                        echo Zend_Json::encode(array('response' => 'exito', 'p' => $promedioPorcentaje, 'n' => $promedioNota, 'pf' => $porcentaje_final, 'nf' => $valor_notafinal));


                    } catch (Exception $e) {
                        //Si hubo problemas. Enviamos marcha atras
                        $db->rollBack();
                        echo Zend_Json::encode(array('response' => 'errorinserta3' . $e));
                    }
                } else {

                    echo Zend_Json::encode(array('response' => 'errorinserta4'));
                }


            }
        }
    }

    public function guardaguianotaAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {

            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();

            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $modelonotas = new Application_Model_DbTable_Notas();
            $modeloprueba = new Application_Model_DbTable_Pruebas();
            $modeloequivalencia = new Application_Model_DbTable_Equivalencia();

            $rutusuario = new Zend_Session_Namespace('id');
            $usuario = $rutusuario->id;

            $est = new Zend_Session_Namespace('establecimiento');
            $idestablecimiento = $est->establecimiento;

            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;

            if (empty($usuario)) {
                echo Zend_Json::encode(array('response' => 'errorsesion1'));
            } else {

                if ($data['id'] == 0 || empty($data['id'])) {
                    echo Zend_Json::encode(array('response' => 'errorinserta2'));
                    die();
                }

                if (is_numeric($data['valor']) && is_numeric($data['valor_f'])) {
                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                    // Iniciamos la transaccion
                    $db->beginTransaction();
                    try {

                        $modelonotas->cambiarguianota($data['id'], $data['valor'], $data['valor_f']);
                        $datosvalor = $modelonotas->getvaloresguias($data['id']);
                        $id_evaluacion = $datosvalor[0]['idEvaluacionGuia'];
                        $id_alumno = $datosvalor[0]['idAlumnos'];

                        //Obtenemos todas las Guias del Alumnos correspondientes a la evaluacion
                        $lista_guias = $modeloprueba->getguiaalumno($id_evaluacion, $id_alumno, $idperiodo);
                        $total = count($lista_guias);
                        $valores = 0;
                        $valores_d = 0;
                        $porcentaje = 0;
                        foreach ($lista_guias as $i => $valor) {

                            if ($valor['valorGuias'] == 1) {
                                $valores += 1;
                            }
                            if ($valor['valorGuias'] == 2) {
                                $valores_d += 1;
                            }


                        }


                        if ($valores > 0) {

                            $porcentaje = round(($valores * 100) / $total);


                        }

                        //Obtenemos la lista de Equivalencia de Porcentajes a Nota

                        $valor_nota = 0;
                        $lista_equivalencia = $modeloequivalencia->listarperiodo($idestablecimiento, $idperiodo, 1);

                        if (count($lista_equivalencia) > 0) {

                            foreach ($lista_equivalencia as $i => $valor) {
                                if (($porcentaje >= $valor['porcentaje_inicio']) && ($porcentaje <= $valor['porcentaje_final'])) {
                                    $valor_nota = $valor['equivalencia_nota'];
                                    break;

                                }
                            }

                        }


                        //Actualizamos la Nota del Alumnos de Acuerdo a la Equivalencia del Pocentaje a  Nota
                        if ($porcentaje != 0) {

                            $modelonotas->cambiarnotaalumno($id_evaluacion, $valor_nota, $porcentaje, $id_alumno);

                        } else if ($porcentaje == 0 && $valores_d > 0) {
                            $modelonotas->cambiarnotaalumno($id_evaluacion, $valor_nota, $porcentaje, $id_alumno);
                        } else if ($porcentaje == 0 && $valores_d == 0) {
                            $valor_nota = null;
                            $porcentaje = null;
                            $modelonotas->cambiarnotaalumno($id_evaluacion, $valor_nota, $porcentaje, $id_alumno);


                        }


                        $db->commit();
                        echo Zend_Json::encode(array('response' => 'exito', 'p' => $porcentaje, 'n' => $valor_nota));

                    } catch (Exception $e) {
                        // Si hubo problemas. Enviamos marcha atras
                        $db->rollBack();
                        echo Zend_Json::encode(array('response' => 'errorinserta3' . $e));
                    }
                } else {

                    echo Zend_Json::encode(array('response' => 'errorinserta4'));
                }


            }
        }
    }

    public function agregarguianotasAction()
    {

        $this->_helper->layout->disableLayout();
        $idasignatura = $this->_getParam('id', 0);

        $est = new Zend_Session_Namespace('establecimiento');
        $idestablecimiento = $est->establecimiento;

        if ($idestablecimiento == 12) {

            $form = new Application_Form_Pruebas();
            $form->idAsignatura->setValue($idasignatura);
            $form->conte->setLabel("Nombre Guia:");
            $form->fecha->setLabel("Fecha:");
            $form->removeElement('coef');
            $form->removeElement('publicar');
            // RQM 41
            // $form->tipoEvaluacionPrueba->removeMultiOption('2');
            // F RQM 41
            $this->view->form = $form;

            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {

                    $modelonotas = new Application_Model_DbTable_Notas();
                    $pruebas = new Application_Model_DbTable_Pruebas();
                    $alumnos = new Application_Model_DbTable_Alumnosactual();

                    $periodo = new Zend_Session_Namespace('periodo');
                    $idperiodo = $periodo->periodo;

                    $idcurso = new Zend_Session_Namespace('id_curso');
                    $idcursos = $idcurso->id_curso;

                    $examenes = new Zend_Session_Namespace('examen');
                    $examen = $examenes->examen;

                    $rutusuario = new Zend_Session_Namespace('id');
                    $usuario = $rutusuario->id;

                    if (empty($usuario)) {
                        echo "<script type=\"text/javascript\">alert(\"Error de Sesión\");</script>";
                        echo "<script>parent.$.fancybox.close();</script>";
                        exit;
                    } else {

                        //ahora los extraemos como se ve abajo
                        $asignatura = $form->getValue('idAsignatura');
                        $contenido = $form->getValue('conte');
                        $tipoev = $form->getValue('tipoEvaluacionPrueba');
                        $fecha = $form->getValue('fecha');
                        $coef = 1;

                        //Nuevos Campos
                        $tiponota = $form->getValue('tipoNota');
                        $criterio = $form->getValue('criterio');
                        $porcentaje = $form->getValue('porcentajeExamen');


                        $listaalumnos = $alumnos->listaralumnoscurso($idcursos, $idperiodo);
                        $fecha = date("Y-m-d", strtotime($fecha));

                        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                        // Iniciamos la transaccion
                        $db->beginTransaction();

                        try {
                            $date = new DateTime;
                            $fechacreacion = $date->format('Y-m-d H:i:s');

                            // RQM 41
                            if ($tipoev == 1) {
                                // F RQM 41
                                //Validamos que Exista una evaluacion Creada para el Primer Semestre
                                $valida = $pruebas->validarpruebaguia("Nota Total", 1, $idcursos, $idasignatura, $idperiodo);

                                if (!$valida) {
                                    if ($tiponota == 2) {
                                        $fecha = date("Y-m-d");
                                        $coef = 1;
                                    }
                                    if ($examen != 1) {
                                        $tiponota = 1;
                                    }


                                    $pruebas->agregar("Nota Total", $fecha, $idcursos, $asignatura, $idperiodo, $usuario, $tipoev, $coef, $tiponota, $criterio, $porcentaje, 0, 0);
                                    $idprueba = $pruebas->getAdapter()->lastInsertId();

                                    //Agregamos la Guia

                                    $pruebas->agregarguias($contenido, $fecha, $idcursos, $asignatura, $idperiodo, $usuario, $tipoev, $idprueba);
                                    $idguia = $pruebas->getAdapter()->lastInsertId();


                                    //recorremos el arreglo con los datos recibidos del formulario
                                    for ($i = 0; $i < count($listaalumnos); $i++) {

                                        $modelonotas->agregar($listaalumnos[$i]['idAlumnos'], $asignatura, $idcursos, 0, $usuario, $idprueba, $fechacreacion, $idperiodo);

                                        //Agregamos los Datos de la Guia
                                        $modelonotas->agregarvaloresguia(0, 0, $idguia, $listaalumnos[$i]['idAlumnos']);
                                    }

                                } else {

                                    //Agregamos la Guia

                                    $pruebas->agregarguias($contenido, $fecha, $idcursos, $asignatura, $idperiodo, $usuario, $tipoev, $valida[0]['idEvaluacion']);
                                    $idguia = $pruebas->getAdapter()->lastInsertId();

                                    $modeloequivalencia = new Application_Model_DbTable_Equivalencia();
                                    $lista_equivalencia = $modeloequivalencia->listarperiodo($idestablecimiento, $idperiodo, 1);
                                    //recorremos el arreglo con los datos recibidos del formulario
                                    for ($i = 0; $i < count($listaalumnos); $i++) {

                                        //Agregamos los Datos de la Guia
                                        $modelonotas->agregarvaloresguia(0, 0, $idguia, $listaalumnos[$i]['idAlumnos']);

                                        //Actualizamos el nuevo valor de la nota
                                        //Obtenemos todas las Guias del Alumnos correspondientes a la evaluacion
                                        $listas_guias = array();
                                        $lista_guias = $pruebas->getguiaalumno($valida[0]['idEvaluacion'], $listaalumnos[$i]['idAlumnos'], $idperiodo);
                                        $total = count($lista_guias);
                                        $valores = 0;
                                        $valores_d = 0;
                                        $porcentaje = 0;
                                        foreach ($lista_guias as $k => $valor) {

                                            if ($valor['valorGuias'] == 1) {
                                                $valores += 1;
                                            }
                                            if ($valor['valorGuias'] == 2) {
                                                $valores_d += 1;
                                            }


                                        }

                                        if ($valores > 0) {

                                            $porcentaje = round(($valores * 100) / $total);


                                        }

                                        //Obtenemos la lista de Equivalencia de Porcentajes a Nota
                                        $valor_nota = 0;
                                        if (count($lista_equivalencia) > 0) {

                                            foreach ($lista_equivalencia as $h => $valor) {
                                                if (($porcentaje >= $valor['porcentaje_inicio']) && ($porcentaje <= $valor['porcentaje_final'])) {
                                                    $valor_nota = $valor['equivalencia_nota'];
                                                    break;

                                                }
                                            }

                                        }


                                        //Actualizamos la Nota del Alumnos de Acuerdo a la Equivalencia del Pocentaje a  Nota
                                        if ($porcentaje != 0) {
                                            $modelonotas->cambiarnotaalumno($valida[0]['idEvaluacion'], $valor_nota, $porcentaje, $listaalumnos[$i]['idAlumnos']);

                                        } else if ($porcentaje == 0 && $valores_d > 0) {

                                            $modelonotas->cambiarnotaalumno($valida[0]['idEvaluacion'], $valor_nota, $porcentaje, $listaalumnos[$i]['idAlumnos']);
                                        } else if ($porcentaje == 0 && $valores_d == 0) {
                                            $valor_nota = null;
                                            $porcentaje = null;
                                            $modelonotas->cambiarnotaalumno($valida[0]['idEvaluacion'], $valor_nota, $porcentaje, $listaalumnos[$i]['idAlumnos']);


                                        }

                                    }


                                }
                                // RQM 41
                            } else if ($tipoev == 2) {


                                $valida = $pruebas->validarpruebaguia("Nota Total", 2, $idcursos, $idasignatura, $idperiodo);

                                if (!$valida) {
                                    if ($tiponota == 2) {
                                        $fecha = date("Y-m-d");
                                        $coef = 1;
                                    }
                                    if ($examen != 1) {
                                        $tiponota = 1;
                                    }


                                    $pruebas->agregar("Nota Total", $fecha, $idcursos, $asignatura, $idperiodo, $usuario, 2, $coef, $tiponota, $criterio, $porcentaje, 0, 0);
                                    $idprueba = $pruebas->getAdapter()->lastInsertId();

                                    //Agregamos la Guia

                                    $pruebas->agregarguias($contenido, $fecha, $idcursos, $asignatura, $idperiodo, $usuario, $tipoev, $idprueba);
                                    $idguia = $pruebas->getAdapter()->lastInsertId();


                                    //recorremos el arreglo con los datos recibidos del formulario
                                    for ($i = 0; $i < count($listaalumnos); $i++) {

                                        $modelonotas->agregar($listaalumnos[$i]['idAlumnos'], $asignatura, $idcursos, 0, $usuario, $idprueba, $fechacreacion, $idperiodo);

                                        //Agregamos los Datos de la Guia
                                        $modelonotas->agregarvaloresguia(0, 0, $idguia, $listaalumnos[$i]['idAlumnos']);
                                    }

                                } else {

                                    // Agregamos la Guia
                                    $pruebas->agregarguias($contenido, $fecha, $idcursos, $asignatura, $idperiodo, $usuario, $tipoev, $valida[0]['idEvaluacion']);
                                    $idguia = $pruebas->getAdapter()->lastInsertId();

                                    // Recorremos el arreglo con los datos recibidos del formulario
                                    for ($i = 0; $i < count($listaalumnos); $i++) {

                                        // Agregamos las notas de cada alumno seteados en 0
                                        $modelonotas->agregar($listaalumnos[$i]['idAlumnos'], $asignatura, $idcursos, 0, $usuario, $valida[0]['idEvaluacion'], $fechacreacion, $idperiodo);

                                        // Agregamos los Datos de la Guia seteados en 0
                                        $modelonotas->agregarvaloresguia(0, 0, $idguia, $listaalumnos[$i]['idAlumnos']);
                                    }

                                }
//                                // Creamos una evaluación y una guía con relación de uno a uno
//                                // Agregamos la Evaluación (prueba)
//                                $pruebas->agregar("Nota Total", $fecha, $idcursos, $asignatura, $idperiodo, $usuario, $tipoev, $coef, $tiponota, $criterio, $porcentaje, 0, 0);
//                                $idprueba = $pruebas->getAdapter()->lastInsertId();


                            }
                            // F RQM 41


                            $db->commit();
                            echo "<script type=\"text/javascript\">window.parent.Nuevo($idguia);</script>";
                            echo "<script>parent.$.fancybox.close();</script>";
                            exit;


                        } catch (Exception $e) {
                            // Si hubo problemas. Enviamos marcha atras
                            $db->rollBack();
                            echo "<script type=\"text/javascript\">alert(\"Error Intentelo nuevamente\" +$e);</script>";
                            echo "<script>parent.$.fancybox.close();</script>";
                            exit;
                        }
                    }

                } else {
                    $form->populate($formData);
                }
            } else {

                $idasignatura = $this->_getParam('id', 0);
                if ($idasignatura == 0) {
                    echo "<script type=\"text/javascript\">alert(\"Debe seleccionar una asignatura\");</script>";
                    echo "<script>parent.$.fancybox.close();</script>";
                    exit;


                }
            }

        } else {

            echo "<script type=\"text/javascript\">alert(\"Sin Acceso\");</script>";
            echo "<script>parent.$.fancybox.close();</script>";
            exit;
        }


    }

    function quitar_tildes($cadena)
    {
        $no_permitidas = array("á", "é", "í", "ó", "ú", "Á", "É", "Í", "Ó", "Ú", "ñ", "À", "Ã", "Ì", "Ò", "Ù", "Ã™", "Ã ", "Ã¨", "Ã¬", "Ã²", "Ã¹", "ç", "Ç", "Ã¢", "ê", "Ã®", "Ã´", "Ã»", "Ã‚", "ÃŠ", "ÃŽ", "Ã”", "Ã›", "ü", "Ã¶", "Ã–", "Ã¯", "Ã¤", "«", "Ò", "Ã", "Ã„", "Ã‹");
        $permitidas = array("a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "n", "N", "A", "E", "I", "O", "U", "a", "e", "i", "o", "u", "c", "C", "a", "e", "i", "o", "u", "A", "E", "I", "O", "U", "u", "o", "O", "i", "a", "e", "U", "I", "A", "E");
        $texto = str_replace($no_permitidas, $permitidas, $cadena);
        return $texto;
    }

    //RQM 41
    public function getvaloresguiaAction()

    {

        $this->_helper->layout->disableLayout();

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcursos = new Zend_Session_Namespace('id_curso');
        $idcurso = $idcursos->id_curso;

        $modelonotas = new Application_Model_DbTable_Notas();
        $modeloprueba = new Application_Model_DbTable_Pruebas();
        $modeloalumnos = new Application_Model_DbTable_Alumnosactual();
        $modeloasignatura = new Application_Model_DbTable_Asignaturascursos();
        $modelcurso = new Application_Model_DbTable_Cursos();
        $modeloequivalencia = new Application_Model_DbTable_Equivalencia();
        $idasignatura = $this->_getParam('id');

        $asignaturas = new Zend_Session_Namespace('idAsignatura');
        $asignaturas->idAsignatura = $idasignatura;

        $est = new Zend_Session_Namespace('establecimiento');
        $idestablecimiento = $est->establecimiento;

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        // Iniciamos la transaccion
        $db->beginTransaction();

        try {
            $listadealumnos = $modeloalumnos->listaralumnoscursoactual($idcurso, $idperiodo);
            $largoalumnos = count($listadealumnos);

            $resultado['notas'] = $modeloprueba->listarguia($idasignatura, $idcurso, $idperiodo, 1);
            $largoresultado = count($resultado['notas']);


            for ($i = 0; $i < $largoresultado; $i++) {

                $date = new DateTime;
                $fechacreacion = $date->format('Y-m-d H:i:s');

                //Seteamos el nombre de la guia a 15 caracteres
                if (strlen($resultado['notas'][$i]['nombreguia']) > 25) {
                    $aux_texto = $this->quitar_tildes($resultado['notas'][$i]['nombreguia']);
                    $resultado['notas'][$i]['nombreguia'] = substr($aux_texto, 0, 24) . "...";

                }


                for ($j = 0; $j < $largoalumnos; $j++) {
                    if ($modelonotas->validarnotaalumno($listadealumnos[$j]['idAlumnos'], $resultado['notas'][$i]['idEvaluacionGuia'], $idasignatura, $idcurso, $idperiodo)) {

                        //Creamos las notas a los alumnos correspondientes
                        $modelonotas->agregar($listadealumnos[$j]['idAlumnos'], $idasignatura, $idcurso, 0, $resultado['notas'][$i]['idCuenta'], $resultado['notas'][$i]['idEvaluacionGuia'], $fechacreacion, $idperiodo);

                    }

                    if ($modelonotas->validarnotaalumnoguia($listadealumnos[$j]['idAlumnos'], $resultado['notas'][$i]['idGuia'])) {

                        //Agregamos los Datos de la Guia
                        $modelonotas->agregarvaloresguia(0, 0, $resultado['notas'][$i]['idGuia'], $listadealumnos[$j]['idAlumnos']);
                    }


                }
                if ($resultado['notas'][$i]['tiempoGuia'] == 1) {
                    $resultado['notas'][$i]['fechaEvaluacion'] = $this->datetranslate($resultado['notas'][$i]['fechaGuia']);
                    $resultado['notas'][$i]['alumnos'] = $modelonotas->getvaloresguia($resultado['notas'][$i]['idGuia'], $idcurso, $idperiodo, $resultado['notas'][$i]['tiempoGuia']);

                } else if ($resultado['notas'][$i]['tiempoGuia'] == 2) {
                    $resultado['notas'][$i]['fechaEvaluacionSegSem'] = $this->datetranslate($resultado['notas'][$i]['fechaGuia']);
                    $resultado['notas'][$i]['alumnosSegSem'] = $modelonotas->getvaloresguia($resultado['notas'][$i]['idGuia'], $idcurso, $idperiodo, $resultado['notas'][$i]['tiempoGuia']);
                    $resultados['notas'][$i] = $resultado['notas'][$i];
                }

            }


            $lista_equivalencia = $modeloequivalencia->listarperiodo($idestablecimiento, $idperiodo, 2);
            foreach ($listadealumnos as $key => $value) {
                $modelonotasTotal = 0;
                $porcetajeTotal = 0;
                $promedioPorcentaje = 0;
                $promedioNota = 0;
                $countGuias = 0;

                $resValorGuiasSegSem = $modelonotas->getvaloresguiasegsem($value['idAlumnos'], $idasignatura, $idperiodo);

                $id_evaluacion = $resValorGuiasSegSem[0]['idEvaluacionGuia'];

                foreach ($resValorGuiasSegSem as $keys2 => $res) {

                    if ($res['valorFormativa'] != null) {

                        if ($res['porcentajeGuiaFinal'] != null) {
                            $porcetajeTotal += $res['porcentajeGuiaFinal'];
                            $countGuias++;
                        } else {
                            $promedioNota += 0;
                            $porcetajeTotal += 0;
                        }
                    }


                }


                if (($countGuias) != 0) {
                    $promedioPorcentaje = round($porcetajeTotal / ($countGuias));
                } else {
                    $promedioPorcentaje = 0;
                }

                if (count($lista_equivalencia) > 0 && $promedioPorcentaje != null) {
                    foreach ($lista_equivalencia as $i => $valor) {
                        if (($promedioPorcentaje >= $valor['porcentaje_inicio']) && ($promedioPorcentaje <= $valor['porcentaje_final'])) {
                            $promedioNota = $valor['equivalencia_nota'];
                            break;
                        }
                    }
                }

                $resultado['notaFinalSegSem'][$key] = ["nota" => $promedioNota, "porcentajenota" => $promedioPorcentaje];

                $modelonotas->cambiarnotaalumno($id_evaluacion, $promedioNota, $promedioPorcentaje, $value['idAlumnos']);

            }

            if (count($resultado['notas']) > 0) {
                $resultadonota = $modelonotas->getnotacursoguia($resultado['notas'][0]['idEvaluacionGuia'], $idcurso, $idperiodo, 1);
                if ($resultadonota) {
                    $resultado['notaFinal'] = $resultadonota;
                }

            }

            $db->commit();
            $this->_helper->json($resultado);

        } catch (Exception $e) {
            $db->rollBack();
            $this->_helper->json(array());

        }

    }

    public function agregarguialagAction()
    {

        $this->_helper->layout->disableLayout();
        $idasignatura = $this->_getParam('id', 0);

        $form = new Application_Form_Pruebas();
        $form->idAsignatura->setValue($idasignatura);
        $form->conte->setLabel("Nombre OA:");
        $form->fecha->setLabel("Fecha:");
        $form->removeElement('coef');
        $form->removeElement('publicar');

        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                $modelonotas = new Application_Model_DbTable_Notas();
                $pruebas = new Application_Model_DbTable_Pruebas();
                $alumnos = new Application_Model_DbTable_Alumnosactual();

                $periodo = new Zend_Session_Namespace('periodo');
                $idperiodo = $periodo->periodo;

                $idcurso = new Zend_Session_Namespace('id_curso');
                $idcursos = $idcurso->id_curso;

                $examenes = new Zend_Session_Namespace('examen');
                $examen = $examenes->examen;

                $rutusuario = new Zend_Session_Namespace('id');
                $usuario = $rutusuario->id;

                $est = new Zend_Session_Namespace('establecimiento');
                $idestablecimiento = $est->establecimiento;


                if (empty($usuario)) {
                    echo "<script type=\"text/javascript\">alert(\"Error de Sesión\");</script>";
                    echo "<script>parent.$.fancybox.close();</script>";
                    exit;
                } else {

                    $asignatura = $form->getValue('idAsignatura');
                    $contenido = $form->getValue('conte');
                    $tipoev = $form->getValue('tipoEvaluacionPrueba');
                    $fecha = $form->getValue('fecha');
                    $coef = 1;

                    $tiponota = $form->getValue('tipoNota');
                    $criterio = $form->getValue('criterio');
                    $porcentaje = $form->getValue('porcentajeExamen');


                    $listaalumnos = $alumnos->listaralumnoscurso($idcursos, $idperiodo);
                    $fecha = date("Y-m-d", strtotime($fecha));


                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                    // Iniciamos la transaccion
                    $db->beginTransaction();

                    try {
                        $date = new DateTime;
                        $fechacreacion = $date->format('Y-m-d H:i:s');


                        $valida = $pruebas->validar($contenido, $tipoev, $fecha, $idcursos, $idasignatura, $idperiodo);
                        if ($valida) {
                            if ($tiponota == 2) {
                                $fecha = date("Y-m-d");
                                $coef = 1;
                            }
                            if ($examen != 1) {
                                $tiponota = 1;
                            }


                            $pruebas->agregar($contenido, $fecha, $idcursos, $asignatura, $idperiodo, $usuario, $tipoev, $coef, $tiponota, $criterio, $porcentaje, 0, 1);
                            $idprueba = $pruebas->getAdapter()->lastInsertId();

                            $pruebas->agregarguias($contenido, $fecha, $idcursos, $asignatura, $idperiodo, $usuario, $tipoev, $idprueba);
                            $idguia = $pruebas->getAdapter()->lastInsertId();


                            //recorremos el arreglo con los datos recibidos del formulario
                            for ($i = 0; $i < count($listaalumnos); $i++) {

                                //Agregamos la Nota final
                                $modelonotas->agregarnotaguia($listaalumnos[$i]['idAlumnos'], $asignatura, $idcursos, 0, $usuario, $idprueba, $fechacreacion, $idperiodo, 1, 0);
                                $idnota = $modelonotas->getAdapter()->lastInsertId();

                                //Agregamos la notaGuia

                                $modelonotas->agregarnotaguialag($listaalumnos[$i]['idAlumnos'], $asignatura, $idcursos, $usuario, $fechacreacion, $idperiodo, 0, 0, 0, $idguia, $idnota);


                            }


                            $db->commit();
                            echo "<script type=\"text/javascript\">window.parent.Nuevo($idprueba);</script>";
                            echo "<script>parent.$.fancybox.close();</script>";
                            exit;
                        } else {
                            echo "<script type=\"text/javascript\">alert(\"Está Intentando agregar una evaluacion existente \");</script>";
                            $form->populate($formData);
                        }


                    } catch (Exception $e) {
                        // Si hubo problemas. Enviamos marcha atras
                        $db->rollBack();
                        echo $e;
                        echo "<script type=\"text/javascript\">alert(\"Error Intentelo nuevamente\" +$e);</script>";
                        echo "<script>parent.$.fancybox.close();</script>";
                        exit;
                    }
                }

            } else {
                $form->populate($formData);
            }
        } else {

            $idasignatura = $this->_getParam('id', 0);
            if ($idasignatura == 0) {
                echo "<script type=\"text/javascript\">alert(\"Debe seleccionar una asignatura\");</script>";
                echo "<script>parent.$.fancybox.close();</script>";
                exit;


            }
        }

    }

    public function guardaguianotalagAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();

            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            $modeloalumnos = new Application_Model_DbTable_Alumnosactual();
            $modelonotas = new Application_Model_DbTable_Notas();
            $modeloprueba = new Application_Model_DbTable_Pruebas();
            $modeloequivalencia = new Application_Model_DbTable_Equivalencia();

            $rutusuario = new Zend_Session_Namespace('id');
            $usuario = $rutusuario->id;

            $est = new Zend_Session_Namespace('establecimiento');
            $idestablecimiento = $est->establecimiento;

            $idtipoev = new Zend_Session_Namespace('tipoevaluacion');
            $idtevalucacion = $idtipoev->tipoevaluacion;

            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;

            $lista_equivalencia = $modeloequivalencia->listarperiodo($idestablecimiento, $idperiodo, 1);

            if (empty($usuario)) {
                echo Zend_Json::encode(array('response' => 'errorsesion1'));
            } else {

                if ($data['id'] == 0 || empty($data['id']) || $data['ids'] == 0 || empty($data['ids'])) {
                    echo Zend_Json::encode(array('response' => 'errorinserta2'));
                    die();
                }

                //Validamos que el valor sea un integer y mayor a 0
                if (is_numeric($data['nota_1']) && is_numeric($data['nota_2']) && is_numeric($data['nota_3'])) {
                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                    // Iniciamos la transaccion
                    $db->beginTransaction();
                    try {

                        $total = 0;
                        $promedio = 0;
                        if ($data['nota_1'] > 0) {
                            $total++;

                        }
                        if ($data['nota_2'] > 0) {
                            $total++;

                        }
                        if ($data['nota_3'] > 0) {
                            $total++;

                        }

                        if ($total > 0) {

                            $promedio = round(($data['nota_1'] + $data['nota_2'] + $data['nota_3']) / $total);


                        }

                        //Actualizamos las notas de lasa guias
                        $modelonotas->cambiarnotaguialag($data['nota_1'], $data['nota_2'], $data['nota_3'], $data['id']);
                        //Cambiamos la nota final
                        $modelonotas->cambiar($data['ids'], $promedio);

                        $resultadoguia = $modelonotas->get($data['ids']);
                        //Obtenemos el alumno Actual
                        $resultadoalumno = $modeloalumnos->getactual($resultadoguia['idAlumnos'], $idperiodo);

                        if ($idtevalucacion == 1) {
                            $resultadopromedio = $this->getpromedioperiodoAction(true, 2, $resultadoalumno[0]['idAlumnosActual'], $resultadoguia['idAsignatura']);
                        } else {
                            $resultadopromedio = $this->getpromedioperiodotrimAction(true, 2, $resultadoalumno[0]['idAlumnosActual'], $resultadoguia['idAsignatura']);
                        }


                        $porcentajes = $this->equivalenciaslag($lista_equivalencia, $promedio);
                        if (count($porcentajes) > 0) {
                            $db->commit();
                            if ($idtevalucacion == 1) {
                                echo Zend_Json::encode(array('response' => 'exito', 't' => false, 'p' => $promedio, 'pr' => $porcentajes[0], 'con' => $porcentajes[1], 'primero' => $resultadopromedio[0]['primero'], 'segundo' => $resultadopromedio[0]['segundo'], 'final' => $resultadopromedio[0]['final']));

                            } else {
                                echo Zend_Json::encode(array('response' => 'exito', 't' => true, 'p' => $promedio, 'pr' => $porcentajes[0], 'con' => $porcentajes[1], 'primero' => $resultadopromedio[0]['primero'], 'segundo' => $resultadopromedio[0]['segundo'], 'tercero' => $resultadopromedio[0]['tercero'], 'final' => $resultadopromedio[0]['final']));

                            }

                        } else {
                            $db->commit();
                            if ($idtevalucacion == 1) {
                                echo Zend_Json::encode(array('response' => 'exito', 't' => false, 'p' => $promedio, 'pr' => null, 'con' => null, 'primero' => $resultadopromedio[0]['primero'], 'segundo' => $resultadopromedio[0]['segundo'], 'final' => $resultadopromedio[0]['final']));

                            } else {
                                echo Zend_Json::encode(array('response' => 'exito', 't' => true, 'p' => $promedio, 'pr' => null, 'con' => null, 'primero' => $resultadopromedio[0]['primero'], 'segundo' => $resultadopromedio[0]['segundo'], 'tercero' => $resultadopromedio[0]['tercero'], 'final' => $resultadopromedio[0]['final']));

                            }

                        }


                    } catch (Exception $e) {
                        // Si hubo problemas. Enviamos marcha atras
                        $db->rollBack();
                        echo Zend_Json::encode(array('response' => 'errorinserta3' . $e));
                    }
                } else {

                    echo Zend_Json::encode(array('response' => 'errorinserta4'));
                }


            }
        }
    }

    public function accionesguialagAction()
    {

        $this->_helper->layout->disableLayout();
        $idasignatura = $this->_getParam('id', 0);
        $cargo = new Zend_Session_NameSpace("cargo");
        $rol = $cargo->cargo;

        $form = new Application_Form_Pruebaspre(array('params' => $idasignatura));
        $form->oa->setAttrib('style', 'max-width:180px');
        $form->idAsignatura->setValue($idasignatura);
        $form->removeElement('oa');
        $form->removeElement('coef');
        $form->removeElement('publicar');

        //$form->idAsignatura->setValue($idasignatura);
        $form->submit->setLabel('Modificar');
        $form->addElement('submit', 'eliminar');
        $form->eliminar->setLabel('Eliminar');
        $form->eliminar->setAttrib('class', 'red');
        $form->eliminar->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $form->eliminar->setOrder(11);
        if ($rol == 1 || $rol == 3 || $rol == 6) {
            $form->submit->setAttrib("disabled", "disabled");
            $form->eliminar->setAttrib("disabled", "disabled");
        }
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            if ($rol == 2) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {


                    $modelonota = new Application_Model_DbTable_Notas();
                    $modeloprueba = new Application_Model_DbTable_Pruebas();


                    $rutusuario = new Zend_Session_Namespace('id');
                    $usuario = $rutusuario->id;
                    if (empty($usuario)) {
                        echo "<script type=\"text/javascript\">alert(\"Error de Sesión\");</script>";
                        echo "<script>parent.$.fancybox.close();</script>";
                        exit;
                    } else {

                        //ahora los extraemos como se ve abajo
                        $idevaluacion = $form->getValue('idEvaluacion');
                        $idguia = $form->getValue('idGuia');
                        $contenido = $form->getValue('conte');
                        $tipoev = $form->getValue('tipoEvaluacionPrueba');
                        $fecha = $form->getValue('fecha');
                        $modificar = $form->getValue('submit');
                        $eliminar = $form->getValue('eliminar');
                        $fecha = date("Y-m-d", strtotime($fecha));

                        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                        // Iniciamos la transaccion
                        $db->beginTransaction();

                        try {


                            $datosguia = $modeloprueba->getguiaidev($idevaluacion);
                            if (count($datosguia) > 0) {
                                $idguia = $datosguia[0]['idGuia'];
                            }

                            if ($modificar) {

                                $modeloprueba->cambiar($idevaluacion, $contenido, $fecha, $tipoev, 1);
                                $modeloprueba->cambiarguia($idguia, $contenido);

                                $db->commit();
                                echo "<script type=\"text/javascript\">window.parent.Cambiar();</script>";
                                echo "<script>parent.$.fancybox.close();</script>";
                                exit;

                            } elseif ($eliminar) {


                                //eliminamos las notas y luego la evaluacion
                                $modelonota->borrar($idevaluacion);
                                $modeloprueba->borrar($idevaluacion);
                                $modeloprueba->borrarguiaidev($idevaluacion);
                                $modelonota->borrarnotasguia($idguia);

                                $db->commit();
                                echo "<script type=\"text/javascript\">window.parent.Eliminar();</script>";
                                echo "<script>parent.$.fancybox.close();</script>";
                                exit;
                            }


                        } catch (Exception $e) {
                            // Si hubo problemas. Enviamos marcha atras
                            $db->rollBack();
                            echo "<script type=\"text/javascript\">alert(\"Error Intentelo nuevamente\");</script>";
                            echo "<script>parent.$.fancybox.close();</script>";
                            exit;
                        }
                    }

                } else {
                    $form->populate($formData);
                }
            } else {
                echo "<script type=\"text/javascript\">alert(\"No posee los Permisos para ésta acción\");</script>";
                echo "<script>parent.$.fancybox.close();</script>";
                exit;

            }
        } else {

            $idevaluacion = $this->_getParam('id', 0);
            if ($idasignatura > 0) {
                $modeloprueba = new Application_Model_DbTable_Pruebas();

                $datosprueba = $modeloprueba->get($idevaluacion);
                $form->tipoEvaluacionPrueba->setValue($datosprueba['tiempo']);
                $fechaa = date("d-m-Y", strtotime($datosprueba['fechaEvaluacion']));
                $form->fecha->setValue($fechaa);
                $form->populate($datosprueba);
                $this->view->fechaset = $fechaa;


            }
        }

    }

    public function getnotasguialagpreAction()
    {
        $this->_helper->layout->disableLayout();

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcursos = new Zend_Session_Namespace('id_curso');
        $idcurso = $idcursos->id_curso;

        $idtipoev = new Zend_Session_Namespace('tipoevaluacion');
        $idtevalucacion = $idtipoev->tipoevaluacion;

        $est = new Zend_Session_Namespace('establecimiento');
        $idestablecimiento = $est->establecimiento;

        $modelonotas = new Application_Model_DbTable_Notas();
        $modeloprueba = new Application_Model_DbTable_Pruebas();
        $modeloalumnos = new Application_Model_DbTable_Alumnosactual();

        $modeloequivalencia = new Application_Model_DbTable_Equivalencia();
        $idasignatura = $this->_getParam('id');

        $asignaturas = new Zend_Session_Namespace('idAsignatura');
        $asignaturas->idAsignatura = $idasignatura;

        $listadealumnos = $modeloalumnos->listaralumnoscursoactual($idcurso, $idperiodo);
        $largoalumnos = count($listadealumnos);

        $date = new DateTime;
        $fechacreacion = $date->format('Y-m-d H:i:s');

        $tiempo = 1;
        if ($idtevalucacion == 2) {
            $tiempo = 3;
        }
        $lista_equivalencia = $modeloequivalencia->listarperiodo($idestablecimiento, $idperiodo, $tiempo);
        $resultado['notas'] = $modeloprueba->listarguiaspre($idasignatura, $idcurso, $idperiodo);
        $largoresultado = count($resultado['notas']);

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $db->beginTransaction();
        try {

            for ($i = 0; $i < $largoresultado; $i++) {
                $resultado['notas'][$i]['fechaEvaluacion'] = $this->datetranslate($resultado['notas'][$i]['fechaEvaluacion']);
                $resultado['notas'][$i]['alumnos'] = $modelonotas->getnotasasignaturalag($resultado['notas'][$i]['idEvaluacion'], $idcurso, $idperiodo);

                for ($j = 0; $j < count($listadealumnos); $j++) {
                    if ($modelonotas->validarnotaalumno($listadealumnos[$j]['idAlumnos'], $resultado['notas'][$i]['idEvaluacion'], $resultado['notas'][$i]['idAsignatura'], $idcurso, $idperiodo)) {
                        $modelonotas->agregarnotaguia($listadealumnos[$j]['idAlumnos'], $resultado['notas'][$i]['idAsignatura'], $idcurso, 0, $resultado['notas'][$i]['idCuenta'], $resultado['notas'][$i]['idEvaluacion'], $fechacreacion, $idperiodo, 1, 0);
                        $idnota = $modelonotas->getAdapter()->lastInsertId();
                        $modelonotas->agregarnotaguialag($listadealumnos[$j]['idAlumnos'], $resultado['notas'][$i]['idAsignatura'], $idcurso, $resultado['notas'][$i]['idCuenta'], $fechacreacion, $idperiodo, 0, 0, 0, $resultado['notas'][$i]['idGuia'], $idnota);

                    }

                    if(is_null($resultado['notas'][$i]['alumnos'][$j]['idNota'])){

                        $modelonotas->cambiarguia($resultado['notas'][$i]['alumnos'][$j]['idNotas'],1);
                        $modelonotas->agregarnotaguialag($listadealumnos[$j]['idAlumnos'], $resultado['notas'][$i]['idAsignatura'], $idcurso, $resultado['notas'][$i]['idCuenta'], $fechacreacion, $idperiodo, 0, 0, 0, $resultado['notas'][$i]['idGuia'], $resultado['notas'][$i]['alumnos'][$j]['idNotas']);
                    }

                }

                $resultado['notas'][$i]['alumnos'] = $modelonotas->getnotasasignaturalag($resultado['notas'][$i]['idEvaluacion'], $idcurso, $idperiodo);
                for ($j = 0; $j < $largoalumnos; $j++) {

                    if(is_null($resultado['notas'][$i]['alumnos'][$j]['nota'])){

                        $promedio=0;
                        $promedio_aux = array_values(array_diff(array($resultado['notas'][$i]['alumnos'][$j]['nota_1'],$resultado['notas'][$i]['alumnos'][$j]['nota_2'],$resultado['notas'][$i]['alumnos'][$j]['nota_3']), array('0')));

                        if (!empty($promedio_aux)) {
                            $promedio=round((array_sum($promedio_aux)) / count($promedio_aux));

                        }
                        $modelonotas->cambiar($resultado['notas'][$i]['alumnos'][$j]['idNota'], $promedio);
                        $resultado['notas'][$i]['alumnos'][$j]['nota']=$promedio;

                    }

                    $resultado['notas'][$i]['alumnos'][$j]['porcentaje'] = $this->equivalenciaslag($lista_equivalencia, $resultado['notas'][$i]['alumnos'][$j]['nota']);

                }
            }


            if ($idtevalucacion == 1) {
                $resultadopromedios = $this->getpromedioperiodopreAction(true, 0, null, null);
            } elseif ($idtevalucacion == 2) {
                $resultadopromedios = $this->getpromedioperiodopretrimAction(true, 0, null, null);
            }

            if ($resultadopromedios) {
                $resultado['promedios'] = $resultadopromedios;
            }

            $db->commit();
            $this->_helper->json($resultado);

        } catch (Exception $e) {
            $db->rollBack();
            $this->_helper->json(array());
        }


    }

    public
    function getpromedioperiodopreAction($opc, $tip, $idal, $idas)
    {
        $this->_helper->viewRenderer->setNeverRender(true);
        $this->_helper->ViewRenderer->setNoRender(true);
        $this->_helper->Layout->disableLayout();

        $idcursos = new Zend_Session_Namespace('id_curso');
        $id = $idcursos->id_curso;
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $modelcurso = new Application_Model_DbTable_Cursos();
        $modelnotas = new Application_Model_DbTable_Notas();
        $modelasignatura = new Application_Model_DbTable_Asignaturascursos();
        $modeloprueba = new Application_Model_DbTable_Pruebas();


        if ($opc) {

            if ($tip == 0) {
                $asignaturas = new Zend_Session_Namespace('idAsignatura');
                $idasignatura = $asignaturas->idAsignatura;
                $listaalumnos = $modelnotas->listaralumnoscurso($id, $idperiodo);

            } elseif ($tip == 2) {
                $idasignatura = $idas;
                $idalumno = $idal;
                $listaalumnos = $modelnotas->listaralumno($idalumno, $id, $idperiodo);
            }

        } else {
            $tipo = $this->_getParam('t', 0);
            if ($tipo == 0 || $tipo == null) {
                $asignaturas = new Zend_Session_Namespace('idAsignatura');
                $idasignatura = $asignaturas->idAsignatura;
                $listaalumnos = $modelnotas->listaralumnoscurso($id, $idperiodo);

            } elseif ($tipo == 2) {
                $idasignatura = $this->_getParam('as', 0);
                $idalumno = $this->_getParam('id', 0);
                $listaalumnos = $modelnotas->listaralumno($idalumno, $id, $idperiodo);
            }
        }


        $datoscurso = $modelcurso->listarcursoid($id, $idperiodo);
        $listanotas = $modelnotas->listarnotascursoperiodo2($id, $idperiodo, 1);
        $datosasignaturas = $modelasignatura->listarporasignaturapre($id, $idperiodo);
        $agregadecimas = false;
        $largoalumnos = count($listaalumnos);

        if ($largoalumnos == 0) {
            echo "<script type=\"text/javascript\">alert(\"Sin Notas\");</script>";
            echo "<script>parent.$.fancybox.close();</script>";
            exit;


        }

        for ($i = 0; $i < $largoalumnos; $i++) {

            $valores = $modelnotas->listarnotasporalumnoasignatura($listaalumnos[$i]['idAlumnos'], $idperiodo, $id, $idasignatura);
            if ($valores != '' || !empty($valores)) {
                $listaalumnos[$i]['listanotas'] = $valores;
            } else {
                $listaalumnos[$i]['listanotas'] = 0;

            }


        }


        //Agregamos cantidad de notas por alumno y la asignamos a cada aisgnatura

        if (!empty($datosasignaturas)) {
            $largoasignatura = count($datosasignaturas);
            for ($i = 0; $i < $largoasignatura; $i++) {

                $datoscuenta[$i] = $modelnotas->listarcantidadnotasanual($id, $idperiodo, $datosasignaturas[$i]['idAsignatura']);
                if (empty($datoscuenta[$i]) && $datosasignaturas[$i]['tipoAsignatura'] == 1) {
                    $datosasig[$i] = 0;
                } else {
                    $largonotas = count($datoscuenta[$i]);
                    $datosasig[$i] = 0;


                    for ($j = 0; $j < $largonotas; $j++) {

                        if ($datoscuenta[$i][$j]['coef'] == 2) {
                            $datosasig[$i] += 2;
                        } else {
                            $datosasig[$i] += 1;
                        }

                    }

                }

            }
        } else {
            echo "<script type=\"text/javascript\">alert(\"El Curso no Posee Asignaturas en el periodo\");</script>";
            echo "<script>parent.$.fancybox.close();</script>";
            exit;

        }


        if (!empty($listaalumnos)) {
            $r = 0;

            for ($i = 0; $i < $largoalumnos; $i++) {

                $r = 0;

                for ($j = 0; $j < $largoasignatura; $j++) {

                    $promtalleraux = array();
                    $porcentajetaller = array();
                    $sumataller = 0;
                    $promtallerauxs = array();
                    $porcentajetallers = array();
                    $sumatallers = 0;
                    $row = $datosasig[$j];


                    $promedio = 0;
                    $contador = 0;
                    $contadorpromedio = 0;
                    $promedios = 0;
                    $contadorpromedios = 0;
                    if ($row != 0) {
                        $largolistanota = count($listaalumnos[$i]['listanotas']);
                        for ($z = 0; $z < $largolistanota; $z++) {
                            $contadoraux = 0;

                            if ($datosasignaturas[$j]['idAsignatura'] == $listaalumnos[$i]['listanotas'][$z]['idAsignatura']) {


                                //Primer Semesre
                                if ($listaalumnos[$i]['listanotas'][$z]['tiempo'] == 1) {

                                    if ($listaalumnos[$i]['listanotas'][$z]['coef'] == 2) {
                                        $contador += 2;
                                        if ($listaalumnos[$i]['listanotas'][$z]['nota'] == 0 || empty($listaalumnos[$i]['listanotas'][$z]['nota']) || $listaalumnos[$i]['listanotas'][$z]['nota'] == NULL || $listaalumnos[$i]['listanotas'][$z]['nota'] == '') {
                                            $promedio += 0;
                                            $contadorpromedio += 0;


                                        } else {
                                            $promedio += ($listaalumnos[$i]['listanotas'][$z]['nota'] + $listaalumnos[$i]['listanotas'][$z]['nota']);
                                            $contadorpromedio += 2;

                                        }

                                    } else {
                                        $contador += 1;

                                        if ($listaalumnos[$i]['listanotas'][$z]['nota'] == 0 || empty($listaalumnos[$i]['listanotas'][$z]['nota']) || $listaalumnos[$i]['listanotas'][$z]['nota'] == NULL || $listaalumnos[$i]['listanotas'][$z]['nota'] == '') {
                                            if (!empty($listaalumnos[$i]["listanotas"][$z]["forma"])) {
                                                if ($listaalumnos[$i]["listanotas"][$z]["nota"] == 0) {

                                                    $promedio += 0;
                                                    $contadorpromedio += 0;

                                                }

                                            } else {
                                                $promedio += 0;
                                                $contadorpromedio += 0;


                                            }


                                        } else {
                                            if (!empty($listaalumnos[$i]["listanotas"][$z]["forma"]) && $listaalumnos[$i]["listanotas"][$z]["forma"] == 2) {

                                                $contadorauxtaller = true;
                                                $promtalleraux[] = $listaalumnos[$i]['listanotas'][$z]['nota'];
                                                $porcentajetaller[] = $listaalumnos[$i]["listanotas"][$z]["porcentaje"];


                                            } else {

                                                $promedio += $listaalumnos[$i]['listanotas'][$z]['nota'];
                                                $contadorpromedio += 1;


                                            }


                                        }


                                    }
                                }
                                if ($listaalumnos[$i]['listanotas'][$z]['tiempo'] == 2) {


                                    if ($listaalumnos[$i]['listanotas'][$z]['coef'] == 2) {
                                        $contador += 2;
                                        if ($listaalumnos[$i]['listanotas'][$z]['nota'] == 0 || empty($listaalumnos[$i]['listanotas'][$z]['nota']) || $listaalumnos[$i]['listanotas'][$z]['nota'] == NULL || $listaalumnos[$i]['listanotas'][$z]['nota'] == '') {
                                            $promedios += 0;
                                            $contadorpromedios += 0;


                                        } else {
                                            $promedios += ($listaalumnos[$i]['listanotas'][$z]['nota'] + $listaalumnos[$i]['listanotas'][$z]['nota']);
                                            $contadorpromedios += 2;

                                        }

                                    } else {


                                        $contador += 1;

                                        if ($listaalumnos[$i]['listanotas'][$z]['nota'] == 0 || empty($listaalumnos[$i]['listanotas'][$z]['nota']) || $listaalumnos[$i]['listanotas'][$z]['nota'] == NULL || $listaalumnos[$i]['listanotas'][$z]['nota'] == '') {
                                            if (!empty($listaalumnos[$i]["listanotas"][$z]["forma"])) {
                                                if ($listaalumnos[$i]["listanotas"][$z]["nota"] == 0) {

                                                    $promedios += 0;
                                                    $contadorpromedios += 0;

                                                }

                                            } else {
                                                $promedios += 0;
                                                $contadorpromedios += 0;


                                            }


                                        } else {
                                            if (!empty($listaalumnos[$i]["listanotas"][$z]["forma"]) && $listaalumnos[$i]["listanotas"][$z]["forma"] == 2) {

                                                $contadorauxtallers = true;
                                                $promtallerauxs[] = $listaalumnos[$i]['listanotas'][$z]['nota'];
                                                $porcentajetallers[] = $listaalumnos[$i]["listanotas"][$z]["porcentaje"];


                                            } else {

                                                $promedios += $listaalumnos[$i]['listanotas'][$z]['nota'];
                                                $contadorpromedios += 1;


                                            }

                                        }

                                    }
                                }


                                //Promedios por notas

                                if ($contador == $row) {
                                    $promedioaux = 0;
                                    $promedioauxs = 0;

                                    if ($contadorpromedio != 0 && $promedio != 0) {
                                        $promedioaux = 0;
                                        //SI el Taller es por porcentaje
                                        if ($contadorauxtaller) {
                                            $totalporcentaje = 100;
                                            if (count($promtalleraux) > 1) {
                                                for ($t = 0; $t < count($promtalleraux); $t++) {
                                                    $totalporcentaje = $totalporcentaje - $porcentajetaller[$t];
                                                    $sumataller += $promtalleraux[$t] * ($porcentajetaller[$t] / 100);
                                                }

                                            } else {
                                                $totalporcentaje = 100 - $porcentajetaller[0];
                                                $sumataller = $promtalleraux[0] * ($porcentajetaller[0] / 100);

                                            }


                                            if ($datoscurso[0]['aproxAsignatura'] == 1) {//Aproxima
                                                $promedioaux = round($promedio / $contadorpromedio);
                                                $promedioaux = ($promedioaux * ($totalporcentaje / 100)) + $sumataller;
                                                $promedioaux = round($promedioaux);


                                            } else {
                                                $promedioaux = round($promedio / $contadorpromedio);
                                                $promedioaux = ($promedioaux * ($totalporcentaje / 100)) + $sumataller;
                                                $promedioaux = intval($promedioaux);
                                            }

                                        } else {

                                            if ($datoscurso[0]['aproxAsignatura'] == 1) {//Aproxima
                                                $promedioaux = round($promedio / $contadorpromedio);
                                                $promfinal[$i][] = $promedioaux;

                                            } else {
                                                $promedioaux = intval($promedio / $contadorpromedio);
                                                $promfinal[$i][] = $promedioaux;
                                            }

                                        }


                                        $promedioalumnos[$i]['primero'] = $promedioaux;


                                    } else {
                                        $promedioalumnos[$i]['primero'] = 0;
                                        $promfinal[$i][] = 0;


                                    }


                                    //Segundo Semestre
                                    if ($contadorpromedios != 0 && $promedios != 0) {
                                        $promedioauxs = 0;
                                        //SI el Taller es por porcentaje
                                        if ($contadorauxtallers) {
                                            $totalporcentajes = 100;
                                            if (count($promtallerauxs) > 1) {
                                                for ($t = 0; $t < count($promtallerauxs); $t++) {
                                                    $totalporcentajes = $totalporcentajes - $porcentajetallers[$t];
                                                    $sumatallers += $promtallerauxs[$t] * ($porcentajetallers[$t] / 100);
                                                }

                                            } else {
                                                $totalporcentajes = 100 - $porcentajetallers[0];
                                                $sumatallers = $promtallerauxs[0] * ($porcentajetallers[0] / 100);

                                            }


                                            if ($datoscurso[0]['aproxAsignatura'] == 1) {//Aproxima
                                                $promedioauxs = round($promedios / $contadorpromedios);
                                                $promedioauxs = ($promedioauxs * ($totalporcentajes / 100)) + $sumatallers;
                                                $promedioauxs = round($promedioauxs);


                                            } else {
                                                $promedioauxs = round($promedios / $contadorpromedios);
                                                $promedioauxs = ($promedioauxs * ($totalporcentajes / 100)) + $sumatallers;
                                                $promedioauxs = intval($promedioauxs);

                                            }

                                        } else {

                                            if ($datoscurso[0]['aproxAsignatura'] == 1) {//Aproxima
                                                $promedioauxs = round($promedios / $contadorpromedios);
                                                $promfinal[$i][] = $promedioauxs;


                                            } else {
                                                $promedioauxs = intval($promedios / $contadorpromedios);
                                                $promfinal[$i][] = $promedioauxs;

                                            }
                                        }


                                        $promedioalumnos[$i]['segundo'] = $promedioauxs;


                                    } else {
                                        $promedioalumnos[$i]['segundo'] = 0;
                                        $promfinal[$i][] = 0;


                                    }


                                    //Promedio Final De Asignatura
                                    $finalasignatura = 0;

                                    if ($promedioaux != 0 && $promedioauxs != 0) {

                                        if ($datoscurso[0]['aproxAnual'] == 1) {
                                            $finalasignatura = round(($promedioaux + $promedioauxs) / 2);
                                            if ($finalasignatura == 39 && $datoscurso[0]['rbd'] == '1864') {
                                                $finalasignatura = 40;
                                            }
                                        } else {
                                            $finalasignatura = intval(($promedioaux + $promedioauxs) / 2);
                                            if ($finalasignatura == 39 && $datoscurso[0]['rbd'] == '1864') {
                                                $finalasignatura = 40;
                                            }

                                        }
                                        if ($agregadecimas) {
                                            if ($finalasignatura >= 60) {
                                                $finalasignatura += 2;
                                                if ($finalasignatura > 70) {
                                                    $finalasignatura = 70;

                                                }

                                            }
                                        }
                                        $promediototal[$i][] = $finalasignatura;

                                        $notamatriz[$r][] = $finalasignatura;


                                    }

                                    if ($promedioaux == 0 && $promedioauxs == 0) {

                                        $finalasignatura = 0;


                                    }

                                    if ($promedioaux == 0 && $promedioauxs != 0) {


                                        $finalasignatura = $promedioauxs;

                                        if ($agregadecimas) {
                                            if ($finalasignatura >= 60) {
                                                $finalasignatura += 2;
                                                if ($finalasignatura > 70) {
                                                    $finalasignatura = 70;

                                                }

                                            }
                                        }
                                        $promediototal[$i][] = $finalasignatura;
                                        $notamatriz[$r][] = $finalasignatura;


                                    }
                                    if ($promedioaux != 0 && $promedioauxs == 0) {

                                        $finalasignatura = $promedioaux;
                                        if ($agregadecimas) {
                                            if ($finalasignatura >= 60) {
                                                $finalasignatura += 2;
                                                if ($finalasignatura > 70) {
                                                    $finalasignatura = 70;

                                                }

                                            }
                                        }

                                        $promediototal[$i][] = $finalasignatura;
                                        $notamatriz[$r][] = $finalasignatura;


                                    }

                                }// fin promedio por notas

                            }

                        }// fin for


                    } else {


                    } //fin if row


                } //fin for Asignaturas


                if ($promediototal[$i] != '' || $promediototal[$i] != null) {

                    if ($datoscurso[0]['aproxFinal'] == 1) {//Aproxima
                        $promedioalumnos[$i]['final'] = round(array_sum($promediototal[$i]) / count($promediototal[$i]));


                    } else {
                        $promedioalumnos[$i]['final'] = intval(array_sum($promediototal[$i]) / count($promediototal[$i]));


                    }


                } else {
                    $promedioalumnos[$i]['final'] = 0;
                }


            }// Fin for lista Alumnos


        }


        $resultadoconcepto = $modelasignatura->getasignaturaconcepto($idasignatura, $id, $idperiodo);
        if ($resultadoconcepto) {
            for ($i = 0; $i < count($promedioalumnos); $i++) {

                for ($k = 0; $k < count($resultadoconcepto); $k++) {

                    if ($promedioalumnos[$i]['primero'] == 0) {
                        $promedioalumnos[$i]['primeroconcepto'] = 0;
                    } else {
                        if ($resultadoconcepto[$k]['desde'] <= $promedioalumnos[$i]['primero'] && $resultadoconcepto[$k]['hasta'] >= $promedioalumnos[$i]['primero']) {
                            $promedioalumnos[$i]['primeroconcepto'] = $resultadoconcepto[$k]['concepto'];
                        }
                    }

                    if ($promedioalumnos[$i]['segundo'] == 0) {
                        $promedioalumnos[$i]['segundoconcepto'] = 0;

                    } else {
                        if ($resultadoconcepto[$k]['desde'] <= $promedioalumnos[$i]['segundo'] && $resultadoconcepto[$k]['hasta'] >= $promedioalumnos[$i]['segundo']) {
                            $promedioalumnos[$i]['segundoconcepto'] = $resultadoconcepto[$k]['concepto'];
                        }
                    }

                    if ($promedioalumnos[$i]['final'] == 0) {
                        $promedioalumnos[$i]['finalconcepto'] = 0;

                    } else {
                        if ($resultadoconcepto[$k]['desde'] <= $promedioalumnos[$i]['final'] && $resultadoconcepto[$k]['hasta'] >= $promedioalumnos[$i]['final']) {
                            $promedioalumnos[$i]['finalconcepto'] = $resultadoconcepto[$k]['concepto'];
                        }
                    }

                }


            }
        }


        if ($tipo == 2) {
            if ($datoscurso[0]['examen'] == 1) {
                //Examen a final de asignatura
                $datosexamenes = $modeloprueba->getexamen($id, $idperiodo, $idasignatura, 6);
                if ($datosexamenes[0]) {
                    $datosalumnosexamen = $modelnotas->getnotasexamenalumno($datosexamenes[0]['idEvaluacion'], $id, $idasignatura, $idperiodo, 6, $listaalumnos[0]['idAlumnos']);

                }

                if ($datosalumnosexamen) {


                    if ($promedioalumnos[0]['final'] > 0) {

                        $totalex = 100 - $datosexamenes[0]['porcentajeExamen'];
                        $sumaex = $datosalumnosexamen[0]['nota'] * ($datosexamenes[0]['porcentajeExamen'] / 100);

                        if ($datoscurso[0]['aproxExamen'] == 1) {
                            $promedioalumnos[0]['finalex'] = round(($promedioalumnos[0]['final'] * ($totalex / 100)) + $sumaex);
                        } else {
                            $promedioalumnos[0]['finalex'] = intval(($promedioalumnos[0]['final'] * ($totalex / 100)) + $sumaex);
                        }


                    } else {
                        $promedioalumnos[0]['finalex'] = $promedioalumnos[0]['final'];
                    }

                }

            }
            $this->_helper->json($promedioalumnos);
        } else {
            return $promedioalumnos;
        }


    }

    public function guardaguianotalagpreAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {


            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();

            $json = file_get_contents('php://input');
            $data = json_decode($json, true);
            $modeloalumnos = new Application_Model_DbTable_Alumnosactual();
            $modelonotas = new Application_Model_DbTable_Notas();
            $modeloequivalencia = new Application_Model_DbTable_Equivalencia();

            $rutusuario = new Zend_Session_Namespace('id');
            $usuario = $rutusuario->id;

            $est = new Zend_Session_Namespace('establecimiento');
            $idestablecimiento = $est->establecimiento;

            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;

            $idtipoev = new Zend_Session_Namespace('tipoevaluacion');
            $idtevalucacion = $idtipoev->tipoevaluacion;
            $tiempo=1;
            if($idtevalucacion==2){
                $tiempo=3;
            }

            $lista_equivalencia = $modeloequivalencia->listarperiodo($idestablecimiento, $idperiodo, $tiempo);

            if (empty($usuario)) {
                echo Zend_Json::encode(array('response' => 'errorsesion1'));
            } else {

                if ($data['id'] == 0 || empty($data['id']) || $data['ids'] == 0 || empty($data['ids'])) {
                    echo Zend_Json::encode(array('response' => 'errorinserta'));
                    die();
                }

                if (is_numeric($data['nota_1']) && is_numeric($data['nota_2']) && is_numeric($data['nota_3'])) {

                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                    $db->beginTransaction();
                    try {

                        $promedio=0;
                        $promedio_aux = array_values(array_diff(array($data['nota_1'],$data['nota_2'],$data['nota_3']), array('0')));

                        if (!empty($promedio_aux)) {
                            $promedio=round((array_sum($promedio_aux)) / count($promedio_aux));

                        }

                        $modelonotas->cambiarnotaguialag($data['nota_1'], $data['nota_2'], $data['nota_3'], $data['id']);
                        $modelonotas->cambiar($data['ids'], $promedio);
                        $resultadoguia = $modelonotas->get($data['ids']);
                        $resultadoalumno = $modeloalumnos->getactual($resultadoguia['idAlumnos'], $idperiodo);
                        $resultadopromedio = $this->getpromedioperiodopreAction(true, 2, $resultadoalumno[0]['idAlumnosActual'], $resultadoguia['idAsignatura']);
                        $porcentajes = $this->equivalenciaslag($lista_equivalencia, $promedio);

                        if (count($porcentajes) > 0) {
                            $db->commit();
                            echo Zend_Json::encode(array('response' => 'exito', 'p' => $promedio, 'pr' => $porcentajes[0], 'con' => $porcentajes[1], 'primero' => $resultadopromedio[0]['primero'], 'segundo' => $resultadopromedio[0]['segundo'], 'final' => $resultadopromedio[0]['final']));

                        } else {
                            $db->commit();
                            echo Zend_Json::encode(array('response' => 'exito', 'p' => $promedio, 'pr' => null, 'con' => null, 'primero' => $resultadopromedio[0]['primero'], 'segundo' => $resultadopromedio[0]['segundo'], 'final' => $resultadopromedio[0]['final']));

                        }

                    } catch (Exception $e) {
                        $db->rollBack();
                        echo Zend_Json::encode(array('response' => 'errorinserta'));
                    }
                } else {
                    echo Zend_Json::encode(array('response' => 'errorinserta'));
                }


            }
        }
    }

    public function agregarguialagpreAction()
    {

        $this->_helper->layout->disableLayout();
        $idasignatura = $this->_getParam('id', 0);

        $form = new Application_Form_Pruebaspre(array('params' => $idasignatura));
        $form->oa->setAttrib('style', 'max-width:180px');
        $form->idAsignatura->setValue($idasignatura);
        $form->fecha->setLabel("Fecha:");
        $form->removeElement('coef');
        $form->removeElement('publicar');

        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                $modelonotas = new Application_Model_DbTable_Notas();
                $pruebas = new Application_Model_DbTable_Pruebas();
                $alumnos = new Application_Model_DbTable_Alumnosactual();

                $periodo = new Zend_Session_Namespace('periodo');
                $idperiodo = $periodo->periodo;

                $idcurso = new Zend_Session_Namespace('id_curso');
                $idcursos = $idcurso->id_curso;

                $examenes = new Zend_Session_Namespace('examen');
                $examen = $examenes->examen;

                $rutusuario = new Zend_Session_Namespace('id');
                $usuario = $rutusuario->id;
                if (empty($usuario)) {
                    echo "<script type=\"text/javascript\">alert(\"Error de Sesión\");</script>";
                    echo "<script>parent.$.fancybox.close();</script>";
                    exit;
                } else {

                    $idoa = $form->getValue('oa');
                    $tipoev = $form->getValue('tipoEvaluacionPrueba');
                    $fecha = $form->getValue('fecha');
                    $coef = 1;

                    $tiponota = $form->getValue('tipoNota');
                    $criterio = $form->getValue('criterio');
                    $porcentaje = $form->getValue('porcentajeExamen');


                    $modeloasignatura = new Application_Model_DbTable_Asignaturascursos();
                    $nombreoa = $modeloasignatura->getoa($idoa);

                    $contenido = explode('-', $nombreoa[0]['nombreAsignatura'], 2);
                    $contenido_final = $contenido[0];


                    $listaalumnos = $alumnos->listaralumnoscurso($idcursos, $idperiodo);
                    $fecha = date("Y-m-d", strtotime($fecha));


                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                    // Iniciamos la transaccion
                    $db->beginTransaction();
                    try {
                        $date = new DateTime;
                        $fechacreacion = $date->format('Y-m-d H:i:s');


                        $valida = $pruebas->validarpre($tipoev, $idcursos, $idoa, $idperiodo);
                        if ($valida) {

                            if ($tiponota == 2) {
                                $fecha = date("Y-m-d");
                                $coef = 1;
                            }
                            if ($examen != 1) {
                                $tiponota = 1;
                            }


                            $pruebas->agregar($contenido_final, $fecha, $idcursos, $idoa, $idperiodo, $usuario, $tipoev, $coef, $tiponota, $criterio, $porcentaje, 0, 1);
                            $idprueba = $pruebas->getAdapter()->lastInsertId();

                            $pruebas->agregarguias($contenido_final, $fecha, $idcursos, $idoa, $idperiodo, $usuario, $tipoev, $idprueba);
                            $idguia = $pruebas->getAdapter()->lastInsertId();

                            for ($i = 0; $i < count($listaalumnos); $i++) {

                                //Agregamos la Nota final
                                $modelonotas->agregarnotaguia($listaalumnos[$i]['idAlumnos'], $idoa, $idcursos, 0, $usuario, $idprueba, $fechacreacion, $idperiodo, 1, 0);
                                $idnota = $modelonotas->getAdapter()->lastInsertId();

                                //Agregamos la notaGuia
                                $modelonotas->agregarnotaguialag($listaalumnos[$i]['idAlumnos'], $idoa, $idcursos, $usuario, $fechacreacion, $idperiodo, 0, 0, 0, $idguia, $idnota);


                            }

                            $db->commit();
                            echo "<script type=\"text/javascript\">window.parent.Nuevo($idprueba);</script>";
                            echo "<script>parent.$.fancybox.close();</script>";
                            exit;
                        } else {
                            echo "<script type=\"text/javascript\">alert(\"Está Intentando agregar una evaluacion existente \");</script>";
                            $form->populate($formData);
                        }


                    } catch (Exception $e) {
                        // Si hubo problemas. Enviamos marcha atras
                        $db->rollBack();
                        echo $e;
                        echo "<script type=\"text/javascript\">alert(\"Error Intentelo nuevamente\" +$e);</script>";
                        echo "<script>parent.$.fancybox.close();</script>";
                        exit;
                    }
                }

            } else {
                $form->populate($formData);
            }
        } else {

            $idasignatura = $this->_getParam('id', 0);
            if ($idasignatura == 0) {
                echo "<script type=\"text/javascript\">alert(\"Debe seleccionar una asignatura\");</script>";
                echo "<script>parent.$.fancybox.close();</script>";
                exit;


            }
        }

    }


    /*Requerimiento Libro Digital 15-01-2020*/


    public function nuevaentrevistaAction()
    {
        $modeloalumnos = new Application_Model_DbTable_Alumnosactual();

        //recuperamos el nivel del curso que esta en sesion
        $idcursos = new Zend_Session_Namespace('id_curso');
        $idcurso = $idcursos->id_curso;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;

        $cryptor = new \Chirp\Cryptor();

        $id = $this->_getParam('id', 0);
        $ids = $cryptor->decrypt($id);
        $this->view->idAlumnos = $ids;

        $datos = $modeloalumnos->getactualalumno($ids, $idperiodo, $idcurso);


        $form = new Application_Form_NuevaEntrevista();
        $form->Alumnos->setValue($datos[0]['nombres'] . ' ' . $datos[0]['apaterno'] . ' ' . $datos[0]['amaterno']);
        $form->ida->setValue($id);

        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($form->isValid($formData)) {


                $modelo = new Application_Model_DbTable_Observacion();


                $idalumnos = $form->getValue('ida');
                $asignatura = $form->getValue('idAsignatura');
                $entrevista = $form->getValue('entrevista');
                $fecha = $form->getValue('fechaEntrevista');

                $fecha = date("Y-m-d", strtotime($fecha));

                $idalumnos = $cryptor->decrypt($idalumnos);


                $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                // Iniciamos la transaccion
                $db->beginTransaction();
                try {

                    $modelo->agregarentrevista($idalumnos, $fecha, $asignatura, $entrevista, $user, $idperiodo, $idcurso);
                    $db->commit();
                    $this->redirect('/Libro/verobservaciones/id/' . $idalumnos);

                } catch (Exception $e) {
                    // Si hubo problemas. Enviamos todos marcha atras
                    $db->rollBack();
                    echo $e;
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage(self::MENSAJE);
                    $this->view->assign('messages', $messages);
                }

            } else {

                $form->populate($formData);

            }

        }

    }

    public function editarentrevistaAction()
    {

        $modeloalumnos = new Application_Model_DbTable_Alumnosactual();
        $modelo = new Application_Model_DbTable_Observacion();

        //recuperamos el nivel del curso que esta en sesion
        $idcursos = new Zend_Session_Namespace('id_curso');
        $idcurso = $idcursos->id_curso;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;

        $cryptor = new \Chirp\Cryptor();

        $id = $this->_getParam('id', 0);
        $ids = $cryptor->decrypt($id);

        $datos = $modeloalumnos->getactualalumno($ids, $idperiodo, $idcurso);


        $form = new Application_Form_NuevaEntrevista();
        $form->Alumnos->setValue($datos[0]['nombres'] . ' ' . $datos[0]['apaterno'] . ' ' . $datos[0]['amaterno']);

        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($form->isValid($formData)) {


                $idalumnos = $form->getValue('ida');
                $asignatura = $form->getValue('idAsignatura');
                $entrevista = $form->getValue('entrevista');
                $fecha = $form->getValue('fechaEntrevista');
                $identrevista = $form->getValue('idEntrevista');
                $identrevista = $cryptor->decrypt($identrevista);

                $fecha = date("Y-m-d", strtotime($fecha));

                $idalumnos = $cryptor->decrypt($idalumnos);


                $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                // Iniciamos la transaccion
                $db->beginTransaction();
                try {

                    $modelo->cambiarentrevista($identrevista, $fecha, $asignatura, $entrevista, $user, $idperiodo, $idcurso);
                    $db->commit();
                    $this->redirect('/Libro/verobservaciones/id/' . $idalumnos);

                } catch (Exception $e) {
                    // Si hubo problemas. Enviamos todos marcha atras
                    $db->rollBack();
                    echo $e;
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage(self::MENSAJE);
                    $this->view->assign('messages', $messages);
                }

            } else {

                $form->populate($formData);

            }

        } else {


            $id = $this->_getParam('id', 0);
            $ids = $cryptor->decrypt($id);

            $ida = $this->_getParam('ida', 0);
            $idas = $cryptor->decrypt($ida);

            if ($ids && $idas) {
                $datosentrevista = $modelo->getentrevista($ids, $idperiodo, $idcurso);

                if ($datosentrevista) {

                    $datoalumno = $modeloalumnos->getactual($datosentrevista[0]['idAlumnos'], $idperiodo);
                    $nombrealumno = $datoalumno[0]['nombres'] . ' ' . $datoalumno[0]['apaterno'] . ' ' . $datoalumno[0]['amaterno'];


                    $form->Alumnos->setValue($nombrealumno);
                    $form->populate($datosentrevista[0]);
                    $fecha = date("d-m-Y", strtotime($datosentrevista[0]['fechaEntrevista']));
                    $form->fechaEntrevista->setValue($fecha);

                    $form->idEntrevista->setValue($id);
                    $form->ida->setValue($cryptor->encrypt($datoalumno[0]['idAlumnos']));
                    $this->view->alumno = $idas;


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


    public function eliminarentrevistaAction()
    {


        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $cryptor = new \Chirp\Cryptor();

        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;

        if ($rol == '3' || $rol == '2' || $rol == '1') {


            $modelo = new Application_Model_DbTable_Observacion();
            $id = $this->_getParam('id', 0);
            $ida = $this->_getParam('ida', 0);
            $ids = $cryptor->decrypt($id);
            $idas = $cryptor->decrypt($ida);

            if ($ids && $idas) {

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                // Iniciamos la transaccion
                $db->beginTransaction();
                try {

                    $modelo->eliminarentrevista($ids, $user, $idperiodo);

                    if ($idas) {

                        $db->commit();
                        $this->redirect('/Libro/verobservaciones/id/' . $idas);
                    } else {

                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage(self::MENSAJE);
                        $this->view->assign('messages', $messages);
                    }


                } catch (Exception $e) {
                    // Si hubo problemas. Enviamos todos marcha atras
                    $db->rollBack();
                    echo $e;
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage(self::MENSAJE);
                    $this->view->assign('messages', $messages);
                }

            } else {
                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage(self::MENSAJE);
                $this->view->assign('messages', $messages);
            }

        }


    }


    public function controlasistenciaAction()
    {

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $idcurso = new Zend_Session_Namespace('id_curso');
        $id_curso = $idcurso->id_curso;

        $nombrecurso = new Zend_Session_Namespace('nombre_curso');
        $nombre_curso = $nombrecurso->nombre_curso;


        $modelocontenido = new Application_Model_DbTable_Contenido();
        $modeloasistencia = new Application_Model_DbTable_Asistencia();


        //Si el es Docente
        if ($rol == '2') {
            $activePage = $this->view->navigation()->findOneByLabel('Abierto');
            $activePage->setlabel($nombre_curso);
            $activePage->setparam('id', $id_curso);

            $usuario = new Zend_Session_Namespace('id');
            $user = $usuario->id;


            $datoscontenido = $modeloasistencia->listarasistencias($id_curso, $user, $idperiodo);
            for ($i = 0; $i < count($datoscontenido); $i++) {
                $datoscontenido [$i]['fechaControl'] = date("d-m-Y", strtotime($datoscontenido [$i]['fechaControl']));
                $datoscontenido[$i]['bloques'] = $modeloasistencia->listarbloquesasistencia($datoscontenido[$i]['idControlContenido']);

            }
            $this->view->datos = $datoscontenido;

        }

        //Si el Rol es Adminitrador daem o Director

        if ($rol == '3' || $rol == '1' || $rol == '6') {

            $activePage = $this->view->navigation()->findOneByLabel('Abierto');
            $activePage->setlabel($nombre_curso);
            $activePage->setparam('id', $id_curso);

            $datosjson = array();

            $datoscontenido = $modeloasistencia->listaradminasistencias($id_curso, $idperiodo);
            for ($i = 0; $i < count($datoscontenido); $i++) {
                $datoscontenido [$i]['fechaControl'] = date("d-m-Y", strtotime($datoscontenido [$i]['fechaControl']));
                $datoscontenido[$i]['bloques'] = $modeloasistencia->listarbloquesasistencia($datoscontenido[$i]['idControlContenido']);


            }

            $this->view->datos = $datoscontenido;


        }

    }

    public function controlasistenciasAction()
    {

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $idcurso = new Zend_Session_Namespace('id_curso');
        $id_curso = $idcurso->id_curso;

        $nombrecurso = new Zend_Session_Namespace('nombre_curso');
        $nombre_curso = $nombrecurso->nombre_curso;


        $modeloasistencia = new Application_Model_DbTable_Asistencia();
        $modeloperiodo = new Application_Model_DbTable_Periodo();
        $modelohorario = new Application_Model_DbTable_Horario();
        $datosjson = array();

        $modelocurso = new Application_Model_DbTable_Cursos();
        $datoscurso = $modelocurso->listarcursoid($id_curso, $idperiodo);

        $results = $modeloperiodo->getfechacurso($id_curso, $idperiodo);

        //Validamos si existe creado el calendario
        if (!empty($results[0])) {
            $date = new DateTime();
            $dateTimestamp1 = strtotime($date->format('Y-m-d') . '+1 day');
            $dateTimestamp2 = strtotime($results[0]['fechaTerminoClase']);
            $results[0]['fechaTerminoClase'] = date("Y-m-d", $dateTimestamp1);
            if ($dateTimestamp1 > $dateTimestamp2) {
                $results[0]['fechaTerminoClase'] = date("Y-m-d", $dateTimestamp2);
            }


            //Si el es Docente
            if ($rol == '2') {
                $activePage = $this->view->navigation()->findOneByLabel('Abierto');
                $activePage->setlabel($nombre_curso);
                $activePage->setparam('id', $id_curso);

                $begin = new DateTime($results[0]['fechaInicioClase']);
                $end = new DateTime($results[0]['fechaTerminoClase']);

                $interval = DateInterval::createFromDateString('1 day');
                $period = new DatePeriod($begin, $interval, $end);

                $usuario = new Zend_Session_Namespace('id');
                $user = $usuario->id;

                $datos_horario = $modelohorario->gethorariocurso($idperiodo, $id_curso, $user, 2);

                foreach ($period as $dt) {
                    $day_week = date('N', strtotime($dt->format("Y-m-d")));
                    if ($day_week < 6) {

                        $idcontrol = $modeloasistencia->getasistenciafecha($id_curso, $idperiodo, $user, $dt->format("Y-m-d"));

                        $search = $day_week;
                        $found = array_filter($datos_horario, function ($v, $k) use ($search) {
                            return $v['dia'] == $search;
                        }, ARRAY_FILTER_USE_BOTH);

                        $found = array_values($found);

                        if (!empty($idcontrol)) {
                            for ($i = 0; $i < count($idcontrol); $i++) {
                                $indice = array_keys(array_column($found, 'idHorario'), $idcontrol[$i]['idHorario']);
                                $found[$indice[0]]['event'] = true;
                                $found[$indice[0]]['idControlAsistencia'] = $idcontrol[$i]['idControlAsistencia'];
                            }
                        }

                        $datoscontenido[] = array('fechaControl' => $dt->format("Y-m-d"), 'bloques' => array_values($found));

                    }
                }

                //Get Feriados y eventos del curso

                $modelocalendario = new Application_Model_DbTable_Periodo();
                $eventos_curso = $modelocalendario->geteventos($id_curso, $idperiodo, 2, $results[0]['fechaInicioClase'], $results[0]['fechaTerminoClase']);
                $eventos_establecimiento = $modelocalendario->geteventos($datoscurso[0]['idEstablecimiento'], $idperiodo, 1, $results[0]['fechaInicioClase'], $results[0]['fechaTerminoClase']);


                $feriado = array_merge($eventos_establecimiento, $eventos_curso);
                foreach ($feriado as $f) {

                    $key = array_search($f['fechaEvento'], array_column($datoscontenido, 'fechaControl'));
                    $datoscontenido[$key]['bloques'] = array();

                    $datosjson[] = array('title' => 'Día Bloqueado, Motivo:' . $f['nombreEvento'], 'overlap' => false, 'start' => $f['fechaEvento'], 'end' => date('Y-m-d ', strtotime($f['fechaEvento'] . ' +1 day')), 'display' => 'block', 'color' => 'red');

                }

                for ($i = 0; $i < count($datoscontenido); $i++) {
                    for ($j = 0; $j < count($datoscontenido[$i]['bloques']); $j++) {
                        $datoscontenido[$i]['bloques'][$j]['timestart'] = strtotime($datoscontenido [$i]['fechaControl'] . ' ' . $datoscontenido[$i]['bloques'][$j]['tiempoInicio']) * 1000;
                        $datoscontenido[$i]['bloques'][$j]['timeend'] = strtotime($datoscontenido [$i]['fechaControl'] . ' ' . $datoscontenido[$i]['bloques'][$j]['tiempoTermino']) * 1000;

                        if (isset($datoscontenido[$i]['bloques'][$j]['event'])) {
                            $datosjson[] = array('title' => $datoscontenido[$i]['bloques'][$j]['nombreAsignatura'], 'start' => $datoscontenido[$i]['bloques'][$j]['timestart'], 'end' => $datoscontenido[$i]['bloques'][$j]['timeend'], 'url' => $this->_request->getBaseUrl() . '/Libro/editarcontrolasistencia/id/' . $datoscontenido[$i]['bloques'][$j]['idControlAsistencia'], 'idca' => $datoscontenido[$i]['bloques'][$j]['idControlAsistencia'], 'color' => 'green');

                        } else {
                            $datosjson[] = array('title' => $datoscontenido[$i]['bloques'][$j]['nombreAsignatura'], 'start' => $datoscontenido[$i]['bloques'][$j]['timestart'], 'end' => $datoscontenido[$i]['bloques'][$j]['timeend'], 'color' => '#D02210');

                        }

                    }

                }


                $datoshorariousuario = $modelohorario->getdiashabilitados($user, $id_curso, $idperiodo);
                $dias = array(0, 1, 2, 3, 4, 5, 6);
                for ($i = 0; $i < count($datoshorariousuario); $i++) {
                    $diasaux[$i] = $datoshorariousuario[$i]['dia'];

                }

                $dias = array_diff($dias, $diasaux);

                $this->view->datoscurso = $results;
                $this->view->datos = $datoscontenido;
                $this->view->json = json_encode($datosjson);
                $this->view->dias = $dias;


            }

            //Si el Rol es Adminitrador daem o Director

            if ($rol == '3' || $rol == '1' || $rol == '6') {

                $activePage = $this->view->navigation()->findOneByLabel('Abierto');
                $activePage->setlabel($nombre_curso);
                $activePage->setparam('id', $id_curso);

                $begin = new DateTime($results[0]['fechaInicioClase']);
                $end = new DateTime($results[0]['fechaTerminoClase']);

                $interval = DateInterval::createFromDateString('1 day');
                $period = new DatePeriod($begin, $interval, $end);


                $datos_horario = $modelohorario->gethorariocurso($idperiodo, $id_curso, null, 2);

                foreach ($period as $dt) {
                    $day_week = date('N', strtotime($dt->format("Y-m-d")));
                    if ($day_week < 6) {

                        $idcontrol = $modeloasistencia->getasistenciafecha($id_curso, $idperiodo, null, $dt->format("Y-m-d"));

                        $search = $day_week;
                        $found = array_filter($datos_horario, function ($v, $k) use ($search) {
                            return $v['dia'] == $search;
                        }, ARRAY_FILTER_USE_BOTH);

                        $found = array_values($found);

                        if (!empty($idcontrol)) {
                            for ($i = 0; $i < count($idcontrol); $i++) {
                                $indice = array_keys(array_column($found, 'idHorario'), $idcontrol[$i]['idHorario']);
                                $found[$indice[0]]['event'] = true;
                                $found[$indice[0]]['idControlAsistencia'] = $idcontrol[$i]['idControlAsistencia'];
                            }
                        }

                        $datoscontenido[] = array('fechaControl' => $dt->format("Y-m-d"), 'bloques' => array_values($found));

                    }
                }

                //Get Feriados y eventos del curso

                $modelocalendario = new Application_Model_DbTable_Periodo();
                $eventos_curso = $modelocalendario->geteventos($id_curso, $idperiodo, 2, $results[0]['fechaInicioClase'], $results[0]['fechaTerminoClase']);
                $eventos_establecimiento = $modelocalendario->geteventos($datoscurso[0]['idEstablecimiento'], $idperiodo, 1, $results[0]['fechaInicioClase'], $results[0]['fechaTerminoClase']);


                $feriado = array_merge($eventos_establecimiento, $eventos_curso);
                foreach ($feriado as $f) {

                    $key = array_search($f['fechaEvento'], array_column($datoscontenido, 'fechaControl'));
                    $datoscontenido[$key]['bloques'] = array();

                    $datosjson[] = array('title' => 'Día Bloqueado, Motivo:' . $f['nombreEvento'], 'overlap' => false, 'start' => $f['fechaEvento'], 'end' => date('Y-m-d ', strtotime($f['fechaEvento'] . ' +1 day')), 'display' => 'block', 'color' => 'red');

                }

                for ($i = 0; $i < count($datoscontenido); $i++) {
                    for ($j = 0; $j < count($datoscontenido[$i]['bloques']); $j++) {
                        $datoscontenido[$i]['bloques'][$j]['timestart'] = strtotime($datoscontenido [$i]['fechaControl'] . ' ' . $datoscontenido[$i]['bloques'][$j]['tiempoInicio']) * 1000;
                        $datoscontenido[$i]['bloques'][$j]['timeend'] = strtotime($datoscontenido [$i]['fechaControl'] . ' ' . $datoscontenido[$i]['bloques'][$j]['tiempoTermino']) * 1000;
                        if (isset($datoscontenido[$i]['bloques'][$j]['event'])) {
                            $datosjson[] = array('title' => $datoscontenido[$i]['bloques'][$j]['nombreAsignatura'] . '-' . $datoscontenido[$i]['bloques'][$j]['nombrescuenta'] . ' ' . $datoscontenido[$i]['bloques'][$j]['paternocuenta'] . ' ' . $datoscontenido[$i]['bloques'][$j]['maternocuenta'], 'start' => $datoscontenido[$i]['bloques'][$j]['timestart'], 'end' => $datoscontenido[$i]['bloques'][$j]['timeend'], 'url' => $this->_request->getBaseUrl() . '/Libro/vercontrolasistencia/id/' . $datoscontenido[$i]['bloques'][$j]['idControlAsistencia'], 'idca' => $datoscontenido[$i]['bloques'][$j]['idControlAsistencia'], 'color' => 'green');

                        } else {
                            $datosjson[] = array('title' => $datoscontenido[$i]['bloques'][$j]['nombreAsignatura'] . '-' . $datoscontenido[$i]['bloques'][$j]['nombrescuenta'] . ' ' . $datoscontenido[$i]['bloques'][$j]['paternocuenta'] . ' ' . $datoscontenido[$i]['bloques'][$j]['maternocuenta'], 'start' => $datoscontenido[$i]['bloques'][$j]['timestart'], 'end' => $datoscontenido[$i]['bloques'][$j]['timeend'], 'color' => '#D02210');

                        }
                    }

                }

                $datoscontenido = array_values($datoscontenido);
                $dias = array(0, 6);


                $this->view->datoscurso = $results;
                $this->view->datos = $datoscontenido;
                $this->view->json = json_encode($datosjson);
                $this->view->dias = $dias;


            }
        }


    }


    public function registrarasistenciaAction()
    {

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcurso = new Zend_Session_Namespace('id_curso');
        $id_curso = $idcurso->id_curso;

        $nombrecurso = new Zend_Session_Namespace('nombre_curso');
        $nombre_curso = $nombrecurso->nombre_curso;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        if ($rol == 2) {

            $activePage = $this->view->navigation()->findOneByLabel('Abierto');
            $activePage->setlabel($nombre_curso);
            $activePage->setparam('id', $id_curso);

            $modeloalumnos = new Application_Model_DbTable_Alumnosactual();

            $datosalumnos = $modeloalumnos->listaralumnoscursoactual($id_curso, $idperiodo);

            $form = new Application_Form_ControlAsistencia();
            $this->view->form = $form;
            $this->view->listaalumnos = $datosalumnos;

        }


    }

    public function editarcontrolasistenciaAction()
    {


        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcurso = new Zend_Session_Namespace('id_curso');
        $id_curso = $idcurso->id_curso;

        $nombrecurso = new Zend_Session_Namespace('nombre_curso');
        $nombre_curso = $nombrecurso->nombre_curso;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        if ($rol == 2) {

            $activePage = $this->view->navigation()->findOneByLabel('Abierto');
            $activePage->setlabel($nombre_curso);
            $activePage->setparam('id', $id_curso);

            $id = $this->_getParam('id');

            if ($id > 0) {

                $modeloasistencia = new Application_Model_DbTable_Asistencia();
                $modeloalumno = new Application_Model_DbTable_Alumnosactual();

                $listaalumnos = $modeloalumno->listaralumnoscursoactual($id_curso, $idperiodo);

                $selectausente = $modeloasistencia->listarcategoriaasistencia(1);
                $selectpresente = $modeloasistencia->listarcategoriaasistencia(2);


                //creamos el Select Ausente
                $elementoselecta = "<select class='observacion'>";
                for ($i = 0; $i < count($selectausente); $i++) {

                    $elementoselecta .= '<option value="' . $selectausente[$i]['codigo'] . '">' . $selectausente[$i]['descripcion'] . '</option>';

                }
                $elementoselecta .= "</select>";

                $largoalumnos = count($listaalumnos);
                $formato = 'H:i:s';

                for ($i = 0; $i < $largoalumnos; $i++) {

                    $datosalumnos = $modeloasistencia->listarasistenciavalores($id, $listaalumnos[$i]['idAlumnos']);


                    if (!empty($datosalumnos)) {

                        $datosalumnos[0]['rutAlumno'] = $listaalumnos[$i]['rutAlumno'];
                        $datosalumnos[0]['nombres'] = $listaalumnos[$i]['nombres'];
                        $datosalumnos[0]['amaterno'] = $listaalumnos[$i]['amaterno'];
                        $datosalumnos[0]['apaterno'] = $listaalumnos[$i]['apaterno'];

                        $date = DateTime::createFromFormat($formato, $datosalumnos[0]['tiempoInicio']);
                        $datosalumnos[0]['tiempoInicio'] = $date->format('H:i');

                        $date = DateTime::createFromFormat($formato, $datosalumnos[0]['tiempoTermino']);
                        $datosalumnos[0]['tiempoTermino'] = $date->format('H:i');


                        if ($datosalumnos[0]['valorasistencia'] == 2) { //Presente

                            $elementoselect = "<select class='observacion'>";
                            for ($j = 0; $j < count($selectpresente); $j++) {
                                if ($datosalumnos[0]['tipoAsistencia'] == $selectpresente[$j]['codigo']) {
                                    $elementoselect .= '<option selected value="' . $selectpresente[$j]['codigo'] . '">' . $selectpresente[$j]['descripcion'] . '</option>';
                                } else {
                                    $elementoselect .= '<option value="' . $selectpresente[$j]['codigo'] . '">' . $selectpresente[$j]['descripcion'] . '</option>';
                                }


                            }
                            $elementoselect .= "</select>";
                            $datosalumnos[0]['select'] = $elementoselect;


                        } elseif ($datosalumnos[0]['valorasistencia'] == 1) {
                            $elementoselecta = "<select class='observacion'>";
                            for ($j = 0; $j < count($selectausente); $j++) {
                                if ($datosalumnos[0]['tipoAsistencia'] == $selectausente[$j]['codigo']) {
                                    $elementoselecta .= '<option selected value="' . $selectausente[$j]['codigo'] . '">' . $selectausente[$j]['descripcion'] . '</option>';
                                } else {
                                    $elementoselecta .= '<option value="' . $selectausente[$j]['codigo'] . '">' . $selectausente[$j]['descripcion'] . '</option>';
                                }


                            }
                            $elementoselecta .= "</select>";
                            $datosalumnos[0]['select'] = $elementoselecta;
                        }

                        $datos[$i] = $datosalumnos;

                    }


                }


                $datos[0][0]['fechaControl'] = date("d-m-Y", strtotime($datos[0][0]['fechaControl']));
                $this->view->datos = $datos;

            }


        }

    }

    public function controlregistrosAction()
    {

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $idcurso = new Zend_Session_Namespace('id_curso');
        $id_curso = $idcurso->id_curso;

        $nombrecurso = new Zend_Session_Namespace('nombre_curso');
        $nombre_curso = $nombrecurso->nombre_curso;


        $modelocontenido = new Application_Model_DbTable_Contenido();
        $modeloperiodo = new Application_Model_DbTable_Periodo();
        $modelohorario = new Application_Model_DbTable_Horario();
        $datosjson = array();

        $modelocurso = new Application_Model_DbTable_Cursos();
        $datoscurso = $modelocurso->listarcursoid($id_curso, $idperiodo);

        $results = $modeloperiodo->getfechacurso($id_curso, $idperiodo);

        //Validamos si existe creado el calendario
        if (!empty($results[0])) {
            $date = new DateTime();
            $dateTimestamp1 = strtotime($date->format('Y-m-d') . '+1 day');
            $dateTimestamp2 = strtotime($results[0]['fechaTerminoClase']);
            $results[0]['fechaTerminoClase'] = date("Y-m-d", $dateTimestamp1);
            if ($dateTimestamp1 > $dateTimestamp2) {
                $results[0]['fechaTerminoClase'] = date("Y-m-d", $dateTimestamp2);
            }


            //Si el es Docente
            if ($rol == '2') {
                $activePage = $this->view->navigation()->findOneByLabel('Abierto');
                $activePage->setlabel($nombre_curso);
                $activePage->setparam('id', $id_curso);

                $begin = new DateTime($results[0]['fechaInicioClase']);
                $end = new DateTime($results[0]['fechaTerminoClase']);

                $interval = DateInterval::createFromDateString('1 day');
                $period = new DatePeriod($begin, $interval, $end);

                $usuario = new Zend_Session_Namespace('id');
                $user = $usuario->id;

                $datos_horario = $modelohorario->gethorariocurso($idperiodo, $id_curso, $user, 2);

                foreach ($period as $dt) {
                    $day_week = date('N', strtotime($dt->format("Y-m-d")));
                    if ($day_week < 6) {

                        $idcontrol = $modelocontenido->getregistrofecha($id_curso, $idperiodo, $user, $dt->format("Y-m-d"));

                        $search = $day_week;
                        $found = array_filter($datos_horario, function ($v, $k) use ($search) {
                            return $v['dia'] == $search;
                        }, ARRAY_FILTER_USE_BOTH);

                        $found = array_values($found);

                        if (!empty($idcontrol)) {
                            for ($i = 0; $i < count($idcontrol); $i++) {
                                $indice = array_keys(array_column($found, 'idHorario'), $idcontrol[$i]['idHorario']);
                                if (!empty($indice)) {
                                    $found[$indice[0]]['event'] = true;
                                    $found[$indice[0]]['idControlContenidoDetalle'] = $idcontrol[$i]['idControlContenidoDetalle'];
                                    $found[$indice[0]]['contenidos'] = $idcontrol[$i]['contenidos'];
                                }

                            }
                        }

                        $datoscontenido[] = array('fechaControl' => $dt->format("Y-m-d"), 'bloques' => array_values($found));

                    }
                }

                //Get Feriados y eventos del curso

                $modelocalendario = new Application_Model_DbTable_Periodo();
                $eventos_curso = $modelocalendario->geteventos($id_curso, $idperiodo, 2, $results[0]['fechaInicioClase'], $results[0]['fechaTerminoClase']);
                $eventos_establecimiento = $modelocalendario->geteventos($datoscurso[0]['idEstablecimiento'], $idperiodo, 1, $results[0]['fechaInicioClase'], $results[0]['fechaTerminoClase']);


                $feriado = array_merge($eventos_establecimiento, $eventos_curso);
                foreach ($feriado as $f) {

                    $key = array_search($f['fechaEvento'], array_column($datoscontenido, 'fechaControl'));
                    $datoscontenido[$key]['bloques'] = array();

                    $datosjson[] = array('title' => 'Día Bloqueado, Motivo:' . $f['nombreEvento'], 'overlap' => false, 'start' => $f['fechaEvento'], 'end' => date('Y-m-d ', strtotime($f['fechaEvento'] . ' +1 day')), 'display' => 'block', 'color' => 'red');

                }


                for ($i = 0; $i < count($datoscontenido); $i++) {

                    for ($j = 0; $j < count($datoscontenido[$i]['bloques']); $j++) {
                        $datoscontenido[$i]['bloques'][$j]['timestart'] = strtotime($datoscontenido [$i]['fechaControl'] . ' ' . $datoscontenido[$i]['bloques'][$j]['tiempoInicio']) * 1000;
                        $datoscontenido[$i]['bloques'][$j]['timeend'] = strtotime($datoscontenido [$i]['fechaControl'] . ' ' . $datoscontenido[$i]['bloques'][$j]['tiempoTermino']) * 1000;

                        if (isset($datoscontenido[$i]['bloques'][$j]['event'])) {
                            $datosjson[] = array('title' => $datoscontenido[$i]['bloques'][$j]['nombreAsignatura'], 'start' => $datoscontenido[$i]['bloques'][$j]['timestart'], 'end' => $datoscontenido[$i]['bloques'][$j]['timeend'], 'url' => $this->_request->getBaseUrl() . '/Libro/editarcontenido/id/' . $datoscontenido[$i]['bloques'][$j]['idControlContenidoDetalle'], 'color' => 'green');

                        } else {
                            $datosjson[] = array('title' => $datoscontenido[$i]['bloques'][$j]['nombreAsignatura'], 'start' => $datoscontenido[$i]['bloques'][$j]['timestart'], 'end' => $datoscontenido[$i]['bloques'][$j]['timeend'], 'color' => '#D02210');

                        }

                    }

                }


                $datoshorariousuario = $modelohorario->getdiashabilitados($user, $id_curso, $idperiodo);
                $dias = array(0, 1, 2, 3, 4, 5, 6);
                for ($i = 0; $i < count($datoshorariousuario); $i++) {
                    $diasaux[$i] = $datoshorariousuario[$i]['dia'];

                }

                $dias = array_diff($dias, $diasaux);


                $this->view->datoscurso = $results;
                $this->view->datos = $datoscontenido;
                $this->view->json = json_encode($datosjson);
                $this->view->dias = $dias;


            }

            //Si el Rol es Adminitrador daem o Director

            if ($rol == '3' || $rol == '1' || $rol == '6') {

                $activePage = $this->view->navigation()->findOneByLabel('Abierto');
                $activePage->setlabel($nombre_curso);
                $activePage->setparam('id', $id_curso);

                $begin = new DateTime($results[0]['fechaInicioClase']);
                $end = new DateTime($results[0]['fechaTerminoClase']);

                $interval = DateInterval::createFromDateString('1 day');
                $period = new DatePeriod($begin, $interval, $end);


                $datos_horario = $modelohorario->gethorariocurso($idperiodo, $id_curso, null, 2);

                foreach ($period as $dt) {
                    $day_week = date('N', strtotime($dt->format("Y-m-d")));
                    if ($day_week < 6) {

                        $idcontrol = $modelocontenido->getregistrofecha($id_curso, $idperiodo, null, $dt->format("Y-m-d"));

                        $search = $day_week;
                        $found = array_filter($datos_horario, function ($v, $k) use ($search) {
                            return $v['dia'] == $search;
                        }, ARRAY_FILTER_USE_BOTH);

                        $found = array_values($found);

                        if (!empty($idcontrol)) {
                            for ($i = 0; $i < count($idcontrol); $i++) {
                                $indice = array_keys(array_column($found, 'idHorario'), $idcontrol[$i]['idHorario']);
                                if (!empty($indice)) {
                                    $found[$indice[0]]['event'] = true;
                                    $found[$indice[0]]['idControlContenidoDetalle'] = $idcontrol[$i]['idControlContenidoDetalle'];
                                    $found[$indice[0]]['contenidos'] = $idcontrol[$i]['contenidos'];
                                }

                            }
                        }

                        $datoscontenido[] = array('fechaControl' => $dt->format("Y-m-d"), 'bloques' => array_values($found));

                    }
                }

                //Get Feriados y eventos del curso

                $modelocalendario = new Application_Model_DbTable_Periodo();
                $eventos_curso = $modelocalendario->geteventos($id_curso, $idperiodo, 2, $results[0]['fechaInicioClase'], $results[0]['fechaTerminoClase']);
                $eventos_establecimiento = $modelocalendario->geteventos($datoscurso[0]['idEstablecimiento'], $idperiodo, 1, $results[0]['fechaInicioClase'], $results[0]['fechaTerminoClase']);


                $feriado = array_merge($eventos_establecimiento, $eventos_curso);
                foreach ($feriado as $f) {

                    $key = array_search($f['fechaEvento'], array_column($datoscontenido, 'fechaControl'));
                    $datoscontenido[$key]['bloques'] = array();

                    $datosjson[] = array('title' => 'Día Bloqueado, Motivo:' . $f['nombreEvento'], 'overlap' => false, 'start' => $f['fechaEvento'], 'end' => date('Y-m-d ', strtotime($f['fechaEvento'] . ' +1 day')), 'display' => 'block', 'color' => 'red');

                }


                for ($i = 0; $i < count($datoscontenido); $i++) {
                    for ($j = 0; $j < count($datoscontenido[$i]['bloques']); $j++) {
                        $datoscontenido[$i]['bloques'][$j]['timestart'] = strtotime($datoscontenido [$i]['fechaControl'] . ' ' . $datoscontenido[$i]['bloques'][$j]['tiempoInicio']) * 1000;
                        $datoscontenido[$i]['bloques'][$j]['timeend'] = strtotime($datoscontenido [$i]['fechaControl'] . ' ' . $datoscontenido[$i]['bloques'][$j]['tiempoTermino']) * 1000;
                        if (isset($datoscontenido[$i]['bloques'][$j]['event'])) {
                            $datosjson[] = array('title' => $datoscontenido[$i]['bloques'][$j]['nombreAsignatura'] . '-' . $datoscontenido[$i]['bloques'][$j]['nombrescuenta'] . ' ' . $datoscontenido[$i]['bloques'][$j]['paternocuenta'] . ' ' . $datoscontenido[$i]['bloques'][$j]['maternocuenta'], 'start' => $datoscontenido[$i]['bloques'][$j]['timestart'], 'end' => $datoscontenido[$i]['bloques'][$j]['timeend'], 'url' => $this->_request->getBaseUrl() . '/Libro/vercontenido/id/' . $datoscontenido[$i]['bloques'][$j]['idControlContenidoDetalle'], 'color' => 'green');

                        } else {
                            $datosjson[] = array('title' => $datoscontenido[$i]['bloques'][$j]['nombreAsignatura'] . '-' . $datoscontenido[$i]['bloques'][$j]['nombrescuenta'] . ' ' . $datoscontenido[$i]['bloques'][$j]['paternocuenta'] . ' ' . $datoscontenido[$i]['bloques'][$j]['maternocuenta'], 'start' => $datoscontenido[$i]['bloques'][$j]['timestart'], 'end' => $datoscontenido[$i]['bloques'][$j]['timeend'], 'color' => '#D02210');

                        }
                    }

                }

                $datoscontenido = array_values($datoscontenido);
                $dias = array(0, 6);


                $this->view->datoscurso = $results;
                $this->view->datos = $datoscontenido;
                $this->view->json = json_encode($datosjson);
                $this->view->dias = $dias;


            }
        }


    }

    public function controlregistroAction()
    {
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $idcurso = new Zend_Session_Namespace('id_curso');
        $id_curso = $idcurso->id_curso;

        $nombrecurso = new Zend_Session_Namespace('nombre_curso');
        $nombre_curso = $nombrecurso->nombre_curso;


        $modelocontenido = new Application_Model_DbTable_Contenido();


        //Si el es Docente
        if ($rol == '2') {
            $activePage = $this->view->navigation()->findOneByLabel('Abierto');
            $activePage->setlabel($nombre_curso);
            $activePage->setparam('id', $id_curso);

            $usuario = new Zend_Session_Namespace('id');
            $user = $usuario->id;


            $datoscontenido = $modelocontenido->listarcontenidos($id_curso, $user, $idperiodo);

            for ($i = 0; $i < count($datoscontenido); $i++) {
                $datoscontenido [$i]['fechaControl'] = date("d-m-Y", strtotime($datoscontenido [$i]['fechaControl']));
                $datoscontenido[$i]['bloques'] = $modelocontenido->listarbloquescontenidouser($datoscontenido[$i]['idControlContenido'], $user);
            }
            $this->view->datos = $datoscontenido;

        }

        //Si el Rol es Adminitrador daem o Director

        if ($rol == '3' || $rol == '1' || $rol == '6') {

            $activePage = $this->view->navigation()->findOneByLabel('Abierto');
            $activePage->setlabel($nombre_curso);
            $activePage->setparam('id', $id_curso);

            $datoscontenido = $modelocontenido->listarcontenidosadmin($id_curso, $idperiodo);
            for ($i = 0; $i < count($datoscontenido); $i++) {
                $datoscontenido [$i]['fechaControl'] = date("d-m-Y", strtotime($datoscontenido [$i]['fechaControl']));
                $datoscontenido[$i]['bloques'] = $modelocontenido->listarbloquescontenido($datoscontenido[$i]['idControlContenido']);
            }
            $this->view->datos = $datoscontenido;


        }
    }

    public function getdiascalendarioAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $modeloperiodo = new Application_Model_DbTable_Periodo();
        $modelocurso = new Application_Model_DbTable_Cursos();
        $modeloasistencia = new Application_Model_DbTable_Asistencia();
        $modelocontenido = new Application_Model_DbTable_Contenido();

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcurso = new Zend_Session_Namespace('id_curso');
        $id_curso = $idcurso->id_curso;

        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;

        $est = new Zend_Session_Namespace('establecimiento');
        $idestablecimiento = $est->establecimiento;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        if ($rol == 2) {


            $tabla = $this->_getParam('t', 0);

            //obtenemos el calendario de establecimiento y del curso

            $calendario_establecimiento = $modeloperiodo->getcalendarioestablecimiento($idestablecimiento, $idperiodo);
            $results[0] = $modeloperiodo->getfechacurso($id_curso, $idperiodo);
            //Validamos si existe creado el calendario
            if (!empty($results[0]) && !empty($calendario_establecimiento[0])) {
                //Si existe cambimos el formato de la fecha de inicio y termino del periodo escolar
                $results[0][0]['fechaInicioClase'] = date("d-m-Y", strtotime($results[0][0]['fechaInicioClase']));
                $auxfecha = $results[0][0]['fechaTerminoClase'];
                $date = new DateTime();
                $dateTimestamp1 = strtotime($date->format('Y-m-d'));
                $dateTimestamp2 = strtotime($results[0][0]['fechaTerminoClase']);
                $results[0][0]['fechaTerminoClase'] = date("d-m-Y", $dateTimestamp1);
                if ($dateTimestamp1 > $dateTimestamp2) {
                    $results[0][0]['fechaTerminoClase'] = date("d-m-Y", $dateTimestamp2);
                }

                $results[1] = $modeloperiodo->getdiaseventoscurso(array($results[0][0]['idCalendario'], $calendario_establecimiento[0]['idCalendario']));
                $results[2] = $modelocurso->getdiashabilitados($user, $id_curso, $idperiodo);

                if ($tabla == 3) {
                    //Si existe cambimos el formato de la fecha de inicio y termino del periodo escolar
                    $results[0][0]['fechaInicioClase'] = date("d-m-Y", strtotime($results[0][0]['fechaInicioClase']));
                    $results[0][0]['fechaTerminoClase'] = date("d-m-Y", strtotime($auxfecha));
                    $results[3] = array();
                }

                if ($tabla == 1) { //Si es contenido

                    //Obtenemos los dias que estan completados de acuerdo al horario y se deshabilitan

                    $datoscontenido = $modelocontenido->listarcontenidos($id_curso, $user, $idperiodo);
                    $diascompletados = array();
                    if (count($datoscontenido) > 0) {


                        for ($i = 0; $i < count($datoscontenido); $i++) {
                            $datoscontenido[$i]['bloque'] = array();
                            $datoscontenido[$i]['dia'] = $dayofweek = date('w', strtotime($datoscontenido[$i]['fechaControl']));
                            //Obtenemos el horario del dia
                            $datoshorariousuario = $modelocurso->gethorariousuariodia($user, $id_curso, $datoscontenido[$i]['dia'], $idperiodo);
                            //Buscamos con el horario del dia del usuario si existen registro guardados por dia
                            for ($j = 0; $j < count($datoshorariousuario); $j++) {

                                $datosaux = $modelocontenido->listarbloquescontenidoid($datoscontenido[$i]['idControlContenido'], $datoshorariousuario[$j]['idHorario']);

                                if (!$datosaux) {
                                    $datoscontenido[$i]['bloque'][] = $datosaux;
                                }


                            }

                            //Obtenemos los dias que mantienen registros completos
                            if (count($datoscontenido[$i]['bloque']) == 0) {
                                $diascompletados[]['fechaCompleta'] = $datoscontenido[$i]['fechaControl'];
                            }

                        }

                    }

                    $results[3] = $diascompletados;

                } elseif ($tabla == 2) { //Si es Asistencia

                    //Obtenemos los dias que estan completados de Asistencia de acuerdo al horario y se deshabilitan

                    $datosasistencia = $modeloasistencia->listarasistencias($id_curso, $user, $idperiodo);
                    $diascompletados = array();
                    if (count($datosasistencia) > 0) {


                        for ($i = 0; $i < count($datosasistencia); $i++) {
                            $datosasistencia[$i]['bloque'] = array();
                            $datosasistencia[$i]['dia'] = $dayofweek = date('w', strtotime($datosasistencia[$i]['fechaControl']));
                            //Obtenemos el horario del dia
                            $datoshorariousuario = $modelocurso->gethorariousuariodia($user, $id_curso, $datosasistencia[$i]['dia'], $idperiodo);
                            //Buscamos con el horario del dia del usuario si existen registro guardados por dia
                            for ($j = 0; $j < count($datoshorariousuario); $j++) {

                                $datosaux = $modeloasistencia->listarbloquesasistenciaid($datosasistencia[$i]['idControlContenido'], $datoshorariousuario[$j]['idHorario']);

                                if (!$datosaux) {
                                    $datosasistencia[$i]['bloque'][] = $datosaux;
                                }


                            }

                            //Obtenemos los dias que mantienen registros completos
                            if (count($datosasistencia[$i]['bloque']) == 0) {
                                $diascompletados[]['fechaCompleta'] = $datosasistencia[$i]['fechaControl'];
                            }

                        }

                    }

                    $results[3] = $diascompletados;

                }


                $this->_helper->json($results);

            }

        }
    }


    public function getasignaturabloqueAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $modelocurso = new Application_Model_DbTable_Cursos();

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcurso = new Zend_Session_Namespace('id_curso');
        $id_curso = $idcurso->id_curso;

        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        if ($rol == 2) {

            $fecha = $this->_getParam('date', 0);
            $fechas = explode('-', $fecha);
            if (count($fechas) == 3) {
                if (checkdate($fechas[1], $fechas[0], $fechas[2])) {
                    //Cambiamos el Formato
                    $fecha = date("Y-m-d", strtotime($fecha));
                    $dayofweek = date('w', strtotime($fecha));
                    $results = $modelocurso->getasignaturabloque($user, $id_curso, $idperiodo, $dayofweek);
                    //Validamos si existe creado el calendario
                    if (!empty($results)) {
                        $this->_helper->json($results);

                    }

                }
                $this->_helper->json(array());
            }


        }
    }

    public function getbloqueAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();


        $modelocurso = new Application_Model_DbTable_Cursos();
        $modeloasistencia = new Application_Model_DbTable_Asistencia();
        $modelocontenido = new Application_Model_DbTable_Contenido();

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcurso = new Zend_Session_Namespace('id_curso');
        $id_curso = $idcurso->id_curso;

        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        if ($rol == 2) {

            $fecha = $this->_getParam('date', 0);
            $idasignatura = $this->_getParam('id', 0);
            $tabla = $this->_getParam('t', 0);

            $fechas = explode('-', $fecha);
            if (count($fechas) == 3 && $idasignatura > 0 && checkdate($fechas[1], $fechas[0], $fechas[2])) {

                //Cambiamos el Formato
                $fecha = date("Y-m-d", strtotime($fecha));
                $dayofweek = date('w', strtotime($fecha));

                //Obtenemos los bloque que ya han sido registrados
                if ($tabla == 2) {
                    $datosbloque = $modeloasistencia->listarasistencia($id_curso, $idasignatura, $fecha, $idperiodo);


                    if ($datosbloque) {
                        $listabloques = $modeloasistencia->listarbloquesasistencia($datosbloque[0]['idControlContenido'], $user);

                    } else {
                        $listabloques = array();
                        $horarios = array();
                    }

                    for ($i = 0; $i < count($listabloques); $i++) {
                        $horarios[] = $listabloques[$i]['idHorario'];

                    }
                    if (count($horarios) > 0) {
                        $results = $modelocurso->getbloquesin($user, $id_curso, $idperiodo, $dayofweek, $idasignatura, $horarios);
                    } else {
                        $results = $modelocurso->getbloque($user, $id_curso, $idperiodo, $dayofweek, $idasignatura);
                    }
                } else if ($tabla == 1) {
                    $datosbloque = $modelocontenido->listarcontenido($id_curso, $idasignatura, $fecha, $idperiodo);
                    if ($datosbloque) {
                        $listabloques = $modelocontenido->listarbloquescontenido($datosbloque[0]['idControlContenido']);

                    } else {
                        $listabloques = array();
                        $horarios = array();
                    }

                    for ($i = 0; $i < count($listabloques); $i++) {
                        $horarios[] = $listabloques[$i]['idHorario'];

                    }
                    if (!empty($horarios)) {
                        $results = $modelocurso->getbloquesin($user, $id_curso, $idperiodo, $dayofweek, $idasignatura, $horarios);
                    } else {
                        $results = $modelocurso->getbloque($user, $id_curso, $idperiodo, $dayofweek, $idasignatura);
                    }

                }


                //Validamos si existe creado el calendario
                if (!empty($results)) {
                    $this->_helper->json($results);

                } else {
                    $this->_helper->json(array());
                }

            }


        }
    }


    public function getcategoriaAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $modeloasistencia = new Application_Model_DbTable_Asistencia();

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $idcategoria = $this->_getParam('id', 0);

        if ($idcategoria > 0 && $rol == 2) {
            $results = $modeloasistencia->listarcategoriaasistencia($idcategoria);
            if (!empty($results)) {
                $this->_helper->json($results);

            }

        }
    }


    public function guardarcontrolasistenciaAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {

            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();

            //guardamos los datos en $json recibidos de la funcion ajax
            $json = file_get_contents('php://input');
            //decodificamos los datos en un array($data) php
            $data = json_decode($json, true);
            $opc = true;

            $date = new DateTime();
            $dateTimestamp1 = strtotime($date->format('Y-m-d'));
            $dateTimestamp2 = strtotime($data['0']['fecha']);
            if ($dateTimestamp1 < $dateTimestamp2) {
                $opc = false;
            }


            $modeloasistencia = new Application_Model_DbTable_Asistencia();
            $modelocontenido = new Application_Model_DbTable_Contenido();
            $modeloauditoria = new Application_Model_DbTable_Auditoria();

            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;

            $idcurso = new Zend_Session_Namespace('id_curso');
            $id_curso = $idcurso->id_curso;

            $usuario = new Zend_Session_Namespace('id');
            $user = $usuario->id;

            $cargo = new Zend_Session_Namespace('cargo');
            $rol = $cargo->cargo;

            if ($rol == 2 && $opc) {

                if (empty($user)) {
                    echo Zend_Json::encode(array('response' => 'errorsesion'));
                    die();
                } else {


                    $fecha = date("Y-m-d", strtotime($data['0']['fecha']));
                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                    $db->beginTransaction();
                    try {

                        //Datos Auditoria
                        $token = $data[0]['token'];
                        $now = new DateTime('now', new DateTimeZone('America/Santiago'));
                        $nowtime = $now->getTimezone();
                        $nombrezonahoraria = $nowtime->getName();
                        $hora_zona_horaria = (new \DateTime('America/Santiago'))->format('P');


                        if (empty($token) && empty($nombrezonahoraria) && empty($user) && strlen($token) != 6) {
                            echo Zend_Json::encode(array('response' => 'errorinserta'));
                            die();
                        }


                        //Valor Asistencia 1=Ausente 2= Presente

                        $validacontenido = $modelocontenido->validacontenido($id_curso, $fecha, $idperiodo);
                        if ($validacontenido) {
                            //si ya existe un registro para el curso y la fecha, obtenemos el idControlContenido
                            $idcontenido = $validacontenido[0]['idControlContenido'];

                        } else {
                            //Si no existe lo creamos y obtenemos el idControlContenido
                            $modelocontenido->agregar($id_curso, $fecha, $idperiodo);
                            $idcontenido = $modelocontenido->getAdapter()->lastInsertId();
                        }

                        $listahorario = explode(",", $data[0]['horario']);

                        for ($i = 0; $i < count($listahorario); $i++) {


                            $modeloasistencia->agregarcontrol($data[0]['asignatura'], $user, $listahorario[$i], $data[0]['tipo'], $idcontenido);
                            $idcontrolasistencia = $modeloasistencia->getAdapter()->lastInsertId();


                            //Agregamos la Auditoria de Cada Bloque
                            $modeloauditoria->agregar($user, $nombrezonahoraria, $hora_zona_horaria, $token);
                            $idauditoria = $modeloauditoria->getAdapter()->lastInsertId();

                            //agragamos lo datos de la asistencia por alumno
                            for ($j = 0; $j < count($data[0]['valores']); $j++) {
                                $modeloasistencia->agregarcontrolvalores($data[0]['valores'][$j]['alumno'], $data[0]['valores'][$j]['valorasistencia'], $data[0]['valores'][$j]['categoria'], $idcontrolasistencia);
                                $modeloauditoria->agregardetalle("asistencia", "idCursos", "INSERT", "", $id_curso, $idauditoria);
                                $modeloauditoria->agregardetalle("asistencia", "fechaControl", "INSERT", "", $fecha, $idauditoria);
                                $modeloauditoria->agregardetalle("asistencia", "idAsignatura", "INSERT", "", $data[0]['asignatura'], $idauditoria);
                                $modeloauditoria->agregardetalle("asistencia", "idHorario", "INSERT", "", $listahorario[$i], $idauditoria);
                                $modeloauditoria->agregardetalle("asistencia", "idAlumnos", "INSERT", "", $data[0]['valores'][$j]['alumno'], $idauditoria);
                                $modeloauditoria->agregardetalle("asistencia", "valorasistencia", "INSERT", "", $data[0]['valores'][$j]['valorasistencia'], $idauditoria);
                                $modeloauditoria->agregardetalle("asistencia", "tipoAsistencia", "INSERT", "", $data[0]['valores'][$j]['categoria'], $idauditoria);
                            }


                        }

                        $db->commit();
                        echo Zend_Json::encode(array('redirect' => '/Libro/controlasistencias'));

                    } catch (Exception $e) {
                        // Si hubo problemas. Enviamos marcha atras
                        $db->rollBack();
                        echo Zend_Json::encode(array('response' => 'errorinserta'));
                    }

                }

            } else {
                echo Zend_Json::encode(array('response' => 'errorinserta'));
            }


        }
    }


    public function registrarcontenidoAction()
    {

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcurso = new Zend_Session_Namespace('id_curso');
        $id_curso = $idcurso->id_curso;

        $nombrecurso = new Zend_Session_Namespace('nombre_curso');
        $nombre_curso = $nombrecurso->nombre_curso;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        if ($rol == 2) {

            $activePage = $this->view->navigation()->findOneByLabel('Abierto');
            $activePage->setlabel($nombre_curso);
            $activePage->setparam('id', $id_curso);

            $usuario = new Zend_Session_Namespace('id');
            $user = $usuario->id;

            $idcurso = new Zend_Session_Namespace('id_curso');
            $id_curso = $idcurso->id_curso;


            $modelocontenido = new Application_Model_DbTable_Contenido();
            $modeloauditoria = new Application_Model_DbTable_Auditoria();


            $form = new Application_Form_RegistroContenido();
            $this->view->form = $form;


            if ($this->getRequest()->isPost()) {

                $formData = $this->getRequest()->getPost();


                if ($form->isValid($formData)) {


                    $idasignatura = $form->getValue('idAsignatura');
                    $contenidos = $form->getValue('contenidos');
                    $fecha = $form->getValue('fechaContenido');
                    $bloque = $form->getValue('bloque');
                    $fecha = date("Y-m-d", strtotime($fecha));


                    //Datos Auditoria
                    $token = $form->getValue('token');
                    $now = new DateTime('now', new DateTimeZone('America/Santiago'));
                    $nowtime = $now->getTimezone();
                    $nombrezonahoraria = $nowtime->getName();
                    $hora_zona_horaria = (new \DateTime('America/Santiago'))->format('P');

                    if (!empty($token) && !empty($nombrezonahoraria) && !empty($user) && strlen($token) == 6) {


                        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                        // Iniciamos la transaccion
                        $db->beginTransaction();
                        try {


                            $validacontenido = $modelocontenido->validacontenido($id_curso, $fecha, $idperiodo);
                            if ($validacontenido) {
                                //si ya existe un registro para el curso y la fecha, obtenemos el idControlContenido
                                $idcontenido = $validacontenido[0]['idControlContenido'];

                            } else {
                                //Si no existe lo creamos y obtenemos el idControlContenido
                                $modelocontenido->agregar($id_curso, $fecha, $idperiodo);
                                $idcontenido = $modelocontenido->getAdapter()->lastInsertId();
                            }

                            //Agreamos el detalle del del contenido

                            for ($i = 0; $i < count($bloque); $i++) {

                                //Validamos que el bloque no este agregado
                                $validabloque = $modelocontenido->validacontenidobloque($idcontenido, $idasignatura, $user, $bloque[$i]);
                                if (!$validabloque) {
                                    $modelocontenido->agregardetalle($bloque[$i], $idasignatura, $contenidos, $user, $idcontenido);

                                    //Agregamos la Auditoria de Cada Bloque
                                    $modeloauditoria->agregar($user, $nombrezonahoraria, $hora_zona_horaria, $token);
                                    $idauditoria = $modeloauditoria->getAdapter()->lastInsertId();
                                    $modeloauditoria->agregardetalle("controlcontenidosdetalle", "idAsignatura", "INSERT", "", $idasignatura, $idauditoria);
                                    $modeloauditoria->agregardetalle("controlcontenidosdetalle", "contenidos", "INSERT", "", $contenidos, $idauditoria);
                                    $modeloauditoria->agregardetalle("controlcontenidosdetalle", "fechaContenido", "INSERT", "", $fecha, $idauditoria);
                                    $modeloauditoria->agregardetalle("controlcontenidosdetalle", "bloque", "INSERT", "", $bloque[$i], $idauditoria);
                                }


                            }


                            $db->commit();
                            $this->_helper->redirector('controlregistros');


                        } catch (Exception $e) {
                            // Si hubo problemas. Enviamos todos marcha atras
                            $db->rollBack();
                            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage(self::MENSAJE);
                            $this->view->assign('messages', $messages);
                        }
                    } else {

                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage(self::MENSAJE);
                        $this->view->assign('messages', $messages);

                    }

                } else {


                    $form->populate($formData);

                }

            }


        }


    }


    public function editarcontenidoAction()
    {

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        if ($rol == 2) {

            $usuario = new Zend_Session_Namespace('id');
            $user = $usuario->id;

            $idcurso = new Zend_Session_Namespace('id_curso');
            $id_curso = $idcurso->id_curso;


            $modelocontenido = new Application_Model_DbTable_Contenido();
            $modeloauditoria = new Application_Model_DbTable_Auditoria();


            $form = new Application_Form_RegistroContenido();
            $form->removeElement('bloque');
            $form->removeElement('idAsignatura');
            $form->enviar->setLabel('Actualizar');
            $this->view->form = $form;


            if ($this->getRequest()->isPost()) {

                $formData = $this->getRequest()->getPost();


                if ($form->isValid($formData)) {

                    $idcontenido = $form->getValue('idControlContenidoDetalle');
                    $contenidos = $form->getValue('contenidos');

                    //Datos Auditoria
                    $token = $form->getValue('token');
                    $now = new DateTime('now', new DateTimeZone('America/Santiago'));
                    $nowtime = $now->getTimezone();
                    $nombrezonahoraria = $nowtime->getName();


                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                    $db->beginTransaction();
                    try {

                        $valida = $modelocontenido->getcontenido($idcontenido, $user, $idperiodo);

                        if ($valida) {
                            $modelocontenido->actualizarcontenido($idcontenido, $contenidos);
                            $modeloauditoria->agregar($user, $nombrezonahoraria, $token);
                            $idauditoria = $modeloauditoria->getAdapter()->lastInsertId();
                            $modeloauditoria->agregardetalle("controlcontenidosdetalle", "contenidos", "UPDATE", $valida[0]['contenidos'], $contenidos, $idauditoria);

                        }


                        $db->commit();
                        $this->_helper->redirector('controlregistros');


                    } catch (Exception $e) {
                        // Si hubo problemas. Enviamos todos marcha atras
                        $db->rollBack();
                        echo $e;
                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage(self::MENSAJE);
                        $this->view->assign('messages', $messages);
                    }

                } else {
                    $form->populate($formData);
                }

            } else {
                $idcontenido = $this->_getParam('id', 0);
                if ($idcontenido > 0) {

                    $datoscontenido = $modelocontenido->getcontenido($idcontenido, $user, $idperiodo);
                    $form->removeElement('bloque');
                    $form->removeElement('idAsignatura');
                    $datoscontenido[0]['fechaControl'] = date("d-m-Y", strtotime($datoscontenido[0]['fechaControl']));

                    $form->fechaContenido->setValue($datoscontenido[0]['fechaControl']);
                    $this->view->datosform = $datoscontenido;
                    $form->populate($datoscontenido[0]);

                }
            }


        }


    }


    public function guardacontrolasistenciaeditarAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $cargo = new Zend_Session_Namespace('cargo');
            $rol = $cargo->cargo;

            if ($rol == 2) {

                $this->_helper->viewRenderer->setNoRender();
                $this->_helper->layout->disableLayout();
                $json = file_get_contents('php://input');

                $data = json_decode($json, true);
                $idcontrol = $data['id'];


                $modeloasistencia = new Application_Model_DbTable_Asistencia();
                $modeloauditoria = new Application_Model_DbTable_Auditoria();
                $listaalumnos = $modeloasistencia->listarasistenciavaloress($idcontrol);

                $usuario = new Zend_Session_Namespace('id');
                $user = $usuario->id;

                $idcurso = new Zend_Session_Namespace('id_curso');
                $id_curso = $idcurso->id_curso;

                if (empty($user)) {
                    echo Zend_Json::encode(array('response' => 'errorsesion'));
                    die();
                } else {
                    if (count($listaalumnos) == count($data['asistencia'])) {


                        //Datos Auditoria
                        $token = $data['token'];
                        $now = new DateTime('now', new DateTimeZone('America/Santiago'));
                        $nowtime = $now->getTimezone();
                        $nombrezonahoraria = $nowtime->getName();
                        $hora_zona_horaria = (new \DateTime('America/Santiago'))->format('P');


                        if (empty($token) && empty($nombrezonahoraria) && empty($user) && strlen($token) != 6) {
                            echo Zend_Json::encode(array('response' => 'errorinserta'));
                            die();
                        }
                        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                        $db->beginTransaction();
                        try {


                            $modeloasistencia->editarcontrol($idcontrol, $data['tipo']);
                            //Agregamos la Auditoria de cada Cambio
                            $modeloauditoria->agregar($user, $nombrezonahoraria, $hora_zona_horaria, $token);
                            $idauditoria = $modeloauditoria->getAdapter()->lastInsertId();

                            for ($i = 0; $i < count($data['asistencia']); $i++) {
                                //Validamos que exista un cambi
                                $validar = $modeloasistencia->validarcambioasistencia($data['asistencia'][$i]['idca'], $data['asistencia'][$i]['valor'], $data['cat'][$i]);
                                if ($validar) {
                                    $modeloasistencia->cambiarcontrolasistencia($data['asistencia'][$i]['idca'], $data['asistencia'][$i]['valor'], $data['cat'][$i]);


                                    $modeloauditoria->agregardetalle("asistencia", "idCursos", "UPDATE", $id_curso, $id_curso, $idauditoria);
                                    $modeloauditoria->agregardetalle("asistencia", "fechaControl", "UPDATE", $listaalumnos[0]['fechaControl'], $listaalumnos[0]['fechaControl'], $idauditoria);
                                    $modeloauditoria->agregardetalle("asistencia", "idAsignatura", "UPDATE", $listaalumnos[0]['idAsignatura'], $listaalumnos[0]['idAsignatura'], $idauditoria);
                                    $modeloauditoria->agregardetalle("asistencia", "idHorario", "UPDATE", $listaalumnos[0]['idHorario'], $listaalumnos[0]['idHorario'], $idauditoria);
                                    $modeloauditoria->agregardetalle("asistencia", "idAlumnos", "UPDATE", $listaalumnos[$i]['idAlumnos'], $listaalumnos[$i]['idAlumnos'], $idauditoria);
                                    $modeloauditoria->agregardetalle("asistencia", "valorasistencia", "UPDATE", $listaalumnos[$i]['valorasistencia'], $data['asistencia'][$i]['valor'], $idauditoria);
                                    $modeloauditoria->agregardetalle("asistencia", "tipoAsistencia", "UPDATE", $listaalumnos[$i]['tipoAsistencia'], $data['cat'][$i], $idauditoria);
                                }


                            }
                            $db->commit();
                            echo Zend_Json::encode(array('redirect' => '/Libro/controlasistencias'));
                        } catch (Exception $e) {
                            $db->rollBack();
                            echo Zend_Json::encode(array('response' => 'errorinserta'));
                            die();
                        }
                    } else {
                        echo Zend_Json::encode(array('response' => 'errorinserta'));
                        die();
                    }


                }
            }

        }
    }

    public function vercontrolasistenciaAction()
    {
        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcurso = new Zend_Session_Namespace('id_curso');
        $id_curso = $idcurso->id_curso;

        if ($rol == 1 || $rol == 3 || $rol == 6) {

            $id = $this->_getParam('id');

            if ($id > 0) {

                $modeloasistencia = new Application_Model_DbTable_Asistencia();
                $modeloalumno = new Application_Model_DbTable_Alumnosactual();

                $listaalumnos = $modeloalumno->listaralumnoscursoactual($id_curso, $idperiodo);
                $selectausente = $modeloasistencia->listarcategoriaasistencia(1);
                $selectpresente = $modeloasistencia->listarcategoriaasistencia(2);


                //creamos el Select Ausente
                $elementoselecta = "<select class='observacion'>";
                for ($i = 0; $i < count($selectausente); $i++) {

                    $elementoselecta .= '<option value="' . $selectausente[$i]['codigo'] . '">' . $selectausente[$i]['descripcion'] . '</option>';

                }
                $elementoselecta .= "</select>";

                $largoalumnos = count($listaalumnos);

                for ($i = 0; $i < $largoalumnos; $i++) {

                    $datosalumnos = $modeloasistencia->listarasistenciavalores($id, $listaalumnos[$i]['idAlumnos']);

                    if (!empty($datosalumnos)) {
                        $datosalumnos[0]['rutAlumno'] = $listaalumnos[$i]['rutAlumno'];
                        $datosalumnos[0]['nombres'] = $listaalumnos[$i]['nombres'];
                        $datosalumnos[0]['amaterno'] = $listaalumnos[$i]['amaterno'];
                        $datosalumnos[0]['apaterno'] = $listaalumnos[$i]['apaterno'];

                        $date = DateTime::createFromFormat('H:i:s', $datosalumnos[0]['tiempoInicio']);
                        $datosalumnos[0]['tiempoInicio'] = $date->format('H:i');

                        $date = DateTime::createFromFormat('H:i:s', $datosalumnos[0]['tiempoTermino']);
                        $datosalumnos[0]['tiempoTermino'] = $date->format('H:i');


                        if ($datosalumnos[0]['valorasistencia'] == 2) { //Presente

                            $elementoselect = "";
                            for ($j = 0; $j < count($selectpresente); $j++) {
                                if ($datosalumnos[0]['tipoAsistencia'] == $selectpresente[$j]['codigo']) {
                                    $elementoselect .= '<p style="color: #4D99E0">' . $selectpresente[$j]['descripcion'] . '</p>';
                                }


                            }
                            $elementoselect .= "";
                            $datosalumnos[0]['select'] = $elementoselect;


                        } elseif ($datosalumnos[0]['valorasistencia'] == 1) {
                            $elementoselecta = "";
                            for ($j = 0; $j < count($selectausente); $j++) {
                                if ($datosalumnos[0]['tipoAsistencia'] == $selectausente[$j]['codigo']) {
                                    $elementoselecta .= '<p style="color: #E74C3C">' . $selectausente[$j]['descripcion'] . '</p>';
                                }


                            }
                            $elementoselecta .= "";
                            $datosalumnos[0]['select'] = $elementoselecta;
                        }

                        $datos[$i] = $datosalumnos;
                    }


                }


                $datos[0][0]['fechaControl'] = date("d-m-Y", strtotime($datos[0][0]['fechaControl']));
                $this->view->datos = $datos;

            }


        }

    }

    public
    function getpromedioperiodotrimAction($opc, $tip, $idal, $idas)
    {
        $this->_helper->viewRenderer->setNeverRender(true);
        $this->_helper->ViewRenderer->setNoRender(true);
        $this->_helper->Layout->disableLayout();

        $idcursos = new Zend_Session_Namespace('id_curso');
        $id = $idcursos->id_curso;
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $modelcurso = new Application_Model_DbTable_Cursos();
        $modelnotas = new Application_Model_DbTable_Notas();
        $modelasignatura = new Application_Model_DbTable_Asignaturascursos();
        $modeloprueba = new Application_Model_DbTable_Pruebas();


        if ($opc) {

            $agregadecimas = false;
            if ($tip == 0) {
                $asignaturas = new Zend_Session_Namespace('idAsignatura');
                $idasignatura = $asignaturas->idAsignatura;
                $listaalumnos = $modelnotas->listaralumnoscurso($id, $idperiodo);

            } elseif ($tip == 2) {
                $idasignatura = $idas;
                $idalumno = $idal;
                $listaalumnos = $modelnotas->listaralumno($idalumno, $id, $idperiodo);
            }

        } else {
            $tipo = $this->_getParam('t', 0);
            $agregadecimas = false;
            if ($tipo == 0 || $tipo == null) {
                $asignaturas = new Zend_Session_Namespace('idAsignatura');
                $idasignatura = $asignaturas->idAsignatura;
                $listaalumnos = $modelnotas->listaralumnoscurso($id, $idperiodo);

            } elseif ($tipo == 2) {
                $idasignatura = $this->_getParam('as', 0);
                $idalumno = $this->_getParam('id', 0);
                $listaalumnos = $modelnotas->listaralumno($idalumno, $id, $idperiodo);
            }
        }


        $datoscurso = $modelcurso->listarcursoid($id, $idperiodo);

        $examenes = new Zend_Session_Namespace('examen');
        $examenes->examen = $datoscurso[0]['examen'];

        //Configuracion de agregar 2 decimas a curso desde 5 a 4to medio cuando el promedio final de asignatura es mayor o igual a 6
        if (($datoscurso[0]['rbd'] == 1863) && (($datoscurso[0]['idCodigo'] == 110 && $datoscurso[0]['idGrado'] > 4) || $datoscurso[0]['idCodigo'] == 310 || $datoscurso[0]['idCodigo'] == 363 || $datoscurso[0]['idCodigo'] == 410 || $datoscurso[0]['idCodigo'] == 510 || $datoscurso[0]['idCodigo'] == 610 || $datoscurso[0]['idCodigo'] == 165)) {
            $agregadecimas = true;
        }


        $listanotas = $modelnotas->listarnotascursoperiodo2($id, $idperiodo, 1);

        $datosasignaturas = $modelasignatura->listarporasignatura($idasignatura, $idperiodo);
        if ($datosasignaturas[0]['tipoAsignatura'] == 4) {
            $agregadecimas = false;

        }

        //Datos Asignatura Combinadas
        $datosasignaturascombinada = $modelasignatura->getcombinada($id, $idperiodo);
        $largoalumnos = count($listaalumnos);
        $largocombinada = count($datosasignaturascombinada);
        $m = 0;


        //Talleres para Alumnos Separados Primer Semestre
        //COnsultar solo los talleres por asignatura


        $datostallerprimer = $modelasignatura->gettaller2($idasignatura, $idperiodo, array(1, 2), 1, array(3));
        $datostallersegundo = $modelasignatura->gettaller2($idasignatura, $idperiodo, array(1, 2), 1, array(4));
        $datostallertercero = $modelasignatura->gettaller2($idasignatura, $idperiodo, array(1, 2), 1, array(5));
        $datostalleranual = $modelasignatura->gettalleranual2($idasignatura, $idperiodo, array(1), 1);
        $datostaller = array_merge($datostallerprimer, $datostallersegundo, $datostallertercero, $datostalleranual);
        $largotaller = count($datostaller);


        //Configuracion de Taller Buscamos si existen configuraciones por alumnos

        $modelotallerdetalle = new Application_Model_DbTable_Asignaturascursos();
        //Obtiene los Talleres que son Trimestrales Tiempo=2.
        $datostalleres = $modelasignatura->gettaller2($idasignatura, $idperiodo, array(2), 2, array(3, 4, 5));


        $promediotalleralumnos = array();
        $promediotalleralumnoss = array();
        $promediotalleralumnost = array();
        if ($datostalleres) {
            for ($j = 0; $j < $largoalumnos; $j++) {
                for ($i = 0; $i < count($datostalleres); $i++) {
                    $detalletaller = $modelotallerdetalle->gettallersegmento($datostalleres[$i]['idConfiguracionTaller'], 3, $listaalumnos[$j]['idAlumnos']);
                    $detalletallers = $modelotallerdetalle->gettallersegmento($datostalleres[$i]['idConfiguracionTaller'], 4, $listaalumnos[$j]['idAlumnos']);
                    $detalletallert = $modelotallerdetalle->gettallersegmento($datostalleres[$i]['idConfiguracionTaller'], 5, $listaalumnos[$j]['idAlumnos']);


                    //Primer Trimestre
                    if ($datostalleres[$i]['tiempoOpcion'] == 3) {
                        if ($detalletaller) {
                            $datosalumnostaller = $modelnotas->listarpromedioalumnotaller($listaalumnos[$j]['idAlumnos'], $idperiodo, 3, $id, $detalletaller[0]['idAsignatura']);

                            if ($datosalumnostaller) {
                                for ($k = 0; $k < count($datosalumnostaller); $k++) {

                                    if ($datostalleres[$i]['idAsignatura'] == $datosalumnostaller[$k]['idAsignatura']) {
                                        if ($datosalumnostaller[$k]['coef'] == 2) {
                                            if ($datosalumnostaller[$k]['nota'] > 0) {
                                                $promediotalleralumnos[$j][$i][] = $datosalumnostaller[$k]['nota'];
                                                $promediotalleralumnos[$j][$i][] = $datosalumnostaller[$k]['nota'];

                                            } else {
                                                $promediotalleralumnos[$j][$i][] = 0;
                                                $promediotalleralumnos[$j][$i][] = 0;
                                            }

                                        } else {
                                            if ($datosalumnostaller[$k]['nota'] > 0) {
                                                $promediotalleralumnos[$j][$i][] = $datosalumnostaller[$k]['nota'];
                                            } else {
                                                $promediotalleralumnos[$j][$i][] = 0;
                                            }

                                        }


                                    }


                                }
                            }
                            if (!empty($promediotalleralumnos[$j][$i])) {
                                $promsetalumnos = array_diff($promediotalleralumnos[$j][$i], array('0'));
                            } else {
                                $promsetalumnos = 0;
                            }

                            if (is_array($promsetalumnos)) {
                                if (count($promsetalumnos) > 0) {

                                    if ($datoscurso[0]['aproxAsignatura'] == 1) {
                                        $promtalleralumnos[$j][$i]['nota'] = round(array_sum($promsetalumnos) / count($promsetalumnos));

                                    } else {
                                        $promtalleralumnos[$j][$i]['nota'] = intval(array_sum($promsetalumnos) / count($promsetalumnos));
                                    }
                                } else {
                                    $promtalleralumnos[$j][$i]['nota'] = 0;
                                }
                            } else {
                                $promtalleralumnos[$j][$i]['nota'] = 0;
                            }


                            $promtalleralumnos[$j][$i]['idAlumnos'] = $listaalumnos[$j]['idAlumnos'];
                            $promtalleralumnos[$j][$i]['idAsignatura'] = $datostalleres[$i]['idAsignaturaTaller'];
                            $promtalleralumnos[$j][$i]['coef'] = 1;
                            $promtalleralumnos[$j][$i]['tiempo'] = 1;
                            $promtalleralumnos[$j][$i]['forma'] = $datostalleres[$i]['forma'];
                            $promtalleralumnos[$j][$i]['porcentaje'] = $datostalleres[$i]['porcentaje'];

                        } else {
                            $promtalleralumnoss[$j][$i]['nota'] = 0;
                            $promtalleralumnos[$j][$i]['idAlumnos'] = $listaalumnos[$j]['idAlumnos'];
                            $promtalleralumnos[$j][$i]['idAsignatura'] = $datostalleres[$i]['idAsignaturaTaller'];
                            $promtalleralumnos[$j][$i]['coef'] = 1;
                            $promtalleralumnos[$j][$i]['tiempo'] = 1;
                            $promtalleralumnos[$j][$i]['forma'] = $datostalleres[$i]['forma'];
                            $promtalleralumnos[$j][$i]['porcentaje'] = $datostalleres[$i]['porcentaje'];
                        }
                    } else {
                        $promtalleralumnos[$j][$i] = array();
                    }

                    //Segundo Trimestre
                    if ($datostalleres[$i]['tiempoOpcion'] == 4) {
                        if ($detalletallers) {
                            $datosalumnostallers = $modelnotas->listarpromedioalumnotaller($listaalumnos[$j]['idAlumnos'], $idperiodo, 4, $id, $detalletallers[0]['idAsignatura']);

                            if ($datosalumnostallers) {
                                for ($k = 0; $k < count($datosalumnostallers); $k++) {

                                    if ($datostalleres[$i]['idAsignatura'] == $datosalumnostallers[$k]['idAsignatura']) {

                                        if ($datosalumnostallers[$k]['coef'] == 2) {
                                            if ($datosalumnostallers[$k]['nota'] > 0) {
                                                $promediotalleralumnoss[$j][$i][] = $datosalumnostallers[$k]['nota'];
                                                $promediotalleralumnoss[$j][$i][] = $datosalumnostallers[$k]['nota'];

                                            } else {
                                                $promediotalleralumnoss[$j][$i][] = 0;
                                                $promediotalleralumnoss[$j][$i][] = 0;
                                            }

                                        } else {
                                            if ($datosalumnostallers[$k]['nota'] > 0) {
                                                $promediotalleralumnoss[$j][$i][] = $datosalumnostallers[$k]['nota'];
                                            } else {
                                                $promediotalleralumnoss[$j][$i][] = 0;
                                            }

                                        }


                                    }


                                }
                            }
                            if (!empty($promediotalleralumnoss[$j][$i])) {
                                $promsetalumnoss = array_diff($promediotalleralumnoss[$j][$i], array('0'));
                            } else {
                                $promsetalumnoss = 0;
                            }

                            if (is_array($promsetalumnoss)) {
                                if (count($promsetalumnoss) > 0) {

                                    if ($datoscurso[0]['aproxAsignatura'] == 1) {
                                        $promtalleralumnoss[$j][$i]['nota'] = round(array_sum($promsetalumnoss) / count($promsetalumnoss));
                                        $promtalleralumnoss[$j][$i]['nota'] = intval($promtalleralumnoss[$j][$i]['nota']);

                                    } else {
                                        $promtalleralumnoss[$j][$i]['nota'] = intval(array_sum($promsetalumnoss) / count($promsetalumnoss));
                                    }
                                } else {
                                    $promtalleralumnoss[$j][$i]['nota'] = 0;
                                }
                            } else {
                                $promtalleralumnoss[$j][$i]['nota'] = 0;
                            }


                            $promtalleralumnoss[$j][$i]['idAlumnos'] = $listaalumnos[$j]['idAlumnos'];
                            $promtalleralumnoss[$j][$i]['idAsignatura'] = $datostalleres[$i]['idAsignaturaTaller'];
                            $promtalleralumnoss[$j][$i]['coef'] = 1;
                            $promtalleralumnoss[$j][$i]['tiempo'] = 2;
                            $promtalleralumnoss[$j][$i]['forma'] = $datostalleres[$i]['forma'];
                            $promtalleralumnoss[$j][$i]['porcentaje'] = $datostalleres[$i]['porcentaje'];

                        } else {
                            $promtalleralumnoss[$j][$i]['nota'] = 0;
                            $promtalleralumnoss[$j][$i]['idAlumnos'] = $listaalumnos[$j]['idAlumnos'];
                            $promtalleralumnoss[$j][$i]['idAsignatura'] = $datostalleres[$i]['idAsignaturaTaller'];
                            $promtalleralumnoss[$j][$i]['coef'] = 1;
                            $promtalleralumnoss[$j][$i]['tiempo'] = 2;
                            $promtalleralumnoss[$j][$i]['forma'] = $datostalleres[$i]['forma'];
                            $promtalleralumnoss[$j][$i]['porcentaje'] = $datostalleres[$i]['porcentaje'];
                        }
                    } else {
                        $promtalleralumnoss[$j][$i] = array();
                    }

                    //Tercer Trimestre
                    if ($datostalleres[$i]['tiempoOpcion'] == 5) {
                        if ($detalletallert) {
                            $datosalumnostallert = $modelnotas->listarpromedioalumnotaller($listaalumnos[$j]['idAlumnos'], $idperiodo, 5, $id, $detalletallert[0]['idAsignatura']);

                            if ($datosalumnostallert) {
                                for ($k = 0; $k < count($datosalumnostallert); $k++) {

                                    if ($datostalleres[$i]['idAsignatura'] == $datosalumnostallert[$k]['idAsignatura']) {

                                        if ($datosalumnostallert[$k]['coef'] == 2) {
                                            if ($datosalumnostallert[$k]['nota'] > 0) {
                                                $promediotalleralumnost[$j][$i][] = $datosalumnostallert[$k]['nota'];
                                                $promediotalleralumnost[$j][$i][] = $datosalumnostallert[$k]['nota'];

                                            } else {
                                                $promediotalleralumnost[$j][$i][] = 0;
                                                $promediotalleralumnost[$j][$i][] = 0;
                                            }

                                        } else {
                                            if ($datosalumnostallert[$k]['nota'] > 0) {
                                                $promediotalleralumnost[$j][$i][] = $datosalumnostallert[$k]['nota'];
                                            } else {
                                                $promediotalleralumnost[$j][$i][] = 0;
                                            }

                                        }


                                    }


                                }
                            }
                            if (!empty($promediotalleralumnost[$j][$i])) {
                                $promsetalumnost = array_diff($promediotalleralumnost[$j][$i], array('0'));
                            } else {
                                $promsetalumnost = 0;
                            }

                            if (is_array($promsetalumnost)) {
                                if (count($promsetalumnost) > 0) {

                                    if ($datoscurso[0]['aproxAsignatura'] == 1) {
                                        $promtalleralumnost[$j][$i]['nota'] = round(array_sum($promsetalumnost) / count($promsetalumnost));
                                        $promtalleralumnost[$j][$i]['nota'] = intval($promtalleralumnost[$j][$i]['nota']);

                                    } else {
                                        $promtalleralumnost[$j][$i]['nota'] = intval(array_sum($promsetalumnost) / count($promsetalumnost));
                                    }
                                } else {
                                    $promtalleralumnost[$j][$i]['nota'] = 0;
                                }
                            } else {
                                $promtalleralumnost[$j][$i]['nota'] = 0;
                            }


                            $promtalleralumnost[$j][$i]['idAlumnos'] = $listaalumnos[$j]['idAlumnos'];
                            $promtalleralumnost[$j][$i]['idAsignatura'] = $datostalleres[$i]['idAsignaturaTaller'];
                            $promtalleralumnost[$j][$i]['coef'] = 1;
                            $promtalleralumnost[$j][$i]['tiempo'] = 2;
                            $promtalleralumnost[$j][$i]['forma'] = $datostalleres[$i]['forma'];
                            $promtalleralumnost[$j][$i]['porcentaje'] = $datostalleres[$i]['porcentaje'];

                        } else {
                            $promtalleralumnost[$j][$i]['nota'] = 0;
                            $promtalleralumnost[$j][$i]['idAlumnos'] = $listaalumnos[$j]['idAlumnos'];
                            $promtalleralumnost[$j][$i]['idAsignatura'] = $datostalleres[$i]['idAsignaturaTaller'];
                            $promtalleralumnost[$j][$i]['coef'] = 1;
                            $promtalleralumnost[$j][$i]['tiempo'] = 2;
                            $promtalleralumnost[$j][$i]['forma'] = $datostalleres[$i]['forma'];
                            $promtalleralumnost[$j][$i]['porcentaje'] = $datostalleres[$i]['porcentaje'];
                        }
                    } else {
                        $promtalleralumnost[$j][$i] = array();
                    }


                }//Fin if talleres

                $largopromedio = count($promtalleralumnos[$j]);
                $largopromedios = count($promtalleralumnoss[$j]);
                $largopromediot = count($promtalleralumnost[$j]);


                for ($l = 0; $l < $largopromedio; $l++) {
                    if (!empty($promtalleralumnos[$j][$l]["idAsignatura"])) {
                        $datosdestino = $modelotallerdetalle->getdestino($promtalleralumnos[$j][$l]["idAsignatura"]);
                        if ($datosdestino[0]['tipoAsignatura'] != 4) {
                            $listapromediostaller[$j][] = $promtalleralumnos[$j][$l];
                        } else {
                            $listapromediostallercom[$j][$promtalleralumnos[$j][$l]['idAsignatura']] = $promtalleralumnos[$j][$l];

                        }
                    }

                }


                for ($l = 0; $l < $largopromedios; $l++) {
                    if (!empty($promtalleralumnoss[$j][$l]["idAsignatura"])) {
                        $datosdestino = $modelotallerdetalle->getdestino($promtalleralumnoss[$j][$l]["idAsignatura"]);
                        if ($datosdestino[0]['tipoAsignatura'] != 4) {
                            $listapromediostallers[$j][] = $promtalleralumnoss[$j][$l];
                        } else {
                            $listapromediostallercoms[$j][$promtalleralumnoss[$j][$l]['idAsignatura']] = $promtalleralumnoss[$j][$l];

                        }
                    }

                }

                for ($l = 0; $l < $largopromediot; $l++) {
                    if (!empty($promtalleralumnost[$j][$l]["idAsignatura"])) {
                        $datosdestino = $modelotallerdetalle->getdestino($promtalleralumnost[$j][$l]["idAsignatura"]);
                        if ($datosdestino[0]['tipoAsignatura'] != 4) {
                            $listapromediostallert[$j][] = $promtalleralumnost[$j][$l];
                        } else {
                            $listapromediostallercomt[$j][$promtalleralumnost[$j][$l]['idAsignatura']] = $promtalleralumnost[$j][$l];

                        }
                    }

                }


            }//Fin for Alumnos


        }


        //Fin Talleres

        //Asignaturas Combinadas


        $datosasignaturascombinada = $modelasignatura->getcombinada($id, $idperiodo, $idasignatura);

        $largoalumnos = count($listaalumnos);
        $largotaller = count($datostaller);
        $largocombinada = count($datosasignaturascombinada);
        $m = 0;


        if ($largocombinada > 0) {
            for ($i = 0; $i < $largocombinada; $i++) {
                $listaasignatura[$i] = unserialize($datosasignaturascombinada[$i]['asignaturas']);
                $m = 0;
                for ($h = 0; $h < count($listaasignatura[$i]); $h++) {
                    $datocombinada = $modelasignatura->getnombre($listaasignatura[$i][$h]);

                    $validacom[$i][$m] = $datosasignaturascombinada[$i]['idAsignatura'];
                    $m++;
                    $validacom[$i][$m] = $datosasignaturascombinada[$i]['idAsignatura'];
                    for ($j = 0; $j < $largoalumnos; $j++) {
                        $datosalumnoscom = $modelnotas->listarpromedioalumnotaller($listaalumnos[$j]['idAlumnos'], $idperiodo, 3, $id, $datocombinada[0]['idAsignatura']);
                        $datosalumnoscoms = $modelnotas->listarpromedioalumnotaller($listaalumnos[$j]['idAlumnos'], $idperiodo, 4, $id, $datocombinada[0]['idAsignatura']);
                        $datosalumnoscomt = $modelnotas->listarpromedioalumnotaller($listaalumnos[$j]['idAlumnos'], $idperiodo, 5, $id, $datocombinada[0]['idAsignatura']);
                        //Primer Trimestre
                        if ($listapromediostallercom) {
                            if ($datocombinada[0]['idAsignatura'] == $listapromediostallercom[$j][$datocombinada[0]['idAsignatura']]['idAsignatura']) {
                                $datosalumnoscom[] = $listapromediostallercom[$j][$datocombinada[0]['idAsignatura']];
                            }

                        }
                        //Segundo Trimestre
                        if ($listapromediostallercoms) {
                            if ($datocombinada[0]['idAsignatura'] == $listapromediostallercoms[$j][$datocombinada[0]['idAsignatura']]['idAsignatura']) {
                                $datosalumnoscoms[] = $listapromediostallercoms[$j][$datocombinada[0]['idAsignatura']];
                            }

                        }

                        //Tercer Trimestre
                        if ($listapromediostallercomt) {
                            if ($datocombinada[0]['idAsignatura'] == $listapromediostallercomt[$j][$datocombinada[0]['idAsignatura']]['idAsignatura']) {
                                $datosalumnoscomt[] = $listapromediostallercomt[$j][$datocombinada[0]['idAsignatura']];
                            }

                        }


                        for ($k = 0; $k < count($datosalumnoscom); $k++) {
                            if ($datocombinada[0]['idAsignatura'] == $datosalumnoscom[$k]['idAsignatura']) {
                                if ($datosalumnoscom[$k]['coef'] == 2) {
                                    if ($datosalumnoscom[$k]['nota'] > 0) {
                                        $promediocom[$j][$m][] = $datosalumnoscom[$k]['nota'];
                                        $promediocom[$j][$m][] = $datosalumnoscom[$k]['nota'];

                                    } else {
                                        $promediocom[$j][$m][] = 0;
                                        $promediocom[$j][$m][] = 0;
                                    }

                                } else {

                                    if ($datosalumnoscom[$k]['nota'] == 0 || empty($datosalumnoscom[$k]['nota']) || $datosalumnoscom[$k]['nota'] == NULL || $datosalumnoscom[$k]['nota'] == '') {
                                        if (!empty($datosalumnoscom[$k]["forma"])) {
                                            if ($datosalumnoscom[$k]['nota'] == 0) {
                                                $promediocom[$j][$m][] = 0;
                                            }

                                        } else {
                                            $promediocom[$j][$m][] = 0;
                                        }


                                    } else {
                                        if (!empty($datosalumnoscom[$k]["forma"]) && $datosalumnoscom[$k]["forma"] == 2) {
                                            $promtalleraux = $datosalumnoscom[$k]['nota'];
                                            $porcentajetaller = $datosalumnoscom[$k]["porcentaje"];


                                        } elseif (!empty($datosalumnoscom[$k]["forma"]) && $datosalumnoscom[$k]["forma"] == 1) {
                                            $promediocom[$j][$m][] = $datosalumnoscom[$k]['nota'];
                                            $promtalleraux = array();
                                            $porcentajetaller = array();
                                        } else {
                                            $promediocom[$j][$m][] = $datosalumnoscom[$k]['nota'];
                                            $promtalleraux = array();
                                            $porcentajetaller = array();
                                        }


                                    }

                                }//fin else


                            }

                        }


                        for ($k = 0; $k < count($datosalumnoscoms); $k++) {
                            if ($datocombinada[0]['idAsignatura'] == $datosalumnoscoms[$k]['idAsignatura']) {
                                if ($datosalumnoscoms[$k]['coef'] == 2) {
                                    if ($datosalumnoscoms[$k]['nota'] > 0) {
                                        $promediocoms[$j][$m][] = $datosalumnoscoms[$k]['nota'];
                                        $promediocoms[$j][$m][] = $datosalumnoscoms[$k]['nota'];

                                    } else {
                                        $promediocoms[$j][$m][] = 0;
                                        $promediocoms[$j][$m][] = 0;
                                    }

                                } else {

                                    if ($datosalumnoscoms[$k]['nota'] == 0 || empty($datosalumnoscoms[$k]['nota']) || $datosalumnoscoms[$k]['nota'] == NULL || $datosalumnoscoms[$k]['nota'] == '') {
                                        if (!empty($datosalumnoscoms[$k]["forma"])) {
                                            if ($datosalumnoscoms[$k]['nota'] == 0) {
                                                $promediocoms[$j][$m][] = 0;
                                            }

                                        } else {
                                            $promediocoms[$j][$m][] = 0;
                                        }


                                    } else {
                                        if (!empty($datosalumnoscoms[$k]["forma"]) && $datosalumnoscoms[$k]["forma"] == 2) {
                                            $promtallerauxs = $datosalumnoscoms[$k]['nota'];
                                            $porcentajetallers = $datosalumnoscoms[$k]["porcentaje"];


                                        } elseif (!empty($datosalumnoscoms[$k]["forma"]) && $datosalumnoscoms[$k]["forma"] == 1) {
                                            $promediocoms[$j][$m][] = $datosalumnoscoms[$k]['nota'];
                                            $promtallerauxs = array();
                                            $porcentajetallers = array();
                                        } else {
                                            $promediocoms[$j][$m][] = $datosalumnoscoms[$k]['nota'];
                                            $promtallerauxs = array();
                                            $porcentajetallers = array();
                                        }


                                    }

                                }//fin else


                            }

                        }

                        for ($k = 0; $k < count($datosalumnoscomt); $k++) {
                            if ($datocombinada[0]['idAsignatura'] == $datosalumnoscomt[$k]['idAsignatura']) {
                                if ($datosalumnoscoms[$k]['coef'] == 2) {
                                    if ($datosalumnoscomt[$k]['nota'] > 0) {
                                        $promediocomt[$j][$m][] = $datosalumnoscomt[$k]['nota'];
                                        $promediocomt[$j][$m][] = $datosalumnoscomt[$k]['nota'];

                                    } else {
                                        $promediocomt[$j][$m][] = 0;
                                        $promediocomt[$j][$m][] = 0;
                                    }

                                } else {

                                    if ($datosalumnoscomt[$k]['nota'] == 0 || empty($datosalumnoscomt[$k]['nota']) || $datosalumnoscomt[$k]['nota'] == NULL || $datosalumnoscomt[$k]['nota'] == '') {
                                        if (!empty($datosalumnoscomt[$k]["forma"])) {
                                            if ($datosalumnoscomt[$k]['nota'] == 0) {
                                                $promediocomt[$j][$m][] = 0;
                                            }

                                        } else {
                                            $promediocomt[$j][$m][] = 0;
                                        }


                                    } else {
                                        if (!empty($datosalumnoscomt[$k]["forma"]) && $datosalumnoscomt[$k]["forma"] == 2) {
                                            $promtallerauxt = $datosalumnoscomt[$k]['nota'];
                                            $porcentajetallert = $datosalumnoscomt[$k]["porcentaje"];


                                        } elseif (!empty($datosalumnoscomt[$k]["forma"]) && $datosalumnoscomt[$k]["forma"] == 1) {
                                            $promediocomt[$j][$m][] = $datosalumnoscomt[$k]['nota'];
                                            $promtallerauxt = array();
                                            $porcentajetallert = array();
                                        } else {
                                            $promediocomt[$j][$m][] = $datosalumnoscomt[$k]['nota'];
                                            $promtallerauxt = array();
                                            $porcentajetallert = array();
                                        }


                                    }

                                }//fin else


                            }

                        }

                        //Primer Trimestre Promedio
                        if (!empty($promediocom[$j][$m])) {
                            $promset = array_diff($promediocom[$j][$m], array('0'));
                        } else {
                            $promset = 0;
                        }

                        if (is_array($promset)) {
                            if (count($promset) > 0) {
                                if ($datoscurso[0]['aproxAsignatura'] == 1) {
                                    if ($porcentajetaller) {
                                        $totalporcentaje = 100;
                                        $porcentajeasig = $totalporcentaje - $porcentajetaller;
                                        $promedioauxalum = array_sum($promset) / count($promset);
                                        //$promcom[$j][$m]['nota'] = round(array_sum($promset) / count($promset));
                                        $promcom[$j][$m]['nota'] = round(($promedioauxalum * ($porcentajeasig / 100)) + ($promtalleraux * ($porcentajetaller / 100)));

                                    } else {
                                        $promcom[$j][$m]['nota'] = round(array_sum($promset) / count($promset));
                                    }

                                } else {
                                    if ($porcentajetaller) {
                                        $totalporcentaje = 100;
                                        $porcentajeasig = $totalporcentaje - $porcentajetaller;
                                        $promedioauxalum = array_sum($promset) / count($promset);
                                        //$promcom[$j][$m]['nota'] = round(array_sum($promset) / count($promset));
                                        $promcom[$j][$m]['nota'] = round(($promedioauxalum * ($porcentajeasig / 100)) + ($promtalleraux * ($porcentajetaller / 100)));
                                        // $promcom[$j][$m]['nota'] = intval(($promedioauxalum * ($porcentajeasig / 100)) + ($promtalleraux * ($porcentajetaller / 100)));

                                    } else {
                                        $promcom[$j][$m]['nota'] = intval(array_sum($promset) / count($promset));
                                    }
                                }
                            } else {
                                $promcom[$j][$m]['nota'] = 0;
                            }

                        } else {

                            $promcom[$j] = array();
                        }
                        $promcom[$j][$m]['idAsignatura'] = $datosasignaturascombinada[$i]['idAsignatura'];
                        $promcom[$j][$m]['coef'] = 1;
                        $promcom[$j][$m]['tiempo'] = 1;

                        //Segundo Trimestre
                        if (!empty($promediocoms[$j][$m])) {
                            $promsets = array_diff($promediocoms[$j][$m], array('0'));
                        } else {
                            $promsets = 0;
                        }


                        if (is_array($promsets)) {
                            if (count($promsets) > 0) {
                                if ($datoscurso[0]['aproxAsignatura'] == 1) {
                                    if ($porcentajetallers) {
                                        $totalporcentajes = 100;
                                        $porcentajeasigs = $totalporcentajes - $porcentajetallers;
                                        $promedioauxalums = array_sum($promsets) / count($promsets);
                                        $promcoms[$j][$m]['nota'] = round(($promedioauxalums * ($porcentajeasigs / 100)) + ($promtallerauxs * ($porcentajetallers / 100)));


                                    } else {
                                        $promcoms[$j][$m]['nota'] = round(array_sum($promsets) / count($promsets));

                                    }

                                } else {
                                    if ($porcentajetallers) {
                                        $totalporcentajes = 100;
                                        $porcentajeasigs = $totalporcentajes - $porcentajetallers;
                                        $promedioauxalums = array_sum($promsets) / count($promsets);
                                        $promcoms[$j][$m]['nota'] = round(($promedioauxalums * ($porcentajeasigs / 100)) + ($promtallerauxs * ($porcentajetallers / 100)));


                                    } else {
                                        $promcoms[$j][$m]['nota'] = intval(array_sum($promsets) / count($promsets));

                                    }
                                }
                            } else {
                                $promcoms[$j][$m]['nota'] = 0;
                            }

                        } else {


                            $promcoms[$j][$m]['nota'] = 0;
                        }
                        $promcoms[$j][$m]['idAsignatura'] = $datosasignaturascombinada[$i]['idAsignatura'];
                        $promcoms[$j][$m]['coef'] = 1;
                        $promcoms[$j][$m]['tiempo'] = 2;

                        //Tercer Trimestre
                        if (!empty($promediocomt[$j][$m])) {
                            $promsett = array_diff($promediocomt[$j][$m], array('0'));
                        } else {
                            $promsett = 0;
                        }


                        if (is_array($promsett)) {
                            if (count($promsett) > 0) {
                                if ($datoscurso[0]['aproxAsignatura'] == 1) {
                                    if ($porcentajetallert) {
                                        $totalporcentajet = 100;
                                        $porcentajeasigt = $totalporcentajet - $porcentajetallert;
                                        $promedioauxalumt = array_sum($promsett) / count($promsett);
                                        $promcomt[$j][$m]['nota'] = round(($promedioauxalumt * ($porcentajeasigt / 100)) + ($promtallerauxt * ($porcentajetallert / 100)));


                                    } else {
                                        $promcomt[$j][$m]['nota'] = round(array_sum($promsett) / count($promsett));

                                    }

                                } else {
                                    if ($porcentajetallert) {
                                        $totalporcentajet = 100;
                                        $porcentajeasigt = $totalporcentajet - $porcentajetallert;
                                        $promedioauxalumt = array_sum($promsett) / count($promsett);
                                        $promcomt[$j][$m]['nota'] = round(($promedioauxalumt * ($porcentajeasigt / 100)) + ($promtallerauxt * ($porcentajetallert / 100)));


                                    } else {
                                        $promcomt[$j][$m]['nota'] = intval(array_sum($promsett) / count($promsett));

                                    }
                                }
                            } else {
                                $promcomt[$j][$m]['nota'] = 0;
                            }

                        } else {


                            $promcomt[$j][$m]['nota'] = 0;
                        }
                        $promcomt[$j][$m]['idAsignatura'] = $datosasignaturascombinada[$i]['idAsignatura'];
                        $promcomt[$j][$m]['coef'] = 1;
                        $promcomt[$j][$m]['tiempo'] = 2;


                    }
                    $m++;
                }

            }
        }


        //Taller Por Segmento Curso Completo
        $datostallersem = $modelasignatura->gettaller2($idasignatura, $idperiodo, array(1, 2), 1, array(3));
        $datostallersemseg = $modelasignatura->gettaller2($idasignatura, $idperiodo, array(1, 2), 1, array(4));
        $datostallersemter = $modelasignatura->gettaller2($idasignatura, $idperiodo, array(1, 2), 1, array(5));
        $largotallerprimero = count($datostallersem);
        $largotallersegundo = count($datostallersemseg);
        $largotallertercero = count($datostallersemter);


        $promediotaller = array();
        if ($largotallerprimero > 0) {

            for ($i = 0; $i < $largotallerprimero; $i++) {
                if (!empty($datostallersem[$i]['idConfiguracionTaller'])) {
                    $validataller[$i] = $datostallersem[$i]['idAsignaturaTaller'];
                    for ($j = 0; $j < $largoalumnos; $j++) {
                        $promediofinal = 0;
                        $datosalumnostaller = $modelnotas->listarpromedioalumnotaller($listaalumnos[$j]['idAlumnos'], $idperiodo, 1, $id, $datostallersem[$i]['idAsignatura']);
                        $largodatos = count($datosalumnostaller);

                        for ($k = 0; $k < $largodatos; $k++) {
                            if ($datostaller[$i]['idAsignatura'] == $datosalumnostaller[$k]['idAsignatura']) {
                                if ($datosalumnostaller[$k]['coef'] == 2) {
                                    if ($datosalumnostaller[$k]['nota'] > 0) {
                                        $promediotaller[$j][$i][] = $datosalumnostaller[$k]['nota'];
                                        $promediotaller[$j][$i][] = $datosalumnostaller[$k]['nota'];

                                    } else {
                                        $promediotaller[$j][$i][] = 0;
                                        $promediotaller[$j][$i][] = 0;
                                    }

                                } else {
                                    if ($datosalumnostaller[$k]['nota'] > 0) {
                                        $promediotaller[$j][$i][] = $datosalumnostaller[$k]['nota'];
                                    } else {
                                        $promediotaller[$j][$i][] = 0;
                                    }

                                }


                            }

                        }


                        //Primer Semestre
                        if (!empty($promediotaller[$j][$i])) {
                            $promset = array_diff($promediotaller[$j][$i], array('0'));
                        } else {
                            $promset = 0;
                        }

                        if (is_array($promset)) {
                            if (count($promset) > 0) {
                                if ($datoscurso[0]['aproxAsignatura'] == 1) {
                                    $promtaller[$j][$i]['nota'] = round(array_sum($promset) / count($promset));

                                } else {
                                    $promtaller[$j][$i]['nota'] = intval(array_sum($promset) / count($promset));
                                }
                            } else {
                                $promtaller[$j][$i]['nota'] = 0;
                            }

                        } else {
                            $promtaller[$j][$i]['nota'] = 0;
                        }


                        $promtaller[$j][$i]['idAsignatura'] = $datostallersem[$i]['idAsignaturaTaller'];
                        $promtaller[$j][$i]['coef'] = 1;
                        $promtaller[$j][$i]['tiempo'] = 1;
                        $promtaller[$j][$i]['forma'] = $datostallersem[$i]['forma'];
                        $promtaller[$j][$i]['porcentaje'] = $datostallersem[$i]['porcentaje'];


                    }

                }


            }
        }

        //Segundo Trimestre
        if ($largotallersegundo > 0) {

            for ($i = 0; $i < $largotallersegundo; $i++) {
                if (!empty($datostallersemseg[$i]['idConfiguracionTaller'])) {
                    $validatallers[$i] = $datostallersemseg[$i]['idAsignaturaTaller'];
                    for ($j = 0; $j < $largoalumnos; $j++) {
                        $promediofinal = 0;
                        $datosalumnostallers = $modelnotas->listarpromedioalumnotaller($listaalumnos[$j]['idAlumnos'], $idperiodo, 2, $id, $datostallersemseg[$i]['idAsignatura']);
                        $largodatoss = count($datosalumnostallers);
                        for ($k = 0; $k < $largodatoss; $k++) {
                            if ($datostallersemseg[$i]['idAsignatura'] == $datosalumnostallers[$k]['idAsignatura']) {
                                if ($datosalumnostallers[$k]['coef'] == 2) {
                                    if ($datosalumnostallers[$k]['nota'] > 0) {
                                        $promediotallers[$j][$i][] = $datosalumnostallers[$k]['nota'];
                                        $promediotallers[$j][$i][] = $datosalumnostallers[$k]['nota'];

                                    } else {
                                        $promediotallers[$j][$i][] = 0;
                                        $promediotallers[$j][$i][] = 0;
                                    }

                                } else {
                                    if ($datosalumnostallers[$k]['nota'] > 0) {
                                        $promediotallers[$j][$i][] = $datosalumnostallers[$k]['nota'];
                                    } else {
                                        $promediotallers[$j][$i][] = 0;
                                    }

                                }


                            }


                        }
                        if (!empty($promediotallers[$j][$i])) {
                            $promsets = array_diff($promediotallers[$j][$i], array('0'));
                        } else {
                            $promsets = 0;
                        }

                        if (is_array($promsets)) {
                            if (count($promsets) > 0) {
                                if ($datoscurso[0]['aproxAsignatura'] == 1) {
                                    $auxtallers = round(array_sum($promsets) / count($promsets));
                                    $promtallers[$j][$i]['nota'] = intval($auxtallers);

                                } else {
                                    $promtallers[$j][$i]['nota'] = intval(array_sum($promsets) / count($promsets));
                                }
                            } else {
                                $promtallers[$j][$i]['nota'] = 0;
                            }

                        } else {
                            $promtallers[$j][$i]['nota'] = 0;
                        }


                        $promtallers[$j][$i]['idAsignatura'] = $datostallersemseg[$i]['idAsignaturaTaller'];
                        $promtallers[$j][$i]['coef'] = 1;
                        $promtallers[$j][$i]['tiempo'] = 2;
                        $promtallers[$j][$i]['forma'] = $datostallersemseg[$i]['forma'];
                        $promtallers[$j][$i]['porcentaje'] = $datostallersemseg[$i]['porcentaje'];


                    }

                }


            }
        }

        //Tercer Trimestre
        if ($largotallertercero > 0) {

            for ($i = 0; $i < $largotallertercero; $i++) {
                if (!empty($datostallersemter[$i]['idConfiguracionTaller'])) {
                    $validatallert[$i] = $datostallersemter[$i]['idAsignaturaTaller'];
                    for ($j = 0; $j < $largoalumnos; $j++) {
                        $promediofinal = 0;
                        $datosalumnostallert = $modelnotas->listarpromedioalumnotaller($listaalumnos[$j]['idAlumnos'], $idperiodo, 2, $id, $datostallersemter[$i]['idAsignatura']);
                        $largodatost = count($datosalumnostallert);
                        for ($k = 0; $k < $largodatost; $k++) {
                            if ($datostallersemter[$i]['idAsignatura'] == $datosalumnostallert[$k]['idAsignatura']) {
                                if ($datosalumnostallert[$k]['coef'] == 2) {
                                    if ($datosalumnostallert[$k]['nota'] > 0) {
                                        $promediotallert[$j][$i][] = $datosalumnostallert[$k]['nota'];
                                        $promediotallert[$j][$i][] = $datosalumnostallert[$k]['nota'];

                                    } else {
                                        $promediotallert[$j][$i][] = 0;
                                        $promediotallert[$j][$i][] = 0;
                                    }

                                } else {
                                    if ($datosalumnostallert[$k]['nota'] > 0) {
                                        $promediotallert[$j][$i][] = $datosalumnostallert[$k]['nota'];
                                    } else {
                                        $promediotallert[$j][$i][] = 0;
                                    }

                                }


                            }


                        }
                        if (!empty($promediotallert[$j][$i])) {
                            $promsett = array_diff($promediotallert[$j][$i], array('0'));
                        } else {
                            $promsett = 0;
                        }

                        if (is_array($promsett)) {
                            if (count($promsett) > 0) {
                                if ($datoscurso[0]['aproxAsignatura'] == 1) {
                                    $auxtallert = round(array_sum($promsett) / count($promsett));
                                    $promtallert[$j][$i]['nota'] = intval($auxtallert);

                                } else {
                                    $promtallert[$j][$i]['nota'] = intval(array_sum($promsett) / count($promsett));
                                }
                            } else {
                                $promtallert[$j][$i]['nota'] = 0;
                            }

                        } else {
                            $promtallert[$j][$i]['nota'] = 0;
                        }


                        $promtallert[$j][$i]['idAsignatura'] = $datostallersemter[$i]['idAsignaturaTaller'];
                        $promtallert[$j][$i]['coef'] = 1;
                        $promtallert[$j][$i]['tiempo'] = 2;
                        $promtallert[$j][$i]['forma'] = $datostallersemter[$i]['forma'];
                        $promtallert[$j][$i]['porcentaje'] = $datostallersemter[$i]['porcentaje'];


                    }

                }


            }
        }


        if ($largoalumnos == 0 || count($listanotas) == 0) {
            echo "<script type=\"text/javascript\">alert(\"Sin Notas\");</script>";
            echo "<script>parent.$.fancybox.close();</script>";
            exit;

        }

        for ($i = 0; $i < $largoalumnos; $i++) {

            $valores = $modelnotas->listarnotasporalumnoasignatura($listaalumnos[$i]['idAlumnos'], $idperiodo, $id, $idasignatura);
            if ($valores != '' || !empty($valores)) {
                $listaalumnos[$i]['listanotas'] = $valores;
            } else {
                $listaalumnos[$i]['listanotas'] = 0;

            }
            for ($j = 0; $j < count($promtaller[$i]); $j++) {
                $listaalumnos[$i]['listanotas'][] = $promtaller[$i][$j];
            }


            foreach ($promcom[$i] as $a => $j) {
                $listaalumnos[$i]['listanotas'][] = $promcom[$i][$a];
            }
            //Segundo Trimestre
            for ($j = 0; $j < count($promtallers[$i]); $j++) {
                $listaalumnos[$i]['listanotas'][] = $promtallers[$i][$j];
            }

            //Tercer Trimestre
            for ($j = 0; $j < count($promtallert[$i]); $j++) {
                $listaalumnos[$i]['listanotas'][] = $promtallert[$i][$j];
            }

            foreach ($promcoms[$i] as $a => $j) {
                $listaalumnos[$i]['listanotas'][] = $promcoms[$i][$a];
            }

            foreach ($promcomt[$i] as $a => $j) {
                $listaalumnos[$i]['listanotas'][] = $promcomt[$i][$a];
            }

            if ($listapromediostaller[$i]) {
                for ($j = 0; $j < count($listapromediostaller[$i]); $j++) {
                    $listaalumnos[$i]['listanotas'][] = $listapromediostaller[$i][$j];
                }
            }

            if ($listapromediostallers[$i]) {
                for ($j = 0; $j < count($listapromediostallers[$i]); $j++) {
                    $listaalumnos[$i]['listanotas'][] = $listapromediostallers[$i][$j];
                }
            }

            if ($listapromediostallert[$i]) {
                for ($j = 0; $j < count($listapromediostallert[$i]); $j++) {
                    $listaalumnos[$i]['listanotas'][] = $listapromediostallert[$i][$j];
                }
            }


        }


        //Agregamos cantidad de notas por alumno y la asignamos a cada aisgnatura

        if (!empty($datosasignaturas)) {
            $largoasignatura = count($datosasignaturas);
            for ($i = 0; $i < $largoasignatura; $i++) {

                $datoscuenta[$i] = $modelnotas->listarcantidadnotasanual($id, $idperiodo, $datosasignaturas[$i]['idAsignatura']);
                if (empty($datoscuenta[$i]) && $datosasignaturas[$i]['tipoAsignatura'] == 1) {
                    $datosasig[$i] = 0;
                } else {
                    $largonotas = count($datoscuenta[$i]);
                    $datosasig[$i] = 0;
                    //Primer Trimestre
                    for ($j = 0; $j < count($validataller); $j++) {
                        if ($datosasignaturas[$i]['idAsignatura'] == $validataller[$j]) {
                            $datosasig[$i] += 1;
                        }
                    }
                    //Segundo Trimestre

                    for ($j = 0; $j < count($validatallers); $j++) {
                        if ($datosasignaturas[$i]['idAsignatura'] == $validatallers[$j]) {
                            $datosasig[$i] += 1;
                        }
                    }

                    //Tercer Trimestre

                    for ($j = 0; $j < count($validatallert); $j++) {
                        if ($datosasignaturas[$i]['idAsignatura'] == $validatallert[$j]) {
                            $datosasig[$i] += 1;
                        }
                    }
                    for ($j = 0; $j < count($validacom); $j++) {
                        for ($k = 0; $k < count($validacom[$j]); $k++) {
                            if ($datosasignaturas[$i]['idAsignatura'] == $validacom[$j][$k]) {

                                $datosasig[$i] += 1;
                            }
                        }

                    }

                    for ($j = 0; $j < count($datostalleres); $j++) {
                        if ($datosasignaturas[$i]['idAsignatura'] == $datostalleres[$j]['idAsignaturaTaller']) {
                            $datosasig[$i] += 1;
                        }
                    }

                    for ($j = 0; $j < $largonotas; $j++) {

                        if ($datoscuenta[$i][$j]['coef'] == 2) {
                            $datosasig[$i] += 2;
                        } else {
                            $datosasig[$i] += 1;
                        }

                    }


                }


            }
        } else {
            echo "<script type=\"text/javascript\">alert(\"El Curso no Posee Asignaturas en el periodo\");</script>";
            echo "<script>parent.$.fancybox.close();</script>";
            exit;

        }


        if (!empty($listaalumnos)) {
            $r = 0;

            for ($i = 0; $i < $largoalumnos; $i++) {

                $r = 0;

                for ($j = 0; $j < $largoasignatura; $j++) {

                    $promtalleraux = array();
                    $porcentajetaller = array();
                    $sumataller = 0;
                    $promtallerauxs = array();
                    $porcentajetallers = array();
                    $sumatallers = 0;
                    $promtallerauxt = array();
                    $porcentajetallert = array();
                    $sumatallert = 0;
                    $row = $datosasig[$j];


                    $promedio = 0;
                    $contador = 0;
                    $contadorpromedio = 0;
                    $promedios = 0;
                    $contadorpromedios = 0;
                    $promediot = 0;
                    $contadorpromediot = 0;
                    if ($row != 0) {
                        $largolistanota = count($listaalumnos[$i]['listanotas']);
                        for ($z = 0; $z < $largolistanota; $z++) {
                            $contadoraux = 0;

                            if ($datosasignaturas[$j]['idAsignatura'] == $listaalumnos[$i]['listanotas'][$z]['idAsignatura']) {


                                //Primer Trimestre
                                if ($listaalumnos[$i]['listanotas'][$z]['tiempo'] == 3) {

                                    if ($listaalumnos[$i]['listanotas'][$z]['coef'] == 2) {
                                        $contador += 2;
                                        if ($listaalumnos[$i]['listanotas'][$z]['nota'] == 0 || empty($listaalumnos[$i]['listanotas'][$z]['nota']) || $listaalumnos[$i]['listanotas'][$z]['nota'] == NULL || $listaalumnos[$i]['listanotas'][$z]['nota'] == '') {
                                            $promedio += 0;
                                            $contadorpromedio += 0;


                                        } else {
                                            $promedio += ($listaalumnos[$i]['listanotas'][$z]['nota'] + $listaalumnos[$i]['listanotas'][$z]['nota']);
                                            $contadorpromedio += 2;

                                        }

                                    } else {
                                        $contador += 1;

                                        if ($listaalumnos[$i]['listanotas'][$z]['nota'] == 0 || empty($listaalumnos[$i]['listanotas'][$z]['nota']) || $listaalumnos[$i]['listanotas'][$z]['nota'] == NULL || $listaalumnos[$i]['listanotas'][$z]['nota'] == '') {
                                            if (!empty($listaalumnos[$i]["listanotas"][$z]["forma"])) {
                                                if ($listaalumnos[$i]["listanotas"][$z]["nota"] == 0) {

                                                    $promedio += 0;
                                                    $contadorpromedio += 0;

                                                }

                                            } else {
                                                $promedio += 0;
                                                $contadorpromedio += 0;


                                            }


                                        } else {
                                            if (!empty($listaalumnos[$i]["listanotas"][$z]["forma"]) && $listaalumnos[$i]["listanotas"][$z]["forma"] == 2) {

                                                $contadorauxtaller = true;
                                                $promtalleraux[] = $listaalumnos[$i]['listanotas'][$z]['nota'];
                                                $porcentajetaller[] = $listaalumnos[$i]["listanotas"][$z]["porcentaje"];


                                            } else {

                                                $promedio += $listaalumnos[$i]['listanotas'][$z]['nota'];
                                                $contadorpromedio += 1;


                                            }


                                        }


                                    }
                                }
                                if ($listaalumnos[$i]['listanotas'][$z]['tiempo'] == 4) {


                                    if ($listaalumnos[$i]['listanotas'][$z]['coef'] == 2) {
                                        $contador += 2;
                                        if ($listaalumnos[$i]['listanotas'][$z]['nota'] == 0 || empty($listaalumnos[$i]['listanotas'][$z]['nota']) || $listaalumnos[$i]['listanotas'][$z]['nota'] == NULL || $listaalumnos[$i]['listanotas'][$z]['nota'] == '') {
                                            $promedios += 0;
                                            $contadorpromedios += 0;


                                        } else {
                                            $promedios += ($listaalumnos[$i]['listanotas'][$z]['nota'] + $listaalumnos[$i]['listanotas'][$z]['nota']);
                                            $contadorpromedios += 2;

                                        }

                                    } else {


                                        $contador += 1;

                                        if ($listaalumnos[$i]['listanotas'][$z]['nota'] == 0 || empty($listaalumnos[$i]['listanotas'][$z]['nota']) || $listaalumnos[$i]['listanotas'][$z]['nota'] == NULL || $listaalumnos[$i]['listanotas'][$z]['nota'] == '') {
                                            if (!empty($listaalumnos[$i]["listanotas"][$z]["forma"])) {
                                                if ($listaalumnos[$i]["listanotas"][$z]["nota"] == 0) {

                                                    $promedios += 0;
                                                    $contadorpromedios += 0;

                                                }

                                            } else {
                                                $promedios += 0;
                                                $contadorpromedios += 0;


                                            }


                                        } else {
                                            if (!empty($listaalumnos[$i]["listanotas"][$z]["forma"]) && $listaalumnos[$i]["listanotas"][$z]["forma"] == 2) {

                                                $contadorauxtallers = true;
                                                $promtallerauxs[] = $listaalumnos[$i]['listanotas'][$z]['nota'];
                                                $porcentajetallers[] = $listaalumnos[$i]["listanotas"][$z]["porcentaje"];


                                            } else {

                                                $promedios += $listaalumnos[$i]['listanotas'][$z]['nota'];
                                                $contadorpromedios += 1;


                                            }


                                        }


                                    }
                                }
                                //Tercer Trimestre
                                if ($listaalumnos[$i]['listanotas'][$z]['tiempo'] == 5) {


                                    if ($listaalumnos[$i]['listanotas'][$z]['coef'] == 2) {
                                        $contador += 2;
                                        if ($listaalumnos[$i]['listanotas'][$z]['nota'] == 0 || empty($listaalumnos[$i]['listanotas'][$z]['nota']) || $listaalumnos[$i]['listanotas'][$z]['nota'] == NULL || $listaalumnos[$i]['listanotas'][$z]['nota'] == '') {
                                            $promediot += 0;
                                            $contadorpromediot += 0;


                                        } else {
                                            $promediot += ($listaalumnos[$i]['listanotas'][$z]['nota'] + $listaalumnos[$i]['listanotas'][$z]['nota']);
                                            $contadorpromediot += 2;

                                        }

                                    } else {


                                        $contador += 1;

                                        if ($listaalumnos[$i]['listanotas'][$z]['nota'] == 0 || empty($listaalumnos[$i]['listanotas'][$z]['nota']) || $listaalumnos[$i]['listanotas'][$z]['nota'] == NULL || $listaalumnos[$i]['listanotas'][$z]['nota'] == '') {
                                            if (!empty($listaalumnos[$i]["listanotas"][$z]["forma"])) {
                                                if ($listaalumnos[$i]["listanotas"][$z]["nota"] == 0) {

                                                    $promediot += 0;
                                                    $contadorpromediot += 0;

                                                }

                                            } else {
                                                $promediot += 0;
                                                $contadorpromediot += 0;


                                            }


                                        } else {
                                            if (!empty($listaalumnos[$i]["listanotas"][$z]["forma"]) && $listaalumnos[$i]["listanotas"][$z]["forma"] == 2) {

                                                $contadorauxtallert = true;
                                                $promtallerauxt[] = $listaalumnos[$i]['listanotas'][$z]['nota'];
                                                $porcentajetallert[] = $listaalumnos[$i]["listanotas"][$z]["porcentaje"];


                                            } else {

                                                $promediot += $listaalumnos[$i]['listanotas'][$z]['nota'];
                                                $contadorpromediot += 1;


                                            }


                                        }


                                    }
                                }


                                //Promedios por notas

                                if ($contador == $row) {
                                    $promedioaux = 0;
                                    $promedioauxs = 0;
                                    $promedioauxt = 0;

                                    if ($contadorpromedio != 0 && $promedio != 0) {
                                        $promedioaux = 0;
                                        //SI el Taller es por porcentaje
                                        if ($contadorauxtaller) {
                                            $totalporcentaje = 100;
                                            if (count($promtalleraux) > 1) {
                                                for ($t = 0; $t < count($promtalleraux); $t++) {
                                                    $totalporcentaje = $totalporcentaje - $porcentajetaller[$t];
                                                    $sumataller += $promtalleraux[$t] * ($porcentajetaller[$t] / 100);
                                                }

                                            } else {
                                                $totalporcentaje = 100 - $porcentajetaller[0];
                                                $sumataller = $promtalleraux[0] * ($porcentajetaller[0] / 100);

                                            }


                                            if ($datoscurso[0]['aproxAsignatura'] == 1) {//Aproxima
                                                $promedioaux = round($promedio / $contadorpromedio);
                                                $promedioaux = ($promedioaux * ($totalporcentaje / 100)) + $sumataller;
                                                $promedioaux = round($promedioaux);


                                            } else {
                                                $promedioaux = round($promedio / $contadorpromedio);
                                                $promedioaux = ($promedioaux * ($totalporcentaje / 100)) + $sumataller;
                                                $promedioaux = intval($promedioaux);
                                            }

                                        } else {

                                            if ($datoscurso[0]['aproxAsignatura'] == 1) {//Aproxima
                                                $promedioaux = round($promedio / $contadorpromedio);
                                                $promfinal[$i][] = $promedioaux;

                                            } else {
                                                $promedioaux = intval($promedio / $contadorpromedio);
                                                $promfinal[$i][] = $promedioaux;
                                            }

                                        }


                                        $promedioalumnos[$i]['primero'] = $promedioaux;


                                    } else {
                                        $promedioalumnos[$i]['primero'] = 0;
                                        $promfinal[$i][] = 0;


                                    }


                                    //Segundo Trimestre
                                    if ($contadorpromedios != 0 && $promedios != 0) {
                                        $promedioauxs = 0;
                                        //SI el Taller es por porcentaje
                                        if ($contadorauxtallers) {
                                            $totalporcentajes = 100;
                                            if (count($promtallerauxs) > 1) {
                                                for ($t = 0; $t < count($promtallerauxs); $t++) {
                                                    $totalporcentajes = $totalporcentajes - $porcentajetallers[$t];
                                                    $sumatallers += $promtallerauxs[$t] * ($porcentajetallers[$t] / 100);
                                                }

                                            } else {
                                                $totalporcentajes = 100 - $porcentajetallers[0];
                                                $sumatallers = $promtallerauxs[0] * ($porcentajetallers[0] / 100);

                                            }


                                            if ($datoscurso[0]['aproxAsignatura'] == 1) {//Aproxima
                                                $promedioauxs = round($promedios / $contadorpromedios);
                                                $promedioauxs = ($promedioauxs * ($totalporcentajes / 100)) + $sumatallers;
                                                $promedioauxs = round($promedioauxs);


                                            } else {
                                                $promedioauxs = round($promedios / $contadorpromedios);
                                                $promedioauxs = ($promedioauxs * ($totalporcentajes / 100)) + $sumatallers;
                                                $promedioauxs = intval($promedioauxs);

                                            }

                                        } else {

                                            if ($datoscurso[0]['aproxAsignatura'] == 1) {//Aproxima
                                                $promedioauxs = round($promedios / $contadorpromedios);
                                                $promfinal[$i][] = $promedioauxs;


                                            } else {
                                                $promedioauxs = intval($promedios / $contadorpromedios);
                                                $promfinal[$i][] = $promedioauxs;

                                            }
                                        }


                                        $promedioalumnos[$i]['segundo'] = $promedioauxs;


                                    } else {
                                        $promedioalumnos[$i]['segundo'] = 0;
                                        $promfinal[$i][] = 0;


                                    }

                                    //Tercer Trimestre
                                    if ($contadorpromediot != 0 && $promediot != 0) {
                                        $promedioauxt = 0;
                                        //SI el Taller es por porcentaje
                                        if ($contadorauxtallert) {
                                            $totalporcentajet = 100;
                                            if (count($promtallerauxt) > 1) {
                                                for ($t = 0; $t < count($promtallerauxt); $t++) {
                                                    $totalporcentajet = $totalporcentajet - $porcentajetallert[$t];
                                                    $sumatallert += $promtallerauxt[$t] * ($porcentajetallert[$t] / 100);
                                                }

                                            } else {
                                                $totalporcentajet = 100 - $porcentajetallert[0];
                                                $sumatallert = $promtallerauxt[0] * ($porcentajetallert[0] / 100);

                                            }


                                            if ($datoscurso[0]['aproxAsignatura'] == 1) {//Aproxima
                                                $promedioauxt = round($promediot / $contadorpromediot);
                                                $promedioauxt = ($promedioauxt * ($totalporcentajet / 100)) + $sumatallert;
                                                $promedioauxt = round($promedioauxt);


                                            } else {
                                                $promedioauxt = round($promediot / $contadorpromediot);
                                                $promedioauxt = ($promedioauxt * ($totalporcentajet / 100)) + $sumatallert;
                                                $promedioauxt = intval($promedioauxt);

                                            }

                                        } else {

                                            if ($datoscurso[0]['aproxAsignatura'] == 1) {//Aproxima
                                                $promedioauxt = round($promediot / $contadorpromediot);
                                                $promfinal[$i][] = $promedioauxt;


                                            } else {
                                                $promedioauxt = intval($promediot / $contadorpromediot);
                                                $promfinal[$i][] = $promedioauxt;

                                            }
                                        }


                                        $promedioalumnos[$i]['tercero'] = $promedioauxt;


                                    } else {
                                        $promedioalumnos[$i]['tercero'] = 0;
                                        $promfinal[$i][] = 0;


                                    }


                                    //Promedio Final De Asignatura
                                    $promedioauxiliar = array($promedioaux, $promedioauxs, $promedioauxt);
                                    $resultadoauxiliar = array_values(array_diff($promedioauxiliar, array('0')));
                                    if (!empty($resultadoauxiliar)) {


                                        if ($datoscurso[0]['aproxAnual'] == 1) {
                                            $finalasignatura = round((array_sum($resultadoauxiliar)) / count($resultadoauxiliar));

                                        } else {
                                            $finalasignatura = intval((array_sum($resultadoauxiliar)) / count($resultadoauxiliar));

                                        }
                                    } else {
                                        $finalasignatura = 0;
                                    }


                                    if ($agregadecimas) {
                                        if ($finalasignatura >= 60 && $finalasignatura > 0) {
                                            $finalasignatura += 2;
                                            if ($finalasignatura > 70) {
                                                $finalasignatura = 70;

                                            }

                                        }
                                    }
                                    $promediototal[$i][] = $finalasignatura;
                                    $notamatriz[$r][] = $finalasignatura;

                                }// fin promedio por notas

                            }

                        }// fin for


                    }  //fin if row


                } //fin for Asignaturas


                if ($promediototal[$i] != '' || $promediototal[$i] != null) {

                    if ($datoscurso[0]['aproxFinal'] == 1) {//Aproxima
                        $promedioalumnos[$i]['final'] = round(array_sum($promediototal[$i]) / count($promediototal[$i]));


                    } else {
                        $promedioalumnos[$i]['final'] = intval(array_sum($promediototal[$i]) / count($promediototal[$i]));


                    }


                } else {
                    $promedioalumnos[$i]['final'] = 0;
                }


            }// Fin for lista Alumnos


        }


        $resultadoconcepto = $modelasignatura->getasignaturaconcepto($idasignatura, $id, $idperiodo);
        if ($resultadoconcepto) {
            for ($i = 0; $i < count($promedioalumnos); $i++) {

                for ($k = 0; $k < count($resultadoconcepto); $k++) {

                    if ($promedioalumnos[$i]['primero'] == 0) {
                        $promedioalumnos[$i]['primeroconcepto'] = 0;
                    } else {
                        if ($resultadoconcepto[$k]['desde'] <= $promedioalumnos[$i]['primero'] && $resultadoconcepto[$k]['hasta'] >= $promedioalumnos[$i]['primero']) {
                            $promedioalumnos[$i]['primeroconcepto'] = $resultadoconcepto[$k]['concepto'];
                        }
                    }

                    if ($promedioalumnos[$i]['segundo'] == 0) {
                        $promedioalumnos[$i]['segundoconcepto'] = 0;

                    } else {
                        if ($resultadoconcepto[$k]['desde'] <= $promedioalumnos[$i]['segundo'] && $resultadoconcepto[$k]['hasta'] >= $promedioalumnos[$i]['segundo']) {
                            $promedioalumnos[$i]['segundoconcepto'] = $resultadoconcepto[$k]['concepto'];
                        }
                    }

                    if ($promedioalumnos[$i]['tercero'] == 0) {
                        $promedioalumnos[$i]['terceroconcepto'] = 0;

                    } else {
                        if ($resultadoconcepto[$k]['desde'] <= $promedioalumnos[$i]['tercero'] && $resultadoconcepto[$k]['hasta'] >= $promedioalumnos[$i]['tercero']) {
                            $promedioalumnos[$i]['terceroconcepto'] = $resultadoconcepto[$k]['concepto'];
                        }
                    }

                    if ($promedioalumnos[$i]['final'] == 0) {
                        $promedioalumnos[$i]['finalconcepto'] = 0;

                    } else {
                        if ($resultadoconcepto[$k]['desde'] <= $promedioalumnos[$i]['final'] && $resultadoconcepto[$k]['hasta'] >= $promedioalumnos[$i]['final']) {
                            $promedioalumnos[$i]['finalconcepto'] = $resultadoconcepto[$k]['concepto'];
                        }
                    }


                }


            }
        }


        if ($tipo == 2) {
            if ($datoscurso[0]['examen'] == 1) {
                //Examen a final de asignatura
                $datosexamenes = $modeloprueba->getexamen($id, $idperiodo, $idasignatura, 6);
                if ($datosexamenes[0]) {
                    $datosalumnosexamen = $modelnotas->getnotasexamenalumno($datosexamenes[0]['idEvaluacion'], $id, $idasignatura, $idperiodo, 6, $listaalumnos[0]['idAlumnos']);

                }

                if ($datosalumnosexamen) {


                    if ($promedioalumnos[0]['final'] > 0) {


                        $totalex = 100 - $datosexamenes[0]['porcentajeExamen'];

                        //nota d examen

                        $sumaex = $datosalumnosexamen[0]['nota'] * ($datosexamenes[0]['porcentajeExamen'] / 100);


                        if ($datoscurso[0]['aproxExamen'] == 1) {
                            $promedioalumnos[0]['finalex'] = round(($promedioalumnos[0]['final'] * ($totalex / 100)) + $sumaex);
                        } else {
                            $promedioalumnos[0]['finalex'] = intval(($promedioalumnos[0]['final'] * ($totalex / 100)) + $sumaex);
                        }


                    } else {
                        $promedioalumnos[0]['finalex'] = $promedioalumnos[0]['final'];
                    }


                }


            }
            $this->_helper->json($promedioalumnos);
        } else {
            return $promedioalumnos;
        }


    }

    public function getnotasasignaturatrimAction()

    {
        $dias = array("Domingo", "Lunes", "Martes", "Miércoles", "Jueves", "Viernes", "Sábado");
        $meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");


        $this->_helper->layout->disableLayout();

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcursos = new Zend_Session_Namespace('id_curso');
        $idcurso = $idcursos->id_curso;

        $modelonotas = new Application_Model_DbTable_Notas();
        $modeloprueba = new Application_Model_DbTable_Pruebas();
        $modeloalumnos = new Application_Model_DbTable_Alumnosactual();
        $modeloasignatura = new Application_Model_DbTable_Asignaturascursos();
        $modelcurso = new Application_Model_DbTable_Cursos();
        $idasignatura = $this->_getParam('id');

        $asignaturas = new Zend_Session_Namespace('idAsignatura');
        $asignaturas->idAsignatura = $idasignatura;

        $resultadoasignatura = $modeloasignatura->get($idasignatura, $idcurso, $idperiodo);

        $datoscurso = $modelcurso->listarcursoid($idcurso, $idperiodo);

        $examenes = new Zend_Session_Namespace('examen');
        $examenes->examen = $datoscurso[0]['examen'];

        $listadealumnos = $modeloalumnos->listaralumnoscursoactual($idcurso, $idperiodo);
        $largoalumnos = count($listadealumnos);
        $datostaller = array();


        //Taller por alumnos

        if (count($resultadoasignatura) > 0) {
            //Si la Asignatura es normal, buscamos si algun taller corresponde a la asignatura
            if ($resultadoasignatura[0]['tipoAsignatura'] == 1) {
                $datostalleres = $modeloasignatura->gettallerasignaturaalumnos($idasignatura);
                $modelotallerdetalle = new Application_Model_DbTable_Asignaturascursos();

                $promediotalleralumnos = array();
                $promediotalleralumnoss = array();
                $promediotalleralumnost = array();

                if ($datostalleres) {

                    for ($i = 0; $i < count($datostalleres); $i++) {
                        for ($j = 0; $j < $largoalumnos; $j++) {
                            $detalletaller = $modelotallerdetalle->gettallersegmento($datostalleres[$i]['idConfiguracionTaller'], 3, $listadealumnos[$j]['idAlumnos']);
                            $detalletallers = $modelotallerdetalle->gettallersegmento($datostalleres[$i]['idConfiguracionTaller'], 4, $listadealumnos[$j]['idAlumnos']);
                            $detalletallert = $modelotallerdetalle->gettallersegmento($datostalleres[$i]['idConfiguracionTaller'], 5, $listadealumnos[$j]['idAlumnos']);

                            //Primer Trimestre
                            if ($datostalleres[$i]['tiempoOpcion'] == 3) {
                                $promtalleralumnos[$i]['idAsignatura'] = $datostalleres[$i]['idAsignaturaTaller'];
                                $promtalleralumnos[$i]['coef'] = 1;
                                $promtalleralumnos[$i]['tiempo'] = 3;
                                $promtalleralumnos[$i]['forma'] = $datostalleres[$i]['forma'];
                                $promtalleralumnos[$i]['taller'] = 1;
                                if ($datostalleres[$i]['forma'] == 2) {
                                    $promtalleralumnos[$i]['fechaEvaluacion'] = $datostalleres[$i]['nombreAsignatura'] . ' ' . $datostalleres[$i]['porcentaje'] . '%';
                                } else {
                                    $promtalleralumnos[$i]['fechaEvaluacion'] = $datostalleres[$i]['nombreAsignatura'];
                                }
                                $promtalleralumnos[$i]['idEvaluacion'] = "taller" . $datostalleres[$i]['idConfiguracionTaller'];


                                if ($detalletaller) {
                                    $datosalumnostaller = $modelonotas->listarpromedioalumnotaller($listadealumnos[$j]['idAlumnos'], $idperiodo, 3, $idcurso, $detalletaller[0]['idAsignatura']);

                                    if ($datosalumnostaller) {
                                        for ($k = 0; $k < count($datosalumnostaller); $k++) {

                                            if ($datostalleres[$i]['idAsignatura'] == $datosalumnostaller[$k]['idAsignatura']) {
                                                if ($datosalumnostaller[$k]['coef'] == 2) {
                                                    if ($datosalumnostaller[$k]['nota'] > 0) {
                                                        $promediotalleralumnos[$j][$i][] = $datosalumnostaller[$k]['nota'];
                                                        $promediotalleralumnos[$j][$i][] = $datosalumnostaller[$k]['nota'];

                                                    } else {
                                                        $promediotalleralumnos[$j][$i][] = 0;
                                                        $promediotalleralumnos[$j][$i][] = 0;
                                                    }

                                                } else {
                                                    if ($datosalumnostaller[$k]['nota'] > 0) {
                                                        $promediotalleralumnos[$j][$i][] = $datosalumnostaller[$k]['nota'];
                                                    } else {
                                                        $promediotalleralumnos[$j][$i][] = 0;
                                                    }

                                                }


                                            }


                                        }
                                    }

                                    if (!empty($promediotalleralumnos[$j][$i])) {
                                        $promsetalumnos = array_diff($promediotalleralumnos[$j][$i], array('0'));
                                    } else {
                                        $promsetalumnos = 0;
                                    }


                                    if (is_array($promsetalumnos)) {
                                        if (count($promsetalumnos) > 0) {

                                            if ($datoscurso[0]['aproxAsignatura'] == 1) {
                                                $promtalleralumnos[$i]['alumnos'][$j]['nota'] = round(array_sum($promsetalumnos) / count($promsetalumnos));
                                                $promtalleralumnos[$i]['alumnos'][$j]['nota'] = intval($promtalleralumnos[$i]['alumnos'][$j]['nota']);

                                            } else {
                                                $promtalleralumnos[$i]['alumnos'][$j]['nota'] = intval(array_sum($promsetalumnos) / count($promsetalumnos));

                                            }
                                        } else {
                                            $promtalleralumnos[$i]['alumnos'][$j]['nota'] = 0;
                                        }
                                    } else {
                                        $promtalleralumnos[$i]['alumnos'][$j]['nota'] = 0;
                                    }


                                } else {
                                    $promtalleralumnos[$i]['alumnos'][$j]['nota'] = 0;

                                }
                            } else {
                                //$promtalleralumnos[$i]['alumnos'][$j]['nota']= 0;
                                //$promtalleralumnos[$i]['alumnos'][$j]['auth'] = 0;

                            }

                            //Segundo Trimestre
                            if ($datostalleres[$i]['tiempoOpcion'] == 4) {
                                $promtalleralumnoss[$i]['idAsignatura'] = $datostalleres[$i]['idAsignaturaTaller'];
                                $promtalleralumnoss[$i]['coef'] = 1;
                                $promtalleralumnoss[$i]['tiempo'] = 4;
                                $promtalleralumnoss[$i]['forma'] = $datostalleres[$i]['forma'];
                                $promtalleralumnoss[$i]['taller'] = 1;
                                if ($datostalleres[$i]['forma'] == 2) {
                                    $promtalleralumnoss[$i]['fechaEvaluacion'] = $datostalleres[$i]['nombreAsignatura'] . ' ' . $datostalleres[$i]['porcentaje'] . '%';
                                } else {
                                    $promtalleralumnoss[$i]['fechaEvaluacion'] = $datostalleres[$i]['nombreAsignatura'];
                                }
                                $promtalleralumnoss[$i]['idEvaluacion'] = "taller" . $datostalleres[$i]['idConfiguracionTaller'];
                                //$promtalleralumnoss[$i]['fechaEvaluacion'] = $datostalleres[$i]['nombreAsignatura'] . ' ' . $datostalleres[$i]['porcentaje'] . '%';
                                $promtalleralumnoss[$i]['idEvaluacion'] = "taller" . $datostalleres[$i]['idConfiguracionTaller'];
                                if ($detalletallers) {
                                    $datosalumnostallers = $modelonotas->listarpromedioalumnotaller($listadealumnos[$j]['idAlumnos'], $idperiodo, 4, $idcurso, $detalletallers[0]['idAsignatura']);


                                    if ($datosalumnostallers) {
                                        for ($k = 0; $k < count($datosalumnostallers); $k++) {

                                            if ($datostalleres[$i]['idAsignatura'] == $datosalumnostallers[$k]['idAsignatura']) {

                                                if ($datosalumnostallers[$k]['coef'] == 2) {
                                                    if ($datosalumnostallers[$k]['nota'] > 0) {
                                                        $promediotalleralumnoss[$j][$i][] = $datosalumnostallers[$k]['nota'];
                                                        $promediotalleralumnoss[$j][$i][] = $datosalumnostallers[$k]['nota'];

                                                    } else {
                                                        $promediotalleralumnoss[$j][$i][] = 0;
                                                        $promediotalleralumnoss[$j][$i][] = 0;
                                                    }

                                                } else {
                                                    if ($datosalumnostallers[$k]['nota'] > 0) {
                                                        $promediotalleralumnoss[$j][$i][] = $datosalumnostallers[$k]['nota'];
                                                    } else {
                                                        $promediotalleralumnoss[$j][$i][] = 0;
                                                    }

                                                }


                                            }


                                        }
                                    }

                                    if (!empty($promediotalleralumnoss[$j][$i])) {
                                        $promsetalumnoss = array_diff($promediotalleralumnoss[$j][$i], array('0'));
                                    } else {
                                        $promsetalumnoss = 0;
                                    }


                                    if (is_array($promsetalumnoss)) {
                                        if (count($promsetalumnoss) > 0) {

                                            if ($datoscurso[0]['aproxAsignatura'] == 1) {
                                                $promtalleralumnoss[$i]['alumnos'][$j]['nota'] = round(array_sum($promsetalumnoss) / count($promsetalumnoss));
                                                $promtalleralumnoss[$i]['alumnos'][$j]['nota'] = intval($promtalleralumnoss[$i]['alumnos'][$j]['nota']);

                                            } else {
                                                $promtalleralumnoss[$i]['alumnos'][$j]['nota'] = intval(array_sum($promsetalumnoss) / count($promsetalumnoss));
                                            }
                                        } else {
                                            $promtalleralumnoss[$i]['alumnos'][$j]['nota'] = 0;
                                        }
                                    } else {
                                        $promtalleralumnoss[$i]['alumnos'][$j]['nota'] = 0;
                                    }


                                } else {
                                    $promtalleralumnoss[$i]['alumnos'][$j]['nota'] = 0;
                                }
                            } else {
                                //$promtalleralumnoss[$j][$i] = array();
                            }

                            //Tercer Trimestre
                            if ($datostalleres[$i]['tiempoOpcion'] == 5) {
                                $promtalleralumnost[$i]['idAsignatura'] = $datostalleres[$i]['idAsignaturaTaller'];
                                $promtalleralumnost[$i]['coef'] = 1;
                                $promtalleralumnost[$i]['tiempo'] = 5;
                                $promtalleralumnost[$i]['forma'] = $datostalleres[$i]['forma'];
                                $promtalleralumnost[$i]['taller'] = 1;
                                if ($datostalleres[$i]['forma'] == 2) {
                                    $promtalleralumnost[$i]['fechaEvaluacion'] = $datostalleres[$i]['nombreAsignatura'] . ' ' . $datostalleres[$i]['porcentaje'] . '%';
                                } else {
                                    $promtalleralumnost[$i]['fechaEvaluacion'] = $datostalleres[$i]['nombreAsignatura'];
                                }
                                $promtalleralumnost[$i]['idEvaluacion'] = "taller" . $datostalleres[$i]['idConfiguracionTaller'];
                                $promtalleralumnost[$i]['idEvaluacion'] = "taller" . $datostalleres[$i]['idConfiguracionTaller'];
                                if ($detalletallert) {
                                    $datosalumnostallert = $modelonotas->listarpromedioalumnotaller($listadealumnos[$j]['idAlumnos'], $idperiodo, 5, $idcurso, $detalletallert[0]['idAsignatura']);


                                    if ($datosalumnostallert) {
                                        for ($k = 0; $k < count($datosalumnostallert); $k++) {

                                            if ($datostalleres[$i]['idAsignatura'] == $datosalumnostallert[$k]['idAsignatura']) {

                                                if ($datosalumnostallert[$k]['coef'] == 2) {
                                                    if ($datosalumnostallert[$k]['nota'] > 0) {
                                                        $promediotalleralumnost[$j][$i][] = $datosalumnostallert[$k]['nota'];
                                                        $promediotalleralumnost[$j][$i][] = $datosalumnostallert[$k]['nota'];

                                                    } else {
                                                        $promediotalleralumnost[$j][$i][] = 0;
                                                        $promediotalleralumnost[$j][$i][] = 0;
                                                    }

                                                } else {
                                                    if ($datosalumnostallers[$k]['nota'] > 0) {
                                                        $promediotalleralumnost[$j][$i][] = $datosalumnostallert[$k]['nota'];
                                                    } else {
                                                        $promediotalleralumnost[$j][$i][] = 0;
                                                    }

                                                }


                                            }


                                        }
                                    }

                                    if (!empty($promediotalleralumnost[$j][$i])) {
                                        $promsetalumnost = array_diff($promediotalleralumnost[$j][$i], array('0'));
                                    } else {
                                        $promsetalumnost = 0;
                                    }


                                    if (is_array($promsetalumnost)) {
                                        if (count($promsetalumnost) > 0) {

                                            if ($datoscurso[0]['aproxAsignatura'] == 1) {
                                                $promtalleralumnost[$i]['alumnos'][$j]['nota'] = round(array_sum($promsetalumnost) / count($promsetalumnost));
                                                $promtalleralumnost[$i]['alumnos'][$j]['nota'] = intval($promtalleralumnost[$i]['alumnos'][$j]['nota']);

                                            } else {
                                                $promtalleralumnost[$i]['alumnos'][$j]['nota'] = intval(array_sum($promsetalumnost) / count($promsetalumnost));
                                            }
                                        } else {
                                            $promtalleralumnost[$i]['alumnos'][$j]['nota'] = 0;
                                        }
                                    } else {
                                        $promtalleralumnost[$i]['alumnos'][$j]['nota'] = 0;
                                    }


                                } else {
                                    $promtalleralumnost[$i]['alumnos'][$j]['nota'] = 0;
                                }
                            } else {
                                //$promtalleralumnoss[$j][$i] = array();
                            }


                        }//Fin if talleres


                    }//Fin for Alumnos


                }


            }
        }


        //Fin Taller por alumnos

        //Taller Por Segmento Curso Completo
        $datostallersem = $modeloasignatura->gettaller2($idasignatura, $idperiodo, array(1, 2), 1, array(3));
        $datostallersemseg = $modeloasignatura->gettaller2($idasignatura, $idperiodo, array(1, 2), 1, array(4));
        $datostallersemter = $modeloasignatura->gettaller2($idasignatura, $idperiodo, array(1, 2), 1, array(5));
        $largotallerprimero = count($datostallersem);
        $largotallersegundo = count($datostallersemseg);
        $largotallertercero = count($datostallersemter);

        $promediotaller = array();

        //Primer Trimestre
        if ($largotallerprimero > 0) {

            for ($i = 0; $i < $largotallerprimero; $i++) {
                if (!empty($datostallersem[$i]['idConfiguracionTaller'])) {
                    $validataller[$i] = $datostallersem[$i]['idAsignaturaTaller'];
                    for ($j = 0; $j < $largoalumnos; $j++) {

                        $datosalumnostaller = $modelonotas->listarpromedioalumnotaller($listadealumnos[$j]['idAlumnos'], $idperiodo, 1, $idcurso, $datostallersem[$i]['idAsignatura']);

                        for ($k = 0; $k < count($datosalumnostaller); $k++) {
                            if ($datostallersem[$i]['idAsignatura'] == $datosalumnostaller[$k]['idAsignatura']) {
                                if ($datosalumnostaller[$k]['coef'] == 2) {
                                    if ($datosalumnostaller[$k]['nota'] > 0) {
                                        $promediotaller[$j][$i][] = $datosalumnostaller[$k]['nota'];
                                        $promediotaller[$j][$i][] = $datosalumnostaller[$k]['nota'];

                                    } else {
                                        $promediotaller[$j][$i][] = 0;
                                        $promediotaller[$j][$i][] = 0;
                                    }

                                } else {
                                    if ($datosalumnostaller[$k]['nota'] > 0) {
                                        $promediotaller[$j][$i][] = $datosalumnostaller[$k]['nota'];
                                    } else {
                                        $promediotaller[$j][$i][] = 0;
                                    }

                                }


                            }

                        }


                        //Primer Trimestre
                        $promtaller[$i]['idAsignatura'] = $datostallersem[$i]['idAsignaturaTaller'];
                        $promtaller[$i]['coef'] = 1;
                        $promtaller[$i]['tiempo'] = 3;
                        $promtaller[$i]['forma'] = $datostallersem[$i]['forma'];
                        $promtaller[$i]['taller'] = 1;
                        $promtaller[$i]['fechaEvaluacion'] = $datostallersem[$i]['nombreAsignatura'];
                        $promtaller[$i]['idEvaluacion'] = "taller" . $datostallersem[$i]['idConfiguracionTaller'];


                        $promtaller[$i]['porcentaje'] = $datostallersem[$i]['porcentaje'];

                        if (!empty($promediotaller[$j][$i])) {
                            $promset = array_diff($promediotaller[$j][$i], array('0'));
                        } else {
                            $promset = 0;
                        }

                        if (is_array($promset)) {
                            if (count($promset) > 0) {
                                if ($datoscurso[0]['aproxAsignatura'] == 1) {
                                    $promtaller[$i]['alumnos'][$j]['nota'] = round(array_sum($promset) / count($promset));

                                } else {
                                    $promtaller[$i]['alumnos'][$j]['nota'] = intval(array_sum($promset) / count($promset));
                                }
                            } else {
                                $promtaller[$i]['alumnos'][$j]['nota'] = 0;
                            }

                        } else {
                            $promtaller[$i]['alumnos'][$j]['nota'] = 0;
                        }


                    }

                }


            }
        }

        //Segundo Trimestre
        if ($largotallersegundo > 0) {

            for ($i = 0; $i < $largotallersegundo; $i++) {
                if (!empty($datostallersemseg[$i]['idConfiguracionTaller'])) {
                    $validatallers[$i] = $datostallersemseg[$i]['idAsignaturaTaller'];
                    for ($j = 0; $j < $largoalumnos; $j++) {
                        $promediofinal = 0;
                        $datosalumnostallers = $modelonotas->listarpromedioalumnotaller($listadealumnos[$j]['idAlumnos'], $idperiodo, 2, $idcurso, $datostallersemseg[$i]['idAsignatura']);

                        for ($k = 0; $k < count($datosalumnostallers); $k++) {
                            if ($datostallersemseg[$i]['idAsignatura'] == $datosalumnostallers[$k]['idAsignatura']) {
                                if ($datosalumnostallers[$k]['coef'] == 2) {
                                    if ($datosalumnostallers[$k]['nota'] > 0) {
                                        $promediotallers[$j][$i][] = $datosalumnostallers[$k]['nota'];
                                        $promediotallers[$j][$i][] = $datosalumnostallers[$k]['nota'];

                                    } else {
                                        $promediotallers[$j][$i][] = 0;
                                        $promediotallers[$j][$i][] = 0;
                                    }

                                } else {
                                    if ($datosalumnostallers[$k]['nota'] > 0) {
                                        $promediotallers[$j][$i][] = $datosalumnostallers[$k]['nota'];
                                    } else {
                                        $promediotallers[$j][$i][] = 0;
                                    }

                                }


                            }


                        }
                        //Segundo Semestre
                        $promtallers[$i]['idAsignatura'] = $datostallersemseg[$i]['idAsignaturaTaller'];
                        $promtallers[$i]['coef'] = 1;
                        $promtallers[$i]['tiempo'] = 4;
                        $promtallers[$i]['forma'] = $datostallersemseg[$i]['forma'];
                        $promtallers[$i]['taller'] = 1;
                        $promtallers[$i]['fechaEvaluacion'] = $datostallersemseg[$i]['nombreAsignatura'];
                        $promtallers[$i]['idEvaluacion'] = "taller" . $datostallersemseg[$i]['idConfiguracionTaller'];


                        $promtallers[$i]['porcentaje'] = $datostallersemseg[$i]['porcentaje'];

                        if (!empty($promediotallers[$j][$i])) {
                            $promsets = array_diff($promediotallers[$j][$i], array('0'));
                        } else {
                            $promsets = 0;
                        }

                        if (is_array($promsets)) {
                            if (count($promsets) > 0) {
                                if ($datoscurso[0]['aproxAsignatura'] == 1) {
                                    $promtallers[$i]['alumnos'][$j]['nota'] = round(array_sum($promsets) / count($promsets));

                                } else {
                                    $promtallers[$i]['alumnos'][$j]['nota'] = intval(array_sum($promsets) / count($promsets));
                                }
                            } else {
                                $promtallers[$i]['alumnos'][$j]['nota'] = 0;
                            }

                        } else {
                            $promtallers[$i]['alumnos'][$j]['nota'] = 0;
                        }

                    }

                }


            }
        }

        //Tercer Trimestre
        if ($largotallertercero > 0) {

            for ($i = 0; $i < $largotallertercero; $i++) {
                if (!empty($datostallersemter[$i]['idConfiguracionTaller'])) {
                    $validatallert[$i] = $datostallersemter[$i]['idAsignaturaTaller'];
                    for ($j = 0; $j < $largoalumnos; $j++) {
                        $promediofinal = 0;
                        $datosalumnostallert = $modelonotas->listarpromedioalumnotaller($listadealumnos[$j]['idAlumnos'], $idperiodo, 2, $idcurso, $datostallersemter[$i]['idAsignatura']);

                        for ($k = 0; $k < count($datosalumnostallert); $k++) {
                            if ($datostallersemter[$i]['idAsignatura'] == $datosalumnostallert[$k]['idAsignatura']) {
                                if ($datosalumnostallert[$k]['coef'] == 2) {
                                    if ($datosalumnostallert[$k]['nota'] > 0) {
                                        $promediotallert[$j][$i][] = $datosalumnostallert[$k]['nota'];
                                        $promediotallert[$j][$i][] = $datosalumnostallert[$k]['nota'];

                                    } else {
                                        $promediotallert[$j][$i][] = 0;
                                        $promediotallert[$j][$i][] = 0;
                                    }

                                } else {
                                    if ($datosalumnostallert[$k]['nota'] > 0) {
                                        $promediotallert[$j][$i][] = $datosalumnostallert[$k]['nota'];
                                    } else {
                                        $promediotallert[$j][$i][] = 0;
                                    }

                                }


                            }


                        }
                        //Tercer Semestre
                        $promtallert[$i]['idAsignatura'] = $datostallersemter[$i]['idAsignaturaTaller'];
                        $promtallert[$i]['coef'] = 1;
                        $promtallert[$i]['tiempo'] = 5;
                        $promtallert[$i]['forma'] = $datostallersemter[$i]['forma'];
                        $promtallert[$i]['taller'] = 1;
                        $promtallert[$i]['fechaEvaluacion'] = $datostallersemter[$i]['nombreAsignatura'];
                        $promtallert[$i]['idEvaluacion'] = "taller" . $datostallersemter[$i]['idConfiguracionTaller'];


                        $promtallert[$i]['porcentaje'] = $datostallersemter[$i]['porcentaje'];

                        if (!empty($promediotallert[$j][$i])) {
                            $promsett = array_diff($promediotallert[$j][$i], array('0'));
                        } else {
                            $promsett = 0;
                        }

                        if (is_array($promsett)) {
                            if (count($promsett) > 0) {
                                if ($datoscurso[0]['aproxAsignatura'] == 1) {
                                    $promtallert[$i]['alumnos'][$j]['nota'] = round(array_sum($promsett) / count($promsett));

                                } else {
                                    $promtallert[$i]['alumnos'][$j]['nota'] = intval(array_sum($promsett) / count($promsett));
                                }
                            } else {
                                $promtallert[$i]['alumnos'][$j]['nota'] = 0;
                            }

                        } else {
                            $promtallert[$i]['alumnos'][$j]['nota'] = 0;
                        }

                    }

                }


            }
        }

        $resultado['notas'] = $modeloprueba->listapruebasasignatura($idasignatura, $idcurso, $idperiodo);

        $largoresultado = count($resultado['notas']);


        //Si la Asignatura es Taller
        if ($resultadoasignatura[0]['tipoAsignatura'] == 2) {

            $datostalleres = $modeloasignatura->gettallerconfiguracion($resultadoasignatura[0]['idAsignaturaCurso']);
            $modelotallerdetalle = new Application_Model_DbTable_Asignaturascursos();
            if ($datostalleres) {
                for ($j = 0; $j < count($datostalleres); $j++) {
                    if ($datostalleres[$j]['tipoAjuste'] == 1) {
                        //El Taller es para todos los alumnos
                        for ($i = 0; $i < $largoresultado; $i++) {

                            //$resultado['notas'][$i]['fechaEvaluacion'] = $this->datetranslate($resultado['notas'][$i]['fechaEvaluacion']);
                            $resultado['notas'][$i]['alumnos'] = $modelonotas->getnotasasignatura($resultado['notas'][$i]['idEvaluacion'], $idcurso, $idperiodo);
                            //si el resultado de las notas no coinciden con la cantidad de alumnos se realiza una validacion de las notas de esa evaluacion
                            if (count($resultado[$i]['alumnos']) != count($listadealumnos)) {
                                $date = new DateTime;
                                $fechaactual = $date->format('Y-m-d');

                                for ($j = 0; $j < count($listadealumnos); $j++) {
                                    if ($modelonotas->validarnotaalumno($listadealumnos[$j]['idAlumnos'], $resultado['notas'][$i]['idEvaluacion'], $idasignatura, $idcurso, $idperiodo)) {
                                        //Creamos las notas a los alumnos correspondientes
                                        $modelonotas->agregar($listadealumnos[$j]['idAlumnos'], $idasignatura, $idcurso, 0, $resultado['notas'][$i]['idCuenta'], $resultado['notas'][$i]['idEvaluacion'], $fechaactual, $idperiodo);

                                    }


                                }

                                //recargamos las notas
                                $resultado['notas'][$i]['alumnos'] = $modelonotas->getnotasasignatura($resultado['notas'][$i]['idEvaluacion'], $idcurso, $idperiodo);
                            }


                        }
                    } elseif ($datostalleres[$j]['tipoAjuste'] == 2) {


                        for ($l = 0; $l < count($listadealumnos); $l++) {

                            $resultadotaller = $modeloasignatura->gettallerdetalles($datostalleres[$j]['idConfiguracionTaller'], $listadealumnos[$l]['idAlumnos']);

                            if ($resultadotaller) { //Se crean los datos del alumno al que corresponden las notas
                                $listadealumnos[$l]['auth'] = 1;
                                if ($resultadotaller[0]['tiempoOpcion'] == 3) {
                                    $listadealumnos[$l]['tiempop'] = 3;
                                } elseif ($resultadotaller[0]['tiempoOpcion'] == 4) {
                                    $listadealumnos[$l]['tiempos'] = 4;
                                } elseif ($resultadotaller[0]['tiempoOpcion'] == 5) {
                                    $listadealumnos[$l]['tiempot'] = 5;
                                }


                            }
                        }
                        $resultado['notas'][0]['authalumnos'] = true;

                    }

                }

            }


            //Asignamos las notas que corresponden a los alumnos.
            for ($i = 0; $i < count($resultado['notas']); $i++) {
                $resultado['notas'][$i]['fechaEvaluacion'] = $this->datetranslate($resultado['notas'][$i]['fechaEvaluacion']);
                $resultado['notas'][$i]['alumnos'] = $modelonotas->getnotasasignatura($resultado['notas'][$i]['idEvaluacion'], $idcurso, $idperiodo);

                //si el resultado de las notas no coinciden con la cantidad de alumnos se realiza una validacion de las notas de esa evaluacion
                if (count($resultado['notas'][$i]['alumnos']) != count($listadealumnos)) {
                    $date = new DateTime;
                    $fechaactual = $date->format('Y-m-d');

                    for ($j = 0; $j < count($listadealumnos); $j++) {

                        if ($modelonotas->validarnotaalumno($listadealumnos[$j]['idAlumnos'], $resultado['notas'][$i]['idEvaluacion'], $idasignatura, $idcurso, $idperiodo)) {
                            $modelonotas->agregar($listadealumnos[$j]['idAlumnos'], $idasignatura, $idcurso, 0, $resultado[$i]['idCuenta'], $resultado['notas'][$i]['idEvaluacion'], $fechaactual, $idperiodo);

                        }

                    }

                    //recargamos las notas
                    $resultado['notas'][$i]['alumnos'] = $modelonotas->getnotasasignatura($resultado['notas'][$i]['idEvaluacion'], $idcurso, $idperiodo);
                }

                //Asignamos la autorizacion a los alumnos correspondientes
                for ($j = 0; $j < count($listadealumnos); $j++) {
                    //si tiene autorizacion para el Primer Trimestre
                    if ($listadealumnos[$j]['auth'] == 1 && $resultado[$i]['tiempo'] == $listadealumnos[$j]['tiempop']) {
                        $resultado['notas'][$i]['alumnos'][$j]['auth'] = true;

                    }

                    //si tiene autorizacion para el Segundo Trimestre
                    if ($listadealumnos[$j]['auth'] == 1 && $resultado['notas'][$i]['tiempo'] == $listadealumnos[$j]['tiempos']) {
                        $resultado['notas'][$i]['alumnos'][$j]['auth'] = true;

                    }
                    //si tiene autorizacion para el Tercer Trimestre
                    if ($listadealumnos[$j]['auth'] == 1 && $resultado['notas'][$i]['tiempo'] == $listadealumnos[$j]['tiempot']) {
                        $resultado['notas'][$i]['alumnos'][$j]['auth'] = true;

                    }


                }


            }


        } elseif ($resultadoasignatura[0]['tipoAsignatura'] == 1 || $resultadoasignatura[0]['tipoAsignatura'] == 4 || $resultadoasignatura[0]['tipoAsignatura'] == 5) {
            for ($i = 0; $i < count($resultado['notas']); $i++) {
                $resultado['notas'][$i]['fechaEvaluacion'] = $this->datetranslate($resultado['notas'][$i]['fechaEvaluacion']);
                $resultado['notas'][$i]['alumnos'] = $modelonotas->getnotasasignatura($resultado['notas'][$i]['idEvaluacion'], $idcurso, $idperiodo);
                //si el resultado de las notas no coinciden con la cantidad de alumnos se realiza una validacion de las notas de esa evaluacion
                if (count($resultado['notas'][$i]['alumnos']) != count($listadealumnos)) {
                    $date = new DateTime;
                    $fechaactual = $date->format('Y-m-d');

                    for ($j = 0; $j < count($listadealumnos); $j++) {


                        if ($modelonotas->validarnotaalumno($listadealumnos[$j]['idAlumnos'], $resultado['notas'][$i]['idEvaluacion'], $idasignatura, $idcurso, $idperiodo)) {
                            $modelonotas->agregar($listadealumnos[$j]['idAlumnos'], $idasignatura, $idcurso, 0, $resultado['notas'][$i]['idCuenta'], $resultado['notas'][$i]['idEvaluacion'], $fechaactual, $idperiodo);

                        }
                    }

                    //recargamos las notas
                    $resultado['notas'][$i]['alumnos'] = $modelonotas->getnotasasignatura($resultado['notas'][$i]['idEvaluacion'], $idcurso, $idperiodo);
                }


            }

        }


        //Realizamos la Llamada a la funcion getpromedios y ontenemos los promedios
        $resultadopromedios = $this->getpromedioperiodotrimAction();


        //Examenes
        $examenes = new Zend_Session_Namespace('examen');
        $examen = $examenes->examen;
        $largoresultado = count($resultado['notas']);
        if ($examen == 1) {

            for ($i = 0; $i < $largoresultado; $i++) {
                if ($resultado['notas'][$i]['tipoNota'] == 2 && $resultado['notas'][$i]['tiempo'] == 6) {
                    $criterio = $resultado['notas'][$i]['criterio'];


                    if ($resultadopromedios) {
                        for ($j = 0; $j < count($resultado['notas'][$i]['alumnos']); $j++) {
                            if ($resultadopromedios[$j]['final'] < $criterio) {
                                $resultado['notas'][$i]['alumnos'][$j]['auth'] = true;
                                if ($resultado['notas'][$i]['alumnos'][$j]['nota'] > 0) {


                                    $totalex = 100 - $resultado['notas'][$i]['porcentajeExamen'];
                                    $sumaex = $resultado['notas'][$i]['alumnos'][$j]['nota'] * ($resultado['notas'][$i]['porcentajeExamen'] / 100);
                                    if ($datoscurso[0]['aproxExamen'] == 1) {
                                        $resultadopromedios[$j]['finalex'] = round(($resultadopromedios[$j]['final'] * ($totalex / 100)) + $sumaex);
                                    } else {
                                        $resultadopromedios[$j]['finalex'] = intval(($resultadopromedios[$j]['final'] * ($totalex / 100)) + $sumaex);
                                    }


                                } else {
                                    $resultadopromedios[$j]['finalex'] = $resultadopromedios[$j]['final'];
                                }


                            } else {
                                $resultado['notas'][$i]['alumnos'][$j]['auth'] = false;
                                $resultadopromedios[$j]['finalex'] = $resultadopromedios[$j]['final'];
                            }


                        }

                    }


                }

            }

        }


        //lista de alumnos


        $asignaturamodelo = new Application_Model_DbTable_Asignaturascursos();
        $dato = $this->_getParam('id');
        $resultadoconcepto = $asignaturamodelo->getasignaturaconcepto($dato, $idcurso, $idperiodo);

        for ($i = 0; $i < count($resultado['notas']); $i++) {
            for ($j = 0; $j < count($resultado['notas'][$i]['alumnos']); $j++) {

                for ($k = 0; $k < count($resultadoconcepto); $k++) {
                    if ($resultadoconcepto[$k]['desde'] <= $resultado['notas'][$i]['alumnos'][$j]['nota'] && $resultadoconcepto[$k]['hasta'] >= $resultado['notas'][$i]['alumnos'][$j]['nota']) {
                        //if ($resultadoconcepto[$k]['notaconcepto'] == $resultado['notas'][$i]['alumnos'][$j]['nota']) {
                        $resultado['notas'][$i]['alumnos'][$j]['notaconconcepto'] = $resultadoconcepto[$k]['concepto'];
                    }


                }

            }

        }


        //Pasamos los Resultados del taller a los datos de notas
        //Primer Trimestre
        $largopromtaller = count($promtaller);
        if ($largopromtaller > 0) {
            for ($i = 0; $i < $largopromtaller; $i++) {
                $resultado['notas'][] = $promtaller[$i];
            }
        }
        //Segundo Trimestre
        $largopromtallers = count($promtallers);
        if ($largopromtallers > 0) {
            for ($i = 0; $i < $largopromtallers; $i++) {
                $resultado['notas'][] = $promtallers[$i];
            }
        }

        //TercerTrimestre
        $largopromtallert = count($promtallert);
        if ($largopromtallert > 0) {
            for ($i = 0; $i < $largopromtallert; $i++) {
                $resultado['notas'][] = $promtallert[$i];
            }
        }

        if (count($promtalleralumnos) > 0) {
            foreach ($promtalleralumnos as $a => $j) {
                if (!is_null($promtalleralumnos[$a])) {
                    $resultado['notas'][] = $promtalleralumnos[$a];
                }

            }
        }


        if (count($promtalleralumnoss) > 0) {
            foreach ($promtalleralumnoss as $a => $j) {
                if (!is_null($promtalleralumnoss[$a])) {
                    $resultado['notas'][] = $promtalleralumnoss[$a];
                }

            }
        }

        if (count($promtalleralumnost) > 0) {
            foreach ($promtalleralumnost as $a => $j) {
                if (!is_null($promtalleralumnost[$a])) {
                    $resultado['notas'][] = $promtalleralumnost[$a];
                }

            }
        }


        //Asignamos los Promedios al Resultado
        if ($resultadopromedios) {
            $resultado['promedios'] = $resultadopromedios;
        }


        $this->_helper->json($resultado);


    }

    public function validafirmaAction()
    {

        $token = $this->_getParam('id', 0);
        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        if ($rol == 2) {

            if (empty($user)) {
                echo Zend_Json::encode(array('response' => 'errorsesion'));
            } else {
                try {
                    $modelo_cuenta = new Application_Model_DbTable_Cuentas();
                    $datos_docente = $modelo_cuenta->getusuario($user, $idperiodo);
                    $rutset = $datos_docente['usuario'];
                    if (!empty($rutset)) {

                        $rest = substr($rutset, 0, -1);
                        $ultimo = substr($rutset, -1);
                        $formatofinal = $rest . '-' . $ultimo;

                    }

                    $fecha = (new \DateTime('America/Santiago'))->format('Y-m-d\TH:i:sP');

                    //API URL Validar Firma Docente
                    $url = 'https://claveunicagobcl.firebaseapp.com/verifyOTPFromSimple?rut=' . $formatofinal . '&otp=' . $token . '&DateWithTimeZone=' . $fecha . '';
                    $ch = curl_init($url);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    $result = curl_exec($ch);
                    curl_close($ch);
                    $resultadodeco = json_decode($result);
                    if ($resultadodeco[0]->OTPVERIFY) {
                        $results['ok'] = true;
                        $results['message'] = 'Firma Válida';

                    } else {
                        $results['ok'] = false;
                        $results['message'] = 'Firma No Válida';

                    }


                } catch (Exception $e) {

                    $results['ok'] = false;
                    $results['message'] = 'Problemas de conexión';

                }

            }

        } else {
            $results['ok'] = false;
            $results['message'] = 'No Autorizado';

        }

        $this->_helper->json($results);


    }

    public
    function getpromedioperiodopretrimAction($opc, $tip, $idal, $idas)
    {
        $this->_helper->viewRenderer->setNeverRender(true);
        $this->_helper->ViewRenderer->setNoRender(true);
        $this->_helper->Layout->disableLayout();

        $idcursos = new Zend_Session_Namespace('id_curso');
        $id = $idcursos->id_curso;
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $modelcurso = new Application_Model_DbTable_Cursos();
        $modelnotas = new Application_Model_DbTable_Notas();
        $modelasignatura = new Application_Model_DbTable_Asignaturascursos();
        $modeloprueba = new Application_Model_DbTable_Pruebas();


        if ($opc) {

            if ($tip == 0) {
                $asignaturas = new Zend_Session_Namespace('idAsignatura');
                $idasignatura = $asignaturas->idAsignatura;
                $listaalumnos = $modelnotas->listaralumnoscurso($id, $idperiodo);

            } elseif ($tip == 2) {
                $idasignatura = $idas;
                $idalumno = $idal;
                $listaalumnos = $modelnotas->listaralumno($idalumno, $id, $idperiodo);
            }

        } else {
            $tipo = $this->_getParam('t', 0);
            if ($tipo == 0 || $tipo == null) {
                $asignaturas = new Zend_Session_Namespace('idAsignatura');
                $idasignatura = $asignaturas->idAsignatura;
                $listaalumnos = $modelnotas->listaralumnoscurso($id, $idperiodo);

            } elseif ($tipo == 2) {
                $idasignatura = $this->_getParam('as', 0);
                $idalumno = $this->_getParam('id', 0);
                $listaalumnos = $modelnotas->listaralumno($idalumno, $id, $idperiodo);
            }
        }


        $datoscurso = $modelcurso->listarcursoid($id, $idperiodo);
        $datosasignaturas = $modelasignatura->listarporasignaturapre($id, $idperiodo);
        $agregadecimas = false;
        $largoalumnos = count($listaalumnos);


        if ($largoalumnos == 0) {
            echo "<script type=\"text/javascript\">alert(\"Sin Notas\");</script>";
            echo "<script>parent.$.fancybox.close();</script>";
            exit;


        }

        for ($i = 0; $i < $largoalumnos; $i++) {

            $valores = $modelnotas->listarnotasporalumnoasignatura($listaalumnos[$i]['idAlumnos'], $idperiodo, $id, $idasignatura);
            if ($valores != '' || !empty($valores)) {
                $listaalumnos[$i]['listanotas'] = $valores;
            } else {
                $listaalumnos[$i]['listanotas'] = 0;

            }


        }


        //Agregamos cantidad de notas por alumno y la asignamos a cada aisgnatura

        if (!empty($datosasignaturas)) {
            $largoasignatura = count($datosasignaturas);
            for ($i = 0; $i < $largoasignatura; $i++) {

                $datoscuenta[$i] = $modelnotas->listarcantidadnotasanual($id, $idperiodo, $datosasignaturas[$i]['idAsignatura']);
                if (empty($datoscuenta[$i]) && $datosasignaturas[$i]['tipoAsignatura'] == 1) {
                    $datosasig[$i] = 0;
                } else {
                    $largonotas = count($datoscuenta[$i]);
                    $datosasig[$i] = 0;


                    for ($j = 0; $j < $largonotas; $j++) {

                        if ($datoscuenta[$i][$j]['coef'] == 2) {
                            $datosasig[$i] += 2;
                        } else {
                            $datosasig[$i] += 1;
                        }

                    }


                }


            }
        } else {
            echo "<script type=\"text/javascript\">alert(\"El Curso no Posee Asignaturas en el periodo\");</script>";
            echo "<script>parent.$.fancybox.close();</script>";
            exit;

        }


        if (!empty($listaalumnos)) {
            $r = 0;

            for ($i = 0; $i < $largoalumnos; $i++) {

                $r = 0;

                for ($j = 0; $j < $largoasignatura; $j++) {

                    $promtalleraux = array();
                    $porcentajetaller = array();
                    $sumataller = 0;
                    $promtallerauxs = array();
                    $porcentajetallers = array();
                    $porcentajetallert = array();
                    $sumatallers = 0;
                    $sumatallert = 0;
                    $row = $datosasig[$j];


                    $promedio = 0;
                    $contador = 0;
                    $contadorpromedio = 0;
                    $promedios = 0;
                    $contadorpromedios = 0;
                    $promediot = 0;
                    $contadorpromediot = 0;
                    if ($row != 0) {
                        $largolistanota = count($listaalumnos[$i]['listanotas']);
                        for ($z = 0; $z < $largolistanota; $z++) {
                            $contadoraux = 0;

                            if ($datosasignaturas[$j]['idAsignatura'] == $listaalumnos[$i]['listanotas'][$z]['idAsignatura']) {


                                //Primer Trimestre
                                if ($listaalumnos[$i]['listanotas'][$z]['tiempo'] == 3) {

                                    if ($listaalumnos[$i]['listanotas'][$z]['coef'] == 2) {
                                        $contador += 2;
                                        if ($listaalumnos[$i]['listanotas'][$z]['nota'] == 0 || empty($listaalumnos[$i]['listanotas'][$z]['nota']) || $listaalumnos[$i]['listanotas'][$z]['nota'] == NULL || $listaalumnos[$i]['listanotas'][$z]['nota'] == '') {
                                            $promedio += 0;
                                            $contadorpromedio += 0;


                                        } else {
                                            $promedio += ($listaalumnos[$i]['listanotas'][$z]['nota'] + $listaalumnos[$i]['listanotas'][$z]['nota']);
                                            $contadorpromedio += 2;

                                        }

                                    } else {
                                        $contador += 1;

                                        if ($listaalumnos[$i]['listanotas'][$z]['nota'] == 0 || empty($listaalumnos[$i]['listanotas'][$z]['nota']) || $listaalumnos[$i]['listanotas'][$z]['nota'] == NULL || $listaalumnos[$i]['listanotas'][$z]['nota'] == '') {
                                            if (!empty($listaalumnos[$i]["listanotas"][$z]["forma"])) {
                                                if ($listaalumnos[$i]["listanotas"][$z]["nota"] == 0) {

                                                    $promedio += 0;
                                                    $contadorpromedio += 0;

                                                }

                                            } else {
                                                $promedio += 0;
                                                $contadorpromedio += 0;


                                            }


                                        } else {
                                            if (!empty($listaalumnos[$i]["listanotas"][$z]["forma"]) && $listaalumnos[$i]["listanotas"][$z]["forma"] == 2) {

                                                $contadorauxtaller = true;
                                                $promtalleraux[] = $listaalumnos[$i]['listanotas'][$z]['nota'];
                                                $porcentajetaller[] = $listaalumnos[$i]["listanotas"][$z]["porcentaje"];


                                            } else {

                                                $promedio += $listaalumnos[$i]['listanotas'][$z]['nota'];
                                                $contadorpromedio += 1;


                                            }


                                        }


                                    }
                                }
                                if ($listaalumnos[$i]['listanotas'][$z]['tiempo'] == 4) {


                                    if ($listaalumnos[$i]['listanotas'][$z]['coef'] == 2) {
                                        $contador += 2;
                                        if ($listaalumnos[$i]['listanotas'][$z]['nota'] == 0 || empty($listaalumnos[$i]['listanotas'][$z]['nota']) || $listaalumnos[$i]['listanotas'][$z]['nota'] == NULL || $listaalumnos[$i]['listanotas'][$z]['nota'] == '') {
                                            $promedios += 0;
                                            $contadorpromedios += 0;


                                        } else {
                                            $promedios += ($listaalumnos[$i]['listanotas'][$z]['nota'] + $listaalumnos[$i]['listanotas'][$z]['nota']);
                                            $contadorpromedios += 2;

                                        }

                                    } else {


                                        $contador += 1;

                                        if ($listaalumnos[$i]['listanotas'][$z]['nota'] == 0 || empty($listaalumnos[$i]['listanotas'][$z]['nota']) || $listaalumnos[$i]['listanotas'][$z]['nota'] == NULL || $listaalumnos[$i]['listanotas'][$z]['nota'] == '') {
                                            if (!empty($listaalumnos[$i]["listanotas"][$z]["forma"])) {
                                                if ($listaalumnos[$i]["listanotas"][$z]["nota"] == 0) {

                                                    $promedios += 0;
                                                    $contadorpromedios += 0;

                                                }

                                            } else {
                                                $promedios += 0;
                                                $contadorpromedios += 0;


                                            }


                                        } else {
                                            if (!empty($listaalumnos[$i]["listanotas"][$z]["forma"]) && $listaalumnos[$i]["listanotas"][$z]["forma"] == 2) {

                                                $contadorauxtallers = true;
                                                $promtallerauxs[] = $listaalumnos[$i]['listanotas'][$z]['nota'];
                                                $porcentajetallers[] = $listaalumnos[$i]["listanotas"][$z]["porcentaje"];


                                            } else {

                                                $promedios += $listaalumnos[$i]['listanotas'][$z]['nota'];
                                                $contadorpromedios += 1;


                                            }


                                        }


                                    }
                                }
                                if ($listaalumnos[$i]['listanotas'][$z]['tiempo'] == 5) {


                                    if ($listaalumnos[$i]['listanotas'][$z]['coef'] == 2) {
                                        $contador += 2;
                                        if ($listaalumnos[$i]['listanotas'][$z]['nota'] == 0 || empty($listaalumnos[$i]['listanotas'][$z]['nota']) || $listaalumnos[$i]['listanotas'][$z]['nota'] == NULL || $listaalumnos[$i]['listanotas'][$z]['nota'] == '') {
                                            $promediot += 0;
                                            $contadorpromediot += 0;


                                        } else {
                                            $promediot += ($listaalumnos[$i]['listanotas'][$z]['nota'] + $listaalumnos[$i]['listanotas'][$z]['nota']);
                                            $contadorpromediot += 2;

                                        }

                                    } else {


                                        $contador += 1;

                                        if ($listaalumnos[$i]['listanotas'][$z]['nota'] == 0 || empty($listaalumnos[$i]['listanotas'][$z]['nota']) || $listaalumnos[$i]['listanotas'][$z]['nota'] == NULL || $listaalumnos[$i]['listanotas'][$z]['nota'] == '') {
                                            if (!empty($listaalumnos[$i]["listanotas"][$z]["forma"])) {
                                                if ($listaalumnos[$i]["listanotas"][$z]["nota"] == 0) {

                                                    $promediot += 0;
                                                    $contadorpromediot += 0;

                                                }

                                            } else {
                                                $promediot += 0;
                                                $contadorpromediot += 0;


                                            }


                                        } else {
                                            if (!empty($listaalumnos[$i]["listanotas"][$z]["forma"]) && $listaalumnos[$i]["listanotas"][$z]["forma"] == 2) {

                                                $contadorauxtallert = true;
                                                $promtallerauxt[] = $listaalumnos[$i]['listanotas'][$z]['nota'];
                                                $porcentajetallert[] = $listaalumnos[$i]["listanotas"][$z]["porcentaje"];


                                            } else {

                                                $promediot += $listaalumnos[$i]['listanotas'][$z]['nota'];
                                                $contadorpromediot += 1;


                                            }


                                        }


                                    }
                                }


                                //Promedios por notas

                                if ($contador == $row) {
                                    $promedioaux = 0;
                                    $promedioauxs = 0;

                                    if ($contadorpromedio != 0 && $promedio != 0) {
                                        $promedioaux = 0;
                                        //SI el Taller es por porcentaje
                                        if ($contadorauxtaller) {
                                            $totalporcentaje = 100;
                                            if (count($promtalleraux) > 1) {
                                                for ($t = 0; $t < count($promtalleraux); $t++) {
                                                    $totalporcentaje = $totalporcentaje - $porcentajetaller[$t];
                                                    $sumataller += $promtalleraux[$t] * ($porcentajetaller[$t] / 100);
                                                }

                                            } else {
                                                $totalporcentaje = 100 - $porcentajetaller[0];
                                                $sumataller = $promtalleraux[0] * ($porcentajetaller[0] / 100);

                                            }


                                            if ($datoscurso[0]['aproxAsignatura'] == 1) {//Aproxima
                                                $promedioaux = round($promedio / $contadorpromedio);
                                                $promedioaux = ($promedioaux * ($totalporcentaje / 100)) + $sumataller;
                                                $promedioaux = round($promedioaux);


                                            } else {
                                                $promedioaux = round($promedio / $contadorpromedio);
                                                $promedioaux = ($promedioaux * ($totalporcentaje / 100)) + $sumataller;
                                                $promedioaux = intval($promedioaux);
                                            }

                                        } else {

                                            if ($datoscurso[0]['aproxAsignatura'] == 1) {//Aproxima
                                                $promedioaux = round($promedio / $contadorpromedio);
                                                $promfinal[$i][] = $promedioaux;

                                            } else {
                                                $promedioaux = intval($promedio / $contadorpromedio);
                                                $promfinal[$i][] = $promedioaux;
                                            }

                                        }


                                        $promedioalumnos[$i]['primero'] = $promedioaux;


                                    } else {
                                        $promedioalumnos[$i]['primero'] = 0;
                                        $promfinal[$i][] = 0;


                                    }


                                    //Segundo Trimestre
                                    if ($contadorpromedios != 0 && $promedios != 0) {
                                        $promedioauxs = 0;
                                        //SI el Taller es por porcentaje
                                        if ($contadorauxtallers) {
                                            $totalporcentajes = 100;
                                            if (count($promtallerauxs) > 1) {
                                                for ($t = 0; $t < count($promtallerauxs); $t++) {
                                                    $totalporcentajes = $totalporcentajes - $porcentajetallers[$t];
                                                    $sumatallers += $promtallerauxs[$t] * ($porcentajetallers[$t] / 100);
                                                }

                                            } else {
                                                $totalporcentajes = 100 - $porcentajetallers[0];
                                                $sumatallers = $promtallerauxs[0] * ($porcentajetallers[0] / 100);

                                            }


                                            if ($datoscurso[0]['aproxAsignatura'] == 1) {//Aproxima
                                                $promedioauxs = round($promedios / $contadorpromedios);
                                                $promedioauxs = ($promedioauxs * ($totalporcentajes / 100)) + $sumatallers;
                                                $promedioauxs = round($promedioauxs);


                                            } else {
                                                $promedioauxs = round($promedios / $contadorpromedios);
                                                $promedioauxs = ($promedioauxs * ($totalporcentajes / 100)) + $sumatallers;
                                                $promedioauxs = intval($promedioauxs);

                                            }

                                        } else {

                                            if ($datoscurso[0]['aproxAsignatura'] == 1) {//Aproxima
                                                $promedioauxs = round($promedios / $contadorpromedios);
                                                $promfinal[$i][] = $promedioauxs;


                                            } else {
                                                $promedioauxs = intval($promedios / $contadorpromedios);
                                                $promfinal[$i][] = $promedioauxs;

                                            }
                                        }


                                        $promedioalumnos[$i]['segundo'] = $promedioauxs;


                                    } else {
                                        $promedioalumnos[$i]['segundo'] = 0;
                                        $promfinal[$i][] = 0;


                                    }

                                    //Tercer Trimestre
                                    if ($contadorpromediot != 0 && $promediot != 0) {
                                        $promedioauxt = 0;
                                        //SI el Taller es por porcentaje
                                        if ($contadorauxtallert) {
                                            $totalporcentajet = 100;
                                            if (count($promtallerauxt) > 1) {
                                                for ($t = 0; $t < count($promtallerauxt); $t++) {
                                                    $totalporcentajet = $totalporcentajet - $porcentajetallert[$t];
                                                    $sumatallert += $promtallerauxt[$t] * ($porcentajetallert[$t] / 100);
                                                }

                                            } else {
                                                $totalporcentajet = 100 - $porcentajetallert[0];
                                                $sumatallert = $promtallerauxs[0] * ($porcentajetallert[0] / 100);

                                            }


                                            if ($datoscurso[0]['aproxAsignatura'] == 1) {//Aproxima
                                                $promedioauxt = round($promediot / $contadorpromediot);
                                                $promedioauxt = ($promedioauxt * ($totalporcentajes / 100)) + $sumatallert;
                                                $promedioauxt = round($promedioauxt);


                                            } else {
                                                $promedioauxt = round($promediot / $contadorpromediot);
                                                $promedioauxt = ($promedioauxt * ($totalporcentajet / 100)) + $sumatallert;
                                                $promedioauxt = intval($promedioauxt);

                                            }

                                        } else {

                                            if ($datoscurso[0]['aproxAsignatura'] == 1) {//Aproxima
                                                $promedioauxt = round($promediot / $contadorpromediot);
                                                $promfinal[$i][] = $promedioauxt;


                                            } else {
                                                $promedioauxt = intval($promediot / $contadorpromediot);
                                                $promfinal[$i][] = $promedioauxt;

                                            }
                                        }


                                        $promedioalumnos[$i]['tercero'] = $promedioauxt;


                                    } else {
                                        $promedioalumnos[$i]['tercero'] = 0;
                                        $promfinal[$i][] = 0;


                                    }


                                    //Promedio Final De Asignatura
                                    $finalasignatura = 0;

                                    //Promedio Final De Asignatura

                                    $promedio_aux_asignatura = array($promedioaux, $promedioauxs, $promedioauxt);
                                    $resultado_auxiliar_asignatura = array_values(array_diff($promedio_aux_asignatura, array('0')));

                                    if (!empty($resultado_auxiliar_asignatura)) {


                                        if ($datoscurso[0]['aproxAnual'] == 1) {

                                            $finalasignatura = round((array_sum($resultado_auxiliar_asignatura)) / count($resultado_auxiliar_asignatura));
                                            if ($finalasignatura == 39 && $datoscurso[0]['rbd'] == '1864') {
                                                $finalasignatura = 40;
                                            }

                                        } else {
                                            $finalasignatura = intval((array_sum($resultado_auxiliar_asignatura)) / count($resultado_auxiliar_asignatura));
                                            if ($finalasignatura == 39 && $datoscurso[0]['rbd'] == '1864') {
                                                $finalasignatura = 40;
                                            }

                                        }

                                        if ($agregadecimas) {
                                            if ($finalasignatura >= 60) {
                                                $finalasignatura += 2;
                                                if ($finalasignatura > 70) {
                                                    $finalasignatura = 70;

                                                }

                                            }
                                        }
                                    } else {
                                        $finalasignatura = 0;
                                    }

                                    $promediototal[$i][] = $finalasignatura;


                                }// fin promedio por notas

                            }

                        }// fin for


                    } else {


                    } //fin if row


                } //fin for Asignaturas


                if ($promediototal[$i] != '' || $promediototal[$i] != null) {

                    if ($datoscurso[0]['aproxFinal'] == 1) {//Aproxima
                        $promedioalumnos[$i]['final'] = round(array_sum($promediototal[$i]) / count($promediototal[$i]));


                    } else {
                        $promedioalumnos[$i]['final'] = intval(array_sum($promediototal[$i]) / count($promediototal[$i]));


                    }


                } else {
                    $promedioalumnos[$i]['final'] = 0;
                }


            }// Fin for lista Alumnos


        }


        $resultadoconcepto = $modelasignatura->getasignaturaconcepto($idasignatura, $id, $idperiodo);
        if ($resultadoconcepto) {
            for ($i = 0; $i < count($promedioalumnos); $i++) {

                for ($k = 0; $k < count($resultadoconcepto); $k++) {

                    if ($promedioalumnos[$i]['primero'] == 0) {
                        $promedioalumnos[$i]['primeroconcepto'] = 0;
                    } else {
                        if ($resultadoconcepto[$k]['desde'] <= $promedioalumnos[$i]['primero'] && $resultadoconcepto[$k]['hasta'] >= $promedioalumnos[$i]['primero']) {
                            $promedioalumnos[$i]['primeroconcepto'] = $resultadoconcepto[$k]['concepto'];
                        }
                    }

                    if ($promedioalumnos[$i]['segundo'] == 0) {
                        $promedioalumnos[$i]['segundoconcepto'] = 0;

                    } else {
                        if ($resultadoconcepto[$k]['desde'] <= $promedioalumnos[$i]['segundo'] && $resultadoconcepto[$k]['hasta'] >= $promedioalumnos[$i]['segundo']) {
                            $promedioalumnos[$i]['segundoconcepto'] = $resultadoconcepto[$k]['concepto'];
                        }
                    }

                    if ($promedioalumnos[$i]['tercero'] == 0) {
                        $promedioalumnos[$i]['terceroconcepto'] = 0;

                    } else {
                        if ($resultadoconcepto[$k]['desde'] <= $promedioalumnos[$i]['tercero'] && $resultadoconcepto[$k]['hasta'] >= $promedioalumnos[$i]['tercero']) {
                            $promedioalumnos[$i]['terceroconcepto'] = $resultadoconcepto[$k]['concepto'];
                        }
                    }

                    if ($promedioalumnos[$i]['final'] == 0) {
                        $promedioalumnos[$i]['finalconcepto'] = 0;

                    } else {
                        if ($resultadoconcepto[$k]['desde'] <= $promedioalumnos[$i]['final'] && $resultadoconcepto[$k]['hasta'] >= $promedioalumnos[$i]['final']) {
                            $promedioalumnos[$i]['finalconcepto'] = $resultadoconcepto[$k]['concepto'];
                        }
                    }


                }


            }
        }


        if ($tipo == 2) {
            if ($datoscurso[0]['examen'] == 1) {
                //Examen a final de asignatura
                $datosexamenes = $modeloprueba->getexamen($id, $idperiodo, $idasignatura, 6);
                if ($datosexamenes[0]) {
                    $datosalumnosexamen = $modelnotas->getnotasexamenalumno($datosexamenes[0]['idEvaluacion'], $id, $idasignatura, $idperiodo, 6, $listaalumnos[0]['idAlumnos']);

                }

                if ($datosalumnosexamen) {


                    if ($promedioalumnos[0]['final'] > 0) {


                        $totalex = 100 - $datosexamenes[0]['porcentajeExamen'];

                        //nota d examen

                        $sumaex = $datosalumnosexamen[0]['nota'] * ($datosexamenes[0]['porcentajeExamen'] / 100);

                        if ($datoscurso[0]['aproxExamen'] == 1) {
                            $promedioalumnos[0]['finalex'] = round(($promedioalumnos[0]['final'] * ($totalex / 100)) + $sumaex);
                        } else {
                            $promedioalumnos[0]['finalex'] = intval(($promedioalumnos[0]['final'] * ($totalex / 100)) + $sumaex);
                        }


                    } else {
                        $promedioalumnos[0]['finalex'] = $promedioalumnos[0]['final'];
                    }


                }


            }
            $this->_helper->json($promedioalumnos);
        } else {
            return $promedioalumnos;
        }


    }


}
