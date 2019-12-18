$(function() {
  // choose target dropdown
  var select = $('#school_year_filter');
  select.html(select.find('option').sort(function(x, y) {
    // to change to descending order switch "<" for ">"
    return $(x).text() > $(y).text() ? 1 : -1;
  }));

  // select default item after sorting (first item)
  // $('select').get(0).selectedIndex = 0;
});
jQuery( document ).ready(function($) {
	if($('#sewing_dashboard_section_sa').is(':visible')){
		financialYearFilterDropdownList();
		financialYearFilterDropdownListForSchool();
		sewingStudentChartShowForSchoolAdmin();
		// Student Filter on change functionality
		jQuery("#student_fy_filter").on('change', function() {
			var fy_filter = $("#student_fy_filter").val();
			sewingStudentChartShowForSchoolAdmin(fy_filter);
		});
	}
	// Chart For HO Admin Dashboard
	if($('#sewing_dashboard_section').is(':visible')){
		jQuery('#school_chart_main').show();
		jQuery('#student_chart_main').hide();
		jQuery('#revenues_chart_main').hide();
		jQuery('#machine_chart_main').hide();

		jQuery("#no_of_school").parent().parent().addClass("active");
		jQuery("#no_of_student").parent().parent().removeClass('active');
		jQuery("#no_of_revenue").parent().parent().removeClass("active");
		jQuery("#no_of_machine").parent().parent().removeClass("active");
		// On Page load Chart Load
		locationFilterDropdownList();
		financialYearFilterDropdownList();
		financialYearFilterDropdownListForSchool();
		sewingSchoolChartShow(); 
		sewingStudentChartShow(); 
		sewingRevenueChartShow(); 
		sewingMachineChartShow();
		//School Click Open Chart
		jQuery("#no_of_school").on('click', function() {
			jQuery('#school_chart_main').show();
			jQuery('#student_chart_main').hide();
			jQuery('#revenues_chart_main').hide();
			jQuery('#machine_chart_main').hide();

			jQuery("#no_of_school").parent().parent().addClass("active");
			jQuery("#no_of_student").parent().parent().removeClass('active');
			jQuery("#no_of_revenue").parent().parent().removeClass("active");
			jQuery("#no_of_machine").parent().parent().removeClass("active");
			sewingSchoolChartShow();
		});
		//Student click open chart
		jQuery("#no_of_student").on('click', function() {
			jQuery('#school_chart_main').hide();
			jQuery('#student_chart_main').show();
			jQuery('#revenues_chart_main').hide();
			jQuery('#machine_chart_main').hide();

			jQuery("#no_of_school").parent().parent().removeClass("active");
			jQuery("#no_of_student").parent().parent().addClass('active');
			jQuery("#no_of_revenue").parent().parent().removeClass("active");
			jQuery("#no_of_machine").parent().parent().removeClass("active");
			sewingStudentChartShow();
		});
		//No of Learner click open chart
		jQuery("#no_of_revenue").on('click', function() {
			jQuery('#school_chart_main').hide();
			jQuery('#student_chart_main').hide();
			jQuery('#revenues_chart_main').show();
			jQuery('#machine_chart_main').hide();

			jQuery("#no_of_school").parent().parent().removeClass("active");
			jQuery("#no_of_student").parent().parent().removeClass('active');
			jQuery("#no_of_revenue").parent().parent().addClass("active");
			jQuery("#no_of_machine").parent().parent().removeClass("active");
			sewingRevenueChartShow();
		});
		//No of Pending MIS click open chart
		jQuery("#no_of_machine").on('click', function() {
			jQuery('#school_chart_main').hide();
			jQuery('#student_chart_main').hide();
			jQuery('#revenues_chart_main').hide();
			jQuery('#machine_chart_main').show();

			jQuery("#no_of_school").parent().parent().removeClass("active");
			jQuery("#no_of_student").parent().parent().removeClass('active');
			jQuery("#no_of_revenue").parent().parent().removeClass("active");
			jQuery("#no_of_machine").parent().parent().addClass("active");
			sewingMachineChartShow(); 
		});
		// School Filter on change functionality
		jQuery("#school_location_filter").on('change', function() {
			var location_id = $("#school_location_filter").val();
			var fy_filter = $("#school_fy_filter").val();
			sewingSchoolChartShow(location_id, fy_filter);
		});
		jQuery("#school_fy_filter").on('change', function() {
			var location_id = $("#school_location_filter").val();
			var fy_filter = $("#school_fy_filter").val();
			sewingSchoolChartShow(location_id, fy_filter);
		});

		// Student Filter on change functionality
		jQuery("#student_location_filter").on('change', function() {
			var location_id = $("#student_location_filter").val();
			var fy_filter = $("#student_fy_filter").val();
			sewingStudentChartShow(location_id, fy_filter);
		});
		jQuery("#student_fy_filter").on('change', function() {
			var location_id = $("#student_location_filter").val();
			var fy_filter = $("#student_fy_filter").val();
			sewingStudentChartShow(location_id, fy_filter);
		});

		// Revenue Filter on change functionality
		jQuery("#revenues_location_filter").on('change', function() {
			var location_id = $("#revenues_location_filter").val();
			var fy_filter = $("#revenues_fy_filter").val();
			sewingRevenueChartShow(location_id, fy_filter);
		});
		jQuery("#revenues_fy_filter").on('change', function() {
			var location_id = $("#revenues_location_filter").val();
			var fy_filter = $("#revenues_fy_filter").val();
			sewingRevenueChartShow(location_id, fy_filter);
		});
		// Machine Filter on change functionality
		jQuery("#machine_location_filter").on('change', function() {
			var location_id = $("#machine_location_filter").val();
			var fy_filter = $("#machine_fy_filter").val();
			sewingMachineChartShow(location_id, fy_filter);
		});
		jQuery("#machine_fy_filter").on('change', function() {
			var location_id = $("#machine_location_filter").val();
			var fy_filter = $("#machine_fy_filter").val();
			sewingMachineChartShow(location_id, fy_filter);
		});
	}
});
// Location option for school filter
function locationFilterDropdownList(){
	jQuery.ajax({
		url : drupalSettings.path.baseUrl+'sewing-dashboard-location-filter',
		type: 'POST',
		success: function(response) { 
			if(response) {
				var data = response.data;
				var location_select = $('#school_location_filter, #student_location_filter, #revenues_location_filter, #machine_location_filter');
				location_select.empty();
				var chartUserRole = $("#sewing_dashboard_user_role").val();
				if(chartUserRole != 'sewing_school_admin' && chartUserRole != 'sewing_ssi'){
				location_select.html("<option value=''>- All Location -</option>");
				}
            	$.each(data, function(key, value) {   
			    location_select.append($("<option></option>")
			                    .attr("value",key)
			                    .text(value)); 
				});
			} else {
				alert('Some error occurred. Please refresh the page and check again.');
				return false;
			}
		}
	}); 
}
// Month option for filter
function financialYearFilterDropdownList(){
	jQuery.ajax({
		url : drupalSettings.path.baseUrl+'sewing-dashboard-fy-filter',
		type: 'POST',
		success: function(response) { 
			if(response) {
				var data = response.data;
				var year_select = $('#student_fy_filter, #revenues_fy_filter, #machine_fy_filter');
				year_select.empty();
				//console.log(data);
				//year_select.html("<option value='_none'>- All (Financial Year) -</option>");
            	$.each(data, function(key, value) {   
			    year_select.append($("<option></option>")
			                    .attr("value",value)
			                    .text('FY '+value)); 
				});
			} else {
				alert('Some error occurred. Please refresh the page and check again.');
				return false;
			}
		}
	}); 
}
function financialYearFilterDropdownListForSchool(){
	jQuery.ajax({
		url : drupalSettings.path.baseUrl+'sewing-dashboard-fy-filter-for-school',
		type: 'POST',
		success: function(response) { 
			if(response) {
				var data = response.data;
				var year_select = $('#school_fy_filter');
				year_select.empty();
				//console.log(data);
				year_select.html("<option value='All'>FY All</option>");
            	$.each(data, function(key, value) {   
			    year_select.append($("<option></option>")
			                    .attr("value",value)
			                    .text('FY '+value)); 
				});
			} else {
				alert('Some error occurred. Please refresh the page and check again.');
				return false;
			}
		}
	}); 
}
// Get Chart data for preparing chart for school;
function sewingSchoolChartShow(location_id, fy_filter){
	//console.log(district_id);
	jQuery.ajax({
		url : drupalSettings.path.baseUrl+'sewing-dashboard-school-chart-dataset',
		type: 'POST',
		data: {location_id: location_id, fy_filter: fy_filter},
		beforeSend: function(){
	    	jQuery('#school_chart').html('<center style="padding: 30px; font-size: 20px;"><i class="fa fa-spinner" aria-hidden="true"></i> Chart is Loading...</center>');
	   	},
		success: function(response) {
			if(response) {
				schoolChart(response.chart);
				jQuery('#school_count').empty();
				jQuery('#school_count').append(response.count);
				
			} else {
				alert('Some error occurred. Please refresh the page and check again.');
				return false;
			}
		}
	});
}

// School chart generate function
function schoolChart(orderDetails) {
	google.charts.load('current', {'packages':['bar']});
	google.charts.setOnLoadCallback(drawSchoolChart);
	function drawSchoolChart() {
		var data = google.visualization.arrayToDataTable(orderDetails);
		var options = {
		  chart: {},
		  chartArea: {width: '50%'},
		  	tooltip: { isHtml: true },
		  	bars: 'vertical', 
		  	vAxis: {title: "Number of Schools",
		  			textStyle : {
			            fontSize: 14,
			        }
		  			},
    	  	hAxis: {
    	  		title: "School Type",
    	  		textStyle : {
		            fontSize: 14,
		        }
    	  	},
		  	height: 300,
		  	colors: ['#FF8C00', '#000000']
		};
		var chart = new google.charts.Bar(document.getElementById('school_chart'));
		chart.draw(data, google.charts.Bar.convertOptions(options));
	}
}
// Get Chart data for preparing chart for student;
function sewingStudentChartShow(location_id, fy_filter){
	//console.log(district_id);
	jQuery.ajax({
		url : drupalSettings.path.baseUrl+'sewing-dashboard-student-chart-dataset',
		type: 'POST',
		data: {location_id: location_id, fy_filter: fy_filter},
		beforeSend: function(){
	    	jQuery('#student_chart').html('<center style="padding: 30px; font-size: 20px;"><i class="fa fa-spinner" aria-hidden="true"></i> Chart is Loading...</center>');
	   	},
		success: function(response) {
			if(response) {
				studentChart(response.chart);
				jQuery('#student_count').empty();
				jQuery('#student_count').append(response.count);
				
			} else {
				alert('Some error occurred. Please refresh the page and check again.');
				return false;
			}
		}
	});
}
// Student chart generate function
function studentChart(orderDetails) {
	//console.log(orderDetails);
	google.charts.load('current', {'packages':['bar']});
	google.charts.setOnLoadCallback(drawStudentChart);
	function drawStudentChart() {
		var data = google.visualization.arrayToDataTable(orderDetails);
		var options = {
		  	chart: {
		    //title: 'Schools',
		   	// subtitle: 'Sales, Expenses, and Profit: 2014-2017',
		  	},
		  	tooltip: { isHtml: true },
		  	vAxis: {title: "Enrolled vs Course Completed",
		  			textStyle : {
			            fontSize: 14,
			        }
			    },
    	  	hAxis: {title: "School Type"},
		  	bars: 'vertical', 
		  	//vAxis: {format: 'none'},
		  	height: 300,
		  	colors: ['#6B8E23', '#FF8C00']
		};
		var chart = new google.charts.Bar(document.getElementById('student_chart'));
		chart.draw(data, google.charts.Bar.convertOptions(options));
	}
}
// Get Chart data for preparing chart for Revenue;
function sewingRevenueChartShow(location_id, fy_filter){
	//console.log(district_id);
	jQuery.ajax({
		url : drupalSettings.path.baseUrl+'sewing-dashboard-revenue-chart-dataset',
		type: 'POST',
		data: {location_id: location_id, fy_filter: fy_filter},
		beforeSend: function(){
	    	jQuery('#revenue_chart').html('<center style="padding: 30px; font-size: 20px;"><i class="fa fa-spinner" aria-hidden="true"></i> Chart is Loading...</center>');
	   	},
		success: function(response) {
			if(response) {
				revenueChart(response.chart);
				jQuery('#revenue_count').empty();
				jQuery('#revenue_count').append(response.count);
				
			} else {
				alert('Some error occurred. Please refresh the page and check again.');
				return false;
			}
		}
	});
}

// Revenue chart generate function
function revenueChart(orderDetails) {
	google.charts.load('current', {'packages':['bar']});
	google.charts.setOnLoadCallback(drawRevenueChart);
	function drawRevenueChart() {
		var data = google.visualization.arrayToDataTable(orderDetails);
		var options = {
		  chart: {},
		  chartArea: {width: '50%'},
		  	tooltip: { isHtml: true },
		  	bars: 'vertical', 
		  	vAxis: {title: "Amount",
		  			textStyle : {
			            fontSize: 14,
			        }
			    },
    	  	hAxis: {
    	  		title: "Revenue Head",
    	  		textStyle : {
		            fontSize: 14,
		        }
    	  	},
		  	height: 300,
		  	colors: ['#6B8E23', '#000000']
		};
		var chart = new google.charts.Bar(document.getElementById('revenue_chart'));
		chart.draw(data, google.charts.Bar.convertOptions(options));
	}
}
// Get Chart data for preparing chart for Machine;
function sewingMachineChartShow(location_id, fy_filter){
	//console.log(district_id);
	jQuery.ajax({
		url : drupalSettings.path.baseUrl+'sewing-dashboard-machine-chart-dataset',
		type: 'POST',
		data: {location_id: location_id, fy_filter: fy_filter},
		beforeSend: function(){
	    	jQuery('#machine_chart').html('<center style="padding: 30px; font-size: 20px;"><i class="fa fa-spinner" aria-hidden="true"></i> Chart is Loading...</center>');
	   	},
		success: function(response) {
			if(response) {
				machineChart(response.chart);
				jQuery('#machine_count').empty();
				jQuery('#machine_count').append(response.count);
				
			} else {
				alert('Some error occurred. Please refresh the page and check again.');
				return false;
			}
		}
	});
}
// Machine chart generate function
function machineChart(orderDetails) {
	//console.log(orderDetails);
	google.charts.load('current', {'packages':['bar']});
	google.charts.setOnLoadCallback(drawMachineChart);
	function drawMachineChart() {
		var data = google.visualization.arrayToDataTable(orderDetails);
		var options = {
		  	chart: {
		    //title: 'Schools',
		   	// subtitle: 'Sales, Expenses, and Profit: 2014-2017',
		  	},
		  	tooltip: { isHtml: true },
		  	vAxis: {title: "Straight Stitch vs Usha Janome sold"},
    	  	hAxis: {title: "Months", 
		  			textStyle : {
			            fontSize: 14,
			        }
			    },
		  	bars: 'vertical', 
		  	//vAxis: {format: 'none'},
		  	height: 300,
		  	colors: ['#6B8E23', '#FF8C00']
		};
		var chart = new google.charts.Bar(document.getElementById('machine_chart'));
		chart.draw(data, google.charts.Bar.convertOptions(options));
	}
}
// Get Chart data for preparing chart for student (School Code)
function sewingStudentChartShowForSchoolAdmin(fy_filter){
	//console.log(district_id);
	jQuery.ajax({
		url : drupalSettings.path.baseUrl+'sewing-dashboard-student-chart-dataset-sa',
		type: 'POST',
		data: {fy_filter: fy_filter},
		beforeSend: function(){
	    	jQuery('#student_chart').html('<center style="padding: 30px; font-size: 20px;"><i class="fa fa-spinner" aria-hidden="true"></i> Chart is Loading...</center>');
	   	},
		success: function(response) {
			if(response) {
				studentChartSA(response.chart);
				jQuery('#student-data-count').empty();
				jQuery('#student-data-count').append(response.count);
				
			} else {
				alert('Some error occurred. Please refresh the page and check again.');
				return false;
			}
		}
	});
}
// Student chart generate function
function studentChartSA(orderDetails) {
	//console.log(orderDetails);
	google.charts.load('current', {'packages':['bar']});
	google.charts.setOnLoadCallback(drawStudentChart);
	function drawStudentChart() {
		var data = google.visualization.arrayToDataTable(orderDetails);
		var options = {
		  	chart: {
		    //title: 'Schools',
		   	// subtitle: 'Sales, Expenses, and Profit: 2014-2017',
		  	},
		  	tooltip: { isHtml: true },
		  	vAxis: {title: "Enrolled vs Course Completed",
		  			textStyle : {
			            fontSize: 14,
			        }
			    },
    	  	hAxis: {title: "School Type"},
		  	bars: 'vertical', 
		  	//vAxis: {format: 'none'},
		  	height: 400,
		  	colors: ['#6B8E23', '#FF8C00']
		};
		var chart = new google.charts.Bar(document.getElementById('student_chart'));
		chart.draw(data, google.charts.Bar.convertOptions(options));
	}
}