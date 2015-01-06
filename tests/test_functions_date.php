<?php
require_once __DIR__.'/../init.php';

class TestDates extends PHPUnit_Framework_TestCase {
	// this needs to be increased on 5th June each year
	private $howOldIsMike=26;

	function testDateComponents(){
		$date='05/06/1988';
		$date=date_components($date);
		$this->assertEquals($date,array('year'=>1988,'month'=>06,'day'=>05));
	}
	function testYearCorrectReturn(){
		$year=88;
		$year=date_year_correct($year);
		$this->assertEquals($year,1988);
	}
	function testYearCorrectRef(){
		$year=88;
		date_year_correct_($year);
		$this->assertEquals($year,1988);
	}
	function testAgeLastMonth(){
		$dob='05/06/1988';
		$age=age_from_dob($dob);
		$this->assertEquals($age,$this->howOldIsMike);
	}

	function testSaturdaysInFeb2014(){
		$date='01/02/2014';
		$sats=days_left(6,$date);
		$this->assertEquals($sats,4);
	}
	function testSaturdaysMidFeb2014(){
		$date='12/02/2014';
		$sats=days_left(6,$date);
		$this->assertEquals($sats,2);
	}
	function testSaturdaysEndFeb2014(){
		$date='28/02/2014';
		$sats=days_left(6,$date);
		$this->assertEquals($sats,0);
	}

	// seconds_to_time
	function testSeconds(){
		$seconds=20;
		$convert=seconds_to_time($seconds);
		$this->assertEquals($convert,'20');
	}
	function testSecondsMinutes(){
		$seconds=80;
		$convert=seconds_to_time($seconds);
		$this->assertEquals($convert,'1:20');
	}
	function testSecondsMinutesHours(){
		$seconds=(60*60)+80;
		$convert=seconds_to_time($seconds);
		$this->assertEquals($convert,'1:1:20');
	}
	function testSecondsMinutesHoursDays(){
		$seconds=(60*60*24)+80;
		$convert=seconds_to_time($seconds);
		$this->assertEquals($convert,'1 days, 0:1:20');
	}
	function testSecondsMinutesHoursDaysYears(){
		$seconds=(60*60*24*365)+80;
		$convert=seconds_to_time($seconds);
		$this->assertEquals($convert,'1 years, 0:1:20');
	}
	function testSecondsMinutesHoursDaysYearsBoth(){
		$seconds=(60*60*24*365)+(60*60*24)+80;
		$convert=seconds_to_time($seconds);
		$this->assertEquals($convert,'1 years, 1 days, 0:1:20');
	}

	// seconds_convert
	function testSecondsToDays(){
		$seconds=(60*60*24*35);
		$convert=seconds_convert($seconds,'day');
		$this->assertEquals(35,$convert);
	}
	function testSecondsToWeeks(){
		$seconds=(60*60*24*35);
		$convert=seconds_convert($seconds,'week');
		$this->assertEquals(5,$convert);
	}
	function testSecondsToMonths(){
		$seconds=(60*60*24*35);
		$convert=seconds_convert($seconds,'month');
		$this->assertEquals(1,$convert);
	}
	function testSecondsToQuarters(){
		$seconds=(60*60*24*285);
		$convert=seconds_convert($seconds,'quarter');
		$this->assertEquals(3,$convert);
	}
	function testSecondsToYears(){
		$seconds=(60*60*24*385);
		$convert=seconds_convert($seconds,'year');
		$this->assertEquals(1,$convert);
	}
	function testSecondsNotYears(){
		$seconds=(60*60*24*364);
		$convert=seconds_convert($seconds,'year');
		$this->assertEquals(0,$convert);
	}

	function testStringToTimeAm(){
		$time='9:24am';
		make_time($time,false,'H:i:s');
		$this->assertEquals($time,'09:24:00');
	}
	function testStringToTimePm(){
		$time='9:24pm';
		make_time($time,false,'H:i:s');
		$this->assertEquals($time,'21:24:00');
	}
	function testStringToTime24h(){
		$time='14:24';
		make_time($time,false,'H:i:s');
		$this->assertEquals($time,'14:24:00');
	}
	function testStringToTime24hPm(){
		$time='14:24pm';
		make_time($time);
		$this->assertEquals($time,'14:24:00');
	}
}
