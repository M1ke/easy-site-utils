<?php
require_once __DIR__.'/../init.php';

class TestValidator extends PHPUnit_Framework_TestCase {
	
	// # Testing Single Item Validation

	// ## Address

	function testValidInputAddress(){
		$input='Test Road\'s
		Test Area
		Test City
		Postcode';
		$input_corrected='Test Road&#39;s
		Test Area
		Test City
		Postcode';
		$valid=array(
			'type'=>'address',
		);
		validate_input($valid,$input,$error);
		$this->assertEquals($input,$input_corrected);
	}
	function testValidInputAddressEmpty(){
		$input='';
		$valid=array(
			'type'=>'address',
		);
		validate_input($valid,$input,$error);
		$this->assertTrue(!empty($error));
		$this->assertEquals($input,'');
	}
	function testValidInputAddressShort(){
		$input='a';
		$valid=array(
			'type'=>'address',
		);
		validate_input($valid,$input,$error);
		$this->assertTrue(!empty($error));
		$this->assertEquals($input,'a');
	}
	function testValidInputAddressBlankEmpty(){
		$input='';
		$valid=array(
			'type'=>'address',
			'blank'=>1,
		);
		validate_input($valid,$input,$error);
		$this->assertTrue(empty($error));
		$this->assertTrue(empty($input));
	}

	// ## Array

	// # Validate group
	private $validatorTests=array(
		'name'=>array(
			'length'=>1
		),
		'email'=>array(
			'type'=>'email'
		),
		'phone'=>array(
			'type'=>'phone',
		),
		'status'=>array(
			'type'=>'boolean',
			'need'=>1,
		),
		'flag'=>array(
			'type'=>'func',
			'func'=>'valid_default',
			'default'=>'default',
		),
	);
	private $validatorTestsInput=array(
		'name'=>'John Smith',
		'evil_hack'=>'Data',
	);
	function testValidatorDoesntAddFields(){
		$valid=$this->validatorTests;
		$input=$this->validatorTestsInput;

		validate($valid,$input,$errors);

		$this->assertTrue(!isset($input['email']));
		$this->assertTrue(!isset($input['phone']));
	}
	function testValidatorAddsNeededFields(){
		$valid=$this->validatorTests;
		$input=$this->validatorTestsInput;
		
		validate($valid,$input,$errors);

		$this->assertTrue(isset($input['status']));
		$this->assertTrue(empty($input['status']));
	}
	function testValidatorRunsNotSetFuncField(){
		$valid=$this->validatorTests;
		$input=$this->validatorTestsInput;

		validate($valid,$input,$errors);

		$this->assertTrue(isset($input['flag']));
	}
	function testValidatorClearsNonValidatedFields(){
		$valid=$this->validatorTests;
		$input=$this->validatorTestsInput;

		validate($valid,$input,$errors,null,true);

		$this->assertTrue($input['evil_hack']===false);
	}

	// Specific type validators

	function testDobAgeMin(){
		$input['dob']='01/01/'.(date('Y')-15);
		$valid=array(
			'dob'=>array(
				'type'=>'dob',
				'min'=>18,
			),
		);
		validate($valid,$input,$errors);
		$this->assertTrue(!empty($errors['dob']));
	}
}
