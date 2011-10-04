<?
class Timesheet {
	public $days = array();
	private $timesheet_template = "../data/timesheet-template.xml";

	function __construct($invoice_number=false) {
		$this->output_dir = getcwd();

		chdir(dirname(__FILE__));

		if (!file_exists($this->timesheet_template)) {
			die("Missing timesheet template: $this->timesheet_template\n");
		}

		if (!class_exists('ZipArchive')) {
			die("zip functions missing, please install the zip extension.\n");
		}

		$this->template = file_get_contents($this->timesheet_template);

		if (!file_exists(dirname(__FILE__)."/../config/config.php")) {
			die("Missing config file config/config.php\n");
		}

		include(dirname(__FILE__)."/../config/config.php");

		foreach ($config as $key => $value) {
			$this->$key = $value;
		}
	}

	function generate($target_filename) {
		if (!preg_match('/\//',$target_filename)) {
			$target_filename = $this->output_dir.'/'.$target_filename;
		}

		$n = count($this->days);

		if ($n <1) {
			die("No work days added to timesheet.\n");
		} else if ($n >60) {
			die("Timesheets are currently limited to a maximum of 30 work days.\n");
		}

		foreach ($this->days as $i => $workday) {
			$this->template = str_replace('{{{TIMESHEET_DATE'.($i+1).'}}}',$workday['date'],$this->template);
			$this->template = str_replace('{{{TIMESHEET_START'.($i+1).'}}}',$workday['start'],$this->template);
			$this->template = str_replace('{{{TIMESHEET_END'.($i+1).'}}}',$workday['finish'],$this->template);

			list($hour1,$min1) = explode(':',$workday['start']);
			list($hour2,$min2) = explode(':',$workday['finish']);

			$ts1 = mktime($hour1,$min1,0,1,1,date('Y'));
			$ts2 = mktime($hour2,$min2,0,1,1,date('Y'));

			$mins = 0;

			while (date('H',$ts1) != date('H',$ts2) || date('m',$ts1) < date('m',$ts2)) {
				$mins++;
				$ts1 += 60;
			}

			$hours = round($mins / 60,1);

			$this->template = str_replace('{{{TIMESHEET_HOURS'.($i+1).'}}}',$hours,$this->template);
		}

		for ($j=$i+1; $j<=60; $j++) {
			$this->template = str_replace('{{{TIMESHEET_DATE'.($j+1).'}}}',null,$this->template);
			$this->template = str_replace('{{{TIMESHEET_START'.($j+1).'}}}',null,$this->template);
			$this->template = str_replace('{{{TIMESHEET_END'.($j+1).'}}}',null,$this->template);
			$this->template = str_replace('{{{TIMESHEET_HOURS'.($j+1).'}}}',null,$this->template);
		}

		foreach ($this as $fieldname => $value) {
			if (!in_array($fieldname, array('template','days'))) {
				$this->template = str_replace('{{{'.strtoupper($fieldname).'}}}',$value,$this->template);
			}
		}

		$tmp = "/tmp/".sha1(rand());

		while (file_exists($tmp)) {
			$tmp = "/tmp/".sha1(rand());
		}

		@mkdir($tmp,0700);

		file_put_contents($tmp.'/buildVersionHistory.plist', '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<array>
	<string>numbers-trunk-20080702_1</string>
	<string>numbers-trunk-20080731_1</string>
	<string>numbers-trunk-20080801_2</string>
	<string>numbers-trunk-20080909_1</string>
	<string>numbers-trunk-20080911_1</string>
	<string>numbers-trunk-20080926_1</string>
	<string>numbers-trunk-20081016_1</string>
	<string>local build-Jul  1 2011</string>
</array>
</plist>');

		file_put_contents($tmp.'/index.xml',$this->template);

		@chdir($tmp);

		$zip = new ZipArchive;
		if ($zip->open($target_filename,ZIPARCHIVE::CREATE) === TRUE) {
			$zip->addFile('buildVersionHistory.plist');
			$zip->addFile('index.xml');
			$zip->close();

			@unlink('buildVersionHistory.plist');
			@unlink('index.xml');
			chdir("/tmp");
			@rmdir($tmp);

		} else {
			die("Unable to open $target_filename for writing.\n");
		}
	}

	function add_day($day, $date, $start, $finish) {
		if (!preg_match('/^[0-9]+:[0-9]+$/',$start)) {
			die("Invalid start time: $start (must be in format: 09:00)\n");
		}
		if (!preg_match('/^[0-9]+:[0-9]+$/',$finish)) {
			die("Invalid finish time: $finish (must be in format: 17:00)\n");
		}

		$this->days[] = array(
			'day' => $day,
			'date' => $date,
			'start' => $start,
			'finish' => $finish
		);
	}
}
?>
