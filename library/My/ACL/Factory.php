<?php

	class My_ACL_Factory {
		
		private static $_sessionNameSpace = 'My_ACL_Namespace';
		private static $_objAuth;
		private static $_objAclSession;
		private static $_objAcl;
		
		public static function get(Zend_Auth $objAuth,$bolClearACL=false) {

			self::$_objAuth = $objAuth;
			self::$_objAclSession = new Zend_Session_Namespace(self::$_sessionNameSpace);
			
			if($bolClearACL) { self::_clear(); }
			
			return (isset(self::$_objAclSession->acl)) ? self::$_objAclSession->acl : self::_loadAclFromDB();

		}
		
		private static function _clear() {
			unset(self::$_objAclSession->acl);
		}
		
		private static function _saveAclToSession() {
			self::$_objAclSession->acl = self::$_objAcl;
		}
		
		private static function _loadAclFromDB() {
			$arrRoles = Entities_RoleTable::getAll();
			$arrResources = Entities_ResourceTable::getAll();
			$arrRoleResources = Entities_RoleResourceTable::getAll();
			$arrCoreRoles = array();
			
			self::$_objAcl = new My_ACL();

			foreach($arrRoles as $role) {
				$arrCoreRoles[] = $role['role'];	
			} 

			while(count($arrRoles) > 0) {
				$role = array_shift($arrRoles);	
				if(isset($role['inherits'])) {
					$exists = true;
					$isCore = false;
					foreach($role['inherits'] as $index => $inherited) {
						if(in_array($inherited,$arrCoreRoles)) {
							$isCore = true;	
						} else {
							unset($role['inherits'][$index]);
						}
						
						if(!self::$_objAcl->hasRole($inherited)) {
							$exists = false;
						}	
					}
					
					if($exists && $isCore) {
						self::$_objAcl->addRole(new Zend_Acl_Role($role['role']),$role['inherits']);	
					} else {
						$arrRoles[] = $role;	
					}
				} else {
					self::$_objAcl->addRole(new Zend_Acl_Role($role['role']));
				}
			}
			

			foreach($arrResources as $resource) {
				self::$_objAcl->add(new Zend_Acl_Resource($resource['module'] .'::' .$resource['controller'] .'::' .$resource['action']));	
			}
			

			foreach($arrRoleResources as $roleResource) {
				self::$_objAcl->allow($roleResource['role']['roleName'],$roleResource['resource']['module'] .'::' .$roleResource['resource']['controller'] .'::' .$roleResource['resource']['action']);	
			}
			

			if(self::$_objAuth->hasIdentity()) {
				$arrRole = self::$_objAuth->getIdentity();
				$roleName = $arrRole['role'];
				$userId = $arrRole['id'];
				$accountId = $arrRole['accountId'];
				$arrAllow = Entities_AllowResourceTable::getAllowedResources($accountId,$userId);
				$arrDeny = Entities_DenyResourceTable::getDeniedResources($accountId,$userId);

				if(count($arrAllow) > 0) {
					foreach($arrAllow as $resource) {
						self::$_objAcl->allow($roleName,$resource);	
					}	
				}
				
				if(count($arrDeny) > 0) {
					foreach($arrDeny as $resource) {
						self::$_objAcl->deny($roleName,$resource);	
					}
				}
			}
			
			self::_saveAclToSession();	
			return self::$_objAcl;
		}
	}