 // var OPTIONS = ['Count_Schools','Students_Institution','Active_Students','Active_Students_School','Active_teachers','LTI_usage'];
 
 var DESCRIPTIONS = {
 	'Count_Schools' : 'This represents the number of schools that are registered with the system',
	'Students_Institution' : 'This is a listing of all the instituions and the number of students registered with them',
	'Active_Students' : 'This represents the number of students who logged on at least *threshold* times within the two dates chosen for the entire system',
	'Active_Students_School' : 'This represents the number of students who logged on at least *threshold* times within the two dates chosen for each school',
	'Active_teachers' : 'This represents the number of teachers who logged on at least *threshold* times within the two dates chosen for the entire system',
	'LTI_usage' : 'This represents the number of students who viewed the LTI system within the given dates'	
};

var NAMES = {
	'Count_Schools' : 'Count of Schools',
	'Students_Institution' : 'Institutions Students',
	'Active_Students' : 'Active Students',
	'Active_Students_School' : 'Active Students Per School',
	'Active_teachers' : 'Active Teachers',
	'LTI_usage' : 'LTI Usage'	
}

var BASE_URL = '/moodle/report/test/api.php';