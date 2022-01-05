<?php

defined('APPLICATION_UPLOADS_DIR')
|| define('APPLICATION_UPLOADS_DIR', realpath(dirname(__FILE__) . '/../documentos/guias'));


class Application_Form_Pruebas extends Zend_Form
{

    protected $_niveles;

    public function setParams($niveles)
    {
        $this->_niveles = $niveles;
    }

    public function init()
    {

        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form'
        ));

        $nombres_tipo[0]=array('1' => 'Primer Semestre', '2' => 'Segundo Semestre');
        $nombres_tipo[1]=array('3' => 'Primer Trimestre', '4' => 'Segundo Trimestre', '5' => 'Tercer Trimestre');

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        $idtipoev = new Zend_Session_Namespace('tipoevaluacion');
        $idtevalucacion = $idtipoev->tipoevaluacion;


        $id_cursos = new Zend_Session_Namespace('id_curso');
        $id_curso = $id_cursos->id_curso;

        $codigos = new Zend_Session_Namespace('codigo');
        $codigo = $codigos->codigo;


        $curso = new Zend_Form_Element_Hidden('idCursos');
        $curso->setValue($id_curso);


        $id = new Zend_Form_Element_Hidden('idEvaluacion');


        $est = new Zend_Session_Namespace('establecimiento');
        $idestablecimiento = $est->establecimiento;


        if ($codigo == 10 && $idestablecimiento != 7 && $idestablecimiento!=8) { //If Prebasica

            $asignaturamodel = new Application_Model_DbTable_Asignaturas();

            $nombreambito = new Zend_Form_Element_Select('nombreAmbito');
            $nombreambito->setLabel('Seleccione Ambito: ');
            $nombreambito->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));

            $nombreambito->addMultiOption("todo", "Mostrar Todos");


            $rowsetambito = $asignaturamodel->listarambito();
            foreach ($rowsetambito as $rowambito) {
                $nombreambito->addMultiOption($rowambito->idAmbito, $rowambito->nombreAmbito);
            }


            $nombrenucleo = new Zend_Form_Element_Select('nombreNucleo');
            $nombrenucleo->setLabel('Seleccione Nucleo: ');
            $nombrenucleo->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));

            $nombrenucleo->addMultiOption("todo", "Mostrar Todos");


            $rowsetnucleo = $asignaturamodel->listarnucleo();
            foreach ($rowsetnucleo as $rownucleo) {
                $nombrenucleo->addMultiOption($rownucleo->idNucleo, $rownucleo->nombreNucleo);
            }

            $asignatura = new Zend_Form_Element_Select('idAsignatura');
            $asignatura->setRequired(true);
            $asignatura->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));
            $asignatura->setLabel('Seleccione Indicador: ');
            $asignatura->addMultiOptions(array(
                "" => "Seleccione Indicador"
            ));

            $asignaturamodel = new Application_Model_DbTable_Asignaturascursos();

            $rowsetasignatura = $asignaturamodel->listarniveltipospre($id_curso,$idperiodo,array(1,2,3,4));

            foreach ($rowsetasignatura as $row) {
                $asignatura->addMultiOption($row->idAsignatura, $row->nombreAsignatura);
            }



            $tiponota = new Zend_Form_Element_Select('tipoEvaluacionPrueba');
            $tiponota->setLabel('Periodo Evaluación:')->setRequired(true);

            if ($idtevalucacion == '1') {

                $tiponota->setDecorators(array(
                    'ViewHelper',
                    array('Errors'),
                    array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                    array('Label', array('tag' => 'td')),
                    array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
                ));

                $tiponota->addMultiOptions($nombres_tipo[0]);
            }

            if ($idtevalucacion == '2') {
                $tiponota->setDecorators(array(
                    'ViewHelper',
                    array('Errors'),
                    array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                    array('Label', array('tag' => 'td')),
                    array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
                ));
                $tiponota->addMultiOptions($nombres_tipo[1]);
            }



            $fecha = new Zend_Form_Element_Text('fecha');
            $fecha->setAttrib('readonly', 'true');

            $fecha->setLabel('Fecha Prueba:')->setRequired(true);
            $fecha->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));

            //fecha de la prueba
            $contenidos = new Zend_Form_Element_Text('conte');


            $contenidos->setLabel('Contenido:')->setRequired(true);
            $contenidos->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));


            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setLabel('Guardar');
            $submit->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));

            $this->addElements(array($id, $curso, $nombreambito, $nombrenucleo, $asignatura, $contenidos, $tiponota, $fecha, $submit));

        } else { //Si es Basica Media

            $modelocurso = new Application_Model_DbTable_Cursos();

            $detalleest = $modelocurso->listarcursoid($id_curso, $idperiodo);


            $asignatura = new Zend_Form_Element_Hidden('idAsignatura');
            $asignatura->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));

            //creamos select para seleccionar tipo de nota
            $tiponota = new Zend_Form_Element_Select('tipoEvaluacionPrueba');
            $tiponota->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));
            $tiponota->setLabel('Periodo Evaluación:')->setRequired(true);



            //fecha de la prueba
            $fecha = new Zend_Form_Element_Text('fecha');
            $fecha->setAttrib('readonly', 'true');

            $fecha->setLabel('Fecha Prueba:');
            $fecha->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));

            //fecha de la prueba
            $contenidos = new Zend_Form_Element_Text('conte');


            $contenidos->setLabel('Contenido:')->setRequired(true);
            $contenidos->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));


            $submit = new Zend_Form_Element_Submit('submit');
            $submit->setLabel('Crear');
            $submit->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));

            $coef = new Zend_Form_Element_Select('coef');
            $coef->setLabel('Coeficiente:')->setRequired(true);
            $coef->addMultiOptions(array('1' => 'Coeficiente 1', '2' => 'Coeficiente 2'));
            $coef->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));


            $publicar = new Zend_Form_Element_Checkbox('publicar','publicar',
                array('checkedValue'  => 1,'uncheckedValue' => 2));
            $publicar->setLabel('Publicar en APP:')->setValue(2);
            $publicar->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));



            if ($detalleest[0]['examen'] == 1) {
                $categoria = new Zend_Form_Element_Radio('tipoNota');
                $categoria->setDecorators(array(
                    'ViewHelper',
                    array('Errors'),
                    array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                    array('Label', array('tag' => 'td')),
                    array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
                ));
                $categoria->setLabel('Tipo De Nota:')->setRequired(true);
                $categoria->addMultiOptions(array('1' => 'Nota', '2' => 'Exámen'))->setSeparator('')->setValue("1");


                $criterio = new Zend_Form_Element_Text('criterio');
                $criterio->setDecorators(array(
                    'ViewHelper',
                    array('Errors'),
                    array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                    array('Label', array('tag' => 'td')),
                    array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
                ));
                $criterio->setLabel('Promedio Menor a:')->setAttrib('maxlength', '2');


                $porcentaje = new Zend_Form_Element_Text('porcentajeExamen');
                $porcentaje->setDecorators(array(
                    'ViewHelper',
                    array('Errors'),
                    array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                    array('Label', array('tag' => 'td')),
                    array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
                ));
                $porcentaje->setLabel('% del Exámen:')->setAttrib('maxlength', '2');
                if ($idtevalucacion == '1') {


                    $tiponota->addMultiOptions(array('1' => 'I Semestre','2' => 'II Semestre','6' => 'Final'));
                }

                if ($idtevalucacion == '2') {
                    //creamos select para seleccionar tipo de nota
                    $tiponota = new Zend_Form_Element_Select('tipoEvaluacionPrueba');
                    $tiponota->setDecorators(array(
                        'ViewHelper',
                        array('Errors'),
                        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                        array('Label', array('tag' => 'td')),
                        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
                    ));
                    $tiponota->setLabel('Periodo Evaluación:')->setRequired(true);
                    $tiponota->addMultiOptions(array('3' => 'Primer Trimestre', '4' => 'Segundo Trimestre', '5' => 'Tercer Trimestre', '6' => 'Final'));
                }

                $this->addElements(array($id, $curso, $asignatura, $categoria, $contenidos, $tiponota, $fecha, $coef,$publicar, $criterio, $porcentaje, $submit));

            } else {
                if ($idtevalucacion == '1') {


                    $tiponota->addMultiOptions($nombres_tipo[0]);
                }

                if ($idtevalucacion == '2') {
                    //creamos select para seleccionar tipo de nota
                    $tiponota = new Zend_Form_Element_Select('tipoEvaluacionPrueba');
                    $tiponota->setDecorators(array(
                        'ViewHelper',
                        array('Errors'),
                        array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                        array('Label', array('tag' => 'td')),
                        array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
                    ));
                    $tiponota->setLabel('Periodo Evaluación:')->setRequired(true);
                    $tiponota->addMultiOptions($nombres_tipo[1]);
                }
                $this->addElements(array($id, $curso, $asignatura, $contenidos, $tiponota, $fecha, $coef,$publicar, $submit));

            }


        }

    }
}





