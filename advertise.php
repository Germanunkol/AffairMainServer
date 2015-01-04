
<?php

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
			while(($line = fgets($file)) !== false) {
				$line = str_replace( "\n", "", $line );
				$s = explode( "\t", $line, 5000 );
				$serverlist[$s[0]] = array(
						"id" => $s[1],
						"info" => $s[2],
						"time" => $s[3],
						);
			}

		//echo "Server info received: ". $ip . ":" . $port . "\n";

		// TODO: Actually test the connection here.

		$serverlist[$address] = array(
				"id" => $id,
				"info" => $info,
				"time" => time(),
				);

		ftruncate( $file, 0 );
		rewind( $file );


		// Write the modified serverlist back to the file:
			foreach( $serverlist as $address => $s ) {
				$line = $address . "\t" . $s['id'] . "\t" . $s['info'] . "\t" . $s['time'] . "\n";
				echo "Line: '" . $line . "'";
				fwrite( $file, $line, 5000 );
			}
		flock($file, LOCK_UN);
	}
}
else
{
	echo "No port given.";
}
?>
