<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../init.php';

class CommonTest extends TestCase {

	protected $csv_files = [
		'header'=>__DIR__.'/files/csv-header',
		'no-file'=>__DIR__.'/files/no-file',
	];

	// csv_file
	function testCsvFile(){
		$file = $this->csv_files['header'];
		$csv_file = csv_file($file);
		self::assertEquals([
			['cola','colB','col-C'],
			['field a','field b',' field c'],
			['field a2','field b 2','field c2'],
		], $csv_file);
	}

	function testCsvNoFile(){
		$file = $this->csv_files['no-file'];
		$csv_file = csv_file($file);
		self::assertEquals([], $csv_file);
	}

	// csv_array_parse
	function testCsvArrayParse(){
		$parsed = [
			['cola','colB','col-C'],
			['field a','field b',' field c'],
			['field a2','field b 2','field c2']
		];
		$csv_array = csv_array_parse($parsed);
		self::assertEquals([
			1=>['cola'=>'field a','colb'=>'field b','col-c'=>'field c'],
			2=>['cola'=>'field a2','colb'=>'field b 2','col-c'=>'field c2'],
		], $csv_array);
	}

	// csv_array
	function testCsvArray(){
		$file = $this->csv_files['header'];
		$csv_array = csv_array($file);
		self::assertEquals([
			1=>['cola'=>'field a','colb'=>'field b','col-c'=>'field c'],
			2=>['cola'=>'field a2','colb'=>'field b 2','col-c'=>'field c2'],
		], $csv_array);
	}

	// log_file_location
	function testLogFileBasic(){
		$blank_log=log_file_location();

		self::assertEquals('/',$blank_log[0]);
		$file='main.log';
		self::assertEquals('main.log',log_file_location($file));
		define('LOG','/tmp/');
		self::assertEquals('/tmp/main.log',log_file_location($file));
		$file='/dev/'.$file;
		self::assertEquals('/dev/main.log',log_file_location($file));
	}
}
