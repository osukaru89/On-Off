<?php

/**
 * Manages the data of statistics stored in the model and in the database
 */
class Statistics_ManageStats {
	
	static private $_instance ;
	static private $_config;
	static private $_config_file = 'stats_config.cfg';
	protected $_hostsUptimes = array() ;
	
	/**
	 * Queries the database and constructs the php classes
	 */
	private function __construct() { 
		$query = "SELECT S.id_host IDHOST, S.week_number WN, S.Mon MON, S.Tue TUE, S.Wed WED, S.Thu THU, S.Fri FRI, S.Sat SAT, S.Sun SUN FROM php_wol_statistics S;";
		$list = mysql_query ($query);
		while ($row = mysql_fetch_array($list)) {	
			if ($this->getByHostID($row['IDHOST']) === false) {
				$this->_hostsUptimes[] = new Statistics_HostUptime($row['IDHOST']);
			}
			$week = new Statistics_HostUptimeWeek ($row['WN'], $row['MON'], $row['TUE'], $row['WED'], $row['THU'], $row['FRI'], $row['SAT'], $row['SUN']);
			$this->getByHostID($row['IDHOST'])->addWeek($week);
		}
		/* Loading config : */		
	}

	/**
	 * Returns the unique object of the class
	 * (and creates it if it does not exist)
	 * @static
	 * @return Statistics_ManageStats the unique instance of the class
	 */
	static public function getInstance() {
		if ( (!isset(self::$_instance)) || (self::$_instance == NULL) ) {
			self::$_instance = new Statistics_ManageStats();
		}
		return self::$_instance;
	}

	/**
	 * Loads the configuration from the file
	 */	
	public function loadConf($dirConf) {
		$confManager = Config_ManageConf::getInstance();
		self::$_config = $confManager->parseConfig($dirConf . self::$_config_file);	
	}
		
	/**
	 * Searches an host's statistics from its host id
	 * @param int $idHost the id of the host
	 * @return Statistics_HostUptime the uptime of the host who has the given id or false if it does not exist in the model
	 */
	public function getByHostID($idHost) {
		foreach ($this->_hostsUptimes as $hostUp) {
			if ($hostUp->getHostID() == $idHost) {
				return $hostUp;
			}
		}
		return false;
	}
	
	/**
	 * Called by Cron script if the host with the given id is online, every X minutes
	 * This function increments, in the model and in the database, the uptime of the current day.
	 * @param int $idHost the host id
	 * @param int $time the time to add
	 */
	public function updateHostOnline ($idHost, $time) {
		$hostUptime = $this->getByHostID($idHost);
		$dayOfWeek = date("D");
		$query = sprintf('UPDATE php_wol_statistics SET %s = %s WHERE id_host = "%s" AND week_number = 0', $dayOfWeek, $hostUptime->getToday() + $time, $idHost);
		$result = mysql_query ($query);
		if ($result !== false) {
			$hostUptime->addToday($time);
			return true;
		}
		return false;
	}
	
	/**
	 * Called by Cron script every week, Monday at 0:00
	 * Increments the weekNumber of every week in the database (and ONLY in the database)
	 * and then creates a new line (with weekNumber 0) for the week that has just begun
	 * @param int $weeks2keep the number of weeks to keep in the database. All the weeks with a number greater will be deleted. Default value = 0 : nothing is deleted.
	 */
	public function weekChange ($weeks2keep=0) {
		$queryNumbers = "SELECT week_number FROM php_wol_statistics GROUP BY week_number DESC;";
		$resultNumbers = mysql_query ($queryNumbers);
		while ( $rowNumbers = mysql_fetch_array($resultNumbers) )  {
			$query = sprintf("UPDATE php_wol_statistics SET week_number = %s WHERE week_number = %s", $rowNumbers['week_number'] + 1, $rowNumbers['week_number']);
			$result = mysql_query ($query);
		}

		$hostsManager = Wol_ManageHosts::getInstance();
		foreach ($hostsManager->getHosts() as $host) {
			$query = sprintf("INSERT INTO php_wol_statistics (id_host, week_number) VALUES (%s, 0)", $host->getID());	
			$result = mysql_query ($query);
		}
		if ($weeks2keep >= 0) {
			$queryDeleteOld = sprintf("DELETE FROM php_wol_statistics WHERE week_number > %s;", $weeks2keep);
			$result = mysql_query ($queryDeleteOld);
		}
	}
	
	/**
	 * Creates the current week's line for the given host (if it did not exist)
	 * Called by host manager at the creation of a new host
	 * @param int $hostID the host id
	 */
	public function addHostStats($hostID) {
		$query = sprintf("INSERT INTO php_wol_statistics (id_host, week_number) VALUES (%s, 0)", $hostID);	
		$result = mysql_query ($query);			
	}
	
	/**
	 * Returns an array with all the data contained in the model serialized (unused function !)
	 * @return array a list of all hosts, with all weeks and uptimes
	 */
	public function getSerialized () {
		$return = array();
		foreach ($this->_hostsUptimes as $hostUp) {
			$return[] = $hostUp->getSerialized();
		}
		return $return;
	}
	
	/**
	 * Returns the uptime for all hosts, for the given date
	 * @param int $day the day
	 * @param int $month the month
	 * @param int $year the year
	 * @return array the list of hosts (contains : the date and the list of serialized hosts' uptimes)
	 */
	public function getHostsOnDate($day, $month, $year) {
		$date = new DateTime ($year . "-" . $month . "-" . $day);
		$now = new DateTime ();
		$interval = date_diff($now, $date);
		$nbOfDays = $interval->format("%a");
		$nbOfDays = intval($nbOfDays);
		
		$weekDayDate = intval($date->format("N"));
		$weekDayNow = intval($now->format("N"));		
		$weekNumber = intval ( ($nbOfDays - $weekDayNow + $weekDayDate)/7 );
		
		$return = array(
			'date' => $date->format('l d F Y'),
			'hosts' => array()
		);
		
		foreach ($this->_hostsUptimes as $hostUp) {
			if ( ($hostForDate = $hostUp->getUptime($weekNumber, $weekDayDate)) !== false ) {
				$return['hosts'][] = $hostForDate;
			}
		}
		
		return $return;
	}
	
	/**
	 * Reads the statistics configuration file and returns contained parameters
	 * @param string $dirConf the absolute path of the directory where are configuration files
	 * @return array|bool the array of stats parameters, or false on failure
	 */			
	public function getStatConfig ($dirConf) {
		$confManager = Config_ManageConf::getInstance();
		return $confManager->parseConfig($dirConf . self::$_config_file);
	}

	/**
	 * Writes the configuration in the statistics config file from parameters stored in the array
	 * @param string $dirConf the absolute path of the directory where are configuration files
	 * @param array $params the array of parameters to write in the file
	 * @return array|bool the array of parameters, or false on failure
	 */			
	public function setStatConfig ($dirConf, $params) {
		$confManager = Config_ManageConf::getInstance();
		$confManager->setConfig($dirConf . self::$_config_file, $params);
	}

}

?>
