<?php
     class My_Controller_Helper_Acl {
     
        public $acl;
        public function __construct()
        {
            // first create Zend_Acl instance
            $this->acl = new Zend_Acl();
        }
        public function setRole()
        {
            $this->acl->addRole(new Zend_Acl_Role('administrador'));
            $this->acl->addRole(new Zend_Acl_Role('Administradordaem'));
             $this->acl->addRole(new Zend_Acl_Role('Docente'));
             $this->acl->addRole(new Zend_Acl_Role('Director'));
             $this->acl->addRole(new Zend_Acl_Role('UTP'));
             
             $this->acl->addRole(new Zend_Acl_Role('Invitado'));
            // added roles above statically

            // Now adding roles below dynamically i.e. from db
            // class Application_Model_DbTable_EntitiesRoleTable is there at -
            // /application/models/DbTable/EntitiesRoleTable.php
            // check comments in Bootstarp.php mentioned below
            // need to add two lines of code in Bootstarp.php
            // with which this helper file can fetch the data from database.
            
            
            // I have model file for roles table with 2 columns roles.id and roles.name
            // at /zend_tutorial/application/models/DbTable/EntitiesRoleTable.php
            
            /*$model = new Application_Model_DbTable_EntitiesRoleTable();
            $data = $model->getAll();
            
            $guest_role_in_db = 0;
            foreach($data as $roles)
            {
                
                $this->acl->addRole(new Zend_Acl_Role($roles['name']));
                if($roles['name'] == 'guest')
                {
                    $guest_role_in_db = 1;
                }
            }
            
            if(!$guest_role_in_db)
            {
                $this->acl->addRole(new Zend_Acl_Role('guest'));
            }*/
        }

        public function setResource()
        {
            // Adding resources action-wise
            // that means, these actions of "every controller" are going to get validated 
            // for access for guest or editor or admin users (roles) etc.
            
            // $this->acl->add(new Zend_Acl_Resource('index'));
            
            $this->acl->add(new Zend_Acl_Resource('agregar'));
            $this->acl->add(new Zend_Acl_Resource('agregarform'));
            $this->acl->add(new Zend_Acl_Resource('editar'));
            $this->acl->add(new Zend_Acl_Resource('eliminar'));
            $this->acl->add(new Zend_Acl_Resource('loginform'));
            $this->acl->add(new Zend_Acl_Resource('logout'));
            $this->acl->add(new Zend_Acl_Resource('auth'));
            $this->acl->add(new Zend_Acl_Resource('getindicadores'));
            $this->acl->add(new Zend_Acl_Resource('guardaplanificacionclases'));
            $this->acl->add(new Zend_Acl_Resource('Cuentausuario'));
             $this->acl->add(new Zend_Acl_Resource('error'));
            
            
            
            // setting up access control as per controller
            // i.e. access control will work for these controllers only.
            // Suppose I want to control the access of only 2 controllers,
            // for ex. for NewsController and JobBoardController
            // you can also fetch these values from DB as done for roles
            // as above in function setRoles().
            
            $this->acl->add(new Zend_Acl_Resource('index'));
            $this->acl->add(new Zend_Acl_Resource('Sostenedor'));
            $this->acl->add(new Zend_Acl_Resource('Establecimiento'));
            $this->acl->add(new Zend_Acl_Resource('Asignaturas'));
            $this->acl->add(new Zend_Acl_Resource('Cursos'));
            $this->acl->add(new Zend_Acl_Resource('Periodo'));
            $this->acl->add(new Zend_Acl_Resource('usuarios'));
            $this->acl->add(new Zend_Acl_Resource('Libro'));
            $this->acl->add(new Zend_Acl_Resource('Alumnos'));
            $this->acl->add(new Zend_Acl_Resource('asistencia'));
            $this->acl->add(new Zend_Acl_Resource('Error'));
            $this->acl->add(new Zend_Acl_Resource('Personal'));
             $this->acl->add(new Zend_Acl_Resource('Planificaciones'));
             $this->acl->add(new Zend_Acl_Resource('Planificacionunidad'));
             $this->acl->add(new Zend_Acl_Resource('Planificacionclases'));
             $this->acl->add(new Zend_Acl_Resource('observaciones'));
             $this->acl->add(new Zend_Acl_Resource('getcursodocente'));
             $this->acl->add(new Zend_Acl_Resource('getasignaturadocente'));
             $this->acl->add(new Zend_Acl_Resource('getunidad'));
             $this->acl->add(new Zend_Acl_Resource('getobjetivos'));
             $this->acl->add(new Zend_Acl_Resource('getcursos'));
             $this->acl->add(new Zend_Acl_Resource('getaprendizajesmedia'));
             $this->acl->add(new Zend_Acl_Resource('guardaplanificacionanual'));
             $this->acl->add(new Zend_Acl_Resource('prueba'));
             
        }

        public function setPrivilage()
        {
        
           
            $this->acl->allow('Invitado','usuarios', array( 'index', 'logout','loginform','auth'));
           // $this->acl->allow('Invitado','index', array( 'index'));
           
            
            //administrador daem
            //Administrador
            $this->acl->allow('Administradordaem','usuarios',array( 'index', 'logout','loginform','auth'));
            $this->acl->allow('Administradordaem','Sostenedor');
            $this->acl->allow('Administradordaem','Establecimiento');
            $this->acl->allow('Administradordaem','index');
            $this->acl->allow('Administradordaem','Personal');
            $this->acl->allow('Administradordaem','Planificaciones', array('index','media','mediat','pdf','pdfsep','pdfmedia','pdfmediat'));
            $this->acl->allow('Administradordaem','Planificacionunidad', array('index','media','mediat','pdf','pdfsep','pdfmedia'));
            $this->acl->allow('Administradordaem','Planificacionclases', array('index','media','mediat','pdf','pdfsep','pdfmedia','pdfmt'));
            $this->acl->allow('Administradordaem','Cuentausuario');
            $this->acl->allow('Administradordaem','Asignaturas');
            $this->acl->allow('Administradordaem','Cursos');
            $this->acl->allow('Administradordaem','Periodo');
            //Administrador
            $this->acl->allow('administrador','usuarios',array( 'index', 'logout','loginform','auth'));
            $this->acl->allow('administrador','Sostenedor');
            $this->acl->allow('administrador','Establecimiento');
            $this->acl->allow('administrador','index');
            $this->acl->allow('administrador','Personal');
            $this->acl->allow('administrador','Planificaciones', array('index','media','mediat','pdf','pdfsep','pdfmedia','pdfmediat'));
            $this->acl->allow('administrador','Planificacionunidad', array('index','media','mediat','pdf','pdfsep','pdfmedia'));
            $this->acl->allow('administrador','Planificacionclases', array('index','media','mediat','pdf','pdfsep','pdfmedia','pdfmt'));
            $this->acl->allow('administrador','Cuentausuario');
            $this->acl->allow('administrador','Asignaturas');
            $this->acl->allow('administrador','Cursos');
            $this->acl->allow('administrador','Periodo');
            //Director
            
            $this->acl->allow('Director','usuarios',array( 'index', 'logout','loginform','auth'));
            $this->acl->allow('Director','Sostenedor');
            $this->acl->allow('Director','Establecimiento');
            $this->acl->allow('Director','index');
            $this->acl->allow('Director','Personal');
            $this->acl->allow('Director','Planificaciones', array('index','media','mediat','pdf','pdfsep','pdfmedia','pdfmediat'));
            $this->acl->allow('Director','Planificacionunidad', array('index','media','mediat','pdf','pdfsep','pdfmedia'));
            $this->acl->allow('Director','Planificacionclases', array('index','media','mediat','pdf','pdfsep','pdfmedia','pdfmt'));
            $this->acl->allow('Director','Cuentausuario');
            //$this->acl->allow('Director','Asignaturas');
            $this->acl->allow('Director','Cursos');
            $this->acl->allow('Director','Periodo');
            
            //Docente
            $this->acl->allow('Docente','usuarios',array( 'index', 'logout','loginform','auth'));
            $this->acl->allow('Docente','Cuentausuario',array('cambiarpass','cambiarperiodo'));
            $this->acl->allow('Docente','Planificaciones');
            $this->acl->allow('Docente','Planificacionunidad');
            $this->acl->allow('Docente','Planificacionclases');
            $this->acl->allow('Docente','index');
            
            //UTP
             $this->acl->allow('UTP','usuarios', array( 'index', 'logout','loginform'));
           $this->acl->allow('UTP','Establecimiento', array( 'index'));
        
        }
        
        public function setAcl()
        {
            // store acl object using Zend_Registry for future use. This is compulsory.
            Zend_Registry::set('acl',$this->acl);
        }
     }