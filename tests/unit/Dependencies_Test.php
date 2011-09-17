<?
class DependenciesTest extends PHPUnit_Framework_TestCase {
	function test_construct_loads_zend_loader() {
		$this->assertTrue(class_exists('Zend_Loader'));
	} 

	function test_dependency_zend_loader() {
		include_once('Zend/Loader.php');
	}

	function test_dependency_zend_gdata() {
		Zend_Loader::loadClass('Zend_Gdata');
	}

	function test_dependency_zend_gdata_authsub() {
		Zend_Loader::loadClass('Zend_Gdata_AuthSub');
	}

	function test_dependency_zend_gdata_clientlogin() {
		Zend_Loader::loadClass('Zend_Gdata_ClientLogin');
	}

	function test_dependency_zend_gdata_httpclient() {
		Zend_Loader::loadClass('Zend_Gdata_HttpClient');
	}

	function test_dependency_zend_gdata_calendar() {
		Zend_Loader::loadClass('Zend_Gdata_Calendar');
	}

	function test_dependency_zip_archive() {
		$this->assertTrue(class_exists('ZipArchive'));
	}
}
?>
