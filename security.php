<?php


/**
 * Generates secure random alphanumeric strings for use as passwords,
 * verification keys, salts, auth tokens etc.
 *
 * Requires random_int which can be provided by PHP 7 or paragonie/random_compat
 *
 * @param int $length
 * @param bool $include_upper_case (default on, set to false for lower case only)
 *
 * @return string
 * @throws Exception
 */
function random_token($length, $include_upper_case = true){
	if ($length<1 || !is_numeric($length)){
		throw new Exception("The password generator must be called with a numeric length parameter greater than 0");
	}

	$max_length = 64;
	if ($length>$max_length){
		throw new Exception("The password generator should be called with a string length of $max_length or less to avoid accidental long loops.");
	}

	$character_set = array_merge(range('a', 'z'), range('0', '9'));
	if ($include_upper_case){
		$character_set = array_merge($character_set, range('A', 'Z'));
	}

	$set_size = count($character_set);
	$password_arr = [];
	for ($n = 0; $n<$length; $n++){
		$password_arr[$n] = $character_set[random_int(0, $set_size-1)];
	}

	$password = implode('', $password_arr);

	return $password;
}

/*
    simple_encode and simple_decode use openssl_encrypt and openssl_descrypt methods internally.
    $data - data to be encoded, required param
    $passwd - a key, use the same key while encoding and decoding, required param
    $method - aes-128-cbc default
    $options - 0 default
    $iv - a non-NULL Initialization Vector, must be 16 chars  
    Use these methods to hide url parameters. Not suitable for passwords as it is more of obfuscation rather than encryption
*/

function simple_encode($data, $passwd, $method = 'aes-128-cbc', $options = 0, $iv = 'SomeA1AweS0meK5y'){
	return openssl_encrypt($data, $method, $passwd, $options, $iv);
}

function simple_decode($data, $passwd, $method = 'aes-128-cbc', $options = 0, $iv = 'SomeA1AweS0meK5y'){
	return openssl_decrypt($data, $method, $passwd, $options, $iv);
} 
