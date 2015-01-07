
<?php

include "loadList.php";

if ( array_key_exists( "port", $_POST ) )
{
	// All the data needed for this (new?) server:
	$port = $_POST["port"];
	$id = $_POST["id"];
	$info = $_POST["info"];
	$ip = $_SERVER["REMOTE_ADDR"];
	$address = $ip . ":" . $port;

	// Initialize empty server list:
	$serverlist = array();

	// Get list of previously saved servers:
	$file = fopen( "serverlist.txt", "c+" );
	if( $file ) {
		flock($file, LOCK_EX);
		$serverlist = loadList( $file );

		// Actually test the connection here (but only if it wsn't active before):
		$connectionFailed = false;
		if( !isset( $serverlist[$address]) )
		{
			// Stop locking file, because this might take a while:
			flock($file, LOCK_UN);
			fclose($file);

			echo "Connecting:\n";
			$sock = fsockopen( "tcp://".$ip, $port, $errno, $errstr, 5 );
			if (!$sock)
			{
				echo "\t[Warning:] Main server can't connect to your application on port " . $port . ", maybe you need to open it on your router?\n";
				$connectionFailed = true;
			} else {
				echo "\tConnection successful.\n";
				fclose( $sock );
			}

			// Update the serverlist with possible changes which may have occurred:
			$file = fopen( "serverlist.txt", "c+" );
			if( $file ) {
				flock($file, LOCK_EX);
				$serverlist = loadList( $file );
			}
		}

		if ( !$connectionFailed )
		{
			$serverlist[$address] = array(
					"id" => $id,
					"info" => $info,
					"time" => time(),
					);
		}

		ftruncate( $file, 0 );
		rewind( $file );


		// Write the modified serverlist back to the file:
			foreach( $serverlist as $address => $s ) {
				$line = $address . "\t" . $s['id'] . "\t" . $s['info'] . "\t" . $s['time'] . "\n";
				echo "Line: '" . $line . "'";
				fwrite( $file, $line, 5000 );
			}
		flock($file, LOCK_UN);
		fclose( $file );
	}
}
else
{
	echo "No port given.\n";
}
?>
