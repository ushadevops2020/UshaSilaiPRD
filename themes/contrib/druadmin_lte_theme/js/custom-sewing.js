jQuery(function ($) {
 // Start Code for Fix Point #1
 /*
  $('.generate-fee-receipt-form').submit(function(){
    $("input[type='submit']", this)
      .val("Please Wait...")
      .attr('disabled', 'disabled');
    return true;
  });
*/
 // End Code for Fix Point #1
	//delete user using confirm box
	$('i.delete-sewing-weekly-mis').on('click', function(){
	    id = $(this).attr("data-id");
		$.confirm({
		    title: 'Delete MIS?',
		    content: 'Are you sure to delete MIS?',
		    autoClose: 'Close|8000',
		    buttons: {
		        deleteUser: {
		            text: 'delete ',
		            action: function () {
		            	jQuery.ajax({
							url: drupalSettings.path.baseUrl + 'weekly-mis-delete',
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
		        Close: function () {
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
		    autoClose: 'Close|8000',
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
		        Close: function () {
		        }
		    }
		});    	
	})
	//delete message using confirm box
	$('i.delete-sewing-notification').on('click', function(){
		
	    id = $(this).attr("data-id");
		$.confirm({
		    title: 'Delete Notification?',
		    content: 'Are you sure to delete Notification?',
		    autoClose: 'Close|8000',
		    buttons: {
		        deleteUser: {
		            text: 'delete ',
		            action: function () {
		            	jQuery.ajax({
							url: drupalSettings.path.baseUrl + 'sewing-delete_notification',
							type: 'POST',
							data: {id:id},
							async : true,
							dataType : "json",
							success: function (response) {
								if (response.status == 1) {
									window.location.reload();
								}
							}							
					    });					    
		            }
		        },
		        Close: function () {
		        }
		    }
		});    	
	});
	//$('.terminate_school').hide();
	jQuery('span.terminate_school a').on('click', function(){
	    var schoolNid = $(this).attr('node-id');
	    //alert(schoolNid);
		$.confirm({
		    title: 'Terminate School?',
		    content: 'Are you sure to Terminate School?',
		    autoClose: 'Close|10000',
		    buttons: {
		        deleteUser: {
		            text: 'Terminate',
		            action: function () {
		            	jQuery.ajax({
							url: drupalSettings.path.baseUrl + 'sewing_school_terminated_state/'+schoolNid,
							type: 'POST',
							//data: {userId:userId},
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
		        Close: function () {
		        }
		    }
		});   	
	})
	jQuery('span.school_modified_need_approval a').on('click', function(){
	    var schoolNid = $(this).attr('node-id');
	    //alert(schoolNid);
		$.confirm({
		    title: 'Modified School Approval?',
		    content: 'Are you sure to Modified School Approval to Approved?',
		    autoClose: 'Close|10000',
		    buttons: {
		        deleteUser: {
		            text: 'Approved',
		            action: function () {
		            	jQuery.ajax({
							url: drupalSettings.path.baseUrl + 'sewing_school_modified_need_approval/'+schoolNid,
							type: 'POST',
							//data: {userId:userId},
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
		        Close: function () {
		        }
		    }
		});   	
	})

	$.ajax({
		url: drupalSettings.path.baseUrl + 'get_sewing_states/',
		type: 'GET',
		error: function () {
			console.log("Error occurred");
		},
		success: function (response) {
			 if (response.status == 1) {
                if (response.data == "") {
                    
                } else {
                	var data = response.data;
                	var location_select = $('#state_filter');
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
	var locationNidForAddSchool = jQuery("#node-sewing-school-form #edit-field-location option:selected").val();
	if(locationNidForAddSchool) {
	    $.ajax({
			url: drupalSettings.path.baseUrl + 'get_districts_by_location/' + locationNidForAddSchool,
			type: 'POST',
			error: function () {
				console.log("Error occurred");
			},
			success: function (response) {
				 if (response.status == 1) {
	                if (response.data == "") {
	                 var district_select = $('#node-sewing-school-form .form-item-field-district select');
						district_select.empty();
						district_select.html("<option value='_none'>- Select a value -</option>");   
	                } else {
	                	var data = response.data;
	                	var district_select = $('#node-sewing-school-form .form-item-field-district select');
						district_select.empty();
						district_select.html("<option value='_none'>- Select a value -</option>");
	                	$.each(data, function(key, value) {   
					    district_select.append($("<option></option>")
					                    .attr("value",key)
					                    .text(value)); 
					        
					});

	                }
	            }
			}
		});		
	}
 
	var locationNidForEditSchool = jQuery("#node-sewing-school-edit-form #edit-field-location option:selected").val();
	var districtNidForEditSchool = jQuery("#node-sewing-school-edit-form #edit-field-district option:selected").val();
	if(locationNidForEditSchool) {
	    $.ajax({
			url: drupalSettings.path.baseUrl + 'get_districts_by_location/' + locationNidForEditSchool,
			type: 'POST',
			error: function () {
				console.log("Error occurred");
			},
			success: function (response) {
				 if (response.status == 1) {
	                if (response.data == "") {
	                 var district_select = $('#node-sewing-school-edit-form .form-item-field-district select');
						district_select.empty();
						district_select.html("<option value='_none'>- Select a value -</option>");   
	                } else {
	                	var data = response.data;
	                	var district_select = $('#node-sewing-school-edit-form .form-item-field-district select');
						district_select.empty();
						district_select.html("<option value='_none'>- Select a value -</option>");
	                	$.each(data, function(key, value) {   
					    district_select.append($("<option></option>")
					                    .attr("value",key)
					                    .text(value));  
						});
						jQuery('#node-sewing-school-edit-form #edit-field-district  option[value="'+districtNidForEditSchool+'"]').attr("selected", true);
	                }
	            }
			}
		});		
	}

	var townNidForEditSchool = jQuery("#node-sewing-school-edit-form #edit-field-town-city option:selected").val();
	if(districtNidForEditSchool) {
		$.ajax({
			url: drupalSettings.path.baseUrl + 'get_towns_by_district/' + districtNidForEditSchool,
			type: 'POST',
			error: function () {
				console.log("Error occurred");
			},
			success: function (response) {
				 if (response.status == 1) {
	                if (response.data == "") {
	                 var town_select = $('#node-sewing-school-edit-form .form-item-field-town-city select');
						town_select.empty();
						town_select.html("<option value='_none'>- Select a value -</option>");   
	                } else {
	                	var data = response.data;
	                	var town_select = $('#node-sewing-school-edit-form .form-item-field-town-city select');
						town_select.empty();
						town_select.html("<option value='_none'>- Select a value -</option>");
	                	$.each(data, function(key, value) {   
					    town_select.append($("<option></option>")
					                    .attr("value",key)
					                    .text(value));  
						});
						jQuery('#node-sewing-school-edit-form #edit-field-town-city  option[value="'+townNidForEditSchool+'"]').attr("selected", true);
	                }
	            }
			}
		});		
	}


	var locationNidForSchool = jQuery("#node-sewing-school-form #edit-field-location option:selected").val();
	if(locationNidForSchool) {
		$.ajax({
			url: drupalSettings.path.baseUrl + 'sewing_get_town_by_location/' + locationNidForSchool,
			type: 'POST',
			error: function () {
				console.log("Error occurred");
			},
			success: function (response) {
				 if (response.status == 1) {
	                if (response.data == "") {
	                 var town_select = $('#node-sewing-school-form .form-item-field-town-city select');
						town_select.empty();
						town_select.html("<option value='_none'>- Select a value -</option>");   
	                } else {
	                	var data = response.data;
	                	var town_select = $('#node-sewing-school-form .form-item-field-town-city select');
						town_select.empty();
						town_select.html("<option value='_none'>- Select a value -</option>");
	                	$.each(data, function(key, value) {   
					    town_select.append($("<option></option>")
					                    .attr("value",key)
					                    .text(value));  
						});
						//jQuery('#node-sewing-school-edit-form #edit-field-town-city  option[value="'+townNidForEditSchool+'"]').attr("selected", true);
	                }
	            }
			}
		});		
	}

	//Menu Management start
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

	var schoolMgmtHref = [];
		var uri = window.location.href.split('/');
		$('ul.sidebar-menu li:contains("School Management")').find('a').each(function() {
	    	if($(this).attr('href') == '/'+ uri[3]) {
	    		$(this).closest('li').addClass('active');
	    	}

	    	if(jQuery.inArray($(this).attr('href').replace('/', ''), schoolMgmtHref) === -1) {
	    		schoolMgmtHref.push($(this).attr('href').replace('/', ''));
	    	}
		});
		
		var studentMgmtHrefArray = [];
		var uri = window.location.href.split('/');
		$('ul.sidebar-menu li:contains("Student Management")').find('a').each(function() {
			
	    	if($(this).attr('href') == '/'+ uri[3]) {
	    		$(this).closest('li').addClass('active');
	    	}

	    	if(jQuery.inArray($(this).attr('href').replace('/', ''), studentMgmtHrefArray) === -1) {
	    		studentMgmtHrefArray.push($(this).attr('href').replace('/', ''));
	    	}
		});

		var revenueTaxesHrefArray = [];
		var uri = window.location.href.split('/');
		$('ul.sidebar-menu li:contains("Revenue and Taxes")').find('a').each(function() {
			
	    	if($(this).attr('href') == '/'+ uri[3]) {
	    		$(this).closest('li').addClass('active');
	    	}

	    	if(jQuery.inArray($(this).attr('href').replace('/', ''), revenueTaxesHrefArray) === -1) {
	    		revenueTaxesHrefArray.push($(this).attr('href').replace('/', ''));
	    	}
		});

		var misHrefArray = [];
		var uri = window.location.href.split('/');
		$('ul.sidebar-menu li:contains("MIS")').find('a').each(function() {
			
	    	if($(this).attr('href') == '/'+ uri[3]) {
	    		$(this).closest('li').addClass('active');
	    	}

	    	if(jQuery.inArray($(this).attr('href').replace('/', ''), misHrefArray) === -1) {
	    		misHrefArray.push($(this).attr('href').replace('/', ''));
	    	}
		});

		var masterMgmtHref = [];
		var uri = window.location.href.split('/');
		$('ul.sidebar-menu li:contains("Master Management")').find('a').each(function() {
			
	    	if($(this).attr('href') == '/'+ uri[3]) {
	    		$(this).closest('li').addClass('active');
	    	}

	    	if(jQuery.inArray($(this).attr('href').replace('/', ''), masterMgmtHref) === -1) {
	    		masterMgmtHref.push($(this).attr('href').replace('/', ''));
	    	}
		});

		var reportsHref = [];
		var uri = window.location.href.split('/');
		$('ul.sidebar-menu li:contains("Reports")').find('a').each(function() {
			
	    	if($(this).attr('href') == '/'+ uri[3]) {
	    		$(this).closest('li').addClass('active');
	    	}

	    	if(jQuery.inArray($(this).attr('href').replace('/', ''), reportsHref) === -1) {
	    		reportsHref.push($(this).attr('href').replace('/', ''));
	    	}
		});

		var locationHrefArray = [];
		var uri = window.location.href.split('/');
		$('.sidebar-menu > li.treeview > ul.treeview-menu > li.treeview:nth-child(6)').find('a').each(function() {
			
	    	if($(this).attr('href') == '/'+ uri[3]) {
	    		$(this).closest('li').addClass('active');
	    	}

	    	if(jQuery.inArray($(this).attr('href').replace('/', ''), locationHrefArray) === -1) {
	    		locationHrefArray.push($(this).attr('href').replace('/', ''));
	    	}
		});

		var courseHrefArray = [];
		var uri = window.location.href.split('/');
		$('.sidebar-menu > li.treeview > ul.treeview-menu > li.treeview:nth-child(4)').find('a').each(function() {
			
	    	if($(this).attr('href') == '/'+ uri[3]) {
	    		$(this).closest('li').addClass('active');
	    	}

	    	if(jQuery.inArray($(this).attr('href').replace('/', ''), courseHrefArray) === -1) {
	    		courseHrefArray.push($(this).attr('href').replace('/', ''));
	    	}
		});

		var inventoryHrefArray = [];
		var uri = window.location.href.split('/');
		$('.sidebar-menu > li.treeview > ul.treeview-menu > li.treeview:nth-child(9)').find('a').each(function() {
			
	    	if($(this).attr('href') == '/'+ uri[3]) {
	    		$(this).closest('li').addClass('active');
	    	}

	    	if(jQuery.inArray($(this).attr('href').replace('/', ''), inventoryHrefArray) === -1) {
	    		inventoryHrefArray.push($(this).attr('href').replace('/', ''));
	    	}
		});

		if(jQuery.inArray(uri[3], sideMenuHref) !== -1) {
			
			$('ul.sidebar-menu > .sidebar-menu li').addClass('active');
			$('ul.sidebar-menu li.treeview ul.treeview-menu li.treeview').removeClass('active');
			$('ul.sidebar-menu > .sidebar-menu li').siblings().removeClass('active');
		}
		

		if(jQuery.inArray(uri[3], schoolMgmtHref) !== -1) {
			
			$('ul.sidebar-menu > li.treeview:contains("School Management")').addClass('active');
			$('ul.sidebar-menu li.treeview ul.treeview-menu li.treeview').removeClass('active');
			$('ul.sidebar-menu > li.treeview:contains("School Management")').siblings().removeClass('active');
			
		}

		if(jQuery.inArray(uri[3], studentMgmtHrefArray) !== -1) {
			
			$('ul.sidebar-menu > li.treeview:contains("Student Management")').addClass('active');
			$('ul.sidebar-menu li.treeview ul.treeview-menu li.treeview').removeClass('active');
			$('ul.sidebar-menu > li.treeview:contains("Student Management")').siblings().removeClass('active');
			
		}

		if(jQuery.inArray(uri[3], revenueTaxesHrefArray) !== -1) {
			
			$('ul.sidebar-menu > li.treeview:contains("Revenue and Taxes")').addClass('active');
			$('ul.sidebar-menu li.treeview ul.treeview-menu li.treeview').removeClass('active');
			$('ul.sidebar-menu > li.treeview:contains("Revenue and Taxes")').siblings().removeClass('active');
			
		}

		if(jQuery.inArray(uri[3], misHrefArray) !== -1) {
			
			$('ul.sidebar-menu > li.treeview:contains("MIS")').addClass('active');
			$('ul.sidebar-menu li.treeview ul.treeview-menu li.treeview').removeClass('active');
			$('ul.sidebar-menu > li.treeview:contains("MIS")').siblings().removeClass('active');
			
		}

		if(jQuery.inArray(uri[3], masterMgmtHref) !== -1) {
			
			$('ul.sidebar-menu > li.treeview:contains("Master Management")').addClass('active');
			$('ul.sidebar-menu li.treeview ul.treeview-menu li.treeview').removeClass('active');
			$('ul.sidebar-menu > li.treeview:contains("Master Management")').siblings().removeClass('active');
			
		}


		if(jQuery.inArray(uri[3], reportsHref) !== -1) {
			
			$('ul.sidebar-menu > li.treeview:contains("Reports")').addClass('active');
			$('ul.sidebar-menu li.treeview ul.treeview-menu li.treeview').removeClass('active');
			$('ul.sidebar-menu > li.treeview:contains("Reports")').siblings().removeClass('active');
			
		}

		if(jQuery.inArray(uri[3], locationHrefArray) !== -1) {
			$('ul.sidebar-menu li.treeview').addClass('active')
			$('ul.sidebar-menu li.treeview').parent().parent().siblings().removeClass('active');
			$('ul.sidebar-menu  li.treeview   ul.treeview-menu  li.treeview:not(:nth-child(6))').removeClass('active');
		}

		if(jQuery.inArray(uri[3], courseHrefArray) !== -1) {
			$('ul.sidebar-menu li.treeview').addClass('active')
			$('ul.sidebar-menu li.treeview').parent().parent().siblings().removeClass('active');
			$('ul.sidebar-menu  li.treeview   ul.treeview-menu  li.treeview:not(:nth-child(4))').removeClass('active');
		}

		if(jQuery.inArray(uri[3], inventoryHrefArray) !== -1) {
			$('ul.sidebar-menu li.treeview').addClass('active')
			$('ul.sidebar-menu li.treeview').parent().parent().siblings().removeClass('active');
			$('ul.sidebar-menu  li.treeview   ul.treeview-menu  li.treeview:not(:nth-child(9))').removeClass('active');
		}
		//Menu Management end
	$(".cancel").click(function() {
	history.back(1);
	});

//$(".form-item-field-sewing-school-code-list-0-target-id .form-autocomplete").on('autocompletechange', function(event) {
	$(".custom-manage-student .form-select").on('change', function(event) {
		// var schoolCodeValue = this.value;
		// var schoolCodeData = schoolCodeValue.split('(');
		// var id = schoolCodeData[1].replace(')', '');
		 
		$.ajax({
		url: drupalSettings.path.baseUrl + 'get_school_detail_by_id/' + this.value,
		type: 'GET',
		error: function () {
			console.log("Error occurred");
		},
		success: function (response) {

			 if (response.status == 1) {
			 	if (response.data == "") {
                    
                } else {
                	$('#edit-field-school-name-sewing-0-value').val('');
                	var data = response.data;
                	$('#edit-field-school-name-sewing-0-value').val(data.schoolName);
                }
            }
		}
	});
	  
	});
 
$("#edit-field-sewing-exam-result").on('change', function(event) {
	var selectedValue = this.value;
	if(selectedValue != 1) {
		$("#edit-field-sewing-grades option:selected").prop("selected", false);
		$("#edit-field-sewing-certificate-print option:selected").prop("selected", false);
		$("#edit-field-sewing-certificate-issued option:selected").prop("selected", false);
		$("#edit-field-sewing-result-date-0-value-date").val('');
		$("#edit-field-date-of-certificate-print-0-value-date").val('');
		$("#edit-field-date-of-certificate-issued-0-value-date").val('');
	} 
	var feedue = ($('#edit-field-sewing-course-fee-due-0-value').val()) ? $('#edit-field-sewing-course-fee-due-0-value').val() : 0;
	var feeReceived = ($('#edit-field-sewing-course-fee-received-0-value').val()) ? $('#edit-field-sewing-course-fee-received-0-value').val() : 0;

    var outstanding = feedue - feeReceived;
    $('#edit-field-sewing-course-fee-out-0-value').val(outstanding);
	if(outstanding != 0) {
		$('.form-item-field-sewing-certificate-print').addClass('hide');
		$('.field--name-field-date-of-certificate-print').addClass('hide');
		$('.field--name-field-sewing-certificate-issued').addClass('hide');
		$('.field--name-field-date-of-certificate-issued').addClass('hide');
	} else {
		$('.form-item-field-sewing-certificate-print').removeClass('hide');
		$('.field--name-field-date-of-certificate-print').removeClass('hide');
		$('.field--name-field-sewing-certificate-issued').removeClass('hide');
		$('.field--name-field-date-of-certificate-issued').removeClass('hide');
	}

});
	
	// load dependent dropdown on couse code value
	$("#edit-field-sewing-course-code-list").on('change', function(event) {
		var schoolNid = $('#edit-field-sewing-school-code-list').val();
		$.ajax({
			url: drupalSettings.path.baseUrl + 'get_course_detail_by_id/' + this.value + '/'+schoolNid,
			type: 'GET',
			error: function () {
				console.log("Error occurred");
			},
			success: function (response) {
				 if (response.status == 1) {
				 	if (response.data == "") {
	                  	$('#edit-field-sewing-course-name-0-value').val('');
	                	$('#edit-field-sewing-course-duration-0-value').val('');
	                	$('#edit-field-sewing-course-fee-0-value').val('');
	                	$('#edit-field-sewing-course-fee-out-0-value').val('');  
	                } else {
	                	$('#edit-field-sewing-course-name-0-value').val('');
	                	$('#edit-field-sewing-course-duration-0-value').val('');
	                	$('#edit-field-sewing-course-fee-0-value').val('');
	                	$('#edit-field-sewing-course-fee-out-0-value').val('');
	                	var data = response.data;
	                	$('#edit-field-sewing-course-name-0-value').val(data.courseName);
	                	$('#edit-field-sewing-course-duration-0-value').val(data.courseDuration);
	                	$('#edit-field-sewing-course-fee-0-value').val(data.feeDue);
	                	$('#edit-field-sewing-course-fee-due-0-value').val(data.paymentToUILFee);
	                	// on page load calulate outstanding
						var feedue = ($('#edit-field-sewing-course-fee-due-0-value').val()) ? $('#edit-field-sewing-course-fee-due-0-value').val() : 0;
						var feeReceived = ($('#edit-field-sewing-course-fee-received-0-value').val()) ? $('#edit-field-sewing-course-fee-received-0-value').val() : 0;
					    var outstanding = feedue - feeReceived;
					    $('#edit-field-sewing-course-fee-out-0-value').val(outstanding);
					   // console.log(outstanding);
						if(outstanding && outstanding != 0) {
							//console.log('if-1');
							$('#edit-field-sewing-certificate-issued').val('_none');
							$('#edit-field-sewing-certificate-print').val('_none');
		                	//$('#edit-field-student-status').val('_none');
		                	$("#edit-field-student-status").removeAttr('disabled', true);
		                	//$('.field--name-field-sewing-exit-code').addClass('hide');

							$('.form-item-field-sewing-certificate-print').addClass('hide');
							$('.field--name-field-date-of-certificate-print').addClass('hide');
							$('.field--name-field-sewing-certificate-issued').addClass('hide');
							$('.field--name-field-date-of-certificate-issued').addClass('hide');
						} else {
							//console.log('else-1');
							$('.form-item-field-sewing-certificate-print').removeClass('hide');
							$('.field--name-field-date-of-certificate-print').removeClass('hide');
							$('.field--name-field-sewing-certificate-issued').removeClass('hide');
							$('.field--name-field-date-of-certificate-issued').removeClass('hide');
						}
	                }
	            } else {
            		$('#edit-field-sewing-course-name-0-value').val('');
                	$('#edit-field-sewing-course-duration-0-value').val('');
                	$('#edit-field-sewing-course-fee-0-value').val('');
                	$('#edit-field-sewing-course-fee-out-0-value').val('');
                	
                	var feedue = ($('#edit-field-sewing-course-fee-due-0-value').val()) ? $('#edit-field-sewing-course-fee-due-0-value').val() : 0;
					var feeReceived = ($('#edit-field-sewing-course-fee-received-0-value').val()) ? $('#edit-field-sewing-course-fee-received-0-value').val() : 0;

				    var outstanding = feedue - feeReceived;
				    $('#edit-field-sewing-course-fee-out-0-value').val(outstanding);

					if(outstanding && outstanding != 0) {
						//console.log('if-2');
						$('#edit-field-sewing-certificate-issued').val('_none');
	                	//$('#edit-field-student-status').val('_none');
	                	$("#edit-field-student-status").removeAttr('disabled', true);
	                	$('.field--name-field-sewing-exit-code').addClass('hide');

						$('.form-item-field-sewing-certificate-print').addClass('hide');
						$('.field--name-field-date-of-certificate-print').addClass('hide');
						$('.field--name-field-sewing-certificate-issued').addClass('hide');
						$('.field--name-field-date-of-certificate-issued').addClass('hide');
					} else {
						//console.log('else-2');
						$('.form-item-field-sewing-certificate-print').removeClass('hide');
						$('.field--name-field-date-of-certificate-print').removeClass('hide');
						$('.field--name-field-sewing-certificate-issued').removeClass('hide');
						$('.field--name-field-date-of-certificate-issued').removeClass('hide');
					} 
	            }
			}
		});
	});


	$("#edit-field-sewing-certificate-issued").on('change', function(event) {
		if($('#field_hidden_student_id').val()) {
			if(this.value == 1){
				$('#edit-field-student-status').val(0);
				$("#edit-field-student-status").attr('disabled', true);
				$('.field--name-field-sewing-exit-code').addClass('hide');
				$("#edit-field-sewing-exit-code").attr('disabled', true);
			} else if(this.value == 0){
			  $("#edit-field-student-status").removeAttr('disabled', true);
			  $("label[for=edit-field-student-status]").addClass('js-form-required form-required');

			} else {
				$("#edit-field-student-status").removeAttr('disabled', true);
				$("#edit-field-sewing-exit-code").removeAttr('disabled', true);
	    		$('.field--name-field-sewing-exit-code').addClass('hide');
			}	
		} else {
			if(this.value == 1){
			$('#edit-field-student-status').val(0);
			$("#edit-field-student-status").attr('disabled', true);
			$('.field--name-field-sewing-exit-code').addClass('hide');
			$("#edit-field-sewing-exit-code").attr('disabled', true);
			} else if(this.value == 0){
			  //$('#edit-field-student-status').val('_none');
			  $("#edit-field-student-status").removeAttr('disabled', true);
			} else {
				//$('#edit-field-student-status').val('_none');
				$("#edit-field-student-status").removeAttr('disabled', true);
				$("#edit-field-sewing-exit-code").removeAttr('disabled', true);
	    		$('.field--name-field-sewing-exit-code').addClass('hide');
			}
		}
		
	});

	$("#edit-field-student-status").on('change', function(event) {
		if($('#field_hidden_student_id').val()) {
			if(this.value == 0){
			//show status dropdown for all exit code other than passing examination (45)
			if($("#edit-field-sewing-exit-code").val() != PASSING_EXIT_CODE)
				$('.field--name-field-sewing-exit-code').removeClass('hide');	
			} else if(this.value == 1){
			  $("#edit-field-sewing-exit-code").removeAttr('disabled', true);
			  $('.field--name-field-sewing-exit-code').addClass('hide');
			} else {
			  $("#edit-field-sewing-exit-code").removeAttr('disabled', true);
			  $('.field--name-field-sewing-exit-code').addClass('hide');
			}	
		} else {
			if(this.value == 0){
			//show status dropdown for all exit code other than passing examination (45)
			if($("#edit-field-sewing-exit-code").val() != PASSING_EXIT_CODE)
				$('.field--name-field-sewing-exit-code').removeClass('hide');	
			} else if(this.value == 1){
			  $("#edit-field-sewing-exit-code").removeAttr('disabled', true);
			  $('.field--name-field-sewing-exit-code').addClass('hide');
			  $("#edit-field-sewing-exit-code").val('_none');
			} else {
			  $("#edit-field-sewing-exit-code").removeAttr('disabled', true);
			  $('.field--name-field-sewing-exit-code').addClass('hide');
			  $("#edit-field-sewing-exit-code").val('_none');
			}
		}
		
	});

	// trigger change event for dropdown
	$('#edit-field-sewing-course-code-list').trigger('change');
	$('#edit-field-sewing-exam-result').trigger('change');
	$("#edit-field-sewing-certificate-issued").trigger('change');
	$("#edit-field-student-status").trigger('change');

	//$('.field--name-field-sewing-exit-code').addClass('hide');
	// if($("#edit-field-sewing-exit-code").val() == PASSING_EXIT_CODE) {
	// 	$("#edit-field-student-status").trigger('change');
 //    }




// 	$('#edit-field-sewing-course-fee-received-0-value').on('blur', function() {
// 		var feedue = ($('#edit-field-sewing-course-fee-0-value').val()) ? $('#edit-field-sewing-course-fee-0-value').val() : 0;
// 		var feeReceived = ($('#edit-field-sewing-course-fee-received-0-value').val()) ? $('#edit-field-sewing-course-fee-received-0-value').val() : 0;

//     var outstanding = feedue - feeReceived;
//     $('#edit-field-sewing-course-fee-out-0-value').val(outstanding);
// });

$("#edit-field-sewing-district-selectlist").on('change', function(event) {
$.ajax({
		url: drupalSettings.path.baseUrl + 'get_towns_by_district/' + this.value,
		type: 'POST',
		error: function () {
			console.log("Error occurred");
		},
		success: function (response) {
			 if (response.status == 1) {
                if (response.data == "") {
                 var town_select = $('#edit-field-town-city');
					town_select.empty();
					town_select.html("<option value='_none'>- Select a value -</option>");   
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


$(".custom-manage-student .form-select").on('change', function(event) {
var selectedCourse =  $('#edit-field-sewing-course-code-list').val();
console.log(selectedCourse);
$.ajax({
		url: drupalSettings.path.baseUrl + 'get_sewing_courses_by_school/' + this.value,
		type: 'GET',
		error: function () {
			console.log("Error occurred");
		},
		success: function (response) {
			 if (response.status == 1) {
                if (response.data == "" || response.data == null) {
                    var course_select = $('#edit-field-sewing-course-code-list');
					course_select.empty();
					course_select.html("<option value='_none'>- Select a value -</option>");   
                } else {
                	var data = response.data;
                	var course_select = $('#edit-field-sewing-course-code-list');
					course_select.empty();
					course_select.html("<option value='_none'>- Select a value -</option>");
                	$.each(data, function(key, value) {  
                	if( selectedCourse && key == selectedCourse) {
						course_select.append($("<option></option>")
					                .attr("value",key)
					                .attr('selected', 'selected')
					                .text(value)); 	
					} else {  
				    	course_select.append($("<option></option>")
				                    .attr("value",key)
				                    .text(value));
                    }  
					});
					
                }
            }
		}
	});
});

$(".custom-manage-student .form-select").trigger('change');
	
});
// handling pop up fields
Drupal.behaviors.my_Behavior = {
  attach: function (context, settings) {
	  
		$(".node-manage-inventory-form .form-item-field-location select").on('change', function() {
			$.ajax({
				url: drupalSettings.path.baseUrl + 'get_ssi_user_by_location/' + this.value,
				type: 'GET',
				error: function () {
					console.log("Error occurred");
				},
				success: function (response) {
					 if (response.status == 1) {
						if (response.data == "") {
							
						} else {
							var data = response.data;
							var user_select = $('.form-item-field-sewing-ssi-user select');
							user_select.empty();
							
							user_select.html("<option value='_none'>- Select a value -</option>");
							$.each(data, function(key, value) {   
							user_select.append($("<option></option>")
											.attr("value",key)
											.text(value)); 
								
						});

						}
					}
				}
			});
		});
		var ssiUserInventory = jQuery(".node-manage-inventory-edit-form .form-item-field-sewing-ssi-user option:selected").val();
		$(".node-manage-inventory-edit-form .form-item-field-location select").on('change', function() {
			$.ajax({
				url: drupalSettings.path.baseUrl + 'get_ssi_user_by_location/' + this.value,
				type: 'GET',
				error: function () {
					console.log("Error occurred");
				},
				success: function (response) {
					 if (response.status == 1) {
						if (response.data == "") {
							
						} else {
							var data = response.data;
							var user_select = $('.form-item-field-sewing-ssi-user select');
							user_select.empty();
							
							user_select.html("<option value='_none'>- Select a value -</option>");
							$.each(data, function(key, value) {   
							user_select.append($("<option></option>")
											.attr("value",key)
											.text(value)); 
								
						});
						jQuery('.node-manage-inventory-edit-form .form-item-field-sewing-ssi-user option[value="'+ssiUserInventory+'"]').attr("selected", true);
						}
					}
				}
			});
		});
		jQuery("#edit-field-sewing-select-dealer").on('change', function() {
			var dealerNid = jQuery("#edit-field-sewing-select-dealer").val();
			if(dealerNid != '_none'){
					jQuery.ajax({
					url : drupalSettings.path.baseUrl+'sewing_school_dealer_details',
					type: 'POST',
					data: {dealerNid: dealerNid},
					success: function(response) {
						if(response) {
							var data = response.data;
							//console.log(data);
							//jQuery('.form-item-field-sewing-select-dealer').append('data');
		                	jQuery('#edit-field-sewing-dealer-details-0-value').empty();
		                	jQuery('#edit-field-sewing-dealer-details-0-value').text(data);
						} else {
							alert('Some error occurred. Please refresh the page and check again.');
							return false;
						}
					}
				});
			}else{
				jQuery('#edit-field-sewing-dealer-details-0-value').empty(); 
			}
		});
		
		jQuery("#edit-field-sewing-grade").on('change', function() {
			var gradeNid = jQuery("#edit-field-sewing-grade").val();
			if(gradeNid != '_none'){
					jQuery.ajax({
					url : drupalSettings.path.baseUrl+'sewing_school_course_count_by_grade',
					type: 'POST',
					data: {gradeNid: gradeNid},
					success: function(response) {
						if(response) {
							var data = response.data;
							console.log(data);
		                	jQuery('#edit-field-no-of-courses-0-value').empty();
		                	jQuery('#edit-field-no-of-courses-0-value').val(data);
						} else {
							alert('Some error occurred. Please refresh the page and check again.');
							return false;
						}
					}
				});
			}else{
				jQuery('#edit-field-no-of-courses-0-value').empty();
			}
		});	

		jQuery(".form-item-field-town-city select").on('change', function() {
			var townId = jQuery(".form-item-field-town-city select").val();
			//alert(townId);
			//if(gradeNid != '_none'){
				jQuery.ajax({
					url : drupalSettings.path.baseUrl+'sewing_get_school_by_town/'+townId,
					type: 'POST',
					//data: {gradeNid: gradeNid},
					success: function(response) {
						 if (response.status == 1) {
			                if (response.data == "" || response.data == null) {
			                 var school_select = $('#edit-field-sewing-school-code-list');
								school_select.empty();
								school_select.html("<option value='_none'>- Select a value -</option>");   
			                } else {
			                	var data = response.data;
			                	var school_select = $('#edit-field-sewing-school-code-list');
								school_select.empty();
								school_select.html("<option value='_none'>- Select a value -</option>");
			                	$.each(data, function(key, value) {   
							    school_select.append($("<option></option>")
							                    .attr("value",key)
							                    .text(value)); 
							        
							});

			                }
			            }
					}
				});
			//}else{
			//	jQuery('#edit-field-no-of-courses-0-value').empty();
			//}
		});	


		jQuery('.form-item-field-sewing-item-group .form-select').on('change', function(e) {
  		$.ajax({
		url: drupalSettings.path.baseUrl + 'get_sewing_items_by_itemgroup/' + this.value,
		type: 'GET',
		error: function () {
			console.log("Error occurred");
		},
		success: function (response) {
			 if (response.status == 1) {
                if (response.data == "" || response.data == null) {
                    var item_select = $('.form-item-field-sewing-item-name .form-select');
					item_select.empty();
					
					item_select.html("<option value='_none'>- Select a value -</option>");
                } else {
                	var data = response.data;
                	var item_select = $('.form-item-field-sewing-item-name .form-select');
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


	jQuery(".addsewingschool-row").click(function(e){
		e.preventDefault();
		var locationIds = $(this).data('id');
		$.ajax({
		url: drupalSettings.path.baseUrl + 'get_schools_by_locationIds/' + locationIds,
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
                	school_select = school_select + '<select  name="field_sewing_frd_to[]" class="form-select required form-control" required="required" aria-required="true"><option value="_none" selected="selected">- Select a value -</option>';
					//school_select.empty();
					
					//school_select.html("<option value='_none'>- Select a value -</option>");
                	$.each(data, function(key, value) {
                	school_select = school_select +  '<option value="'+key+'">'+value+'</option>'  
				    
				});
                	school_select = school_select + '</select>';
                	
                } 
            }
          $("#sewingschool-wrapper-repater").append('<div><div class="form-item"><label for="edit-field-sewing-frd-to" class="js-form-required form-required">School Code</label>' + school_select + '</div><div class="form-item"><label for="edit" class="js-form-required form-required">Quantity</label><input class="NUMERIC form-text required form-control" name="field_sewing_item_sent[]" type="text" value=""  maxlength="10" required="required" aria-required="true"></div><div class="form-item"><label for="edit-field-sewing-courier-number" class="js-form-required form-required">Remark</label><input class="form-text required form-control" type="text" id="edit-field-sewing-courier-number-0--J2dg-Kmms9A" name="field_sewing_courier_number[]" value="" size="60" maxlength="10" required="required" aria-required="true"><div id="edit-field-sewing-courier-number-0--J2dg-Kmms9A--description" class="description">*Courior No./Docket No./Other</div></div>&nbsp;<a href="javascript:void(0);" class="remSchoolRow">Remove</a></div>');
		}
	}); 
		//$("#school-wrapper-repater").append('<div style="margin-top:50px"><label for="edit-field-sewing-frd-to" class="js-form-required form-required">School Code</label><select  name="field_sewing_frd_to[]" class="form-select required form-control" required="required" aria-required="true"><option value="_none" selected="selected">- Select a value -</option><option value="1">School 1</option><option value="2">School 2</option></select><label for="edit" class="js-form-required form-required">Quantity</label><input class="NUMERIC form-text required form-control" name="field_sewing_item_sent[]" type="text" value=""  maxlength="10" required="required" aria-required="true"> &nbsp; <a href="javascript:void(0);" class="remSchoolRow">Remove</a></div>');
	});

	jQuery("#sewingschool-wrapper-repater").on('click','.remSchoolRow',function(){
        $(this).parent().remove();
    });	
		
	}
}

function resetPage(pageId) {
	if(pageId == 1) {
		$(".form-item-location .form-select").val('_none');
		$(".form-item-schooltype .form-select").val('_none');
		$(".form-item-from-date .form-date").val('');
		$(".form-item-to-date .form-date").val('');
		window.location.href = "school-revenue-report";
	}
	if(pageId == 2) {
		$(".form-item-location .form-select").val('_none');
		window.location.href = "workshop-activity-report";
	}

	if(pageId == 3) {
		$(".form-item-location .form-select").val('_none');
		$(".form-item-from-date .form-date").val('');
		$(".form-item-to-date .form-date").val('');
		window.location.href = "school-type-wise-report";
	}
}
