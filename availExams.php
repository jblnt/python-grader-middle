<?php
	#header('content-type: application/json');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
	header('Access-Control-Allow-Headers: X-PINGOTHER, Content-Type');
	header('Access-Control-Max-Age: 86400');

    $_POST["status"]="available";

    /*
	//handle POST data from front-end...
	if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['username'], $_POST['password'])){
		$username=$_POST['username'];
		$password=$_POST['password'];				
	} else {
		//redirect and exit if GET and/or password/user field is empty.
		header('Location: https://www.njit.edu');
		exit();
    }
     */

	//Backend Connection:	
	$cb=curl_init();
	
	//backend checking
	$payload=array("available_exams" => $_POST["status"]);

	//$backURL="https://web.njit.edu/~job5/test/exam_avail.php";
	$backURL="https://web.njit.edu/~jcn22/displayExamStatus.php";

	$arr=array("Connection: Keep-Alive");
	curl_setopt($cb, CURLOPT_URL, $backURL);
	curl_setopt($cb, CURLOPT_POST, true);
	curl_setopt($cb, CURLOPT_POSTFIELDS, $payload);
	curl_setopt($cb, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:73.0) Gecko/20100101 Firefox/73.0");
	curl_setopt($ch, CURLOPT_HTTPHEADER, $arr);

	$backResult=curl_exec($cb);

    //echo $backResult;

	//Finished with cURL. So close connection..
	curl_close($cb);

    $exam_nums=explode(',', $backResult); 

    $formatted_arr=array();

    foreach($exam_nums as $value){
        array_push($formatted_arr, "Exam $value");
    }
    
    echo implode(',', $formatted_arr);
?>
