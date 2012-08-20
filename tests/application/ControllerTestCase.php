<?php
include_once("Zend/Test/PHPUnit/ControllerTestCase.php");

class ControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase {

	protected $application;

	public function setUp() {

		$this->bootstrap = array($this, 'appBootstrap');
		parent::setUp();
	}

	public function appBootstrap() {
		$this->application = new Zend_Application(APPLICATION_ENV,
				APPLICATION_PATH . '/configs/application.ini');
		return $this->application->bootstrap();
	}

	/**
	 * Set bootstrap for fronController
	 * @see http://stackoverflow.com/questions/1226758
	 */
	public function dispatch($url = null) {
		$test = $this->application->getBootstrap();
		$this->getFrontController()->setParam('bootstrap', $test);
		parent::dispatch($url);
	}
}
