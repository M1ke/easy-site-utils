<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../init.php';

class SecurityTest extends TestCase {
	function test_simple_encode(){
		$test_array = [
			900, '1060', 'A10090', 'awes0me@gmail.com', 'nice_param1', '/?!Rand0m12@*[}', 'simple string with space',
		];
		$pass = 'testpassword';
		foreach ($test_array as $test){
			self::assertEquals(simple_decode(simple_encode($test, $pass), $pass), $test);

		}
	}
}

