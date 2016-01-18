<?php

require('../../config.php');
// require_login(); //Uncomment in a bit
/* get the system context*/
$systemcontext = get_system_context();
$url = new moodle_url('/report/test/index.php');

/*check basic permissions*/
//require_capbility('report/something/:view',$systemcontext); This was throwing an error i have no idea why

//get the string from the language file
$str_title = get_string('title','report_test');

$req_type     = optional_param('req_type', null, PARAM_STR); // what thingy is it

if($req_type==null){
	$PAGE->set_url($url);
	$PAGE->set_context($systemcontext);
	$PAGE->set_title($str);
	$PAGE->set_pagelayout('report');
	$PAGE->requires->js('/lib/jquery/jquery-1.11.2.min.js');
	// $PAGE->requires->js('/report/test/jquery-2.1.3.min.js');
	// $PAGE->requires->js('/report/test/highcharts.js');
	// $PAGE->requires->js('/report/test/bootstrap-datepicker.min.js');
	//$PAGE->requires->js('/report/test/datatables.min.js');
	$PAGE->requires->js('/report/test/stuff.js');
	$userid = $USER->id;

	echo $OUTPUT->header();
	echo $OUTPUT->heading($strtitle);
	// echo "<data id='base_url'>".getcwd()."</data>";
	echo '<div class="nav-tabs-custom">
	                <ul class="nav nav-tabs nav-justifie" id ="nav_tabs">
	                  <li id="ti0" class="ctabs active"><a id="link0" href="#tab_0" data-toggle="tab">Reports</a></li>
	                  <li id="ti1" class="ctabs" ><a id="link1" href="#tab_1" data-toggle="tab">Query</a></li>
	                </ul>
	                
	                <div class="tab-content">

	                  <div class="tab-pane active" id="tab_0">
	                     <div class"container" id=\'main\'></div>
	                  </div>

	                  <div class="tab-pane" id="tab_1">
	                    <div class="col-md-12"> <h5>Enter mysql Query then click the button to the right</h5></div>
	                    <div class="col-md-8 flex-cont"><textarea id="input_query"class="flex-input"></textarea></div>
	                    <div class="col-md-4 flex-cont"><button id="btn_query" class="flex-input btn btn-default btn-lg">
	                    	<span class="glyphicon glyphicon-send" aria-hidden="true"></span>
	                    </button></div>
	                    
	                    <div id="query_table" ></div>
	                  </div>
	                
	                 </div>
	    </div>';


	echo $OUTPUT->footer();
	echo "<link rel=\"stylesheet\" href=\"css\bootstrap.min.css\" type=\"text/css\">";
	echo "<link rel=\"stylesheet\" href=\"css\bootstrap-datepicker.min.css\" type=\"text/css\">";
	echo "<link rel=\"stylesheet\" href=\"css\bootgrid.css\" type=\"text/css\">";
	echo "<link rel=\"stylesheet\" href=\"http://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.4.0/css/font-awesome.min.css\">";
	echo "<link rel=\"stylesheet\" href=\"css\custom.css\" type=\"text/css\">";
	// echo "<link rel=\"stylesheet\" href=\"font-awesome.min.css\" type=\"text/css\">";
	
	//echo '<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/r/bs-3.3.5/dt-1.10.9,af-2.0.0,b-1.0.3,b-print-1.0.3,r-1.0.7,se-1.0.1/datatables.min.css"/>'; 
	//echo '<script type="text/javascript" src="https://cdn.datatables.net/r/bs-3.3.5/dt-1.10.9,af-2.0.0,b-1.0.3,b-print-1.0.3,r-1.0.7,se-1.0.1/datatables.min.js"></script>';
	// echo $req_type." ---------\n";
	// echo "---------".$userid;
	
	
	echo "<p id='base_url'>".getcwd()."</p>";
	// echo dirname(dirname(__FILE__));
}

function loadPage(){

}
///admin_externalpage_setup('reportsomething', '', null, '', array('pagelayout'=>'report'));


