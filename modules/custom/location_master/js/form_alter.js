jQuery(function ($) {	
	$("#edit-field-country").on('change', function() {
	  $.ajax({
		url: drupalSettings.path.baseUrl + 'get_locations_by_country/' + this.value,
		type: 'GET',
		error: function () {
			console.log("Error occurred");
		},
		success: function (response) {
			 if (response.status == 1) {
                if (response.data == "") {
                    
                } else {
                	var data = response.data;
                	var location_select = $('#edit-field-location');
					location_select.empty();

					
					
					location_select.html("<option value='_none'>- Select a value -</option>");
                	$.each(data, function(key, value) {   
				    location_select.append($("<option></option>")
				                    .attr("value",key)
				                    .text(value)); 
				        
				});

	                var state_select = $('#edit-field-business-state');
					state_select.empty();
					state_select.html("<option value='_none'>- Select a value -</option>");

					var district_select = $('#edit-field-district');
					district_select.empty();
					district_select.html("<option value='_none'>- Select a value -</option>");

					var town_select = $('#edit-field-town-city');
					town_select.empty();
					town_select.html("<option value='_none'>- Select a value -</option>");

                }
            }
		}
	});
	});
	
	$("#edit-field-location").on('change', function() {
	  $.ajax({
		url: drupalSettings.path.baseUrl + 'get_states_by_location/' + this.value,
		type: 'GET',
		error: function () {
			console.log("Error occurred");
		},
		success: function (response) {
			 if (response.status == 1) {
                if (response.data == "") {
                    
                } else {
                	var data = response.data;
                	var state_select = $('#edit-field-business-state');
					state_select.empty();
					
					state_select.html("<option value='_none'>- Select a value -</option>");
                	$.each(data, function(key, value) {   
				    state_select.append($("<option></option>")
				                    .attr("value",key)
				                    .text(value)); 
				        
				});

                	var district_select = $('#edit-field-district');
					district_select.empty();
					district_select.html("<option value='_none'>- Select a value -</option>");

					var town_select = $('#edit-field-town-city');
					town_select.empty();
					town_select.html("<option value='_none'>- Select a value -</option>");

                }
            }
		}
	});
	});
	
	
	$("#edit-field-business-state").on('change', function() {
	  $.ajax({
		url: drupalSettings.path.baseUrl + 'get_districts_by_state/' + this.value,
		type: 'GET',
		error: function () {
			console.log("Error occurred");
		},
		success: function (response) {
			 if (response.status == 1) {
                if (response.data == "") {
                    
                } else {
                	var data = response.data;
                	var district_select = $('#edit-field-district');
					district_select.empty();
					
					district_select.html("<option value='_none'>- Select a value -</option>");
                	$.each(data, function(key, value) {   
				    district_select.append($("<option></option>")
				                    .attr("value",key)
				                    .text(value)); 
				        
				});

                	var town_select = $('#edit-field-town-city');
					town_select.empty();
					town_select.html("<option value='_none'>- Select a value -</option>");

                }
            }
		}
	});
	});


	$("#edit-field-district").on('change', function() {
	  $.ajax({
		url: drupalSettings.path.baseUrl + 'get_towns_by_district/' + this.value,
		type: 'GET',
		error: function () {
			console.log("Error occurred");
		},
		success: function (response) {
			 if (response.status == 1) {
                if (response.data == "") {
                    
                } else {
                	var data = response.data;
                	var town_select = $('#edit-field-town-city');
					town_select.empty();
					
					town_select.html("<option value='_none'>- Select a value -</option>");
                	$.each(data, function(key, value) {   
				    town_select.append($("<option></option>")
				                    .attr("value",key)
				                    .text(value)); 
				        
				});

                }
            }
		}
	});
	});

	// For handling pop-up fields
	Drupal.behaviors.myBehavior = {
  	  attach: function (context, settings) {

  	  	//populate state dropdown based on location selection
  	  	jQuery(".form-item-field-country .form-select").on('change', function() {
		    $.ajax({
				url: drupalSettings.path.baseUrl + 'get_locations_by_country/' + this.value,
				type: 'GET',
				error: function () {
					console.log("Error occurred");
				},
				success: function (response) {
					 if (response.status == 1) {
		                if (response.data == "" || response.data == null) {
		                    var location_select = $('.form-item-field-location .form-select');
							location_select.empty();

							location_select.html("<option value='_none'>- Select a value -</option>");
		                } else {
		                	var data = response.data;
		                	var location_select = $('.form-item-field-location .form-select');
							location_select.empty();

							location_select.html("<option value='_none'>- Select a value -</option>");
		                	$.each(data, function(key, value) {   
						    location_select.append($("<option></option>")
						                    .attr("value",key)
						                    .text(value)); 
						        
						});

			                var state_select = $('.form-item-field-business-state .form-select');
							state_select.empty();
							state_select.html("<option value='_none'>- Select a value -</option>");

							var district_select = $('.form-item-field-district .form-select');
							district_select.empty();
							district_select.html("<option value='_none'>- Select a value -</option>");

							var town_select = $('.form-item-field-town-city .form-select');
							town_select.empty();
							town_select.html("<option value='_none'>- Select a value -</option>");

		                }
		            }
				}
			});
		});

  	  	//populate state dropdown based on location selection
  	  	jQuery(".form-item-field-location .form-select").on('change', function() {
  	  		var selectedStete =  $('.form-item-field-business-state .form-select').val();
		    $.ajax({
				url: drupalSettings.path.baseUrl + 'get_states_by_location/' + this.value,
				type: 'GET',
				error: function () {
					console.log("Error occurred");
				},
				success: function (response) {
					 if (response.status == 1) {
		                if (response.data == "" || response.data == null) {
		                	var state_select = $('.form-item-field-business-state .form-select');
							state_select.empty();
							
							state_select.html("<option value='_none'>- Select a value -</option>");    
		                } else {
		                	var data = response.data;
		                	var state_select = $('.form-item-field-business-state .form-select');
							state_select.empty();
							
							state_select.html("<option value='_none'>- Select a value -</option>");
		                	$.each(data, function(key, value) {
		                	if(key == selectedStete) {
		                		state_select.append($("<option></option>")
						                    .attr("value",key)
						                    .attr('selected', 'selected')
						                    .text(value)); 	
		                	} else { 
						    state_select.append($("<option></option>")
						                    .attr("value",key)
						                    .text(value)); 
						    }    
						});

		                	var district_select = $('.form-item-field-district .form-select');
							district_select.empty();
							district_select.html("<option value='_none'>- Select a value -</option>");

							var town_select = $('.form-item-field-town-city .form-select');
							town_select.empty();
							town_select.html("<option value='_none'>- Select a value -</option>");

		                }
		            }
				}
			});
		});

		jQuery('.form-item-field-location .form-select').trigger("change");	

  	  	//populate district dropdown based on state selection
  	  	jQuery(".form-item-field-business-state .form-select").on('change', function() {
		  $.ajax({
			url: drupalSettings.path.baseUrl + 'get_districts_by_state/' + this.value,
			type: 'GET',
			error: function () {
				console.log("Error occurred");
			},
			success: function (response) {
				 if (response.status == 1) {
	                if (response.data == "" || response.data == null) {
	                	var district_select = $('.form-item-field-district .form-select');
						district_select.empty();
						
						district_select.html("<option value='_none'>- Select a value -</option>");
	                	    
	                } else {
	                	var data = response.data;
	                	var district_select = $('.form-item-field-district .form-select');
						district_select.empty();
						
						district_select.html("<option value='_none'>- Select a value -</option>");
	                	$.each(data, function(key, value) {   
					    district_select.append($("<option></option>")
					                    .attr("value",key)
					                    .text(value)); 
					        
					});

	                	var town_select = $('.form-item-field-town-city .form-select');
						town_select.empty();
						town_select.html("<option value='_none'>- Select a value -</option>");

	                }
	            }
			}
		});
	}); 
  }
}

	$(document).delegate('.alphanumeric','keypress', function(e) {
	    var regex = /^[a-z\d\-_\s\b]+$/i;
	    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
	    if (regex.test(str)) {
	        return true;
	    }

	    e.preventDefault();
	    return false;
	});

	$(document).delegate('.email','keypress', function(e) {
	    var regex = /^[a-z\d\-_@\s\b]+$/i;
	    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
	    if (regex.test(str)) {
	        return true;
	    }

	    e.preventDefault();
	    return false;
	});

	$(document).delegate('.numeric-validation','keypress', function(e) {
	    var regex = /^-?\d*[.,\b]?\d*$/;
	    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
	    if (regex.test(str)) {
	        return true;
	    }

	    e.preventDefault();
	    return false;
	});

	$(document).delegate('.only-numeric-value','keypress', function(e) {
	    //var regex = /^\d+$/;
	    var regex = /^[0-9\b]*$/;
	    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
	    if (regex.test(str)) {
	        return true;
	    }

	    e.preventDefault();
	    return false;
	});

	// Removing whitespaces from begining and end 
	$(document).on('blur', "input[type=text]", function () { 
		$(this).val($(this).val().trim());
	});

	// Add Calender on textfield
	
});



