<?php
	header('content-type: application/json');
	header('Access-Control-Allow-Origin: *');
	header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
	header('Access-Control-Allow-Headers: X-PINGOTHER, Content-Type');
	header('Access-Control-Max-Age: 86400');

	//var_dump($_POST);
	//echo ($_POST);
	//die();

	//Backend Connection:	
	$cb=curl_init();
	
	//backend answer
	//$backURL_question_data="https://web.njit.edu/~job5/test/db_display.php";
	$backURL_question_data="https://web.njit.edu/~jcn22/displayInputOutput.php";

	$arr=array("Connection: Keep-Alive");
	curl_setopt($cb, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($cb, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:73.0) Gecko/20100101 Firefox/73.0");
	curl_setopt($cb, CURLOPT_HTTPHEADER, $arr);

	//decode json from backend with role information

	//$backJson=json_decode($backResult, true);
	//$backJson = json_decode(file_get_contents('php://input'), true);
	$backJson = $_POST;

	$master_array=array();

	//BEGINNING FOR LOOP TO CYCLE THROUGH QUESTION ANSWERS:
	foreach($backJson as $key => $value) {

		//backend question data from ID
		$id=$value['qid'];

		$payload=array("id" => $id);

		curl_setopt($cb, CURLOPT_URL, $backURL_question_data);
		curl_setopt($cb, CURLOPT_POST, true);
		curl_setopt($cb, CURLOPT_POSTFIELDS, $payload);

		$ques_Json=curl_exec($cb);
		$ques_Data=json_decode($ques_Json, true);

		//var_dump($ques_Data);
		//die();
		
		//Question Specific Arr.	
		$payload=array();
		$comment=array();

		//{"0":{"studAns":"def doubit(x):\n\tprint(x*2)","examNo":"19","qid":"28","points":"100"}}
		$payload["exam_id"]=$value["examNo"];
		$payload["question_id"]=$value["qid"];
		$payload["username"]="jcn22";

		$ans=$value["studAns"];
		$payload["raw_ans"]=addslashes($ans);

		$func_name=$ques_Data[$id]["function"];
		$user_func_name=$func_name;
		
		#$t_cases_in=$value["inputs"];
		#$t_cases_out=$value["outputs"];
		$t_cases_in=$ques_Data[$id]["input"];
		$t_cases_out=$ques_Data[$id]["output"];

		$req=$ques_Data[$id]["requirement"];

		$points=intval($value["points"]);
		//$score=$points;
		$score=0;

		$inputs=explode(";:", $t_cases_in); 
		$outputs=explode(";:", $t_cases_out);

		if ($req !== null and $req !== "null"){
			$factorial=$points/(count($inputs) + 3);
		} else {
			$factorial=$points/(count($inputs) + 2);
		}

		$factorial=round($factorial, 2);

		//STARTING GRADING
        
		//Syntax Check for Colon and requirement
		$explode_str=explode("\n", $ans, 2);
		$colon_array=str_split(trim($explode_str[0]));

        //var_dump($req);
        //var_dump($explode_str[1]);
        //var_dump($explode_str[0]);

        if ($req !== null and $req !== "null"){
            $pos=strpos($explode_str[1], $req);
            if ($pos !== false) {
			    $payload["requirement_points"]=$factorial;
				$score+=$factorial;
				array_push($comment, "$req");
				array_push($comment, "match");
			    //array_push($comment, "Requirement [$req] was fulfilled.");
                //echo "got it";
            } else {
				$payload["requirement_points"]=0;
				array_push($comment, null);
				array_push($comment, "no match");		
                //echo "no good";
            }
		} else {
			//array_push($comment, "Requirement Was Not Assigned to Question.");
			array_push($comment, null);
			array_push($comment, null);
			$score+=$factorial;
		}

		if(end($colon_array) == ":"){
			$payload["colon_points"]=$factorial;
			$score+=$factorial;

			array_push($comment, ":");
			array_push($comment, "match");
			//array_push($comment, "Correct Syntax. Colon was present.");
		} else {
			//array_push($comment, "Syntax Error. Colon Not Present.");
			$payload["colon_points"]=0;
			
			//array_push($colon_array, ":");
			array_push($colon_array, ":\n");
		
			$explode_str[0]=implode("", $colon_array);
			$str=implode($explode_str);

			$ans=$str;

			array_push($comment, null);
			array_push($comment, "no match");
		}
		
        //Simple String search for name
		$first_line=explode(":", $ans);

		if (strpos(($first_line[0]), $func_name."(")){
			$score+=$factorial;
			$payload["func_name_points"]=$factorial;

			//array_push($comment, "Correct use of Function Name: $func_name");
			array_push($comment, "$func_name");
			array_push($comment, "match");
		} else {			
			$payload["func_name_points"]=0;

			$f_space=strpos($first_line[0], " ");
			$f_left_brac=strpos($first_line[0], "(");
			$user_func_name=trim(substr($first_line[0], $f_space, $f_left_brac - $f_space));

			//echo $user_func_name."<br>";

			//array_push($comment, "Did not Use Specified Function Name.");
			array_push($comment, "$user_func_name");
			array_push($comment, "no match");
		}

		//PYTHON exec
		//write student ans to file
		$pyfile = fopen("ans.py", "w") or die("Unable to open file!");
		$txt = $ans;
		fwrite($pyfile, $txt);
		fclose($pyfile);

		$ot=array();
		foreach ($inputs as $ii) {
			//echo $user_func_name;
			//echo $ii;

			$py_result= exec("python syntax_grade.py '$user_func_name' '$ii'", $out);
			$py_r_explode=explode("\n", $py_result);
			
			$arr=array();
			foreach ($py_r_explode as $r){
				array_push($arr, $r);
			}
			
			$formatted_str="[".implode(",", $arr)."]";
			array_push($ot, $formatted_str);			
		}

		for ($i = 0; $i < count($outputs); $i++) {
			array_push($comment, $func_name."(".trim($inputs[$i], "[]").")");
			array_push($comment, "".trim($outputs[$i], "[]"));

			if ($outputs[$i] == $ot[$i]){
				$score+=$factorial;
				$payload["testcase_".$i."_points"]=$factorial;

				//array_push($comment, "Passed testcase $i: $ot[$i]");				
				array_push($comment, "".trim($ot[$i], "[]"));
				array_push($comment, "match");
			}
			else {				
				$payload["testcase_".$i."_points"]=0;

				//array_push($comment, "Failed testcase $i: Required: $outputs[$i] Actual: $ot[$i]");
				array_push($comment, "".trim($ot[$i], "[]"));
				array_push($comment, "no match");
			}
			
		}

		array_push($comment, "$ $factorial ");

		$payload["comments"]=implode(" ; ", $comment)."$";
		$payload["final_score"]=$score;
		//$payload["raw_ans"]=addslashes($txt);
		//$payload["each_points"]=$factorial;

		$master_array[$key]=$payload;

        //POST to write to db.
	    //$backURL="https://web.njit.edu/~job5/test/grades_enter.php";
	    $backURL="https://web.njit.edu/~jcn22/insertGrade.php";

		curl_setopt($cb, CURLOPT_URL, $backURL);
		curl_setopt($cb, CURLOPT_POST, true);
		curl_setopt($cb, CURLOPT_POSTFIELDS, http_build_query($payload));

        print_r($payload);

		$grade_stat=curl_exec($cb);
        echo $grade_stat;

	//forloop end bracket
	}

	//Finished with cURL. So close connection..
	curl_close($cb);
	
	//echo json_encode($master_array);

?>
