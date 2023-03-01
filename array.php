<?php
/**
 * @param array $arr
 *
 * @return array
 */
function array_assoc(array $arr){
	$assoc = [];
	foreach ($arr as $key => $val){
		$assoc[$val] = $key;
	}

	return $assoc;
}

/**
 * generates the php code to create a variable (or blank if var is not array)
 *
 * @param mixed $arr
 *
 * @return string
 */
function array_code($arr){
	if (is_array($arr)){
		/** @noinspection ForgottenDebugOutputInspection */
		return var_export($arr) ?: '';
	}

	return '';
}

/**
 * @param array $arr
 * @param string $sort_field
 * @param bool $desc
 *
 * @return array
 */
function array_data_sort(array $arr, $sort_field, $desc = false){
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

/**
 * @param array $arr
 * @param string|int $field
 *
 * @return array
 */
function array_extract(array $arr, $field){
	$return = [];
	foreach ($arr as $key => $val){
		$return[$key] = $val[$field];
	}

	return $return;
}


/**
 * places items in a flat array into a multidimensional array based on keys
 *
 * @param array $arr
 * @param string $key
 * @param string $key2
 * @param string $key3
 */
function array_id(&$arr, $key, $key2 = null, $key3 = null){
	if (!is_array($arr)){
		return;
	}

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

/**
 * Inserts an item between specific keys in an array. useful when relying on the iteration order of an array
 *
 * @param array $arr
 * @param int $offset
 * @param mixed $insert
 * @param bool $before
 *
 * @return array
 */
function array_insert_assoc(array $arr, $offset, $insert, $before = false){
	$keys = array_keys($arr);
	$offset = (int) array_search($offset, $keys, true);
	if ($before && $offset>0){
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

/**
 * @param array $item
 * @param string|int $item_key
 * @param array $twod
 * @param array $include
 * @param string $ext
 * @return array
 */
function array_invert(array $item, $item_key, array &$twod, $include = [], $ext = ''){
	foreach ($item as $key => $value){
		if (empty($include) || in_array($key, $include)){
			$twod[$key.$ext][$item_key] = $value;
		}
	}

	return $twod;
}

/**
 * Shifts all array keys by a specified value (default reduces by 1)
 *
 * @param array $arr
 * @param int $num
 */
function array_key_shift(array &$arr, $num = -1){
	$temp = [];
	foreach ($arr as $key => $val){
		$key += $num;
		$temp[$key] = $val;
	}
	$arr = $temp;
	unset($temp);
}

/**
 * Returns the keys of the assoc arrays making up a list
 *
 * @param array $arr
 *
 * @return array
 */
function array_keys_2d(array $arr){
	$first_row = reset($arr);

	return array_keys($first_row);
}

/**
 * returns true if any of the keys exist in the array
 * by default uses OR condition
 * @param array $keys
 * @param array $arr
 * @param bool $and
 *
 * @return bool
 */
function array_keys_exist(array $keys, array $arr, $and = false){
	$result = $and;
	foreach ($keys as $key){
		$result = $and ? $result && array_key_exists($key, $arr) : $result || array_key_exists($key, $arr);
	}

	return $result;
}

/**
 * Verifies, recursively, existence of keys and if key values are an array
 * Will return an empty array is verified
 *
 * @param array $format
 * @param array $check
 * @param string $depth
 * @param array $errors
 *
 * @return array
 */
function array_keys_verify(array $format, array $check, $depth = '', array $errors = []){

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
		}
	}

	return $errors;
}

/**
 * takes specific keys out of an array and returns the result
 *
 * @param array $arr
 * @param array|string $pull
 * @param array $new
 *
 * @return array
 */
function array_pull(array $arr, $pull, array &$new = []){
	if (!is_array($pull)){
		$pull = explode(',', $pull);
	}

	foreach ($pull as $key => $field){
		$field = trim($field);
		$key = is_numeric($key) ? $field : $key;
		$new[$field] = $arr[$key] ?? null;
	}

	return $new;
}

/**
 * flattens multiple keys of an array into a single dimension
 *
 * @param array $arr
 * @param string $assoc
 * @param string $pre
 */
function array_oned(array &$arr, $assoc, $pre){
	if (is_array($arr[$assoc])){
		foreach ($arr[$assoc] as $key => $val){
			$arr[$pre.'-'.$key] = $val;
		}
	}
	unset($arr[$assoc]);
}

/**
 * @param array $base
 * @param array $fill
 *
 * @return array
 */
function array_overwrite(array $base, array $fill = []){
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

/**
 * @param array $arr
 *
 * @return mixed
 * @throws Exception
 */
function array_random(array $arr){
	$keys = array_keys($arr);
	$key = $keys[random_int(0, count($keys)-1)];

	return $arr[$key];
}

/**
 * @param mixed $needle
 * @param array $haystack
 *
 * @return array
 */
function array_remove($needle, array $haystack){
	$key = array_search($needle, $haystack);
	if ($key!==false){
		unset($haystack[$key]);
	}

	return $haystack;
}

/**
 * @param array $arr
 *
 * @return array
 */
function array_remove_empty(array $arr){
	foreach ($arr as $key => $val){
		if (empty($val)){
			unset($arr[$key]);
		}
	}

	return $arr;
}

/**
 * Replace all entries in $array of $search with $replace
 *
 * @param mixed $search
 * @param mixed $replace
 * @param array $array
 *
 * @return array
 */
function array_replace_value($search, $replace, array $array){
	$keys = array_keys($array, $search);
	if (!empty($keys)){
		foreach ($keys as $key){
			$array[$key] = $replace;
		}
	}

	return $array;
}

/**
 * @param mixed $needle
 * @param array $haystack
 * @param string|int $key
 * @param bool $last
 *
 * @return mixed
 */
function array_search_2d($needle, array $haystack, $key, $last = false){
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

/**
 * inserts an item into an array at a specified key without creating a new array
 *
 * @param array $arr
 * @param array|string $assign
 * @param bool $overwrite_or_val
 * @param bool $overwrite
 *
 * @return array
 */
function array_slip(array $arr, $assign, $overwrite_or_val = true, $overwrite = true){
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

/**
 * @param array $arr
 * @param mixed $slip
 * @param mixed $val
 * @return array
 */
function array_slip_2d(array $arr, $slip, $val = null){
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

/**
 * opposite of array_slip, removes one item from an array without creating a new array
 *
 * @param array $arr
 * @param string|int $key
 *
 * @return array
 */
function array_snip(array $arr, $key){
	unset($arr[$key]);

	return $arr;
}

/**
 * @param array $arr
 * @param array $order
 * @param string $glue
 * @param bool $missing
 *
 * @return string
 */
function array_stitch(array $arr, array $order, $glue = '', $missing = false){
	$new_arr = [];
	foreach ($order as $key){
		$new_arr[] = $arr[$key]??null;
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

/**
 * @param array $arr
 * @param array $order
 * @param string $glue
 *
 * @return string
 */
function array_stitch_empty(array $arr, array $order, $glue = ''){
	foreach ($order as $n => $key){
		if (empty($arr[$key])){
			unset($order[$n]);
		}
	}

	return array_stitch($arr, $order, $glue);
}

/**
 * @param array $arr
 *
 * @return array
 */
function array_strip_end(array $arr){
	$assoc = is_assoc($arr);

	$n = count($arr)-1;
	while (empty($arr[$n]) && $n>=0){
		unset($arr[$n]);
		$n--;
	}

	return $assoc ? $arr : array_values($arr);
}

/**
 * @param array $arr
 *
 * @return array
 */
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

/**
 * @param array $arr
 *
 * @return array
 */
function array_strip(array $arr){
	$arr = array_strip_start($arr);
	$arr = array_strip_end($arr);

	return $arr;
}

/**
 * @param array $arr
 * @param string|int $key1
 * @param string|int $key2
 *
 * @return array
 */
function array_switch(array $arr, $key1, $key2){
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

/**
 * builds a compressed array up into a multidimensional array
 *
 * @param array $arr
 * @param string|int $assoc
 * @param string $pre
 */
function array_twod(array &$arr, $assoc, $pre){
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

/**
 * Merges two arrays and removes duplicate values
 *
 * @param array $a
 * @param array $b
 *
 * @return array
 */
function array_union(array $a, array $b){
	return array_unique(array_merge($a, $b));
}

/**
 * Type safe unserialize that always returns array
 *
 * @param string $string
 *
 * @return array
 */
function array_unserialize($string){
	$arr = unserialize($string);
	if ($arr===false){
		$arr = [];
	}
	elseif (!is_array($arr)) {
		$arr = (array)$arr;
	}

	return $arr;
}

/**
 * unsets specific keys in an array
 *
 * @param array $arr
 * @param array $keys
 * @return array
 */
function array_unset($arr, $keys){
	foreach ($keys as $key){
		unset($arr[$key]);
	}

	return $arr;
}

/**
 * Unsets items in an array where the value is false or empty
 *
 * @param array $arr
 * @param bool $strict
 *
 * @return array
 */
function array_unset_false(array $arr, $strict = true){
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

/**
 * returns the first item of an array. Not certain where we needed this, reset($arr) does the same!
 *
 * @param array $array
 * @return string
 * @deprecated use reset($array)
 */
function arraystr(array $array){
	foreach ($array as $item){
		return (string)$item;
	}

	return '';
}

/**
 * @param string $string
 * @param mixed $item
 * @param string $sep
 *
 * @return string
 */
function append_to_string_array($string, $item, $sep = ','){
	$arr = explode($sep, $string);
	$arr[] = $item;
	$string = implode($sep, $arr);

	return $string;
}

/**
 * @param mixed $item
 *
 * @return array
 * @deprecated use (array) $item
 */
function as_array($item){
	if (!is_array($item)){
		$item = [$item];
	}

	return $item;
}

/**
 * @param array $arr
 * @param int $round
 * @return float
 */
function average(array $arr, $round = null){
	if (empty($arr)){
		return 0;
	}

	$avg = array_sum($arr) / count($arr);

	if (is_int($round)){
		$avg = round($avg, $round);
	}

	return $avg;
}

/**
 * @param array $array
 *
 * @return array
 */
function flatten_array(array $array){
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

/**
 * @param string $sep
 * @param array $arr
 *
 * @return string
 */
function implode_assoc($sep, array $arr){
	$string = '';
	foreach ($arr as $key => $val){
		$string .= $val;
		if (!is_numeric($key) and $val!=end($arr)){
			$string .= $sep;
		}
	}

	return $string;
}

/**
 * @param string $sep
 * @param array $arr1
 * @param array $arr2
 *
 * @return string
 */
function implode_dual($sep, array $arr1, array $arr2){
	$string = '';
	foreach ($arr1 as $key => $val){
		$string .= $val.$sep[1].$arr2[$key];
		if ($val!=end($arr1)){
			$string .= $sep[0];
		}
	}

	return $string;
}

/**
 * @param string $sep
 * @param array $arr
 *
 * @return string
 */
function implode_key($sep, array $arr){
	foreach ($arr as $key => &$val){
		$val = $key;
	}

	return implode($sep, $arr);
}

/**
 * @param string $pre
 * @param string $suf
 * @param array $arr
 *
 * @return string
 */
function implode_wrap($pre, $suf, $arr){
	$html = '';
	if (is_array($arr)){
		foreach ($arr as $val){
			$html .= $pre.$val.$suf;
		}
	}

	return $html;
}

/**
 * @param mixed $arr
 *
 * @return bool
 */
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

/**
 * @param mixed $arr
 *
 * @return bool
 */
function is_array_true($arr){
	if (!is_array($arr)){
		return false;
	}

	foreach ($arr as $item){
		if ($item!==false){
			return true;
		}
	}

	return false;
}

/**
 * @param mixed $arr
 *
 * @return bool
 */
function is_assoc(&$arr){
	if (is_array($arr)){
		return !(array_values($arr)===$arr);
	}

	return false;
}

/**
 * @param string $string
 * @param bool $throw
 *
 * @return array
 * @throws Exception
 */
function json_as_array($string, $throw = false){
	$arr = json_decode($string, true);

	if (!$arr && $throw){
		throw new Exception("The data could not be decoded, parser reported: ".json_last_error_msg()." The data was ".$string);
	}

	return $arr ?: [];
}

/**
 * @param array[] ...$arrays
 *
 * @return mixed
 */
function largest_array(array ...$arrays){
	$large = 0;
	$largest = 0;
	foreach ($arrays as $key => $array){
		$count = count($array);
		if ($count>$large){
			$large = $count;
			$largest = $key;
		}
	}

	return $arrays[$largest];
}

/**
 * @param array[] ...$arrays
 *
 * @return int
 */
function largest_array_count(array ...$arrays){
	$arrays = func_get_args();
	$large = 0;
	foreach ($arrays as $array){
		$count = count($array);
		if ($count>$large){
			$large = $count;
		}
	}

	return $large;
}

/**
 * @param int $count
 *
 * @return array
 */
function make_array($count){
	$arr = [];
	for ($n = 0; $n<$count; $n++){
		$arr[$n] = 1;
	}

	return $arr;
}

/**
 * @param int $to
 * @param int $start
 *
 * @return array
 */
function number_list($to = 10, $start = 1){
	$arr = [];
	for ($n = $start; $n<=$to; $n++){
		$arr[] = $n;
	}

	return $arr;
}

/**
 * @param string $sep
 * @param array $arr
 * @param string $assoc
 *
 * @return string
 */
function super_implode($sep, array $arr, $assoc){
	$temp = [];
	foreach ($arr as $val){
		$temp[] = $val[$assoc];
	}
	$temp = implode($sep, $temp);

	return $temp;
}
