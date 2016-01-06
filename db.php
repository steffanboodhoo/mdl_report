<?php

	$host = "localhost";
	$user = "admin";
	$pass = "pr&2race5aK=";
	$db = "moodledb";
	$conn = null;
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
ini_set('memory_limit','256M');

	$conn = new mysqli("localhost","root","admin","moodledb");
	if($conn -> connect_errno){
		echo "failed to connect to mysql:(". $conn->connect_errno .") ".$mysql->connect_error;
	}	
	
	// dailyCohortUsage(3,0,1445744779);
	
	//--------------------------- 1 
	function school_count(){
		$conn = new mysqli("localhost","root","admin","moodledb");
		if($conn -> connect_errno){
			echo "failed to connect to mysql:(". $conn->connect_errno .") ".$mysql->connect_error;
		}	
		$code = "select name from mdl_cohort;";

		$resp = $conn->query($code);
		$namelist = array();

		while ($row = $resp->fetch_assoc()) {
		   convert_name($row['name'],$namelist)."\n";
		}
		$arr['count']=count($namelist);
		$return_obj['values']=$arr;
		$return_obj['column_names']=['attribute','value'];
		echo json_encode($return_obj);
	}

	//-------------------------- 2
	function school_student_count(){
		$conn = new mysqli("localhost","root","admin","moodledb");
		if($conn -> connect_errno){
			echo "failed to connect to mysql:(". $conn->connect_errno .") ".$mysql->connect_error;
		}	
		$code = "select ". 
					"cm.cohortid,count(cm.userid) as 'amt',m.name ".
				"from ".
					"mdl_cohort_members cm, mdl_role_assignments r, mdl_cohort m ".
				"where ".
					"r.roleid = 5 and r.userid = cm.userid ".
				"and m.id = cm.cohortid ".
				"group by cm.cohortid ;";

		$resp = $conn->query($code);
		$school_count = array();
		$namelist = array();
		while ($row = $resp->fetch_assoc()) {
		   $key = convert_name($row['name'],$namelist);
		   if(array_key_exists($key, $school_count)){
		   		$school_count[$key] += $row['amt'];
		   }else{
		   		$school_count[$key] = $row['amt'];
		   }
		}
		$return_obj['values']=$school_count;
		$return_obj['column_names']=['Institution','Students registered'];
		echo json_encode($return_obj);
	}

	//-------------------3
	function active_students($threshold,$t_start,$t_stop){
		$conn = new mysqli("localhost","root","admin","moodledb");
		if($conn -> connect_errno){
			echo "failed to connect to mysql:(". $conn->connect_errno .") ".$mysql->connect_error;
		}	
		$code = "select ".
					"l.userid,count(l.id) as 'usage', c.name ".
				"from ".
					"mdl_logstore_standard_log l, mdl_role_assignments r, mdl_cohort c, mdl_cohort_members cm ".
				"where ".
					"r.userid = l.userid and r.roleid=5 ".
					"and cm.userid=l.userid and cm.cohortid=c.id ";
		if(( gettype($t_start) == "integer" or gettype($t_start) == "string") 
			and ( gettype($t_stop) == "integer" or gettype($t_stop) == "string")){
			$code = $code."and l.timecreated > ".$t_start." and l.timecreated < ".$t_stop." ";
		}
		$code = $code."group by l.userid ".
				"having count(l.id)>".$threshold.";";
		
		$resp = $conn->query($code);
		$arr['count'] = $resp->num_rows;
		$return_obj['values'] = $arr;
		$return_obj['column_names'] = ['attribute','value'];
		echo json_encode($return_obj);

		// echo count($resp->count);
		/*$student_list = array();
		$namelist = array();
		while ($row = $resp->fetch_assoc()) {
			$key = convert_name($row['name'],$namelist);
		}*/

	}

	//----------------4
	function active_students_bySchool($threshold,$t_start,$t_stop){
		$conn = new mysqli("localhost","root","admin","moodledb");
		if($conn -> connect_errno){
			echo "failed to connect to mysql:(". $conn->connect_errno .") ".$mysql->connect_error;
		}	
		$code = "select ".
					"l.userid,count(l.id) as 'amt', c.name ".
				"from ".
					"mdl_logstore_standard_log l, mdl_role_assignments r, mdl_cohort c, mdl_cohort_members cm ".
				"where ".
					"r.userid = l.userid and r.roleid=5 ".
					"and cm.userid=l.userid and cm.cohortid=c.id ";
		if(( gettype($t_start) == "integer" or gettype($t_start) == "string") 
			and ( gettype($t_stop) == "integer" or gettype($t_stop) == "string")){
			$code = $code."and l.timecreated > ".$t_start." and l.timecreated < ".$t_stop." ";
		}
		$code = $code."group by l.userid ".
				"having count(l.id)>".$threshold.";";
		
		$resp = $conn->query($code);
		
		$school_count = array();
		$namelist = array();
		while ($row = $resp->fetch_assoc()) {
			$key = convert_name($row['name'],$namelist);
			if(array_key_exists($key, $school_count)){
		   		$school_count[$key] += 1;
		   }else{
		   		$school_count[$key] = 1;
		   }
		}
		arsort($school_count);
		$return_obj['values'] = $school_count;
		$return_obj['column_names'] = ['Institution','User Count'];
		echo json_encode($return_obj);
	}

	//--------------5
	function active_teachers($threshold,$t_start,$t_stop){
		$conn = new mysqli("localhost","root","admin","moodledb");
		if($conn -> connect_errno){
			echo "failed to connect to mysql:(". $conn->connect_errno .") ".$mysql->connect_error;
		}	
		$code = "select ".
					"l.userid,count(l.id) as 'amt', c.name ".
				"from ".
					"mdl_logstore_standard_log l, mdl_role_assignments r, mdl_cohort c, mdl_cohort_members cm ".
				"where ".
					"r.userid = l.userid and (r.roleid=3 or r.roleid=4) ".
					"and cm.userid=l.userid and cm.cohortid=c.id ";
		if(( gettype($t_start) == "integer" or gettype($t_start) == "string") 
			and ( gettype($t_stop) == "integer" or gettype($t_stop) == "string")){
			$code = $code."and l.timecreated > ".$t_start." and l.timecreated < ".$t_stop." ";
		}
		$code = $code."group by l.userid ".
				"having count(l.id)>".$threshold.";";
		
		$resp = $conn->query($code);
		// echo "number of active students ".$resp->num_rows."\n";
		// echo count($resp->count);
		$teacher_list = array();
		$namelist = array();
		while ($row = $resp->fetch_assoc()) {
			// $key = convert_name($row['name'],$namelist);
			$teacher_list[($row['userid'])]=$row['amt'];
		}
		$return_obj['values'] = $teacher_list;
		$return_obj['column_names'] = ['teacher id',' activity count'];
		echo json_encode($return_obj);

	}

	//-------------6
	function dailyCohortUsage($start_time, $end_time){
		$conn = new mysqli("localhost","root","admin","moodledb");
		if($conn -> connect_errno){
			echo "failed to connect to mysql:(". $conn->connect_errno .") ".$mysql->connect_error;
		}
		$code = "select id,name from mdl_cohort;";
		$resp = $conn->query($code);
		$name_map = array();
		$namelist = array();
		while ($row = $resp->fetch_assoc()) {
			$name_map[$row['id']] = convert_name($row['name'],$namelist);
		}

		$code = "select ".
					"l.timecreated, cm.cohortid ".
				"from ".
					"mdl_logstore_standard_log l,mdl_cohort_members cm, mdl_role_assignments r ".
				"where ". 
					"l.userid=cm.userid and r.userid = l.userid ".
	  			"and r.roleid=5 order by l.timecreated asc;";	
	  	
		$resp = $conn->query($code);
		$days = array();
		$days[$start_time] = createEmptyCohortList($name_map);
		$next_day = $start_time + 86400;
		while ($row = $resp->fetch_assoc()) {
			// $key = convert_name($row['name'],$namelist);
			while( $row['timecreated'] > $next_day){
				$start_time = $next_day;
				$days[$start_time] = createEmptyCohortList($name_map);
				$next_day += 86400;
			}
			$days[$start_time][$name_map[$row['cohortid']]] += 1; 		
		}
		$return_obj['column_names'] = ['institutions','values'];
		$return_obj['values'] = $days;
		echo json_encode($return_obj);
	}
	
	//cohortid/userid
	function dailyCohortActiveStudents($threshold, $start_time, $end_time){
		$conn = new mysqli("localhost","root","admin","moodledb");
		if($conn -> connect_errno){
			echo "failed to connect to mysql:(". $conn->connect_errno .") ".$mysql->connect_error;
		}
		$code = "select id,name from mdl_cohort;";
		$resp = $conn->query($code);
		$name_map = array();
		$namelist = array();
		while ($row = $resp->fetch_assoc()) {
			$name_map[$row['id']] = convert_name($row['name'],$namelist);
		}

		$code = "select ".
					"l.userid, l.timecreated, cm.cohortid ".
				"from ".
					"mdl_logstore_standard_log l,mdl_cohort_members cm, mdl_role_assignments r ".
				"where ". 
					"l.userid=cm.userid and r.userid = l.userid ".
	  			"and r.roleid=5 order by l.timecreated asc;";	
	  	
		$resp = $conn->query($code);
		$days = array();
		$days[$start_time] = createEmptyCohortList($name_map);
		$next_day = $start_time + 86400;
		$day_rec = array();
		while ($row = $resp->fetch_assoc()) {
			// $key = convert_name($row['name'],$namelist);
			while( $row['timecreated'] > $next_day){
				// aggregate
				$days[$start_time] = createEmptyCohortList($name_map);//create a new thingy for today's aggregation
				foreach($day_rec as $insti){
					if($insti != null){
						foreach($insti as $usrCount){
							if($usrCount >= $threshold){
								$days[$start_time][$name_map[$row['cohortid']]] += 1;
							}
						}
					}
				}
				//iterate
				$start_time = $next_day;
				$day_rec = createEmptyCohortList($name_map);
				//print_r($day_rec);
				$next_day += 86400;
			}

			// if(isset($day_rec[$row['cohortid']]))
				// print_r( $day_rec[$row['cohortid']]); 

			if(!isset($day_rec[$row['cohortid']])){
				echo "--------being set------\n";
				// $day_rec[$row['cohortid']] = array($row['userid'] => 0);
				// $day_rec[$row['cohortid']] = [$row['userid'] => 0];
				// $day_rec[$row['cohortid']][$row['userid']] = 0;
			}
			// echo '---'.$day_rec[$row['cohortid']][$row['userid']].'---';
			$day_rec[$row['cohortid']][$row['userid']]=$day_rec[$row['cohortid']][$row['userid']]+1;
			//$days[$start_time][$name_map[$row['cohortid']]] += 1; 		
		}
		//$return_obj['column_names'] = ['institutions','values'];
		//$return_obj['values'] = $days;
		//echo json_encode($return_obj);
	}

	function getCoursesTaught($userid){
		$conn = new mysqli("localhost","root","admin","moodledb");
		if($conn -> connect_errno){
			echo "failed to connect to mysql:(". $conn->connect_errno .") ".$mysql->connect_error;
		}
		$code = "select ".
			"e.courseid ".
		"from ".
			"mdl_user_enrolments ue, mdl_enrol e ".
		"where ".
			"roleid = 0 and userid =".$userid.
			" and ue.enrolid = e.id; ";

		$resp = $conn->query($code);
		$courses = array();
		while ($row = $resp->fetch_assoc()) {
			 $courses.push($row['courseid']);
		}
		print_r($courses);
	}

	function getStudents($courseid){
		$conn = new mysqli("localhost","root","admin","moodledb");
		if($conn -> connect_errno){
			echo "failed to connect to mysql:(". $conn->connect_errno .") ".$mysql->connect_error;
		}
		$code = "select ".
				"ue.userid ".
			"from ".
				"mdl_user_enrolments ue, mdl_enrol e ".
			"where ".
				"e.roleid = 5 and ue.enrolid = e.id ".
				"and e.courseid=Y;";

		$resp = $conn->query($code);	
		$users = array();
		while ($row = $resp->fetch_assoc()) {
			 $users.push($row['userid']);
		}
		print_r($users);
	}

	function createEmptyCohortList($names){
		$aggr = array();
		foreach($names as $n){
			$aggr[$n]=0;
		}
		return $aggr;
	}


	function convert_name($name,&$namelist){
		$parts = explode("-",$name);
		$base_name = "";
		foreach($parts as $sub){
			if($base_name!=""){
				$temp = $base_name." ".$sub;
			}else{
				$temp = $sub;
			}
			if(in_array($temp, $namelist)){
				return $temp;				
			}else if(strlen($sub)<=1){
				break;
			}
			$base_name = $temp;
		}
		array_push($namelist, $base_name);
		return $base_name;
	}
	
	
	// dailyCohortActiveStudents(1,1420070400,1420156800);
	// dailyCohortUsage(1420070400,1420156800);
	/*school_count();
	school_student_count();
	active_students(10,0,1444173037);
	active_students_bySchool(10,0,1444173037);
	active_teachers(10,0,1444173037);*/
	//echo $conn;

	/*$conn->real_query("SELECT distinct(id) FROM cohort ORDER BY id ASC");
	$res = $conn->use_result();

	echo "Result set order...\n";
	while ($row = $res->fetch_assoc()) {
	    echo " id = " . $row['distinct(id)'] . "\n";
	}*/
?>