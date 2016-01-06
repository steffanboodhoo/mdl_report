<?php
// require('../../config.php');
require 'mdl_db.php';
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);
// require_login(); //Uncomment in a bit
/* get the system context*/
// $systemcontext = get_system_context();
// $name = optional_param('name', 'john doe', PARAM_STR); // what thingy is it
// $name = $_GET['name'];// THIS IS WHAT I DIDNT KNOW I COULD DO

$req_type = $_GET['req_type'];


if($req_type == 'Count_Schools'){
	echo school_count();
}else if($req_type == 'Students_Institution'){
	echo school_student_count();
}else if($req_type == 'Active_Students'){
	$threshold = $_GET['threshold'];
	$start_time = $_GET['start_time'];
	$end_time = $_GET['end_time'];
	if( $threshold == null ){
		$threshold = 3;
	}
	if( $start_time == null ){
	    $start_time = 0;
	}
	if( $end_time == null ){
	    $end_time = time();
	}
	echo active_students($threshold, $start_time, $end_time);
}else if($req_type == 'Active_Students_School'){
	$threshold = $_GET['threshold'];
	$start_time = $_GET['start_time'];
	$end_time = $_GET['end_time'];
	
	if( $threshold == null ){
		$threshold = 3;
	}
	if( $start_time == null ){
	    $start_time = 0;
	}
	if( $end_time == null ){
	    $end_time = time();
	}
	echo  active_students_bySchool($threshold, $start_time, $end_time);
}else if($req_type == 'Active_teachers'){
	$threshold = $_GET['threshold'];
	$start_time = $_GET['start_time'];
	$end_time = $_GET['end_time'];
	
	if( $threshold == null ){
		$threshold = 3;
	}
	if( $start_time == null ){
	    $start_time = 0;
	}
	if( $end_time == null ){
	    $end_time = time();
	}
	echo active_teachers($threshold, $start_time, $end_time);
}else if($req_type == 'daily_Cohort_Usage'){
	// $threshold = $_GET['threshold'];
	$start_time = $_GET['start_time'];
	$end_time = $_GET['end_time'];
	
	/*if( $threshold == null ){
		$threshold = 3;
	}*/
	if( $start_time == null ){
	    $start_time = 0;
	}
	if( $end_time == null ){
	    $end_time = time();
	}
	echo dailyCohortUsage($start_time, $end_time);
}else if($req_type == 'daily_Cohort_Active_Students'){
	$threshold = $_GET['threshold'];
	$start_time = $_GET['start_time'];
	$end_time = $_GET['end_time'];
	
	if( $threshold == null ){
		$threshold = 3;
	}
	if( $start_time == null ){
	    $start_time = 0;
	}
	if( $end_time == null ){
	    $end_time = time();
	}
	echo dailyCohortActiveStudents($threshold, $start_time, $end_time);
}else if($req_type == 'LTI_daily'){
	$threshold = $_GET['threshold'];
	$start_time = $_GET['start_time'];
	$end_time = $_GET['end_time'];
	
	if( $threshold == null ){
		$threshold = 3;
	}
	if( $start_time == null ){
	    $start_time = 0;
	}
	if( $end_time == null ){
	    $end_time = time();
	}
	echo dailyLTI($threshold, $start_time, $end_time);
}else if($req_type == 'LTI_usage'){
	$threshold = $_GET['threshold'];
	$start_time = $_GET['start_time'];
	$end_time = $_GET['end_time'];
	
	if( $threshold == null ){
		$threshold = 3;
	}
	if( $start_time == null ){
	    $start_time = 0;
	}
	if( $end_time == null ){
	    $end_time = time();
	}
	echo LTIusage($threshold, $start_time, $end_time);

}else if($req_type == 'custom_query'){
	$query = $_GET['query'];
	$columns = $_GET['names'];
	echo customQuery($query, $columns);
	// echo json_encode($columns);
}

?>