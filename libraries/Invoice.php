<?
class Invoice {
	private $transactions = array();
	private $invoice_template = "../data/invoice-template.xml";

	function __construct($invoice_number=false) {
		$this->output_dir = getcwd();

		chdir(dirname(__FILE__));

		if (!file_exists($this->invoice_template)) {
			die("Missing invoice template: $this->invoice_template\n");
		}

		if (!class_exists('ZipArchive')) {
			die("zip functions missing, please install the zip extension.\n");
		}

		$this->template = file_get_contents($this->invoice_template);

		if (!file_exists(dirname(__FILE__)."/../config/config.php")) {
			die("Missing config file config/config.php\n");
		}

		include(dirname(__FILE__)."/../config/config.php");

		foreach ($config as $key => $value) {
			$this->$key = $value;
		}

		$this->invoice_number = $invoice_number;
		$this->date = date('d/m/Y');
	}

	function generate($target_filename) {
		if (!preg_match('/\//',$target_filename)) {
			$target_filename = $this->output_dir.'/'.$target_filename;
		}

		$n = count($this->transactions);

		if ($n <1) {
			die("No transactions added to invoice.\n");
		}

		$this->template = str_replace('{{{TABLESTYLE1}}}','<sf:tabular-style-ref sfa:IDREF="SFTTableStyle-1"/><sf:grid sf:ocnt="'.(16 + (4*$n)).'" sf:numcols="4" sf:numrows="'.(4+$n).'" sf:hiddennumcols="0" sf:hiddennumrows="0" sf:ncc="true">',$this->template);

		$this->template = str_replace('{{{TABLESTYLE2}}}','<sf:columns sf:count="4"><sf:grid-column sf:width="198.07794189453125" sf:preferred-width="198.07794189453125" sf:fitting-width="78.677978515625" sf:nc="'.(4+$n).'"/><sf:grid-column sf:width="54.021232604980469" sf:preferred-width="54.021232604980469" sf:fitting-width="42.171066284179688" sf:nc="'.(4+$n).'"/><sf:grid-column sf:width="54.021232604980469" sf:preferred-width="54.021232604980469" sf:fitting-width="48.1561279296875" sf:nc="'.(4+$n).'"/><sf:grid-column sf:width="54.021232604980469" sf:preferred-width="54.021232604980469" sf:fitting-width="38.025985717773438" sf:nc="'.(4+$n).'"/></sf:columns>',$this->template);

		$this->template = str_replace('{{{TABLESTYLE3}}}','<sf:vertical-gridline-styles sf:array-size="4"><sf:style-run sf:gridline-index="0" sf:count="3"><sf:vector-style-ref sfa:IDREF="SFTVectorStyle-22" sf:start-index="0" sf:stop-index="1"/><sf:vector-style-ref sfa:IDREF="SFTVectorStyle-21" sf:start-index="1" sf:stop-index="'.(3+$n).'"/><sf:vector-style-ref sfa:IDREF="SFTVectorStyle-19" sf:start-index="'.(3+$n).'" sf:stop-index="'.(4+$n).'"/></sf:style-run><sf:style-run sf:gridline-index="1" sf:count="2"><sf:vector-style-ref sfa:IDREF="SFTVectorStyle-20" sf:start-index="'.(1+$n).'" sf:stop-index="'.(3+$n).'"/><sf:vector-style-ref sfa:IDREF="SFTVectorStyle-13" sf:start-index="'.(3+$n).'" sf:stop-index="'.(4+$n).'"/></sf:style-run><sf:style-run sf:gridline-index="2" sf:count="2"><sf:vector-style-ref sfa:IDREF="SFTVectorStyle-20" sf:start-index="'.(1+$n).'" sf:stop-index="'.(3+$n).'"/><sf:vector-style-ref sfa:IDREF="SFTVectorStyle-13" sf:start-index="'.(3+$n).'" sf:stop-index="'.(4+$n).'"/></sf:style-run><sf:style-run sf:gridline-index="4" sf:count="3"><sf:vector-style-ref sfa:IDREF="SFTVectorStyle-22" sf:start-index="0" sf:stop-index="1"/><sf:vector-style-ref sfa:IDREF="SFTVectorStyle-21" sf:start-index="1" sf:stop-index="'.(3+$n).'"/><sf:vector-style-ref sfa:IDREF="SFTVectorStyle-19" sf:start-index="'.(3+$n).'" sf:stop-index="'.(4+$n).'"/></sf:style-run></sf:vertical-gridline-styles>',$this->template);

		$transaction_rows = '';
		for ($i=0; $i<$n; $i++) {
			$transaction_rows .= '<sf:grid-row sf:height="16" sf:fitting-height="16" sf:nc="4" sf:ncoc="2"/>';
		}

		$this->template = str_replace('{{{TABLESTYLE4}}}','<sf:rows sf:count="'.(4+$n).'"><sf:grid-row sf:height="17.081077575683594" sf:preferred-height="17.081077575683594" sf:fitting-height="15.5" sf:nc="4" sf:ncoc="4" sf:manually-sized="true"/><sf:grid-row sf:height="17.081077575683594" sf:preferred-height="17.081077575683594" sf:fitting-height="16" sf:nc="4" sf:ncoc="2" sf:manually-sized="true"/>'.$transaction_rows.'<sf:grid-row sf:height="17.081077575683594" sf:preferred-height="17.081077575683594" sf:fitting-height="16" sf:nc="4" sf:ncoc="4" sf:manually-sized="true"/><sf:grid-row sf:height="17.081077575683594" sf:preferred-height="17.081077575683594" sf:fitting-height="16" sf:nc="4" sf:ncoc="1" sf:manually-sized="true"/><sf:grid-row sf:height="17.081077575683594" sf:preferred-height="17.081077575683594" sf:fitting-height="15.5" sf:nc="4" sf:ncoc="1" sf:manually-sized="true"/></sf:rows>',$this->template);

		$this->template = str_replace('{{{TABLESTYLE5}}}','<sf:horizontal-gridline-styles sf:array-size="2"><sf:style-run sf:gridline-index="0" sf:count="1"><sf:vector-style-ref sfa:IDREF="SFTVectorStyle-22" sf:start-index="0" sf:stop-index="4"/></sf:style-run><sf:style-run sf:gridline-index="'.(4+$n).'" sf:count="1"><sf:vector-style-ref sfa:IDREF="SFTVectorStyle-19" sf:start-index="0" sf:stop-index="4"/></sf:style-run></sf:horizontal-gridline-styles>',$this->template);

		$subtotal = 0;

		$transactions = '';
		foreach ($this->transactions as $t) {
			$transactions .= '<sf:t sf:s="SFTCellStyle-16" sf:w="77.177979" sf:h="15" sf:ct="253">	<sf:ct sfa:s="'.$t['name'].'"/></sf:t>	<sf:n sf:f="4" sf:s="SFTCellStyle-20" sf:w="9.0039978" sf:h="15" sf:v="'.$t['quantity'].'"/>	<sf:n sf:f="4" sf:s="SFTCellStyle-9" sf:w="36.525986" sf:h="15" sf:v="'.$t['unit_price'].'"/>  <sf:f sf:s="SFTCellStyle-15"><sf:fo sf:fs="=B*C"/>  <sf:r><sf:rn sf:f="4" sf:w="36.525986" sf:h="15" sf:v="'.$t['cost'].'"/></sf:r></sf:f>';
			$subtotal += $t['cost'];
		}

		$this->template = str_replace('{{{TRANSACTIONS}}}',$transactions,$this->template);

		foreach ($this as $fieldname => $value) {
			if ($fieldname != 'template') {
				$this->template = str_replace('{{{'.strtoupper($fieldname).'}}}',$value,$this->template);
			}
		}

		$this->template = str_replace('{{{SUBTOTAL}}}',$subtotal,$this->template);
		$this->template = str_replace('{{{NUM_PLUS_ONE}}}',$n+1,$this->template);
		$this->template = str_replace('{{{VAT_AMOUNT}}}',(($subtotal/100) * $this->vat_rate),$this->template);
		$this->template = str_replace('{{{TOTAL}}}',$subtotal + ((($subtotal/100) * $this->vat_rate)),$this->template);

		$tmp = "/tmp/".sha1(rand());

		while (file_exists($tmp)) {
			$tmp = "/tmp/".sha1(rand());
		}

		@mkdir($tmp,0700);

		file_put_contents($tmp.'/buildVersionHistory.plist', '<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" "http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<array>
	<string>pages-trunk-20080703_5</string>
	<string>pages-trunk-20080705_1</string>
	<string>pages-trunk-20080829_1</string>
	<string>pages-trunk-20081028_1</string>
	<string>local build-Nov 17 2010</string>
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

	function add_transaction($name,$quantity,$unit_price) {
		$this->transactions[] = array(
			'name' => $name,
			'quantity' => $quantity,
			'unit_price' => $unit_price,
			'cost' => $quantity * $unit_price
		);
	}
}
?>
