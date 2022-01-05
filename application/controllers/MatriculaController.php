<?php

class MatriculaController extends Zend_Controller_Action
{

    public function init()
    {
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();
    }

    public function indexAction()
    {


    }


    public function editarAction()
    {
        //titulo de la pagina
        $this->view->title = "Modificar Alumno";
        $this->view->headTitle($this->view->title);
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;
        //creo el formulario
        $form = new Application_Form_Alumnos();
        //elimino los decoradores de zend form
        $form->setDecorators(
            array(
                'FormElements',
                'Form',
            )
        );

        $this->view->form = $form;

        //si el usuario envia datos del form
        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            //veo si son validos
            if ($form->isValid($formData)) {

                $idalumno = $form->getValue('idAlumnos');
                $periodo_get = $form->getValue('periodo');
                $rut = $form->getValue('rutAlumno');
                $RBD_alumno = $form->getValue('idEstablecimiento');
                $curso = $form->getValue('idCursos');
                $nombres = $form->getValue('nombres');
                $apaterno = $form->getValue('apaterno');
                $amaterno = $form->getValue('amaterno');
                $direccion = $form->getValue('direccion');
                $telefono = $form->getValue('telefono');
                $movil = $form->getValue('celular');
                $comuna = $form->getValue('comuna');
                $correo = $form->getValue('correo');
                $sexo = $form->getValue('sexo');
                $fechanacimiento = $form->getValue('fechanacimiento');
                $idApoderado = $form->getValue('idApoderado');

                $idApoderados = $form->getValue('idApoderadoS');
                $prioritario = $form->getValue('prioritario');
                $beneficio = $form->getValue('beneficio');
                $numeromatricula = $form->getValue('numeromatricula');

                //datos medicos alumnos
                $prevision = $form->getValue('prevision');
                $letra = $form->getValue('letra');
                $actividad = $form->getValue('actividad');
                $patologia = $form->getValue('patologia');
                $hora = $form->getValue('horario');
                $profesional = $form->getValue('profesional');
                $tprofesional = $form->getValue('telefonoProfesional');
                $documento = $form->getValue('documento');

                //datos medicos alumnos

                $patologia = $form->getValue('patologia');
                $hora = $form->getValue('horario');
                $profesional = $form->getValue('profesional');
                $tprofesional = $form->getValue('telefonoProfesional');
                $documento = $form->getValue('documento');

                //datos nuecleo familiar alumno
                $rutpadre = $form->getValue('rutPadre');
                $nombrepadre = $form->getValue('nombrepadre');
                $apellidopadre = $form->getValue('apellidopadre');
                $telefonopadre = $form->getValue('telefonopadre');
                $nivelpadre = $form->getValue('nivelpadre');
                $ocupacionpadre = $form->getValue('ocupacionPadre');

                $rutmadre = $form->getValue('rutMadre');
                $nombremadre = $form->getValue('nombremadre');
                $apellidomadre = $form->getValue('apellidomadre');
                $telefonomadre = $form->getValue('telefonomadre');
                $nivelmadre = $form->getValue('nivelmadre');
                $ocupacionmadre = $form->getValue('ocupacionMadre');

                //creo objeto Establecimiento que controla la talba Alumnos de la base de datos
                $alumnos = new Application_Model_DbTable_Alumnos();
                $alumnosactual = new Application_Model_DbTable_Alumnosactual();
                $medicos = new Application_Model_DbTable_Alumnomedico();
                $familiar = new Application_Model_DbTable_Alumnonucleo();

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                // Iniciamos la transaccion
                $db->beginTransaction();
                try {
                    if ($fechanacimiento != "0000-00-00") {

                    } else {

                    }


                    $alumnos->cambiar($idalumno, $rut, $nombres, $apaterno, $amaterno, $direccion, $telefono, $movil, $comuna, $correo, $sexo, $fechanacimiento, $idApoderado, $curso, $idApoderados, $prioritario, $beneficio, $numeromatricula);
                    $medicos->cambiar($prevision, $letra, $actividad, $patologia, $profesional, $tprofesional, $hora, $documento, $idalumno);
                    $familiar->cambiar($nombremadre, $apellidomadre, $telefonomadre, $nivelmadre, $nombrepadre, $apellidopadre, $telefonopadre, $nivelpadre, $rutpadre, $rutmadre, $ocupacionpadre, $ocupacionmadre, $idalumno);

                    //Actualizamos la tabla de los alumnos actual
                    $alumnosactual->actualizarcurso($idalumno, $periodo_get, $curso);
                    $db->commit();
                    $this->_helper->redirector('index');

                } catch (Exception $e) {
                    // Si hubo problemas. Enviamos todo marcha atras
                    $db->rollBack();
                    echo $e;
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al ingresar los datos, intente nuevamente');
                    /// Assign the messages
                    //$this->view->assign('messages', $messages);
                }
            } else {
                //carga la provincia y la comuna seleccionada anteriormente y la carga en el formulario con los errores
                $curso = $form->getValue('idCursos');
                $cursos = new Application_Model_DbTable_Cursos();
                $setcurso = $cursos->getnombreactual($curso);
                $rowsetcursos = $cursos->getValueCurso($setcurso[0]['idEstablecimiento']);
                $form->idCursos->clearMultiOptions();
                $form->idCursos->addMultiOptions($rowsetcursos);
                $this->view->form = $form;
                $form->populate($formData);
            }
        } else {
            $rut = $this->_getParam('id', 0);
            if ($rut > 0) {

                $personal = new Application_Model_DbTable_Alumnos();
                $alumnosactual = new Application_Model_DbTable_Alumnosactual();
                $tabla = $personal->getcomunaalumno($rut);
                $tablacompleta = $personal->get($rut);
                $resultadoalumnoactual = $alumnosactual->getactual($rut, $idperiodo);

                $periodoprueba = $tabla[0]['fechanacimiento'];
                if (!empty($periodoprueba)) {
                    $fecha2 = date("d-m-Y", strtotime($periodoprueba));
                    $tabla[0]['fechanacimiento'] = $fecha2;
                }

                $cursos = new Application_Model_DbTable_Cursos();
                $setcurso = $cursos->getnombreactual($resultadoalumnoactual[0]['idCursosActual']);
                $rowsetcursos = $cursos->getValueCurso($setcurso[0]['idEstablecimiento']);

                $form->idEstablecimiento->setValue($resultadoalumnoactual[0]['idEstablecimiento']);
                $form->populate($tablacompleta[0]);
                $form->idCursos->clearMultiOptions();
                $form->idCursos->addMultiOptions($rowsetcursos);
                $form->idCursos->setValue($resultadoalumnoactual[0]['idCursosActual']);
                $form->periodo->setValue($idperiodo);
                $form->idAlumnos->setValue($rut);

            }
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

            //$actual->remove($rut, $idperiodo);
            $tablamed = new Application_Model_DbTable_Alumnomedico();
            //$tablamed->borrar($rut);
            $tablanuc = new Application_Model_DbTable_Alumnonucleo();
            //$tablanuc->borrar($rut);
            $tabla = new Application_Model_DbTable_Alumnos();
            //$tabla->borrar($rut);
            //$db->commit();
            $this->_helper->redirector('index');
        } catch (Exception $e) {
            // Si hubo problemas. Enviamos todo marcha atras
            $db->rollBack();
            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No se puede eliminar este registro, posee datos asociados');

            $this->view->assign('messages', $messages);
        }
    }


    public function getalumnosAction()
    {
        $modelModelo = new Application_Model_DbTable_Alumnos();
        $results = $modelModelo->getAsKeyValueJSON($this->_getParam('id'));
        $this->_helper->json($results);
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
        $modelModelo = new Application_Model_DbTable_Cursos();
        $results = $modelModelo->getcursojson($this->_getParam('id'), $idperiodo);
        $this->_helper->json($results);
    }

    public function getalumnoAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $modeloalumno = new Application_Model_DbTable_Alumnos();
        $rut = $this->_getParam('search');
        if (is_numeric($rut)) {
            $tipo = 2;

        } else {
            if (strpos($rut, ' ') !== false) {
                $tipo = 3;
                $dividir = explode(" ", $rut);
                $rut = $dividir[0];

                if (strlen($dividir[1]) > 0) {
                    $materno = $dividir[1];
                } else {
                    $tipo = 1;
                }
            } else {
                $tipo = 1;
            }
        }

        if (strlen($rut) > 2) {


            if (!empty($rut)) {
                if ($tipo == 1 || $tipo == 2) {
                    $results = $modeloalumno->getalumno($rut, $tipo, '');
                } elseif ($tipo == 3) {

                    $results = $modeloalumno->getalumno($rut, $tipo, $materno);

                }


                $resultado = '';
                for ($i = 0; $i < count($results); $i++) {
                    if ($tipo == 2) {
                        $resultado[$i]['rut'] = $results[$i]['rutAlumno'];
                    }

                    $resultado[$i]['id'] = $results[$i]['idAlumnos'];
                    $resultado[$i]['apaterno'] = $results[$i]['apaterno'];
                    $resultado[$i]['amaterno'] = $results[$i]['amaterno'];
                    $resultado[$i]['nombres'] = $results[$i]['nombres'];

                }
                $this->_helper->json($resultado);
            } else {
                echo Zend_Json::encode(array('response' => 'error'));
            }
        }

    }


    public function getalumnoidAction()
    {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();
        $modeloalumno = new Application_Model_DbTable_Alumnos();
        $modeloapoderado = new Application_Model_DbTable_Apoderados();
        $id = $this->_getParam('id');

        if (!empty($id)) {
            $results = $modeloalumno->get($id);
            // Zend_Debug::dump($results);
            //Otenemos los datos del apoderado Suplente si es que existen
            if ($results[0]['idApoderados'] > 0) {
                $datossuplente = $modeloapoderado->get($results[0]['idApoderados']);
                //var_dump($datossuplente);

                $results[0]['rutApoderadoSuplente'] = $datossuplente['rutApoderado'];
                $results[0]['nombreApoderadoSuplente'] = $datossuplente['nombreApoderado'];
                $results[0]['paternoApoderadoSuplente'] = $datossuplente['paternoApoderado'];
                $results[0]['maternoApoderadoSuplente'] = $datossuplente['maternoApoderado'];
                $results[0]['telefonoApoderadoSuplente'] = $datossuplente['telefonoApoderado'];
                $results[0]['direccionApoderadoSuplente'] = $datossuplente['direccionApoderado'];
                $results[0]['correoApoderadoSuplente'] = $datossuplente['correoApoderado'];
                $results[0]['comunaApoderadoSuplente'] = $datossuplente['comunaApoderado'];

            }
            $pag = $this->_request->getBaseUrl() . ('/Informes/generamatricula/id/' . $results[0]['idAlumnosActual']);
            $results[1] = array('url' => $pag);
            //Zend_Debug::dump($results);
            $this->_helper->json($results);
        } else {
            echo Zend_Json::encode(array('response' => 'error'));
        }


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
            if (empty($usuario)) {
                die(json_encode(["response" => 0, "status" => "1"]));
            } else {

                //Modelos a utilizar
                $alumnos = new Application_Model_DbTable_Alumnos();
                $alumnosactual = new Application_Model_DbTable_Alumnosactual();
                $medicos = new Application_Model_DbTable_Alumnomedico();
                $familiar = new Application_Model_DbTable_Alumnonucleo();
                $apoderados = new Application_Model_DbTable_Apoderados();

                $rut = trim($data['rutAlumno']);
                $caracteres = array(",", ".", "-");
                $rut = str_replace($caracteres, "", $rut);
                $idestablecimiento = $data['idEstablecimiento'];
                $curso = $data['idCursos'];

                $nombres = trim($data['nombres']);
                $apaterno = trim($data['apaterno']);
                $amaterno = trim($data['amaterno']);
                $direccion = $data['direccion'];
                $telefono = $data['telefono'];
                $movil = $data['celular'];
                $comuna = $data['comuna'];
                $correo = $data['correo'];
                $sexo = $data['sexo'];
                $fechanacimiento = $data['fechanacimiento'];
                $fechanacimiento2 = $data['fechanacimiento'];

                //Validar Datos
                if ($telefono == "") {
                    $telefono = 0;
                }

                if ($movil == "") {
                    $movil = 0;
                }


                $prioritario = $data['prioritario'];
                $beneficio = $data['beneficio'];
                $numeromatricula = $data['numeromatricula'];
                if ($numeromatricula == "") {
                    $numeromatricula = 0;
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
                $alumnosverifica = $alumnosverificas->validar($rut);
                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                if (empty($rut) || empty($nombres) || empty($apaterno) || empty($amaterno) || empty($curso) || empty($fechanacimiento) || $fechanacimiento == "0000-00-00") {
                    die(json_encode(["response" => 0, "status" => "3"]));
                }

                $db->beginTransaction();
                try {

                    $ultimo=$alumnosactual->ultimo($curso,$idperiodo);
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
                                        $idApoderado = $alumnos->getAdapter()->lastInsertId();
                                    } else {
                                        $apoderados->cambiarportur($rutapoderado, $nombreapoderado, $paternoapoderado, $maternoapoderado, $direccionapoderado, $telefonoapoderado, $comunaapoderado, '');
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
                                    $apoderados->cambiarportur($rutapoderado, $nombreapoderado, $paternoapoderado, $maternoapoderado, $direccionapoderado, $telefonoapoderado, $comunaapoderado, '');
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
                                        $apoderados->cambiarportur($rutapoderadosuplente, $nombreapoderadosuplente, $paternoapoderadosuplente, $maternoapoderadosuplente, $direccionapoderadosuplente, $telefonoapoderadosuplente, $comunaapoderadosuplente, $correoapoderadosuplente);
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
                                    $apoderados->cambiarportur($rutapoderadosuplente, $nombreapoderadosuplente, $paternoapoderadosuplente, $maternoapoderadosuplente, $direccionapoderadosuplente, $telefonoapoderadosuplente, $comunaapoderadosuplente, $correoapoderadosuplente);
                                    $datoss = $apoderados->getrut($rutapoderadosuplente);
                                    //Zend_Debug::dump($datoss);
                                    if ($datoss) {
                                        $idApoderados = $datoss['idApoderado'];
                                    }


                                }

                            }
                        }


                        //Ingresamos los datos generales del alumnos

                        $alumnos->agregar($rut, $nombres, $apaterno, $amaterno, $direccion, $telefono, $movil, $comuna, $correo, $sexo, $fechanacimiento, $idApoderado, $curso, $idApoderados, $prioritario, $beneficio, $numeromatricula, $archivo, $nacionalidad, $etnia, $religion, $junaeb, $pie, $autorizacion, $aprendizaje, $transporte);
                        $idingresado = $alumnos->getAdapter()->lastInsertId();


                        $medicos->agregar($prevision, $letra, $actividad, $patologia, $profesional, $tprofesional, $hora, $documento, $persona, $telefonoemergencia, $traslado, $personaretira, $telefonoretira, $rutretira, $tratamiento, $otrotratamieto, $entratamiento, $alegia, $idingresado);

                        $familiar->agregar($nombremadre, $paternomadre, $maternomadre, $telefonomadre, $nivelmadre, $nombrepadre, $paternopadre, $maternopadre, $telefonopadre, $nivelpadre, $rutpadre, $rutmadre, $ocupacionpadre, $ocupacionmadre, $idingresado);


                        //$apoderados->agregar($rutapoderado,$nombreapoderado,$paternoapoderado,$maternoapoderado,$direccionapoderado,$telefonoapoderado,$comunaapoderado,'');
                        // Sino hubo ningun inconveniente hacemos un commit
                        //$upload = $form->foto->getTransferAdapter();

                        //$form->foto->addFilter('Rename', $newFilename);

                        //$upload = $form->foto->receive();

                        //si no existe se crea para el periodo actual.
                        if ($alumnosactual->validar($idingresado, $idperiodo)) {
                            $alumnosactual->agregar($idingresado, $curso, $idperiodo,$ultimo[0]['max']+1,$fechainscripcion,$numeromatricula);
                            $idalumnoactual = $alumnosactual->getAdapter()->lastInsertId();


                        } else {
                            //Actualizamos la tabla de los alumnos actual
                            $alumnosactual->actualizarcurso($idingresado, $idperiodo, $curso,$fechainscripcion,$numeromatricula);
                            $alumnosactual->cambiarestado($idingresado, $estado);
                            $datosactual = $alumnosactual->getactualalumno($idingresado, $idperiodo, $curso);
                            if ($datosactual) {
                                $idalumnoactual = $datosactual[0]['idAlumnosActual'];

                            }

                        }


//                        $alumnosactual->agregar($idingresado, $curso, $idperiodo);
//                        $idalumnosactual=$alumnosactual->getAdapter()->lastInsertId();

                        //Obtenemos las Evaluaciones Activas que posee el curso al que esta ingresando el alumno, excluyendo las que podria tener
                        $modelevaluacion = new Application_Model_DbTable_Pruebas();
                        $modelnotas = new Application_Model_DbTable_Notas();

                        $listapruebas = $modelevaluacion->listapruebas($curso, $idperiodo);
                        $date = new DateTime;
                        $fechaactual = $date->format('Y-m-d');
                        for ($i = 0; $i < count($listapruebas); $i++) {
                            $modelnotas->agregar($idingresado, $listapruebas[$i]['idAsignatura'], $curso, 0, $listapruebas[$i]['idCuenta'], $listapruebas[$i]['idEvaluacion'], $fechaactual, $idperiodo);

                        }

                        $db->commit();
                        $pag = $this->_request->getBaseUrl() . ('/Informes/generamatricula/id/' . $idalumnoactual);
                        //die(json_encode(["response" => 1, "ex" => $idingresado]));
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
                                    $apoderados->cambiarportur($rutapoderado, $nombreapoderado, $paternoapoderado, $maternoapoderado, $direccionapoderado, $telefonoapoderado, $comunaapoderado, '');
                                    $idApoderado = $alumnosverifica[0]['idApoderado'];
                                } else { // si no es igual se crea uno nuevo
                                    //validamos que no exista en la base de datos
                                    $validarapoderado = $apoderados->validar($rutapoderado);

                                    if ($validarapoderado) {
                                        $apoderados->agregar($rutapoderado, $nombreapoderado, $paternoapoderado, $maternoapoderado, $direccionapoderado, $telefonoapoderado, $comunaapoderado, '');
                                        $idApoderado = $alumnos->getAdapter()->lastInsertId();
                                    } else {
                                        $apoderados->cambiarportur($rutapoderado, $nombreapoderado, $paternoapoderado, $maternoapoderado, $direccionapoderado, $telefonoapoderado, $comunaapoderado, '');
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
                                    $apoderados->cambiarportur($rutapoderado, $nombreapoderado, $paternoapoderado, $maternoapoderado, $direccionapoderado, $telefonoapoderado, $comunaapoderado, '');
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
                                        $apoderados->cambiarportur($rutapoderadosuplente, $nombreapoderadosuplente, $paternoapoderadosuplente, $maternoapoderadosuplente, $direccionapoderadosuplente, $telefonoapoderadosuplente, $comunaapoderadosuplente, $correoapoderadosuplente);
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
                                    $apoderados->cambiarportur($rutapoderadosuplente, $nombreapoderadosuplente, $paternoapoderadosuplente, $maternoapoderadosuplente, $direccionapoderadosuplente, $telefonoapoderadosuplente, $comunaapoderadosuplente, $correoapoderadosuplente);
                                    $datoss = $apoderados->getrut($rutapoderadosuplente);

                                    if ($datoss) {
                                        $idApoderados = $datoss['idApoderado'];
                                    }


                                }

                            }
                        }


                        $alumnos->cambiar($alumnosverifica[0]['idAlumnos'], $rut, $nombres, $apaterno, $amaterno, $direccion, $telefono, $movil, $comuna, $correo, $sexo, $fechanacimiento, $idApoderado, $curso, $idApoderados, $prioritario, $beneficio, $numeromatricula, $nacionalidad, $etnia, $religion, $junaeb, $pie, $autorizacion, $aprendizaje, $transporte);

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
                        if ($alumnosactual->validar($alumnosverifica[0]['idAlumnos'], $idperiodo)) {
                            $alumnosactual->agregar($alumnosverifica[0]['idAlumnos'], $curso, $idperiodo,$ultimo[0]['max']+1,$fechainscripcion,$numeromatricula);
                            $idalumnoactual = $alumnosactual->getAdapter()->lastInsertId();


                        } else {
                            //Actualizamos la tabla de los alumnos actual
                            $alumnosactual->actualizarcurso($alumnosverifica[0]['idAlumnos'], $idperiodo, $curso,$fechainscripcion);
                            $datosactual = $alumnosactual->getactualalumno($alumnosverifica[0]['idAlumnos'], $idperiodo, $curso);
                            if ($datosactual) {
                                $alumnosactual->cambiarestado($datosactual[0]['idAlumnosActual'], $estado);
                                $idalumnoactual = $datosactual[0]['idAlumnosActual'];

                            }

                        }

                        $db->commit();
                        $pag = $this->_request->getBaseUrl() . ('/Informes/generamatricula/id/' . $idalumnoactual);
                        $results[1] = array('url' => $pag);

                        die(json_encode(["response" => 1, "url" => $pag]));
                    }
                } catch (Exception $e) {

                    // Si hubo problemas. Enviamos todo marcha atras
                    $db->rollBack();
                    var_dump($e);
                    die(json_encode(["response" => 0, "status" => "2"]));
                }

            }


        }
    }

    public function gettotalalumnosAction()
    {
        $modeloalumnos = new Application_Model_DbTable_Alumnosactual();
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $est = new Zend_Session_Namespace('establecimiento');
        $idestablecimiento = $est->establecimiento;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        if ($rol == 1) {
            $results = $modeloalumnos->totalalumnos($idperiodo);
        } else {
            $results = $modeloalumnos->totalalumnosest($idperiodo, $idestablecimiento);
        }
        $datos[0]["total"] = $results[0]['total'];
        $this->_helper->json($datos);
    }

}
