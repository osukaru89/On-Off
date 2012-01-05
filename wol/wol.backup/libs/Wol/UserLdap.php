<?php

/**
 * Stores the information of a user imported from LDAP base
 */
class Wol_UserLdap extends Wol_UserData {
	
	protected $_uid = false;
	
	/**
	 * Creates a UserLdap object
	 * @param string $uid user's id in LDAP database
	 * @param int $id user id
	 * @param string $login user login
	 * @param string $email user email address
	 * @param string $registration_date user registration date	 
	 * @param string $role user's role : 'user' (default) or 'admin'
	 */

	public function __construct($uid, $id, $login, $email, $registration_date, $role='user') {
	    $this->_id = $id;
	    $this->_login = $login;
	    $this->_email = $email;
	    $this->_registration_date = $registration_date;
	    $this->_role = $role;
	    $this->_uid = $uid;
	}

	/**
	 * Returns the user's LDAP id
	 * @return int the id
	 */	
	public function getLdapUID () {
		return $this->_uid;
	}

}
?>
