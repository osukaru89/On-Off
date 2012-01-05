<?php

/**
 * Stores the data contained in the database about logs
 */
class Wol_Log_Log {

	protected $_id = false;
	protected $_eventName = false;
	protected $_eventID = false;	
	protected $_info = false;
	protected $_date = false;
	protected $_authorID = false;
	protected $_authorLogin = false;
	protected $_success = false;	

	/**
	 * Creates a log object
	 * @param int $id id of log
 	 * @param string $evName name of the event
	 * @param int $evID id of the event
	 * @param string $info information about what happened
	 * @param string $date date of the event
	 * @param int $authorID id of the author of this action
	 * @param string $authorLogin login of the author
	 * @param bool $success status returned by the action
	 */  	
	public function __construct($id, $evName, $evID, $info, $date, $authorID, $authorLogin, $success) {
		$this->_id = $id;
		$this->_eventName = $evName;
		$this->_eventID = $evID;
		$this->_info = $info;
		$this->_date = $date;
		$this->_authorID = $authorID;
		$this->_authorLogin = $authorLogin;
		$this->_success = $success;
	}

	/**
	 * Returns the id of this log
	 * @return int id
	 */  		
	public function getID () {
		return $this->_id;
	}

	/**
	 * Returns the event name
	 * @return string event name
	 */  		
	public function getEventName () {
		return $this->_eventName;
	}

	/**
	 * Returns the event id
	 * @return int event id
	 */  	
	public function getEventID () {
		return $this->_eventID;
	}

	/**
	 * Returns the information
	 * @return string a short sentence
	 */  		
	public function getInfo () {
		return $this->_info;
	}

	/**
	 * Returns the date
	 * @return string date
	 */  		
	public function getDate () {
		return $this->_date;
	}

	/**
	 * Returns the id of the author
	 * @return int author id
	 */  		
	public function getAuthorID () {
		return $this->_authorID;
	}

	/**
	 * Returns the login of the author
	 * @return string author login
	 */  		
	public function getAuthorLogin () {
		return $this->_authorLogin;
	}

	/**
	 * Returns the status of the action
	 * @return bool generally, true if successful and false if something failed
	 */  		
	public function getSuccess () {
		return $this->_success;
	}

	/**
	 * Serializes all the logs in the model
	 * @return array A list of logs (content : id, eventName, eventID, info, date, authorID, authorLogin, success)
	 */		
	public function getSerialized() {
		return array (
			'id' => $this->_id,
			'eventName' => $this->_eventName,
			'eventID' => $this->_eventID,			
			'info' => $this->_info,
			'date' => $this->_date,
			'authorID' => $this->_authorID,
			'authorLogin' => $this->_authorLogin,
			'success' => $this->_success
		);	
	}

}
?>
