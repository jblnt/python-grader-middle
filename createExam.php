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

    $ids=explode(',', $_POST["Questions"]);
    $points=explode(',', $_POST["Points"]);

	//Backend Connection::	
	$cb=curl_init();

	$arr=array("Connection: Keep-Alive");
	curl_setopt($cb, CURLOPT_POST, true);
	curl_setopt($cb, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:73.0) Gecko/20100101 Firefox/73.0");
	curl_setopt($ch, CURLOPT_HTTPHEADER, $arr);

    //CREATE NEW EXAM AND GET ID
	//$backURL="https://web.njit.edu/~job5/test/db_createExam.php";
	$backURL="https://web.njit.edu/~jcn22/createExam.php";
	curl_setopt($cb, CURLOPT_URL, $backURL);

    curl_setopt($cb, CURLOPT_POSTFIELDS, array("exam_status" => "available", "grade_status" => "available"));
    $backResult_id=curl_exec($cb);

    //CREATE QUESTIONS TIED TO ID.
    $backURL="https://web.njit.edu/~jcn22/createExamQuestion.php";
    //$backURL="https://web.njit.edu/~job5/test/db_createExamQuestions.php";
	curl_setopt($cb, CURLOPT_URL, $backURL);

    if (count($ids) == count($points)){
        for ($i=0; $i < count($ids); $i++){
            $payload=array(
                "exam_id" => trim($backResult_id),
                "question_id" => intval($ids[$i]),
                "question_points" => intval($points[$i]),
            );

	        curl_setopt($cb, CURLOPT_POSTFIELDS, $payload);
	        $backResult=curl_exec($cb);
       }
    }
    
    //Finished with cURL. So close connection..
	curl_close($cb);
	
	echo $backResult;
?>
