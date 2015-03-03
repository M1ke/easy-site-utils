<?php
/* Constants
	LOG - directory to store log files in
	LOG_REDIRECTS - log redirects
*/

function count_true($arr){
	$count=0;
	foreach ($arr as $item){
		$count+=$item ? 1 : 0;
	}
	return $count;
}

function csv_array($file,$check=false,$delimiter=',',$start=1){
	if (is_array($file)){
		$file=$file['tmp_name'];
	}
	$fh=fopen($file,'r');
	$parsed=array();
	// might want to improve this; maybe limit it based on number of empty rows?
	$limit=50000; // limit to 50,000
	$n=0;
	while ($parse=fgetcsv($fh,0,$delimiter) and $n<$limit){
		$parsed[]=$parse;
		$n++;
	}
	fclose($fh);
	return csv_array_parse($parsed,$check,$start);
}

function csv_array_parse($parsed,$check=false,$start=1){
	$title_line=$parsed[0];
	foreach ($title_line as $n => $field){
		$field=trim($field);
		$field=@strtolower($field);
		// $field=first_word($field); // its probably a bad idea to do this - is legacy from old import
		if (!empty($field)){
			$title_line[$n]=$field;
		}
	}
	$arr=array();
	for ($n=$start;$n<count($parsed);$n++){
		$item=$parsed[$n];
		$i=0;
		foreach ($title_line as $field){
			$item[$i]=trim($item[$i]);
			$arr[$n][$field]=$check ? string_check($item[$i]) : $item[$i];
			$i++;
		}
	}
	return $arr;
}

function csv_array_string($string,$check=false,$delimiter=',',$start=1){
	$string=preg_split("/\r\n|\n|\r/",$string);
	foreach ($string as $line){
		$parsed[]=str_getcsv($line,$delimiter);
	}
	return csv_array_parse($parsed,$check,$start);
}

// This code comes from somewhere online, find where and add a citation
function debug_code_error($num,$str,$file,$line,$context){
    if (!(error_reporting() & $num)) return;
    switch($num){
    case E_WARNING:
    case E_USER_WARNING:
    case E_STRICT:
    case E_NOTICE:
    case E_USER_NOTICE:
        $type='warning';
        $fatal=false;
    break;
    default:
        $type='fatal error';
        $fatal=true;
    }
    $trace=array_reverse(debug_backtrace());
    array_pop($trace);
    if (php_sapi_name()=='cli'){
        echo 'Backtrace from '.$type.' \''.$str.'\' at '.$file.' '.$line.':'."\n";
        foreach ($trace as $item) echo '  '.(isset($item['file'])?$item['file']:'<unknown file>').' '.(isset($item['line'])?$item['line']:'<unknown line>').' calling '.$item['function'].'()'."\n";
    }
	else {
        echo '<p class="error_backtrace">Backtrace from '.$type.' \''.$str.'\' at '.$file.' '.$line.':';
        echo '<ol>';
        foreach ($trace as $item) echo '<li>'.(isset($item['file'])?$item['file']:'<unknown file>').' '.(isset($item['line'])?$item['line']:'<unknown line>').' calling '.$item['function'].'()</li>';
        echo '</ol></p>';
    }
    if (ini_get('log_errors')){
        $items=array();
        foreach($trace as $item) $items[]=(isset($item['file'])?$item['file']:'<unknown file>').' '.(isset($item['line'])?$item['line']:'<unknown line>').' calling '.$item['function'].'()';
        $message='Backtrace from '.$type.' \''.$str.'\' at '.$file.' '.$line.': '.join(' | ', $items);
        error_log($message);
    }
    if ($fatal) exit(1);
}

function get_args_smart(Array $args,$n=0){
	for (;$n<count($args);$n++){
		if (is_array($args[$n])){
			$array=$args[$n];
		}
		elseif (is_callable($args[$n])){
			$callable=$args[$n];
		}
		else {
			$string=$args[$n];
		}
	}
	return ['array'=>$array,'callable'=>$callable,'string'=>$string];
}

function get_browse(){
	$browser=array('OPERA','MSIE','NETSCAPE','FIREFOX','SAFARI','KONQUEROR','MOZILLA');
	$info['browser']='OTHER';
	foreach ($browser as $parent){
		if (($s=strpos(strtoupper($_SERVER['HTTP_USER_AGENT']),$parent))!==false){        
			$f=$s+strlen($parent);
			$version=substr($_SERVER['HTTP_USER_AGENT'],$f,5);
			$version=preg_replace('/[^0-9,.]/','',$version);
			$info['browser']=$parent;
			$info['version']=$version;
			break;
		}
	}
	return $info;
}

function get_domain($url){
	$url=str_replace('http://','',$url);
	$dom=substr($url,0,strpos($url,'/'));
	return $dom;
}

function is_binary($number){
	return ($number==1 or $number==0);
}

function is_coord($lat,$lng){
	return is_numeric($lat) and is_numeric($lng) and ($lat!=0 or $lng!=0);
}

function is_mobile($agent=null,$session=true,$make_mobile=false){
	if ($make_mobile){
		log_file('made mobile');
		$_SESSION['S_mobile']=true;
		$_SESSION['S_desktop']=false;
	}
	if (isset($_SESSION['S_mobile']) and $session){
		return $_SESSION['S_mobile'];
	}
	if (empty($agent)){
		$agent=$_SERVER['HTTP_USER_AGENT'];
	}
	switch (true){
		case (preg_match('/mobile/i',$agent));
			$mobile=true;
		break;
		case (preg_match('/iphone/i',$agent));
			$mobile=true;
		break;
		// only for people on WiFi
		case (preg_match('/ipod/i',$agent));
			$mobile=true;
		break;
		case (preg_match('/blackberry/i',$agent));
			$mobile=true;
		break;
		// can run on a number of phones
		case (preg_match('/opera mini/i',$agent));
			$mobile=true;
		break;
	}
	if ($session){
		$_SESSION['S_mobile']=$mobile;
	}
	else {
		$_SESSION['S_mobile']=false;
		unset($_SESSION['S_mobile']);
	}
	return $mobile;
}

function is_provided(&$val){
	$val=trim($val);
	return (strlen($val)>0);
}

function is_input($var){
	return (!is_null($var) and $var!=='');
}

function local(){
	return in_string(['127.0.0.1','localhost'],$_SERVER['HTTP_HOST']);
}

function log_file($log,$var=null,$file=null,$overwrite=false){
	$file=log_file_location($file);
	$log='----Logged on '.date('r').' ----'.PHP_EOL.PHP_EOL.'$'.$var.': '.((is_array($log) or is_object($log)) ? print_r($log,true) : $log).PHP_EOL.PHP_EOL;
	file_save_($file,$log,$overwrite);
	return true;
}

function log_file_location($file=null){
	if (empty($file)){
		$file='main.log';
		$set_dir=true;
	}
	if (defined('LOG') and substr($file,0,1)!='/'){
		$file=LOG.$file;
	}
	elseif ($set_dir){
		$file=__DIR__.'/logs/'.$file;
	}
	return $file;
}

function redirect_url($url=null,$debug=false){
	if (empty($url)){
		$url=$_SERVER['HTTP_REFERER'];
	}
	if (defined('LOG_REDIRECTS')){
		log_file($url,'redirect');
	}
	if ($debug){
		echo $url;
	}
	else {
		header('location:'.$url);
	}
	die;
}

function shell(){
	return !empty($_SERVER['shell']);
}

function success(){	
	$return=func_get_args();
	if (is_array($return[0])){
		$return=$return[0];
	}
	echo 'success'.(!empty($return) ? '|'.implode('|',$return) : '');
	die;
}

function var_switch(&$var1,&$var2){
	$copy=$var1;
	$var1=$var2;
	$var1=$copy;
}

function upload_array($input,$original,$n){
	$_FILES[$input]=array(
		'name'=>$_FILES[$original]['name'][$n],
		'type'=>$_FILES[$original]['type'][$n],
		'tmp_name'=>$_FILES[$original]['tmp_name'][$n],
		'error'=>$_FILES[$original]['error'][$n],
		'size'=>$_FILES[$original]['size'][$n]
	);
	return $_FILES[$input];
}