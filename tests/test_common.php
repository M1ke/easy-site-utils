<?php
require_once __DIR__.'/../init.php';

class TestCommon extends PHPUnit_Framework_TestCase {
	function testArrayPull1d(){
		$arr=array('a'=>1,'b'=>2,'c'=>3);
		$pull=array('a','c');
		$new=array_pull($arr,$pull);
		$this->assertEquals($new,array('a'=>1,'c'=>3));
	}
	function testArrayPull2d(){
		$arr=array('a'=>array(1,2),'b'=>array(3,4),'c'=>array(5,6));
		$pull=array('a','c');
		$new=array_pull($arr,$pull);
		$this->assertEquals($new,array('a'=>array(1,2),'c'=>array(5,6)));
	}
	function testArrayPull2dAssoc(){
		$arr=array('a'=>array('i'=>1,'ii'=>2),'b'=>array('iii'=>3,'iv'=>4),'c'=>array('v'=>5,'vi'=>6));
		$pull=array('a','c');
		$new=array_pull($arr,$pull);
		$this->assertEquals($new,array('a'=>array('i'=>1,'ii'=>2),'c'=>array('v'=>5,'vi'=>6)));
	}

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

/*class TestArraySwitch extends UnitTestCase {
	public $arr=array('a'=>'a','b'=>'b','c'=>'c','d'=>'d');
    function testSwitchAB(){
		$array=array_switch($this->arr,'a','b');
		$this->assertEqual($array,array('b'=>'b','a'=>'a','c'=>'c','d'=>'d'));
    }
    function testSwitchBC(){
		$array=array_switch($this->arr,'b','c');
		$this->assertEqual($array,array('a'=>'a','c'=>'c','b'=>'b','d'=>'d'));
    }
    function testSwitchCD(){
		$array=array_switch($this->arr,'c','d');
		$this->assertEqual($array,array('a'=>'a','b'=>'b','d'=>'d','c'=>'c'));
    }
    function testSwitchDA(){
		$array=array_switch($this->arr,'a','d');
		$passed=($array===array('d'=>'d','b'=>'b','c'=>'c','a'=>'a'));
		$this->assertEqual($passed,true);
    }
    function testSwitchComplex(){
		$array=array_switch(array(0=>'a','a'=>'a','b'=>'b','c'=>'c'),'a','c');
		$passed=($array===array(0=>'a','c'=>'c','b'=>'b','a'=>'a'));
		if (!$passed){
			print_r($array);
		}
		$this->assertEqual($passed,true);
    }
}*/
