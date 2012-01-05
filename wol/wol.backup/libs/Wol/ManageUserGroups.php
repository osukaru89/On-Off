<?php

/**
 * Manages the data about user groups stored in the model and in the database
 */
class Wol_ManageUserGroups {

	protected $_usersGroups = array();
	static private $_instance;

	/**
	 * Queries the database and constructs the model
	 */
	private function __construct() {
		$query = 'SELECT UG.id_usergroup ID, UG.name NAME, UG.level LEVEL FROM php_wol_usergroups UG;';
		$list = mysql_query ($query);

		$usersManager = Wol_ManageUsers::getInstance();
		
	   while ( $rowList = mysql_fetch_array($list) ) {
			$newGroup = new Wol_UserGroup ($rowList['ID'], $rowList['NAME'], $rowList['LEVEL'] );
			
			$queryUsers = sprintf (
				'SELECT UUG.id_user USERID FROM php_wol_users_usergroups UUG WHERE UUG.id_usergroup = %s;', 
				$rowList['ID']
			);
			$listUsers = mysql_query ($queryUsers);
			
			while ( $rowListUsers = mysql_fetch_array($listUsers) ) {
				$newGroup->addUser($usersManager->getByID($rowListUsers['USERID']));	
			}
			
			$this->_usersGroups[] = $newGroup;
		}
	}

	/**
	 * Returns the unique object of the class
	 * (and creates it if it does not exist)
	 * @static
	 * @return Wol_ManageUserGroups the unique instance of the class
	 */		
	static public function getInstance() {
		if ( (!isset(self::$_instance)) || (self::$_instance == NULL) ) {
			self::$_instance = new Wol_ManageUserGroups();
		}
		return self::$_instance;
	}

	/**
	 * Searches a group in this class' list
	 * @param int $id the id of the group we want to get
	 * @return Wol_UserGroup|bool the group with the given id (or false if it does not exist)
	 */
	public function getByID ($id) {
		foreach ( $this->_usersGroups as $currentGroup) {
			if ($currentGroup->getID() === $id) {
				return $currentGroup;
			}
		}
		return false;
	}

	/**
	 * Returns a list with all the existing user groups
	 * @return array the list of groups
	 */		
	public function getUserGroups () {
		return $this->_usersGroups;
	}
	
	/**
	 * Searches a group in this class' list and deletes it
	 * @param int $id the id of the group we want to delete
	 */
	protected function removeUserGroupFromList ($id) {
		for ($i=0;$i<count($this->_usersGroups);$i++) {
			if ($this->_usersGroups[$i]->getID() === $id) {
				unset($this->_usersGroups[$i]);
			}
		}
	}

	/**
	 * Adds a group to this class' list of groups
	 * @param Wol_UserGroup $newUserGroup the new group
	 */
	protected function addUserGroupToList ($newUserGroup) {
		$this->_usersGroups[] = $newUserGroup;
	}
	
	/**
	 * Deletes the group from the database and from the model
	 * @param int $id the id of the group to delete
	 * @return bool true if the group has been deleted from the database, else false
	 */
	public function deleteUserGroup ($id) {
		$query = sprintf("DELETE FROM php_wol_usergroups WHERE id_usergroup = '%s' ;", $id);
		$result = mysql_query ($query);
		if ($result) {
			$this->removeUserGroupFromList($id);
			return true;
		} else {
			return false;
		}
	}

	/**
	 * Creates a new group in the database and in the model
	 * @param string $name the name of the new group
	 * @param string $level the level of the new group
	 * @return int the id of the new group
	 */
	public function createUserGroup ($name, $level) {
		$query = sprintf("INSERT INTO php_wol_usergroups (name, level) VALUES ('%s', %s);", $name, $level);
		$result = mysql_query ($query);    
		if ($result !== false) {
			$queryID = sprintf("SELECT UG.name, UG.id_usergroup IDUG FROM php_wol_usergroups UG WHERE UG.name = '%s';", $name);
   		$resultID = mysql_query ($queryID);
   		$rowID = mysql_fetch_array($resultID);
			$newGroup = new Wol_UserGroup ($rowID['IDUG'], $name, $level);
			$this->addUserGroupToList($newGroup);
			return $rowID['IDUG'];
		} else {
			return false ;
		}
	}

	/**
	 * Changes the access level of an user group
	 * @param int $id the id of the group
	 * @param string $newLevel the new value of level
	 * @return bool true on success, false on failure
	 */		
	public function changeLevel ($idGroup, $newLevel) {
		$query = sprintf ('UPDATE php_wol_usergroups SET level = %s WHERE id_usergroup = %s;', $newLevel, $idGroup);
		$result = mysql_query ($query);
		return ($result !== false);
	}
	
	/**
	 * Adds an user to an user group in the database and in the model
	 * @param int $idGroup the id of the group
	 * @param int $idUser the id of the user
	 * @return bool true if the relationship has been added in the database, else false
	 */	
	public function addUserToGroup ($idGroup, $idUser) {
		$query = sprintf ('INSERT INTO php_wol_users_usergroups (id_user, id_usergroup) VALUES (%s, %s);', $idUser, $idGroup);
		$result = mysql_query ($query);
		return ($result !== false);
	}

	/**
	 * Removes an user from an user group in the database and in the model
	 * @param int $idGroup the id of the group
	 * @param int $idUser the id of the user
	 * @return bool true if the relationship has been deleted in the database, else false
	 */	
	public function removeUserFromGroup ($idGroup, $idUser) {
		$query = sprintf ('DELETE FROM php_wol_users_usergroups WHERE id_user = %s AND id_usergroup = %s;', $idUser, $idGroup);
		$result = mysql_query ($query);
		return ($result !== false);
	}


	/**
	 * Serializes all user groups
	 * @param bool $onlyUserID if true, only the id of users will be return (default false : all users data are returned)
	 * @return array A list of groups with the required data
	 */			
	public function getSerialized ($onlyUserID = false) {
		$list2return = array();
		foreach ( $this->_usersGroups as $currentGroup) {
			$list2return[] = $currentGroup->getSerialized($onlyUserID);
		}
		return $list2return;
	}

	
}
?>
