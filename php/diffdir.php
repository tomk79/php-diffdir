<?php
/**
 * tomk79/diffdir
 * 
 * @author Tomoya Koyanagi <tomk79@gmail.com>
 */

namespace tomk79;

/**
 * tomk79/diffdir core class
 * 
 * @author Tomoya Koyanagi <tomk79@gmail.com>
 */
class diffdir{

	private $fs;
	private $before, $after, $conf = array();
	private $errors = array();

	/**
	 * constructor
	 * 
	 * @param string $before 比較対象(before) 必須
	 * @param string $after 比較対象(after) 必須
	 * @param array $conf config
	 * $conf['output'] = 結果出力先ディレクトリ(省略時、カレントディレクトリに日時情報を含んだディレクトリを作成)
	 */
	public function __construct( $before, $after, $conf = array() ){
		$this->fs = new filesystem();
		$this->before = $before;
		$this->after = $after;
		$this->conf = $conf;

		if( !strlen( @$this->conf['output'] ) ){
			$this->conf['output'] = '_report_'.@date('Ymd_His');
		}
		$this->conf['output'] = $this->fs->get_realpath( $this->conf['output'] ).DIRECTORY_SEPARATOR;

		if( !$this->validate() ){
			return false;
		}

		$this->execute();
		return true;
	}

	/**
	 * validate args
	 */
	private function validate(){
		if( !strlen( $this->before ) || !is_dir($this->before) ){
			$this->error('before NOT exists.', __FILE__, __LINE__);
			return false;
		}
		if( !strlen( $this->after ) || !is_dir($this->after) ){
			$this->error('after NOT exists.', __FILE__, __LINE__);
			return false;
		}
		if( $this->before === $this->after ){
			$this->error('Paths "before" equals "after" given.', __FILE__, __LINE__);
			return false;
		}
		if( is_dir( $this->conf['output'] ) ){
			$this->error('output directory exists.', __FILE__, __LINE__);
			return false;
		}elseif( !$this->fs->is_writable( $this->conf['output'] ) ){
			$this->error('output directory is NOT writable.', __FILE__, __LINE__);
			return false;
		}
		return true;
	}

	/**
	 * executing diffdir command
	 */
	private function execute(){
		$res = $this->fs->mkdir( $this->conf['output'] );
	}

	/**
	 * reporting error message
	 */
	private function error( $msg, $FILE, $LINE ){
		array_push( $this->errors, array('msg'=>$msg, 'FILE'=>$FILE, 'LINE'=>$LINE) );
		return true;
	}

	/**
	 * is error
	 * @return bool is error
	 */
	public function is_error(){
		return !empty($this->errors);
	}

	/**
	 * getting error messages
	 * @return array Errors.
	 */
	public function get_errors(){
		return $this->errors;
	}

	/**
	 * getting output directory
	 * @return bool output directory
	 */
	public function get_output_dir(){
		return $this->conf['output'];
	}

}