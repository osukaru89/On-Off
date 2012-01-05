<?php

/**
 * Stores all the information about an invitation
 */
class Invitation_Invit {
	
	protected $_id = false;
	protected $_peopleName = false;
	protected $_projectName = false;
	protected $_hash = false; 
	protected $_email = false;
	protected $_toPower = false;
	protected $_begin = false;
	protected $_end = false;
	
	/**
	 * Creates an Invit object
	 * @param int $id the id of the invitation
	 * @param string $people people's name
	 * @param string $project project's name
	 * @param string $hash the generated hash that will be used by the person to authenticate
	 * @param string $email the email address where the email has been sent
	 * @param Wol_Host|Wol_Farm $toPower the host or farm that can be powered on with the hash
	 * @param string $begin the invitation's beginning date
	 * @param string $end the invitation's end date
	 */
	public function __construct($id, $people, $project, $hash, $email, $toPower, $begin, $end) { 
		$this->_id = $id;
		$this->_peopleName = $people;
		$this->_projectName = $project;
		$this->_hash = $hash;
		$this->_email = $email; 
		$this->_toPower = $toPower;
		$this->_begin = $begin;
		$this->_end = $end;
	}

	/**
	 * Returns the hash
	 * @return string the hash
	 */	
	public function getHash() {
		return $this->_hash;	
	}

	/**
	 * Returns the people name
	 * @return string the people name
	 */	
	public function getPeopleName() {
		return $this->_peopleName;	
	}

	/**
	 * Returns the project name
	 * @return string the project name
	 */	
	public function getProjectName() {
		return $this->_projectName;	
	}
	
	/**
	 * Returns the beginning date
	 * @return string the beginning date
	 */		
	public function getBegin() {
		return $this->_begin;			
	}

	/**
	 * Returns the end date
	 * @return string the end date
	 */	
	public function getEnd() {
		return $this->_end;			
	}

	/**
	 * Returns the host or farm to power on
	 * @return Wol_Host|Wol_Farm the host or farm to power on
	 */		
	public function getToPower() {
		return $this->_toPower;			
	}	

	/**
	 * Returns an array with the serialized object
	 * @return array serialized object containing : id, peopleName, projectName, hash, email, nameSubj, typeSubj, begin, end
	 */			
	public function getSerialized() {
		return array(
			"id" => $this->_id,
			"peopleName" => $this->_peopleName,
			"projectName" => $this->_projectName,
			"hash" => $this->_hash,
			"email" => $this->_email,
			"nameSubj" => $this->_toPower->getName(),
			"typeSubj" => ($this->_toPower instanceof Wol_Farm) ? 'farm' : ( ( $this->_toPower instanceof Wol_Host ) ? 'host' : NULL ),
			"begin" => $this->_begin,
			"end" => $this->_end
		);	
	}
	
}


?>
