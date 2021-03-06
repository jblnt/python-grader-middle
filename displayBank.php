<?php
	#header('content-type: application/json');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
	header('Access-Control-Allow-Headers: X-PINGOTHER, Content-Type');
	header('Access-Control-Max-Age: 86400');

	if($_SERVER['REQUEST_METHOD'] == 'GET'){
		header('Location: https://www.njit.edu');
		exit();
	}
	
	$payload=array();

	foreach ($_POST as $key => $value){
    		if (strlen($value) != 0){
			$payload[$key]=strtolower($value);
		}
	}

	//Backend Connection::	
	$cb=curl_init();
	
	//backend checking
    //$backURL="https://web.njit.edu/~job5/test/db_display.php";
    $backURL="https://web.njit.edu/~jcn22/displayBank.php";

	$arr=array("Connection: Keep-Alive");
	curl_setopt($cb, CURLOPT_URL, $backURL);
	curl_setopt($cb, CURLOPT_POST, true);
	curl_setopt($cb, CURLOPT_POSTFIELDS, $payload);
	curl_setopt($cb, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:73.0) Gecko/20100101 Firefox/73.0");
	curl_setopt($ch, CURLOPT_HTTPHEADER, $arr);

	$backResult=curl_exec($cb);

	//Finished with cURL. So close connection..
	curl_close($cb);
	
	echo $backResult;
?>
