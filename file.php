<?php
function dir_list_files($path){
	$dir=opendir($path);
	$files=[];
	if (!empty($dir)){
		while (false!==($file=readdir($dir))){
			if ($file!='.' and $file!='..'){
				$files[]=$file;
			}
		}
		closedir($dir);
	}
	return $files;
}

function dir_tree($dir,$root=null){
	foreach ($dir as $name => $sub){
		$name=$root.$name;
		if (!file_exists($name)){
			mkdir($name);
		}
		if (is_array($sub)){
			dir_tree($sub,$name.'/');
		}
	}
	return true;
}

function file_delete($file){
	if (!file_exists($file)){
		return true;
	}
	if (!is_writable($file)){
		return false;
	}
	$fh=fopen($file,'w');
	if (empty($fh)){
		return false;
	}
	fclose($fh);
	unlink($file);
	return true;
}

function file_load($file,$serialize=false){
	if (!is_readable($file)){
		return false;
	}
	$fh=fopen($file,'r');
	if (!empty($fh)){
		$string=fread($fh,filesize($file));
		fclose($fh);
	}
	return $serialize ? unserialize($string) : $string;
}
// deprecated
function get_file($filename){
	return file_load($filename);
}

function file_output($file_path,$mime=null,$file_name=null){
	if (empty($file_name)){
		$file_name=substr_after($file_path,'/');
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

function file_save($file,$string,$overwrite=false){
	if (is_array($string)){
		$string=serialize($string);
	}
	return file_save_($file,$string,$overwrite);
}
function file_save_($file,$string,$overwrite){
	if (file_exists($file) and !is_writable($file)){
		return false;
	}
	$fh=fopen($file,$overwrite ? 'w' : 'a');
	if (empty($fh)){
		return false;
	}
	fwrite($fh,$string);
	fclose($fh);
	return true;
}

function file_store($id,$params,&$error=null){
	$out=$params['out'].$id.(!empty($params['ext']) ? '.'.$params['ext'] : '');
	if (!copy($params['in'],$out)){
		$error='The file was uploaded but could not be stored. The file "'.$params['in'].'" could not be copied to "'.$out.'".';
		return false;
	}
	return true;
}

function file_types($check,$file_types=null){
	if (empty($file_types)){
		$file_types=array('pdf','doc','xls','ppt','docx','xlsx','pptx','mp3','jpeg','jpg','png','avi','mov','mpg','mpeg','mp4','3gs','wmv','asx');
	}
	return in_array($check,$file_types);
}

function file_valid($file,&$error,$file_types=null){
	$new_file['temp']=$file['tmp_name'];
	$dot=strrpos($file['name'],'.');
	$new_file['ext']=strtolower(substr($file['name'],($dot+1)));
	if (file_types($new_file['ext'],$file_types)){
		return $new_file;
	}
	else {
		$error='The file you have uploaded is the wrong file type ('.$new_file['ext'].').';
		return false;
	}
}

function folder_copy($src,$dst,$recurse=true){
    $dir=opendir($src);
	if (!empty($dir)){
		@mkdir($dst);
		while (false!==($file=readdir($dir))){
			if ($file!='.' and $file!='..'){
				if (is_dir($src.'/'.$file)){
					if ($recurse){
						folder_copy($src.'/'.$file,$dst.'/'.$file);
					}
				}
				else {
					copy($src.'/'.$file,$dst.'/'.$file);
				}
			}
		}
		closedir($dir);
	}
}

// Taken from http://stackoverflow.com/a/22500394/518703
function php_size_to_bytes($sSize)  {  
    if (is_numeric($sSize)){
       return $sSize;
    }
    $sSuffix=substr($sSize,-1);  
    $iValue=substr($sSize,0,-1);  
    switch(strtoupper($sSuffix)){  
	    case 'P':  
	        $iValue*=1024;  
	    case 'T':  
	        $iValue*=1024;  
	    case 'G':  
	        $iValue*=1024;  
	    case 'M':  
	        $iValue*=1024;  
	    case 'K':  
	        $iValue*=1024;  
	        break;  
    }  
    return $iValue;  
}  

function get_max_upload_size(){
    return min(php_size_to_bytes(ini_get('post_max_size')),php_size_to_bytes(ini_get('upload_max_filesize')));  
}
//