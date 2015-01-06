<?php
require_once('../../../../../Websites/php/simpletest/autorun.php');
require_once '../_functions.php';

class TestOfValidate extends UnitTestCase{
	function testInputTimeAM(){
		$input='9am';
		$return=validate_input(array('type'=>'time'),$input,$error);
		$this->assertEqual($input,'9:00');
		$this->assertEqual($return,true);
    }
	function testInputTimePM(){
		$input='9pm';
		$return=validate_input(array('type'=>'time'),$input,$error);
		$this->assertEqual($input,'21:00');
		$this->assertEqual($return,true);
    }
	function testInputTime24am(){
		$input='9:00';
		$return=validate_input(array('type'=>'time'),$input,$error);
		$this->assertEqual($input,'9:00');
		$this->assertEqual($return,true);
    }
	function testInputTime24amLeadZero(){
		$input='09:00';
		$return=validate_input(array('type'=>'time'),$input,$error);
		$this->assertEqual($input,'9:00');
		$this->assertEqual($return,true);
    }
	function testInputTime24pm(){
		$input='21:00';
		$return=validate_input(array('type'=>'time'),$input,$error);
		$this->assertEqual($input,'21:00');
		$this->assertEqual($return,true);
    }
	function testInputTimeAmbig(){
		$input='9:30';
		$return=validate_input(array('type'=>'time'),$input,$error);
		$this->assertEqual($input,'9:30');
		$this->assertEqual($return,true);
    }
	function testInputTimeHour(){
		$input='9';
		$return=validate_input(array('type'=>'time'),$input,$error);
		$this->assertEqual($input,'9:00');
		$this->assertEqual($return,true);
    }
	function testInputTimeDecimal(){
		$input='9.30';
		$return=validate_input(array('type'=>'time'),$input,$error);
		$this->assertEqual($input,'9:30');
		$this->assertEqual($return,true);
    }
	function testInputTimeWrong(){
		$input='21:30am';
		$return=validate_input(array('type'=>'time'),$input,$error);
		$this->assertEqual($input,'21:30am');
		$this->assertEqual($return,false);
    }
}
