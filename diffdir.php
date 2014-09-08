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
$path_output_dir = null;

for( $i = 0; $i < count($argv); $i ++ ){
	if( $argv[$i] == '-o' ){
		if( preg_match( '/^\-[a-zA-Z0-9]$/s', $argv[$i+1] ) ){
			continue;
		}
		$i++;
		$path_output_dir = $argv[$i];
		continue;
	}
}

print '-- starting "diffdir" --'."\n";
print '$before = '.$before."\n";
print '$after = '.$after."\n";
$diffdir = new tomk79\diffdir( $before, $after, array('output'=>$path_output_dir) );
if( $diffdir->is_error() ){
	print 'ERROR.'."\n";
	var_dump( $diffdir->get_errors() );
}else{
	print 'success.'."\n";
	print ''."\n";
	print 'see: '.$diffdir->get_output_dir()."\n";
}
print ''."\n";
print 'bye;'."\n";
print ''."\n";
