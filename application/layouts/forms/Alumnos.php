<?php
defined('APPLICATION_UPLOADS_DIR')
|| define('APPLICATION_UPLOADS_DIR', realpath(dirname(__FILE__) . '/../documentos/fotografia'));

class Application_Form_Alumnos extends Zend_Form
{

    public function init()
    {
        $this->setName('formElem');
        $this->setAttrib('class', 'vertical');

        $procedencia = new Zend_Form_Element_Text('procedencia');
        $procedencia->setLabel('Procedencia:');

        $cursorepetido = new Zend_Form_Element_Text('cursorepetido');
        $cursorepetido->setLabel('Curso que ha repetido:');

        $edad = new Zend_Form_Element_Text('edad');
        $edad->setLabel('Edad:');

        $hermanos = new Zend_Form_Element_Text('hermanos');
        $hermanos->setLabel('Nº hermanos en el colegio:');

        $vive = new Zend_Form_Element_Text('vive');
        $vive->setLabel('Con quien vive:');

        $alimentacion = new Zend_Form_Element_Select('alimentacion');
        $alimentacion->setLabel('Programa alimentación escolar: ');

        $alimentacion->addMultiOption("1", "Si");
        $alimentacion->addMultiOption("2", "No");

        $etnico = new Zend_Form_Element_Select('etnico');
        $etnico->setLabel('Origen Étnico: ');
        $etnico->addMultiOption("NULL", "Seleccione Previsión");
        $etnico->addMultiOption("1", "Si");
        $etnico->addMultiOption("2", "No");

        $etnicosub = new Zend_Form_Element_Select('etnicosub');
        $etnicosub->setLabel('Descendencia de: ');
        $etnicosub->addMultiOption("1", "Padre");
        $etnicosub->addMultiOption("2", "Madre");
        $etnicosub->addMultiOption("3", "Abuelos");

        $pase = new Zend_Form_Element_Select('pase');
        $pase->setLabel('Pase escolar (TNE): ');
        $pase->addMultiOption("1", "Si");
        $pase->addMultiOption("2", "No");

        $responsable = new Zend_Form_Element_Text('responsable');
        $responsable->setLabel('Responsable Retiro:');

        $noautorizada = new Zend_Form_Element_Text('noautorizada');
        $noautorizada->setLabel('Persona no autorizada a retirar el alumno:');

        $pie = new Zend_Form_Element_Select('pie');
        $pie->setLabel('Pertenece a PIE: ');
        $pie->addMultiOption("1", "Si");
        $pie->addMultiOption("2", "No");

        $fechain = new Zend_Form_Element_Text('fechaincorporacion');
        $fechain->setAttrib('id', 'id3');
        $fechain->setAttrib('readonly', 'true');
        $fechain->setLabel('Fecha Incorporación al curso:');

        $fecharet = new Zend_Form_Element_Text('fecharetiro');
        $fecharet->setAttrib('id', 'id3');
        $fecharet->setAttrib('readonly', 'true');
        $fecharet->setLabel('Fecha Retiro del curso:');

        //creamos select comuna sostenedor
        $comunaSelect = new Zend_form_element_select('comuna');
        $comunaSelect->setLabel('Comuna: ')->setRequired(true);

        $comunamodel = new Application_Model_DbTable_Comuna();
        $rowset      = $comunamodel->listar();
        foreach ($rowset as $row) {
            $comunaSelect->addMultiOption($row->idComuna, $row->nombreComuna);
        }

        //creamos <input text> para escribir Rut alumno
        $id = new Zend_Form_Element_Hidden('idAlumnos');

        //creamos <input text> para escribir Rut alumno
        $rut = new Zend_Form_Element_Text('rutAlumno');
        $rut->setLabel('RUT:')->setRequired(true);

        //creamos select para seleccionar Establecimiento
        $establecimientoalumno = new Zend_Form_Element_Select('idEstablecimiento');
        $establecimientoalumno->setLabel('Seleccione Establecimiento:')->setRequired(true);

        //cargo en un select los Peridos
        $tablaestablecimiento = new Application_Model_DbTable_Establecimiento();
        $rowestablecimientos = $tablaestablecimiento->listar();
        $establecimientoalumno->addMultiOption("Null", "Seleccione Establecimiento");
        foreach ($rowestablecimientos as $e) {

            $establecimientoalumno->addMultiOption($e->idEstablecimiento, $e->nombreEstablecimiento);
        }

        //creamos select para seleccionar Curso
        $curso = new Zend_Form_Element_Select('idCursos');
        $curso->setLabel('Seleccione Curso:');
        $curso->setAttrib('id', 'idcurso')->setRegisterInArrayValidator(false);

        $prioritarioSelect = new Zend_Form_Element_Select('prioritario');
        $prioritarioSelect->setLabel('Alumno Prioritario: ');

        $prioritarioSelect->addMultiOption("1", "Si");
        $prioritarioSelect->addMultiOption("2", "No");

        $beneficioSelect = new Zend_Form_Element_Select('beneficio');
        $beneficioSelect->setLabel('Beneficio Chile Solidario o Puente: ');

        $beneficioSelect->addMultiOption("1", "Si");
        $beneficioSelect->addMultiOption("2", "No");

        $previsionSelect = new Zend_Form_Element_Select('prevision');
        $previsionSelect->setLabel('Previsión de Salud: ')->setRequired(true);

        $previsionSelect->addMultiOption("NULL", "Seleccione Previsión");
        $previsionSelect->addMultiOption("1", "Fonasa");
        $previsionSelect->addMultiOption("2", "Isapre");

        $fonasa = new Zend_Form_Element_Select('letra');
        $fonasa->setLabel('Tramo Fonasa: ');
        $fonasa->addMultiOption("1", "A");
        $fonasa->addMultiOption("2", "B");
        $fonasa->addMultiOption("3", "C");
        $fonasa->addMultiOption("4", "D");

        $actividad = new Zend_Form_Element_Select('actividad');
        $actividad->setLabel('Actividad Física: ');
        $actividad->addMultiOption("1", "Si");
        $actividad->addMultiOption("2", "No");

        //creamos <input text> para escribir nombres
        $nombres = new Zend_Form_Element_Text('nombres');
        $nombres->setLabel('Nombres:')->setRequired(true)->

            addValidator('NotEmpty');

        //creamos <input text> para escribir Apellido paterno
        $apaterno = new Zend_Form_Element_Text('apaterno');
        $apaterno->setLabel('Apellido Paterno:')->setRequired(true);

        //creamos <input text> para escribir Aplliedo Materno
        $amaterno = new Zend_Form_Element_Text('amaterno');
        $amaterno->setLabel('Apellido Materno:')->setRequired(true);

        //creamos <input text> para escribir direccion
        $direccion = new Zend_Form_Element_Text('direccion');
        $direccion->setLabel('Direccion:');

        //creamos <input text> para escribir telefono
        $telefono = new Zend_Form_Element_Text('telefono');
        $telefono->setLabel('Telefono:')->addValidator('digits');

        //creamos <input text> para escribir telefono
        $celular = new Zend_Form_Element_Text('celular');
        $celular->setLabel('Celular:')->addValidator('digits');

        //creamos <input text> para escribir correo
        $correo = new Zend_Form_Element_Text('correo');
        $correo->setLabel('Correo:')->addValidator('EmailAddress', true);

        //creamos <input text> para escribir sexo
        $sexo = new Zend_Form_Element_Select('sexo');
        $sexo->setLabel('Sexo:')->setRequired(true);
        $sexo->addMultiOption("1", "Femenino");
        $sexo->addMultiOption("2", "Masculino");

        //creamos <input text> para escribir fecha nacimiento

        $fecha = new Zend_Form_Element_Text('fechanacimiento');
        $fecha->setAttrib('id', 'id3');
        $fecha->setAttrib('readonly', 'true');
        $fecha->setLabel('Fecha Nacimiento:');

        //select apoderado
        //creamos select para seleccionar Establecimiento
        $selectapoderado = new Zend_Form_Element_Select('idApoderado');
        $selectapoderado->setLabel('Seleccione Apoderado:');

        //cargo en un select los Peridos
        $tablaapoderado = new Application_Model_DbTable_Apoderados();
        //obtengo listado de todos los Peridos y los recorro en un
        //arreglo para agregarlos a la lista
        $rowapoderado = $tablaapoderado->listar();
        foreach ($rowapoderado as $e) {

            $selectapoderado->addMultiOption($e->idApoderado, $e->nombreApoderado . " " . $e->paternoApoderado . " " . $e->maternoApoderado . " | Rut:" . $e->rutApoderado);
        }

        //select apoderado suplente
        //creamos select para seleccionar Establecimiento
        $selectapoderados = new Zend_Form_Element_Select('idApoderadoS');
        $selectapoderados->setLabel('Apoderado Suplente:');

        //cargo en un select los Peridos

        //obtengo listado de todos los Peridos y los recorro en un
        //arreglo para agregarlos a la lista
        $selectapoderados->addMultiOption("Null", "Seleccione opción");
        $rowapoderados = $tablaapoderado->listar();
        foreach ($rowapoderados as $e) {

            $selectapoderados->addMultiOption($e->idApoderado, $e->nombreApoderado . " " . $e->paternoApoderado . " " . $e->maternoApoderado . "  | Rut:" . $e->rutApoderado);
        }

        $nmatricula = new Zend_Form_Element_Text('numeromatricula');

        $nmatricula->setLabel('Número Matricula:');

        //Elementos Datos Clinicos Alumnos

        //creamos <input text> para escribir patologia
        $patologia = new Zend_Form_Element_Text('patologia');
        $patologia->setLabel('Patologia:');

        //creamos <input text> para escribir horario medicacion
        $hora = new Zend_Form_Element_Text('horario');
        $hora->setLabel('Horario Medicacion:');

        //creamos <input text> para escribir Profesional Tratante
        $profesional = new Zend_Form_Element_Text('profesional');
        $profesional->setLabel('Profesional Tratante:');

        //creamos <input text> para escribir telefono
        $telefonom = new Zend_Form_Element_Text('telefonoProfesional');
        $telefonom->setLabel('Telefono:')->addValidator('digits');

        //creamos select ciudad datos generales alumnos
        $documento = new Zend_Form_Element_Select('documento');
        $documento->setLabel('Documento: ');
        $documento->addMultiOption("Null", "Seleccione opción");
        $documento->addMultiOption("1", "Si");
        $documento->addMultiOption("2", "No");

        //Elementos Nucleo Familiar Alumno

        //creamos <input text> para escribir nombre de la madre
        $nombremadre = new Zend_Form_Element_Text('nombremadre');
        $nombremadre->setLabel('Nombre de la Madre:');

        //creamos <input text> para escribir apellidos de la madre
        $apellidosmadre = new Zend_Form_Element_Text('apellidomadre');
        $apellidosmadre->setLabel('Apellidos de la Madre:');

        $ocupacionmadre = new Zend_Form_Element_Text('ocupacionMadre');
        $ocupacionmadre->setLabel('Ocupación de la Madre:');

        $rutmadre = new Zend_Form_Element_Text('rutMadre');
        $rutmadre->setLabel('RUT de la Madre:');

        //creamos <input text> para escribir apellidos de la madre
        $nombrepadre = new Zend_Form_Element_Text('nombrepadre');
        $nombrepadre->setLabel('Nombre del Padre:');

        //creamos <input text> para escribir apellidos del padre
        $apellidospadre = new Zend_Form_Element_Text('apellidopadre');
        $apellidospadre->setLabel('Apellidos del Padre:');

        $ocupacionpadre = new Zend_Form_Element_Text('ocupacionPadre');
        $ocupacionpadre->setLabel('Ocupación del Padre:');

        $rutpadre = new Zend_Form_Element_Text('rutPadre');
        $rutpadre->setLabel('RUT del Padre:');

        //creamos <input text> para escribir telefono de la madre
        $telefonomadre = new Zend_Form_Element_Text('telefonomadre');
        $telefonomadre->setLabel('Telefono de la Madre:');

        //creamos <input text> para escribir telefono del Padre
        $telefonopadre = new Zend_Form_Element_Text('telefonopadre');
        $telefonopadre->setLabel('Telefono del Padre:');

        //creamos <input text> para escribir nivel educacion madre
        $nivelmadre = new Zend_Form_Element_Text('nivelmadre');
        $nivelmadre->setLabel('Escolaridad Madre:');

        //creamos <input text> para escribir nivel educacion Padre
        $nivelpadre = new Zend_Form_Element_Text('nivelpadre');
        $nivelpadre->setLabel('Escolaridad Padre:');

        //Elementos Datos Apoderado Alumno

        //creamos <input text> para escribir Rut alumno

        $rutapoderado = new Zend_Form_Element_Text('idApoderado');
        $rutapoderado->setLabel('RUT:')->setRequired(true);

        //creamos <input text> para escribir nombres Apoderado
        $nombresapoderado = new Zend_Form_Element_Text('nombreApoderado');
        $nombresapoderado->setLabel('Nombre Apoderado:')->setRequired(true)->
            addFilter('StripTags')->addFilter('StringTrim')->
            addValidator('NotEmpty')->addValidator('alpha');

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
        $telefonoapoderado->setLabel('Telefono:')->setRequired(true)->addValidator('digits');

        //creamos <input text> para escribir correo
        $correoapoderado = new Zend_Form_Element_Text('correoApoderado');
        $correoapoderado->setLabel('Correo:')->setRequired(true);

        //boton para enviar formulario
        $submit = new Zend_Form_Element_Submit('submit');

        $submit->removeDecorator('label');
        $submit->setLabel('Guardar');

        $logo = new Zend_Form_Element_File('foto');

        $logo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
        $logo->addValidator('Size', false, '10024000');

        $logo->setLabel('Fotografia Alumno(Formato:jpg,jpeg,png,gif):');

        $logo->setDestination(APPLICATION_UPLOADS_DIR);

        //creamos select para seleccionar establecimiento
        $periodo = new Zend_Form_Element_Select('periodo');
        $periodo->setLabel('Seleccione año :')->setRequired(true);
        //cargo en un select los Peridos
        $table = new Application_Model_DbTable_Periodo();
        //obtengo listado de todos los Peridos y los recorro en un
        //arreglo para agregarlos a la lista

        $periodo->addMultiOptions(array(
            "" => "Seleccione Periodo",
        ));
        foreach ($table->listar() as $c) {

            $periodo->addMultiOption($c->idPeriodo, $c->nombrePeriodo);
        }

        //boton volver
        $join = new Zend_Form_Element_Button('join');
        $join->setLabel('Volver')
            ->setAttrib('onclick', 'window.location =\'' . $this->getView()->url(array('controller' => 'Alumnos', 'action' => 'index'), null, true) . '\' ');
        $join->removeDecorator('label');
        //agrego los objetos creados al formulario
        $this->addElements(array($id, $rut,
            $establecimientoalumno,
            $curso,
            $periodo,
            $logo,
            $nombres,
            $apaterno,
            $amaterno,
            $direccion,
            $telefono,
            $celular,
            $comunaSelect,
            $correo,
            $sexo,
            $fecha,
            $selectapoderado,
            $selectapoderados,
            $beneficioSelect,
            $prioritarioSelect,
            $nmatricula,
            $previsionSelect,
            $fonasa,
            $actividad,
            $patologia,
            $hora,
            $profesional,
            $telefonom,
            $documento,
            $rutpadre,
            $nombrepadre,
            $apellidospadre,
            $telefonopadre,
            $nivelpadre,
            $ocupacionpadre,
            $rutmadre,
            $nombremadre,
            $apellidosmadre,
            $telefonomadre,
            $nivelmadre,
            $ocupacionmadre,
            $submit,
            $join));

        //se crean los grupos en que se mostrara el formulario
        $this->addDisplayGroup(array(
            //agrego los campos que iran dentro del primer fieldset
            'idAlumnos',
            'idEstablecimiento',
            'idCursos',
            'periodo',
            'foto',
            'rutAlumno',
            'nombres',
            'apaterno',
            'amaterno',
            'region',
            'provincia',
            'comuna',
            'direccion',
            'telefono',
            'celular',
            'correo',
            'sexo',
            'fechanacimiento',
            'idApoderado',
            'idApoderadoS',
            'prioritario',
            'beneficio',
            'numeromatricula',

        ), 'general', array('legend' => 'Datos Generales Alumno'));

        $contact = $this->getDisplayGroup('general');
        $contact->setDecorators(array(

            'FormElements',
            //array('HtmlTag',array('tag'=>'p')),
            'Fieldset',

        ));

        //agrego los campos que iran el fieldset de subvencion
        $this->addDisplayGroup(array(
            'prevision',
            'letra',
            'actividad',
            'patologia',
            'horario',
            'profesional',
            'telefonoProfesional',
            'documento',
        ), 'medico', array('legend' => 'Datos Clinicos Alumno'));

        $pass = $this->getDisplayGroup('medico');
        $pass->setDecorators(array(

            'FormElements',
            //array('HtmlTag',array('tag'=>'p')),
            'Fieldset',
            //array('HtmlTag')
        ));

        //agrego los campos que iran en el fieldset de Nucleo Familiar
        $this->addDisplayGroup(array(

            'rutPadre',
            'nombrepadre',
            'apellidopadre',

            'telefonopadre',

            'nivelpadre',
            'ocupacionPadre',
            'rutMadre',
            'nombremadre',
            'apellidomadre',
            'telefonomadre',
            'nivelmadre',
            'ocupacionMadre',

        ), 'nucleo', array('legend' => 'Datos Nucleo Familiar'));

        $web = $this->getDisplayGroup('nucleo');
        $web->setDecorators(array(

            'FormElements',
            //array('HtmlTag',array('tag'=>'p')),
            'Fieldset',
            //array('HtmlTag')
        ));

        //agrego los botones que iran en el fieldset de botones
        $this->addDisplayGroup(array(

            'submit',
            'join',

        ), 'botones', array('legend' => 'Enviar Formulario'));

        $botones = $this->getDisplayGroup('botones');
        $botones->setDecorators(array(

            'FormElements',
            //array('HtmlTag',array('tag'=>'p','class'=>'submit')),

            'Fieldset',
            //array('HtmlTag')
        ));

    }
}
