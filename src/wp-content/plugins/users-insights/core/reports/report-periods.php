<?php 

class USIN_Report_Periods{

	const DATE_FORMAT = 'Y-m-d H:i:s';

	const DAILY_NUM_DAYS = 10;
	const WEEKLY_NUM_WEEKS = 8;
	const MONTHLY_NUM_MONTHS = 12;
	const YEARLY_NUM_YEARS = 10;


	public static function daily($current_time, $num_days = null){
		if($num_days == null){
			$num_days = self::DAILY_NUM_DAYS;
		}
		
		$periods = array();
		
		$time = $current_time;
		
		for($i = 1; $i<= $num_days; $i++){
			$start = strtotime("midnight", $time);
			$name = $i==1 ? __('Today', 'usin') : self::format_date($start, 'd M');

			$periods[]= array(
				'start' => self::format_date($start),
				'end' =>  self::format_date(strtotime("tomorrow", $start) - 1),
				'name' => $name
			);
			$time -= DAY_IN_SECONDS;
		}

		return array_reverse($periods);
	}


	public static function weekly($current_time, $num_weeks = null){
		if($num_weeks == null){
			$num_weeks = self::WEEKLY_NUM_WEEKS;
		}

		$periods = array();

		$this_monday = strtotime("this week", $current_time);
		$start = strtotime("midnight", $this_monday);
		
		for($i = 1; $i<= $num_weeks; $i++){
			$end = $start + 7*DAY_IN_SECONDS - 1;
			$name = $i==1 ? __('This week', 'usin') : sprintf('%s - %s', self::format_date($start, 'd M'), self::format_date($end, 'd M'));

			$periods[]= array(
				'start' => self::format_date($start),
				'end' =>  self::format_date($end),
				'name' => $name
			);
			$start -= 7*DAY_IN_SECONDS;
		}

		return array_reverse($periods);
	}



	public static function monthly($current_time, $num_months = null){
		if($num_months == null){
			$num_months = self::MONTHLY_NUM_MONTHS;
		}

		$periods = array();

		$time = $current_time;
		
		for($i = 1; $i<= $num_months; $i++){
			$first_day = strtotime("first day of this month", $time);
			$start = strtotime("midnight", $first_day);

			$last_day = strtotime("last day of this month", $time);
			$end = strtotime("tomorrow", $last_day) - 1;

			$periods[]= array(
				'start' => self::format_date($start),
				'end' =>  self::format_date($end),
				'name' => self::format_date($start, 'M y')
			);
			$time = strtotime("first day of previous month", $time);
		}

		return array_reverse($periods);
	}



	public static function yearly($current_time, $num_years = null){
		if($num_years == null){
			$num_years = self::YEARLY_NUM_YEARS;
		}

		$periods = array();

		$time = $current_time;
		
		for($i = 1; $i<= $num_years; $i++){
			$first_day = strtotime("first day of January ".date('Y', $time), $time);
			$start = strtotime("midnight", $first_day);

			$last_day = strtotime("last day of December ".date('Y', $time), $time);
			$end = strtotime("tomorrow", $last_day) - 1;

			$periods[]= array(
				'start' => self::format_date($start),
				'end' =>  self::format_date($end),
				'name' => self::format_date($start, 'Y')
			);
			$time = strtotime("previous year", $time);
		}

		return array_reverse($periods);
	}



	protected static function format_date($timestamp, $format = self::DATE_FORMAT){
		return date($format, $timestamp);
	}

}