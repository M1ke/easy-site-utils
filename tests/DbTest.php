<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../init.php';

class DbTest extends TestCase {
	function testInputTimeAM(){
		$input='9am';
		$return=validate_input(array('type'=>'time'),$input,$error);
		self::assertEquals('9:00', $input);
		self::assertEquals(true, $return);
    }
	function testInputTimePM(){
		$input='9pm';
		$return=validate_input(array('type'=>'time'),$input,$error);
		self::assertEquals('21:00', $input);
		self::assertEquals(true, $return);
    }
	function testInputTime24am(){
		$input='9:00';
		$return=validate_input(array('type'=>'time'),$input,$error);
		self::assertEquals('9:00', $input);
		self::assertEquals(true, $return);
    }
	function testInputTime24amLeadZero(){
		$input='09:00';
		$return=validate_input(array('type'=>'time'),$input,$error);
		self::assertEquals('9:00', $input);
		self::assertEquals(true, $return);
    }
	function testInputTime24pm(){
		$input='21:00';
		$return=validate_input(array('type'=>'time'),$input,$error);
		self::assertEquals('21:00', $input);
		self::assertEquals(true, $return);
    }
	function testInputTimeAmbig(){
		$input='9:30';
		$return=validate_input(array('type'=>'time'),$input,$error);
		self::assertEquals('9:30', $input);
		self::assertEquals(true, $return);
    }
	function testInputTimeHour(){
		$input='9';
		$return=validate_input(array('type'=>'time'),$input,$error);
		self::assertEquals('9:00', $input);
		self::assertEquals(true, $return);
    }
	function testInputTimeDecimal(){
		$input='9.30';
		$return=validate_input(array('type'=>'time'),$input,$error);
		self::assertEquals('9:30', $input);
		self::assertEquals(true, $return);
    }
	function testInputTimeWrong(){
		$input='21:30am';
		$return=validate_input(array('type'=>'time'),$input,$error);
		self::assertEquals('21:30am', $input);
		self::assertEquals(false, $return);
    }
}
