<?php

/**
 * Stores the data contained in the database about tasks
 */
class Wol_Scheduler_Task {
	
	protected $_id = false;
	protected $_minute = false;
	protected $_hour = false;
	protected $_dayOfMonth = false;
	protected $_month = false;
	protected $_dayOfWeek = false;
	protected $_idTask = false;
	protected $_taskName = false;
	protected $_typeOfSubject = false;
	protected $_idSubject = false;
  
   protected $_inDB = false;

	/**
	 * Creates a Task object with the informations about the type of action, the subject of action and the hour and date(s)
	 * @param int $id task id
	 * @param int $minute the minute the task must be run
	 * @param int $hour the hour the task must be run
	 * @param string $dayOfMonth the list of day(s) of Month (1..31)
	 * @param string $month the list of month ('jan', 'feb' ... 'dec')
	 * @param string $dayOfWeek the list of days of week ('mon', 'tue' ... 'sun')
	 * @param int $idTask the id of the task
	 * @param string $taskName the name of the task ('wakeon' or 'turnoff') corresponding to the id
	 * @param string $typeOfSubject the type of thing that must be waked on / turned off ('host', 'farm' or 'group')
	 * @param int $idSubject the id of the thing
	 */  
	public function __construct ($id, $minute, $hour, $dayOfMonth, $month, $dayOfWeek, $idTask, $taskName, $typeOfSubject, $idSubject) {
		$this->_id = $id;
		$this->_minute = $minute;
		$this->_hour = $hour;
		$this->_dayOfMonth = $dayOfMonth;
		$this->_month = $month;
		$this->_dayOfWeek = $dayOfWeek;
		$this->_idTask = $idTask;
		$this->_taskName = $taskName;
		$this->_typeOfSubject = $typeOfSubject ;
		$this->_idSubject = $idSubject;	
	}	

	/**
	 * Sets the id of the task
	 * @param int $id task id
	 */  	
	public function setID ($id) {
		$this->_id = $id;
	}
	
	/**
	 * Sets the minute
	 * @param int $id task id
	 * @param int $minute the minute the task must be run
	 */  
	public function setMinute ($minute) {
		$this->_minute = $minute;
	}

	/**
	 * Sets the hour
	 * @param int $hour the hour the task must be run
	 */  	
	public function setHour ($hour) {
		$this->_hour = $hour;
	}

	/**
	 * Sets the day(s) of month
	 * @param string $dayOfMonth the list of day(s) of Month (1..31)
	 */  	
	public function setDayOfMonth ($dayOfMonth) {
		$this->_dayOfMonth = $dayOfMonth;
	}

	/**
	 * Sets the month(s)
	 * @param string $month the list of month ('jan', 'feb' ... 'dec')
	 */  	
	public function setMonth ($month) {
		$this->_month = $month;
	}

	/**
	 * Sets the day(s) of week
	 * @param string $dayOfWeek the list of days of week ('mon', 'tue' ... 'sun')
	 */  	
	public function setDayOfWeek ($dayOfWeek) {
		$this->_dayOfWeek = $dayOfWeek;
	}

	/**
	 * Sets the id of task
	 * @param int $idTask the id of the task
	 */  	
	public function setIDTask ($idTask) {
		$this->_idTask = $idTask;
	}

	/**
	 * Sets the name of task
	 * @param string $taskName the name of the task ('wakeon' or 'turnoff') corresponding to the id
	 */  	
	public function setTaskName ($taskName) {
		$this->_taskName = $taskName;
	}

	/**
	 * Sets the type of subject
	 * @param string $typeOfSubject the type of thing that must be waked on / turned off ('host', 'farm' or 'group')
	 */  	
	public function setTypeOfSubject ($typeOfSubject) {
		$this->_typeOfSubject = $typeOfSubject ;
	}

	/**
	 * Sets the id of subject
	 * @param int $idSubject the id of the thing
	 */  	
	public function setIDSubject ($idSubject) {
		$this->_idSubject = $idSubject;	
	}

	/**
	 * Returns the id
	 * @return int id
	 */  	
	public function getID () {
		return $this->_id;
	}

	/**
	 * Returns the minute
	 * @return int minute
	 */  		
	public function getMinute() {
		return $this->_minute;
	}

	/**
	 * Returns the hour
	 * @return int hour
	 */  	
	public function getHour() {
		return $this->_hour;
	}

	/**
	 * Returns the day(s) of month
	 * @return string day of month
	 */  		
	public function getDayOfMonth() {
		return $this->_dayOfMonth;
	}

	/**
	 * Returns the month(s)
	 * @return string month
	 */  		
	public function getMonth() {
		return $this->_month;
	}

	/**
	 * Returns the day(s) of week
	 * @return string day of week
	 */  		
	public function getDayOfWeek() {
		return $this->_dayOfWeek;
	}

	/**
	 * Returns the id of task
	 * @return int id task
	 */  		
	public function getIDTask() {
		return $this->_idTask;
	}

	/**
	 * Returns the name of task
	 * @return string name
	 */  		
	public function getTaskName() {
		return $this->_taskName;
	}

	/**
	 * Returns the type of thing
	 * @return string type
	 */  		
	public function getTypeOfSubject() {
		return $this->_typeOfSubject;
	}

	/**
	 * Returns the id of the thing
	 * @return int id of host/farm/group
	 */  	
	public function getIDSubject() {
		return $this->_idSubject;
	}	

	/**
	 * Indicates if the value of day of month, day of week or month exists in the serialized list
	 * @param string|int $value the value to search in the list
	 * @param string|int $value the string with the list of values
	 * @return bool true if the value matches, else false
	 */ 
	private function isMatching ($value, $string) {
		if ($string === "*") {
			return true;
		}
		
		$regexp = '/^(.*[,])*' . $value . '([,].*)*$/i';

		return ( preg_match($regexp, "" . $string) === 1 ) ;
	}

	/**
	 * Indicates if at the current date and hours the task must be run 
	 * @param int $currHour current hour
	 * @param int $currMinute current minute
	 * @param string $currDayOfWeek current day of week
	 * @param string $currMonth current month
	 * @param int $currDayOfMonth current day of month 
	 * @return bool true if this task's values match, else false
	 */ 
	public function mustBeRunNow($currHour, $currMinute, $currDayOfWeek, $currMonth, $currDayOfMonth) {
		return ($this->isMatching ($currHour, $this->_hour)) && 
				($this->isMatching ($currMinute, $this->_minute)) &&
				($this->isMatching ($currDayOfWeek, $this->_dayOfWeek)) &&
				($this->isMatching ($currMonth, $this->_month)) &&
				($this->isMatching ($currDayOfMonth, $this->_dayOfMonth));
	}

	/**
	 * Serializes all tasks in the database
	 * @return array A list of tasks, containing : id, minute, hour, dayOfMonth, month, dayOfWeek, idTask, taskName, typeOfSubject, idSubject, subjectName  
	 */	
	public function getSerialized() {
		switch($this->_typeOfSubject) {
			case 'host' : 
				$hostsManager = Wol_ManageHosts::getInstance();
				$subj = $hostsManager->getByID($this->_idSubject);
			break;
			case 'farm' :
				$farmsManager = Wol_ManageFarms::getInstance();
				$subj = $farmsManager->getByID($this->_idSubject);
			break;
			case 'group' :
				$hostGroupManager = Wol_ManageHostGroups::getInstance();
				$subj = $hostGroupManager->getByID($this->_idSubject);
			break;
			default:
				$subj = false;
			break;
		}
		
		if ($subj === false) {
			$nameOfSubj = 'has been deleted';
		} else {
			$nameOfSubj = $subj->getName();
		}

		return array(
			"id" => $this->_id,
			"minute" => $this->_minute,
			"hour" => $this->_hour,
			"dayOfMonth" => $this->_dayOfMonth,
			"month" => $this->_month,
			"dayOfWeek" => $this->_dayOfWeek,
			"idTask" => $this->_idTask,
			"taskName" => $this->_taskName,
			"typeOfSubject" => $this->_typeOfSubject,
			"idSubject" => $this->_idSubject,
			'subjectName' => $nameOfSubj
		);
	}	

}
?>
