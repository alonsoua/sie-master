<?php

class Application_Form_Personalidad extends Zend_Form
{

    public function init()
    {
        $this->setName('formElem');

        $idalumno = new Zend_Form_Element_Hidden('idAlumnos');
        $idcurso = new Zend_Form_Element_Hidden('idCursos');
        $idpersonalidad = new Zend_Form_Element_Hidden('idPersonalidad');

        $est = new Zend_Session_Namespace('establecimiento');
        $establecimiento = $est->establecimiento;

        $idtipoev = new Zend_Session_Namespace('tipoevaluacion');
        $idtevalucacion = $idtipoev->tipoevaluacion;

        if ($establecimiento == 12) {
            $conceptos = array(
                '1' => 'Desarrollo Excelente',
                '2' => 'Desarrollo Adecuado',
                '3' => 'Puede Superarse',
                '4' => 'Necesita Refuerzo');
        } else {
            $conceptos = array(
                '1' => 'Siempre',
                '2' => 'Generalmente',
                '3' => 'Ocasionalmente',
                '4' => 'Nunca',
                '5' => 'No Observado');
        }

        $radio1 = new Zend_Form_Element_Radio("R1");
        $radio1->setLabel('Asiste regularmente a clases')->setRequired(true)
            ->addMultiOptions($conceptos)
            ->setSeparator(' ');
        $radio1->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'radioobs')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));


        $radio2 = new Zend_Form_Element_Radio("R2");
        $radio2->setLabel('Ingresa puntualmente a clases')->setRequired(true)
            ->addMultiOptions($conceptos)
            ->setSeparator(' ');

        $radio2->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'radioobs')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $radio3 = new Zend_Form_Element_Radio("R3");
        $radio3->setLabel('Cuida su higiene y presentación personal')->setRequired(true)
            ->addMultiOptions($conceptos)
            ->setSeparator(' ');

        $radio3->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'radioobs')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $radio4 = new Zend_Form_Element_Radio("R4");
        $radio4->setLabel('Cumple puntualmente con sus tareas y deberes escolares')->setRequired(true)
            ->addMultiOptions($conceptos)
            ->setSeparator(' ');

        $radio4->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'radioobs')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $radio5 = new Zend_Form_Element_Radio("R5");
        $radio5->setLabel('Reconoce sus errores y trata de corregirlos')->setRequired(true)
            ->addMultiOptions($conceptos)
            ->setSeparator(' ');

        $radio5->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'radioobs')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $radio6 = new Zend_Form_Element_Radio("R6");
        $radio6->setLabel('Demuestra creatividad en su trabajo escolar')->setRequired(true)
            ->addMultiOptions($conceptos)
            ->setSeparator(' ');

        $radio6->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'radioobs')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));


        $radio7 = new Zend_Form_Element_Radio("R7");
        $radio7->setLabel('Es respetuoso con los miembros de su comunidad escolar')->setRequired(true)
            ->addMultiOptions($conceptos)
            ->setSeparator(' ');

        $radio7->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'radioobs')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $radio8 = new Zend_Form_Element_Radio("R8");
        $radio8->setLabel('Es sincero y veraz en su actuar diario')->setRequired(true)
            ->addMultiOptions($conceptos)
            ->setSeparator(' ');

        $radio8->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'radioobs')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $radio9 = new Zend_Form_Element_Radio("R9");
        $radio9->setLabel(' Respeta y valora las ideas y opiniones de los demás')->setRequired(true)
            ->addMultiOptions($conceptos)
            ->setSeparator(' ');

        $radio9->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'radioobs')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $radio10 = new Zend_Form_Element_Radio("R10");
        $radio10->setLabel('Actúa en forma solidaria frente a los problemas de los demás')->setRequired(true)
            ->addMultiOptions($conceptos)
            ->setSeparator(' ');

        $radio10->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'radioobs')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $radio11 = new Zend_Form_Element_Radio("R11");
        $radio11->setLabel(' Actúa de acuerdo a las normas establecidas en el Manual de Convivencia Escolar')->setRequired(true)
            ->addMultiOptions($conceptos)
            ->setSeparator(' ');

        $radio11->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'radioobs')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $radio12 = new Zend_Form_Element_Radio("R12");
        $radio12->setLabel(' Comunica sus ideas y convicciones en forma clara y coherente')->setRequired(true)
            ->addMultiOptions($conceptos)
            ->setSeparator(' ');

        $radio12->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'radioobs')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $radio13 = new Zend_Form_Element_Radio("R13");
        $radio13->setLabel('Resuelve en forma autónoma los problemas que se le presentan')->setRequired(true)
            ->addMultiOptions($conceptos)
            ->setSeparator(' ');

        $radio13->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'radioobs')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $radio14 = new Zend_Form_Element_Radio("R14");
        $radio14->setLabel('Demuestra creatividad en la ejecución de tareas y/o trabajos')->setRequired(true)
            ->addMultiOptions($conceptos)
            ->setSeparator(' ');

        $radio14->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'radioobs')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $radio15 = new Zend_Form_Element_Radio("R15");
        $radio15->setLabel('Trabaja en forma activa y colaborativa')->setRequired(true)
            ->addMultiOptions($conceptos)
            ->setSeparator(' ');

        $radio15->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'radioobs')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $radio16 = new Zend_Form_Element_Radio("R16");
        $radio16->setLabel('Respeta los símbolos y tradiciones patrias')->setRequired(true)
            ->addMultiOptions($conceptos)
            ->setSeparator(' ');

        $radio16->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'radioobs')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $radio17 = new Zend_Form_Element_Radio("R17");
        $radio17->setLabel('Participa con responsabilidad en actividades del curso y del colegio')->setRequired(true)
            ->addMultiOptions($conceptos)
            ->setSeparator(' ');

        $radio17->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'radioobs')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $radio18 = new Zend_Form_Element_Radio("R18");
        $radio18->setLabel(' Colabora activamente en el cuidado del entorno y en la mantención de los materiales del establecimiento')->setRequired(true)
            ->addMultiOptions($conceptos)
            ->setSeparator(' ');

        $radio18->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'radioobs')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));


        $radio19 = new Zend_Form_Element_Radio("R19");
        $radio19->setLabel('Mantiene una actitud de respeto, modales y lenguaje adecuado hacia los miembros de su comunidad')->setRequired(true)
            ->addMultiOptions($conceptos)
            ->setSeparator(' ');

        $radio19->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'radioobs')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));


        $observacion = new Zend_Form_Element_Textarea('comentario');
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


        $submit = new Zend_Form_Element_Submit('submit');
        $submit->removeDecorator('label');
        $submit->setLabel('Guardar');

        $tiponota = new Zend_Form_Element_Select('segmento');
        $tiponota->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $tiponota->setRequired(true);

        if($idtevalucacion==1){
            $tiponota->addMultiOptions(array(
                '1' => 'Primer Semestre',
                '2' => 'Segundo Semestre'));
        }elseif($idtevalucacion==2){
            $tiponota->addMultiOptions(array(
                '3' => 'Primer Trimestre',
                '4' => 'Segundo Trimestre',
                '5' => 'Tercer Trimestre'));
        }


        $join = new Zend_Form_Element_Button('join');
        $join->setLabel('Volver')
            ->setAttrib('onclick', 'window.location =\'' . $this->getView()->url(array('controller' => 'Informes', 'action' => 'personalidad'), null, TRUE) . '\' ');
        $join->removeDecorator('label');
        if ($establecimiento == 12) {
            $this->addElements(array($idpersonalidad, $idalumno, $idcurso, $tiponota, $radio1,
                $radio2,
                $radio3,
                $radio4,
                $radio5,
                $radio6,
                $radio7,
                $radio8,
                $radio9,
                $radio10,
                $radio11,
                $radio12,
                $radio13,
                $radio14,
                $radio15,
                $radio16,
                $radio17,
                $radio18,
                $radio19,
                $observacion,
                $submit,
                $join));
        } else {
            $this->addElements(array($idpersonalidad, $idalumno, $idcurso, $tiponota, $radio1,
                $radio2,
                $radio3,
                $radio4,
                $radio5,
                $radio6,
                $radio7,
                $radio8,
                $radio9,
                $radio10,
                $radio11,
                $radio12,
                $radio13,
                $radio14,
                $radio15,
                $radio16,
                $radio17,
                $radio18,
                $radio19,
                $submit,
                $join));
        }

        $this->addDisplayGroup(array(
            'R1',
            'R2',
            'R3',
            'R4',
            'R5',
            'R6'
        ), 'general', array('legend' => 'I CRECIMIENTO Y AUTOAFIRMACIÓN PERSONAL.', 'class' => 'step'));

        $contact = $this->getDisplayGroup('general');
        $contact->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Fieldset'
        ));

        $this->addDisplayGroup(array(
            'R7',
            'R8',
            'R9',
            'R10',
            'R11'
        ), 'medico', array('legend' => 'II FORMACIÓN ETICA', 'class' => 'step'));

        $pass = $this->getDisplayGroup('medico');
        $pass->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Fieldset'
        ));
        $this->addDisplayGroup(array(

            'R12',
            'R13',
            'R14'
        ), 'nucleo', array('legend' => 'III DESARROLLO DEL PENSAMIENTO', 'class' => 'step'));

        $web = $this->getDisplayGroup('nucleo');
        $web->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Fieldset'
        ));

        $this->addDisplayGroup(array(
            'R15',
            'R16',
            'R17',
            'R18',
            'R19'

        ), 'nucleo2', array('legend' => 'IV LA PERSONA Y SU ENTORNO', 'class' => 'step'));

        $web2 = $this->getDisplayGroup('nucleo2');
        $web2->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Fieldset'
        ));
        if ($establecimiento == 12) {

            $this->addDisplayGroup(array(
                'comentario',

            ), 'come', array('legend' => 'Observación', 'class' => 'step'));

            $comentario = $this->getDisplayGroup('come');
            $comentario->setDecorators(array(
                'FormElements',
                array('HtmlTag', array('tag' => 'table')),
                'Fieldset'
            ));
        }

        $this->addDisplayGroup(array(

            'submit',
            'join',


        ), 'botones', array('legend' => 'Enviar Formulario', 'class' => 'step'));


        $botones = $this->getDisplayGroup('botones');
        $botones->setDecorators(array(

            'FormElements',
            'Fieldset'

        ));


    }
}
