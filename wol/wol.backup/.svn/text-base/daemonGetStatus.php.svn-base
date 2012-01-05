<?php

include_once( dirname(__FILE__) . '/libs/Wol/Bootstrap.php');
spl_autoload_register(array('Wol_Bootstrap', 'autoload'));

$dirConf = dirname(__FILE__) . '/config/'; // The config files are here

$db = Wol_DB_Database::getInstance();
$db->connect();
	
$hostsManager = Wol_ManageHosts::getInstance();
$hostsManager->ipUpdate();
$hostsManager->updateStatus();

/* For statistics : */
$currHour = intval($argv[1]);
$currMinute = intval($argv[2]);
$currDayOfWeek = intval($argv[3]);

$statsManager = Statistics_ManageStats::getinstance();
$statsManager->loadConf($dirConf);
$conf = $statsManager->getStatConfig($dirConf);

if ( ( (($currHour*60)+$currMinute) % $conf['refresh_minutes']) === 0) {
	foreach ($hostsManager->getHosts() as $host) {
		if (intval($host->getStatus()) === 0) { 
			$statsManager->updateHostOnline($host->getID(), $conf['refresh_minutes']);
		}
	}
}

if ( ($currDayOfWeek === 1) && ($currMinute === 0) && ($currHour === 0) ) {
	$statsManager->weekChange($conf['weeks_to_keep']);
}

exit(0);

?>
