jQuery(function ($) {
	// click function for check box
	$('div.silai-bulk-school-checkbox').click(function(){  
		var schoolNid = $(this).attr('node-id');
	    $(this, "div.silai-bulk-school-checkbox").empty();
	    $(this, "div.silai-bulk-school-checkbox").append ( "<input name='check_school_nid' type='checkbox' value='" + schoolNid + "' />" );
	    $(this, "div.silai-bulk-school-checkbox").removeAttr('node-id');
	});
	// click function for check box
	$('div.silai-bulk-learner-checkbox').click(function(){  
		var learnerNid = $(this).attr('node-id');
	    $(this, "div.silai-bulk-learner-checkbox").empty();
	    $(this, "div.silai-bulk-learner-checkbox").append ( "<input name='check_learner_nid' type='checkbox' value='" + learnerNid + "' />" );
	    $(this, "div.silai-bulk-learner-checkbox").removeAttr('node-id');
	});
	$('div.silai-bulk-monthly-mis-checkbox').click(function(){  
		var monthlyMisId = $(this).attr('node-id');
	    $(this, "div.silai-bulk-monthly-mis-checkbox").empty();
	    $(this, "div.silai-bulk-monthly-mis-checkbox").append ( "<input name='check_monthly_mis_id' type='checkbox' value='" + monthlyMisId + "' />" );
	    $(this, "div.silai-bulk-monthly-mis-checkbox").removeAttr('node-id');
	});
	// Sewing School Bulk Approve Function
	$("button#silai-bulk-school-approve").click(function(){
        var schoolNids = [];
        $.each($("input[name='check_school_nid']:checked"), function(){            
            schoolNids.push($(this).val());
        });
        if (schoolNids.length === 0) {
		    alert('Please select atleast One row.');
		}else {
		 	$.confirm({
			    title: 'Approve School?',
			    content: 'Are you sure to Approve School?',
			    autoClose: 'Close|12000',
			    buttons: {
			        approveSchool: {
			            text: 'Approve School',
			            action: function () {
			            	jQuery.ajax({
								url: drupalSettings.path.baseUrl + 'silai-school-bulk-approval-process',
								type: 'POST',
								data: {schoolNids:schoolNids},
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
			        	window.location.reload();
			        }
			    }
			});
		}
    });
    // Sewing School Bulk Partially Function
	$("button#silai-bulk-school-partially-closed").click(function(){
        var schoolNids = [];
        $.each($("input[name='check_school_nid']:checked"), function(){            
            schoolNids.push($(this).val());
        });
        if (schoolNids.length === 0) {
		    alert('Please select atleast One row.');
		}else {
		 	$.confirm({
			    title: 'Partially Close School?',
			    content: 'Are you sure to Partially Close School?',
			    autoClose: 'Close|12000',
			    buttons: {
			        approveSchool: {
			            text: 'Partially Close School',
			            action: function () {
			            	jQuery.ajax({
								url: drupalSettings.path.baseUrl + 'silai-school-bulk-partially-close',
								type: 'POST',
								data: {schoolNids:schoolNids},
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
			        	window.location.reload();
			        }
			    }
			});
		}
    });
    // Sewing School Bulk Fully Function
	$("button#silai-bulk-school-fully-closed").click(function(){
        var schoolNids = [];
        $.each($("input[name='check_school_nid']:checked"), function(){            
            schoolNids.push($(this).val());
        });
        if (schoolNids.length === 0) {
		    alert('Please select atleast One row.');
		}else {
		 	$.confirm({
			    title: 'Fully Close School?',
			    content: 'Are you sure to Fully Close School?',
			    autoClose: 'Close|12000',
			    buttons: {
			        approveSchool: {
			            text: 'Fully Close School',
			            action: function () {
			            	jQuery.ajax({
								url: drupalSettings.path.baseUrl + 'silai-school-bulk-fully-close',
								type: 'POST',
								data: {schoolNids:schoolNids},
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
			        	window.location.reload();
			        }
			    }
			});
		}
    });
    // School partially Close State to approve State
    jQuery('span.school_partially_closed a').on('click', function(){
	    var schoolNid = $(this).attr('node-id');
		$.confirm({
		    title: 'Partially close school approval?',
		    content: 'Are you sure to Partially Close to Approved?',
		    autoClose: 'Close|10000',
		    buttons: {
		        deleteUser: {
		            text: 'Approved',
		            action: function () {
		            	jQuery.ajax({
							url: drupalSettings.path.baseUrl + 'silai_school_partially_close_to_approve/'+schoolNid,
							type: 'POST',
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
		        	window.location.reload();
		        }
		    }
		});   	
	})
	// Silai Learner Bulk Inactive Function
	$("button#silai-bulk-learner-inactive").click(function(){
        var learnerNids = [];
        $.each($("input[name='check_learner_nid']:checked"), function(){            
            learnerNids.push($(this).val());
        });
        if (learnerNids.length === 0) {
		    alert('Please select atleast One row.');
		}else {
		 	$.confirm({
			    title: 'Inactive Learners?',
			    content: 'Are you sure to Inactive Learner?',
			    autoClose: 'Close|12000',
			    buttons: {
			        approveSchool: {
			            text: 'Inactive Learner',
			            action: function () {
			            	jQuery.ajax({
								url: drupalSettings.path.baseUrl + 'silai_bulk_learner_inactive_process',
								type: 'POST',
								data: {learnerNids:learnerNids},
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
			        	window.location.reload();
			        }
			    }
			});
		}
    });
    // Learner Bulk Course Completion Date
    $("button#silai-bulk-learner-course-completion").click(function(){
        var learnerNids = [];
        $.each($("input[name='check_learner_nid']:checked"), function(){            
            learnerNids.push($(this).val());
        });
        if (learnerNids.length === 0) {
		    alert('Please select atleast One row.');
		}else {
		 	$.confirm({
			    title: 'Course Completion',
			    content: '' +
			    '<form action="" class="formName2">' +
				    '<div class="form-group">' +
				    	'<label>Date of Course Completion</label>' +
					    '<input type="date" id="course-completion-date" class="form-control" required />' +
				    '</div>' +
			    '</form>',
			    buttons: {
			        formSubmit: {
			            text: 'Submit',
			            btnClass: 'btn-blue',
			            action: function () {
			                var courseCompletionDate = this.$content.find('#course-completion-date').val();
			                if(!courseCompletionDate){
			                    $.alert('Please select course completion date.');
			                    return false;
			                }
			                jQuery.ajax({
								url: drupalSettings.path.baseUrl + 'bulk_learner_course_completion_date',
								type: 'POST',
								data: {courseCompletionDate:courseCompletionDate, learnerNids:learnerNids},
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
			        cancel: function () {
			            window.location.reload();
			        },
			    },
			});
		}
    });
    // Learner Bulk Certificate Issued
    $("button#silai-bulk-learner-certificate-issue").click(function(){
        var learnerNids = [];
        $.each($("input[name='check_learner_nid']:checked"), function(){            
            learnerNids.push($(this).val());
        });
        if (learnerNids.length === 0) {
		    alert('Please select atleast One row.');
		}else {
		 	$.confirm({
			    title: 'Certificate Issued',
			    content: '' +
			    '<form action="" class="formName1">' +
				    '<div class="form-group">' +
				    	'<label>Have you received certificate?</label>' +
					    '<select id="received_certificate" class="form-control" required>' +
					    	'<option value="">- Select -</option>' +
							'<option value="0">No</option>' +
							'<option value="1">Yes</option>' +
					    '</select>' +
				    '</div>' +
			    '</form>',
			    buttons: {
			        formSubmit: {
			            text: 'Submit',
			            btnClass: 'btn-blue',
			            action: function () {
			                var receivedCertificate = this.$content.find('#received_certificate').val();
			                if(!receivedCertificate){
			                    $.alert('Please select received certificate.');
			                    return false;
			                }
			                jQuery.ajax({
								url: drupalSettings.path.baseUrl + 'bulk_learner_received_certificate',
								type: 'POST',
								data: {receivedCertificate:receivedCertificate, learnerNids:learnerNids},
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
			        cancel: function () {
			            window.location.reload();
			        },
			    },
			});
		}
    });
	// Silai Learner Bulk delete
	$("button#silai-bulk-learner-delete").click(function(){
        var learnerNids = [];
        $.each($("input[name='check_learner_nid']:checked"), function(){            
            learnerNids.push($(this).val());
        });
        if (learnerNids.length === 0) {
		    alert('Please select atleast One row.');
		}else {
		 	$.confirm({
			    title: 'Delete Learners?',
			    content: 'Are you sure to Delete Learner?',
			    autoClose: 'Close|12000',
			    buttons: {
			        approveSchool: {
			            text: 'Delete Learner',
			            action: function () {
			            	jQuery.ajax({
								url: drupalSettings.path.baseUrl + 'silai_bulk_learner_delete',
								type: 'POST',
								data: {learnerNids:learnerNids},
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
			        	window.location.reload();
			        }
			    }
			});
		}
    });
	// Silai Monthly MIS Bulk delete
	$("button#silai-bulk-monthly-mis-delete").click(function(){
        var monthlyMISids = [];
        $.each($("input[name='check_monthly_mis_id']:checked"), function(){            
            monthlyMISids.push($(this).val());
        });
        if (monthlyMISids.length === 0) {
		    alert('Please select atleast One row.');
		}else {
		 	$.confirm({
			    title: 'Delete Monthly/Quarterly MIS',
			    content: 'Are you sure to Delete Monthly/Quarterly MIS?',
			    autoClose: 'Close|12000',
			    buttons: {
			        approveSchool: {
			            text: 'Delete MIS',
			            action: function () {
			            	jQuery.ajax({
								url: drupalSettings.path.baseUrl + 'silai_bulk_monthly_mis_delete',
								type: 'POST',
								data: {monthlyMISids:monthlyMISids},
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
			        	window.location.reload();
			        }
			    }
			});
		}
    });
	// Silai School Select All Function
    $('button#silai-bulk-school-select-all').click(function(){  
	    $("input[name='check_school_nid']").prop('checked', true);
	});
	// Silai School Deselect All Function
	$('button#silai-bulk-school-deselect-all').click(function(){  
	    $("input[name='check_school_nid']").prop('checked', false);
	});
	// Silai Learner Select All Function
    $('button#silai-bulk-learner-select-all').click(function(){  
	    $("input[name='check_learner_nid']").prop('checked', true);
	});
	// Silai Learner Deselect All Function
	$('button#silai-bulk-learner-deselect-all').click(function(){  
	    $("input[name='check_learner_nid']").prop('checked', false);
	});
	// Silai Monthly MIS Select All Function
    $('button#silai-bulk-monthly-mis-select-all').click(function(){  
	    $("input[name='check_monthly_mis_id']").prop('checked', true);
	});
	// Silai Monthly MIS Deselect All Function
	$('button#silai-bulk-monthly-mis-deselect-all').click(function(){  
	    $("input[name='check_monthly_mis_id']").prop('checked', false);
	});
});
jQuery( document ).ready(function($) {
	if($('body.path-silai-manage-school, body.path-silai-manage-school-pc, body.path-silai-manage-school-ngo').is(':visible')){
		$(".silai-bulk-school-checkbox").click();
	}
    SchoolListCheckBoxDesable();
    if($('body.path-ho-learners-listing, body.path-learners-listing, body.path-pc-learners-listing').is(':visible')){
		$(".silai-bulk-learner-checkbox").click();
	}
    LearnerListCheckBoxDesable();
	if($('body.path-ho-monthly-quarterly-mis-list').is(':visible')){
		$(".silai-bulk-monthly-mis-checkbox").click();
	}
    MonthlyMISListCheckBoxDisable();
});
function SchoolListCheckBoxDesable(){
	$('div.silai-bulk-school-checkbox').off('click');
}
function LearnerListCheckBoxDesable(){
	$('div.silai-bulk-learner-checkbox').off('click');
}
function MonthlyMISListCheckBoxDisable(){
	$('div.silai-bulk-monthly-mis-checkbox').off('click');
}