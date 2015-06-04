<?php
require_once __DIR__.'/../init.php';

class TestArray extends PHPUnit_Framework_TestCase {
	public $data=array(
		0=>array('name'=>'Barry','email'=>'email@a.com','phone'=>'0777532906'),
		1=>array('name'=>'Arnold','email'=>'email@c.com','phone'=>'0787532906'),
		2=>array('name'=>'Zeta','email'=>'email@b.com','phone'=>'0757532906'),
	);

	// array_data_sort
	function testArraySortName(){
		$sorted=array_data_sort($this->data,'name');
		$correct=array(
			$this->data[1],
			$this->data[0],
			$this->data[2],
		);
		$this->assertEquals($sorted,$correct);
	}

	function testArraySortNameDesc(){
		$sorted=array_data_sort($this->data,'name',true);
		$correct=array(
			$this->data[2],
			$this->data[0],
			$this->data[1],
		);
		$this->assertEquals($sorted,$correct);
	}

	function testArraySortPhone(){
		$sorted=array_data_sort($this->data,'phone');
		$correct=array(
			$this->data[2],
			$this->data[0],
			$this->data[1],
		);
		$this->assertEquals($sorted,$correct);
	}

	function testArraySortEmail(){
		$sorted=array_data_sort($this->data,'email');
		$correct=array(
			$this->data[0],
			$this->data[2],
			$this->data[1],
		);
		$this->assertEquals($sorted,$correct);
	}

	// array_extract
	function testArrayExtract(){
		$two_d = [
			['title'=>'Test','text'=>'Lorem'],
			['title'=>'Test 2','text'=>'Ipsum'],
			['title'=>'Test 3','text'=>'Dolor'],
		];
		$extracted = array_extract($two_d, 'text');
		$this->assertEquals(['Lorem', 'Ipsum', 'Dolor'], $extracted);
	}

	function testArrayExtractAssoc(){
		$two_d = [
			'a' => ['title'=>'Test','text'=>'Lorem'],
			'b' => ['title'=>'Test 2','text'=>'Ipsum'],
			'c' => ['title'=>'Test 3','text'=>'Dolor'],
		];
		$extracted = array_extract($two_d, 'text');
		$this->assertEquals(['a'=>'Lorem', 'b'=>'Ipsum', 'c'=>'Dolor'], $extracted);
	}

	// array_keys_exist
	function testArrayKeysExistOr(){
		// are in array
		$this->assertTrue(array_keys_exist(['Audi', 'Bmw', 'Citroen'], ['Audi'=>'3', 'Bmw'=>'2', 'Citroen'=>'4']));
		$this->assertTrue(array_keys_exist(['Audi', 'Bmw', 'Citroen'], ['Audi'=>'3']));
		$this->assertTrue(array_keys_exist(['Audi', 'Bmw', 'Citroen'], ['Audi'=>'3', 'Volvo'=>'9']));
		$this->assertTrue(array_keys_exist(['Audi', 'Bmw', 'Citroen'], ['Audi'=>null]));

		// none in array
		$this->assertFalse(array_keys_exist(['Audi', 'Bmw', 'Citroen'], []));
		$this->assertFalse(array_keys_exist(['Audi', 'Bmw', 'Citroen'], [''=>'']));
		$this->assertFalse(array_keys_exist(['Bmw', 'Citroen'], ['Audi'=>'3']));
		$this->assertFalse(array_keys_exist(['Bmw', 'Citroen'], ['Audi'=>null]));
	}

	function testArrayKeysExistAnd(){
		// all in array
		$this->assertTrue(array_keys_exist(['Audi', 'Bmw', 'Citroen'], ['Audi'=>'3', 'Bmw'=>'2', 'Citroen'=>'4'], true));

		// some in array
		$this->assertFalse(array_keys_exist(['Audi', 'Bmw', 'Citroen'], ['Audi'=>'3'], true));
		$this->assertFalse(array_keys_exist(['Audi', 'Bmw', 'Citroen'], ['Audi'=>'3', 'Volvo'=>'9'], true));

		// none in array
		$this->assertFalse(array_keys_exist(['Bmw', 'Citroen'], ['Audi'=>'3']));
		$this->assertFalse(array_keys_exist(['Bmw', 'Citroen'], []));
		$this->assertFalse(array_keys_exist(['Bmw', 'Citroen'], [''=>'']));
	}

	function testArrayKeysExistEmpty(){
		$this->assertFalse(array_keys_exist([], ['Audi'=>'3']));
		$this->assertFalse(array_keys_exist([], ['Audi'=>null]));
	}
}
