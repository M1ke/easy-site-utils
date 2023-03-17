<?php


/**
 * Generates secure random alphanumeric strings for use as passwords,
 * verification keys, salts, auth tokens etc.
 *
 * Requires random_int which can be provided by PHP 7 or paragonie/random_compat
 */
function random_token(int $length, bool $include_upper_case = true): string{
	$length = min(64, max(1, $length));

	$character_set = array_merge(range('a', 'z'), range('0', '9'));
	if ($include_upper_case){
		$character_set = array_merge($character_set, range('A', 'Z'));
	}

	$set_size = count($character_set);
	$password_arr = [];
	for ($n = 0; $n<$length; $n++){
		/** @noinspection PhpUnhandledExceptionInspection */
		$password_arr[$n] = $character_set[random_int(0, $set_size-1)];
	}

	return implode('', $password_arr);
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
