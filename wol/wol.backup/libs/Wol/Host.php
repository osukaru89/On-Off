<?php

/**
 * Stores the information of an host
 */
class Wol_Host {

	protected $_id = false;
	protected $_mac = false;
	protected $_hostName = false;
	protected $_inetAddr = false;
	protected $_owner = false;
	protected $_ownerName = false;
	protected $_wolAccess = false;
	protected $_status = false;

	public function __construct() { }

	/**
	 * Returns the host id
	 * @return int the id
	 */
	public function getID() {
		return $this->_id;
	}

	/**
	 * Returns the host's mac
	 * @return int the mac address
	 */
	public function getMac() {
		return $this->_mac;
	}

	public function getFormattedMac() {
		return wordwrap($this->_mac, 2, ":", 1);
	}	

		
	/**
	 * Returns the host's IP
	 * @return string the IP
	 */	
	public function getInetAddr() {
 		return $this->_inetAddr;
	}

	/**
	 * Returns the hostName
	 * @return string the hostName
	 */
	public function getHostName() {
		return $this->_hostName;
	}

	/**
	 * Alias for getHostName()
	 * @return string the hostName
	 */
	public function getName() {
		return $this->_hostName;
	}

	/**
	 * Returns the host's owner id
	 * @return string the id
	 */	
	public function getOwnerID () {
		return $this->_owner;
	}

	/**
	 * Returns the host's owner name
	 * @return string the name
	 */
	public function getOwnerName () {
		return $this->_ownerName;
	}

	/**
	 * Returns the status of the host
	 * @return int 0 if online, 1 if outline
	 */
	public function getStatus () {
		return $this->_status;
	}
	
	/**
	 * Sets the host id
	 * @param int $id the new id
	 */
	public function setID($newID) {
		$this->_id = $newID;
	}

	/**
	 * Sets the host mac address
	 * @param string $mac the new mac
	 */
	public function setMac($mac) {
		$this->_mac = $mac;
	}

	/**
	 * Sets the host IP address
	 * @param string $inetAddr the new IP
	 */
	public function setInetAddr($inetAddr) {
		$this->_inetAddr = $inetAddr;
	}

	/**
	 * Sets the host name
	 * @param string $hostName the new name
	 */
	public function setHostName($hostName) {
		$this->_hostName = $hostName;
	}

	/**
	 * Sets the host owner id
	 * @param int $newOwnerID the new owner id
	 */
	public function setOwnerID ($newOwnerID) {
		$this->_owner = $newOwnerID;
	}

	/**
	 * Sets the host's owner's name
	 * @param string $newOwnerName the new name
	 */
	public function setOwnerName ($newOwnerName) {
		$this->_ownerName = $newOwnerName;
	}

	/**
	 * Sets the status
	 * @param int $hostName the new status (0 => online, 1 => offline)
	 */	
	public function setStatus ($status) {
		$this->_status = $status;
	}
	
	/**
	 * Returns the custom name given to this host for a special user
	 * @param int $userID the user id
	 * @return string the custom name (or NULL if it does not exist)
	 */

	public function getNameForUser($userID) {
		$query = sprintf("	SELECT UH.id_host H, UH.id_user, UH.hostname_for_user HNFU
				FROM php_wol_users_hosts UH
				WHERE UH.id_host='%s'
				AND UH.id_user='%s';", $this->_id, $userID);
		$result = mysql_query ($query);
		$rowName = mysql_fetch_array($result);
		return $rowName['HNFU'];
	}

	/**
	 * Asks to wake on this host
	 */
	
	public function wakeOnLan () {
		Wol_Tools_Wakeonlan::wakeOnLan($this->_mac, 7);
		Wol_Tools_Wakeonlan::wakeOnLan($this->_mac, 9);
	}

	/**
	 * Asks to turn off this host (libssh2-php)
	 * Done.
	 * Si sobra tiempo, plantear la opción de hacer una clase SHH, para modularizar y encapsular la conexión y autenticación mediante SHH
	 */	

	public function turnOff () {

		if($ssh = ssh2_connect($this->_inetAddr, 22)){

		$key_path = "/var/www/.ssh/id_dsa"; 
		$passphrase = "";

		if(ssh2_auth_pubkey_file($ssh, 'root' , $key_path.'.pub', $key_path, $passphrase)){

			$copy_script = ssh2_scp_send($ssh, "shutdown.sh", "/root/shutdown.sh", 0777);
			//$export_x = ssh2_exec($ssh, 'export DISPLAY=:0.0'); Doing this int shutdown.sh
			$command = ssh2_exec($ssh, 'sh /root/shutdown.sh');

		} else {
			die("Can't connect [ $this->_inetAddr '$ssh' ".strtolower($this->_hostName)); 
		   }

		/*$auth = ssh2_auth_password($ssh, 'root', 'pass');*/

		} else {
		    die("Error while connecting ".$this->_inetAddr."");
		  }

	}

	/**
	 * Returns some information about this host
	 * @param int $forUser when not null, searches the custom names given to hosts for the user with this id 
	 * @param bool $onlyNamesAndID if true, only returns host id and host name. if not specified or false, returns all the information
	 * @return array the serialized data. Contains : idH, nameH (+ mac, ip, ownerid, ownername, online)
	 */

	public function getSerialized ($forUser = NULL, $onlyNamesAndID=false) {
		if ($onlyNamesAndID) {
			return array("idH" => $this->_id, "nameH" => $this->_hostName);
		}
		$return = array(
			"idH" => $this->_id,
			"mac" => wordwrap($this->_mac, 2, ":", 1),
			"nameH" => ($forUser === NULL) ? $this->_hostName : ( ($this->getNameForUser($forUser) === NULL) ? ($this->_hostName) : ($this->getNameForUser($forUser)) ),
			"ip" => $this->_inetAddr,
	    	"ownerid" => $this->_owner,
	    	"ownername" => $this->_ownerName,
			"online" => $this->_status
	    );
	    return $return;
	}

}
?>
