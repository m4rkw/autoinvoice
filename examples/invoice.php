<?
require("../libraries/Invoice.php");

if (!file_exists("../config/config.php")) {
	die("config/config.php missing - please copy config.php.sample and edit for your needs.\n");
}

$i = new Invoice;

$i->invoice_number = "INV001";

// Invoice transactions - name, qty, unit cost

$i->add_transaction('August 10th - 12th','3','100');
$i->add_transaction('August 15th - 18th','4','100');
$i->add_transaction('August 23rd - 28th','8','100');

$i->generate("my-invoice.pages");

echo "Generated my-invoice.pages\n";
?>
