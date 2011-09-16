<?
require("libraries/Invoice.php");
require("libraries/Calendar.php");
require("libraries/Timesheet.php");

$config = dirname(__FILE__)."/config/config.php";
if (!file_exists($config)) {
	die("Missing config file config/config.php\n");
}
include($config);

$calendar = new Calendar;
if ($invoice_number = $calendar->are_we_invoicing_today()) {

	$last_invoice_date = $calendar->get_date_of_previous_invoice();

	$sick_days = $calendar->get_sick_days_since_previous_invoice($last_invoice_date);

	$billable_days = $calendar->get_billable_days_since($last_invoice_date, time(), $sick_days);

	$transactions = $calendar->get_days_as_invoice_transactions($billable_days);

	$invoice = new Invoice($invoice_number);

	foreach ($transactions as $transaction) {
		$invoice->add_transaction($transaction['title'], $transaction['days'], $config['day_rate']);
	}

	$invoice_filename = str_replace('{{DATE}}',date('Ymd'),$config['invoice_filename_format']);
	$invoice_filename = str_replace('{{INVOICE_NUMBER}}',$invoice_number,$invoice_filename);

	$invoice->generate($config['invoice_path'].'/'.$invoice_filename);

	echo "Saved new invoice: $invoice_filename\n";

	if ($config['invoice_email_destination']) {
		require_once("libraries/class.phpmailer.php");

		$mail = new PHPMailer(true);
		$mail->SetFrom($config['invoice_email_sender_address'], $config['invoice_email_sender_name']);
		$mail->AddAddress($config['invoice_email_destination']);
		$mail->Subject = str_replace('{{INVOICE_NUMBER}}',$invoice_number,$config['invoice_email_subject']);
		$mail->Subject = str_replace('{{DATE}}',date('d.m.Y'),$mail->Subject);
		$mail->Body = "Hi there,

Your new invoice $invoice_number has been generated.

Much love,
{$config['invoice_email_sender_name']}";
		$mail->AddAttachment($config['invoice_path'].'/'.$invoice_filename);
		$mail->Send();
	}
}

if ($calendar->are_we_timesheeting_today()) {
	$last_invoice_date = $calendar->get_date_of_previous_invoice();

	$sick_days = $calendar->get_sick_days_since_previous_invoice($last_invoice_date);

	$billable_days = $calendar->get_billable_days_since($last_invoice_date, time() + ($config['timesheet_lead_time_days'] * 86400), $sick_days);

	$entries = $calendar->get_days_as_timesheet_entries($billable_days);

	$timesheet = new Timesheet;

	$timesheet->timesheet_name = $config['timesheet_name'];
	$timesheet->timesheet_jobtitle = $config['timesheet_jobtitle'];
	$timesheet->timesheet_client = $config['timesheet_client'];
	$timesheet->timesheet_client_location = $config['timesheet_client_location'];

	foreach ($entries as $entry) {
		$timesheet->add_day($entry['day'],$entry['date'],$config['timesheet_time_start'],$config['timesheet_time_finish']);
	}

	$timesheet_filename = str_replace('{{DATE}}',date('Ymd',time()+($config['timesheet_lead_time_days'] * 86400)),$config['timesheet_filename_format']);

	$timesheet->generate($config['timesheet_path'].'/'.$timesheet_filename);

	echo "Saved new timesheet: $timesheet_filename\n";

	if ($config['timesheet_email_destination']) {
		require_once("libraries/class.phpmailer.php");

		$mail = new PHPMailer(true);
		$mail->SetFrom($config['timesheet_email_sender_address'], $config['timesheet_email_sender_name']);
		$mail->AddAddress($config['timesheet_email_destination']);
		$mail->Subject = str_replace('{{DATE}}',date('d.m.Y',time()+($config['timesheet_lead_time_days'] * 86400)),$config['timesheet_email_subject']);
		$mail->Body = "Hi there,

Your new timesheet $timesheet_number has been generated.

Much love,
{$config['timesheet_email_sender_name']}";
		$mail->AddAttachment($config['timesheet_path'].'/'.$timesheet_filename);
		$mail->Send();
	}
}
?>
