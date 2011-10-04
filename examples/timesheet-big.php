<?
require("../libraries/TimeSheet.php");

if (!file_exists("../config/config.php")) {
	die("config/config.php missing - please copy config.php.sample and edit for your needs.\n");
}

$t = new Timesheet;

$t->timesheet_name = 'Bob Dylan';
$t->timesheet_jobtitle = 'Window Cleaner';
$t->timesheet_client = 'Google Inc';
$t->timesheet_client_location = 'Space';

$t->add_day('Monday','10 Aug 2011','09:00','17:00');
$t->add_day('Tuesday','11 Aug 2011','09:00','17:00');
$t->add_day('Wednesday','12 Aug 2011','09:00','17:00');
$t->add_day('Thursday','13 Aug 2011','09:00','17:00');
$t->add_day('Friday','14 Aug 2011','09:00','17:00');
$t->add_day('Monday','17 Aug 2011','09:00','17:00');
$t->add_day('Tuesday','18 Aug 2011','09:00','17:00');
$t->add_day('Wednesday','19 Aug 2011','09:00','17:00');
$t->add_day('Thursday','20 Aug 2011','09:00','17:00');
$t->add_day('Friday','21 Aug 2011','09:00','17:00');
$t->add_day('Monday','24 Aug 2011','09:00','17:00');
$t->add_day('Tuesday','25 Aug 2011','09:00','17:00');
$t->add_day('Monday','10 Aug 2011','09:00','17:00');
$t->add_day('Tuesday','11 Aug 2011','09:00','17:00');
$t->add_day('Wednesday','12 Aug 2011','09:00','17:00');
$t->add_day('Thursday','13 Aug 2011','09:00','17:00');
$t->add_day('Friday','14 Aug 2011','09:00','17:00');
$t->add_day('Monday','17 Aug 2011','09:00','17:00');
$t->add_day('Tuesday','18 Aug 2011','09:00','17:00');
$t->add_day('Wednesday','19 Aug 2011','09:00','17:00');
$t->add_day('Thursday','20 Aug 2011','09:00','17:00');
$t->add_day('Friday','21 Aug 2011','09:00','17:00');
$t->add_day('Monday','24 Aug 2011','09:00','17:00');
$t->add_day('Tuesday','25 Aug 2011','09:00','17:00');
$t->add_day('Monday','10 Aug 2011','09:00','17:00');
$t->add_day('Tuesday','11 Aug 2011','09:00','17:00');
$t->add_day('Wednesday','12 Aug 2011','09:00','17:00');
$t->add_day('Thursday','13 Aug 2011','09:00','17:00');
$t->add_day('Friday','14 Aug 2011','09:00','17:00');
$t->add_day('Monday','17 Aug 2011','09:00','17:00');
$t->add_day('Tuesday','18 Aug 2011','09:00','17:00');
$t->add_day('Wednesday','19 Aug 2011','09:00','17:00');
$t->add_day('Thursday','20 Aug 2011','09:00','17:00');
$t->add_day('Friday','21 Aug 2011','09:00','17:00');
$t->add_day('Monday','24 Aug 2011','09:00','17:00');
$t->add_day('Tuesday','25 Aug 2011','09:00','17:00');
$t->add_day('Monday','10 Aug 2011','09:00','17:00');
$t->add_day('Tuesday','11 Aug 2011','09:00','17:00');
$t->add_day('Wednesday','12 Aug 2011','09:00','17:00');
$t->add_day('Thursday','13 Aug 2011','09:00','17:00');
$t->add_day('Friday','14 Aug 2011','09:00','17:00');
$t->add_day('Monday','17 Aug 2011','09:00','17:00');
$t->add_day('Tuesday','18 Aug 2011','09:00','17:00');
$t->add_day('Wednesday','19 Aug 2011','09:00','17:00');
$t->add_day('Thursday','20 Aug 2011','09:00','17:00');
$t->add_day('Friday','21 Aug 2011','09:00','17:00');
$t->add_day('Monday','24 Aug 2011','09:00','17:00');
$t->add_day('Tuesday','25 Aug 2011','09:00','17:00');

$t->generate("my-timesheet.numbers");

echo "Generated my-timesheet.numbers\n";
?>
