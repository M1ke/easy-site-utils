<?php
function get_facebook_cookie($fbid, $fbsecret){
	$args = [];
	parse_str(trim($_COOKIE['fbs_'.$fbid], '\\"'), $args);
	ksort($args);
	$payload = '';
	foreach ($args as $key => $value){
		if ($key!='sig'){
			$payload .= $key.'='.$value;
		}
	}
	if (md5($payload.$fbsecret)!=$args['sig']){
		return null;
	}

	return $args;
}

/**
 * @param int $length
 * @param string $salt
 * @return string
 *
 * @deprecated This does not generate secure random values.
 * @deprecated Use random_token (from this lib) for user passwords, or uniqid for identifier strings
 */
function rand_pass($length = null, $salt = '0123456789abcdef'){
	$num = rand(0, 99999);
	$num2 = rand(10000, 99999) ^ rand(10000, 99999);
	$hash = md5($num.$salt.$num2);
	if (empty($length)){
		$length = rand(8, 10);
	}
	$pass = substr($hash, (-1) * $length);

	return $pass;
}

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
		throw new Exception("The password generator should be called with a string length of 128 or less to avoid accidental long loops. $max_length characters really should be enough for a password");
	}

	$character_set = array_merge(range('a', 'z'), range('0', '9'));
	if ($include_upper_case){
		$character_set = array_merge($character_set, range('A', 'Z'));
	}

	$set_size = count($character_set);
	$password_arr = [];
	for ($n = 0; $n<$length; $n++){
		$password_arr[$n] = $character_set[random_int(0, $set_size - 1)];
	}

	$password = implode('', $password_arr);

	return $password;
}
