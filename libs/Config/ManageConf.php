<?php

/**
 * Manages the configuration files
 */
class Config_ManageConf {
	
	static private $_instance ;

	private function __construct() { }

	/**
	 * Returns the unique instance of the ManageConf class
	 * (creates it if it does not exist)
	 * @static
	 * @return Config_ManageConf the instance
	 */
	static public function getInstance() {
		if ( (!isset(self::$_instance)) || (self::$_instance == NULL) ) {
			self::$_instance = new Config_ManageConf();
		}
		return self::$_instance;
	}

	/**
	 * Reads the config file at the given path 
	 * returns an array of parameters, or false if the file does not exist
	 * @param $file string $file the file path
	 * @return array|bool the configuration read (or false)
	 */

	
	public function parseConfig($file) {
		if (!file_exists($file)) {
			echo ('file not found' . $file);
			return false;
		}
		return parse_ini_file($file, true);
	}

	/**
	 * Creates and returns a string to print in a config file from an array of parameters.
	 * @param array $params the array configuration
	 * @return string the string configuration
	 */
	protected function getStringConfig ($params) {
		$return = "";		
		foreach ($params as $name => $value) {
			$return .= $name . ' = "' . $value . '"' . PHP_EOL;
		}
		return $return;
	}

	/**
	 * Writes in the file at the given path the given parameters
	 * @param string $file the file path
	 * @param array $params the array of pararameters (where "paramName" => paramValue)
	 */	
	public function setConfig ($file, $params) {
		$stringToWrite = $this->getStringConfig($params);
		$FILE = fopen($file, "w");
		fwrite ($FILE, $stringToWrite);
	}
}
?>