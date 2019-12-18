//Drupal.behaviors.chartBehavior = {
//  attach: function (context, settings) {

//  }
//};
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
	// Chart For HO Admin Dashboard
	if($('#dashboard_section').is(':visible')){
		jQuery('#school_chart_main').show();
		jQuery('#agreement_chart_main').hide();
		jQuery('#learner_chart_main').hide();
		jQuery('#mis_chart_main').hide();
		jQuery("#no_of_school").parent().parent().addClass("active");
		jQuery("#pending_agreement_amount").parent().parent().removeClass('active');
		jQuery("#no_of_active_lerner").parent().parent().removeClass("active");
		jQuery("#no_of_pending_mis").parent().parent().removeClass("active");
		locationFilterDropdown();
		//districtFilterDropdown();
	    hoadminSchoolChartShow();
	    hoadminLearnerChartShow();
	    hoadminTotalMachineSold();
	    hoadminPendingInventory();
	    monthFilterDropdown();
	    misTimeWeeklyFilterDropdown();
	    //yearFilterDropdown();
	    financialYearFilterDropdown();
	}
	//No of School click open chart
	jQuery("#no_of_school").on('click', function() {
		jQuery('#school_chart_main').show();
		jQuery('#agreement_chart_main').hide();
		jQuery('#learner_chart_main').hide();
		jQuery('#mis_chart_main').hide();

		jQuery("#no_of_school").parent().parent().addClass("active");
		jQuery("#pending_agreement_amount").parent().parent().removeClass('active');
		jQuery("#no_of_active_lerner").parent().parent().removeClass("active");
		jQuery("#no_of_pending_mis").parent().parent().removeClass("active");
		hoadminSchoolChartShow();
	});
	//Agreement click open chart
	jQuery("#pending_agreement_amount").on('click', function() {
		jQuery('#school_chart_main').hide();
		jQuery('#agreement_chart_main').show();
		jQuery('#learner_chart_main').hide();
		jQuery('#mis_chart_main').hide();

		jQuery("#no_of_school").parent().parent().removeClass("active");
		jQuery("#pending_agreement_amount").parent().parent().addClass('active');
		jQuery("#no_of_active_lerner").parent().parent().removeClass("active");
		jQuery("#no_of_pending_mis").parent().parent().removeClass("active");
		hoadminAgreementChartShow();
	});
	//No of Learner click open chart
	jQuery("#no_of_active_lerner").on('click', function() {
		jQuery('#school_chart_main').hide();
		jQuery('#agreement_chart_main').hide();
		jQuery('#learner_chart_main').show();
		jQuery('#mis_chart_main').hide();

		jQuery("#no_of_school").parent().parent().removeClass("active");
		jQuery("#pending_agreement_amount").parent().parent().removeClass('active');
		jQuery("#no_of_active_lerner").parent().parent().addClass("active");
		jQuery("#no_of_pending_mis").parent().parent().removeClass("active");
		hoadminLearnerChartShow();
	});
	//No of Pending MIS click open chart
	jQuery("#no_of_pending_mis").on('click', function() {
		jQuery('#school_chart_main').hide();
		jQuery('#agreement_chart_main').hide();
		jQuery('#learner_chart_main').hide();
		jQuery('#mis_chart_main').show();

		jQuery("#no_of_school").parent().parent().removeClass("active");
		jQuery("#pending_agreement_amount").parent().parent().removeClass('active');
		jQuery("#no_of_active_lerner").parent().parent().removeClass("active");
		jQuery("#no_of_pending_mis").parent().parent().addClass("active");
		hoadminMISChartShow(); 
	});
	// School Location filter chart
	jQuery("#school_location_filter").on('change', function() {
		var district_id = $("#school_district_filter").val();
		var location_id = $("#school_location_filter").val();
		var yearFilter = $("#school_time_filter").val();
		hoadminSchoolChartShow(location_id, yearFilter, district_id);
	});
	// School Year filter chart
	jQuery("#school_time_filter").on('change', function() {
		var district_id = $("#school_district_filter").val();
		var location_id = $("#school_location_filter").val();
		var yearFilter = $("#school_time_filter").val();

		hoadminSchoolChartShow(location_id, yearFilter, district_id);
	});
	// School District filter chart
	jQuery("#school_district_filter").on('change', function() {
		var district_id = $("#school_district_filter").val();
		var location_id = $("#school_location_filter").val();
		var yearFilter = $("#school_time_filter").val();
				//alert(district_id);
		hoadminSchoolChartShow(location_id, yearFilter, district_id);
	});
	// Agreement Location filter chart
	jQuery("#agreement_location_filter").on('change', function() {
		var location_id = $("#agreement_location_filter").val();
		var monthData = $("#agreement_time_filter").val();
		hoadminAgreementChartShow(location_id, monthData);
	});
	// Agreement Month filter chart
	jQuery("#agreement_time_filter").on('change', function() {
		var location_id = $("#agreement_location_filter").val();
		var monthData = $("#agreement_time_filter").val();
		hoadminAgreementChartShow(location_id, monthData);
	});
	// Learner Time filter chart
	jQuery("#learner_time_filter").on('change', function() {
		var learner_time = $("#learner_time_filter").val();
		var location_id = $("#learner_location_filter").val();
		//alert(location_id);
		hoadminLearnerChartShow(learner_time, location_id);
	});
	// Learner Time filter chart
	jQuery("#learner_location_filter").on('change', function() {
		var learner_time = $("#learner_time_filter").val();
		var location_id = $("#learner_location_filter").val();
		//alert(location_id);
		hoadminLearnerChartShow(learner_time, location_id);
	});
	// Total Machine Location filter
	jQuery("#machine_location_filter").on('change', function() {
		var location_id = $("#machine_location_filter").val();
		var YearFilter = $("#machine_time_filter").val();
		//alert(location_id);
		jQuery('#machine_sold').empty();
		hoadminTotalMachineSold(location_id, YearFilter);
	});
	// Total Machine Location filter
	jQuery("#machine_time_filter").on('change', function() {
		var location_id = $("#machine_location_filter").val();
		var YearFilter = $("#machine_time_filter").val();
		//alert(location_id);
		jQuery('#machine_sold').empty();
		hoadminTotalMachineSold(location_id, YearFilter);
	});
	// Pending Inventory User Role Filter
	jQuery("#invontery_user_role_filter").on('change', function() {
		var user_role = $("#invontery_user_role_filter").val();
		var location_id = $("#inventory_location_filter").val();
		jQuery('#pending_inventory').empty();
		hoadminPendingInventory(user_role, location_id);
	});
	jQuery("#inventory_location_filter").on('change', function() {
		var location_id = $("#inventory_location_filter").val();
		var user_role = $("#invontery_user_role_filter").val();
		jQuery('#pending_inventory').empty();
		hoadminPendingInventory(user_role, location_id);
	});
	// Mis filter
	jQuery("#mis_time_filter").on('change', function() {
		var misTypeFilter = $("#mis_type_filter").val();
		var misTimeFilter = $("#mis_time_filter").val();
		hoadminMISChartShow(misTypeFilter, misTimeFilter);
	});
	// Mis filter
	jQuery("#mis_type_filter").on('change', function() {
		var misTypeFilter = $("#mis_type_filter").val();
		var misTimeFilter = $("#mis_time_filter").val();
		hoadminMISChartShow(misTypeFilter, misTimeFilter);
	});
	
});

// MIS Time option for filter
function misTimeWeeklyFilterDropdown(){
	var timeFilter = $("#mis_type_filter").val();
	if(timeFilter == 'weekly'){
		var url = 'hoadmin_mis_weekly_time_filter';
	}else if(timeFilter == 'monthly'){
		var url = 'hoadmin_mis_monthly_time_filter';
	}else if(timeFilter == 'quarterly'){
		var url = 'hoadmin_mis_quarterly_time_filter';
	}else{}
	jQuery.ajax({
		url : drupalSettings.path.baseUrl+url,
		type: 'POST',
		success: function(response) { 
			if(response) {
				var data = response.data;
				var mis_type_select = $('#mis_time_filter');
				mis_type_select.empty();
				//mis_type_select.html("<option value=''>- Select a value -</option>");
            	$.each(data, function(key, value) {   
			    mis_type_select.append($("<option></option>")
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
jQuery("#mis_type_filter").on('change', function() {
	var timeFilter = $("#mis_type_filter").val();
	if(timeFilter == 'weekly'){
		var url = 'hoadmin_mis_weekly_time_filter';
	}else if(timeFilter == 'monthly'){
		var url = 'hoadmin_mis_monthly_time_filter';
	}else if(timeFilter == 'quarterly'){
		var url = 'hoadmin_mis_quarterly_time_filter';
	}else{}
	jQuery.ajax({
		url : drupalSettings.path.baseUrl+url,
		type: 'POST',
		success: function(response) { 
			if(response) {
				var data = response.data;
				var mis_type_select = $('#mis_time_filter');
				mis_type_select.empty();
				//mis_type_select.html("<option value=''>- Select a value -</option>");
            	$.each(data, function(key, value) {   
			    mis_type_select.append($("<option></option>")
			                    .attr("value",key)
			                    .text(value)); 
				});
			} else {
				alert('Some error occurred. Please refresh the page and check again.');
				return false;
			}
		}
	}); 
});
// District option for school filter
/*function districtFilterDropdown(){
	jQuery.ajax({
		url : drupalSettings.path.baseUrl+'hoadmin_district_filter',
		type: 'POST',
		success: function(response) { 
			if(response) {
				var data = response.data;
				var district_select = $('#school_district_filter');
				district_select.empty();
				district_select.html("<option value=''>- Select a District -</option>");
            	$.each(data, function(key, value) {   
			    district_select.append($("<option></option>")
			                    .attr("value",key)
			                    .text(value)); 
				});
			} else {
				alert('Some error occurred. Please refresh the page and check again.');
				return false;
			}
		}
	}); 
}*/
// Location option for school filter
function locationFilterDropdown(){
	jQuery.ajax({
		url : drupalSettings.path.baseUrl+'hoadmin_location_filter',
		type: 'POST',
		success: function(response) { 
			if(response) {
				var data = response.data;
				
				var location_select = $('#school_location_filter, #agreement_location_filter, #learner_location_filter, #mis_location_filter, #machine_location_filter, #inventory_location_filter');
				location_select.empty();
				var chartUserRole = $("#chart_user_role").val();
				if(chartUserRole != 'pc' && chartUserRole != 'ngo_admin'){
					location_select.html("<option value=''>- All Location -</option>");
				}
            	$.each(data, function(key, value) {   
			    location_select.append($("<option></option>")
			                    .attr("value",key.replace(/['"]+/g, ''))
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
function monthFilterDropdown(){
	jQuery.ajax({
		url : drupalSettings.path.baseUrl+'hoadmin_month_filter',
		type: 'POST',
		success: function(response) { 
			if(response) {
				var data = response.data;
				var month_select = $('#agreement_time_filter');
				month_select.empty();
				month_select.html("<option value=''>- Till Now -</option>");
            	$.each(data, function(key, value) {   
			    month_select.append($("<option></option>")
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
/*function yearFilterDropdown(){
	jQuery.ajax({
		url : drupalSettings.path.baseUrl+'hoadmin_year_filter_options',
		type: 'POST',
		success: function(response) { 
			if(response) {
				var data = response.data;
				//var year_select = $('#learner_time_filter');
				year_select.empty();
				var data = $.map(data, function(value, index) {
				    return [value];
				});
				data.reverse();
            	$.each(data, function(key, value) {   
			    year_select.append($("<option></option>")
			                    .attr("value",value)
			                    .text(value)); 
				});
			} else {
				alert('Some error occurred. Please refresh the page and check again.');
				return false;
			}
		}
	}); 
}*/
// Month option for filter
function financialYearFilterDropdown(){
	jQuery.ajax({
		url : drupalSettings.path.baseUrl+'hoadmin_financial_year_filter_options',
		type: 'POST',
		success: function(response) { 
			if(response) {
				var data = response.data;
				var year_select = $('#machine_time_filter, #school_time_filter, #learner_time_filter');
				year_select.empty();
				//console.log(data);
				year_select.html("<option value=''>- All -</option>");
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
// Get Chart data for preparing chart
function hoadminSchoolChartShow(location_id, yearFilter, district_id){
	//console.log(district_id);
	jQuery.ajax({
		url : drupalSettings.path.baseUrl+'hoadmin_school_chart_load',
		type: 'POST',
		data: {location_id: location_id, yearFilter: yearFilter, district_id: district_id},
		beforeSend: function(){
	    	jQuery('#school_chart').html('<center style="padding: 30px; font-size: 20px;"><i class="fa fa-spinner" aria-hidden="true"></i> Chart is Loading...</center>');
	   	},
		success: function(response) {
			if(response) {
				schoolChart(response.chartData);
				jQuery('#schoolChartDataCount').empty();
				jQuery('#schoolChartDataCount').append(response.rawData);
				
			} else {
				alert('Some error occurred. Please refresh the page and check again.');
				return false;
			}
		}
	});
}
// Get Chart data for preparing chart
function hoadminAgreementChartShow(location_id, monthData){
	jQuery.ajax({
		url : drupalSettings.path.baseUrl+'hoadmin_agreement_chart_load',
		type: 'POST',
		data: {location_id: location_id, monthData: monthData},
		beforeSend: function(){
	    	jQuery('#agreement_chart').html('<center style="padding: 30px; font-size: 20px;"><i class="fa fa-spinner" aria-hidden="true"></i> Chart is Loading...</center>');
	   	},
		success: function(response) {
			if(response) {
				if($.isArray(response)){
					AgreementChart(response);
				}else{
					var agreement_chart = $('#agreement_chart');
					agreement_chart.empty();
					agreement_chart.html('<center style="padding: 30px; font-size: 20px;">'+response+'</center>');
				}
				
			} else {
				alert('Some error occurred. Please refresh the page and check again.');
				return false;
			}
		}
	});
}
// Get Chart data for preparing chart
function hoadminLearnerChartShow(learner_time, location_id){
	//console.log(location_id);
	jQuery.ajax({
		url : drupalSettings.path.baseUrl+'hoadmin_learner_chart_load',
		type: 'POST',
		data: {learner_time: learner_time, location_id: location_id},
		beforeSend: function(){
	    	jQuery('#learner_chart').html('<center style="padding: 30px; font-size: 20px;"><i class="fa fa-spinner" aria-hidden="true"></i> Chart is Loading...</center>');
	   	},
		success: function(response) {
			if(response) {
				//LearnerChart(response);
				
				LearnerChart(response.chart);
				jQuery('#learnerChartDataCount').empty();
				jQuery('#learnerChartDataCount').append(response.count);
			} else {
				alert('Some error occurred. Please refresh the page and check again.');
				return false;
			}
		}
	});
}
// Get Chart data for preparing chart
function hoadminMISChartShow(misTypeFilter, misTimeFilter){
	jQuery.ajax({
		url : drupalSettings.path.baseUrl+'hoadmin_mis_chart_load',
		type: 'POST',
		data: {misTypeFilter: misTypeFilter, misTimeFilter: misTimeFilter},
		beforeSend: function(){
	    	jQuery('#mis_chart').html('<center style="padding: 30px; font-size: 20px;"><i class="fa fa-spinner" aria-hidden="true"></i> Chart is Loading...</center>');
	   	},
		success: function(response) {
			if(response) {
				MisChart(response);
			} else {
				alert('Some error occurred. Please refresh the page and check again.');
				return false;
			}
		}
	});
}
// Total Machine Sold Data
function hoadminTotalMachineSold(location_id, YearFilter){
	//console.log(location_id);
	jQuery.ajax({
		url : drupalSettings.path.baseUrl+'hoadmin_total_machine_sold',
		type: 'POST',
		data: {location_id: location_id, YearFilter: YearFilter},
		success: function(response) {
			//console.log(response);
			if(response) {
				jQuery('#machine_sold').html(response);
			} else {
				alert('Some error occurred. Please refresh the page and check again.');
				return false;
			}
		}
	});
}
// Total Machine Sold Data
function hoadminPendingInventory(user_role, location_id){
	//console.log(user_role);
	jQuery.ajax({
		url : drupalSettings.path.baseUrl+'hoadmin_pending_inventory',
		type: 'POST',
		data: {user_role: user_role, location_id: location_id},
		success: function(response) {
			//console.log(response);
			if(response) {
				//jQuery('#pending_inventory')empty();
				jQuery('#pending_inventory').html(response);
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
		  chart: {
		    //title: 'Schools',
		   // subtitle: 'Sales, Expenses, and Profit: 2014-2017',
		  },
		  
		  chartArea: {width: '50%'},
		  	tooltip: { isHtml: true },
		  	bars: 'vertical', 
		  	vAxis: {title: "Number of Schools",
		  		//textStyle : {
		            //fontSize: 12, // or the number you want
		        //}
		  	},
    	  	hAxis: {
    	  		title: "School Type",
    	  		textStyle : {
		            fontSize: 14,
		            //bold: true,
		        }

    	  	},
		  	//vAxis: {format: 'none'},
		  	height: 300,
		  	colors: ['#FF8C00', '#000000']
		};
		var chart = new google.charts.Bar(document.getElementById('school_chart'));
		chart.draw(data, google.charts.Bar.convertOptions(options));
	}
}
// Agreement chart generate function
function AgreementChart(orderDetails) {
	google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawAgreementChart);
      function drawAgreementChart() {
        var data = google.visualization.arrayToDataTable(orderDetails);
        var options = {
        	is3D: true,
         	// title: 'My Daily Activities'
         	tooltip: { isHtml: true },
         	height: 300,
         	colors: ['#FF0000', '#6B8E23']
        };
        var chart = new google.visualization.PieChart(document.getElementById('agreement_chart'));
        chart.draw(data, options);
      }
}
// Learner chart generate function
function LearnerChart(orderDetails) {
	//console.log(orderDetails);
	google.charts.load('current', {'packages':['bar']});
	google.charts.setOnLoadCallback(drawLearnerChart);
	function drawLearnerChart() {
		var data = google.visualization.arrayToDataTable(orderDetails);
		var options = {
		  	chart: {
		    //title: 'Schools',
		   	// subtitle: 'Sales, Expenses, and Profit: 2014-2017',
		  	},
		  	tooltip: { isHtml: true },
		  	vAxis: {title: "Number of Learners"},
    	  	hAxis: {title: "Months"},
		  	bars: 'vertical', 
		  	//vAxis: {format: 'none'},
		  	height: 300,
		  	colors: ['#6B8E23', '#FF8C00']
		};
		var chart = new google.charts.Bar(document.getElementById('learner_chart'));
		chart.draw(data, google.charts.Bar.convertOptions(options));
	}
}
// Agreement chart generate function
function MisChart(orderDetails) {
	google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawMisChart);
      function drawMisChart() {
        var data = google.visualization.arrayToDataTable(orderDetails);
        var options = {
        	is3D: true,
         	// title: 'My Daily Activities'
         	tooltip: { isHtml: true },
         	height: 300,
         	colors: ['#21AFC5', '#9030AB']
        };
        var chart = new google.visualization.PieChart(document.getElementById('mis_chart'));
        chart.draw(data, options);
      }
}
////