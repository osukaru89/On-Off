<?php

/**
 * Manages the data about tasks stored in the model and in the database
 */
class Wol_Scheduler_CronManager {
	
	static private $_instance;
	protected $_scheduledTasks = array();
	
	/**
	 * Queries the database and constructs the model
	 */
	private function __construct() {
		$query = "SELECT ST.id_scheduled_task ID, ST.minute MINUTE, ST.hour HOUR, ST.dayOfMonth DOM, ST.month MONTH, 
			ST.dayOfWeek DOW, ST.id_task TASKID, ST.typeOfSubject SUBJTYPE, ST.id_subject SUBJID, T.id_task, T.name TASKNAME 
			FROM php_wol_tasks T, php_wol_scheduled_tasks ST 
			WHERE T.id_task = ST.id_task;";
					
		$listTasks = mysql_query ($query);

		while ($rowListTasks = mysql_fetch_array($listTasks)) {
			$newTask = new Wol_Scheduler_Task ($rowListTasks['ID'], $rowListTasks['MINUTE'], $rowListTasks['HOUR'],
					$rowListTasks['DOM'], $rowListTasks['MONTH'], $rowListTasks['DOW'], $rowListTasks['TASKID'], 
					$rowListTasks['TASKNAME'], $rowListTasks['SUBJTYPE'], $rowListTasks['SUBJID']
			);
			$this->_scheduledTasks[] = $newTask;
		}		
	}
	
	/**
	 * Returns the unique object of the class
	 * (and creates it if it does not exist)
	 * @static
	 * @return Wol_Scheduler_CronManager the unique instance of the class
	 */	
	static public function getInstance() {
		if ( (!isset(self::$_instance)) || (self::$_instance == NULL) ) {
			self::$_instance = new Wol_Scheduler_CronManager();
		}
		return self::$_instance;
	}

	/**
	 * Returns the list of tasks in the model
	 * @return array List of Wol_Scheduler_Task
	 */		
	public function getScheduledTasks() {
		return $this->_scheduledTasks;
	}

	/**
	 * Adds the task in the model
	 * @param Wol_Scheduler_Task $newTask the task to add
	 */	
	protected function addScheduledTaskInList($newTask) {
		$this->_scheduledTasks[] = $newTask;
	}

	/**
	 * Searches a task in this class' list and deletes it
	 * @param int $id the id of the task we want to delete
	 */
	protected function removeScheduledTaskFromList($idTask) {
		for ($i=0;$i<count($this->_scheduledTasks);$i++) {
			if ($this->_scheduledTasks[$i]->getID() === $idTask) {
				unset($this->_scheduledTasks[$i]);
			}
		}		
	}

	/**
	 * Creates a new task in the database and in the model
	 * @param Wol_Scheduler_Task $newTask the task to add
	 */	
	public function addScheduledTask ($newTask) {
		$createdTask = $this->addScheduledTaskInDB ($newTask);
		$this->addScheduledTaskInList($createdTask);
	}

	/**
	 * Adds the task in the DB. 
	 * @param Wol_Scheduler_Task $task the task (without id) 
	 * @return Wol_Scheduler_Task the task with id set if success, false if the query failed.
	 */
	protected function addScheduledTaskInDB ($task) {
		$query = sprintf ("INSERT INTO php_wol_scheduled_tasks (minute, hour, dayOfMonth, month, dayOfWeek, id_task, typeOfSubject, id_subject) 
			VALUES ('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s');",
			$task->getMinute(),
			$task->getHour(),
			$task->getDayOfMonth(),
			$task->getMonth(),
			$task->getDayOfWeek(),
			$task->getIDTask(),  
			$task->getTypeOfSubject(),
			$task->getIDSubject()  						
		);

		$return = mysql_query ($query);
		if ($return === false) {
			return false;
		}
				
		/*Search the last added task in DB */
		$queryLastTask = 'SELECT ST.id_scheduled_task ID FROM php_wol_scheduled_tasks ST  ORDER BY ST.id_scheduled_task DESC;' ;
		$resultQuery = mysql_query ($queryLastTask);
		$rowTask = mysql_fetch_array($resultQuery);
		$task->setID($rowTask['ID']);
		return $task;	
	}

	/**
	 * Removes the task in the DB. 
	 * @param int $idTask the task (without id) 
	 * @return bool true on success, false if the query failed.
	 */	
	public function removeScheduledTask ($idTask) {
		$query = sprintf("DELETE FROM php_wol_scheduled_tasks WHERE id_scheduled_task = %s;", $idTask);
		$result = mysql_query ($query);
		if ($result) {
			$this->removeScheduledTaskFromList($idTask);
			return true;
		} else {
			return false;
		}
	}
	
	/**
	 * Serializes all tasks
	 * @return array A list of serialized tasks
	 */	
	public function getSerialized() {
		$return = array();
		foreach ($this->_scheduledTasks as $currentTask) {
			$return[]= $currentTask->getSerialized();	
		}	
		return $return ;
	}
}
?>
