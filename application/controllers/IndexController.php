<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {

        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();
    }


    public function indexAction()
    {

        $cargo = new Zend_Session_Namespace('cargo');
        $rol = $cargo->cargo;

        // rol = 3 (director)
        if ($rol == 3) {

            $est = new Zend_Session_Namespace('establecimiento');
            $idestablecimiento = $est->establecimiento;

            $periodo = new Zend_Session_Namespace('periodo');
            $idperiodo = $periodo->periodo;

            $tablacomunas = new Application_Model_DbTable_Comuna();
            $modeloperiodo = new Application_Model_DbTable_Periodo();
            $modelJson = new Application_Model_DbTable_Json();

            $datosperiodo = $modeloperiodo->nombreperiodo($idperiodo);
            $dias = array(
                '1' => 'Lunes',
                '2' => 'Martes',
                '3' => 'Miércoles',
                '4' => 'Jueves',
                '5' => 'Viernes'
            );


            /* ----------------------- _Auditoria ----------------------- */

//            $auditorias = $modelJson->listarAuditoriaJSON($idperiodo, $idestablecimiento);
//
//
//            $largo = count($auditorias);
//            for ($audi = 0; $audi < $largo; $audi++) {
//
//                $largoAu = count($auditorias[$audi]['auditoria']);
//                for ($aud = 0; $aud < $largoAu; $aud++) {
//
//                    $auditoria = $auditorias[$audi]['auditoria'];
//
//                    $_Auditorias[$aud] = array(
//
//                        'RUT' => $auditorias[$audi]['RUT'],    # NUMBER
//                        'DateTimeWithTimeZone' => '',                           # STRING
//                        'Token' => $auditoria[$aud]['token'],          # NUMBER
//                        'TableName' => $auditoria[$aud]['TableName'],      # OBJETO
//                        'FieldName' => $auditoria[$aud]['FieldName'],      # OBJETO
//                        'OperationType' => $auditoria[$aud]['OperationType'],  # STRING # ENUM: INSERT - UPDATE - DELETE
//                        'OldData' => $auditoria[$aud]['OldData'],        # STRING
//                        'NewData' => $auditoria[$aud]['NewData']         # STRING
//                    );
//
//                }
//
//            }


            /* ----------------------- Fin _Auditoria ----------------------- */


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
            $numeromatricula = 0;

            $regiones = array(
                1 => 63,
                2 => 64,
                3 => 65,
                4 => 66,
                5 => 67,
                6 => 68,
                7 => 69,
                8 => 70,
                9 => 71,
                10 => 72,
                11 => 73,
                12 => 74,
                13 => 75,
                14 => 76,
                15 => 77,
                16 => 78
            );
            $RefCountryId = 45; // CL

            // 1000 = Alumnos
            // 1001 = Docentes
            // 1002 = Apoderado Principal
            // 1003 = Apoderado Secundario

            $cursos = $modelJson->listarCursosJSON($idperiodo, $idestablecimiento);

            $lista_curso = array();
            foreach ($cursos as $item) {
                $lista_curso[] = $item['idCursos'];
            }

            $alumnos = $modelJson->listarAlumnosJSON($idperiodo, $idestablecimiento, $lista_curso);

            $largo = count($alumnos);
            for ($i = 0; $i < $largo; $i++) {

                // AUTOINCREMENTS
                $PersonId = intval('1000' . $alumnos[$i]->idAlumnosActual);
                $PersonIdentifierId++;
                $PersonAddressId++;
                $PersonEmailAddressId++;

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
                $Birthdate = $alumnos[$i]->fechanacimiento;
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

                $_Personas[$i]['Person'][] = array(

                    'PersonId' => $PersonId,
                    'FirstName' => $FirstName,
                    'MiddleName' => $MiddleName,
                    'LastName' => $LastName,
                    'SecondLastName' => $SecondLastName,
                    // 'GenerationCode' => '',
                    // 'Prefix' => '',
                    'Birthdate' => $Birthdate, //Falta formatear
                    'RefSexId' => $RefSexId,
                    'HispanicLatinoEthnicity' => false, // ETNIA HISPANA
                    // 'RefUSCitizenshipStatusId' => '',// Referencia si es ciudadano de USA, al ser "Permanent resident"
                    //  es ciudadano permanente de usa?.
                    // 'RefVisaTypeId' => '',// va de la mano con el anterior
                    // 'RefStateOfResidenceId' => '',// estado donde vive
                    // 'RefProofOfResidencyTypeId' => '',// prueba de residencia
                    // 'RefHighestEducationLevelCompletedId' => '',
                    // 'RefPersonalInformationVerificationId' => '',// Verificador de información personal
                    // 'BirthdateVerification' => '',// Verificador de cumpleaños
                    // 'RefTribalAffiliationId' => '' // entidad tribal nativa americana

                );


                // PersonIdentifier
                // RUT Alumno
                $Identifier = substr($alumnos[$i]->rutAlumno, 0, 40);

                $_Personas[$i]['PersonIdentifier'][] = array(

                    'PersonIdentifierId' => $PersonIdentifierId,
                    'PersonId' => $PersonId,
                    'Identifier' => $Identifier,
                    'RefPersonIdentificationSystemId' => 51, // Ref RUN
                    // 'RefPersonalInformationVerificationId'  => ''

                );

                // Matricula

                // if ($alumnos[$i]->numeromatricula != 0) {
                $PersonIdentifierId++;
                if ($alumnos[$i]->numeroMatricula != 0) {

                    $_Personas[$i]['PersonIdentifier'][] = array(

                        'PersonIdentifierId' => $PersonIdentifierId,
                        'PersonId' => $PersonId,
                        'Identifier' => $alumnos[$i]->numeroMatricula,
                        'RefPersonIdentificationSystemId' => 31, // Ref School (Matricula) tipo alumno
                        //'RefPersonalInformationVerificationId'  => ''
                    );

                } else {
                    $_Personas[$i]['PersonIdentifier'][] = array(

                        'PersonIdentifierId' => $PersonIdentifierId,
                        'PersonId' => $PersonId,
                        //'Identifier' => $alumnos[$i]->numeroMatricula,
                        'RefPersonIdentificationSystemId' => 31, // Ref School (Matricula) tipo alumno
                        //'RefPersonalInformationVerificationId'  => ''
                    );
                }

                // }


                // PersonAddress
                $comuna = $tablacomunas->getcomuna($alumnos[$i]->comunaActual);

                $StreetNumberAndName = substr($alumnos[$i]->direccion, 0, 40);
                $City = substr($comuna[0]->nombreComuna, 0, 30);

                $RefStateId = $regiones[$comuna[0]->idRegion]; //Region
                $AddressCountyName = substr($comuna[0]->nombreComuna, 0, 30);
                // $RefCountyId = (string)$comuna[0]->KeySI;


                // PersonBirthplace
                $_Personas[$i]['PersonBirthplace'][] = array(

                    'PersonId' => $PersonId,
                    'City' => $City,
                    'RefStateId' => $RefStateId,
                    'RefCountryId' => $RefCountryId

                );

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
                    // 'RefCountyId'     => $RefCountyId,
                    'RefCountryId' => $RefCountryId
                    // 'Latitude' => '',
                    // 'Longitude' => '',
                    // 'RefPersonalInformationVerificationId' => ''

                );


                // PersonEmailAddress

                if (count($alumnos[$i]->correo) == 0
                    || count($alumnos[$i]->correo) == 1
                ) {

                    $EmailAddress = 'alumno@gmail.com';

                } else {
                    $EmailAddress = substr($alumnos[$i]->correo, 0, 128);
                }

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
                if (count($alumnos[$i]->telefono) >= 6
                    || count($alumnos[$i]->celular) >= 8
                ) {

                    if (count($alumnos[$i]->telefono) >= 6) {
                        // TELÉFONO

                        $PersonTelephoneId++;
                        $TelephoneNumber = substr($alumnos[$i]->telefono, 0, 24);

                        $_Personas[$i]['PersonTelephone'][] = array(

                            'PersonTelephoneId' => $PersonTelephoneId,
                            'PersonId' => $PersonId,
                            'TelephoneNumber' => "+56" . $TelephoneNumber,
                            'PrimaryTelephoneNumberIndicator' => (Bool)True,
                            'RefPersonTelephoneNumberTypeId' => 1 // Ref Mobile

                        );

                    }

                    if (count($alumnos[$i]->celular) >= 8) {
                        // CELULAR

                        $PersonTelephoneId++;
                        $TelephoneNumber = substr($alumnos[$i]->celular, 0, 24);

                        $_Personas[$i]['PersonTelephone'][] = array(

                            'PersonTelephoneId' => $PersonTelephoneId,
                            'PersonId' => $PersonId,
                            'TelephoneNumber' => "+56" . $TelephoneNumber,
                            'PrimaryTelephoneNumberIndicator' => (Bool)True,
                            'RefPersonTelephoneNumberTypeId' => 3 // Ref Mobile

                        );

                    }

                } else {

                    $_Personas[$i]['PersonTelephone'] = array(

                        // 'PersonTelephoneId' => $PersonTelephoneId,
                        // 'PersonId'          => $PersonId,
                        // 'TelephoneNumber'   => "+56".$TelephoneNumber,
                        // 'PrimaryTelephoneNumberIndicator' => (Bool)True,
                        // 'RefPersonTelephoneNumberTypeId'  => 3 // Ref Mobile

                    );

                }

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


                $_Personas[$i]['PersonLanguage'] = array(

                    // 'PersonLanguageId' => $PersonLanguageId,
                    // 'PersonId' => $PersonId
                    // 'RefLanguageId' => '',
                    // 'RefLanguageUseTypeId' => ''

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
                // $PersonTelephoneId++;
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


                $comuna = $tablacomunas->getcomuna($docentes[$do]->comuna);

                $City = substr($comuna[0]->nombreComuna, 0, 30);
                $RefStateId = $regiones[$comuna[0]->idRegion]; //Region

                $AddressCountyName = substr($comuna[0]->nombreComuna, 0, 30);
                // $RefCountyId = (string)$comuna[0];
                $RefCountyId = '';


                // PersonBirthplace
                $_Personas[$d]['PersonBirthplace'][] = array(

                    'PersonId' => $PersonId,
                    'City' => $City,
                    'RefStateId' => $RefStateId,
                    'RefCountryId' => $RefCountryId

                );


                // PersonAddress
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
                    // 'RefCountyId'       => $RefCountyId,
                    'RefCountryId' => $RefCountryId
                    // 'Latitude' => '',
                    // 'Longitude' => '',
                    // 'RefPersonalInformationVerificationId' => ''

                );


                // PersonEmailAddress

                if (count($docentes[$do]->correo) == 0
                    || count($docentes[$do]->correo) == 1
                ) {

                    $EmailAddress = 'docente@gmail.com';

                } else {
                    $EmailAddress = substr($docentes[$do]->correo, 0, 128);
                }

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


                $_Personas[$d]['PersonDegreeOrCertificate'][] = array(

                    'PersonDegreeOrCertificateId' => $PersonDegreeOrCertificateId,
                    'PersonId' => $PersonId,
                    'DegreeOrCertificateTitleOrSubject' => 'test',
                    'RefDegreeOrCertificateTypeId' => 8,
                    'AwardDate' => '09-11-2000',
                    'NameOfInstitution' => 'Duoc Uc',
                    'RefHigherEducationInstitutionAccreditationStatusId' => 5,
                    'RefEducationVerificationMethodId' => 5

                );

                $docentesRel[$docentes[$do]->idCuenta] = [
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
                // $PersonTelephoneId++;
                $PersonStatusId++;
                $PersonLanguageId++;

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
                // $RefCountyId = (string)$comuna[0]->KeySI;

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
                    // 'RefCountyId'       => $RefCountyId,
                    'RefCountryId' => $RefCountryId
                    // 'Latitude' => '',
                    // 'Longitude' => '',
                    // 'RefPersonalInformationVerificationId' => ''

                );


                // PersonEmailAddress
                if (count($apoderadosP[$apri]->correoApoderado) == 0
                    || count($apoderadosP[$apri]->correoApoderado) == 1
                ) {

                    $EmailAddress = 'apoderado@gmail.com';

                } else {
                    $EmailAddress = substr($apoderadosP[$apri]->correoApoderado, 0, 128);
                }

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


                //PersonTelephone
                if (count($apoderadosP[$apri]->telefonoApoderado) >= 6
                    || count($apoderadosP[$apri]->celular) >= 8
                ) {

                    if (count($apoderadosP[$apri]->telefonoApoderado) >= 6) {
                        // TELÉFONO

                        $PersonTelephoneId++;
                        $TelephoneNumber = substr($apoderadosP[$apri]->telefonoApoderado, 0, 24);

                        $_Personas[$ap]['PersonTelephone'][] = array(

                            'PersonTelephoneId' => $PersonTelephoneId,
                            'PersonId' => $PersonId,
                            'TelephoneNumber' => "+56" . $TelephoneNumber,
                            'PrimaryTelephoneNumberIndicator' => (Bool)True,
                            'RefPersonTelephoneNumberTypeId' => 1 // Ref Mobile

                        );

                    }

                    if (count($apoderadosP[$apri]->celular) >= 8) {
                        // CELULAR

                        $PersonTelephoneId++;
                        $TelephoneNumber = substr($apoderadosP[$apri]->celular, 0, 24);

                        $_Personas[$ap]['PersonTelephone'][] = array(

                            'PersonTelephoneId' => $PersonTelephoneId,
                            'PersonId' => $PersonId,
                            'TelephoneNumber' => "+56" . $TelephoneNumber,
                            'PrimaryTelephoneNumberIndicator' => (Bool)True,
                            'RefPersonTelephoneNumberTypeId' => 3 // Ref Mobile

                        );

                    }

                } else {

                    $_Personas[$ap]['PersonTelephone'] = array(

                        // 'PersonTelephoneId' => $PersonTelephoneId,
                        // 'PersonId'          => $PersonId,
                        // 'TelephoneNumber'   => "+56".$TelephoneNumber,
                        // 'PrimaryTelephoneNumberIndicator' => (Bool)True,
                        // 'RefPersonTelephoneNumberTypeId'  => 3 // Ref Mobile

                    );

                }



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

                $PersonRelationshipId++;

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
                // $PersonTelephoneId++;
                $PersonStatusId++;
                $PersonLanguageId++;
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
                // $RefCountyId = (string)$comuna[0]->KeySI;

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
                    // 'RefCountyId'       => $RefCountyId,
                    'RefCountryId' => $RefCountryId
                    // 'Latitude' => '',
                    // 'Longitude' => '',
                    // 'RefPersonalInformationVerificationId' => ''

                );


                // PersonEmailAddress

                if (count($apoderadosSec[$asec]->correoApoderado) == 0
                    || count($apoderadosSec[$asec]->correoApoderado) == 1) {

                    $EmailAddress = 'apoderadoSec@gmail.com';

                } else {
                    $EmailAddress = substr($apoderadosSec[$asec]->correoApoderado, 0, 128);
                }

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

                if (count($apoderadosSec[$asec]->telefonoApoderado) >= 6
                    || count($apoderadosSec[$asec]->celular) >= 8
                ) {

                    if (count($apoderadosSec[$asec]->telefonoApoderado) >= 6) {
                        // TELÉFONO

                        $PersonTelephoneId++;
                        $TelephoneNumber = substr($apoderadosSec[$asec]->telefonoApoderado, 0, 24);

                        $_Personas[$as]['PersonTelephone'][] = array(

                            'PersonTelephoneId' => $PersonTelephoneId,
                            'PersonId' => $PersonId,
                            'TelephoneNumber' => "+56" . $TelephoneNumber,
                            'PrimaryTelephoneNumberIndicator' => (Bool)True,
                            'RefPersonTelephoneNumberTypeId' => 1 // Ref Mobile

                        );

                    }

                    if (count($apoderadosSec[$asec]->celular) >= 8) {
                        // CELULAR

                        $PersonTelephoneId++;
                        $TelephoneNumber = substr($apoderadosSec[$asec]->celular, 0, 24);

                        $_Personas[$as]['PersonTelephone'][] = array(

                            'PersonTelephoneId' => $PersonTelephoneId,
                            'PersonId' => $PersonId,
                            'TelephoneNumber' => "+56" . $TelephoneNumber,
                            'PrimaryTelephoneNumberIndicator' => (Bool)True,
                            'RefPersonTelephoneNumberTypeId' => 3 // Ref Mobile

                        );

                    }

                } else {

                    $_Personas[$as]['PersonTelephone'] = array(

                        // 'PersonTelephoneId' => $PersonTelephoneId,
                        // 'PersonId'          => $PersonId,
                        // 'TelephoneNumber'   => "+56".$TelephoneNumber,
                        // 'PrimaryTelephoneNumberIndicator' => (Bool)True,
                        // 'RefPersonTelephoneNumberTypeId'  => 3 // Ref Mobile

                    );

                }


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

                $PersonRelationshipId++;

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
            // $alumnosApoderadoPrincipalRel = $modelJson->listarAlumnosApoderadoPrincipalJSON($idperiodo, $idestablecimiento);

            // foreach ($alumnosApoderadoPrincipalRel as $key => $alumnoApopRel) {


            //     if ($alumnoApopRel->idApoderado != 0) {

            //         $PersonRelationshipId++;

            //         $PersonIdAlumno    = $alumnosRel[$alumnoApopRel->idAlumnos]['Item'];
            //         $PersonIdApoderado = $apoderadosRel['P' . $alumnoApopRel->idApoderado]['Item'];

            //         $nuevosValoresPersonRelationship = array(

            //             'PersonRelationshipId'      => $PersonRelationshipId,
            //             'PersonId'                  => $PersonIdAlumno,
            //             'RelatedPersonId'           => (integer)$PersonIdApoderado,
            //             'RefPersonRelationshipId'   => 19, // Ref Mother
            //             'CustodialRelationshipIndicator' => true,
            //             'EmergencyContactInd'       => true,
            //             'ContactPriorityNumber'     => 1,
            //             // 'ContactRestrictions' => '',
            //             'LivesWithIndicator'        => true,
            //             'PrimaryContactIndicator'   => true

            //         );

            //         array_push($_Personas[$PersonIdAlumno]['PersonRelationship'], $nuevosValoresPersonRelationship);
            //     }
            // }


            // // // Apoderado Secundario

            // $alumnosApoderadoSecundarioRel = $modelJson->listarAlumnosApoderadoSecundarioJSON($idperiodo, $idestablecimiento);

            // foreach ($alumnosApoderadoSecundarioRel as $key => $alumnoAposRel) {

            //     if ($alumnoAposRel->idApoderados != 0) {

            //         $PersonRelationshipId++;

            //         $PersonIdAlumno    = $alumnosRel[$alumnoAposRel->idAlumnos]['Item'];
            //         $PersonIdApoderado = $apoderadosRel['S' . $alumnoAposRel->idApoderados]['Item'];

            //         $nuevosValoresPersonRelationshipId = array(

            //             'PersonRelationshipId'      => $PersonRelationshipId,
            //             'PersonId'                  => $PersonIdAlumno,
            //             'RelatedPersonId'           => (integer)$PersonIdApoderado,
            //             'RefPersonRelationshipId'   => 19, // Ref Mother
            //             'CustodialRelationshipIndicator' => true,
            //             'EmergencyContactInd'       => true,
            //             'ContactPriorityNumber'     => 2,
            //             // 'ContactRestrictions' => '',
            //             'LivesWithIndicator'        => true,
            //             'PrimaryContactIndicator'   => false

            //         );
            //         array_push($_Personas[$PersonIdAlumno]['PersonRelationship'], $nuevosValoresPersonRelationshipId);
            //     }
            // }
            /* ------------------------------- FIN RELACIONES ALUMNO ------------------------------- */

            /* ----------------------------------- Fin _Personas ----------------------------------- */


            /* ----------------------------------- _Organizaciones ----------------------------------- */

            // 1000 = Establecimientos
            // 1001 = Modalidad
            // 1002 = Jornada
            // 1003 = Niveles
            // 1004 = Rama
            // 1005 = Sector económico
            // 1006 = Especialidad
            // 1007 = Tipo Curso
            // 1008 = Cod Ense
            // 1009 = Cod Ense
            // 1010 = Cursos
            // 1011 = Asignaturas


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
                    'RefOrganizationTypeId' => 10, // Ref EducationInstitution
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
                    'TelephoneNumber' => $TelephoneNumber,
                    'PrimaryTelephoneNumberIndicator' => True,
                    'RefInstitutionTelephoneTypeId' => 2 // Ref Main

                );


                // OrganizationEmail

                if (count($establecimientos[$e]->correo) == 0
                    || count($establecimientos[$e]->correo) == 1
                ) {

                    $ElectronicMailAddress = 'establecimiento@gmail.com';

                } else {
                    $ElectronicMailAddress = substr($establecimientos[$e]->correo, 0, 128);
                }

                $_Organizaciones[$e]['OrganizationEmail'][] = array(

                    'OrganizationEmailId' => (int)$OrganizationEmailId,
                    'OrganizationId' => (int)$OrganizationId,
                    'ElectronicMailAddress' => $ElectronicMailAddress,
                    'RefEmailTypeId' => 3 // Ref Organizational

                );


                // OrganizationIdentifier
                $rbd=str_replace('-','',$establecimientos[$e]->rbd);
                $Identifier = 'RBD' . substr($rbd, 0, 5);
                //$Identifier = 'RBD' . substr($establecimientos[$e]->rbd, 0, 5);

                $_Organizaciones[$e]['OrganizationIdentifier'][] = array(

                    'OrganizationIdentifierId' => (int)$OrganizationIdentifierId,
                    'Identifier' => $Identifier,
                    'RefOrganizationIdentificationSystemId' => 48, // Ref Other tipo School
                    'OrganizationId' => (int)$OrganizationId,
                    'RefOrganizationIdentifierTypeId' => 17 //aca

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

                $establecimientosOrganizationRel[$establecimientos[$e]->idEstablecimiento] = [ //aca
                    'OrganizationId' => $OrganizationId,
                    'idEstablecimiento' => $establecimientos[$e]->idEstablecimiento,
                    'Item' => $e
                ];
            }


            // Modalidad

            // 1000 = Establecimientos
            // 1001 = Modalidad
            // 1002 = Jornada
            // 1003 = Niveles
            // 1004 = Rama
            // 1005 = Sector económico
            // 1006 = Especialidad
            // 1007 = Tipo Curso
            // 1008 = Cod Ense
            // 1009 = Cod Ense
            // 1010 = Cursos
            // 1011 = Asignaturas

            // $e++;
            $m = $e;

            $OrganizationId = '10011';
            // $OrganizationLocationId++;
            // $OrganizationTelephoneId++;
            // $OrganizationEmailId++;
            // $RefOrganizationRelationshipId++;
            // $OrganizationIdentifierId++;
            // $OrganizationOperationalStatusId++;
            // $OrganizationIndicatorId++;
            $OrganizationRelationshipId++;

            // Organization
            $Name = substr('Regular', 0, 128);

            $_Organizaciones[$m]['Organization'][] = array(

                'OrganizationId' => (int)$OrganizationId,
                'Name' => $Name,
                'RefOrganizationTypeId' => 38,
                // 'ShortName'      => $ShortName,
                // 'RegionGeoJSON' => '',

            );


            $_Organizaciones[$m]['OrganizationLocation'] = array(

                // 'OrganizationLocationId' => $OrganizationLocationId,
                // 'OrganizationId' => $OrganizationId,
                // 'LocationId' => '',
                // 'RefOrganizationLocationTypeId' => ''

            );


            // OrganizationTelephone
            $_Organizaciones[$m]['OrganizationTelephone'] = array(

                // 'OrganizationTelephoneId'   => $OrganizationTelephoneId,
                // 'OrganizationId'            => $OrganizationId,
                // 'TelephoneNumber'           => $TelephoneNumber,
                // 'PrimaryTelephoneNumberIndicator' => ,
                // 'RefInstitutionTelephoneTypeId'   =>

            );


            // OrganizationEmail

            $_Organizaciones[$m]['OrganizationEmail'] = array(

                // 'OrganizationEmailId'   => (int)$OrganizationEmailId,
                // 'OrganizationId'        => (int)$OrganizationId,
                // 'ElectronicMailAddress' => $ElectronicMailAddress,
                // 'RefEmailTypeId'        =>

            );


            // OrganizationIdentifier

            $_Organizaciones[$m]['OrganizationIdentifier'] = array(

                // 'OrganizationIdentifierId'  => (int)$OrganizationIdentifierId,
                // 'Identifier'                => $Identifier,
                // 'RefOrganizationIdentificationSystemId' => ,
                // 'OrganizationId'            => (int)$OrganizationId
                // 'RefOrganizationIdentifierTypeId' => ''

            );


            $_Organizaciones[$m]['OrganizationWebsite'] = array(

                // 'OrganizationId' => $OrganizationId
                // 'Website' => '',

            );


            $_Organizaciones[$m]['OrganizationOperationalStatus'] = array(

                // 'OrganizationOperationalStatusId' => (int)$OrganizationOperationalStatusId,
                // 'OrganizationId'                  => (int)$OrganizationId,
                // 'RefOperationalStatusId'          => ,
                // 'OperationalStatusEffectiveDate' => ''

            );


            $_Organizaciones[$m]['OrganizationIndicator'] = array(

                // 'OrganizationIndicatorId' => $OrganizationIndicatorId,
                // 'OrganizationId' => $OrganizationId
                // 'IndicatorValue' => '',
                // 'RefOrganizationIndicatorId' => ''

            );

            $Parent_OrganizationId = $establecimientosOrganizationRel[$idestablecimiento]['OrganizationId'];
            $_Organizaciones[$m]['OrganizationRelationship'][] = array(

                'OrganizationRelationshipId' => $OrganizationRelationshipId,
                'Parent_OrganizationId' => (int)$Parent_OrganizationId,
                'OrganizationId' => $OrganizationId,
                'RefOrganizationRelationshipId' => 2

            );

            $modalidadOrganizationRel = $OrganizationId;


            $m++;


            // Jornada

            // 1000 = Establecimientos
            // 1001 = Modalidad
            // 1002 = Jornada
            // 1003 = Niveles
            // 1004 = Rama
            // 1005 = Sector económico
            // 1006 = Especialidad
            // 1007 = Tipo Curso
            // 1008 = Cod Ense
            // 1009 = Cod Ense
            // 1010 = Cursos
            // 1011 = Asignaturas


            $jornada = array(
                1 => "Mañana",
                2 => "Tarde",
                3 => "Mañana y Tarde",
                4 => "Vespertina/Nocturna",
            );
            $largo = count($jornada) + $m;


            for ($jo = $m; $jo < $largo; $jo++) {
                $jor = ($jo - $m) + 1;

                $OrganizationId = intval('1002' . $jor);

                // $OrganizationLocationId++;
                // $OrganizationTelephoneId++;
                // $OrganizationEmailId++;
                // $RefOrganizationRelationshipId++;
                // $OrganizationIdentifierId++;
                // $OrganizationOperationalStatusId++;
                // $OrganizationIndicatorId++;
                $OrganizationRelationshipId++;

                // Organization
                $Name = substr($jornada[$jor], 0, 128);

                $_Organizaciones[$jo]['Organization'][] = array(

                    'OrganizationId' => (int)$OrganizationId,
                    'Name' => $Name,
                    'RefOrganizationTypeId' => 39,
                    // 'ShortName'      => $ShortName,
                    // 'RegionGeoJSON' => '',

                );


                $_Organizaciones[$jo]['OrganizationLocation'] = array(

                    // 'OrganizationLocationId' => $OrganizationLocationId,
                    // 'OrganizationId' => $OrganizationId,
                    // 'LocationId' => '',
                    // 'RefOrganizationLocationTypeId' => ''

                );


                // OrganizationTelephone
                $_Organizaciones[$jo]['OrganizationTelephone'] = array(

                    // 'OrganizationTelephoneId'   => $OrganizationTelephoneId,
                    // 'OrganizationId'            => $OrganizationId,
                    // 'TelephoneNumber'           => $TelephoneNumber,
                    // 'PrimaryTelephoneNumberIndicator' => ,
                    // 'RefInstitutionTelephoneTypeId'   =>

                );


                // OrganizationEmail

                $_Organizaciones[$jo]['OrganizationEmail'] = array(

                    // 'OrganizationEmailId'   => (int)$OrganizationEmailId,
                    // 'OrganizationId'        => (int)$OrganizationId,
                    // 'ElectronicMailAddress' => $ElectronicMailAddress,
                    // 'RefEmailTypeId'        =>

                );


                // OrganizationIdentifier

                $_Organizaciones[$jo]['OrganizationIdentifier'] = array(

                    // 'OrganizationIdentifierId'  => (int)$OrganizationIdentifierId,
                    // 'Identifier'                => $Identifier,
                    // 'RefOrganizationIdentificationSystemId' => ,
                    // 'OrganizationId'            => (int)$OrganizationId
                    // 'RefOrganizationIdentifierTypeId' => ''

                );


                $_Organizaciones[$jo]['OrganizationWebsite'] = array(

                    // 'OrganizationId' => $OrganizationId
                    // 'Website' => '',

                );


                $_Organizaciones[$jo]['OrganizationOperationalStatus'] = array(

                    // 'OrganizationOperationalStatusId' => (int)$OrganizationOperationalStatusId,
                    // 'OrganizationId'                  => (int)$OrganizationId,
                    // 'RefOperationalStatusId'          => ,
                    // 'OperationalStatusEffectiveDate' => ''

                );


                $_Organizaciones[$jo]['OrganizationIndicator'] = array(

                    // 'OrganizationIndicatorId' => $OrganizationIndicatorId,
                    // 'OrganizationId' => $OrganizationId
                    // 'IndicatorValue' => '',
                    // 'RefOrganizationIndicatorId' => ''

                );


                $Parent_OrganizationId = $modalidadOrganizationRel;
                $_Organizaciones[$jo]['OrganizationRelationship'][] = array(

                    'OrganizationRelationshipId' => $OrganizationRelationshipId,
                    'Parent_OrganizationId' => (int)$Parent_OrganizationId,
                    'OrganizationId' => $OrganizationId,
                    'RefOrganizationRelationshipId' => 2

                );


                $jornadaOrganizationRel[$jor] = [
                    'OrganizationId' => $OrganizationId,
                    'name' => $Name,
                    'Item' => $jo
                ];

            }


            // NIVELES

            // 1000 = Establecimientos
            // 1001 = Modalidad
            // 1002 = Jornada
            // 1003 = Niveles
            // 1004 = Rama
            // 1005 = Sector económico
            // 1006 = Especialidad
            // 1007 = Tipo Curso
            // 1008 = Cod Ense
            // 1009 = Cod Ense
            // 1010 = Cursos
            // 1011 = Asignaturas


//                10 => '01:Educación Parvularia',
//                110 => '02:Enseñanza Básica Niños',
//                165 => '03:Educación Básica Adultos',
//                167 => '03:Educación Básica Adultos',
//                310 => '05:Enseñanza Media Humanístico Científica Jóvenes',
//                360 => '06:Educación Media Humanístico Científica Adultos',
//                363 => '06:Educación Media H-C Adultos',
//                410 => '07:Enseñanza Media Técnico Profesional y Artística, Jóvenes',
//                463 => '08:Educación Media Técnico Profesional y Artística, Adultos',
//                510 => '09:Enseñanza Media Técnico-Profesional Industrial niños',
//                563 => '10:Educación Media T-P Adultos Industrial',
//                610 => '11:Enseñanza Media Técnico-Profesional Técnica niños',
//                663 => '12:Educación Media T-P Adultos Técnica',
//                710 => '13:Enseñanza Media Técnico-Profesional Agrícola niños',
//                763 => '14:Educación Media T-P Adultos Agrícola',
//                810 => '15:Enseñanza Media Técnico-Profesional Marítima niños',
//                863 => '16:Educación Media T-P Adultos Marítima',
//                910 => '17:Enseñanza Media Artística Niños y Jóvenes',
//                963 => '18:Enseñanza Media Artística Adultos'

            $niveles = array(
                10 => '01:Educación Parvularia',
                110 => '02:Enseñanza Básica Niños',
                165 => '03:Educación Básica Adultos',
                167 => '03:Educación Básica Adultos',
                310 => '05:Enseñanza Media Humanístico Científica Jóvenes',
                360 => '06:Educación Media Humanístico Científica Adultos',
                363 => '06:Educación Media H-C Adultos',
                410 => '07:Enseñanza Media Técnico Profesional y Artística, Jóvenes',
                463 => '08:Educación Media Técnico Profesional y Artística, Adultos',


            );

            $nivelesCursoQuery = $modelJson->listarNivelesCursosJSON($idperiodo, $idestablecimiento, $lista_curso);

            $largo = count($nivelesCursoQuery) + $jo;
            for ($ni = $jo; $ni < $largo; $ni++) {

                $nive = $ni - $jo;
                $OrganizationId = intval('1003' . $nive);

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
                $Name = substr($niveles[$nivelesCursoQuery[$nive]->idCodigoTipo], 0, 128);
                $_Organizaciones[$ni]['Organization'][] = array(

                    'OrganizationId' => (int)$OrganizationId,
                    'Name' => $Name,
                    'RefOrganizationTypeId' => 40,
                    // 'ShortName'         => $ShortName
                    // 'RegionGeoJSON' => '',

                );


                $_Organizaciones[$ni]['OrganizationLocation'] = array(

                    // 'OrganizationLocationId' => $OrganizationLocationId,
                    // 'OrganizationId' => $OrganizationId,
                    // 'LocationId' => '',
                    // 'RefOrganizationLocationTypeId' => ''

                );


                // OrganizationTelephone
                $_Organizaciones[$ni]['OrganizationTelephone'] = array(

                    // 'OrganizationTelephoneId' => $OrganizationTelephoneId,
                    // 'OrganizationId' => $OrganizationId,
                    // 'TelephoneNumber' => $TelephoneNumber,
                    // 'PrimaryTelephoneNumberIndicator' => 'Yes',
                    // 'RefInstitutionTelephoneTypeId' => 'Main'

                );


                // OrganizationEmail
                $_Organizaciones[$ni]['OrganizationEmail'] = array(

                    // 'OrganizationEmailId' => (int)$OrganizationEmailId,
                    // 'OrganizationId' => (int)$OrganizationId,
                    // 'ElectronicMailAddress' => $nilectronicMailAddress
                    // 'RefEmailTypeId' => ''

                );


                // OrganizationIdentifier
                $_Organizaciones[$ni]['OrganizationIdentifier'] = array(

                    // 'OrganizationIdentifierId'              => (int)$OrganizationIdentifierId,
                    // 'Identifier'                            => $Identifier,
                    // 'RefOrganizationIdentificationSystemId' => 35, // Ref Other
                    // 'OrganizationId'                        => (int)$OrganizationId
                    // 'RefOrganizationIdentifierTypeId' => ''

                );


                $_Organizaciones[$ni]['OrganizationWebsite'] = array(

                    // 'OrganizationId' => $OrganizationId
                    // 'Website' => '',

                );


                $_Organizaciones[$ni]['OrganizationOperationalStatus'] = array(

                    // 'OrganizationOperationalStatusId' => (int)$OrganizationOperationalStatusId,
                    // 'OrganizationId'                  => (int)$OrganizationId,
                    // 'RefOperationalStatusId'          => 17, // Ref Active
                    // 'OperationalStatusEffectiveDate' => ''

                );


                $_Organizaciones[$ni]['OrganizationIndicator'] = array(

                    // 'OrganizationIndicatorId' => $OrganizationIndicatorId,
                    // 'OrganizationId' => $OrganizationId
                    // 'IndicatorValue'=> '',
                    // 'RefOrganizationIndicatorId' => ''

                );

                $Parent_OrganizationId = $jornadaOrganizationRel[$nivelesCursoQuery[$nive]['tipoJornada']]['OrganizationId'];
                $_Organizaciones[$ni]['OrganizationRelationship'][] = array(

                    'OrganizationRelationshipId' => $OrganizationRelationshipId,
                    'Parent_OrganizationId' => (int)$Parent_OrganizationId,
                    'OrganizationId' => $OrganizationId,
                    'RefOrganizationRelationshipId' => 2

                );


                $nivelesOrganizationRel[$nive] = [
                    'OrganizationId' => $OrganizationId,
                    'Item' => $nive
                ];

                $nivelesCursoOrganizationRel[$nive] = [
                    'OrganizationId' => $OrganizationId,
                    'IdCursos' => $nivelesCursoQuery[$nive]->idCursos,
                    'idCodigoGrado' => $nivelesCursoQuery[$nive]->idCodigoGrado,
                    'idCodigoTipo' => $nivelesCursoQuery[$nive]->idCodigoTipo,
                    'tipoJornada' => $nivelesCursoQuery[$nive]->tipoJornada,
                    'codigoSector' => $nivelesCursoQuery[$nive]->codigoSector,
                    'codigoEspecialidad' => $nivelesCursoQuery[$nive]->codigoEspecialidad
                ];

            }


            // Rama

            // 1000 = Establecimientos
            // 1001 = Modalidad
            // 1002 = Jornada
            // 1003 = Niveles
            // 1004 = Rama
            // 1005 = Sector económico
            // 1006 = Especialidad
            // 1007 = Tipo Curso
            // 1008 = Cod Ense
            // 1009 = Cod Ense
            // 1010 = Cursos
            // 1011 = Asignaturas


            $rama = array(
                1 => '000:Ciclo General',
                2 => '000:Sin Información',
                3 => '400:Comercial',
                4 => '500:Industrial',
                5 => '600:Técnica',
                6 => '700:Agrícola',
                7 => '800:Marítima',
                8 => '900:Artística'
            );


            $largo = count($nivelesOrganizationRel) + $ni;


            for ($ra = $ni; $ra < $largo; $ra++) {
                $ram = ($ra - $ni) + 1;

                $OrganizationId = intval('1004' . $ram);

                // $OrganizationLocationId++;
                // $OrganizationTelephoneId++;
                // $OrganizationEmailId++;
                // $RefOrganizationRelationshipId++;
                // $OrganizationIdentifierId++;
                // $OrganizationOperationalStatusId++;
                // $OrganizationIndicatorId++;
                $OrganizationRelationshipId++;

                // Organization
                $Name = substr($rama[2], 0, 128);

                $_Organizaciones[$ra]['Organization'][] = array(

                    'OrganizationId' => (int)$OrganizationId,
                    'Name' => $Name,
                    'RefOrganizationTypeId' => 41,
                    // 'ShortName'      => $ShortName,
                    // 'RegionGeoJSON' => '',

                );


                $_Organizaciones[$ra]['OrganizationLocation'] = array(

                    // 'OrganizationLocationId' => $OrganizationLocationId,
                    // 'OrganizationId' => $OrganizationId,
                    // 'LocationId' => '',
                    // 'RefOrganizationLocationTypeId' => ''

                );


                // OrganizationTelephone
                $_Organizaciones[$ra]['OrganizationTelephone'] = array(

                    // 'OrganizationTelephoneId'   => $OrganizationTelephoneId,
                    // 'OrganizationId'            => $OrganizationId,
                    // 'TelephoneNumber'           => $TelephoneNumber,
                    // 'PrimaryTelephoneNumberIndicator' => ,
                    // 'RefInstitutionTelephoneTypeId'   =>

                );


                // OrganizationEmail

                $_Organizaciones[$ra]['OrganizationEmail'] = array(

                    // 'OrganizationEmailId'   => (int)$OrganizationEmailId,
                    // 'OrganizationId'        => (int)$OrganizationId,
                    // 'ElectronicMailAddress' => $ElectronicMailAddress,
                    // 'RefEmailTypeId'        =>

                );


                // OrganizationIdentifier

                $_Organizaciones[$ra]['OrganizationIdentifier'] = array(

                    // 'OrganizationIdentifierId'  => (int)$OrganizationIdentifierId,
                    // 'Identifier'                => $Identifier,
                    // 'RefOrganizationIdentificationSystemId' => ,
                    // 'OrganizationId'            => (int)$OrganizationId
                    // 'RefOrganizationIdentifierTypeId' => ''

                );


                $_Organizaciones[$ra]['OrganizationWebsite'] = array(

                    // 'OrganizationId' => $OrganizationId
                    // 'Website' => '',

                );


                $_Organizaciones[$ra]['OrganizationOperationalStatus'] = array(

                    // 'OrganizationOperationalStatusId' => (int)$OrganizationOperationalStatusId,
                    // 'OrganizationId'                  => (int)$OrganizationId,
                    // 'RefOperationalStatusId'          => ,
                    // 'OperationalStatusEffectiveDate' => ''

                );


                $_Organizaciones[$ra]['OrganizationIndicator'] = array(

                    // 'OrganizationIndicatorId' => $OrganizationIndicatorId,
                    // 'OrganizationId' => $OrganizationId
                    // 'IndicatorValue' => '',
                    // 'RefOrganizationIndicatorId' => ''

                );


                $Parent_OrganizationId = $nivelesOrganizationRel[$ram - 1]['OrganizationId'];
                $_Organizaciones[$ra]['OrganizationRelationship'][] = array(

                    'OrganizationRelationshipId' => $OrganizationRelationshipId,
                    'Parent_OrganizationId' => (int)$Parent_OrganizationId,
                    'OrganizationId' => $OrganizationId,
                    'RefOrganizationRelationshipId' => 2

                );


                $ramaOrganizationRel[$ram] = [
                    'OrganizationId' => $OrganizationId,
                    'name' => $Name,
                    'Item' => $ram
                ];

            }


            // Sector enseñanza

            // 1000 = Establecimientos
            // 1001 = Modalidad
            // 1002 = Jornada
            // 1003 = Niveles
            // 1004 = Rama
            // 1005 = Sector enseñanza
            // 1006 = Especialidad
            // 1007 = Tipo Curso
            // 1008 = Cod Ense
            // 1009 = Cod Ense
            // 1010 = Cursos
            // 1011 = Asignaturas


            $sectorEnseñanza = array(
                1 => '000:Ciclo General',
                2 => '000:Sin Información',
                410 => '410:Administración y Comercio',
                510 => '510:Construcción',
                520 => '520:Metalmecánico',
                530 => '530:Electricidad',
                540 => '540:Minero',
                550 => '550:Gráfica',
                560 => '560:Químico',
                570 => '570:Confección',
                580 => '580:Tecnología y Telecomunicaciones',
                610 => '610:Alimentación',
                620 => '620:Programas y Proyectos Sociales',
                630 => '630:Hotelería y Turismo',
                640 => '640:Salud y Educación',
                710 => '710:Maderero',
                720 => '720:Agropecuario',
                810 => '810:Marítimo',
                910 => '910:Artes Visuales',
                920 => '920:Artes Escénicas Teatro',
                930 => '930:Artes Escénicas Danza'
            );

            $largo = count($ramaOrganizationRel) + $ra;

            for ($sec = $ra; $sec < $largo; $sec++) {
                $sect = ($sec - $ra) + 1;

                $OrganizationId = intval('1005' . $sect);

                // $OrganizationLocationId++;
                // $OrganizationTelephoneId++;
                // $OrganizationEmailId++;
                // $RefOrganizationRelationshipId++;
                // $OrganizationIdentifierId++;
                // $OrganizationOperationalStatusId++;
                // $OrganizationIndicatorId++;
                $OrganizationRelationshipId++;

                // Organization
                $Name = substr($sectorEnseñanza[2], 0, 128);

                $_Organizaciones[$sec]['Organization'][] = array(

                    'OrganizationId' => (int)$OrganizationId,
                    'Name' => $Name,
                    'RefOrganizationTypeId' => 42,
                    // 'ShortName'      => $ShortName,
                    // 'RegionGeoJSON' => '',

                );


                $_Organizaciones[$sec]['OrganizationLocation'] = array(

                    // 'OrganizationLocationId' => $OrganizationLocationId,
                    // 'OrganizationId' => $OrganizationId,
                    // 'LocationId' => '',
                    // 'RefOrganizationLocationTypeId' => ''

                );


                // OrganizationTelephone
                $_Organizaciones[$sec]['OrganizationTelephone'] = array(

                    // 'OrganizationTelephoneId'   => $OrganizationTelephoneId,
                    // 'OrganizationId'            => $OrganizationId,
                    // 'TelephoneNumber'           => $TelephoneNumber,
                    // 'PrimaryTelephoneNumberIndicator' => ,
                    // 'RefInstitutionTelephoneTypeId'   =>

                );


                // OrganizationEmail

                $_Organizaciones[$sec]['OrganizationEmail'] = array(

                    // 'OrganizationEmailId'   => (int)$OrganizationEmailId,
                    // 'OrganizationId'        => (int)$OrganizationId,
                    // 'ElectronicMailAddress' => $ElectronicMailAddress,
                    // 'RefEmailTypeId'        =>

                );


                // OrganizationIdentifier

                $_Organizaciones[$sec]['OrganizationIdentifier'] = array(

                    // 'OrganizationIdentifierId'  => (int)$OrganizationIdentifierId,
                    // 'Identifier'                => $Identifier,
                    // 'RefOrganizationIdentificationSystemId' => ,
                    // 'OrganizationId'            => (int)$OrganizationId
                    // 'RefOrganizationIdentifierTypeId' => ''

                );


                $_Organizaciones[$sec]['OrganizationWebsite'] = array(

                    // 'OrganizationId' => $OrganizationId
                    // 'Website' => '',

                );


                $_Organizaciones[$sec]['OrganizationOperationalStatus'] = array(

                    // 'OrganizationOperationalStatusId' => (int)$OrganizationOperationalStatusId,
                    // 'OrganizationId'                  => (int)$OrganizationId,
                    // 'RefOperationalStatusId'          => ,
                    // 'OperationalStatusEffectiveDate' => ''

                );


                $_Organizaciones[$sec]['OrganizationIndicator'] = array(

                    // 'OrganizationIndicatorId' => $OrganizationIndicatorId,
                    // 'OrganizationId' => $OrganizationId
                    // 'IndicatorValue' => '',
                    // 'RefOrganizationIndicatorId' => ''

                );


                $Parent_OrganizationId = $ramaOrganizationRel[$sect]['OrganizationId'];
                $_Organizaciones[$sec]['OrganizationRelationship'][] = array(

                    'OrganizationRelationshipId' => $OrganizationRelationshipId,
                    'Parent_OrganizationId' => (int)$Parent_OrganizationId,
                    'OrganizationId' => $OrganizationId,
                    'RefOrganizationRelationshipId' => 2

                );


                $sectorEnseñanzaOrganizationRel[$sect] = [
                    'OrganizationId' => $OrganizationId,
                    'name' => $Name,
                    'Item' => $sec
                ];

            }


            // Especialidad

            // 1000 = Establecimientos
            // 1001 = Modalidad
            // 1002 = Jornada
            // 1003 = Niveles
            // 1004 = Rama
            // 1005 = Sector enseñanza
            // 1006 = Especialidad
            // 1007 = Tipo Curso
            // 1008 = Cod Ense
            // 1009 = Cod Ense
            // 1010 = Cursos
            // 1011 = Asignaturas


            $Especialidad = array(
                1 => '000:Ciclo General',
                2 => '000:Sin Información',
                41001 => '410.41001:Administración',
                41002 => '410.41002:Contabilidad',
                41003 => '410.41003:Secretariado',
                41004 => '410.41004:Ventas',
                41005 => '410.41005:Administración (con mención)',
                51001 => '510.51001:Edificación',
                51002 => '510.51002:Terminaciones de Construcción',
                51003 => '510.51003:Montaje Industrial',
                51004 => '510.51004:Obras viales y de infraestructura',
                51005 => '510.51005:Instalaciones sanitarias',
                51006 => '510.51006:Refrigeración y climatización',
                51009 => '510.51009:Construcción (con mención)',
                52008 => '520.52008:Mecánica Industrial',
                52009 => '520.52009:Construcciones Metálicas',
                52010 => '520.52010:Mecánica Automotriz',
                52011 => '520.52011:Matricería',
                52012 => '520.52012:Mecánica de mantención de aeronaves',
                52013 => '520.52013:Mecánica Industrial (con mención)',
                53014 => '530.53014:Electricidad',
                53015 => '530.53015:Electrónica',
                53016 => '530.53016:Telecomunicaciones hasta el año 2015',
                54018 => '540.54018:Explotación minera',
                54019 => '540.54019:Metalurgia Extractiva',
                54020 => '540.54020:Asistencia de geología',
                55022 => '550.55022:Gráfica',
                55023 => '550.55023:Dibujo Técnico',
                56025 => '560.56025:Operación de planta química',
                56026 => '560.56026:Laboratorio químico',
                56027 => '560.56027:Química Industrial (con mención)',
                57028 => '570.57028:Tejido',
                57029 => '570.57029:Textil',
                57030 => '570.57030:Vestuario y Confección Textil',
                57031 => '570.57031:Productos del cuero',
                58033 => '580.58033:Conectividad y Redes',
                58034 => '580.58034:Programación',
                58035 => '580.58035:Telecomunicaciones',
                61001 => '610.61001:Elaboración Industrial de Alimentos',
                61002 => '610.61002:Servicio de Alimentación Colectiva',
                61003 => '610.61003:Gastronomía (con mención)',
                62004 => '620.62004:Atención de párvulos hasta año 2015',
                62005 => '620.62005:Atención de adultos mayores',
                62006 => '620.62006:Atención de Enfermería',
                62007 => '620.62007:Atención Social y Recreativa',
                62008 => '620.62008:Atención de Enfermería (con mención) hasta año 2015',
                63009 => '630.63009:Servicio de turismo',
                63010 => '630.63010:Servicios Hoteleros',
                63011 => '630.63011:Servicio de hotelería',
                64001 => '640.64001:Atención de párvulos',
                64008 => '640.64008:Atención de Enfermería (con mención)',
                71001 => '710.71001:Forestal',
                71002 => '710.71002:Procesamiento de la madera',
                71003 => '710.71003:Productos de la madera',
                71004 => '710.71004:Celulosa y Papel',
                71005 => '710.71005:Muebles y Terminaciones de la madera',
                72006 => '720.72006:Agropecuaria',
                72007 => '720.72007:Agropecuaria (con mención)',
                81001 => '810.81001:Naves mercantes y especiales',
                81002 => '810.81002:Pesquería',
                81003 => '810.81003:Acuicultura',
                81004 => '810.81004:Operación portuaria',
                81005 => '810.81005:Tripulación naves mercantes y especiales',
                91001 => '910.91001:Artes Visuales',
                91002 => '910.91002:Artes Audiovisuales',
                91003 => '910.91003:Diseño',
                92004 => '920.92004:Interpretación Teatral',
                92005 => '920.92005:Diseño Escénico',
                93006 => '930.93006:Interpretación en Danza de Nivel Intermedio',
                93007 => '930.93007:Monitoría de Danza'
            );


            $largo = count($nivelesCursoOrganizationRel) + $sec;


            for ($esp = $sec; $esp < $largo; $esp++) {
                $espe = ($esp - $sec) + 1;

                $OrganizationId = intval('1006' . $espe);

                // $OrganizationLocationId++;
                // $OrganizationTelephoneId++;
                // $OrganizationEmailId++;
                // $RefOrganizationRelationshipId++;
                // $OrganizationIdentifierId++;
                // $OrganizationOperationalStatusId++;
                // $OrganizationIndicatorId++;
                $OrganizationRelationshipId++;

                $codigoEspecialidad = $nivelesCursoOrganizationRel[$espe - 1]['codigoEspecialidad'];

                // Organization
                $Name = (is_null($codigoEspecialidad)) ? substr($Especialidad[2], 0, 128) : substr($Especialidad[$codigoEspecialidad], 0, 128);

                $_Organizaciones[$esp]['Organization'][] = array(

                    'OrganizationId' => (int)$OrganizationId,
                    'Name' => $Name,
                    'RefOrganizationTypeId' => 43,
                    // 'ShortName'      => $ShortName,
                    // 'RegionGeoJSON' => '',

                );


                $_Organizaciones[$esp]['OrganizationLocation'] = array(

                    // 'OrganizationLocationId' => $OrganizationLocationId,
                    // 'OrganizationId' => $OrganizationId,
                    // 'LocationId' => '',
                    // 'RefOrganizationLocationTypeId' => ''

                );


                // OrganizationTelephone
                $_Organizaciones[$esp]['OrganizationTelephone'] = array(

                    // 'OrganizationTelephoneId'   => $OrganizationTelephoneId,
                    // 'OrganizationId'            => $OrganizationId,
                    // 'TelephoneNumber'           => $TelephoneNumber,
                    // 'PrimaryTelephoneNumberIndicator' => ,
                    // 'RefInstitutionTelephoneTypeId'   =>

                );


                // OrganizationEmail

                $_Organizaciones[$esp]['OrganizationEmail'] = array(

                    // 'OrganizationEmailId'   => (int)$OrganizationEmailId,
                    // 'OrganizationId'        => (int)$OrganizationId,
                    // 'ElectronicMailAddress' => $ElectronicMailAddress,
                    // 'RefEmailTypeId'        =>

                );


                // OrganizationIdentifier

                $_Organizaciones[$esp]['OrganizationIdentifier'] = array(

                    // 'OrganizationIdentifierId'  => (int)$OrganizationIdentifierId,
                    // 'Identifier'                => $Identifier,
                    // 'RefOrganizationIdentificationSystemId' => ,
                    // 'OrganizationId'            => (int)$OrganizationId
                    // 'RefOrganizationIdentifierTypeId' => ''

                );


                $_Organizaciones[$esp]['OrganizationWebsite'] = array(

                    // 'OrganizationId' => $OrganizationId
                    // 'Website' => '',

                );


                $_Organizaciones[$esp]['OrganizationOperationalStatus'] = array(

                    // 'OrganizationOperationalStatusId' => (int)$OrganizationOperationalStatusId,
                    // 'OrganizationId'                  => (int)$OrganizationId,
                    // 'RefOperationalStatusId'          => ,
                    // 'OperationalStatusEffectiveDate' => ''

                );


                $_Organizaciones[$esp]['OrganizationIndicator'] = array(

                    // 'OrganizationIndicatorId' => $OrganizationIndicatorId,
                    // 'OrganizationId' => $OrganizationId
                    // 'IndicatorValue' => '',
                    // 'RefOrganizationIndicatorId' => ''

                );


                $Parent_OrganizationId = $sectorEnseñanzaOrganizationRel[$espe]['OrganizationId'];
                $_Organizaciones[$esp]['OrganizationRelationship'][] = array(

                    'OrganizationRelationshipId' => $OrganizationRelationshipId,
                    'Parent_OrganizationId' => (int)$Parent_OrganizationId,
                    'OrganizationId' => $OrganizationId,
                    'RefOrganizationRelationshipId' => 2

                );


                $especialidadOrganizationRel[$espe] = [
                    'OrganizationId' => $OrganizationId,
                    'name' => $Name,
                    'Item' => $sec
                ];

            }

            // tipoCurso

            // 1000 = Establecimientos
            // 1001 = Modalidad
            // 1002 = Jornada
            // 1003 = Niveles
            // 1004 = Rama
            // 1005 = Sector enseñanza
            // 1006 = Especialidad
            // 1007 = Tipo Curso
            // 1008 = Cod Ense
            // 1009 = Cod Ense
            // 1010 = Cursos
            // 1011 = Asignaturas


            $tipoCurso = array(
                1 => '01:Simple',
                2 => '02:Combinado'
            );


            $largo = count($especialidadOrganizationRel) + $esp;


            for ($tiCu = $esp; $tiCu < $largo; $tiCu++) {
                $tipoCu = ($tiCu - $esp) + 1;

                $OrganizationId = intval('1007' . $tipoCu);

                // $OrganizationLocationId++;
                // $OrganizationTelephoneId++;
                // $OrganizationEmailId++;
                // $RefOrganizationRelationshipId++;
                // $OrganizationIdentifierId++;
                // $OrganizationOperationalStatusId++;
                // $OrganizationIndicatorId++;
                $OrganizationRelationshipId++;

                // Organization
                $Name = substr($tipoCurso[1], 0, 128);

                $_Organizaciones[$tiCu]['Organization'][] = array(

                    'OrganizationId' => (int)$OrganizationId,
                    'Name' => $Name,
                    'RefOrganizationTypeId' => 44,
                    // 'ShortName'      => $ShortName,
                    // 'RegionGeoJSON' => '',

                );


                $_Organizaciones[$tiCu]['OrganizationLocation'] = array(

                    // 'OrganizationLocationId' => $OrganizationLocationId,
                    // 'OrganizationId' => $OrganizationId,
                    // 'LocationId' => '',
                    // 'RefOrganizationLocationTypeId' => ''

                );


                // OrganizationTelephone
                $_Organizaciones[$tiCu]['OrganizationTelephone'] = array(

                    // 'OrganizationTelephoneId'   => $OrganizationTelephoneId,
                    // 'OrganizationId'            => $OrganizationId,
                    // 'TelephoneNumber'           => $TelephoneNumber,
                    // 'PrimaryTelephoneNumberIndicator' => ,
                    // 'RefInstitutionTelephoneTypeId'   =>

                );


                // OrganizationEmail

                $_Organizaciones[$tiCu]['OrganizationEmail'] = array(

                    // 'OrganizationEmailId'   => (int)$OrganizationEmailId,
                    // 'OrganizationId'        => (int)$OrganizationId,
                    // 'ElectronicMailAddress' => $ElectronicMailAddress,
                    // 'RefEmailTypeId'        =>

                );


                // OrganizationIdentifier

                $_Organizaciones[$tiCu]['OrganizationIdentifier'] = array(

                    // 'OrganizationIdentifierId'  => (int)$OrganizationIdentifierId,
                    // 'Identifier'                => $Identifier,
                    // 'RefOrganizationIdentificationSystemId' => ,
                    // 'OrganizationId'            => (int)$OrganizationId
                    // 'RefOrganizationIdentifierTypeId' => ''

                );


                $_Organizaciones[$tiCu]['OrganizationWebsite'] = array(

                    // 'OrganizationId' => $OrganizationId
                    // 'Website' => '',

                );


                $_Organizaciones[$tiCu]['OrganizationOperationalStatus'] = array(

                    // 'OrganizationOperationalStatusId' => (int)$OrganizationOperationalStatusId,
                    // 'OrganizationId'                  => (int)$OrganizationId,
                    // 'RefOperationalStatusId'          => ,
                    // 'OperationalStatusEffectiveDate' => ''

                );


                $_Organizaciones[$tiCu]['OrganizationIndicator'] = array(

                    // 'OrganizationIndicatorId' => $OrganizationIndicatorId,
                    // 'OrganizationId' => $OrganizationId
                    // 'IndicatorValue' => '',
                    // 'RefOrganizationIndicatorId' => ''

                );


                $Parent_OrganizationId = $especialidadOrganizationRel[$tipoCu]['OrganizationId'];
                $_Organizaciones[$tiCu]['OrganizationRelationship'][] = array(

                    'OrganizationRelationshipId' => $OrganizationRelationshipId,
                    'Parent_OrganizationId' => (int)$Parent_OrganizationId,
                    'OrganizationId' => $OrganizationId,
                    'RefOrganizationRelationshipId' => 2

                );


                $tipoCursoOrganizationRel[$tipoCu] = [
                    'OrganizationId' => $OrganizationId,
                    'name' => $Name,
                    'Item' => $tiCu
                ];

            }


            // Cod Ense

            // 1000 = Establecimientos
            // 1001 = Modalidad
            // 1002 = Jornada
            // 1003 = Niveles
            // 1004 = Rama
            // 1005 = Sector enseñanza
            // 1006 = Especialidad
            // 1007 = Tipo Curso
            // 1008 = Cod Ense
            // 1009 = Grado
            // 1010 = Cursos
            // 1011 = Asignaturas


            $codEnse = array(
                10 => '010:Educación Parvularia',
                110 => '110:Enseñanza Básica',
                160 => '160:Educación Básica Común Adultos (Decreto 584/2007)',
                161 => '161:Educación Básica Especial Adultos',
                163 => '163:Escuelas Cárceles (Básica Adultos)',
                165 => '165:Educación Básica Adultos Sin Oficios (Decreto 584/2007)',
                167 => '167:Educación Básica Adultos Con Oficios (Decreto 584/2007 y 999/2009)',
                211 => '211:Educación Especial Discapacidad Auditiva',
                212 => '212:Educación Especial Discapacidad Intelectual',
                213 => '213:Educación Especial Discapacidad Visual',
                214 => '214:Educación Especial Trastornos Específicos del Lenguaje',
                215 => '215:Educación Especial Trastornos Motores',
                216 => '216:Educación Especial Autismo',
                217 => '217:Educación Especial Discapacidad Graves Alteraciones en la Capacidad de Relación y Comunicación',
                299 => '299:Opción 4 Programa Integración Escolar',
                310 => '310:Enseñanza Media H-C niños y jóvenes',
                360 => '360:Educación Media H-C adultos vespertino y nocturno (Decreto N° 190/1975)',
                361 => '361:Educación Media H-C adultos (Decreto N° 12/1987)',
                362 => '362:Escuelas Cárceles (Media Adultos)',
                363 => '363:Educación Media H-C Adultos (Decreto N°1000/2009)',
                410 => '410:Enseñanza Media T-P Comercial Niños y Jóvenes',
                460 => '460:Educación Media T-P Comercial Adultos (Decreto N° 152/1989)',
                461 => '461:Educación Media T-P Comercial Adultos (Decreto N° 152/1989)',
                463 => '463:Educación Media T-P Comercial Adultos (Decreto N° 1000/2009)',
                510 => '510:Enseñanza Media T-P Industrial Niños y Jóvenes',
                560 => '560:Educación Media T-P Industrial Adultos (Decreto N° 152/1989)',
                561 => '561:Educación Media T-P Industrial Adultos (Decreto N° 152/1989)',
                563 => '563:Educación Media T-P Industrial Adultos (Decreto N° 1000/2009)',
                610 => '610:Enseñanza Media T-P Técnica Niños y Jóvenes',
                660 => '660:Educación Media T-P Técnica Adultos (Decreto N° 152/1989)',
                661 => '661:Educación Media T-P Técnica Adultos (Decreto N° 152/1989)',
                663 => '663:Educación Media T-P Técnica Adultos (Decreto N° 1000/2009)',
                710 => '710:Enseñanza Media T-P Agrícola Niños y Jóvenes',
                760 => '760:Educación Media T-P Agrícola Adultos (Decreto N° 152/1989)',
                761 => '761:Educación Media T-P Agrícola Adultos (Decreto N° 152/1989)',
                763 => '763:Educación Media T-P Agrícola Adultos (Decreto N° 1000/2009)',
                810 => '810:Enseñanza Media T-P Marítima Niños y Jóvenes',
                860 => '860:Enseñanza Media T-P Marítima Adultos (Decreto N° 152/1989)',
                863 => '863:Enseñanza Media T-P Marítima Adultos (Decreto N° 1000/2009)',
                910 => '910:Enseñanza Media Artística Niños y Jóvenes',
                963 => '963:Enseñanza Media Artística Adultos'
            );


            $largo = count($especialidadOrganizationRel) + $tiCu;

            for ($cen = $tiCu; $cen < $largo; $cen++) {
                $cEnse = ($cen - $tiCu) + 1;

                $OrganizationId = intval('1008' . $cEnse);

                // $OrganizationLocationId++;
                // $OrganizationTelephoneId++;
                // $OrganizationEmailId++;
                // $RefOrganizationRelationshipId++;
                // $OrganizationIdentifierId++;
                // $OrganizationOperationalStatusId++;
                // $OrganizationIndicatorId++;
                $OrganizationRelationshipId++;


                $idCodigoTipo = $nivelesCursoOrganizationRel[$cEnse - 1]['idCodigoTipo'];
                $Name = substr($codEnse[$idCodigoTipo], 0, 128);

                $_Organizaciones[$cen]['Organization'][] = array(

                    'OrganizationId' => (int)$OrganizationId,
                    'Name' => $Name,
                    'RefOrganizationTypeId' => 45,
                    // 'ShortName'      => $ShortName,
                    // 'RegionGeoJSON' => '',

                );


                $_Organizaciones[$cen]['OrganizationLocation'] = array(

                    // 'OrganizationLocationId' => $OrganizationLocationId,
                    // 'OrganizationId' => $OrganizationId,
                    // 'LocationId' => '',
                    // 'RefOrganizationLocationTypeId' => ''

                );


                // OrganizationTelephone
                $_Organizaciones[$cen]['OrganizationTelephone'] = array(

                    // 'OrganizationTelephoneId'   => $OrganizationTelephoneId,
                    // 'OrganizationId'            => $OrganizationId,
                    // 'TelephoneNumber'           => $TelephoneNumber,
                    // 'PrimaryTelephoneNumberIndicator' => ,
                    // 'RefInstitutionTelephoneTypeId'   =>

                );


                // OrganizationEmail

                $_Organizaciones[$cen]['OrganizationEmail'] = array(

                    // 'OrganizationEmailId'   => (int)$OrganizationEmailId,
                    // 'OrganizationId'        => (int)$OrganizationId,
                    // 'ElectronicMailAddress' => $ElectronicMailAddress,
                    // 'RefEmailTypeId'        =>

                );


                // OrganizationIdentifier

                $_Organizaciones[$cen]['OrganizationIdentifier'] = array(

                    // 'OrganizationIdentifierId'  => (int)$OrganizationIdentifierId,
                    // 'Identifier'                => $Identifier,
                    // 'RefOrganizationIdentificationSystemId' => ,
                    // 'OrganizationId'            => (int)$OrganizationId
                    // 'RefOrganizationIdentifierTypeId' => ''

                );


                $_Organizaciones[$cen]['OrganizationWebsite'] = array(

                    // 'OrganizationId' => $OrganizationId
                    // 'Website' => '',

                );


                $_Organizaciones[$cen]['OrganizationOperationalStatus'] = array(

                    // 'OrganizationOperationalStatusId' => (int)$OrganizationOperationalStatusId,
                    // 'OrganizationId'                  => (int)$OrganizationId,
                    // 'RefOperationalStatusId'          => ,
                    // 'OperationalStatusEffectiveDate' => ''

                );


                $_Organizaciones[$cen]['OrganizationIndicator'] = array(

                    // 'OrganizationIndicatorId' => $OrganizationIndicatorId,
                    // 'OrganizationId' => $OrganizationId
                    // 'IndicatorValue' => '',
                    // 'RefOrganizationIndicatorId' => ''

                );


                $Parent_OrganizationId = $tipoCursoOrganizationRel[$cEnse]['OrganizationId'];
                $_Organizaciones[$cen]['OrganizationRelationship'][] = array(

                    'OrganizationRelationshipId' => $OrganizationRelationshipId,
                    'Parent_OrganizationId' => (int)$Parent_OrganizationId,
                    'OrganizationId' => $OrganizationId,
                    'RefOrganizationRelationshipId' => 2

                );


                $codEnseOrganizationRel[$cEnse] = [
                    'OrganizationId' => $OrganizationId,
                    'name' => $Name,
                    'Item' => $cen
                ];

            }


            // Grado


            // 1000 = Establecimientos
            // 1001 = Modalidad
            // 1002 = Jornada
            // 1003 = Niveles
            // 1004 = Rama
            // 1005 = Sector económico
            // 1006 = Especialidad
            // 1007 = Tipo Curso
            // 1008 = Cod Ense
            // 1009 = Grados
            // 1010 = Cursos
            // 1011 = Asignaturas


            $grados = $modelJson->listarNivelesCursosJSON($idperiodo, $idestablecimiento, $lista_curso);

            $largo = count($grados) + $cen;
            for ($ni = $cen; $ni < $largo; $ni++) {

                $nive = $ni - $cen;
                $OrganizationId = intval('1009' . $grados[$nive]->idCodigoGrado);

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

                $idCodigo = ($grados[$nive]->idCodigo == 10) ? '0' . $grados[$nive]->idCodigo : $grados[$nive]->idCodigo;
                $idGrado = ($grados[$nive]->idGrado < 10) ? '0' . $grados[$nive]->idGrado : $grados[$nive]->idGrado;

                $Name = substr(strval($idCodigo) . '.' . strval($idGrado) . ':' . str_replace('°','º',$grados[$nive]->nombreGrado), 0, 128);
                $ShortName = substr($grados[$nive]->letra, 0, 30);
                $_Organizaciones[$ni]['Organization'][] = array(

                    'OrganizationId' => (int)$OrganizationId,
                    'Name' => $Name,
                    'RefOrganizationTypeId' => 46, // Ref K12School
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
                // $TelephoneNumber = substr($grados[$ni]->telefono, 0, 24);

                $_Organizaciones[$ni]['OrganizationTelephone'] = array(

                    // 'OrganizationTelephoneId' => $OrganizationTelephoneId,
                    // 'OrganizationId' => $OrganizationId,
                    // 'TelephoneNumber' => $TelephoneNumber,
                    // 'PrimaryTelephoneNumberIndicator' => 'Yes',
                    // 'RefInstitutionTelephoneTypeId' => 'Main'

                );


                // OrganizationEmail
                // $electronicMailAddress = substr($grados[$ni]->correo, 0, 128);

                $_Organizaciones[$ni]['OrganizationEmail'] = array(

                    // 'OrganizationEmailId' => (int)$OrganizationEmailId,
                    // 'OrganizationId' => (int)$OrganizationId,
                    // 'ElectronicMailAddress' => $nilectronicMailAddress
                    // 'RefEmailTypeId' => ''

                );


                // OrganizationIdentifier
                $Identifier = substr($grados[$nive]->idCodigoGrado, 0, 40);

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

                $Parent_OrganizationId = $codEnseOrganizationRel[$nive + 1]['OrganizationId'];
                $_Organizaciones[$ni]['OrganizationRelationship'][] = array(

                    'OrganizationRelationshipId' => $OrganizationRelationshipId,
                    'Parent_OrganizationId' => (int)$Parent_OrganizationId,
                    'OrganizationId' => $OrganizationId,
                    'RefOrganizationRelationshipId' => 2

                );


                $gradosOrganizationRel[$nive] = [
                    'OrganizationId' => $OrganizationId,
                    'Item' => $nive
                ];

            }


            // CURSOS
            $cursos = $modelJson->listarCursosJSON($idperiodo, $idestablecimiento);

            $largo = count($cursos) + $ni;
            for ($c = $ni; $c < $largo; $c++) {

                $curs = $c - $ni;
                $OrganizationId = intval('1010' . $cursos[$curs]->idCursos);
                $OrganizationId++;
                $OrganizationLocationId++;
                $OrganizationTelephoneId++;
                $OrganizationEmailId++;
                $RefOrganizationRelationshipId++;
                $OrganizationIdentifierId++;
                $OrganizationOperationalStatusId++;
                $OrganizationIndicatorId++;


                // Organization
                $Name = substr($cursos[$curs]->letra, 0, 128);
                $ShortName = substr($cursos[$curs]->letra, 0, 30);

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


                // OrganizationIdentifier
                $Identifier = substr($cursos[$curs]->idCursos, 0, 40);

                $_Organizaciones[$c]['OrganizationIdentifier'][] = array(

                    'OrganizationIdentifierId' => (int)$OrganizationIdentifierId,
                    'Identifier' => $Identifier,
                    'RefOrganizationIdentificationSystemId' => 4, // Ref Other
                    'OrganizationId' => (int)$OrganizationId
                    // 'RefOrganizationIdentifierTypeId' => 18
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


                $OrganizationRelationshipId++;
                $Parent_OrganizationId = $gradosOrganizationRel[$curs]['OrganizationId'];

                $_Organizaciones[$c]['OrganizationRelationship'][] = array(

                    'OrganizationRelationshipId' => $OrganizationRelationshipId,
                    'Parent_OrganizationId' => (int)$Parent_OrganizationId,
                    'OrganizationId' => $OrganizationId,
                    'RefOrganizationRelationshipId' => 2
                    //
                );


                $cursosOrganizationRel[$cursos[$curs]->idCursos] = [
                    'OrganizationId' => $OrganizationId,
                    'Item' => $curs
                ];
            }


            // ASIGNATURAS
            $asignaturas = $modelJson->listarAsignaturasJSON($idperiodo, $idestablecimiento, $lista_curso);

            $largo = count($asignaturas) + $c;
            for ($a = $c; $a < $largo; $a++) {

                $asig = $a - $c;
                $OrganizationId = intval('1011' . $asignaturas[$asig]->idAsignatura);
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
                $Name = mb_convert_encoding(substr($asignaturas[$asig]->nombreAsignatura, 0, 128), 'UTF-8', 'UTF-8');
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


                // $OrganizationRelationshipId++;

                $Parent_OrganizationId = $cursosOrganizationRel[$asignaturas[$asig]['idCursos']]['OrganizationId'];
                $_Organizaciones[$a]['OrganizationRelationship'][] = array(

                    'OrganizationRelationshipId' => $OrganizationRelationshipId,
                    'Parent_OrganizationId' => (int)$Parent_OrganizationId,
                    'OrganizationId' => $OrganizationId,
                    'RefOrganizationRelationshipId' => 2
                    //
                );

                $asignaturasOrganizationRel[$asignaturas[$asig]->idAsignatura] = [
                    'OrganizationId' => $OrganizationId,
                    'Item' => $asig
                ];
            }

            /* ----------------------------------- Fin _Organizaciones ----------------------------------- */

            /* ----------------------------------- _Establecimientos ----------------------------------- */


            // ESTABLECIMIENTOS
            $establecimientos2 = $modelJson->listarEstablecimientosJSON($idperiodo, $idestablecimiento);

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
                    'RefGradeLevelId' => 170, // ??? Ref 001210

                );


                $_Establecimientos[$es]['K12SchoolStatus'][] = array(

                    'OrganizationId' => $OrganizationId,
                    //'RefMagnetSpecialProgramId'=> '',
                    //'RefAlternativeSchoolFocusId'=> '',
                    'RefInternetAccessId' => 2, //1-Acceso Rapido 2-Hay disponible una conexión a Internet de menos de alta velocidad.
                    //'RefRestructuringActionId'=> '',
                    //'RefTitleISchoolStatusId' => '',
                    //'ConsolidatedMepFundsStatus' => '',
                    //'RefNationalSchoolLunchProgramStatusId' => '',
                    //'RefVirtualSchoolStatusId'=> '', //
                    //1.The school focuses on a systematic program of virtual instruction but includes some physical meetings among students or with teachers.
                    //2.La escuela no tiene un edificio físico donde los estudiantes se reúnan entre sí o con los maestros y toda la instrucción es virtual.
                    //3.La escuela no ofrece ninguna instrucción virtual.
                    //4.La escuela ofrece cursos virtuales, pero la instrucción virtual no es el medio principal de instrucción.


                );


                $_Establecimientos[$es]['K12SchoolImprovement'][] = array(

                    'K12SchoolImprovementId' => (int)$K12SchoolImprovementId,
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


            $establecimientos = $modelJson->listarEstablecimientosJSON($idperiodo, $idestablecimiento);
            $eventosEstableci = $modelJson->getEventosEstablecimientoJSON($idperiodo, $idestablecimiento);


            $largo = count($establecimientos);
            for ($est = 0; $est < $largo; $est++) {

                $OrganizationCalendarId++;

                $calendariosEstablecimiento[$establecimientos[$est]['idEstablecimiento']] = $OrganizationCalendarId;

                $CalendarYear = substr($eventosEstableci[0]['nombrePeriodo'], 0, 4);
                $_Calendarios[$est]['OrganizationCalendar'][] = array(

                    'OrganizationCalendarId' => $OrganizationCalendarId,
                    'OrganizationId' => $establecimientosOrganizationRel[$establecimientos[$est]['idEstablecimiento']]['OrganizationId'],
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
//                'OrganizationCalendarEventId' => $OrganizationCalendarEventId,
//                'OrganizationCalendarId'=> $OrganizationCalendarId
                    //----'Name' =>'',
                    //----'EventDate' =>'',
                    //'RefCalendarEventType' =>'',

                );
            }


            // CALENDARIOS CURSOS
            $cursos = $modelJson->listarCursosJSON($idperiodo, $idestablecimiento);

            //$listaeventos = $modelJson->getdiaseventosJSON($idperiodo, $idestablecimiento);

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

                    // 'OrganizationCalendarEventId' => (int)$OrganizationCalendarEventId,
                    // 'OrganizationCalendarId' => (int)$OrganizationCalendarId
                    // 'Name'=>'',
                    // 'EventDate' => '',
                    // 'RefCalendarEventType' => '',

                );

                if (count($listaeventos) > 0) {

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
            $asignaturas = $modelJson->listarAsignaturasJSON($idperiodo, $idestablecimiento, $lista_curso);

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

                // 'OrganizationPersonRoleId' => ,
                // 'RefParticipationTypeId' => ,
                // 'RefProgramExitReasonId' => ,
                // 'ParticipationStatus' =>

            );


            $_Intervenciones[0]['IndividualizedProgram'] = array(

                // 'IndividualizedProgramId' => '',
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


                $OrganizationId = $cursosOrganizationRel[$cursos2[$c2]->idCursos]['OrganizationId'];

                // Classroom
                $ClassroomIdentifier = (String)$cursos2[$c2]->idCursos;

                $LocationId = (integer)$LocationId;


                // Location
                $_Cursos[$c2]['Location'][] = array(

                    'LocationId' => $LocationId

                );


                // LocationAddress
                $_Cursos[$c2]['LocationAddress'] = array(

                    // 'LocationId' => $LocationId,
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
                $_Cursos[$c2]['FacilityLocation'] = array(

                    // 'FacilityLocationId' => (integer)$FacilityLocationId,
                    // 'FacilityId' => $FacilityLocationId,
                    // 'LocationId' => $LocationId

                );


                // Classroom
                $_Cursos[$c2]['Classroom'][] = array(

                    'LocationId' => $LocationId,
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


                // CourseSectionLocation
                $_Cursos[$c2]['CourseSectionLocation'] = array(

                    // 'CourseSectionLocationId' => (Int)$CourseSectionLocationId,
                    // 'LocationId'            => $LocationId,
                    // 'OrganizationId'        => (Int)$OrganizationId
                    // 'RefInstructionLocationTypeId' => ''

                );


                // Course
                // $Description = $cursos2[$c2]->numeroGrado;
                // $SubjectAbbreviation = $cursos2[$c2]->letra;


                // Curso
                $_Cursos[$c2]['Course'][] = array(

                    'OrganizationId' => (Int)$OrganizationId,
                    // 'Description'           => $Description,
                    // 'SubjectAbbreviation' => $SubjectAbbreviation,
                    // 'SCEDSequenceOfCourse' => '',
                    'InstructionalMinutes' => (Int)45,
                    // 'RefCourseLevelCharacteristicsId' => '',
                    // 'RefCourseCreditUnitId' => '',
                    'CreditValue' => (Float)0.1,
                    // 'RefInstructionLanguage' => '',
                    // 'CertificationDescription' => '',
                    // 'RefCourseApplicableEducationLevelId' => '',
                    'RepeatabilityMaximumNumber' => (Int)2

                );


                // Buscamos las asignaturas del curso y seteamoos su OrganizationId
                $listaAsignaturas = $modelJson->listarAsignaturasCursosJSON($ClassroomIdentifier, $idperiodo, $idestablecimiento);


                foreach ($listaAsignaturas as $key => $asignatura) {

                    $CourseSection++;

                    $OrganizationAsignaturasId = $asignaturasOrganizationRel[$asignatura->idAsignatura]['OrganizationId'];

                    // Asignaturas
                    $_Cursos[$c2]['CourseSection'][] = array(   // Asignaturas

                        'OrganizationId' => $OrganizationAsignaturasId,
                        'AvailableCarnegieUnitCredit' => (Float)0.1,
                        // 'RefCourseSectionDeliveryModeId' => '',
                        // 'RefSingleSexClassStatusId' => '',
                        'TimeRequiredForCompletion' => (Float)0.1,

                        'CourseId' => (int)$OrganizationId,

                        // 'RefAdditionalCreditTypeId' => '',
                        // 'RefInstructionLanguageId' => '',
                        'VirtualIndicator' => false,
                        // ---- 'OrganizationCalendarSessionId' => '',
                        // 'RefCreditTypeEarnedId' => '',
                        // 'RefAdvancedPlacementCourseCodeId' => '',
                        'MaximumCapacity' => 30, // Máx capacidad del curso.
                        // 'RelatedCompetencyFrameworkItems' => ''

                    );

                    // Bloques
                    $listaBloques = $modelJson->listarBloquesAsignaturasJSON($idperiodo, $idestablecimiento, $ClassroomIdentifier, $asignatura->idAsignatura);

                    foreach ($listaBloques as $keyBlo => $bloque) { // aca

                        $CourseSectionScheduleId++;

                        $_Cursos[$c2]['CourseSectionSchedule'][] = array(

                            'CourseSectionScheduleId' => (Int)$CourseSectionScheduleId,
                            'OrganizationId' => (Int)$OrganizationAsignaturasId,
                            'ClassMeetingDays' => $dias[$bloque->dia],
                            'ClassBeginningTime' => $bloque->tiempoInicio,
                            'ClassEndingTime' => $bloque->tiempoTermino,
                            // 'ClassPeriod' => '',
                            // 'TimeDayIdentifier' => ''

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
                'Jefe(a) UTP' => 2,
                'Inspector(a) Jefe' => 3,
                'Profesor Jefe' => 4,
                'Docente' => 5,
                'Alumno' => 6
            );

//            $roles = array(
//                'Director' => 1,
//                'Jefe_Utp'=>2,
//                'Inspector' => 3,
//                'Profesor_Jefe' => 4,
//                'Docente' => 5,
//                'Estudiante' => 6,
//            );


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
//                $_ComunidadEducativa[$ce]['K12StudentEnrollment'] = array(
//
////                'OrganizationPersonRoleId' => $OrganizationPersonRoleId,
////                'RefEntryGradeLevelId' => $RefEntryGradeLevelId[$alumno->idCodigoGrado],
////                // 'RefPublicSchoolResidence'=> '',
////                'RefEnrollmentStatusId' => [
////                    'Code' => '01811',
////                    'Description' => 'Currently enrolled'
////                ],
////                // 'RefEntryType'=> '',
////                'RefExitGradeLevel' => $RefExitGradeLevel,
////                // 'RefExitOrWithdrawalStatusId'=> '',//Permanent-Temporary (Una indicaci�n de si una instancia de salida / retirada de estudiantes se considera de naturaleza permanente o temporal.)
////                // 'RefExitOrWithdrawalTypeId'=> '',//Las circunstancias bajo las cuales el estudiante sali� de la membres�a en una instituci�n educativa.
////                // 'DisplacedStudentStatus'=> '',//Un estudiante que se inscribi�, o que es elegible para la inscripci�n, pero se ha inscrito en otro lugar debido a una crisis.
////                // 'RefEndOfTermStatusId'=> '', // Si Repitio el periodo anterior
////                // 'RefPromotionReasonId'=> '', // La raz�n por la cual un alumno paso de curso
////                // 'RefNonPromotionReasonId'=> '', // La raz�n por la cual un alumno no paso de curso
////
////                'RefFoodServiceEligibilityId' => $RefFoodServiceEligibilityId,//Una indicaci�n del nivel de elegibilidad de un estudiante para participar en el Programa Nacional de Almuerzos Escolares para los programas de desayuno, almuerzo, merienda, cena y leche.
//                    // 'FirstEntryDateIntoUSSchool'=> '',//El a�o, mes y d�a de la inscripci�n inicial de una persona en una escuela de los Estados Unidos.
//                    // 'RefDirectoryInformationBlockStatusId'=> '',
//                    // 'NSLPDirectCertificationIndicator'=> '',
//                    // 'RefStudentEnrollmentAccessTypeId'=> ''
//                );


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
                        'RoleId' => $roles['Profesor Jefe'],
                        'Name' => 'Profesor Jefe',
                        'RefJurisdictionId' => 1
                    ],
                    [
                        'RoleId' => $roles['Alumno'],
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
                $alumnos = $modelJson->listarAlumnosCursoJSON($cursoComunidadE[$ce]->idCursos, $idperiodo, $idestablecimiento);
                foreach ($alumnos as $keyAl => $alumno) {

                    // Alumno x Establecimiento
                    $OrganizationPersonRoleId++;

                    if ($alumno->fechaInscripcion == '0000-00-00') {

                        $_ComunidadEducativa[$ce]['OrganizationPersonRole'][] = [

                            'OrganizationPersonRoleId' => (Int)$OrganizationPersonRoleId,
                            'OrganizationId' => (Int)$establecimientosOrganizationRel[$alumno->idEstablecimiento]['OrganizationId'],
                            'PersonId' => $alumnosRel[$alumno->idAlumnos]['PersonId'],
                            'RoleId' => $roles['Alumno'],
                            //'EntryDate' => '2019-03-01',
                            // 'ExitDate' => ''

                        ];

                    } else {

                        $_ComunidadEducativa[$ce]['OrganizationPersonRole'][] = [

                            'OrganizationPersonRoleId' => (Int)$OrganizationPersonRoleId,
                            'OrganizationId' => (Int)$establecimientosOrganizationRel[$alumno->idEstablecimiento]['OrganizationId'],
                            'PersonId' => $alumnosRel[$alumno->idAlumnos]['PersonId'],
                            'RoleId' => $roles['Alumno'],
                            'EntryDate' => $alumno->fechaInscripcion,
                            // 'ExitDate' => ''

                        ];

                    }


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
                        'RoleId' => $roles['Alumno'],
                        // 'EntryDate' => '',
                        // 'ExitDate' => ''

                    ];


                    // Asistencia Diaria x Alumno
                    $datosasistenciadiaria = $modelJson->listarAsistenciaDiariaAlumnoJSON($alumno->idAlumnos, $idperiodo, $alumno->idCursosActual);
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
                    $alumnosAsignatura = $modelJson->listarAlumnosAsignaturaPorCursoJSON($alumno->idAlumnosActual, $alumno->idCursosActual, $idperiodo, $idestablecimiento);
                    foreach ($alumnosAsignatura as $keyAlAs => $alumnoAsignatura) {

                        $OrganizationPersonRoleId++;
                        $_ComunidadEducativa[$ce]['OrganizationPersonRole'][] = [

                            'OrganizationPersonRoleId' => (Int)$OrganizationPersonRoleId,
                            'OrganizationId' => (Int)$asignaturasOrganizationRel[$alumnoAsignatura->idAsignatura]['OrganizationId'],
                            'PersonId' => $alumnosRel[$alumno->idAlumnos]['PersonId'],
                            'RoleId' => $roles['Alumno'],
                            // 'EntryDate' => '',
                            // 'ExitDate' => ''

                        ];

                        $refAlumnoAsignaturaRoleId[$alumno->idAlumnos . '' . $alumnoAsignatura->idAsignatura] = array(
                            'OrganizationPersonRole' => $OrganizationPersonRoleId
                        );


                        $asistenciasBloque = $modelJson->listarAsistenciaBloqueAlumnoJSON($alumno->idAlumnos, $idperiodo, $alumno->idCursosActual, $alumnoAsignatura->idAsignatura);

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

                $docentes = $modelJson->listarDocentesPorCursosJSON($idperiodo, $idestablecimiento, $cursoComunidadE[$ce]->idCursos);
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

                    $RefTeachingAssignmentRoleId = 2; // Ref TeamTeacher
                    $PrimaryAssignment = True;

                    if ($docente->idCuenta == $docente->idCuentaJefe) {
                        $RefTeachingAssignmentRoleId = 1; // Ref LeadTeacher
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
                    $asignaturasDoc = $modelJson->listarAsignaturasDocenteJSON($cursoComunidadE[$ce]->idCursos, $docente->idCuenta, $idperiodo, $idestablecimiento);

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
                            'OrganizationPersonRole' => $OrganizationPersonRoleId,
                            'PersonId' => $PersonId
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
            $IncidentId++;
            $K12StudentDisciplineId++;
            $_Incidentes[0]['Incident'] = array(

//                'IncidentId' => $IncidentId,
//                'IncidentIdentifier' => '',
//                'IncidentDate' => '',
//                'IncidentTime' => '',
//                'RefIncidentTimeDescriptionCodeId' => '',
//                'IncidentDescription' => '',
//                'RefIncidentBehaviorId' => '',
//                'RefIncidentInjuryTypeId' => '',
//                'RefWeaponTypeId' => '',
//                'IncidentCost' => '',
//                'OrganizationPersonRoleId' => '',
//                'IncidentReporterId' => '',
//                'RefIncidentReporterTypeId' => '',
//                'RefIncidentLocationId' => '',
//                'RefFirearmTypeId' => '',
//                'RegulationViolatedDescription' => '',
//                'RelatedToDisabilityManifestationInd' => '',
//                'ReportedToLawEnforcementInd' => '',
//                'RefIncidentMultipleOffenseTypeId' => '',
//                'RefIncidentPerpetratorInjuryTypeId' => ''

            );


            $_Incidentes[0]['K12StudentDiscipline'] = array(

//                'K12StudentDisciplineId' => $K12StudentDisciplineId,
//                'OrganizationPersonRoleId' => null,
//                // 'RefDisciplineReasonId' => '',
//                // 'RefDisciplinaryActionTakenId' => '',
//                // 'DisciplinaryActionStartDate' => '',
//                // 'DisciplinaryActionEndDate' => '',
//                'DurationOfDisciplinaryAction' => null, //La duración, en días escolares, de la acción disciplinaria.
//                // 'RefDisciplineLengthDifferenceReasonId' => '',
//                'FullYearExpulsion' => null,
//                'ShortenedExpulsion' => null, // Una expulsión con o sin servicios que el superintendente o administrador principal de un distrito escolar acorta a un término de menos de un año.
//                'EducationalServicesAfterRemoval' => null, //Una indicación de si los niños (estudiantes) recibieron servicios educativos cuando fueron retirados del programa escolar regular por razones disciplinarias.
//                // 'RefIdeaInterimRemovalId' => '',
//                // 'RefIdeaInterimRemovalReasonId' => '',
//                'RelatedToZeroTolerancePolicy' => null, // Una indicación de si alguna de las acciones disciplinarias tomadas contra un estudiante se impusieron como consecuencia de las políticas estatales o locales de tolerancia cero.
//                'IncidentId' => null,
//                'IEPPlacementMeetingIndicator' => null, // Una indicación de que el oficial de recursos escolares o cualquier otro oficial de la ley fue notificado sobre el incidente, independientemente de si se toman medidas oficiales.
//                // 'RefDisciplineMethodFirearmsId' => '',
//                // 'RefDisciplineMethodOfCwdId' => '',
//                // 'RefIDEADisciplineMethodFirearmId' => ''

            );


            // FALTAN
            $_Incidentes[0]['IncidentPerson'] = array(

//                'IncidentId' => $IncidentId,
//                'PersonId' => null,
//                'Identifier' => null,
//                'RefIncidentPersonRoleTypeId' => null, // Ref Perpetrator
//                'RefIncidentPersonTypeId' => null  // Ref 04710

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


            $jsonencoded = json_encode($_JSON, JSON_UNESCAPED_UNICODE);

            $direccion = fopen("ArchivoPrueba_2.json", 'x+');
            fwrite($direccion, $jsonencoded);
            fclose($direccion);

            //header('Content-disposition: attachment; filename=jsonFile.json');
            //header('Content-type: application/json');

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
