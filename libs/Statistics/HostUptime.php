<?php

/**
 * Stores all the statistics related to an host.
 */
class Statistics_HostUptime {
	
	protected $_hostID = false;
	protected $_weeks = array();
	
	/**
	 * Creates a HostUptime object
	 * @param int $hostID the id of the host
	 */
	public function __construct($hostID) {
		$this->_hostID = $hostID;
	}
	
	/**
	 * gets the object's host ID
	 * @return int the host id
	 */
	public function getHostID () {
   	return $this->_hostID;	
   }
   
   /**
	 * Adds a new week to the host
	 * @param Statistics_HostUptimeWeek $week the week to add
	 */  
   public function addWeek ($week) {
   	$this->_weeks[] = $week;
   }
 
 	/**
	 * Returns the week with the given weekNumber
	 * @param int $nb the number of the week
	 * @return Statistics_HostUptimeWeek the week
	 */  
	protected function getWeekByNumber ($nb) {
		foreach ($this->_weeks as $week) {
			if ($week->getNumberOfWeek() == $nb) {
				return $week;	
			}
		}
		return false;
	}

 	/**
	 * Returns the current week
	 * @return Statistics_HostUptimeWeek the current week
	 */  	
	protected function getCurrentWeek() {
		return $this->getWeekByNumber(0);
	}
	
 	/**
	 * Returns the today's uptime
	 * @return int today's uptime (in minutes)
	 */  
	public function getToday() {
		if ( ($currWeek = $this->getCurrentWeek()) !== false ) {
			return $currWeek->getDayFromNumber(date("N"));
		}
	}
	
 	/**
	 * Adds the value to today's uptime
	 * @param int $time the time to add (given in minutes)
	 */  	
	public function addToday ($time) {
		if ( ($currWeek = $this->getCurrentWeek()) !== false ) {
			return $currWeek->addToday($time);
		}			
	}
	
 	/**
	 * Returns an array 
	 * with the uptimes of the given day of the given week (in hours and minutes)
	 * and the name and id of the host
	 * or false if it is not in the model.
	 * @param int $week
	 * @param int $day
	 * @return array|bool an array (with indexes : hostid, hostname, hours, minutes) or false if the requested data does not exist 
	 */     
	public function getUptime($week, $day) {
		if ( ($week = $this->getWeekByNumber($week)) !== false) {
			$hostsManager = Wol_ManageHosts::getInstance();
			return array(
				'hostid' => $this->_hostID,
				'hostname' => $hostsManager->getByID($this->_hostID)->getName(), 
				'hours' => Statistics_HostUptimeWeek::toHours($week->getDayFromNumber($day)),
				'minutes' => Statistics_HostUptimeWeek::toMinutes($week->getDayFromNumber($day))
			);
		}
	}
   
	/**
	 * Returns an array with the data contained in the object
	 * @return array array with indexes : host ID, hostname and weeks (containing serialized Statistics_HostUptimeWeek)
	 */  
	public function getSerialized () {
		$hostsManager = Wol_ManageHosts::getInstance();
		$return = array(
			'hostid' => $this->_hostID,
			'hostname' => $hostsManager->getByID($this->_hostID)->getName(),
			'weeks' => array(),
		);
		
		foreach ($this->_weeks as $week) {
			$return['weeks'][] = $week->getSerialized();
		}
		return $return;
	}
 
}