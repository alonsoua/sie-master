<?php

class CursosController extends Zend_Controller_Action
{

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

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        if ($rol == '2' || $rol == '3' || $rol == '6') {
            $this->view->dato = $table->listartodasactual($establecimiento, $idperiodo);

        }
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

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;


        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                $idestablecimiento = $form->getValue('idEstablecimiento');
                $idcodigo = $form->getValue('idCodigoTipo');
                $idnivel = $form->getValue('idNiveles');
                $letra = $form->getValue('letra');
                $idcuentajefe = $form->getValue('idCuentaJefe');
                $combinado = $form->getValue('combinado');
                $numero = '';
                $jornada = $form->getValue('tipoJornada');
                $codigosector = $form->getValue('codigoSector');
                $codigoespecialidad = $form->getValue('codigoEspecialidad');
                $codigoalternativa = $form->getValue('codigoAlternativa');
                $infraestructura = $form->getValue('infraestructura');

                if ($numero == null) {
                    $numero = 0;
                }

                if ($codigosector == 'Null' || empty($codigosector)) {
                    $codigosector = 0;
                }

                $curso = new Application_Model_DbTable_Cursos();

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $db->beginTransaction();
                try {
                    $curso->agregaractual($idestablecimiento, $idperiodo, $idcodigo, $idnivel, $letra, $idcuentajefe, $combinado, $numero, $jornada, $codigosector, $codigoespecialidad, $codigoalternativa, $infraestructura);
                    $db->commit();
                    $this->_helper->redirector('index');
                } catch (Exception $e) {
                    $db->rollBack();
                }
            } else {

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
                $idcuentajefe = $form->getValue('idCuentaJefe');
                $combinado = $form->getValue('combinado');
                $numero = '';
                $jornada = $form->getValue('tipoJornada');
                $codigosector = $form->getValue('codigoSector');
                $codigoespecialidad = $form->getValue('codigoEspecialidad');
                $codigoalternativa = $form->getValue('codigoAlternativa');
                $infraestructura = $form->getValue('infraestructura');

                $curso = new Application_Model_DbTable_Cursos();

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $db->beginTransaction();
                try {
                    $curso->cambiaractual($idcurso, $combinado, $numero, $jornada, $codigosector, $codigoespecialidad, $codigoalternativa, $infraestructura);
                    $db->commit();
                    $this->_helper->redirector('index');

                } catch (Exception $e) {
                    $db->rollBack();

                }
            } else {
                $idcod = $this->_getParam('idCodigoTipo', 0);
                $idgrado = $this->_getParam('idGrado', 0);


                $modelocodigo = new Application_Model_DbTable_Codigo();
                $rowcodigo = $modelocodigo->listartipoensenanza();
                foreach ($rowcodigo as $e) {

                    if ($e['idCodigoTipo'] == $idcod) {
                        $datacod[$e['idCodigoTipo']] = '(' . $e['idCodigoTipo'] . ') ' . $e['nombreTipoEnsenanza'];
                    }
                }

                $modelo = new Application_Model_DbTable_Nivel();
                $resultado = $modelo->getcodigos($idcod);
                foreach ($resultado as $row) {
                    $data[$row['idGrado']] = $row['nombreGrado'];
                }


                $form->idCodigoTipo->clearMultiOptions();
                $form->idCodigoTipo->addMultiOptions($datacod);
                $form->idCodigoTipo->setValue($idcod);

                $form->idNiveles->clearMultiOptions();
                $form->idNiveles->addMultiOptions($data);
                $form->idNiveles->setValue($idgrado);

                $form->populate($formData);

            }
        } else {

            $idcurso = $this->_getParam('id', 0);
            if ($idcurso > 0) {
                $curso = new Application_Model_DbTable_Cursos();
                $cursos = $curso->getactual($idcurso);
                $idcod = $cursos[0]['idCodigoTipo'];
                $idgrado = $cursos[0]['idCodigoGrado'];

                $modelocodigo = new Application_Model_DbTable_Codigo();

                $rowcodigo = $modelocodigo->listartipoensenanza();
                foreach ($rowcodigo as $e) {

                    if ($e['idCodigoTipo'] == $idcod) {
                        $datacod[$e['idCodigoTipo']] = '(' . $e['idCodigoTipo'] . ') ' . $e['nombreTipoEnsenanza'];
                    }
                }

                $modelo = new Application_Model_DbTable_Nivel();
                $resultado = $modelo->getcodigos($idcod);
                $data = array();
                foreach ($resultado as $row) {

                    if ($row['idCodigoGrado'] == $idgrado) {
                        $data[$row['idCodigoGrado']] = $row['nombreGrado'];
                        break;
                    }
                }

                $form->idCodigoTipo->clearMultiOptions();
                $form->idCodigoTipo->addMultiOptions($datacod);
                $form->idCodigoTipo->setValue($idcod);

                $form->idNiveles->clearMultiOptions();
                $form->idNiveles->addMultiOptions($data);
                $form->idNiveles->setValue($idgrado);


                $data_letra[$cursos[0]['letra']] = $cursos[0]['letra'];
                $form->letra->clearMultiOptions();
                $form->letra->addMultiOptions($data_letra);
                $form->letra->setValue($cursos[0]['letra']);

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


    public function eliminarAction()
    {

        $idcurso = $this->_getParam('id', 0);
        $modelocurso = new Application_Model_DbTable_Cursos();

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $db->beginTransaction();
        try {

            $modelocurso->borrar($idcurso);
            $db->commit();
            $this->_helper->redirector('index');

        } catch (Exception $e) {
            $db->rollBack();
            echo $e;
        }
    }

    public function getjefeAction()
    {
        $modelModelo = new Application_Model_DbTable_Cuentas();
        $results = $modelModelo->listarjefe($this->_getParam('id'));
        $this->_helper->json($results);
    }


    public function asignaturasAction()
    {
        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        if ($rol == '1' || $rol == '3' || $rol == '6') {

            $idcurso = $this->_getParam('id', 0);
            $modeloasignatura = new Application_Model_DbTable_Asignaturascursos();
            $modelocurso = new Application_Model_DbTable_Cursos();


            $datos = $modeloasignatura->listarnivelidcurso($idcurso);
            $datoscurso = $modelocurso->listarcursoid($idcurso, $idperiodo);

            $this->view->nombrecurso = $datoscurso[0]['nombreGrado'] . ' ' . $datoscurso[0]['letra'];
            $this->view->datocurso = $datoscurso[0]['idCursos'];
            $this->view->dato = $datos;
            $this->view->tipo = $datoscurso[0]['idCodigoTipo'];
        }
    }

    public function asignaturasearchAction()
    {

        $this->_helper->layout->disableLayout();
        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;
        if ($rol == '1' || $rol == '3' || $rol == '6') {
            $idcurso = $this->_getParam('id', 0);

            $table = new Application_Model_DbTable_Asignaturas();
            $tablacurso = new Application_Model_DbTable_Cursos();

            $datos = $table->listarnivelidcurso($idcurso);
            $datoscurso = $tablacurso->get($idcurso);
            $this->view->datoscurso = $datoscurso;
            $this->view->dato = $datos;
        }

    }

    public function guardarasignaturasearchAction()
    {

        if ($this->getRequest()->isXmlHttpRequest()) {

            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();

            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $asignaturascurso = new Application_Model_DbTable_Asignaturascursos();

            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;

            $cargo = new Zend_Session_Namespace('cargo');
            $rol = $cargo->cargo;

            if ($rol == 1 || $rol == 3) {

                if (empty($data)) {
                    echo Zend_Json::encode(array('response' => 'error'));
                } else {

                    $idcurso = $data[0]['idcurso'];
                    $ultimoorden = $asignaturascurso->ultimoorden($idcurso, $idperiodo);

                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                    $db->beginTransaction();

                    try {
                        for ($i = 0; $i < count($data); $i++) {
                            $idasignatura = intval($data[$i]['asignatura']);
                            $verifica = $asignaturascurso->validar($idasignatura, $idperiodo, $idcurso);
                            if ($verifica) {
                                $ultimoorden[0]['max']++;
                                $asignaturascurso->agregar($idasignatura, 1, $ultimoorden[0]['max'], 1, $data[$i]['nucleo'], $idperiodo, $idcurso);

                            }
                        }
                        $db->commit();
                        echo Zend_Json::encode(array('redirect' => '/Cursos/asignaturas/id/' . $idcurso . ''));

                    } catch (Exception $e) {
                        $db->rollBack();
                        echo Zend_Json::encode(array('response' => 'error'));
                    }

                }
            }
        }
    }

    public function guardaasignaturaAction()
    {

        if ($this->getRequest()->isXmlHttpRequest()) {

            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();

            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $tabla = new Application_Model_DbTable_Asignaturascursos();
            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
            $db->beginTransaction();

            try {
                for ($i = 0; $i < count($data); $i++) {
                    $tabla->cambiarestadoorden($data[$i]['idasignatura'], $i + 1, $data[$i]['prioritaria'], $data[$i]['horas'], $data[$i]['electivo']);
                }
                $db->commit();
                echo Zend_Json::encode(array('redirect' => '/Cursos/asignaturas/id/' . $data[0]['curso']));

            } catch (Exception $e) {
                $db->rollBack();
                echo Zend_Json::encode(array('response' => 'error'));

            }

        }

    }

    public function agregarasignaturaAction()
    {

        $idcurso = $this->_getParam('id', 0);
        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;
        if ($rol == '1' || $rol == '3' || $rol == '6') {

            $asignaturas = new Application_Model_DbTable_Asignaturas();
            $asignaturascurso = new Application_Model_DbTable_Asignaturascursos();
            $cursos = new Application_Model_DbTable_Cursos();
            $datoscurso = $cursos->get($idcurso);

            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;

            $datos = $asignaturascurso->ultimo($idcurso, $idperiodo);
            $this->view->dato = $idcurso;
            $this->view->title = "Agregar Asignatura";
            $this->view->headTitle($this->view->title);
            $form = new Application_Form_Asignaturas(array('params' => $datoscurso[0]['idCodigoTipo']));

            //creamos elementos nuevos
            $form->addElement('hidden', 'idCursos');
            //$form->idCursos->set($idcurso);
            $form->addElement('Text', 'orden', array('order' => 3));
            $form->orden->setLabel('Orden: ')->setRequired(true);
            $form->orden->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));
            $form->addElement('radio', 'promedio', array('order' => 4));
            $form->promedio->setLabel('Incide en Promedio: ');
            $form->promedio->addMultiOptions(array(1 => 'Si', 0 => 'No'))->setValue("1");
            $form->promedio->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));

            //valores de los nuevos elementos
            $form->idCursos->setValue($idcurso);
            $form->orden->setValue($datos[0]['max'] + 1);
            $form->orden->setAttrib('readonly', 'readonly');
            $form->submit->setLabel('Guardar');
            $this->view->form = $form;

            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {
                    $nombre_asignatura = $form->getValue('nombreAsignatura');
                    $tipoasignatura = $form->getValue('tipoAsignatura');
                    $curso = $form->getValue('idCursos');
                    $nivel = $form->getValue('idNiveles');
                    $orden = $form->getValue('orden');
                    $promedio = $form->getValue('promedio');
                    $ultimoid = $asignaturas->ultimoid();
                    $idasignatura = $ultimoid[0]['max'] + 1;
                    $nucleo = '';
                    if ($datoscurso[0]['idCodigoTipo'] == 10) {
                        $tipoasignatura = 1;
                        $nucleo = $form->getValue('idNucleo');
                    }

                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();

                    // Iniciamos la transaccion
                    $db->beginTransaction();
                    try {

                        $asignaturas->agregar($idasignatura, $nombre_asignatura, $orden, $promedio, $nivel);
                        $ultimoid = $asignaturas->ultimoid();
                        $asignaturascurso->agregar('' . $ultimoid[0]['max'] . '', $tipoasignatura, $orden, $promedio, $nucleo, $idperiodo, $curso);

                        // Sino hubo ningun inconveniente hacemos un commit
                        $db->commit();
                        $this->_helper->redirector('asignaturas', 'Cursos', null, array('id' => $idcurso));
                    } catch (Exception $e) {
                        $db->rollBack();
                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al ingresar los datos, intente nuevamente');
                        $this->view->assign('messages', $messages);
                    }
                } else {
                    $form->populate($formData);
                }
            }
        }
    }

    public function editarasignaturaAction()
    {
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $this->view->title = "Modificar";
        $this->view->headTitle($this->view->title);
        $idCurso = $this->_getParam('c', 0);
        $this->view->dato = $idCurso;

        $cursos = new Application_Model_DbTable_Cursos();
        $datoscurso = $cursos->get($idCurso);

        $form = new Application_Form_Asignaturas(array('params' => $datoscurso[0]['idCodigoTipo']));
        $form->addElement('hidden', 'idNiveles');

        $form->submit->setLabel('Modificar');
        $form->addElement('hidden', 'idCursos');
        $form->addElement('hidden', 'idAsignaturaCurso');
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $idasignaturas = $form->getValue('idAsignatura');
                $nombreasignatura = $form->getValue('nombreAsignatura');
                $idasignaturacurso = $form->getValue('idAsignaturaCurso');
                $tipoasignatura = $form->getValue('tipoAsignatura');
                $nucleo = '';
                if ($datoscurso[0]['idCodigoTipo'] == 10) {
                    $tipoasignatura = 1;
                    $nucleo = $form->getValue('idNucleo');
                }

                $asignatura = new Application_Model_DbTable_Asignaturas();
                $asignaturacurso = new Application_Model_DbTable_Asignaturascursos();

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $db->beginTransaction();
                try {

                    $asignatura->cambiar($idasignaturas, $nombreasignatura);

                    if ($tipoasignatura == '1' || $tipoasignatura == '3') {
                        $asignaturacurso->cambiar($idasignaturacurso, $tipoasignatura, 1, $nucleo);
                    }
                    if ($tipoasignatura == '2' || $tipoasignatura == '4' || $tipoasignatura == '5') {
                        $asignaturacurso->cambiar($idasignaturacurso, $tipoasignatura, 0, $nucleo);
                    }

                    $db->commit();
                    $this->_helper->redirector('asignaturas', 'Cursos', null, array('id' => $idCurso));

                } catch (Exception $e) {
                    $db->rollBack();
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al ingresar los datos, intente nuevamente');
                    $this->view->assign('messages', $messages);
                }

            } else {
                $idasignaturas = $this->_getParam('id', 0);
                $idcurso = $this->_getParam('c', 0);
                if ($idasignaturas > 0 && $idcurso > 0) {
                    $fasignatura = new Application_Model_DbTable_Asignaturascursos();
                    $fasignaturas = $fasignatura->get($idasignaturas, $idcurso, $idperiodo);
                    $nombre = $fasignaturas[0]['nombreAsignatura'];
                    $length = strlen(utf8_decode($nombre));
                    $form->nombreAsignatura->setAttrib('size', $length + 2);
                }
                $form->populate($formData);
            }
        } else {

            $idasignaturas = $this->_getParam('id', 0);
            $idcurso = $this->_getParam('c', 0);
            if ($idasignaturas > 0 && $idcurso > 0) {
                $fasignatura = new Application_Model_DbTable_Asignaturascursos();
                $fasignaturas = $fasignatura->get($idasignaturas, $idcurso, $idperiodo);
                if ($fasignaturas[0]['idAmbito'] > 0) {
                    $nucleos = $fasignatura->listarnucleoambito($fasignaturas[0]['idAmbito']);
                    $form->idNucleo->clearMultiOptions();
                    foreach ($nucleos as $row) {
                        $form->idNucleo->addMultiOption($row->idNucleo, $row->nombreNucleo);
                    }
                }
                $nombre = $fasignaturas[0]['nombreAsignatura'];
                $length = strlen(utf8_decode($nombre));
                $form->nombreAsignatura->setAttrib('size', $length + 2);

                $form->populate($fasignaturas[0]);

            }
        }
    }

    public function configuraciontallerAction()
    {
        $this->view->title = "Configuración Taller";
        $this->view->headTitle($this->view->title);

        $asignaturacurso = new Application_Model_DbTable_Asignaturascursos();

        $idasig = $this->_getParam('id', 0);
        $idcurso = $this->_getParam('c', 0);
        $listalumnostaller = $asignaturacurso->getalumnostaller($idasig, 1);
        $lista = unserialize($listalumnostaller[0]['listaAlumnos']);

        $form = new Application_Form_Configuraciontaller(array('idcurso' => $idcurso, 'lista' => $lista));
        $form->addElement('hidden', 'idAsignaturaCurso');
        $form->addElement('hidden', 'idConfiguracionTaller');
        $form->idAsignaturaCurso->setValue($idasig);
        $form->submit->setLabel('Guardar');
        $this->view->dato = $idcurso;
        $this->view->datoasig = $idasig;
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $forma = $form->getValue('forma');
                $porcentaje = $form->getValue('porcentaje');
                $idasignaturadestino = $form->getValue('idAsignaturaTaller');
                $idasignaturacurso = $form->getValue('idAsignaturaCurso');
                $idconfiguraciontaller = $form->getValue('idConfiguracionTaller');
                $segmento = $form->getValue('segmento');
                $opcionsegmento = $form->getValue('opsegmento');
                $tipo = $form->getValue('tipo');

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $db->beginTransaction();
                if ($tipo == 1) {
                    $listalumnos = '';

                } else {
                    $listalumnos = $form->getValue('idAlumnos');

                }
                try {


                    $asignaturacurso->agregarconfiguracion($forma, $porcentaje, $idasignaturadestino, $idasignaturacurso, $segmento, $opcionsegmento, $tipo, $listalumnos);
                    $idconfig = $asignaturacurso->getAdapter()->lastInsertId();
                    foreach ($listalumnos as $key => $value) {
                        $asignaturacurso->agregarconfiguraciondetalle($idasignaturadestino, $value, $idconfig);
                    }

                    $db->commit();
                    $this->_helper->redirector('taller', 'Cursos', null, array('id' => $idasig, 'c' => $idcurso));

                } catch (Exception $e) {
                    $db->rollBack();
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al ingresar los datos, intente nuevamente');
                    $this->view->assign('messages', $messages);

                }


            } else {

                $form->populate($formData);
            }
        } else {

            $idasignaturataller = $this->_getParam('id', 0);
            $idcurso = $this->_getParam('c', 0);


        }

    }

    public function configuraciondepAction()
    {
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;
        $this->view->title = "Configuración Asignatura Dependiente";
        $this->view->headTitle($this->view->title);

        $asignaturacurso = new Application_Model_DbTable_Asignaturascursos();

        $idasig = $this->_getParam('id', 0);
        $idcurso = $this->_getParam('c', 0);
        $validar = $asignaturacurso->validardep($idasig);
        $nombredatos = $asignaturacurso->getnombre($idasig);

        $form = new Application_Form_Configuraciondep(array('params' => $idcurso, 'nombre' => $nombredatos[0]['nombreAsignatura']));
        $form->addElement('hidden', 'idAsignaturaCurso');
        $form->idAsignaturaCurso->setValue($idasig);
        $form->addElement('radio', 'estadonp', array('order' => 2));
        $form->estadonp->setLabel('Obtener de las Asignaturas: ');
        $form->estadonp->addMultiOptions(array(1 => 'Nota', 0 => 'Promedio'))->setValue("1");
        $form->estadonp->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $form->submit->setLabel('Guardar');
        $this->view->dato = $idcurso;

        if ($validar) {
            $this->view->form = $form;
        } else {
            $datosconfig = $asignaturacurso->getdep($idasig);
            $this->view->form = $form;
            $form->asignaturas->clearMultiOptions();
            $tipos = array('1', '4');
            $rowsetasignatura = $asignaturacurso->listarniveltipos($idcurso, $idperiodo, $tipos);

            foreach ($rowsetasignatura as $row) {
                $form->asignaturas->addMultiOption($row->idAsignaturaCurso, $row->nombreAsignatura);
            }
            $datosconfig[0]['asignaturas'] = unserialize($datosconfig[0]['asignaturas']);
            $form->populate($datosconfig[0]);
        }

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $asignaturaslista = $form->getValue('asignaturas');
                $idasignaturacurso = $form->getValue('idAsignaturaCurso');
                $idConfiguraciondependencia = $form->getValue('idConfiguracionDependencia');
                $estadonp = $form->getValue('estadonp');

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $db->beginTransaction();

                $datosaux = $asignaturacurso->getdep($idasig);
                $listaaux = unserialize($datosaux[0]['asignaturas']);

                $resultado = array_diff($listaaux, $asignaturaslista);


                try {
                    foreach ($resultado as $key => $value) {
                        $asignaturacurso->cambiartipoasignatura($value, 1, 1);
                    }
                    foreach ($asignaturaslista as $key => $value) {
                        $asignaturacurso->cambiartipoasignatura($value, 4, 0);
                    }
                    $asignaturaslista = serialize($asignaturaslista);

                    if ($validar) {
                        $asignaturacurso->agregardep($asignaturaslista, $idasignaturacurso, $estadonp);
                    } else {
                        $asignaturacurso->cambiardep($idConfiguraciondependencia, $asignaturaslista, $estadonp);
                    }

                    $db->commit();
                    $this->_helper->redirector('asignaturas', 'Cursos', null, array('id' => $idcurso));

                } catch (Exception $e) {
                    $db->rollBack();
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al ingresar los datos, intente nuevamente');
                    $this->view->assign('messages', $messages);

                }

            } else {
                $form->addElement('radio', 'estadonp', array('order' => 2));
                $form->estadonp->setLabel('Obtener de las Asignaturas: ');
                $form->estadonp->addMultiOptions(array(1 => 'Nota', 0 => 'Promedio'));
                $form->estadonp->setDecorators(array(
                    'ViewHelper',
                    array('Errors'),
                    array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                    array('Label', array('tag' => 'td')),
                    array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
                ));
                $form->populate($formData);
            }
        }

    }

    public function eliminarasignaturaAction()
    {
        $idasignatura = $this->_getParam('id', 0);
        $idcurso = $this->_getParam('c', 0);
        $this->view->dato = $idcurso;
        $tabla = new Application_Model_DbTable_Asignaturascursos();
        $tablanotas = new Application_Model_DbTable_Notas();

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $validar = $tablanotas->validar($idasignatura, $idcurso, $idperiodo);
        $datoasignatura = $tabla->get($idasignatura, $idcurso, $idperiodo);

        if ($validar) {

            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
            $db->beginTransaction();

            try {
                if ($datoasignatura[0]['tipoAsignatura'] == 3) {
                    $datosaux = $tabla->getdep($datoasignatura[0]['idAsignaturaCurso']);
                    $listaaux = unserialize($datosaux[0][asignaturas]);

                    foreach ($listaaux as $key => $value) {
                        $tabla->cambiartipoasignatura($value, 1);
                    }
                }
                $tabla->eliminar($datoasignatura[0]['idAsignaturaCurso']);
                $db->commit();
                $this->_helper->redirector('asignaturas', 'Cursos', null, array('id' => $idcurso));

            } catch (Exception $e) {
                $db->rollBack();
                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Ocurrio un error');
                $this->view->assign('messages', $messages);
            }
        } else {
            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('No se puede eliminar la asignatura, existen notas');
            $this->view->assign('messages', $messages);
        }
    }

    public function getnucleoAction()
    {

        $modelModelo = new Application_Model_DbTable_Asignaturas();
        $dato = $this->_getParam('id');
        $results = $modelModelo->listarnucleoporambito($dato);
        $this->_helper->json($results);
    }

    public function configuracionconceptoAction()
    {
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;
        $this->view->title = "Configuración Asignatura por Concepto";
        $this->view->headTitle($this->view->title);

        $asignaturacurso = new Application_Model_DbTable_Asignaturascursos();
        $idasig = $this->_getParam('id', 0);
        $idcurso = $this->_getParam('c', 0);

        $form = new Application_Form_Configuracionconcepto();
        $form->submit->setLabel('Guardar');
        $this->view->form = $form;
        $this->view->dato = $idcurso;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $concepto = $form->getValue('concepto');
                $desde = $form->getValue('desde');
                $hasta = $form->getValue('hasta');

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $db->beginTransaction();

                try {
                    for ($i = 0; $i < count($concepto); $i++) {
                        $validarconcepto = $asignaturacurso->validarconcepto($idasig, strtoupper($concepto[$i]));
                        if ($validarconcepto) {
                            $asignaturacurso->agregarconcepto(strtoupper($concepto[$i]), round(($desde[$i] + $hasta[$i]) / 2), $desde[$i], $hasta[$i], $idasig);
                        }

                    }
                    $db->commit();
                    $this->_helper->redirector('concepto', 'Cursos', null, array('id' => $idasig, 'c' => $idcurso));

                } catch (Exception $e) {
                    $db->rollBack();
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al ingresar los datos, intente nuevamente');
                    $this->view->assign('messages', $messages);

                }
            } else {
                $form->populate($formData);
            }
        }
    }


    public function tallerAction()
    {
        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;
        if ($rol == '1' || $rol == '3' || $rol == '6') {
            $idasig = $this->_getParam('id', 0);
            $idcurso = $this->_getParam('c', 0);

            $table = new Application_Model_DbTable_Asignaturascursos();
            $this->view->dato = $table->gettallerconfiguracion($idasig);
            $this->view->nombre = $table->getnombre($idasig);
            $this->view->idasignatura = $idasig;
            $this->view->idcurso = $idcurso;

        }
    }


    public function eliminartallerAction()
    {
        $idconfig = $this->_getParam('t', 0);
        $idasig = $this->_getParam('id', 0);
        $idcurso = $this->_getParam('c', 0);
        $tabla = new Application_Model_DbTable_Asignaturascursos();

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $tabla->eliminartaller($idconfig);
            $tabla->eliminartallerdetalle($idconfig);
            $db->commit();
            $this->_helper->redirector('taller', 'Cursos', null, array('id' => $idasig, 'c' => $idcurso));

        } catch (Exception $e) {
            $db->rollBack();
            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Ocurrio un error');
            $this->view->assign('messages', $messages);
        }

    }

    public function listaalumnosAction()
    {
        $this->_helper->layout->disableLayout();
        $id = $this->_getParam('id');
        $tipo = $this->_getParam('s');

        if ($id > 0) {

            $modeloalumnos = new Application_Model_DbTable_Alumnosactual();
            $listaalumnos = $modeloalumnos->getalumnostaller($id, $tipo);
            $this->view->listanotas = $listaalumnos;

        }

    }

    public function conceptoAction()
    {
        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;
        if ($rol == '1' || $rol == '3' || $rol == '6') {
            $idasig = $this->_getParam('id', 0);
            $idcurso = $this->_getParam('c', 0);

            $table = new Application_Model_DbTable_Asignaturascursos();
            $this->view->dato = $table->getconceptoconfiguracion($idasig);
            $this->view->nombre = $table->getnombre($idasig);
            $this->view->idasignatura = $idasig;
            $this->view->idcurso = $idcurso;

        }
    }

    public function editarconceptoAction()
    {
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idasig = $this->_getParam('as', 0);
        $idcurso = $this->_getParam('c', 0);

        $this->view->title = "Modificar concepto";
        $this->view->headTitle($this->view->title);
        $modeloasignatura = new Application_Model_DbTable_Asignaturascursos();
        $form = new Application_Form_Configuracionconcepto();
        $form->submit->setLabel('Modificar');
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {

                $idconcepto = $form->getValue('idConfiguracionConcepto');
                $concepto = $form->getValue('concepto');
                $desde = $form->getValue('desde');
                $hasta = $form->getValue('hasta');

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $db->beginTransaction();
                try {
                    $validarconcepto = $modeloasignatura->validarconcepto($idasig, strtoupper($concepto[0]));
                    if ($validarconcepto) {
                        $modeloasignatura->cambiarconceptoid($idconcepto, $concepto[0], round(($desde[0] + $hasta[0]) / 2), $desde[0], $hasta[0]);
                    }
                    $db->commit();
                    $this->_helper->redirector('concepto', 'Cursos', null, array('id' => $idasig, 'c' => $idcurso));

                } catch (Exception $e) {
                    $db->rollBack();
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al ingresar los datos, intente nuevamente');
                    $this->view->assign('messages', $messages);
                }

            } else {
                $idconcepto = $this->_getParam('id', 0);
                if ($idconcepto > 0) {
                    $datosconcepto = $modeloasignatura->getconceptoid($idconcepto);
                    $form->idConfiguracionConcepto->setValue($datosconcepto[0]['idConcepto']);
                    $form->populate($datosconcepto[0]);
                }
            }
        } else {
            $idconcepto = $this->_getParam('id', 0);
            if ($idconcepto > 0) {
                $datosconcepto = $modeloasignatura->getconceptoid($idconcepto);
                $form->idConfiguracionConcepto->setValue($datosconcepto[0]['idConcepto']);
                $form->populate($datosconcepto[0]);
            }
        }
    }

    public function eliminarconceptoAction()
    {

        $idconcepto = $this->_getParam('id', 0);
        $idasig = $this->_getParam('as', 0);
        $idcurso = $this->_getParam('c', 0);
        $tabla = new Application_Model_DbTable_Asignaturascursos();

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $tabla->eliminarconcepto($idconcepto);
            $db->commit();
            $this->_helper->redirector('concepto', 'Cursos', null, array('id' => $idasig, 'c' => $idcurso));

        } catch (Exception $e) {
            $db->rollBack();
            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Ocurrio un error');
            $this->view->assign('messages', $messages);
        }

    }

    public function conceptoparvulariaAction()
    {
        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;
        if ($rol == '1' || $rol == '3' || $rol == '6') {
            $id = $this->_getParam('id', 0);
            $modeloasignatura = new Application_Model_DbTable_Asignaturascursos();
            $modelocurso = new Application_Model_DbTable_Cursos();
            $this->view->dato = $modeloasignatura->getconceptoconfiguracionprebasica($id);
            $datoscurso = $modelocurso->getnombreactual($id);
            $this->view->nombrecurso = $datoscurso[0]['nombreGrado'] . ' ' . $datoscurso[0]['letra'];
            $this->view->idcurso = $id;

        }
    }

    public function configuracionconceptoparvulariaAction()
    {
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;
        $this->view->title = "Configuración Conceptos Parvularia";
        $this->view->headTitle($this->view->title);
        $asignaturacurso = new Application_Model_DbTable_Asignaturascursos();
        $idcurso = $this->_getParam('c', 0);
        $form = new Application_Form_Configuracionconceptoparvularia();
        $form->submit->setLabel('Guardar');
        $this->view->form = $form;
        $this->view->dato = $idcurso;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $concepto = $form->getValue('concepto');
                $notaconcepto = $form->getValue('notaConcepto');
                $descripcion = $form->getValue('descripcionConcepto');

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $db->beginTransaction();

                try {
                    for ($i = 0; $i < count($concepto); $i++) {
                        $validarconcepto = $asignaturacurso->validarconceptoparvularia($idcurso, strtoupper($concepto[$i]));
                        if ($validarconcepto) {
                            $asignaturacurso->agregarconceptoparvularia(strtoupper($concepto[$i]), $notaconcepto[$i], $descripcion[$i], $idcurso);
                        }
                    }

                    $db->commit();
                    $this->_helper->redirector('conceptoparvularia', 'Cursos', null, array('id' => $idcurso));

                } catch (Exception $e) {
                    $db->rollBack();
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al ingresar los datos, intente nuevamente');
                    $this->view->assign('messages', $messages);

                }
            } else {
                $form->populate($formData);
            }
        }
    }

    public function editarconceptoparvulariaAction()
    {
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcurso = $this->_getParam('c', 0);

        $this->view->title = "Modificar Concepto  Parvularia";
        $this->view->headTitle($this->view->title);
        $modeloasignatura = new Application_Model_DbTable_Asignaturascursos();
        $form = new Application_Form_Configuracionconceptoparvularia();
        $form->submit->setLabel('Modificar');
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $idconcepto = $form->getValue('idConcepto');
                $concepto = $form->getValue('concepto');
                $nota = $form->getValue('notaConcepto');
                $descripcion = $form->getValue('descripcionConcepto');

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $db->beginTransaction();
                try {
                    $modeloasignatura->cambiarconceptoparvularia($idconcepto, strtoupper($concepto[0]), $nota[0], $descripcion[0]);
                    $db->commit();
                    $this->_helper->redirector('conceptoparvularia', 'Cursos', null, array('id' => $idcurso));

                } catch (Exception $e) {
                    $db->rollBack();
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al ingresar los datos, intente nuevamente');
                    $this->view->assign('messages', $messages);
                }

            } else {
                $idconcepto = $this->_getParam('id', 0);

                if ($idconcepto > 0) {
                    $datosconcepto = $modeloasignatura->getconceptoidparvularia($idconcepto);
                    $form->populate($datosconcepto[0]);
                }
            }
        } else {

            $idconcepto = $this->_getParam('id', 0);
            if ($idconcepto > 0) {
                $datosconcepto = $modeloasignatura->getconceptoidparvularia($idconcepto);
                $form->populate($datosconcepto[0]);

            }
        }
    }

    public function eliminarconceptoparvulariaAction()
    {

        $idconcepto = $this->_getParam('id', 0);
        $idcurso = $this->_getParam('c', 0);
        $tabla = new Application_Model_DbTable_Asignaturascursos();

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $db->beginTransaction();
        try {
            $tabla->eliminarconceptoparvularia($idconcepto);
            $db->commit();
            $this->_helper->redirector('conceptoparvularia', 'Cursos', null, array('id' => $idcurso));

        } catch (Exception $e) {
            $db->rollBack();
            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Ocurrio un error');
            $this->view->assign('messages', $messages);
        }

    }

    public function nuevosindicadoresAction()
    {
        $this->_helper->layout->disableLayout();
        $modelocurso = new Application_Model_DbTable_Cursos();
        $asignaturas = new Application_Model_DbTable_Asignaturas();
        $asignaturascurso = new Application_Model_DbTable_Asignaturascursos();

        $idcurso = $this->_getParam('id', 0);
        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        if ($idcurso > 0) {
            $datoscurso = $modelocurso->get($idcurso);
            if ($datoscurso[0]['idCodigoTipo'] == 10) {

                //Verificamos que el curso no posea indicadores
                $listaasignatura = $asignaturascurso->listarnivelidcurso($idcurso);

                if (count($listaasignatura) > 0) {
                    $this->_helper->redirector('asignaturas', 'Cursos', null, array('id' => $idcurso));

                } else {
                    $csvs = getcwd();
                    $csvs .= "/application/documentos/nuevos.xlsx";

                    require 'PHPExcel-1.8/Classes/PHPExcel.php';
                    $excelReader = PHPExcel_IOFactory::createReaderForFile($csvs);
                    $excelObj = $excelReader->load($csvs);
                    $worksheet = $excelObj->getSheet(0);
                    $lastRow = $worksheet->getHighestRow();


                    try {
                        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                        $db->beginTransaction();

                        if ($datoscurso[0]['idCodigoTipo'] == 10 && $datoscurso[0]['idCodigoGrado'] == 4) {
                            $row = 1;
                            $lastRow = 84;

                        } elseif ($datoscurso[0]['idCodigoTipo'] == 10 && $datoscurso[0]['idCodigoGrado'] == 5) {
                            $row = 1;
                            $lastRow = 84;
                        }
                        $contador = 1;
                        for ($row; $row < $lastRow; $row++) {

                            if ($row > 1) {
                                try {
                                    $ultimoid = $asignaturas->ultimoid();
                                    $idasignatura = $ultimoid[0]['max'] + 1;

                                    $asignaturas->agregar($idasignatura, $worksheet->getCell('A' . $row)->getValue(), $contador, 1, $worksheet->getCell('B' . $row)->getValue());
                                    $ultimoid = $asignaturas->ultimoid();
                                    $asignaturascurso->agregar('' . $ultimoid[0]['max'] . '', 1, $contador, 1, $worksheet->getCell('B' . $row)->getValue(), $idperiodo, $idcurso);
                                    $contador++;

                                } catch (Exception $e) {
                                    $db->rollBack();

                                    echo $e;
                                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Ha ocurido un error, al ingresar los datos ');

                                    $this->view->assign('messages', $messages);
                                    exit();
                                }

                            }


                        }
                        $db->commit();
                        $this->_helper->redirector('asignaturas', 'Cursos', null, array('id' => $idcurso));

                    } catch (Exception $e) {
                        $db->rollBack();
                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al actualizar el estado, intente nuevamente' . $e);
                        $this->view->assign('messages', $messages);
                    }
                }


            }
        }


    }

    public function generarasignaturasAction()
    {

        $idcurso = $this->_getParam('id', 0);
        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        if ($rol == '1' || $rol == '3' || $rol == '6') {

            $asignaturas = new Application_Model_DbTable_Asignaturas();
            $asignaturascurso = new Application_Model_DbTable_Asignaturascursos();
            $cursos = new Application_Model_DbTable_Cursos();
            $datoscurso = $cursos->listarcursoid($idcurso, $idperiodo);


            $datos = $asignaturascurso->ultimo($idcurso, $idperiodo);

            if (empty($datos[0]['max'])) {
                if ($datoscurso[0]['idCodigoTipo'] != 10) {

                    if ($datoscurso[0]['idCodigoTipo'] == 110 && $datoscurso[0]['idGrado'] < 7) {
                        $nombres = array(
                            'Lenguaje y Comunicación',
                            'Matemática',
                            'Idioma Extranjero: Inglés',
                            'Historia, Geografía y Ciencias Sociales',
                            'Ciencias Naturales',
                            'Artes Visuales',
                            'Educación Física y Salud',
                            'Tecnología',
                            'Música',
                            'Orientación'

                        );

                    } elseif ($datoscurso[0]['idCodigoTipo'] == 110 && $datoscurso[0]['idGrado'] > 6) {

                        $nombres = array(
                            'Lengua y Literatura',
                            'Matemática',
                            'Idioma Extranjero: Inglés',
                            'Historia, Geografía y Ciencias Sociales',
                            'Ciencias Naturales',
                            'Artes Visuales',
                            'Educación Física y Salud',
                            'Tecnología',
                            'Música',
                            'Orientación'

                        );
                    } elseif ($datoscurso[0]['idCodigoTipo'] == 310 || $datoscurso[0]['idCodigoTipo'] == 410 || $datoscurso[0]['idCodigoTipo'] = 510 || $datoscurso[0]['idCodigoTipo'] = 610) {

                        $nombres = array(
                            'Lengua y Literatura',
                            'Matemática',
                            'Idioma Extranjero: Inglés',
                            'Historia, Geografía y Ciencias Sociales',
                            'Ciencias Naturales',
                            'Biología',
                            'Física',
                            'Química',
                            'Educación Física y Salud',
                            'Tecnología',
                            'Música'

                        );
                    }


                    $nucleo = '';
                    $tipoasignatura = 1;
                    $incide = 1;

                    $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                    $db->beginTransaction();
                    try {
                        for ($i = 0; $i < count($nombres); $i++) {
                            $ultimoid = $asignaturas->ultimoid();
                            $idasignatura = $ultimoid[0]['max'] + 1;
                            if ($nombres[$i] == 'Orientación') {
                                $tipoasignatura = 2;
                                $incide = 2;

                            }
                            $asignaturas->agregar($idasignatura, $nombres[$i], ($i + 1), $incide, null);
                            $ultimoid = $asignaturas->ultimoid();
                            $asignaturascurso->agregar('' . $ultimoid[0]['max'] . '', $tipoasignatura, ($i + 1), $incide, $nucleo, $idperiodo, $idcurso);

                        }
                        $db->commit();
                        $this->_helper->redirector('asignaturas', 'Cursos', null, array('id' => $idcurso));
                    } catch (Exception $e) {
                        $db->rollBack();
                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al ingresar los datos, intente nuevamente');
                        $this->view->assign('messages', $messages);
                    }

                } else {
                    $this->_helper->redirector('asignaturas', 'Cursos', null, array('id' => $idcurso));
                }

            } else {
                $this->_helper->redirector('asignaturas', 'Cursos', null, array('id' => $idcurso));

            }


        }
    }


    public function horarioAction()
    {
        $idcurso = $this->_getParam('id', 0);
        $idtipo = $this->_getParam('idt', 0);
        if ($idcurso > 0 && $idtipo > 0) {

            if ($idtipo == 1) {
                $this->view->title = "Horario Mañana";
            } elseif ($idtipo) {
                $this->view->title = "Horario Tarde";
            }

            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;
            $modeloasignatura = new Application_Model_DbTable_Asignaturascursos();
            $modelocurso = new Application_Model_DbTable_Cursos();
            $datoscurso = $modelocurso->listarcursoid($idcurso, $idperiodo);
            $datosbloque = $modelocurso->listarbloquesestablecimiento($datoscurso[0]->idEstablecimiento, $idperiodo, $idtipo);


            $datosdocente = $modelocurso->listarbloquetotaldocentes($idperiodo);

            for ($i = 0; $i < count($datosbloque); $i++) {


                for ($j = 1; $j < 6; $j++) {
                    $form [$i][$j] = new Application_Form_Horario(array('params' => $idcurso));

                    for ($d = 0; $d < count($datosdocente); $d++) {

                        if ($datosdocente[$d]['bloque'] == $datosbloque[$i]['idBloque'] && $datosdocente[$d]['dia'] == $j) {

                            $form[$i][$j]->idCuenta->removeMultiOption($datosdocente[$d]['idCuenta']);

                        }
                    }
                }
            }

            $this->view->dato = $form;

            $datoscurso = $modelocurso->getnombreactual($idcurso);
            $datoshorario = $modelocurso->listarbloque($idcurso);

            $this->view->horario = $datoshorario;
            $this->view->nombrecurso = $datoscurso[0]['nombreGrado'] . ' ' . $datoscurso[0]['letra'];
            $this->view->datocurso = $datoscurso[0]['idCursos'];
            $this->view->tipo = $datoscurso[0]['idCodigoTipo'];
            $this->view->bloques = $datosbloque;

            if ($this->getRequest()->isPost()) {
                $form = new Application_Form_Horario(array('params' => $idcurso));
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {

                    $idhorario = $form->getValue('idHorario');
                    $idasignatura = $form->getValue('idAsignatura');
                    $idcuenta = $form->getValue('idCuenta');
                    $bloque = $formData['bloque'];
                    $dia = $formData['dia'];
                    $enviar = $form->getValue('submit');
                    $eliminar = $form->getValue('eliminar');
                    if ($enviar) {
                        if (empty($bloque) || empty($dia)) {

                            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al ingresar los datos, intente nuevamente');
                            $this->view->assign('messages', $messages);
                        } else {


                            $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                            $db->beginTransaction();

                            try {
                                if ($idhorario > 0) {
                                    $horario = $modelocurso->listarbloqueid($idhorario);
                                    if (count($horario) > 0) {
                                        if ($horario[0]['idAsignatura'] == $idasignatura) {

                                            $modelocurso->actualizarbloque($idhorario, $idasignatura, $idcuenta);
                                            $db->commit();
                                            $this->_helper->redirector('horario', 'Cursos', null, array('id' => $idcurso, 'idt' => $idtipo));


                                        } else {
                                            $datosasignatura = $modeloasignatura->getdestino($idasignatura);
                                            if ($datosasignatura[0]['horas'] > $datosasignatura[0]['horasAsignadas']) {
                                                $datosasignaturaold = $modeloasignatura->getdestino($horario[0]['idAsignatura']);
                                                $horaold = $datosasignaturaold[0]['horaAsignadas'] - 1;
                                                $modeloasignatura->actualizarhoraasignada($horaold, $horario[0]['idAsignatura']);
                                                $modelocurso->actualizarbloque($idhorario, $idasignatura, $idcuenta);

                                                $horanew = $datosasignatura[0]['horasAsignaturas'] + 1;
                                                $modeloasignatura->actualizarhoraasignada($horanew, $idasignatura);

                                                $db->commit();
                                                $this->_helper->redirector('horario', 'Cursos', null, array('id' => $idcurso, 'idt' => $idtipo));


                                            } else {

                                                $db->rollBack();
                                                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al ingresar los datos, intente nuevamente');
                                                $this->view->assign('messages', $messages);

                                            }

                                        }
                                    }


                                } else {
                                    $datosasignatura = $modeloasignatura->getdestino($idasignatura);
                                    if ($datosasignatura[0]['horas'] > $datosasignatura[0]['horasAsignadas']) {
                                        $valida = $modelocurso->validarbloque($bloque, $dia, $idcuenta, $idperiodo);

                                        if ($valida) {

                                            $modelocurso->agregarbloque($bloque, $dia, $idcurso, $idasignatura, $idcuenta, $idperiodo);
                                            //Actualizamos la hora asignada
                                            $horanueva = $datosasignatura[0]['horasAsignadas'] + 1;
                                            $modeloasignatura->actualizarhoraasignada($horanueva, $idasignatura);
                                            $db->commit();
                                            $this->_helper->redirector('horario', 'Cursos', null, array('id' => $idcurso, 'idt' => $idtipo));
                                        } else {

                                            $db->rollBack();
                                            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al ingresar los datos, intente nuevamente');
                                            $this->view->assign('messages', $messages);

                                        }

                                    }

                                }


                            } catch (Exception $e) {
                                $db->rollBack();
                                $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al ingresar los datos, intente nuevamente');
                                $this->view->assign('messages', $messages);


                            }

                        }

                    } elseif ($eliminar) {

                        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                        $db->beginTransaction();

                        try {
                            $horario = $modelocurso->listarbloqueid($idhorario);
                            if (count($horario) > 0) {

                                $hora = $horario[0]['horasAsignadas'] - 1;
                                if ($hora >= 0) {
                                    $modeloasignatura->actualizarhoraasignada($hora, $horario[0]['idAsignatura']);

                                    $modelocurso->eliminarbloque($idhorario);
                                    $db->commit();
                                    $this->_helper->redirector('horario', 'Cursos', null, array('id' => $idcurso, 'idt' => $idtipo));
                                }


                            }


                        } catch (Exception $e) {
                            $db->rollBack();
                            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al ingresar los datos, intente nuevamente');
                            $this->view->assign('messages', $messages);


                        }
                    }


                } else {

                    $form->populate($formData);
                }
            }
        }


    }

    public function combinarcursosAction()
    {

        $idcurso = $this->_getParam('id', 0);
        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        if ($rol == '1' || $rol == '3' || $rol == '6') {

            $modelocurso = new Application_Model_DbTable_Cursos();
            $form = new Application_Form_CombinarCurso();
            $this->view->form = $form;

            if ($this->getRequest()->isPost()) {
                $formData = $this->getRequest()->getPost();
                if ($form->isValid($formData)) {
                    $idestablecimiento = $form->getValue('idEstablecimiento');
                    $idcursos = $form->getValue('idCursos');

                    if (count($idcursos) > 1) {

                        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                        $db->beginTransaction();
                        try {


                            //$db->commit();
                            $this->_helper->redirector('index', 'Cursos');
                        } catch (Exception $e) {
                            $db->rollBack();
                            $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al ingresar los datos, intente nuevamente');
                            $this->view->assign('messages', $messages);
                        }

                    } else {
                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Debe Combinar al menos dos cursos');
                        $this->view->assign('messages', $messages);
                    }


                } else {
                    $form->populate($formData);
                }
            }
        }
    }

    public function getcursosAction()
    {

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        if ($rol == '1' || $rol == '3' || $rol == '6') {

            $idestablecimiento = $this->_getParam('id', 0);
            $lista_cursos = array();
            if ($idestablecimiento > 0) {
                $modelocurso = new Application_Model_DbTable_Cursos();
                $lista_cursos = $modelocurso->listarcursoscombinados($idperiodo, $idestablecimiento)->toArray();
            }
            $this->_helper->json($lista_cursos);
        }
    }

    public function electivoAction()
    {
        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;
        if ($rol == '1' || $rol == '3' || $rol == '6') {
            $idasignatura = $this->_getParam('id', 0);
            $idcurso = $this->_getParam('c', 0);

            $modeloasignatura = new Application_Model_DbTable_Asignaturascursos();
            $this->view->dato = $modeloasignatura->getelectivoconfiguracion($idasignatura);
            $this->view->nombre = $modeloasignatura->getnombre($idasignatura);
            $this->view->idasignatura = $idasignatura;
            $this->view->idcurso = $idcurso;

        }
    }

    public function configuracionelectivoAction()
    {
        $this->view->title = "Configuración Electivo";
        $this->view->headTitle($this->view->title);

        $modeloasignatura = new Application_Model_DbTable_Asignaturascursos();

        $idasignatura = $this->_getParam('id', 0);
        $idcurso = $this->_getParam('c', 0);
        $form = new Application_Form_Configuracionelectivo(array('idcurso' => $idcurso));
        $form->addElement('hidden', 'idAsignatura');
        $form->addElement('hidden', 'idConfiguracionElectivo');
        $form->idAsignatura->setValue($idasignatura);
        $form->submit->setLabel('Guardar');
        $this->view->dato = $idcurso;
        $this->view->datoasignatura = $idasignatura;
        $this->view->form = $form;

        if ($this->getRequest()->isPost()) {
            $formData = $this->getRequest()->getPost();
            if ($form->isValid($formData)) {
                $idasignatura = $form->getValue('idAsignatura');
                $idconfiguracionelectivo = $form->getValue('idConfiguracionElectivo');
                $tiempo = $form->getValue('opsegmento');
                $listalumnos = $form->getValue('idAlumnos');

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $db->beginTransaction();
                try {

                    if (!$modeloasignatura->validarelectivo($idasignatura, $tiempo)) {

                        $modeloasignatura->agregarconfiguracionelectivo($idasignatura, $tiempo);
                        $iddetalle = $modeloasignatura->getAdapter()->lastInsertId();
                        foreach ($listalumnos as $key => $item) {
                            $modeloasignatura->agregaralumnoselectivo($item, $iddetalle);
                        }
                        $db->commit();
                        $this->_helper->redirector('electivo', 'Cursos', null, array('id' => $idasignatura, 'c' => $idcurso));

                    } else {
                        $db->rollBack();
                        $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('La Asignatura ya posee una configuración');
                        $this->view->assign('messages', $messages);
                    }


                } catch (Exception $e) {
                    $db->rollBack();
                    $messages = $this->_helper->getHelper('FlashMessenger')->addMessage('Hubo un problema al ingresar los datos, intente nuevamente');
                    $this->view->assign('messages', $messages);

                }


            } else {

                $form->populate($formData);
            }
        } else {

            $idasignaturataller = $this->_getParam('id', 0);
            $idcurso = $this->_getParam('c', 0);


        }

    }


}
