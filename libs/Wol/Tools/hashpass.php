<?php

/**
 * Returns the md5 of the word given
 *	@param string $string2hash the word
 * @return string md5 of this word
 */

function hashPass ( $string2hash ) {
   return hash ('md5', $string2hash );
}

?>
