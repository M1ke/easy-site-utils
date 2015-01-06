<?php
require_once __DIR__.'/../init.php';

class TestPolygon extends PHPUnit_Framework_TestCase {
	private $polygon=array(
		array(5,5),
		array(5,0),
		array(-2,-2),
		array(0,5),
	);
	private $pl;
	private $polygonDurham;
	private $polygonLondon;

	function __construct(){
		$this->pl=new pointLocation();
		$this->polygonDurham=file_load(__DIR__.'/polygons/durham','serialize');
		$this->polygonLondon=file_load(__DIR__.'/polygons/london','serialize');
		$this->polygonCanterbury=file_load(__DIR__.'/polygons/canterbury','serialize');
		$this->polygonHatfield=file_load(__DIR__.'/polygons/hatfield','serialize');
	}
	
	function testPointInPolygon(){
		$point=array(3,3);
		$point_in_polygon=point_in_polygon($this->polygon,$point);
		$this->assertTrue($point_in_polygon);
	}
	function testPointNotInPolygon(){
		$point=array(6,6);
		$point_in_polygon=point_in_polygon($this->polygon,$point);
		$this->assertTrue(!$point_in_polygon);
	}
	function testNegativePointInPolygon(){
		$point=array(-1,-1);
		$point_in_polygon=point_in_polygon($this->polygon,$point);
		$this->assertTrue($point_in_polygon);
	}
	function testNegativePointNotInPolygon(){
		$point=array(-5,-5);
		$point_in_polygon=point_in_polygon($this->polygon,$point);
		$this->assertTrue(!$point_in_polygon);
	}

	// polygons stored in separate file, generated using the StuRents site as a serialized array in the form (point(lat,lng),point(lat,lng)...)
	// point coords in same format below, taken from located houses in those areas
	private $pointDurham=array('54.7810935974121100','-1.5678850412368774');
	private $pointLondon=array('51.5614852905273440','-0.2779701054096222');
	private $pointCanterbury=array('51.2790794372558600','1.0590524673461914');
	private $pointCiren=array('51.7919464111328100','-2.0458130836486816');
	// private $pointCiren=array('51.7939464111328100','-2.9814400672912598');
	function testPointLocationDurham(){
		$point_in_polygon=$this->pl->pointInPolygon($this->pointDurham,$this->polygonDurham);
		$this->assertEquals($point_in_polygon,'inside');
	}
	function testPointLocationDurhamOut(){
		$point_in_polygon=$this->pl->pointInPolygon($this->pointLondon,$this->polygonDurham);
		$this->assertEquals($point_in_polygon,'outside');
	}
	function testPointLocationLondon(){
		$point_in_polygon=$this->pl->pointInPolygon($this->pointLondon,$this->polygonLondon);
		$this->assertEquals($point_in_polygon,'inside');
	}
	function testPointLocationLondonOut(){
		$point_in_polygon=$this->pl->pointInPolygon($this->pointDurham,$this->polygonLondon);
		$this->assertEquals($point_in_polygon,'outside');
	}
	// we use these because they're some of the few with a positive longitude
	function testPointLocationCanterbury(){
		$point_in_polygon=$this->pl->pointInPolygon($this->pointCanterbury,$this->polygonCanterbury);
		$this->assertEquals($point_in_polygon,'inside');
	}
	function testPointLocationCanterburyOut(){
		$point_in_polygon=$this->pl->pointInPolygon($this->pointDurham,$this->polygonCanterbury);
		$this->assertEquals($point_in_polygon,'outside');
	}
	// testing these because there was an anomaly (2014-03-25)
	// anomaly due to lack of closed loop affecting a thin line of latitude directly away from the polygon
	function testPointLocationHatfieldOut(){
		$point_in_polygon=$this->pl->pointInPolygon($this->pointCiren,$this->polygonHatfield);
		$this->assertEquals('outside',$point_in_polygon);
	}

	// Point distance
	function testPointDistance(){
		$a=['lat'=>1,'lng'=>1];
		$b=['lat'=>4,'lng'=>5];
		$dist=point_distance($a,$b);
		$this->assertEquals(5,$dist);
	}
	function testPointDistanceReal(){
		$a=['lat'=>53.45099,'lng'=>-2.23994];
		$b=['lat'=>53.45618,'lng'=>-2.23325];
		$dist=point_distance($a,$b);
		$this->assertEquals(0.0084671246595323,$dist);
	}
	function testPointDistanceZero(){
		$a=['lat'=>53.45099,'lng'=>-2.23994];
		$b=['lat'=>53.45099,'lng'=>-2.23994];
		$dist=point_distance($a,$b);
		$this->assertEquals(0,$dist);
	}
}
