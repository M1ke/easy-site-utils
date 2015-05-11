<?php
require_once __DIR__.'/../init.php';

class TestSecurity extends PHPUnit_Framework_TestCase {
        function test_simple_encode(){
		$test_array=[
                	900, '1060','A10090','awes0me@gmail.com','nice_param1', '/?!Rand0m12@*[}', 'simple string with space'
		];
		$pass = 'testpassword';
		foreach($test_array as $test){
                	$this->assertEquals(simple_decode(simple_encode($test,$pass),$pass), $test);

		}
        }
}

