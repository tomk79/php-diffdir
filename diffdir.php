<?php
/**
 * command "diffdir".
 *
 * $ php diffdir.php  "./php/tests/sample_a/" "./php/tests/sample_b/"
 *
 * @author Tomoya Koyanagi <tomk79@gmail.com>
 */
require_once( __DIR__.'/vendor/autoload.php' );

$argv = $_SERVER['argv'];

array_shift($argv);
$after = array_pop($argv);
$before = array_pop($argv);
$conf = array('output'=>null, 'strip_crlf'=>false, 'verbose'=>true);

for( $i = 0; $i < count($argv); $i ++ ){
	if( $argv[$i] == '-o' ){
		if( preg_match( '/^\-[a-zA-Z0-9\-\_]+$/s', $argv[$i+1] ) ){
			continue;
		}
		$i++;
		$conf['output'] = $argv[$i];
		continue;
	}
	if( $argv[$i] == '--readme' ){
		if( preg_match( '/^\-[a-zA-Z0-9\-\_]+$/s', $argv[$i+1] ) ){
			continue;
		}
		$i++;
		$conf['readme'] = $argv[$i];
		continue;
	}
	if( $argv[$i] == '--strip-crlf' ){
		$conf['strip_crlf'] = true;
		continue;
	}
	if( $argv[$i] == '-v' ){
		$conf['verbose'] = true;
		continue;
	}
	if( $argv[$i] == '-q' ){
		$conf['verbose'] = false;
		continue;
	}
}

if( $conf['verbose'] ){
	print '-- starting "diffdir" --'."\n";
	print '$before = '.$before."\n";
	print '$after = '.$after."\n";
}
$diffdir = new tomk79\diffdir( $before, $after, $conf );
if( $conf['verbose'] ){
	if( $diffdir->is_error() ){
		print 'ERROR.'."\n";
		$errors = $diffdir->get_errors();
		foreach( $errors as $error ){
			print @$error['msg']."\n";
			print '  ('.@$error['FILE'].':'.@$error['LINE'].')'."\n";
		}
	}else{
		print 'success.'."\n";
		print ''."\n";
		print 'see: '.$diffdir->get_output_dir()."\n";
	}
	print ''."\n";
	print 'bye;'."\n";
	print ''."\n";
}
