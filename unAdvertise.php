<?php

	include "loadList.php";

	// All the data needed for this (new?) server:
	$port = $_POST["port"];
	$ip = $_SERVER["REMOTE_ADDR"];
	$address = $ip . ":" . $port;	// unique address string identifying a server

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
		$serverlist = loadList( $file );
		fclose( $file );

		echo "Server info received: ". $address . "\n";

		// Remove
		if( $serverlist[$address] )
		{
			unset( $serverlist[$address] );
		}

		// Write the modified serverlist back to the file:
		$file = fopen( "serverlist.txt", "w" );
		if( $file ) {
			foreach( $serverlist as $address => $s ) {
				$line = $address . "\t" . $s['id'] . "\t" . $s['info'] . "\t" . $s['time'] . "\n";
				//echo "Line: " . $line;
				fwrite( $file, $line, 500 );
			}
			fclose( $file );
		}
	}
?>
