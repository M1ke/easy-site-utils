<?php
function csv_output($data, $filename = 'download', $to_screen = false){
	$data = strip_tags($data);
	if (!empty($filename) and empty($to_screen)){
		header('Content-Type: text/csv, charset=utf-8');
		header('Content-Disposition: inline; filename="' . $filename . '.csv"');
	}
	else {
		$data = '<pre>' . $data . '</pre>';
		if (!empty($filename)){
			$data = '<p>File name: ' . $filename . '</p>' . $data;
		}
	}
	echo $data;
	die;
}

function echo_array($array, $return = false, $comment = false){
	if (!defined('BETA') and empty($_SERVER['SHELL']) and (function_exists('developer') and !developer())){
		return '';
	}
	$html = print_r($array, true);
	if (!empty($_SERVER['SHELL']) and !$return){
		$html .= PHP_EOL;
	}
	else {
		$html = '<pre>' . htmlspecialchars($html) . '</pre>';
		if ($comment){
			$html = '<!--' . $html . '-->';
		}
	}
	if ($return){
		return $html;
	}
	echo $html;

	return '';
}

function echo_html($html){
	echo '<pre>' . htmlspecialchars($html) . '</pre>';
}

function echo_shell($string, $eol = true){
	if (PHP_SAPI!=='cli'){
		return;
	}
	if ($string instanceof Exception){
		$string = 'E: ' . $string->getMessage();
	}
	if (is_array($string)){
		$string = print_r($string, true);
	}
	echo $string . ($eol ? PHP_EOL : '');
}

function json_out($data){
	header('Content-type: application/json');
	$data = json_encode($data);
	die($data);
}

function redirect_perm($url = null){
	header("HTTP/1.1 301 Moved Permanently");
	redirect($url);
}
