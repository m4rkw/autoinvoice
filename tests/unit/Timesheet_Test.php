<?
class TimesheetTest extends PHPUnit_Framework_TestCase {
	function __construct() {
		require_once(dirname(__FILE__)."/../../libraries/Timesheet.php");
	}

	function test_construct_sets_output_dir() {
		$cwd = getcwd();
		$i = new Timesheet;
		$this->assertEquals($cwd, $i->output_dir);
	}

	function test_construct_sets_config_params() {
		$i = new Timesheet;

		foreach (array(
			'output_dir',
			'template',
			'company_name',
			'vat_registration_no',
			'address1',
			'address2',
			'telephone',
			'email',
			'attention_of_name',
			'attention_of_position',
			'attention_of_address1',
			'attention_of_address2',
			'attention_of_address3',
			'project_title',
			'terms',
			'day_rate',
			'vat_rate',
			'bank',
			'bank_address',
			'account_name',
			'account_number',
			'sort_code',
			'signed_name',
			'calendar_entry',
			'calendar_sick_entry',
			'google_username',
			'google_password',
			'invoice_path',
			'invoice_filename_format',
			'invoice_file_find_by_date',
			'invoice_email_destination',
			'invoice_email_sender_address',
			'invoice_email_sender_name',
			'invoice_email_subject',
			'timesheet_name',
			'timesheet_jobtitle',
			'timesheet_client',
			'timesheet_client_location',
			'timesheet_lead_time_days',
			'timesheet_time_start',
			'timesheet_time_finish',
			'timesheet_path',
			'timesheet_filename_format',
			'timesheet_file_find_by_date',
			'timesheet_email_destination',
			'timesheet_email_sender_address',
			'timesheet_email_sender_name',
			'timesheet_email_subject'
		) as $key) {
			$this->assertObjectHasAttribute($key,$i);
		}
	}

	function test_construct_loads_xml_template() {
		$i = new Timesheet;
		$this->assertEquals(file_get_contents(dirname(__FILE__)."/../../data/timesheet-template.xml"), $i->template);
	}

	function test_add_day() {
		$i = new Timesheet;

		$i->add_day('Monday','10 Aug 2011','09:00','17:00');
		$i->add_day('Tuesday','11 Aug 2011','09:00','17:00');
		$i->add_day('Wednesday','12 Aug 2011','09:00','17:00');

		$this->assertEquals(array(
			0 => array(
				'day' => 'Monday',
				'date' => '10 Aug 2011',
				'start' => '09:00',
				'finish' => '17:00'
			),
			1 => array(
				'day' => 'Tuesday',
				'date' => '11 Aug 2011',
				'start' => '09:00',
				'finish' => '17:00'
			),
			2 => array(
				'day' => 'Wednesday',
				'date' => '12 Aug 2011',
				'start' => '09:00',
				'finish' => '17:00'
			)
		),
		$i->days);
	}
}
?>
