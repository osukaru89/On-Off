<?php

/**
 * Manages the logs in the database and in the model
 */
class Wol_Log_Logger {
	
	static private $_instance;
	protected $_allLogs;
	
	private function __construct() { }

	/**
	 * Returns the unique object of the class
	 * (and creates it if it does not exist)
	 * @static
	 * @return Wol_Log_Logger the unique instance of the class
	 */
	static public function getInstance() {
		if ( (!isset(self::$_instance)) || (self::$_instance == NULL) ) {
			self::$_instance = new Wol_Log_Logger();
		}
		return self::$_instance;
	}

	/**
	 * Adds a log in the database with the id of event
	 * @param int $idEvent the id of event
	 * @param int $idAuthor the id of the author
	 * @param string $info the short sentence to describe the action
	 * @param bool $success the status of the action
	 * @return bool if the log has been added in the database, false if query failed
	 */	
	protected function addLogByID ($idEvent, $idAuthor, $info, $success) {
		$query = sprintf("INSERT INTO php_wol_logger (id_event, id_author, info_event, success) VALUES (%s, %s, '%s', '%s');",
			$idEvent, ($idAuthor === NULL) ? 'NULL' : $idAuthor, $info, $success ? 'true' : 'false'
		);
		$result = mysql_query ($query);
		if ($result) {
			return true;
		} else {
			return false;
		}	
	}

	/**
	 * Gets the event id from the event name
	 * @param int $name the event name
	 * @return int|bool the event id (or false if query failed)
	 */		
	protected function getEventByName($name) {
		$query = sprintf('SELECT E.id_event ID, E.name_event EVENT FROM php_wol_events E WHERE E.name_event = "%s";', $name);
		$result = mysql_query ($query);
		if ($result == false) {
			return false;	
		} else {
			$row = mysql_fetch_array($result);
			return $row['ID'];
		}
	}

	/**
	 * Gets the list of all the events with their names and ids
	 * @return array the list of events. For each event, contains : id, name
	 */		
	public function getEventNamesAndID() {
		$return = array();		
		$query = 'SELECT id_event ID, name_event EVENT FROM php_wol_events;';
		$result = mysql_query ($query);
		while ($row = mysql_fetch_array($result)) {
			$return[] = array(
				"id" => $row['ID'],
				"name" => $row['EVENT']
			);
		}
		return $return;
	}

	/**
	 * Adds a log in the database
	 * @param int $eventName the name of event
	 * @param int $idAuthor the id of the author
	 * @param string $info the short sentence to describe the action
	 * @param bool $success the status of the action
	 * @return bool if the log has been added in the database, false if query failed
	 */	
	public function addLog($eventName, $idAuthor, $info, $success) {
		$eventID = $this->getEventByName($eventName);
		if ($eventID == false) {
			return false;			
		} else {
			return $this->addLogByID ($eventID, $idAuthor, $info, $success);
		}
	}

	/**
	 * Adds a log about some task performed by Cron
	 * @param string $info the short sentence to describe the action
	 * @param bool $success the status of the action
	 * @return bool if the log has been added in the database, false if query failed
	 */		
	public function addCronLog($info, $success) {
		$eventID = $this->getEventByName("cronAction");
		if ($eventID == false) {
			return false;			
		} else {
			return $this->addLogByID ($eventID, NULL, $info, $success);
		}
	}

	/**
	 * Adds a log in the database about an action performed by an unknown person
	 * @param int $eventName the name of event
	 * @param string $info the short sentence to describe the action
	 * @param bool $success the status of the action
	 * @return bool if the log has been added in the database, false if query failed
	 */	
	public function addAnonymousLog($action, $info, $success) {
		$eventID = $this->getEventByName($action);
		if ($eventID == false) {
			return false;			
		} else {
			return $this->addLogByID ($eventID, NULL, $info, $success);
		}
	}
	
	
	/**
	 * Returns a list of serialized logs from the database, with some custom search parameters
	 * @param int|bool $idUser the id of the user we want to get the actions. Set to false to get all users
	 * @param int|bool $idEvent the id of the type of event we want to get. Set to false to get all events
	 * @param string|bool $date the date of the event. Set to false to get all events. Other values : 'hour', 'today', 'week', 'month', 'year'
	 * @return array the list of serialized logs that matches the given parameters
	 */		
	public function getSerializedLogs($idUser, $idEvent, $date) {
		
		if ($date !== false) {
			switch($date) {
				case 'hour' :	
					$dateCase = '1 HOUR';
				break;	
				case 'today' :	
					$dateCase = '1 DAY';				
				break;		
				case 'week' :	
					$dateCase = '1 WEEK';				
				break;		
				case 'month' :	
					$dateCase = '1 MONTH';				
				break;		
				case 'year' :	
					$dateCase = '1 YEAR';				
				break;			
			}
		}
		
		$query = 'SELECT L.id_log ID, L.id_event IDEVENT, L.date_event DAT, L.id_author AUTHID, L.info_event INFO, L.success SUC, 
			L.id_author AUTHID, E.id_event, E.name_event EVENT
			FROM php_wol_logger L, php_wol_events E
			WHERE L.id_event = E.id_event ' .
			(($idUser !== false) ? 'AND L.id_author =' . $idUser . ' ' : '' ) .
			(($idEvent !== false) ? 'AND L.id_event =' . $idEvent . ' ' : '' ) .
			(($date !== false) ? 'AND L.date_event >= date_sub(now(), INTERVAL ' . $dateCase . ') ' : '' ) .			
			'ORDER BY L.id_log;' ;
		$listLogs = mysql_query ($query);

		$usersManager = Wol_ManageUsers::getInstance();
		while ($rowListLogs = mysql_fetch_array($listLogs)) {
			$newLog = new Wol_Log_Log($rowListLogs['ID'],$rowListLogs['EVENT'],$rowListLogs['IDEVENT'],$rowListLogs['INFO'],
										$rowListLogs['DAT'],$rowListLogs['AUTHID'], (($rowListLogs['AUTHID']!== NULL) ? $usersManager->getByID($rowListLogs['AUTHID'])->getLogin() : NULL), $rowListLogs['SUC']);
			$this->_allLogs[] = $newLog;
		}		

		$logs2return = array();		
		foreach ($this->_allLogs as $log) {
			$logs2return[] = $log->getSerialized();
		}
		return $logs2return;
	}	
}
?>
