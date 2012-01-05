<?php

/**
 * Connects to the database
 */
class Wol_DB_Database {
	
	private static $_instance;	
	
	private function __construct() { }

	/**
	 * Returns the unique object of the class
	 * (and creates it if it does not exist)
	 * @static
	 * @return Wol_DB_Database the unique instance of the class
	 */	
	public static function getInstance() {
		if ( (!isset(self::$_instance)) || (self::$_instance == NULL) ) {
			self::$_instance = new Wol_DB_Database();
		}
		return self::$_instance;
	} 

	/**
	 * Tries to connect to the database
	 * @return bool true if success, false if the connection failed
	 */	
	public function connect() {
		if ( ($link2base = mysql_connect('localhost', 'root', 'farsa')) === False ) {
			return false;
		}
		if ( mysql_select_db('bdd_php_wol', $link2base) === False ) {
			return false;
		}
		return true;
	}
}

?>
