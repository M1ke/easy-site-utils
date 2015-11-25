<?php
require_once __DIR__.'/../init.php';
require_once __DIR__.'/../display.php';

class TestDisplay extends PHPUnit_Framework_TestCase {

	// options

	private $options=array(0=>'Zero',1=>'One',2=>'Two','_'=>'');
	private $pipe;

	public function setUp(){
		$this->pipe = urlencode('|');
	}

	public function testMakeOptions(){
		$options=make_options_($this->options);
		$this->assertEquals($options,'<option value="0">Zero</option><option value="1">One</option><option value="2">Two</option>');
	}
	public function testMakeOptionsZeroSelected(){
		$options=make_options_($this->options,0);
		$this->assertEquals($options,'<option value="0" selected>Zero</option><option value="1">One</option><option value="2">Two</option>');
	}
	public function testMakeOptionsOneSelected(){
		$options=make_options_($this->options,1);
		$this->assertEquals($options,'<option value="0">Zero</option><option value="1" selected>One</option><option value="2">Two</option>');
	}

	// html_implode

	public function testHtmlImplode(){
		$html = html_implode(['Test', 'Test2', 'Test3'], 'span');
		$this->assertEquals('<span>Test</span><span>Test2</span><span>Test3</span>', $html);
	}

	public function testHtmlImplodeClass(){
		$html = html_implode(['Test', 'Test2', 'Test3'], 'span', 'bold');
		$this->assertEquals("<span class='bold'>Test</span><span class='bold'>Test2</span><span class='bold'>Test3</span>", $html);
	}

	// make_table

	public function testMakeTableCsv(){
		$table = [
			'student'=> ['title'=> 'First'],
			['title'=> 'Repeat 3', 'colspan'=> 3],
			['title'=> 'Repeat 2', 'colspan'=> 2],
			['title'=> 'Last'],
		];
		$csv = make_table_csv($table);
		$this->assertEquals('First,Repeat 3,Repeat 3,Repeat 3,Repeat 2,Repeat 2,Last', $csv);
	}

	private function makeTableHeadArray(){
		return [
			'name'=>['title'=>'Name', 'order'=>'l.name', 'sort'=>'asc', 'default'=>1],
			'houses'=>['title'=>'Houses','order'=>'l.houses','sort'=>'desc'],
			['title'=>'Score'],
			['title'=>'Messages','colspan'=>3],
			'another'=> ['title'=>'Another','no'=> true, 'order'=>'l.name', 'sort'=>'asc', ],
		];
	}

	private function getPipe(){
		return urlencode('|');
	}

	private function assertTableHtml($expected, $actual){
		$replace = ["\n", "\r", "\t"];
		$expected = str_replace($replace, '', $expected);
		$actual = str_replace($replace, '', $actual);
		$this->assertEquals($expected, $actual);
	}

	public function testMakeTableHead(){
		$table = $this->makeTableHeadArray();
		$head = make_table_head($table, '/path/script/');
		$html = '<thead>
			<tr>
				<th class=" sort-desc " data-name="name">
					<a href="/path/script/?sort=name'.$this->pipe.'desc">Name</a>
				</th>
				<th class=" sort-desc " data-name="houses">
					<a href="/path/script/?sort=houses'.$this->pipe.'desc">Houses</a>
				</th>
				<th>
					<span>Score</span>
				</th>
				<th colspan="3" data-name="1">
					<span>Messages</span>
				</th>
			</tr>
		</thead>';
		$order = 'l.name ASC';

		$this->assertTableHtml($html, $head['head']);
		$this->assertEquals($order, $head['order']);
	}

	public function testMakeTableHeadSort(){
		$table = $this->makeTableHeadArray();
		$head = make_table_head($table, '/path/script/', ['sort'=>'houses|desc', 'page'=> 2]);
		$html = '<thead>
			<tr>
				<th class=" sort-asc " data-name="name">
					<a href="/path/script/?sort=name'.$this->pipe.'asc&page=2">Name</a>
				</th>
				<th class=" sort-asc sort-current sort-current-desc" data-name="houses">
					<a href="/path/script/?sort=houses'.$this->pipe.'asc&page=2">Houses</a>
				</th>
				<th>
					<span>Score</span>
				</th>
				<th colspan="3" data-name="1">
					<span>Messages</span>
				</th>
			</tr>
		</thead>';
		$order = 'l.houses DESC';

		$this->assertTableHtml($html, $head['head']);
		$this->assertEquals($order, $head['order']);
	}

	public function testMakeTableHeadEl(){
		$table = $this->makeTableHeadArray();
		$head = make_table_head($table, '/path/script/', [], ['el'=> 'div', 'norow'=> true]);
		$html = '
			<div class=" sort-desc " data-name="name">
				<a href="/path/script/?sort=name'.$this->pipe.'desc">Name</a>
			</div>
			<div class=" sort-desc " data-name="houses">
				<a href="/path/script/?sort=houses'.$this->pipe.'desc">Houses</a>
			</div>
			<div>
				<span>Score</span>
			</div>
			<div colspan="3" data-name="1">
				<span>Messages</span>
			</div>';

		$this->assertTableHtml($html, $head['head']);
	}
}
