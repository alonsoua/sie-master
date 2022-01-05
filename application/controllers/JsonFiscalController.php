<?php

class JsonFiscalController extends Zend_Controller_Action
{

    public function init()
    {
        
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();
    }

    public function indexAction()
    {

        /* RQM 26: JSON */

        $periodo      = new Zend_Session_Namespace('periodo');
        $idperiodo    = $periodo->periodo;

        $idestablecimiento = 4;

        $tablacomunas = new Application_Model_DbTable_Comuna();  

        /* ----------------------------------- _Personas ----------------------------------- */
        // ALUMNOS
        $tablaalumnos = new Application_Model_DbTable_Alumnos();
        $alumnos      = $tablaalumnos->listarAlumnosJSON($idperiodo, $idestablecimiento);
        
        $PersonId = 0;
        $PersonIdentifierId = 0;
        $PersonAddressId = 0;
        $PersonEmailAddressId = 0;
        $PersonTelephoneId = 0;
        $PersonStatusId = 0;
        $PersonLanguageId = 0;
        $PersonRelationshipId = 0;
        $PersonDegreeOrCertificateId = 0;
                

        foreach ($alumnos as $key => $alumno) {      

            // AUTOINCREMENTS
            $PersonId++;
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
            if (strpos($alumno->nombres, ' ') !== false) {                
                        
                $nombres = explode(" ", $alumno->nombres);
                $nombre = $nombres[0];
                
                if (count($nombres) == 2) {
                    $segundoNombre = $nombres[1];
                } else if (count($nombres) >= 3) {
                    $segundoNombre = $nombres[1].' '.$nombres[2];
                } else {
                    $segundoNombre = $nombres[1];
                }
            } else {
                $nombre = $alumno->nombres;
                $segundoNombre = '';
            }

            // Person
            $FirstName      = substr($nombre, 0 , 45);
            $MiddleName     = substr($segundoNombre, 0 , 45);
            $LastName       = substr($alumno->apaterno, 0 , 45);
            $SecondLastName = substr($alumno->amaterno, 0 , 45);
           
            $Birthdate      = $alumno->fechanacimiento; 
            //FALTA DAR FORMATO ^[0-9]{4}-[0-9]{2}-[0-9]{2}$

            $sexo = $alumno->sexo;            
            switch ($sexo) {
                
                case 0:
                    $sexId = 'NotSelected';
                    break;
                
                case 1:
                    $sexId = 'Male';
                    break;
                
                case 2:
                    $sexId = 'Female';
                    break;

                default:
                    $sexId = 'NotSelected';
                    break;

            }

            $RefSexId = $sexId;

            // PersonIdentifier
            $Identifier = substr($alumno->rutAlumno, 0 , 40);

            // PersonAddress            
            $comuna = $tablacomunas->getcomuna($alumno->comuna);
            
            $StreetNumberAndName = substr($alumno->direccion, 0 , 40);                   
            $City                = substr($comuna[0]->nombreComuna, 0 , 30);
                                
            $cero       = ($comuna[0]->idRegion < 10) ? '0': '';
            $RefStateId = 'R'.$cero.''.$comuna[0]->idRegion; //Region     
            $StateName  = $comuna[0]->nombreRegion;

            $AddressCountyName   = substr($comuna[0]->nombreComuna, 0 , 30);
            $RefCountyId         = $comuna[0]->idComuna;

            // PersonEmailAddress                        
            $EmailAddress = substr($alumno->correo, 0 , 128);

            // PersonTelephone            
            $TelephoneNumber = substr($alumno->telefono, 0 , 24);                    

            // PersonStatus
            $StatusValue = $alumno->idEstadoActual;


            $_Personas[$PersonId] = [ 
                'Person' => [
                    'PersonId'                              => $PersonId,
                    'FirstName'                             => $FirstName,
                    'MiddleName'                            => $MiddleName,
                    'LastName'                              => $LastName,
                    'SecondLastName'                        => $SecondLastName,
                    // 'GenerationCode'                        => '',
                    // 'Prefix'                                => '',
                    'Birthdate'                             => $Birthdate, //Falta formatear
                    'RefSexId'                              => [ 'Code' => $RefSexId ]
                    // 'HispanicLatinoEthnicity'               => '',// CONSULTAR ETNIA ARRIBA
                    // 'RefUSCitizenshipStatusId'              => '',// Referencia si es ciudadano de USA, al ser "Permanent resident"
                    // // es ciudadano permanente de usa?. 
                                        
                    // 'RefVisaTypeId'                         => '',// va de la mano con el anterior
                    // 'RefStateOfResidenceId'                 => '',// estado donde vive
                    // 'RefProofOfResidencyTypeId'             => '',// prueba de residencia
                    // 'RefHighestEducationLevelCompletedId'   => '',
                    // 'RefPersonalInformationVerificationId'  => '',// Verificador de información personal
                    // 'BirthdateVerification'                 => '',// Verificador de cumpleaños
                    // 'RefTribalAffiliationId'                => '' // entidad tribal nativa americana
                ],

                'PersonIdentifier' => [
                    'PersonIdentifierId'                    => $PersonIdentifierId,
                    'PersonId'                              => $PersonId,
                    'Identifier'                            => $Identifier,
                    'RefPersonIdentificationSystemId'       => [
                                'Code' => 'RUN',
                                'Description' => 'Rol Unico Nacional'
                    ]
                    // 'RefPersonalInformationVerificationId'  => ''
                ],

                'PersonBirthplace' => [
                    'PersonId'                              => $PersonId
                    // 'City'                                  => '',
                    // 'RefStateId'                            => '',
                    // 'RefCountryId'                          => ''
                ],

                'PersonAddress' => [
                    'PersonAddressId'                       => $PersonAddressId,
                    'PersonId'                              => $PersonId,
                    // 'RefPersonLocationTypeId'               => '',
                    'StreetNumberAndName'                   => $StreetNumberAndName,
                    // 'ApartmentRoomOrSuiteNumber'            => '',
                    'City'                                  => $City,

                    'RefStateId'                            => [ 'Code'      => $RefStateId, 
                                                                 'StateName' => $StateName                              
                                                                ],

                    // 'PostalCode'                            => '',
                    'AddressCountyName'                     => $AddressCountyName,
                    'RefCountyId'                           => [ 'Code' => $RefCountyId ],
                    'RefCountryId'                          => 'CL'
                    // 'Latitude'                              => '',
                    // 'Longitude'                             => '',
                    // 'RefPersonalInformationVerificationId'  => ''                    
                ],

                'PersonEmailAddress' => [
                    'PersonEmailAddressId'                  => $PersonEmailAddressId,
                    'PersonId'                              => $PersonId,
                    'EmailAddress'                          => $EmailAddress,
                    'RefEmailTypeId'                        => 'Home'                
                ],

                'PersonTelephone' => [
                    'PersonTelephoneId'                     => $PersonTelephoneId,
                    'PersonId'                              => $PersonId,
                    'TelephoneNumber'                       => $alumno->telefono,
                    'PrimaryTelephoneNumberIndicator'       => 'Yes',
                    'RefPersonTelephoneNumberTypeId'        => 'Home'                
                ],

                'PersonStatus' => [
                    'PersonStatusId'                        => $PersonStatusId,
                    'PersonId'                              => $PersonId,
                    // 'RefPersonStatusTypeId'                 => $RefPersonStatusTypeId,
                    'StatusValue'                           => $StatusValue
                    // 'StatusStartDate'                       => '',
                    // 'StatusEndDate'                         => ''                
                ],

                'PersonLanguage' => [
                    'PersonLanguageId'                      => $PersonLanguageId,
                    'PersonId'                              => $PersonId
                    // 'RefLanguageId'                         => '',
                    // 'RefLanguageUseTypeId'                  => ''           
                ],

                'PersonDisability' => [
                    'PersonId'                                => $PersonId,
                    // 'PrimaryDisabilityTypeId'                 => '',
                    'DisabilityStatus'                        => 'No'
                    // 'RefAccommodationsNeededTypeId'           => '',
                    // 'RefDisabilityConditionTypeId'            => '',
                    // 'RefDisabilityDeterminationSourceTypeId'  => '',
                    // 'RefDisabilityConditionStatusCodeId'      => '',
                    // 'RefIDEADisabilityTypeId'                 => '',
                    // 'SignificantCognitiveDisabilityIndicator' => ''
                ],

                'PersonRelationship' => [
                    'PersonRelationshipId'                    => $PersonRelationshipId,
                    'PersonId'                                => $PersonId,
                    // 'RelatedPersonId'                         => '',
                    // 'RefPersonRelationshipId'                 => '',
                    // 'CustodialRelationshipIndicator'          => '',
                    // 'EmergencyContactInd'                     => '',
                    // 'ContactPriorityNumber'                   => '',
                    // 'ContactRestrictions'                     => '',
                    // 'LivesWithIndicator'                      => '',
                    // 'PrimaryContactIndicator'                 => ''
                ],

                'PersonDegreeOrCertificate' => [
                    'PersonDegreeOrCertificateId'                        => $PersonDegreeOrCertificateId,
                    'PersonId'                                           => $PersonId,
                    // 'DegreeOrCertificateTitleOrSubject'                  => '',
                    // 'RefDegreeOrCertificateTypeId'                       => '',
                    // 'AwardDate'                                          => '',
                    // 'NameOfInstitution'                                  => '',
                    // 'RefHigherEducationInstitutionAccreditationStatusId' => '',
                    // 'RefEducationVerificationMethodId'                   => ''
                ]
            ];

            $alumnosRel[$alumno->idAlumnos] = [
                'PersonId' => $PersonId,
            ];

        }

        // DOCENTES
        $cuentas  = new Application_Model_DbTable_Cuentas();
        $docentes = $cuentas->listarDocentesJSON($idperiodo, $idestablecimiento);

        foreach ($docentes as $key => $docente) {
                    
            $PersonId++;
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
                
            if (strpos($docente->nombrescuenta, ' ') !== false) {                
                        
                $nombres = explode(" ", $docente->nombrescuenta);
                $nombre = $nombres[0];
                
                if (count($nombres) == 2) {
                    $segundoNombre = $nombres[1];
                } else if (count($nombres) >= 3) {
                    $segundoNombre = $nombres[1].' '.$nombres[2];
                } else {
                    $segundoNombre = $nombres[1];
                }
            } else {
                $nombre = $docente->nombrescuenta;
                $segundoNombre = '';
            }

            // Person
            $FirstName      = substr($nombre, 0 , 45);        
            $MiddleName     = substr($segundoNombre, 0 , 45);
            $LastName       = substr($docente->paternocuenta, 0 , 45);
            $SecondLastName = substr($docente->maternocuenta, 0 , 45);
          

            // PersonIdentifier
            $Identifier = substr($docente->usuario, 0 , 40);

            // PersonAddress            
            $comuna = $tablacomunas->getcomuna($docente->comuna);
                        
            $City       = substr($comuna[0]->nombreComuna, 0 , 30);
            $cero       = ($comuna[0]->idRegion < 10) ? '0': '';
            $RefStateId = 'R'.$cero.''.$comuna[0]->idRegion; //Region     
            $StateName  = $comuna[0]->nombreRegion;

            $AddressCountyName   = substr($comuna[0]->nombreComuna, 0 , 30);
            $RefCountyId         = $comuna[0]->idComuna;

            // PersonEmailAddress                        
            $EmailAddress = substr($docente->correo, 0 , 128);

            // PersonStatus
            $StatusValue           = $docente->estadoCuenta;       
                                

            $_Personas[$PersonId] = [
                'Person' => [
                    'PersonId'                              => $PersonId,
                    'FirstName'                             => $FirstName,
                    // 'MiddleName'                            => $MiddleName,
                    'LastName'                              => $LastName,
                    'SecondLastName'                        => $SecondLastName,
                    // 'GenerationCode'                        => '',
                    // 'Prefix'                                => '',
                    // 'Birthdate'                             => '', //Falta formatear
                    // 'RefSexId'                              => '',
                    // 'HispanicLatinoEthnicity'               => '',// CONSULTAR ETNIA ARRIBA
                    // 'RefUSCitizenshipStatusId'              => '',// Referencia si es ciudadano de USA, al ser "Permanent resident"
                    // // es ciudadano permanente de usa?. 
                                        
                    // 'RefVisaTypeId'                         => '',// va de la mano con el anterior
                    // 'RefStateOfResidenceId'                 => '',// estado donde vive
                    // 'RefProofOfResidencyTypeId'             => '',// prueba de residencia
                    // 'RefHighestEducationLevelCompletedId'   => '',
                    // 'RefPersonalInformationVerificationId'  => '',// Verificador de información personal
                    // 'BirthdateVerification'                 => '',// Verificador de cumpleaños
                    // 'RefTribalAffiliationId'                => '' // entidad tribal nativa americana
                ],

                'PersonIdentifier' => [
                    'PersonIdentifierId'                    => $PersonIdentifierId,
                    'PersonId'                              => $PersonId,
                    'Identifier'                            => $Identifier,
                    'RefPersonIdentificationSystemId'       => [
                                'Code' => 'RUN',
                                'Description' => 'Rol Unico Nacional'
                    ]
                    // 'RefPersonalInformationVerificationId'  => ''

                ],

                'PersonBirthplace' => [
                    'PersonId'                              => $PersonId
                    // 'City'                                  => '',
                    // 'RefStateId'                            => '',
                    // 'RefCountryId'                          => ''
                ],

                'PersonAddress' => [
                    'PersonAddressId'                       => $PersonAddressId,
                    'PersonId'                              => $PersonId,
                    // 'RefPersonLocationTypeId'               => '',
                    // 'StreetNumberAndName'                   => '',
                    // 'ApartmentRoomOrSuiteNumber'            => '',
                    'City'                                  => $City,
                    'RefStateId'                            => [ 'Code'      => $RefStateId, 
                                                                 'StateName' => $StateName                              
                                                                ],
                    // 'PostalCode'                            => '',
                    'AddressCountyName'                     => $AddressCountyName,
                    'RefCountyId'                           => [ 'Code' => $RefCountyId ],    
                    'RefCountryId'                          => 'CL'
                    // 'Latitude'                              => '',
                    // 'Longitude'                             => '',
                    // 'RefPersonalInformationVerificationId'  => ''
                ],

                'PersonEmailAddress' => [
                    'PersonEmailAddressId'                  => $PersonEmailAddressId,
                    'PersonId'                              => $PersonId,
                    'EmailAddress'                          => $docente->correo,
                    'RefEmailTypeId'                        => 'Home'                
                ],

                'PersonTelephone' => [
                    'PersonTelephoneId'                     => $PersonTelephoneId,
                    'PersonId'                              => $PersonId
                    // 'TelephoneNumber'                       => '',
                    // 'PrimaryTelephoneNumberIndicator'       => '',
                    // 'RefPersonTelephoneNumberTypeId'        => ''                
                ],

                'PersonStatus' => [
                    'PersonStatusId'                        => $PersonStatusId,
                    'PersonId'                              => $PersonId,
                    // 'RefPersonStatusTypeId'                 => '',
                    'StatusValue'                           => $StatusValue
                    // 'StatusStartDate'                       => '',
                    // 'StatusEndDate'                         => ''                
                ],

                'PersonLanguage' => [
                    'PersonLanguageId'                      => $PersonLanguageId,
                    'PersonId'                              => $PersonId
                    // 'RefLanguageId'                         => '',
                    // 'RefLanguageUseTypeId'                  => ''           
                ],

                'PersonDisability' => [
                    'PersonId'                                => $PersonId
                    // 'PrimaryDisabilityTypeId'                 => '',
                    // 'DisabilityStatus'                        => '',
                    // 'RefAccommodationsNeededTypeId'           => '',
                    // 'RefDisabilityConditionTypeId'            => '',
                    // 'RefDisabilityDeterminationSourceTypeId'  => '',
                    // 'RefDisabilityConditionStatusCodeId'      => '',
                    // 'RefIDEADisabilityTypeId'                 => '',
                    // 'SignificantCognitiveDisabilityIndicator' => ''
                ],

                'PersonRelationship' => [
                    'PersonRelationshipId'                    => $PersonRelationshipId,
                    'PersonId'                                => $PersonId
                    // 'RelatedPersonId'                         => '',
                    // 'RefPersonRelationshipId'                 => '',
                    // 'CustodialRelationshipIndicator'          => '',
                    // 'EmergencyContactInd'                     => '',
                    // 'ContactPriorityNumber'                   => '',
                    // 'ContactRestrictions'                     => '',
                    // 'LivesWithIndicator'                      => '',
                    // 'PrimaryContactIndicator'                 => ''
                ],

                'PersonDegreeOrCertificate' => [
                    'PersonDegreeOrCertificateId'                        => $PersonDegreeOrCertificateId,
                    'PersonId'                                           => $PersonId
                    // 'DegreeOrCertificateTitleOrSubject'                  => '',
                    // 'RefDegreeOrCertificateTypeId'                       => '',
                    // 'AwardDate'                                          => '',
                    // 'NameOfInstitution'                                  => '',
                    // 'RefHigherEducationInstitutionAccreditationStatusId' => '',
                    // 'RefEducationVerificationMethodId'                   => ''
                ]
            ];
            $docentesRel[$docente->idCuenta] = ['PersonId' => $PersonId];
        }
       

        // APODERADO PRINCIPAL
        $apodera  = new Application_Model_DbTable_Apoderados();
        $apoderadosP = $apodera->listarApoderadosPrincipalJSON($idperiodo, $idestablecimiento);        

        foreach ($apoderadosP as $key => $apoderado) {

            $PersonId++;
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
            if (strpos($apoderado->nombreApoderado, ' ') !== false) {                
                        
                $nombres = explode(" ", $apoderado->nombreApoderado);
                $nombre = $nombres[0];                
                if (count($nombres) == 2) {
                    $segundoNombre = $nombres[1];
                } else if (count($nombres) >= 3) {
                    $segundoNombre = $nombres[1].' '.$nombres[2];
                } else {
                    $segundoNombre = $nombres[1];
                }
            } else {
                $nombre = $apoderado->nombreApoderado;
                $segundoNombre = '';
            }

            // Person
            $FirstName      = substr($nombre, 0 , 45);
            $MiddleName     = substr($segundoNombre, 0 , 45);
            $LastName       = substr($apoderado->paternoApoderado, 0 , 45);
            $SecondLastName = substr($apoderado->maternoApoderado, 0 , 45);
                       
            // PersonIdentifier
            $Identifier = substr($apoderado->rutApoderado, 0 , 40);

            // PersonAddress            
            $comuna = $tablacomunas->getcomuna($apoderado->comunaApoderado);
   
            $StreetNumberAndName = substr($apoderado->direccionApoderado, 0 , 40);                   
            $City                = substr($comuna[0]->nombreComuna, 0 , 30);
                                
            $cero       = ($comuna[0]->idRegion < 10) ? '0': '';
            $RefStateId = 'R'.$cero.''.$comuna[0]->idRegion; //Region     
            $StateName  = $comuna[0]->nombreRegion;

            $AddressCountyName   = substr($comuna[0]->nombreComuna, 0 , 30);
            $RefCountyId         = $comuna[0]->idComuna;

            // PersonEmailAddress                        
            $EmailAddress = substr($apoderado->correoApoderado, 0 , 128);

            //PersonTelephone            
            $TelephoneNumber = substr($apoderado->telefonoApoderado, 0 , 24);                      

            // PersonRelationship
            $RelatedPersonId = $alumnosRel[$apoderado->idAlumnos];

            $sexoAlumno = $apoderado->sexo; // 0 No Ingresado - 1 Niña - 2 Niño
            
            switch ($sexoAlumno) {
                case '0':
                    $RefPersonRelationshipId = [
                                                'Code' => 'Unknown',
                                                'Description' => 'Unknown',
                                                'Definition' => "The person is the learner''s Unknown."
                                            ];
                    break;
                
                case '1':
                    $RefPersonRelationshipId = [
                                                'Code' => 'Daughter',
                                                'Description' => 'Daughter',
                                                'Definition' => "The person is the learner''s Daughter."
                                            ];
                    break;

                case '2':
                    $RefPersonRelationshipId = [
                                                'Code' => 'Son',
                                                'Description' => 'Son',
                                                'Definition' => "The person is the learner''s Son."
                                            ];
                    break;

                default:
                     $RefPersonRelationshipId = [
                                                'Code' => 'Unknown',
                                                'Description' => 'Unknown',
                                                'Definition' => "The person is the learner''s Unknown."
                                            ];
                    break;
            }         
     

            $_Personas[$PersonId] = [ 
           
                'Person' => [
                    'PersonId'                              => $PersonId,
                    'FirstName'                             => $FirstName,
                    'MiddleName'                            => $MiddleName,
                    'LastName'                              => $LastName,
                    'SecondLastName'                        => $SecondLastName,
                    // 'GenerationCode'                        => '',
                    // 'Prefix'                                => '',
                    // 'Birthdate'                             => '', //Falta formatear
                    // 'RefSexId'                              => '',
                    // 'HispanicLatinoEthnicity'               => '',// CONSULTAR ETNIA ARRIBA
                    // 'RefUSCitizenshipStatusId'              => '',// Referencia si es ciudadano de USA, al ser "Permanent resident"
                    // // es ciudadano permanente de usa?. 
                                        
                    // 'RefVisaTypeId'                         => '',// va de la mano con el anterior
                    // 'RefStateOfResidenceId'                 => '',// estado donde vive
                    // 'RefProofOfResidencyTypeId'             => '',// prueba de residencia
                    // 'RefHighestEducationLevelCompletedId'   => '',
                    // 'RefPersonalInformationVerificationId'  => '',// Verificador de información personal
                    // 'BirthdateVerification'                 => '',// Verificador de cumpleaños
                    // 'RefTribalAffiliationId'                => '' // entidad tribal nativa americana
                ],

                'PersonIdentifier' => [
                    'PersonIdentifierId'                    => $PersonIdentifierId,
                    'PersonId'                              => $PersonId,
                    'Identifier'                            => $Identifier,
                    'RefPersonIdentificationSystemId'       => [
                                'Code' => 'RUN',
                                'Description' => 'Rol Unico Nacional'
                    ]
                    // 'RefPersonalInformationVerificationId'  => ''

                ],

                'PersonBirthplace' => [
                    'PersonId'                              => $PersonId
                    // 'City'                                  => '',
                    // 'RefStateId'                            => '',
                    // 'RefCountryId'                          => ''
                ],

                'PersonAddress' => [
                    'PersonAddressId'                       => $PersonAddressId,
                    'PersonId'                              => $PersonId,
                    // 'RefPersonLocationTypeId'               => '',
                    'StreetNumberAndName'                   => $StreetNumberAndName,
                    // 'ApartmentRoomOrSuiteNumber'            => '',
                    'City'                                  => $City,

                    'RefStateId'                            => [ 'Code'      => $RefStateId, 
                                                                 'StateName' => $StateName                              
                                                                ],

                    // 'PostalCode'                            => '',
                    'AddressCountyName'                     => $AddressCountyName,
                    'RefCountyId'                           => [ 'Code' => $RefCountyId ],
                    'RefCountryId'                          => 'CL'
                    // 'Latitude'                              => '',
                    // 'Longitude'                             => '',
                    // 'RefPersonalInformationVerificationId'  => ''                    
                ],

                'PersonEmailAddress' => [
                    'PersonEmailAddressId'                  => $PersonEmailAddressId,
                    'PersonId'                              => $PersonId,
                    'EmailAddress'                          => $EmailAddress,
                    'RefEmailTypeId'                        => 'Home'                
                ],

                'PersonTelephone' => [
                    'PersonTelephoneId'                     => $PersonTelephoneId,
                    'PersonId'                              => $PersonId,
                    'TelephoneNumber'                       => $TelephoneNumber
                    // 'PrimaryTelephoneNumberIndicator'       => '',
                    // 'RefPersonTelephoneNumberTypeId'        => ''                
                ],

                'PersonStatus' => [
                    'PersonStatusId'                        => $PersonStatusId,
                    'PersonId'                              => $PersonId
                    // 'RefPersonStatusTypeId'                 => '',
                    // 'StatusValue'                           => $docente->estadoCuenta
                    // 'StatusStartDate'                       => '',
                    // 'StatusEndDate'                         => ''                
                ],

                'PersonLanguage' => [
                    'PersonLanguageId'                      => $PersonLanguageId,
                    'PersonId'                              => $PersonId
                    // 'RefLanguageId'                         => '',
                    // 'RefLanguageUseTypeId'                  => ''           
                ],

                'PersonDisability' => [
                    'PersonId'                                => $PersonId
                    // 'PrimaryDisabilityTypeId'                 => '',
                    // 'DisabilityStatus'                        => '',
                    // 'RefAccommodationsNeededTypeId'           => '',
                    // 'RefDisabilityConditionTypeId'            => '',
                    // 'RefDisabilityDeterminationSourceTypeId'  => '',
                    // 'RefDisabilityConditionStatusCodeId'      => '',
                    // 'RefIDEADisabilityTypeId'                 => '',
                    // 'SignificantCognitiveDisabilityIndicator' => ''
                ],

               'PersonRelationship' => [
                    'PersonRelationshipId'                    => $PersonRelationshipId,
                    'PersonId'                                => $PersonId,
                    'RelatedPersonId'                         => $RelatedPersonId['PersonId'],
                    'RefPersonRelationshipId'                 => [  'Code' => $RefPersonRelationshipId['Code'],
                                                                    'Description' => $RefPersonRelationshipId['Description'],
                                                                    'Definition' => $RefPersonRelationshipId['Definition'] 
                                                                    ],
                    // 'CustodialRelationshipIndicator'          => '',
                    'EmergencyContactInd'                     => 'No',
                    // 'ContactPriorityNumber'                   => '',
                    // 'ContactRestrictions'                     => '',
                    // 'LivesWithIndicator'                      => '',
                    // 'PrimaryContactIndicator'                 => ''
                ],

                'PersonDegreeOrCertificate' => [
                    'PersonDegreeOrCertificateId'                        => $PersonDegreeOrCertificateId,
                    'PersonId'                                           => $PersonId
                    // 'DegreeOrCertificateTitleOrSubject'                  => '',
                    // 'RefDegreeOrCertificateTypeId'                       => '',
                    // 'AwardDate'                                          => '',
                    // 'NameOfInstitution'                                  => '',
                    // 'RefHigherEducationInstitutionAccreditationStatusId' => '',
                    // 'RefEducationVerificationMethodId'                   => ''
                ],            
            ];

            $apoderadosRel['P'.$apoderado->idApoderado] = ['PersonId' => $PersonId];

        }


        // APODERADOS SECUNDARIOS
        $apoderadosSec = $apodera->listarApoderadosSecundarioJSON($idperiodo, $idestablecimiento);        

        foreach ($apoderadosSec as $key => $apoderados) {

            $PersonId++;
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
            if (strpos($apoderados->nombreApoderado, ' ') !== false) {                
                        
                $nombres = explode(" ", $apoderados->nombreApoderado);
                $nombre = $nombres[0];                
                if (count($nombres) == 2) {
                    $segundoNombre = $nombres[1];
                } else if (count($nombres) >= 3) {
                    $segundoNombre = $nombres[1].' '.$nombres[2];
                } else {
                    $segundoNombre = $nombres[1];
                }
            } else {
                $nombre = $apoderados->nombreApoderado;
                $segundoNombre = '';
            }

            // Person
            $FirstName      = substr($nombre, 0 , 45);
            $MiddleName     = substr($segundoNombre, 0 , 45);
            $LastName       = substr($apoderados->paternoApoderado, 0 , 45);
            $SecondLastName = substr($apoderados->maternoApoderado, 0 , 45);
                       
            // PersonIdentifier
            $Identifier = substr($apoderados->rutApoderado, 0 , 40);

            // PersonAddress            
            $comuna = $tablacomunas->getcomuna($apoderados->comunaApoderado);
   
            $StreetNumberAndName = substr($apoderados->direccionApoderado, 0 , 40);                   
            $City                = substr($comuna[0]->nombreComuna, 0 , 30);
                                
            $cero       = ($comuna[0]->idRegion < 10) ? '0': '';
            $RefStateId = 'R'.$cero.''.$comuna[0]->idRegion; //Region     
            $StateName  = $comuna[0]->nombreRegion;

            $AddressCountyName   = substr($comuna[0]->nombreComuna, 0 , 30);
            $RefCountyId         = $comuna[0]->idComuna;

            // PersonEmailAddress                        
            $EmailAddress = substr($apoderados->correoApoderado, 0 , 128);

            //PersonTelephone            
            $TelephoneNumber = substr($apoderados->telefonoApoderado, 0 , 24);                      

            // PersonRelationship
            $RelatedPersonId = $alumnosRel[$apoderados->idAlumnos];

            $sexoAlumno = $apoderados->sexo; // 0 No Ingresado - 1 Niña - 2 Niño
            
            switch ($sexoAlumno) {
                case '0':
                    $RefPersonRelationshipId = [
                                                'Code' => 'Unknown',
                                                'Description' => 'Unknown',
                                                'Definition' => "The person is the learner''s Unknown."
                                            ];
                    break;
                
                case '1':
                    $RefPersonRelationshipId = [
                                                'Code' => 'Daughter',
                                                'Description' => 'Daughter',
                                                'Definition' => "The person is the learner''s Daughter."
                                            ];
                    break;

                case '2':
                    $RefPersonRelationshipId = [
                                                'Code' => 'Son',
                                                'Description' => 'Son',
                                                'Definition' => "The person is the learner''s Son."
                                            ];
                    break;

                default:
                     $RefPersonRelationshipId = [
                                                'Code' => 'Unknown',
                                                'Description' => 'Unknown',
                                                'Definition' => "The person is the learner''s Unknown."
                                            ];
                    break;
            }         
     

            $_Personas[$PersonId] = [ 
           
                'Person' => [
                    'PersonId'                              => $PersonId,
                    'FirstName'                             => $FirstName,
                    'MiddleName'                            => $MiddleName,
                    'LastName'                              => $LastName,
                    'SecondLastName'                        => $SecondLastName,
                    // 'GenerationCode'                        => '',
                    // 'Prefix'                                => '',
                    // 'Birthdate'                             => '', //Falta formatear
                    // 'RefSexId'                              => '',
                    // 'HispanicLatinoEthnicity'               => '',// CONSULTAR ETNIA ARRIBA
                    // 'RefUSCitizenshipStatusId'              => '',// Referencia si es ciudadano de USA, al ser "Permanent resident"
                    // // es ciudadano permanente de usa?. 
                                        
                    // 'RefVisaTypeId'                         => '',// va de la mano con el anterior
                    // 'RefStateOfResidenceId'                 => '',// estado donde vive
                    // 'RefProofOfResidencyTypeId'             => '',// prueba de residencia
                    // 'RefHighestEducationLevelCompletedId'   => '',
                    // 'RefPersonalInformationVerificationId'  => '',// Verificador de información personal
                    // 'BirthdateVerification'                 => '',// Verificador de cumpleaños
                    // 'RefTribalAffiliationId'                => '' // entidad tribal nativa americana
                ],

                'PersonIdentifier' => [
                    'PersonIdentifierId'                    => $PersonIdentifierId,
                    'PersonId'                              => $PersonId,
                    'Identifier'                            => $Identifier,
                    'RefPersonIdentificationSystemId'       => [
                                'Code' => 'RUN',
                                'Description' => 'Rol Unico Nacional'
                    ]
                    // 'RefPersonalInformationVerificationId'  => ''

                ],

                'PersonBirthplace' => [
                    'PersonId'                              => $PersonId
                    // 'City'                                  => '',
                    // 'RefStateId'                            => '',
                    // 'RefCountryId'                          => ''
                ],

                'PersonAddress' => [
                    'PersonAddressId'                       => $PersonAddressId,
                    'PersonId'                              => $PersonId,
                    // 'RefPersonLocationTypeId'               => '',
                    'StreetNumberAndName'                   => $StreetNumberAndName,
                    // 'ApartmentRoomOrSuiteNumber'            => '',
                    'City'                                  => $City,

                    'RefStateId'                            => [ 'Code'      => $RefStateId, 
                                                                 'StateName' => $StateName                              
                                                                ],

                    // 'PostalCode'                            => '',
                    'AddressCountyName'                     => $AddressCountyName,
                    'RefCountyId'                           => [ 'Code' => $RefCountyId ],
                    'RefCountryId'                          => 'CL'
                    // 'Latitude'                              => '',
                    // 'Longitude'                             => '',
                    // 'RefPersonalInformationVerificationId'  => ''                    
                ],

                'PersonEmailAddress' => [
                    'PersonEmailAddressId'                  => $PersonEmailAddressId,
                    'PersonId'                              => $PersonId,
                    'EmailAddress'                          => $EmailAddress,
                    'RefEmailTypeId'                        => 'Home'                
                ],

                'PersonTelephone' => [
                    'PersonTelephoneId'                     => $PersonTelephoneId,
                    'PersonId'                              => $PersonId,
                    'TelephoneNumber'                       => $TelephoneNumber
                    // 'PrimaryTelephoneNumberIndicator'       => '',
                    // 'RefPersonTelephoneNumberTypeId'        => ''                
                ],

                'PersonStatus' => [
                    'PersonStatusId'                        => $PersonStatusId,
                    'PersonId'                              => $PersonId
                    // 'RefPersonStatusTypeId'                 => '',
                    // 'StatusValue'                           => $docente->estadoCuenta
                    // 'StatusStartDate'                       => '',
                    // 'StatusEndDate'                         => ''                
                ],

                'PersonLanguage' => [
                    'PersonLanguageId'                      => $PersonLanguageId,
                    'PersonId'                              => $PersonId
                    // 'RefLanguageId'                         => '',
                    // 'RefLanguageUseTypeId'                  => ''           
                ],

                'PersonDisability' => [
                    'PersonId'                                => $PersonId
                    // 'PrimaryDisabilityTypeId'                 => '',
                    // 'DisabilityStatus'                        => '',
                    // 'RefAccommodationsNeededTypeId'           => '',
                    // 'RefDisabilityConditionTypeId'            => '',
                    // 'RefDisabilityDeterminationSourceTypeId'  => '',
                    // 'RefDisabilityConditionStatusCodeId'      => '',
                    // 'RefIDEADisabilityTypeId'                 => '',
                    // 'SignificantCognitiveDisabilityIndicator' => ''
                ],

               'PersonRelationship' => [
                    'PersonRelationshipId'                    => $PersonRelationshipId,
                    'PersonId'                                => $PersonId,
                    'RelatedPersonId'                         => $RelatedPersonId['PersonId'],
                    'RefPersonRelationshipId'                 => [  'Code' => $RefPersonRelationshipId['Code'],
                                                                    'Description' => $RefPersonRelationshipId['Description'],
                                                                    'Definition' => $RefPersonRelationshipId['Definition'] 
                                                                    ],
                    // 'CustodialRelationshipIndicator'          => '',
                    'EmergencyContactInd'                     => 'No',
                    // 'ContactPriorityNumber'                   => '',
                    // 'ContactRestrictions'                     => '',
                    // 'LivesWithIndicator'                      => '',
                    // 'PrimaryContactIndicator'                 => ''
                ],

                'PersonDegreeOrCertificate' => [
                    'PersonDegreeOrCertificateId'                        => $PersonDegreeOrCertificateId,
                    'PersonId'                                           => $PersonId
                    // 'DegreeOrCertificateTitleOrSubject'                  => '',
                    // 'RefDegreeOrCertificateTypeId'                       => '',
                    // 'AwardDate'                                          => '',
                    // 'NameOfInstitution'                                  => '',
                    // 'RefHigherEducationInstitutionAccreditationStatusId' => '',
                    // 'RefEducationVerificationMethodId'                   => ''
                ],            
            ];

            $apoderadosRel['S'.$apoderados->alumnoIdApoderado] = ['PersonId' => $PersonId];
        }
        
        /* ------------------------------- RELACIONES ALUMNO ------------------------------- */
        
        // Apoderado Principal
        $alumnosApoderadoPrincipalRel = $tablaalumnos->listarAlumnosApoderadoPrincipalJSON($idperiodo, $idestablecimiento);
        
        foreach ($alumnosApoderadoPrincipalRel as $key => $alumnoApopRel) {
            

            if ($alumnoApopRel->idApoderado != 0) {

                $PersonRelationshipId++;
                
                $AlumnoPersonId    = $alumnosRel[$alumnoApopRel->idAlumnos]['PersonId'];
                $ApoderadoPersonId = $apoderadosRel['P'.$alumnoApopRel->idApoderado]['PersonId'];

                $nuevosValores = array( 'PersonRelationshipId'                    => $PersonRelationshipId,
                                        'PersonId'                                => $AlumnoPersonId,
                                        'RelatedPersonId'                         => $ApoderadoPersonId,
                                        'RefPersonRelationshipId'                 => [  'Code' => 'Mother',
                                                                                        'Description' => 'Mother',
                                                                                        'Definition' => "The person is the learner''s Mother."
                                                                                    ]
                                        // 'CustodialRelationshipIndicator'          => '',
                                        // 'EmergencyContactInd'                     => '',
                                        // 'ContactPriorityNumber'                   => '',
                                        // 'ContactRestrictions'                     => '',
                                        // 'LivesWithIndicator'                      => '',
                                        // 'PrimaryContactIndicator'                 => ''
                                    );

                if (isset($_Personas[$AlumnoPersonId]['PersonRelationship']['RelatedPersonId']) || isset($_Personas[$AlumnoPersonId]['PersonRelationship'][0])) {
                                        
                    array_push($_Personas[$AlumnoPersonId]['PersonRelationship'], $nuevosValores);
                    
                } else {
                    //Elimina cuando no hay datos
                    unset($_Personas[$AlumnoPersonId]['PersonRelationship']['PersonRelationshipId']);
                    unset($_Personas[$AlumnoPersonId]['PersonRelationship']['PersonId']);
                    array_push($_Personas[$AlumnoPersonId]['PersonRelationship'], $nuevosValores);
                    
                }
     
            }

        }


        // Apoderado Secundario

        $alumnosApoderadoSecundarioRel = $tablaalumnos->listarAlumnosApoderadoSecundarioJSON($idperiodo, $idestablecimiento);

        foreach ($alumnosApoderadoSecundarioRel as $key => $alumnoAposRel) {
                        
            if ($alumnoAposRel->idApoderados != 0) {
            
                $PersonRelationshipId++;
            
                $AlumnoPersonId    = $alumnosRel[$alumnoAposRel->idAlumnos]['PersonId'];

                $ApoderadoPersonId = $apoderadosRel['S'.$alumnoAposRel->idApoderados]['PersonId'];

                $nuevosValores = array( 'PersonRelationshipId'                    => $PersonRelationshipId,
                                        'PersonId'                                => $AlumnoPersonId,
                                        'RelatedPersonId'                         => $ApoderadoPersonId,
                                        'RefPersonRelationshipId'                 => [  'Code' => 'Mother',
                                                                                        'Description' => 'Mother',
                                                                                        'Definition' => "The person is the learner''s Mother."
                                                                                    ]
                                        // 'CustodialRelationshipIndicator'          => '',
                                        // 'EmergencyContactInd'                     => '',
                                        // 'ContactPriorityNumber'                   => '',
                                        // 'ContactRestrictions'                     => '',
                                        // 'LivesWithIndicator'                      => '',
                                        // 'PrimaryContactIndicator'                 => ''
                                    );

                if (isset($_Personas[$AlumnoPersonId]['PersonRelationship']['RelatedPersonId']) || isset($_Personas[$AlumnoPersonId]['PersonRelationship'][0])) {
                                        
                    array_push($_Personas[$AlumnoPersonId]['PersonRelationship'], $nuevosValores);
                    
                } else {
                    //Elimina cuando no hay datos
                    unset($_Personas[$AlumnoPersonId]['PersonRelationship']['PersonRelationshipId']);
                    unset($_Personas[$AlumnoPersonId]['PersonRelationship']['PersonId']);
                    array_push($_Personas[$AlumnoPersonId]['PersonRelationship'], $nuevosValores);
                    
                }
     
            }
        }

        /* ------------------------------- FIN RELACIONES ALUMNO ------------------------------- */

        /* ----------------------------------- Fin _Personas ----------------------------------- */



        /* ----------------------------------- _Organizaciones ----------------------------------- */

        // ESTABLECIMIENTOS
        $establ  = new Application_Model_DbTable_Establecimiento();
        $establecimientos = $establ->listarEstablecimientosJSON(null, $idestablecimiento);

        $OrganizationId = 0;        
        $OrganizationLocationId = 0;
        $OrganizationTelephoneId = 0;
        $OrganizationEmailId = 0;
        $RefOrganizationRelationshipId = 0;
        $OrganizationIdentifierId = 0;
        $OrganizationOperationalStatusId = 0;
        $OrganizationIndicatorId = 0;
        $OrganizationRelationshipId = 0;

        foreach ($establecimientos as $key => $establecimiento) {

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
            $Name      = substr($establecimiento->nombreEstablecimiento, 0 , 128);
            $ShortName = substr($establecimiento->rbd, 0 , 30);

            // OrganizationTelephone
            $TelephoneNumber = substr($establecimiento->telefono, 0 , 24);

            // OrganizationEmail             
            $ElectronicMailAddress = substr($establecimiento->correo, 0 , 128);
                  
            // OrganizationIdentifier            
            $Identifier = substr($establecimiento->rbd, 0 , 40);


            $_Organizaciones = [ 
                
                'Organization' => [
                    'OrganizationId'        => $OrganizationId,
                    'Name'                  => $Name,
                    'RefOrganizationTypeId' => [    'Code' => 'EducationInstitution',
                                                    'Description' => 'Education Institution'
                                                ],
                    'ShortName'             => $ShortName
                    // 'RegionGeoJSON'         => '',
                ],
                
                'OrganizationLocation' => [
                    'OrganizationLocationId'        => $OrganizationLocationId,
                    'OrganizationId'                => $OrganizationId
                    // 'LocationId'                    => '',
                    // 'RefOrganizationLocationTypeId' => ''

                ],

                'OrganizationTelephone' => [
                    'OrganizationTelephoneId'         => $OrganizationTelephoneId,
                    'OrganizationId'                  => $OrganizationId,
                    'TelephoneNumber'                 => $TelephoneNumber,
                    'PrimaryTelephoneNumberIndicator' => 'Yes',
                    'RefInstitutionTelephoneTypeId'   => 'Main'
                ],

                'OrganizationEmail' => [
                    'OrganizationEmailId'             => $OrganizationEmailId,
                    'OrganizationId'                  => $OrganizationId,
                    'ElectronicMailAddress'           => $ElectronicMailAddress
                    // 'RefEmailTypeId'                  => ''                        
                ],

                'RefOrganizationRelationship' => [
                    'RefOrganizationRelationshipId' => $RefOrganizationRelationshipId,
                    // 'Description'                   => '',
                    // 'Code'                          => '',
                    // 'Definition'                    => '',
                    // 'RefJurisdictionId'             => '',
                    // 'SortOrder'                     => ''
                ],

                'OrganizationIdentifier' => [
                    'OrganizationIdentifierId'              => $OrganizationIdentifierId,
                    'Identifier'                            => $Identifier,
                    'RefOrganizationIdentificationSystemId' => 'Other',
                    'OrganizationId'                        => $OrganizationId
                    // 'RefOrganizationIdentifierTypeId'       => ''                
                ],

                'OrganizationWebsite' => [
                    'OrganizationId'                        => $OrganizationId
                    // 'Website'                               => '',                        
                ],

                'OrganizationOperationalStatus' => [
                    'OrganizationOperationalStatusId'       => $OrganizationOperationalStatusId,
                    'OrganizationId'                        => $OrganizationId,
                    'RefOperationalStatusId'                => 'Active'
                    // 'OperationalStatusEffectiveDate'        => ''           
                ],

                'OrganizationIndicator' => [
                    'OrganizationIndicatorId'               => $OrganizationIndicatorId,
                    'OrganizationId'                        => $OrganizationId
                    // 'IndicatorValue'                        => '',
                    // 'RefOrganizationIndicatorId'            => ''
                ],

                'OrganizationRelationship' => [
                    'OrganizationRelationshipId'             => $OrganizationRelationshipId,
                    // 'Parent_OrganizationId'                  => '',
                    'OrganizationId'                         => $OrganizationId
                    // 'RefOrganizationRelationshipId'          => ''
                ]     
            ];

            $establecimientosOrganizationRel[$establecimiento->idEstablecimiento] = ['OrganizationId' => $OrganizationId];
        }

        

        // Sostenedores 
        $sost  = new Application_Model_DbTable_Sostenedor();
        $sostenedores = $sost->listarSostenedoresJSON(null, $idestablecimiento);
        
        foreach ($sostenedores as $key => $sostenedor) {

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
            $Name      = substr($sostenedor->nombreSostenedor, 0 , 128);
            // $ShortName = substr($sostenedor->rbd, 0 , 30);

            // OrganizationTelephone
            $TelephoneNumber = substr($sostenedor->telefono, 0 , 24);

            // OrganizationEmail             
            $ElectronicMailAddress = substr($sostenedor->correo, 0 , 128);
                  
            // OrganizationIdentifier            
            $Identifier = substr($sostenedor->rutSostenedor, 0 , 40);


            $_Organizaciones = [ 
           
                'Organization' => [
                    'OrganizationId'        => $OrganizationId,
                    'Name'                  => $Name
                    // 'RefOrganizationTypeId' => [    'Code' => 'EducationInstitution',
                    //                                 'Description' => 'Education Institution',
                    //                                 'Definition' => 'A public or private institution, organization, or agency that provides instructional or support services to students or staff at any level.'
                    //                             ],
                    // 'ShortName'             => '',
                    // 'RegionGeoJSON'         => '',
                ],
                
                'OrganizationLocation' => [
                    'OrganizationLocationId'        => $OrganizationLocationId,
                    'OrganizationId'                => $OrganizationId
                    // 'LocationId'                    => '',
                    // 'RefOrganizationLocationTypeId' => ''

                ],

                'OrganizationTelephone' => [
                    'OrganizationTelephoneId'         => $OrganizationTelephoneId,
                    'OrganizationId'                  => $OrganizationId,
                    'TelephoneNumber'                 => $TelephoneNumber,
                    'PrimaryTelephoneNumberIndicator' => 'Yes',
                    'RefInstitutionTelephoneTypeId'   => 'Main'
                ],

                'OrganizationEmail' => [
                    'OrganizationEmailId'             => $OrganizationEmailId,
                    'OrganizationId'                  => $OrganizationId,
                    'ElectronicMailAddress'           => $ElectronicMailAddress
                    // 'RefEmailTypeId'                  => ''                        
                ],

                'RefOrganizationRelationship' => [
                    'RefOrganizationRelationshipId' => $RefOrganizationRelationshipId,
                    // 'Description'                   => '',
                    // 'Code'                          => '',
                    // 'Definition'                    => '',
                    // 'RefJurisdictionId'             => '',
                    // 'SortOrder'                     => ''
                ],

                'OrganizationIdentifier' => [
                    'OrganizationIdentifierId'              => $OrganizationIdentifierId,
                    'Identifier'                            => $Identifier,
                    'RefOrganizationIdentificationSystemId' => 'Other',
                    'OrganizationId'                        => $OrganizationId
                    // 'RefOrganizationIdentifierTypeId'       => ''                
                ],

                'OrganizationWebsite' => [
                    'OrganizationId'                        => $OrganizationId
                    // 'Website'                               => '',                        
                ],

                'OrganizationOperationalStatus' => [
                    'OrganizationOperationalStatusId'       => $OrganizationOperationalStatusId,
                    'OrganizationId'                        => $OrganizationId
                    // 'RefOperationalStatusId'                => '',
                    // 'OperationalStatusEffectiveDate'        => ''           
                ],

                'OrganizationIndicator' => [
                    'OrganizationIndicatorId'               => $OrganizationIndicatorId,
                    'OrganizationId'                        => $OrganizationId
                    // 'IndicatorValue'                        => '',
                    // 'RefOrganizationIndicatorId'            => ''
                ],

                'OrganizationRelationship' => [
                    'OrganizationRelationshipId'             => $OrganizationRelationshipId,
                    // 'Parent_OrganizationId'                  => '',
                    'OrganizationId'                         => $OrganizationId
                    // 'RefOrganizationRelationshipId'          => ''
                ]  
            ];

        }

        
        // CURSOS
        $curs   = new Application_Model_DbTable_Cursos();
        // Zend_Debug::dump($idperiodo);
        $cursos = $curs->listarCursosJSON($idperiodo, $idestablecimiento);

        foreach ($cursos as $key => $curso) {

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
            $Name      = substr($curso->nombreGrado, 0 , 128);
            $ShortName = substr($curso->letra, 0 , 30);

                  
            // OrganizationIdentifier            
            $Identifier = substr($curso->idCursos, 0 , 40);


            $_Organizaciones = [ 
           
                'Organization' => [
                    'OrganizationId'        => $OrganizationId,
                    'Name'                  => $Name,
                    'RefOrganizationTypeId' => [    'Code' => 'Course',
                                                    'Description' => 'Course'
                                                ],
                    'ShortName'             => $ShortName
                    // 'RegionGeoJSON'         => '',
                ],
                
                'OrganizationLocation' => [
                    'OrganizationLocationId'        => $OrganizationLocationId,
                    'OrganizationId'                => $OrganizationId
                    // 'LocationId'                    => '',
                    // 'RefOrganizationLocationTypeId' => ''

                ],

                'OrganizationTelephone' => [
                    'OrganizationTelephoneId'         => $OrganizationTelephoneId,
                    'OrganizationId'                  => $OrganizationId
                    // 'TelephoneNumber'                 => $TelephoneNumber,
                    // 'PrimaryTelephoneNumberIndicator' => 'Yes',
                    // 'RefInstitutionTelephoneTypeId'   => 'Main'
                ],

                'OrganizationEmail' => [
                    'OrganizationEmailId'             => $OrganizationEmailId,
                    'OrganizationId'                  => $OrganizationId
                    // 'ElectronicMailAddress'           => $sostenedor->correo
                    // 'RefEmailTypeId'                  => ''                        
                ],

                'RefOrganizationRelationship' => [
                    'RefOrganizationRelationshipId' => $RefOrganizationRelationshipId,
                    // 'Description'                   => '',
                    // 'Code'                          => '',
                    // 'Definition'                    => '',
                    // 'RefJurisdictionId'             => '',
                    // 'SortOrder'                     => ''
                ],

                'OrganizationIdentifier' => [
                    'OrganizationIdentifierId'              => $OrganizationIdentifierId,
                    'Identifier'                            => $Identifier,
                    'RefOrganizationIdentificationSystemId' => 'Other',
                    'OrganizationId'                        => $OrganizationId
                    // 'RefOrganizationIdentifierTypeId'       => ''                
                ],

                'OrganizationWebsite' => [
                    'OrganizationId'                        => $OrganizationId
                    // 'Website'                               => '',                        
                ],

                'OrganizationOperationalStatus' => [
                    'OrganizationOperationalStatusId'       => $OrganizationOperationalStatusId,
                    'OrganizationId'                        => $OrganizationId
                    // 'RefOperationalStatusId'                => '',
                    // 'OperationalStatusEffectiveDate'        => ''           
                ],

                'OrganizationIndicator' => [
                    'OrganizationIndicatorId'               => $OrganizationIndicatorId,
                    'OrganizationId'                        => $OrganizationId
                    // 'IndicatorValue'                        => '',
                    // 'RefOrganizationIndicatorId'            => ''
                ],

                'OrganizationRelationship' => [
                    'OrganizationRelationshipId'             => $OrganizationRelationshipId,
                    // 'Parent_OrganizationId'                  => '',
                    'OrganizationId'                         => $OrganizationId
                    // 'RefOrganizationRelationshipId'          => ''
                ] 
            ];


            $cursosOrganizationRel[$curso->idCursos] = ['OrganizationId' => $OrganizationId];
        }


        // ASIGNATURAS
        $asig   = new Application_Model_DbTable_Asignaturas();
        $asignaturas = $asig->listarAsignaturasJSON(null, null);

        foreach ($asignaturas as $key => $asignatura) {

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
            $Name      = mb_convert_encoding(substr($asignatura->nombreAsignatura, 0 , 128), 'UTF-8', 'UTF-8'); 
            // $ShortName = substr($asignatura->letra, 0 , 30);

                  
            // OrganizationIdentifier            
            $Identifier = substr($asignatura->idAsignatura, 0 , 40);


            $_Organizaciones = [ 
           
                'Organization' => [
                    'OrganizationId'        => $OrganizationId,
                    'Name'                  => $Name,
                    'RefOrganizationTypeId' => [    'Code' => 'CourseSection',
                                                    'Description' => 'Course Section'
                                                ]
                    // 'ShortName'             => '',
                    // 'RegionGeoJSON'         => '',
                ],
                
                'OrganizationLocation' => [
                    'OrganizationLocationId'        => $OrganizationLocationId,
                    'OrganizationId'                => $OrganizationId
                    // 'LocationId'                    => '',
                    // 'RefOrganizationLocationTypeId' => ''

                ],

                'OrganizationTelephone' => [
                    'OrganizationTelephoneId'         => $OrganizationTelephoneId,
                    'OrganizationId'                  => $OrganizationId
                    // 'TelephoneNumber'                 => $TelephoneNumber,
                    // 'PrimaryTelephoneNumberIndicator' => 'Yes',
                    // 'RefInstitutionTelephoneTypeId'   => 'Main'
                ],

                'OrganizationEmail' => [
                    'OrganizationEmailId'             => $OrganizationEmailId,
                    'OrganizationId'                  => $OrganizationId
                    // 'ElectronicMailAddress'           => $sostenedor->correo
                    // 'RefEmailTypeId'                  => ''                        
                ],

                'RefOrganizationRelationship' => [
                    'RefOrganizationRelationshipId' => $RefOrganizationRelationshipId
                    // 'Description'                   => '',
                    // 'Code'                          => '',
                    // 'Definition'                    => '',
                    // 'RefJurisdictionId'             => '',
                    // 'SortOrder'                     => ''
                ],

                'OrganizationIdentifier' => [
                    'OrganizationIdentifierId'              => $OrganizationIdentifierId,
                    'Identifier'                            => $Identifier,
                    'RefOrganizationIdentificationSystemId' => 'Other',
                    'OrganizationId'                        => $OrganizationId
                    // 'RefOrganizationIdentifierTypeId'       => ''                
                ],

                'OrganizationWebsite' => [
                    'OrganizationId'                        => $OrganizationId
                    // 'Website'                               => '',                        
                ],

                'OrganizationOperationalStatus' => [
                    'OrganizationOperationalStatusId'       => $OrganizationOperationalStatusId,
                    'OrganizationId'                        => $OrganizationId
                    // 'RefOperationalStatusId'                => '',
                    // 'OperationalStatusEffectiveDate'        => ''           
                ],

                'OrganizationIndicator' => [
                    'OrganizationIndicatorId'               => $OrganizationIndicatorId,
                    'OrganizationId'                        => $OrganizationId
                    // 'IndicatorValue'                        => '',
                    // 'RefOrganizationIndicatorId'            => ''
                ],

                'OrganizationRelationship' => [
                    'OrganizationRelationshipId'             => $OrganizationRelationshipId,
                    // 'Parent_OrganizationId'                  => '',
                    'OrganizationId'                         => $OrganizationId
                    // 'RefOrganizationRelationshipId'          => ''
                ]  
            ];
            $asignaturasOrganizationRel[$Identifier] = ['OrganizationId' => $OrganizationId];
        }
        
        /* ----------------------------------- Fin _Organizaciones ----------------------------------- */

      
        /* ----------------------------------- _Incidentes ----------------------------------- */


        // Observaciones
        $obser  = new Application_Model_DbTable_Observacion();
        $observaciones = $obser->listarObservacionesJSON($idperiodo, $idestablecimiento);
             
        $IncidentId = 0;
        $K12StudentDisciplineId = 0;    

        foreach ($observaciones as $key => $observacion) {
            
            $IncidentId++;
            $K12StudentDisciplineId++;            
            
            // Incident
            
            $IncidentIdentifier  = substr($observacion->idObservaciones, 0 , 40);
            
            $IncidentDate = $observacion->fechaObservacion; //FORMATEAR
            
            $IncidentDescription = $observacion->observacion;

            $_Incidentes[$IncidentId] = [ 
                
                'Incident' => [
                    'IncidentId'                          => $IncidentId,
                    'IncidentIdentifier'                  => $IncidentIdentifier,
                    'IncidentDate'                        => $IncidentDate,
                    // 'IncidentTime'                        => '',
                    // 'RefIncidentTimeDescriptionCodeId'    => '',
                    'IncidentDescription'                 => $IncidentDescription
                    // 'RefIncidentBehaviorId'               => '',
                    // 'RefIncidentInjuryTypeId'             => '',
                    // 'RefWeaponTypeId'                     => '',
                    // 'IncidentCost'                        => '',
                    // 'OrganizationPersonRoleId'            => '', //PERSONE ROLE ID?
                    // 'IncidentReporterId'                  => '',
                    // 'RefIncidentReporterTypeId'           => '',
                    // 'RefIncidentLocationId'               => '',
                    // 'RefFirearmTypeId'                    => '',
                    // 'RegulationViolatedDescription'       => '',
                    // 'RelatedToDisabilityManifestationInd' => '',
                    // 'ReportedToLawEnforcementInd'         => '',
                    // 'RefIncidentMultipleOffenseTypeId'    => '',
                    // 'RefIncidentPerpetratorInjuryTypeId'  => ''
                ],
                
                'K12StudentDiscipline' => [
                    'K12StudentDisciplineId' => $K12StudentDisciplineId
                    // 'OrganizationPersonRoleId' => '', //relacion?
                    // 'RefDisciplineReasonId' => '',
                    // 'RefDisciplinaryActionTakenId' => '',
                    // 'DisciplinaryActionStartDate' => '',
                    // 'DisciplinaryActionEndDate' => '',
                    // 'DurationOfDisciplinaryAction' => '',
                    // 'RefDisciplineLengthDifferenceReasonId' => '',
                    // 'FullYearExpulsion' => '',
                    // 'ShortenedExpulsion' => '',
                    // 'EducationalServicesAfterRemoval' => '',
                    // 'RefIdeaInterimRemovalId' => '',
                    // 'RefIdeaInterimRemovalReasonId' => '',
                    // 'RelatedToZeroTolerancePolicy' => '',
                    // 'IncidentId' => '',
                    // 'IEPPlacementMeetingIndicator' => '',
                    // 'RefDisciplineMethodFirearmsId' => '',
                    // 'RefDisciplineMethodOfCwdId' => '',
                    // 'RefIDEADisciplineMethodFirearmId' => ''
                ],

                'IncidentPerson' => [
                    'IncidentId' => $IncidentId,
                    // 'PersonId' => '',
                    'Identifier' => $observacion->rutAlumno
                    // 'RefIncidentPersonRoleTypeId' => '',
                    // 'RefIncidentPersonTypeId' => ''
                ]            
            ];

        }
        

        /* ----------------------------------- Fin _Incidentes ----------------------------------- */

        /* ----------------------------------- _Establecimientos ----------------------------------- */


        // ESTABLECIMIENTOS
        $est  = new Application_Model_DbTable_Establecimiento();
        $establecimientos2 = $est->listarRelEstablecimientosJSON(null, $idestablecimiento);
             
        $K12SchoolCorrectiveActionId = 0;
        $K12SchoolGradeOfferedId = 0;    
        $K12SchoolImprovementId = 0;

        foreach ($establecimientos2 as $key => $establecimiento2) {
            
            $K12SchoolCorrectiveActionId++;
            $K12SchoolGradeOfferedId++;  
            $K12SchoolImprovementId++;      
            
            $OrganizationId = $establecimientosOrganizationRel[$establecimiento2->idEstablecimiento];       
            
            switch ($establecimiento2->dependencia) {
                case 'Municipal':
                    $RefAdministrativeFundingControlId = [  'Code' => 'Public',
                                                            'Description' => 'Public School'
                                                        ];
                    break;
                
                default:
                    $RefAdministrativeFundingControlId = [  'Code' => 'Public',
                                                            'Description' => 'Public School'
                                                        ];
                    break;
            }
            
            
            $CharterSchoolContractIdNumber = $establecimiento2->numeroDecreto;

            $_Establecimientos = [ 
                
                'K12SchoolCorrectiveAction' => [
                    'K12SchoolCorrectiveActionId'   => $K12SchoolCorrectiveActionId,
                    'OrganizationId'                => $OrganizationId
                    // 'RefCorrectiveActionTypeId'     => ''  //PREGUNTAR
                ],
                
                'K12School' => [
                    'OrganizationId'                               => $OrganizationId,
                    'RefSchoolTypeId'                              => [ 'Code' => 'Regular',
                                                                        'Description' => 'Regular School'
                                                                        ],
                    'RefSchoolLevelId'                             => [ 'Code' => '02397',
                                                                        'Description' => 'Primary'
                                                                        ],

                    'RefAdministrativeFundingControlId'            => $RefAdministrativeFundingControlId,
                    // 'CharterSchoolIndicator'                       => '', //PREGUNTAR
                    // 'RefCharterSchoolTypeId'                       => '', //PREGUNTAR
                    // 'RefIncreasedLearningTimeTypeId'               => '', //PREGUNTAR
                    // 'RefStatePovertyDesignationId'                 => '', //PREGUNTAR
                    // 'CharterSchoolApprovalYear'                    => '', El año decreto?  establecimientoconfiguracion
                    'RefCharterSchoolApprovalAgencyTypeId'         => [ 'Code' => 'State',
                                                                        'Description' => 'State board of education'
                                                                       ],
                    // 'AccreditationAgencyName'                      => '',
                    // 'CharterSchoolOpenEnrollmentIndicator'         => '',
                    // 'CharterSchoolContractApprovalDate'            => '',
                    'CharterSchoolContractIdNumber'                => $CharterSchoolContractIdNumber, //Preguntar
                    // 'CharterSchoolContractRenewalDate'             => '',
                    // 'RefCharterSchoolManagementOrganizationTypeId' => ''
                ],

                'K12SchoolGradeOffered' => [
                    'K12SchoolGradeOfferedId' => $K12SchoolGradeOfferedId,
                    'OrganizationId'          => $OrganizationId
                    // 'RefGradeLevelId'         => ''
                ],

                'K12SchoolStatus' => [
                    'OrganizationId'                        => $OrganizationId
                    // 'RefMagnetSpecialProgramId'             => '',
                    // 'RefAlternativeSchoolFocusId'           => '',
                    // 'RefInternetAccessId'                   => '',
                    // 'RefRestructuringActionId'              => '',
                    // 'RefTitleISchoolStatusId'               => '',
                    // 'ConsolidatedMepFundsStatus'            => '', 
                    // 'RefNationalSchoolLunchProgramStatusId' => '',
                    // 'RefVirtualSchoolStatusId'              => '',
                ],

                'K12SchoolImprovement' => [
                    'K12SchoolImprovementId'       => $K12SchoolImprovementId,
                    'OrganizationId'               => $OrganizationId
                    // 'RefSchoolImprovementStatusId' => '',
                    // 'RefSchoolImprovementFundsId'  => '',
                    // 'RefSigInterventionTypeId'     => '',
                    // 'SchoolImprovementExitDate'    => ''

                ]            
            ];

        }


        /* ----------------------------------- FIN _Establecimientos ----------------------------------- */



        /* -----------------------------------  _Cursos ----------------------------------- */
        
        $cursos2 = $curs->listarCursosJSON($idperiodo, $idestablecimiento);
             
        $LocationId = 0;
        $FacilityLocationId = 0;
        $CourseSectionLocationId = 0;
        $CourseSectionScheduleId = 0;

        foreach ($cursos2 as $key => $curso2) {
        
            $LocationId++;
            $FacilityLocationId++;
            $CourseSectionLocationId++;
            $CourseSectionScheduleId++;
              
        
            $OrganizationId = $cursosOrganizationRel[$curso2->idCursos]['OrganizationId'];

            // Classroom
            $ClassroomIdentifier = $curso2->idCursos;

            
            // Course
            // $Description = $curso2->numeroGrado;
            // $SubjectAbbreviation = $curso2->letra;
             

            // Buscamos las asignaturas del curso y seteamoos su OrganizationId
            $listaAsignaturas = $curs->listarAsignaturasCursosJSON($ClassroomIdentifier);

            foreach ($listaAsignaturas as $key => $asignatura) {                
                $OrganizationCourseSectionId = $asignaturasOrganizationRel[$asignatura->idAsignatura]['OrganizationId'];
            }


            $_Cursos = [ 
                
                'Location' => [

                    'LocationId' => $LocationId                 
                ],
                
                'LocationAddress' => [

                    'LocationId' => $LocationId,
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

                ],

                'FacilityLocation' => [

                    'FacilityLocationId' => $FacilityLocationId,
                    // 'FacilityId' => '',
                    'LocationId' => $LocationId

                ],

                'Classroom' => [

                    'LocationId' => $LocationId,
                    'ClassroomIdentifier' => $ClassroomIdentifier

                ],

                'K12Course' => [

                    'OrganizationId' => $OrganizationId,
                    // 'HighSchoolCourseRequirement' => '',
                    // 'RefAdditionalCreditTypeId' => '',
                    // 'AvailableCarnegieUnitCredit' => '',
                    // 'RefCourseGpaApplicabilityId' => '',// Este curso se incluye o no en el cálculo del promedio de calificaciones
                    // 'CoreAcademicCourse' => '',// El curso cumple con la definición estatal de un curso académico básico.
                    // 'RefCurriculumFrameworkTypeId' => '',
                    // 'CourseAlignedWithStandards' => '',
                    // 'RefCreditTypeEarnedId' => '',
                    // 'FundingProgram' => '',
                    // 'FamilyConsumerSciencesCourseInd' => '',
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

                ],

                'CourseSectionLocation' => [

                    'CourseSectionLocationId' => $CourseSectionLocationId,
                    'LocationId' => $LocationId,
                    'OrganizationId' => $OrganizationId,
                    // 'RefInstructionLocationTypeId' => ''

                ],

                'Course' => [

                    'OrganizationId' => $OrganizationId,
                    // 'Description' => $Description,
                    // 'SubjectAbbreviation' => $SubjectAbbreviation,
                    // 'SCEDSequenceOfCourse' => '',
                    // 'InstructionalMinutes' => '',
                    // 'RefCourseLevelCharacteristicsId' => '',
                    // 'RefCourseCreditUnitId' => '',
                    // 'CreditValue' => '',
                    // 'RefInstructionLanguage' => '',
                    // 'CertificationDescription' => '',
                    // 'RefCourseApplicableEducationLevelId' => '',
                    // 'RepeatabilityMaximumNumber' => ''

                ],

                'CourseSection' => [

                    'OrganizationId' => $OrganizationCourseSectionId,
                    // 'AvailableCarnegieUnitCredit' => '',
                    // 'RefCourseSectionDeliveryModeId' => '',
                    // 'RefSingleSexClassStatusId' => '',
                    // 'TimeRequiredForCompletion' => '',
                    // 'CourseId' => '',
                    // 'RefAdditionalCreditTypeId' => '',
                    // 'RefInstructionLanguageId' => '',
                    // 'VirtualIndicator' => '',
                    // 'OrganizationCalendarSessionId' => '',
                    // 'RefCreditTypeEarnedId' => '',
                    // 'RefAdvancedPlacementCourseCodeId' => '',
                    // 'MaximumCapacity' => '',
                    // 'RelatedCompetencyFrameworkItems' => ''

                ],

                'CourseSectionSchedule' => [

                    'CourseSectionScheduleId' => $CourseSectionScheduleId,
                    'OrganizationId' => $OrganizationCourseSectionId,
                    // 'ClassMeetingDays' => '',
                    // 'ClassBeginningTime' => '',
                    // 'ClassEndingTime' => '',
                    // 'ClassPeriod' => '',
                    // 'TimeDayIdentifier' => ''

                ]

            ];
        }


        /* ----------------------------------- FIN _Cursos ----------------------------------- */









        /* ----------------------------------- _ComunidadEducativa ----------------------------------- */

        // $est  = new Application_Model_DbTable_Establecimiento();
        // $establecimientos2 = $est->listarEstablecimientosJSON(null, $idestablecimiento);
        
        $OrganizationPersonRoleId = 0;
        $K12StudentAcademicHonorId = 0;
        $StaffEmploymentId = 0;
        $RefK12StaffClassificationId = 0;
        $RoleAttendanceId = 0;
        $RoleStatusId = 0;
        $K12StudentSessionId = 0;
        $RoleAttendanceEventId = 0;
        $ActivityRecognitionId = 0;
        $RefEmployedWhileEnrolledId = 0;
        



        // OrganizationPersonRole
        
        // ALUMNOS
        $alumnos = $tablaalumnos->listarAlumnosOrganizationJSON($idperiodo, $idestablecimiento);
        $k = 0;
        foreach ($alumnos as $key => $alumno) {
            
            // Alumno - Establecimiento
            $OrganizationPersonRoleId++;
            $OrganizationPersonRole[$k++] = [
                'OrganizationPersonRoleId' => $OrganizationPersonRoleId,
                'OrganizationId' => $establecimientosOrganizationRel[$alumno->idEstablecimiento]['OrganizationId'],
                'PersonId' => $alumnosRel[$alumno->idAlumnos]['PersonId'],
                'RoleId' => 7,
                'EntryDate' => '',
                'ExitDate' => ''
            ];       

            // Alumno - Curso
            $OrganizationPersonRoleId++;
            $OrganizationPersonRole[$k++] = [
                'OrganizationPersonRoleId' => $OrganizationPersonRoleId,
                'OrganizationId' => $cursosOrganizationRel[$alumno->idCursos]['OrganizationId'],
                'PersonId' => $alumnosRel[$alumno->idAlumnos]['PersonId'],
                'RoleId' => 7,
                'EntryDate' => '',
                'ExitDate' => ''
            ];        
            
            switch ($alumno->idCodigoGrado) {
                
                case 1: // Sala Cuna
                case 2:
                case 3:

                    $RefEntryGradeLevelId = [
                        'Code' => 'IT',
                        'Description' => 'Infant/toddler'
                    ];

                    break;

                case 4: // 1er nivel de transición ( Pre-kinder)
                    
                    $RefEntryGradeLevelId = [
                        'Code' => 'PK',
                        'Description' => 'Prekindergarten'
                    ];

                    break;
                
                case 5: // 2°nivel de Transición (Kinder)
                    
                    $RefEntryGradeLevelId = [
                        'Code' => 'KG',
                        'Description' => 'Kindergarten'
                    ];

                    break;

                case 6: // 1° Básico
                    
                    $RefEntryGradeLevelId = [
                        'Code' => '01',
                        'Description' => 'First grade'
                    ];

                    break;

                case 7: // 2° Básico
                    
                    $RefEntryGradeLevelId = [
                        'Code' => '02',
                        'Description' => 'Second grade'
                    ];

                    break;

                case 8: // 3° Básico
                    
                    $RefEntryGradeLevelId = [
                        'Code' => '03',
                        'Description' => 'Third grade'
                    ];

                    break;

                case 9: // 4° Básico
                    
                    $RefEntryGradeLevelId = [
                        'Code' => '04',
                        'Description' => 'Fourth grade'
                    ];

                    break;

                case 10: // 5° Básico
                    
                    $RefEntryGradeLevelId = [
                        'Code' => '05',
                        'Description' => 'Fifth grade'
                    ];

                    break;

                case 11: // 6° Básico
                    
                    $RefEntryGradeLevelId = [
                        'Code' => '06',
                        'Description' => 'Sixth grade'
                    ];

                    break;

                case 12: // 7° Básico
                    
                    $RefEntryGradeLevelId = [
                        'Code' => '07',
                        'Description' => 'Seventh grade'
                    ];

                    break;

                case 13: // 8° Básico
                    
                    $RefEntryGradeLevelId = [
                        'Code' => '08',
                        'Description' => 'Eighth grade'
                    ];

                    break;

                case 127: // 1° Medio
                case 135:
                case 142:
                case 149:
                case 156:
                case 163:
                case 170:

                    $RefEntryGradeLevelId = [
                        'Code' => '09',
                        'Description' => 'Ninth grade'
                    ];

                    break;
                
                case 128: // 2° Medio
                case 136:
                case 143:
                case 150:
                case 157:
                case 164:
                case 171:
                case 176:

                    $RefEntryGradeLevelId = [
                        'Code' => '10',
                        'Description' => 'Tenth grade'
                    ];

                    break;

                case 129: // 3° Medio
                case 137:
                case 144:
                case 151:
                case 158:
                case 165:
                case 172:
                case 177:

                    $RefEntryGradeLevelId = [
                        'Code' => '11',
                        'Description' => 'Eleventh grade'
                    ];

                    break;

                case 130: // 4° Medio
                case 138:
                case 145:
                case 152:
                case 159:
                case 166:
                case 173:
                case 178:

                    $RefEntryGradeLevelId = [
                        'Code' => '12',
                        'Description' => 'Twelfth grade'
                    ];

                    break;

                default:
                    # code...
                    break;
            }

            // K12StudentEnrollment
            $K12StudentEnrollment[$key] = [

                'OrganizationPersonRoleId'=> $OrganizationPersonRoleId,
                'RefEntryGradeLevelId'=> $RefEntryGradeLevelId,
                // 'RefPublicSchoolResidence'=> '',
                'RefEnrollmentStatusId'=> [
                                                'Code' => '01811',
                                                'Description' => 'Currently enrolled'
                                            ],
                // 'RefEntryType'=> '',
                'RefExitGradeLevel'=> [
                                            'Code' => '12',
                                            'Description' => 'Twelfth grade'
                                        ],
                // 'RefExitOrWithdrawalStatusId'=> '',
                // 'RefExitOrWithdrawalTypeId'=> '',
                // 'DisplacedStudentStatus'=> '',
                // 'RefEndOfTermStatusId'=> '', // Si Repitio el periodo anterior
                // 'RefPromotionReasonId'=> '', // La razón por la cual un alumno paso de curso
                // 'RefNonPromotionReasonId'=> '', // La razón por la cual un alumno no paso de curso
                // 'RefFoodServiceEligibilityId'=> '',
                // 'FirstEntryDateIntoUSSchool'=> '',
                // 'RefDirectoryInformationBlockStatusId'=> '',
                // 'NSLPDirectCertificationIndicator'=> '',
                // 'RefStudentEnrollmentAccessTypeId'=> ''

            ];

            // K12StudentAcademicHonor
            $K12StudentAcademicHonorId++;
            $K12StudentAcademicHonor[$key] = [

                'K12StudentAcademicHonorId' => $K12StudentAcademicHonorId,
                'OrganizationPersonRoleId' => $OrganizationPersonRoleId
                // 'RefAcademicHonorTypeId' => '',
                // 'HonorDescription' => ''

            ];

            // K12StudentCourseSection
            $K12StudentCourseSection[$key] = [

                'OrganizationPersonRoleId' => $OrganizationPersonRoleId,
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

            ];

            // K12StudentAcademicRecord
            $K12StudentAcademicRecord[$key] = [

                'OrganizationPersonRoleId' => $OrganizationPersonRoleId,
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

            ];

            // RoleAttendance
            $RoleAttendanceId++;
            $RoleAttendance[$key] = [

                'RoleAttendanceId' => $RoleAttendanceId,
                'OrganizationPersonRoleId' => $OrganizationPersonRoleId,
                // 'NumberOfDaysInAttendance' => '', // Asistencia
                // 'NumberOfDaysAbsent' => '', // Inasistencia
                // 'AttendanceRate' => ''
            ];

            // RoleStatus
            $RoleStatusId++;
            $RoleStatus[$key] = [

                'RoleStatusId' => $RoleStatusId,
                // 'StatusStartDate' => '',
                // 'StatusEndDate' => '',
                // 'RefRoleStatusId' => '',
                'OrganizationPersonRoleId' => $OrganizationPersonRoleId

            ];

            // K12StudentSession
            $K12StudentSessionId++;
            $K12StudentSession[$key] = [

                'K12StudentSessionId' => $K12StudentSessionId,
                'OrganizationPersonRoleId' => $OrganizationPersonRoleId,
                // 'OrganizationCalendarSessionId' => '',
                // 'GradePointAverageGivenSession' => ''

            ];

            // RoleAttendanceEvent
            $RoleAttendanceEventId++;
            $RoleAttendanceEvent[$key] = [
                
                'RoleAttendanceEventId' => $RoleAttendanceEventId,
                'OrganizationPersonRoleId' => $OrganizationPersonRoleId,
                // 'Date' => '',
                // 'RefAttendanceEventTypeId' => '',
                // 'RefAttendanceStatusId' => '',
                // 'RefAbsentAttendanceCategoryId' => '',
                // 'RefPresentAttendanceCategoryId' => '',
                // 'RefPresentAttendanceCategoryId' => '',

            ];


            // K12StudentEmployment
            $RefEmployedWhileEnrolledId++;
            $K12StudentEmployment[$key] = [

                'OrganizationPersonRoleId' => $OrganizationPersonRoleId,
                'RefEmployedWhileEnrolledId' => $RefEmployedWhileEnrolledId,
                // 'RefEmployedAfterExitId' => '',
                // 'EmploymentNaicsCode' => ''

            ];

            // ActivityRecognition
            $ActivityRecognitionId++;
            $ActivityRecognition[$key] = [

                'ActivityRecognitionId' => $ActivityRecognitionId,
                'OrganizationPersonRoleId' => $OrganizationPersonRoleId,
                // 'RefActivityRecognitionTypeId' => ''

            ];

            // Alumno - Asignaturas
            $asignaturas = $asig->listarAsignaturasAlumnoJSON($alumno->idCursos);
            
            foreach ($asignaturas as $key2 => $asignatura) {

                $OrganizationPersonRoleId++;
                
                $OrganizationPersonRole[$k++] = [
                    'OrganizationPersonRoleId' => $OrganizationPersonRoleId,
                    'OrganizationId' => $cursosOrganizationRel[$alumno->idCursos]['OrganizationId'],
                    'PersonId' => $alumnosRel[$alumno->idAlumnos]['PersonId'],
                    'RoleId' => 7,
                    'EntryDate' => '',
                    'ExitDate' => ''
                ];
                
            }


        }
            // Zend_Debug::dump($OrganizationPersonRole);die();

        // DOCENTES
        $docentes = $cuentas->listarDocentesOrganizationJSON($idperiodo, $idestablecimiento);
        $kd = 0;
        foreach ($docentes as $keyD => $docente) {
            
            // Docente - Establecimiento
            $OrganizationPersonRoleId++;
            $OrganizationPersonRoleD[$kd++] = [
                'OrganizationPersonRoleId' => $OrganizationPersonRoleId,
                'OrganizationId' => $establecimientosOrganizationRel[$docente->idEstablecimiento]['OrganizationId'],
                'PersonId' => $docentesRel[$docente->idCuenta]['PersonId'],
                'RoleId' => 6,
                'EntryDate' => '',
                'ExitDate' => ''
            ];       


            // Docente - Cursos
            $cursos = $cuentas->listarDocentesCursosJSON($docente->idCuenta, $idestablecimiento);

            foreach ($cursos as $key => $curso) {


                if (!is_null($curso->idCursos)) {
                    if (isset($cursosOrganizationRel[$curso->idCursos]['OrganizationId'])) {
                        $OrganizationPersonRoleId++;
                        $OrganizationPersonRole[$kd++] = [
                            'OrganizationPersonRoleId' => $OrganizationPersonRoleId,
                            'OrganizationId' => $cursosOrganizationRel[$curso->idCursos]['OrganizationId'],
                            'PersonId' => $docentesRel[$docente->idCuenta]['PersonId'],
                            'RoleId' => 6,
                            'EntryDate' => '',
                            'ExitDate' => ''
                        ];
                    }
                }
            }


            // Docente - Asignaturas
            // $asignaturas = $asig->listarAsignaturasDocenteJSON($docente->idCursos);
            
            // foreach ($asignaturas as $key => $asignatura) {


            //     $OrganizationPersonRoleId++;
                
            //     $OrganizationPersonRole = [
            //         'OrganizationPersonRoleId' => $OrganizationPersonRoleId,
            //         'OrganizationId' => $cursosOrganizationRel[$docente->idCursos]['OrganizationId'],
            //         'PersonId' => $docentesRel[$docente->iddocentes]['PersonId'],
            //         'RoleId' => 6,
            //         'EntryDate' => '',
            //         'ExitDate' => ''
            //     ];
                
            // }

        }


        $StaffEmploymentId++;
        $RefK12StaffClassificationId++;

        $_ComunidadEducativa = [ 
            
            'K12StaffEmployment' => [

                'StaffEmploymentId' => $StaffEmploymentId,  
                'RefK12StaffClassificationId' => $RefK12StaffClassificationId,    
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
             
            ],
            
            'K12StudentEnrollment' => [ $K12StudentEnrollment ],

            'K12StudentAcademicHonor' => [ $K12StudentAcademicHonor ],

            'StaffEmployment' => [

                'StaffEmploymentId' => $StaffEmploymentId,
                // 'OrganizationPersonRoleId' => '',
                // 'HireDate' => '',
                // 'PositionTitle' => '',
                // 'RefEmploymentSeparationTypeId' => '',
                // 'RefEmploymentSeparationReasonId' => '',
                // 'UnionMembershipName' => '',
                // 'WeeksEmployedPerYear' => ''

            ],

            'K12StaffAssignment' => [
               
                // 'OrganizationPersonRoleId' => '',
                'RefK12StaffClassificationId' => $RefK12StaffClassificationId,
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
                
            ],

            'K12StudentCourseSection' => [ $K12StudentCourseSection ],

            'K12StudentAcademicRecord' => [ $K12StudentAcademicRecord ],

            'RoleAttendance' => [ $RoleAttendance ],

            'RoleStatus' => [ $RoleStatus ],

            'K12StudentSession' => [ $K12StudentSession ],

            'OrganizationPersonRole' => [ $OrganizationPersonRole ],

            'RoleAttendanceEvent' => [ $RoleAttendanceEvent ],

            'Role' => [                    
                [
                    "RoleId" => 1,
                    "Name" => "Director"
                ],
                [
                    "RoleId" => 2,
                    "Name" => "Administrador"
                ],
                [
                    "RoleId" => 3,
                    "Name" => "Jefe UTP"
                ],
                [
                    "RoleId" => 4,
                    "Name" => "Inspector"
                ],
                [
                    "RoleId" => 5,
                    "Name" => "Profesor Jefe"
                ],
                [
                    "RoleId" => 6,
                    "Name" => "Docente"
                ],
                [
                    "RoleId" => 7,
                    "Name" => "Alumno"
                ],
                [
                    "RoleId" => 8,
                    "Name" => "Apoderado"
                ]
            ],

            'K12StudentEmployment' => [ $K12StudentEmployment ],

            'ActivityRecognition' => [ $ActivityRecognitionId ]
        ];
        

        Zend_Debug::dump($_ComunidadEducativa);die();


        /* ----------------------------------- FIN _ComunidadEducativa ----------------------------------- */






        

        /* ----------------------- CREA JSON ----------------------- */

        $_JSON = [
                    '$schema' => "http://json-schema.org/draft-07/schema#",
                    "title" => "LCD",
                    "description" => "Esquema del lilbro de clases electrónico",
                    "type" => "object",
                    "properties" => [
                                        "_Personas"       => $_Personas,
                                        "_Organizaciones" => $_Organizaciones,
                                        "_Incidentes"     => $_Incidentes 
                                ]
        ];

        // $jsonencoded = json_encode($_JSON,JSON_UNESCAPED_UNICODE);
        // $direccion = fopen("PRUEBA9.json", 'x+');
        // fwrite($direccion, $jsonencoded);
        // fclose($direccion);

        /* --------------------- FIN CREA JSON --------------------- */

        /* FIN RQM 26: JSON */
    }

   
}