<?php
defined('APPLICATION_UPLOADS_DIR')
|| define('APPLICATION_UPLOADS_DIR', realpath(dirname(__FILE__) . '/../documentos/logos'));

class Application_Form_Establecimiento extends Zend_Form
{

    public function init()
    {
        $this->setName('formElem');
        // form decorators
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form',
        ));

        //campo <input text> para guardar rbd establecimiento
        $id = new Zend_Form_Element_Hidden('idEstablecimiento');

        $id->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        //campo <input text> para guardar rbd establecimiento
        $RBD = new Zend_Form_Element_Text('rbd');
        $RBD->setLabel('RBD:')->setRequired(true);
        $RBD->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        //$RBD->setErrorMessages(array('messages'=>'El campo RBD solo puede contener numeros'));

        //creamos select para seleccionar Sostenedor
        $sostenedor = new Zend_Form_Element_Select('idSostenedor');
        $sostenedor->setLabel('Seleccione Sostenedor:')->setRequired(true);
        $sostenedor->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        //cargo en un select los Peridos
        $tables = new Application_Model_DbTable_Sostenedor();
        //obtengo listado de todos los Peridos y los recorro en un
        //arreglo para agregarlos a la lista
        foreach ($tables->listar() as $e) {

            //$sostenedor=$e['Rut_sostenedor'];

            $sostenedor->addMultiOption($e->idSostenedor, $e->nombreSostenedor);
        }

        //creamos <input text> para escribir nombre Establecimiento
        $nombre_establecimiento = new Zend_Form_Element_Text('nombreEstablecimiento');
        $nombre_establecimiento->setLabel('Nombre Establecimiento:')->setRequired(true);
        $nombre_establecimiento->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        //$nombre_establecimiento->setErrorMessages(array('messages' => 'El campo nombre solo puede contener letras'));

        //creamos <input text> para escribir dependencia establecimiento
        $dependencia = new Zend_Form_Element_Text('dependencia');
        $dependencia->setLabel('Dependencia:')->setRequired(true);
        $dependencia->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        //creamos select comuna sostenedor
        $comunaSelect = new Zend_form_element_select('comuna');
        $comunaSelect->setLabel('Comuna: ')->setRequired(true);

        $comunamodel = new Application_Model_DbTable_Comuna();
        $rowset = $comunamodel->listar();
        foreach ($rowset as $row) {
            $comunaSelect->addMultiOption($row->idComuna, $row->nombreComuna);
        }

        $comunaSelect->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        //creamos <input text> para escribir direccion
        $direccion = new Zend_Form_Element_Text('direccion');
        $direccion->setLabel('Dirección:')->setRequired(true);
        $direccion->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        //creamos <input text> para escribir Telefono
        $telefono = new Zend_Form_Element_Text('telefono');
        $telefono->setLabel('Telefóno:')->setRequired(true);
        $telefono->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        //creamos <input text> para escribir correo
        $correo = new Zend_Form_Element_Text('correo');
        $correo->setLabel('Correo:')->setRequired(true);
        $correo->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        //creamos <input text> para escribir correo
        $matricula = new Zend_Form_Element_Text('matricula');
        $matricula->setLabel('Matricula:')->setRequired(true);
        $matricula->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        //creamos <input text> para escribir nombre subvencion
        $nombresub = new Zend_Form_Element_Text('subvencion');
        $nombresub->setLabel('Nombre Subvencion:')->setRequired(true);
        $nombresub->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        //creamos <input text> para escribir concentracion
        $concentracion = new Zend_Form_Element_Text('concentracion');
        $concentracion->setLabel('Concentracion:')->setRequired(true);
        $concentracion->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        //creamos <input text> para escribir concentracion
        $calificacion = new Zend_Form_Element_Text('calificacion');
        $calificacion->setLabel('Calificacion:')->setRequired(true);
        $calificacion->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        //creamos <input text> para escribir concentracion
        $matriculapie = new Zend_Form_Element_Text('matriculapie');
        $matriculapie->setLabel('Matricula PIE:')->setRequired(true);
        $matriculapie->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
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

        //boton volver
        $join = new Zend_Form_Element_Button('join');
        $join->setLabel('Volver')
            ->setAttrib('onclick', 'window.location =\'' . $this->getView()->url(array('controller' => 'Establecimiento', 'action' => 'index'), null, true) . '\' ');
        $join->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        //logo
        $logo = new Zend_Form_Element_File('logo');
        $logo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
        $logo->addValidator('Size', false, '10024000');
        $logo->addValidator('ImageSize', false,
            array(
                'maxwidth' => 300,

                'maxheight' => 300)
        );
        $logo->setLabel('Logo(jpg,jpeg,png,gif):');

        $logo->setDestination(APPLICATION_UPLOADS_DIR);
        $logo->setDecorators(array(
            'File',
            'Errors',
            array(array('data' => 'HtmlTag'), array('tag' => 'td')),
            array('Label', array('tag' => 'th')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        //creamos select tipo de evaluacion
        $tipoSelect = new Zend_Form_Element_Select('tipoEvaluacion');
        $tipoSelect->setLabel('Tipo de Evaluación: ');

        $tipoSelect->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $tipoSelect->addMultiOptions(array(
            "" => "Seleccione Tipo",
        ));

        $tipoSelect->addMultiOption("1", "Semestral");
        $tipoSelect->addMultiOption("2", "Trimestral");

        //agregolos objetos creados al formulario
        $this->addElements(array($id, $RBD, $logo, $sostenedor, $nombre_establecimiento, $dependencia, $comunaSelect, $direccion, $telefono, $correo, $nombresub, $matricula, $concentracion, $calificacion, $matriculapie, $tipoSelect, $submit, $join));

    }
}
