<?php
require_once __DIR__.'/../init.php';

class TestDates extends PHPUnit_Framework_TestCase {

	function testDateComponents(){
		$date = '05/06/1988';
		$date = date_components($date);
		$this->assertEquals([
			'year' => 1988, 'month' => 06, 'day' => 05,
		], $date);
	}

	function testYearCorrectReturn(){
		$year = 88;
		$year = date_year_correct($year);
		$this->assertEquals(1988, $year);
	}

	function testYearCorrectRef(){
		$year = 88;
		date_year_correct_($year);
		$this->assertEquals(1988, $year);
	}

	function testAgeLastMonth(){
		$dob = '05/06/1988';
		$age = age_from_dob($dob, '2015-01-01');
		$this->assertEquals(26, $age);
	}

	function testSaturdaysInFeb2014(){
		$date = '01/02/2014';
		$saturdays = days_left(6, $date);
		$this->assertEquals(4, $saturdays);
	}

	function testSaturdaysMidFeb2014(){
		$date = '12/02/2014';
		$sats = days_left(6, $date);
		$this->assertEquals(2, $sats);
	}

	function testSaturdaysEndFeb2014(){
		$date = '28/02/2014';
		$sats = days_left(6, $date);
		$this->assertEquals(0, $sats);
	}

	// seconds_to_time
	function testSeconds(){
		$seconds = 20;
		$convert = seconds_to_time($seconds);
		$this->assertEquals('20', $convert);
	}

	function testSecondsMinute(){
		$seconds = 60;
		$convert = seconds_to_time($seconds);
		$this->assertEquals('1:00', $convert);
	}

	function testSecondsMinutes(){
		$seconds = 80;
		$convert = seconds_to_time($seconds);
		$this->assertEquals('1:20', $convert);
	}

	function testSecondsMinutesHours(){
		$seconds = (60 * 60) + 80;
		$convert = seconds_to_time($seconds);
		$this->assertEquals('1:1:20', $convert);
	}

	function testSecondsHour(){
		$seconds = (60 * 60);
		$convert = seconds_to_time($seconds);
		$this->assertEquals('1:00:00', $convert);
	}

	function testSecondsDay(){
		$seconds = (60 * 60 * 24);
		$convert = seconds_to_time($seconds);
		$this->assertEquals('1 days, 0:00:00', $convert);
	}

	function testSecondsMinutesHoursDays(){
		$seconds = (60 * 60 * 24) + 80;
		$convert = seconds_to_time($seconds);
		$this->assertEquals('1 days, 0:1:20', $convert);
	}

	function testSecondsYear(){
		$seconds = (60 * 60 * 24 * 365);
		$convert = seconds_to_time($seconds);
		$this->assertEquals('1 years, 0:00:00', $convert);
	}

	function testSecondsMinutesHoursDaysYears(){
		$seconds = (60 * 60 * 24 * 365) + 80;
		$convert = seconds_to_time($seconds);
		$this->assertEquals('1 years, 0:1:20', $convert);
	}

	function testSecondsMinutesHoursDaysYearsBoth(){
		$seconds = (60 * 60 * 24 * 365) + (60 * 60 * 24) + 80;
		$convert = seconds_to_time($seconds);
		$this->assertEquals('1 years, 1 days, 0:1:20', $convert);
	}

	// seconds_convert
	function testSecondsToDays(){
		$seconds = (60 * 60 * 24 * 35);
		$convert = seconds_convert($seconds, 'day');
		$this->assertEquals(35, $convert);
	}

	function testSecondsToWeeks(){
		$seconds = (60 * 60 * 24 * 35);
		$convert = seconds_convert($seconds, 'week');
		$this->assertEquals(5, $convert);
	}

	function testSecondsToMonths(){
		$seconds = (60 * 60 * 24 * 35);
		$convert = seconds_convert($seconds, 'month');
		$this->assertEquals(1, $convert);
	}

	function testSecondsToQuarters(){
		$seconds = (60 * 60 * 24 * 285);
		$convert = seconds_convert($seconds, 'quarter');
		$this->assertEquals(3, $convert);
	}

	function testSecondsToYears(){
		$seconds = (60 * 60 * 24 * 385);
		$convert = seconds_convert($seconds, 'year');
		$this->assertEquals(1, $convert);
	}

	function testSecondsNotYears(){
		$seconds = (60 * 60 * 24 * 364);
		$convert = seconds_convert($seconds, 'year');
		$this->assertEquals(0, $convert);
	}

	function testStringToTimeAm(){
		$time = '9:24am';
		make_time($time, false, 'H:i:s');
		$this->assertEquals('09:24:00', $time);
	}

	function testStringToTimePm(){
		$time = '9:24pm';
		make_time($time, false, 'H:i:s');
		$this->assertEquals('21:24:00', $time);
	}

	function testStringToTime24h(){
		$time = '14:24';
		make_time($time, false, 'H:i:s');
		$this->assertEquals('14:24:00', $time);
	}

	function testStringToTime24hPm(){
		$time = '14:24pm';
		make_time($time);
		$this->assertEquals('14:24pm', $time);
	}

	// sql_dat
	function testSqlDatUTC(){
		$date = '2015-02-13';
		$dat = sql_dat($date);
		$this->assertEquals('2015-02-13', $dat);
	}

	function testSqlDatUK(){
		$date = '13/02/2015';
		$dat = sql_dat($date);
		$this->assertEquals('2015-02-13', $dat);
	}

	function testSqlDatUKShort(){
		$date = '1/2/15';
		$dat = sql_dat($date);
		$this->assertEquals('2015-02-01', $dat);
	}

	function testSqlDatUKDot(){
		$date = '13.02.2015';
		$dat = sql_dat($date);
		$this->assertEquals('2015-02-13', $dat);
	}

	function testSqlDatUKDash(){
		$date = '13-02-2015';
		$dat = sql_dat($date);
		$this->assertEquals('2015-02-13', $dat);
	}

	function testSqlDatUSA(){
		$date = '02/13/2015';
		$dat = sql_dat($date, $error, true);
		$this->assertEquals('2015-02-13', $dat);
	}

	function testSqlDatUSAShort(){
		$date = '2/1/15';
		$dat = sql_dat($date, $error, true);
		$this->assertEquals('2015-02-01', $dat);
	}

	function testSqlDatYmd(){
		$date = '2015/02/13';
		$dat = sql_dat($date);
		$this->assertEquals('2015-02-13', $dat);
	}

	function testSqlDatYmdShort(){
		$date = '2015/2/13'; // cant do short year in this format - would just be silly!
		$dat = sql_dat($date);
		$this->assertEquals('2015-02-13', $dat);
	}
}
