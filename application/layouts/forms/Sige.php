<?php
/**
 * Created by PhpStorm.
 * User: raulretamal
 * Date: 19-07-17
 * Time: 3:18 PM
 */
class Application_Form_Sige extends Zend_Form
{
    public function init()
    {
        $this->setName('Sige');
        // form decorators
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag',array('tag' => 'table')),
            'Form'
        ));

        //Creamos los Elementos del Formulario

        $id=new Zend_Form_Element_Hidden('idSige');
        $cliente=new Zend_Form_Element_Text('ClienteId');
        $cliente->setLabel('Cliente')->addValidator('digits')->setRequired(true);

        $convenio=new Zend_Form_Element_Text('ConvenioId');
        $convenio->setLabel('Convenio')->addValidator('digits')->setRequired(true);


        $token=new Zend_Form_Element_Text('ConvenioToken');
        $token->setLabel('Token')->setRequired(true);


        $establecimiento = new Zend_Form_Element_Select('idEstablecimiento');
        $establecimiento->setLabel('Seleccione Establecimiento:')->setRequired(true);
        $modeloestablecimiento = new Application_Model_DbTable_Establecimiento();
        $rowestablecimientos = $modeloestablecimiento->listar();
        $establecimiento->addMultiOption("Null", "Seleccione Establecimiento");
        foreach ($rowestablecimientos as $e) {

            $establecimiento->addMultiOption($e->idEstablecimiento, $e->nombreEstablecimiento);
        }

        //boton para enviar formulario
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('idcurso', 'submitbutton');


        //boton volver
        $join = new Zend_Form_Element_Button('volver');
        $join->setLabel('Volver')
            ->setAttrib('onclick','window.location =\''.$this->getView()->url(array('controller'=>'Sige','action'=>'index'),null, TRUE).'\' ');

        //agrego los objetos creados al formulario
        $this->addElements(array($id,$cliente,$convenio,$token,$establecimiento,$submit,$join));



    }
}