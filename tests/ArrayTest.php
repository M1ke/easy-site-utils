<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../init.php';

class ArrayTest extends TestCase {
	public $data = [
		0 => ['name' => 'Barry', 'email' => 'email@a.com', 'phone' => '0777532906'],
		1 => ['name' => 'Arnold', 'email' => 'email@c.com', 'phone' => '0787532906'],
		2 => ['name' => 'Zeta', 'email' => 'email@b.com', 'phone' => '0757532906'],
	];

	// array_data_sort
	function testArraySortName(){
		$sorted = array_data_sort($this->data, 'name');
		$correct = [
			$this->data[1],
			$this->data[0],
			$this->data[2],
		];
		self::assertEquals($sorted, $correct);
	}

	function testArraySortNameDesc(){
		$sorted = array_data_sort($this->data, 'name', true);
		$correct = [
			$this->data[2],
			$this->data[0],
			$this->data[1],
		];
		self::assertEquals($sorted, $correct);
	}

	function testArraySortPhone(){
		$sorted = array_data_sort($this->data, 'phone');
		$correct = [
			$this->data[2],
			$this->data[0],
			$this->data[1],
		];
		self::assertEquals($sorted, $correct);
	}

	function testArraySortEmail(){
		$sorted = array_data_sort($this->data, 'email');
		$correct = [
			$this->data[0],
			$this->data[2],
			$this->data[1],
		];
		self::assertEquals($sorted, $correct);
	}

	// array_extract
	function testArrayExtract(){
		$two_d = [
			['title' => 'Test', 'text' => 'Lorem'],
			['title' => 'Test 2', 'text' => 'Ipsum'],
			['title' => 'Test 3', 'text' => 'Dolor'],
		];
		$extracted = array_extract($two_d, 'text');
		self::assertEquals(['Lorem', 'Ipsum', 'Dolor'], $extracted);
	}

	function testArrayExtractAssoc(){
		$two_d = [
			'a' => ['title' => 'Test', 'text' => 'Lorem'],
			'b' => ['title' => 'Test 2', 'text' => 'Ipsum'],
			'c' => ['title' => 'Test 3', 'text' => 'Dolor'],
		];
		$extracted = array_extract($two_d, 'text');
		self::assertEquals(['a' => 'Lorem', 'b' => 'Ipsum', 'c' => 'Dolor'], $extracted);
	}

	// array_keys_exist
	function testArrayKeysExistOr(){
		// are in array
		self::assertTrue(array_keys_exist(['Audi', 'Bmw', 'Citroen'], ['Audi' => '3', 'Bmw' => '2', 'Citroen' => '4']));
		self::assertTrue(array_keys_exist(['Audi', 'Bmw', 'Citroen'], ['Audi' => '3']));
		self::assertTrue(array_keys_exist(['Audi', 'Bmw', 'Citroen'], ['Audi' => '3', 'Volvo' => '9']));
		self::assertTrue(array_keys_exist(['Audi', 'Bmw', 'Citroen'], ['Audi' => null]));

		// none in array
		self::assertFalse(array_keys_exist(['Audi', 'Bmw', 'Citroen'], []));
		self::assertFalse(array_keys_exist(['Audi', 'Bmw', 'Citroen'], ['' => '']));
		self::assertFalse(array_keys_exist(['Bmw', 'Citroen'], ['Audi' => '3']));
		self::assertFalse(array_keys_exist(['Bmw', 'Citroen'], ['Audi' => null]));
	}

	function testArrayKeysExistAnd(){
		// all in array
		self::assertTrue(array_keys_exist(['Audi', 'Bmw', 'Citroen'], ['Audi' => '3', 'Bmw' => '2', 'Citroen' => '4'], true));

		// some in array
		self::assertFalse(array_keys_exist(['Audi', 'Bmw', 'Citroen'], ['Audi' => '3'], true));
		self::assertFalse(array_keys_exist(['Audi', 'Bmw', 'Citroen'], ['Audi' => '3', 'Volvo' => '9'], true));

		// none in array
		self::assertFalse(array_keys_exist(['Bmw', 'Citroen'], ['Audi' => '3']));
		self::assertFalse(array_keys_exist(['Bmw', 'Citroen'], []));
		self::assertFalse(array_keys_exist(['Bmw', 'Citroen'], ['' => '']));
	}

	function testArrayKeysExistEmpty(){
		self::assertFalse(array_keys_exist([], ['Audi' => '3']));
		self::assertFalse(array_keys_exist([], ['Audi' => null]));
	}

	// array_pull
	function testArrayPull1d(){
		$arr = ['a' => 1, 'b' => 2, 'c' => 3];
		$pull = ['a', 'c'];
		$new = array_pull($arr, $pull);
		self::assertEquals($new, ['a' => 1, 'c' => 3]);
	}

	function testArrayPull1dMissing(){
		$arr = ['a' => 1, 'b' => 2, 'c' => 3];
		$pull = ['a', 'c', 'd'];
		$new = array_pull($arr, $pull);
		self::assertEquals($new, ['a' => 1, 'c' => 3, 'd' => null]);
	}

	function testArrayPullString(){
		$arr = ['a' => 1, 'b' => 2, 'c' => 3];
		$pull = "a, c";
		$new = array_pull($arr, $pull);
		self::assertEquals($new, ['a' => 1, 'c' => 3]);
	}

	function testArrayPull2d(){
		$arr = ['a' => [1, 2], 'b' => [3, 4], 'c' => [5, 6]];
		$pull = ['a', 'c'];
		$new = array_pull($arr, $pull);
		self::assertEquals($new, ['a' => [1, 2], 'c' => [5, 6]]);
	}

	function testArrayPull2dAssoc(){
		$arr = ['a' => ['i' => 1, 'ii' => 2], 'b' => ['iii' => 3, 'iv' => 4], 'c' => ['v' => 5, 'vi' => 6]];
		$pull = ['a', 'c'];
		$new = array_pull($arr, $pull);
		self::assertEquals($new, ['a' => ['i' => 1, 'ii' => 2], 'c' => ['v' => 5, 'vi' => 6]]);
	}

	// Array strip

	public function testArrayStripEnd(){
		$array = ['', 'a', '', 'b', ''];
		$array_strip_end = array_strip_end($array);
		self::assertEquals(['', 'a', '', 'b'], $array_strip_end);
	}

	public function testArrayStripEndLots(){
		$array = ['', 'a', '', 'b', '', '', ''];
		$array_strip_end = array_strip_end($array);
		self::assertEquals(['', 'a', '', 'b'], $array_strip_end);
	}

	public function testArrayStripStart(){
		$array = ['', 'a', '', 'b', ''];
		$array_strip_start = array_strip_start($array);
		self::assertEquals(['a', '', 'b', ''], $array_strip_start);
	}

	public function testArrayStripStartLots(){
		$array = ['', '', '', 'a', '', 'b', ''];
		$array_strip_start = array_strip_start($array);
		self::assertEquals(['a', '', 'b', ''], $array_strip_start);
	}

	public function testArrayStrip(){
		$array = ['', '', 'a', '', 'b', '', ''];
		$array_strip = array_strip($array);
		self::assertEquals(['a', '', 'b'], $array_strip);
	}

	public function testArrayRemove(){
		$arr = ['a', 'b', 'c'];
		$removed = array_remove('a', $arr);
		self::assertEquals([1 => 'b', 2 => 'c'], $removed);
	}
}
