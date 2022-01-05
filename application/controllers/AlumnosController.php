<?php

class AlumnosController extends Zend_Controller_Action
{

    public function init()
    {
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();
    }

    public function indexAction()
    {
        //$this->_helper->viewRenderer->setNoRender(true);
        //recuperamos el establecimiento guardado en sesion
        $establecimientos = new Zend_Session_NameSpace("establecimiento");
        $establecimiento = $establecimientos->establecimiento;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;

        //creo objeto que maneja la tabla Periodo
        $table = new Application_Model_DbTable_Alumnos();
        $cargo = new Zend_Session_NameSpace("cargo");
        if ($cargo->cargo == '1' || $cargo->cargo == '4') {

            $result = $table->listar($idperiodo);
        }
        if ($cargo->cargo == '3' || $cargo->cargo == '6') {
            $result = $table->listarestablecimiento($establecimiento, $idperiodo);
        }

        if ($cargo->cargo == '2') {

            $result = $table->listardocente($user, $establecimiento, $idperiodo);
        }

        $this->view->paginator = $result;

    }

    public function indexrAction()
    {

        $establecimientos = new Zend_Session_NameSpace("establecimiento");
        $establecimiento = $establecimientos->establecimiento;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $usuario = new Zend_Session_Namespace('id');
        $user = $usuario->id;

        $table = new Application_Model_DbTable_Alumnos();
        $cargo = new Zend_Session_NameSpace("cargo");


        if ($cargo->cargo == '1' || $cargo->cargo == '4') {
            $result = $table->listarretirado($idperiodo);
        }
        if ($cargo->cargo == '3' || $cargo->cargo == '6') {
            $result = $table->listarestablecimientoretirado($establecimiento, $idperiodo);
        }

        if ($cargo->cargo == '2') {

            $result = $table->listardocenteretirado($user, $establecimiento, $idperiodo);
        }

        $this->view->paginator = $result;

    }

    public function indexaAction()
    {

        //creo objeto que maneja la tabla Periodo
        $table = new Application_Model_DbTable_Apoderados();

        $result = $table->listar();

        $this->view->paginator = $result;
    }

    public function agregarAction()
    {


        $form = new Application_Form_Matricula();
        $form->setDecorators(
            array(
                'FormElements',
                'Form',
            )
        );

        $form->submit->setLabel('Agregar Alumno');
        $this->view->form = $form;


    }

    public function guardamatriculaAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            //Detectamos si es una llamada AJAX
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();
            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;

            $rutusuario = new Zend_Session_Namespace('id');
            $usuario = $rutusuario->id;

            $monitoreos = new Zend_Session_Namespace('monitoreo');
            $monitoreo = $monitoreos->monitoreo;

            $modelocurso = new Application_Model_DbTable_Cursos();

            $idcurso = $data['idCursos'];

            $detalleest = $modelocurso->listarcursoid($idcurso, $idperiodo);


            if (empty($usuario)) {
                die(json_encode(["response" => 0, "status" => "1"]));
            } else {

                $identificadors = new Zend_Session_Namespace('identificador');
                $ididentificador = $identificadors->identificador;

                if ($detalleest[0]['activarapp'] == 1) {

                    //Logeamos al usuario master y obtenemos el token

                    $chl = curl_init();
                    curl_setopt($chl, CURLOPT_URL, "http://api.softinnova.cl/logins?");
                    curl_setopt($chl, CURLOPT_POST, 1);

                    curl_setopt($chl, CURLOPT_POSTFIELDS,
                        "user=76189207k&password=rino711633");

                    curl_setopt($chl, CURLOPT_RETURNTRANSFER, true);

                    //execute the POST request
                    $result = curl_exec($chl);
                    //close cURL resource
                    curl_close($chl);
                    $resultadodeco = json_decode($result);
                    $token = $resultadodeco->token;

                }


                //Modelos a utilizar
                $alumnos = new Application_Model_DbTable_Alumnos();
                $alumnosactual = new Application_Model_DbTable_Alumnosactual();
                $medicos = new Application_Model_DbTable_Alumnomedico();
                $familiar = new Application_Model_DbTable_Alumnonucleo();
                $apoderados = new Application_Model_DbTable_Apoderados();
                $modeloescolar = new Application_Model_DbTable_Alumnoescolar();
                $modelcurso = new Application_Model_DbTable_Cursos();


                $tipoidentificacion = $data['tipoIdentificacion'];

                $rut = trim($data['rutAlumno']);
                $caracteres = array(",", ".", "-");
                $rut = str_replace($caracteres, "", $rut);
                $idestablecimiento = $data['idEstablecimiento'];
                $curso = $data['idCursos'];

                $nombres = trim($data['nombres']);
                $apaterno = trim($data['apaterno']);
                $amaterno = trim($data['amaterno']);
                $direccion = $data['calle'];
                $calle = $data['calle'];
                $numerocasa = $data['numeroCasa'];
                $villa = $data['villa'];
                $ciudadactual = $data['ciudadActual'];
                $comunaactual = $data['comunaActual'];
                $telefono = $data['telefono'];
                $movil = $data['celular'];
                $comuna = $data['comunaActual'];
                $correo = $data['correo'];
                $sexo = $data['sexo'];
                $fechanacimiento = $data['fechanacimiento'];
                $pais = $data['paisNacimiento'];
                $ciudadnacimiento = $data['ciudadNacimiento'];
                $fechanacimiento2 = $data['fechanacimiento'];

                $repitencia = $data['repitencia'];

                //Validar Datos
                if ($telefono == "") {
                    $telefono = 0;
                }

                if ($movil == "") {
                    $movil = 0;
                }


                $prioritario = $data['prioritario'];
                $beneficio = $data['beneficio'];

                $numeromatricula = 0;
                $datoscurso = $modelcurso->get($idcurso);
                $numerom = $alumnosactual->getnumeromatricula($datoscurso[0]['idEstablecimiento'], $datoscurso[0]['idCodigoTipo'], $idperiodo);
                if (!empty($numerom[0]['max'])) {
                    $numeromatricula = $numerom[0]['max'] + 1;
                }


                //datos medicos alumnos
                $prevision = $data['prevision'];
                $letra = $data['letra'];
                $actividad = $data['actividad'];
                $patologia = $data['patologia'];
                $hora = $data['horario'];
                $profesional = $data['profesional'];
                $tprofesional = $data['telefonoProfesional'];
                $documento = $data['documento'];
                $persona = $data['personaEmergencia'];
                $telefonoemergencia = $data['telefonoEmergencia'];
                $traslado = $data['trasladoEmergencia'];
                $personaretira = $data['personaRetira'];
                $telefonoretira = $data['telefonoRetira'];
                $rutretira = $data['rutRetira'];

                //Valida Datos

                if ($prevision == "" || $prevision == "NULL") {
                    $prevision = 0;
                }

                if ($tprofesional == "") {
                    $tprofesional = 0;
                }
                if ($telefonoemergencia == "") {
                    $telefonoemergencia = 0;
                }

                if ($telefonoretira == "") {
                    $telefonoretira = 0;
                }

                //datos nuecleo familiar alumno
                $rutpadre = $data['rutPadre'];
                $nombrepadre = $data['nombrepadre'];
                $paternopadre = $data['paternopadre'];
                $maternopadre = $data['maternopadre'];
                $telefonopadre = $data['telefonopadre'];
                $nivelpadre = $data['nivelpadre'];
                $ocupacionpadre = $data['ocupacionPadre'];

                if ($telefonopadre == "") {
                    $telefonopadre = 0;
                }

                $rutmadre = $data['rutMadre'];
                $nombremadre = $data['nombremadre'];
                $paternomadre = $data['paternomadre'];
                $maternomadre = $data['maternomadre'];
                $telefonomadre = $data['telefonomadre'];
                $nivelmadre = $data['nivelmadre'];
                $ocupacionmadre = $data['ocupacionMadre'];

                if ($telefonomadre == "") {
                    $telefonomadre = 0;
                }

                //Datos Apoderado

                $rutapoderado = $data['rutApoderado'];
                $rutapoderado = str_replace($caracteres, "", $rutapoderado);
                $nombreapoderado = $data['nombreApoderado'];
                $paternoapoderado = $data['paternoApoderado'];
                $maternoapoderado = $data['maternoApoderado'];
                $telefonoapoderado = $data['telefonoApoderado'];
                $direccionapoderado = $data['direccionApoderado'];
                $comunaapoderado = $data['comunaApoderado'];
                $correoapoderado = $data['correoApoderado'];
                //Nuevo Campos
                $estado = $data['idEstadoActual'];
                $fechainscripcion = $data['fechaInscripcion'];

                //Valida Datos
                if ($telefonoapoderado == "") {
                    $telefonoapoderado = 0;
                }


                $nacionalidad = $data['nacionalidad'];
                $vive = $data['vive'];
                $etnia = $data['etnia'];
                $religion = $data['religion'];
                $junaeb = $data['junaeb'];

                $pie = $data['pie'];
                $autorizacion = $data['autorizacion'];
                $aprendizaje = $data['aprendizaje'];
                $transporte = $data['transporte'];

                //Apoderado Suplente
                $rutapoderadosuplente = $data['rutApoderadoSuplente'];
                $nombreapoderadosuplente = $data['nombreApoderadoSuplente'];
                $paternoapoderadosuplente = $data['paternoApoderadoSuplente'];
                $maternoapoderadosuplente = $data['maternoApoderadoSuplente'];
                $telefonoapoderadosuplente = $data['telefonoApoderadoSuplente'];
                $direccionapoderadosuplente = $data['direccionApoderadoSuplente'];
                $comunaapoderadosuplente = $data['comunaApoderadoSuplente'];
                $correoapoderadosuplente = $data['correoApoderadoSuplente'];

                if ($telefonoapoderadosuplente == "") {
                    $telefonoapoderadosuplente = 0;
                }


                $tratamiento = $data['tratamiento'];
                $otrotratamieto = $data['otroTratamiento'];
                $entratamiento = $data['enTratamiento'];
                $alegia = $data['alergia'];

                $nacimiento = $data['nacimiento'];
                $estudios = $data['estudio'];
                $personalidad = $data['personalidad'];


                //Datos vacios
                $archivo = '0';
                $alumnosverificas = new Application_Model_DbTable_Alumnos();
                $alumnosverifica = $alumnosverificas->validaralumno($rut);
                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                if (empty($rut) || empty($nombres) || empty($apaterno) || empty($amaterno) || empty($curso) || empty($fechanacimiento) || $fechanacimiento == "0000-00-00") {
                    die(json_encode(["response" => 0, "status" => "3"]));
                }

                if ($detalleest[0]["activarapp"] == 1) {
                    if (empty($token)) {
                        die(json_encode(["response" => 0, "status" => "3"]));
                    }
                }

                $db->beginTransaction();
                try {

                    $ultimo = $alumnosactual->ultimo($curso, $idperiodo);
                    if (!$alumnosverifica) {

//                            $originalFilename = pathinfo($form->foto->getFileName());
//                            if ($originalFilename == null) {
//                                $newFilename = '0';
//
//                            } else {
//                                $newFilename = 'fotografia-' . uniqid() . '.' . $originalFilename['extension'];
//                            }


                        if ($fechanacimiento != "0000-00-00") {
                            $fechanacimiento = date("Y-m-d", strtotime($fechanacimiento));
                        } else {
                            $fechanacimiento = "0000-00-00";
                        }

                        if ($fechainscripcion != "0000-00-00") {
                            $fechainscripcion = date("Y-m-d", strtotime($fechainscripcion));
                        } else {
                            $fechainscripcion = "0000-00-00";
                        }


                        if (empty($rutapoderado) || empty($nombreapoderado) || empty($paternoapoderado) || empty($maternoapoderado)) {
                            $idApoderado = 0;
                        } else {

                            //si el rut es posee datos
                            if (!empty($alumnosverifica[0]['rutApoderado'])) {
                                //Sie el rut del apoderado es igual se actualiza
                                if ($alumnosverifica[0]['rutApoderado'] == $rutapoderado) {
                                    $apoderados->cambiarportur($rutapoderado, $nombreapoderado, $paternoapoderado, $maternoapoderado, $direccionapoderado, $telefonoapoderado, $comunaapoderado, '');
                                    $idApoderado = $alumnosverifica[0]['idApoderado'];
                                } else { // si no es igual se crea uno nuevo
                                    //validamos que no exista en la base de datos
                                    $validarapoderado = $apoderados->validar($rutapoderado);

                                    if ($validarapoderado) {
                                        $apoderados->agregar($rutapoderado, $nombreapoderado, $paternoapoderado, $maternoapoderado, $direccionapoderado, $telefonoapoderado, $comunaapoderado, '');
                                        $idApoderado = $apoderados->getAdapter()->lastInsertId();
                                    } else {
                                        $apoderados->cambiarporrut($rutapoderado, $nombreapoderado, $paternoapoderado, $maternoapoderado, $direccionapoderado, $telefonoapoderado, $comunaapoderado, '');
                                        $datoss = $apoderados->getrut($rutapoderado);
                                        if ($datoss) {
                                            $idApoderados = $datoss['idApoderado'];
                                        }


                                    }

                                }


                            } else {
                                //validamos que no exista en la base de datos
                                $validarapoderado = $apoderados->validar($rutapoderado);

                                if ($validarapoderado) {
                                    $apoderados->agregar($rutapoderado, $nombreapoderado, $paternoapoderado, $maternoapoderado, $direccionapoderado, $telefonoapoderado, $comunaapoderado, '');
                                    $idApoderado = $alumnos->getAdapter()->lastInsertId();
                                } else {
                                    $apoderados->cambiarporrut($rutapoderado, $nombreapoderado, $paternoapoderado, $maternoapoderado, $direccionapoderado, $telefonoapoderado, $comunaapoderado, '');
                                    $datoss = $apoderados->getrut($rutapoderado);
                                    if ($datoss) {
                                        $idApoderados = $datoss['idApoderado'];
                                    }


                                }
                            }

                        }


                        if (empty($rutapoderadosuplente) || empty($nombreapoderadosuplente) || empty($paternoapoderadosuplente) || empty($maternoapoderadosuplente)) {
                            $idApoderados = 0;
                        } else {

                            //Verificamos que el apoderado suplente exista o no
                            if ($alumnosverifica[0]['idApoderados']) {
                                $datossuplente = $apoderados->get($alumnosverifica[0]['idApoderados']);
                            } else {
                                $datossuplente = array();
                            }


                            if (count($datossuplente) > 0) {

                                //Obtenemos los datos guardados del apoderado suplente
                                $datosapoderadosuplente = $apoderados->get($alumnosverifica[0]['idApoderados']);

                                //Si el apoderado Suplente guardado no coincide con el que viene por parametro guardamos el nuevo
                                if ($datosapoderadosuplente['rutApoderado'] != $rutapoderadosuplente) {

                                    //Validamos que no exista en la base de datos
                                    $validarsuplente = $apoderados->validar($rutapoderadosuplente);
                                    if ($validarsuplente) {
                                        $apoderados->agregar($rutapoderadosuplente, $nombreapoderadosuplente, $paternoapoderadosuplente, $maternoapoderadosuplente, $direccionapoderadosuplente, $telefonoapoderadosuplente, $comunaapoderadosuplente, $correoapoderadosuplente);
                                        $idApoderados = $alumnos->getAdapter()->lastInsertId();
                                    } else {
                                        $apoderados->cambiarporrut($rutapoderadosuplente, $nombreapoderadosuplente, $paternoapoderadosuplente, $maternoapoderadosuplente, $direccionapoderadosuplente, $telefonoapoderadosuplente, $comunaapoderadosuplente, $correoapoderadosuplente);
                                        $datoss = $apoderados->getrut($rutapoderadosuplente);
                                        if ($datoss) {
                                            $idApoderados = $datoss['idApoderado'];
                                        }


                                    }

                                } else {
                                    $apoderados->cambiarportur($rutapoderadosuplente, $nombreapoderadosuplente, $paternoapoderadosuplente, $maternoapoderadosuplente, $direccionapoderadosuplente, $telefonoapoderadosuplente, $comunaapoderadosuplente, $correoapoderadosuplente);
                                    $idApoderados = $datosapoderadosuplente['idApoderado'];
                                }


                            } else {
                                $validarsuplente = $apoderados->validar($rutapoderadosuplente);
                                if ($validarsuplente) {
                                    $apoderados->agregar($rutapoderadosuplente, $nombreapoderadosuplente, $paternoapoderadosuplente, $maternoapoderadosuplente, $direccionapoderadosuplente, $telefonoapoderadosuplente, $comunaapoderadosuplente, $correoapoderadosuplente);
                                    $idApoderados = $alumnos->getAdapter()->lastInsertId();
                                } else {
                                    $apoderados->cambiarporrut($rutapoderadosuplente, $nombreapoderadosuplente, $paternoapoderadosuplente, $maternoapoderadosuplente, $direccionapoderadosuplente, $telefonoapoderadosuplente, $comunaapoderadosuplente, $correoapoderadosuplente);
                                    $datoss = $apoderados->getrut($rutapoderadosuplente);
                                    //Zend_Debug::dump($datoss);
                                    if ($datoss) {
                                        $idApoderados = $datoss['idApoderado'];
                                    }


                                }

                            }
                        }



                        $alumnos->agregar($rut, $nombres, $apaterno, $amaterno, $sexo, $fechanacimiento, $nacionalidad, $etnia, $pais, $ciudadnacimiento);
                        $idingresado = $alumnos->getAdapter()->lastInsertId();


                        $medicos->agregar($prevision, $letra, $actividad, $patologia, $profesional, $tprofesional, $hora, $documento, $persona, $telefonoemergencia, $traslado, $personaretira, $telefonoretira, $rutretira, $tratamiento, $otrotratamieto, $entratamiento, $alegia, $idingresado);

                        $familiar->agregar($nombremadre, $paternomadre, $maternomadre, $telefonomadre, $nivelmadre, $nombrepadre, $paternopadre, $maternopadre, $telefonopadre, $nivelpadre, $rutpadre, $rutmadre, $ocupacionpadre, $ocupacionmadre, $idingresado);

                        //si no existe se crea para el periodo actual.
                        if ($alumnosactual->validar($idingresado, $idperiodo)) {
                            $alumnosactual->agregar($idingresado, $curso, $idperiodo, $ultimo[0]['max'] + 1, $fechainscripcion, $numeromatricula, $calle, $numerocasa, $villa, $ciudadactual, $telefono, $movil, $comunaactual, $correo, $idApoderado, $idApoderados, $repitencia, $tipoidentificacion);
                            $idalumnoactual = $alumnosactual->getAdapter()->lastInsertId();


                        } else {
                            //Actualizamos la tabla de los alumnos actual
                            $alumnosactual->actualizarcurso($idingresado, $idperiodo, $curso, $fechainscripcion, $numeromatricula, $calle, $numerocasa, $villa, $ciudadactual, $telefono, $movil, $comunaactual, $correo, $idApoderado, $idApoderados, $repitencia, $tipoidentificacion);
                            $datosactual = $alumnosactual->getactualalumno($idingresado, $idperiodo, $curso);
                            if ($datosactual) {
                                $idalumnoactual = $datosactual[0]['idAlumnosActual'];

                            }

                        }

                        $modeloescolar->agregar($prioritario, $beneficio, $archivo, $pie, $religion, $junaeb, $autorizacion, $aprendizaje, $transporte, $idalumnoactual);


                        if ($monitoreo == 0) {
                            //Obtenemos las Evaluaciones Activas que posee el curso al que esta ingresando el alumno, excluyendo las que podria tener
                            $modelevaluacion = new Application_Model_DbTable_Pruebas();
                            $modelnotas = new Application_Model_DbTable_Notas();

                            $listapruebas = $modelevaluacion->listapruebas($curso, $idperiodo);
                            $date = new DateTime;
                            $fechaactual = $date->format('Y-m-d');
                            for ($i = 0; $i < count($listapruebas); $i++) {
                                $modelnotas->agregar($idingresado, $listapruebas[$i]['idAsignatura'], $curso, 0, $listapruebas[$i]['idCuenta'], $listapruebas[$i]['idEvaluacion'], $fechaactual, $idperiodo);

                            }
                        }


                        if ($detalleest[0]["activarapp"] == 1) {

                            //Verificamos que exista en la Tabla de la App Apoderados
                            //API URL Obtenemos los dias feriados desde la api

                            //Seteamos los ultimo 4 digitos del rut
                            $pass = substr($rut, 5, 8);
                            $ch = curl_init();

                            curl_setopt($ch, CURLOPT_URL, "http://api.softinnova.cl/crearalumno?");
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS,
                                "user=" . $rut . "&password=" . $pass . "&identificador=" . $ididentificador . "&idCursos=" . $curso . "&idAlumno=" . $idalumnoactual . "&idPeriodo=" . $idperiodo . "&userc=76189207k");
                            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Acceso: ' . $token));
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                            //execute the POST request
                            $result = curl_exec($ch);
                            //close cURL resource
                            curl_close($ch);
                            $resultadodeco = json_decode($result);


                            if ($resultadodeco->success) {

                                //Actualizaos el Estado del Alumno si está Activo en la App
                                $alumnosactual->cambiarestadoapp($idalumnoactual, 5);

                                $db->commit();
                                $pag = $this->_request->getBaseUrl() . ('/Alumnos');
                                die(json_encode(["response" => 1, "url" => $pag]));


                            } elseif (!$resultadodeco->success && $resultadodeco->message == 1) {


                                //Actualizamos al Alumno
                                //Convertimos el rut
                                $rut = mb_convert_case($rut, MB_CASE_LOWER, "UTF-8");

                                if (strlen($rut) > 9) {
                                    $pass = substr($rut, 0, 7);
                                } else {
                                    $pass = substr($rut, 0, 7);
                                }


                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, "https://api.softinnova.cl/actualizaralumno?");
                                curl_setopt($ch, CURLOPT_POST, 1);

                                curl_setopt($ch, CURLOPT_POSTFIELDS,
                                    "user=" . $rut . "&password=" . $pass . "&identificador=" . $ididentificador . "&idCursos=" . $curso . "&idAlumno=" . $idalumnoactual . "&idPeriodo=" . $idperiodo . "&userc=76189207k");
                                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Acceso: ' . $token));
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                                //execute the POST request
                                $result = curl_exec($ch);
                                //close cURL resource
                                curl_close($ch);
                                $resultadodeco = json_decode($result);
                                if ($resultadodeco->success) {
                                    //Actualizaos el Estado del Alumno si se Activo en la App
                                    $alumnosactual->cambiarestadoapp($idalumnoactual, 5);
                                    $db->commit();
                                    $pag = $this->_request->getBaseUrl() . ('/Alumnos');
                                    die(json_encode(["response" => 1, "url" => $pag]));

                                }


                            } else {
                                $db->rollBack();
                                die(json_encode(["response" => 0, "status" => "2"]));
                            }

                        }

                        $db->commit();
                        $pag = $this->_request->getBaseUrl() . ('/Alumnos');
                        die(json_encode(["response" => 1, "url" => $pag]));


                    } else {

                        //Actualizamos el alumnos


                        if ($fechanacimiento != "0000-00-00") {
                            $fechanacimiento = date("Y-m-d", strtotime($fechanacimiento));
                        } else {
                            $fechanacimiento = "0000-00-00";
                        }

                        if ($fechainscripcion != "0000-00-00") {
                            $fechainscripcion = date("Y-m-d", strtotime($fechainscripcion));
                        } else {
                            $fechainscripcion = "0000-00-00";
                        }


                        if (empty($rutapoderado) || empty($nombreapoderado) || empty($paternoapoderado) || empty($maternoapoderado)) {
                            $idApoderado = 0;
                        } else {

                            //si el rut es posee datos
                            if (!empty($alumnosverifica[0]['rutApoderado'])) {
                                //Sie el rut del apoderado es igual se actualiza
                                if ($alumnosverifica[0]['rutApoderado'] == $rutapoderado) {
                                    $apoderados->cambiarporrut($rutapoderado, $nombreapoderado, $paternoapoderado, $maternoapoderado, $direccionapoderado, $telefonoapoderado, $comunaapoderado, '');
                                    $idApoderado = $alumnosverifica[0]['idApoderado'];
                                } else { // si no es igual se crea uno nuevo
                                    //validamos que no exista en la base de datos
                                    $validarapoderado = $apoderados->validar($rutapoderado);

                                    if ($validarapoderado) {
                                        $apoderados->agregar($rutapoderado, $nombreapoderado, $paternoapoderado, $maternoapoderado, $direccionapoderado, $telefonoapoderado, $comunaapoderado, '');
                                        $idApoderado = $alumnos->getAdapter()->lastInsertId();
                                    } else {
                                        $apoderados->cambiarporrut($rutapoderado, $nombreapoderado, $paternoapoderado, $maternoapoderado, $direccionapoderado, $telefonoapoderado, $comunaapoderado, '');
                                        $datoss = $apoderados->getrut($rutapoderado);
                                        if ($datoss) {
                                            $idApoderado = $datoss['idApoderado'];
                                        }


                                    }

                                }


                            } else {
                                //validamos que no exista en la base de datos
                                $validarapoderado = $apoderados->validar($rutapoderado);

                                if ($validarapoderado) {
                                    $apoderados->agregar($rutapoderado, $nombreapoderado, $paternoapoderado, $maternoapoderado, $direccionapoderado, $telefonoapoderado, $comunaapoderado, '');
                                    $idApoderado = $alumnos->getAdapter()->lastInsertId();
                                } else {
                                    $apoderados->cambiarporrut($rutapoderado, $nombreapoderado, $paternoapoderado, $maternoapoderado, $direccionapoderado, $telefonoapoderado, $comunaapoderado, '');
                                    $datoss = $apoderados->getrut($rutapoderado);
                                    if ($datoss) {
                                        $idApoderado = $datoss['idApoderado'];
                                    }


                                }
                            }

                        }


                        if (empty($rutapoderadosuplente) || empty($nombreapoderadosuplente) || empty($paternoapoderadosuplente) || empty($maternoapoderadosuplente)) {
                            $idApoderados = 0;
                        } else {

                            //Verificamos que el apoderado suplente exista o no
                            if ($alumnosverifica[0]['idApoderados']) {
                                $datossuplente = $apoderados->get($alumnosverifica[0]['idApoderados']);
                            } else {
                                $datossuplente = array();
                            }


                            if (count($datossuplente) > 0) {

                                //Obtenemos los datos guardados del apoderado suplente
                                $datosapoderadosuplente = $apoderados->get($alumnosverifica[0]['idApoderados']);

                                //Si el apoderado Suplente guardado no coincide con el que viene por parametro guardamos el nuevo
                                if ($datosapoderadosuplente['rutApoderado'] != $rutapoderadosuplente) {

                                    //Validamos que no exista en la base de datos
                                    $validarsuplente = $apoderados->validar($rutapoderadosuplente);
                                    if ($validarsuplente) {
                                        $apoderados->agregar($rutapoderadosuplente, $nombreapoderadosuplente, $paternoapoderadosuplente, $maternoapoderadosuplente, $direccionapoderadosuplente, $telefonoapoderadosuplente, $comunaapoderadosuplente, $correoapoderadosuplente);
                                        $idApoderados = $alumnos->getAdapter()->lastInsertId();
                                    } else {
                                        $apoderados->cambiarporrut($rutapoderadosuplente, $nombreapoderadosuplente, $paternoapoderadosuplente, $maternoapoderadosuplente, $direccionapoderadosuplente, $telefonoapoderadosuplente, $comunaapoderadosuplente, $correoapoderadosuplente);
                                        $datoss = $apoderados->getrut($rutapoderadosuplente);
                                        if ($datoss) {
                                            $idApoderados = $datoss['idApoderado'];
                                        }


                                    }

                                } else {
                                    $apoderados->cambiarporrut($rutapoderadosuplente, $nombreapoderadosuplente, $paternoapoderadosuplente, $maternoapoderadosuplente, $direccionapoderadosuplente, $telefonoapoderadosuplente, $comunaapoderadosuplente, $correoapoderadosuplente);
                                    $idApoderados = $datosapoderadosuplente['idApoderado'];
                                }


                            } else {
                                $validarsuplente = $apoderados->validar($rutapoderadosuplente);
                                if ($validarsuplente) {
                                    $apoderados->agregar($rutapoderadosuplente, $nombreapoderadosuplente, $paternoapoderadosuplente, $maternoapoderadosuplente, $direccionapoderadosuplente, $telefonoapoderadosuplente, $comunaapoderadosuplente, $correoapoderadosuplente);
                                    $idApoderados = $alumnos->getAdapter()->lastInsertId();
                                } else {
                                    $apoderados->cambiarporrut($rutapoderadosuplente, $nombreapoderadosuplente, $paternoapoderadosuplente, $maternoapoderadosuplente, $direccionapoderadosuplente, $telefonoapoderadosuplente, $comunaapoderadosuplente, $correoapoderadosuplente);
                                    $datoss = $apoderados->getrut($rutapoderadosuplente);

                                    if ($datoss) {
                                        $idApoderados = $datoss['idApoderado'];
                                    }


                                }

                            }
                        }


                        $alumnos->cambiar($alumnosverifica[0]['idAlumnos'], $rut, $nombres, $apaterno, $amaterno, $sexo, $fechanacimiento, $nacionalidad, $etnia, $pais, $ciudadnacimiento);

                        //Verificamos si existe
                        if ($medicos->validar($alumnosverifica[0]['idAlumnos'])) {
                            $medicos->cambiar($prevision, $letra, $actividad, $patologia, $profesional, $tprofesional, $hora, $documento, $persona, $telefonoemergencia, $traslado, $personaretira, $telefonoretira, $rutretira, $tratamiento, $otrotratamieto, $entratamiento, $alegia, $alumnosverifica[0]['idAlumnos']);
                        } else {
                            $medicos->agregar($prevision, $letra, $actividad, $patologia, $profesional, $tprofesional, $hora, $documento, $persona, $telefonoemergencia, $traslado, $personaretira, $telefonoretira, $rutretira, $tratamiento, $otrotratamieto, $entratamiento, $alegia, $alumnosverifica[0]['idAlumnos']);

                        }

                        if ($familiar->validar($alumnosverifica[0]['idAlumnos'])) {
                            $familiar->cambiar($nombremadre, $paternomadre, $maternomadre, $telefonomadre, $nivelmadre, $nombrepadre, $paternopadre, $maternopadre, $telefonopadre, $nivelpadre, $rutpadre, $rutmadre, $ocupacionpadre, $ocupacionmadre, $vive, $nacimiento, $estudios, $personalidad, $alumnosverifica[0]['idAlumnos']);
                        } else {
                            $familiar->agregar($nombremadre, $paternomadre, $maternomadre, $telefonomadre, $nivelmadre, $nombrepadre, $paternopadre, $maternopadre, $telefonopadre, $nivelpadre, $rutpadre, $rutmadre, $ocupacionpadre, $ocupacionmadre, $alumnosverifica[0]['idAlumnos']);

                        }

                        //verificamos si existe en el periodo actual

                        //si no existe se crea para el periodo actual.
                        $idalumnoactual = 0;
                        if ($alumnosactual->validar($alumnosverifica[0]['idAlumnos'], $idperiodo)) {
                            $alumnosactual->agregar($alumnosverifica[0]['idAlumnos'], $curso, $idperiodo, $ultimo[0]['max'] + 1, $fechainscripcion, $numeromatricula, $calle, $numerocasa, $villa, $ciudadactual, $telefono, $movil, $comunaactual, $correo, $idApoderado, $idApoderados, $repitencia, $tipoidentificacion);
                            $idalumnoactual = $alumnosactual->getAdapter()->lastInsertId();


                        } else {
                            //Actualizamos la tabla de los alumnos actual
                            $alumnosactual->actualizarcurso($alumnosverifica[0]['idAlumnos'], $idperiodo, $curso, $fechainscripcion, $numeromatricula, $calle, $numerocasa, $villa, $ciudadactual, $telefono, $movil, $comunaactual, $correo, $idApoderado, $idApoderados, $repitencia, $tipoidentificacion);
                            $datosactual = $alumnosactual->getactualalumno($alumnosverifica[0]['idAlumnos'], $idperiodo, $curso);


                            if ($datosactual) {
                                $idalumnoactual = $datosactual[0]['idAlumnosActual'];

                            }

                        }

                        if ($modeloescolar->validar($idalumnoactual)) {
                            $modeloescolar->cambiar($idalumnoactual, $prioritario, $beneficio, $archivo, $pie, $religion, $junaeb, $autorizacion, $aprendizaje, $transporte);
                        } else {
                            $modeloescolar->agregar($prioritario, $beneficio, $archivo, $pie, $religion, $junaeb, $autorizacion, $aprendizaje, $transporte, $idalumnoactual);
                        }


                        if ($detalleest[0]["activarapp"] == 1) {

                            //Verificamos que exista en la Tabla de la App Apoderados

                            //API URL Obtenemos los dias feriados desde la api

                            //Seteamos los ultimo 4 digitos del rut
                            $pass = substr($rut, 5, 8);
                            $chll = curl_init();

                            curl_setopt($chll, CURLOPT_URL, "http://api.softinnova.cl/crearalumno?");
                            curl_setopt($chll, CURLOPT_POST, 1);
                            curl_setopt($chll, CURLOPT_POSTFIELDS,
                                "user=" . $rut . "&password=" . $pass . "&identificador=" . $ididentificador . "&idCursos=" . $curso . "&idAlumno=" . $idalumnoactual . "&idPeriodo=" . $idperiodo . "&userc=76189207k");
                            curl_setopt($chll, CURLOPT_HTTPHEADER, array("Acceso: " . $token));
                            curl_setopt($chll, CURLOPT_RETURNTRANSFER, true);

                            //execute the POST request
                            $result = curl_exec($chll);
                            //close cURL resource
                            curl_close($chll);
                            $resultadodeco = json_decode($result);


                            if ($resultadodeco->success) {

                                //Actualizaos el Estado del Alumno si está Activo en la App
                                $alumnosactual->cambiarestadoapp($idalumnoactual, 5);

                                $db->commit();
                                $pag = $this->_request->getBaseUrl() . ('/Alumnos');
                                die(json_encode(["response" => 1, "url" => $pag]));


                            } elseif (!$resultadodeco->success && $resultadodeco->message == 2) {

                                //Actualizamos al Alumno

                                //Convertimos el rut
                                $rut = mb_convert_case($rut, MB_CASE_LOWER, "UTF-8");


                                $ch = curl_init();
                                curl_setopt($ch, CURLOPT_URL, "http://api.softinnova.cl/actualizaralumno?");
                                curl_setopt($ch, CURLOPT_POST, 1);

                                curl_setopt($ch, CURLOPT_POSTFIELDS,
                                    "user=" . $rut . "&identificador=" . $ididentificador . "&idCursos=" . $curso . "&idAlumno=" . $idalumnoactual . "&idPeriodo=" . $idperiodo . "&userc=76189207k");
                                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Acceso: ' . $token));
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                                //execute the POST request
                                $result = curl_exec($ch);
                                //close cURL resource
                                curl_close($ch);
                                $resultadodeco = json_decode($result);

                                if ($resultadodeco->success) {
                                    //Actualizaos el Estado del Alumno si se Activo en la App
                                    $alumnosactual->cambiarestadoapp($idalumnoactual, 5);
                                    $db->commit();
                                    $pag = $this->_request->getBaseUrl() . ('/Alumnos');
                                    die(json_encode(["response" => 1, "url" => $pag]));

                                }


                            } else {

                                $db->rollBack();
                                die(json_encode(["response" => 0, "status" => "2"]));
                            }

                        }


                        //Fin App Apoderados

                        $db->commit();
                        $pag = $this->_request->getBaseUrl() . ('/Alumnos');
                        die(json_encode(["response" => 1, "url" => $pag]));


                    }
                } catch (Exception $e) {

                    // Si hubo problemas. Enviamos  marcha atras
                    $db->rollBack();
                    die(json_encode(["response" => 0, "status" => "2"]));
                }

            }


        }
    }

    public function editarAction()
    {


        $idalumno = $this->_getParam('id', 0);
        if ($idalumno > 0) {

            $cargo = new Zend_Session_Namespace('cargo');
            $rol = $cargo->cargo;

            $form = new Application_Form_Matricula();
            $form->setDecorators(
                array(
                    'FormElements',
                    'Form',
                )
            );

            if ($rol == 2) {

                $valida = false;

                $establecimientos = new Zend_Session_NameSpace("establecimiento");
                $establecimiento = $establecimientos->establecimiento;

                $rutusuario = new Zend_Session_Namespace('id');
                $usuario = $rutusuario->id;

                $periodo = new Zend_Session_Namespace('periodo');
                $idperiodo = $periodo->periodo;

                //Obtenemos los datos del alumno que esta editando

                $modeloalumnosactual = new Application_Model_DbTable_Alumnosactual();
                $datosalumno = $modeloalumnosactual->getactual($idalumno, $idperiodo);

                //Obtenemos los datos del Docente en sesion

                $modelocurso = new Application_Model_DbTable_Cursos();
                $datosdocente = $modelocurso->getcursojsonusuario($usuario, $establecimiento, $idperiodo);


                if (count($datosalumno) > 0 && count($datosdocente) > 0) {

                    //Verificamos que el curso corresponda al del docente jefe

                    for ($i = 0; $i < count($datosdocente); $i++) {

                        if ($datosdocente[$i]['idCursos'] == $datosalumno[0]['idCursosActual']) {

                            $valida = true;
                            break;

                        }

                    }

                } else {

                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No Posee Permisos');
                    $this->view->assign('messages', $messages);

                }

                if ($valida) {
                    $this->view->datos = $idalumno;
                    $this->view->form = $form;
                } else {
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Sin Autorización');
                    $this->view->assign('messages', $messages);
                }

            } elseif ($rol == 1 || $rol == 3 || $rol == 4 || $rol == 6) {

                $this->view->datos = $idalumno;
                $this->view->form = $form;

            }


        } else {

            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Error al cargar Alumno');
            $this->view->assign('messages', $messages);
        }


    }

    public function eliminarAction()
    {

        $rut = $this->_getParam('id', 0);
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        // Iniciamos la transaccion
        $db->beginTransaction();
        try {

            $actual = new Application_Model_DbTable_Alumnosactual();

            $tablamed = new Application_Model_DbTable_Alumnomedico();
            $tablanuc = new Application_Model_DbTable_Alumnonucleo();
            $tabla = new Application_Model_DbTable_Alumnos();

            //$db->commit();
            $this->_helper->redirector('index');
        } catch (Exception $e) {
            // Si hubo problemas. Enviamos marcha atras
            $db->rollBack();
            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No se puede eliminar este registro, posee datos asociados');

            $this->view->assign('messages', $messages);
        }
    }

    public function getmodelosAction()
    {
        $modelModelo = new Application_Model_DbTable_Comuna();
        $results = $modelModelo->getAsKeyValueJSON($this->_getParam('id'));
        $this->_helper->json($results);
    }

    public function getalumnosAction()
    {
        $modelModelo = new Application_Model_DbTable_Alumnos();
        $results = $modelModelo->getAsKeyValueJSON($this->_getParam('id'));
        $this->_helper->json($results);
    }


    public function verificarut($r)
    {


        $r = strtoupper(ereg_replace('\.|,|-', '', $r));
        $sub_rut = substr($r, 0, strlen($r) - 1);
        $sub_dv = substr($r, -1);
        $x = 2;
        $s = 0;
        for ($i = strlen($sub_rut) - 1; $i >= 0; $i--) {
            if ($x > 7) {
                $x = 2;
            }
            $s += $sub_rut[$i] * $x;
            $x++;
        }
        $dv = 11 - ($s % 11);
        if ($dv == 10) {
            $dv = 'K';
        }
        if ($dv == 11) {
            $dv = '0';
        }
        if ($dv == $sub_dv) {
            return true;
        } else {
            return false;
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

    public function getcursoAction()
    {
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $procesomatricula = false;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $modelcurso = new Application_Model_DbTable_Cursos();

        // Si es Docente
        if ($rol == 2) {

            $usuario = new Zend_Session_Namespace('id');
            $user = $usuario->id;
            //Si el Docente posee jefatura

            $results = $modelcurso->getcursojson($user, $this->_getParam('id'), $idperiodo);

            if ($results) {
                $this->_helper->json($results);
            }


        } elseif ($rol == 1 || $rol == 3 || $rol == 4 || $rol == 6 || ($procesomatricula && $rol == 2)) {

            $results = $modelcurso->getcursojson(null, $this->_getParam('id'), $idperiodo);
            $this->_helper->json($results);
        }


    }


    public function agregarapoderadoAction()
    {
        $this->view->title = "Agregar Apoderado";
        $this->view->headTitle($this->view->title);
        $form = new Application_Form_Apoderado();
        $form->setDecorators(
            array(
                'FormElements',
                'Form',
            )
        );
        foreach ($form->getElements() as $element) {
            $element->removeDecorator('DtDdWrapper')
                ->removeDecorator('HtmlTag', array('tag' => 'dl'))
                ->setDecorators(array('ViewHelper',
                    'Errors',
                    'Label',
                    array('HtmlTag', array('tag' => 'p')),
                ));
        }


        $form->submit->setLabel('Agregar Apoderado');
        $this->view->form = $form;
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                $rutapoderado = $form->getValue('rutApoderado');
                $nombreapoderado = $form->getValue('nombreApoderado');
                $apaternoapoderado = $form->getValue('paternoApoderado');
                $amaternoapoderado = $form->getValue('maternoApoderado');
                $direccionapoderado = $form->getValue('direccionApoderado');
                $telefonoapoderado = $form->getValue('telefonoApoderado');

                $comunaapoderado = $form->getValue('comuna');
                $correoapoderado = $form->getValue('correoApoderado');
                $relacion = $form->getValue('relacion');


                $apoderadoverificas = new Application_Model_DbTable_Apoderados();
                $apoderadoverifica = $apoderadoverificas->validar($rutapoderado);

                if ($apoderadoverifica) {
                    $apoderado = new Application_Model_DbTable_Apoderados();
                    $apoderado->agregar($rutapoderado, $nombreapoderado, $apaternoapoderado, $amaternoapoderado, $direccionapoderado, $telefonoapoderado, $comunaapoderado, $correoapoderado, $relacion);

                    $this->_helper->redirector('indexa');
                } else {

                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('El Rut de Apoderado que intenta ingresar, esta ya ingresado');
                    $this->view->assign('messages', $messages);

                }


            } else {
                $form->populate($formData);
            }
        }

    }

    public function editarapoderadoAction()
    {

        $this->view->title = "Editar Apoderado";
        $this->view->headTitle($this->view->title);
        $form = new Application_Form_Apoderado();
        $form->setDecorators(
            array(
                'FormElements',
                'Form',
            )
        );
        foreach ($form->getElements() as $element) {
            $element->removeDecorator('DtDdWrapper')
                ->removeDecorator('HtmlTag', array('tag' => 'dl'))
                ->setDecorators(array('ViewHelper',

                    'Errors',

                    'Label',
                    array('HtmlTag', array('tag' => 'p')),
                ));
        }

        $form->submit->setLabel('Editar Apoderado');
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();

            if ($form->isValid($formData)) {

                //Datos Apoderado del Alumno
                $id = $form->getValue('idApoderado');
                $rutapoderado = $form->getValue('rutApoderado');
                $nombreapoderado = $form->getValue('nombreApoderado');
                $apaternoapoderado = $form->getValue('paternoApoderado');
                $amaternoapoderado = $form->getValue('maternoApoderado');
                $direccionapoderado = $form->getValue('direccionApoderado');
                $telefonoapoderado = $form->getValue('telefonoApoderado');

                $comunaapoderado = $form->getValue('comuna');
                $correoapoderado = $form->getValue('correoApoderado');
                $relacion = $form->getValue('relacion');

                //creo objeto apoderado que controla la talba apoderados de la base de datos
                $apoderado = new Application_Model_DbTable_Apoderados();

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                // Iniciamos la transaccion
                $db->beginTransaction();
                try {

                    //llamo a la funcion agregar, con los datos que recibi del form
                    $apoderado->cambiar($id, $rutapoderado, $nombreapoderado, $apaternoapoderado, $amaternoapoderado, $direccionapoderado, $telefonoapoderado, $comunaapoderado, $correoapoderado, $relacion);

                    // Sino hubo ningun inconveniente hacemos un commit
                    $db->commit();
                    $this->_helper->redirector('indexa');
                } catch (Exception $e) {
                    // Si hubo problemas. Enviamos  marcha atras
                    $db->rollBack();
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un error al intentar actualizar el registro, esto puede ocurrir porque posee datos asociados');
                    /// Assign the messages
                    $this->view->assign('messages', $messages);
                }

            }


        } else {
            $rut = $this->_getParam('id', 0);
            if ($rut > 0) {

                $personal = new Application_Model_DbTable_Apoderados();
                $tabla = $personal->get($rut);

                $comuna = new Application_Model_DbTable_Comuna();
                $rowset = $comuna->getcomuna($tabla['comuna']);

                $rowsetcom = $comuna->getAsKeyValue($rowset[0]["idProvincia"]);

                $provincia = new Application_Model_DbTable_Provincia();
                $rowsetprovincia = $provincia->getAsKeyValue($rowset[0]["idRegion"]);

                $form->provincia->clearMultiOptions();
                $form->provincia->addMultiOptions($rowsetprovincia);
                $form->provincia->setValue($rowset[0][idProvincia]);
                $form->comuna->clearMultiOptions();
                $form->comuna->addMultiOptions($rowsetcom);
                $form->region->setValue($rowset[0][idRegion]);

                $form->populate($tabla);
            }
        }

    }

    public function eliminarapoderadoAction()
    {

        $id = $this->_getParam('id', 0);
        $tabla = new Application_Model_DbTable_Apoderados();

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();

        $db->beginTransaction();
        try {

            $tabla->borrar($id);
            $db->commit();
            $this->_helper->redirector('indexa');
        } catch (Exception $e) {
            // Si hubo problemas. Enviamos  marcha atras
            $db->rollBack();
            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No se puede eliminar este registro, posee datos asociados');
            $this->view->assign('messages', $messages);
        }
    }

    public function estadoAction()
    {

        $this->view->title = "Modificar Estado Alumno";
        $this->view->headTitle($this->view->title);
        $form = new Application_Form_Cambiarestado();
        $this->view->form = $form;

        //si el usuario envia datos del form
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                $idalumnoActual = $form->getValue('idAlumnosActual');
                $idestado = $form->getValue('idEstado');

                $alumnos = new Application_Model_DbTable_Alumnosactual();

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $db->beginTransaction();
                try {

                    $alumnos->cambiarestado($idalumnoActual, $idestado);
                    $db->commit();
                    $this->_helper->redirector('index');

                } catch (Exception $e) {
                    // Si hubo problemas. Enviamos marcha atras
                    $db->rollBack();
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al actualizar el estado, intente nuevamente');
                    $this->view->assign('messages', $messages);
                }
            } else {

                $this->view->form = $form;
                $form->populate($formData);
            }
        } else {

            $ideditar = $this->_getParam('id', 0);
            if ($ideditar > 0) {
                $alumnosmodel = new Application_Model_DbTable_Alumnosactual();
                $tabla = $alumnosmodel->get($ideditar);
                $form->populate($tabla[0]);
            }
        }
    }

    public function subiralumnosAction()
    {

        $this->view->title = "Subir Alumnos";
        $this->view->headTitle($this->view->title);
        $form = new Application_Form_SubirAlumnos();
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                try {
                    $originalFilename = pathinfo($form->excel->getFileName());
                    if ($originalFilename == null) {
                        $newFilename = '0';

                    } else {
                        $newFilename = 'excel-' . uniqid() . '.' . $originalFilename['extension'];
                    }

                    $upload = $form->excel->getTransferAdapter();

                    $form->excel->addFilter('Rename', $newFilename);

                    $upload = $form->excel->receive();
                    if ($upload) {
                        $this->datosalumnosAction($newFilename);
                    } else {

                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Ha ocurido un error, al ingresar los datos, intente nuevamente' . $upload);
                        $this->view->assign('messages', $messages);
                    }

                } catch (Exception $e) {

                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al actualizar el estado, intente nuevamente');
                    $this->view->assign('messages', $messages);
                }
            } else {

                $this->view->form = $form;
                $form->populate($formData);
            }
        }

    }

    public function datosalumnosAction($nombre)
    {


        $modelperiodo = new Application_Model_DbTable_Periodo();
        $modelestablecimiento = new Application_Model_DbTable_Establecimiento();
        $modelcurso = new Application_Model_DbTable_Cursos();
        $modelalumnos = new Application_Model_DbTable_Alumnos();
        $modelalumnosactual = new Application_Model_DbTable_Alumnosactual();


        //Codigos del Sige
        /*
        10 - 4 Primer Nivel de Transicion
        10 - 5 Segundo Nivel de Transicion

        100 - 1 Primero Básico
        100 - 2 Segundo Básico
        100 - 3 Tercero Básico
        100 - 4 Cuarto Básico
        100 - 5 Quinto Básico
        100 - 6 Sexto Básico
        100 - 7 Séptimo Básico
        100 - 8 Octavo Básico
         */

        require 'PHPExcel-1.8/Classes/PHPExcel.php';
        $tmpfname = APPLICATION_UPLOADS_DIR . "/" . $nombre;
        $excelReader = PHPExcel_IOFactory::createReaderForFile($tmpfname);
        $excelObj = $excelReader->load($tmpfname);
        $worksheet = $excelObj->getSheet(0);
        $lastRow = $worksheet->getHighestRow();

        //Buscamos el Periodo
        $idperiodo = $modelperiodo->getname($worksheet->getCell('A2')->getValue());

        if (!$idperiodo) {
            $modelperiodo->agregar($worksheet->getCell('A2')->getValue());
            $idperiodo = $modelperiodo->getAdapter()->lastInsertId();
        } else {

            $idperiodo = $idperiodo[0]['idPeriodo'];
        }

        //Buscamos el establecimiento

        $idestablecimiento = $modelestablecimiento->getnamerbd($worksheet->getCell('B2')->getValue());
        if (!$idestablecimiento) {
            var_dump('no existe establecimiento');
        } else {

            $idestablecimiento = $idestablecimiento[0]['idEstablecimiento'];
        }
        try {
            $db = Zend_Db_Table_Abstract::getDefaultAdapter();

            // Iniciamos la transaccion
            $db->beginTransaction();


            for ($row = 1; $row <= $lastRow; $row++) {

                if ($row > 1) {


                    $datoscurso = $modelcurso->getcursoalumno($worksheet->getCell('C' . $row)->getValue(), $worksheet->getCell('D' . $row)->getValue(), $worksheet->getCell('F' . $row)->getValue(), $idperiodo, $idestablecimiento);

                    //buscamos el rut del alumno, si existe guadamos el id, si no creamos el id
                    $rut = $worksheet->getCell('G' . $row)->getValue() . '' . $worksheet->getCell('H' . $row)->getValue();
                    $nombres = $worksheet->getCell('J' . $row)->getValue();
                    $apaterno = $worksheet->getCell('K' . $row)->getValue();
                    $amaterno = $worksheet->getCell('L' . $row)->getValue();
                    $direccion = $worksheet->getCell('M' . $row)->getValue();
                    $telefono = intval($worksheet->getCell('Q' . $row)->getValue());
                    $celular = intval($worksheet->getCell('R' . $row)->getValue());
                    $comuna = intval($worksheet->getCell('O' . $row)->getValue());

                    if ($worksheet->getCell('I' . $row)->getValue() == "F") {
                        $sexo = 1;
                    } else {
                        $sexo = 2;
                    }

                    if (strlen($rut) > 9) {
                        $tipoidentificacion = 2;
                    } else {
                        $tipoidentificacion = 1;
                    }


                    $correo = '';
                    // utilizo la función y obtengo el timestamp
                    $timestamp = PHPExcel_Shared_Date::ExcelToPHP($worksheet->getCell('S' . $row)->getValue());
                    $fecha_php = date("Y-m-d", $timestamp);
                    $fechanacimiento = $fecha_php;

                    //Fecha incorporacion

                    // utilizo la función y obtengo el timestamp
                    $timestampin = PHPExcel_Shared_Date::ExcelToPHP($worksheet->getCell('U' . $row)->getValue());
                    $fechaincorporacion = date("Y-m-d", $timestampin);
                    $idapoderado = 0;
                    $idapoderados = 0;
                    $prioritario = 0;
                    $beneficio = 0;
                    $numeromatricula = 1;
                    $numerom = $modelalumnosactual->getnumeromatricula($datoscurso[0]['idEstablecimiento'], $datoscurso[0]['idCodigoTipo'], $idperiodo);
                    if (!empty($numerom[0]['max'])) {
                        $numeromatricula = $numerom[0]['max'] + 1;
                    }

                    $foto = '';
                    $calle = '';
                    $villa = '';
                    $numerocasa = 0;
                    $ciudadactual = '';
                    $pais = 45;
                    $ciudad = '';
                    $nacionalidad='';
                    $etnia='';


                    try {

                        $verifica = $modelalumnos->validaralumno($rut);

                        if (!$verifica) {
                            $ultimo = $modelalumnosactual->ultimo($datoscurso[0]['idCursos'], $idperiodo);

                            $modelalumnos->agregar($rut, $nombres, $apaterno, $amaterno,  $sexo, $fechanacimiento,$nacionalidad,$etnia,$pais, $ciudad);

                            $idalumnosnuevos[$row] = $modelalumnos->getAdapter()->lastInsertId();
                            $modelalumnosactual->agregar($idalumnosnuevos[$row], $datoscurso[0]['idCursos'], $idperiodo, $ultimo[0]['max'] + 1, $fechaincorporacion, $numeromatricula, $calle, $numerocasa, $villa, $ciudadactual, $telefono, $celular, $comuna, $correo, null, null, null, $tipoidentificacion);

                        } else {
                            $ultimo = $modelalumnosactual->ultimo($datoscurso[0]['idCursos'], $idperiodo);
                            $idalumnos[$row] = $verifica[0]['idAlumnos'];
                            $verificaactual = $modelalumnosactual->validar($verifica[0]['idAlumnos'], $idperiodo);
                            if ($verificaactual) {
                                $modelalumnosactual->agregar($idalumnos[$row], $datoscurso[0]['idCursos'], $idperiodo, $ultimo[0]['max'] + 1, $fechaincorporacion, $numeromatricula, $calle, $numerocasa, $villa, $ciudadactual, $telefono, $celular, $comuna, $correo, null, null, null, $tipoidentificacion);
                            }

                        }
                        $db->commit();
                    } catch (Exception $e) {
                        $db->rollBack();

                        echo $e;
                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Ha ocurido un error, al ingresar los datos del rut' . $rut);

                        $this->view->assign('messages', $messages);
                        exit();
                    }

                }

            }
        } catch (Exception $e) {
            // Si hubo problemas. Enviamos  marcha atras
            $db->rollBack();
            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al actualizar el estado, intente nuevamente' . $e);
            $this->view->assign('messages', $messages);
        }


    }

    public function getalumnoidAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $modeloalumno = new Application_Model_DbTable_Alumnos();
        $modeloapoderado = new Application_Model_DbTable_Apoderados();
        $id = $this->_getParam('id');

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        if (!empty($id)) {
            $results = $modeloalumno->getidperiodo($id, $idperiodo);
            //Otenemos los datos del apoderado Suplente si es que existen
            if ($results[0]['idApoderadoSuplente'] > 0) {
                $datossuplente = $modeloapoderado->get($results[0]['idApoderadoSuplente']);

                $results[0]['rutApoderadoSuplente'] = $datossuplente['rutApoderado'];
                $results[0]['nombreApoderadoSuplente'] = $datossuplente['nombreApoderado'];
                $results[0]['paternoApoderadoSuplente'] = $datossuplente['paternoApoderado'];
                $results[0]['maternoApoderadoSuplente'] = $datossuplente['maternoApoderado'];
                $results[0]['telefonoApoderadoSuplente'] = $datossuplente['telefonoApoderado'];
                $results[0]['direccionApoderadoSuplente'] = $datossuplente['direccionApoderado'];
                $results[0]['correoApoderadoSuplente'] = $datossuplente['correoApoderado'];
                $results[0]['comunaApoderadoSuplente'] = $datossuplente['comunaApoderado'];

            }
            if ($results[0]['rutApoderado'] > 0) {

                $results[0]['rutApoderado'] = number_format(substr($results[0]['rutApoderado'], 0, -1), 0, "", ".") . '-' . substr($results[0]['rutApoderado'], strlen($results[0]['rutApoderado']) - 1, 1);


            }

            $results[0]['fechaInscripcion'] = date("d-m-Y", strtotime($results[0]['fechaInscripcion']));
            $results[0]['fechanacimiento'] = date("d-m-Y", strtotime($results[0]['fechanacimiento']));
            $results[0]['rutAlumno'] = number_format(substr($results[0]['rutAlumno'], 0, -1), 0, "", ".") . '-' . substr($results[0]['rutAlumno'], strlen($results[0]['rutAlumno']) - 1, 1);


            $this->_helper->json($results);
        } else {
            echo Zend_Json::encode(array('response' => 'error'));
        }


    }

    public function retirarAction()
    {

        $this->view->title = "Retirar Alumno";
        $this->view->headTitle($this->view->title);
        $form = new Application_Form_RetirarAlumno();
        $form->setDecorators(
            array(
                'FormElements',
                'Form',
            )
        );

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $identificadors = new Zend_Session_Namespace('identificador');
        $ididentificador = $identificadors->identificador;

        $modeloalumnoactual = new Application_Model_DbTable_Alumnosactual();
        $modelocurso = new Application_Model_DbTable_Cursos();
        $idalumnoactual = $this->_getParam('id', 0);
        $datosalumno = $modeloalumnoactual->getactual($idalumnoactual, $idperiodo);
        $detalleest = $modelocurso->listarcursoid($datosalumno[0]['idCursosActual'], $idperiodo);

        $form->submit->setLabel('Retirar Alumno');
        $this->view->form = $form;

        $this->view->alumno = $datosalumno[0]['nombres'] . ' ' . $datosalumno[0]['apaterno'] . ' ' . $datosalumno[0]['amaterno'];
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                $idalumnoactual = $form->getValue('idAlumnosActual');
                $fecharetiro = $form->getValue('fechaRetiro');
                $motivo = $form->getValue('motivo');
                $fecharetiro = date("Y-m-d", strtotime($fecharetiro));

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $db->beginTransaction();
                try {
                    if ($datosalumno[0]['idCodigoTipo'] != 10) {

                        if ($detalleest[0]["activarapp"] == 1) {


                            //Logeamos al usuario master y obtenemos el token

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, "http://api.softinnova.cl/logins?");
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

                            //API URL Obtenemos los dias feriados desde la api

                            //Seteamos los ultimo 4 digitos del rut
                            $rut = $datosalumno[0]['rutAlumno'];
                            $ch = curl_init();

                            curl_setopt($ch, CURLOPT_URL, "http://api.softinnova.cl/eliminaralumno?");
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS,
                                "user=" . $rut . "&identificador=" . $ididentificador . "&userc=76189207k");
                            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Acceso: ' . $token));

                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                            //execute the POST request
                            $result = curl_exec($ch);
                            //close cURL resource
                            curl_close($ch);
                            $resultadodeco = json_decode($result);


                            if (!$resultadodeco->success) {
                                //Actualizaos el Estado del Alumno si está Activo en la App

                                $modeloalumnoactual->cambiarestadoapp($idalumnoactual, 6);

                                $modeloalumnoactual->retiraralumno($idalumnoactual, $fecharetiro, $motivo);
                                $db->commit();
                                $this->_helper->redirector('index');


                            } else {
                                $db->rollBack();
                                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al retirar al alumno, intente nuevamente');
                                $this->view->assign('messages', $messages);
                            }

                        } else {

                            // Retiramos solo el alumnos
                            $modeloalumnoactual->retiraralumno($idalumnoactual, $fecharetiro, $motivo);
                            $db->commit();
                            $this->_helper->redirector('index');

                        }
                        //Fin App Apoderados

                    } else {
                        // Retiramos solo el alumnos
                        $modeloalumnoactual->retiraralumno($idalumnoactual, $fecharetiro, $motivo);
                        $db->commit();
                        $this->_helper->redirector('index');
                    }


                } catch (Exception $e) {

                    // Si hubo problemas. Enviamos  marcha atras
                    $db->rollBack();
                    echo $e;
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al retirar al alumno, intente nuevamente');
                    /// Assign the messages
                    $this->view->assign('messages', $messages);
                }


            } else {


                $form->populate($formData);
            }
        } else {
            $idalumnoactual = $this->_getParam('id', 0);
            if ($idalumnoactual > 0) {
                $datosalumno = $modeloalumnoactual->getactual($idalumnoactual, $idperiodo);
                $form->populate($datosalumno[0]);


            }
        }

    }

    public function reincorporarAction()
    {

        $this->view->title = "Reincorparar Alumno";
        $this->view->headTitle($this->view->title);
        $form = new Application_Form_RetirarAlumno();
        $form->setDecorators(
            array(
                'FormElements',
                'Form',
            )
        );

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $identificadors = new Zend_Session_Namespace('identificador');
        $ididentificador = $identificadors->identificador;

        $modeloalumnoactual = new Application_Model_DbTable_Alumnosactual();
        $modelocurso = new Application_Model_DbTable_Cursos();
        $idalumnoactual = $this->_getParam('id', 0);
        $datosalumno = $modeloalumnoactual->getactualperiodo($idalumnoactual, $idperiodo);
        $detalleest = $modelocurso->listarcursoid($datosalumno[0]['idCursosActual'], $idperiodo);
        $form->fechaRetiro->setLabel('Fecha Reincorporación(*)');
        $form->join->setAttrib('onclick', 'window.location =\'' . $this->_request->getBaseUrl() . ('/Alumnos/indexr') . '\' ');


        $form->submit->setLabel('Guardar');
        $this->view->form = $form;

        $this->view->alumno = $datosalumno[0]['nombres'] . ' ' . $datosalumno[0]['apaterno'] . ' ' . $datosalumno[0]['amaterno'];

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                $idalumnoactual = $form->getValue('idAlumnosActual');
                $fecharetiro = $form->getValue('fechaRetiro');
                $motivo = $form->getValue('motivo');
                $fecharetiro = date("Y-m-d", strtotime($fecharetiro));

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $db->beginTransaction();
                try {

                    if ($datosalumno[0]['idCodigoTipo'] != 10) {

                        if ($detalleest[0]["activarapp"] == 1) {

                            //Logeamos al usuario master y obtenemos el token

                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, "http://api.softinnova.cl/logins?");
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


                            //API URL Obtenemos los dias feriados desde la api

                            $rut = $datosalumno[0]['rutAlumno'];
                            //Seteamos los ultimo 4 digitos del rut
                            $pass = substr($rut, 5, 8);
                            $ch = curl_init();

                            curl_setopt($ch, CURLOPT_URL, "http://api.softinnova.cl/crearalumno?");
                            curl_setopt($ch, CURLOPT_POST, 1);
                            curl_setopt($ch, CURLOPT_POSTFIELDS,
                                "user=" . $rut . "&password=" . $pass . "&identificador=" . $ididentificador . "&idCursos=" . $datosalumno[0]['idCursosActual'] . "&idAlumno=" . $idalumnoactual . "&idPeriodo=" . $idperiodo . "&userc=76189207k");
                            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Acceso: ' . $token));
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                            //execute the POST request
                            $result = curl_exec($ch);
                            //close cURL resource
                            curl_close($ch);
                            $resultadodeco = json_decode($result);
                            //Zend_Debug::dump($pass);

                            if ($resultadodeco->success) {
                                //Actualizaos el Estado del Alumno si está Activo en la App

                                $modeloalumnoactual->cambiarestadoapp($idalumnoactual, 5);

                                $modeloalumnoactual->reincorporaralumno($idalumnoactual, $fecharetiro, $motivo);
                                $db->commit();
                                $this->_helper->redirector('index');


                            } else {

                                // Si hubo problemas. Enviamos  marcha atras
                                $db->rollBack();
                                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al reincorporar al alumno, intente nuevamente');
                                $this->view->assign('messages', $messages);
                            }


                            //Fin App Apoderados

                        } else {

                            //Solo Reincorporamos al alumno
                            $modeloalumnoactual->reincorporaralumno($idalumnoactual, $fecharetiro, $motivo);
                            $db->commit();
                            $this->_helper->redirector('index');

                        }


                    } else {

                        //Solo Reincorporamos al alumno
                        $modeloalumnoactual->reincorporaralumno($idalumnoactual, $fecharetiro, $motivo);
                        $db->commit();
                        $this->_helper->redirector('index');
                    }


                } catch (Exception $e) {

                    // Si hubo problemas. Enviamos  marcha atras
                    $db->rollBack();
                    echo $e;
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al reincorporar al alumno, intente nuevamente');
                    $this->view->assign('messages', $messages);
                }


            } else {


                $form->populate($formData);
            }
        } else {
            $idalumnoactual = $this->_getParam('id', 0);
            if ($idalumnoactual > 0) {
                $datosalumno = $modeloalumnoactual->getactualperiodo($idalumnoactual, $idperiodo);


                //Validamos que el alumno no este activo en otro establecimiento o curso

                $datos = $modeloalumnoactual->getalumnoestado($datosalumno[0]['idAlumnos'], $idperiodo, 1);


                if (count($datos) == 0) {
                    $datosalumno[0]['fechaRetiro'] = date("d-m-Y", strtotime($datosalumno[0]['fechaRetiro']));
                    $form->populate($datosalumno[0]);
                } else {

                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No se puede reincorporar, el alumno, está activo en el periodo escolar');
                    $this->view->assign('messages', $messages);

                }


            }
        }

    }

    public function activarAction()
    {

        $this->view->title = "Activar Alumnos";
        $this->view->headTitle($this->view->title);

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;


        $identificadors = new Zend_Session_Namespace('identificador');
        $ididentificador = $identificadors->identificador;

        $modeloalumnoactual = new Application_Model_DbTable_Alumnosactual();
        $listadealumnos = $modeloalumnoactual->listartotalalumnos($idperiodo, array(10), array(5));

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $db->beginTransaction();


        //Logeamos al usuario master y obtenemos el token

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://api.softinnova.cl/logins?");
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

        if (empty($token)) {
            //if(!empty($token)){

            foreach ($listadealumnos as $a => $j) {

                //Si el alumno está activo no realizamos la operacion de activarlo

                if ($j['idEstadoApp'] == 6 || empty($j['idEstadoApp'])) {

                    $rut = $j['rutAlumno'];
                    $idcurso = $j['idCursos'];
                    $idalumnoactual = $j['idAlumnosActual'];

                    //Convertimos el rut
                    $rut = mb_convert_case($rut, MB_CASE_LOWER, "UTF-8");
                    $pass = substr($rut, 0, 6);

                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "http://api.softinnova.cl/crearalumno?");
                    curl_setopt($ch, CURLOPT_POST, 1);

                    curl_setopt($ch, CURLOPT_POSTFIELDS,
                        "user=" . $rut . "&password=" . $pass . "&identificador=" . $ididentificador . "&idCursos=" . $idcurso . "&idAlumno=" . $idalumnoactual . "&idPeriodo=" . $idperiodo . "&userc=76189207k");

                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Acceso: ' . $token));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    //execute the POST request
                    $result = curl_exec($ch);
                    //close cURL resource
                    curl_close($ch);
                    $resultadodeco = json_decode($result);
                    if ($resultadodeco->success) {
                        //Actualizaos el Estado del Alumno si se Activo en la App
                        $modeloalumnoactual->cambiarestadoapp($idalumnoactual, 5);
                        $db->commit();

                    }

                    //Fin App Apoderados

                } else {

                    //Actualizamos al Alumno

                    $rut = $j['rutAlumno'];
                    $idcurso = $j['idCursos'];
                    $idalumnoactual = $j['idAlumnosActual'];

                    //Convertimos el rut
                    $rut = mb_convert_case($rut, MB_CASE_LOWER, "UTF-8");


                    if (strlen($rut) > 9) {
                        $pass = substr($rut, 0, 7);
                    } else {
                        $pass = substr($rut, 0, 7);
                    }


                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, "http://api.softinnova.cl/actualizaralumno?");
                    curl_setopt($ch, CURLOPT_POST, 1);

                    curl_setopt($ch, CURLOPT_POSTFIELDS,
                        "user=" . $rut . "&password=" . $pass . "&identificador=" . $ididentificador . "&idCursos=" . $idcurso . "&idAlumno=" . $idalumnoactual . "&idPeriodo=" . $idperiodo . "&userc=76189207k");
                    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Acceso: ' . $token));
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                    //execute the POST request
                    $result = curl_exec($ch);
                    //close cURL resource
                    curl_close($ch);
                    $resultadodeco = json_decode($result);
                    if ($resultadodeco->success) {
                        //Actualizaos el Estado del Alumno si se Activo en la App
                        $modeloalumnoactual->cambiarestadoapp($idalumnoactual, 5);
                        $db->commit();

                    }

                }

            }
            $this->_helper->redirector('index');

        } else {
            echo 'Error';
        }


    }

}
