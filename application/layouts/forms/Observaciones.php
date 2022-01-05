<?php

class Application_Form_Observaciones extends Zend_Form
{

    public function init()
    {


        //recuperamos el nivel del curso que esta en sesion
        $nivelcurso = new Zend_Session_Namespace('nivel_curso');
        $nivel_curso = $nivelcurso->nivel_curso;

        $ingreson = new Zend_Session_Namespace('ingresonota');
        $ingresonota = $ingreson->ingresonota;

        //recuperamos el iddetallecursocuenta que esta en sesion
        $id_detalle_cursos = new Zend_Session_Namespace('id_detalle_curso');
        $id_detalle_curso = $id_detalle_cursos->id_detalle_curso;

        //recuperamos el nivel del curso que esta en sesion
        $id_cursos = new Zend_Session_Namespace('id_curso');
        $id_curso = $id_cursos->id_curso;

        $periodo = new Zend_Session_Namespace('periodo');
        $idperiodo = $periodo->periodo;


        $this->setName('formobs');
        // form decorators
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form'
        ));

        //creamos <input text hidden> para curso
        $idalumno = new Zend_Form_Element_Hidden('idAlumnos');
        $idcurso = new Zend_Form_Element_Hidden('idCursos');


        //creamos <input text hidden para rut
        $rut = new Zend_Form_Element_Text('rutAlumno');
        $rut->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $rut->setLabel('Rut:')->setRequired(true);
        $rut->setAttrib('id', 'rutalumno');
        $rut->setAttrib('readonly', 'true');

        //creamos <input text> para escribir nombres
        $nombres = new Zend_Form_Element_Text('nombres');
        $nombres->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $nombres->setLabel('Nombres:')->setRequired(true);
        $nombres->setAttrib('id', 'nombalumno');
        $nombres->setAttrib('readonly', 'true');

        //creamos <input text> para escribir Apellido paterno
        $apaterno = new Zend_Form_Element_Text('apaterno');
        $apaterno->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $apaterno->setLabel('Apellido Paterno:')->setRequired(true);
        $apaterno->setAttrib('id', 'apalumno');
        $apaterno->setAttrib('readonly', 'true');

        //creamos <input text> para escribir Aplliedo Materno
        $amaterno = new Zend_Form_Element_Text('amaterno');
        $amaterno->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $amaterno->setLabel('Apellido Materno:')->setRequired(true);
        $amaterno->setAttrib('id', 'amalumno');
        $amaterno->setAttrib('readonly', 'true');


        $observacion = new Zend_Form_Element_Textarea('observacion');
        $observacion->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $observacion->setLabel('Observacion:')->setRequired(true);
        $observacion->setAttrib('cols', '40')
            ->setAttrib('rows', '5');
        $observacion->setAttrib('id', 'observacion');
        //boton para enviar formulario
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $submit->setAttrib('Rut', 'submitbutton');
        $submit->setLabel('Guardar');
        $submit->setAttrib('id', 'guardarobservaciones');



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
            "" => "Seleccione Asignatura"
        ));

        $asignaturamodel = new Application_Model_DbTable_Asignaturascursos();

        if ($ingresonota == 1) {
            $tipos=array(1,2,3,4,5,6);

            $asignaturamodel = new Application_Model_DbTable_Asignaturascursos();
            $rowsetasignatura = $asignaturamodel->listarniveltipos($id_curso,$idperiodo,$tipos);

            foreach ($rowsetasignatura as $row) {
                $asignatura->addMultiOption($row->idAsignatura, $row->nombreAsignatura);
            }

        } else {
            //si no lo ingresan por asignaturas.
            $cursomodel = new Application_Model_DbTable_Detallecursocuenta();
            $asignaturamodel = new Application_Model_DbTable_Asignaturascursos();

            $rowcurso = $cursomodel->listarcursoasignatura($id_detalle_curso);
            $listaasigatura = unserialize($rowcurso[0]['asignaturasLista']);
            $rowsetasignatura = $asignaturamodel->getasignaturas($listaasigatura);
            foreach ($rowsetasignatura as $row) {
                $asignatura->addMultiOption($row->idAsignatura, $row->nombreAsignatura);
            }
        }


        $this->addElements(array($idalumno, $idcurso, $rut, $nombres, $apaterno, $amaterno, $asignatura,$observacion, $submit));
    }


}

