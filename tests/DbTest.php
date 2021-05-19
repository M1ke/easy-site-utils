<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__.'/../init.php';

class DbTest extends TestCase {
	function testInputTimeAM(){
		$input = '9am';
		$return = validate_input(['type' => 'time'], $input, $error);
		self::assertEquals('9:00', $input);
		self::assertEquals(true, $return);
	}

	function testInputTimePM(){
		$input = '9pm';
		$return = validate_input(['type' => 'time'], $input, $error);
		self::assertEquals('21:00', $input);
		self::assertEquals(true, $return);
	}

	function testInputTime24am(){
		$input = '9:00';
		$return = validate_input(['type' => 'time'], $input, $error);
		self::assertEquals('9:00', $input);
		self::assertEquals(true, $return);
	}

	function testInputTime24amLeadZero(){
		$input = '09:00';
		$return = validate_input(['type' => 'time'], $input, $error);
		self::assertEquals('9:00', $input);
		self::assertEquals(true, $return);
	}

	function testInputTime24pm(){
		$input = '21:00';
		$return = validate_input(['type' => 'time'], $input, $error);
		self::assertEquals('21:00', $input);
		self::assertEquals(true, $return);
	}

	function testInputTimeAmbig(){
		$input = '9:30';
		$return = validate_input(['type' => 'time'], $input, $error);
		self::assertEquals('9:30', $input);
		self::assertEquals(true, $return);
	}

	function testInputTimeHour(){
		$input = '9';
		$return = validate_input(['type' => 'time'], $input, $error);
		self::assertEquals('9:00', $input);
		self::assertEquals(true, $return);
	}

	function testInputTimeDecimal(){
		$input = '9.30';
		$return = validate_input(['type' => 'time'], $input, $error);
		self::assertEquals('9:30', $input);
		self::assertEquals(true, $return);
	}

	function testInputTimeWrong(){
		$input = '21:30am';
		$return = validate_input(['type' => 'time'], $input, $error);
		self::assertEquals('21:30am', $input);
		self::assertEquals(false, $return);
	}

	function testTableArray(){
		$cols = [

			[
				'Field' => 'user_id',
				'Type' => 'bigint(20)',
				'Null' => 'NO',
				'Key' => 'PRI',
				'Default' => null,
				'Extra' => 'auto_increment',
			],

			[
				'Field' => 'joined',
				'Type' => 'datetime',
				'Null' => 'NO',
				'Key' => '',
				'Default' => null,
				'Extra' => '',
			],

			[
				'Field' => 'landlord_id',
				'Type' => 'bigint(20)',
				'Null' => 'NO',
				'Key' => '',
				'Default' => null,
				'Extra' => '',
			],

			[
				'Field' => 'shared',
				'Type' => 'tinyint(1)',
				'Null' => 'NO',
				'Key' => '',
				'Default' => '0',
				'Extra' => '',
			],

			[
				'Field' => 'email',
				'Type' => 'varchar(100)',
				'Null' => 'NO',
				'Key' => 'MUL',
				'Default' => null,
				'Extra' => '',
			],

			[
				'Field' => 'original',
				'Type' => 'varchar(50)',
				'Null' => 'NO',
				'Key' => '',
				'Default' => null,
				'Extra' => '',
			],


			[
				'Field' => 'extra',
				'Type' => 'text',
				'Null' => 'NO',
				'Key' => '',
				'Default' => null,
				'Extra' => '',
			],

			[
				'Field' => 'send',
				'Type' => 'tinyint(1)',
				'Null' => 'NO',
				'Key' => '',
				'Default' => null,
				'Extra' => '',
			],

			[
				'Field' => 'status',
				'Type' => 'tinyint(1)',
				'Null' => 'NO',
				'Key' => '',
				'Default' => '9',
				'Extra' => '',
			],

			[
				'Field' => 'gender',
				'Type' => 'enum(\'\',\'f\',\'m\',\'fm\')',
				'Null' => 'NO',
				'Key' => '',
				'Default' => '',
				'Extra' => '',
			],

			[
				'Field' => 'hunting',
				'Type' => 'tinyint(1)',
				'Null' => 'NO',
				'Key' => '',
				'Default' => '1',
				'Extra' => '',
			],
			[
				'Field' => 'timestamp',
				'Type' => 'timestamp',
				'Null' => 'NO',
				'Key' => '',
				'Default' => 'CURRENT_TIMESTAMP',
				'Extra' => 'on update CURRENT_TIMESTAMP',
			],

			[
				'Field' => 'dob',
				'Type' => 'date',
				'Null' => 'NO',
				'Key' => '',
				'Default' => null,
				'Extra' => '',
			],

			[
				'Field' => 'img',
				'Type' => 'varchar(4)',
				'Null' => 'NO',
				'Key' => '',
				'Default' => null,
				'Extra' => '',
			],

			[
				'Field' => 'contract',
				'Type' => 'decimal(4,2)',
				'Null' => 'NO',
				'Key' => '',
				'Default' => null,
				'Extra' => '',
			],
		];
		$table = table_array_inner($cols, []);
		$expected = [
			'primary' => 'user_id',
			'fields' => [
				'user_id' => ['type' => 'int',
					'auto' => 1],
				'joined' => ['type' => 'datetime'],
				'landlord_id' => ['type' => 'int'],
				'shared' => ['type' => 'single',
					'default' => '0'],
				'email' => ['type' => 'string'],
				'original' => ['length' => '50',
					'type' => 'string'],
				'extra' => ['type' => 'text'],
				'send' => ['type' => 'single'],
				'status' => ['type' => 'single',
					'default' => '9'],
				'gender' => ['choices' => ['', 'f', 'm', 'fm'],
					'type' => 'choice'],
				'hunting' => ['type' => 'single',
					'default' => '1'],
				'timestamp' => ['type' => 'timestamp',
					'default' => 'CURRENT_TIMESTAMP'],
				'dob' => ['type' => 'date'],
				'img' => ['length' => '4',
					'type' => 'string'],
				'contract' => ['length' => '4,2',
					'type' => 'decimal'],
			],
		];
		self::assertEquals($expected, $table);
	}
}
