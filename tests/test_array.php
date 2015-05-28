<?php
require_once __DIR__.'/../init.php';

class TestArray extends PHPUnit_Framework_TestCase {
	public $data=array(
		0=>array('name'=>'Barry','email'=>'email@a.com','phone'=>'0777532906'),
		1=>array('name'=>'Arnold','email'=>'email@c.com','phone'=>'0787532906'),
		2=>array('name'=>'Zeta','email'=>'email@b.com','phone'=>'0757532906'),
	);

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
}
