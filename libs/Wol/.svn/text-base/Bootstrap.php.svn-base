<?php

/**
 * Searches php file path from class name (if not yet included), and includes them
 */
class Wol_Bootstrap {
	
	/**
	 * Searches and includes the class
	 * @param string class' name
	 */	
	public static function autoload($class) {
		$parts = explode('_', $class);
		$fileName = array_pop($parts) . '.php';
		$file = dirname(dirname(__FILE__)) . '/' . implode('/', $parts) . '/' . $fileName;
		if (file_exists($file)) {
			include_once($file);
		}
	}
}