<?php

class HorariosController extends Zend_Controller_Action
{

    public function init()
    {
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();
    }

    public function indexAction()
    {
        $modelo_curso = new Application_Model_DbTable_Cursos();
        $modelo_horario = new Application_Model_DbTable_Horario();
        $est = new Zend_Session_Namespace('establecimiento');
        $establecimiento = $est->establecimiento;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;


        if ($rol == '3' || $rol == '6') {
            $datos = $modelo_curso->listartodasactual($establecimiento, $idperiodo);
            foreach ($datos as $item) {
                $item_array = $item->toArray();
                $item_array['estadoHorario'] = False;
                if (!empty($modelo_horario->gethorariocursos($item_array['idCursos'], $idperiodo))) {
                    $item_array['estadoHorario'] = True;
                }
                $datos_new[] = array($item_array);

            }
            $this->view->dato = $datos_new;

        }
        if ($rol == '1') {
            $this->view->dato = $modelo_curso->listaractual($idperiodo);

            $datos = $modelo_curso->listaractual($idperiodo);
            foreach ($datos as $item) {
                $item_array = $item->toArray();
                $item_array['estadoHorario'] = False;
                if (!empty($modelo_horario->gethorariocursos($item_array['idCursos'], $idperiodo))) {
                    $item_array['estadoHorario'] = True;
                }
                $datos_new[] = array($item_array);

            }
            $this->view->dato = $datos_new;

        }


    }


    public function horarioAction()
    {


        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcurso = $this->_getParam('id');


        if ($rol == '1' || $rol == '3' || $rol == '6') {

            $modelcurso = new Application_Model_DbTable_Cursos();
            $modelocuenta = new Application_Model_DbTable_Cuentas();
            $datos = $modelcurso->listarcursoid($idcurso, $idperiodo);

            $lista_docentes = $modelocuenta->listardocentesperiodo($datos[0]['idEstablecimiento'], $idperiodo);

            //creamos el Select Ausente
            $elemento = "<select class=\"form-control\" id='idCuentaJefe'>";
            $elemento .= '<option value="null">Seleccione Docente</option>';
            for ($i = 0; $i < count($lista_docentes); $i++) {

                if ($datos[0]['idCuentaJefe'] == $lista_docentes[$i]['idCuenta']) {
                    $elemento .= '<option selected value="' . $lista_docentes[$i]['idCuenta'] . '">' . $lista_docentes[$i]['nombrescuenta'] . ' ' . $lista_docentes[$i]['paternocuenta'] . ' ' . $lista_docentes[$i]['maternocuenta'] . '</option>';
                } else {
                    $elemento .= '<option value="' . $lista_docentes[$i]['idCuenta'] . '">' . $lista_docentes[$i]['nombrescuenta'] . ' ' . $lista_docentes[$i]['paternocuenta'] . ' ' . $lista_docentes[$i]['maternocuenta'] . '</option>';
                }

            }
            $elemento .= "</select>";

            $this->view->docentes = $elemento;


            $this->view->curso = $datos->toArray();

            $modelohorario = new Application_Model_DbTable_Horario();
            $datoshorario = $modelohorario->gethorariocurso($idperiodo, $idcurso, null, 1);
            $this->view->horario = $datoshorario;
            $datoshorario = array_values(array_intersect_key($datoshorario, array_unique(array_column($datoshorario, 'tiempoInicio'))));
            $this->view->bloques = $datoshorario;


        }


    }

    public function gethorarioAction()
    {

        $idcurso = $this->_getParam('c');

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;


        $modelohorario = new Application_Model_DbTable_Horario();
        $datoshorario = $modelohorario->gethorariocurso($idperiodo, $idcurso, null, 1);
        $this->_helper->json($datoshorario);

    }

    public function getformAction()
    {


        $idcurso = $this->_getParam('c');
        $dia = $this->_getParam('d');
        $inicio = $this->_getParam('i');
        $termino = $this->_getParam('t');
        $idhorario = $this->_getParam('h');

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;


        $modelocuenta = new Application_Model_DbTable_Cuentas();
        $modelohorario = new Application_Model_DbTable_Horario();
        $modelocurso = new Application_Model_DbTable_Cursos();

        if (empty($idcurso) || empty($dia) || empty($inicio) || empty($termino) || $inicio == 'undefined' || $termino == 'undefined') {

            $this->_helper->json(array());
            exit();
        }

        $datocurso = $modelocurso->listarcursoid($idcurso, $idperiodo);

        $validar = $modelohorario->validarangohorario($dia, $inicio, $termino, $idperiodo);
        $listadocentes = $modelocuenta->listarjefeperiodo($datocurso[0]['idEstablecimiento'], $idperiodo);

        if (!empty($validar)) {
            foreach ($validar as $item) {
                if ($idhorario != $item['idHorario']) {
                    //unset($listadocentes[$item['idCuenta']]);
                }
            }
        }

        $tipo =1;
        $datoselectivo =array();
        if (!empty($idhorario)) {
            $datoshorario = $modelohorario->listarhorarioid($idhorario);
            if ($datoshorario[0]['electivo'] == 1) {
                $tipo=2;
                $contador=0;

                foreach ($datoshorario as $item)
                {
                    $datoselectivo[$contador]=array("profesor"=>$item['nombrescuenta'].' '.$item['paternocuenta'].' '.$item['maternocuenta'] ,"id"=>$item['idHorario'],"idAsignatura"=>$item['idAsignatura'],"nombreAsignatura"=>$item['nombreAsignatura']);
                        $contador++;
                }
            }
        }
        $listaasignatura = $this->getAsignaturas($idcurso, $tipo);
        $this->_helper->json(array($listadocentes, $listaasignatura, $idhorario,$datoselectivo));


    }

    private function getAsignaturas($idcurso, $tipo)
    {

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $modeloasignatura = new Application_Model_DbTable_Asignaturascursos();
        $tipos = array(1, 2, 3, 4, 5, 6);

        $rowasignatura = $modeloasignatura->listarniveltipos($idcurso, $idperiodo, $tipos);
        $listaasignatura = [];
        foreach ($rowasignatura as $row) {
            if ($row->horas > $row->horasAsignadas) {
                if ($tipo == 2 && $row->electivo == 1) {
                    $listaasignatura[] = array('id' => $row->idAsignatura, 'nombreAsignatura' => $row->nombreAsignatura, 'hora' => $row->horas - $row->horasAsignadas);

                } elseif ($tipo == 1) {
                    $listaasignatura[] = array('id' => $row->idAsignatura, 'nombreAsignatura' => $row->nombreAsignatura, 'hora' => $row->horas - $row->horasAsignadas);

                }
            }

        }
        return $listaasignatura;

    }

    public function guardarhorarioAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();

            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $cargo = new Zend_Session_Namespace('cargo');
            $rol = $cargo->cargo;

            if ($rol == 1 || $rol == 3 || $rol == 6) {

                $idhorario = array_key_exists('h', $data) ? $data['h'] : null;
                Zend_Debug::dump(array($data['d'], $data['i'], $data['t'], $data['c'], $data['id'], $data['ida'], $idhorario,$data['tip']));
                die();
                //$this->Crear($data['d'], $data['i'], $data['t'], $data['c'], $data['id'], $data['ida'], $idhorario);

            }
        }
    }

    private function Crear($dia, $inicio, $termino, $idcurso, $idcuenta, $idasignatura, $idhorario)
    {

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $modelohorario = new Application_Model_DbTable_Horario();
        $modeloasignatura = new Application_Model_DbTable_Asignaturascursos();
        $modelocurso = new Application_Model_DbTable_Cursos();
        $datoscurso = $modelocurso->listarcursoid($idcurso, $idperiodo);

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $valida = $modelohorario->validahorariodocente($dia, $inicio, $termino, $idcurso);
            //$valida=null;

            if (empty($valida)) {

                //$validarango = $modelohorario->validarangohorariodocente($dia, $inicio, $termino, $idcuenta, $idperiodo);
                $validarango = null;
                if (empty($validarango)) {


                    if ($datoscurso[0]['idCodigoTipo'] == 10) {


                        $modelohorario->agregarhorario($dia, $idcurso, null, $idcuenta, $idperiodo, $inicio, $termino);
                        $id = $modelohorario->getAdapter()->lastInsertId();

                    } else {
                        $datosasignatura = $modeloasignatura->getdestino($idasignatura);
                        if ($datosasignatura[0]['horas'] > $datosasignatura[0]['horasAsignadas']) {
                            $horanueva = $datosasignatura[0]['horasAsignadas'] + 1;
                            $modeloasignatura->actualizarhoraasignada($horanueva, $idasignatura);
                        }
                        $modelohorario->agregarhorario($dia, $idcurso, $idasignatura, $idcuenta, $idperiodo, $inicio, $termino);
                        $id = $modelohorario->getAdapter()->lastInsertId();

                    }

                    $db->commit();
                    echo Zend_Json::encode(array('response' => '1', 'h' => $id, 'id' => $idcuenta, 'ida' => $idasignatura));
                    die();
                }
                $db->rollBack();
                echo Zend_Json::encode(array('response' => '3'));
                exit();


            } else {

                $datoshorario = $modelohorario->gethorarioid($idhorario);
                if ($datoshorario[0]['idAsignatura'] != $idasignatura && $datoscurso[0]['idCodigoTipo'] != 10) {

                    $datosasignatura = $modeloasignatura->getdestino($idasignatura);
                    if ($datosasignatura[0]['horas'] > $datosasignatura[0]['horasAsignadas']) {
                        $datosasignaturaold = $modeloasignatura->getdestino($datoshorario[0]['idAsignatura']);
                        $horaold = $datosasignaturaold[0]['horasAsignadas'] - 1;
                        $modeloasignatura->actualizarhoraasignada($horaold, $datoshorario[0]['idAsignatura']);
                        $horanew = $datosasignatura[0]['horasAsignadas'] + 1;
                        $modeloasignatura->actualizarhoraasignada($horanew, $idasignatura);

                    }
                }

                $modelohorario->actualizarhorario($idasignatura, $idcuenta, $idhorario);
                $db->commit();
                echo Zend_Json::encode(array('response' => '2', 'h' => $idhorario, 'id' => $idcuenta, 'ida' => $idasignatura));
            }


        } catch (Exception $e) {
            $db->rollBack();
            echo Zend_Json::encode(array('response' => '3'));
        }

    }

    public function guardardocentejefeAction()
    {

        if ($this->getRequest()->isXmlHttpRequest()) {

            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();

            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $cargo = new Zend_Session_Namespace('cargo');
            $rol = $cargo->cargo;

            if ($rol == 1 || $rol == 3 || $rol == 6) {

                $curso = new Application_Model_DbTable_Cursos();

                $db = Zend_Db_Table_Abstract::getDefaultAdapter();
                $db->beginTransaction();

                try {
                    $curso->actualizardocente($data['id'], $data['c']);
                    $db->commit();
                    echo Zend_Json::encode(array('response' => '1'));
                } catch (Exception $e) {
                    $db->rollBack();
                    echo $e;
                }


            }
        }

    }

    public function verhorarioAction()
    {

        $this->_helper->viewRenderer->setNeverRender(true);
        $this->_helper->ViewRenderer->setNoRender(true);
        $this->_helper->Layout->disableLayout();

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idcurso = $this->_getParam('id');


        if ($rol != 2) {

            $modelcurso = new Application_Model_DbTable_Cursos();
            $modelocuenta = new Application_Model_DbTable_Cuentas();
            $datos = $modelcurso->listarcursoid($idcurso, $idperiodo);


            $nombre_docente = 'No Asignado';
            if (!empty($datos[0]['idCuentaJefe'])) {

                $datos_jefe = $modelocuenta->get($datos[0]['idCuentaJefe']);
                $nombre_docente = $datos_jefe['nombrescuenta'] . ' ' . $datos_jefe['paternocuenta'] . ' ' . $datos_jefe['maternocuenta'];
            }

            $modelohorario = new Application_Model_DbTable_Horario();
            $datoshorario = $modelohorario->gethorariocurso($idperiodo, $idcurso, null, 1);
            $datos_bloques = array_values(array_intersect_key($datoshorario, array_unique(array_column($datoshorario, 'tiempoInicio'))));


            if (empty($datoshorario)) {
                echo "<script type=\"text/javascript\">alert(\"Horario Sin Datos\");</script>";
                echo "<script>parent.$.fancybox.close();</script>";
                exit;

            }
            $style = "body{
                font:12px ,Arial, Tahoma, Verdana, Helvetica, sans-serif;
                
                color:#000;
                }
                img{
                width:90px;
                height:100px;
                }
                
                table{
                font-size:12px;
                width:100%;
                height:auto;
                margin:10px 0 10px 0;
                border-collapse:collapse;
                text-align:center;
                text-justify:inter-word;
                color:#000000;
                }
                
                table td,th{
                border:1px solid black;
                padding:5px;
                }
                
                table th{
                padding:1px;
                background-color: #ccc;
                }
               
                ";

            $tituloest = "<b>" . $datos[0]['nombreEstablecimiento'] . "</b>";
            $logo = getcwd();
            $logo .= "/application/documentos/logos/" . $datos[0]['extension'];
            $plogo = '';
            if (file_exists($logo)) {
                $plogo = '<p style="position:absolute;top:2px;text-align:center"><img  src="' . $logo . '" /></p>';
            }
            $encabezado = $plogo . '<h4 style="position:absolute;top:100px;text-align:center">' . $tituloest . '</h4><h4 style="position:absolute;top:120px;text-align:center">Horario</h4><h4 style="position:absolute;top:140px;text-align:center">' . $datos[0]['nombreGrado'] . ' ' . $datos[0]['letra'] . '</h4>';

            $body = '<div style="text-align: left"><h5>Profesor Jefe: ' . $nombre_docente . '</h5></div>
            <table id="horarios">
            <tr>
                <th scope="col" class="blank">Hora</th>
                <th scope="col" class="title">Lunes</th>
                <th scope="col" class="title">Martes</th>
                <th scope="col" class="title">Miercoles</th>
                <th scope="col" class="title">Jueves</th>
                <th scope="col" class="title">Viernes</th>
            </tr>';

            for ($i = 0; $i < count($datos_bloques); $i++) {
                $body .= '<tr><td style="width:80px;"><p>' . date("H:i", strtotime($datos_bloques[$i]["tiempoInicio"])) . '-' . date("H:i", strtotime($datos_bloques[$i]["tiempoTermino"])) . ' hrs.</p></td>';

                for ($j = 1; $j < 6; $j++) {
                    $aux[$i][$j] = false;

                    for ($d = 0; $d < count($datoshorario); $d++) {

                        if ($datoshorario[$d]["tiempoInicio"] == $datos_bloques[$i]["tiempoInicio"] && $datoshorario[$d]["dia"] == $j) {

                            $body .= '<td style="width: 110px;"><p><b>' . $datoshorario[$d]['nombreAsignatura'] . '</b></p><p>' . $datoshorario[$d]["nombrescuenta"] . ' ' . $datoshorario[$d]["paternocuenta"] . ' ' . $datoshorario[$d]["maternocuenta"] . '</p></td>';

                            $aux[$i][$j] = true;
                        }
                    }

                    if (!$aux[$i][$j]) {
                        $body .= '<td class="drop" style="width: 13%;"><div id="datos"></div></td>';
                    }
                }

                $body .= '</tr>';
            }
            $body .= '</table>';


            $content = '
                        <!doctype html>
                        <html>
                        <head>
                            <style>' . $style . '</style>
                        </head>
                        <body>  <page_footer>
                        <table class="page_footer">

                        </table>
                    </page_footer>'
                . $encabezado
                . $body .
                '</body>
                    </html>';

            if ($content != '') {
                echo '<page>' . $content . '</page>';
                $content = ob_get_clean();
                require 'fpdf/html2pdf.class.php';
                try {
                    $pdf = new HTML2PDF('P', 'LETTER', 'es', array(10, 10, 10, 10));
                    $pdf->WriteHTML($content);
                    if (preg_match("/MSIE/i", $_SERVER["HTTP_USER_AGENT"])) {
                        header("Content-type: application/PDF");
                    } else {
                        header("Content-type: application/PDF");
                        header("Content-Type: application/pdf");
                    }
                    $pdf->Output('Horario ' . $datos[0]['nombreGrado'] . ' ' . $datos[0]['letra'] . '.pdf', I);
                } catch (HTML2PDF_exception $e) {
                    echo $e;
                }
            }


        }


    }

    public function eliminarAction()
    {
        if ($this->getRequest()->isXmlHttpRequest()) {
            $this->_helper->viewRenderer->setNoRender();
            $this->_helper->layout->disableLayout();

            $json = file_get_contents('php://input');
            $data = json_decode($json, true);

            $cargo = new Zend_Session_Namespace('cargo');
            $rol = $cargo->cargo;

            if ($rol == 1 || $rol == 3 || $rol == 6) {

                $idhorario = array_key_exists('id', $data) ? $data['id'] : null;
                $this->Eliminar($idhorario);

            }
        }
    }

    private function Eliminar($idhorario)
    {

        $modelohorario = new Application_Model_DbTable_Horario();
        $modeloasignatura = new Application_Model_DbTable_Asignaturascursos();

        $db = Zend_Db_Table_Abstract::getDefaultAdapter();
        $db->beginTransaction();

        try {
            $datohorario = $modelohorario->gethorarioin($idhorario);

            if (empty($datohorario[0]['idHorario'])) {
                $idasignatura = $datohorario[0]['idAsignatura'];
                $datosasignatura = $modeloasignatura->getdestino($idasignatura);

                if ($datosasignatura[0]['horas'] > 0) {
                    $horanueva = $datosasignatura[0]['horasAsignadas'] - 1;
                    $modeloasignatura->actualizarhoraasignada($horanueva, $idasignatura);
                }

                $modelohorario->eliminarhorario($idhorario);
                $db->commit();
                echo Zend_Json::encode(array('response' => '1'));
                die();
            } else {
                $db->rollBack();
                echo Zend_Json::encode(array('response' => '3'));
            }

        } catch (Exception $e) {
            $db->rollBack();
            echo Zend_Json::encode(array('response' => '3'));
        }

    }


}
