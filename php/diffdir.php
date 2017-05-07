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
	// $ php ./diffdir.php -o "./_output/" ./php/tests/sample_a/ ./php/tests/sample_b/
	// $ rm -r _output/

	private $fs;
	private $before, $after, $conf = array();
	private $errors = array();
	// private $reports = array();

	/**
	 * constructor
	 *
	 * @param string $before 比較対象(before) 必須
	 * @param string $after 比較対象(after) 必須
	 * @param array $conf config
	 * $conf['output'] = 結果出力先ディレクトリ(省略時、カレントディレクトリに日時情報を含んだディレクトリを作成)
	 */
	public function __construct( $before, $after, $conf = array() ){
		mb_detect_order("ASCII,UTF-8,sjis,sjis-win,sjis-mac,euc-jp,eucjp-win,jis");
		$this->fs = new filesystem();
		$this->before = $this->fs->get_realpath( $before ).DIRECTORY_SEPARATOR;
		$this->after = $this->fs->get_realpath( $after ).DIRECTORY_SEPARATOR;
		$this->conf = $conf;

		if( !strlen( @$this->conf['output'] ) ){
			$this->conf['output'] = '_report_'.@date('Ymd_His');
		}
		$this->conf['output'] = $this->fs->get_realpath( $this->conf['output'] ).DIRECTORY_SEPARATOR;

		if( !$this->validate() ){
			return false;
		}

		if( !$this->execute() ){
			return false;
		}
		return true;
	}

	/**
	 * validate args
	 */
	private function validate(){
		if( !strlen( $this->before ) || !is_dir($this->before) ){
			$this->error('before NOT exists. ('.$this->before.')', __FILE__, __LINE__);
			return false;
		}
		if( !strlen( $this->after ) || !is_dir($this->after) ){
			$this->error('after NOT exists. ('.$this->after.')', __FILE__, __LINE__);
			return false;
		}
		if( $this->before === $this->after ){
			$this->error('Paths "before" equals "after" given. ('.$this->after.')', __FILE__, __LINE__);
			return false;
		}
		if( is_dir( $this->conf['output'] ) ){
			$this->error('output directory already exists. ('.$this->fs->get_realpath($this->conf['output']).')', __FILE__, __LINE__);
			return false;
		}elseif( !$this->fs->is_writable( $this->conf['output'] ) ){
			$this->error('output directory is NOT writable. ('.$this->fs->get_realpath($this->conf['output']).')', __FILE__, __LINE__);
			return false;
		}
		return true;
	}

	/**
	 * executing diffdir command
	 */
	private function execute(){
		$htmlreport = new \tomk79\diffdir\htmlreport( $this->fs, $this->before, $this->after, $this->conf );
		$this->diffdir();

		// reporting...
		if( !$this->mkdir( $this->conf['output']           , __FILE__, __LINE__ ) ){ return false; }
		if( !$this->mkdir( $this->conf['output'].'/pickup/', __FILE__, __LINE__ ) ){ return false; }
		if( !$this->mkdir( $this->conf['output'].'/report/', __FILE__, __LINE__ ) ){ return false; }

		// 差分を知らせるHTMLのindex.html(一覧)を生成
		$htmlreport->save_diff_report_index_html_header();


		$fp = fopen( $this->conf['output'].'report.csv', 'r' );
		while( $csv_row = fgetcsv( $fp , 100000 , ',' , '"' ) ){

			$i = 0;
			$repo = array(
				'path'=>$csv_row[$i++] ,
				'status'=>$csv_row[$i++] ,
				'before_info'=>json_decode($csv_row[$i++],true) ,
				'after_info'=>json_decode($csv_row[$i++],true) ,
			);

			$csv = array(
				$repo['path'] ,
				$repo['status'] ,

				$repo['before_info']['type'] ,
				$repo['before_info']['size'] ,
				@date('Y-m-d H:i:s', $repo['before_info']['timestamp']) ,
				$repo['before_info']['md5'] ,
				$repo['before_info']['encoding'] ,
				$repo['before_info']['crlf'] ,

				$repo['after_info']['type'] ,
				$repo['after_info']['size'] ,
				@date('Y-m-d H:i:s', $repo['after_info']['timestamp']) ,
				$repo['after_info']['md5'] ,
				$repo['after_info']['encoding'] ,
				$repo['after_info']['crlf'] ,
			);

			$this->verbose( $repo['path'] );
			switch( $repo['status'] ){
				case 'changed':
				case 'added':
					// 差分があったファイルを抽出する
					$this->fs->mkdir_r( dirname( $this->conf['output'].'/pickup/'.$repo['path'] ) );
					$this->fs->copy_r(
						$this->after.$repo['path'] ,
						$this->conf['output'].'/pickup/'.$repo['path']
					);
					break;
			}

			// 差分を知らせるHTMLを生成
			$htmlreport->save_diff_report_html( $repo );

			// 差分を知らせるHTMLのindex.html(一覧)を生成
			$html_index_html_list = '';
			$html_index_html_list .= '<li class="'.htmlspecialchars($repo['status']).' '.htmlspecialchars($repo['before_info']['type']).' '.htmlspecialchars($repo['after_info']['type']).'">';
			$html_index_html_list .= '<a href="'.htmlspecialchars('diff/'.$repo['path'].'.diff.html').'" target="diffpreview">';
			$item_type = ($repo['after_info']['type'] ? $repo['after_info']['type'] : $repo['before_info']['type']);
			switch($item_type){
				case 'file':
					$html_index_html_list .= '<span class="glyphicon glyphicon-file" style="margin-right: 0.5em;"></span>';
					break;
				case 'dir':
					$html_index_html_list .= '<span class="glyphicon glyphicon-folder-open" style="margin-right: 0.5em;"></span>';
					break;
			}
			$html_index_html_list .= htmlspecialchars($repo['path']);
			switch( @strtolower($repo['status']) ){
				case 'added':
					$html_index_html_list .= '<span class="label label-primary">A</span>';
					break;
				case 'changed':
					$html_index_html_list .= '<span class="label label-warning">C</span>';
					break;
				case 'deleted':
					$html_index_html_list .= '<span class="label label-danger">D</span>';
					break;
				default:
					break;
			}
			$html_index_html_list .= '</a>';
			$html_index_html_list .= '</li>';
			$htmlreport->save_diff_report_index_html_list( $html_index_html_list );

			$src_csv = $this->fs->mk_csv( array( $csv ) );
			@error_log( $src_csv, 3, $this->conf['output'].'/report/diffdir.csv' );

		}
		fclose($fp);


		// 差分を知らせるHTMLのindex.html(一覧)を生成
		$htmlreport->save_diff_report_index_html_footer();

		return true;
	}

	/**
	 * diffdir
	 */
	private function diffdir( $localpath=null ){
		// 一覧を作成
		$files = array();
		if( $this->fs->is_dir( $this->before.$localpath ) ){
			foreach( $this->fs->ls( $this->before.$localpath ) as $tmp_filename ){
				switch( basename($tmp_filename) ){
					case '.DS_Store':
					case 'Thumbs.db':
						continue 2;
				}
				if( array_search( $tmp_filename, $files) === false ){
					array_push( $files, $tmp_filename );
				}
			}
		}
		if( $this->fs->is_dir( $this->after.$localpath ) ){
			foreach( $this->fs->ls( $this->after.$localpath ) as $tmp_filename ){
				switch( basename($tmp_filename) ){
					case '.DS_Store':
					case 'Thumbs.db':
						continue 2;
				}
				if( array_search( $tmp_filename, $files) === false ){
					array_push( $files, $tmp_filename );
				}
			}
		}
		unset($tmp_filename);

		// 検証
		foreach( $files as $tmp_filename ){
			// var_dump($localpath.$tmp_filename);

			$status = null;
			$before_info = array(
				'type'=>null ,
				'size'=>null ,
				'timestamp'=>null ,
				'md5'=>null ,
				'encoding'=>null ,
				'crlf'=>null ,
			);
			$after_info = $before_info;

			$realpath_before = $this->fs->get_realpath( $this->before.$localpath.$tmp_filename );
			$realpath_after  = $this->fs->get_realpath( $this->after .$localpath.$tmp_filename );

			if( $this->fs->is_file($this->before.$localpath.$tmp_filename) && $this->fs->is_file($this->after.$localpath.$tmp_filename) ){
				// 両方ファイルだったら、md5比較
				$before_info['md5'] = md5_file( $realpath_before );
				$after_info['md5']  = md5_file( $realpath_after  );
				$before_info['encoding'] = mb_detect_encoding( $this->fs->read_file( $realpath_before ) );
				$after_info['encoding']  = mb_detect_encoding( $this->fs->read_file( $realpath_after  ) );
				$before_info['crlf'] = $this->detect_cr_lf( $this->fs->read_file( $realpath_before ) );
				$after_info['crlf']  = $this->detect_cr_lf( $this->fs->read_file( $realpath_after  ) );
				$before_info['size'] = filesize( $realpath_before );
				$after_info['size']  = filesize( $realpath_after  );
				$before_info['timestamp'] = filemtime( $realpath_before );
				$after_info['timestamp']  = filemtime( $realpath_after  );
				$before_info['type'] = 'file';
				$after_info['type']  = 'file';
				if( @$this->conf['strip_crlf'] ){
					if(
						md5( preg_replace( '/\r\n|\r|\n/', '', $this->fs->read_file( $realpath_before ) ) ) !==
						md5( preg_replace( '/\r\n|\r|\n/', '', $this->fs->read_file( $realpath_after  ) ) )
						){
						$status = 'changed';
					}
				}else{
					if( $before_info['md5'] !== $after_info['md5'] ){
						$status = 'changed';
					}
				}
			}elseif( $this->fs->is_dir($realpath_before) && $this->fs->is_dir($this->after.$localpath.$tmp_filename) ){
				// 両方ディレクトリだったら
				$before_info['timestamp'] = filemtime( $realpath_before );
				$after_info['timestamp']  = filemtime( $realpath_after  );
				$before_info['type'] = 'dir';
				$after_info['type']  = 'dir';
			}elseif( $this->fs->is_file($realpath_before) && !$this->fs->file_exists($this->after.$localpath.$tmp_filename) ){
				// before がファイルで、after が存在しなかったら
				$status = 'deleted';
				$before_info['md5'] = md5_file( $realpath_before );
				$before_info['encoding'] = mb_detect_encoding( $this->fs->read_file( $realpath_before ) );
				$before_info['crlf'] = $this->detect_cr_lf( $this->fs->read_file( $realpath_before ) );
				$before_info['size'] = filesize( $realpath_before );
				$before_info['timestamp'] = filemtime( $realpath_before );
				$before_info['type'] = 'file';
			}elseif( $this->fs->is_dir($realpath_before) && !$this->fs->file_exists($this->after.$localpath.$tmp_filename) ){
				// before がディレクトリで、after が存在しなかったら
				$status = 'deleted';
				$before_info['timestamp'] = filemtime( $realpath_before );
				$before_info['type'] = 'dir';
			}elseif( !$this->fs->file_exists($realpath_before) && $this->fs->is_file($this->after.$localpath.$tmp_filename) ){
				// before が存在しなくて、after がファイルだったら
				$status = 'added';
				$after_info['md5']  = md5_file( $realpath_after  );
				$after_info['encoding']  = mb_detect_encoding( $this->fs->read_file( $realpath_after  ) );
				$after_info['crlf']  = $this->detect_cr_lf( $this->fs->read_file( $realpath_after  ) );
				$after_info['size']  = filesize( $realpath_after  );
				$after_info['timestamp']  = filemtime( $realpath_after  );
				$after_info['type']  = 'file';
			}elseif( !$this->fs->file_exists($realpath_before) && $this->fs->is_dir($this->after.$localpath.$tmp_filename) ){
				// before が存在しなくて、after がディレクトリだったら
				$status = 'added';
				$after_info['timestamp']  = filemtime( $realpath_after  );
				$after_info['type']  = 'dir';
			}elseif( $this->fs->is_file($realpath_before) && $this->fs->is_dir($this->after.$localpath.$tmp_filename) ){
				// before がファイルで、after がディレクトリだったら
				$status = 'changed';
				$before_info['md5'] = md5_file( $realpath_before );
				$before_info['encoding'] = mb_detect_encoding( $this->fs->read_file( $realpath_before ) );
				$before_info['crlf'] = $this->detect_cr_lf( $this->fs->read_file( $realpath_before ) );
				$before_info['size'] = filesize( $realpath_before );
				$before_info['timestamp'] = filemtime( $realpath_before );
				$after_info['timestamp']  = filemtime( $realpath_after  );
				$before_info['type'] = 'file';
				$after_info['type']  = 'dir';
			}elseif( $this->fs->is_dir($realpath_before) && $this->fs->is_file($this->after.$localpath.$tmp_filename) ){
				// before がディレクトリで、after がファイルだったら
				$status = 'changed';
				$after_info['md5']  = md5_file( $realpath_after  );
				$after_info['encoding']  = mb_detect_encoding( $this->fs->read_file( $realpath_after  ) );
				$after_info['crlf']  = $this->detect_cr_lf( $this->fs->read_file( $realpath_after  ) );
				$after_info['size']  = filesize( $realpath_after  );
				$before_info['timestamp'] = filemtime( $realpath_before );
				$after_info['timestamp']  = filemtime( $realpath_after  );
				$before_info['type'] = 'dir';
				$after_info['type']  = 'file';
			}else{
				// 不明な状態
				$status = 'unknown';
			}
			$this->report( $localpath.$tmp_filename, $status, $before_info, $after_info );

			// 両方またはどちらか一方がディレクトリだったら再帰的に掘る
			if( $this->fs->is_dir($realpath_before) || $this->fs->is_dir($this->after.$localpath.$tmp_filename) ){
				$this->diffdir( $localpath.$tmp_filename.'/' );
			}
		}

		return true;
	}

	/**
	 * make directory
	 */
	private function mkdir( $path, $FILE=null, $LINE=null ){
		if( !$this->fs->mkdir( $path ) ){
			$this->error('Making directory "'.realpath($path).'" was failed.', $FILE, $LINE);
			return false;
		}
		return true;
	}

	/**
	 * detect CR/LF
	 */
	private function detect_cr_lf( $text ){
		$memo = array();
		// $memo = array('CRLF'=>false, 'CR'=>false, 'LF'=>false);
		if( preg_match('/\r\n/', $text) ){
			$memo['CRLF'] = true;
		}
		$text = preg_replace('/\r\n/', '', $text);
		if( preg_match('/\r/', $text) ){
			$memo['CR'] = true;
		}
		if( preg_match('/\n/', $text) ){
			$memo['LF'] = true;
		}
		$rtn = implode( '/', array_keys( $memo ));
		if( !strlen($rtn) ){
			$rtn = 'unknown';
		}
		return $rtn;
	}

	/**
	 * report
	 */
	private function report( $localpath, $status, $before_info, $after_info ){
		if( !is_dir( $this->conf['output'] ) ){
			$this->fs->mkdir_r( $this->conf['output'] );
		}
		$row = array(
			'path'=>$localpath ,
			'status'=>$status ,
			'before_info'=>json_encode($before_info) ,
			'after_info'=>json_encode($after_info) ,
		);
		// array_push( $this->reports, $row );
		@error_log( $this->fs->mk_csv( array( $row ) ), 3, $this->conf['output'].'report.csv' );
	}

	/**
	 * get report
	 */
	public function get_reports(){
		// return $this->reports;
		$path = $this->conf['output'].'report.csv';
		$data = array();
		$rtn = array();
		if( is_file($path) ){
			$data = $this->fs->read_csv( $path );
		}
		while( count( $data ) ){
			$row = array_shift( $data );
			$i = 0;
			array_push( $rtn, array(
				'path'=>$row[$i++] ,
				'status'=>$row[$i++] ,
				'before_info'=>json_decode($row[$i++],true) ,
				'after_info'=>json_decode($row[$i++],true) ,
			) );
		}
		return $rtn;
	}

	/**
	 * reporting error message
	 */
	private function error( $msg, $FILE=null, $LINE=null ){
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
	 * verbose
	 * @param string $msg Message string.
	 * @return bool true
	 */
	public function verbose( $msg ){
		if( @$this->conf['verbose'] ){
			print $msg."\n";
		}
		return true;
	}

	/**
	 * getting output directory
	 * @return bool output directory
	 */
	public function get_output_dir(){
		return @$this->conf['output'];
	}

}
