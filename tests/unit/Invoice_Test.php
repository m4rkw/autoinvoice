<?
class InvoiceTest extends PHPUnit_Framework_TestCase {
	function __construct() {
		require_once(dirname(__FILE__)."/../../libraries/Invoice.php");
	}

	function test_construct_sets_output_dir() {
		$cwd = getcwd();
		$i = new Invoice;
		$this->assertEquals($cwd, $i->output_dir);
	}

	function test_construct_sets_invoice_number() {
		$i = new Invoice('my arbitrary invoice number');
		$this->assertEquals('my arbitrary invoice number', $i->invoice_number);
	}

	function test_construct_sets_current_date() {
		$i = new Invoice;
		$this->assertEquals(date('d/m/Y'),$i->date);
	}

	function test_construct_sets_config_params() {
		$i = new Invoice;

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
			'timesheet_email_subject',
			'invoice_number',
			'date'
		) as $key) {
			$this->assertObjectHasAttribute($key,$i);
		}
	}

	function test_construct_loads_xml_template() {
		$i = new Invoice;
		$this->assertEquals(file_get_contents(dirname(__FILE__)."/../../data/invoice-template.xml"), $i->template);
	}

	function test_add_transaction() {
		$i = new Invoice;

		$i->add_transaction('test transaction',10,100);
		$i->add_transaction('test transaction 2',13,123);
		$i->add_transaction('test transaction 3',100,10);

		$this->assertEquals(array(
			0 => array(
				'name' => 'test transaction',
				'quantity' => 10,
				'unit_price' => 100,
				'cost' => 1000
			),
			1 => array(
				'name' => 'test transaction 2',
				'quantity' => 13,
				'unit_price' => 123,
				'cost' => 1599
			),
			2 => array(
				'name' => 'test transaction 3',
				'quantity' => 100,
				'unit_price' => 10,
				'cost' => 1000
			)
		),
		$i->transactions);
	}
}
?>
