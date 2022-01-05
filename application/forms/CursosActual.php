<?php
/**
 * Created by PhpStorm.
 * User: raulretamal
 * Date: 25-07-17
 * Time: 9:12 PM
 */

class Application_Form_CursosActual extends Zend_Form
{

    public function init()
    {

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $this->setName('Cursos');
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag',array('tag' => 'table')),
            'Form'
        ));

        $idcurso = new Zend_Form_Element_Hidden('idCursos');

        $establecimiento = new Zend_Form_Element_Select('idEstablecimiento');
        $establecimiento->setLabel('Seleccione Establecimiento:')->setRequired(true);
        $modeloestablecimiento = new Application_Model_DbTable_Establecimiento();
        $establecimiento->addMultiOption("Null", "Seleccione Establecimiento");

        if ($rol == '4' || $rol == '3' || $rol == '6') {
            $establecimiento->addMultiOptions(array(
                "" => "Seleccione Establecimiento"
            ));
            foreach ($modeloestablecimiento->listarestablecimiento($establecimiento) as $c) {

                $establecimiento->addMultiOption($c->idEstablecimiento, $c->nombreEstablecimiento);
            }
        }

        if ($rol == '1') {
            $establecimiento->addMultiOptions(array(
                "" => "Seleccione Establecimiento"
            ));
            foreach ($modeloestablecimiento->listar() as $c) {


                $establecimiento->addMultiOption($c->idEstablecimiento, $c->nombreEstablecimiento);
            }
        }
        $establecimiento->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $codigo = new Zend_Form_Element_Select('idCodigoTipo');
        $codigo->setLabel('Seleccione Tipo de Enseñanza:')->setRequired(true);
        $modelocodigo = new Application_Model_DbTable_Codigo();
        $rowcodigo = $modelocodigo->listartipoensenanza();
        $codigo->addMultiOption("Null", "Seleccione Tipo Enseñanza");
        foreach ($rowcodigo as $e) {

            $codigo->addMultiOption($e['idCodigoTipo'], '('.$e['idCodigoTipo'].') '.$e['nombreTipoEnsenanza']);
        }

        $codigo->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $nivel = new Zend_Form_Element_Select('idNiveles');
        $nivel->setRegisterInArrayValidator(false);
        $nivel->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $nivel->setLabel('Seleccione Grado: ')->setRequired(true);
        $nivel->setAttrib('id', 'nivel');
        $nivel->addMultiOptions(array(
            "" => "Seleccione Grado"
        ));


        $letra= new Zend_Form_Element_Select('letra');
        $letra->setLabel('Letra:')->setRequired(true);
        for($i=65; $i<=90; $i++) {
            $letra->addMultiOptions(array(chr($i)=>chr($i)));

        }

        $letra->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $combinado = new Zend_Form_Element_Select('combinado');
        $combinado->setLabel('Curso Combinado:')->setRequired(true);
        $combinado->addMultiOption("0", "Si");
        $combinado->addMultiOption("1", "No");
        $combinado->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $jornada = new Zend_Form_Element_Select('tipoJornada');
        $jornada->setLabel('Jornada:')->setRequired(true);
        $jornada->addMultiOption("1", "Mañana");
        $jornada->addMultiOption("2", "Tarde");
        $jornada->addMultiOption("3", "Mañana y Tarde");
        $jornada->addMultiOption("4", "Vespertina/Nocturna");
        $jornada->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $codigosector = new Zend_Form_Element_Select('codigoSector');
        $codigosector->setLabel('Código Sector Económico:');
        $modelocodigo = new Application_Model_DbTable_Codigo();
        $rowcodigo = $modelocodigo->listarcodigosector();
        $codigosector->addMultiOption("Null", "Seleccione Sector Económico");
        foreach ($rowcodigo as $e) {

            $codigosector->addMultiOption($e->idCodigoSector, '('.$e->idCodigoSector.')'.$e->nombreSector);
        }
        $codigosector->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $codigoespecialidad = new Zend_Form_Element_Select('codigoEspecialidad');
        $codigoespecialidad->setRegisterInArrayValidator(false);
        $codigoespecialidad->setLabel('Código Especialidad:');
        $codigoespecialidad->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));



        $codigoalternativa = new Zend_Form_Element_Select('codigoAlternativa');
        $codigoalternativa->setLabel('Código Alternativa Desarrollo Curricular:')->setRequired(true);
        $codigoalternativa->addMultiOption("1", "Tradicional (Solo Establecimiento)");
        $codigoalternativa->addMultiOption("2", "Formación Dual (Establecimiento y Empresa)");
        $codigoalternativa->addMultiOption("3", "Otra");
        $codigoalternativa->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $infraestructura = new Zend_Form_Element_Select('infraestructura');
        $infraestructura->setLabel('Tiene Infraestructura:')->setRequired(true);
        $infraestructura->addMultiOption("1", "Si");
        $infraestructura->addMultiOption("0", "No");
        $infraestructura->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));



        $guardar = new Zend_Form_Element_Submit('submit');
        $guardar->setAttrib('idcurso', 'submitbutton');
        $guardar->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $volver = new Zend_Form_Element_Button('join');
        $volver->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $volver->setLabel('Volver')
            ->setAttrib('onclick','window.location =\''.$this->getView()->url(array('controller'=>'Cursos','action'=>'index'),null, TRUE).'\' ');

        $this->addElements(array($idcurso,$establecimiento,$codigo,$nivel,$letra,$combinado,$jornada,$codigosector,$codigoespecialidad,$codigoalternativa,$infraestructura,$guardar,$volver));

    }




}
