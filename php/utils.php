<?php

	function receive() {
		if (isset($_GET["callback"])) {
			return array($_GET["callback"], $_GET["data"]);
		}
		else {
			return "";
		}
	}
	
	function send($callback, $dataToSend) {
		header('Content-Type: text/javascript; charset=UTF-8');
		echo $callback."(".$dataToSend.");";
	}
	
	function appendToFile($fileName, $data) {
		$file_handle = fopen($fileName, "a");
		fwrite($file_handle, $data);
		fwrite($file_handle, "\n");
		fclose($file_handle);
	}
	
	function readFromFile($filename) {
		echo("Reading file: {$filename}..<br />");
		return explode("\n", file_get_contents($filename));
	}
	
?>