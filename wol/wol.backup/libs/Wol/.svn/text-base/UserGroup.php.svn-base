<?php

/**
 * Stores the information about a group of hosts
 */
class Wol_UserGroup {

  	protected $_id = false;
  	protected $_name = false;
	protected $_users = array();
	protected $_level = false;
	
	/**
	 * Creates a UserGroup object
	 * @param int $id id of the group
	 * @param string $name group name
	 * @param int $level permission level of the group (1 => god, 255 => nothing)
	 */
	public function __construct ($id, $name, $level) {
		$this->_id = $id;
		$this->_name = $name;	
		$this->_level = $level;
	}

	/**
	 * Returns the group id
	 * @return int the id
	 */	
	public function getId () {
		return $this->_id;
	}

	/**
	 * Returns the groupName
	 * @return string the name
	 */
	public function getName () {
		return $this->_name;
	}

	/**
	 * Returns the users contained in the group
	 * @return array List containing all the Wol_UserData being part of the group
	 */
	public function getUsers () {
		return $this->_users;
	}

	/**
	 * Returns the permission level of this group
	 * @return int the permission level
	 */		
	public function getLevel () {
		return $this->_level;	
	}

	/**
	 * Changes the permission level of the group
	 * @param int $newLevel the new level
	 */	
	public function setLevel ($newLevel) {
		$this->_level=$newLevel;	
	}
	
	/**
	 * Adds a new user in the group
	 * @param Wol_UserData $user the new host
	 */
	public function addUser ($user) {
		if ($user instanceof Wol_UserData) {
			$this->_users[]=$user;
			return true;
		} else {
			return false;
		}	
	}

	/**
	 * Indicates if the user is in group or not
	 * @param Wol_UserData $idUser the user id
	 * @return bool true if the user is part of this group, else false
	 */	
	public function isInGroup ($idUser) {
		foreach ($this->_users as $user) {
			if ($user->getID() === $idUser) {
				return true;			
			}
		}		
		return false;
	}

	/**
	 * Returns some information about this group
	 * @param bool $onlyUserID if true, only returns the list of user ids. if not specified or false, returns all the information about each user
	 * @return array the serialized data. Contains : idUserGroup, nameUserGroup, usersInGroup, level
	 */	 	
	public function getSerialized ($onlyUserID = false) {
		$return = array(
			"idUserGroup" => $this->_id,
			"nameUserGroup" => $this->_name,
			"usersInGroup" => array(),
			"level" => $this->_level
		);
		foreach ($this->_users as $user) {
			$return['usersInGroup'][] = ($onlyUserID) ? $user->getID() : $user->getSerialized();
		}
		return $return;
	}
	
}
?>
