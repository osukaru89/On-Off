<?php

/**
 * Manages the invitations
 */
class Invitation_InvitManager {
	
	static private $_instance ;
	static private $_invitations = array() ;	
	
	
	/**
	 * Creates an InvitManager object
	 * Searches in the database all the informations on sent invitations and constructs a model
	 */
	private function __construct() { 
		$query = "SELECT I.id_invitation ID, I.name_of_people PEOPLENAME, I.email EMAIL, I.name_of_project PROJNAME, I.hash HASH, I.typeOfSubject SUBJTYPE, 
		I.id_subject SUBJID, I.date_begin BEG, I.date_end END FROM php_wol_invitations I;";
		$list = mysql_query ($query);
		while ( $row = mysql_fetch_array($list) )  {
			switch ($row['SUBJTYPE']) {
				case 'host' : 
					$goodManager = Wol_ManageHosts::getInstance();
				break;
				case 'farm' :
					$goodManager = Wol_ManageFarms::getInstance();
				break;
				default: 
					$goodManager = NULL;
				break;
			}
			$toTurnOn = $goodManager->getByID($row['SUBJID']);
			if ( $toTurnOn !== false) {
				self::$_invitations[] = new Invitation_Invit($row['ID'], $row['PEOPLENAME'], $row['PROJNAME'], $row['HASH'], $row['EMAIL'], $toTurnOn, $row['BEG'], $row['END']);
			}
		}
	}

	/**
	 * Returns the unique instance of the InvitManager class
	 * (and creates it if it does not exist)
	 * @static
	 * @return Invitation_InvitManager the instance
	 */
	static public function getInstance() {
		if ( (!isset(self::$_instance)) || (self::$_instance == NULL) ) {
			self::$_instance = new Invitation_InvitManager();
		}
		return self::$_instance;
	}
	
	/**
	 * Adds a new invitation (on the database ONLY) for a defined period of time
	 * @param string $peopleName the name of the people we send the invitation
	 * @param string $projectName the project name
	 * @param string $typeOfSubject the type of thing the invitation allows to power on (value : 'host' or 'farm')
	 * @param int $idSubject the id of the thing
	 * @param string $email the email address of the people to send the invitation
	 * @param string $hash the hash that will allow to power on the host
	 * @param int $beginDay the invitation's day of beginning (from 1 to 31)
	 * @param int $beginMonth the invitation's day of beginning (from 1 to 12)
	 * @param int $beginYear the invitation's year of beginning (from 1970 to ??)
	 * @param int $endDay the invitation's day of ending (from 1 to 31)
	 * @param int $endMonth the invitation's day of ending (from 1 to 12)
	 * @param int $endYear the invitation's year of ending (from 1970 to ??)
	 * @return bool true on success, false on failure
	 */
	public function addInvitation ($peopleName, $projectName, $typeOfSubject, $idSubject, $email, $hash, $beginDay, $beginMonth, $beginYear, $endDay, $endMonth, $endYear) {
		$query = sprintf("INSERT INTO php_wol_invitations (name_of_people, name_of_project, typeOfSubject, id_subject, email, hash, date_begin, date_end) 
		VALUES ('%s', '%s', '%s', %s, '%s', '%s', '%s-%s-%s', '%s-%s-%s');", 
		$peopleName, $projectName, $typeOfSubject, $idSubject, $email, $hash, $beginYear, $beginMonth, $beginDay, $endYear, $endMonth, $endDay);
		$result = mysql_query ($query);
		return ($result !== false);
	}

	/**
	 * Adds a new invitation (on the database ONLY) from now to the end date
	 * @param string $peopleName the name of the people we send the invitation
	 * @param string $projectName the project name
	 * @param string $typeOfSubject the type of thing the invitation allows to power on (value : 'host' or 'farm')
	 * @param int $idSubject the id of the thing
	 * @param string $email the email address of the people to send the invitation
	 * @param string $hash the hash that will allow to power on the host
	 * @param int $endDay the invitation's day of ending (from 1 to 31)
	 * @param int $endMonth the invitation's day of ending (from 1 to 12)
	 * @param int $endYear the invitation's year of ending (from 1970 to ??)
	 * @return bool true on success, false on failure
	 */
	public function addInvitationFromNow ($peopleName, $projectName, $typeOfSubject, $idSubject, $email, $hash, $endDay, $endMonth, $endYear) {
		$query = sprintf("INSERT INTO php_wol_invitations (name_of_people, name_of_project, typeOfSubject, id_subject, email, hash, date_begin, date_end) 
		VALUES ('%s', '%s', '%s', %s, '%s', '%s', CURDATE(), '%s-%s-%s');", 
		$peopleName, $projectName, $typeOfSubject, $idSubject, $email, $hash, $endYear, $endMonth, $endDay);
		$result = mysql_query ($query);
		return ($result !== false);	
	}

	/**
	 * Deletes an invitation (from the database only !)
	 * @param int $id the id of the invitation to be deleted
	 * @return bool true on success, false on failure
	 */			
	public function deleteInvitation($id) {
		$query = sprintf("DELETE FROM php_wol_invitations WHERE id_invitation = %s;", $id);
		$result = mysql_query ($query);
		return ($result !== false);
	}

	/**
	 * Returns the invitation that has the given hash, if it exists
	 * @param string $hash the hash of the searched invitation
	 * @return Invitation_Invit|bool the invitation on success, false on failure
	 */
	public function getByHash($hash) {
		foreach (self::$_invitations as $invit) {
			if ($invit->getHash() === $hash) return $invit;
		}
		return false;
	}

	/**
	 * Searches in the database to check if the given hash is a valid one
	 * @param string $hash the hash provided by the extern user
	 * @return bool true if valid, else false
	 */
	public function isValidRequest ($hash) {
		if ( ($invit = $this->getByHash($hash)) !== false ) {
			if ( (strtotime($invit->getBegin()) < time()) && (strtotime($invit->getEnd()) > time()) ) {
				return true;
			}
		}
		return false;
	}

	/**
	 * Returns all the serialized invitations' data stored in the class
	 * @return array an array of serialized invitations (see the Invitation_Invit for more information)
	 */		
	public function getSerialized () {
		$return = array();
		foreach (self::$_invitations as $invit) {
			$return[] = $invit->getSerialized();	
		}	
		return $return;
	}
}


?>
