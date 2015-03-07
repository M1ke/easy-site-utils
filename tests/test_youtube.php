<?php
require_once __DIR__.'/../init.php';

class TestFunctionsYoutube extends PHPUnit_Framework_TestCase {

	/*
	 * Must supply your own YOUTUBE API key on command line to this test
	 */
	function testYoutubeVideoIdValid(){
		global $argv, $argc;
		$youtube_urls=[
			['url'=>'http://youtu.be/HKK5-uIfh2I','id'=>'HKK5-uIfh2I', 'pass'=>true],//public video
			['url'=>'http://www.youtube.com/v/OaYtF1HTYC0','id'=>'DsrykdW0re0', 'pass'=>false],//private video
			['url'=>'http://www.youtube.com/v/XYZ','id'=>'XYZ', 'pass'=>false],//invalid video
			['url'=>'http://www.youtube.com/v/','id'=>'', 'pass'=>false],//no video
		];
		$api_key=$argv[2];
		foreach($youtube_urls as $test){
			$this->assertEquals($test['pass'],youtube_video_valid($api_key,$test['id'],$error));
		}
	}
}