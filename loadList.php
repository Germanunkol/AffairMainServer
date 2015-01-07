<?php

function loadList( $file )
{
	$serverlist = array();
	while(($line = fgets($file)) !== false) {
		$line = str_replace( "\n", "", $line );
		$s = explode( "\t", $line, 5000 );
		$serverlist[$s[0]] = array(
				"id" => $s[1],
				"info" => $s[2],
				"time" => $s[3],
				);
	}

	return $serverlist;
}
?>
