<?php
function decimal_ratio($decimal, $sep = ':'){
	$num=array(0,0);
	do {
		$num[1]++;
		$num[0] = $num[1]*$decimal;
	}
	while (!is_whole($num[0]));
	$ratio = implode($sep, $num);
	return $ratio;
}

function is_decimal($num){
	return ((int)$num!=$num);
}

function is_even($n){
	return ($n%2 == 0);
}

function is_number(&$number, $blank = null){
	if (@strlen($number)>0){
		$number = trim($number);
		if (is_numeric($number)){
			return true;
		}
		else {
			$result=word_to_number($number);
		}
	}
	if (!empty($result) || $blank){
		$number = false;
		return true;
	}
	else {
		return false;
	}
}

/**
 * @param $number
 * @param null $blank
 * @return bool
 *
 * @deprecated use is_pos which does not pass by reference
 */
function is_positive(&$number, $blank = null){
	if (@strlen($number)>0){
		$number=trim($number);
		if ($number>0 && is_numeric($number)){
			return true;
		}
		else {
			return word_to_number($number);
		}
	}
	elseif ($blank){
		$number = false;
		return true;
	}
	return false;
}

function is_pos($number, $null_allowed = null){
	if (is_null($number) && $null_allowed){
		return true;
	}
	if (!is_numeric($number)){
		return false;
	}

	return ($number>0);
}

function is_not_zero($number, $null_allowed = null){
	$number = abs($number);

	return is_pos($number);
}

function is_whole($num){
	return $num/round($num)==1;
}

function num_position($num){
	$string = (string)$num;
	$end = substr($string, strlen($string)-1);
	switch ($end){
		case '1':
			return $num.'st';
		break;
		case '2':
			return $num.'nd';
		break;
		case '3':
			return $num.'rd';
		break;
		default:
			return $num.'th';
	}
}

function ratio_decimal($ratio, $sep = ':'){
	$num = explode($sep, $ratio);
	return $num[1]==0 ? 1 : $num[0]/$num[1];
}

function round_custom($num, $precision = 5, $func = 'round'){
	return $precision * $func($num / $precision);
}

// These just seem lazy, should likely check for use and remove them
function round_down($num){
	return floor($num);
}

function round_up($num){
	return ceil($num);
}
