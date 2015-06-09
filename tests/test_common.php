<?php
require_once __DIR__.'/../init.php';

class TestCommon extends PHPUnit_Framework_TestCase {

	protected $csv_files = [
		'header'=>__DIR__.'/files/csv-header',
	];

	// csv_file
	function testCsvFile(){
		$file = $this->csv_files['header'];
		$csv_file = csv_file($file);
		$this->assertEquals([
			['cola','colB','col-C'],
			['field a','field b',' field c'],
			['field a2','field b 2','field c2'],
		], $csv_file);
	}

	// csv_array_parse
	function testCsvArrayParse(){
		$parsed = [
			['cola','colB','col-C'],
			['field a','field b',' field c'],
			['field a2','field b 2','field c2']
		];
		$csv_array = csv_array_parse($parsed);
		$this->assertEquals([
			1=>['cola'=>'field a','colb'=>'field b','col-c'=>'field c'],
			2=>['cola'=>'field a2','colb'=>'field b 2','col-c'=>'field c2'],
		], $csv_array);
	}

	// csv_array
	function testCsvArray(){
		$file = $this->csv_files['header'];
		$csv_array = csv_array($file);
		$this->assertEquals([
			1=>['cola'=>'field a','colb'=>'field b','col-c'=>'field c'],
			2=>['cola'=>'field a2','colb'=>'field b 2','col-c'=>'field c2'],
		], $csv_array);
	}

	// log_file_location
	function testLogFileBasic(){
		$blank_log=log_file_location();

		$this->assertEquals('/',$blank_log[0]);
		$file='main.log';
		$this->assertEquals('main.log',log_file_location($file));
		define('LOG','/tmp/');
		$this->assertEquals('/tmp/main.log',log_file_location($file));
		$file='/dev/'.$file;
		$this->assertEquals('/dev/main.log',log_file_location($file));
	}
}
