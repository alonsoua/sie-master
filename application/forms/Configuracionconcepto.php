<?php

class Application_Form_Configuracionconcepto extends Zend_Form
{


    public function init()
    {

        $this->setName('Configuraciondep');

        // form decorators
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table','id'=>'tablacon')),
            'Form',
        ));

        $id = new Zend_Form_Element_Hidden('idConfiguracionConcepto');

        //boton para enviar formulario
        $enviar = new Zend_Form_Element_Submit('submit');
        $enviar->setAttrib('rut', 'submitbutton', 'class', '');
        $enviar->removeDecorator('label');
        $enviar->setLabel('Guardar');
        $enviar->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $concepto= new Zend_Form_Element_Text('concepto');
        $concepto->setLabel('Concepto Nº 1:');
        $concepto->setAttrib('maxlength','2');
        $concepto->setAttrib('size','4');
        $concepto->setAttrib('name','concepto[]');
        $concepto->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),

        ));


        $desde= new Zend_Form_Element_Text('desde');
        $desde->setLabel(' Nota Desde:');
        $desde->setAttrib('maxlength','2');
        $desde->setAttrib('size','4');
        $desde->setAttrib('name','desde[]');
        $desde->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),

        ));
        $hasta= new Zend_Form_Element_Text('hasta');
        $hasta->setLabel(' Nota Hasta:');
        $hasta->setAttrib('maxlength','2');
        $hasta->setAttrib('size','4');
        $hasta->setAttrib('name','hasta[]');
        $hasta->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),

        ));


        //agrego los objetos creados al formulario
        $this->addElements(array($id, $concepto,$desde,$hasta, $enviar));

    }

}
