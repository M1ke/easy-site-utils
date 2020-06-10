<?php
function decimal_ratio($decimal, $sep = ':'){
	$num = [0, 0];
	do {
		$num[1]++;
		$num[0] = $num[1] * $decimal;
	} while (!is_whole($num[0]));

	return implode($sep, $num);
}

/**
 * Takes two float values and asserts whether they are equal to a certain precision
 * This avoids problems when a specific large floating point value will not accurately
 * be compared to a conversion from another type e.g. a submitted string
 *
 * @param float|int $a currency value in pounds
 * @param float|int $b currency value in pounds
 * @param int $dp
 *
 * @return bool
 */
function floats_equal($a, $b, $dp = 2){
	$multiply = 10 ^ $dp;

	return (int)round($a * $multiply)===(int)round($b * $multiply);
}

function is_decimal($num){
	return (int)$num!=$num;
}

function is_even($n){
	return $n % 2===0;
}

function is_number(&$number, $blank = null){
	if (@strlen($number)>0){
		$number = trim($number);
		if (is_numeric($number)){
			return true;
		}

		$result = word_to_number($number);
	}

	if (!empty($result) || $blank){
		$number = false;

		return true;
	}

	return false;
}

function is_not_zero($number){
	$number = abs($number);

	return is_pos($number);
}

function is_pos($number){
	if (!is_numeric($number)){
		return false;
	}

	return ($number>0);
}

function is_pos_nullable($number){
	if (is_null($number)){
		return true;
	}

	return is_pos($number);
}

function is_pos_blank($number){
	if ($number===''){
		return true;
	}

	return is_pos_nullable($number);
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
		$number = trim($number);
		if ($number>0 && is_numeric($number)){
			return true;
		}

		return word_to_number($number);
	}

	if ($blank){
		$number = false;

		return true;
	}

	return false;
}

function is_whole($num){
	return $num / round($num)==1;
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

/**
 * Originally this was more complex; it split out the
 * pence and pound values and added them separately to avoid
 * rounding errors. However when writing unit tests it seemed
 * just a plain round after the multiply works as well. The
 * function is still worth having as it better clarifies what is happening
 *
 * @param float|string $input
 *
 * @return int
 */
function pounds_to_pence($input){
	// Must round afterwards or can get e.g. 641.66999999999
	return (int) round($input * 100);
}

function ratio_decimal($ratio, $sep = ':'){
	$num = explode($sep, $ratio);

	return $num[1]==0 ? 1 : $num[0] / $num[1];
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
