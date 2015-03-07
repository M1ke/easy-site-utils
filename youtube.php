<?php

/*
 * Returns true if the video id supplied corresponds to a valid and public youtube video
 */
function youtube_video_valid($youtube_api_key,$video_id,&$error){
	$youtube_url="https://www.googleapis.com/youtube/v3/videos?id=$video_id&key=$youtube_api_key&part=status";
	$ch = curl_init($youtube_url);
	curl_setopt($ch, CURLOPT_HTTPGET, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
		'Content-Type: application/json',
		'Accept: application/json'
		));
	$response=curl_exec($ch);
	$retcode=curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if ($retcode!=200){
		$error="Video not found $video_id";
	}
	else {
		$result=json_decode($response,true,10);
		$item_details=$result['items'];
		if (!empty($item_details)){
			$item=$item_details[0];
			if (!empty($item)){
				if ($item['status']['privacyStatus']=='public'){
					return true;
				}
				else {
					$error="Video is private $video_id";
				}
			}
		}
		else {
			$error="Video not valid $video_id";
		}
	}
	return false;
}