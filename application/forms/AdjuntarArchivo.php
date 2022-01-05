<?php
defined('APPLICATION_UPLOADS_DIR')
|| define('APPLICATION_UPLOADS_DIR', realpath(dirname(__FILE__) . '/../documentos/certificados'));

class Application_Form_AdjuntarArchivo extends Zend_Form
{

    public function init()
    {
        $this->setName('formElem');
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form',
        ));
        $id = new Zend_Form_Element_Hidden('idAccidente');
        $id->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $archivo = new Zend_Form_Element_File('certificado');
        $archivo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
        $archivo->addValidator('Size', false, '10024000');
        $archivo->addValidator('ImageSize', false,
            array(
                'maxwidth' => 2000,
                'maxheight' => 1500)
        );
        $archivo->setLabel('Certificado(jpg,jpeg,png,gif):');
        $archivo->setDestination(APPLICATION_UPLOADS_DIR);
        $archivo->setDecorators(array(
            'File',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'th')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));



        //boton para enviar formulario
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('RBD', 'submitbutton');
        $submit->setLabel('Guardar');
        $submit->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));


        $this->addElements(array($id,$archivo, $submit));

    }
}
