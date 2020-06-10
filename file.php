<?php

/**
 * @param string $path
 * @param bool $recursive
 * @param int $chmod
 *
 * @return bool
 */
function create_dir($path, $recursive = true, $chmod = 0775){
	// NB: people disagree with using '@' to silence warnings;
	// however in this case it is used sensibly as the previous check
	// should have eliminated the possibility of the directory already
	// eisting, and the method will return false if the creation fails,
	// so the calling scope can be aware. Where possible libraries
	// should not cause unexpected warnings to appear, so this meets
	// that requirement as well
	if (!is_dir($path) && !@mkdir($path, $chmod, $recursive)){
		return false;
	}

	return true;
}

/**
 * @param string $path
 *
 * @return array
 */
function dir_list_files($path){
	if (!is_dir($path)){
		return [];
	}

	$dir = opendir($path);
	$files = [];
	if (!empty($dir)){
		while (false!==($file = readdir($dir))){
			if ($file!=='.' && $file!=='..'){
				$files[] = $file;
			}
		}
		closedir($dir);
		sort($files);
	}

	return $files;
}

/**
 * @param string $path
 * @param array $list
 * @param string $prefix
 *
 * @return array
 */
function dir_list_recursive($path, array $list = [], $prefix = ''){
	$files = dir_list_files($path);

	foreach ($files as $file){
		$file_path = $path.'/'.$file;
		if (is_dir($file_path)){
			$list = dir_list_recursive($file_path, $list, $prefix.$file.'/');
		}
		else {
			$list[] = $prefix.$file;
		}
	}
	sort($list);

	return $list;
}

/**
 * @param array $dir
 * @param string $root
 *
 * @return bool
 */
function dir_tree($dir, $root = null){
	foreach ($dir as $name => $sub){
		$name = $root.$name;
		if (!file_exists($name)){
			@mkdir($name);
		}
		if (is_array($sub)){
			dir_tree($sub, $name.'/');
		}
	}

	return true;
}

/**
 * @param string $file
 *
 * @return bool
 */
function file_delete($file){
	if (!file_exists($file)){
		return true;
	}
	if (!is_writable($file)){
		return false;
	}
	$fh = fopen($file, 'w');
	if (empty($fh)){
		return false;
	}
	fclose($fh);
	@unlink($file);

	return !file_exists($file);
}

/**
 * @param string $url
 * @param bool $throw_on_error
 *
 * @return array|mixed
 * @throws Exception
 */
function file_get_json($url, $throw_on_error = true){
	if (substr($url, 0, 4)!='http'){
		$url = 'http://'.$url;
	}
	$string = file_get_contents($url);

	return file_parse_json($string, $throw_on_error);
}

function file_load($file, $serialize = false){
	if (!is_readable($file)){
		return false;
	}
	$size = filesize($file);
	if (empty($size)){
		return '';
	}
	$fh = fopen($file, 'r');
	$string = '';
	if (!empty($fh)){
		$string = fread($fh, $size);
		fclose($fh);
	}

	return $serialize ? unserialize($string) : $string;
}

function file_load_json($file, $throw_on_error = true){
	$string = file_load($file);

	return file_parse_json($string, $throw_on_error);
}

function file_output($file_path, $mime = null, $file_name = null){
	if (empty($file_name)){
		$file_name = substr_after($file_path, '/');
	}
	header('Content-type: '.$mime);
	header('Content-Disposition: attachment; filename="'.$file_name.'"');
	header('Content-Transfer-Encoding: binary');
	// header('Content-Length: '.filesize(DIR.$summary_pdf));
	header('Accept-Ranges: bytes');
	ob_clean();
	flush();
	readfile($file_path);
}

/**
 * @param $string
 * @param bool $throw_on_error
 * @return array|mixed
 * @throws Exception
 */
function file_parse_json($string, $throw_on_error = true){
	if (empty($string)){
		return [];
	}
	$json = json_decode($string, true);
	if ($json===null){
		if ($throw_on_error){
			throw new \Exception('The JSON could not be parsed correctly: "'.json_error_msg().'"');
		}
		$json = [];
	}

	return $json;
}

function file_save($file, $string, $overwrite = false){
	if (is_array($string)){
		$string = serialize($string);
	}

	return file_save_($file, $string, $overwrite);
}

function file_save_json($file, $string, $overwrite = true, $pretty = false){
	if (is_array($string)){
		$string = json_encode($string, $pretty);
	}

	return file_save_($file, $string, $overwrite);
}

function file_save_($file, $string, $overwrite){
	$dir = dirname($file);
	if (!is_dir($dir)){
		mkdir($dir, 0755, true);
	}
	if (file_exists($file) && !is_writable($file)){
		return false;
	}
	$fh = @fopen($file, $overwrite ? 'w' : 'a');
	if (empty($fh)){
		return false;
	}
	fwrite($fh, $string);
	fclose($fh);

	return true;
}

function file_store($id, $params, &$error = null){
	if (!file_exists($params['in'])){
		$error = 'The file "'.$params['in'].'" could not found to copy.';

		return false;
	}

	$out = $params['out'].$id.(!empty($params['ext']) ? '.'.$params['ext'] : '');

	$dirname = dirname($out);
	if (!is_dir($dirname)){
		$error = "The directory '$dirname' did not exist to copy in to.";

		return false;
	}

	if (!copy($params['in'], $out)){
		$error = 'The file was uploaded but could not be stored. The file "'.$params['in'].'" could not be copied to "'.$out.'".';

		return false;
	}

	return true;
}

function file_types($check, $file_types = null){
	if (empty($file_types)){
		$file_types = ['pdf', 'doc', 'xls', 'ppt', 'docx', 'xlsx', 'pptx', 'mp3', 'jpeg', 'jpg', 'png', 'avi', 'mov', 'mpg', 'mpeg', 'mp4', '3gs', 'wmv', 'asx'];
	}

	return in_array($check, $file_types);
}

function file_valid($file, &$error, $file_types = null){
	$new_file['temp'] = $file['tmp_name'];
	$dot = strrpos($file['name'], '.');
	$new_file['ext'] = strtolower(substr($file['name'], $dot+1));
	if (file_types($new_file['ext'], $file_types)){
		return $new_file;
	}

	$error = 'The file you have uploaded is the wrong file type ('.$new_file['ext'].').';

	return false;
}

function folder_copy($src, $dst, $recurse = true){
	$dir = opendir($src);
	if (!empty($dir)){
		@mkdir($dst);
		while (false!==($file = readdir($dir))){
			if ($file!=='.' && $file!=='..'){
				if (is_dir($src.'/'.$file)){
					if ($recurse){
						folder_copy($src.'/'.$file, $dst.'/'.$file);
					}
				}
				else {
					copy($src.'/'.$file, $dst.'/'.$file);
				}
			}
		}
		closedir($dir);
	}
}

// deprecated
function get_file($filename){
	return file_load($filename);
}

function get_max_upload_size(){
	return min(php_size_to_bytes(ini_get('post_max_size')), php_size_to_bytes(ini_get('upload_max_filesize')));
}

// Taken from http://stackoverflow.com/a/22500394/518703
function php_size_to_bytes($sSize){
	if (is_numeric($sSize)){
		return $sSize;
	}
	$sSuffix = substr($sSize, -1);
	$iValue = substr($sSize, 0, -1);
	switch (strtoupper($sSuffix)){
		case 'P':
			$iValue *= 1024;
		case 'T':
			$iValue *= 1024;
		case 'G':
			$iValue *= 1024;
		case 'M':
			$iValue *= 1024;
		case 'K':
			$iValue *= 1024;
		break;
	}

	return $iValue;
}
//
