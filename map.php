<?php
function geo_bounds($address,&$input,$geocode=null){
	if (empty($geocode)){
		$geocode=geocode($address);
	}
	if (empty($geocode['results'][0]['geometry'])){
		return false;
	}
	// could use "bounds" or "viewport" here. using viewport as bounds can include 
	// locations within legal bounds but a distance away
	$geocode=$geocode['results'][0]['geometry']['viewport'];
	$input['top']=$geocode['northeast']['lat'];
	$input['bottom']=$geocode['southwest']['lat'];
	$input['left']=$geocode['southwest']['lng'];
	$input['right']=$geocode['northeast']['lng'];
	return true;
}

function geo_coord($address,&$input,$geocode=null){
	if (empty($geocode)){
		$geocode=geocode($address);
	}
	if (empty($geocode['results'][0]['geometry'])){
		return false;
	}
	$geocode=$geocode['results'][0]['geometry']['location'];
	$input['lat']=$geocode['lat'];
	$input['lng']=$geocode['lng'];
	// remove this
	if (empty($geocode)){
		$input['lat']=51.3351234;
		$input['lng']=-0.8255623;
	}
	return true;
}

function geo_town($address,$geocode=null){
	if (empty($geocode)){
		$geocode=geocode($address);
	}
	if (empty($geocode['results'][0]['address_components'])){
		return false;
	}
	$geocode=$geocode['results'][0]['address_components'];
	foreach ($geocode as $place){
		if (in_array('locality',$place['types'])){
			return $place['long_name'];
		}
	}
	return '';
}

function geocode($address,$key=null){
	$url='https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false'.(!empty($key) ? '&key='.$key : '');
	try {
		$geocode=http_get_json($url);
		if ($geocode['status']=='ZERO_RESULTS'){
			throw new Exception('Zero results');
		}
	}
	catch (Exception $e){
		$geocode=false;
	}
	return $geocode;
}

function google_places_process($url,$key){
	$url.='&key='.$key;
	$places=http_get_json($url);
	if ($places['error_message']){
		throw new Exception('Google Places returned the following error: '.$places['error_message']);
	}
	elseif ($places['status']!='OK'){
		throw new Exception('The request did not return the correct information.');
	}
	return $places['results'];
}

function google_places_nearby($location,$key,$radius=10){
	if (is_array($location)){
		$location=$location['lat'].','.$location['lng'];
	}
	$url='https://maps.googleapis.com/maps/api/place/nearbysearch/json?location='.$location.'&radius='.$radius;
	return google_places_process($url,$key);
}

function google_places_vicinity($places){
	foreach ($places as $place){
		if (empty($place['vicinity'])){
			continue;
		}
		$vicinity=string_split($place['vicinity']);
		if (!empty($vicinity)){
			return end($vicinity);
		}
	}
	return '';
}

// https://github.com/hannesvdvreken/Point-in-polygon
function point_in_polygon($polygon,$point){
	$number_of_points=count($polygon);
	//duplicate last coordinates for creating a circular list
	$polygon[$number_of_points]=$polygon[0] ;
	//fyi: circular list is only gone through once, after this, so no need for pointers etc.
	//number of edges, 
	$numleft=0 ;
	$numright=0 ;

	for ($i=0;$i<$number_of_points;$i++){
		$x1=$polygon[$i][1];
		$x2=$polygon[$i+1][1];
		$y1=$polygon[$i][0];
		$y2=$polygon[$i+1][0];
		if (max($x2,$x1)>$point[1] and min($x2,$x1)<$point[1]){
			//next if-structure: 	needed to change edge's direction
			//                   	especially needed for next if-structure, 
                        //			to decide position of point (lat,lng) according to edge
			if ($x2>$x1){
				$x3=$x2;
				$x2=$x1;
				$x1=$x3;
				$y3=$y2;
				$y2=$y1;
				$y1=$y3;
			}
			// is edge located on the west/east (left/right) side of the point? Edges are always running from bottom to top
			// (switched in prev. if-structure)
			if (($x2-$x1)*($point[0]-$y1) - ($y2-$y1)*($point[1]-$x1) < 0){
				$numleft++;
			}
			else {
				$numright++;
			}
		}
	}
	//It's definitely not inside the polygon if none of the edges has x coordinates which are 
        //respectively above the points x coordinate, respectively below...
	//human: if all vertexes (points of polygon) are located above or below point (lng,lat)
	//and the point is inside the polygon, if it has an even index, as descibed by
        //     http://en.wikipedia.org/wiki/Point_in_polygon
	return !($numleft==0 or $numright==0) and abs($numleft - $numright) % 2==0;
}

class pointLocation {
	var $pointOnVertex=false;
	var $arr_x=1;
	var $arr_y=0;
	function pointLocation(){
	}
	function pointInPolygon($point,$polygon,$pointOnVertex=false){
		$this->pointOnVertex=$pointOnVertex;
		$vertices=$polygon;
		$arr_x=$this->arr_x;
		$arr_y=$this->arr_y;

		// Check if the point sits exactly on a vertex
		if ($this->pointOnVertex==true and $this->pointOnVertex($point,$vertices)==true){
			return "vertex";
		}

		// Check if the point is inside the polygon or on the boundary
		$intersections=0; 
		$vertices_count=count($vertices);
		// add the first verticie to the end to complete a loop
		if (reset($vertices)!=end($vertices)){
			$vertices[]=reset($vertices);
			$vertices_count++;
		}
		for ($i=1;$i<$vertices_count;$i++){
			$vertex1=$vertices[$i-1];
			$vertex2=$vertices[$i];
			if ($vertex1[$arr_y] == $vertex2[$arr_y] and $vertex1[$arr_y] == $point[$arr_y] and $point[$arr_x] > min($vertex1[$arr_x], $vertex2[$arr_x]) and $point[$arr_x] < max($vertex1[$arr_x], $vertex2[$arr_x])){ // Check if point is on an horizontal polygon boundary
				return "boundary";
			}
			$min_y=min($vertex1[$arr_y],$vertex2[$arr_y]);
			$max_y=max($vertex1[$arr_y],$vertex2[$arr_y]);
			$max_x=max($vertex1[$arr_x],$vertex2[$arr_x]);
			$point_over_y=($point[$arr_y] > $min_y);
			$point_under_y=($point[$arr_y] <= $max_y);
			$point_under_x=($point[$arr_x] <= $max_x);
			$no_intersect_y=($vertex1[$arr_y] != $vertex2[$arr_y]);
			if ($point_over_y and $point_under_y and $point_under_x and $no_intersect_y){
				$xinters = ($point[$arr_y] - $vertex1[$arr_y]) * ($vertex2[$arr_x] - $vertex1[$arr_x]) / ($vertex2[$arr_y] - $vertex1[$arr_y]) + $vertex1[$arr_x]; 
				if ($xinters == $point[$arr_x]){ // Check if point is on the polygon boundary (other than horizontal)
					return "boundary";
				}
				if ($vertex1[$arr_x]==$vertex2[$arr_x] || $point[$arr_x]<=$xinters){
					$intersections++; 
				}
			} 
		} 
		// If the number of edges we passed through is odd, then it's in the polygon
		if ($intersections%2!=0){
			return "inside";
		}
		else {
			return "outside";
		}
	}
	function pointOnVertex($point, $vertices){
		foreach($vertices as $vertex){
			if ($point==$vertex){
				return true;
			}
		}
	}
	function pointStringToCoordinates($pointString){
		$coordinates=explode(" ",$pointString);
		return array($this->arr_x=>$coordinates[0],$this->arr_y=>$coordinates[1]);
	}
}

function point_distance($a,$b){
	$height=abs($a['lat']-$b['lat']);
	$width=abs($a['lng']-$b['lng']);
	$hyp=($height*$height)+($width*$width);
	$hyp=sqrt($hyp);
	return $hyp;
}

function polygon_load($polygon,$serial=false){
	if ($serial){
		$polygon=unserialize($polygon);
	}
	if (empty($polygon)){
		return '';
	}
	foreach ($polygon as &$point){
		$point=implode(',',$point);
	}
	$polygon=implode(';',$polygon);
	return $polygon;
}

function polygon_save($polygon,$serial=false){
	$polygon=explode(';',$polygon);
	foreach ($polygon as $point_key => &$point){
		$point=explode(',',$point);
		foreach ($point as $coord_key => &$coord){
			if ($coord==0){
				unset($point[$coord_key]);
				continue;
			}
			$coord=round($coord,6);
		}
		if (count($point)!=2){
			unset($polygon[$point_key]);
			continue;
		}
	}
	if ($serial){
		$polygon=serialize($polygon);
	}
	return $polygon;
}

function query_bounds($bounds,$pfx=''){
	$query=$pfx."lat<='{$bounds['top']}' and ".$pfx."lat>='{$bounds['bottom']}' and ".$pfx."lng<='{$bounds['right']}' and ".$pfx."lng>='{$bounds['left']}'";
	return $query;
}

function query_distance($lat,$lng,$kilometres=1){
	$query="lat<>0 and lng<>0 and ".query_distance_convert($lat,$lng)." <= $kilometres";
	return $query;
}

function query_distance_convert($lat,$lng){
	// $query="(acos(sin(RADIANS($lat)) * sin(lat_rads) + cos(RADIANS($lat)) * cos(lat_rads) * cos(lng_rads - (RADIANS($lng)))) * 6371)";
	$query="(acos(sin(RADIANS($lat)) * sin(RADIANS(lat)) + cos(RADIANS($lat)) * cos(RADIANS(lat)) * cos(RADIANS(lng) - (RADIANS($lng)))) * 6371)";
	return $query;
}

function sql_polygon($polygon,$arr=true){
	$polygon=str_replace(['POLYGON','(',')'],'',$polygon);
	$polygon=explode(',',$polygon);
	foreach ($polygon as &$coords){
		$coords=explode(' ',$coords);
		$coords=$arr ? [$coords[1],$coords[0]] : $coords[1].','.$coords[0];
	}
	return $polygon;
}

function sql_map_contains($lat,$lng=null,$field='polygon'){
	if (is_array($lat)){
		$lng=$lat['lng'];
		$lat=$lat['lat'];
	}
	return "st_contains(`$field`,POINT($lat,$lng))";
}