<?php
function encrypt($string,&$salt=null){
	return sha1($salt.$string);
}

function encrypt_token($string,$length=20,&$salt=null){
	$token=encrypt($string,$salt);
	$token=substr($token,-1*$length);
	return $token;
}

function get_facebook_cookie($fbid,$fbsecret){
	$args=array();
	parse_str(trim($_COOKIE['fbs_'.$fbid],'\\"'),$args);
	ksort($args);
	$payload='';
	foreach ($args as $key => $value){
		if ($key!='sig'){
			$payload.=$key.'='.$value;
		}
	}
	if (md5($payload.$fbsecret)!=$args['sig']){
		return null;
	}
	return $args;
}

function make_pass(&$p,$names=null){
	if (empty($names)){
		$names=array('input'=>'pass','encrypt'=>'pass','salt'=>'salt','clear'=>'clear');
	}
	$clear=$p[$names['input']];
	$salt=rand_pass();
	$encrypt=encrypt($clear,$salt);
	$p[$names['encrypt']]=$encrypt;
	$p[$names['salt']]=$salt;
	$p[$names['clear']]=$clear;
}

function make_pass_rand(&$p,$names=null){
	$p[!empty($names['input']) ? $names['input'] : 'pass']=rand_pass();
	make_pass($p,$names);
}

function rand_pass($length=null,$salt='0123456789abcdef'){
	$num=rand(0,99999);
	$num2=rand(10000,99999) ^ rand(10000,99999);
	$hash=md5($num.$salt.$num2);
	if (empty($length)){
		$length=rand(8,10);
	}
	$pass=substr($hash,(-1)*$length);
	return $pass;
}