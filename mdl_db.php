<?php
	


	//--------------------------- 1 
require('../../config.php');
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
ini_set('memory_limit','256M');
	// school_count();
	// LTIusage(3,0,1445744779);
	function school_count(){
		global $DB;
		$code = "select name from mdl_cohort;";
		$result = $DB->get_records_sql($code, null);	
		$namelist = array();

		foreach ($result as $row) {
		   convert_name($row->name,$namelist)."\n";
		}
		$return_vals = [];
		// $arr['count']=count($namelist);
		array_push($return_vals,['count',count($namelist)]);
		$return_obj['values']=$return_vals;
		$return_obj['column_names']=['attribute','value'];
		echo json_encode($return_obj);
	}
	// school_count(); works
	
	//-------------------------- 2
	function school_student_count(){
		global $DB;
		$code = "select ". 
					"cm.cohortid,count(cm.userid) as 'amt',m.name ".
				"from ".
					"mdl_cohort_members cm, mdl_role_assignments r, mdl_cohort m ".
				"where ".
					"r.roleid = 5 and r.userid = cm.userid ".
				"and m.id = cm.cohortid ".
				"group by cm.cohortid ;";

		$result = $DB->get_records_sql($code, null);
		
		$school_count = array();
		$namelist = array();

		foreach ($result as $row) {
		   $key = convert_name($row->name,$namelist);
		   if(array_key_exists($key, $school_count)){
		   		$school_count[$key] += $row->amt;
		   }else{
		   		$school_count[$key] = $row->amt;
		   }
		}


		$return_vals = [];
		foreach($school_count as $school => $val){
			array_push($return_vals, [$school,$val]);
		}
		$return_obj['values']=$return_vals;
		$return_obj['column_names']=['Institution','Students registered'];
		echo json_encode($return_obj);
	}
	// school_student_count(); works

	//-------------------3
	function active_students($threshold,$t_start,$t_stop){
		global $DB;
		$code = "select ".
					"l.userid,count(l.id) as 'usage', c.name ".
				"from ".
					"mdl_logstore_standard_log l, mdl_role_assignments r, mdl_cohort c, mdl_cohort_members cm ".
				"where ".
					"r.userid = l.userid and r.roleid=5 ".
					"and l.action = 'loggedin' ".
					"and cm.userid=l.userid and cm.cohortid=c.id ";

		if(( gettype($t_start) == "integer" or gettype($t_start) == "string") 
			and ( gettype($t_stop) == "integer" or gettype($t_stop) == "string")){
			$code = $code."and l.timecreated > ".$t_start." and l.timecreated < ".$t_stop." ";
		}
		$code = $code."group by l.userid ".
				"having count(l.id)>".$threshold.";";
		$result = $DB->get_records_sql($code, null);
		
		$return_vals = [];
		// $arr['count'] = count($result);
		array_push($return_vals,['count',count($result)]);
		$return_obj['values'] = $return_vals;
		$return_obj['column_names'] = ['attribute','value'];
		echo json_encode($return_obj);
	}
	// active_students(3,0,1445744779);

	//--------------------4
	function active_students_bySchool($threshold,$t_start,$t_stop){
		global $DB;
		$code = "select ".
					"l.userid,count(l.id) as 'amt', c.name ".
				"from ".
					"mdl_logstore_standard_log l, mdl_role_assignments r, mdl_cohort c, mdl_cohort_members cm ".
				"where ".
					"r.userid = l.userid and r.roleid=5 ".
					"and l.action = 'loggedin' ".
					"and cm.userid=l.userid and cm.cohortid=c.id ";
		if(( gettype($t_start) == "integer" or gettype($t_start) == "string") 
			and ( gettype($t_stop) == "integer" or gettype($t_stop) == "string")){
			$code = $code."and l.timecreated > ".$t_start." and l.timecreated < ".$t_stop." ";
		}
		$code = $code."group by l.userid ".
				"having count(l.id)>".$threshold.";";

		$result = $DB->get_records_sql($code, null);
		
		$school_count = array();
		$namelist = array();
		foreach ($result as $row) {
			$key = convert_name($row->name,$namelist);
			if(array_key_exists($key, $school_count)){
		   		$school_count[$key] += 1;
		   }else{
		   		$school_count[$key] = 1;
		   }
		}
		arsort($school_count);
		$return_vals = [];
		foreach($school_count as $school => $val){
			array_push($return_vals, [$school,$val]);
		}
		$return_obj['values'] = $return_vals;
		$return_obj['column_names'] = ['Institution','User Count'];
		echo json_encode($return_obj);
	}
	// active_students_bySchool(3,0,1445744779); works

	//-----------------5
	function active_teachers($threshold,$t_start,$t_stop){
		global $DB;
		$code = "select ".
					"l.userid,count(l.id) as 'amt', c.name ".
				"from ".
					"mdl_logstore_standard_log l, mdl_role_assignments r, mdl_cohort c, mdl_cohort_members cm ".
				"where ".
					"r.userid = l.userid and (r.roleid=3 or r.roleid=4) ".
					"and l.action = 'loggedin' ".
					"and cm.userid=l.userid and cm.cohortid=c.id ";
		if(( gettype($t_start) == "integer" or gettype($t_start) == "string") 
			and ( gettype($t_stop) == "integer" or gettype($t_stop) == "string")){
			$code = $code."and l.timecreated > ".$t_start." and l.timecreated < ".$t_stop." ";
		}
		$code = $code."group by l.userid ".
				"having count(l.id)>".$threshold.";";
		
		$result = $DB->get_records_sql($code, null);

		$teacher_list = array();
		$namelist = array();
		// while ($row = $resp->fetch_assoc()) {
		foreach($result as $row){
			$key = convert_name($row->name,$namelist);
			$teacher_list[($row->userid)]=$row->amt;
		}

		$return_vals = [];
		foreach($teacher_list as $teacher => $val){
			array_push($return_vals, [$teacher,$val]);
		}

		$return_obj['values'] = $return_vals;
		$return_obj['column_names'] = ['teacher id',' activity count'];
		echo json_encode($return_obj);
	}
	// active_teachers(3,0,1445744779);

	//---------------6
	function dailyCohortUsage($start_time, $end_time){
		global $DB;
		$code = "select id,name from mdl_cohort;";
		$name_map = array();
		$namelist = array();
		$result = $DB->get_records_sql($code, null);
		foreach($result as $row){
		// while ($row = $resp->fetch_assoc()) {
			$name_map[$row->id] = convert_name($row->name,$namelist);
		}

		$code = "select ".
					"l.timecreated, cm.cohortid ".
				"from ".
					"mdl_logstore_standard_log l,mdl_cohort_members cm, mdl_role_assignments r ".
				"where ". 
				"l.userid=cm.userid and r.userid = l.userid ".
				"and l.action = 'loggedin' ".
	  			"and r.roleid=5 order by l.timecreated asc;";	
	  	
		$result = $DB->get_records_sql($code, null);
		$days = array();
		$days[$start_time] = createEmptyCohortList($name_map);
		$next_day = $start_time + 86400;
		foreach($result as $row){
			
			while( $row->timecreated > $next_day){
				$start_time = $next_day;
				$days[$start_time] = createEmptyCohortList($name_map);
				$next_day += 86400;
			}
			$days[$start_time][$name_map[$row->cohortid]] += 1; 		
		}
		$return_obj['column_names'] = ['institutions','values'];
		$return_obj['values'] = $days;
		echo json_encode($return_obj);
	}
	// dailyCohortUsage(3,0,1445744779);

	function dailyLTI($start_time, $end_time){
		global $DB;
		$code = "select id,name from mdl_cohort;";
		$name_map = array();
		$namelist = array();
		$result = $DB->get_records_sql($code, null);
		foreach($result as $row){
		// while ($row = $resp->fetch_assoc()) {
			$name_map[$row->id] = convert_name($row->name,$namelist);
		}

		$code = "select ".
					"l.timecreated, cm.cohortid ".
				"from ".
					"mdl_logstore_standard_log l,mdl_cohort_members cm, mdl_role_assignments r ".
				"where ". 
				"l.userid=cm.userid and r.userid = l.userid ".
				"and l.component = 'mod_lti' ".
	  			"and r.roleid=5 order by l.timecreated asc;";	
	  	
		$result = $DB->get_records_sql($code, null);
		$days = array();
		$days[$start_time] = createEmptyCohortList($name_map);
		$next_day = $start_time + 86400;
		foreach($result as $row){
			
			while( $row->timecreated > $next_day){
				$start_time = $next_day;
				$days[$start_time] = createEmptyCohortList($name_map);
				$next_day += 86400;
			}
			$days[$start_time][$name_map[$row->cohortid]] += 1; 		
		}
		$return_obj['column_names'] = ['institutions','values'];
		$return_obj['values'] = $days;
		echo json_encode($return_obj);
	}

	function LTIusage($start_time, $end_time){
		global $DB;

		$code = "select ".
					"count(l.id) as 'count' ".
				"from ".
					"mdl_logstore_standard_log l, mdl_role_assignments r ".
				"where ". 
				"r.userid = l.userid  and l.component = 'mod_lti' ".
	  			"and r.roleid=5";	
	  	
		$result = $DB->get_records_sql($code, null);
		$return_vals = [];
		$obj = reset($result);
		array_push($return_vals,['count',$obj->count]);	
		$return_obj['column_names'] = ['attribute','value'];
		$return_obj['values'] = $return_vals;
		echo json_encode($return_obj);
	}
	// usageLTI(3,0,1445744779);
	function customQuery($query,$columns){
		global $DB;

		$result = $DB->get_records_sql($query,null);
		$return_data = [];
		foreach ($result as $row) {

			$cleaned_row = [];
			foreach($row as $val){
				array_push($cleaned_row,$val);	
			}
			array_push($return_data, $cleaned_row);
		}
		
		$return_obj['values'] = $return_data;
		$return_obj['column_names'] = $columns;
		echo json_encode($return_obj);

	}
	// customQuery('select id, firstname, lastname from mdl_user;',null);

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

	function createEmptyCohortList($names){
		$aggr = array();
		foreach($names as $n){
			$aggr[$n]=0;
		}
		return $aggr;
	}	
?>