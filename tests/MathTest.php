<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../init.php';

class MathTest extends TestCase {
	public function testRoundCustomUp(){
		$val = 13;
		$precision = 5;
		self::assertEquals(15, round_custom($val, $precision));
	}

	public function testRoundCustomDown(){
		$val = 12;
		$precision = 5;
		self::assertEquals(10, round_custom($val, $precision));
	}

	public function testRoundCustomCeil(){
		$val = 11;
		$precision = 5;
		self::assertEquals(15, round_custom($val, $precision, 'ceil'));
	}

	public function testRoundCustomFloor(){
		$val = 14;
		$precision = 5;
		self::assertEquals(10, round_custom($val, $precision, 'floor'));
	}

	public function testIsPos(){
		$num = 5;
		self::assertTrue(is_pos($num));
	}

	public function testIsNotPos(){
		$num = 0;
		self::assertFalse(is_pos($num));
	}

	public function testIsPosString(){
		$num = '5';
		self::assertTrue(is_pos($num));
	}

	public function testIsNotPosString(){
		$num = '0';
		self::assertFalse(is_pos($num));
	}

	public function testIsPosWords(){
		$num = '0hello';
		self::assertFalse(is_pos($num));
	}

	public function testIsPosNull(){
		$num = null;
		self::assertFalse(is_pos($num));
	}

	public function testIsPosNullable(){
		$num = null;
		self::assertTrue(is_pos_nullable($num));
	}

	public function testIsPosStringEmpty(){
		$num = '';
		self::assertFalse(is_pos($num));
	}

	public function testIsPosBlank(){
		$num = '';
		self::assertTrue(is_pos_blank($num));
	}

	public function testIsPosBlankString(){
		$num = '0abc';
		self::assertFalse(is_pos_blank($num));
	}
}
