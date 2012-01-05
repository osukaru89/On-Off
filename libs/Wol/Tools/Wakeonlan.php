<?php

/**
 * Creates and sends magic packets to hosts.
 */
class Wol_Tools_Wakeonlan {

	/**
	 * Converts the given number to hexadecimal string
	 *	@param int $hex the number
	 * @return string the number converted to string
	 */
	
	public function hexToStr($hex) {
		$string='';
		for ($i=0; $i < strlen($hex)-1; $i+=2) {
			$string .= chr(hexdec($hex[$i].$hex[$i+1])) ;
		}
		return $string;
	}
	
	/**
	 * Sends wake on LAN magic packet
	 *	@param string $MACaddress the mac address of the host to wake on
	 *	@param int $port the port number
	 **/
	
	public function wakeOnLan ($MACaddress, $port) {
		$magic_packet = self::hexToStr('FFFFFFFFFFFF');

		for ($i = 0; $i < 16; $i++) {
			$strmac = self::hexToStr($MACaddress);
			$magic_packet = $magic_packet . $strmac;
		}

		echo 'Paquete Mágico '.$magic_packet;

		if ( ($sock = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP)) === False ) {
			echo "Socket creation error";
		}

		socket_set_option($sock, SOL_SOCKET, SO_BROADCAST, 1);  // To send broadcast packets  

		if ( (socket_sendto($sock, $magic_packet, strlen($magic_packet), 0, '255.255.255.255', $port)) === False) {
			echo "Error sending magic packet" ;
		}
	}
}

?>
