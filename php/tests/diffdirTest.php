<?php
/**
 * test for tomk79\diffdir
 * 
 * $ cd (project dir)
 * $ ./vendor/phpunit/phpunit/phpunit php/tests/diffdirTest
 */
require_once( __DIR__.'/../diffdir.php' );

class diffdirTest extends PHPUnit_Framework_TestCase{

	public function setup(){
		mb_internal_encoding('UTF-8');
	}

	/**
	 * ディレクトリ比較のテスト
	 */
	public function testDiffDir(){

		$diffdir = new tomk79\diffdir( __DIR__.'/sample_a/', __DIR__.'/sample_b/' );
		$this->assertFalse( is_dir( $diffdir->is_error() ) );
		$this->assertTrue( is_dir( $diffdir->get_output_dir() ) );

	}



}
