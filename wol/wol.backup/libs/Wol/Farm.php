<?php

/**
 * Stores the information of a farm
 */
class Wol_Farm {
	
	protected $_name = false;
	protected $_id = false;
	protected $_users = array();
	protected $_hosts = array();
	protected $_view = false;

	/**
	 * Returns the farm's name
	 * @return string the farm's name
	 */
	public function getName() {
		return $this->_name;
	}

	/**
	 * Sets the farm's name
	 * @param string $name the new name
	 */
	public function setName($name) {
		$this->_name = $name;
	}

	/**
	 * Returns the farm id
	 * @return int the id
	 */
	public function getID() {
		return $this->_id;
 	}

	/**
	 * Sets the farm's id
	 * @param int $id the new id
	 */
	public function setID($id) {
		$this->_id = $id;
	}

	/**
	 * Returns the view of the farm (i.e. if the users can see hosts in the farm or not)
	 * @return bool true if users can see, false if they cannot
	 */	
	public function getView() {
		return $this->_view;
 	}

	/**
	 * Sets the farm's id
	 * @param bool $view the new id
	 */
	public function setView($view) {
		$this->_view = $view;
	}

	/**
	 * Adds a new user in the farm
	 * @param Wol_UserData $user the new user
	 */
	public function addUser($user) {
		$this->_users[] = $user;
	}

	/**
	 * Adds a new host in the farm
	 * @param Wol_Host $host the new host
	 */
	public function addHost($host) {
		$this->_hosts[] = $host;
	}
	
	public function getUsers() {
		return $this->_users;	
	}	
	
	/**
	 * Removes an user from the farm
	 * @param int $userID the id of user to remove from farm
	 */
	public function removeUser ($userID) {
		for ($i=0;$i<count($this->_users);$i++) {
			if ($this->_users[$i]->getID() === $userID) {
				unset($this->_users[$i]);
			}
		}
	}

	/**
	 * Removes an host from the farm
	 * @param int $hostID the id of host to remove from farm
	 */	
	public function removeHost ($hostID) {
		for ($i=0;$i<count($this->_hosts);$i++) {
			if ($this->_hosts[$i]->getID() === $hostID) {
				unset($this->_hosts[$i]);
			}
		}
	}

	/**
	 * Asks to wake on all the hosts being part of the farm
	 */		
	public function wakeOnLan () {
		foreach ($this->_hosts as $h) {
			$h->wakeOnLan();
		}
	}

	/**
	 * Asks to turn off all the hosts being part of the farm
	 */			
	public function turnOff () {
		foreach ($this->_hosts as $h) {
			$h->turnOff();
		}
	}

	/**
	 * Serializes all the farm's data
	 * @param bool $onlyID if true, only the id of hosts and farms will be return (default false : all data are returned)
	 * @param bool $withUsers if true, a list of users being part of farm is also returned (default false : no users data)
	 * @param bool $withHosts if true, a list of hosts being part of farm is also returned (default false : no hosts data)
	 * @param int $forUser if true, hosts data will be returned with custom names given to hosts for the user with the given id (default NULL : no custom names)
	 * @return array A list of farm's data (content : id, name, hostsinfarm, usersinfarm, usersCanView (0/1), forAdminPage (0/1))
	 */	
	public function getSerialized ($onlyID=false, $withUsers=false, $withHosts=false, $forUser = NULL) {
		$USER = Wol_User::getInstance();
		$return = array(
			"id" => $this->_id,
			"name" => $this->_name,
			"hostsinfarm" => array(),
			"usersinfarm" => array(),
			"usersCanView" => ($this->_view) ? 1 : 0,
			"forAdminPage" => ( ($forUser == NULL) && ($USER->isAuthenticatedAndAdmin()) ) ? 1 : 0
		);
		
		if ($withUsers) {
			$usersManager = Wol_ManageUsers::getInstance();
			foreach ($this->_users as $u) {
				$return["usersinfarm"][]= $onlyID ? $u->getID() : $u->getSerialized();
			}
		}
		
		if ($withHosts) {
			$hostsManager = Wol_ManageHosts::getInstance();
			$hostsNumber = 0;
			$hostsOn = 0;
			foreach ($this->_hosts as $h) {
				$hostsNumber++;
				if (! $h->getStatus()) {
					$hostsOn++;
				}
			}		
			$return["hostsOn"] = $hostsOn;
			$return["hostsNumber"] = $hostsNumber;	
			$return['percent'] = ($hostsNumber === 0) ? 0 : 100.0 * $hostsOn / $hostsNumber;				
			if (  ! ( ($forUser !== NULL) && (! ($this->_view)) )  ) {  
			/* If we want to get the page for a custom user and have to hide hosts */
				$hostsManager = Wol_ManageHosts::getInstance();
				foreach ($this->_hosts as $h) {
					$return["hostsinfarm"][]= $onlyID ? $h->getID() : $h->getSerialized($forUser);
				}
			}
		}

		return $return;
	}
}

?>
