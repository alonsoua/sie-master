<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {

        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();
    }

//    public  function indexAction(){
//
//    }

    //Modificado 19-08-2020 Solo se Comenta el codigo
    public function indexAction()
    {

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        $tablacomunas = new Application_Model_DbTable_Comuna();
        $modeloperiodo = new Application_Model_DbTable_Periodo();
        $modelJson = new Application_Model_DbTable_Json();

        if ($rol == 3) {

            $est = new Zend_Session_Namespace('establecimiento');
            $idestablecimiento = $est->establecimiento;

            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;

            //$idestablecimiento = 12;


            $datosperiodo = $modeloperiodo->nombreperiodo($idperiodo);
            $dias = array(
                '1' => 'Lunes',
                '2' => 'Martes',
                '3' => 'Miércoles',
                '4' => 'Jueves',
                '5' => 'Viernes'
            );


            /* ----------------------------------- _Personas ----------------------------------- */


            // ALUMNOS

            $PersonIdentifierId = 0;
            $PersonAddressId = 0;
            $PersonEmailAddressId = 0;
            $PersonTelephoneId = 0;
            $PersonStatusId = 0;
            $PersonLanguageId = 0;
            $PersonRelationshipId = 0;
            $PersonDegreeOrCertificateId = 0;

            $regiones = array(
                1=>63,
                2=>64,
                3=>65,
                4=>66,
                5=>67,
                6=>68,
                7=>69,
                8=>70,
                9=>71,
                10=>72,
                11=>73,
                12=>74,
                13=>75,
                14=>76,
                15=>77,
                16=>78
            );
            $RefCountryId = 45; // CL

            // 1000 = Alumnos
            // 1001 = Docentes
            // 1002 = Apoderado Principal
            // 1003 = Apoderado Secundario
            $cursos = $modelJson->listarCursosJSON($idperiodo, $idestablecimiento);

            $lista_curso=array();
            foreach ($cursos as $item){
                $lista_curso[]=$item['idCursos'];
            }

            $alumnos = $modelJson->listarAlumnosJSON($idperiodo, $idestablecimiento,$lista_curso);

            $largo = count($alumnos);
            for ($i = 0; $i < $largo; $i++) {

                // AUTOINCREMENTS
                $PersonId = intval('1000' . $alumnos[$i]->idAlumnosActual);
                $PersonIdentifierId++;
                $PersonAddressId++;
                $PersonEmailAddressId++;
                $PersonTelephoneId++;
                $PersonStatusId++;
                $PersonLanguageId++;
                // $PersonRelationshipId++;
                $PersonDegreeOrCertificateId++;

                // Separamos Nombres
                if (strpos($alumnos[$i]->nombres, ' ') !== false) {

                    $nombres = explode(" ", $alumnos[$i]->nombres);
                    $nombre = $nombres[0];

                    if (count($nombres) == 2) {
                        $segundoNombre = $nombres[1];
                    } else if (count($nombres) >= 3) {
                        $segundoNombre = $nombres[1] . ' ' . $nombres[2];
                    } else {
                        $segundoNombre = $nombres[1];
                    }
                } else {
                    $nombre = $alumnos[$i]->nombres;
                    $segundoNombre = ' ';
                }

                // Person
                $FirstName = substr($nombre, 0, 45);
                $MiddleName = substr($segundoNombre, 0, 45);
                $MiddleName = ($MiddleName == false) ? ' ' : $MiddleName;
                $LastName = substr($alumnos[$i]->apaterno, 0, 45);
                $SecondLastName = substr($alumnos[$i]->amaterno, 0, 45);
                $SecondLastName = ($SecondLastName == false) ? ' ' : $SecondLastName;
                $Birthdate = date("Y-m-d", strtotime($alumnos[$i]->fechanacimiento));
                $sexo = $alumnos[$i]->sexo;
                switch ($sexo) {

                    case 0:
                        $sexId = 3; // Code => NotSelected
                        break;

                    case 1:
                        $sexId = 1; // Code => Male
                        break;

                    case 2:
                        $sexId = 2; // Code => Female
                        break;

                    default:
                        $sexId = 3; // Code => NotSelected
                        break;

                }
                $RefSexId = $sexId;
                $RefCountyId = empty($alumnos[$i]->paisNacimiento) ? 45 : (int)$alumnos[$i]->paisNacimiento;

                $_Personas[$i]['Person'][] = array(

                    'PersonId' => $PersonId,
                    'FirstName' => $FirstName,
                    'MiddleName' => $MiddleName,
                    'LastName' => $LastName,
                    'SecondLastName' => $SecondLastName,
                    // 'GenerationCode' => '',
                    // 'Prefix' => '',
                    'Birthdate' => $Birthdate,
                    'RefSexId' => $RefSexId,
                    'HispanicLatinoEthnicity' => false, // ETNIA HISPANA
                    // 'RefUSCitizenshipStatusId' => '',// Referencia si es ciudadano de USA, al ser "Permanent resident"
                    //  es ciudadano permanente de usa?.
                    // 'RefVisaTypeId' => '',// va de la mano con el anterior
                    'RefStateOfResidenceId' => (int)$regiones[$alumnos[$i]->idRegion],// estado donde vive
                    'RefProofOfResidencyTypeId' => 4,// Tipo Residencia Crddito-Arriendo, etc/
                    // 'RefHighestEducationLevelCompletedId' => '',
                    'RefPersonalInformationVerificationId' => 15,// Verificador de información personal(Certificado) 15-Doceumento emitido por el estado
                    // 'BirthdateVerification' => '',// Verificador de cumpleaños
                    // 'RefTribalAffiliationId' => '' // entidad tribal nativa americana

                );


                // PersonIdentifier
                // RUT Alumno
                $tipoIdenticacion = 51;
                $Identifier = substr($alumnos[$i]->rutAlumno, 0, 40);

                if ($alumnos[$i]->tipoIdentificacion == 2) {
                    $Identifier = substr($alumnos[$i]->rutAlumno, 0, 40);
                    $tipoIdenticacion = 52;
                }

                $_Personas[$i]['PersonIdentifier'][] = array(
                    'PersonIdentifierId' => $PersonIdentifierId,
                    'PersonId' => $PersonId,
                    'Identifier' => $Identifier,
                    'RefPersonIdentificationSystemId' => $tipoIdenticacion, // Ref RUN
                    //'RefPersonalInformationVerificationId'  => ''
                );


                // Matricula
                if ($alumnos[$i]->numeroMatricula != 0) {
                    $PersonIdentifierId++;
                    $_Personas[$i]['PersonIdentifier'][] = array(

                        'PersonIdentifierId' => $PersonIdentifierId,
                        'PersonId' => $PersonId,
                        'Identifier' => $alumnos[$i]->numeroMatricula,
                        'RefPersonIdentificationSystemId' => 31, // Ref School (Matricula) tipo alumno
                        //'RefPersonalInformationVerificationId'  => ''
                    );

                }
                // PersonBirthplace
                $_Personas[$i]['PersonBirthplace'][] = array(
                     'PersonId' => $PersonId,
                     'City' => (string)$alumnos[$i]->ciudadActual,
                     'RefStateId' => (int)$regiones[$alumnos[$i]->idRegion],
                     'RefCountryId' => $RefCountyId
                );


                // PersonAddress
                $comuna = $tablacomunas->getcomuna($alumnos[$i]->comunaActual);

                $StreetNumberAndName = substr($alumnos[$i]->direccion, 0, 40);
                $City = substr($comuna[0]->nombreComuna, 0, 30);

                $RefStateId = $regiones[$comuna[0]->idRegion]; //Region
                $AddressCountyName = substr($comuna[0]->nombreComuna, 0, 30);
                //$RefCountyId = (string)$comuna[0]->KeySI;


                $_Personas[$i]['PersonAddress'][] = array(

                    'PersonAddressId' => $PersonAddressId,
                    'PersonId' => $PersonId,
                    'RefPersonLocationTypeId' => 2, // Ref Physical
                    'StreetNumberAndName' => $StreetNumberAndName,
                    // 'ApartmentRoomOrSuiteNumber' => '',
                    'City' => $City,
                    'RefStateId' => $RefStateId,
                    // 'PostalCode' => '',
                    'AddressCountyName' => $AddressCountyName,
                    //'RefCountyId' => $RefCountyId,//idComuna
                    'RefCountryId' => $RefCountryId// idPais
                    // 'Latitude' => '',
                    // 'Longitude' => '',
                    // 'RefPersonalInformationVerificationId' => ''

                );


                // PersonEmailAddress
                $EmailAddress = substr($alumnos[$i]->correo, 0, 128);

                if ($EmailAddress != false) {

                    $_Personas[$i]['PersonEmailAddress'][] = array(

                        'PersonEmailAddressId' => $PersonEmailAddressId,
                        'PersonId' => $PersonId,
                        'EmailAddress' => $EmailAddress,
                        'RefEmailTypeId' => 1 // Ref Home

                    );

                } else {

                    $_Personas[$i]['PersonEmailAddress'] = array();

                }


                // PersonTelephone

                $tipo_telefono=3;
                if($alumnos[$i]->celular>0 && strlen((string)$alumnos[$i]->celular)==9){
                    $alumnos[$i]->celular="+56".$alumnos[$i]->celular;
                }elseif ($alumnos[$i]->celular>0 && strlen((string)$alumnos[$i]->celular)==8){
                    $alumnos[$i]->celular="+569".$alumnos[$i]->celular;
                }else{
                    $alumnos[$i]->celular="";
                    $tipo_telefono="";
                }


                $TelephoneNumber = $alumnos[$i]->celular;


                $_Personas[$i]['PersonTelephone'][] = array(

                    'PersonTelephoneId' => $PersonTelephoneId,
                    'PersonId' => $PersonId,
                    'TelephoneNumber' => (string)$TelephoneNumber,
                    'PrimaryTelephoneNumberIndicator' => (Bool)True,
                    'RefPersonTelephoneNumberTypeId' => $tipo_telefono // Ref Mobile

                );


                // PersonStatus
                $StatusValue = ($alumnos[$i]->idEstadoActual == 1) ? true : false;

                $_Personas[$i]['PersonStatus'][] = array(

                    'PersonStatusId' => $PersonStatusId,
                    'PersonId' => $PersonId,
                    'RefPersonStatusTypeId' => 7, // SchoolChoiceEligibleforTransfer
                    'StatusValue' => $StatusValue
                    // 'StatusStartDate' => '',
                    // 'StatusEndDate' => ''

                );


                $_Personas[$i]['PersonLanguage'][] = array(

                    'PersonLanguageId' => $PersonLanguageId,
                    'PersonId' => $PersonId,
                    'RefLanguageId' => 400, //Espanol
                    'RefLanguageUseTypeId' => 1 //4=Lenguaje correspondiente

                );


                $_Personas[$i]['PersonDisability'] = array(

                    // 'PersonId' => $PersonId,
                    // 'PrimaryDisabilityTypeId' => '',
                    // 'DisabilityStatus' => 'No',
                    // 'RefAccommodationsNeededTypeId' => '',
                    // 'RefDisabilityConditionTypeId' => '',
                    // 'RefDisabilityDeterminationSourceTypeId' => '',
                    // 'RefDisabilityConditionStatusCodeId' => '',
                    // 'RefIDEADisabilityTypeId' => '',
                    // 'SignificantCognitiveDisabilityIndicator' => 'No'

                );


                $_Personas[$i]['PersonRelationship'] = array(

                    // 'PersonRelationshipId' => $PersonRelationshipId,
                    // 'PersonId' => $PersonId,
                    // 'RelatedPersonId' => '',
                    // 'RefPersonRelationshipId' => '',
                    // 'CustodialRelationshipIndicator' => '',
                    // 'EmergencyContactInd' => '',
                    // 'ContactPriorityNumber' => '',
                    // 'ContactRestrictions' => '',
                    // 'LivesWithIndicator' => '',
                    // 'PrimaryContactIndicator' => ''

                );


                $_Personas[$i]['PersonDegreeOrCertificate'] = array(

                    // 'PersonDegreeOrCertificateId' => $PersonDegreeOrCertificateId,
                    // 'PersonId' => $PersonId,
                    // 'DegreeOrCertificateTitleOrSubject' => '',
                    // 'RefDegreeOrCertificateTypeId' => '',
                    // 'AwardDate' => '',
                    // 'NameOfInstitution' => '',
                    // 'RefHigherEducationInstitutionAccreditationStatusId' => '',
                    // 'RefEducationVerificationMethodId' => ''

                );

                $alumnosRel[$alumnos[$i]->idAlumnos] = [
                    'PersonId' => $PersonId,
                    'Item' => $i
                ];

            }

            // DOCENTES
            $docentes = $modelJson->listarDocentesJSON($idperiodo, $idestablecimiento);

            $largo = count($docentes) + $i;
            for ($d = $i; $d < $largo; $d++) {

                $do = $d - $i;
                $PersonId = intval('1001' . $docentes[$do]->idCuenta);
                $PersonIdentifierId++;
                $PersonAddressId++;
                $PersonEmailAddressId++;
                $PersonTelephoneId++;
                $PersonStatusId++;
                $PersonLanguageId++;
                // $PersonRelationshipId++;
                $PersonDegreeOrCertificateId++;

                // Separamos Nombres
                $nombres = '';

                if (strpos($docentes[$do]->nombrescuenta, ' ') !== false) {

                    $nombres = explode(" ", $docentes[$do]->nombrescuenta);
                    $nombre = $nombres[0];

                    if (count($nombres) == 2) {
                        $segundoNombre = $nombres[1];
                    } else if (count($nombres) >= 3) {
                        $segundoNombre = $nombres[1] . ' ' . $nombres[2];
                    } else {
                        $segundoNombre = $nombres[1];
                    }
                } else {
                    $nombre = $docentes[$do]->nombrescuenta;
                    $segundoNombre = ' ';
                }

                // Person
                $FirstName = substr($nombre, 0, 45);
                $MiddleName = substr($segundoNombre, 0, 45);
                $MiddleName = ($MiddleName == false) ? ' ' : $MiddleName;
                $LastName = substr($docentes[$do]->paternocuenta, 0, 45);
                $SecondLastName = substr($docentes[$do]->maternocuenta, 0, 45);
                $SecondLastName = ($SecondLastName == false) ? ' ' : $SecondLastName;

                $_Personas[$d]['Person'][] = array(

                    'PersonId' => $PersonId,
                    'FirstName' => $FirstName,
                    'MiddleName' => $MiddleName,
                    'LastName' => $LastName,
                    'SecondLastName' => $SecondLastName,
                    // 'GenerationCode' => '',
                    // 'Prefix' => '',
//                'Birthdate'  => '0000-00-00',
                    'RefSexId' => 3, // Ref NotSelected
                    'HispanicLatinoEthnicity' => false,// ETNIA HISPANA
                    // 'RefUSCitizenshipStatusId' => '',// Referencia si es ciudadano de USA, al ser "Permanent resident"
                    // // es ciudadano permanente de usa?.

                    // 'RefVisaTypeId' => '',// va de la mano con el anterior
                    // 'RefStateOfResidenceId' => '',// estado donde vive
                    // 'RefProofOfResidencyTypeId' => '',// prueba de residencia
                    // 'RefHighestEducationLevelCompletedId' => '',
                    // 'RefPersonalInformationVerificationId' => '',// Verificador de información personal
                    // 'BirthdateVerification' => '',// Verificador de cumpleaños
                    // 'RefTribalAffiliationId' => '' // entidad tribal nativa americana

                );


                // PersonIdentifier
                // RUT Docente
                $Identifier = substr($docentes[$do]->usuario, 0, 40);

                $_Personas[$d]['PersonIdentifier'][] = array(

                    'PersonIdentifierId' => $PersonIdentifierId,
                    'PersonId' => $PersonId,
                    'Identifier' => $Identifier,
                    'RefPersonIdentificationSystemId' => 51, // Ref RUN
                    // 'RefPersonalInformationVerificationId'  => ''

                );


                $_Personas[$d]['PersonBirthplace'] = array(

                    // 'PersonId' => $PersonId
                    // 'City' => '',
                    // 'RefStateId' => '',
                    // 'RefCountryId' => ''

                );


                // PersonAddress
                //$comuna = $tablacomunas->getcomuna($docentes[$do]->comuna);
                $comuna = $tablacomunas->getcomuna(4103);

                $City = substr($comuna[0]->nombreComuna, 0, 30);
                $RefStateId = $regiones[$comuna[0]->idRegion]; //Region

                $AddressCountyName = substr($comuna[0]->nombreComuna, 0, 30);
                //$RefCountyId = (string)$comuna[0]->KeySI;
                //$RefCountyId = null;

                $_Personas[$d]['PersonAddress'][] = array(

                    'PersonAddressId' => $PersonAddressId,
                    'PersonId' => $PersonId,
                    'RefPersonLocationTypeId' => 2, // Ref Physical
                    'StreetNumberAndName' => '0',
                    // 'ApartmentRoomOrSuiteNumber' => '',
                    'City' => $City,
                    'RefStateId' => $RefStateId,
                    // 'PostalCode' => '',
                    'AddressCountyName' => $AddressCountyName,
                    //'RefCountyId' => $RefCountyId, //idComuna
                    'RefCountryId' => $RefCountryId //idPais
                    // 'Latitude' => '',
                    // 'Longitude' => '',
                    // 'RefPersonalInformationVerificationId' => ''

                );

                // PersonEmailAddress
                $EmailAddress = substr($docentes[$do]->correo, 0, 128);

                if ($EmailAddress != false) {

                    $_Personas[$d]['PersonEmailAddress'][] = array(

                        'PersonEmailAddressId' => $PersonEmailAddressId,
                        'PersonId' => $PersonId,
                        'EmailAddress' => $EmailAddress,
                        'RefEmailTypeId' => 1 // Ref Home

                    );

                } else {

                    $_Personas[$d]['PersonEmailAddress'] = array();

                }


                // PersonTelephone  //
                $_Personas[$d]['PersonTelephone'] = array(

                    // 'PersonTelephoneId' => $PersonTelephoneId,
                    // 'PersonId' => $PersonId
                    // 'TelephoneNumber'                       => '',
                    // 'PrimaryTelephoneNumberIndicator'       => '',
                    // 'RefPersonTelephoneNumberTypeId'        => ''

                );


                // PersonStatus
//            $StatusValue = ($docentes[$do]->estadoCuenta == 1) ? true : false;

                $_Personas[$d]['PersonStatus'] = array(

//                'PersonStatusId' => $PersonStatusId,
//                'PersonId'       => $PersonId,
//                'RefPersonStatusTypeId' => Null, // ???
//                'StatusValue'    => $StatusValue
                    // 'StatusStartDate' => '',
                    // 'StatusEndDate' => ''

                );

                // PersonLanguage
                $_Personas[$d]['PersonLanguage'] = array(

                    // 'PersonLanguageId' => $PersonLanguageId,
                    // 'PersonId' => $PersonId
                    // 'RefLanguageId' => '',
                    // 'RefLanguageUseTypeId' => ''

                );


                $_Personas[$d]['PersonDisability'] = array(

                    // 'PersonId' => $PersonId
                    // 'PrimaryDisabilityTypeId' => '',
                    // 'DisabilityStatus' => '',
                    // 'RefAccommodationsNeededTypeId' => '',
                    // 'RefDisabilityConditionTypeId' => '',
                    // 'RefDisabilityDeterminationSourceTypeId' => '',
                    // 'RefDisabilityConditionStatusCodeId' => '',
                    // 'RefIDEADisabilityTypeId' => '',
                    // 'SignificantCognitiveDisabilityIndicator' => ''

                );


                $_Personas[$d]['PersonRelationship'] = array(

                    // 'PersonRelationshipId' => $PersonRelationshipId,
                    // 'PersonId' => $PersonId
                    // 'RelatedPersonId' => '',
                    // 'RefPersonRelationshipId' => '',
                    // 'CustodialRelationshipIndicator' => '',
                    // 'EmergencyContactInd' => '',
                    // 'ContactPriorityNumber' => '',
                    // 'ContactRestrictions' => '',
                    // 'LivesWithIndicator' => '',
                    // 'PrimaryContactIndicator' => ''

                );


                $_Personas[$d]['PersonDegreeOrCertificate'] = array(

                    // 'PersonDegreeOrCertificateId' => $PersonDegreeOrCertificateId,
                    // 'PersonId' => $PersonId
                    // 'DegreeOrCertificateTitleOrSubject' => '',
                    // 'RefDegreeOrCertificateTypeId' => '',
                    // 'AwardDate' => '',
                    // 'NameOfInstitution' => '',
                    // 'RefHigherEducationInstitutionAccreditationStatusId' => '',
                    // 'RefEducationVerificationMethodId' => ''

                );

                $docentesRel[$docentes[$do]['idCuenta']] = [
                    'PersonId' => $PersonId,
                    'Item' => $do
                ];
            }


            // APODERADO PRINCIPAL
            $apoderadosP = $modelJson->listarApoderadosPrincipalJSON($idperiodo, $idestablecimiento);

            $largo = count($apoderadosP) + $d;
            for ($ap = $d; $ap < $largo; $ap++) {

                $apri = $ap - $d;
                $PersonId = intval('1002' . $apoderadosP[$apri]->idApoderado);
                $PersonIdentifierId++;
                $PersonAddressId++;
                $PersonEmailAddressId++;
                $PersonTelephoneId++;
                $PersonStatusId++;
                $PersonLanguageId++;
                $PersonRelationshipId++;
                $PersonDegreeOrCertificateId++;


                // Separamos Nombres
                $nombres = '';
                if (strpos($apoderadosP[$apri]->nombreApoderado, ' ') !== false) {

                    $nombres = explode(" ", $apoderadosP[$apri]->nombreApoderado);
                    $nombre = $nombres[0];
                    if (count($nombres) == 2) {
                        $segundoNombre = $nombres[1];
                    } else if (count($nombres) >= 3) {
                        $segundoNombre = $nombres[1] . ' ' . $nombres[2];
                    } else {
                        $segundoNombre = $nombres[1];
                    }
                } else {
                    $nombre = $apoderadosP[$apri]->nombreApoderado;
                    $segundoNombre = ' ';
                }

                // Person
                $FirstName = substr($nombre, 0, 45);
                $MiddleName = substr($segundoNombre, 0, 45);
                $MiddleName = ($MiddleName == false) ? ' ' : $MiddleName;
                $LastName = substr($apoderadosP[$apri]->paternoApoderado, 0, 45);
                $SecondLastName = substr($apoderadosP[$apri]->maternoApoderado, 0, 45);
                $SecondLastName = ($SecondLastName == false) ? ' ' : $SecondLastName;

                $sexoAlumno = $apoderadosP[$apri]->sexo; // 0 No Ingresado - 1 Niña - 2 Niño
                switch ($sexoAlumno) {
                    case '0':
                        $RefPersonRelationshipId = 27; // Ref Unknown
                        break;

                    case '1':
                        $RefPersonRelationshipId = 5;  // Ref Daughter
                        break;

                    case '2':
                        $RefPersonRelationshipId = 26; // Ref Son
                        break;

                    default:
                        $RefPersonRelationshipId = 27; // Ref Unknown
                        break;
                }


                $_Personas[$ap]['Person'][] = array(

                    'PersonId' => $PersonId,
                    'FirstName' => $FirstName,
                    'MiddleName' => $MiddleName,
                    'LastName' => $LastName,
                    'SecondLastName' => $SecondLastName,
                    // 'GenerationCode' => '',
                    // 'Prefix' => '',
//                'Birthdate'  => '0000-00-00',
                    'RefSexId' => 3, // Ref NotSelected
                    'HispanicLatinoEthnicity' => false,// ETNIA HISPANA
                    // 'RefUSCitizenshipStatusId' => '',// Referencia si es ciudadano de USA, al ser "Permanent resident"
                    // // es ciudadano permanente de usa?.

                    // 'RefVisaTypeId' => '',// va de la mano con el anterior
                    // 'RefStateOfResidenceId' => '',// estado donde vive
                    // 'RefProofOfResidencyTypeId' => '',// prueba de residencia
                    // 'RefHighestEducationLevelCompletedId' => '',
                    // 'RefPersonalInformationVerificationId' => '',// Verificador de información personal
                    // 'BirthdateVerification' => '',// Verificador de cumpleaños
                    // 'RefTribalAffiliationId' => '' // entidad tribal nativa americana

                );


                // PersonIdentifier
                $Identifier = substr($apoderadosP[$apri]->rutApoderado, 0, 40);

                $_Personas[$ap]['PersonIdentifier'][] = array(

                    'PersonIdentifierId' => $PersonIdentifierId,
                    'PersonId' => $PersonId,
                    'Identifier' => $Identifier,
                    'RefPersonIdentificationSystemId' => 51, // Ref RUN
                    // 'RefPersonalInformationVerificationId' => ''

                );


                $_Personas[$ap]['PersonBirthplace'] = array(

                    // 'PersonId' => $PersonId
                    // 'City' => '',
                    // 'RefStateId' => '',
                    // 'RefCountryId' => ''

                );


                // PersonAddress
                $comuna = $tablacomunas->getcomuna($apoderadosP[$apri]->comunaApoderado);

                $StreetNumberAndName = substr($apoderadosP[$apri]->direccionApoderado, 0, 40);
                $City = substr($comuna[0]->nombreComuna, 0, 30);

                $RefStateId = $regiones[$comuna[0]->idRegion]; //Region

                $AddressCountyName = substr($comuna[0]->nombreComuna, 0, 30);
                $RefCountyId = (string)$comuna[0]->KeySI;

                $_Personas[$ap]['PersonAddress'][] = array(

                    'PersonAddressId' => $PersonAddressId,
                    'PersonId' => $PersonId,
                    'RefPersonLocationTypeId' => 2, // Ref Physical
                    'StreetNumberAndName' => $StreetNumberAndName,
                    // 'ApartmentRoomOrSuiteNumber' => '',
                    'City' => $City,
                    'RefStateId' => $RefStateId,
                    // 'PostalCode' => '',
                    'AddressCountyName' => $AddressCountyName,
                    'RefCountyId' => $RefCountyId,
                    'RefCountryId' => $RefCountryId
                    // 'Latitude' => '',
                    // 'Longitude' => '',
                    // 'RefPersonalInformationVerificationId' => ''

                );


                // PersonEmailAddress
                $EmailAddress = substr($apoderadosP[$apri]->correoApoderado, 0, 128);

                if ($EmailAddress != false) {

                    $_Personas[$ap]['PersonEmailAddress'][] = array(

                        'PersonEmailAddressId' => $PersonEmailAddressId,
                        'PersonId' => $PersonId,
                        'EmailAddress' => $EmailAddress,
                        'RefEmailTypeId' => 1 // Ref Home

                    );

                } else {

                    $_Personas[$ap]['PersonEmailAddress'] = array();

                }


                $tipo_telefono=3;
                if($apoderadosP[$apri]->telefonoApoderado>0 && strlen((string)$apoderadosP[$apri]->telefonoApoderado)==9){
                    $apoderadosP[$apri]->telefonoApoderado="+56".$apoderadosP[$apri]->telefonoApoderado;
                }elseif ($apoderadosP[$apri]->telefonoApoderado>0 && strlen((string)$apoderadosP[$apri]->telefonoApoderado)==8){
                    $apoderadosP[$apri]->telefonoApoderado="+569".$apoderadosP[$apri]->telefonoApoderado;
                }else{
                    $apoderadosP[$apri]->telefonoApoderado="";
                    $tipo_telefono="";
                }
                //PersonTelephone
                $TelephoneNumber = $apoderadosP[$apri]->telefonoApoderado;

                $_Personas[$ap]['PersonTelephone'][] = array(

                    'PersonTelephoneId' => $PersonTelephoneId,
                    'PersonId' => $PersonId,
                    'TelephoneNumber' => (string)$TelephoneNumber,
                    'PrimaryTelephoneNumberIndicator' => (Bool)true,
                    'RefPersonTelephoneNumberTypeId' => $tipo_telefono // Ref Mobile

                );


                $_Personas[$ap]['PersonStatus'] = array(

                    // 'PersonStatusId' => $PersonStatusId,
                    // 'PersonId' => $PersonId
                    // 'RefPersonStatusTypeId' => '',
                    // 'StatusValue' => '',
                    // 'StatusStartDate' => '',
                    // 'StatusEndDate' => ''
                );


                $_Personas[$ap]['PersonLanguage'] = array(

                    // 'PersonLanguageId' => $PersonLanguageId,
                    // 'PersonId' => $PersonId
                    // 'RefLanguageId' => '',
                    // 'RefLanguageUseTypeId' => ''

                );


                $_Personas[$ap]['PersonDisability'] = array(

                    // 'PersonId' => $PersonId
                    // 'PrimaryDisabilityTypeId' => '',
                    // 'DisabilityStatus' => '',
                    // 'RefAccommodationsNeededTypeId' => '',
                    // 'RefDisabilityConditionTypeId' => '',
                    // 'RefDisabilityDeterminationSourceTypeId' => '',
                    // 'RefDisabilityConditionStatusCodeId' => '',
                    // 'RefIDEADisabilityTypeId' => '',
                    // 'SignificantCognitiveDisabilityIndicator' => ''

                );


                // PersonRelationship

                // FALTAN
                // CustodialRelationshipIndicator
                // ContactPriorityNumber
                // LivesWithIndicator
                // PrimaryContactIndicator

                $RelatedPersonId = (integer)$alumnosRel[$apoderadosP[$apri]->idAlumnos]['PersonId'];

                $_Personas[$ap]['PersonRelationship'][] = array(

                    'PersonRelationshipId' => $PersonRelationshipId,
                    'PersonId' => $PersonId,
                    'RelatedPersonId' => (Int)$RelatedPersonId,
                    'RefPersonRelationshipId' => $RefPersonRelationshipId,
                    'CustodialRelationshipIndicator' => true,
                    'EmergencyContactInd' => false,
                    'ContactPriorityNumber' => 1,
                    // 'ContactRestrictions' => '',
                    'LivesWithIndicator' => true,
                    'PrimaryContactIndicator' => false

                );


                $_Personas[$ap]['PersonDegreeOrCertificate'] = array(

                    // 'PersonDegreeOrCertificateId' => $PersonDegreeOrCertificateId,
                    // 'PersonId' => $PersonId
                    // 'DegreeOrCertificateTitleOrSubject' => '',
                    // 'RefDegreeOrCertificateTypeId' => '',
                    // 'AwardDate' => '',
                    // 'NameOfInstitution' => '',
                    // 'RefHigherEducationInstitutionAccreditationStatusId' => '',
                    // 'RefEducationVerificationMethodId' => ''

                );

                $apoderadosRel['P' . $apoderadosP[$apri]->idApoderado] = [
                    'PersonId' => $PersonId,
                    'Item' => $apri
                ];

            }


            // APODERADOS SECUNDARIOS
            $apoderadosSec = $modelJson->listarApoderadosSecundarioJSON($idperiodo, $idestablecimiento);

            $largo = count($apoderadosSec) + $ap;
            for ($as = $ap; $as < $largo; $as++) {

                $asec = $as - $ap;
                $PersonId = intval('1003' . $apoderadosSec[$asec]->idApoderado);
                $PersonIdentifierId++;
                $PersonAddressId++;
                $PersonEmailAddressId++;
                $PersonTelephoneId++;
                $PersonStatusId++;
                $PersonLanguageId++;
                $PersonRelationshipId++;
                $PersonDegreeOrCertificateId++;


                // Separamos Nombres
                $nombres = '';
                if (strpos($apoderadosSec[$asec]->nombreApoderado, ' ') !== false) {

                    $nombres = explode(" ", $apoderadosSec[$asec]->nombreApoderado);
                    $nombre = $nombres[0];
                    if (count($nombres) == 2) {
                        $segundoNombre = $nombres[1];
                    } else if (count($nombres) >= 3) {
                        $segundoNombre = $nombres[1] . ' ' . $nombres[2];
                    } else {
                        $segundoNombre = $nombres[1];
                    }
                } else {
                    $nombre = $apoderadosSec[$asec]->nombreApoderado;
                    $segundoNombre = '';
                }

                // Person
                $FirstName = substr($nombre, 0, 45);
                $MiddleName = substr($segundoNombre, 0, 45);
                $MiddleName = ($MiddleName == false) ? ' ' : $MiddleName;

                $LastName = substr($apoderadosSec[$asec]->paternoApoderado, 0, 45);
                $SecondLastName = substr($apoderadosSec[$asec]->maternoApoderado, 0, 45);
                $SecondLastName = ($SecondLastName == false) ? ' ' : $SecondLastName;

                $sexoAlumno = $apoderadosSec[$asec]->sexo; // 0 No Ingresado - 1 Niña - 2 Niño

                switch ($sexoAlumno) {
                    case '0':
                        $RefPersonRelationshipId = 27; // Ref Unknown
                        break;

                    case '1':
                        $RefPersonRelationshipId = 5;  // Ref Daughter
                        break;

                    case '2':
                        $RefPersonRelationshipId = 26; // Ref Son
                        break;

                    default:
                        $RefPersonRelationshipId = 27; // Ref Unknown
                        break;
                }


                $_Personas[$as]['Person'][] = array(

                    'PersonId' => $PersonId,
                    'FirstName' => $FirstName,
                    'MiddleName' => $MiddleName,
                    'LastName' => $LastName,
                    'SecondLastName' => $SecondLastName,
                    // 'GenerationCode' => '',
                    // 'Prefix' => '',
//                'Birthdate'  => '0000-00-00',
                    'RefSexId' => 3, // Code => NotSelected
                    'HispanicLatinoEthnicity' => false,// ETNIA HISPANA
                    // 'RefUSCitizenshipStatusId' => '',// Referencia si es ciudadano de USA, al ser "Permanent resident"
                    // // es ciudadano permanente de usa?.

                    // 'RefVisaTypeId' => '',// va de la mano con el anterior
                    // 'RefStateOfResidenceId' => '',// estado donde vive
                    // 'RefProofOfResidencyTypeId' => '',// prueba de residencia
                    // 'RefHighestEducationLevelCompletedId' => '',
                    // 'RefPersonalInformationVerificationId' => '',// Verificador de información personal
                    // 'BirthdateVerification' => '',// Verificador de cumpleaños
                    // 'RefTribalAffiliationId' => '' // entidad tribal nativa americana

                );


                // PersonIdentifier
                $Identifier = substr($apoderadosSec[$asec]->rutApoderado, 0, 40);

                $_Personas[$as]['PersonIdentifier'][] = array(

                    'PersonIdentifierId' => $PersonIdentifierId,
                    'PersonId' => $PersonId,
                    'Identifier' => $Identifier,
                    'RefPersonIdentificationSystemId' => 51, // RUN
                    // 'RefPersonalInformationVerificationId' => ''

                );

                // PersonBirthplace
                $_Personas[$as]['PersonBirthplace'] = array(

                    // 'PersonId' => $PersonId
                    // 'City' => '',
                    // 'RefStateId' => '',
                    // 'RefCountryId' => ''

                );


                // PersonAddress
                $comuna = $tablacomunas->getcomuna($apoderadosSec[$asec]->comunaApoderado);

                $StreetNumberAndName = substr($apoderadosSec[$asec]->direccionApoderado, 0, 40);
                $City = substr($comuna[0]->nombreComuna, 0, 30);


                $StateName = $comuna[0]->nombreRegion;

                $AddressCountyName = substr($comuna[0]->nombreComuna, 0, 30);
                $RefCountyId = (string)$comuna[0]->KeySI;

                $_Personas[$as]['PersonAddress'][] = array(

                    'PersonAddressId' => $PersonAddressId,
                    'PersonId' => $PersonId,
                    'RefPersonLocationTypeId' => 2, // Ref Physical
                    'StreetNumberAndName' => $StreetNumberAndName,
                    // 'ApartmentRoomOrSuiteNumber' => '',
                    'City' => $City,
                    'RefStateId' => $RefStateId,
                    // 'PostalCode' => '',
                    'AddressCountyName' => $AddressCountyName,
                    'RefCountyId' => $RefCountyId,
                    'RefCountryId' => $RefCountryId
                    // 'Latitude' => '',
                    // 'Longitude' => '',
                    // 'RefPersonalInformationVerificationId' => ''

                );


                // PersonEmailAddress
                $EmailAddress = substr($apoderadosSec[$asec]->correoApoderado, 0, 128);

                if ($EmailAddress != false) {

                    $_Personas[$as]['PersonEmailAddress'][] = array(

                        'PersonEmailAddressId' => $PersonEmailAddressId,
                        'PersonId' => $PersonId,
                        'EmailAddress' => $EmailAddress,
                        'RefEmailTypeId' => 1 // Ref Home

                    );

                } else {

                    $_Personas[$as]['PersonEmailAddress'] = array();

                }


                //PersonTelephone
                $tipo_telefono=3;
                if($apoderadosSec[$asec]->telefonoApoderado>0 && strlen((string)$apoderadosSec[$asec]->telefonoApoderado)==9){
                    $apoderadosSec[$asec]->telefonoApoderado="+56".$apoderadosSec[$asec]->telefonoApoderado;
                }elseif ($apoderadosSec[$asec]->telefonoApoderado>0 && strlen((string)$apoderadosSec[$asec]->telefonoApoderado)==8){
                    $apoderadosSec[$asec]->telefonoApoderado="+569".$apoderadosSec[$asec]->telefonoApoderado;
                }else{
                    $apoderadosSec[$asec]->telefonoApoderado="";
                    $tipo_telefono="";
                }
                $TelephoneNumber = $apoderadosSec[$asec]->telefonoApoderado;

                $_Personas[$as]['PersonTelephone'][] = array(

                    'PersonTelephoneId' => $PersonTelephoneId,
                    'PersonId' => $PersonId,
                    'TelephoneNumber' => (string)$TelephoneNumber,
                    'PrimaryTelephoneNumberIndicator' => (Bool)True,
                    'RefPersonTelephoneNumberTypeId' => $tipo_telefono // Ref Mobile

                );


                // PersonStatus
                $_Personas[$as]['PersonStatus'] = array(

                    // 'PersonStatusId' => $PersonStatusId,
                    // 'PersonId' => $PersonId
                    // 'RefPersonStatusTypeId' => '',
                    // 'StatusValue' => '',
                    // 'StatusStartDate' => '',
                    // 'StatusEndDate' => ''

                );


                // PersonLanguage
                $_Personas[$as]['PersonLanguage'] = array(

                    // 'PersonLanguageId' => $PersonLanguageId,
                    // 'PersonId' => $PersonId
                    // 'RefLanguageId' => '',
                    // 'RefLanguageUseTypeId' => ''

                );


                // PersonDisability
                $_Personas[$as]['PersonDisability'] = array(

                    // 'PersonId' => $PersonId
                    // 'PrimaryDisabilityTypeId' => '',
                    // 'DisabilityStatus' => '',
                    // 'RefAccommodationsNeededTypeId' => '',
                    // 'RefDisabilityConditionTypeId' => '',
                    // 'RefDisabilityDeterminationSourceTypeId' => '',
                    // 'RefDisabilityConditionStatusCodeId' => '',
                    // 'RefIDEADisabilityTypeId' => '',
                    // 'SignificantCognitiveDisabilityIndicator' => ''

                );


                // PersonRelationship

                // FALTAN
                // CustodialRelationshipIndicator
                // ContactPriorityNumber
                // LivesWithIndicator
                // PrimaryContactIndicator
                $RelatedPersonId = (integer)$alumnosRel[$apoderadosSec[$asec]->idAlumnos]['PersonId'];

                $_Personas[$as]['PersonRelationship'][] = array(

                    'PersonRelationshipId' => $PersonRelationshipId,
                    'PersonId' => $PersonId,
                    'RelatedPersonId' => (Int)$RelatedPersonId,
                    'RefPersonRelationshipId' => $RefPersonRelationshipId,
                    'CustodialRelationshipIndicator' => true,
                    'EmergencyContactInd' => false,
                    'ContactPriorityNumber' => 1,
                    // 'ContactRestrictions' => '',
                    'LivesWithIndicator' => true,
                    'PrimaryContactIndicator' => true

                );


                $_Personas[$as]['PersonDegreeOrCertificate'] = array(

                    // 'PersonDegreeOrCertificateId' => $PersonDegreeOrCertificateId,
                    // 'PersonId' => $PersonId
                    // 'DegreeOrCertificateTitleOrSubject' => '',
                    // 'RefDegreeOrCertificateTypeId' => '',
                    // 'AwardDate' => '',
                    // 'NameOfInstitution' => '',
                    // 'RefHigherEducationInstitutionAccreditationStatusId' => '',
                    // 'RefEducationVerificationMethodId' => ''

                );


                $apoderadosRel['S' . $apoderadosSec[$asec]->alumnoIdApoderado] = [
                    'PersonId' => $PersonId,
                    'Item' => $asec
                ];
            }



            /* ------------------------------- RELACIONES ALUMNO ------------------------------- */

            // Apoderado Principal
            $alumnosApoderadoPrincipalRel = $modelJson->listarAlumnosApoderadoPrincipalJSON($idperiodo, $idestablecimiento);

            foreach ($alumnosApoderadoPrincipalRel as $key => $alumnoApopRel) {


                if ($alumnoApopRel->idApoderado != 0) {

                    $PersonRelationshipId++;

                    $PersonIdAlumno = $alumnosRel[$alumnoApopRel->idAlumnos]['Item'];

                    $PersonIdApoderado = $apoderadosRel['P' . $alumnoApopRel->idApoderado]['Item'];

                    $nuevosValoresPersonRelationship = array(

                        'PersonRelationshipId' => $PersonRelationshipId,
                        'PersonId' => $PersonIdAlumno,
                        'RelatedPersonId' => (integer)$PersonIdApoderado,
                        'RefPersonRelationshipId' => 19, // Ref Mother
                        'CustodialRelationshipIndicator' => true,
                        'EmergencyContactInd' => true,
                        'ContactPriorityNumber' => 1,
                        // 'ContactRestrictions' => '',
                        'LivesWithIndicator' => true,
                        'PrimaryContactIndicator' => true

                    );

                    array_push($_Personas[$PersonIdAlumno]['PersonRelationship'], $nuevosValoresPersonRelationship);
                }
            }


            // // Apoderado Secundario

            $alumnosApoderadoSecundarioRel = $modelJson->listarAlumnosApoderadoSecundarioJSON($idperiodo, $idestablecimiento);

            foreach ($alumnosApoderadoSecundarioRel as $key => $alumnoAposRel) {

                if ($alumnoAposRel->idApoderados != 0) {

                    $PersonRelationshipId++;

                    $PersonIdAlumno = $alumnosRel[$alumnoAposRel->idAlumnos]['Item'];
                    $PersonIdApoderado = $apoderadosRel['S' . $alumnoAposRel->idApoderados]['Item'];

                    $nuevosValoresPersonRelationshipId = array(

                        'PersonRelationshipId' => $PersonRelationshipId,
                        'PersonId' => $PersonIdAlumno,
                        'RelatedPersonId' => (integer)$PersonIdApoderado,
                        'RefPersonRelationshipId' => 19, // Ref Mother
                        'CustodialRelationshipIndicator' => true,
                        'EmergencyContactInd' => true,
                        'ContactPriorityNumber' => 2,
                        // 'ContactRestrictions' => '',
                        'LivesWithIndicator' => true,
                        'PrimaryContactIndicator' => false

                    );
                    array_push($_Personas[$PersonIdAlumno]['PersonRelationship'], $nuevosValoresPersonRelationshipId);
                }
            }
            /* ------------------------------- FIN RELACIONES ALUMNO ------------------------------- */

            /* ----------------------------------- Fin _Personas ----------------------------------- */


            /* ----------------------------------- _Organizaciones ----------------------------------- */

            // 1000 = Establecimientos
            // 1001 = Sostenedores
            // 1002 = Cursos
            // 1003 = Asignaturas
            // 1004 = Niveles

            $OrganizationLocationId = 0;
            $OrganizationTelephoneId = 0;
            $OrganizationEmailId = 0;
            $RefOrganizationRelationshipId = 0;
            $OrganizationIdentifierId = 0;
            $OrganizationOperationalStatusId = 0;
            $OrganizationIndicatorId = 0;
            $OrganizationRelationshipId = 0;


            // ESTABLECIMIENTOS
            $establecimientos = $modelJson->listarEstablecimientosJSON($idperiodo, $idestablecimiento);

            $largo = count($establecimientos);
            for ($e = 0; $e < $largo; $e++) {

                $OrganizationId = intval('1000' . $establecimientos[$e]->idEstablecimiento);
                $OrganizationLocationId++;
                $OrganizationTelephoneId++;
                $OrganizationEmailId++;
                $RefOrganizationRelationshipId++;
                $OrganizationIdentifierId++;
                $OrganizationOperationalStatusId++;
                $OrganizationIndicatorId++;
                $OrganizationRelationshipId++;


                // Organization
                $Name = substr($establecimientos[$e]->nombreEstablecimiento, 0, 128);
                $ShortName = substr($establecimientos[$e]->nombreEstablecimiento, 0, 30);

                $_Organizaciones[$e]['Organization'][] = array(

                    'OrganizationId' => (int)$OrganizationId,
                    'Name' => $Name,
                    'RefOrganizationTypeId' => 18, // Ref EducationInstitution
                    'ShortName' => $ShortName
                    // 'RegionGeoJSON'         => '',

                );


                $_Organizaciones[$e]['OrganizationLocation'] = array(

                    // 'OrganizationLocationId' => $OrganizationLocationId,
                    // 'OrganizationId' => $OrganizationId,
                    // 'LocationId' => '',
                    // 'RefOrganizationLocationTypeId' => ''

                );

                // OrganizationTelephone
                $TelephoneNumber = substr($establecimientos[$e]->telefono, 0, 24);

                $_Organizaciones[$e]['OrganizationTelephone'][] = array(

                    'OrganizationTelephoneId' => $OrganizationTelephoneId,
                    'OrganizationId' => $OrganizationId,
                    'TelephoneNumber' => (string)$TelephoneNumber,
                    'PrimaryTelephoneNumberIndicator' => True,
                    'RefInstitutionTelephoneTypeId' => 2 // Ref Main

                );


                // OrganizationEmail
                $ElectronicMailAddress = substr($establecimientos[$e]->correo, 0, 128);

                $_Organizaciones[$e]['OrganizationEmail'][] = array(

                    'OrganizationEmailId' => (int)$OrganizationEmailId,
                    'OrganizationId' => (int)$OrganizationId,
                    'ElectronicMailAddress' => $ElectronicMailAddress,
                    'RefEmailTypeId' => 3 // Ref Organizational

                );


                $_Organizaciones[$e]['RefOrganizationRelationship'] = array(

                    // 'RefOrganizationRelationshipId' => $RefOrganizationRelationshipId,
                    // 'Description' => '',
                    // 'Code' => '',
                    // 'Definition'=> '',
                    // 'RefJurisdictionId' => '',
                    // 'SortOrder' => ''

                );


                // OrganizationIdentifier
                $Identifier = "RBD".substr($establecimientos[$e]->rbd, 0, 40);

                $_Organizaciones[$e]['OrganizationIdentifier'][] = array(

                    'OrganizationIdentifierId' => (int)$OrganizationIdentifierId,
                    'Identifier' => $Identifier,
                    'RefOrganizationIdentificationSystemId' => 45, // Ref Other tipo School
                    'OrganizationId' => (int)$OrganizationId
                    // 'RefOrganizationIdentifierTypeId' => ''

                );


                $_Organizaciones[$e]['OrganizationWebsite'] = array(

                    // 'OrganizationId' => $OrganizationId
                    // 'Website' => '',

                );


                $_Organizaciones[$e]['OrganizationOperationalStatus'][] = array(

                    'OrganizationOperationalStatusId' => (int)$OrganizationOperationalStatusId,
                    'OrganizationId' => (int)$OrganizationId,
                    'RefOperationalStatusId' => 17, // Ref Active
                    // 'OperationalStatusEffectiveDate' => ''

                );


                $_Organizaciones[$e]['OrganizationIndicator'] = array(

                    // 'OrganizationIndicatorId' => $OrganizationIndicatorId,
                    // 'OrganizationId' => $OrganizationId
                    // 'IndicatorValue' => '',
                    // 'RefOrganizationIndicatorId' => ''

                );


                $_Organizaciones[$e]['OrganizationRelationship'] = array(

                    // 'OrganizationRelationshipId' => $OrganizationRelationshipId,
                    // 'Parent_OrganizationId' => '',
                    // 'OrganizationId' => $OrganizationId
                    // 'RefOrganizationRelationshipId' => ''

                );

                $establecimientosOrganizationRel[$establecimientos[$e]->idEstablecimiento] = [
                    'OrganizationId' => $OrganizationId,
                    'Item' => $e
                ];
            }

            $s = $e;
            // Sostenedores
//        $sostenedores = $modelJson->listarSostenedoresJSON(null, $idestablecimiento);
//        $largo = count($sostenedores) + $e;
//        for ($s = $e; $s < $largo; $s++) {
//
//
//            $sost = $s - $e;
//            $OrganizationId = intval('1001'.$sostenedores[$sost]->idSostenedor);
//            $OrganizationLocationId++;
//            $OrganizationTelephoneId++;
//            $OrganizationEmailId++;
//            $RefOrganizationRelationshipId++;
//            $OrganizationIdentifierId++;
//            $OrganizationOperationalStatusId++;
//            $OrganizationIndicatorId++;
//            $OrganizationRelationshipId++;
//
//            // Organization
//            $Name = substr($sostenedores[$sost]->nombreSostenedor, 0, 128);
//            $ShortName = substr($sostenedores[$sost]->nombreSostenedor, 0, 128);
//
//            $_Organizaciones[$s]['Organization'][] = array(
//
//                'OrganizationId' => (int)$OrganizationId,
//                'Name'           => $Name,
//                // 'RefOrganizationTypeId' => ['Code' => 'EducationInstitution'],
//                'ShortName'      => $ShortName,
//                // 'RegionGeoJSON' => '',
//
//            );
//
//
//            $_Organizaciones[$s]['OrganizationLocation'] = array(
//
//                // 'OrganizationLocationId' => $OrganizationLocationId,
//                // 'OrganizationId' => $OrganizationId,
//                // 'LocationId' => '',
//                // 'RefOrganizationLocationTypeId' => ''
//
//            );
//
//
//            // OrganizationTelephone
//            $TelephoneNumber = substr($sostenedores[$sost]->telefono, 0, 24);
//
//            $_Organizaciones[$s]['OrganizationTelephone'][] = array(
//
//                'OrganizationTelephoneId'   => $OrganizationTelephoneId,
//                'OrganizationId'            => $OrganizationId,
//                'TelephoneNumber'           => $TelephoneNumber,
//                'PrimaryTelephoneNumberIndicator' => true,
//                'RefInstitutionTelephoneTypeId'   => 2 // Ref Main
//
//            );
//
//
//            // OrganizationEmail
//            $ElectronicMailAddress = substr($sostenedores[$sost]->correo, 0, 128);
//
//            $_Organizaciones[$s]['OrganizationEmail'][] = array(
//
//                'OrganizationEmailId'   => (int)$OrganizationEmailId,
//                'OrganizationId'        => (int)$OrganizationId,
//                'ElectronicMailAddress' => $ElectronicMailAddress,
//                'RefEmailTypeId' => 2 // Ref Work
//
//            );
//
//
//            $_Organizaciones[$s]['RefOrganizationRelationship'] = array(
//
//                // 'RefOrganizationRelationshipId' => $RefOrganizationRelationshipId,
//                // 'Description' => '',
//                // 'Code' => '',
//                // 'Definition' => '',
//                // 'RefJurisdictionId' => '',
//                // 'SortOrder' => ''
//
//            );
//
//
//            // OrganizationIdentifier
//            $Identifier = substr($sostenedores[$sost]->rutSostenedor, 0, 40);
//
//            $_Organizaciones[$s]['OrganizationIdentifier'][] = array(
//
//                'OrganizationIdentifierId'  => (int)$OrganizationIdentifierId,
//                'Identifier'                => $Identifier,
//                'RefOrganizationIdentificationSystemId' => 'Other', // ??? Ref Other
//                'OrganizationId'            => (int)$OrganizationId
//                // 'RefOrganizationIdentifierTypeId' => ''
//
//            );
//
//
//            $_Organizaciones[$s]['OrganizationWebsite'] = array(
//
//                // 'OrganizationId' => $OrganizationId
//                // 'Website' => '',
//
//            );
//
//
//            $_Organizaciones[$s]['OrganizationOperationalStatus'][] = array(
//
//                'OrganizationOperationalStatusId' => (int)$OrganizationOperationalStatusId,
//                'OrganizationId'                  => (int)$OrganizationId,
//                'RefOperationalStatusId'          => ['Code' => 'Active'],
//                // 'OperationalStatusEffectiveDate' => ''
//
//            );
//
//
//            $_Organizaciones[$s]['OrganizationIndicator'] = array(
//
//                // 'OrganizationIndicatorId' => $OrganizationIndicatorId,
//                // 'OrganizationId' => $OrganizationId
//                // 'IndicatorValue' => '',
//                // 'RefOrganizationIndicatorId' => ''
//
//            );
//
//
//            $_Organizaciones[$s]['OrganizationRelationship'] = array(
//
//                // 'OrganizationRelationshipId' => $OrganizationRelationshipId,
//                // 'Parent_OrganizationId' => '',
//                // 'OrganizationId' => $OrganizationId
//                // 'RefOrganizationRelationshipId' => ''
//
//            );
//
//        }


            // CURSOS
            $cursos = $modelJson->listarCursosJSON($idperiodo, $idestablecimiento);

            $largo = count($cursos) + $s;
            for ($c = $s; $c < $largo; $c++) {

                $curs = $c - $s;
                $OrganizationId = intval('1002' . $cursos[$curs]->idCursos);
                $OrganizationId++;
                $OrganizationLocationId++;
                $OrganizationTelephoneId++;
                $OrganizationEmailId++;
                $RefOrganizationRelationshipId++;
                $OrganizationIdentifierId++;
                $OrganizationOperationalStatusId++;
                $OrganizationIndicatorId++;
                $OrganizationRelationshipId++;


                // Organization
                $nombreCurso = $cursos[$curs]->nombreGrado . ' ' . $cursos[$curs]->letra;
                $Name = substr($nombreCurso, 0, 128);
                $ShortName = substr($nombreCurso, 0, 30);

                $_Organizaciones[$c]['Organization'][] = array(

                    'OrganizationId' => (int)$OrganizationId,
                    'Name' => $Name,
                    'RefOrganizationTypeId' => 21, // Ref Course
                    'ShortName' => $ShortName
                    // 'RegionGeoJSON' => '',
                );


                $_Organizaciones[$c]['OrganizationLocation'] = array(

                    // 'OrganizationLocationId' => $OrganizationLocationId,
                    // 'OrganizationId' => $OrganizationId,
                    // 'LocationId' => '',
                    // 'RefOrganizationLocationTypeId' => ''

                );


                $_Organizaciones[$c]['OrganizationTelephone'] = array(

                    // 'OrganizationTelephoneId' => $OrganizationTelephoneId,
                    // 'OrganizationId' => $OrganizationId
                    // 'TelephoneNumber' => '',
                    // 'PrimaryTelephoneNumberIndicator' => 'Yes',
                    // 'RefInstitutionTelephoneTypeId' => 'Main'
                );


                $_Organizaciones[$c]['OrganizationEmail'] = array(

                    // 'OrganizationEmailId' => (int)$OrganizationEmailId,
                    // 'OrganizationId' => (int)$OrganizationId
                    // 'ElectronicMailAddress' => '',
                    // 'RefEmailTypeId'=> ''
                );


                $_Organizaciones[$c]['RefOrganizationRelationship'] = array(

                    // 'RefOrganizationRelationshipId' => $RefOrganizationRelationshipId,
                    // 'Description' => '',
                    // 'Code' => '',
                    // 'Definition' => '',
                    // 'RefJurisdictionId'=> '',
                    // 'SortOrder' => ''
                );


                // OrganizationIdentifier
                $Identifier = substr($cursos[$curs]->idCursos, 0, 40);

                $_Organizaciones[$c]['OrganizationIdentifier'][] = array(

                    'OrganizationIdentifierId' => (int)$OrganizationIdentifierId,
                    'Identifier' => $Identifier,
                    'RefOrganizationIdentificationSystemId' => 4, // Ref Other
                    'OrganizationId' => (int)$OrganizationId
                    // 'RefOrganizationIdentifierTypeId' => ''
                );


                $_Organizaciones[$c]['OrganizationWebsite'] = array(

                    // 'OrganizationId' => $OrganizationId
                    // 'Website' => '',
                );


                $_Organizaciones[$c]['OrganizationOperationalStatus'][] = array(

                    'OrganizationOperationalStatusId' => (int)$OrganizationOperationalStatusId,
                    'OrganizationId' => (int)$OrganizationId,
                    'RefOperationalStatusId' => 17, // Ref Active
                    // 'OperationalStatusEffectiveDate' => ''
                );


                $_Organizaciones[$c]['OrganizationIndicator'] = array(

                    // 'OrganizationIndicatorId' => $OrganizationIndicatorId,
                    // 'OrganizationId' => $OrganizationId
                    // 'IndicatorValue' => '',
                    // 'RefOrganizationIndicatorId' => ''
                );


                $_Organizaciones[$c]['OrganizationRelationship'] = array(

                    // 'OrganizationRelationshipId' => $OrganizationRelationshipId,
                    // 'Parent_OrganizationId' => '',
                    // 'OrganizationId' => $OrganizationId
                    // 'RefOrganizationRelationshipId' => ''
                );


                $cursosOrganizationRel[$cursos[$curs]->idCursos] = [
                    'OrganizationId' => $OrganizationId,
                    'Item' => $curs
                ];
            }


            // ASIGNATURAS
            $asignaturas = $modelJson->listarAsignaturasJSON($idperiodo, $idestablecimiento,$lista_curso);

            $largo = count($asignaturas) + $c;
            for ($a = $c; $a < $largo; $a++) {

                $asig = $a - $c;
                $OrganizationId = intval('1003' . $asignaturas[$asig]->idAsignatura);
                // $OrganizationId++;
                $OrganizationLocationId++;
                $OrganizationTelephoneId++;
                $OrganizationEmailId++;
                $RefOrganizationRelationshipId++;
                $OrganizationIdentifierId++;
                $OrganizationOperationalStatusId++;
                $OrganizationIndicatorId++;
                $OrganizationRelationshipId++;

                // Organization
                $Name = mb_convert_encoding(substr($asignaturas[$asig]->nombreAsignatura . ' ' . $asignaturas[$asig]->letra, 0, 128), 'UTF-8', 'UTF-8');
                $ShortName = substr($asignaturas[$asig]->nombreAsignatura, 0, 4);

                $_Organizaciones[$a]['Organization'][] = array(

                    'OrganizationId' => (int)$OrganizationId,
                    'Name' => $Name,
                    'RefOrganizationTypeId' => 22, // Ref CourseSection
                    'ShortName' => $ShortName,
                    // 'RegionGeoJSON' => '',

                );


                $_Organizaciones[$a]['OrganizationLocation'] = array(

                    // 'OrganizationLocationId' => $OrganizationLocationId,
                    // 'OrganizationId' => $OrganizationId,
                    // 'LocationId' => '',
                    // 'RefOrganizationLocationTypeId' => ''

                );


                $_Organizaciones[$a]['OrganizationTelephone'] = array(

                    // 'OrganizationTelephoneId' => $OrganizationTelephoneId,
                    // 'OrganizationId' => $OrganizationId
                    // 'TelephoneNumber' => '',
                    // 'PrimaryTelephoneNumberIndicator' => 'Yes',
                    // 'RefInstitutionTelephoneTypeId' => 'Main'

                );


                $_Organizaciones[$a]['OrganizationEmail'] = array(

                    // 'OrganizationEmailId' => (int)$OrganizationEmailId,
                    // 'OrganizationId' => (int)$OrganizationId
                    // 'ElectronicMailAddress' =>
                    // 'RefEmailTypeId'  => ''

                );


                $_Organizaciones[$a]['RefOrganizationRelationship'] = array(

                    // 'RefOrganizationRelationshipId' => $RefOrganizationRelationshipId
                    // 'Description' => '',
                    // 'Code'  => '',
                    // 'Definition'   => '',
                    // 'RefJurisdictionId' => '',
                    // 'SortOrder' => ''

                );


                // OrganizationIdentifier
                $Identifier = substr($asignaturas[$asig]->idAsignatura, 0, 40);

                $_Organizaciones[$a]['OrganizationIdentifier'][] = array(

                    'OrganizationIdentifierId' => (int)$OrganizationIdentifierId,
                    'Identifier' => $Identifier,
                    'RefOrganizationIdentificationSystemId' => 26, // Ref Other
                    'OrganizationId' => (int)$OrganizationId
                    // 'RefOrganizationIdentifierTypeId' => ''

                );


                $_Organizaciones[$a]['OrganizationWebsite'] = array(

                    // 'OrganizationId' => $OrganizationId
                    // 'Website' => '',

                );


                $_Organizaciones[$a]['OrganizationOperationalStatus'][] = array(

                    'OrganizationOperationalStatusId' => (int)$OrganizationOperationalStatusId,
                    'OrganizationId' => (int)$OrganizationId,
                    'RefOperationalStatusId' => 17, // Ref Active
                    // 'OperationalStatusEffectiveDate' => ''

                );


                $_Organizaciones[$a]['OrganizationIndicator'] = array(

                    // 'OrganizationIndicatorId' => $OrganizationIndicatorId,
                    // 'OrganizationId' => $OrganizationId
                    // 'IndicatorValue' => '',
                    // 'RefOrganizationIndicatorId' => ''

                );


                $_Organizaciones[$a]['OrganizationRelationship'] = array(

                    // 'OrganizationRelationshipId' => $OrganizationRelationshipId,
                    // 'Parent_OrganizationId' => '',
                    // 'OrganizationId' => $OrganizationId
                    // 'RefOrganizationRelationshipId' => ''

                );


                $asignaturasOrganizationRel[$asignaturas[$asig]->idAsignatura] = [
                    'OrganizationId' => $OrganizationId,
                    'Item' => $asig
                ];
            }


            // Grados de Educacion
            $niveles = $modelJson->listarNivelesJSON($idperiodo,$idestablecimiento,$lista_curso);

            $largo = count($niveles) + $a;
            for ($ni = $a; $ni < $largo; $ni++) {

                $nive = $ni - $a;
                $OrganizationId = intval('1004' . $niveles[$nive]->idCodigoGrado);

                // $OrganizationId++;
                $OrganizationLocationId++;
                $OrganizationTelephoneId++;
                $OrganizationEmailId++;
                $RefOrganizationRelationshipId++;
                $OrganizationIdentifierId++;
                $OrganizationOperationalStatusId++;
                $OrganizationIndicatorId++;
                $OrganizationRelationshipId++;


                // Organization
                $Name = substr($niveles[$nive]->nombreGrado, 0, 128);
                $ShortName = substr($niveles[$nive]->nombreGrado, 0, 30);

                $_Organizaciones[$ni]['Organization'][] = array(

                    'OrganizationId' => (int)$OrganizationId,
                    'Name' => $Name,
                    'RefOrganizationTypeId' => 10, // Ref K12School
                    'ShortName' => $ShortName
                    // 'RegionGeoJSON' => '',

                );


                $_Organizaciones[$ni]['OrganizationLocation'] = array(

                    // 'OrganizationLocationId' => $OrganizationLocationId,
                    // 'OrganizationId' => $OrganizationId,
                    // 'LocationId' => '',
                    // 'RefOrganizationLocationTypeId' => ''

                );

                // OrganizationTelephone
                // $TelephoneNumber = substr($niveles[$ni]->telefono, 0, 24);

                $_Organizaciones[$ni]['OrganizationTelephone'] = array(

                    // 'OrganizationTelephoneId' => $OrganizationTelephoneId,
                    // 'OrganizationId' => $OrganizationId,
                    // 'TelephoneNumber' => $TelephoneNumber,
                    // 'PrimaryTelephoneNumberIndicator' => 'Yes',
                    // 'RefInstitutionTelephoneTypeId' => 'Main'

                );


                // OrganizationEmail
                // $electronicMailAddress = substr($niveles[$ni]->correo, 0, 128);

                $_Organizaciones[$ni]['OrganizationEmail'] = array(

                    // 'OrganizationEmailId' => (int)$OrganizationEmailId,
                    // 'OrganizationId' => (int)$OrganizationId,
                    // 'ElectronicMailAddress' => $nilectronicMailAddress
                    // 'RefEmailTypeId' => ''

                );


                $_Organizaciones[$ni]['RefOrganizationRelationship'] = array(

                    // 'RefOrganizationRelationshipId' => $RefOrganizationRelationshipId,
                    // 'Description' => '',
                    // 'Code' => '',
                    // 'Definition' => '',
                    // 'RefJurisdictionId' => '',
                    // 'SortOrder' => ''

                );


                // OrganizationIdentifier
                $Identifier = substr($niveles[$nive]->idCodigoGrado, 0, 40);

                $_Organizaciones[$ni]['OrganizationIdentifier'][] = array(

                    'OrganizationIdentifierId' => (int)$OrganizationIdentifierId,
                    'Identifier' => $Identifier,
                    'RefOrganizationIdentificationSystemId' => 35, // Ref Other
                    'OrganizationId' => (int)$OrganizationId
                    // 'RefOrganizationIdentifierTypeId' => ''

                );


                $_Organizaciones[$ni]['OrganizationWebsite'] = array(

                    // 'OrganizationId' => $OrganizationId
                    // 'Website' => '',

                );


                $_Organizaciones[$ni]['OrganizationOperationalStatus'][] = array(

                    'OrganizationOperationalStatusId' => (int)$OrganizationOperationalStatusId,
                    'OrganizationId' => (int)$OrganizationId,
                    'RefOperationalStatusId' => 17, // Ref Active
                    // 'OperationalStatusEffectiveDate' => ''

                );


                $_Organizaciones[$ni]['OrganizationIndicator'] = array(

                    // 'OrganizationIndicatorId' => $OrganizationIndicatorId,
                    // 'OrganizationId' => $OrganizationId
                    // 'IndicatorValue'=> '',
                    // 'RefOrganizationIndicatorId' => ''

                );


                $_Organizaciones[$ni]['OrganizationRelationship'] = array(

                    // 'OrganizationRelationshipId' => $OrganizationRelationshipId,
                    // 'Parent_OrganizationId' => '',
                    // 'OrganizationId' => $OrganizationId
                    // 'RefOrganizationRelationshipId' => ''

                );


                $nivelesOrganizationRel[$niveles[$nive]->idCodigoGrado] = [
                    'OrganizationId' => $OrganizationId,
                    'Item' => $nive
                ];
            }

            /* ----------------------------------- Fin _Organizaciones ----------------------------------- */

            /* ----------------------------------- _Establecimientos ----------------------------------- */


            // ESTABLECIMIENTOS
            $establecimientos2 = $modelJson->listarRelEstablecimientosJSON(null, $idestablecimiento);

            $K12SchoolCorrectiveActionId = 0;
            $K12SchoolGradeOfferedId = 0;
            $K12SchoolImprovementId = 0;

            $largo = count($establecimientos2);
            for ($es = 0; $es < $largo; $es++) {


                $K12SchoolCorrectiveActionId++;
                $K12SchoolGradeOfferedId++;
                $K12SchoolImprovementId++;

                $OrganizationId = $establecimientosOrganizationRel[$establecimientos2[$es]->idEstablecimiento]['OrganizationId'];

                switch ($establecimientos2[$es]->dependencia) {
                    case 'Municipal':
                        $RefAdministrativeFundingControlId = 1;
                        break;

                    default:
                        $RefAdministrativeFundingControlId = 1;
                        break;
                }


                $CharterSchoolContractIdNumber = $establecimientos2[$es]->numeroDecreto;

                $_Establecimientos[$es]['K12SchoolCorrectiveAction'][] = array(

                    'K12SchoolCorrectiveActionId' => (integer)$K12SchoolCorrectiveActionId,
                    'OrganizationId' => (integer)$OrganizationId,
                    'RefCorrectiveActionTypeId' => 1 // Ref CA1

                );


                // CharterSchoolIndicator
                // CharterSchoolOpenEnrollmentIndicator
                // CharterSchoolContractIdNumber

                $_Establecimientos[$es]['K12School'][] = array(

                    'OrganizationId' => $OrganizationId,
                    'RefSchoolTypeId' => 1,  // Ref Regular
                    'RefSchoolLevelId' => 10, // Ref 02397
                    'RefAdministrativeFundingControlId' => $RefAdministrativeFundingControlId,
                    'CharterSchoolIndicator' => true,
                    // 'RefCharterSchoolTypeId' => '',
                    // 'RefIncreasedLearningTimeTypeId' => '',
                    // 'RefStatePovertyDesignationId'  => '',
                    // 'CharterSchoolApprovalYear' => '', El año decreto?  establecimientoconfiguracion
                    'RefCharterSchoolApprovalAgencyTypeId' => 1, // Ref State
                    // 'AccreditationAgencyName' => '',
                    'CharterSchoolOpenEnrollmentIndicator' => true,
                    // 'CharterSchoolContractApprovalDate' => '',
                    'CharterSchoolContractIdNumber' => (String)$CharterSchoolContractIdNumber,
                    // 'CharterSchoolContractRenewalDate' => '',
                    // 'RefCharterSchoolManagementOrganizationTypeId' => ''

                );


                $_Establecimientos[$es]['K12SchoolGradeOffered'][] = array(

                    'K12SchoolGradeOfferedId' => (integer)$K12SchoolGradeOfferedId,
                    'OrganizationId' => $OrganizationId,
                    'RefGradeLevelId' => 7, // ??? Ref 001210

                );


                $_Establecimientos[$es]['K12SchoolStatus'] = array(

                    // 'OrganizationId' => (integer)$OrganizationId
                    // 'RefMagnetSpecialProgramId'             => '',
                    // 'RefAlternativeSchoolFocusId'           => '',
                    // 'RefInternetAccessId'                   => '',
                    // 'RefRestructuringActionId'              => '',
                    // 'RefTitleISchoolStatusId'               => '',
                    // 'ConsolidatedMepFundsStatus'            => '',
                    // 'RefNationalSchoolLunchProgramStatusId' => '',
                    // 'RefVirtualSchoolStatusId'              => '',

                );


                $_Establecimientos[$es]['K12SchoolImprovement'][] = array(

                    'K12SchoolImprovementId' => (integer)$K12SchoolImprovementId,
                    'OrganizationId' => $OrganizationId
                    // 'RefSchoolImprovementStatusId' => '',
                    // 'RefSchoolImprovementFundsId'  => '',
                    // 'RefSigInterventionTypeId'     => '',
                    // 'SchoolImprovementExitDate'    => ''

                );

            }


            /* ----------------------------------- FIN _Establecimientos ----------------------------------- */


            /* ----------------------------------- _Calendarios ----------------------------------- */


            $OrganizationCalendarId = 0;
            $OrganizationCalendarDayId = 0;
            $OrganizationCalendarCrisisId = 0;
            $OrganizationCalendarSessionId = 0;
            $OrganizationCalendarEventId = 0;
            $_Calendarios = array();

            // ESTABLECIMIENTOS


            //$establecimientos = $modelJson->listarEstablecimientosJSON(null, $idestablecimiento);
            $eventosEstableci = $modelJson->getEventosEstablecimientoJSON($idperiodo, $idestablecimiento);


            $largo = 1;
            for ($est = 0; $est < $largo; $est++) {

                $OrganizationCalendarId++;

                $calendariosEstablecimiento[$idestablecimiento] = $OrganizationCalendarId;

                $CalendarYear = substr($eventosEstableci[0]['nombrePeriodo'], 0, 4);
                $_Calendarios[$est]['OrganizationCalendar'][] = array(

                    'OrganizationCalendarId' => $OrganizationCalendarId,
                    'OrganizationId' => $establecimientosOrganizationRel[$idestablecimiento]['OrganizationId'],
                    'CalendarCode' => (string)$eventosEstableci[0]['codigoCalendario'],
                    'CalendarDescription' => (string)$eventosEstableci[0]['descripcionCalendario'],
                    'CalendarYear' => (string)$CalendarYear

                );


                $_Calendarios[$est]['OrganizationCalendarDay'] = array(
//                'OrganizationCalendarDayId' => $OrganizationCalendarDayId,
//                'OrganizationCalendarId'   =>$OrganizationCalendarId
                    //----'DayName' => '',  // falta
                    //'AlternateDayName'=>''
                );

                $OrganizationCalendarCrisisId++;
                $_Calendarios[$est]['OrganizationCalendarCrisis'][] = array(
                    'OrganizationCalendarCrisisId' => $OrganizationCalendarCrisisId,
                    'OrganizationId' => $OrganizationCalendarId,
//                'Code' => '',
//                'Name' => '',
                    'StartDate' => $eventosEstableci[0]['fechaInicioClase'],
                    'EndDate' => $eventosEstableci[0]['fechaTerminoClase'],
//                'Type' => '',
//                'CrisisDescription' => '',
//                'CrisisEndDate' => ''

                );


                // InstructionalMinutes
                // Description
                // MarkingTermIndicator
                // SchedulingTermIndicator
                // AttendanceTermIndicator
                // DaysInSession
                // MinutesPerDay
                // MinutesPerDay
                // SessionStartTime
                // SessionEndTime


                $_Calendarios[$est]['OrganizationCalendarSession'] = array(
//                'OrganizationCalendarSessionId' => $OrganizationCalendarSessionId,
                    //'Designator' =>'',
                    //'BeginDate' =>'',
                    //'EndDate' =>'',
                    //'RefSessionTypeId' =>'',
                    //'InstructionalMinutes' =>'',
                    //'Code' =>'',
                    //'Description' =>'',
                    //'MarkingTermIndicator' =>'',
                    //'SchedulingTermIndicator' =>'',
                    //'AttendanceTermIndicator' =>'',
//                'OrganizationCalendarId' => $OrganizationCalendarId,
                    //'DaysInSession' =>'',
                    //'FirstInstructionDate' =>'',
                    //'LastInstructionDate' =>'',
                    //'MinutesPerDay' =>'',
                    //'SessionStarTime' =>'',
                    //'SessionEndTime' =>'',

                );

                $OrganizationCalendarEventId++;
                $_Calendarios[$est]['OrganizationCalendarEvent'] = array(
                    //'OrganizationCalendarEventId' => $OrganizationCalendarEventId,
//                'OrganizationCalendarId'=> $OrganizationCalendarId
                    //----'Name' =>'',
                    //----'EventDate' =>'',
                    //'RefCalendarEventType' =>'',

                );
            }


            // CALENDARIOS CURSOS
            $cursos = $modelJson->listarCursosJSON($idperiodo, $idestablecimiento);


            $largo = count($cursos) + $est;
            for ($cur = $est; $cur < $largo; $cur++) {

                $curs = $cur - $est;
                $OrganizationCalendarId++;
                $listaeventos = $modelJson->getdiaseventosJSON($idperiodo, $idestablecimiento, $cursos[$curs]['idCursos']);

                $calendariosCurso[$cursos[$curs]['idCursos']] = $OrganizationCalendarId;


                $_Calendarios[$cur]['OrganizationCalendar'][] = array(
                    'OrganizationCalendarId' => $OrganizationCalendarId,
                    'OrganizationId' => $cursosOrganizationRel[$cursos[$curs]['idCursos']]['OrganizationId'],
                    'CalendarCode' => (string)$listaeventos[0]['codigoCalendario'],
                    'CalendarDescription' => (string)$listaeventos[0]['descripcionCalendario'],
                    'CalendarYear' => (string)$datosperiodo[0]['nombrePeriodo']
                );

                $_Calendarios[$cur]['OrganizationCalendarDay'] = array(
//                'OrganizationCalendarDayId' => $OrganizationCalendarDayId,
//                'OrganizationCalendarId'=>$OrganizationCalendarId
                    //----'DayName' => '',
                    //'AlternateDayName'=>''
                );


                $_Calendarios[$cur]['OrganizationCalendarCrisis'] = array(
//                'OrganizationCalendarCrisisId' => $OrganizationCalendarCrisisId,
//                'OrganizationId'=>$OrganizationCalendarId
                    //'Code' => '',
                    //'Name'=>'',
                    //StartDate=>'',
                    //EndDate =>'',
                    //Type=>'',
                    //CrisisDescription=>'',
                    //CrisisEndDate=>''

                );


                // InstructionalMinutes
                // Description
                // MarkingTermIndicator
                // SchedulingTermIndicator
                // AttendanceTermIndicator
                // DaysInSession
                // MinutesPerDay
                // MinutesPerDay
                // SessionStartTime
                // SessionEndTime

                $_Calendarios[$cur]['OrganizationCalendarSession'] = array(
//                'OrganizationCalendarSessionId' => $OrganizationCalendarSessionId,
                    //Designator=>'',
                    //BeginDate=>'',
                    //EndDate=>'',
                    //RefSessionTypeId=>'',
                    //InstructionalMinutes=>'',
                    //Code=>'',
                    //Description=>'',
                    //MarkingTermIndicator=>'',
                    //SchedulingTermIndicator=>'',
                    //AttendanceTermIndicator=>'',
//                'OrganizationCalendarId' => $OrganizationCalendarId,
                    //'DaysInSession'=>'',
                    //'FirstInstructionDate'=>'',
                    //'LastInstructionDate'=>'',
                    //'MinutesPerDay'=>'',
                    //'SessionStarTime'=>'',
                    //'SessionEndTime'=>'',

                );

                $_Calendarios[$cur]['OrganizationCalendarEvent'] = array(

                    //'OrganizationCalendarEventId' => (int)$OrganizationCalendarEventId,
                    // 'OrganizationCalendarId' => (int)$OrganizationCalendarId
                    // 'Name'=>'',
                    // 'EventDate' => '',
                    // 'RefCalendarEventType' => '',

                );


                if (!empty($listaeventos)) {

                    foreach ($listaeventos as $keyev => $datoseventos) {

                        if (!is_null($datoseventos['tipoEvento'])) {
                            $RefCalendarEventType = $datoseventos['tipoEvento'];
                        } else {
                            $RefCalendarEventType = 4;
                        }

                        $OrganizationCalendarEventId++;

                        $name = substr($datoseventos['nombreEvento'], 0, 30);
                        $_Calendarios[$cur]['OrganizationCalendarEvent'][] = array(

                            'OrganizationCalendarEventId' => (int)$OrganizationCalendarEventId,
                            'OrganizationCalendarId' => (int)$OrganizationCalendarId,
                            'Name' => $name,
                            'EventDate' => $datoseventos['fechaEvento'],
                            'RefCalendarEventType' => $RefCalendarEventType // ???

                        );

                    }
                }
            }


            // CALENDARIOS ASIGNATURAS
            $asignaturas = $modelJson->listarAsignaturasJSON($idperiodo, $idestablecimiento,$lista_curso);

            $largo = count($asignaturas) + $cur;
            for ($as = $cur; $as < $largo; $as++) {

                $asig = $as - $cur;

                $OrganizationCalendarId++;
                $calendarioasignaturasOrganizationRel[$asignaturas[$asig]['idAsignatura']] = $OrganizationCalendarId;


                $_Calendarios[$as]['OrganizationCalendar'][] = array(
                    'OrganizationCalendarId' => $OrganizationCalendarId,
                    'OrganizationId' => $asignaturasOrganizationRel[$asignaturas[$asig]['idAsignatura']]['OrganizationId'],
                    'CalendarCode' => 'Asi',
                    'CalendarDescription' => 'Calendario Asignatura',
                    'CalendarYear' => (string)$datosperiodo[0]['nombrePeriodo']
                );

                $OrganizationCalendarDayId++;
                $_Calendarios[$as]['OrganizationCalendarDay'][] = array(
                    'OrganizationCalendarDayId' => $OrganizationCalendarDayId,
                    'OrganizationCalendarId' => $OrganizationCalendarId,
                    'DayName' => $dias[$asignaturas[$asig]['dia']],
                    //'AlternateDayName'=>''
                );


                $_Calendarios[$as]['OrganizationCalendarCrisis'] = array(

//                'OrganizationCalendarCrisisId' => $OrganizationCalendarCrisisId,
//                'OrganizationId'               => $OrganizationCalendarId
                    //'Code' => '',
                    //'Name'=> '',
                    //'StartDate' => '',
                    //'EndDate' => '',
                    //'Type' => '',
                    //'CrisisDescription' => '',
                    //'CrisisEndDate' => ''

                );


                $_Calendarios[$as]['OrganizationCalendarSession'] = array(

//                'OrganizationCalendarSessionId' => $OrganizationCalendarSessionId,
//                //'Designator' => '',
//                //'BeginDate' => '',
//                //'EndDate' => '',
//                //'RefSessionTypeId' => '',
//                'InstructionalMinutes'      => '',      // El número total de minutos de instrucción en una sesión determinada, según lo determinado por el tiempo en clase, el tiempo en la tarea (por ejemplo, participar en una clase) o según lo estimado por un diseñador calificado del curso.
//                //'Code' => '',
//                'Description'               => $control->contenidos,
//                'MarkingTermIndicator'      => false,  // ??? Indica que la sesión es un término marcado
//                'SchedulingTermIndicator'   => false,  // Indica que la sesión es un término de programación.
//                'AttendanceTermIndicator'   => false,  // Indica que la sesión es un término de asistencia.
//                'OrganizationCalendarId'    => $OrganizationCalendarId,
//                'DaysInSession'             => '',     // El número total de días que la escuela estuvo o se espera que esté en sesión durante el año escolar. También se incluyen los días en que las instalaciones de la institución educativa están cerradas y el cuerpo estudiantil en su conjunto se dedica a actividades planificadas fuera del campus bajo la guía y dirección de los miembros del personal.
//                //'FirstInstructionDate' => '',
//                //'LastInstructionDate' => '',
//                'MinutesPerDay'             => '',     // El número de minutos en el día en que la escuela normalmente está en sesión.
//                'SessionStarTime'           => $control->tiempoInicio,
//                'SessionEndTime'            => $control->tiempoTermino

                );


                $controlContenidos = $modelJson->listarControlContenidosJSON($idperiodo, $idestablecimiento, $asignaturas[$asig]['idCursos'], $asignaturas[$asig]['idAsignatura']);
                $evento = $modelJson->getEventosEstablecimientoJSON($idperiodo, $idestablecimiento);
                foreach ($controlContenidos as $keybloq => $control) {

                    $horaInicio = new DateTime($control->tiempoInicio);
                    $horaTermino = new DateTime($control->tiempoTermino);
                    $Minutes = $horaInicio->diff($horaTermino);
                    $instructionalMinutes = $Minutes->format('%i') . '.1';

                    $fechaInicio = new DateTime($evento[0]['fechaInicioClase']);
                    $fechaTermino = new DateTime($evento[0]['fechaTerminoClase']);
                    $DaysSession = $fechaInicio->diff($fechaTermino);

                    $OrganizationCalendarSessionId++;
                    $_Calendarios[$as]['OrganizationCalendarSession'][] = array(

                        'OrganizationCalendarSessionId' => $OrganizationCalendarSessionId,
                        //'Designator' => '',
                        //'BeginDate' => '',
                        //'EndDate' => '',
                        //'RefSessionTypeId' => '',
                        'InstructionalMinutes' => (Float)$instructionalMinutes,      // El número total de minutos de instrucción en una sesión determinada, según lo determinado por el tiempo en clase, el tiempo en la tarea (por ejemplo, participar en una clase) o según lo estimado por un diseñador calificado del curso.
                        //'Code' => '',
                        'Description' => $control->contenidos,
                        'MarkingTermIndicator' => false,  // ??? Indica que la sesión es un término marcado
                        'SchedulingTermIndicator' => false,  // Indica que la sesión es un término de programación.
                        'AttendanceTermIndicator' => false,  // Indica que la sesión es un término de asistencia.
                        'OrganizationCalendarId' => $OrganizationCalendarId,
                        'DaysInSession' => $DaysSession->days,     // El número total de días que la escuela estuvo o se espera que esté en sesión durante el año escolar. También se incluyen los días en que las instalaciones de la institución educativa están cerradas y el cuerpo estudiantil en su conjunto se dedica a actividades planificadas fuera del campus bajo la guía y dirección de los miembros del personal.
                        //'FirstInstructionDate' => '',
                        //'LastInstructionDate' => '',
                        'MinutesPerDay' => 8,     // El número de minutos en el día en que la escuela normalmente está en sesión.
                        'SessionStartTime' => $control->tiempoInicio,
                        'SessionEndTime' => $control->tiempoTermino

                    );

                }


                // OrganizationCalendarEvent
                $OrganizationCalendarEventId++;
                $_Calendarios[$as]['OrganizationCalendarEvent'] = array(
//
//                'OrganizationCalendarEventId' => (int)$OrganizationCalendarEventId,
//                'OrganizationCalendarId' => (int)$OrganizationCalendarId,
//                'Name' => $datoseventos['nombreEvento'],
//                'EventDate' => $datoseventos['fechaEvento'],
//                'RefCalendarEventType' => ['Code'=>$datoseventos['codigoEvento']]

                );

            }


            /* ----------------------------------- _FIN Calendarios ----------------------------------- */


            /* ----------------------------------- _Intervenciones ----------------------------------- */

            $_Intervenciones[0]['ProgramParticipationSpecialEducation'] = array(

                //          'OrganizationPersonRoleId' => '',
                // 'AwaitingInitialIDEAEvaluationStatus' => '',
                // 'RefIDEAEducationalEnvironmentECId' => '',
                // 'RefIDEAEdEnvironmentSchoolAgeId' => '',
                // 'SpecialEducationFTE' => '',
                // 'RefSpecialEducationExitReasonId' => '',
                // 'SpecialEducationServicesExitDate' => '',
                // 'IDEAPlacementRationale' => ''

            );


            $_Intervenciones[0]['PersonProgramParticipation'] = array(

                //          'OrganizationPersonRoleId' => ,
                // 'RefParticipationTypeId' => ,
                // 'RefProgramExitReasonId' => ,
                // 'ParticipationStatus' =>

            );


            $_Intervenciones[0]['IndividualizedProgram'] = array(

                //          'IndividualizedProgramId' => '',
                // 'OrganizationPersonRoleId' => '',
                // 'RefIndividualizedProgramDateType' => '',
                // 'IndividualizedProgramDate' => '',
                // 'NonInclusionMinutesPerWeek' => '',
                // 'InclusionMinutesPerWeek' => '',
                // 'RefIndividualizedProgramTransitionTypeId' => '',
                // 'RefIndividualizedProgramTypeId' => '',
                // 'ServicePlanDate' => '',
                // 'RefIndividualizedProgramLocationId' => '',
                // 'ServicePlanMeetingParticipants' => '',
                // 'ServicePlanSignedBy' => '',
                // 'ServicePlanSignatureDate' => '',
                // 'ServicePlanReevaluationDate' => '',
                // 'RefStudentSupportServiceTypeId' => '',
                // 'InclusiveSettingIndicator' => '',
                // 'ServicePlanEndDate' => '',
                // 'TransferOfRightsStatement' => ''

            );

            /* ----------------------------------- Fin _Intervenciones ----------------------------------- */


            /* -----------------------------------  _Cursos ----------------------------------- */


            $LocationId = 0;
            $FacilityLocationId = 0;
            $CourseSectionLocationId = 0;
            $CourseSectionScheduleId = 0;
            $CourseSection = 0;

            $cursos2 = $modelJson->listarCursosJSON($idperiodo, $idestablecimiento);


            $largo = count($cursos2);
            for ($c2 = 0; $c2 < $largo; $c2++) {

                $LocationId++;
                $FacilityLocationId++;
                $CourseSectionLocationId++;


                $OrganizationId = $cursosOrganizationRel[$cursos2[$c2]['idCursos']]['OrganizationId'];

                // Classroom
                $ClassroomIdentifier = (String)$cursos2[$c2]['idCursos'];

                // Location
                $_Cursos[$c2]['Location'][] = array(

                    'LocationId' => (int)$LocationId

                );


                // LocationAddress
                $_Cursos[$c2]['LocationAddress'] = array(

                    //'LocationId' => (int)$LocationId,
                    // 'StreetNumberAndName' => '',
                    // 'ApartmentRoomOrSuiteNumber' => '',
                    // 'BuildingSiteNumber' => '',
                    // 'City' => '',
                    // 'RefStateId' => '',
                    // 'PostalCode' => '',
                    // 'CountyName' => '',
                    // 'RefCountyId' => '',
                    // 'RefCountryId' => '',
                    // 'Latitude' => '',
                    // 'Longitude' => '',
                    // 'RefERSRuralUrbanContinuumCodeId' => '',
                    // 'FacilityBlockNumberArea' => '',
                    // 'FacilityCensusTract' => ''

                );


                // FacilityLocation
                $_Cursos[$c2]['FacilityLocation'][] = array(

                    'FacilityLocationId' => (int)$FacilityLocationId,
                    'FacilityId' => '',
                    'LocationId' => (int)$LocationId

                );


                // Classroom
                $_Cursos[$c2]['Classroom'][] = array(

                    'LocationId' => (int)$LocationId,
                    'ClassroomIdentifier' => $ClassroomIdentifier

                );


                // K12Course
                $_Cursos[$c2]['K12Course'][] = array(

                    'OrganizationId' => (Int)$OrganizationId,
                    'HighSchoolCourseRequirement' => true,
                    // 'RefAdditionalCreditTypeId' => '',
                    'AvailableCarnegieUnitCredit' => (Float)40.1,
                    // 'RefCourseGpaApplicabilityId' => '',// Este curso se incluye o no en el cálculo del promedio de calificaciones
                    'CoreAcademicCourse' => true,// El curso cumple con la definición estatal de un curso académico básico.
                    // 'RefCurriculumFrameworkTypeId' => '',
                    'CourseAlignedWithStandards' => true,
                    'RefCreditTypeEarnedId' => 2, // Ref 00586
                    // 'FundingProgram' => '',
                    'FamilyConsumerSciencesCourseInd' => false,
                    // 'SCEDCourseCode' => '',
                    // 'SCEDGradeSpan' => '',
                    // 'RefSCEDCourseLevelId' => '',
                    // 'RefSCEDCourseSubjectAreaId' => '',
                    // 'RefCareerClusterId' => '',
                    // 'RefBlendedLearningModelTypeId' => '',
                    // 'RefCourseInteractionModeId' => '',
                    // 'RefK12EndOfCourseRequirementId' => '',
                    // 'RefWorkbasedLearningOpportunityTypeId' => '',
                    // 'CourseDepartmentName' => ''

                );





                // Course
                $Description = $cursos2[$c2]->nombreGrado;
                $SubjectAbbreviation = $cursos2[$c2]->letra;


                // Curso
                $_Cursos[$c2]['Course'][] = array(

                    'OrganizationId' => (int)$OrganizationId,
                    'Description' => (string)$Description,
                    'SubjectAbbreviation' => (string)$SubjectAbbreviation,
                    'SCEDSequenceOfCourse' => '',
                    'InstructionalMinutes' => (Int)45,
                    //'RefCourseLevelCharacteristicsId' => 0,
                    //'RefCourseCreditUnitId' => 0,
                    'CreditValue' => (Float)0.1,
                    //'RefInstructionLanguage' => 0,
                    'CertificationDescription' => '',
                    //'RefCourseApplicableEducationLevelId' => 0,
                    'RepeatabilityMaximumNumber' => (Int)2

                );


                // Buscamos las asignaturas del curso y seteamoos su OrganizationId
                $listaAsignaturas = $modelJson->listarAsignaturasCursosJSON($ClassroomIdentifier, $idperiodo, $idestablecimiento);

                foreach ($listaAsignaturas as $key => $asignatura) {

                    $CourseSection++;
                    $OrganizationAsignaturasId = $asignaturasOrganizationRel[$asignatura->idAsignatura]['OrganizationId'];

                    // Asignaturas
                    $_Cursos[$c2]['CourseSection'][] = array(   // Asignaturas

                        'OrganizationId' => (int)$OrganizationAsignaturasId,
                        'AvailableCarnegieUnitCredit' => (Float)0.1,
                        //'RefCourseSectionDeliveryModeId' => 0,
                        //'RefSingleSexClassStatusId' => 0,
                        'TimeRequiredForCompletion' => (Float)0.1,
                        'CourseId' => (int)$OrganizationId,
                        //'RefAdditionalCreditTypeId' => 0,
                        //'RefInstructionLanguageId' => 0,
                        'VirtualIndicator' => false,
                        //'OrganizationCalendarSessionId' => 0,
                        //'RefCreditTypeEarnedId' => 0,
                        //'RefAdvancedPlacementCourseCodeId' => 0,
                        'MaximumCapacity' => 30, // Máx capacidad del curso.
                        'RelatedCompetencyFrameworkItems' => ''

                    );

                    // CourseSectionLocation
                    $_Cursos[$c2]['CourseSectionLocation'][] = array(

                        'CourseSectionLocationId' => (int)$CourseSectionLocationId,
                        'LocationId' => (int)$LocationId,
                        'OrganizationId' => (int)$OrganizationAsignaturasId
                        // 'RefInstructionLocationTypeId' => ''

                    );

                    // Bloques
                    $listaBloques = $modelJson->listarBloquesAsignaturasJSON($idperiodo, $idestablecimiento, $ClassroomIdentifier, $asignatura['idAsignatura']);

                    foreach ($listaBloques as $keyBlo => $bloque) {

                        $CourseSectionScheduleId++;

                        $tiempo_inicio = date("H:i:s", strtotime($bloque->tiempoInicio));
                        $tiempo_termino = date("H:i:s", strtotime($bloque->tiempoTermino));

                        $_Cursos[$c2]['CourseSectionSchedule'][] = array(

                            'CourseSectionScheduleId' => (int)$CourseSectionScheduleId,
                            'OrganizationId' => (int)$OrganizationAsignaturasId,
                            'ClassMeetingDays' => $dias[$bloque->dia],
                            'ClassBeginningTime' => $tiempo_inicio,
                            'ClassEndingTime' => $tiempo_termino,
                            'ClassPeriod' => '',
                            'TimeDayIdentifier' => ''

                        );
                    }
                }
            }



            /* ----------------------------------- FIN _Cursos ----------------------------------- */


            /* ----------------------------------- _ComunidadEducativa ----------------------------------- */


            $OrganizationPersonRoleId = 0;
            $K12StudentEnrollmentId = 0;
            $StaffEmploymentId = 0;
            $RoleAttendanceId = 0;
            $RoleStatusId = 0;
            $K12StudentSessionId = 0;
            $RoleAttendanceEventId = 0;
            $ActivityRecognitionId = 0;
            $RefEmployedWhileEnrolledId = 0;
            $K12StudentEnrollment = array();
            $_ComunidadEducativa = array();


            // Grados
            $RefEntryGradeLevelId = array(
                1 => 1,  // Sala Cuna
                2 => 1,
                3 => 1,
                4 => 3,  // 1er nivel de transicion ( Pre-kinder)
                5 => 5,  // 2do nivel de transicion ( kinder)
                6 => 6,  // 1ro basico
                7 => 7,  // 2do basico
                8 => 8,  // 3ro basico
                9 => 9,  // 4to basico
                10 => 10,  // 5to basico
                11 => 11,  // 6to basico
                12 => 12,  // 7mo basico
                13 => 13,  // 8vo basico
                127 => 14,  // 1ro medio
                135 => 14,
                142 => 14,
                149 => 14,
                156 => 14,
                163 => 14,
                170 => 14,
                128 => 15,  // 2do medio
                136 => 15,
                143 => 15,
                150 => 15,
                157 => 15,
                164 => 15,
                171 => 15,
                176 => 15,
                129 => 16,  // 3ro medio
                137 => 16,
                144 => 16,
                151 => 16,
                158 => 16,
                165 => 16,
                172 => 16,
                177 => 16,
                130 => 17,  // 4to medio
                138 => 17,
                145 => 17,
                152 => 17,
                159 => 17,
                166 => 17,
                173 => 17,
                178 => 17

            );


            // Roles
            $roles = array(
                'Director' => 1,
                'Jefe_Utp'=>2,
                'Inspector' => 3,
                'Profesor_Jefe' => 4,
                'Docente' => 5,
                'Estudiante' => 6,
            );


            // Asistencia
            $present = array(
                13288 => 2,
                13289 => 3,
                13290 => 1,
                13291 => 4,
                13292 => 5
            );

            $absent = array(
                13293 => 6,
                13294 => 7,
                13295 => 4,
                13296 => 3,
                13297 => 1,
                13298 => 5,
                13299 => 2,
                13300 => 9,
                13301 => 11,
                13302 => 10,
                13303 => 8
            );


            // Cursos
            $cursoComunidadE = $modelJson->listarCursosJSON($idperiodo, $idestablecimiento);
            $largoce = count($cursoComunidadE);
            for ($ce = 0; $ce < $largoce; $ce++) {


                // K12StaffEmployment
                $_ComunidadEducativa[$ce]['K12StaffEmployment'] = array(

                    // 'StaffEmploymentId' => $StaffEmploymentId,
                    // 'RefK12StaffClassificationId' => $RefK12StaffClassificationId,
                    // 'RefEmploymentStatusId' => '',
                    // 'ContractDaysOfServicePerYear' => '',
                    // 'StaffCompensationBaseSalary' => '',
                    // 'StaffCompensationRetirementBenefits' => '',
                    // 'StaffCompensationHealthBenefits' => '',
                    // 'StaffCompensationOtherBenefits' => '',
                    // 'StaffCompensationTotalBenefits' => '',
                    // 'StaffCompensationTotalSalary' => '',
                    // 'MepPersonnelIndicator' => '',
                    // 'TitleITargetedAssistanceStaffFunded' => '',
                    // 'SalaryForTeachingAssignmentOnlyIndicator' => '',

                );

                //si corresponde a junaeb
//             if ($alumno->junaeb == 1) {
//                 $RefFoodServiceEligibilityId = ['Code' => 'Free'];
//             } else {
//                 $RefFoodServiceEligibilityId = [];
//             }
//
//             if ($alumno->idEstadoActual == 4) {
//                 $RefExitGradeLevel = ['Code' => $RefEntryGradeLevelId[$alumno->idCodigoGrado]];
//             } else {
//                 $RefExitGradeLevel = [];
//             }


                // K12StudentEnrollment
                $_ComunidadEducativa[$ce]['K12StudentEnrollment'] = array(

//                'OrganizationPersonRoleId' => $OrganizationPersonRoleId,
//                'RefEntryGradeLevelId' => $RefEntryGradeLevelId[$alumno->idCodigoGrado],
//                // 'RefPublicSchoolResidence'=> '',
//                'RefEnrollmentStatusId' => [
//                    'Code' => '01811',
//                    'Description' => 'Currently enrolled'
//                ],
//                // 'RefEntryType'=> '',
//                'RefExitGradeLevel' => $RefExitGradeLevel,
//                // 'RefExitOrWithdrawalStatusId'=> '',//Permanent-Temporary (Una indicaci�n de si una instancia de salida / retirada de estudiantes se considera de naturaleza permanente o temporal.)
//                // 'RefExitOrWithdrawalTypeId'=> '',//Las circunstancias bajo las cuales el estudiante sali� de la membres�a en una instituci�n educativa.
//                // 'DisplacedStudentStatus'=> '',//Un estudiante que se inscribi�, o que es elegible para la inscripci�n, pero se ha inscrito en otro lugar debido a una crisis.
//                // 'RefEndOfTermStatusId'=> '', // Si Repitio el periodo anterior
//                // 'RefPromotionReasonId'=> '', // La raz�n por la cual un alumno paso de curso
//                // 'RefNonPromotionReasonId'=> '', // La raz�n por la cual un alumno no paso de curso
//
//                'RefFoodServiceEligibilityId' => $RefFoodServiceEligibilityId,//Una indicaci�n del nivel de elegibilidad de un estudiante para participar en el Programa Nacional de Almuerzos Escolares para los programas de desayuno, almuerzo, merienda, cena y leche.
                    // 'FirstEntryDateIntoUSSchool'=> '',//El a�o, mes y d�a de la inscripci�n inicial de una persona en una escuela de los Estados Unidos.
                    // 'RefDirectoryInformationBlockStatusId'=> '',
                    // 'NSLPDirectCertificationIndicator'=> '',
                    // 'RefStudentEnrollmentAccessTypeId'=> ''
                );


                // K12StudentAcademicHonor
                $_ComunidadEducativa[$ce]['K12StudentAcademicHonor'] = array(

                    // 'K12StudentAcademicHonorId' => $K12StudentAcademicHonorId,
                    // 'OrganizationPersonRoleId' => $OrganizationPersonRoleId
                    // 'RefAcademicHonorTypeId' => '',
                    // 'HonorDescription' => ''

                );


                // StaffEmployment
                $_ComunidadEducativa[$ce]['StaffEmployment'] = array(

                    // 'StaffEmploymentId' => $StaffEmploymentId,
                    // 'OrganizationPersonRoleId' => '',
                    // 'HireDate' => '',
                    // 'PositionTitle' => '',
                    // 'RefEmploymentSeparationTypeId' => '',
                    // 'RefEmploymentSeparationReasonId' => '',
                    // 'UnionMembershipName' => '',
                    // 'WeeksEmployedPerYear' => ''

                );


                // K12StaffAssignment
                $_ComunidadEducativa[$ce]['K12StaffAssignment'] = array(

                    // 'OrganizationPersonRoleId' => '',
                    // 'RefK12StaffClassificationId' => $RefK12StaffClassificationId,
                    // 'RefProfessionalEducationJobClassificationid' => '',
                    // 'RefTeachingAssignmentRoleId' => '',
                    // 'PrimaryAssignment' => '',
                    // 'TeacherOfRecord' => '',
                    // 'RefClassroomPositionTypeId' => '',
                    // 'FullTimeEquivalency' => '',
                    // 'ContributionPercentage' => '',
                    // 'ItinerantTeacher' => '',
                    // 'HighlyQualifiedTeacherIndicator' => '',
                    // 'SpecialEducationTeacher' => '',
                    // 'RefSpecialEducationStaffCategoryId' => '',
                    // 'SpecialEducationRelatedServicesPersonnel' => '',
                    // 'SpecialEducationParaprofessional' => '',
                    // 'RefSpecialEducationAgeGroupTaughtId' => '',
                    // 'RefMepStaffCategoryId' => '',
                    // 'RefTitleIProgramStaffCategoryId' => ''
                );


                // K12StudentCourseSection
                $_ComunidadEducativa[$ce]['K12StudentCourseSection'] = array(

                    // 'OrganizationPersonRoleId' => $OrganizationPersonRoleId,
                    // 'RefCourseRepeatCodeId' => '',
                    // 'RefCourseSectionEnrollmentStatusTypeId' => '',
                    // 'RefCourseSectionEntryTypeId' => '',
                    // 'RefCourseSectionExitTypeId' => '',
                    // 'RefExitOrWithdrawalStatusId' => '',
                    // 'RefGradeLevelWhenCourseTakenId' => '',
                    // 'GradeEarned' => '',
                    // 'GradeValueQualifier' => '',
                    // 'NumberOfCreditsAttempted' => '',
                    // 'RefCreditTypeEarnedId' => '',
                    // 'RefAdditionalCreditTypeId' => '',
                    // 'RefPreAndPostTestIndicatorId' => '',
                    // 'RefProgressLevelId' => '',
                    // 'RefCourseGpaApplicabilityId' => '',
                    // 'NumberOfCreditsEarned' => '',
                    // 'TuitionFunded' => '',
                    // 'ExitWithdrawalDate' => ''

                );


                // K12StudentAcademicRecord
                $_ComunidadEducativa[$ce]['K12StudentAcademicRecord'] = array(

                    // 'OrganizationPersonRoleId' => $OrganizationPersonRoleId,
                    // 'CreditsAttemptedCumulative' => '',
                    // 'CreditsEarnedCumulative' => '',
                    // 'GradePointsEarnedCumulative' => '',
                    // 'GradePointAverageCumulative' => '',
                    // 'RefGpaWeightedIndicatorId' => '',
                    // 'ProjectedGraduationDate' => '',
                    // 'HighSchoolStudentClassRank' => '',
                    // 'ClassRankingDate' => '',
                    // 'TotalNumberInClass' => '',
                    // 'DiplomaOrCredentialAwardDate' => '',
                    // 'RefHighSchoolDiplomaTypeId' => '',
                    // 'RefHighSchoolDiplomaDistinctionTypeId' => '',
                    // 'RefTechnologyLiteracyStatusId' => '',
                    // 'RefPsEnrollmentActionId' => '',
                    // 'RefPreAndPostTestIndicatorId' => '',
                    // 'RefProfessionalTechnicalCredentialTypeId' => '',
                    // 'RefProgressLevelId' => ''

                );


                // RoleAttendance
                $_ComunidadEducativa[$ce]['RoleAttendance'] = array(

                    // 'RoleAttendanceId' => $RoleAttendanceId,
                    // 'OrganizationPersonRoleId' => $OrganizationPersonRoleId,
                    // 'NumberOfDaysInAttendance' => '', // Asistencia
                    // 'NumberOfDaysAbsent' => '', // Inasistencia
                    // 'AttendanceRate' => ''

                );


                // RoleStatus
                $_ComunidadEducativa[$ce]['RoleStatus'] = array(

                    // 'RoleStatusId' => $RoleStatusId,
                    // 'StatusStartDate' => '',
                    // 'StatusEndDate' => '',
                    // 'RefRoleStatusId' => '',
                    // 'OrganizationPersonRoleId' => $OrganizationPersonRoleId

                );


                // K12StudentSession
                $_ComunidadEducativa[$ce]['K12StudentSession'] = array(

                    // 'K12StudentSessionId' => $K12StudentSessionId,
                    // 'OrganizationPersonRoleId' => $OrganizationPersonRoleId,
                    // 'OrganizationCalendarSessionId' => '',
                    // 'GradePointAverageGivenSession' => ''

                );


                // ServicesReceived
                $_ComunidadEducativa[$ce]['ServicesReceived'] = array(

                    // 'ServicesReceivedId' => $ServicesReceivedId,
                    // 'OrganizationPersonRoleId' => $OrganizationPersonRoleId,
                    // 'FullTimeEquivalency' => '',
                    // 'ServicePlanId' => ''

                );


                // OrganizationPersonRole
                $_ComunidadEducativa[$ce]['OrganizationPersonRole'] = array(

//                'OrganizationPersonRoleId'  => $OrganizationPersonRoleId,
//                'OrganizationId'  => $OrganizationId, // Asignatura
//                'PersonId'  => $PersonId, // Alumno
//                'RoleId'  => '',    //obligatorio int
//                'EntryDate'  => '', // Horario de Entrada
//                'ExitDate'  => ''   // Horario de Salida

                );


                // RoleAttendanceEvent
                $_ComunidadEducativa[$ce]['RoleAttendanceEvent'] = array(

                    // 'RoleAttendanceEventId' => '',
                    // 'OrganizationPersonRoleId' => $OrganizationPersonRoleId, // col=50
                    // 'Date' => '',
                    // 'RefAttendanceEventTypeId' => 'DailyAttendance', // col=50
                    // 'RefAttendanceStatusId' => '',
                    // 'RefAbsentAttendanceCategoryId' => '',
                    // 'RefPresentAttendanceCategoryId' => '',
                    // 'RefPresentAttendanceCategoryId' => '',

                );


                // Role
                $_ComunidadEducativa[$ce]['Role'] = array(
                    [
                        'RoleId' => $roles['Director'],
                        'Name' => 'Director',
                        'RefJurisdictionId' => 1
                    ],
                    [
                        'RoleId' => $roles['Docente'],
                        'Name' => 'Docente',
                        'RefJurisdictionId' => 1
                    ],
                    [
                        'RoleId' => $roles['Profesor_Jefe'],
                        'Name' => 'Profesor Jefe',
                        'RefJurisdictionId' => 1
                    ],
                    [
                        'RoleId' => $roles['Estudiante'],
                        'Name' => 'Estudiante',
                        'RefJurisdictionId' => 1
                    ]
                );


                // K12StudentEmployment
                $_ComunidadEducativa[$ce]['K12StudentEmployment'] = array(

                    // 'OrganizationPersonRoleId' => $OrganizationPersonRoleId,
                    // 'RefEmployedWhileEnrolledId' => $RefEmployedWhileEnrolledId,
                    // 'RefEmployedAfterExitId' => '',
                    // 'EmploymentNaicsCode' => ''

                );


                // ActivityRecognition
                $_ComunidadEducativa[$ce]['ActivityRecognition'] = array(

                    // 'ActivityRecognitionId' => '',
                    // 'OrganizationPersonRoleId' => '',
                    // 'RefActivityRecognitionTypeId' => ''

                );


                // Alumnos
                $alumnos = $modelJson->listarAlumnosCursoJSON($cursoComunidadE[$ce]['idCursos'], $idperiodo, $idestablecimiento);
                foreach ($alumnos as $keyAl => $alumno) {


                    // Alumno x Establecimiento
                    $OrganizationPersonRoleId++;
                    $_ComunidadEducativa[$ce]['OrganizationPersonRole'][] = [

                        'OrganizationPersonRoleId' => (Int)$OrganizationPersonRoleId,
                        'OrganizationId' => (Int)$establecimientosOrganizationRel[$alumno->idEstablecimiento]['OrganizationId'],
                        'PersonId' => $alumnosRel[$alumno->idAlumnos]['PersonId'],
                        'RoleId' => $roles['Estudiante'],
//                    'EntryDate'                => $alumno->fechaInscripcion,
                        // 'ExitDate' => ''

                    ];


                    // K12StudentEnrollment
                    //si corresponde a junaeb
                    if ($alumno->junaeb == 1) {
                        $RefFoodServiceEligibilityId = 1; // Ref Free
                    } else {
                        $RefFoodServiceEligibilityId = 4; // Ref Other
                    }

                    if ($alumno->idEstadoActual == 4) {
                        $RefExitGradeLevel = $RefEntryGradeLevelId[$alumno->idCodigoGrado];
                    } else {
                        $RefExitGradeLevel = 21;   // Ref Other
                    }

//                $K12StudentEnrollmentId++;
                    $_ComunidadEducativa[$ce]['K12StudentEnrollment'][] = array(

//                    'K12StudentEnrollmentId'        => $K12StudentEnrollmentId,
                        'OrganizationPersonRoleId' => $OrganizationPersonRoleId,
                        'RefEntryGradeLevelId' => $RefEntryGradeLevelId[$alumno->idCodigoGrado],
                        // 'RefPublicSchoolResidence'=> '',
                        'RefEnrollmentStatusId' => 2, // Ref 01811
                        // 'RefEntryType'=> '',
                        'RefExitGradeLevel' => $RefExitGradeLevel,
                        // 'RefExitOrWithdrawalStatusId'=> '',  // Permanent-Temporary (Una indicaci�n de si una instancia de salida / retirada de estudiantes se considera de naturaleza permanente o temporal.)
                        // 'RefExitOrWithdrawalTypeId'=> '',    // Las circunstancias bajo las cuales el estudiante sali� de la membres�a en una instituci�n educativa.
                        'DisplacedStudentStatus' => false,       // ??? Un estudiante que se inscribi�, o que es elegible para la inscripci�n, pero se ha inscrito en otro lugar debido a una crisis.
                        // 'RefEndOfTermStatusId'=> '',         // Si Repitio el periodo anterior
                        // 'RefPromotionReasonId'=> '',         // La raz�n por la cual un alumno paso de curso
                        // 'RefNonPromotionReasonId'=> '',      // La raz�n por la cual un alumno no paso de curso
                        'RefFoodServiceEligibilityId' => $RefFoodServiceEligibilityId,           // Una indicaci�n del nivel de elegibilidad de un estudiante para participar en el Programa Nacional de Almuerzos Escolares para los programas de desayuno, almuerzo, merienda, cena y leche.
                        // 'FirstEntryDateIntoUSSchool'=> '',   // El a�o, mes y d�a de la inscripci�n inicial de una persona en una escuela de los Estados Unidos.
                        // 'RefDirectoryInformationBlockStatusId'=> '',
                        'NSLPDirectCertificationIndicator' => false,
                        // 'RefStudentEnrollmentAccessTypeId'=> '',
                        'StudentListNumber' => $alumno->ordenAlumno // Numero de Lista

                    );


                    // Alumno x Curso
                    $OrganizationPersonRoleId++;
                    $_ComunidadEducativa[$ce]['OrganizationPersonRole'][] = [

                        'OrganizationPersonRoleId' => (Int)$OrganizationPersonRoleId,
                        'OrganizationId' => (Int)$cursosOrganizationRel[$alumno->idCursosActual]['OrganizationId'],
                        'PersonId' => $alumnosRel[$alumno->idAlumnos]['PersonId'],
                        'RoleId' => $roles['Estudiante'],
                        // 'EntryDate' => '',
                        // 'ExitDate' => ''

                    ];


                    // Asistencia Diaria x Alumno
                    $datosasistenciadiaria = $modelJson->listarAsistenciaDiariaAlumnoJSON($alumno['idAlumnos'], $idperiodo, $alumno['idCursosActual']);
                    foreach ($datosasistenciadiaria as $keydad => $asistenciaDiaria) {

                        $RoleAttendanceEventId++;

                        if ($asistenciaDiaria['valorasistencia'] == 2) {
                            $RefAttendanceStatusId = 1; // Ref Present
                            $RefAbsentAttendanceCategoryId = "";
                            $RefPresentAttendanceCategoryId = $present[$asistenciaDiaria['tipoAsistencia']];

                        } else if ($asistenciaDiaria['valorasistencia'] == 1) {
                            $RefAttendanceStatusId = 3; // Ref UnexcusedAbsence
                            $RefAbsentAttendanceCategoryId = $absent[$asistenciaDiaria['tipoAsistencia']];
                            $RefPresentAttendanceCategoryId = "";

                        }

                        $_ComunidadEducativa[$ce]['RoleAttendanceEvent'][] = array(

                            'RoleAttendanceEventId' => $RoleAttendanceEventId,
                            'OrganizationPersonRoleId' => $OrganizationPersonRoleId,
                            'Date' => $asistenciaDiaria['fechaControl'],
                            'RefAttendanceEventTypeId' => 1, // Ref DailyAttendance
                            'RefAttendanceStatusId' => $RefAttendanceStatusId,
                            'RefAbsentAttendanceCategoryId' => $RefAbsentAttendanceCategoryId,
                            'RefPresentAttendanceCategoryId' => $RefPresentAttendanceCategoryId,

                        );
                    }


                    // Alumno - Asignatura
                    $alumnosAsignatura = $modelJson->listarAlumnosAsignaturaPorCursoJSON($alumno['idAlumnosActual'], $alumno['idCursosActual'], $idperiodo, $idestablecimiento);
                    foreach ($alumnosAsignatura as $keyAlAs => $alumnoAsignatura) {

                        $OrganizationPersonRoleId++;
                        $_ComunidadEducativa[$ce]['OrganizationPersonRole'][] = [

                            'OrganizationPersonRoleId' => (Int)$OrganizationPersonRoleId,
                            'OrganizationId' => (Int)$asignaturasOrganizationRel[$alumnoAsignatura->idAsignatura]['OrganizationId'],
                            'PersonId' => $alumnosRel[$alumno->idAlumnos]['PersonId'],
                            'RoleId' => $roles['Estudiante'],
                            // 'EntryDate' => '',
                            // 'ExitDate' => ''

                        ];

                        $refAlumnoAsignaturaRoleId[$alumno->idAlumnos . '' . $alumnoAsignatura->idAsignatura] = array(
                            'OrganizationPersonRole' => $OrganizationPersonRoleId
                        );


                        $asistenciasBloque = $modelJson->listarAsistenciaBloqueAlumnoJSON($alumno['idAlumnos'], $idperiodo, $alumno['idCursosActual'], $alumnoAsignatura['idAsignatura']);

                        foreach ($asistenciasBloque as $keydad => $asistenciaBloque) {

                            $RoleAttendanceEventId++;

                            if ($asistenciaDiaria['valorasistencia'] == 2) {
                                $RefAttendanceStatusId = 1; // Ref Present
                                $RefAbsentAttendanceCategoryId = "";
                                $RefPresentAttendanceCategoryId = $present[$asistenciaDiaria['tipoAsistencia']];

                            } else if ($asistenciaDiaria['valorasistencia'] == 1) {
                                $RefAttendanceStatusId = 3; // Ref UnexcusedAbsence
                                $RefAbsentAttendanceCategoryId = $absent[$asistenciaDiaria['tipoAsistencia']];
                                $RefPresentAttendanceCategoryId = "";

                            }


                            $_ComunidadEducativa[$ce]['RoleAttendanceEvent'][] = array(

                                'RoleAttendanceEventId' => $RoleAttendanceEventId,
                                'OrganizationPersonRoleId' => $OrganizationPersonRoleId,
                                'Date' => $asistenciaBloque['fechaControl'],
                                'RefAttendanceEventTypeId' => 2, // Ref ClassSectionAttendance
                                'RefAttendanceStatusId' => $RefAttendanceStatusId,
                                'RefAbsentAttendanceCategoryId' => $RefAbsentAttendanceCategoryId,
                                'RefPresentAttendanceCategoryId' => $RefPresentAttendanceCategoryId,

                            );


                        }


                    }

                }


                // DOCENTES

                $docentes = $modelJson->listarDocentesPorCursosJSON($idperiodo, $idestablecimiento,array($cursoComunidadE[$ce]['idCursos']));


                foreach ($docentes as $keyD => $docente) {

                    $PersonId = $docentesRel[$docente->idCuenta]['PersonId'];
                    // Docente x Establecimiento
                    $OrganizationPersonRoleId++;
                    $_ComunidadEducativa[$ce]['OrganizationPersonRole'][] = [
                        'OrganizationPersonRoleId' => (Int)$OrganizationPersonRoleId,
                        'OrganizationId' => (Int)$establecimientosOrganizationRel[$docente->idEstablecimiento]['OrganizationId'],
                        'PersonId' => $PersonId,
                        'RoleId' => $roles['Docente'],
                        // 'EntryDate' => '',
                        // 'ExitDate' => ''
                    ];


                    // K12StaffAssignment
                    $OrganizationPersonRoleId++;


//                RefK12StaffClassificationId
//                ELAssistantTeachers   = Maestros Asistentes
//                ElementaryTeachers    = Maestros de primaria
//                ELTeachers            = Maestros De Jardín De Infancia
//                SecondaryTeachers     = Maestros Secundarios

                    $RefTeachingAssignmentRoleId = 2; // Ref Docente de Asignatura
                    $PrimaryAssignment = True;

                    if ($docente->idCuenta == $docente->idCuentaJefe) {
                        $RefTeachingAssignmentRoleId = 1; // Ref Profesor jefe
                        $PrimaryAssignment = False;
                    }

                    $_ComunidadEducativa[$ce]['K12StaffAssignment'][] = array(

                        'OrganizationPersonRoleId' => (Int)$OrganizationPersonRoleId,
                        'RefK12StaffClassificationId' => 7, // Ref ElementaryTeachers
                        // 'RefProfessionalEducationJobClassificationid' => '',
                        'RefTeachingAssignmentRoleId' => $RefTeachingAssignmentRoleId,
                        'PrimaryAssignment' => $PrimaryAssignment,  // ???  Una indicación de si la asignación es la asignación principal del miembro del personal.
                        'TeacherOfRecord' => true,            // ??? Miembro del personal que tiene la responsabilidad de un maestro de registro para una sección de clase basada en la definición del estado de maestro de registro.
                        // 'RefClassroomPositionTypeId' => '',
                        'FullTimeEquivalency' => 1,               // ??? La relación entre las horas de trabajo esperadas en un puesto y las horas de trabajo normalmente esperadas en un puesto de tiempo completo en el mismo entorno.
                        'ContributionPercentage' => (Float)1.1,      // ??? Un porcentaje utilizado para ponderar la responsabilidad asignada del educador para el aprendizaje del alumno en una Sección de la Clase, particularmente cuando se asigna más de un educador a la sección de la clase.
                        'ItinerantTeacher' => false,           // ??? Una indicación de si un maestro brinda instrucción en más de un sitio de instrucción.
                        'HighlyQualifiedTeacherIndicator' => false,         // ??? Una indicación de que el maestro ha sido clasificado como altamente calificado en función de la asignación.
                        'SpecialEducationTeacher' => false,           // ??? Una indicación de si un maestro está empleado o contratado para trabajar con niños con discapacidades que tienen entre 3 y 21 años.
                        // 'RefSpecialEducationStaffCategoryId' => '',
                        'SpecialEducationRelatedServicesPersonnel' => false,// ??? Una indicación de si una persona de servicios relacionados está empleada o contratada para trabajar con niños con discapacidades de entre 3 y 21 años.
                        'SpecialEducationParaprofessional' => false,        // ??? Una indicación de si un paraprofesional está empleado o contratado para trabajar con niños con discapacidades de entre 3 y 21 años.
                        // 'RefSpecialEducationAgeGroupTaughtId' => '',
                        // 'RefMepStaffCategoryId' => '',
                        // 'RefTitleIProgramStaffCategoryId' => ''

                    );

                    // Docente - Cursos
                    $_ComunidadEducativa[$ce]['OrganizationPersonRole'][] = [
                        'OrganizationPersonRoleId' => (Int)$OrganizationPersonRoleId,
                        'OrganizationId' => (Int)$cursosOrganizationRel[$cursoComunidadE[$ce]->idCursos]['OrganizationId'],
                        'PersonId' => $PersonId,
                        'RoleId' => $roles['Docente'],
                        // 'EntryDate' => '',
                        // 'ExitDate' => ''
                    ];

                    // Docente - Asignaturas
                    $asignaturasDoc = $modelJson->listarAsignaturasDocenteJSON($cursoComunidadE[$ce]['idCursos'], $docente['idCuenta'], $idperiodo, $idestablecimiento);

                    foreach ($asignaturas as $key => $asignatura) {

                        $OrganizationPersonRoleId++;

                        $_ComunidadEducativa[$ce]['OrganizationPersonRole'][] = [

                            'OrganizationPersonRoleId' => (Int)$OrganizationPersonRoleId,
                            'OrganizationId' => (Int)$asignaturasOrganizationRel[$asignatura->idAsignatura]['OrganizationId'],
                            'PersonId' => $PersonId,
                            'RoleId' => $roles['Docente'],
//                         'EntryDate'                => '',
//                         'ExitDate'                 => ''

                        ];

                        $refDocenteAsignaturaRoleId[$docente->idCuenta . '' . $asignatura->idAsignatura] = array(
                            'OrganizationPersonRole' => $OrganizationPersonRoleId
                        );


                    }
                }
            }


            /* ----------------------------------- FIN _ComunidadEducativa ----------------------------------- */


            /* ----------------------------------- _Incidentes ----------------------------------- */

            // Observaciones
            $observaciones = $modelJson->listarObservacionesJSON($idperiodo, $idestablecimiento);

            $IncidentId = 0;
            $K12StudentDisciplineId = 0;
            $_Incidentes = array();
            $_Incidentes[0]['Incident'][] = array(

                'IncidentId' => '',
                'IncidentIdentifier' => '',
                'IncidentDate' => '',
                'IncidentTime' => '',
                'RefIncidentTimeDescriptionCodeId' => '',
                'IncidentDescription' =>'',
                'RefIncidentBehaviorId' => '',
                'RefIncidentInjuryTypeId' => '',
                'RefWeaponTypeId' => '',
                'IncidentCost' => '',
                'OrganizationPersonRoleId' => '',
                'IncidentReporterId' => '',
                'RefIncidentReporterTypeId' => '',
                'RefIncidentLocationId' => '',
                'RefFirearmTypeId' => '',
                'RegulationViolatedDescription' => '',
                'RelatedToDisabilityManifestationInd' => '',
                'ReportedToLawEnforcementInd' => '',
                'RefIncidentMultipleOffenseTypeId' => '',
                'RefIncidentPerpetratorInjuryTypeId' => ''

            );


            $_Incidentes[0]['K12StudentDiscipline'][] = array(

                'K12StudentDisciplineId' => null,
                'OrganizationPersonRoleId' => null,
                // 'RefDisciplineReasonId' => '',
                // 'RefDisciplinaryActionTakenId' => '',
                // 'DisciplinaryActionStartDate' => '',
                // 'DisciplinaryActionEndDate' => '',
                'DurationOfDisciplinaryAction' => null, //La duración, en días escolares, de la acción disciplinaria.
                // 'RefDisciplineLengthDifferenceReasonId' => '',
                'FullYearExpulsion' => null,
                'ShortenedExpulsion' => null, // Una expulsión con o sin servicios que el superintendente o administrador principal de un distrito escolar acorta a un término de menos de un año.
                'EducationalServicesAfterRemoval' => null, //Una indicación de si los niños (estudiantes) recibieron servicios educativos cuando fueron retirados del programa escolar regular por razones disciplinarias.
                // 'RefIdeaInterimRemovalId' => '',
                // 'RefIdeaInterimRemovalReasonId' => '',
                'RelatedToZeroTolerancePolicy' => null, // Una indicación de si alguna de las acciones disciplinarias tomadas contra un estudiante se impusieron como consecuencia de las políticas estatales o locales de tolerancia cero.
                'IncidentId' => null,
                'IEPPlacementMeetingIndicator' => null, // Una indicación de que el oficial de recursos escolares o cualquier otro oficial de la ley fue notificado sobre el incidente, independientemente de si se toman medidas oficiales.
                // 'RefDisciplineMethodFirearmsId' => '',
                // 'RefDisciplineMethodOfCwdId' => '',
                // 'RefIDEADisciplineMethodFirearmId' => ''

            );


            // FALTAN
            $_Incidentes[0]['IncidentPerson'][] = array(

                'IncidentId' => null,
                'PersonId' => null,
                'Identifier' => null,
                'RefIncidentPersonRoleTypeId' => null, // Ref Perpetrator
                'RefIncidentPersonTypeId' => null  // Ref 04710

            );

            $largo = count($observaciones);
            for ($ob = 0; $ob < $largo; $ob++) {

                $IncidentId = $observaciones[$ob]->idObservaciones;
                $K12StudentDisciplineId++;

                // Incident
                $IncidentIdentifier = substr($observaciones[$ob]->idObservaciones, 0, 40);

                $IncidentDate = $observaciones[$ob]->fechaObservacion; //FORMATEAR

                $IncidentDescription = $observaciones[$ob]->observacion;

                $OrganizationPersonRoleIdAlumno = $refAlumnoAsignaturaRoleId[$observaciones[$ob]->idAlumnos . '' . $observaciones[$ob]->idAsignatura]['OrganizationPersonRole'];

                $OrganizationPersonRoleIdDocente = $refDocenteAsignaturaRoleId[$observaciones[$ob]->idCuenta . '' . $observaciones[$ob]->idAsignatura]['OrganizationPersonRole'];


                $_Incidentes[$ob]['Incident'][] = array(

                    'IncidentId' => (integer)$IncidentId,
                    'IncidentIdentifier' => $IncidentIdentifier,
                    'IncidentDate' => $IncidentDate,
                    // 'IncidentTime' => '',
                    // 'RefIncidentTimeDescriptionCodeId' => '',
                    'IncidentDescription' => $IncidentDescription,
                    // 'RefIncidentBehaviorId' => '',
                    // 'RefIncidentInjuryTypeId'  => '',
                    // 'RefWeaponTypeId' => '',
                    // 'IncidentCost' => '',

                    'OrganizationPersonRoleId' => (Int)$OrganizationPersonRoleIdDocente,
                    'IncidentReporterId' => $observaciones[$ob]->idObservaciones,
                    // 'RefIncidentReporterTypeId' => '',
                    // 'RefIncidentLocationId' => '',
                    // 'RefFirearmTypeId' => '',
                    // 'RegulationViolatedDescription' => '',

                    'RelatedToDisabilityManifestationInd' => false,
                    'ReportedToLawEnforcementInd' => true,

                    // 'RefIncidentMultipleOffenseTypeId' => '',
                    // 'RefIncidentPerpetratorInjuryTypeId' => ''

                );

                // FALTAN

                $_Incidentes[$ob]['K12StudentDiscipline'][] = array(

                    'K12StudentDisciplineId' => (integer)$K12StudentDisciplineId,
                    'OrganizationPersonRoleId' => $OrganizationPersonRoleIdAlumno, //
                    // 'RefDisciplineReasonId' => '',
                    // 'RefDisciplinaryActionTakenId' => '',
                    // 'DisciplinaryActionStartDate' => '',
                    // 'DisciplinaryActionEndDate' => '',
                    'DurationOfDisciplinaryAction' => (Float)0.1, //La duración, en días escolares, de la acción disciplinaria.
                    // 'RefDisciplineLengthDifferenceReasonId' => '',
                    'FullYearExpulsion' => false,
                    'ShortenedExpulsion' => false, // Una expulsión con o sin servicios que el superintendente o administrador principal de un distrito escolar acorta a un término de menos de un año.
                    'EducationalServicesAfterRemoval' => false, //Una indicación de si los niños (estudiantes) recibieron servicios educativos cuando fueron retirados del programa escolar regular por razones disciplinarias.
                    // 'RefIdeaInterimRemovalId' => '',
                    // 'RefIdeaInterimRemovalReasonId' => '',
                    'RelatedToZeroTolerancePolicy' => false, // Una indicación de si alguna de las acciones disciplinarias tomadas contra un estudiante se impusieron como consecuencia de las políticas estatales o locales de tolerancia cero.
                    'IncidentId' => (integer)$IncidentId,
                    'IEPPlacementMeetingIndicator' => true, // Una indicación de que el oficial de recursos escolares o cualquier otro oficial de la ley fue notificado sobre el incidente, independientemente de si se toman medidas oficiales.
                    // 'RefDisciplineMethodFirearmsId' => '',
                    // 'RefDisciplineMethodOfCwdId' => '',
                    // 'RefIDEADisciplineMethodFirearmId' => ''

                );

                $PersonId = $alumnosRel[$observaciones[$ob]->idAlumnos]['PersonId'];

                // FALTAN
                $_Incidentes[$ob]['IncidentPerson'][] = array(

                    'IncidentId' => (integer)$IncidentId,
                    'PersonId' => $PersonId,
                    'Identifier' => $observaciones[$ob]->rutAlumno,
                    'RefIncidentPersonRoleTypeId' => 2, // Ref Perpetrator
                    'RefIncidentPersonTypeId' => 9  // Ref 04710

                );

            }

            /* ----------------------------------- Fin _Incidentes ----------------------------------- */


            /* ----------------------- JSON ----------------------- */


            $_JSON = [

                '$schema' => "http://json-schema.org/draft-07/schema#",
                "title" => "LCD",
                "description" => "Esquema del lilbro de clases electrónico",
                "type" => "object",
                // "properties" =>
                "_Personas" => $_Personas,
                "_Organizaciones" => $_Organizaciones,
                "_Establecimientos" => $_Establecimientos,
                "_Calendarios" => $_Calendarios,
                "_Incidentes" => $_Incidentes,
                "_Intervenciones" => $_Intervenciones,
                "_Cursos" => $_Cursos,
                "_ComunidadEducativa" => $_ComunidadEducativa

            ];

            //Zend_Debug::dump($_JSON);


            $jsonencoded = json_encode($_JSON, JSON_UNESCAPED_UNICODE);

            $direccion = fopen("ArchivoPrueba_1.json", 'x+');
            fwrite($direccion, $jsonencoded);
            fclose($direccion);


            /* --------------------- FIN JSON --------------------- */
        }
    }

    public function errorAction()
    {
        // action body
    }
}


//ERROR: (pyodbc.DataError) ('22001', '[22001] [Microsoft][ODBC Driver 17 for SQL Server][SQL Server]
//String or binary data would be truncated. (8152) (SQLExecDirectW); [22001] [Microsoft]
//[ODBC Driver 17 for SQL Server][SQL Server]The statement has been terminated. (3621)')
//[SQL: INSERT INTO [OrganizationCalendarEvent] ([OrganizationCalendarEventId], [OrganizationCalendarId], [Name], [EventDate], [RefCalendarEventType]) VALUES (?, ?, ?, ?, ?)]
//[parameters: ((2, 2, 'A�o Nuevo', '2019-01-01', 2), (3, 2, 'Viernes Santo', '2019-04-19', 2),
//(4, 2, 'S�bado Santo', '2019-04-20', 2), (5, 2, 'D�a Nacional del Trabajo', '2019-05-01', 2),
//(6, 2, 'D�a de las Glorias Navales', '2019-05-21', 2), (7, 2, 'San Pedro y San Pablo', '2019-06-29', 2),
//(8, 2, 'D�a de la Virgen del Carmen', '2019-07-16', 2), (9, 2, 'Asunci�n de la Virgen', '2019-08-15', 2)
//... displaying 10 of 64 total bound parameter sets ...  (64, 5, 'Inmaculada Concepci�n', '2019-12-08', 2),
//(65, 5, 'Navidad', '2019-12-25', 2))]
//(Background on this error at: http://sqlalche.me/e/9h9h)
