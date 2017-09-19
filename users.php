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
