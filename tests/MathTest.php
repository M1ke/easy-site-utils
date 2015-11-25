<?php
require_once __DIR__.'/../init.php';

class TestMath extends PHPUnit_Framework_TestCase {
	public function testRoundCustomUp(){
		$val = 13;
		$precision = 5;
		$this->assertEquals(15, round_custom($val, $precision));
	}

	public function testRoundCustomDown(){
		$val = 12;
		$precision = 5;
		$this->assertEquals(10, round_custom($val, $precision));
	}

	public function testRoundCustomCeil(){
		$val = 11;
		$precision = 5;
		$this->assertEquals(15, round_custom($val, $precision, 'ceil'));
	}

	public function testRoundCustomFloor(){
		$val = 14;
		$precision = 5;
		$this->assertEquals(10, round_custom($val, $precision, 'floor'));
	}
}
