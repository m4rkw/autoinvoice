<?
require("../libraries/Calendar.php");

$c = new Calendar;
if ($i = $c->are_we_invoicing_today()) {
	echo "\nInvoice entry for today: $i\n";

	$date = $c->get_date_of_previous_invoice();

	echo "\nLast invoice was on: $date\n";

	$sick_days = $c->get_sick_days_since_previous_invoice($date);

	echo "\nSick days since last invoice:\n\n";

	foreach ($sick_days as $day) {
		echo $day."\n";
	}

	$billable = $c->get_billable_days_since($date, $sick_days);

	echo "\nBillable days since last invoice:\n\n";

	foreach ($billable as $day) {
		echo $day."\n";
	}

	echo "\nBillable days expressed as invoice transactions:\n\n";

	$transactions = $c->get_days_as_invoice_transactions($billable);

	foreach ($transactions as $t) {
		echo $t['title']." (".$t['days']." days)\n";
	}

	echo "\n";
}
?>
