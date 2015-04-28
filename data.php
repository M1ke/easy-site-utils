<?php
function data_valid($fields){
	foreach ($fields as $key => $field){
		if (count($field)>1){
			$temp[]=$key;
		}
	}
	return $temp;
}

function valid_copy(&$p,&$error=null,$valid=null){
	if (isset($p[$valid['copy']])){
		$p[$valid['_input']]=$p[$valid['copy']];
	}
	return true;
}

function valid_default(&$p,&$error=null,$valid=null){
	$p[$valid['_input']]=!isset($p[$valid['_input']]) ? $valid['default'] : string_check($p[$valid['_input']]);
	return true;
}

function valid_age_dob(&$p,&$error=null,$valid=null){
	if (!empty($p[$valid['dob']])){
		$p[$valid['_input']]=age_from_dob($p[$valid['dob']]);
	}
	return true;
}

function valid_geocode(&$p,&$error=null,$valid=null){
	if (!empty($p[$valid['geocode']])){
		geo_coord($p[$valid['geocode']],$p);
	}
	return true;
}

function valid_map(&$p,&$error=null,$valid=null){
	return true;
}

function valid_address(&$p,&$error=null,$valid=null){
	$val=$p[$valid['_input']];
	if (!is_array($val)){
		$val=str_replace(array("\r","\n","\r\n",', '),',',$val);
		$val=explode(',',$val);
	}
	$lines=0;
	foreach ($val as &$line){
		$line=string_check($line);
		if (!empty($line)){
			$lines++;
		}
	}
	if (!empty($valid['lines']) and $lines<$valid['lines']){
		$error=!empty($valid['msg']) ? $valid['msg'] : 'You must enter at least '.$valid['lines'].' lines.';
		return false;
	}
	$p[$valid['_input']]=implode(', ',$val);
	return true;
}

function valid_permalink(&$p,&$error=null,$valid=null){
	$p[$valid['_input']]=make_permalink($p[$valid['permalink']]);
	return true;
}

function valid_same(&$p,&$error=null,$valid=null){
	if ($p[$valid['_input']]!=$p[$valid['same']]){
		$error=!empty($valid['msg']) ? $valid['msg'] : 'This must be the same as the value above ('.$p[$valid['same']].').';
		return false;
	}
	return true;
}

function valid_same_anon(&$p,&$error=null,$valid=null){
	if ($p[$valid['_input']]!=$p[$valid['same']]){
		$error=!empty($valid['msg']) ? $valid['msg'] : 'This must be the same as the value above.';
		return false;
	}
	return true;
}

function validate($validate,&$p=null,&$errors=null,$type=null,$clear=false){
	$checked=array();
	// this has to be done at the start as func validators may add new keys that are permitted
	if ($clear){
		foreach ($p as $key => &$val){
			if (!isset($validate[$key])){
				$val=false;
			}
		}
	}
	foreach ($validate as $input => $valid){
		$error=false;
		if ($input=='__primary'){
			continue;
		}
		// this bit caters for the improved prototype model
		// each data item contains db, and can contain form and valid
		if (isset($valid['db'])){
			if (!is_array($valid['valid'])){
				if (isset($valid[$type])){
					if (function_exists($valid[$type])){
						$p[$input]=$valid[$type]();
					}
					else {
						$p[$input]=$valid[$type];
					}
				}
				elseif (isset($p[$input])){
					$p[$input]=false;
				}
				continue;
			}
			else {
				$valid=$valid['valid'];
			}
		}
		$valid['_input']=$input;
		// if they havent submitted it and there's no processing (which can handle the requirement)
		// then we need to check if it is needed
		if (!isset($p[$input]) and empty($valid['func'])){
			// copying and checking null still doesn't avoid requirement for need
			// as sometimes we can end up with a function returning blank which is good, otehr times bad
			// we either need to ensure all validation checks the variables in play first, 
			// or we need to specify need for items that might not be submitted
			if (!empty($valid['need'])){
				$copy=$p;
				if (!validate_input($valid,$copy,$error)){
					$errors[$input]=$error;
				}
				if (!is_null($copy[$input])){
					$p[$input]=$copy[$input];
				}
			}
		}
		else {
			if (is_array($p[$input]) and $valid['type']!='func'){
				validate_input_array($valid,$p[$input],$errors);
				if (!empty($valid['serialize'])){
					$p[$input]=serialize($p[$input]);
				}
				if (!empty($valid['json_encode'])){
					$p[$input]=json_encode($p[$input]);
				}
				if (isset($valid['implode'])){
					$p[$input]=string_check(implode($valid['implode'],$p[$input]));
					if (!isset($valid['blank']) and empty($p[$input])){
						$errors[$input]='You must enter a value';
					}
				}
			}
			else {
				if (!validate_input($valid,$p,$error)){
					$errors[$input]=$error;
				}
			}
			$checked[$input]=true;
		}
	}
	if (!empty($errors)){
		return false;
	}
	return true;
}

function validate_input_array($valid,&$val,&$errors){
	foreach ($val as $key => &$item){
		if (is_array($item)){
			validate_input_array($valid,$item,$errors);
		}
		else {
			validate_input($valid,$item,$error);
			if (!empty($error)){
				$errors[$valid['_input']][$key]=$error;
			}
		}
	}
}

// need to write a more robust way of handling array input in the form creation stage, link to validator, avoid '[]'
function validate_input($valid,&$p,&$error){
	$error=null;
	if ($valid['type']!='func'){
		if (is_array($p)){
			$val=&$p[$valid['_input']];
		}
		else {
			$val=&$p;
		}
	}
	switch ($valid['type']){
		case 'address':
			$val=string_check($val);
			if (empty($valid['blank']) and strlen($val)<4){
				$error=!empty($valid['msg']) ? $valid['msg'] : 'You must enter a valid address.';
			}
			if (!empty($valid['lines']) and !empty($val) and substr_count($val,"\n")<($valid['lines']-1)){
				$error='This address must contain at least '.$valid['lines'].' lines.';
			}
			if (!empty($valid['format'])){
				$val=str_replace(array("\r","\n","\r\n",', '),',',$val);
			}
		break;
		case 'array':
		case 'choice':
		case 'select':
		// $val can't be an array at this point as that's sorted higher up by validate_input_array()
			if (!is_array($valid['options']) and function_exists($valid['options'])){
				$valid['options']=$valid['options']();
			}
			if (is_array($valid['options'])){
				if (is_assoc($valid['options'])){
					$err=(!@isset($valid['options'][$val]));
				}
				else {
					$err=(!in_array($val,$valid['options']));
				}
			}
			elseif (isset($valid['no-opts'])){
				$val='';
			}
			else {
				$err=true;
				$valid['msg']='The options could not be found for this field.';
			}
			if (isset($valid['not-empty']) and empty($val)){
				$err=true;
			}
			if (!empty($err)){
				if (!empty($valid['blank'])){
					$val='';
				}
				elseif (!empty($valid['msg'])){
					$error=$valid['msg'];
				}
				else {
					$error='You must select one of the available options.';
				}
			}
		break;
		case 'bool':
		case 'boolean':
			if (!empty($val)){
				$val=!empty($valid['set']) ? $valid['set'] : 1;
			}
			elseif (!empty($valid['mandatory'])){
				$error='You must tick this box to continue.';
			}
			else {
				$val=!empty($valid['empty']) ? $valid['empty'] : 0;
			}
		break;
		case 'clear':
			$val=false;
		break;
		// we can't do this because of the isset check in valid; use the func method to point to valid_copy instead
		// case 'copy':
			// $val=$p[$valid['copy']];
		// break;
		case 'currency':
			if (!make_currency($val,$valid['blank'] ? 1 : false)){
				$error=!empty($valid['msg']) ? $valid['msg'] : 'You must enter a valid currency value';
			}
			if (!empty($valid['positive']) and $val<0){
				$val*=-1;
			}
		break;
		case 'dat':
		case 'date':
			// we had to be careful here, as when we moved to a function with &$error
			// it started adding the error even if we planned to ignore it
			// use $err in these cases but might be better to pass on the blank flag
			// to sub functions of the validator
			$func='sql_'.$valid['type'];
			$val=$func($val,$err);
			$today_date=date('Y-m-d');
			if (empty($val)){
				if (!empty($valid['blank'])){
					$val=$valid['blank']=='today' ? $today_date : '';
				}
				else {
					$error=!empty($err) ? $err : 'The date you entered was not recognised';
				}
			}
			else {
				if (!empty($valid['past'])){
					$valid['max']=$today_date;
				}
				if (!empty($valid['future'])){
					$valid['min']=$today_date;
				}
				if (!empty($valid['max']) and $val>$valid['max']){
					$error='The date specified is greater than the maximum allowed.';
				}
				if (!empty($valid['min']) and $val<$valid['min']){
					$error='The date specified is less than the minimum allowed.';
				}
			}
		break;
		case 'dob':
			if (!empty($val)){
				$val=date_from_dob($val);
			}
			if (empty($val) and empty($valid['blank'])){
				if (!empty($valid['msg'])){
					$error=$valid['msg'];
				}
				else {
					$error='You must enter a valid date of birth, try '.(defined(DATE_USA) ? 'mm/dd/yy' : 'dd/mm/yy').'.';
				}
			}
			if (isset($valid['max']) or isset($valid['min'])){
				$age=age_from_dob($val);
				if (!empty($valid['max']) and $age>$valid['max']){
					$error='This date of birth indicates an age of '.$age.'. It is required that the age is '.$valid['max'].' or less.';
				}
				if (!empty($valid['min']) and $age<$valid['min']){
					$error='This date of birth indicates an age of '.$age.'. It is required that the age is '.$valid['min'].' or more.';
				}
			}
			if ($val>date('Y-m-d')){
				$error='A date of birth may not be in the future. If time travel has been invented, please let us know last year.';
			}
		break;
		case 'email':
			if (!make_email($val,($valid['blank'] ? 1 : false))){
				$error=!empty($valid['msg']) ? $valid['msg'] : 'You must enter a valid email address.';
			}
		break;
		case 'equal':
			if (!string_compare($val,$valid['equal'])){
				$error=!empty($valid['msg']) ? $valid['msg'] : 'You must enter the exact value.';
			}
		break;
		// this isn't really a data type, could be removed now that we can accept arrays
		case 'extra':
			$extra=array();
			if (is_array($val['key'])){
				foreach ($val['key'] as $n => $key){
					$extra[string_check($key)]=string_check($val['val'][$n]);
				}
			}
			$val=serialize($extra);
		break;
		case 'html':
			$val=make_html($val,$valid['tags'],!empty($valid['multi_byte']) ? true : false);
			if ($valid['length']>0){
				if (strlen($val)<$valid['length']){
					$error=!empty($valid['msg']) ? $valid['msg'] : 'You must enter a value at least '.($valid['length']==1 ? '1 character' : $valid['length'].' characters.').' long';
				}
			}
		break;
		case 'image':
		break;
		case 'keygen':
			if (empty($val) and empty($valid['regen'])){
				$val=rand_pass();
			}
		break;
		case 'name':
			$val=make_name($val);
			if (empty($valid['blank']) and empty($val)){
				$error=!empty($valid['msg']) ? $valid['msg'] : 'You must enter a valid name.';
			}
		break;
		case 'num':
		case 'number':
			if (!is_number($val,($valid['blank'] ? 1 : false))){
				if (!empty($valid['default'])){
					$val=$valid['default'];
				}
				else {
					$error=!empty($valid['msg']) ? $valid['msg'] : 'You must enter a valid number.';
				}
			}
			if (!empty($val)){
				// for legacy support
				if (isset($valid['ulimit'])) $valid['max']=$valid['ulimit'];
				if (isset($valid['dlimit'])) $valid['min']=$valid['dlimit'];
				//
				if (isset($valid['max']) and $val>$valid['max']){
					$error='You must enter a number no greater than '.$valid['max'].'.';
				}
				if (isset($valid['min']) and $val<$valid['min']){
					$error='You must enter a number no lower than '.$valid['min'].'.';
				}
				if (isset($valid['max-other']) and $val>$p[$valid['max-other']]){
					$error='You must enter a number no greater than '.$p[$valid['max-other']].'.';
				}
			}
		break;
		case 'phone':
			if (isset($valid['other'])){
				$error=(!make_phones($val,$p[$valid['other']]));
			}
			else {
				$error=(!make_phone($val,$valid['blank'] ? 1 : false));
			}
			if (!empty($error)){
				$error=!empty($valid['msg']) ? $valid['msg'] : 'You must enter a valid phone number.';
			}
		break;
		case 'postcode':
			if (!make_postcode($val,($valid['blank'] ? 1 : false))){
				$error=!empty($valid['msg']) ? $valid['msg'] : 'You must enter a valid postcode.';
			}
		break;
		case 'time':
			if (!make_time($val,$valid['blank'] ? 1 : false,$valid['format'] ? $valid['format'] : null)){
				$error=!empty($valid['msg']) ? $valid['msg'] : 'You must enter a valid time.';
			}
		break;
		case 'url':
		case 'website':
			if (!make_website($val,$valid['blank'] ? 1 : false)){
				$error=!empty($valid['msg']) ? $valid['msg'] : 'You must enter a valid website address.';
			}
			if (is_array($valid['unique'])){
				$check=query("SELECT ".$valid['unique']['id']." FROM ".$valid['unique']['table']." WHERE website='$val'",'single');
				if ($check>0){
					$error='The website address you entered is already registered.';
				}
			}
		break;
		case 'func':
			$func=$valid['func'];
			if (function_exists($func)){
				if (!$func($p,$err,$valid)){
					$error=!empty($valid['msg']) ? $valid['msg'] : $err;
				}
				break;
			}
		default:
			if (!empty($val)){
				$val=string_check($val,$valid['strip']);
			}
			if (!empty($valid['length'])){
				if (strlen($val)<$valid['length']){
					$error=!empty($valid['msg']) ? $valid['msg'] : 'You must enter a value at least '.($valid['length']==1 ? '1 character' : $valid['length'].' characters.').' long';
				}
			}
			elseif (!empty($valid['default']) and empty($val)){
				$val=$valid['default'];
			}
			if (!empty($valid['max']) and $strlen>$valid['max']){
				$error='You may not enter a value longer than '.$valid['max'].' characters.';
			}
	}
	validate_unique($valid,$val,$error);
	if ($error){
		return false;
	}
	return true;
}

function validate_unique($valid,$val,&$error){
	/* needs as part of ['unique']
		table
		field (default to _input)
		except && id
	*/
	if (is_array($valid['unique']) and !empty($val)){
		$check=query("SELECT count(*) FROM ".$valid['unique']['table']." WHERE ".(isset($valid['unique']['field']) ? $valid['unique']['field'] : $valid['_input'])."='$val'".(!empty($valid['unique']['except']) ? " and ".$valid['unique']['id']."<>'".$valid['unique']['except']()."'" : '').(!empty($valid['unique']['status']) ? " and status<>0" : ''),'single');
		if (!empty($check)){
			$error=!empty($valid['unique']['error']) ? $valid['unique']['error'] : 'The value you entered is already present.';
		}
	}
}
