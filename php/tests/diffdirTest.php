<?php
/**
 * test for tomk79\diffdir
 * 
 * $ cd (project dir)
 * $ ./vendor/phpunit/phpunit/phpunit php/tests/diffdirTest
 */
require_once( __DIR__.'/../diffdir.php' );

class diffdirTest extends PHPUnit_Framework_TestCase{

	private $diffdir;

	public function setup(){
		mb_internal_encoding('UTF-8');
		$this->diffdir = new tomk79\diffdir();
	}



}
