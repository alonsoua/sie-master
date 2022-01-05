<?php
/**
 * Created by PhpStorm.
 * User: raulretamal
 * Date: 24-07-17
 * Time: 5:12 PM
 */
class Application_Form_Tipo extends Zend_Form
{
    public function init()
    {
        $this->setName('Tipo');
        // form decorators
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form'
        ));

        //Creamos los Elementos del Formulario

        $id = new Zend_Form_Element_Hidden('idTipo');

        $establecimiento = new Zend_Form_Element_Select('idEstablecimiento');
        $establecimiento->setLabel('Seleccione Establecimiento:')->setRequired(true);
        $modeloestablecimiento = new Application_Model_DbTable_Establecimiento();
        $rowestablecimientos = $modeloestablecimiento->listar();
        $establecimiento->addMultiOption("Null", "Seleccione Establecimiento");
        foreach ($rowestablecimientos as $e) {

            $establecimiento->addMultiOption($e->idEstablecimiento, $e->nombreEstablecimiento);
        }

        $periodo = new Zend_Form_Element_Select('idPeriodo');
        $periodo->setLabel('Seleccione Periodo:')->setRequired(true);
        $table = new Application_Model_DbTable_Periodo();
        $periodo->addMultiOptions(array(
            "" => "Seleccione Periodo"
        ));
        foreach ($table->listar() as $c)
        {
            $periodo->addMultiOption($c->idPeriodo,$c->nombrePeriodo);
        }

        $codigo = new Zend_Form_Element_Select('idCodigoTipo');
        $codigo->setLabel('Seleccione Tipo de enseñanza:')->setRequired(true);
        $modelocodigo = new Application_Model_DbTable_Codigo();
        $rowcodigo = $modelocodigo->listar();
        $codigo->addMultiOption("Null", "Seleccione Tipo Enseñanza");
        foreach ($rowcodigo as $e) {

            $codigo->addMultiOption($e->idCodigoTipo, '('.$e->idCodigoTipo.')'.$e->nombreTipoEnsenanza);
        }

        $estado = new Zend_Form_Element_Select('estadoTipo');
        $estado->setLabel('Estado Tipo Enseñanza:')->setRequired(true);
        $estado->addMultiOption("1", "1");
        $estado->addMultiOption("2", "2");

        $autorizacion = new Zend_Form_Element_Text('autorizacion');
        $autorizacion->setLabel('Número Autorización')->addValidator('digits')->setRequired(true);

        $fechaautorizacion = new Zend_Form_Element_Text('fechaAutorizacion');
        $fechaautorizacion->setLabel('Fecha Autorización')->setRequired(true);

        $centro = new Zend_Form_Element_Select('centro');
        $centro->setLabel('Tiene Centro de Padre:')->setRequired(true);
        $centro->addMultiOption("1", "Si");
        $centro->addMultiOption("0", "No");

        $juridica = new Zend_Form_Element_Select('juridica');
        $juridica->setLabel('Tiene Personalidad Jurídica:')->setRequired(true);
        $juridica->addMultiOption("1", "Si");
        $juridica->addMultiOption("0", "No");

        $numero = new Zend_Form_Element_Text('numeroGrupo');
        $numero->setLabel('Número Grupo Diferenciales')->addValidator('digits')->setRequired(true);

        $iniciomanana = new Zend_Form_Element_Text('inicioManana');
        $iniciomanana->setLabel('Horario Inicio Mañana')->setRequired(true)->setAttrib('class', 'horario');

        $terminomanana = new Zend_Form_Element_Text('terminoManana');
        $terminomanana->setLabel('Horario Termino Mañana')->setRequired(true)->setAttrib('class', 'horario');

        $iniciotarde = new Zend_Form_Element_Text('inicioTarde');
        $iniciotarde->setLabel('Horario Inicio Tarde')->setRequired(true)->setAttrib('class', 'horario');

        $terminotarde = new Zend_Form_Element_Text('terminoTarde');
        $terminotarde->setLabel('Horario Termino Tarde')->setRequired(true)->setAttrib('class', 'horario');

        $iniciomananatarde = new Zend_Form_Element_Text('inicioMananaTarde');
        $iniciomananatarde->setLabel('Horario Inicio Mañana Tarde')->setRequired(true)->setAttrib('class', 'horario');

        $terminomananatarde = new Zend_Form_Element_Text('terminoMananaTarde');
        $terminomananatarde->setLabel('Horario Termino Mañana Tarde')->setRequired(true)->setAttrib('class', 'horario');

        $iniciovespertino = new Zend_Form_Element_Text('inicioVespertino');
        $iniciovespertino->setLabel('Horario Inicio Vespertino')->setRequired(true)->setAttrib('class', 'horario');

        $terminovespertino = new Zend_Form_Element_Text('terminoVespertino');
        $terminovespertino->setLabel('Horario Termino Vespertino')->setRequired(true)->setAttrib('class', 'horario');


        //boton para enviar formulario
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('idcurso', 'submitbutton');


        //boton volver
        $join = new Zend_Form_Element_Button('volver');
        $join->setLabel('Volver')
            ->setAttrib('onclick', 'window.location =\'' . $this->getView()->url(array('controller' => 'Sige', 'action' => 'index'), null, TRUE) . '\' ');

        //agrego los objetos creados al formulario
        $this->addElements(array($id,$establecimiento,$periodo,$codigo,$estado,$autorizacion,$fechaautorizacion,$centro,$juridica,$numero,$iniciomanana,$terminomanana,$iniciotarde,$terminotarde,$iniciomananatarde,$terminomananatarde,$iniciovespertino,$terminovespertino,$submit, $join));


    }
}