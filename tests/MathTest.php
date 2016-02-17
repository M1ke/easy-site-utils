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

	public function testIsPos(){
		$num = 5;
		$this->assertTrue(is_pos($num));
	}

	public function testIsNotPos(){
		$num = 0;
		$this->assertFalse(is_pos($num));
	}

	public function testIsPosString(){
		$num = '5';
		$this->assertTrue(is_pos($num));
	}

	public function testIsNotPosString(){
		$num = '0';
		$this->assertFalse(is_pos($num));
	}

	public function testIsPosWords(){
		$num = '0hello';
		$this->assertFalse(is_pos($num));
	}

	public function testIsPosNull(){
		$num = null;
		$this->assertFalse(is_pos($num));
	}

	public function testIsPosNullable(){
		$num = null;
		$this->assertTrue(is_pos_nullable($num));
	}

	public function testIsPosStringEmpty(){
		$num = '';
		$this->assertFalse(is_pos($num));
	}

	public function testIsPosBlank(){
		$num = '';
		$this->assertTrue(is_pos_blank($num));
	}

	public function testIsPosBlankString(){
		$num = '0abc';
		$this->assertFalse(is_pos_blank($num));
	}
}
