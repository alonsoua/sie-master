<?php

class Application_Form_Asignaturas extends Zend_Form
{

    protected $_id;

    public function setParams($id)
    {
        $this->_id = $id;
    }

    public function init()
    {

        $this->setName('Asignaturas');

        // form decorators
        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form',
        ));

        if ($this->_id == 10) {
            $asignaturamodel = new Application_Model_DbTable_Asignaturascursos();
            //campo <input hidden> para guardar Curso
            $id = new Zend_Form_Element_Hidden('idAsignatura');

            $nombre = new Zend_Form_Element_Text('nombreAsignatura');
            $nombre->setLabel('Nombre Indicador: ')->setRequired(true);
            $nombre->setAttrib('size','100');
            $nombre->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));

            $nombrea = new Zend_Form_Element_Select('idNucleo');
            $nombrea->setLabel('Seleccione Núcleo: ')->setRequired(true);
            $nombrea->setRegisterInArrayValidator(false);

            $rowset = $asignaturamodel->listarnucleoambito(1);
            foreach ($rowset as $row) {
                $nombrea->addMultiOption($row->idNucleo, $row->nombreNucleo);
            }
            $nombrea->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));

            $nombreambito = new Zend_Form_Element_Select('idAmbito');
            $nombreambito->setLabel('Seleccione Ambito: ');
            $nombreambito->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));

            $rowsetambito = $asignaturamodel->listarambito();
            foreach ($rowsetambito as $rowambito) {
                $nombreambito->addMultiOption($rowambito->idAmbito, $rowambito->nombreAmbito);
            }

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

            //agrego los objetos creados al formulario
            $this->addElements(array($id, $nombreambito, $nombrea, $nombre, $enviar));

        } else {

            //campo <input hidden> para guardar Curso
            $id = new Zend_Form_Element_Hidden('idAsignatura');
            $nombre = new Zend_Form_Element_Text('nombreAsignatura');
            $nombre->setLabel('Nombre Asignatura: ')->setRequired(true);
            $nombre->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));

            $tipo = new Zend_Form_Element_Select('tipoAsignatura');
            $tipo->setLabel('Tipo de Asignatura: ');
            $tipo->setRegisterInArrayValidator(false);
            $tipo->addMultiOption("1", "Normal");
            $tipo->addMultiOption("2", "Taller");
            $tipo->addMultiOption("3", "Dependiente");
            $tipo->addMultiOption("5", "Concepto");
            $tipo->setDecorators(array(
                'ViewHelper',
                array('Errors'),
                array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
                array('Label', array('tag' => 'td')),
                array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
            ));

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

            //agrego los objetos creados al formulario
            $this->addElements(array($id, $nombre, $tipo, $enviar));
        }
    }

}
