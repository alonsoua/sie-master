<?php

class Application_Form_Notas extends Zend_Form
{

    public function init()
    {
        // form decorators
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form',
        ));

        $asignaturamodel = new Application_Model_DbTable_Asignaturascursos();

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;

        //recuperamos el iddetallecursocuenta que esta en sesion
        $idtipoev       = new Zend_Session_Namespace('tipoevaluacion');
        $idtevalucacion = $idtipoev->tipoevaluacion;

        $ingreson    = new Zend_Session_Namespace('ingresonota');
        $ingresonota = $ingreson->ingresonota;

        //recuperamos el iddetallecursocuenta que esta en sesion
        $id_detalle_cursos = new Zend_Session_Namespace('id_detalle_curso');
        $id_detalle_curso  = $id_detalle_cursos->id_detalle_curso;

        //recuperamos el nivel del curso que esta en sesion
        $codigos = new Zend_Session_Namespace('codigo');
        $codigo = $codigos->codigo;

        //recuperamos el nivel del curso que esta en sesion
        $id_cursos = new Zend_Session_Namespace('id_curso');
        $id_curso  = $id_cursos->id_curso;

        //campo <input text> para guardar contenido
        $curso = new Zend_Form_Element_Hidden('idCursos');
        $curso->setValue($id_curso);

        //campo <input text> para guardar contenido
        $id = new Zend_Form_Element_Hidden('idEvaluacion');
        if ($codigo ==10) {

            $asignaturamodel = new Application_Model_DbTable_Asignaturascursos();


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
                "" => "Seleccione Indicador",
            ));


            $tipos=array('1');
            $rowsetasignatura = $asignaturamodel->listarniveltipos($id_curso,$idperiodo,$tipos);

            foreach ($rowsetasignatura as $row) {
                $asignatura->addMultiOption($row->idAsignatura, $row->nombreAsignatura);
            }

            if ($idtevalucacion == '1') {
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
                $tiponota->addMultiOptions(array('' => 'Seleccione Semestre', '1' => 'Primer Semestre', '2' => 'Segundo Semestre'));
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
                $tiponota->addMultiOptions(array('' => 'Seleccione Trimestre', '3' => 'Primer Trimestre', '4' => 'Segundo Trimestre', '5' => 'Tercer Trimestre'));
            }

            //fecha de la prueba
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

            $this->addElements(array($id, $curso, $nombreambito, $nombrenucleo, $asignatura, $contenidos, $tiponota, $fecha));

        } else {

            $asignatura = new Zend_Form_Element_Select('idAsignatura');
            $asignatura->setRequired(true);
            $asignatura->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));
            $asignatura->setLabel('Seleccione Asignatura: ');
            $asignatura->addMultiOptions(array(
                "" => "Seleccione Asignatura",
            ));
            //Tipos de asignaturas 1=normal 2=Taller 4=asignatura que compone a otra.
            $tipos=array('1','2','4','5');
            //si el ingreso de notas es 1, el profesor Jefe ingresa las notas
            if ($ingresonota == 1) {



                $rowsetasignatura = $asignaturamodel->listarniveltipos($id_curso,$idperiodo,$tipos);
                $clases=array();
                foreach ($rowsetasignatura as $row) {
                    $asignatura->addMultiOption($row->idAsignatura, $row->nombreAsignatura);
                    $clases[$row->idAsignatura]=$row->tipoAsignatura;
                }
                $asignatura->setAttrib('optionClasses', $clases);

            } else {
                //si no lo ingresan por asignaturas.
                $cursomodel = new Application_Model_DbTable_Detallecursocuenta();
                $asignaturamodel = new Application_Model_DbTable_Asignaturascursos();

                $rowcurso = $cursomodel->listarcursoasignatura($id_detalle_curso);
                $listaasigatura=unserialize($rowcurso[0]['asignaturasLista']);
                $rowsetasignatura=$asignaturamodel->getasignaturas($listaasigatura);
                $clases=array();
                foreach ($rowsetasignatura as $row) {
                    $asignatura->addMultiOption($row->idAsignatura, $row->nombreAsignatura);
                    $clases[$row->idAsignatura]=$row->tipoAsignatura;
                }
                $asignatura->setAttrib('optionClasses', $clases);
            }

            if ($idtevalucacion == '1') {
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
                $tiponota->addMultiOptions(array('' => 'Seleccione Semestre', '1' => 'Primer Semestre', '2' => 'Segundo Semestre'));
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
                $tiponota->addMultiOptions(array('' => 'Seleccione Trimestre', '3' => 'Primer Trimestre', '4' => 'Segundo Trimestre', '5' => 'Tercer Trimestre'));
            }

            //fecha de la prueba
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

            $coef = new Zend_Form_Element_Select('coef');
            $coef->setLabel('Coeficiente:')->setRequired(true);
            $coef->addMultiOptions(array('null' => 'Seleccione', '1' => 'Coeficiente 1', '2' => 'Coeficiente 2'));
            $coef->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));

            $this->addElements(array($id, $curso, $asignatura, $contenidos, $tiponota, $fecha, $coef));

        }

    }

}
