<?php

class Application_Form_Libro extends Zend_Form
{

    public function init()
    {
        $this->setName('libro');
        //campo <input hidden> para guardar idPeriodo
        $idlibro = new Zend_Form_Element_Hidden('idLibro');
        $idlibro->addValidator('digits');

        //creamos select para seleccionar Curso
        $curso = new Zend_Form_Element_Select('idCursos');
        $curso->setLabel('Seleccione Curso:')->setRequired(true);
        //cargo en un select los Cursos
        $tables = new Application_Model_DbTable_Cursos();
        //obtengo listado de todos los Peridos y los recorro en un
        //arreglo para agregarlos a la lista
        foreach ($tables->listar() as $e) {

            //$sostenedor=$e['Rut_sostenedor'];

            $curso->addMultiOption($e->idCursos, $e->nombre_curso);
        }

        //boton para enviar formulario
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('idlibro', 'submitbutton');
        $submit->setLabel('Guardar');


        //boton volver
        $join = new Zend_Form_Element_Button('join');
        $join->setLabel('Volver')
            ->setAttrib('onclick', 'window.location =\'' . $this->getView()->url(array('controller' => 'Libro', 'action' => 'index'), null, TRUE) . '\' ');

        //agregolos objetos creados al formulario
        $this->addElements(array($idlibro, $curso, $submit, $join));


    }


}

