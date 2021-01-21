<?php
	//header('content-type: application/json');

	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
	header('Access-Control-Allow-Headers: X-PINGOTHER, Content-Type');
	header('Access-Control-Max-Age: 86400');

    //var_dump($_POST);
    
    //handle POST data from front-end...
	if ($_SERVER['REQUEST_METHOD'] == 'POST'){
		$examNo=$_POST['examNo'];
		$username=$_POST['username'];		
		$updates=json_decode($_POST['updates'], true);
	} else {
		header('Location: https://www.njit.edu');
		exit();
	}

	//$i=array();
	//$o=array();

	//$case_num = 6;

    /*    
	for ($x = 1; $x <= $case_num; $x++) {
	    if($_POST["case".$x] != ""){
		array_push($i, '['.str_replace('"', '\"', $_POST['case'.$x]).']');		
		array_push($o, '['.str_replace('"', '', $_POST['out'.$x]).']');		
		#array_push($o, '['.str_replace('"', '\"', $_POST['out'.$x]).']');		
	    }
    } 
    */

	//$input=implode(";:", $i);
	//$output=implode(";:", $o);

	//Backend Connection::	
	$cb=curl_init();
	
	//backend checking
	//$payload=array("topic" => $bank_topic, "difficulty" => $bank_level, "func_name" => $bank_func_name, "func_desc" => $bank_func_desc, "input" => $input, "output" => $output);
  
	$backURL="https://web.njit.edu/~jcn22/updateGrade.php";

	$arr=array("Connection: Keep-Alive");
	curl_setopt($cb, CURLOPT_URL, $backURL);
	curl_setopt($cb, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($cb, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:73.0) Gecko/20100101 Firefox/73.0");
	curl_setopt($cb, CURLOPT_HTTPHEADER, $arr);

    foreach($updates as $key => $value){
        $total=0;

        $payload=array();
        $payload["grade_id"]=$key;
        $payload["username"]=$username;
        $payload["question_id"]=$value["question_id"];
        
        foreach($value as $k1 => $v1){
            $pos_1 = strpos($k1, "points");
            $pos_2 = strpos($k1, "case");

            if ($pos_1 !== false or $pos_2 !== false){
                $total= $total + intval(trim($v1));
            }
        }

        foreach($value as $k => $v){
            if ($k == "requirement_points" and $v == ""){
                continue;
            } else if ($k != "question_id"){
                $payload[$k]=$v;
            } 

        }
		$payload["total_points"]=$total;
		
		$payload["exam_id"]=$examNo;

        print_r($payload);

	    curl_setopt($cb, CURLOPT_POST, true);
	    curl_setopt($cb, CURLOPT_POSTFIELDS, $payload);
	    $backResult=curl_exec($cb);

        echo $backResult;
    }

	//Finished with cURL. So close connection..
	curl_close($cb);
	
?>
