<?php

require_once 'lib/swift_required.php';

/**
 * Provides functions to send emails
 */
class Mailing_Sender {
	
	static private $_instance ;
	protected $_textManager;
	
	private function __construct() { }

	/**
	 * Returns the unique object of the class
	 * (and creates it if it does not exist)
	 * @static
	 * @return Mailing_Sender the unique instance of the class
	 */	
	static public function getInstance() {
		if ( (!isset(self::$_instance)) || (self::$_instance == NULL) ) {
			self::$_instance = new Mailing_Sender();
		}
		return self::$_instance;
		$this->_textManager = Mailing_TextManager::getinstance();
	}

	/**
	 * If the admin choosed to send a email for this type of action
	 * gets the email data in the database, replaces the tokens with valued provided and sends it to all recipients
	 * @param string $action the action
	 * @param array $tokensArray the tokens to replace
	 * @param array $recipArray the recipients
	 * @return int the number of emails sent
	 */		
	public function sendMail ($actionName, $tokensArray, $recipArray, $lang="EN") {

		$textManager = Mailing_TextManager::getinstance();		
		$numSent = 0;		

		if ($textManager->haveToSend($actionName)) {		
			$idAction = $textManager->getActionID($actionName);
			$data = $textManager->getEmail ($idAction, $tokensArray, $lang);
			
			//Create the Transport
			$transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');

			//Create the Mailer using your created Transport
			$mailer = Swift_Mailer::newInstance($transport);

			foreach ($recipArray as $recipient) {
				$mail = new Mailing_Mail($data['subject'], $data['text'], $recipient, $data['sender']);
				
				//Send the message
				$numSent += $mailer->send($mail->getMessage());
			}
		}
		return $numSent;
	}
}

?>
