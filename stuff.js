
// Shorthand for $( document ).ready()

(function(window){
	jQuery(function($) {
		var chart_count=0, curr_opt=null;
		$.get('literals.js');
		var base_url = $('#base_url').val();
		console.log('---------'+base_url);
		var OPTIONS = ['Count_Schools','Students_Institution','Active_Students','Active_Students_School','Active_teachers','LTI_usage'];
	   
	    var CONFIGURATIONS = {};
	    CONFIGURATIONS [ OPTIONS[0] ] = config_options0;
		CONFIGURATIONS [ OPTIONS[1] ] = config_options0;
		CONFIGURATIONS [ OPTIONS[2] ] = config_options1;
		CONFIGURATIONS [ OPTIONS[3] ] = config_options1;
		CONFIGURATIONS [ OPTIONS[4] ] = config_options1;
		CONFIGURATIONS [ OPTIONS[5] ] = config_options1;
	   
	    
	    $.get('libs/bootstrap-datepicker.min.js');
	    $.get('libs/highcharts.js');
	    $.get('libs/highchartsexporting.js');
	    $.get('libs/jquerybootgrid.js');
	    $.get('libs/jquerybase64.js');
	    $.get('libs/tableexport.js');
	    $.get('libs/html2canvas.js');
	    $.get('libs/base64.js');
	    $.get('libs/sprintf.js');
	    $.get('libs/jspdf.js');
	    $.get('query.js');
	    
	    // init();
		// daily_Cohort_Usage();	    
	    (function(){
	    	//Structure 
	    	$('<div/>',{'id':'content-container','class':'container-fluid'}).appendTo($('#main'));
	    	//MAIN CONTROLS
			$('<div/>',{'id':'content-controls','class':'row-fluid'}).appendTo($('#content-container'));
			//REPORT OPTIONS
			$('<div/>',{'id':'content-options','class':'row-fluid'}).appendTo($('#content-container'));
			//PRIMARY REPORT DISPLAY
			$('<div/>',{'id':'primary-report-content','class':'row'}).appendTo($('#content-container'));

			$('<div/>',{'id':'chart-container','class':'container-fluid'}).appendTo($('#main'));
			//Report Menu
	    	var option_code = "";
	    	OPTIONS.forEach(function(el){
	    		option_code+="<li id="+"\"option_"+el+"\" ><a  href=\"#\">"+el+"</a></li>";
	    	});
	    	var btn_code = "<div class=\"dropdown\">"
			  +"<button width=\"100%\" class=\"btn btn-default dropdown-toggle\" type=\"button\" id=\"dropdownMenu1\" data-toggle=\"dropdown\" aria-haspopup=\"true\" aria-expanded=\"true\">"
			    +"Select the desired report"
			    +"<span class=\"caret\"></span>"
			  +"</button>"
			  +"<ul class=\"dropdown-menu\" aria-labelledby=\"dropdownMenu1\">"
			    +option_code
			  +"</ul>"
			+"</div>";
	    	$("#content-controls").append(btn_code);

	    	OPTIONS.forEach(function(el){
	    		$("#option_"+el).click(function(){
	    			$('#primary-report-content').empty();
	    			document.getElementById('dropdownMenu1').innerHTML=el+"<span class=\"caret\"></span>";
		    		curr_opt=el;
		    		CONFIGURATIONS[el](el);// uses the values from options as keys to map to a function in configurations
		    	});
	    	});
	    })();

	    // For options WITH NO Configurations
	    function config_options0(choice_action){
	    	console.log('clicked opt 0');

	    	var str='<div class="container-fluid"> <div class="alert alert-success" role="alert">'+
				  '<span class="glyphicon glyphicon-exclamation-sign" aria-hidden="true"></span>'+
				  '<span class="sr-only">Error:</span>'+
				  'No configurations needed'+
				'</div></div>';
			$('#content-options').empty();
			$('#content-options').append(str);

			
			if(choice_action == OPTIONS[0]){ //Count_Schools
				_get(null,{'req_type':'Count_Schools'},function(dataObj){createTable('#primary-report-content',dataObj)});

			}else if(choice_action == OPTIONS[1]){//Students_Institution
				_get(null,{'req_type':'Students_Institution'},function(dataObj){
					createTable('#primary-report-content',
						dataObj,
						//function being passed into createTable
						function(key){
							var params = {'start_time':1438387200,'end_time':1444173037,'req_type':'daily_Cohort_Usage'}
			    			_get(null,params,function(data){
					    		//console.log(data);	
					    		// verticalFilterInstitution(data,key);
					    		verticalFilterInstitutionTime(data,key);
					    	});
						}
					);
				});
			}//extend here lol

	    }

	    // For options WITH Configurations
	    function config_options1(choice_action){
	    	console.log('clicked opt 1');
	    	//Create date picker widget
	    	var today = new Date();
	    	var str='<form class="bs-example bs-example-form" data-example-id="simple-input-groups">'+
	    				'<h2>Timeline</h2>'+
	    				'<div class="input-group input-daterange">'+
					    	'<input id="start_date" type="text" class="form-control date_field" value="01-01-2015">'+
					    	'<span class="input-group-addon">to</span>'+
					    	'<input id="end_date" type="text" class="form-control date_field" value="'+(today.getMonth()+1)+'-'+today.getDate()+'-'+today.getFullYear()+'">'+
						'</div>'+

						'<h2>Threshold</h2>'+
						// '<div class="input-group">'+
							'<div class="row">'+
								'<div class="col-md-5">' +
									'<span class="input-group-addon" id="basic-addon1">'+
										'Threshold DefinitionThreshold DefinitionThreshold DefinitionThreshold DefinitionThreshold DefinitionThreshold DefinitionThreshold DefinitionThreshold Definition'+
									'</span>'+
								'</div>'+	
								'<div class="col-md-7">' +
									'<input id="threshold" value="3" type="text" class="form-control" placeholder="How much is active" aria-describedby="basic-addon1">'+
								'</div>'+
							'</div>'+
						// '</div>'+
					'</form>';

			$('#content-options').empty();
			$('#content-options').append(str);
			$('.date_field').datepicker();

			//create confirm button
			str = '<div id="btn_cheat" class="container-fluid"><div id=\"btn_send\" width=\"100%\" type="button" class="btn btn-success btn-lg" >'+
					'Submit'+
					 // '<span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span> '+
					'</div></div>'; 
					$('#content-options').append(str);
			
			// $('#btn_send').on('click',(handle_option2(choice_action)));
	    	$('#btn_cheat').click(handle_option2);

	    	function handle_option2(){
		    	console.log('clicke');
		    	//Grab dates
				var dates = [$('#start_date').datepicker('getDate'),$('#end_date').datepicker('getDate')];
				dates = [dates[0].getTime()/1000,dates[1].getTime()/1000];
				var threshold = parseInt(document.getElementById("threshold").value);

				var params = {'start_time':dates[0],'end_time':dates[1],'threshold':threshold};
				if(choice_action==OPTIONS[2]){//Active_Students
					_get(null,{'start_time':dates[0],'end_time':dates[1],'threshold':threshold,'req_type':'Active_Students'},function(dataObj){createTable('#primary-report-content',dataObj)});
				
				}else if(choice_action==OPTIONS[3]){//Active_Students_School
					_get(null,{'start_time':dates[0],'end_time':dates[1],'threshold':threshold,'req_type':'Active_Students_School'},function(dataObj){createTable('#primary-report-content',dataObj)});
				
				}else if(choice_action==OPTIONS[4]){//Active_teachers
					_get(null,{'start_time':dates[0],'end_time':dates[1],'threshold':threshold,'req_type':'Active_teachers'},function(dataObj){createTable('#primary-report-content',dataObj)});
				
				}else if(choice_action==OPTIONS[5]){//Active_teachers
					_get(null,{'start_time':dates[0],'end_time':dates[1],'threshold':threshold,'req_type':'LTI_usage'},function(dataObj){createTable('#primary-report-content',dataObj)});
				
				}
		    	console.log(dates);
		    }
	    }	    
	    
	    function _get(url,params,call_back){

	    	var adj_url = window.location.origin;
	    	adj_url += getUrl() +"/api.php";
	    	console.log(adj_url);
	    	// adj_url+='/moodle_update/report/test/api.php';
	        $.ajax({
	            url:adj_url,
	            type:'GET',
	            data:params,
	            success:function(response){
	            	console.log(response);
	                if(typeof call_back==='function')
	                    call_back(JSON.parse(response))
	                else
	                    console.log(JSON.parse(response))
	            },
	            headers: {
            		"Content-Type":"application/x-www-form-urlencoded",
        		}
	        })
	    	
	    }
	    function getUrl(){
	    	var path = $("#base_url").text();
	    	console.log(path);
	    	var tolkens = path.split('html');
	    	return tolkens[1];
	    }

	    function createTable(table_container, dataObj, clickCallBack){
	    	console.log(dataObj)
	    	var columnNames = dataObj['column_names'], data = dataObj['values'];
	    	console.log(data);
	    	var table = $('<table/>',{id:'results_table',class:'display table table-condensed table-hover table-striped',cellspacing:'0',width:'100%'}),
			thead = $('<thead/>'), tr = $('<tr/>');

			//head [creation/init]
			columnNames.forEach(function(el){ //creates the header for each column
				// tr.append($('<th/>').append(el));
				tr.append($('<th/>',{'data-column-id':el}).append(el));
			});
			if(typeof(clickCallBack)=='function'){
						var td = $('<td/>',{'data-column-id':'commands','data-formatter':'commands','data-sortable':'false'}).append('Commands');
						tr.append(td);
			}
			thead.append(tr);

			var tbody = $('<tbody/>');
			
			//body [creation/init]
			for(var k=0; k<data.length; k++){
				tr = $('<tr/>');// create row
/*
				if(typeof(data[k]) != "object"){
					console.log(data[k]);
					tr.append($('<td/>').append(k)); tr.append($('<td/>').append(data[k]));
				}else{*/
					var row_data = data[k]
					for(var i=0; i<row_data.length; i++){
						tr.append($('<td/>').append(row_data[i])); //tr.append($('<td/>').append(data[k]));
					}
				// }
				tbody.append(tr);
			}

			table.append(thead);table.append(tbody);
	    	$(table_container).empty();
	    	Object.keys($);
	    	$(table_container).append($('<div/>',{'class':'container-fluid','id':'table-options'}));
	    	$(table_container).append(table);
	    	var grid = $('#results_table').bootgrid({
	    		 formatters: {
			        "commands": function(column, row){
			            return "<button type=\"button\" class=\"btn btn-xs btn-default command-graph\" data-row-id=\"" + row[columnNames[0]] + "\"><span class=\"fa fa-bar-chart\"></span></button> ";
			        }
			    }
			}).on("loaded.rs.jquery.bootgrid", function(){
			    /* Executes after data is loaded and rendered */
			    grid.find(".command-graph").on("click", function(e){
			        var key = $(this).data("row-id");
			    	console.log('about to call callback passed into createTable');
			    	clickCallBack(key);
			    })/*.end().find(".command-delete").on("click", function(e)
			    {
			        alert("You pressed delete on row: " + $(this).data("row-id"));
			    })*/;
			});
	    	setupTableExport();

	    }

	    function setupTableExport(){
	    	var html =			"<div class=\"btn-group align_btn\">"+
							"<button class=\"btn btn-info btn-sm dropdown-toggle\" data-toggle=\"dropdown\"><i class=\"fa fa-bars\"></i> Export Table Data</button>"+
							"<ul class=\"dropdown-menu \" id=\"export_btn\" role=\"menu\">"+
							"</ul></div>";		
			$('#table-options').empty();
			$('#table-options').append($('<div/>',{'class':'col-md-4','id':'table-option-left'}));
			$('#table-options').append($('<div/>',{'class':'col-md-8','id':'table-option-right'}));
			$('#table-option-left').append(html);
			html = "<h3>Description</h3><p>"+DESCRIPTIONS[curr_opt]+"</p>";
			$('#table-option-right').append(html);
			$('#export_btn').append($('<li/>').append(" <i class=\"fa fa-bars\"></i> JSON</a>").click(function(){$('#results_table').tableExport({type:'json',escape:'false'});}));
			$('#export_btn').append($('<li/>').append(" <i class=\"fa fa-bars\"></i> CSV</a>").click(function(){$('#results_table').tableExport({type:'csv',escape:'false'});}));
			$('#export_btn').append($('<li/>',{'class':'divider'}));
			$('#export_btn').append($('<li/>').append(" <i class=\"fa fa-bars\"></i> PNG</a>").click(function(){$('#results_table').tableExport({type:'png',escape:'false'});}));
			$('#export_btn').append($('<li/>').append(" <i class=\"fa fa-bars\"></i> PDF</a>").click(function(){$('#results_table').tableExport({type:'pdf',pdfFontSize:'7',escape:'false'});}));
			$('#export_btn').append($('<li/>',{'class':'divider'}));
			$('#export_btn').append($('<li/>').append(" <i class=\"fa fa-bars\"></i> XLS</a>").click(function(){$('#results_table').tableExport({type:'excel',escape:'false'});}));
			$('#export_btn').append($('<li/>').append(" <i class=\"fa fa-bars\"></i> Word</a>").click(function(){$('#results_table').tableExport({type:'doc',escape:'false'});})); 
	    }
	    
	    function daily_Cohort_Usage(){
	    	var params = {'start_time':1438387200,'end_time':1444173037,'req_type':'daily_Cohort_Usage'}
	    	_get(null,params,function(data){
	    		//console.log(data);	
	    		verticalFilterSystem(data);
	    	});	
	    }

	    function verticalFilterInstitution(dataObj,name){
	    	var data = dataObj['values'];
	    	var dates = [];
	    	var institution_data = {};
	    	for(time in data){
	    		dates.push((new Date().setSeconds(time)));
	    		for(institution in data[time]){
	    			if(typeof institution_data[institution]=='undefined')
	    				institution_data[institution] = [];
	    			institution_data[institution].push(data[time][institution]);
	    		}
	    	}
	    	var chartData = [];
	    	for(var institution in institution_data){
	    		var obj={'name':institution,'data':institution_data[institution]}
	    		chartData.push(obj);
	    	}
	    	console.log(chartData);
	    	
	    	var filteredData = chartData.filter(function(obj){
	    		return obj['name'] == name;
	    	})
	    	var idObj = createChart();
	    	createChartB(filteredData,dates,idObj['container']);
	    	$('html, body').animate({'scrollTop': $(idObj['container']).offset().top}, 'slow', 'swing');
	    }

	    function verticalFilterSystem(dataObj){
	    	var data = dataObj['values'];
	    	var time_series = [];//list of lists
	    	
	    	for(time in data){
				var sum = 0;
	    		for(institution in data[time]){
	    			sum += data[time][institution];
	    		}
	    		// console.log((new Date().setSeconds(time)))
	    		time_series.push([(new Date().setSeconds(time)),sum])
	    	}
	    	// console.log(time_series);
	    	var idObj = createChart();
	    	createChartT(time_series,idObj['container']);
	    	$('html, body').animate({'scrollTop': $(idObj['container']).offset().top}, 'slow', 'swing');
	    }

	    function verticalFilterInstitutionTime(dataObj,name){
	    	var data = dataObj['values'];
	    	var dates = [];
	    	var institution_data = {};
	    	for(time in data){
	    		dates.push((new Date().setSeconds(time)));
	    		for(institution in data[time]){
	    			if(typeof institution_data[institution]=='undefined')
	    				institution_data[institution] = [];
	    			institution_data[institution].push(data[time][institution]);
	    		}
	    	}
	    	var chartData = institution_data[name];
	    	var time_series = [];
	    	for(i = 0; i<chartData.length; i++){
	    		time_series.push([dates[i], chartData[i]]);
	    	}
	    	
	    	
	    	var idObj = createChart();
	    	createChartT(time_series, idObj['container']);
	    	$('html, body').animate({'scrollTop': $(idObj['container']).offset().top}, 'slow', 'swing');
	    }

	    function createChart(){
	    	var container_id='chart_cont_'+chart_count;
	    	var container = $('<div/>',{'class':'container-fluid','id':container_id}); 	    	
	    	container.appendTo($('#chart-container'));

	    	var close_btn = $("<button>",{id:"btn_close_"+chart_count,'class':'btn btn-default btn-lg btn-close'}).append($('<span>',{class:'fa fa-close fa-2x'}));
         	close_btn.appendTo(container);
         	setupCloseBtn("btn_close_"+chart_count,container_id);

	    	var chart_id='chart_'+chart_count;
	    	var chart = $('<div/>',{'class':'container-fluid','id':chart_id});
	    	chart.appendTo(container);
  	    	   	
	    	chart_count+=1;
	    	return {'container':'#'+chart_id};

	    	//what happens when u click the close button i.e. the Big X
	    	function setupCloseBtn(closeBtnId,divId){
		        $('#'+closeBtnId).click(function(){
		            console.log('click')
		            $('#'+divId).fadeOut(1000,function(){
		                $('#'+divId).remove();
		            })
		        })
		    }
	    }
	    //-------------------------------------------------------------------------------------
	    //-------------------------------------------------------------------------------------
	    //-------------------------------------------------------------------------------------

	    $('#btn_query').click(function(){
	        //get text
          	var query = $('#input_query').val();
            var params = {};
            params['query'] = query;
            params['names'] = getNames(query);
            // _get(null,{'req_type':'custom_query','query':params['query'],'names':params['names']},null)
            _get(null,{'req_type':'custom_query','query':params['query'],'names':params['names']},function(dataObj){createTable('#query_table',dataObj)})
	    })

		function getNames(str){
            //cleaning tolkens
            var clean_tolkens = [];
            var tolkens = str.split(" ");
            console.log(tolkens)
            for( var t=0; t<tolkens.length; t++){
            	console.log(tolkens[t])
                tolkens[t].trim();
                tolk_sub = tolkens[t].split(",");
                for(ts = 0; ts<tolk_sub.length; ts++){
                    if(tolk_sub[ts]!="")
                        clean_tolkens.push(tolk_sub[ts]);
                }
            }
            //getting column names
            var i = 1;
            tolkens = [];
            while(clean_tolkens[i] != "from"){
                if(clean_tolkens[i]=="as")
                    tolkens.pop();
                else
                    tolkens.push(clean_tolkens[i]);
                i++;
            }
            if(tolkens[0]=="*")
                return ['id','age','sex','address','diagnosis','symptoms','notes','onset','seen','referral'];
            
            return tolkens;
        }

	    

	    function createChartA (data,categories,chartContainer) {
	    	(function($){


	        $(function () {
	            $(chartContainer).highcharts({
	            	exporting: {
			            chartOptions: { // specific options for the exported image
			                plotOptions: {
			                    series: {
			                        dataLabels: {
			                            enabled: true
			                        }
			                    }
			                }
			            },
			            scale: 3,
	            		fallbackToExportServer: false
	        		},
	                title: {
	                    text: 'Activity over time',
	                    x: -20 //center
	                },
	                subtitle: {
	                    x: -20
	                },
	                xAxis: {
	                    type: datetime
	                },
	                yAxis: {
	                    title: {
	                        text: 'Predicted Values'
	                    },
	                    plotLines: [{
	                        value: 0,
	                        width: 1,
	                        color: '#808080'
	                    }]
	                },
	                tooltip: {
	                    valueDecimals: 3
	                },
	                legend: {
	                    layout: 'vertical',
	                    align: 'right',
	                    verticalAlign: 'middle',
	                    borderWidth: 0
	                },
	                series: data
	                /* {
	                    name: 'New York',
	                    data: [-0.2, 0.8, 5.7, 11.3, 17.0, 22.0, 24.8, 24.1, 20.1, 14.1, 8.6, 2.5]
	                }*/
	            });
	        });
			
			})(jQuery);
    	}

	    function createChartB(data,categories,chartContainer){
	    	// (function($){

	        $(function () {
	            $(chartContainer).highcharts({
	            	exporting: {
			            chartOptions: { // specific options for the exported image
			                plotOptions: {
			                    series: {
			                        dataLabels: {
			                            enabled: true
			                        }
			                    }
			                }
			            },
			            scale: 3,
	            		fallbackToExportServer: false
	        		},
	                title: {
	                    text: 'Activity over time'
	                },

	                xAxis: {
	                     // one week
	                    type: 'datetime'
	                },

	                yAxis: [{ // left y axis
	                    title: {
	                        text: null
	                    },
	                    labels: {
	                        align: 'left',
	                        x: 3,
	                        y: 16,
	                        format: '{value:.,0f}'
	                    },
	                    showFirstLabel: false
	                }, { // right y axis
	                    linkedTo: 0,
	                    gridLineWidth: 0,
	                    opposite: true,
	                    title: {
	                        text: null
	                    },
	                    labels: {
	                        align: 'right',
	                        x: -3,
	                        y: 16,
	                        format: '{value:.,0f}'
	                    },
	                    showFirstLabel: false
	                }],

	                legend: {
	                    align: 'left',
	                    verticalAlign: 'top',
	                    y: 20,
	                    floating: true,
	                    borderWidth: 0
	                },

	                tooltip: {
	                    shared: true,
	                    crosshairs: true
	                },

	                plotOptions: {
	                    series: {
	                        cursor: 'pointer',
	                        point: {
	                            events: {
	                                click: function (e) {
	                                    hs.htmlExpand(null, {
	                                        pageOrigin: {
	                                            x: e.pageX || e.clientX,
	                                            y: e.pageY || e.clientY
	                                        },
	                                        headingText: this.series.name,
	                                        maincontentText: Highcharts.dateFormat('%A, %b %e, %Y', this.x) + ':<br/> ' +
	                                            this.y + ' visits',
	                                        width: 200
	                                    });
	                                }
	                            }
	                        },
	                        marker: {
	                            lineWidth: 1
	                        }
	                    }
	                },
	                series:data
	            });
	        });

			// })(jQuery);
	    }

	    function createChartT(data,chartContainer){
	        $(chartContainer).highcharts({
	        	  exporting: {
		            chartOptions: { // specific options for the exported image
		                plotOptions: {
		                    series: {
		                        dataLabels: {
		                            enabled: true
		                        }
		                    }
		                }
		            },
		            scale: 3,
            		fallbackToExportServer: false
        		},
	            chart: {
	                zoomType: 'x'
	            },
	            title: {
	                text: 'Usage of the system over time'
	            },
	            subtitle: {
	                text: document.ontouchstart === undefined ?
	                        'Click and drag in the plot area to zoom in' : 'Pinch the chart to zoom in'
	            },
	            xAxis: {
	                type: 'datetime'
	            },
	            yAxis: {
	                title: {
	                    text: 'loggins'
	                }
	            },
	            legend: {
	                enabled: false
	            },
	            plotOptions: {
	                area: {
	                    fillColor: {
	                        linearGradient: {
	                            x1: 0,
	                            y1: 0,
	                            x2: 0,
	                            y2: 1
	                        },
	                        stops: [
	                            [0, Highcharts.getOptions().colors[0]],
	                            [1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
	                        ]
	                    },
	                    marker: {
	                        radius: 2
	                    },
	                    lineWidth: 1,
	                    states: {
	                        hover: {
	                            lineWidth: 1
	                        }
	                    },
	                    threshold: null
	                }
	            },

	            series: [{
	                type: 'area',
	                name: 'usage',
	                data: data
	            }]
	        });
	    }

	// });
	});

})(this);