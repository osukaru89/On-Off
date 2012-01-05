<?php

/**
 * Authenticates the users with LDAP
 */
class Ldap_Authentication {
	
	static private $_instance ;
	static private $_config;
	static private $_default_config_file = 'ldap_default_config.cfg' ;
	static private $_config_file = 'ldap_access.cfg' ;
	static private $_connect_resource = false;
	
	private function __construct() { }

	/**
	 * Returns the unique object of the class
	 * (and creates it if it does not exist)
	 * @static
	 * @return Ldap_Authentication the unique instance of the class
	 */	
	static public function getInstance() {
		if ( (!isset(self::$_instance)) || (self::$_instance == NULL) ) {
			self::$_instance = new Ldap_Authentication();
		}
		return self::$_instance;
	}

	/**
	 * Gets connexion to the LDAP database
	 * Reads config files to get the address and port of LDAP server and connects to it
	 * and then try to authenticate as 'admin' of the LDAP server with the identifiers stored by the config file
	 * @param string $dirConf the absolute path of the directory where are configuration files
	 * @return bool true on success, false on failure
	 */	
	public function connect($dirConf) {
		$confManager = Config_ManageConf::getInstance();
		self::$_config = $confManager->parseConfig($dirConf . self::$_config_file);
		self::$_connect_resource = ldap_connect(self::$_config['host'], self::$_config['port']);
		ldap_set_option(self::$_connect_resource, LDAP_OPT_PROTOCOL_VERSION, 3);
		return ldap_bind (self::$_connect_resource, self::$_config['binddn'], self::$_config['bindpw']);
	}

	/**
	 * Reads the config file to know if the user decided to use LDAP or not
	 * @return bool true if LDAP authentication must be used, false if not
	 */		
	public function ldapIsUsed() {
		return (self::$_config['useldap'] === 'yes');
	}

	/**
	 * Reads the configuration file and returns contained parameters
	 * @param string $dirConf the absolute path of the directory where are configuration files
	 * @return array|bool the array of parameters, or false on failure
	 */		
	public function getLdapConfig ($dirConf) {
		$confManager = Config_ManageConf::getInstance();
		return $confManager->parseConfig($dirConf . self::$_config_file);
	}

	/**
	 * Reads the default configuration file and returns contained parameters
	 * @param string $dirConf the absolute path of the directory where are configuration files
	 * @return array|bool the array of default parameters, or false on failure
	 */			
	public function getDefaultLdapConfig ($dirConf) {
		$confManager = Config_ManageConf::getInstance();
		return $confManager->parseConfig($dirConf . self::$_default_config_file);
	}

	/**
	 * Writes the configuration in the file from parameters stored in the array
	 * @param string $dirConf the absolute path of the directory where are configuration files
	 * @param array $params the array of parameters to write in the file
	 * @return array|bool the array of parameters, or false on failure
	 */			
	public function setLdapConfig ($dirConf, $params) {
		$confManager = Config_ManageConf::getInstance();
		$confManager->setConfig($dirConf . self::$_config_file, $params);
	}

	/**
	 * Search in the LDAP database and returns all users
	 * @return array the array of users. Contains for each user : login, ldapID and email (if exists)
	 */			
	public function getAllUsers () {
		$filter= self::$_config['userloginattr'] . "=*";
		$toGet = array(self::$_config['userloginattr'], self::$_config['usermailattr'], self::$_config['useridattr']);
		$search = ldap_search(self::$_connect_resource, self::$_config['usersdn'], $filter, $toGet);
		$info = ldap_get_entries(self::$_connect_resource, $search);
		
		$users = array();
		
		foreach ($info as $i) {
			if (isset ($i[self::$_config['userloginattr']])) {
				$users[] = array (
					"login" => $i[self::$_config['userloginattr']][0],
					"email" => ( isset ($i[self::$_config['usermailattr']]) ) ? $i[self::$_config['usermailattr']][0] : NULL,
					"ldapID" => $i[self::$_config['useridattr']][0]
				);
			}
		}
		return $users;
	}

	/**
	 * Authenticates the user
	 * @param string $login the login given by the user
	 * @param array $password the password
	 * @return bool true if the login is in the database and if the password matches, else false
	 */	
	public function authenticate ($login, $password) {
		$filter= self::$_config['userloginattr'] . "=" . $login;
		$toGet = array(self::$_config['userloginattr'], self::$_config['userpwdattr']);
		$search = ldap_search(self::$_connect_resource, self::$_config['usersdn'], $filter, $toGet);
		$info = ldap_get_entries(self::$_connect_resource, $search);

		foreach ($info as $i) {
			if (isset ($i[self::$_config['userloginattr']])) {
				if ($this->compare($password, $i[self::$_config['userpwdattr']][0])) {
					return true;
				}
			}
		}
		return false;
	}

	/**
	 * Gets all the information about an user
	 * @param string $login the login of the user
	 * @return array|bool a list containing the email and the ldapID of the user (or false if the login has not been found)
	 */		
	public function getLdapData ($login) {
		$filter= self::$_config['userloginattr'] . "=" . $login;
		$toGet = array(self::$_config['userloginattr'], self::$_config['usermailattr'], self::$_config['useridattr']);
		$search = ldap_search(self::$_connect_resource, self::$_config['usersdn'], $filter, $toGet);
		$info = ldap_get_entries(self::$_connect_resource, $search);

		foreach ($info as $i) {
			if (isset ($i[self::$_config['userloginattr']])) {
				return array (
					"email" => $i[self::$_config['usermailattr']][0],
					"ldapID" => $i[self::$_config['useridattr']][0]
				);
			}
		}
		return false;		

	}

	/**
	 * Gets all the information about an user
	 * @param string $password the password provided by the user
	 * @param string $hash the hash stored in the LDAP database
	 * @return bool true if the password matches with the hash, else false
	**/		
	protected function compare ($password, $hash) {
		if (preg_match('/(\{(?P<method>.+)\})?(?P<hash>.*)/', $hash, $matches)) {
			switch($matches['method']) {
				case 'MD5':
					$newHash = base64_encode(md5($password, true));
				break;
				case 'SHA':
					$newHash = base64_encode(mhash(MHASH_SHA1, $password));
				break;
				case 'SSHA':
					$salt = substr(base64_decode($matches['hash']), 20);
					$newHash = base64_encode(mhash(MHASH_SHA1, $password . $salt) . $salt);
				break;
				case 'SMD5':
					$salt = substr(base64_decode($matches['hash']), -4);
					$newHash = base64_encode(mhash(MHASH_MD5, $password . $salt) . $salt);
				break;
				default:
					$newHash = $hash;
				break;
			}
			return $newHash === $matches['hash'];
		}
      return false;
	}

}
?>