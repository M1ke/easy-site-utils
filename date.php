<?php
/* Constants
	DATE_FORMAT - a PHP formatted datetime string (defaults to d/m/Y)
	DATE_USA - set if dates are expected to be passed in USA format "m/d/y" (defaults to false)
*/

function arr_day(){
	return array(
		1=>'Monday',
		2=>'Tuesday',
		3=>'Wednesday',
		4=>'Thursday',
		5=>'Friday',
		6=>'Saturday',
		7=>'Sunday',
	);
}

function arr_hours(){
	return array(0,1,2,3,4,5,6,7,8,9,10,11,12);
}

function arr_hours_24(){
	return array(0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23);
}

function arr_mins(){
	return array(0,5,10,15,20,25,30,35,40,45,50,55,60);
}

function arr_month(){
	return array(
		1=>'January',
		2=>'February',
		3=>'March',
		4=>'April',
		5=>'May',
		6=>'June',
		7=>'July',
		8=>'August',
		9=>'September',
		10=>'October',
		11=>'November',
		12=>'December',
	);
}

function date_age($date){
	$date=sql_date($date);
    list($Y,$m,$d)    = explode('-',$date);
    return( date('md') < $m.$d ? date('Y')-$Y-1 : date('Y')-$Y );
}

function date_list($duration,$start=false){
	if (!$start) $start=strtotime(date('Y-m-d'));
	elseif (!is_numeric($start)) $start=strtotime($start);
	$end=inc_date($date,$duration);
	$vals=array($start);
	$names=array(custom_date($start,'d/m/Y'));
	for ($n=1;$n<$duration;$n++){
		$stamp=inc_date($start,array('date'=>$n),1);
		$vals[]=$stamp;
		$names[]=custom_date($stamp,'d/m/Y');
	}
	return array('names'=>$names,'vals'=>$vals);
}

function date_list_assoc($duration,$start=false){
	if (!$start) $start=time();
	elseif (!is_numeric($start)) $start=strtotime($start);
	$end=inc_date($date,$duration);
	$dates[inc_date($start,null,1)]=custom_date(time(),'d/m/Y');
	for ($n=1;$n<$duration;$n++){
		$stamp=inc_date($start,array('date'=>$n),1);
		$vals[]=$stamp;
		$dates[$stamp]=custom_date($stamp,'d/m/Y');
	}
	return $dates;
}

function is_date($date){
	if (empty($date) or $date=='0000-00-00 00:00:00' or $date=='0000-00-00' or $date=='00:00:00'){
		return false;
	}
	return true;
}

function is_today($date){
	if (!is_numeric($date)){
		$date=strtotime($date);
	}
	if ($date<1){
		return false;
	}
	$today=strtotime(date('Y-m-d'));
	$sub=$date-$today;
	if ($sub<86400 and $sub>=0){
		return true;
	}
	return false;	
}

function is_tomorrow($date){
	if (!is_numeric($date)){
		$date=strtotime($date);
	}
	if ($date<1){
		return false;
	}
	$today=strtotime(date('Y-m-d'));
	$sub=$date-$today;
	if ($sub<172800 and $sub>=86400){
		return true;
	}
	return false;	
}

function days_left($day,$date=null){
	if (!is_numeric($day)){
		$day=date('N',strtotime($day));
	}
	if (!empty($date)){
		$date=string_time($date);
	}
	if (empty($date)){
		$date=time();
	}
	$month=$month_now=date('n',$date);
	while ($month_now==$month){
		if (date('N',$date)==$day){
			$count++;
		}
		$date=inc_date($date,array('day'=>1),true);
		$month_now=date('n',$date);
	}
	return $count;
}

function date_difference($date1,$return='',$date2=false,$pos=true){
	$date1=string_time($date1);
	$date2=empty($date2) ? time() : string_time($date2);
	if (empty($date1) or empty($date2)){
		return false;
	}
	$diff=$date2-$date1;
	$diff=($diff< 0 and $pos) ? $diff*-1 : $diff;
	return !empty($return) ? seconds_convert($diff,$return) : $diff;
}

function seconds_convert($time,$return='day'){
	switch ($return){
		case 'year':
			$time/=365;
			$started=true;
		case 'quarter':
			if (!$started){
				$time*=4/365;
				$started=true;
			}
		case 'month':
			if (!$started){
				$time*=12/365;
				$started=true;
			}
		case 'week':
			if (!$started){
				$time/=7;
			}
		case 'day':
			$time/=24;
		case 'hour':
			$time/=60;
		case 'minute':
			$time/=60;
	}
	return floor($time);
}

function seconds_to_time($time,$seconds_only=''){
	$seconds=$time%60;
	if ($time>60){
		$time/=60;
		$minutes=$time%60;
		if ($time>60){
			$time/=60;
			$hours=$time%24;
			if ($time>24){
				$time/=24;
				$days=$time%365;
				if ($time>365){
					$time/=365;
					$years=floor($time);
				}
			}
		}
	}
	if (!empty($years)){
		$return=$years.' years';
	}
	if (!empty($days)){
		$return.=(!empty($return) ? ', ' : '').$days.' days';
	}
	$return.=(!empty($return) ? ', ' : '').(!empty($hours) ? $hours.':' : (!empty($return) ? '0:' : ''));
	$return.=!empty($minutes) ? $minutes.':' : (!empty($return) ? '00:' : '');
	$return.=!empty($return) ? zero_pad($seconds) : $seconds.(!empty($seconds) ? $seconds_only : '');
	return $return;
}

function date_from_dob($date){
	return date_store($date);
}

function date_stitch($date){
	return empty($date) ? '' : $date['year'].'-'.$date['month'].'-'.$date['day'];
}

function date_display($date,$usa=false){
	if (!is_date($date)){
		return '';
	}
	list($year,$month,$day)=explode('-',$date);
	if ($usa){
		$copy=$day;
		$day=$month;
		$month=$copy;
	}
	return !empty($day) ? $day.'/'.$month.'/'.$year : '';
}

function date_store($date){
	$date=date_components($date);
	return date_stitch($date);
}

function date_display_check(&$date){
	$date=!is_date($date) ? '' : date_display($date);
	return $date;
}

function dob_from_date($date){
	return date_display($date);
}

function date_components($date){
	if (strpos($date,'/')!==false){
		list($day,$month,$year)=explode('/',$date);
	}
	elseif (strpos($date,'.')!==false){
		list($day,$month,$year)=explode('.',$date);
	}
	if (defined('DATE_USA')){
		$copy=$day;
		$day=$month;
		$month=$copy;
	}
	if (strpos($date,'-')!==false){
		list($year,$month,$day)=explode('-',$date);
	}
	if (empty($month)){
		return '';
	}
	date_year_correct_($year);
	return array('year'=>$year,'month'=>$month,'day'=>$day);
}

function date_inc($date,$inc){
	$date=date_components($date);
	foreach ($inc as $key => $val){
		$date[$key]+=$val;
	}
	return date_stitch($date);
}

function date_year_correct_(&$year){
	$year=date_year_correct($year);
}

function date_year_correct($year){
	if (strlen($year)==2){
		if ($year>date('y')){
			// let's face it, we're doing well if this needs future proofing!
			$year=1900+$year;
		}
	}
	return $year;
}

function age_from_dob($date){
	$date=date_components($date);
	date_year_correct_($date['year']);
    $diff['year']=date('Y')-$date['year'];
    $diff['month']=date('m')-$date['month'];
    $diff['day']=date('d')-$date['day'];
    if ($diff['month']< 0 or ($diff['day']< 0 and $diff['month']==0)){
		$diff['year']--;
	}
    return $diff['year'];
}

function date_sever($date){
	return (strpos($date,' ')>0 ? substr($date,0,strpos($date,' ')) : $date);
}

function date_split($string,$date,$time){
	$datestamp=strtotime($string);
	$array['date']=date($date,$datestamp);
	$array['time']=date($time,$datestamp);
	return $array;
}

function custom_date($date=null,$format=null,$sep=' at '){
	if (is_numeric($date)){
		$stamp=$date;
	}
	elseif (is_date($date)){
		$stamp=string_time($date,$error);
	}
	if (empty($stamp)){
		return false;
	}
	return custom_date_format($stamp,$format,$sep);
}

function custom_date_format($stamp,$format,$sep=' at '){
	$default_date_format=defined('DATE_FORMAT') ? DATE_FORMAT : 'd/m/Y';
	if (is_array($format)){
		if (empty($format[0])){
			$format[0]=$default_date_format;
		}
		if (empty($format[1])){
			$format[1]='g:ia';
		}
		$date=array();
		foreach ($format as $format_split){
			$date[]=date($format_split,$stamp);
		}
		$date=implode($sep,$date);
	}
	else {
		if (empty($format)){
			$format=$default_date_format;
		}
		$date=date($format,$stamp);
	}
	return $date;
}

function int_day($int){
	$arr=arr_day();
	return $arr[$int];
}

function int_month($int){
	$arr=arr_month();
	return $arr[$int];
}

function inc_date($date=null,$inc=array(),$stamp=false,$format='Y-m-d H:i:s'){
	if (!is_numeric($date)){
		$date=string_time($date);
	}
	if (empty($date)){
		$date=time();
	}
	// everything else calls it day; date refers to the whole value. this is here for legacy
	if (!empty($inc['date'])){
		$inc['day']=$inc['date'];
	}
	$date=mktime(date('H',$date)+$inc['hour'],date('i',$date)+$inc['min'],date('s',$date)+$inc['second'],date('m',$date)+$inc['month'],date('d',$date)+$inc['day'],date('Y',$date)+$inc['year']);
	if ($stamp){
		return $date;
	}
	return custom_date_format($date,$format);
}

function inc_date_repeat($date,$inc,$repeat,$format='Y-m-d H:i:s'){
	foreach ($inc as &$val){
		$val*=$repeat;
	}
	return inc_date($date,$inc,false,$format,$repeat);
}

function list_months($length=null,$month=null,$year=null){
	$months=arr_month();
	if (empty($length)){
		$length=12;
	}
	if (empty($month)){
		$month=date('n');
	}
	if (empty($year)){
		$year=date('Y');
	}
	$opts=array();
	for ($n=0;$n<$length;$n++){
		$opts[$month.'-'.$year]=$months[$month].' '.$year;
		$month++;
		if ($month>12){
			$month=1;
			$year++;
		}
	}
	return $opts;
}

// need to deprecate this, have it pass to custom_date
function make_date($date,$separator=','){
	$datestamp=strtotime($date);
	if ($separator!=null) $time=$separator.' '.date('g:ia',$datestamp);
	if (date('Y-m-d',$datestamp)==date('Y-m-d')) $date='Today'.$time;
	elseif (date('Y-m-d',$datestamp)==date('Y-m-d',time()-86400)) $date='Yesterday'.$time;
	elseif (date('Y-m-d',$datestamp)==date('Y-m-d',time()+86400)) $date='Tomorrow'.$time;
	else $date=date('jS F Y',$datestamp).$time;
	return $date;
}

function make_time(&$time,$blank=false,$format=false){
	if (empty($format)){
		$format='G:i';
	}
	if (is_numeric($time) and !strpos($time,'.')){
		$time.=':00';
	}
	$stamp=strtotime($time);
	if (!empty($stamp)){
		$time=date($format,$stamp);
		return true;
	}
	if ($blank){
		$time=null;
		return true;
	}
	return false;
}

function month_limits($month,$year,$months=1){
	if (empty($month)){
		$month=date('n');
	}
	if (empty($year)){
		$year=date('Y');
	}
	$cal_date=strtotime($year.'-'.$month.'-01 00:00:00');
	$limits['date_start']=$cal_date=inc_date($cal_date,array('day'=>-1*(date('N',$cal_date)-1)),1);
	while (!$complete and $n<($months*30)+14){
		$next_date=inc_date($cal_date,array('day'=>1),1);
		$complete=(date('n',$next_date)==$month+$months and date('N',$next_date)==1);
		if (empty($complete)){
			$cal_date=$next_date;
		}
		$n++;
	}
	$limits['date_end']=$cal_date;
	return $limits;
}

function sql_dat($date,&$error=null){
	if (empty($date)){
		return false;
	}
	if (!is_numeric($date)){
		$stamp=string_time($date,$error);
		if ($stamp===false){
			return false;
		}
	}
	else {
		$stamp=$date;
	}
	$date=date('Y-m-d',$stamp);
	return $date;
}

function sql_date($date,&$error=null){
	if (empty($date)){
		return false;
	}
	if (!is_numeric($date)){
		$stamp=string_time($date,$error);
		if ($stamp===false){
			return false;
		}
	}
	else {
		$stamp=$date;
	}
	$date=date('Y-m-d H:i:s',$stamp);
	return $date;
}

function sql_time($time,$stamp=null,&$error=null){
	if (empty($stamp)){
		$stamp=strtotime($time);
		if (empty($stamp)){
			$error='The time entered ('.$time.') wasn\'t recognised as a valid time. Try writing it in the format hh:mm.';
			return false;
		}
	}
	else {
		$stamp=$time;
	}
	$time=date('H:i:s',$stamp);
	return $time;
}

function string_time($date,&$error=null){
	if (substr_count($date,'/')==1){
		$date='01/'.$date;
	}
	$date=preg_replace('/(?<![0-9])([0-9]{1,2})-([0-9]{1,2})-([0-9]{2,4})/','$1/$2/$3',$date);
	$date=preg_replace('/(?<![0-9])([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{2,4})/','$1/$2/$3',$date);
	if (!defined('DATE_USA')){ // converts date to USA m/d/y format
		$date=preg_replace('/([0-9]{1,2})\/([0-9]{1,2})\//','$2/$1/',$date);
	}
	$stamp=strtotime($date);
	if (empty($stamp)){
		$error='The date entered wasn\'t recognised as a valid date. Try writing it in the format dd/mm/yyyy.';
		return false;
	}
	return $stamp;
}

function time_ago($time){
	$time=time()-strtotime($time);
	if ($time>32536000){
		$time/=32536000;
		$time=plural('year',round($time,0)).' ago';
	}
	elseif ($time>86400){
		$time/=86400;
		$time=plural('day',round($time,0)).' ago';
	}
	elseif ($time>3600){
		$time/=3600;
		$time=plural('hour',round($time,0)).' ago';
	}
	elseif ($time>60){
		$time/=60;
		$time=plural('minute',round($time,0)).' ago';
	}
	else {
		$time=plural('second',$time).' ago';
	}
	return $time;
}

function us_date($date){
	$date=explode('/',$date);
	$date=$date[1].'/'.$date[0].'/'.$date[2];
	return $date;
}

function year_list($duration,$start=false){
	$year=date('Y');
	$custom=$format!='Y' ? date($format) : $year;
	if ($duration<0){
		for ($n=0;$n>$duration;$n--){
			$vals[]=$custom+$n;
			$names[]=$year+$n;
		}
	}
	else {
		for ($n=0;$n<$duration;$n++){
			$vals[]=$custom+$n;
			$names[]=$year+$n;
		}
	}
	return array('names'=>$names,'vals'=>$vals);
}

function year_list_assoc($duration,$format){
	$year=date('Y');
	$custom=$format!='Y' ? date($format) : $year;
	if ($duration<0){
		for ($n=0;$n>$duration;$n--){
			$dates[$year+$n]=$custom+$n;
		}
	}
	else {
		for ($n=0;$n<$duration;$n++){
			$dates[$year+$n]=$custom+$n;
		}
	}
	return $dates;
}
