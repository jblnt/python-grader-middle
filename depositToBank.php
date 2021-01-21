<?php
	//header('content-type: application/json');

	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
	header('Access-Control-Allow-Headers: X-PINGOTHER, Content-Type');
	header('Access-Control-Max-Age: 86400');

	//handle POST data from front-end...
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		$bank_level=strtolower($_POST['diff']);
		$bank_topic=strtolower($_POST['topic']);		
		$bank_func_name=$_POST['func'];
		$bank_func_desc=str_replace("'", '"', $_POST['desc']);
	} else {
		//redirect and exit if GET and/or password/user field is empty.
		header('Location: https://www.njit.edu');
		exit();
	}

	$i=array();
	$o=array();

	$case_num = 4;

	for ($x = 1; $x <= $case_num; $x++) {
	    if($_POST["case".$x] != ""){
		array_push($i, '['.str_replace('"', '\"', $_POST['case'.$x]).']');		
		array_push($o, '['.str_replace('"', '', $_POST['out'.$x]).']');		
		#array_push($o, '['.str_replace('"', '\"', $_POST['out'.$x]).']');		
	    }
	} 
	
	$input=implode(";:", $i);
	$output=implode(";:", $o);

	//Backend Connection::	
	$cb=curl_init();
	
	//backend checking
	$payload=array("topic" => $bank_topic, "difficulty" => $bank_level, "func_name" => $bank_func_name, "func_desc" => $bank_func_desc, "input" => $input, "output" => $output);

    if(isset($_POST['requirement'])){
        $bank_requirement=strtolower($_POST['requirement']);		
        $payload["requirement"]=$bank_requirement;
    }

	$backURL="https://web.njit.edu/~jcn22/addQuestion.php";

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
