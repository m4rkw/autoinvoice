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

		$this->client = Zend_Gdata_ClientLogin::getHttpClient($this->config['google_username'], $this->config['google_password'], Zend_Gdata_Calendar::AUTH_SERVICE_NAME);
	}

	// Checks the google calendar for today and looks for an invoice entry. If there is one we return the invoice number.
	function are_we_invoicing_today() {
		$gdataCal = new Zend_Gdata_Calendar($this->client);
		$query = $gdataCal->newEventQuery();
		$query->setUser('default');
		$query->setVisibility('private');
		$query->setProjection('full');
		$query->setOrderby('starttime');
		$query->setStartMin(date('Y-m-d'));
		$query->setStartMax(date('Y-m-d',time()+86400));
		foreach ($gdataCal->getCalendarEventFeed($query) as $event) {
			if (preg_match($this->config['calendar_entry'], $event->title->text, $m)) {
				return $m[1];
			}
		}
		return false;
	}

	// Return the most recent invoice before the one being generated today
	function get_date_of_previous_invoice() {
		$gdataCal = new Zend_Gdata_Calendar($this->client);
		$query = $gdataCal->newEventQuery();
		$query->setUser('default');
		$query->setVisibility('private');
		$query->setProjection('full');
		$query->setOrderby('starttime');
		$query->setStartMin(date('Y-m-d',time()-(86400 * 90)));
		$query->setStartMax(date('Y-m-d'));
		foreach ($gdataCal->getCalendarEventFeed($query) as $event) {
			if (preg_match($this->config['calendar_entry'], $event->title->text, $m)) {
				foreach ($event->when as $when) {
					return substr($when->startTime,0,10);
				}
			}
		}

		die("Unable to find a previous invoice entry in the calendar.\n");
	}

	// return all the billable days since $date
	function get_billable_days_since($date) {
		$timestamp = mktime(0,0,0,substr($date,5,2),substr($date,8,2),substr($date,0,4));

		$holidays = BankHolidays::get(date('Y',$timestamp));

		$billable_days = array();

		while (1) {
			$timestamp += 86400;

			if (!in_array(date('D',$timestamp),array('Sat','Sun')) && !in_array(date('Y-m-d',$timestamp),$holidays)) {
				$billable_days[] = date('Y-m-d',$timestamp);
			}

			if (date('Y-m-d',$timestamp) == date('Y-m-d')) break;
		}

		return $billable_days;
	}

	function get_days_as_invoice_transactions($days) {
		$transactions = array();

		foreach ($days as $day) {
			$timestamp = mktime(0,0,0,substr($day,5,2),substr($day,8,2),substr($day,0,4));

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
}
?>
