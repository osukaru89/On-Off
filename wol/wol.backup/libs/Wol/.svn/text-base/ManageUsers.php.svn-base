<?php

/**
 * Manages the data about users stored in the model and in the database
 */
class Wol_ManageUsers {

	protected $_usersData = array();
	static private $_instance ;
	
	/**
	 * Queries the database and constructs the model
	 */
	public function __construct() {
		$db = Wol_DB_Database::getInstance();
		$db->connect();
		$queryListUsers = 'SELECT U.login LOG, U.id_user ID, U.is_ldap_user ISLDAP, U.email EM, U.date_insert DI, UR.id_user, UR.id_role, R.id_role, R.name ROLE
			FROM php_wol_users U, php_wol_roles R, php_wol_users_roles UR
			WHERE UR.id_user = U.id_user
			AND R.id_role = UR.id_role
			ORDER BY U.id_user;';
		$listUsers = mysql_query ($queryListUsers);

		while ($rowListUsers = mysql_fetch_array($listUsers)) {
			if ($rowListUsers['ISLDAP']) {
				$queryLdap = 'SELECT LU.id_user, LU.ldap_uid LDAPID FROM php_wol_ldap_users LU WHERE LU.id_user = ' . $rowListUsers['ID'] . ';';				
				$listLdap = mysql_query ($queryLdap);
				$rowLdap = mysql_fetch_array($listLdap);
				$user2add = new Wol_UserLdap($rowLdap['LDAPID'], $rowListUsers['ID'], $rowListUsers['LOG'], $rowListUsers['EM'], $rowListUsers['DI'], $rowListUsers['ROLE']);
			} else {
				$user2add = new Wol_UserData($rowListUsers['ID'], $rowListUsers['LOG'], $rowListUsers['EM'], $rowListUsers['DI'], $rowListUsers['ROLE']);
			}
			
			$queryAssociatedHosts = sprintf('SELECT H.name NAME, H.id_host IDH, UH.id_host, UH.id_user FROM php_wol_hosts H, php_wol_users_hosts UH WHERE UH.id_host = H.id_host AND UH.id_user = %s ORDER BY H.id_host;', $rowListUsers['ID']);
			$resultqueryAssociatedHosts = mysql_query ($queryAssociatedHosts);

			while ($rowAssociatedHosts = mysql_fetch_array($resultqueryAssociatedHosts)) {
				$hostsManager = Wol_ManageHosts::getInstance();
				$user2add->addHost( $hostsManager->getByID ($rowAssociatedHosts['IDH']) );
			}
		
			$this->_usersData[] = $user2add;
		}
	}

	/**
	 * Returns the unique object of the class
	 * (and creates it if it does not exist)
	 * @static
	 * @return Wol_ManageUsers the unique instance of the class
	 */	
	static public function getInstance() {
		if ( (!isset(self::$_instance)) || (self::$_instance == NULL) ) {
			self::$_instance = new Wol_ManageUsers();
		}
		return self::$_instance;
	}

	/**
	 * Searches a user in this class' list of farms from his id
	 * @param int $id the id of the user we want to get
	 * @return Wol_UserData|bool the user with the given id (or false if it does not exist)
	 */
	public function getByID($id) {
		$nbUsers = count($this->_usersData);
		for ($i = 0; $i < $nbUsers ; $i++) {
			if ( $this->_usersData[$i]->getID() == $id ) return $this->_usersData[$i];
		}
		return false;
	}

	/**
	 * Searches a user in this class' list of farms from his email
	 * @param string $email the email of the user we want to get
	 * @return Wol_UserData|bool the user with the given id (or false if it does not exist)
	 */	
	public function getByEmail($email) {
		$nbUsers = count($this->_usersData);
		for ($i = 0; $i < $nbUsers ; $i++) {
			if ( $this->_usersData[$i]->getEmail() === $email ) return $this->_usersData[$i];
		}
		return false;
	}

	/**
	 * Searches a user in this class' list of farms from his LDAP id
	 * @param string $uid the LDAP id of the user we want to get
	 * @return Wol_UserData|bool the user with the given LDAP id (or false if it does not exist)
	 */	
	public function getByLdapID($uid) {
		$nbUsers = count($this->_usersData);
		for ($i = 0; $i < $nbUsers ; $i++) {
			if ( $this->_usersData[$i] instanceof Wol_UserLdap ) {
				if ($this->_usersData[$i]->getLdapUID() === $uid) {
					return $this->_usersData[$i];
				}
			}
		}
		return false;
	}

	/**
	 * Gets a On/Off admin
	 * @return Wol_UserData|bool the first user found that is admin of On/Off  (or false if no one could be found)
	 */		
	public function getAdmin() {
		$nbUsers = count($this->_usersData);
		for ($i = 0; $i < $nbUsers ; $i++) {
			if ( $this->_usersData[$i]->getRole() == 'admin' ) return $this->_usersData[$i];
		}
		return false;
	}

	/**
	 * Searches a user in this class' list and deletes him
	 * @param int $id the id of the user we want to delete
	 */
	protected function removeUserFromList($id) {
		for ($i=0;$i<count($this->_usersData);$i++) {
			if ($this->_usersData[$i]->getID() === $id) {
				unset($this->_usersData[$i]);
			}
		}
	}

	/**
	 * Adds a user to this class' list
	 * @param Wol_UserData $newUser the new user
	 */
	protected function addUserToList ($newUser) {
		$this->_usersData[] = $newUser;
	}

	/**
	 * Gets the login and id of each user in the list
	 * @return array For each user, contains his login and id
	 */	
	public function getLoginsAndID() {
		foreach ( $this->_usersData as $currentUser) {
			$user2add = array (
				'id' => $currentUser->getId(),
				'login' => $currentUser->getLogin(),
			);
		$list2return[] = $user2add;
		}
		return $list2return;
	}

	/**
	 * Deletes the user from the database and from the model
	 * @param int $id the id of the user to delete
	 * @return bool true if the user has been deleted from the database, else false
	 */
	public function deleteUser ($id) {
		$usersManager = Wol_ManageUsers::getInstance();
		$hostsManager = Wol_ManageHosts::getInstance();
		$adminId = $usersManager->getAdmin()->getID();
		$adminName = $usersManager->getAdmin()->getLogin();		
		$hostsManager->deletedOwner ($id, $adminId, $adminName);
		$query = sprintf("DELETE FROM php_wol_users WHERE id_user = '%s' ;", $id);
		$result = mysql_query ($query);
		if ($result) {
			$this->removeUserFromList($id);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Creates a new user in the database and in the model
	 * @param string $login user login
	 * @param string $email user email address
	 * @param string $password user password	 
	 * @param string $role user's role : 'user' (default) or 'admin'
	 * @return bool true if the user has been added in the database, else false
	 */
	public function createUser ($login, $email, $password, $role = 'user') {

		$query = sprintf("INSERT INTO php_wol_users (login, pass, email) VALUES ('%s', '%s', '%s');", $login, hashpass ($password), $email);
		$result = mysql_query ($query); 

		if (! ($result)) return false;

		$searchNewUserID = mysql_query( sprintf('SELECT U.login, U.id_user IDU, U.date_insert DATE FROM php_wol_users U WHERE U.login = "%s";', $login) );
		$rowUserID = mysql_fetch_array($searchNewUserID);

		$querySearchRole = sprintf ('SELECT R.name, R.id_role IDR FROM php_wol_roles R WHERE R.name = "%s";', $role);
		$resultSearchRole = mysql_query ($querySearchRole);
		$rowSearchRole = mysql_fetch_array($resultSearchRole);

		$queryInsertRole = sprintf('INSERT INTO php_wol_users_roles (id_role, id_user) VALUES (%s, %s);', $rowSearchRole['IDR'], $rowUserID['IDU']);
		$resultInsertRole = mysql_query ($queryInsertRole);

		if ($resultInsertRole !== false) $this->addUserToList( new Wol_UserData($rowUserID['IDU'], $login, $email, $rowUserID['DATE'], $role) );

		return ($resultInsertRole !== false);
	}

	/**
	 * Creates a new LDAP authenticated user in the database and in the model
	 * @param string $login user login
	 * @param string $email user email address
	 * @param string $uid user LDAP id
	 * @param string $role user's role : 'user' (default) or 'admin'
	 * @return bool true if the user has been added in the database, else false
	 */	
	public function createLdapUser ($login, $email, $uid, $role = 'user') {

		$query = sprintf("INSERT INTO php_wol_users (login, pass, email, is_ldap_user) VALUES ('%s', '0', '%s', true);", $login, $email);
		$result = mysql_query ($query); 

		if (! ($result)) return false;

		$searchNewUserID = mysql_query( sprintf('SELECT U.login, U.id_user IDU, U.date_insert DATE FROM php_wol_users U WHERE U.login = "%s";', $login) );
		$rowUserID = mysql_fetch_array($searchNewUserID);

		$querySearchRole = sprintf ('SELECT R.name, R.id_role IDR FROM php_wol_roles R WHERE R.name = "%s";', $role);
		$resultSearchRole = mysql_query ($querySearchRole);
		$rowSearchRole = mysql_fetch_array($resultSearchRole);

		$queryInsertRole = sprintf('INSERT INTO php_wol_users_roles (id_role, id_user) VALUES (%s, %s);', $rowSearchRole['IDR'], $rowUserID['IDU']);
		$resultInsertRole = mysql_query ($queryInsertRole);

		$queryInsertLdap = sprintf('INSERT INTO php_wol_ldap_users (ldap_uid, id_user) VALUES ("%s", %s);', $uid, $rowUserID['IDU']);
		$resultInsertLdap = mysql_query ($queryInsertLdap);
		
		if ( ($resultInsertRole !== false) && ($resultInsertLdap !== false) ) $this->addUserToList( new Wol_UserLdap($uid, $rowUserID['IDU'], $login, $email, $rowUserID['DATE'], $role) );

		return ( ($resultInsertRole !== false) && ($resultInsertLdap !== false) );
	}

	/**
	 * Changes the email address of an user in the database and in the model
	 * @param int $id the id of the host
	 * @param string $newEmail the new value of email address
	 * @return bool true on success, false on failure
	 */	
	public function changeEmail ($id, $newEmail) {
		$query = sprintf ('UPDATE php_wol_users SET email = "%s" WHERE id_user = "%s";', $newEmail, $id);
		$result = mysql_query ($query);
		if (($usr = $this->getByID($id)) !== false) $usr->setEmail($newEmail);
		return ($result !== false);
	}

	/**
	 * Changes the password of an user in the database and in the model, if he is not a LDAP user
	 * @param int $id the id of the user
	 * @param string $newpass the new value of password
	 * @return bool true on success, false on failure
	 */	
	public function changePassword ($id, $newpass) {
		if ($this->getByID($id) instanceof Wol_UserLdap) {
			return false;
		} else {
			$query = sprintf ('UPDATE php_wol_users SET pass = "%s" WHERE id_user = "%s";', hashpass($newpass), $id);
			$result = mysql_query ($query);
			return ($result !== false);
		}
	}

	/**
	 * Changes the role of an user in the database and in the model
	 * @param int $id the id of the user
	 * @param string $newRole the new role ('user' or 'admin')
	 * @return bool true on success, false on failure
	 */	
	public function changeRole ($id, $newRole) {
		$queryIDRole = sprintf('SELECT R.id_role IDROLE, R.name FROM php_wol_roles R WHERE R.name = "%s";', $newRole);
		$resultIDRole = mysql_query ($queryIDRole);
		$rowIDRole = mysql_fetch_array($resultIDRole);

		$query = sprintf ('UPDATE php_wol_users_roles SET id_role = "%s" WHERE id_user = "%s";', $rowIDRole['IDROLE'], $id);
		$result = mysql_query ($query);
		if (($usr = $this->getByID($id)) !== false) $usr->setRole($newRole);
		return ($result !== false);
	}
	
	/**
	 * Adds an host to a farm in the database and in the model
	 * @param int $userID the id of the user
	 * @param int $hostID the id of the host
	 * @return bool true if the relationship has been added in the database, else false
	 */
	public function addHost ($userID, $hostID) {
		$query = sprintf ('INSERT INTO php_wol_users_hosts (id_user, id_host) VALUES (%s, %s);', $userID, $hostID);
		$result = mysql_query ($query);
		$hostsManager = Wol_ManageHosts::getInstance();
		if (($usr = $this->getByID($userID)) !== false) $usr->addHost($hostsManager->getByID($hostID));
		return ($result !== false);
	}

	/**
	 * Removes an host from an user in the database and in the model
	 * @param int $userID the id of the user
	 * @param int $hostID the id of the host
	 * @return bool true if the relationship has been deleted from the database, else false
	 */
	public function removeHost ($userID, $hostID) {
		$query = sprintf ('DELETE FROM php_wol_users_hosts WHERE id_host = %s AND id_user = %s;', $hostID, $userID);
		$result = mysql_query ($query);
		if (($usr = $this->getByID($userID)) !== false) $usr->removeHost($hostID);
		return ($result !== false);
	}
	
	/**
	 * Adds a custom name for the host, just for this user.
	 * Warning : this relationship only exists on the database, never in the model !
	 * @param int $userID the id of the user
	 * @param int $hostID the id of the host
	 * @param string $newName the custom name for the relationship host <-> user
	 * @return bool true if the name has been added in the database, else false
	 */
	public function setHostNameForUser ($userID, $hostID, $newName) {
		$query = sprintf ('UPDATE php_wol_users_hosts SET hostname_for_user = "%s" WHERE id_user = "%s" AND id_host = "%s";', $newName, $userID, $hostID);		
		$result = mysql_query ($query);
		return ($result !== false);
	}

	/**
	 * Generates a new password for the user and writes its "md5" in the database
	 * @param Wol_UserData $user the user
	 * @return string the text-clear generated password
	 */	
	public function generatePassForUser ($user) {
		$newpass = "";
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$nbChars = strlen($chars);
		for($u = 1; $u <= 10; $u++) {
    		$selectedChar = mt_rand(0,($nbChars-1));
    		$newpass .= $chars[$selectedChar];
    	}
		$query = sprintf ('UPDATE php_wol_users SET pass = "%s" WHERE id_user = "%s";', hashpass($newpass), $user->getID());
		$result = mysql_query ($query);
    	
    	return $newpass;
   }
 
 	/**
	 * Returns an array with all the custom names given to hosts for this user
	 * @param int $idUser the user id
	 * @return array the list of hosts with custom names (for each host, contains : idH, nameForUser)
	 */	  
	public function getCustomHostnamesForUser($idUser) {
		$return = array();
		$query = sprintf('SELECT UH.id_user, UH.id_host IDH, UH.hostname_for_user HNFU FROM php_wol_users_hosts UH
			WHERE UH.hostname_for_user <> "" AND UH.id_user = "%s";', $idUser);
		$result = mysql_query ($query);

		while ($row = mysql_fetch_array($result)) {
			$return[]= array (
				'idH' => $row['IDH'],
				'nameForUser' => $row['HNFU']
			);
		}
		return $return;
	}

	/**
	 * Serializes all users
	 * @param bool $withHosts if true, a list of hosts being with each user is also returned (default false : no hosts)
	 * @param bool $onlyHostID if true, only the id of hosts will be returned (default false : all data are returned)
	 * @return array A list of users with the required data
	 */	
	public function getSerialized ($withHosts=false, $onlyHostID=false) {
		$return = array();
		foreach ($this->_usersData as $user) {
			$return[]=$user->getSerialized($withHosts, $onlyHostID);
		}
		return $return;
	}
	
	
}

?>	