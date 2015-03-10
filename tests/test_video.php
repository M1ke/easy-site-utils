<?php
require_once __DIR__.'/../init.php';

class TestFunctionsVideo extends PHPUnit_Framework_TestCase {

	function testYoutubeVideoId(){
		$youtube_urls=[
			['url'=>'http://youtu.be/HKK5-uIfh2I','id'=>'HKK5-uIfh2I'],
			['url'=>'https://youtu.be/_UZ92FPv_Ko','id'=>'_UZ92FPv_Ko'],
			['url'=>'http://www.youtube.com/watch?v=XVH2ZFkqjSU&feature=mfu_in_order&list=UL','id'=>'XVH2ZFkqjSU'],
			['url'=>'https://www.youtube.com/watch?v=fe7y9X3-GIU&feature=youtu.be','id'=>'fe7y9X3-GIU'],
			['url'=>'http://www.youtube.com/watch?v=VV0dBqapdkc','id'=>'VV0dBqapdkc'],
			['url'=>'http://www.youtube.com/embed/mz4vWHLwbPE','id'=>'mz4vWHLwbPE'],
			['url'=>'https://www.youtube.com/embed/1NUbUYrVuPA','id'=>'1NUbUYrVuPA'],
			['url'=>'http://www.youtube.com/v/OaYtF1HTYC0','id'=>'OaYtF1HTYC0'],
			['url'=>'http://www.vistabee.com/en/v/q6h9ok','id'=>''],
		];
		foreach($youtube_urls as $test){
			$this->assertEquals($test['id'],youtube_video_id($test['url']));
		}
	}

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
			['url'=>'https://www.youtube.com/watch?feature=player_embedded&v=LGbMJYjmyoI', id=>'LGbMJYjmyoI', 'pass'=>false],//video does not exist
		];
		$api_key=$argv[2];
		foreach($youtube_urls as $test){
			$this->assertEquals($test['pass'],youtube_video_valid($api_key,$test['id'],$error));
		}
	}

}
