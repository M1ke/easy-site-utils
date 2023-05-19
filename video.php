<?php
function make_video($url,$protocol='http'){
	$parse=parse_url_imp($url);
	$html='';
	if (empty($parse)){
		$html.='<!-- this link would not parse and is invalid : '.$url.'-->';
	}
	switch ($parse['domain']){
		case 'youtube.':
			// creates the variable $v. if YouTube ever change url scheme for 'watch' this will need altering
			parse_str($parse['arg'], $parse_output);
			$v = $parse_output['v']??null;
			$html.='<object type="application/x-shockwave-flash" style="width:480px; height:385px;" data="'.$protocol.'://www.youtube.com/v/'.$v.'&color1=0x006699&color2=0x54abd6"><param name="movie" value="'.$protocol.'://www.youtube.com/v/'.$v.'&color1=0x006699&color2=0x54abd6"/></object>';
		break;
		default:
			$html.='<!-- this link does not correspond to a supported domain : '.$url.'-->';
	}
	return $html;
}

/*
 * returns a embed html from a video url
 */
function make_youtube_video($url,$params){
	$query=http_build_query($params);
	$width=isset($params['width'])?$params['width']:'480';
	$height=isset($params['height'])?$params['height']:'385';
	$parse=parse_url_imp($url);
	$html='';
	if (empty($parse)){
		$html.='<!-- this link would not parse and is invalid : '.$url.'-->';
	}
	switch ($parse['domain']){
		case 'youtu.':
		case 'youtube.':
			$video_id=youtube_video_id($url,false);
			$html.='<iframe width="'.$width.'" height="'.$height.'" frameborder="0" src="https://www.youtube.com/embed/'.$video_id.'?'.$query.'"></iframe>';
		break;
		default:
			$html.='<!-- this link does not correspond to a supported domain : '.$url.'-->';
	}
	return $html;
}

/*
 * Returns the value of param v from url if its a valid 'youtube.' url
 * else tries to extract the last half of a url like youtu.be/######
 * If validate=true , would validate the url to be a valid youtube url
 */
function youtube_video_id($url,$validate=false){
	if ($validate) {
		$test=parse_url_imp($url);
		if (!in_array($test['domain'], ['youtube.','youtu.'])){
			return null;
		}
	}
	parse_str(parse_url( $url, PHP_URL_QUERY )??'', $vars );
	if (!empty($vars['v'])){
		$out=$vars['v'];
	}
	else {
		$regex="/.*\/{0,2}(?:w{3}\.)*(?:youtube\.com|youtu\.be)\/(?:v\/|embed\/)*([^\&\?\/]+)/";
		preg_match($regex,$url,$match);
		$out=$match[1]??null;
	}
	return $out;
}

/*
 * Returns true if the video id supplied corresponds to a valid and public youtube video
 */
function youtube_video_valid($youtube_api_key,$video_id){
	$youtube_url="https://www.googleapis.com/youtube/v3/videos?id=$video_id&key=$youtube_api_key&part=status";
	try {
		$result=http_get_json($youtube_url,10);
	}
	catch (Exception $e){
		throw new Exception("Video not found $video_id");
	}
	$item_details=$result['items'];
	if (empty($item_details)){
		throw new Exception("Video not valid $video_id");
	}
	$item=$item_details[0];
	if (empty($item) or $item['status']['privacyStatus']!='public'){
		throw new Exception("Video is private $video_id");
	}
	return true;
}
