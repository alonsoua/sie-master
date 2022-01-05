<?php

class Application_Form_Cursos extends Zend_Form
{

    public function init()
    {

        $usuario = new Zend_Session_Namespace('establecimiento');
        $establecimiento = $usuario->establecimiento;

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $this->setName('Cursos');

        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'table')),
            'Form'
        ));

        $nivel = new Zend_Form_Element_Select('idNiveles');
        $nivel->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $nivel->setLabel('Seleccione Nivel: ')->setRequired(true);
        $nivel->setAttrib('id', 'nivel');
        $nivel->addMultiOptions(array(
            "" => "Seleccione Nivel"
        ));

        $nivelModel = new Application_Model_DbTable_Nivel();
        $rowsetnivel = $nivelModel->listarnivel();
        foreach ($rowsetnivel as $row) {
            $nivel->addMultiOption($row->idNiveles, $row->nombreNiveles);
        }

        $idcurso = new Zend_Form_Element_Hidden('idCursos');
        $idcurso->addValidator('digits');

        $nombrecurso = new Zend_Form_Element_Text('nombreCursos');
        $nombrecurso->setLabel('Nombre Curso:')->setRequired(true);
        $nombrecurso->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));

        $cantidad = new Zend_Form_Element_Text('cantidad');
        $cantidad->setLabel('Cantidad Alumnos:')->setRequired(true);
        $cantidad->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));


        $establecimientoSelect = new Zend_Form_Element_Select('idEstablecimiento');
        $establecimientoSelect->setDecorators(array(
            'ViewHelper',
            array('Errors'),
            array(array('data' => 'HtmlTag'), array('tag' => 'td', 'class' => 'element')),
            array('Label', array('tag' => 'td')),
            array(array('row' => 'HtmlTag'), array('tag' => 'tr')),
        ));
        $establecimientoSelect->setLabel('Establecimiento: ');
        $establecimientoSelect->setAttrib('id', 'establecimiento');

        $asignaturaModel = new Application_Model_DbTable_Establecimiento();
        if ($rol == '4' || $rol == '3' || $rol == '6') {
            $establecimientoSelect->addMultiOptions(array(
                "" => "Seleccione Establecimiento"
            ));
            foreach ($asignaturaModel->listarestablecimiento($establecimiento) as $c) {

                $establecimientoSelect->addMultiOption($c->idEstablecimiento, $c->nombreEstablecimiento);
            }
        }

        if ($rol == '1') {
            $establecimientoSelect->addMultiOptions(array(
                "" => "Seleccione Establecimiento"
            ));
            foreach ($asignaturaModel->listar() as $c) {


                $establecimientoSelect->addMultiOption($c->idEstablecimiento, $c->nombreEstablecimiento);
            }
        }
        $enviar = new Zend_Form_Element_Submit('submit');
        $enviar->setAttrib('idcurso', 'submitbutton');
        $enviar->setDecorators(array(
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
            ->setAttrib('onclick', 'window.location =\'' . $this->getView()->url(array('controller' => 'Cursos', 'action' => 'index'), null, TRUE) . '\' ');
        $this->addElements(array($idcurso, $nombrecurso, $cantidad, $establecimientoSelect, $nivel, $enviar,$volver));

    }

}

