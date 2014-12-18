<?php
/**
 * test for tomk79\diffdir
 * 
 * $ cd (project dir)
 * $ ./vendor/phpunit/phpunit/phpunit php/tests/diffdirTest
 */
require_once( __DIR__.'/../diffdir.php' );

class diffdirTest extends PHPUnit_Framework_TestCase{
	private $fs;

	public function setup(){
		mb_internal_encoding('UTF-8');
		$this->fs = new \tomk79\filesystem();
	}

	/**
	 * ディレクトリ比較のテスト
	 */
	public function testDiffDir1(){

		$this->fs->mkdir( __DIR__.'/_output1/' );
		$this->assertTrue( $this->fs->rm( __DIR__.'/_output1/' ) );

		$crlfHTML = '';
		$crlfHTML .= '<div>LF</div>'."\n";
		$crlfHTML .= '<div>CRLF</div>'."\r\n";
		$crlfHTML .= '<div>CR</div>'."\r";
		$crlfHTML .= '<div>(EOL)</div>';
		$this->fs->save_file( __DIR__.'/sample_a/crlf.html', $crlfHTML );
		$crlfHTML = preg_replace( '/(?:\r\n|\r|\n)/', "\r\n", $crlfHTML );
		$this->fs->save_file( __DIR__.'/sample_b/crlf.html', $crlfHTML );

		$diffdir = new \tomk79\diffdir(
			__DIR__.'/sample_a/',
			__DIR__.'/sample_b/',
			array( 'output'=>__DIR__.'/_output1/' )
		);
		$this->assertFalse( is_dir( $diffdir->is_error() ) );
		$this->assertTrue( is_dir( $diffdir->get_output_dir() ) );

		clearstatcache();
		$this->assertTrue( $this->fs->is_dir( __DIR__.'/_output1/' ) );
		$this->assertTrue( $this->fs->is_dir( __DIR__.'/_output1/pickup/' ) );
		$this->assertTrue( $this->fs->is_dir( __DIR__.'/_output1/report/' ) );
	}

	/**
	 * ディレクトリ比較のテスト(改行コードを無視するテスト)
	 */
	public function testDiffDir2(){

		$this->fs->mkdir( __DIR__.'/_output2/' );
		$this->assertTrue( $this->fs->rm( __DIR__.'/_output2/' ) );

		$crlfHTML = '';
		$crlfHTML .= '<div>LF</div>'."\n";
		$crlfHTML .= '<div>CRLF</div>'."\r\n";
		$crlfHTML .= '<div>CR</div>'."\r";
		$crlfHTML .= '<div>(EOL)</div>';
		$this->fs->save_file( __DIR__.'/sample_a/crlf.html', $crlfHTML );
		$crlfHTML = preg_replace( '/(?:\r\n|\r|\n)/', "\r\n", $crlfHTML );
		$this->fs->save_file( __DIR__.'/sample_b/crlf.html', $crlfHTML );

		$diffdir = new \tomk79\diffdir(
			__DIR__.'/sample_a/',
			__DIR__.'/sample_b/',
			array( 'output'=>__DIR__.'/_output2/', 'strip_crlf'=>true )
		);
		$this->assertFalse( is_dir( $diffdir->is_error() ) );
		$this->assertTrue( is_dir( $diffdir->get_output_dir() ) );

		clearstatcache();
		$this->assertTrue( $this->fs->is_dir( __DIR__.'/_output2/' ) );
		$this->assertTrue( $this->fs->is_dir( __DIR__.'/_output2/pickup/' ) );
		$this->assertTrue( $this->fs->is_dir( __DIR__.'/_output2/report/' ) );
	}



}
