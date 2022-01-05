<?php

class Application_Form_Configuracionconceptoparvularia extends Zend_Form
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

        $id = new Zend_Form_Element_Hidden('idConcepto');

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


        $valor= new Zend_Form_Element_Text('notaConcepto');
        $valor->setLabel(' Valor:');
        $valor->setAttrib('maxlength','2');
        $valor->setAttrib('size','4');
        $valor->setAttrib('name','notaConcepto[]');
        $valor->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),

        ));
        $descripcion= new Zend_Form_Element_Text('descripcionConcepto');
        $descripcion->setLabel(' Descripción Concepto:');
        $descripcion->setAttrib('size','100');
        $descripcion->setAttrib('name','descripcionConcepto[]');
        $descripcion->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),

        ));


        //agrego los objetos creados al formulario
        $this->addElements(array($id, $concepto,$valor,$descripcion, $enviar));

    }

}
