<?php

/**
 * Stores the information about a group of hosts
 */
class Wol_HostGroup {

  	protected $_id = false;
  	protected $_name = false;
	protected $_level = false;
	protected $_hosts = array();

	/**
	 * Creates a HostGroup object
	 * @param int $id id of the group
	 * @param string $name group name
	 * @param int $level permission level of the group (1 => god, 255 => nothing)
	 */
	public function __construct($id, $name, $level) {
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
	 * Returns the hosts contained in the group
	 * @return array List containing all the Wol_Host being part of the group
	 */
	public function getHosts () {
		return $this->_hosts;
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
		$this->_level = $newLevel;
	}

	/**
	 * Adds a new host in the group
	 * @param Wol_Host $host the new host
	 */
	public function addHost ($host) {
		$this->_hosts[] = $host;
		return true;
	}
	
	/**
	 * Adds a new host in the group
	 * @param Wol_Host $host the new host
	 */
	public function removeHost ($hostID) {
		for ($i=0;$i<count($this->_hosts);$i++) {
			if ($this->_hosts[$i]->getID() === $hostID){
				unset($this->_hosts[$i]);	
			}
		}
	}

	/**
	 * Asks to wake on each host contained in the group
	 */	
	public function wakeOnLan () {
		foreach ($this->_hosts as $h) {
			$h->wakeOnLan();
		}
	}

	/**
	 * Asks to turn off each host contained in the group
	 */		
	public function turnOff () {
		foreach ($this->_hosts as $h) {
			$h->turnOff();
		}
	}

	/**
	 * Returns some information about this group
	 * @param bool $onlyHostID if true, only returns the list of host ids. if not specified or false, returns all the information about each host
	 * @param int $forUser when not null, searches the custom names given to hosts for the user with this id
	 * @return array the serialized data. Contains : idHostGroup, nameHostGroup, nameHostGroup, hostsInGroup
	 */	   	
	public function getSerialized ($onlyHostID=false, $forUser=NULL) {
		$return = array(
			"idHostGroup" => $this->_id,
			"nameHostGroup" => $this->_name,
			"level" => $this->_level,
			"hostsInGroup" => array()
		);
		foreach ($this->_hosts as $host) {
			$return['hostsInGroup'][] = ($onlyHostID) ? $host->getID() : $host->getSerialized($forUser);
		}
		return $return;
	}

}
?>
