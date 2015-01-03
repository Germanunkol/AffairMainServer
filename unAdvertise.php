<?php

	// All the data needed for this (new?) server:
	$ip = $_SERVER["REMOTE_ADDR"];

	// Initialize empty server list:
	$serverlist = array();

	// If file doesn't exist, create it:
	if (! file_exists( "serverlist.txt" ) )
	{
		$file = fopen( "serverlist.txt", "w" );
		fclose($file);
	}

	// Get list of previously saved servers:
	$file = fopen( "serverlist.txt", "r" );
	if( $file ) {
		flock($file, LOCK_EX);
			while(($line = fgets($file)) !== false) {
				$s = explode( "\t", $line, 500 );
				$serverlist[$s[0]] = array(
						"port" => $s[1],
						"time" => $s[2],
						"info" => $s[3],
						);
			}
		fclose( $file );

		echo "Server info received: ". $ip . ":" . $port . "\n";

		// Remove
		if( $serverlist[$ip] )
		{
			unset( $serverlist[$ip] );
		}

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
?>
