<?php

require_once 'lib/swift_required.php';

/**
 * Stores the informations required to send an email
 */
class Mailing_Mail {
	
	protected $message = false;
	
	/**
	 * Constructor
	 * @param string $subject the email's subject
	 * @param string $text the email's content
	 * @param string $sendTo the recipient
	 * @param string $sendFrom the sender (generally, the admin of the application)
	 */
	public function __construct($subject, $text, $sendTo, $sendFrom) {
		$this->_message = Swift_Message::newInstance();
		$this->_message->setSubject($subject);
		$this->_message->setFrom(array('geru@irontec.com' => 'geru@irontec.com'));//$sendFrom => $sendFrom));
		$this->_message->setTo(array($sendTo));
		$this->_message->setBody($text);
	}
	
	public function getMessage () {
		return $this->_message;	
	}
}

?>
