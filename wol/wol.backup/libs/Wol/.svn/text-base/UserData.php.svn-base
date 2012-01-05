<?php

/**
 * Stores the information of a user
 */
class Wol_UserData {

	protected $_id = 0;
	protected $_login = null;
	protected $_email = null;
	protected $_registration_date=null;
	protected $_role;
	protected $_hosts = array();

	/**
	 * Creates a UserData object
	 * @param int $id user id
	 * @param string $login user login
	 * @param string $email user email address
	 * @param string $registration_date user registration date	 
	 * @param string $role user's role : 'user' (default) or 'admin'
	 */
	public function __construct($id, $login, $email, $registration_date, $role='user') {
	    $this->_id = $id;
	    $this->_login = $login;
	    $this->_email = $email;
	    $this->_registration_date = $registration_date;
	    $this->_role = $role;
	}

	/**
	 * Returns the user id
	 * @return int the id
	 */
	public function getId() { 
	    return $this->_id; 
	}

	/**
	 * Returns the user login
	 * @return string the login
	 */
	public function getLogin() { 
	    return $this->_login; 
	}

	/**
	 * Returns the user's email
	 * @return string the email
	 */
	public function getEmail() { 
	    return $this->_email; 
	}

	/**
	 * Returns the user's registration date
	 * @return string the id
	 */
	public function getRegistrationDate() { 
	    return $this->_registration_date; 
	}

	/**
	 * Returns the user's role
	 * @return string the role ('user' or 'admin')
	 */
	public function getRole() {
	    return $this->_role;
	}
	
	/**
	 * Returns the hosts associated with the user
	 * @return array List of the Wol_Host
	 */	
	public function getHosts() {
		return $this->_hosts;
	}

	/**
	 * Changes the user's email
	 * @param string $newEmail the new email
	 */
	public function setEmail($newEmail) { 
	    $this->_email = $newEmail; 
	}

	/**
	 * Changes the user's role
	 * @param string $newRole the new role
	 */
	public function setRole($newRole) {
	    $this->_role = $newRole;
	}

	/**
	 * Adds a new relationship host-user
	 * @param Wol_Host $host the new host
	 */	
	public function addHost($host) {
		$this->_hosts[]=$host;
	}
	
	/**
	 * Removes an host from the user
	 * @param int $hostID the id of host to remove from user
	 */	
	public function removeHost ($hostID) {
		for ($i=0;$i<count($this->_hosts);$i++) {
			if ($this->_hosts[$i]->getID() === $hostID) {
				unset($this->_hosts[$i]);
			}
		}
	}

	/**
	 * Returns some information about this user
	 * @param bool $withHosts if true, puts for each user a list of hosts. if not specified or false, only returns user data.
	 * @param bool $onlyHostID if true, only returns the list of host ids. if not specified or false, returns all the information about each host
	 * @return array the serialized data. Contains : id, login, email, regDate, role (+ hosts)
	 */	   	
	public function getSerialized ($withHosts=false,  $onlyHostID=false) {
		$return = array(
			"id" => $this->_id,
			"login" => $this->_login,
			"email" => $this->_email,
			"regDate" => $this->_registration_date,
	    	"role" => $this->_role
		);
		if ($withHosts) {
			$return['hosts'] = array();
			foreach ($this->_hosts as $h) {
				$return['hosts'][] = $onlyHostID ? $h->getID() : $h->getSerialized();	
			}
		}
		return $return;
	}

}
?>
