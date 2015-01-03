
<?php

// Initialize empty server list:
$serverlist = array();

$maxPassedTime = 70;	// maximum amount of time a since last server update before it is removed...

// Get the list of servers from the hard disk:
$file = fopen( "serverlist.txt", "r" );
if( $file ) {
	flock($file, LOCK_EX);
	while(($line = fgets($file)) !== false) {
		$line = str_replace( "\n", "", $line );
		$s = explode( "\t", $line, 500 );
		$serverlist[$s[0]] = array(
				"port" => $s[1],
				"time" => $s[2],
				"info" => $s[3],
				);
	}
	fclose( $file );
}

//$inactive = array();	// set if an inactive server was in the list.
$inactive = false;
foreach( $serverlist as $ip => $s ) {
	if (time() > $s['time'] + $maxPassedTime )
	{
		$inactive = true;
		unset( $serverlist[$ip] );
		//array_push( $inactive, $ip );
	} else {
		$line = $ip . "\t" . $s['port'] . "\t" . $s['time'] . "\t" . $s['info'] . "\n";
		echo $line;
	}
}
if( $inactive )
{
	/*// remove the inactive servers from the list:
	foreach( $inactive as $num => $ip )
	{
		unset($serverlist[$ip]);
	}*/
	
	$file = fopen( "serverlist.txt", "r" );
	if( $file ) {
		// Write the modified serverlist back to the file:
		$file = fopen( "serverlist.txt", "w" );
		if( $file ) {
			foreach( $serverlist as $ip => $s ) {
				$line = $ip . "\t" . $s['port'] . "\t" . $s['time'] . "\t" . $s['info'] . "\n";
				//echo "Line: " . $line;
				fwrite( $file, $line, 500 );
			}
			fclose( $file );
		}

	}
}

flock($file, LOCK_UN);
?>
