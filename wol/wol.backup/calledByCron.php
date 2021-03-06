<?php
/* Warning : this is not a Class !
 * This program is only called by Cron, never by the application itself !
 */
 	include_once( dirname(__FILE__) . '/libs/Wol/Bootstrap.php');
	spl_autoload_register(array('Wol_Bootstrap', 'autoload'));

   if (! ( isset($db) && (db instanceof Wol_DB_Database) )) {
		$db = Wol_DB_Database::getInstance();
	}
	$db->connect();
	
		
	$logger = Wol_Log_Logger::getInstance();
	$scheduler = Wol_Scheduler_CronManager::getInstance();
	
	
	$months = array(
		1 => "jan",
		2 => "feb",
		3 => "mar",
		4 => "apr",
		5 => "may",
		6 => "jun",
		7 => "jul",
		8 => "aug",
		9 => "sep",
		10 => "oct",
		11 => "nov",
		12 => "dec"
	);

	$daysOfWeek = array (
		1 => "mon",
		2 => "tue",
		3 => "wed",
		4 => "thu",
		5 => "fri",
		6 => "sat",
		7 => "sun",
	);

	/* Read params : */

	$currHour = intval($argv[1]);
	$currMinute = intval($argv[2]);
	$currDayOfWeek = $daysOfWeek[intval ($argv[3])];
	$currMonth = $months[intval ($argv[4])];
	$currDayOfMonth = intval($argv[5]);

	$allTasks = $scheduler->getScheduledTasks();

	foreach ($allTasks as $task) {
	
		if ( $task->mustBeRunNow($currHour, $currMinute, $currDayOfWeek, $currMonth, $currDayOfMonth) )	{	
			$goodManager = false;	
			
			switch ($task->getTypeOfSubject()) {
			
				case 'host' :	
					$goodManager = Wol_ManageHosts::getInstance();
				break;
			
				case 'group' :
					$goodManager = Wol_ManageHostGroups::getInstance();
				break;
				
				case 'farm' :
					$goodManager = Wol_ManageFarms::getInstance();
				break;
			
				default : 
				break;
			}

			echo 'matches';
			if ($goodManager === false) {
				$logger->addCronLog('Cron failed : bad object type' , false);
			}
		
			if ($task->getTaskName() === 'wakeon') {
				$return = $goodManager->getByID($task->getIDSubject())->wakeOnLan();
				$logger->addCronLog('Cron called wake on LAN function on ' . $task->getTypeOfSubject() . " " . $goodManager->getByID($task->getIDSubject())->getName() , $return);
			} else if ($task->getTaskName() === 'turnoff') {
				$return = $goodManager->getByID($task->getIDSubject())->turnOff();
				$logger->addCronLog('Cron called turn off function on ' . $task->getTypeOfSubject() . " " . $goodManager->getByID($task->getIDSubject())->getName(), $return);	
			}
		}
	}

?>