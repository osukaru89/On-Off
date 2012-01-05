<?php

/**
 * Manages the data about host groups stored in the model and in the database
 */
class Wol_ManageHostGroups {

	protected $_hostsGroups = array();
	static private $_instance;

	/**
	 * Queries the database and constructs the model
	 */
	private function __construct() {
		$query = 'SELECT HG.id_hostgroup ID, HG.name NAME, HG.level LEVEL FROM php_wol_hostgroups HG;';
		$list = mysql_query ($query);

		$hostsManager = Wol_ManageHosts::getInstance();
		
	   while ( $rowList = mysql_fetch_array($list) ) {
			$newGroup = new Wol_HostGroup ($rowList['ID'], $rowList['NAME'], $rowList['LEVEL'] );
			
			$queryHosts = sprintf (
				'SELECT HHG.id_host HOSTID FROM php_wol_hosts_hostgroups HHG WHERE HHG.id_hostgroup = %s;', 
				$rowList['ID']
			);
			$listHosts = mysql_query ($queryHosts);
			
			while ( $rowListHosts = mysql_fetch_array($listHosts) ) {
				$newGroup->addHost($hostsManager->getByID($rowListHosts['HOSTID']));
			}
			
			$this->_hostsGroups[] = $newGroup;
		}
	}

	/**
	 * Returns the unique object of the class
	 * (and creates it if it does not exist)
	 * @static
	 * @return Wol_ManageHostGroups the unique instance of the class
	 */	
	static public function getInstance() {
		if ( (!isset(self::$_instance)) || (self::$_instance == NULL) ) {
			self::$_instance = new Wol_ManageHostGroups();
		}
		return self::$_instance;
	}

	/**
	 * Searches a group in this class' list
	 * @param int $id the id of the group we want to get
	 * @return Wol_HostGroup|bool the group with the given id (or false if it does not exist)
	 */
	public function getByID ($id) {
		foreach ( $this->_hostsGroups as $currentGroup) {
			if ($currentGroup->getId() === $id) {
				return $currentGroup;
			}
		}
		return false;
	}

	/**
	 * Returns a list with all the existing host groups
	 * @return array the list of groups
	 */	
	public function getHostGroups () {
		return $this->_hostsGroups;
	}	
	
	/**
	 * Searches a group in this class' list and deletes it
	 * @param int $id the id of the group we want to delete
	 */
	protected function removeHostGroupFromList ($id) {
		for ($i=0;$i<count($this->_hostsGroups);$i++) {
			if ($this->_hostsGroups[$i]->getID() === $id) {
				unset($this->_hostsGroups[$i]);
			}
		}
	}

	/**
	 * Adds a group to this class' list of groups
	 * @param Wol_HostGroup $newHostGroup the new group
	 */
	protected function addHostGroupToList ($newHostGroup) {
		$this->_hostsGroups[] = $newHostGroup;
	}
	
	/**
	 * Deletes the group from the database and from the model
	 * @param int $id the id of the group to delete
	 * @return bool true if the group has been deleted from the database, else false
	 */
	public function deleteHostGroup ($id) {
		$query = sprintf("DELETE FROM php_wol_hostgroups WHERE id_hostgroup = %s;", $id);
		$result = mysql_query ($query);
		if ($result) {
			$this->removeHostGroupFromList($id);
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
	public function createHostGroup ($name, $level) {
		$query = sprintf("INSERT INTO php_wol_hostgroups (name, level) VALUES ('%s', %s);", $name, $level);
		$result = mysql_query ($query);    
		if ($result !== false) {
			$queryID = sprintf("SELECT HG.name, HG.id_hostgroup IDHG FROM php_wol_hostgroups HG WHERE HG.name = '%s';", $name);
   		$resultID = mysql_query ($queryID);
   		$rowID = mysql_fetch_array($resultID);
			
			$newGroup = new Wol_HostGroup($rowID['IDHG'], $name, $level);

			$this->addHostGroupToList($newGroup);
			return $rowID['IDHG'];
		}
	}
	
	/**
	 * Changes the access level of an host group
	 * @param int $id the id of the group
	 * @param string $newLevel the new value of level
	 * @return bool true on success, false on failure
	 */	
	public function changeLevel ($id, $newLevel) {
		$query = sprintf ('UPDATE php_wol_hostgroups SET level = "%s" WHERE id_hostgroup = "%s"', $newLevel, $id);
		$result = mysql_query ($query);
		if (($grp = $this->getByID($id)) !== false) $grp->setLevel($newLevel);
		return ($result !== false);
	}

	/**
	 * Adds an host to an host group in the database and in the model
	 * @param int $idGroup the id of the group
	 * @param int $idHost the id of the host
	 * @return bool true if the relationship has been added in the database, else false
	 */	
	public function addHostToGroup ($idGroup, $idHost) {
		$query = sprintf ('INSERT INTO php_wol_hosts_hostgroups (id_host, id_hostgroup) VALUES (%s, %s);', $idHost, $idGroup);
		$result = mysql_query ($query);
		$hostsManager = Wol_ManageHosts::getInstance();
		if (($grp = $this->getByID($idGroup)) !== false) $grp->addHost($hostsManager->getByID($idHost));
		return ($result !== false);
	}

	/**
	 * Removes an host from an host group in the database and in the model
	 * @param int $idGroup the id of the group
	 * @param int $idHost the id of the host
	 * @return bool true if the relationship has been deleted in the database, else false
	 */		
	public function removeHostFromGroup ($idGroup, $idHost) {
		$query = sprintf ('DELETE FROM php_wol_hosts_hostgroups WHERE id_host = %s AND id_hostgroup = %s;', $idHost, $idGroup);
		$result = mysql_query ($query);
		
		$hostsManager = Wol_ManageHosts::getInstance();
		if (($grp = $this->getByID($idGroup)) !== false) $grp->removeHost($hostsManager->getByID($idHost));
		return ($result !== false);
	}	

	/**
	 * Serializes all host groups
	 * @param bool $onlyHostID if true, only the id of hosts will be return (default false : all hosts data are returned)
	 * @return array A list of groups with the required data
	 */
	public function getSerialized ($onlyHostID = false) {
		$list2return = array();
		foreach ( $this->_hostsGroups as $currentGroup) {
			$list2return[] = $currentGroup->getSerialized($onlyHostID);
		}
		return $list2return;
	}


}
?>
