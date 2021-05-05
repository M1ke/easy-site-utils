<?php
function create_field($field, bool $null_default = false): string{
	$field['title'] = '`'.string_check($field['title']).'`';
	if (!$null_default && $field['null_default']){
		$null_default = true;
	}
	$default = '';
	switch ($field['type']){
		case 'choice':
		case 'enum':
			$type = " enum('".implode("','", $field['choices'])."')";
			$default = "'{$field['choices'][0]}'";
		break;
		case 'date':
		case 'datetime':
			$type = ' '.$field['type'];
			$default = "'0000-00-00".($field['type']==='datetime' ? ' 00:00:00' : '')."'";
		break;
		case 'decimal':
		case 'float':
			$type = ' '.$field['type'];
			if (!isset($field['length'])){
				$field['length'] = '4,2';
			}
			$type .= '('.$field['length'].')';
			$default = '0';
		break;
		case 'int':
			$type = ' bigint(20)';
			$default = '0';
		break;
		case 'single':
			$type = ' tinyint(1)';
			$default = '0';
		break;
		case 'blob':
			$type = ' blob';
		break;
		case 'mediumtext':
			$type = ' mediumtext collate utf8_unicode_ci';
		break;
		case 'text':
			$type = ' text collate utf8_unicode_ci';
		break;
		case 'timestamp':
			$type = " TIMESTAMP ON UPDATE CURRENT_TIMESTAMP";
			$field['default'] = "CURRENT_TIMESTAMP";
		break;
		case 'point':
			$type = " POINT";
		break;
		case 'polygon':
			$type = " POLYGON";
		break;
		default:
			$type = ' varchar';
			if (!isset($field['length'])){
				$field['length'] = 100;
			}
			$type .= '('.$field['length'].') collate utf8_unicode_ci';
	}
	$null = ' '.($field['null'] ? '' : 'NOT ').'NULL';
	if (isset($field['default'])){
		if ($field['type']!=='timestamp'){
			$field['default'] = "'".$field['default']."'";
		}
		$default = " default {$field['default']}";
	}
	elseif (!$field['null'] && $null_default){
		$default = " default $default";
	}
	else {
		$default = '';
	}
	if ($field['auto']==1){
		$auto = ' auto_increment';
	}
	else {
		$auto = '';
	}

	return $field['title'].$type.$null.$default.$auto;
}

function create_table(&$table, &$error, $engine = 'MyISAM'){
	if (is_array($table['fields'])){
		foreach ($table['fields'] as $title => $field){
			if ($field['db']){
				$field = $field['db'];
			}
			$field['title'] = $title;
			$field_query = create_field($field, (bool) $table['null_default']);

			$fields[] = $field_query;
		}
	}
	else {
		$error = 'The table '.$table['title'].' does not have any valid fields and cannot be created.';

		return false;
	}
	$table['title'] = string_check($table['title']);
	if (isset($table['primary']) && isset($table['fields'][$table['primary']])){
		$fields[] = ' PRIMARY KEY  (`'.$table['primary'].'`)';
	}
	$fields = implode(',', $fields);

	return "CREATE TABLE IF NOT EXISTS `{$table['title']}` ($fields) ENGINE=$engine DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;";
}

function db_update($new, $old, &$out = null, $echo = null, $engine = 'MyISAM'){
	foreach ($new as $new_table_title => $new_table){
		$new_table['title'] = $new_table_title;
		if (is_array($old[$new_table_title])){
			$old_fields = $old[$new_table_title]['fields'];
			foreach ($new_table['fields'] as $new_field_title => $new_field){
				if (isset($new_field['db'])){
					$new_field = $new_field['db'];
				}
				if (!is_array($old_fields[$new_field_title])){
					$alter = false;
					if (is_array($new_field['prev'])){
						foreach ($new_field['prev'] as $old_field_title){
							if (is_array($old_fields[$old_field_title])){
								$new_field['title'] = $new_field_title;
								$new_field_query = create_field($new_field);

								$queries[] = "ALTER TABLE `{$new_table['title']}` CHANGE `$old_field_title` $new_field_query";
								$alter = true;
							}
						}
					}
					if (!$alter){
						$new_field['title'] = $new_field_title;
						$new_field_query = create_field($new_field);

						$queries[] = "ALTER TABLE `{$new_table['title']}` ADD $new_field_query";
					}
				}
				else {
					$old_field = $old_fields[$new_field_title];
					$alter = false;
					foreach ($new_field as $new_field_param => $new_field_val){
						switch ($new_field_param){
							case 'prev':
							break;
							default:
								if ($old_field[$new_field_param]!=$new_field_val){
									$alter = true;
								}
						}
					}
					if ($alter){
						$new_field['title'] = $new_field_title;
						$new_field_query = create_field($new_field);
						$queries[] = "ALTER TABLE `{$new_table['title']}` CHANGE `$new_field_title` $new_field_query";
					}
				}
			}
			// dropping fields is probably a bad idea in case an update goes wrong
			// the developer has responsibility but the framework shouldn't be so destructive
			// foreach ($old_fields as $old_field_title => $old_field){
			// 	if (!isset($new_table['fields'][$old_field_title])){
			// 		$queries[]="ALTER TABLE `".$new_table['title']."` DROP `".$old_field_title."`";
			// 	}
			// }
			if (is_array($new_table['index'])){
				foreach ($new_table['index'] as $index){
					$index_name = explode(',', $index);
					$index_name = $index_name[0];
					$new_table['index_names'][] = $index_name;
					$index = str_replace(',', '`,`', $index);
					if (!@in_array($index_name, $old[$new_table['title']]['index'])){
						$queries[] = "ALTER TABLE `{$new_table['title']}` ADD INDEX (`$index`)";
					}
				}
			}
			if (is_array($old[$new_table['title']]['index'])){
				foreach ($old[$new_table['title']]['index'] as $index){
					$index_name = explode(',', $index);
					$index_name = $index_name[0];
					if (!@in_array($index_name, $new_table['index_names'])){
						$queries[] = "ALTER TABLE `{$new_table['title']}` DROP INDEX `$index`";
					}
				}
			}
		}
		elseif (is_array($new_table['prev'])) {
			foreach ($new_table['prev'] as $old_table_title){
				if (is_array($old[$old_table_title])){
					$queries[] = "RENAME TABLE `$old_table_title` TO `{$new_table['title']}`";
				}
			}
		}
		else {
			$create = create_table($new_table, $error, $engine);
			if (empty($create)){
				error($error);
			}
			$queries[] = $create;
			if (is_array($new_table['index'])){
				foreach ($new_table['index'] as $index){
					$index = str_replace(',', '`,`', $index);
					$queries[] = "ALTER TABLE `{$new_table['title']}` ADD INDEX (`$index`)";
				}
			}
		}
		if (is_array($new_table['triggers'])){
			foreach ($new_table['triggers'] as $trigger_type => $trigger){
				$trigger_name = $new_table['title'].'_'.$trigger_type;
				// need to ensure it doesn't crash the process if user lacks privilege or trigger exists
				// $queries[]="CREATE TRIGGER `$trigger_name` BEFORE $trigger_type ON `{$new_table['title']}` FOR EACH ROW $trigger";
			}
		}
	}
	log_file($queries, 'DB Update', 'db.log');
	if (!$echo){
		if (is_array($queries)){
			foreach ($queries as $query){
				$query = query($query, null, null, null, null, true);
				$out .= $query.'<br/>';
			}
		}
	}
	else {
		echo implode(";\n\n", $queries);
		die;
	}
}

function field_type(&$type, &$length, &$choices = null){
	switch ($type){
		case 'coord':
			$type = 'decimal';
			$length = '18,16';
		break;
		case 'float':
			$type = 'float';
		break;
		case 'bigint':
		case 'smallint':
		case 'int':
			$type = 'int';
			$length = null;
		break;
		case 'tinyint':
			$type = 'single';
			if ($length==1){
				$length = null;
				$type = 'single';
			}
			else {
				$type = 'int';
			}
		break;
		case 'datetime':
			$type = 'datetime';
		break;
		case 'date':
			$type = 'date';
		break;
		case 'text':
			$type = 'text';
		break;
		case 'enum':
			$type = 'choice';
			$length = substr($length, 1, strlen($length)-2);
			$choices = explode("','", $length);
		break;
		case 'timestamp':
			$type = 'timestamp';
		break;
		default:
			$type = 'string';
			if ($length==100){
				$length = null;
			}
	}
}

function need_table($table){
	$table = string_check($table);
	$check = query("SHOW TABLES LIKE '$table'", 'single');
	if ($check!=$table){
		error('The database "'.$table.'" could not be found and is required for this page to function. Please make sure the extension you are trying to use has installed properly.');
	}

	return true;
}

function query_insert(array $arr, $exc = [], $repeat = false){
	foreach ($arr as $key => $val){
		if ($val!==false and strpos($key, '-')===false and !in_array($key, $exc) and !is_array($val)){
			$fields[] = "`$key`";
			$val = sql_slashes($val);
			$vals[] = "'$val'";
		}
	}

	return query_insert_make($fields, $vals, $repeat);
}

function query_insert_make(array $fields, array $vals, $repeat = false){
	$vals = "(".implode(',', $vals).")";
	if (!empty($repeat)){
		for ($n = 0; $n<$repeat; $n++){
			$vals_arr[] = $vals;
		}
		$vals = implode(',', $vals_arr);
	}
	$query = "(".implode(',', $fields).") VALUES $vals";

	return $query;
}

function query_insert_inc(array $arr, array $inc, $repeat = false){
	foreach ($arr as $key => $val){
		if (in_array($key, $inc) and $val!==false and !is_null($val)){
			$fields[] = "`$key`";
			$val = sql_slashes($val);
			$vals[] = "'$val'";
		}
	}

	return query_insert_make($fields, $vals, $repeat);
}

function query_update($arr, $exc = []){
	foreach ($arr as $key => $val){
		if ($val!==false and strpos($key, '-')===false and !in_array($key, $exc) and !is_array($val)){
			$val = sql_slashes($val);
			$query[] = "`$key`='$val'";
		}
	}
	$query = implode(',', $query);

	return $query;
}

function query_update_inc($arr, $inc){
	foreach ($arr as $key => $val){
		if (in_array($key, $inc) and $val!==false and !is_null($val)){
			$val = sql_slashes($val);
			$query[] = "`$key`='$val'";
		}
	}
	$query = implode(',', $query);

	return $query;
}

function table_array($table){
	$fields = [];
	$cols = query("SHOW columns FROM `$table`", '2d');
	foreach ($cols as $col){
		$field = [];
		$bracket = strpos($col['Type'], '(');
		if ($bracket>0){
			$bracket++;
			$field['length'] = substr($col['Type'], $bracket, strpos($col['Type'], ')')-$bracket);
			$field['type'] = substr($col['Type'], 0, $bracket-1);
		}
		else {
			$field['type'] = $col['Type'];
		}
		field_type($field['type'], $field['length']);
		if (empty($field['length'])){
			unset($field['length']);
		}
		if (strlen($col['Default'])>0){
			$field['default'] = $col['Default'];
		}
		if ($col['Extra']=='auto_increment'){
			$field['auto'] = 1;
		}
		if ($col['Key']=='PRI'){
			$arr['primary'] = $col['Field'];
		}
		$fields[$col['Field']] = $field;
	}
	$arr['fields'] = $fields;
	$indexes = query("SHOW INDEX FROM `$table` WHERE Key_name<>'PRIMARY'", '2d');
	foreach ($indexes as $index){
		$arr['index'][] = $index['Key_name'];
	}

	return $arr;
}
