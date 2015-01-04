
<?php

// Initialize empty server list:
$serverlist = array();

$maxPassedTime = 70;	// maximum amount of time a since last server update before it is removed...

$id = htmlspecialchars($_POST["id"]);

// Get the list of servers from the hard disk:
$file = fopen( "serverlist.txt", "c+" );
if( $file ) {
	flock($file, LOCK_EX);
	while(($line = fgets($file)) !== false) {
		$line = str_replace( "\n", "", $line );
		$s = explode( "\t", $line, 500 );
		$serverlist[$s[0]] = array(
				"id" => $s[1],
				"info" => $s[2],
				"time" => $s[3],
				);
	}

	// set if an inactive server was in the list:
	$inactive = false;
	foreach( $serverlist as $address => $s ) {
		if (time() > $s['time'] + $maxPassedTime )
		{
			$inactive = true;
			unset( $serverlist[$address] );
		} else {
			//Filter by ID:
			if ( $s['id'] == $id )
			{
				$line = $address . "\t" . $s['info'] . "\n";
				echo $line;
			}
		}
	}

	// remove the inactive servers from the list by recreating it:
	if( $inactive )
	{
		ftruncate( $file, 0 );
		rewind( $file );

		// Write the modified serverlist back to the file:
		foreach( $serverlist as $address => $s ) {
			$line = $address . "\t" . "\t" . $s['info'] . "\n";
			//echo "Line: " . $line;
			fwrite( $file, $line, 500 );
		}
	}

	flock($file, LOCK_UN);
	fclose( $file );

} else {
	echo "File not found.";
}

?>
