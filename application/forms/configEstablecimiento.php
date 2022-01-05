<?php

class Application_Form_configEstablecimiento extends Zend_Form
{

    protected $_tipo;

    public function setParams($tipo)
    {
        $this->_tipo = $tipo;
    }

    public function init()
    {
        $this->setName('formElem');
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form',
        ));




        $id = new Zend_Form_Element_Hidden('idConfiguracion');
        $idest = new Zend_Form_Element_Hidden('idEstablecimiento');
        $tipoevaluacion = new Zend_Form_Element_Hidden('tipoEvaluacion');


        $numero = new Zend_Form_Element_Text('numeroDecreto');
        $numero->setLabel('Número:')->setRequired(true)->addValidator('digits');

        $fecha = new Zend_Form_Element_Text('yeardecreto');
        $fecha->setAttrib('id', 'yearperiodo');
        $fecha->setAttrib('readonly', 'true');
        $fecha->setLabel('Año :')->setRequired(true);

        $aproximarasignatura = new Zend_Form_Element_Radio('aproxAsignatura');
        $aproximarasignatura->setLabel('Aproximar Promedio Asignatura por Semestre o Trimestre: ')->addMultiOptions(array(
            '1' => 'SI',
            '2' => 'NO',

        ))
            ->setSeparator(' ')->setValue('1');
        $aproximarasignatura->setOptions(array('label_class' => array('class' => 'control-label')));

        $aproximarperiodo = new Zend_Form_Element_Radio('aproxPeriodo');
        $aproximarperiodo->setLabel('Aproximar Promedio Final por Semestre o Trimestre : ')->addMultiOptions(array(
            '1' => 'SI',
            '2' => 'NO',

        ))
            ->setSeparator(' ')->setValue('1');
        $aproximarperiodo->setOptions(array('label_class' => array('class' => 'control-label')));

        $aproximarasignaturafinal = new Zend_Form_Element_Radio('aproxAnual');
        $aproximarasignaturafinal->setLabel('Aproximar Promedio Asignatura Final: ')->addMultiOptions(array(
            '1' => 'SI',
            '2' => 'NO',

        ))
            ->setSeparator(' ')->setValue('1');
        $aproximarasignaturafinal->setOptions(array('label_class' => array('class' => 'control-label')));

        $aproxfinal = new Zend_Form_Element_Radio('aproxFinal');
        $aproxfinal->setLabel('Aproximar Promedio Final: ')->addMultiOptions(array(
            '1' => 'SI',
            '2' => 'NO',

        ))
            ->setSeparator(' ')->setValue('1');
        $aproxfinal->setOptions(array('label_class' => array('class' => 'control-label')));

        $examen = new Zend_Form_Element_Radio('examen');
        $examen->setLabel('Utilizar Examén: ')->addMultiOptions(array(
            '1' => 'SI',
            '2' => 'NO',

        ))
            ->setSeparator(' ')->setValue('1');
        $examen->setOptions(array('label_class' => array('class' => 'control-label')));


        $aproxexamen = new Zend_Form_Element_Radio('aproxExamen');
        $aproxexamen->setLabel('Aproximar Promedio más Examen: ')->addMultiOptions(array(
            '1' => 'SI',
            '2' => 'NO',

        ))
            ->setSeparator(' ')->setValue('1');
        $aproxexamen->setOptions(array('label_class' => array('class' => 'control-label')));



        $monitoreos = new Zend_Form_Element_Radio('monitoreo');
        $monitoreos->setLabel('Monitoreo : ')->addMultiOptions(array(
            '1' => 'Si',
            '0' => 'NO',

        ))
            ->setSeparator(' ')->setValue('0');


        $fechaprimer = new Zend_Form_Element_Text('fechaPrimer');
        $fechaprimer->setAttrib('id', 'fechaprimer');
        $fechaprimer->setAttrib('readonly', 'true');


        $fechasegundo = new Zend_Form_Element_Text('fechaSegundo');
        $fechasegundo->setAttrib('id', 'fechasegundo');
        $fechasegundo->setAttrib('readonly', 'true');


        $fechatercer = new Zend_Form_Element_Text('fechaTercer');
        $fechatercer->setAttrib('id', 'fechatercer');
        $fechatercer->setAttrib('readonly', 'true');


        $monitoreos->setOptions(array('label_class' => array('class' => 'control-label')));
        //boton para enviar formulario
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('RBD', 'submitbutton');
        $submit->setLabel('Guardar');

        //Nuevos Campos Configuracion de Informes

        //informe Parcial
        $profesorparcial = new Zend_Form_Element_Radio('profesorParcial');
        $profesorparcial->setLabel('Mostrar Profesor Jefe: ')->addMultiOptions(array(
            '1' => 'SI',
            '2' => 'NO',

        ))
            ->setSeparator(' ')->setValue('1');
        $profesorparcial->setOptions(array('label_class' => array('class' => 'control-label')));

        $apoderadoparcial = new Zend_Form_Element_Radio('apoderadoParcial');
        $apoderadoparcial->setLabel('Mostrar Apoderado: ')->addMultiOptions(array(
            '1' => 'SI',
            '2' => 'NO',

        ))
            ->setSeparator(' ')->setValue('1');
        $apoderadoparcial->setOptions(array('label_class' => array('class' => 'control-label')));

        $directorparcial = new Zend_Form_Element_Radio('directorParcial');
        $directorparcial->setLabel('Mostrar Director: ')->addMultiOptions(array(
            '1' => 'SI',
            '2' => 'NO',

        ))
            ->setSeparator(' ')->setValue('1');
        $directorparcial->setOptions(array('label_class' => array('class' => 'control-label')));

        //Informe Periodo

        $profesorperiodo = new Zend_Form_Element_Radio('profesorPeriodo');
        $profesorperiodo->setLabel('Mostrar Profesor Jefe: ')->addMultiOptions(array(
            '1' => 'SI',
            '2' => 'NO',

        ))
            ->setSeparator(' ')->setValue('1');
        $profesorperiodo->setOptions(array('label_class' => array('class' => 'control-label')));

        $apoderadoperiodo = new Zend_Form_Element_Radio('apoderadoPeriodo');
        $apoderadoperiodo->setLabel('Mostrar Apoderado: ')->addMultiOptions(array(
            '1' => 'SI',
            '2' => 'NO',

        ))
            ->setSeparator(' ')->setValue('1');
        $apoderadoperiodo->setOptions(array('label_class' => array('class' => 'control-label')));

        $directorperiodo = new Zend_Form_Element_Radio('directorPeriodo');
        $directorperiodo->setLabel('Mostrar Director: ')->addMultiOptions(array(
            '1' => 'SI',
            '2' => 'NO',

        ))
            ->setSeparator(' ')->setValue('1');
        $directorperiodo->setOptions(array('label_class' => array('class' => 'control-label')));


        $mostrarprimeroperiodo = new Zend_Form_Element_Radio('primerPeriodo');
        $mostrarprimeroperiodo->setLabel('Mostrar Promedio General I Semestre: ')->addMultiOptions(array(
            '1' => 'SI',
            '2' => 'NO',

        ))
            ->setSeparator(' ')->setValue('1');
        $mostrarprimeroperiodo->setOptions(array('label_class' => array('class' => 'control-label')));

        $mostrarsegundoperiodo = new Zend_Form_Element_Radio('segundoPeriodo');
        $mostrarsegundoperiodo->setLabel('Mostrar Promedio General II Semestre: ')->addMultiOptions(array(
            '1' => 'SI',
            '2' => 'NO',

        ))
            ->setSeparator(' ')->setValue('1');
        $mostrarsegundoperiodo->setOptions(array('label_class' => array('class' => 'control-label')));

        //Informe Anual
        $profesoranual = new Zend_Form_Element_Radio('profesorAnual');
        $profesoranual->setLabel('Mostrar Profesor Jefe: ')->addMultiOptions(array(
            '1' => 'SI',
            '2' => 'NO',

        ))
            ->setSeparator(' ')->setValue('1');
        $profesoranual->setOptions(array('label_class' => array('class' => 'control-label')));

        $apoderadoanual = new Zend_Form_Element_Radio('apoderadoAnual');
        $apoderadoanual->setLabel('Mostrar Apoderado: ')->addMultiOptions(array(
            '1' => 'SI',
            '2' => 'NO',

        ))
            ->setSeparator(' ')->setValue('1');
        $apoderadoanual->setOptions(array('label_class' => array('class' => 'control-label')));

        $directoranual = new Zend_Form_Element_Radio('directorAnual');
        $directoranual->setLabel('Mostrar Director: ')->addMultiOptions(array(
            '1' => 'SI',
            '2' => 'NO',

        ))
            ->setSeparator(' ')->setValue('1');
        $directoranual->setOptions(array('label_class' => array('class' => 'control-label')));


        //Informe Ranking
        $profesorranking = new Zend_Form_Element_Radio('profesorRanking');
        $profesorranking->setLabel('Mostrar Profesor Jefe: ')->addMultiOptions(array(
            '1' => 'SI',
            '2' => 'NO',

        ))
            ->setSeparator(' ')->setValue('1');
        $profesorranking->setOptions(array('label_class' => array('class' => 'control-label')));


        $directorranking = new Zend_Form_Element_Radio('directorRanking');
        $directorranking->setLabel('Mostrar Director: ')->addMultiOptions(array(
            '1' => 'SI',
            '2' => 'NO',

        ))
            ->setSeparator(' ')->setValue('1');
        $directorranking->setOptions(array('label_class' => array('class' => 'control-label')));


        $activarapp = new Zend_Form_Element_Radio('activarapp');
        $activarapp->setLabel('Activar App: ')->addMultiOptions(array(
            '1' => 'SI',
            '2' => 'NO',

        ))
            ->setSeparator(' ')->setValue('2');
        $activarapp->setOptions(array('label_class' => array('class' => 'control-label')));


        $decimas = new Zend_Form_Element_Text('decimas');
        $decimas->setLabel('Decimales Promedio Ranking:')->setRequired(true)->addValidator('digits')->setAttrib('maxlength','1');


        //creamos select tipo de evaluacion
        $tipoSelect = new Zend_Form_Element_Select('tipoModalidad');
        $tipoSelect->setLabel('Tipo de Evaluación: ');

        $tipoSelect->addMultiOptions(array(
            "" => "Seleccione Modalidad",
        ));

        $tipoSelect->addMultiOption("1", "Semestral");
        $tipoSelect->addMultiOption("2", "Trimestral");


        //agregolos objetos creados al formulario
        //if ($this->_tipo == 1) {
            $fechaprimer->setLabel('Fecha Cierre Notas Primer Semestre:');
            $fechasegundo->setLabel('Fecha Cierre Notas Segundo Semestre:');
            $this->addElements(array($id,$tipoSelect, $idest,$tipoevaluacion, $numero, $fecha, $aproximarasignatura, $aproximarperiodo, $aproximarasignaturafinal, $aproxfinal,$examen,$aproxexamen, $monitoreos,$profesorparcial,$apoderadoparcial,$directorparcial,$profesorperiodo,$apoderadoperiodo,$directorperiodo,$mostrarprimeroperiodo,$mostrarsegundoperiodo,$profesoranual,$apoderadoanual,$directoranual,$profesorranking,$directorranking,$decimas,$activarapp, $submit));

//        } else {
//
//            $fechaprimer->setLabel('Fecha Cierre Notas Primer Trimestre:');
//            $fechasegundo->setLabel('Fecha Cierre Notas Segundo Trimestre:');
//            $fechatercer->setLabel('Fecha Cierre Notas Tercer Trimestre:');
//            $this->addElements(array($id,$tipoSelect, $idest,$tipoevaluacion, $numero, $fecha, $aproximarasignatura, $aproximarperiodo, $aproximarasignaturafinal, $aproxfinal, $examen,$aproxexamen,$monitoreos,$profesorparcial,$apoderadoparcial,$directorparcial,$profesorperiodo,$apoderadoperiodo,$directorperiodo,$mostrarprimeroperiodo,$mostrarsegundoperiodo,$profesoranual,$apoderadoanual,$directoranual, $profesorranking,$directorranking,$decimas, $activarapp, $submit));
//
//
//        }
        //se crean los grupos en que se mostrara el formulario
        $this->addDisplayGroup(array(
            //agrego los campos que iran dentro del primer fieldset
            'idEstablecimiento',
            'numeroDecreto',
            'yeardecreto',
            'tipoModalidad'

        ), 'general', array('legend' => 'Resolución Exenta de Educación'));

        $contact = $this->getDisplayGroup('general');
        $contact->setDecorators(array(

            'FormElements',
            'Fieldset',

        ));


        //if ($this->_tipo == 1) {
            $this->addDisplayGroup(array(
                //agrego los campos que iran dentro del primer fieldset
                'aproxAsignatura',
                'aproxPeriodo',
                'aproxAnual',
                'aproxFinal',
                'examen',
                'aproxExamen',
                'monitoreo',


            ), 'promedios', array('legend' => 'Configuración Notas y Promedios'));

            $contact = $this->getDisplayGroup('promedios');
            $contact->setDecorators(array(

                'FormElements',
                'Fieldset',

            ));

            //Parcial

            $this->addDisplayGroup(array(
                //agrego los campos que iran dentro del primer fieldset
                'profesorParcial',
                'apoderadoParcial',
                'directorParcial',


            ), 'informes', array('legend' => 'Informe Parcial'));

            $informe = $this->getDisplayGroup('informes');
            $informe->setDecorators(array(

                'FormElements',
                'Fieldset',

            ));

            //Periodo

            $this->addDisplayGroup(array(
                //agrego los campos que iran dentro del primer fieldset
                'profesorPeriodo',
                'apoderadoPeriodo',
                'directorPeriodo',
                'primerPeriodo',
                'segundoPeriodo'


            ), 'informesperiodo', array('legend' => 'Informe Periodo'));

            $informeperiodo = $this->getDisplayGroup('informesperiodo');
            $informeperiodo->setDecorators(array(

                'FormElements',
                'Fieldset',

            ));

            //Anual

            $this->addDisplayGroup(array(
                //agrego los campos que iran dentro del primer fieldset
                'profesorAnual',
                'apoderadoAnual',
                'directorAnual',


            ), 'informesanual', array('legend' => 'Informe Anual'));

            $informeanual = $this->getDisplayGroup('informesanual');
            $informeanual->setDecorators(array(

                'FormElements',
                'Fieldset',

            ));

            //ranking

            $this->addDisplayGroup(array(
                //agrego los campos que iran dentro del primer fieldset
                'decimas',
                'profesorRanking',
                'directorRanking'

            ), 'informesran', array('legend' => 'Informe Ranking'));

            $informer = $this->getDisplayGroup('informesran');
            $informer->setDecorators(array(

                'FormElements',
                'Fieldset',

            ));

//        } else {
//            $this->addDisplayGroup(array(
//                //agrego los campos que iran dentro del primer fieldset
//                'aproxAsignatura',
//                'aproxPeriodo',
//                'aproxAnual',
//                'aproxFinal',
//                'examen',
//                'aproxExamen',
//                'ingresonota',
//
//
//            ), 'promedios', array('legend' => 'Configuración Notas y Promedios'));
//
//            $contact = $this->getDisplayGroup('promedios');
//            $contact->setDecorators(array(
//
//                'FormElements',
//                'Fieldset',
//
//            ));
//
//            //Parcial
//
//            $this->addDisplayGroup(array(
//                //agrego los campos que iran dentro del primer fieldset
//                'profesorParcial',
//                'apoderadoParcial',
//                'directorParcial',
//
//
//            ), 'informes', array('legend' => 'Informe Parcial'));
//
//            $informe = $this->getDisplayGroup('informes');
//            $informe->setDecorators(array(
//
//                'FormElements',
//                'Fieldset',
//
//            ));
//
//            //Periodo
//
//            $this->addDisplayGroup(array(
//                //agrego los campos que iran dentro del primer fieldset
//                'profesorPeriodo',
//                'apoderadoPeriodo',
//                'directorPeriodo',
//                'primerPeriodo',
//                'segundoPeriodo'
//
//
//            ), 'informesperiodo', array('legend' => 'Informe Periodo'));
//
//            $informeperiodo = $this->getDisplayGroup('informesperiodo');
//            $informeperiodo->setDecorators(array(
//
//                'FormElements',
//                'Fieldset',
//
//            ));
//
//            //Anual
//
//            $this->addDisplayGroup(array(
//                //agrego los campos que iran dentro del primer fieldset
//                'profesorAnual',
//                'apoderadoAnual',
//                'directorAnual',
//
//
//            ), 'informesanual', array('legend' => 'Informe Anual'));
//
//            $informeanual = $this->getDisplayGroup('informesanual');
//            $informeanual->setDecorators(array(
//
//                'FormElements',
//                'Fieldset',
//
//            ));
//
//            //ranking
//
//            $this->addDisplayGroup(array(
//                //agrego los campos que iran dentro del primer fieldset
//                'decimas',
//                'profesorRanking',
//                'directorRanking'
//
//
//
//            ), 'informesran', array('legend' => 'Informe Ranking'));
//
//            $informer = $this->getDisplayGroup('informesran');
//            $informer->setDecorators(array(
//
//                'FormElements',
//                'Fieldset',
//
//            ));
//
//        }

        $this->addDisplayGroup(array(
            //agrego los campos que iran dentro del primer fieldset
            'activarapp'

        ), 'app', array('legend' => 'APP Apoderados'));

        $app = $this->getDisplayGroup('app');
        $app->setDecorators(array(

            'FormElements',
            'Fieldset',

        ));

        //agrego los botones que iran en el fieldset de botones
        $this->addDisplayGroup(array(

            'submit',

        ), 'botones', array('legend' => 'Enviar Formulario'));

        $botones = $this->getDisplayGroup('botones');
        $botones->setDecorators(array(

            'FormElements',
            'Fieldset',

        ));

    }
}
