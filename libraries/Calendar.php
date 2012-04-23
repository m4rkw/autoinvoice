<?
class Calendar {
	function __construct() {
		$config = dirname(__FILE__)."/../config/config.php";
		if (!file_exists($config)) {
			die("Missing config file config/config.php\n");
		}

		include($config);
		$this->config = $config;

		require_once 'Zend/Loader.php';
		require_once dirname(__FILE__)."/BankHolidays.php";

		Zend_Loader::loadClass('Zend_Gdata');
		Zend_Loader::loadClass('Zend_Gdata_AuthSub');
		Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
		Zend_Loader::loadClass('Zend_Gdata_HttpClient');
		Zend_Loader::loadClass('Zend_Gdata_Calendar');
	}

	function get_calendar_events($ts_from=false, $ts_to=false, $reverse=false) {
		if (!$ts_from) $ts_from = time();
		if (!$ts_to) $ts_to = time();

		if (!isset($this->client)) {
			$this->client = Zend_Gdata_ClientLogin::getHttpClient($this->config['google_username'], $this->config['google_password'], Zend_Gdata_Calendar::AUTH_SERVICE_NAME);
		}

		$gdataCal = new Zend_Gdata_Calendar($this->client);
		$query = $gdataCal->newEventQuery();
		$query->setUser('default');
		$query->setVisibility('private');
		$query->setProjection('full');
		$query->setOrderby('starttime');
		$query->setStartMin(date('Y-m-d',$ts_from));
		$query->setStartMax(date('Y-m-d',($ts_to + 86400)));

		if ($reverse) {
			$items = array();
			foreach ($gdataCal->getCalendarEventFeed($query) as $item) {
				$items[] = $item;
			}
			return array_reverse($items);
		}

		return $gdataCal->getCalendarEventFeed($query);
	}

	// Checks the google calendar for today and looks for an invoice entry. If there is one we return the invoice number.
	function are_we_invoicing_today() {
		// see if an invoice exists with today's date
		$invoice_regex = str_replace('{{DATE}}',date('Ymd'),$this->config['invoice_file_find_by_date']);

		$dh = opendir($this->config['invoice_path']);

		while ($file = readdir($dh)) {
			if (preg_match($invoice_regex,$file)) {
				echo "Today's invoice has already been generated: $file\n";
				return false;
			}
		}

		closedir($dh);

		foreach ($this->get_calendar_events() as $event) {
			if (preg_match($this->config['calendar_entry'], $event->title->text, $m)) {
				return $m[1];
			}
		}

		return false;
	}

	// Return the most recent invoice before the one being generated today
	function get_date_of_previous_invoice() {
		foreach ($this->get_calendar_events(time()-(86400 * 90), time()-86400) as $event) {
			if (preg_match($this->config['calendar_entry'], $event->title->text, $m)) {
				foreach ($event->when as $when) {
					return substr($when->startTime,0,10);
				}
			}
		}

		die("Unable to find a previous invoice entry in the calendar.\n");
	}

	function get_date_of_next_invoice($last_invoice) {
		$ts = strtotime($last_invoice) + 86400;

		foreach ($this->get_calendar_events($ts, $ts + (90 * 86400), true) as $event) {
			if (preg_match($this->config['calendar_entry'], $event->title->text, $m)) {
				foreach ($event->when as $when) {
					return substr($when->startTime,0,10);
				}
			}
		}

		return false;
	}

	function get_calendar_marked_entries_for_period($date_from, $timestamp_to, $regex) {
		if (!$timestamp_to) $timestamp_to = time();

		$days = array();

		if ($regex) {
			foreach ($this->get_calendar_events(strtotime($date_from), $timestamp_to) as $event) {
				if (preg_match($regex, $event->title->text, $m)) {
					foreach ($event->when as $when) {
						$ts = strtotime(substr($when->startTime,0,10));

						while (1) {
							$days[] = date('Y-m-d',$ts);
							$ts += 86400;
							if (date('Y-m-d',$ts) == substr($when->endTime,0,10)) break;
						}
					}
				}
			}
		}

		return $days;
	}

	function get_sick_days_for_period($date_from, $timestamp_to=false) {
		return $this->get_calendar_marked_entries_for_period($date_from, $timestamp_to, $this->config['calendar_sick_entry']);
	}

	function get_holiday_days_for_period($date_from, $timestamp_to=false) {
		return $this->get_calendar_marked_entries_for_period($date_from, $timestamp_to, $this->config['calendar_holiday_entry']);
	}

	// return all the billable days from $date_from to $timestamp_to (inclusive)
	function get_billable_days_since($date_from, $timestamp_to, $sick_days=false, $holiday_days=false) {
		if (!$sick_days) $sick_days = array();
		if (!$holiday_days) $holiday_days = array();

		$timestamp = strtotime($date_from);

		$holidays = BankHolidays::get(date('Y',$timestamp));

		$billable_days = array();

		while (1) {
			$timestamp += 86400;

			if (!in_array(date('D',$timestamp),array('Sat','Sun')) && !in_array(date('Y-m-d',$timestamp),$holidays) && !in_array(date('Y-m-d',$timestamp),$sick_days) && !in_array(date('Y-m-d',$timestamp),$holiday_days)) {
				$billable_days[] = date('Y-m-d',$timestamp);
			}

			if (date('Y-m-d',$timestamp) == date('Y-m-d',$timestamp_to)) break;
		}

		return $billable_days;
	}

	function get_days_as_invoice_transactions($days) {
		$transactions = array();

		foreach ($days as $day) {
			$timestamp = strtotime($day);

			if (!isset($transaction)) {
				$transaction = array(
					'title' => date('F jS - ',$timestamp),
					'days' => 1
				);
			} else {
				if ($last_timestamp+86400 == $timestamp) {
					$transaction['days']++;
				} else {
					$transaction['title'] .= date('F jS',$last_timestamp);
					$transactions[] = $transaction;

					$transaction = array(
						'title' => date('F jS - ',$timestamp),
						'days' => 1
					);
				}
			}

			$last_timestamp = $timestamp;
		}

		$transaction['title'] .= date('F jS',$last_timestamp);
		$transactions[] = $transaction;

		return $transactions;
	}

	function get_days_as_timesheet_entries($days) {
		$entries = array();

		foreach ($days as $day) {
			$entries[] = array(
				'day' => date('l',strtotime($day)),
				'date' => date('j M Y',strtotime($day)),
				'start' => $this->config['timesheet_time_start'],
				'finish' => $this->config['timesheet_time_finish']
			);
		}

		return $entries;
	}

	function are_we_timesheeting_today() {
		// see if an timesheet exists with today's date
		$timesheet_regex = str_replace('{{DATE}}',date('Ymd',time()+($this->config['timesheet_lead_time_days'] * 86400)),$this->config['timesheet_file_find_by_date']);

		$dh = opendir($this->config['timesheet_path']);

		while ($file = readdir($dh)) {
			if (preg_match($timesheet_regex,$file)) { 
				echo "Today's timesheet has already been generated: $file\n";
				return false;
			} 
		}
	
		closedir($dh);

		foreach ($this->get_calendar_events(time()+($this->config['timesheet_lead_time_days'] * 86400), ((time()+($this->config['timesheet_lead_time_days']+1) * 86400)-86400)) as $event) {

			if (preg_match($this->config['calendar_entry'], $event->title->text, $m)) {
				return true;
			}
		}

		return false;
	}
}
?>
