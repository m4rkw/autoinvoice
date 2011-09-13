<?
require("libraries/Invoice.php");
require("libraries/Calendar.php");

$config = dirname(__FILE__)."/config/config.php";
if (!file_exists($config)) {
	die("Missing config file config/config.php\n");
}
include($config);

$calendar = new Calendar;
if ($invoice_number = $calendar->are_we_invoicing_today()) {

	$last_invoice_date = $calendar->get_date_of_previous_invoice();

	$sick_days = $calendar->get_sick_days_since_previous_invoice($last_invoice_date);

	$billable_days = $calendar->get_billable_days_since($last_invoice_date, $sick_days);

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
		require("libraries/class.phpmailer.php");

		$mail = new PHPMailer(true);
		$mail->SetFrom($config['invoice_email_sender_address'], $config['invoice_email_sender_name']);
		$mail->AddAddress($config['invoice_email_destination']);
		$mail->Subject = str_replace('{{INVOICE_NUMBER}}',$invoice_number,$config['invoice_email_subject']);
		$mail->Body = "Hi there,

Your new invoice $invoice_number has been generated.

Much love,
{$config['invoice_email_sender_name']}";
		$mail->AddAttachment($config['invoice_path'].'/'.$invoice_filename);
		$mail->Send();
	}

} else {
	echo "No invoices today.\n";
}
?>
