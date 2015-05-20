<?php
require_once __DIR__.'/../init.php';
require_once __DIR__.'/../display.php';

class TestDisplay extends PHPUnit_Framework_TestCase {

	// options

	private $options=array(0=>'Zero',1=>'One',2=>'Two','_'=>'');

	function testMakeOptions(){
		$options=make_options_($this->options);
		$this->assertEquals($options,'<option value="0">Zero</option><option value="1">One</option><option value="2">Two</option>');
	}
	function testMakeOptionsZeroSelected(){
		$options=make_options_($this->options,0);
		$this->assertEquals($options,'<option value="0" selected>Zero</option><option value="1">One</option><option value="2">Two</option>');
	}
	function testMakeOptionsOneSelected(){
		$options=make_options_($this->options,1);
		$this->assertEquals($options,'<option value="0">Zero</option><option value="1" selected>One</option><option value="2">Two</option>');
	}

	// html_implode

	function testHtmlImplode(){
		$html = html_implode(['Test', 'Test2', 'Test3'], 'span');
		$this->assertEquals('<span>Test</span><span>Test2</span><span>Test3</span>', $html);
	}

	function testHtmlImplodeClass(){
		$html = html_implode(['Test', 'Test2', 'Test3'], 'span', 'bold');
		$this->assertEquals("<span class='bold'>Test</span><span class='bold'>Test2</span><span class='bold'>Test3</span>", $html);
	}

	// make_table

	function testMakeTableCsv(){
		$table['head']=[
			'student'=>['title'=>'First'],
			['title'=>'Repeat 3','colspan'=>3],
			['title'=>'Repeat 2','colspan'=>2],
			['title'=>'Last'],
		];
		$csv=make_table_csv($table);
		$this->assertEquals('First,Repeat 3,Repeat 3,Repeat 3,Repeat 2,Repeat 2,Last',$csv);
	}
}
// class TestOfDisplay extends UnitTestCase {
//     function testMakeInputCreatesCheckbox(){
// 		$input=array(
// 			'type'=>'checkbox'
// 			,'title'=>'Test'
// 			,'_name'=>'test'
// 		);
// 		$html=make_input($input);
//         $this->assertEqual($html,'<div class="smalls"><label for="test">Test</label> <input type="checkbox" name="test" value="1"/></div>');
//     }
//     function testMakeInputCreatesCheckboxWithClass(){
// 		$input=array(
// 			'type'=>'checkbox'
// 			,'title'=>'Test'
// 			,'_name'=>'test'
// 			,'class'=>'test'
// 		);
// 		$html=make_input($input);
//         $this->assertEqual($html,'<div class="smalls"><label for="test">Test</label> <input type="checkbox" name="test" value="1" class="test"/></div>');
//     }
//     function testMakeInputCreatesCheckboxWithValue(){
// 		$input=array(
// 			'type'=>'checkbox'
// 			,'title'=>'Test'
// 			,'_name'=>'test'
// 			,'value'=>'test'
// 		);
// 		$html=make_input($input);
//         $this->assertEqual($html,'<div class="smalls"><label for="test">Test</label> <input type="checkbox" name="test" value="test"/></div>');
//     }
//     function testMakeInputCreatesCheckboxChecked(){
// 		$input=array(
// 			'type'=>'checkbox'
// 			,'title'=>'Test'
// 			,'_name'=>'test'
// 		);
// 		$value=1;
// 		$html=make_input($input,$value);
//         $this->assertEqual($html,'<div class="smalls"><label for="test">Test</label> <input type="checkbox" name="test" value="1" checked/></div>');
//     }
//     function testMakeInputCreatesCheckboxCheckedBool(){
// 		$input=array(
// 			'type'=>'checkbox'
// 			,'title'=>'Test'
// 			,'_name'=>'test'
// 			,'checked'=>'bool'
// 		);
// 		$value='test';
// 		$html=make_input($input,$value);
//         $this->assertEqual($html,'<div class="smalls"><label for="test">Test</label> <input type="checkbox" name="test" value="1" checked/></div>');
//     }
//     function testMakeInputCreatesCheckboxCheckedDate(){
// 		$input=array(
// 			'type'=>'checkbox'
// 			,'title'=>'Test'
// 			,'_name'=>'test'
// 			,'checked'=>'date'
// 		);
// 		$value='2012-03-22';
// 		$html=make_input($input,$value);
//         $this->assertEqual($html,'<div class="smalls"><label for="test">Test</label> <input type="checkbox" name="test" value="1" checked/></div>');
//     }
//     function testMakeInputCreatesCheckboxCheckedError(){
// 		$input=array(
// 			'type'=>'checkbox'
// 			,'title'=>'Test'
// 			,'_name'=>'test'
// 		);
// 		$value=1;
// 		$error='test';
// 		$html=make_input($input,$value,$error);
//         $this->assertEqual($html,'<span class="error"><div class="smalls"><label for="test">Test</label> <input type="checkbox" name="test" value="1" checked/></div><strong>test</strong></span>');
//     }
// }