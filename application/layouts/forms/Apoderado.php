<?php

class Application_Form_Apoderado extends Zend_Form
{

    public function init()
    {
       $this->setName('formElem');
     
       
       $idApoderado=new Zend_Form_Element_Hidden('idApoderado');
          //creamos select provincia sostenedor
      $regionSelect = new Zend_Form_Element_Select('region');
      $regionSelect->setLabel('Región: ');
      $regionSelect->setAttrib('id', 'item_region');
      
     $regionModel = new Application_Model_DbTable_Region();
     $rowset1 = $regionModel->listarregiones();
      foreach($rowset1 as $row){
         $regionSelect->addMultiOption($row->idRegion, $row->nombreRegion);
      }
        
        
        //creamos select provincia sostenedor
      $provinciaSelect = new Zend_Form_Element_Select('provincia');
      $provinciaSelect->setLabel('Provincia: ');
      $provinciaSelect->setAttrib('id', 'item_provincia')->setRegisterInArrayValidator(false);
 
      //creamos select comuna sostenedor
      $comunaSelect = new Zend_form_element_select('comuna');
      $comunaSelect->setLabel('Comuna: ');
      $comunaSelect->setAttrib('id', 'item_comuna')->setRegisterInArrayValidator(false);
 
 
     
        
       
        //creamos <input text> para escribir Rut alumno
        
        $rutapoderado = new Zend_Form_Element_Text('rutApoderado');
        $rutapoderado->setLabel('Rut Apoderado:')->setRequired(true);
       
        
        
        //creamos <input text> para escribir nombres Apoderado
        $nombresapoderado = new Zend_Form_Element_Text('nombreApoderado');
        $nombresapoderado->setLabel('Nombre Apoderado:')->setRequired(true);
        

        
        //creamos <input text> para escribir Apellido paterno Apoderado
        $apaternoapoderado = new Zend_Form_Element_Text('paternoApoderado');
        $apaternoapoderado->setLabel('Apellido Paterno:')->setRequired(true);
        
        //creamos <input text> para escribir Aplliedo Materno
        $amaternoapoderado = new Zend_Form_Element_Text('maternoApoderado');
        $amaternoapoderado->setLabel('Apellido Materno:')->setRequired(true);
        
        //creamos <input text> para escribir direccion
        $direccionapoderado = new Zend_Form_Element_Text('direccionApoderado');
        $direccionapoderado->setLabel('Direccion:')->setRequired(true);
        
        //creamos <input text> para escribir telefono
        $telefonoapoderado = new Zend_Form_Element_Text('telefonoApoderado');
        $telefonoapoderado->setLabel('Telefono:')->addValidator('digits');
        
    
      
        
        
        
        //creamos <input text> para escribir correo 
        $correoapoderado = new Zend_Form_Element_Text('correoApoderado');
        $correoapoderado->setLabel('Correo:');
        
        $relacion = new Zend_Form_Element_Text('relacion');
        $relacion->setLabel('Parentesco con el Alumno:');
        
        //boton para enviar formulario
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->setAttrib('rut', 'submitbutton','class','');
        $submit->removeDecorator('label');
        $submit->setLabel('Guardar');
        
        
        
        //boton volver
        $join = new Zend_Form_Element_Button('join');
        $join->setLabel('Volver')
            ->setAttrib('onclick','window.location =\''.$this->getView()->url(array('controller'=>'Alumnos','action'=>'indexa'),null, TRUE).'\' ');
       
            //agrego los objetos creados al formulario
        $this->addElements(array($idApoderado,$rutapoderado,$nombresapoderado,$apaternoapoderado,$amaternoapoderado,$direccionapoderado,$regionSelect,$provinciaSelect,$comunaSelect,$telefonoapoderado,$correoapoderado,$relacion,$submit));
        $this->addElements(array($join));
    
        
       
       



}
}