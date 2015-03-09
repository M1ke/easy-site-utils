<?php
require_once __DIR__.'/../init.php';

class TestFunctionsString extends PHPUnit_Framework_TestCase {
	// Make email
	function testBasicEmail(){
		$email='me@m1ke.me';
		$this->assertTrue(make_email($email));
	}
	function testComplexEmail(){
		$email='inferno.m1ke_test@m1ke.test-test.me';
		$this->assertTrue(make_email($email));
	}
	function testNoDomain(){
		$email='inferno.m1ke_test@m1ke_test';
		$this->assertFalse(make_email($email));
	}
	function testNoName(){
		$email='@m1ke_test';
		$this->assertFalse(make_email($email));
	}
	function testInvalidChar(){
		$email='me@m1>ke.me';
		$this->assertFalse(make_email($email));
	}
	function testProceedingInvalidChar(){
		$email='me@m1ke.me>';
		$this->assertFalse(make_email($email));
	}

	// Message split
	function testShortMessageReturnsSingleItemArray(){
		$test_string='This is a test.';
		$split_string=message_split($test_string);
		$this->assertEquals($split_string,array($test_string));
	}
	function testLongMessageReturnsTwoItemArray(){
		$test_string='Hi stacey davies, your skydive has been booked for Saturday 1st December 2012 at 8:30am. We look forward to seeing you at the airfield. This is a long message to test the splitter.';
		$split_string=message_split($test_string);
		$this->assertEquals('Hi stacey davies, your skydive has been booked for Saturday 1st December 2012 at 8:30am. We look forward to seeing you at the airfield. This is a long messa 1/2',$split_string[0]);
		$this->assertEquals('ge to test the splitter. 2/2',$split_string[1]);
	}
	function testLongerMessageReturnsThreeItemArray(){
		$test_string='Hi stacey davies, your skydive has been booked for Saturday 1st December 2012 at 8:30am. We look forward to seeing you at the airfield. Hi stacey davies, your skydive has been booked for Saturday 1st December 2012 at 8:30am. We look forward to seeing you at the airfield. Hi stacey davies, your skydive has been booked for Saturday 1st December 2012 at 8:30am. We look forward to seeing you at the airfield.';
		$split_string=message_split($test_string);
		$this->assertEquals('Hi stacey davies, your skydive has been booked for Saturday 1st December 2012 at 8:30am. We look forward to seeing you at the airfield. Hi stacey davies, yo 1/3',$split_string[0]);
		$this->assertEquals('ur skydive has been booked for Saturday 1st December 2012 at 8:30am. We look forward to seeing you at the airfield. Hi stacey davies, your skydive has been  2/3',$split_string[1]);
		$this->assertEquals('booked for Saturday 1st December 2012 at 8:30am. We look forward to seeing you at the airfield. 3/3',$split_string[2]);
	}
	function testActualMessage(){
		$test_string='Hi Aaron Beales We\'re looking forward to seeing you. If you don’t hear from us later today, it means we are hopeful to skydive and look forward to meeting you in person at your allocated arrival time. The GOskydive Team';
		$split_string=message_split($test_string);
		$this->assertEquals('Hi Aaron Beales We\'re looking forward to seeing you. If you don’t hear from us later today, it means we are hopeful to skydive and look forward to meeting 1/2',$split_string[0]);
		$this->assertEquals(' you in person at your allocated arrival time. The GOskydive Team 2/2',$split_string[1]);
	}

	// Comma list

	function testSingleItemReturnsItem(){
		$list=array('one');
		$this->assertEquals(comma_list($list),'one');
	}
	function testTwoItemsReturnsListAnded(){
		$list=array('one','two');
		$this->assertEquals(comma_list($list),'one and two');
	}
	function testThreeItemsReturnsListAnded(){
		$list=array('one','two','three');
		$this->assertEquals(comma_list($list),'one, two and three');
	}
	function testFourItemsReturnsListAnded(){
		$list=array('one','two','three','four');
		$this->assertEquals(comma_list($list),'one, two, three and four');
	}
	function testThreeItemsReturnsListOr(){
		$list=array('one','two','three');
		$this->assertEquals(comma_list($list,'or'),'one, two or three');
	}

	// make phone
	function testMakePhoneUK(){
		$num='07816581298';
		$is_phone=make_phone($num);
		$this->assertTrue($is_phone);
	}
	function testMakePhoneLongUK(){
		$num='+44. 7816581298';
		$is_phone=make_phone($num);
		$this->assertTrue($is_phone);
	}
	function testMakePhoneLongerUK(){
		$num='0044 78 1658 1298';
		$is_phone=make_phone($num);
		$this->assertTrue($is_phone);
	}
	function testMakePhoneUSA(){
		$num='1-386-490-9400';
		$is_phone=make_phone($num);
		$this->assertTrue($is_phone);
	}
	function testMakePhoneLongUSA(){
		$num='+1-386-490-9400';
		$is_phone=make_phone($num);
		$this->assertTrue($is_phone);
	}
	function testMakePhoneLongerUSA(){
		$num='001-386-490-9400';
		$is_phone=make_phone($num);
		$this->assertTrue($is_phone);
	}
	function testMakePhonebadlyspaced(){
		$num='0 0 1 .3 8 6 -4 9 0-9 4 +0 0';
		$is_phone=make_phone($num);
		$this->assertTrue($is_phone);
	}

	// Phone country
	function testPhoneCountryReturnsSameIfNoCode(){
		$num='07816581298';
		$national=phone_country($num,'44');
		$this->assertEquals($num,$national);
	}
	function testPhoneCountryReturnsStripped(){
		$num='447816581298';
		$national=phone_country($num,'44');
		$this->assertEquals('7816581298',$national);
	}
	function testPhoneCountryReturnsStrippedZeroed(){
		$num='447816581298';
		$national=phone_country($num,'44',true);
		$this->assertEquals('07816581298',$national);
	}
	function testPhoneCountryPlusReturnsStrippedZeroed(){
		$num='+447816581298';
		$national=phone_country($num,'44',true);
		$this->assertEquals('07816581298',$national);
	}

	// first_word
	function testFirstWordReturnsFirstWordOfString(){
		$test_string='this is a sentence';
		$first_word=first_word($test_string);
		$this->assertEquals($first_word,'this');
	}

	function testBlankReturnsBlank(){
		$test_string='';
		$first_word=first_word($test_string);
		$this->assertEquals($first_word,'');
	}
	
	// substr_words
	function testSubstrWords(){
		$sentence='The quick brown fox jumped over the lazy dog';
		$three_words=substr_words($sentence,3);
		$this->assertEquals($three_words,'The quick brown');
	}
	function testSubstrWordsLessThanRequired(){
		$sentence='The quick brown';
		$four_words=substr_words($sentence,4);
		$this->assertEquals($four_words,'The quick brown');
	}
	function testSubstrWordsEndSep(){
		$sentence='The quick brown ';
		$four_words=substr_words($sentence,4);
		$this->assertEquals($four_words,'The quick brown');
	}

	// custom_number
	function testCustomNumber(){
		$this->assertEquals('1,000',custom_number('1000'));
	}
	function testCustomNumberLonger(){
		$this->assertEquals('12,345,678',custom_number('12345678'));
	}
	function testCustomNumberDecimal(){
		$this->assertEquals('1,000.12',custom_number('1000.12'));
	}
	function testCustomNumberDecimalLonger(){
		$this->assertEquals('12,345,678.12345678',custom_number('12345678.12345678'));
	}

	// substr_until
	function testSubstrUntil(){
		$string='test_string';
		$this->assertEquals('test',substr_until($string,'_'));
	}
	function testSubstrUntilArr(){
		$string='test._string';
		$this->assertEquals('test',substr_until($string,['_','.']));
	}
	function testSubstrUntilArrSingle(){
		$string='test.string';
		$this->assertEquals('test',substr_until($string,['_','.']));
	}

	// string_replace_once
	function testStringReplaceOnce(){
		$url='/test/this/url/for/me';
		$this->assertEquals('test/this/url/for/me',string_replace_once('/','',$url));
	}
	function testStringReplaceOnceLater(){
		$url='test/this/url/for/me';
		$this->assertEquals('testthis/url/for/me',string_replace_once('/','',$url));
	}

	// in_string
	function testInString(){
		$haystack='this is a string with things in it';
		$needle='string';
		$this->assertTrue(in_string($needle,$haystack));
	}
	function testInStringCase(){
		$haystack='this is a StrIng with things in it';
		$needle='string';
		$this->assertTrue(in_string($needle,$haystack));
	}
	function testNotInString(){
		$haystack='this is a String with things in it';
		$needle='panda';
		$this->assertTrue(!in_string($needle,$haystack));
	}
}

class TestPlural extends PHPUnit_Framework_TestCase {
	private $str='apple';
	private $num=5;

	function testPluralStringFirst(){
		$plural=plural($this->str,$this->num);
		$this->assertEquals($plural,($this->num).' '.($this->str).'s');
	}
	function testPluralStringFirst1(){
		$plural=plural($this->str,1);
		$this->assertEquals($plural,'1 '.$this->str);
	}
	function testPluralNumFirst(){
		$plural=plural($this->num,$this->str);
		$this->assertEquals($plural,($this->num).' '.($this->str).'s');
	}
	function testPluralNumFirst1(){
		$plural=plural(1,$this->str);
		$this->assertEquals($plural,'1 '.$this->str);
	}
}

