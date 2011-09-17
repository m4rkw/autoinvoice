<?
class CalendarTest extends PHPUnit_Framework_TestCase {
	function __construct() {
		require_once(dirname(__FILE__)."/../../libraries/Calendar.php");
	}

	function test_construct_loads_config() {
		$c = new Calendar;

		$this->assertInternalType('array', $c->config);

		$this->assertArrayHasKey('company_name', $c->config);
		$this->assertArrayHasKey('vat_registration_no', $c->config);
		$this->assertArrayHasKey('address1', $c->config);
		$this->assertArrayHasKey('address2', $c->config);
		$this->assertArrayHasKey('telephone', $c->config);
		$this->assertArrayHasKey('email', $c->config);
		$this->assertArrayHasKey('attention_of_name', $c->config);
		$this->assertArrayHasKey('attention_of_position', $c->config);
		$this->assertArrayHasKey('attention_of_address1', $c->config);
		$this->assertArrayHasKey('attention_of_address2', $c->config);
		$this->assertArrayHasKey('attention_of_address3', $c->config);
		$this->assertArrayHasKey('project_title', $c->config);
		$this->assertArrayHasKey('terms', $c->config);
		$this->assertArrayHasKey('day_rate', $c->config);
		$this->assertArrayHasKey('vat_rate', $c->config);
		$this->assertArrayHasKey('bank', $c->config);
		$this->assertArrayHasKey('bank_address', $c->config);
		$this->assertArrayHasKey('account_name', $c->config);
		$this->assertArrayHasKey('account_number', $c->config);
		$this->assertArrayHasKey('sort_code', $c->config);
		$this->assertArrayHasKey('signed_name', $c->config);
		$this->assertArrayHasKey('calendar_entry', $c->config);
		$this->assertArrayHasKey('calendar_sick_entry', $c->config);
		$this->assertArrayHasKey('google_username', $c->config);
		$this->assertArrayHasKey('google_password', $c->config);
		$this->assertArrayHasKey('invoice_path', $c->config);
		$this->assertArrayHasKey('invoice_filename_format', $c->config);
		$this->assertArrayHasKey('invoice_file_find_by_date', $c->config);
		$this->assertArrayHasKey('invoice_email_destination', $c->config);
		$this->assertArrayHasKey('invoice_email_sender_address', $c->config);
		$this->assertArrayHasKey('invoice_email_sender_name', $c->config);
		$this->assertArrayHasKey('invoice_email_subject', $c->config);
		$this->assertArrayHasKey('timesheet_name', $c->config);
		$this->assertArrayHasKey('timesheet_jobtitle', $c->config);
		$this->assertArrayHasKey('timesheet_client', $c->config);
		$this->assertArrayHasKey('timesheet_client_location', $c->config);
		$this->assertArrayHasKey('timesheet_lead_time_days', $c->config);
		$this->assertArrayHasKey('timesheet_time_start', $c->config);
		$this->assertArrayHasKey('timesheet_time_finish', $c->config);
		$this->assertArrayHasKey('timesheet_path', $c->config);
		$this->assertArrayHasKey('timesheet_filename_format', $c->config);
		$this->assertArrayHasKey('timesheet_file_find_by_date', $c->config);
		$this->assertArrayHasKey('timesheet_email_destination', $c->config);
		$this->assertArrayHasKey('timesheet_email_sender_address', $c->config);
		$this->assertArrayHasKey('timesheet_email_sender_name', $c->config);
		$this->assertArrayHasKey('timesheet_email_subject', $c->config);
	}

	function test_get_billable_days_since() {
		$c = new Calendar;

		$billable_days = $c->get_billable_days_since("2011-01-01", 1296432000);

		$this->assertEquals(array(
			'2011-01-04', // Tuesday
			'2011-01-05', // Wednesday
			'2011-01-06', // Thursday
			'2011-01-07', // Friday
			'2011-01-10', // Monday
			'2011-01-11', // Tuesday
			'2011-01-12', // Wednesday
			'2011-01-13', // Thursday
			'2011-01-14', // Friday
			'2011-01-17', // Monday
			'2011-01-18', // Tuesday
			'2011-01-19', // Wednesday
			'2011-01-20', // Thursday
			'2011-01-21', // Friday
			'2011-01-24', // Monday
			'2011-01-25', // Tuesday
			'2011-01-26', // Wednesday
			'2011-01-27', // Thursday
			'2011-01-28', // Friday
			'2011-01-31'	// Monday
		),
		$billable_days);
	}

	function test_get_billable_days_since_with_sick_days() {
		$c = new Calendar;

		$sick_days = array(
			'2011-01-07',
			'2011-01-12',
			'2011-01-26'
		);

		$billable_days = $c->get_billable_days_since("2011-01-01", 1296432000,$sick_days);

		$this->assertEquals(array(
			'2011-01-04', // Tuesday
			'2011-01-05', // Wednesday
			'2011-01-06', // Thursday
			//'2011-01-07', // Friday - SICK
			'2011-01-10', // Monday
			'2011-01-11', // Tuesday
			//'2011-01-12', // Wednesday - SICK
			'2011-01-13', // Thursday
			'2011-01-14', // Friday
			'2011-01-17', // Monday
			'2011-01-18', // Tuesday
			'2011-01-19', // Wednesday
			'2011-01-20', // Thursday
			'2011-01-21', // Friday
			'2011-01-24', // Monday
			'2011-01-25', // Tuesday
			//'2011-01-26', // Wednesday - SICK
			'2011-01-27', // Thursday
			'2011-01-28', // Friday
			'2011-01-31'	// Monday
		),
		$billable_days);
	}

	function test_get_days_as_invoice_transactions() {
		$c = new Calendar;

		$transactions = $c->get_days_as_invoice_transactions(array(
			'2011-01-04', // Tuesday
			'2011-01-05', // Wednesday
			'2011-01-06', // Thursday
			'2011-01-07', // Friday
			'2011-01-10', // Monday
			'2011-01-11', // Tuesday
			'2011-01-12', // Wednesday
			'2011-01-13', // Thursday
			'2011-01-14', // Friday
			'2011-01-17', // Monday
			'2011-01-18', // Tuesday
			'2011-01-19', // Wednesday
			'2011-01-20', // Thursday
			'2011-01-21', // Friday
			'2011-01-24', // Monday
			'2011-01-25', // Tuesday
			'2011-01-26', // Wednesday
			'2011-01-27', // Thursday
			'2011-01-28', // Friday
			'2011-01-31'	// Monday
		));

		$this->assertEquals(array(
			0 => array(
				'title' => 'January 4th - January 7th',
				'days' => 4
			),
			1 => array(
				'title' => 'January 10th - January 14th',
				'days' => 5
			),
			2 => array(
				'title' => 'January 17th - January 21st',
				'days' => 5
			),
			3 => array(
				'title' => 'January 24th - January 28th',
				'days' => 5
			),
			4 => array(
				'title' => 'January 31st - January 31st',
				'days' => 1
			)
		),
		$transactions);
	}

	function test_get_days_as_timesheet_entries() {
		$c = new Calendar;
		
		$entries = $c->get_days_as_timesheet_entries(array(
			'2011-01-04', // Tuesday
			'2011-01-05', // Wednesday
			'2011-01-06', // Thursday
			'2011-01-07', // Friday
			'2011-01-10', // Monday
			'2011-01-11', // Tuesday
			'2011-01-12', // Wednesday
			'2011-01-13', // Thursday
			'2011-01-14', // Friday
			'2011-01-17', // Monday
			'2011-01-18', // Tuesday
			'2011-01-19', // Wednesday
			'2011-01-20', // Thursday
			'2011-01-21', // Friday
			'2011-01-24', // Monday
			'2011-01-25', // Tuesday
			'2011-01-26', // Wednesday
			'2011-01-27', // Thursday
			'2011-01-28', // Friday
			'2011-01-31'	// Monday
		));

		$this->assertEquals(array(
			0 => array(
				'day' => 'Tuesday',
				'date' => '4 Jan 2011',
				'start' => '09:00',
				'finish' => '17:00'
			),
			1 => array(
				'day' => 'Wednesday',
				'date' => '5 Jan 2011',
				'start' => '09:00',
				'finish' => '17:00'
			),
			2 => array(
				'day' => 'Thursday',
				'date' => '6 Jan 2011',
				'start' => '09:00',
				'finish' => '17:00'
			),
			3 => array(
				'day' => 'Friday',
				'date' => '7 Jan 2011',
				'start' => '09:00',
				'finish' => '17:00'
			),
			4 => array(
				'day' => 'Monday',
				'date' => '10 Jan 2011',
				'start' => '09:00',
				'finish' => '17:00'
			),
			5 => array(
				'day' => 'Tuesday',
				'date' => '11 Jan 2011',
				'start' => '09:00',
				'finish' => '17:00'
			),
			6 => array(
				'day' => 'Wednesday',
				'date' => '12 Jan 2011',
				'start' => '09:00',
				'finish' => '17:00'
			),
			7 => array(
				'day' => 'Thursday',
				'date' => '13 Jan 2011',
				'start' => '09:00',
				'finish' => '17:00'
			),
			8 => array(
				'day' => 'Friday',
				'date' => '14 Jan 2011',
				'start' => '09:00',
				'finish' => '17:00'
			),
			9 => array(
				'day' => 'Monday',
				'date' => '17 Jan 2011',
				'start' => '09:00',
				'finish' => '17:00'
			),
			10 => array(
				'day' => 'Tuesday',
				'date' => '18 Jan 2011',
				'start' => '09:00',
				'finish' => '17:00'
			),
			11 => array(
				'day' => 'Wednesday',
				'date' => '19 Jan 2011',
				'start' => '09:00',
				'finish' => '17:00'
			),
			12 => array(
				'day' => 'Thursday',
				'date' => '20 Jan 2011',
				'start' => '09:00',
				'finish' => '17:00'
			),
			13 => array(
				'day' => 'Friday',
				'date' => '21 Jan 2011',
				'start' => '09:00',
				'finish' => '17:00'
			),
			14 => array(
				'day' => 'Monday',
				'date' => '24 Jan 2011',
				'start' => '09:00',
				'finish' => '17:00'
			),
			15 => array(
				'day' => 'Tuesday',
				'date' => '25 Jan 2011',
				'start' => '09:00',
				'finish' => '17:00'
			),
			16 => array(
				'day' => 'Wednesday',
				'date' => '26 Jan 2011',
				'start' => '09:00',
				'finish' => '17:00'
			),
			17 => array(
				'day' => 'Thursday',
				'date' => '27 Jan 2011',
				'start' => '09:00',
				'finish' => '17:00'
			),
			18 => array(
				'day' => 'Friday',
				'date' => '28 Jan 2011',
				'start' => '09:00',
				'finish' => '17:00'
			),
			19 => array(
				'day' => 'Monday',
				'date' => '31 Jan 2011',
				'start' => '09:00',
				'finish' => '17:00'
			)
		),
		$entries);
	}
}
?>
