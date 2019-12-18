jQuery(function ($) {
	var type = jQuery('#edit-field-monthly-quarterly-type').val();
	var fiscalYr = jQuery('#edit-field-fiscal-year').val();
	var schoolCode = jQuery('#edit-field-mis-school-code').find(":selected").val();
	jQuery("#edit-field-monthly-quarterly-type").on('change', function() {
		type = this.value;
		jQuery('#edit-field-fiscal-year').val('');
		jQuery('#edit-field-monthly-value').val('');
		fiscalYr = jQuery('#edit-field-fiscal-year').val();
		schoolCode = jQuery('#edit-field-mis-school-code').find(":selected").val();
		if(schoolCode){
			monthly_quarterly_fiscalYr(type, schoolCode);
		} else {
			alert('Please select school code.');
		}
	});
	jQuery("#edit-field-fiscal-year").on('change', function() {
		jQuery('#edit-monthly-value').val('');
		fiscalYr = this.value;
		type = jQuery('#edit-field-monthly-quarterly-type').val();
		schoolCode = jQuery('#edit-field-mis-school-code').find(":selected").val();
		if(type == '') {
			alert('Please select MONTHLY/QUARTERLY Type.');
		} else {
			monthly_quarterly_value(type, fiscalYr, schoolCode);
		}
	});

	// monthly_quarterly_value(type, fiscalYr, schoolCode);
	
	jQuery("#edit-field-mis-state").on('change', function() {
		jQuery('#edit-field-mis-district').val('');
		jQuery('#edit-field-mis-block').val('');
		jQuery('#edit-field-mis-village').val('');
		jQuery('#edit-field-mis-school-code').val('');
		jQuery('#edit-field-monthly-quarterly-type').val('');
		jQuery('#edit-field-fiscal-year').val('');
		jQuery('#edit-field-monthly-value').val('');
		  jQuery.ajax({
			url: drupalSettings.path.baseUrl + 'get_silai_districts_by_state/' + this.value,
			type: 'GET',
			error: function () {
				console.log("Error occurred");
			},
			success: function (response) {
				 if (response.status == 1) {
	                if (response.data == "") {
	                    var location_select = $('#edit-field-mis-district');
						location_select.empty();
						location_select.html("<option value=''>- Select a value -</option>");
	                } else {
	                	var data = response.data;
	                	var location_select = jQuery('#edit-field-mis-district');
						location_select.empty();
						location_select.html("<option value=''>- Select a value -</option>");
	                	jQuery.each(data, function(key, value) {   
					    location_select.append(jQuery("<option></option>")
					                    .attr("value",key)
					                    .text(value)); 
					        
					});

	                }
	            }
			}
		});
	});

	jQuery("#edit-field-mis-district").on('change', function() {
		jQuery('#edit-field-mis-block').val('');
		jQuery('#edit-field-mis-village').val('');
		jQuery('#edit-field-mis-school-code').val('');
		jQuery('#edit-field-monthly-quarterly-type').val('');
		jQuery('#edit-field-fiscal-year').val('');
		jQuery('#edit-field-monthly-value').val('');
		  $.ajax({
			url: drupalSettings.path.baseUrl + 'get_block_by_district/' + this.value,
			type: 'GET',
			error: function () {
				console.log("Error occurred");
			},
			success: function (response) {
				 if (response.status == 1) {
	                if (response.data == "") {
	                    
	                } else {
	                	var data = response.data;
	                	var location_select = $('#edit-field-mis-block');
						location_select.empty();
						
						location_select.html("<option value=''>- Select a value -</option>");
	                	$.each(data, function(key, value) {   
					    location_select.append($("<option></option>")
					                    .attr("value",key.replace(/['"]+/g, ''))
					                    .text(value)); 
					        
					});

	                }
	            }
			}
		});
	});

	jQuery("#edit-field-mis-block").on('change', function(event) {
		jQuery('#edit-field-mis-village').val('');
		jQuery('#edit-field-mis-school-code').val('');
		jQuery('#edit-field-monthly-quarterly-type').val('');
		jQuery('#edit-field-fiscal-year').val('');
		jQuery('#edit-field-monthly-value').val('');
		$.ajax({
		url: drupalSettings.path.baseUrl + 'get_silai_villages_by_block/' + this.value,
		type: 'GET',
		error: function () {
			console.log("Error occurred");
		},
		success: function (response) {
			 if (response.status == 1) {
	            if (response.data == "" || response.data == null) {
	            	var location_select = $('#edit-field-mis-village');
					location_select.empty();
					location_select.html("<option value=''>- Select a value -</option>");
	                
	            } else {
	            	var data = response.data;
	            	var location_select = $('#edit-field-mis-village');
					location_select.empty();
					
					location_select.html("<option value=''>- Select a value -</option>");
	            	$.each(data, function(key, value) {   
				    location_select.append($("<option></option>")
				                    .attr("value",key)
				                    .text(value)); 
				        
				});

	            }
	        }
		}
		});
	});

	jQuery("#edit-field-mis-village").on('change', function(event) {
		jQuery('#edit-field-mis-school-code').val('');
		jQuery('#edit-field-monthly-quarterly-type').val('');
		jQuery('#edit-field-fiscal-year').val('');
		jQuery('#edit-field-monthly-value').val('');
		jQuery.ajax({
		url: drupalSettings.path.baseUrl + 'get_mis_school_code_by_village/' + this.value,
		type: 'GET',
		error: function () {
			console.log("Error occurred");
		},
		success: function (response) {
			 if (response.status == 1) {
	            if (response.data == "" || response.data == null) {
	            	var location_select = $('#edit-field-mis-school-code');
					location_select.empty();
					location_select.html("<option value=''>- Select a value -</option>");
	                
	            } else {
	            	var data = response.data;
	            	var location_select = $('#edit-field-mis-school-code');
					location_select.empty();
					
					location_select.html("<option value=''>- Select a value -</option>");
	            	jQuery.each(data, function(key, value) {   
				    location_select.append($("<option></option>")
				                    .attr("value",key)
				                    .text(value)); 
				        
				});

	            }
	        }
		}
		});
	});

	jQuery("#edit-field-mis-school-code").on('change', function(event) {
		jQuery('#edit-field-monthly-quarterly-type').val('');
		jQuery('#edit-field-fiscal-year').val('');
		jQuery('#edit-field-monthly-value').val('');
	});

	jQuery("#add-monthly-mis-form").submit(function( event ) {
		// var data = jQuery("#edit-field-mismachine_condition").val();
		// if(data == 0) {
		// 	jQuery("#edit-field-mismachine_condition").prop('required',true);
		// }
	});
	var data = jQuery("#edit-field-mismachine_condition").val();
	if(data == 0) {
		jQuery("#edit-machine-remark").prop('required',true);
	} else {
		jQuery("#edit-machine-remark").prop('required',false);
	}
	jQuery("#edit-field-mismachine_condition").on('change', function() {
		var data = jQuery("#edit-field-mismachine_condition").val();
		if(data == 0) {
			jQuery("#edit-machine-remark").prop('required',true);
		} else {
			jQuery("#edit-machine-remark").prop('required',false);
		}
	});	
	
});	
$(document).delegate('.mis-only-numeric-value','keypress', function(e) {
    //var regex = /^\d+$/;
    var regex = /^[0-9\b]*$/;
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(str)) {
        return true;
    }

    e.preventDefault();
    return false;
});

$(document).delegate('.mis-alphanumeric','keypress', function(e) {
    var regex = /^[a-z\d\-_\s\b]+$/i;
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(str)) {
        return true;
    }
    e.preventDefault();
    return false;
});

$(document).delegate('.mis-numeric-validation','keypress', function(e) {
    var regex = /^-?\d*[.,\b]?\d*$/;
    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
    if (regex.test(str)) {
        return true;
    }

    e.preventDefault();
    return false;
});

function monthly_mis_location() {
	var state = jQuery('select[id*="edit-field-mis-state"]').val();
	if(state) {
		jQuery.ajax({
			url: drupalSettings.path.baseUrl + 'get_silai_districts_by_state/' + state,
			type: 'GET',
			error: function () {
				console.log("Error occurred");
			},
			success: function (response) {
				 if (response.status == 1) {
	                if (response.data == "") {
	                    var location_select = $('#edit-field-mis-district');
						location_select.empty();
						location_select.html("<option value=''>- Select a value -</option>");
	                } else {
	                	var data = response.data;
	                	var location_select = jQuery('#edit-field-mis-district');
						location_select.empty();
						location_select.html("<option value=''>- Select a value -</option>");
	                	jQuery.each(data, function(key, value) {   
					    location_select.append(jQuery("<option></option>")
					                    .attr("value",key)
					                    .text(value)); 
					        
					});

	                }
	            }
			}
		});
	}
}

function monthly_quarterly_value(type, fiscalYr, schoolCode) {
	var misid= jQuery('#field-hidden-misid').val();
	jQuery.ajax({
		url: drupalSettings.path.baseUrl + 'get_monthly_quarter_value',
		type: 'POST',
		data: {fiscalYr:fiscalYr,type:type,schoolCode:schoolCode,misid:misid},
		error: function () {
			console.log("Error occurred");
		},
		success: function (response) {
		    if (response.status == "1") {
		        var data = response.data;
		    	var location_select = jQuery('#edit-field-monthly-value');
				location_select.empty();
		    	jQuery.each(data, function(key, value) {   
			    location_select.append(jQuery("<option></option>")
			                    .attr("value",key)
			                    .text(value)); 
				});                 
		    } else {
		    	var data = response.data;
		    	var location_select = jQuery('#edit-field-monthly-value');
				location_select.empty();
		    	jQuery.each(data, function(key, value) {   
			    location_select.append(jQuery("<option></option>")
			                    .attr("value",key)
			                    .text(value)); 
			        
				});
		    }
		}
	});
}

function monthly_quarterly_fiscalYr(type, schoolCode) {
	var misid = jQuery('#field-hidden-misid').val();
	jQuery.ajax({
		url: drupalSettings.path.baseUrl + 'get_monthly_quarter_fiscalYr',
		type: 'POST',
		data: {schoolCode:schoolCode, type:type, misid:misid},
		error: function () {
			console.log("Error occurred");
		},
		success: function (response) {
		    if (response.status == "1") {
		        var data = response.data;
		    	var location_select = jQuery('#edit-field-fiscal-year');
				location_select.empty();
		    	jQuery.each(data, function(key, value) {   
			    location_select.append(jQuery("<option></option>")
			                    .attr("value",key)
			                    .text(value)); 
				});                 
		    }
		}
	});
}	
