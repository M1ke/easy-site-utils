<?php
/* Constants
	DATE_FORMAT - a PHP formatted datetime string (defaults to d/m/Y)
	DATE_USA - set if dates are expected to be passed in USA format "m/d/y" (defaults to false)
*/

function age_from_dob($date, $current_date = null){
	$components = ['year' => 'Y', 'month' => 'm', 'day' => 'd'];
	$compare_date = [];
	if (is_null($current_date)){
		foreach ($components as $key => $php_date){
			$compare_date[$key] = date($php_date);
		}
	}
	else {
		$current_date = explode('-', $current_date);
		foreach (array_keys($components) as $n => $key){
			$compare_date[$key] = 1 * $current_date[$n];
		}
	}

	$date = date_components($date);
	date_year_correct_($date['year']);
	$diff = [];
	foreach ($components as $key => $php_date){
		$diff[$key] = $compare_date[$key] - $date[$key];
	}
	if ($diff['month']<0 or ($diff['day']<0 and $diff['month']==0)){
		$diff['year']--;
	}

	return $diff['year'];
}

function arr_dates(){
	return [
		1 => '1st',
		2 => '2nd',
		3 => '3rd',
		4 => '4th',
		5 => '5th',
		6 => '6th',
		7 => '7th',
		8 => '8th',
		9 => '9th',
		10 => '10th',
		11 => '11th',
		12 => '12th',
		13 => '13th',
		14 => '14th',
		15 => '15th',
		16 => '16th',
		17 => '17th',
		18 => '18th',
		19 => '19th',
		20 => '20th',
		21 => '21st',
		22 => '22nd',
		23 => '23rd',
		24 => '24th',
		25 => '25th',
		26 => '26th',
		27 => '27th',
		28 => '28th',
		29 => '29th',
		30 => '30th',
		31 => '31st',
	];
}

function arr_day(){
	return [
		1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday',
		5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday',
	];
}

function arr_hours(){
	return [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
}

function arr_hours_24(){
	return [
		0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19,
		20, 21, 22, 23,
	];
}

function arr_mins(){
	return [0, 5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60];
}

function arr_month(){
	return [
		1 => 'January', 2 => 'February', 3 => 'March', 4 => 'April', 5 => 'May',
		6 => 'June', 7 => 'July', 8 => 'August', 9 => 'September',
		10 => 'October', 11 => 'November', 12 => 'December',
	];
}

function arr_month_days(){
	return [
		1 => 31,
		2 => 28,
		3 => 31,
		4 => 30,
		5 => 31,
		6 => 30,
		7 => 31,
		8 => 31,
		9 => 30,
		10 => 31,
		11 => 30,
		12 => 31,
	];
}

/**
 * @param bool $lower
 *
 * @return array
 */
function arr_month_short($lower = false){
	$months = arr_month();
	foreach ($months as &$month){
		$month = substr($month, 0, 3);
		if ($lower){
			$month = strtolower($month);
		}
	}

	return $months;
}

/**
 * @param array $month_titles
 * @param int $current_month
 *
 * @return array
 */
function arr_month_order(array $month_titles, $current_month){
	$months = [];
	for ($n = 0; $n<12; $n++){
		$months[$current_month] = $month_titles[$current_month];

		$current_month++;
		if ($current_month>12){
			$current_month = 1;
		}
	}

	return $months;
}

function custom_date($date = null, $format = null, $sep = ' at '){
	if (is_numeric($date)){
		$stamp = $date;
	}
	elseif (is_date($date)) {
		$stamp = string_time($date, $error);
	}
	if (empty($stamp)){
		return '';
	}

	return custom_date_format($stamp, $format, $sep);
}

function custom_date_format($stamp, $format, $sep = ' at '){
	$default_date_format = defined('DATE_FORMAT') ? DATE_FORMAT : 'd/m/Y';
	if (is_array($format)){
		if (empty($format[0])){
			$format[0] = $default_date_format;
		}
		if (empty($format[1])){
			$format[1] = 'g:ia';
		}
		$date = [];
		foreach ($format as $format_split){
			$date[] = date($format_split, $stamp);
		}
		$date = implode($sep, $date);
	}
	else {
		if (empty($format)){
			$format = $default_date_format;
		}
		$date = date($format, $stamp);
	}

	return $date;
}

function date_age($date){
	$date = sql_date($date);
	list($Y, $m, $d) = explode('-', $date);

	return (date('md')<$m.$d ? date('Y') - $Y - 1 : date('Y') - $Y);
}

function date_components($date){
	$day = $month = '';
	if (strpos($date, '/')!==false){
		list($day, $month, $year) = explode('/', $date);
	}
	elseif (strpos($date, '.')!==false) {
		list($day, $month, $year) = explode('.', $date);
	}
	if (defined('DATE_USA')){
		$copy = $day;
		$day = $month;
		$month = $copy;
	}
	if (strpos($date, '-')!==false){
		list($year, $month, $day) = explode('-', $date);
	}
	if (empty($month)){
		return ['year' => '', 'month' => '', 'day' => ''];
	}
	date_year_correct_($year);

	return ['year' => $year, 'month' => $month, 'day' => $day];
}

function date_difference($date1, $return = '', $date2 = false, $pos = true){
	$date1 = string_time($date1);
	$date2 = empty($date2) ? time() : string_time($date2);
	if (empty($date1) or empty($date2)){
		return false;
	}
	$diff = $date2 - $date1;
	$diff = ($diff<0 and $pos) ? $diff * -1 : $diff;

	return !empty($return) ? seconds_convert($diff, $return) : $diff;
}

function date_display($date, $usa = false){
	if (!is_date($date)){
		return '';
	}
	list($year, $month, $day) = explode('-', $date);
	if ($usa){
		$copy = $day;
		$day = $month;
		$month = $copy;
	}

	return !empty($day) ? $day.'/'.$month.'/'.$year : '';
}

function date_display_check(&$date){
	$date = !is_date($date) ? '' : date_display($date);

	return $date;
}

function date_from_dob($date){
	$date = custom_date($date);
	return date_store($date);
}

function date_inc($date, $inc){
	$date = date_components($date);
	foreach ($inc as $key => $val){
		$date[$key] += $val;
	}

	return date_stitch($date);
}

function date_list($duration, $start = false){
	if (!$start){
		$start = strtotime(date('Y-m-d'));
	}
	elseif (!is_numeric($start)) {
		$start = strtotime($start);
	}
	$vals = [$start];
	$names = [custom_date($start, 'd/m/Y')];
	for ($n = 1; $n<$duration; $n++){
		$stamp = inc_date($start, ['date' => $n], 1);
		$vals[] = $stamp;
		$names[] = custom_date($stamp, 'd/m/Y');
	}

	return ['names' => $names, 'vals' => $vals];
}

function date_list_assoc($duration, $start = false){
	if (!$start){
		$start = time();
	}
	elseif (!is_numeric($start)) {
		$start = strtotime($start);
	}
	$dates[inc_date($start, null, 1)] = custom_date(time(), 'd/m/Y');
	for ($n = 1; $n<$duration; $n++){
		$stamp = inc_date($start, ['date' => $n], 1);
		$vals[] = $stamp;
		$dates[$stamp] = custom_date($stamp, 'd/m/Y');
	}

	return $dates;
}

function date_nearest_day($direction, $date_today, $day, $format = 'Y-m-d'){
	if (custom_date($date_today, 'D')===ucfirst($day)){
		return custom_date($date_today, $format);
	}

	$time_today = string_time($date_today);
	$time_day = strtotime("$direction $day", $time_today);

	return date($format, $time_day);
}

function date_next_day($date_today, $day, $format = 'Y-m-d'){
	return date_nearest_day('next', $date_today, $day, $format);
}

function date_not_weekend($date, $dir){
	$date = sql_dat($date);

	$bank_holidays = uk_bank_holidays();

	$day = custom_date($date, 'N'); // 6 is Sat, 7 is Sun
	$day -= 5;

	while (in_array($date, $bank_holidays) || $day>0){
		if ($day>0){
			if ($dir>0){
				$day = 3 - $day;
			}
			$date = inc_date($date, ['day' => $dir * $day], false, 'Y-m-d');
		}
		elseif (in_array($date, $bank_holidays)) {
			$date = inc_date($date, ['day' => $dir], false, 'Y-m-d');
		}

		$day = custom_date($date, 'N');
		$day -= 5;
	}

	return $date;
}

function date_not_weekend_backward($date){
	return date_not_weekend($date, -1);
}

function date_not_weekend_forward($date){
	return date_not_weekend($date, 1);
}

function date_prev_day($date_today, $day, $format = 'Y-m-d'){
	return date_nearest_day('last', $date_today, $day, $format);
}

function date_sever($date){
	return (strpos($date, ' ')>0 ? substr($date, 0, strpos($date, ' ')) : $date);
}

function date_split($string, $date, $time){
	$datestamp = strtotime($string);
	$array['date'] = date($date, $datestamp);
	$array['time'] = date($time, $datestamp);

	return $array;
}

function date_stitch($date){
	return empty($date) ? '' : $date['year'].'-'.$date['month'].'-'.$date['day'];
}

function date_store($date){
	$date = date_components($date);

	return date_stitch($date);
}

function date_working_backward($date, $days){
	$date = date_not_weekend_forward($date);

	for ($n = 0; $n<$days; $n++){
		$date = inc_date($date, ['day' => -1], false, 'Y-m-d');

		$date = date_not_weekend_backward($date);
	}

	return $date;
}

function date_working_forward($date, $days){
	$date = date_not_weekend_forward($date);

	for ($n = 0; $n<$days; $n++){
		$date = inc_date($date, ['day' => 1], false, 'Y-m-d');

		$date = date_not_weekend_forward($date);
	}

	return $date;
}

function date_year_correct_(&$year){
	$year = date_year_correct($year);
}

function date_year_correct($year){
	if (strlen($year)==2){
		if ($year>date('y')){
			// let's face it, we're doing well if this needs future proofing!
			$year = 1900 + $year;
		}
	}

	return $year;
}

function days_left($day, $date = null){
	if (!is_numeric($day)){
		$day = date('N', strtotime($day));
	}
	if (!empty($date)){
		$date = string_time($date);
	}
	if (empty($date)){
		$date = time();
	}
	$month = $month_now = date('n', $date);
	$count = 0;
	while ($month_now==$month){
		if (date('N', $date)==$day){
			$count++;
		}
		$date = inc_date($date, ['day' => 1], true);
		$month_now = date('n', $date);
	}

	return $count;
}

function dob_from_date($date){
	return date_display($date);
}

function inc_date($date = null, $inc = [], $stamp = false, $format = 'Y-m-d H:i:s'){
	if (!is_numeric($date)){
		$date = string_time($date);
	}
	if (empty($date)){
		$date = time();
	}
	// everything else calls it day; date refers to the whole value. this is here for legacy
	if (!empty($inc['date'])){
		$inc['day'] = $inc['date'];
	}
	$date = mktime(date('H', $date) + $inc['hour'], date('i', $date) + $inc['min'], date('s', $date) + $inc['second'], date('m', $date) + $inc['month'], date('d', $date) + $inc['day'], date('Y', $date) + $inc['year']);
	if ($stamp){
		return $date;
	}

	return custom_date_format($date, $format);
}

function inc_date_repeat($date, $inc, $repeat, $format = 'Y-m-d H:i:s'){
	foreach ($inc as &$val){
		$val *= $repeat;
	}

	return inc_date($date, $inc, false, $format);
}

function int_day($int){
	$arr = arr_day();

	return $arr[$int];
}

function int_month($int){
	$arr = arr_month();

	return $arr[$int];
}

function is_date($date){
	if (empty($date) or $date=='0000-00-00 00:00:00' or $date=='0000-00-00' or $date=='00:00:00'){
		return false;
	}

	return true;
}

function is_today($date){
	if (!is_numeric($date)){
		$date = strtotime($date);
	}
	if ($date<1){
		return false;
	}
	$today = strtotime(date('Y-m-d'));
	$sub = $date - $today;
	if ($sub<86400 and $sub>=0){
		return true;
	}

	return false;
}

function is_tomorrow($date){
	if (!is_numeric($date)){
		$date = strtotime($date);
	}
	if ($date<1){
		return false;
	}
	$today = strtotime(date('Y-m-d'));
	$sub = $date - $today;
	if ($sub<172800 and $sub>=86400){
		return true;
	}

	return false;
}

function list_months($length = null, $month = null, $year = null){
	$months = arr_month();
	if (empty($length)){
		$length = 12;
	}
	if (empty($month)){
		$month = date('n');
	}
	if (empty($year)){
		$year = date('Y');
	}
	$opts = [];
	for ($n = 0; $n<$length; $n++){
		$opts[$month.'-'.$year] = $months[$month].' '.$year;
		$month++;
		if ($month>12){
			$month = 1;
			$year++;
		}
	}

	return $opts;
}

// need to deprecate this, have it pass to custom_date
function make_date($date, $separator = ','){
	$datestamp = strtotime($date);
	$time = '';
	if ($separator!=null){
		$time = $separator.' '.date('g:ia', $datestamp);
	}
	if (date('Y-m-d', $datestamp)==date('Y-m-d')){
		$date = 'Today'.$time;
	}
	elseif (date('Y-m-d', $datestamp)==date('Y-m-d', time() - 86400)) {
		$date = 'Yesterday'.$time;
	}
	elseif (date('Y-m-d', $datestamp)==date('Y-m-d', time() + 86400)) {
		$date = 'Tomorrow'.$time;
	}
	else {
		$date = date('jS F Y', $datestamp).$time;
	}

	return $date;
}

function make_time(&$time, $blank = false, $format = false){
	if (empty($format)){
		$format = 'G:i';
	}
	if (is_numeric($time) and !strpos($time, '.')){
		$time .= ':00';
	}
	$stamp = strtotime($time);
	if (!empty($stamp)){
		$time = date($format, $stamp);

		return true;
	}
	if ($blank){
		$time = null;

		return true;
	}

	return false;
}

function month_limits($month, $year, $months = 1){
	if (empty($month)){
		$month = date('n');
	}
	if (empty($year)){
		$year = date('Y');
	}
	$cal_date = strtotime($year.'-'.$month.'-01 00:00:00');
	$limits['date_start'] = $cal_date = inc_date($cal_date, ['day' => -1 * (date('N', $cal_date) - 1)], 1);
	$complete = false;
	$n = 0;
	while (!$complete and $n<($months * 30) + 14){
		$next_date = inc_date($cal_date, ['day' => 1], 1);
		$complete = (date('n', $next_date)==$month + $months and date('N', $next_date)==1);
		if (empty($complete)){
			$cal_date = $next_date;
		}
		$n++;
	}
	$limits['date_end'] = $cal_date;

	return $limits;
}

/**
 * @param      $start_date
 * @param      $end_date
 * @param bool $both_ends_included
 * @param bool $round
 *
 * @return int
 */
function months_between($start_date, $end_date, $both_ends_included = false, $round = true){
	// RentsDashboardDisplay used a 30 day month, not both ends, rounded
	// Ajax\Manage\Rents\Periods used last month length days, both ends, not rounded

	$start_stamp = strtotime($start_date);
	$end_stamp = strtotime($end_date);
	$years = date('Y', $end_stamp) - date('Y', $start_stamp);
	$months = date('n', $end_stamp) - date('n', $start_stamp);
	$days = date('j', $end_stamp) - date('j', $start_stamp) + ($both_ends_included ? 1 : 0);

	$total_months = $years * 12 + $months + ($days / (arr_month_days()[date('n', $end_stamp)]));

	if ($round){
		$total_months = round($total_months);
	}

	return $total_months;
}

function seconds_convert($time, $return = 'day'){
	$started = false;
	switch ($return){
		case 'year':
			$time /= 365;
			$started = true;
		case 'quarter':
			if (!$started){
				$time *= 4 / 365;
				$started = true;
			}
		case 'month':
			if (!$started){
				$time *= 12 / 365;
				$started = true;
			}
		case 'week':
			if (!$started){
				$time /= 7;
			}
		case 'day':
			$time /= 24;
		case 'hour':
			$time /= 60;
		case 'minute':
			$time /= 60;
	}

	return floor($time);
}

function seconds_to_time($time, $seconds_only = ''){
	$seconds = $time % 60;
	if ($time>=60){
		$time /= 60;
		$minutes = $time % 60;
		if ($time>=60){
			$time /= 60;
			$hours = $time % 24;
			if ($time>=24){
				$time /= 24;
				$days = $time % 365;
				if ($time>=365){
					$time /= 365;
					$years = floor($time);
				}
			}
		}
	}
	$return = '';
	if (!empty($years)){
		$return = $years.' years';
	}
	if (!empty($days)){
		$return .= (!empty($return) ? ', ' : '').$days.' days';
	}
	$return .= (!empty($return) ? ', ' : '').(!empty($hours) ? $hours.':' : (!empty($return) ? '0:' : ''));
	$return .= !empty($minutes) ? $minutes.':' : (!empty($return) ? '00:' : '');
	$return .= !empty($return) ? zero_pad($seconds) : $seconds.(!empty($seconds) ? $seconds_only : '');

	return $return;
}

function sql_dat($date, &$error = null, $date_usa = false){
	if (!is_date($date)){
		return false;
	}
	if (!is_numeric($date)){
		$stamp = string_time($date, $error, $date_usa);
		if ($stamp===false){
			return false;
		}
	}
	else {
		$stamp = $date;
	}
	$date = date('Y-m-d', $stamp);

	return $date;
}

function sql_date($date, &$error = null, $date_usa = false){
	if (!is_date($date)){
		return false;
	}
	if (!is_numeric($date)){
		$stamp = string_time($date, $error, $date_usa);
		if ($stamp===false){
			return false;
		}
	}
	else {
		$stamp = $date;
	}
	$date = date('Y-m-d H:i:s', $stamp);

	return $date;
}

function sql_time($time, $stamp = null, &$error = null){
	if (empty($stamp)){
		$stamp = strtotime($time);
		if (empty($stamp)){
			$error = 'The time entered ('.$time.') wasn\'t recognised as a valid time. Try writing it in the format hh:mm.';

			return false;
		}
	}
	else {
		$stamp = $time;
	}
	$time = date('H:i:s', $stamp);

	return $time;
}

/**
 * @param string $date
 * @param string $error
 * @param bool $date_usa
 * @return bool|int
 */
function string_time($date, &$error = null, $date_usa = false){
	if (substr_count($date, '/')==1){
		$date = '01/'.$date;
	}
	$date = preg_replace('/(?<![0-9])([0-9]{1,2})-([0-9]{1,2})-([0-9]{2,4})/', '$1/$2/$3', $date);
	$date = preg_replace('/(?<![0-9])([0-9]{1,2})\.([0-9]{1,2})\.([0-9]{2,4})/', '$1/$2/$3', $date);
	if (!defined('DATE_USA') and !$date_usa){ // converts date to USA m/d/y format
		$date = preg_replace('/(?<![0-9])([0-9]{1,2})\/([0-9]{1,2})\//', '$2/$1/', $date);
	}
	$stamp = strtotime($date);
	if ($stamp===false){
		$error = 'The date entered wasn\'t recognised as a valid date. Try writing it in the format dd/mm/yyyy.';

		return false;
	}

	$first_jan_1900 = -2208988800;
	if ($stamp<$first_jan_1900){
		$error = 'The date entered was thought to be earlier than 1st Jan 1900. This suggests a potential error for most uses of this date.';

		return false;
	}

	$end_dec_2099 = 4102444800;
	if ($stamp>$end_dec_2099){
		$error = 'The date entered was thought to be later than 31st Dec 2099. This suggests a potential error for most uses of this date.';

		return false;
	}

	return $stamp;
}

function time_ago($time){
	$time = time() - strtotime($time);
	if ($time>32536000){
		$time /= 32536000;
		$time = plural('year', round($time, 0)).' ago';
	}
	elseif ($time>86400) {
		$time /= 86400;
		$time = plural('day', round($time, 0)).' ago';
	}
	elseif ($time>3600) {
		$time /= 3600;
		$time = plural('hour', round($time, 0)).' ago';
	}
	elseif ($time>60) {
		$time /= 60;
		$time = plural('minute', round($time, 0)).' ago';
	}
	else {
		$time = plural('second', $time).' ago';
	}

	return $time;
}

function uk_bank_holidays(){
	return [
		'2016-08-29',
		'2016-12-26',
		'2016-12-27',
		'2017-01-02',
		'2017-04-14',
		'2017-04-17',
		'2017-05-01',
		'2017-05-29',
		'2017-08-28',
		'2017-12-25',
		'2017-12-26',
		'2018-01-01',
		'2018-03-30',
		'2018-04-02',
		'2018-05-07',
		'2018-05-28',
		'2018-08-27',
		'2018-12-25',
		'2018-12-26',
		'2019-01-01',
		'2019-04-19',
		'2019-04-22',
		'2019-05-06',
		'2019-05-27',
		'2019-08-26',
		'2019-12-25',
		'2019-12-26',
	];
}

function us_date($date){
	$date = explode('/', $date);
	$date = $date[1].'/'.$date[0].'/'.$date[2];

	return $date;
}

function year_list($duration, $format){
	$year = date('Y');
	$custom = $format!='Y' ? date($format) : $year;
	$vals = $names = [];
	if ($duration<0){
		for ($n = 0; $n>$duration; $n--){
			$vals[] = $custom + $n;
			$names[] = $year + $n;
		}
	}
	else {
		for ($n = 0; $n<$duration; $n++){
			$vals[] = $custom + $n;
			$names[] = $year + $n;
		}
	}

	return ['names' => $names, 'vals' => $vals];
}

function year_list_assoc($duration, $format){
	$year = date('Y');
	$custom = $format!='Y' ? date($format) : $year;
	$dates = [];
	if ($duration<0){
		for ($n = 0; $n>$duration; $n--){
			$dates[$year + $n] = $custom + $n;
		}
	}
	else {
		for ($n = 0; $n<$duration; $n++){
			$dates[$year + $n] = $custom + $n;
		}
	}

	return $dates;
}
