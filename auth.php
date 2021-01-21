<?php
	#file complete. last edit 2020 04 04 to add this header,

	header('content-type: application/json');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
	header('Access-Control-Allow-Headers: X-PINGOTHER, Content-Type');
	header('Access-Control-Max-Age: 86400');

	//handle POST data from front-end...
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username'], $_POST['password'])){
		$username=$_POST['username'];
		$password=$_POST['password'];				
	} else {
		//redirect and exit if GET and/or password/user field is empty.
		header('Location: https://www.njit.edu');
		exit();
	}

	//
	$arr=array("Connection: Keep-Alive");

	//Backend Connection:	
	$cb=curl_init();
	
	//backend checking
	$payload=array("username" => $username, "password" => $password);
	

	$backURL="https://web.njit.edu/~jcn22/backend.php";
	//$backURL="https://web.njit.edu/~job5/test/db.php";

	curl_setopt($cb, CURLOPT_URL, $backURL);
	curl_setopt($cb, CURLOPT_POST, true);
	curl_setopt($cb, CURLOPT_POSTFIELDS, $payload);
	curl_setopt($cb, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:73.0) Gecko/20100101 Firefox/73.0");
	curl_setopt($ch, CURLOPT_HTTPHEADER, $arr);

	$backResult=curl_exec($cb);

	//Finished with cURL. So close connection..
	curl_close($cb);

	//decode json from backend with role information
	$backJson=json_decode($backResult, true);	

	if ($backJson["Access"] == 0) {
		echo '{"backend": "invalid"}';
	} else {
		if ($backJson["role"] == "teacher"){
			echo '{"backend": "approved", "role": "T"}';
		} else if ($backJson["role"] == "student"){
			echo '{"backend": "approved", "role": "S"}';
	
		}
	}

?>
