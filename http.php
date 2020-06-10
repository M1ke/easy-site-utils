<?php
/**
 * @param $url
 * @param int $json_depth
 * @return mixed
 * @throws Exception
 *
 * @deprecated use Guzzle
 */
function http_get_json($url, $json_depth = 512){
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_HTTPGET, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_HTTPHEADER, [
		'Content-Type: application/json',
		'Accept: application/json',
	]);
	$response = curl_exec($ch);
	$retcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	curl_close($ch);
	if ($retcode!=200){
		throw new \Exception('CURL failed to return a valid response');
	}
	$result = json_decode($response, true, $json_depth);
	if ($result===null){
		throw new \Exception('The JSON could not be parsed correctly: "'.json_error_msg().'"');
	}

	return $result;
}

/**
 * @param $post
 * @param $url
 * @param bool $return
 * @return bool|string
 *
 * @deprecated use Guzzle
 */
function http_post_curl($post, $url, $return = false){
	//url-ify the data for the POST
	$post_string = '';
	foreach ($post as $key => $value){
		$value = urlencode($value);
		$post_string .= $key.'='.$value.'&';
	}
	rtrim($post_string, '&');

	//open connection
	$ch = curl_init();

	//set the url, number of POST vars, POST data
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, count($post));
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_string);
	if ($return){
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	}

	//execute post
	$result = curl_exec($ch);

	//close connection
	curl_close($ch);

	return $result;
}

/**
 * @param $url
 * @param $data
 * @param string $content
 * @return false|string
 *
 * @deprecated use Guzzle
 */
function http_stream($url, $data, $content = 'post'){
	switch ($content){
		case 'post':
			$data = http_build_query($data);
			$content_type = "application/x-www-form-urlencoded";
		break;
		case 'xml':
			$content_type = 'text/xml';
		break;
		default:
			$content_type = 'text/plain';
	}
	$options = [
		'http' => [
			'header' => "Content-type: $content_type\r\n",
			'method' => 'POST',
			'content' => $data,
		],
	];
	$context = stream_context_create($options);
	$result = file_get_contents($url, false, $context);

	return $result;
}
