<?php
// generates the php code to create a selected array
function array_code($arr){
	if (is_array($arr)){
		return var_export($arr);
	}

	return '';
}

function array_assoc($arr){
	$assoc = [];
	foreach ($arr as $key => $val){
		$assoc[$val] = $key;
	}

	return $assoc;
}

function array_overwrite($base, $fill){
	if (empty($fill)){
		return $base;
	}
	$arr = [];
	foreach ($base as $n => $key){
		if (isset($fill[$key])){
			$arr[$key] = $fill[$key];
		}
		else {
			$arr[$n] = $key;
		}
	}

	return $arr;
}

function array_extract(array $arr, $field){
	$return = [];
	foreach ($arr as $key => $val){
		$return[$key] = $val[$field];
	}

	return $return;
}

// places items in a flat array into a multidimensional array based on keys
function array_id(&$arr, $key, $key2 = null, $key3 = null){
	if (is_array($arr)){
		$new = [];
		foreach ($arr as $item){
			if ($key3){
				$new[$item[$key3]][$item[$key2]][$item[$key]] = $item;
			}
			elseif ($key2) {
				$new[$item[$key2]][$item[$key]] = $item;
			}
			else {
				$new[$item[$key]] = $item;
			}
		}
		$arr = $new;
	}
}

function array_data_sort(Array $arr, $sort_field, $desc = false){
	$first = reset($arr);
	if (!isset($first[$sort_field])){
		return $arr;
	}
	foreach ($arr as $key => $item){
		$sort[$key] = $item[$sort_field];
	}
	array_multisort($sort, $desc ? SORT_DESC : SORT_ASC, $arr);

	return $arr;
}

function array_union(Array $a, Array $b){
	return array_unique(array_merge($a, $b));
}

function array_unserialize($string){
	$arr = unserialize($string);
	if ($arr===false){
		$arr = [];
	}

	return $arr;
}

// shifts all array keys by a specified value (default reduces by 1)
function array_key_shift(&$arr, $num = -1){
	$temp = [];
	foreach ($arr as $key => $val){
		$key += $num;
		$temp[$key] = $val;
	}
	$arr = $temp;
	unset($temp);
}

function array_keys_2d($arr){
	$first_row = reset($arr);

	return array_keys($first_row);
}

// takes specific keys out of an array and returns the result
function array_pull(array $arr, $pull, &$new = []){
	if (!is_array($pull)){
		$pull = explode(',', $pull);
	}

	foreach ($pull as $key => $field){
		$field = trim($field);
		$key = is_numeric($key) ? $field : $key;
		$new[$field] = $arr[$key];
	}

	return $new;
}

// reutrns the first item of an array. Not certain where we needed this, reset($arr) does the same!
function arraystr($array){
	foreach ($array as $item){
		return (string)$item;
	}

	return '';
}

// flattens multiple keys of an array into a single dimension
function array_oned(&$arr, $assoc, $pre){
	if (is_array($arr[$assoc])){
		foreach ($arr[$assoc] as $key => $val){
			$arr[$pre.'-'.$key] = $val;
		}
	}
	unset($arr[$assoc]);
}

function array_random(Array $arr){
	$keys = array_keys($arr);
	$key = $keys[rand(0, count($keys)-1)];

	return $arr[$key];
}

function array_remove($needle, Array $haystack){
	$key = array_search($needle, $haystack);
	if ($key!==false){
		unset($haystack[$key]);
	}

	return $haystack;
}

function array_remove_empty(Array $arr){
	foreach ($arr as $key => $val){
		if (empty($val)){
			unset($arr[$key]);
		}
	}

	return $arr;
}

// inserts an item into an array at a specified key without creating a new array
function array_slip($arr, $assign, $overwrite_or_val = true, $overwrite = true){
	if (!is_array($assign)){
		$assign = [$assign => $overwrite_or_val];
	}
	else {
		$overwrite = $overwrite_or_val;
	}
	foreach ($assign as $key => $val){
		if ($overwrite or !isset($arr[$key])){
			$arr[$key] = $val;
		}
	}

	return $arr;
}

function array_slip_2d($arr, $slip, $val = null){
	if (!is_array($slip)){
		$slip = [$slip => $val];
	}
	foreach ($arr as &$sub_arr){
		foreach ($slip as $sub_key => $sub_val){
			$sub_arr = array_slip($sub_arr, $sub_key, $sub_val);
		}
	}

	return $arr;
}

// opposite of array_slip, removes one item from an array without creating a new array
function array_snip($arr, $key){
	unset($arr[$key]);

	return $arr;
}

function array_invert($item, $item_key, &$twod, $include = [], $ext = ''){
	foreach ($item as $key => $value){
		if (empty($include) || in_array($key, $include)){
			$twod[$key.$ext][$item_key] = $value;
		}
	}

	return $twod;
}

function array_stitch(Array $arr, Array $order, $glue = '', $missing = false){
	$new_arr = [];
	foreach ($order as $key){
		$new_arr[] = $arr[$key];
		if ($missing){
			unset($arr[$key]);
		}
	}
	if ($missing){
		foreach ($arr as $val){
			$new_arr[] = $val;
		}
	}

	return implode($glue, $new_arr);
}

function array_stitch_empty(Array $arr, Array $order, $glue = ''){
	foreach ($order as $n => $key){
		if (empty($arr[$key])){
			unset($order[$n]);
		}
	}

	return array_stitch($arr, $order, $glue);
}

function array_strip_end(array $arr){
	$assoc = is_assoc($arr);

	$n = count($arr)-1;
	while (empty($arr[$n]) && $n>=0){
		unset($arr[$n]);
		$n--;
	}

	return $assoc ? $arr : array_values($arr);
}

function array_strip_start(array $arr){
	$assoc = is_assoc($arr);

	$count = count($arr)-1;
	$n = 0;
	while (empty($arr[$n]) && $n<=$count){
		unset($arr[$n]);
		$n++;
	}

	return $assoc ? $arr : array_values($arr);
}

function array_strip(array $arr){
	$arr = array_strip_start($arr);
	$arr = array_strip_end($arr);

	return $arr;
}

// inserts an item between specific keys in an array. useful when relying on the iteration order of an array
function array_insert_assoc($arr, $offset, $insert, $before = false){
	$keys = array_keys($arr);
	$offset = array_search($offset, $keys, true);
	if ($before and $offset>0){
		$offset--;
	}
	$temp = [];
	$n = 0;
	$adj = 0;
	foreach ($arr as $key => $val){
		$temp[is_numeric($key) ? ($key+$adj) : $key] = $val;
		if ($n==$offset){
			if (is_array($insert)){
				foreach ($insert as $ins_key => $ins_val){
					$temp[$ins_key] = $ins_val;
				}
			}
			else {
				$temp[] = $insert;
				$adj++;
			}
		}
		$n++;
	}

	return $temp;
}

function array_search_2d($needle, $haystack, $key, $last = false){
	$found = false;
	foreach ($haystack as $index => $item){
		if ($item[$key]==$needle){
			$found = $index;
			if (!$last){
				break;
			}
		}
	}

	return $found;
}

function array_switch($arr, $key1, $key2){
	$temp = [];
	foreach ($arr as $key => $val){
		if ($key===$key1){
			$temp[$key2] = $arr[$key2];
		}
		elseif ($key===$key2) {
			$temp[$key1] = $arr[$key1];
		}
		else {
			$temp[$key] = $val;
		}
	}

	return $temp;
}

// builds a compressed array up into a multidimensional array
function array_twod(&$arr, $assoc, $pre){
	$keys = array_keys($arr);
	foreach ($keys as $key){
		if (strpos($key, $pre)!==false){
			if (is_array($arr[$key])){
				foreach ($arr[$key] as $n => $val){
					$arr[$assoc][$n][str_replace($pre.'-', '', $key)] = $val;
				}
			}
			else {
				$arr[$assoc][str_replace($pre.'-', '', $key)] = $arr[$key];
			}
			unset($arr[$key]);
		}
	}
}

// unsets specific keys in an array
function array_unset($arr, $keys){
	foreach ($keys as $key){
		unset($arr[$key]);
	}

	return $arr;
}

// unsets items in an array where the value is false or empty
function array_unset_false($arr, $strict = true){
	foreach ($arr as $key => $val){
		if ($strict and $val===false){
			unset($arr[$key]);
		}
		elseif ($val=='') {
			unset($arr[$key]);
		}
	}

	return $arr;
}

function average($arr){
	$avg = array_sum($arr) / count($arr);

	return $avg;
}

function flatten_array($array){
	$new = [];
	if (is_array($array)){
		foreach ($array as $item){
			if (is_array($item)){
				$item = arraystr($item);
			}
			$new[] = $item;
		}
	}

	return $new;
}

function implode_assoc($sep, $arr){
	$string = '';
	foreach ($arr as $key => $val){
		$string .= $val;
		if (!is_numeric($key) and $val!=end($arr)){
			$string .= $sep;
		}
	}

	return $string;
}

function implode_dual($sep, $arr1, $arr2){
	$string = '';
	foreach ($arr1 as $key => $val){
		$string .= $val.$sep[1].$arr2[$key];
		if ($val!=end($arr1)){
			$string .= $sep[0];
		}
	}

	return $string;
}

function implode_key($sep, $arr){
	foreach ($arr as $key => &$val){
		$val = $key;
	}
	$arr = implode($sep, $arr);

	return $arr;
}

function implode_wrap($pre, $suf, $arr){
	$html = '';
	if (is_array($arr)){
		foreach ($arr as $val){
			$html .= $pre.$val.$suf;
		}
	}

	return $html;
}

function is_array_full($arr){
	if (!is_array($arr)){
		return false;
	}

	if (empty($arr)){
		return false;
	}

	foreach ($arr as $item){
		if (!empty($item)){
			return true;
		}
	}

	return false;
}

function is_array_true($arr){
	if (!is_array($arr)){
		return false;
	}
	$return = false;
	foreach ($arr as $item){
		if ($item!==false){
			$return = true;
		}
	}

	return $return;
}

function is_assoc(&$arr){
	if (is_array($arr)){
		return !(array_values($arr)===$arr);
	}

	return false;
}

function largest_array(){
	$arrs = func_get_args();
	$large = 0;
	$largest = 0;
	foreach ($arrs as $key => $arr){
		$count = count($arr);
		if ($count>$large){
			$large = $count;
			$largest = $key;
		}
	}

	return $arrs[$largest];
}

function largest_array_count(){
	$arrs = func_get_args();
	$large = 0;
	foreach ($arrs as $arr){
		$count = count($arr);
		if ($count>$large){
			$large = $count;
		}
	}

	return $large;
}

function make_array($count){
	$arr = [];
	for ($n = 0; $n<$count; $n++){
		$arr[$n] = 1;
	}

	return $arr;
}

function number_list($to = 10, $start = 1){
	$arr = [];
	for ($n = $start; $n<=$to; $n++){
		$arr[] = $n;
	}

	return $arr;
}

function super_implode($sep, $arr, $assoc){
	$temp = [];
	foreach ($arr as $val){
		$temp[] = $val[$assoc];
	}
	$temp = implode($sep, $temp);

	return $temp;
}

// returns true if any of the keys exist in the array
// by default uses OR condition
function array_keys_exist($keys, $arr, $and = false){
	$result = $and;
	foreach ($keys as $key){
		$result = $and ? $result && array_key_exists($key, $arr) : $result || array_key_exists($key, $arr);
	}

	return $result;
}

function as_array($item){
	if (!is_array($item)){
		$item = [$item];
	}

	return $item;
}

/**
 * Verifies, recursively, existence of keys and if key values are an array
 * Will return an empty array is verified
 *
 * @param array $format
 * @param array $check
 * @param string $depth
 * @param array $errors
 * @return array
 */
function array_keys_verify(array $format, array $check, $depth = '', $errors = []){

	foreach ($format as $key => $sub_format){
		$current_depth = $depth.'.'.$key;
		// First see if they key is even set
		if (!isset($check[$key])){
			$errors[$current_depth] = "The key '$key' must be set, even if it is blank";
			continue;
		}

		if (is_null($sub_format)){
			continue;
		}

		// If the sub-format isn't an array we're done with this key
		if (!is_array($sub_format)){
			$type = gettype($check[$key]);
			if ($sub_format==='non-zero'){
				// non-zero isn't a regular type, but requiring non-zero values
				//  can be as relevant to operational logic, preventing div by zero etc
				if ((int)$check[$key]===0){
					$errors[$current_depth] = "The key '$key' must be a non-zero integer";
				}
			}
			elseif ($type!==$sub_format) {
				$errors[$current_depth] = "The key '$key' must be of type '$sub_format', not '$type''";
			}

			continue;
		}

		// If sub-format is an array and the value we're checking isn't, that's a problem
		if (!is_array($check[$key])){
			$errors[$current_depth] = "The key '$key' must be an array, even if it is empty";
			continue;
		}

		// If our sub-format is an array with values we then dive into that
		if (!empty($sub_format)){
			$errors = array_keys_verify($sub_format, $check[$key], $current_depth, $errors);
			continue;
		}
	}

	return $errors;
}
