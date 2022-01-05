<?php
defined('APPLICATION_UPLOADS_DIR')
|| define('APPLICATION_UPLOADS_DIR', realpath(dirname(__FILE__) . '/../documentos/fotografia'));

class Application_Form_Matricula extends Zend_Form
{

    public function init()
    {
        $this->setName('formElem');
        //$this->setAttrib('class', 'vertical');


        $est = new Zend_Session_Namespace('establecimiento');
        $establecimiento = $est->establecimiento;


        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;


        $procedencia = new Zend_Form_Element_Text('procedencia');
        $procedencia->setLabel('Procedencia:');
        $procedencia->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $cursorepetido = new Zend_Form_Element_Text('cursorepetido');
        $cursorepetido->setLabel('Curso que ha repetido:');
        $cursorepetido->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $edad = new Zend_Form_Element_Text('edad');
        $edad->setLabel('Edad:');
        $edad->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $hermanos = new Zend_Form_Element_Text('hermanos');
        $hermanos->setLabel('Nº hermanos en el colegio:');
        $hermanos->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));







        $etnico = new Zend_Form_Element_Select('etnico');
        $etnico->setLabel('Origen Étnico: ');
        $etnico->addMultiOption("NULL", "Previsión");
        $etnico->addMultiOption("1", "Si");
        $etnico->addMultiOption("2", "No");
        $etnico->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $etnicosub = new Zend_Form_Element_Select('etnicosub');
        $etnicosub->setLabel('Descendencia de: ');
        $etnicosub->addMultiOption("1", "Padre");
        $etnicosub->addMultiOption("2", "Madre");
        $etnicosub->addMultiOption("3", "Abuelos");
        $etnicosub->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $pase = new Zend_Form_Element_Select('pase');
        $pase->setLabel('Pase escolar (TNE): ');
        $pase->addMultiOption("1", "Si");
        $pase->addMultiOption("2", "No");
        $pase->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $responsable = new Zend_Form_Element_Text('responsable');
        $responsable->setLabel('Responsable Retiro:');
        $responsable->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $noautorizada = new Zend_Form_Element_Text('noautorizada');
        $noautorizada->setLabel('Persona no autorizada a retirar el alumno:');
        $noautorizada->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));



        $fechain = new Zend_Form_Element_Text('fechaincorporacion');
        $fechain->setAttrib('id', 'id3');
        $fechain->setAttrib('readonly', 'true');
        $fechain->setLabel('Fecha Incorporación al curso:');
        $fechain->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $fecharet = new Zend_Form_Element_Text('fecharetiro');
        $fecharet->setAttrib('id', 'id3');
        $fecharet->setAttrib('readonly', 'true');
        $fecharet->setLabel('Fecha Retiro del curso:');
        $fecharet->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        //creamos select comuna sostenedor
        $comunaSelect = new Zend_form_element_select('comunaActual');
        $comunaSelect->setLabel('Comuna: ')->setRequired(true);
        $comunaSelect->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $comunaSelectapoderado = new Zend_form_element_select('comunaApoderado');
        $comunaSelectapoderado->setLabel('Comuna: ')->setRequired(true);
        $comunaSelectapoderado->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));
        $comunaSelectapoderadosuplente = new Zend_form_element_select('comunaApoderadoSuplente');
        $comunaSelectapoderadosuplente->setLabel('Comuna: ')->setRequired(true);
        $comunaSelectapoderadosuplente->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $comunamodel = new Application_Model_DbTable_Comuna();
        $rowset = $comunamodel->listar();
        foreach ($rowset as $row) {
            $comunaSelect->addMultiOption($row->idComuna, $row->nombreComuna);
            $comunaSelectapoderado->addMultiOption($row->idComuna, $row->nombreComuna);
            $comunaSelectapoderadosuplente->addMultiOption($row->idComuna, $row->nombreComuna);
        }

        //creamos select comuna sostenedor
        $pais = new Zend_form_element_select('paisNacimiento');
        $pais->setLabel('Pais de Origen: ')->setRequired(true);
        $pais->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));
        $modelopais=new Application_Model_DbTable_Resources();
        $rowsetpais = $modelopais->listarpais();
        foreach ($rowsetpais as $row) {
            $pais->addMultiOption($row->RefCountryId, $row->Description);

        }
        $pais->setValue(45);





        //creamos <input text> para escribir Rut alumno
        $id = new Zend_Form_Element_Hidden('idAlumnos');
        $id->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        //creamos <input text> para escribir Rut alumno
        $rut = new Zend_Form_Element_Text('rutAlumno');
        $rut->setLabel('RUT *:')->setRequired(true);
        $rut->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        //creamos select para seleccionar Establecimiento
        $establecimientoalumno = new Zend_Form_Element_Select('idEstablecimiento');
        $establecimientoalumno->setLabel('Establecimiento *:')->setRequired(true);
        $establecimientoalumno->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        if ($rol == 2 || $rol == 3 || $rol == 6) { //Si es un profesor

            $tablaestablecimiento = new Application_Model_DbTable_Establecimiento();
            $rowestablecimientos = $tablaestablecimiento->get($establecimiento);
            $establecimientoalumno->addMultiOption("Null", "Establecimiento");
            foreach ($rowestablecimientos as $e) {

                $establecimientoalumno->addMultiOption($e['idEstablecimiento'], $e['nombreEstablecimiento']);
            }
        } else {
            $tablaestablecimiento = new Application_Model_DbTable_Establecimiento();
            $rowestablecimientos = $tablaestablecimiento->listar();
            $establecimientoalumno->addMultiOption("Null", "Establecimiento");
            foreach ($rowestablecimientos as $e) {

                $establecimientoalumno->addMultiOption($e->idEstablecimiento, $e->nombreEstablecimiento);
            }
        }


        //creamos select para seleccionar Curso
        $curso = new Zend_Form_Element_Select('idCursos');
        $curso->setLabel('Curso *:');
        $curso->setAttrib('id', 'idCursos')->setRegisterInArrayValidator(false);
        $curso->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $prioritario = new Zend_Form_Element_Checkbox('prioritario');
        $prioritario->setLabel('Alumno Prioritario: ');
        $prioritario->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $beneficio= new Zend_Form_Element_Checkbox('beneficio');
        $beneficio->setLabel('Beneficio Chile Solidario o Puente: ');
        $beneficio->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $previsionSelect = new Zend_Form_Element_Select('prevision');
        $previsionSelect->setLabel('Previsión de Salud: ')->setRequired(true);
        $previsionSelect->addMultiOption("NULL", "Previsión");
        $previsionSelect->addMultiOption("1", "Fonasa");
        $previsionSelect->addMultiOption("2", "Isapre");
        $previsionSelect->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $fonasa = new Zend_Form_Element_Select('letra');
        $fonasa->setLabel('Tramo Fonasa: ');
        $fonasa->addMultiOption("1", "A");
        $fonasa->addMultiOption("2", "B");
        $fonasa->addMultiOption("3", "C");
        $fonasa->addMultiOption("4", "D");
        $fonasa->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $actividad = new Zend_Form_Element_Checkbox('actividad');
        $actividad->setLabel('Actividad Física: ');
        $actividad->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $nombres = new Zend_Form_Element_Text('nombres');
        $nombres->setLabel('Nombres *:')->setRequired(true)->addValidator('NotEmpty');
        $nombres->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $apaterno = new Zend_Form_Element_Text('apaterno');
        $apaterno->setLabel('Apellido Paterno *:')->setRequired(true);
        $apaterno->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $amaterno = new Zend_Form_Element_Text('amaterno');
        $amaterno->setLabel('Apellido Materno *:')->setRequired(true);
        $amaterno->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $direccion = new Zend_Form_Element_Text('calle');
        $direccion->setLabel('Calle:');
        $direccion->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $numerocasa = new Zend_Form_Element_Text('numeroCasa');
        $numerocasa->setLabel('Número Casa :')->setRequired(true);
        $numerocasa->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $villa = new Zend_Form_Element_Text('villa');
        $villa->setLabel('Villa/Población :')->setRequired(true);
        $villa->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $ciudad = new Zend_Form_Element_Text('ciudadActual');
        $ciudad->setLabel('Ciudad Residencia :')->setRequired(true);
        $ciudad->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $ciudadorigen = new Zend_Form_Element_Text('ciudadNacimiento');
        $ciudadorigen->setLabel('Ciudad Origen :')->setRequired(true);
        $ciudadorigen->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $telefono = new Zend_Form_Element_Text('telefono');
        $telefono->setLabel('Telefono:')->addValidator('digits');
        $telefono->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        //creamos <input text> para escribir telefono
        $celular = new Zend_Form_Element_Text('celular');
        $celular->setLabel('Celular:')->addValidator('digits');
        $celular->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $correo = new Zend_Form_Element_Text('correo');
        $correo->setLabel('Correo:')->addValidator('EmailAddress', true);
        $correo->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $sexo = new Zend_Form_Element_Select('sexo');
        $sexo->setLabel('Sexo:')->setRequired(true);
        $sexo->addMultiOption("1", "Femenino");
        $sexo->addMultiOption("2", "Masculino");
        $sexo->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        //creamos <input text> para escribir fecha nacimiento

        $fecha = new Zend_Form_Element_Text('fechanacimiento');
        $fecha->setAttrib('id', 'fechanacimiento');
        $fecha->setAttrib('readonly', 'true');
        $fecha->setLabel('Fecha Nacimiento *:');
        $fecha->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $nmatricula = new Zend_Form_Element_Text('numeromatricula');

        $nmatricula->setLabel('Número Matricula:');
        $nmatricula->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $nmatricula = new Zend_Form_Element_Text('numeromatricula');

        $nmatricula->setLabel('Número Matricula:');
        $nmatricula->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $repitencia = new Zend_Form_Element_Text('repitencia');
        $repitencia->setLabel('Ha Repetido  :');
        $repitencia->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $tipoidentificacion = new Zend_Form_Element_Select('tipoIdentificacion');
        $tipoidentificacion->setLabel('Tipo Documento:')->setRequired(true);
        $tipoidentificacion->addMultiOption("1", "RUT");
        $tipoidentificacion->addMultiOption("2", "IPE");
        $tipoidentificacion->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        //Elementos Datos Clinicos Alumnos

        $patologia = new Zend_Form_Element_Text('patologia');
        $patologia->setLabel('Patologia:');
        $patologia->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $hora = new Zend_Form_Element_Text('horario');
        $hora->setLabel('Horario Medicacion:');
        $hora->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $profesional = new Zend_Form_Element_Text('profesional');
        $profesional->setLabel('Profesional Tratante:');
        $profesional->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $telefonom = new Zend_Form_Element_Text('telefonoProfesional');
        $telefonom->setLabel('Telefono:')->addValidator('digits');
        $telefonom->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $documento = new Zend_Form_Element_Select('documento');
        $documento->setLabel('Documento: ');
        $documento->addMultiOption("Null", "Seleccione opción");
        $documento->addMultiOption("1", "Si");
        $documento->addMultiOption("2", "No");
        $documento->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        //Elementos Nucleo Familiar Alumno

        $nombremadre = new Zend_Form_Element_Text('nombremadre');
        $nombremadre->setLabel('Nombre :');
        $nombremadre->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $paternomadre = new Zend_Form_Element_Text('paternomadre');
        $paternomadre->setLabel('Apellido Paterno :');
        $paternomadre->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $maternomadre = new Zend_Form_Element_Text('maternomadre');
        $maternomadre->setLabel('Apellido Materno :');
        $maternomadre->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));



        $ocupacionmadre = new Zend_Form_Element_Text('ocupacionMadre');
        $ocupacionmadre->setLabel('Ocupación :');
        $ocupacionmadre->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));



        $rutmadre = new Zend_Form_Element_Text('rutMadre');
        $rutmadre->setLabel('RUT :');
        $rutmadre->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $nombrepadre = new Zend_Form_Element_Text('nombrepadre');
        $nombrepadre->setLabel('Nombre :');
        $nombrepadre->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $paternopadre = new Zend_Form_Element_Text('paternopadre');
        $paternopadre->setLabel('Apellido Paterno :');
        $paternopadre->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $maternopadre = new Zend_Form_Element_Text('maternopadre');
        $maternopadre->setLabel('Apellido Materno :');
        $maternopadre->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));



        $ocupacionpadre = new Zend_Form_Element_Text('ocupacionPadre');
        $ocupacionpadre->setLabel('Ocupación :');
        $ocupacionpadre->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $rutpadre = new Zend_Form_Element_Text('rutPadre');
        $rutpadre->setLabel('RUT :');
        $rutpadre->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $telefonomadre = new Zend_Form_Element_Text('telefonomadre');
        $telefonomadre->setLabel('Telefono :');
        $telefonomadre->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $telefonopadre = new Zend_Form_Element_Text('telefonopadre');
        $telefonopadre->setLabel('Telefono :');
        $telefonopadre->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $nivelmadre = new Zend_Form_Element_Text('nivelmadre');
        $nivelmadre->setLabel('Escolaridad :');
        $nivelmadre->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $nivelpadre = new Zend_Form_Element_Text('nivelpadre');
        $nivelpadre->setLabel('Escolaridad :');
        $nivelpadre->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        //Elementos Datos Apoderado Alumno

        $rutapoderado = new Zend_Form_Element_Text('rutApoderado');
        $rutapoderado->setLabel('RUT :')->setRequired(true);
        $rutapoderado->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $nombresapoderado = new Zend_Form_Element_Text('nombreApoderado');
        $nombresapoderado->setLabel('Nombre Apoderado :')->setRequired(true)->
        addFilter('StripTags')->addFilter('StringTrim')->
        addValidator('NotEmpty')->addValidator('alpha');
        $nombresapoderado->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $apaternoapoderado = new Zend_Form_Element_Text('paternoApoderado');
        $apaternoapoderado->setLabel('Apellido Paterno :')->setRequired(true);
        $apaternoapoderado->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $amaternoapoderado = new Zend_Form_Element_Text('maternoApoderado');
        $amaternoapoderado->setLabel('Apellido Materno :')->setRequired(true);
        $amaternoapoderado->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $direccionapoderado = new Zend_Form_Element_Text('direccionApoderado');
        $direccionapoderado->setLabel('Dirección:')->setRequired(true);
        $direccionapoderado->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));




        $telefonoapoderado = new Zend_Form_Element_Text('telefonoApoderado');
        $telefonoapoderado->setLabel('Telefono :')->setRequired(true)->addValidator('digits');
        $telefonoapoderado->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $correoapoderado = new Zend_Form_Element_Text('correoApoderado');
        $correoapoderado->setLabel('Correo :')->setRequired(true);
        $correoapoderado->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        //Elementos Datos Apoderado Suplente Alumno


        $rutapoderadosuplente = new Zend_Form_Element_Text('rutApoderadoSuplente');
        $rutapoderadosuplente->setLabel('RUT :')->setRequired(true);
        $rutapoderadosuplente->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $nombresapoderadosuplente = new Zend_Form_Element_Text('nombreApoderadoSuplente');
        $nombresapoderadosuplente->setLabel('Nombre Apoderado :')->setRequired(true)->
        addFilter('StripTags')->addFilter('StringTrim')->
        addValidator('NotEmpty')->addValidator('alpha');
        $nombresapoderadosuplente->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $apaternoapoderadosuplente = new Zend_Form_Element_Text('paternoApoderadoSuplente');
        $apaternoapoderadosuplente->setLabel('Apellido Paterno :')->setRequired(true);
        $apaternoapoderadosuplente->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $amaternoapoderadosuplente = new Zend_Form_Element_Text('maternoApoderadoSuplente');
        $amaternoapoderadosuplente->setLabel('Apellido Materno :')->setRequired(true);
        $amaternoapoderadosuplente->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $direccionapoderadosuplente = new Zend_Form_Element_Text('direccionApoderadoSuplente');
        $direccionapoderadosuplente->setLabel('Direccion :')->setRequired(true);
        $direccionapoderadosuplente->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $telefonoapoderadosuplente = new Zend_Form_Element_Text('telefonoApoderadoSuplente');
        $telefonoapoderadosuplente->setLabel('Telefono :')->setRequired(true)->addValidator('digits');
        $telefonoapoderadosuplente->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $correoapoderadosuplente = new Zend_Form_Element_Text('correoApoderadoSuplente');
        $correoapoderadosuplente->setLabel('Correo :')->setRequired(true);
        $correoapoderadosuplente->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        //boton para enviar formulario
        $submit = new Zend_Form_Element_Submit('submit');
        $submit->removeDecorator('label');
        $submit->setLabel('Guardar');
        $submit->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $logo = new Zend_Form_Element_File('foto');
        $logo->addValidator('Extension', false, 'jpg,png,gif,jpeg');
        $logo->addValidator('Size', false, '10024000');
        $logo->setDestination(APPLICATION_UPLOADS_DIR);
        //$logo->setAttribs('style','color: transparent');
        $logo->setDecorators(array(
            'File',
            'Errors',


        ));


        //boton volver
        $join = new Zend_Form_Element_Button('join');
        $join->setLabel('Volver')
            ->setAttrib('onclick', 'window.location =\'' . $this->getView()->url(array('controller' => 'Alumnos', 'action' => 'index'), null, true) . '\' ');
        $join->removeDecorator('label');
        $join->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        //Emergencia

        $persona = new Zend_Form_Element_Text('personaEmergencia');
        $persona->setLabel('Llamar a:');
        $persona->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $telefonoemergencia = new Zend_Form_Element_Text('telefonoEmergencia');
        $telefonoemergencia->setLabel('Telefono:');
        $telefonoemergencia->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $traslado = new Zend_Form_Element_Text('trasladoEmergencia');
        $traslado->setLabel('Trasladar a:');
        $traslado->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $personaretira = new Zend_Form_Element_Text('personaRetira');
        $personaretira->setLabel('Nombre:');
        $personaretira->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $telefonoretira = new Zend_Form_Element_Text('telefonoRetira');
        $telefonoretira->setLabel('Telefono:');
        $telefonoretira->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $rutretira = new Zend_Form_Element_Text('rutRetira');
        $rutretira->setLabel('Rut::');
        $rutretira->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        //creamos <input text> para escribir fecha inscripcionç
        $date = new DateTime;
        $fechaactual = $date->format('d-m-Y');

        $fechains = new Zend_Form_Element_Text('fechaInscripcion');
        $fechains->setAttrib('readonly', 'true');
        $fechains->setLabel('Fecha Inscripción:');
        $fechains->setValue($fechaactual);
        $fechains->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));



        $estado = new Zend_Form_Element_Select('idEstadoActual');
        $estado->setLabel('Estado:');
        $estado->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));
        $estadomodel = new Application_Model_DbTable_Estado();
        $rowset = $estadomodel->listar();
//        foreach ($rowset as $row) {
//            $estado->addMultiOption($row->idEstado, $row->nombreEstado);
//        }

        $estado->addMultiOption($rowset[0]->idEstado, $rowset[0]->nombreEstado);

        //Campos nuevos 2019

        $nacionalidad = new Zend_Form_Element_Text('nacionalidad');
        $nacionalidad->setLabel('Nacionalidad:');
        $nacionalidad->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $vive = new Zend_Form_Element_Text('vive');
        $vive->setLabel('Vive con:');
        $vive->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $etnia = new Zend_Form_Element_Text('etnia');
        $etnia->setLabel('Etnia:');
        $etnia->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $autorizacion = new Zend_Form_Element_Checkbox('autorizacion');
        $autorizacion->setLabel('Autorización para almorzar afuera:');
        $autorizacion->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        $religion = new Zend_Form_Element_Checkbox('religion');
        $religion->setLabel('Religión:');
        $religion->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $junaeb = new Zend_Form_Element_Checkbox('junaeb');
        $junaeb->setLabel('Junaeb:');
        $junaeb->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));



        $aprendizaje = new Zend_Form_Element_Checkbox('aprendizaje');
        $aprendizaje->setLabel('Problema del Aprendizaje:');

        $aprendizaje->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $pie = new Zend_Form_Element_Checkbox('pie');
        $pie->setLabel('PIE:');
        $pie->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $transporte = new Zend_Form_Element_Select('transporte');
        $tipostrasnsportes = array(1 => 'Escolar',
            2 => 'Rural',
            3 => 'Municipal',
            4 => 'Casa Acogida'
        );
        $transporte->setLabel('Transporte Escolar:')->setRequired(true);
        $transporte->addMultiOptions($tipostrasnsportes);
        $transporte->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        //Documentos entregados por Apoderado
        $nacimiento = new Zend_Form_Element_Checkbox('nacimiento');
        $nacimiento->setLabel('Nacimiento:');
        $nacimiento->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $estudios = new Zend_Form_Element_Checkbox('estudio');
        $estudios->setLabel('Estudios:');
        $estudios->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $personalidad = new Zend_Form_Element_Checkbox('personalidad');
        $personalidad->setLabel('Personalidad:');
        $personalidad->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $tratamiento = new Zend_Form_Element_Select('tratamiento');
        $tipo = array(1 => 'Neurológico',
            2 => 'Psicopedagógico',
            3 => 'Psicológico'

        );
        $tratamiento->setLabel('Ha estado en tratamiento:')->setRequired(true);
        $tratamiento->addMultiOptions($tipo);
        $tratamiento->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));
        $otrotratamiento = new Zend_Form_Element_Text('otroTratamiento');
        $otrotratamiento->setLabel('Otro tratamiento:')->setRequired(true);
        $otrotratamiento->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $entratamiento = new Zend_Form_Element_Checkbox('enTratamiento');
        $entratamiento->setLabel('En Tratamiento:');
        $entratamiento->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));

        $alergia = new Zend_Form_Element_Text('alergia');
        $alergia->setLabel('Alérgico:')->setRequired(true);
        $alergia->setDecorators(array(
            'ViewHelper',
            'Errors',
            'Label'

        ));


        //agrego los objetos creados al formulario
        $this->addElements(array(
            $id,
            $rut,
            $establecimientoalumno,
            $curso,
            $logo,
            $nombres,
            $apaterno,
            $amaterno,
            $ciudadorigen,
            $direccion,
            $numerocasa,
            $villa,
            $ciudad,
            $pais,
            $telefono,
            $celular,
            $comunaSelect,
            $correo,
            $sexo,
            $fecha,
            $beneficio,
            $prioritario,
            $nmatricula,
            $repitencia,
            $tipoidentificacion,
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
            $paternopadre,
            $maternopadre,
            $telefonopadre,
            $nivelpadre,
            $ocupacionpadre,
            $rutmadre,
            $nombremadre,
            $paternomadre,
            $maternomadre,
            $telefonomadre,
            $nivelmadre,
            $ocupacionmadre,
            $rutapoderado,
            $nombresapoderado,
            $apaternoapoderado,
            $amaternoapoderado,
            $direccionapoderado,
            $correoapoderado,
            $comunaSelectapoderado,
            $telefonoapoderado,
            $rutapoderadosuplente,
            $nombresapoderadosuplente,
            $apaternoapoderadosuplente,
            $amaternoapoderadosuplente,
            $direccionapoderadosuplente,
            $correoapoderadosuplente,
            $comunaSelectapoderadosuplente,
            $telefonoapoderadosuplente,
            $persona,
            $telefonoemergencia,
            $traslado,
            $estado,
            $fechains,
            $nacimiento,
            $nacionalidad,
            $vive,
            $etnia,
            $religion,
            $junaeb,
            $autorizacion,
            $aprendizaje,
            $pie,
            $transporte,
            $estudios,
            $personalidad,
            $tratamiento,
            $otrotratamiento,
            $entratamiento,
            $alergia,
            $personaretira,
            $telefonoretira,
            $rutretira,
            $submit,
            $join));


    }
}
