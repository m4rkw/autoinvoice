<?
class BankHolidaysTest extends PHPUnit_Framework_TestCase {
	function __construct() {
		require_once(dirname(__FILE__)."/../../libraries/BankHolidays.php");
	}

	function test_current_year_is_tested() {
		$this->assertLessThan(2012, date('Y'));
	}

	function test_bank_holidays_2011() {
		$bank_holidays = BankHolidays::get(2011);

		$actual_holidays = array(
			'2011-01-03', // New Year (or lieu)
			'2011-04-22', // Good Friday
			'2011-04-25', // Easter Monday
			'2011-05-02', // May Day
			'2011-05-30', // Spring
			'2011-08-29', // August
			'2011-12-26', // Christmas (or lieu)
			'2011-12-27'	// Boxing Day (or lieu)
		);

		$timestamp = mktime(0,0,0,1,1,2011);

		while (1) {
			if (date('Y',$timestamp) > 2011) break;

			$day = date('Y-m-d',$timestamp);

			if (in_array($day, $actual_holidays)) {
				$this->assertContains($day, $bank_holidays);
			} else {
				$this->assertNotContains($day, $bank_holidays);
			}

			$timestamp += 86400;
		}
	}

	function test_bank_holidays_2012() {
		$bank_holidays = BankHolidays::get(2012);

		$actual_holidays = array(
			'2012-01-02', // New Year (or lieu)
			'2012-04-06', // Good Friday
			'2012-04-09', // Easter Monday
			'2012-05-07', // May Day
			'2012-06-04', // Spring
			'2012-06-05', // Queen's Diamond Jubilee
			'2012-08-27', // August
			'2012-12-25', // Christmas (or lieu)
			'2012-12-26'	// Boxing Day (or lieu)
		);

		$timestamp = mktime(0,0,0,1,1,2012);

		while (1) {
			if (date('Y',$timestamp) > 2012) break;

			$day = date('Y-m-d',$timestamp);

			if (in_array($day, $actual_holidays)) {
				$this->assertContains($day, $bank_holidays);
			} else {
				$this->assertNotContains($day, $bank_holidays);
			}

			$timestamp += 86400;
		}
	}
}
?>
