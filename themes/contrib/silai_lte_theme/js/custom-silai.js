jQuery(function ($) {
	$(document).ready(function(){

		var sideMenuHref = [];
		var uri = window.location.href.split('/');
		
		$('.sidebar-menu li').find('a').each(function() {
	    	if($(this).attr('href') == '/'+ uri[3]) {
	    		$(this).closest('li').addClass('active');
	    	}

	    	if(jQuery.inArray($(this).attr('href').replace('/', ''), sideMenuHref) === -1) {
	    		sideMenuHref.push($(this).attr('href').replace('/', ''));
	    	}
		});

		
		var masterMgmtHref = [];
		var uri = window.location.href.split('/');
		$('ul.sidebar-menu li:nth-child(2)').find('a').each(function() {
	    	if($(this).attr('href') == '/'+ uri[3]) {
	    		$(this).closest('li').addClass('active');
	    	}

	    	if(jQuery.inArray($(this).attr('href').replace('/', ''), masterMgmtHref) === -1) {
	    		masterMgmtHref.push($(this).attr('href').replace('/', ''));
	    	}
		});

		var locationHrefArray = [];
		var uri = window.location.href.split('/');
		$('.sidebar-menu > li.treeview > ul.treeview-menu > li.treeview:nth-child(3)').find('a').each(function() {
			
	    	if($(this).attr('href') == '/'+ uri[3]) {
	    		$(this).closest('li').addClass('active');
	    	}

	    	if(jQuery.inArray($(this).attr('href').replace('/', ''), locationHrefArray) === -1) {
	    		locationHrefArray.push($(this).attr('href').replace('/', ''));
	    	}
		});


		var inventoryHrefArray = [];
		var uri = window.location.href.split('/');
		$('.sidebar-menu > li.treeview > ul.treeview-menu > li.treeview:nth-child(5)').find('a').each(function() {
			
	    	if($(this).attr('href') == '/'+ uri[3]) {
	    		$(this).closest('li').addClass('active');
	    	}

	    	if(jQuery.inArray($(this).attr('href').replace('/', ''), inventoryHrefArray) === -1) {
	    		inventoryHrefArray.push($(this).attr('href').replace('/', ''));
	    	}
		});

		var schoolMgmtHref = [];
		var uri = window.location.href.split('/');
		$('ul.sidebar-menu > li:nth-child(3)').find('a').each(function() {
	    	if($(this).attr('href') == '/'+ uri[3]) {
	    		$(this).closest('li').addClass('active');
	    	}

	    	if(jQuery.inArray($(this).attr('href').replace('/', ''), schoolMgmtHref) === -1) {
	    		schoolMgmtHref.push($(this).attr('href').replace('/', ''));
	    	}
		});

		var paymentMgmtHref = [];
		var uri = window.location.href.split('/');
		$('ul.sidebar-menu > li:nth-child(5)').find('a').each(function() {
	    	if($(this).attr('href') == '/'+ uri[3]) {
	    		$(this).closest('li').addClass('active');
	    	}

	    	if(jQuery.inArray($(this).attr('href').replace('/', ''), paymentMgmtHref) === -1) {
	    		paymentMgmtHref.push($(this).attr('href').replace('/', ''));
	    	}
		});

		var misMgmtHref = [];
		var uri = window.location.href.split('/');
		$('ul.sidebar-menu > li:nth-child(6)').find('a').each(function() {
	    	if($(this).attr('href') == '/'+ uri[3]) {
	    		$(this).closest('li').addClass('active');
	    	}

	    	if(jQuery.inArray($(this).attr('href').replace('/', ''), misMgmtHref) === -1) {
	    		misMgmtHref.push($(this).attr('href').replace('/', ''));
	    	}
		});

		var utilityMgmtHref = [];
		var uri = window.location.href.split('/');
		$('ul.sidebar-menu > li:nth-child(7)').find('a').each(function() {
	    	if($(this).attr('href') == '/'+ uri[3]) {
	    		$(this).closest('li').addClass('active');
	    	}

	    	if(jQuery.inArray($(this).attr('href').replace('/', ''), utilityMgmtHref) === -1) {
	    		utilityMgmtHref.push($(this).attr('href').replace('/', ''));
	    	}
		});
		

		var systemHref = [];
		var uri = window.location.href.split('/');
		$('ul.sidebar-menu > li:nth-child(8)').find('a').each(function() {
	    	if($(this).attr('href') == '/'+ uri[3]) {
	    		$(this).closest('li').addClass('active');
	    	}

	    	if(jQuery.inArray($(this).attr('href').replace('/', ''), systemHref) === -1) {
	    		systemHref.push($(this).attr('href').replace('/', ''));
	    	}
		});


		
		
		if(jQuery.inArray(uri[3], masterMgmtHref) !== -1) {
			$('ul.sidebar-menu > li.treeview:nth-child(2)').addClass('active');
			$('ul.sidebar-menu li.treeview ul.treeview-menu li.treeview').removeClass('active');
			$('ul.sidebar-menu > li:nth-child(2)').siblings().removeClass('active');
			// $('ul.sidebar-menu li.treeview').addClass('active');
			// $('ul.sidebar-menu li.treeview ul.treeview-menu li.treeview').removeClass('active');	
		}

		if(jQuery.inArray(uri[3], schoolMgmtHref) !== -1) {
			
			$('ul.sidebar-menu > li.treeview:nth-child(3)').addClass('active');
			$('ul.sidebar-menu li.treeview ul.treeview-menu li.treeview').removeClass('active');
			$('ul.sidebar-menu > li.treeview:nth-child(3)').siblings().removeClass('active');
			
		}

		if(jQuery.inArray(uri[3], paymentMgmtHref) !== -1) {
			$('ul.sidebar-menu > li.treeview:nth-child(5)').addClass('active');
			$('ul.sidebar-menu li.treeview ul.treeview-menu li.treeview').removeClass('active');
			$('ul.sidebar-menu > li.treeview:nth-child(5)').siblings().removeClass('active');
			// $('ul.sidebar-menu li.treeview').addClass('active');
			// $('ul.sidebar-menu li.treeview ul.treeview-menu li.treeview').removeClass('active');	
		}

		if(jQuery.inArray(uri[3], misMgmtHref) !== -1) {
			$('ul.sidebar-menu > li.treeview:nth-child(6)').addClass('active');
			$('ul.sidebar-menu li.treeview ul.treeview-menu li.treeview').removeClass('active');
			$('ul.sidebar-menu > li.treeview:nth-child(6)').siblings().removeClass('active');
			// $('ul.sidebar-menu li.treeview').addClass('active');
			// $('ul.sidebar-menu li.treeview ul.treeview-menu li.treeview').removeClass('active');	
		}

		if(jQuery.inArray(uri[3], utilityMgmtHref) !== -1) {
			$('ul.sidebar-menu > li.treeview:nth-child(7)').addClass('active');
			$('ul.sidebar-menu li.treeview ul.treeview-menu li.treeview').removeClass('active');
			$('ul.sidebar-menu > li.treeview:nth-child(7)').siblings().removeClass('active');
			// $('ul.sidebar-menu li.treeview').addClass('active');
			// $('ul.sidebar-menu li.treeview ul.treeview-menu li.treeview').removeClass('active');	
		}

		if(jQuery.inArray(uri[3], systemHref) !== -1) {
			$('ul.sidebar-menu > li.treeview:nth-child(8)').addClass('active');
			$('ul.sidebar-menu li.treeview ul.treeview-menu li.treeview').removeClass('active');
			$('ul.sidebar-menu > li.treeview:nth-child(8)').siblings().removeClass('active');
			// $('ul.sidebar-menu li.treeview').addClass('active');
			// $('ul.sidebar-menu li.treeview ul.treeview-menu li.treeview').removeClass('active');	
		}

		

		
		
		if(jQuery.inArray(uri[3], locationHrefArray) !== -1) {
			$('ul.sidebar-menu li.treeview').addClass('active')
			$('ul.sidebar-menu li.treeview').parent().parent().siblings().removeClass('active');
			$('ul.sidebar-menu  li.treeview   ul.treeview-menu  li.treeview:not(:nth-child(3))').removeClass('active');
		}
		
		console.log(misMgmtHref);

		if(jQuery.inArray(uri[3], inventoryHrefArray) !== -1) {
			$('ul.sidebar-menu li.treeview').addClass('active')
			$('ul.sidebar-menu li.treeview').parent().parent().siblings().removeClass('active');
			$('ul.sidebar-menu  li.treeview   ul.treeview-menu  li.treeview:not(:nth-child(5))').removeClass('active');
		}
		// Add teacher form repater field - Start
		var i=1;
		jQuery("#before_add_household_row").click(function(){ 
		jQuery('#before_household_row_'+i).html('<div class="js-form-item form-item js-form-type-select form-type-select js-form-item-before-silai-school-household form-item-before-silai-school-household form-group"><label for="edit-before-silai-school-household">Before opening of Silai school household core</label><select class="before_silai_school_household form-select required form-control" data-drupal-selector="edit-before-silai-school-household" id="edit-before-silai-school-household" name="before_silai_school_household[]" aria-required="true"><option value="">- Select a value -</option><option value="1">Sweeping and cleaning</option><option value="2">Washing dishes</option><option value="3">Feeding pets</option><option value="4">Doing laundry</option><option value="5">Preparing meals</option><option value="6">Look after parents/husband</option><option value="7">Children caring</option><option value="8">Any other</option></select></div>');

			jQuery('#before_household_section').append('<div id="before_household_row_'+(i+1)+'"></div>');
		  i++; 
		});
		jQuery("#before_delete_household_row").click(function(){
		if(i>1){
			jQuery("#before_household_row_"+(i-1)).html('');
				i--;
			}
		});
		//
		var i=1;
		jQuery("#after_add_household_row").click(function(){ 
		jQuery('#after_household_row_'+i).html('<div class="js-form-item form-item js-form-type-select form-type-select js-form-item-after-silai-school-household form-item-after-silai-school-household form-group"><label for="edit-after-silai-school-household">After opening of Silai school household core</label><select class="after_silai_school_household form-select required form-control" data-drupal-selector="edit-after-silai-school-household" id="edit-after-silai-school-household" name="after_silai_school_household[]" aria-required="true"><option value="">- Select a value -</option><option value="1">Sweeping and cleaning</option><option value="2">Washing dishes</option><option value="3">Feeding pets</option><option value="4">Doing laundry</option><option value="5">Preparing meals</option><option value="6">Look after parents/husband</option><option value="7">Children caring</option><option value="8">Any other</option></select></div>');
			var addv = i+1;
			jQuery('#after_household_section').append('<div id="after_household_row_'+addv+'"></div>');
		  i++; 
		});
		jQuery("#after_delete_household_row").click(function(){
		if(i>1){
			jQuery("#after_household_row_"+(i-1)).html('');
				i--;
			}
		});
		// for add more children
		

	jQuery("#add_children_row").click(function(){
		var child_count = jQuery( ".children_name" ).length;
		jQuery('#children_row').append('<div id="new_child_row'+(child_count)+'"></div>');
		jQuery('#children_row #new_child_row'+child_count).html('<div class="children_row_data"><div class="js-form-item form-item js-form-type-textfield form-type-textfield js-form-item-children-details-'+(child_count)+'-children-name-'+(child_count)+' form-item-children-details-'+(child_count)+'-children-name-'+(child_count)+' form-group"><label for="edit-children-details-'+(child_count)+'-children-name-'+(child_count)+'">Children Name</label><input class="children_name form-text required form-control" data-drupal-selector="edit-children-details-'+(child_count)+'-children-name-'+(child_count)+'" type="text" id="edit-children-details-'+(child_count)+'-children-name-'+(child_count)+'" name="children_details['+(child_count)+'][children_name]['+(child_count)+']" value="" size="60" maxlength="128" aria-required="true"></div><div class="js-form-item form-item js-form-type-select form-type-select js-form-item-children-details-'+(child_count)+'-children-gender-'+(child_count)+' form-item-children-details-'+(child_count)+'-children-gender-'+(child_count)+' form-group"><label for="edit-children-details-0-children-gender-0">Children Gender</label><select class="children_gender form-select required form-control" data-drupal-selector="edit-children-details-'+(child_count)+'-children-gender-'+(child_count)+'" id="edit-children-details-'+(child_count)+'-children-gender-'+(child_count)+'" name="children_details['+(child_count)+'][children_gender]['+(child_count)+']" aria-required="true"><option value="" selected="selected">- Select -</option><option value="1">Male</option><option value="2">Female</option></select></div><div class="js-form-item form-item js-form-type-textfield form-type-textfield js-form-item-children-details-'+(child_count)+'-children-age-'+(child_count)+' form-item-children-details-'+(child_count)+'-children-age-'+(child_count)+' form-group"><label for="edit-children-details-'+(child_count)+'-children-age-'+(child_count)+'">Children Age</label><input class="only-numeric-value form-text required form-control" data-drupal-selector="edit-children-details-'+(child_count)+'-children-age-'+(child_count)+'" type="text" id="edit-children-details-'+(child_count)+'-children-age-'+(child_count)+'" name="children_details['+(child_count)+'][children_age]['+(child_count)+']" value="" size="60" maxlength="128"  aria-required="true"></div><div class="js-form-item form-item js-form-type-select form-type-select js-form-item-children-details-'+(child_count)+'-children-education-level-'+(child_count)+' form-item-children-details-'+(child_count)+'-children-education-level-'+(child_count)+' form-group"><label for="edit-children-details-'+(child_count)+'-children-education-level-'+(child_count)+'">Children Education Level</label><select class="children_education_level form-select required form-control" data-drupal-selector="edit-children-details-'+(child_count)+'-children-education-level-'+(child_count)+'" id="edit-children-details-'+(child_count)+'-children-education-level-'+(child_count)+'" name="children_details['+(child_count)+'][children_education_level]['+(child_count)+']"  aria-required="true"><option value="" selected="selected">- Select -</option><option value="1">Not enrolled</option><option value="2">Pre-primary/ Nursery</option><option value="3">Literate/ Class I</option><option value="4">Class 2</option><option value="5">Class 3</option><option value="6">Class 4</option><option value="7">Class 5</option><option value="8">Class 6</option><option value="9">Class 7</option><option value="10">Class 8</option><option value="11">Class 9</option><option value="12">Class 10</option><option value="13">Class 11</option><option value="14">Class 12</option><option value="15">Class 12 pass or (Intermediate.)</option><option value="16">Graduation (Not completed)</option><option value="17">Graduation (completed)</option><option value="18">Post-graduation (not completed)</option><option value="19">Post-graduation (completed)</option><option value="20">Technical/professionals Qual.</option><option value="21">Any other technical/vocational course after a degree</option></select></div><fieldset class="children_school_type fieldgroup form-composite required js-form-item form-item js-form-wrapper form-wrapper" data-drupal-selector="edit-children-details-'+(child_count)+'-children-school-type-'+(child_count)+'" id="edit-children-details-'+(child_count)+'-children-school-type-'+(child_count)+'--wrapper" aria-required="true"><legend><span class="fieldset-legend">School/College type</span></legend><div class="fieldset-wrapper"><div id="edit-children-details-'+(child_count)+'-children-school-type-'+(child_count)+'" class="form-radios"><div class="js-form-item form-item js-form-type-radio form-type-radio js-form-item-children-details-'+(child_count)+'-children-school-type-'+(child_count)+' form-item-children-details-'+(child_count)+'-children-school-type-'+(child_count)+' form-group"><input class="children_school_type form-radio" data-drupal-selector="edit-children-details-'+(child_count)+'-children-school-type-'+(child_count)+'-0" type="radio" id="edit-children-details-'+(child_count)+'-children-school-type-'+(child_count)+'-'+(child_count)+'" name="children_details['+(child_count)+'][children_school_type]['+(child_count)+']" value="0"><label for="edit-children-details-'+(child_count)+'-children-school-type-'+(child_count)+'-0" class="option">Government</label></div><div class="js-form-item form-item js-form-type-radio form-type-radio js-form-item-children-details-'+(child_count)+'-children-school-type-'+(child_count)+' form-item-children-details-'+(child_count)+'-children-school-type-'+(child_count)+' form-group"><input class="children_school_type form-radio" data-drupal-selector="edit-children-details-'+(child_count)+'-children-school-type-'+(child_count)+'-1" type="radio" id="edit-children-details-'+(child_count)+'-children-school-type-'+(child_count)+'-1" name="children_details['+(child_count)+'][children_school_type]['+(child_count)+']" value="1"><label for="edit-children-details-'+(child_count)+'-children-school-type-'+(child_count)+'-1" class="option">Private</label></div></div></div></fieldset><div class="js-form-item form-item js-form-type-textfield form-type-textfield js-form-item-children-details-'+(child_count)+'-children-monthly-expense-over-education-'+(child_count)+' form-item-children-details-'+(child_count)+'-children-monthly-expense-over-education-'+(child_count)+' form-group"><label for="edit-children-details-'+(child_count)+'-children-monthly-expense-over-education-'+(child_count)+'">Monthly expense over education per month</label><input class="only-numeric-value form-text required form-control" data-drupal-selector="edit-children-details-'+(child_count)+'-children-monthly-expense-over-education-'+(child_count)+'" type="text" id="edit-children-details-'+(child_count)+'-children-monthly-expense-over-education-'+(child_count)+'" name="children_details['+(child_count)+'][children_monthly_expense_over_education]['+(child_count)+']" value="" size="60" maxlength="128"  aria-required="true"></div></div>');
	});
	jQuery("#delete_children_row").click(function(){
		var child_count = jQuery( ".children_name" ).length;
		if(child_count > 1){
			var check = child_count-1;
			jQuery('#new_child_row'+check).remove('');
		}
	});


	});
	// Side Menu selection code end

	$("#edit-field-silai-country").on('change', function() {
	    $.ajax({
			url: drupalSettings.path.baseUrl + 'get_silai_locations_by_country/' + this.value,
			type: 'GET',
			error: function () {
				console.log("Error occurred");
			},
			success: function (response) {
				 if (response.status == 1) {
	                if (response.data == "") {
	                    
	                } else {
	                	var data = response.data;
	                	var location_select = $('#edit-field-silai-location');
						location_select.empty();
						
						location_select.html("<option value='_none'>- Select a value -</option>");
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
	
	

	$("#edit-field-silai-location").on('change', function() {

	  $.ajax({
		url: drupalSettings.path.baseUrl + 'get_silai_states_by_location/' + this.value,
		type: 'GET',
		error: function () {
			console.log("Error occurred");
		},
		success: function (response) {
			 if (response.status == 1) {
                if (response.data == "") {
                    
                } else {
                	var data = response.data;
                	var location_select = $('#edit-field-silai-business-state');
					location_select.empty();
					
					location_select.html("<option value='_none'>- Select a value -</option>");
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
	
	
	$("#edit-field-silai-business-state").on('change', function() {
		$.ajax({
			url: drupalSettings.path.baseUrl + 'get_silai_districts_by_state/' + this.value,
			type: 'GET',
			error: function () {
				console.log("Error occurred");
			},
			success: function (response) {
				 if (response.status == 1) {
			        if (response.data == "") {
			            
			        } else {
			        	var data = response.data;
			        	var location_select = $('#edit-field-silai-district');
						location_select.empty();
						location_select.html("<option value='_none'>- Select a value -</option>");
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


	$("#edit-field-silai-district").on('change', function() {
		var selectedTown =  $('.form-item-field-silai-town .form-select').val();
	  $.ajax({
		url: drupalSettings.path.baseUrl + 'get_silai_towns_by_district/' + this.value,
		type: 'GET',
		error: function () {
			console.log("Error occurred");
		},
		success: function (response) {
			 if (response.status == 1) {
                if (response.data == "") {
                    
                } else {
                	var data = response.data;
                	var location_select = $('#edit-field-silai-town');
					location_select.empty();
					
					location_select.html("<option value='_none'>- Select a value -</option>");
                	$.each(data, function(key, value) {
                	if( selectedTown && key == selectedTown) {
								location_select.append($("<option></option>")
							                .attr("value",key)
							                .attr('selected', 'selected')
							                .text(value)); 	
							} else { 
								location_select.append($("<option></option>")
							                .attr("value",key)
							                .text(value)); 
							}   
				    // location_select.append($("<option></option>")
				    //                 .attr("value",key)
				    //                 .text(value)); 
				        
				});

                }
            }
		}
	});
	});

	$("#edit-field-silai-block").on('change', function(event) {
		var selectedVill =  $('#edit-field-silai-village').val(); 
		$.ajax({
			url: drupalSettings.path.baseUrl + 'get_silai_villages_by_block/' + this.value,
			type: 'GET',
			error: function () {
			console.log("Error occurred");
			},
			success: function (response) {
				if (response.status == 1) {
					if (response.data == "" || response.data == null) {
						var location_select = $('#edit-field-silai-village');
						location_select.empty();
						location_select.html("<option value='_none'>- Select a value -</option>");
					    
					} else {
						var data = response.data;
						var location_select = $('#edit-field-silai-village');
						location_select.empty();
						
						location_select.html("<option value='_none'>- Select a value -</option>");
						$.each(data, function(key, value) {   
							if(key == selectedVill) {
								location_select.append($("<option></option>")
							                .attr("value",key)
							                .attr('selected', 'selected')
							                .text(value)); 	
							} else { 
								location_select.append($("<option></option>")
							                .attr("value",key)
							                .text(value)); 
							}					        
						});
					}
				}
			}
		});
	});

	
	//delete user using confirm box
	$('i.delete-user').on('click', function(){
	    userId = $(this).attr("data-id");
		$.confirm({
		    title: 'Delete user?',
		    content: 'Are you sure to delete User?',
		    autoClose: 'cancel|8000',
		    buttons: {
		        deleteUser: {
		            text: 'delete user',
		            action: function () {
		            	jQuery.ajax({
							url: drupalSettings.path.baseUrl + 'delete_user',
							type: 'POST',
							data: {userId:userId},
							async : true,
							dataType : "json",
							success: function (response) {
								if (response.status == 1) {
									//console.log('Data deleted');
									window.location.reload();
								}
							}							
					    });					    
		            }
		        },
		        cancel: function () {
		        }
		    }
		});    	
	});	
	
	//delete message using confirm box
	$('i.delete-message').on('click', function(){
	    id = $(this).attr("data-id");
		$.confirm({
		    title: 'Delete Message?',
		    content: 'Are you sure to delete Message?',
		    autoClose: 'cancel|8000',
		    buttons: {
		        deleteUser: {
		            text: 'delete message',
		            action: function () {
		            	jQuery.ajax({
							url: drupalSettings.path.baseUrl + 'delete_message',
							type: 'POST',
							data: {id:id},
							async : true,
							dataType : "json",
							success: function (response) {
								if (response.status == 1) {
									//console.log('Data deleted');
									window.location.reload();
								}
							}							
					    });					    
		            }
		        },
		        cancel: function () {
		        }
		    }
		});    	
	});


	//delete user using confirm box
	$('i.delete-school-mis-data').on('click', function(){
	    id = $(this).attr("data-id");
		$.confirm({
		    title: 'Delete School Mis Data?',
		    content: 'Are you sure to delete?',
		    autoClose: 'cancel|8000',
		    buttons: {
		        deleteUser: {
		            text: 'delete mis data',
		            action: function () {
		            	jQuery.ajax({
							url: drupalSettings.path.baseUrl + 'delete_school_mis_data',
							type: 'POST',
							data: {id:id},
							async : true,
							dataType : "json",
							success: function (response) {
								if (response.status == 1) {
									//console.log('Data deleted');
									window.location.reload();
								}
							}							
					    });					    
		            }
		        },
		        cancel: function () {
		        }
		    }
		});    	
	});	


	//Approve user using confirm box
	$('i.confirmation').on('click', function(){
	    schoolId = $(this).attr("data-id");
		$.confirm({
		    title: 'Approve school?',
		    content: 'Are you sure to Approve School?',
		    autoClose: 'cancel|8000',
		    buttons: {
		        approveSchool: {
		            text: 'approve school',
		            action: function () {
		            	jQuery.ajax({
							url: drupalSettings.path.baseUrl + 'approve_school',
							type: 'POST',
							data: {schoolId:schoolId},
							async : true,
							dataType : "json",
							success: function (response) {
								if (response.status == 1) {
									//console.log('Data deleted');
									window.location.reload();
								}
							}							
					    });					    
		            }
		        },
		        rejectSchool: {
		            text: 'Reject School',
		            action: function () {
		            	jQuery.ajax({
							url: drupalSettings.path.baseUrl + 'reject_school',
							type: 'POST',
							data: {schoolId:schoolId},
							async : true,
							dataType : "json",
							success: function (response) {
								if (response.status == 1) {
									//console.log('Data deleted');
									window.location.reload();
								}
							}							
					    });					    
		            }
		        },
		        cancel: function () {
		        }
		    }
		});    	
	}); 

	//adding class for mandatory sign
	jQuery("#edit-field-school-type").on('change', function() {
		const setelliteSchoolType = 14338;
		if($(this).val() == setelliteSchoolType){
			$('label[for="edit-field-silai-learner-id-0-value"]').addClass('form-required');
		}
	});


	//delete Weekly Mis using confirm box
	$('i.delete-weekly-mis').on('click', function(){
	    id = $(this).attr("data-id");
		$.confirm({
		    title: 'Delete Weekly MIS?',
		    content: 'Are you sure to delete Weekly MIS?',
		    autoClose: 'cancel|8000',
		    buttons: {
		        deleteWeeklyMIS: {
		            text: 'Delete',
		            action: function () {
		            	jQuery.ajax({
							url: drupalSettings.path.baseUrl + 'delete-weekly-mis',
							type: 'POST',
							data: {id:id},
							async : true,
							dataType : "json",
							success: function (response) {
								if (response.status == 1) {
									//console.log('Data deleted');
									window.location.reload();
								}
							}							
					    });					    
		            }
		        },
		        cancel: function () {
		        }
		    }
		});    	
	});

	//delete Monthly Mis using confirm box
	$('i.delete-monthly-mis').on('click', function(){
	    id = $(this).attr("data-id");
		$.confirm({
		    title: 'Delete Monthly/Quarterly MIS?',
		    content: 'Are you sure to delete MIS?',
		    autoClose: 'cancel|8000',
		    buttons: {
		        deleteWeeklyMIS: {
		            text: 'Delete',
		            action: function () {
		            	jQuery.ajax({
							url: drupalSettings.path.baseUrl + 'delete-monthly-mis',
							type: 'POST',
							data: {id:id},
							async : true,
							dataType : "json",
							success: function (response) {
								if (response.status == 1) {
									//console.log('Data deleted');
									window.location.reload();
								}
							}							
					    });					    
		            }
		        },
		        cancel: function () {
		        }
		    }
		});    	
	});

	//delete user using confirm box
	$('i.delete-silai-notification').on('click', function(){
	    notificationId = $(this).attr("data-id");
	    console.log(notificationId);
		$.confirm({
		    title: 'Delete notification?',
		    content: 'Are you sure to delete this notification?',
		    autoClose: 'cancel|8000',
		    buttons: {
		        deleteNotification: {
		            text: 'delete',
		            action: function () {
		            	jQuery.ajax({
							url: drupalSettings.path.baseUrl + 'delete_silai_notification',
							type: 'POST',
							data: {notificationId:notificationId},
							async : true,
							dataType : "json",
							success: function (response) {
								if (response.status == 1) {
									//console.log('Data deleted');
									window.location.reload();
								}
							}							
					    });					    
		            }
		        },
		        cancel: function () {
		        }
		    }
		});    	
	});	

	jQuery("#edit-field-silai-district").on('change', function() {
		var selectedBlock =  $('.form-item-field-silai-block .form-select').val(); 
		$.ajax({
			url: drupalSettings.path.baseUrl + 'get_block_by_district/' + this.value,
			type: 'GET',
			error: function () {
				console.log("Error occurred");
			},
			success: function (response) {
				 if (response.status == 1) {
			        if (response.data == "" || response.data == null) {
			            $('#edit-field-silai-block').empty();
			            $('#edit-field-silai-block').html("<option value='_none'>- Select a value -</option>");
			        } else {
			        	var data = response.data;
			        	var location_select = $('#edit-field-silai-block');
						location_select.empty();
						
						location_select.html("<option value='_none'>- Select a value -</option>");
			        	$.each(data, function(key, value) {   
							if(key.replace(/['"]+/g, '') == selectedBlock) {
								location_select.append($("<option></option>")
							                .attr("value",key.replace(/['"]+/g, ''))
							                .attr('selected', 'selected')
							                .text(value)); 	
							} else { 
								location_select.append($("<option></option>")
							                .attr("value",key.replace(/['"]+/g, ''))
							                .text(value)); 
							}					        
						});	     
			        }
			    }
			}
		});
	});


	$("#edit-field-silai-school-0-target-id").on('autocompletechange', function(event) {
		var schoolCodeValue = this.value;
		var schoolCodeData = schoolCodeValue.split('(');
		var id = schoolCodeData[1].replace(')', '');
		 
		$.ajax({
			url: drupalSettings.path.baseUrl + 'get_silai_school_by_id/' + id,
			type: 'GET',
			error: function () {
				console.log("Error occurred");
			},
			success: function (response) {

				 if (response.status == 1) {
				 	if (response.data == "") {
	                    
	                } else {
	                	$('#edit-field-silai-school-name-0-value').val('');
	                	var data = response.data;
	                	$('#edit-field-silai-school-name-0-value').val(data.schoolName);
	                }
	            }
			}
		});
	  
	});



	$("#edit-field-training-school-code-0-target-id").on('blur', function(event) {
		
		if(this.value) {
			var schoolCodeValue = this.value;
			var schoolCodeData = schoolCodeValue.split('(');
			var id = schoolCodeData[1].replace(')', '');
		$.ajax({
			url: drupalSettings.path.baseUrl + 'get_silai_school_by_id/' + id,
			type: 'GET',
			error: function () {
				console.log("Error occurred");
			},
			success: function (response) {

				 if (response.status == 1) {
				 	if (response.data == "") {
				 		$('#edit-title-0-value').val('');
	                	$('#edit-field-silai-contact-number-0-value').val('');
	                	$('#edit-field-silai-email-id-0-value').val('');

	                	$('#edit-title-0-value').removeAttr('readonly', true);
	                	$('#edit-field-silai-contact-number-0-value').removeAttr('readonly', true);
	                	$('#edit-field-silai-email-id-0-value').removeAttr('readonly', true);
	                    
	                } else {
	                	$('#edit-title-0-value').val('');
	                	$('#edit-field-silai-contact-number-0-value').val('');
	                	$('#edit-field-silai-email-id-0-value').val('');
	                	var data = response.data;
	                	$('#edit-title-0-value').val(data.schoolAdminName);
	                	$('#edit-field-silai-contact-number-0-value').val(data.schoolAdminContactNo);
	                	$('#edit-field-silai-email-id-0-value').val(data.schoolAdminEmailId);

	                	$('#edit-title-0-value').attr('readonly', true);
	                	$('#edit-field-silai-contact-number-0-value').attr('readonly', true);
	                	$('#edit-field-silai-email-id-0-value').attr('readonly', true);
	                }
	            }
			}
		});
	} else {
		$('#edit-title-0-value').val('');
    	$('#edit-field-silai-contact-number-0-value').val('');
    	$('#edit-field-silai-email-id-0-value').val('');

    	$('#edit-title-0-value').removeAttr('readonly', true);
    	$('#edit-field-silai-contact-number-0-value').removeAttr('readonly', true);
    	$('#edit-field-silai-email-id-0-value').removeAttr('readonly', true);
	}
	  
	});

	// $("#edit-field-training-school-code-0-target-id").on('blur', function(){
	// 	$("#edit-field-training-school-code-0-target-id").trigger('autocompletechange');	
	// });

	if($('#field_hidden_learner_id').val()) {
		$("#edit-field-silai-school-0-target-id").trigger('autocompletechange');
	}
	jQuery('#edit-field-silai-district').trigger("change");
	jQuery('#edit-field-silai-block').trigger("change");
 	
});

// handling pop up fields
Drupal.behaviors.myBehavior2 = {
  attach: function (context, settings) {
  	$(".form-item-field-silai-state-code-0-value .form-control").bind('keyup', function (e) {
    if (e.which >= 97 && e.which <= 122) {
        var newKey = e.which - 32;
        // I have tried setting those
        e.keyCode = newKey;
        e.charCode = newKey;
    }
    $(".form-item-field-silai-state-code-0-value .form-control").val(($(".form-item-field-silai-state-code-0-value .form-control").val()).toUpperCase());
});
  	$('.form-item-field-agreement-amount-0-value input, .form-item-field-sactioned-amount-0-value input').bind("cut copy paste", function(e) {
        e.preventDefault();
        alert("You cannot paste into this field.");
    });
	var districtId = $("#village-hidden-district-id").val();
	if(districtId) {
		var selectedBlock = $('.form-item-field-silai-block .form-select').val();
		$.ajax({
			url: drupalSettings.path.baseUrl + 'get_block_by_district/' + districtId,
			type: 'GET',
			error: function () {
				console.log("Error occurred");
			},
			success: function (response) {
				 if (response.status == 1) {
			        if (response.data == "" || response.data == null) {
			            $('.form-item-field-silai-block .form-select').empty();
			            $('.form-item-field-silai-block .form-select').html("<option value='_none'>- Select a value -</option>");
			        } else {
			        	var data = response.data;
			        	var location_select = $('.form-item-field-silai-block .form-select');
						location_select.empty();
						
						location_select.html("<option value='_none'>- Select a value -</option>");
			        	$.each(data, function(key, value) {   
							if(key.replace(/['"]+/g, '') == selectedBlock) {
								location_select.append($("<option></option>")
							                .attr("value",key.replace(/['"]+/g, ''))
							                .attr('selected', 'selected')
							                .text(value)); 	
							} else { 
								location_select.append($("<option></option>")
							                .attr("value",key.replace(/['"]+/g, ''))
							                .text(value)); 
							}				      
						});	         
			        }
			    }
			}
		});		
	}

  	jQuery(".form-item-field-silai-district-0-target-id .form-autocomplete").on('autocompletechange', function(event) {
		var id = this.value.replace ( /[^\d.]/g, '' );
		$.ajax({
			url: drupalSettings.path.baseUrl + 'get_block_by_district/' + id,
			type: 'GET',
			error: function () {
				console.log("Error occurred");
			},
			success: function (response) {
				 if (response.status == 1) {
			        if (response.data == "" || response.data == null) {
			        	$('.form-item-field-silai-block .form-select').empty();
			            $('.form-item-field-silai-block .form-select').html("<option value='_none'>- Select a value -</option>");
			            
			        } else {
			        	var data = response.data;
			        	var location_select = $('.form-item-field-silai-block .form-select');
						location_select.empty();
						
						location_select.html("<option value='_none'>- Select a value -</option>");
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

	$(".form-item-field-silai-district-0-target-id .form-autocomplete").on('autocompletechange', function(event) {
		var id = this.value.replace ( /[^\d.]/g, '' );
		$.ajax({
		url: drupalSettings.path.baseUrl + 'get_silai_towns_by_district/' + id,
		type: 'GET',
		error: function () {
			console.log("Error occurred");
		},
		success: function (response) {
			 if (response.status == 1) {
                if (response.data == "") {
                    
                } else {
                	var data = response.data;
                	var location_select = $('.form-item-field-silai-town .form-select');
					location_select.empty();
					
					location_select.html("<option value='_none'>- Select a value -</option>");
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

  	//populate location dropdown based on country selection
  	jQuery(".form-item-field-silai-country .form-select").on('change', function() {
	    $.ajax({
			url: drupalSettings.path.baseUrl + 'get_silai_locations_by_country/' + this.value,
			type: 'GET',
			error: function () {
				console.log("Error occurred");
			},
			success: function (response) {
				 if (response.status == 1) {
	                if (response.data == "") {
	                 var location_select = $('.form-item-field-silai-location .form-select');
						location_select.empty();
						
						location_select.html("<option value='_none'>- Select a value -</option>");   
	                } else {
	                	var data = response.data;
	                	var location_select = $('.form-item-field-silai-location .form-select');
						location_select.empty();
						
						location_select.html("<option value='_none'>- Select a value -</option>");
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

  	//populate state dropdown based on location selection
  	jQuery(".form-item-field-silai-location .form-select").on('change', function() {
  	  var selectedState =  $('.form-item-field-silai-business-state .form-select').val();	
	  $.ajax({
		url: drupalSettings.path.baseUrl + 'get_silai_states_by_location/' + this.value,
		type: 'GET',
		error: function () {
			console.log("Error occurred");
		},
		success: function (response) {
			 if (response.status == 1) {
                if (response.data == "" || response.data == null) {
                 var location_select = $('.form-item-field-silai-business-state .form-select');
					location_select.empty();
					
					location_select.html("<option value='_none'>- Select a value -</option>");   
                } else {
                	var data = response.data;
                	var location_select = $('.form-item-field-silai-business-state .form-select');
					location_select.empty();
					
					location_select.html("<option value='_none'>- Select a value -</option>");
                	$.each(data, function(key, value) {
                		if(key == selectedState) {   
					    	location_select.append($("<option></option>")
					                       .attr("value",key)
					                       .attr('selected', 'selected')
					                       .text(value));
					    } else {
				    		location_select.append($("<option></option>")
				                           .attr("value",key)
				                           .text(value)); 					    	
					    } 				        
					});
                }
            }
		}
	});
	});
	//Training management - populate state dropdown based on location selection
  	jQuery(".form-item-field-silai-training-location .form-select").on('change', function() {

	  $.ajax({
		url: drupalSettings.path.baseUrl + 'get_silai_states_by_location/' + this.value,
		type: 'GET',
		error: function () {
			console.log("Error occurred");
		},
		success: function (response) {
			 if (response.status == 1) {
                if (response.data == "" || response.data == null) {
                 var location_select = $('.form-item-field-silai-training-state .form-select');
					location_select.empty();
					
					location_select.html("<option value='_none'>- Select a value -</option>");   
                } else {
                	var data = response.data;
                	var location_select = $('.form-item-field-silai-training-state .form-select');
					location_select.empty();
					
					location_select.html("<option value='_none'>- Select a value -</option>");
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

	//Training management - populate district dropdown based on state selection
  	jQuery(".form-item-field-silai-training-state .form-select").on('change', function() {
	  $.ajax({
		url: drupalSettings.path.baseUrl + 'get_silai_districts_by_state/' + this.value,
		type: 'GET',
		error: function () {
			console.log("Error occurred");
		},
		success: function (response) {
			 if (response.status == 1) {
                if (response.data == "" || response.data == null) {
                 var location_select = $('.form-item-field-silai-training-district .form-select');
					location_select.empty();
					
					location_select.html("<option value='_none'>- Select a value -</option>");   
                } else {
                	var data = response.data;
                	var location_select = $('.form-item-field-silai-training-district .form-select');
					location_select.empty();
					
					location_select.html("<option value='_none'>- Select a value -</option>");
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

  	//populate district dropdown based on state selection
  	jQuery(".form-item-field-silai-business-state .form-select").on('change', function() {
  		var selectedDist =  $('.form-item-field-silai-district .form-select').val();	
		$.ajax({
			url: drupalSettings.path.baseUrl + 'get_silai_districts_by_state/' + this.value,
			type: 'GET',
			error: function () {
				console.log("Error occurred");
			},
			success: function (response) {
				 if (response.status == 1) {
			        if (response.data == "" || response.data == null) {
			         var location_select = $('.form-item-field-silai-district .form-select');
						location_select.empty();
						
						location_select.html("<option value='_none'>- Select a value -</option>");   
			        } else {
			        	var data = response.data;
			        	var location_select = $('.form-item-field-silai-district .form-select');
						location_select.empty();
						
						location_select.html("<option value='_none'>- Select a value -</option>");
			        	$.each(data, function(key, value) {   
							if(key == selectedDist) {
								location_select.append($("<option></option>")
							                .attr("value",key)
							                .attr('selected', 'selected')
							                .text(value)); 	
							} else { 
								location_select.append($("<option></option>")
							                .attr("value",key)
							                .text(value)); 
							}					     
						});
			        }
			    }
			}
		});
	});

	//populate district dropdown based on state selection
  	jQuery(".form-item-field-silai-trainer-state .form-select").on('change', function() {
	  $.ajax({
		url: drupalSettings.path.baseUrl + 'get_silai_districts_by_state/' + this.value,
		type: 'GET',
		error: function () {
			console.log("Error occurred");
		},
		success: function (response) {
			 if (response.status == 1) {
                if (response.data == "" || response.data == null) {
                 var location_select = $('.form-item-field-silai-trainer-district .form-select');
					location_select.empty();
					
					location_select.html("<option value='_none'>- Select a value -</option>");   
                } else {
                	var data = response.data;
                	var location_select = $('.form-item-field-silai-trainer-district .form-select');
					location_select.empty();
					
					location_select.html("<option value='_none'>- Select a value -</option>");
                	$.each(data, function(key, value) {   
				    location_select.append($("<option></option>")
				                    .attr("value",key)
				                    .text(value)); 
				        
				});


                }
                 var location_select = $('.form-item-field-silai-town-city .form-select');
					location_select.empty();
					
					location_select.html("<option value='_none'>- Select a value -</option>");
            }
		}
	});
	});

	//populate town for trainer dropdown based on district selection
  	jQuery(".form-item-field-silai-trainer-district .form-select").on('change', function() {
	  $.ajax({
		url: drupalSettings.path.baseUrl + 'get_silai_towns_by_district/' + this.value,
		type: 'GET',
		error: function () {
			console.log("Error occurred");
		},
		success: function (response) {
			 if (response.status == 1) {
                if (response.data == "" || response.data == null) {
                 var location_select = $('.form-item-field-silai-town-city .form-select');
					location_select.empty();
					
					location_select.html("<option value='_none'>- Select a value -</option>");   
                } else {
                	var data = response.data;
                	var location_select = $('.form-item-field-silai-town-city .form-select');
					location_select.empty();
					
					location_select.html("<option value='_none'>- Select a value -</option>");
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

  	//Training management - populate towns dropdown based on district selection
  	jQuery(".form-item-field-silai-training-district .form-select").on('change', function() {
	  $.ajax({
		url: drupalSettings.path.baseUrl + 'get_silai_towns_by_district/' + this.value,
		type: 'GET',
		error: function () {
			console.log("Error occurred");
		},
		success: function (response) {
			 if (response.status == 1) {
                if (response.data == "" || response.data == null) {
                  var location_select = $('.form-item-field-silai-training-town .form-select');
					location_select.empty();
					
					location_select.html("<option value='_none'>- Select a value -</option>");  
                } else {
                	var data = response.data;
                	var location_select = $('.form-item-field-silai-training-town .form-select');
					location_select.empty();
					
					location_select.html("<option value='_none'>- Select a value -</option>");
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
	
  	//populate towns dropdown based on district selection
  	jQuery(".form-item-field-silai-district .form-select").on('change', function() {
  		
  		var selectedTown =  $('.form-item-field-silai-town .form-select').val();
		$.ajax({
			url: drupalSettings.path.baseUrl + 'get_silai_towns_by_district/' + this.value,
			type: 'GET',
			error: function () {
				console.log("Error occurred");
			},
			success: function (response) {
				 if (response.status == 1) {
			        if (response.data == "" || response.data == null) {
			          var location_select = $('.form-item-field-silai-town .form-select');
						location_select.empty();
						
						location_select.html("<option value='_none'>- Select a value -</option>");  
			        } else {
			        	var data = response.data;
			        	var location_select = $('.form-item-field-silai-town .form-select');
						location_select.empty();
						
						location_select.html("<option value='_none'>- Select a value -</option>");
			        	$.each(data, function(key, value) {   
							if(key == selectedTown) {
								location_select.append($("<option></option>")
							                .attr("value",key)
							                .attr('selected', 'selected')
							                .text(value)); 	
							} else { 
								location_select.append($("<option></option>")
							                .attr("value",key)
							                .text(value)); 
							}					        
						});
			        }
			    }
			}
		});
	});

  	//populate towns dropdown based on district selection using autocomplete suggestion
  	jQuery(".form-item-field-silai-district-0-target-id .form-select").on('autocompletechange', function(event) {
  		var id = this.value.replace ( /[^\d.]/g, '' );
		$.ajax({
		url: drupalSettings.path.baseUrl + 'get_silai_towns_by_district/' + this.value,
		type: 'GET',
		error: function () {
			console.log("Error occurred");
		},
		success: function (response) {
			 if (response.status == 1) {
                if (response.data == "" || response.data == null) {
                   var location_select = $('.form-item-field-silai-town .form-select');
					location_select.empty();
					
					location_select.html("<option value='_none'>- Select a value -</option>"); 
                } else {
                	var data = response.data;
                	var location_select = $('.form-item-field-silai-town .form-select');
					location_select.empty();
					
					location_select.html("<option value='_none'>- Select a value -</option>");
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

  	//populate item dropdown based on item group selection
  	jQuery('.form-item-field-silai-item-group .form-select').on('change', function(e) {
  		$.ajax({
		url: drupalSettings.path.baseUrl + 'get_items_by_itemgroup/' + this.value,
		type: 'GET',
		error: function () {
			console.log("Error occurred");
		},
		success: function (response) {
			 if (response.status == 1) {
                if (response.data == "" || response.data == null) {
                    var item_select = $('.form-item-field-silai-item-name .form-select');
					item_select.empty();
					
					item_select.html("<option value='_none'>- Select a value -</option>");
                } else {
                	var data = response.data;
                	var item_select = $('.form-item-field-silai-item-name .form-select');
					item_select.empty();
					
					item_select.html("<option value='_none'>- Select a value -</option>");
                	$.each(data, function(key, value) {   
				    item_select.append($("<option></option>")
				                    .attr("value",key)
				                    .text(value)); 
				        
				});

                }
            }
		}
	});
	});

	jQuery('.form-item-field-silai-location .form-select').trigger("change");
	jQuery('.form-item-field-silai-business-state .form-select').trigger("change");
	jQuery('.form-item-field-silai-district .form-select').trigger("change");


  	jQuery(".addCF").click(function(e){
  		e.preventDefault();
  		var locationId = $(this).data('id');
		$.ajax({
		url: drupalSettings.path.baseUrl + 'get_ngo_by_locationid/' + locationId,
		type: 'GET',
		error: function () {
			console.log("Error occurred");
		},
		success: function (response) {
			var location_select = '';
			 if (response.status == 1) {
                if (response.data == "") {
                    
                } else {
                	var data = response.data;
                	//console.log(data);
                	location_select = location_select + '<select name="field_silai_frd_to[]" class="form-select required form-control" id="location" required="required" aria-required="true"><option value="_none">- Select a value -</option>';
					//location_select.empty();
					
					//location_select.html("<option value='_none'>- Select a value -</option>");
                	$.each(data, function(key, value) {
                	location_select = location_select +  '<option value="'+key+'">'+value+'</option>'  
				    
				});
                	location_select = location_select + '</select>';
                	
                }
            }
           $("#wrapper-repater").append('<div><div class="form-item"><label for="edit" class="js-form-required form-required">NGO Name</label>' + location_select + '</div><div class="form-item"><label for="edit" class="js-form-required form-required">Quantity</label><input class="NUMERIC form-text required form-control" name="field_silai_item_sent[]" type="text" value=""  maxlength="10" required="required" aria-required="true"></div> &nbsp; <a href="javascript:void(0);" class="remCF">Remove</a></div>'); 
		}
	});
		
	});

	jQuery("#wrapper-repater").on('click','.remCF',function(){
        $(this).parent().remove();
    });

    jQuery(".addschool-row").click(function(e){
		e.preventDefault();
		var ngoId = $(this).data('id');
		$.ajax({
		url: drupalSettings.path.baseUrl + 'get_schools_by_ngoid/' + ngoId,
		type: 'GET',
		error: function () {
			console.log("Error occurred");
		},
		success: function (response) {
			var school_select = '';
			 if (response.status == 1) {
                if (response.data == "") {
                    
                } else {
                	var data = response.data;
                	//console.log(data);
                	school_select = school_select + '<select  name="field_silai_frd_to[]" class="form-select required form-control" required="required" aria-required="true"><option value="_none" selected="selected">- Select a value -</option>';
					//school_select.empty();
					
					//school_select.html("<option value='_none'>- Select a value -</option>");
                	$.each(data, function(key, value) {
                	school_select = school_select +  '<option value="'+key+'">'+value+'</option>'  
				    
				});
                	school_select = school_select + '</select>';
                	
                } 
            }
          $("#school-wrapper-repater").append('<div><div class="form-item"><label for="edit-field-silai-frd-to" class="js-form-required form-required">School Name</label>' + school_select + '</div><div class="form-item"><label for="edit" class="js-form-required form-required">Quantity</label><input class="NUMERIC form-text required form-control" name="field_silai_item_sent[]" type="text" value=""  maxlength="10" required="required" aria-required="true"></div> &nbsp; <a href="javascript:void(0);" class="remCF">Remove</a></div>');  
		}
	}); 
		//$("#school-wrapper-repater").append('<div style="margin-top:50px"><label for="edit-field-silai-frd-to" class="js-form-required form-required">School Name</label><select  name="field_silai_frd_to[]" class="form-select required form-control" required="required" aria-required="true"><option value="_none" selected="selected">- Select a value -</option><option value="1">School 1</option><option value="2">School 2</option></select><label for="edit" class="js-form-required form-required">Quantity</label><input class="NUMERIC form-text required form-control" name="field_silai_item_sent[]" type="text" value=""  maxlength="10" required="required" aria-required="true"> &nbsp; <a href="javascript:void(0);" class="remCF">Remove</a></div>');
	});

	jQuery("#school-wrapper-repater").on('click','.remCF',function(){
        $(this).parent().remove();
    });

    jQuery(".node-nfa-form .form-item-title-0-value .form-control").on('keypress keyup keydown',function(event) { 
  		// create the event
	   var press = jQuery.Event(event.type);
	   var code = event.keyCode || event.which;
	   var val = $(this).val();
	   var updatedval = '';
	   press.which = code ;  
	   console.log($(this).val());
	   if(val.length == 4) {
	   	updatedval = val + '/';
	   	$(this).val(updatedval);
	   }
	   
	  
	});

    // Js for maintaining star rating on training feedback pop up form
  	!function(a){"use strict";function b(a){return"[data-value"+(a?"="+a:"")+"]"}function c(a,b,c){var d=c.activeIcon,e=c.inactiveIcon;a.removeClass(b?e:d).addClass(b?d:e)}function d(b,c){var d=a.extend({},i,b.data(),c);return d.inline=""===d.inline||d.inline,d.readonly=""===d.readonly||d.readonly,d.clearable===!1?d.clearableLabel="":d.clearableLabel=d.clearable,d.clearable=""===d.clearable||d.clearable,d}function e(b,c){if(c.inline)var d=a('<span class="rating-input"></span>');else var d=a('<div class="rating-input"></div>');c.copyClasses&&(d.addClass(b.attr("class")),d.removeClass("rating"));for(var e=c.min;e<=c.max;e++)d.append('<i class="'+c.iconLib+'" data-value="'+e+'"></i>');return c.clearable&&!c.readonly&&d.append("&nbsp;").append('<a class="'+f+'"><i class="'+c.iconLib+" "+c.clearableIcon+'"/>'+c.clearableLabel+"</a>"),d}var f="rating-clear",g="."+f,h="hidden",i={min:1,max:5,"empty-value":0,iconLib:"glyphicon",activeIcon:"glyphicon-star",inactiveIcon:"glyphicon-star-empty",clearable:!1,clearableIcon:"glyphicon-remove",clearableRemain:!1,inline:!1,readonly:!1,copyClasses:!0},j=function(a,b){var c=this.$input=a;this.options=d(c,b);var f=this.$el=e(c,this.options);c.addClass(h).before(f),c.attr("type","hidden"),this.highlight(c.val())};j.VERSION="0.4.0",j.DEFAULTS=i,j.prototype={clear:function(){this.setValue(this.options["empty-value"])},setValue:function(a){this.highlight(a),this.updateInput(a)},highlight:function(a,d){var e=this.options,f=this.$el;if(a>=this.options.min&&a<=this.options.max){var i=f.find(b(a));c(i.prevAll("i").addBack(),!0,e),c(i.nextAll("i"),!1,e)}else c(f.find(b()),!1,e);d||(this.options.clearableRemain?f.find(g).removeClass(h):a&&a!=this.options["empty-value"]?f.find(g).removeClass(h):f.find(g).addClass(h))},updateInput:function(a){var b=this.$input;b.val()!=a&&b.val(a).change()}};var k=a.fn.rating=function(c){return this.filter("input[type=number]").each(function(){var d=a(this),e="object"==typeof c&&c||{},f=new j(d,e);f.options.readonly||f.$el.on("mouseenter",b(),function(){f.highlight(a(this).data("value"),!0)}).on("mouseleave",b(),function(){f.highlight(d.val(),!0)}).on("click",b(),function(){f.setValue(a(this).data("value"))}).on("click",g,function(){f.clear()})})};k.Constructor=j,a(function(){a("input.rating[type=number]").each(function(){a(this).rating()})})}
	(jQuery);
		

  	}
};

// JS for Survey Quetionnaire School
jQuery(document).ready(function() {
	//Field Mobile Phone
	jQuery('input[name="use_mobile_phone"]').click(function(){
        var inputValue = jQuery(this).attr("value");
       	if(inputValue =='1'){
       		jQuery('.form-item-alternative-mobile-number').css("display", "block");
       		jQuery('.form-item-type-of-mobile-phone').css("display", "block");
       	}else{
       		jQuery('.form-item-alternative-mobile-number').css("display", "none");
       		jQuery('.form-item-type-of-mobile-phone').css("display", "none");
       	}
    });
	jQuery('input[name="use_mobile_phone"]:checked').trigger( "click" );

	//Field Email Id
	jQuery('input[name="have_email_id"]').click(function(){
        var inputValue = jQuery(this).attr("value");
       	if(inputValue =='1'){
       		jQuery('.form-item-mention-email').css("display", "block");
       	}else{
       		jQuery('.form-item-mention-email').css("display", "none");
       	}
    });
	jQuery('input[name="have_email_id"]:checked').trigger( "click" );

	//Field Ration card
	jQuery('input[name="ration_card"]').click(function(){
        var inputValue = jQuery(this).attr("value");
       	if(inputValue =='1'){
       		jQuery('.form-item-type-of-ration-card').css("display", "block");
       	}else{
       		jQuery('.form-item-type-of-ration-card').css("display", "none");
       	}
    });
	jQuery('input[name="ration_card"]:checked').trigger( "click" );

	// Field Bank Account
	jQuery('input[name="have_bank_account"]').click(function(){
        var inputValue = jQuery(this).attr("value");
       	if(inputValue =='1'){
       		jQuery('.form-item-bank-name').css("display", "block");
       	}else{
       		jQuery('.form-item-bank-name').css("display", "none");
       	}
    });
	jQuery('input[name="have_bank_account"]:checked').trigger( "click" );

	//Field Adhar card
	jQuery('input[name="have_aadhar_card"]').click(function(){
        var inputValue = jQuery(this).attr("value");
       	if(inputValue =='1'){
       		jQuery('.form-item-aadhar-number').css("display", "block");
       	}else{
       		jQuery('.form-item-aadhar-number').css("display", "none");
       	}
    });
	jQuery('input[name="have_aadhar_card"]:checked').trigger( "click" );

	// Field MFI
	jQuery('input[name="associated_with_any_mfi"]').click(function(){
        var inputValue = jQuery(this).attr("value");
       	if(inputValue =='1'){
       		jQuery('.form-item-mfi-number').css("display", "block");
       		jQuery('.form-item-other-mifs-working-your-area').css("display", "block");
       	}else{
       		jQuery('.form-item-mfi-number').css("display", "none");
       		jQuery('.form-item-other-mifs-working-your-area').css("display", "none");
       	}
    });
	jQuery('input[name="associated_with_any_mfi"]:checked').trigger( "click" );

	//Field Gov benefitted schemes
	jQuery('input[name="getting_benefitted_government_schemes"]').click(function(){
        var inputValue = jQuery(this).attr("value");
       	if(inputValue =='1'){
       		jQuery('.form-item-kindly-give-government-benefitted-details').css("display", "block");
       	}else{
       		jQuery('.form-item-kindly-give-government-benefitted-details').css("display", "none");
       	}
    });
	jQuery('input[name="getting_benefitted_government_schemes"]:checked').trigger( "click" );

	// Field Electricity Status
	jQuery('input[name="electricity_status_in_home"]').click(function(){
        var inputValue = jQuery(this).attr("value");
       	if(inputValue =='1'){
       		jQuery('.form-item-average-electricity-hours').css("display", "block");
       	}else{
       		jQuery('.form-item-average-electricity-hours').css("display", "none");
       	}
    });
	jQuery('input[name="electricity_status_in_home"]:checked').trigger( "click" );

	// Field Complete Training from usha
	jQuery('input[name="completed_training_from_usha"]').click(function(){
        var inputValue = jQuery(this).attr("value");
       	if(inputValue =='1'){
       		jQuery('.form-item-if-yes-got-trainined').css("display", "block");
       	}else{
       		jQuery('.form-item-if-yes-got-trainined').css("display", "none");
       	}
    });
	jQuery('input[name="completed_training_from_usha"]:checked').trigger( "click" );
	
	// Field Related order work
	jQuery('input[name="stitching_related_order_work_completed"]').click(function(){
        var inputValue = jQuery(this).attr("value");
       	if(inputValue =='1'){
       		jQuery('.form-item-if-yes-order-completed').css("display", "block");
       	}else{
       		jQuery('.form-item-if-yes-order-completed').css("display", "none");
       	}
    });
	jQuery('input[name="stitching_related_order_work_completed"]:checked').trigger( "click" );

});