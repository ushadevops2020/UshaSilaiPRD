jQuery(function ($) {
	$('div.bulk-school-checkbox').click(function(){  
		var schoolNid = $(this).attr('node-id');
	    $(this, "div.bulk-school-checkbox").empty();
	    $(this, "div.bulk-school-checkbox").append ( "<input name='check_school_nid' type='checkbox' value='" + schoolNid + "' />" );
	    $(this, "div.bulk-school-checkbox").removeAttr('node-id');
	});
	// For Training Bulk Checkbox 
	$('div.bulk-training-checkbox').click(function(){  
		var trainingNid = $(this).attr('node-id');
	    $(this, "div.bulk-training-checkbox").empty();
	    $(this, "div.bulk-training-checkbox").append ( "<input name='check_training_nid' type='checkbox' value='" + trainingNid + "' />" );
	    $(this, "div.bulk-training-checkbox").removeAttr('node-id');
	});
	// For Student Bulk Checkbox 
	$('div.sewing-bulk-student-checkbox').click(function(){  
		var studentNid = $(this).attr('node-id');
	    $(this, "div.sewing-bulk-student-checkbox").empty();
	    $(this, "div.sewing-bulk-student-checkbox").append ( "<input name='check_student_nid' type='checkbox' value='" + studentNid + "' />" );
	    $(this, "div.sewing-bulk-student-checkbox").removeAttr('node-id');
	});
	// For Certificate Management Bulk Checkbox 
	$('div.sewing-bulk-certificate-checkbox').click(function(){  
		var studentNid = $(this).attr('node-id');
	    $(this, "div.sewing-bulk-certificate-checkbox").empty();
	    $(this, "div.sewing-bulk-certificate-checkbox").append ( "<input name='check_student_nid' type='checkbox' value='" + studentNid + "' />" );
	    $(this, "div.sewing-bulk-certificate-checkbox").removeAttr('node-id');
	});
	// Sewing School Bulk terminate Function
	$("button#bulk-school-terminate").click(function(){
        var schoolNids = [];
        $.each($("input[name='check_school_nid']:checked"), function(){            
            schoolNids.push($(this).val());
        });
        if (schoolNids.length === 0) {
		    alert('Please select atleast One row.');
		}else {
		 	$.confirm({
			    title: 'Terminate School?',
			    content: 'Are you sure to Terminate School?',
			    autoClose: 'Close|12000',
			    buttons: {
			        deleteUser: {
			            text: 'Terminate',
			            action: function () {
			            	jQuery.ajax({
								url: drupalSettings.path.baseUrl + 'bulk-school-terminate-status',
								type: 'POST',
								data: {schoolNids:schoolNids},
								async : true,
								dataType : "json",
								success: function (response) {
									console.log(response);
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
	// Sewing School Bulk Approve Function
	$("button#bulk-school-approve").click(function(){
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
			            text: 'Approve',
			            action: function () {
			            	jQuery.ajax({
								url: drupalSettings.path.baseUrl + 'bulk-school-approve-status',
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
    // Sewing School On Hold Function
	$("button#bulk-school-on-hold").click(function(){
        var schoolNids = [];
        $.each($("input[name='check_school_nid']:checked"), function(){            
            schoolNids.push($(this).val());
        });
        if (schoolNids.length === 0) {
		    alert('Please select atleast One row.');
		}else {
			//alert(schoolNids);
		 	$.confirm({
			    title: 'On-Hold School?',
			    content: 'Are you sure to On-Hold School?',
			    autoClose: 'Close|12000',
			    buttons: {
			        approveSchool: {
			            text: 'On-Hold',
			            action: function () {
			            	jQuery.ajax({
								url: drupalSettings.path.baseUrl + 'bulk-school-on-hold-status',
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
    // Sewing Training Bulk terminate Function
	$("button#bulk-training-terminate").click(function(){
        var trainingNids = [];
        $.each($("input[name='check_training_nid']:checked"), function(){            
            trainingNids.push($(this).val());
        });
        if (trainingNids.length === 0) {
		    alert('Please select atleast One row.');
		}else {
			//console.log(trainingNids);
		 	$.confirm({
			    title: 'Delete Training?',
			    content: 'Are you sure to Delete Training?',
			    autoClose: 'Close|12000',
			    buttons: {
			        deleteUser: {
			            text: 'Delete',
			            action: function () {
			            	jQuery.ajax({
								url: drupalSettings.path.baseUrl + 'bulk-traning-terminate',
								type: 'POST',
								data: {trainingNids:trainingNids},
								async : true,
								dataType : "json",
								success: function (response) {
									//console.log(response);
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

    // Single Training Terminate function
    jQuery('span.terminate_training a').on('click', function(){
    	var trainingNids = [];
	    trainingNids.push ($(this).attr('node-id'));
		$.confirm({
		    title: 'Delete',
		    content: 'Are you sure to Delete?',
		    autoClose: 'Close|12000',
		    buttons: {
		        deleteUser: {
		            text: 'Delete',
		            action: function () {
		            	jQuery.ajax({
							url: drupalSettings.path.baseUrl + 'bulk-traning-terminate',
							type: 'POST',
							data: {trainingNids:trainingNids},
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
	// Single Attendee Terminate function
    jQuery('span.terminate_attendee a').on('click', function(){
    	var attendeeNids = [];
	    attendeeNids.push ($(this).attr('node-id')); 
		$.confirm({
		    title: 'Delete Attendee?',
		    content: 'Are you sure to Delete Attendee?',
		    autoClose: 'Close|12000',
		    buttons: {
		        deleteUser: {
		            text: 'Delete',
		            action: function () {
		            	jQuery.ajax({
							url: drupalSettings.path.baseUrl + 'bulk-attendee-terminate',
							type: 'POST',
							data: {attendeeNids:attendeeNids},
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
	// Student Bulk Result function
    $("button#sewing-student-result").click(function(){
        var studentNids = [];
        $.each($("input[name='check_student_nid']:checked"), function(){            
            studentNids.push($(this).val());
        });
        if (studentNids.length === 0) {
		    alert('Please select atleast One row.');
		}else {
		 	$.confirm({
			    title: 'Result',
			    content: '' +
			    '<form action="" class="formName">' +
				    '<div class="form-group">' +
					    '<label>Exam Result</label>' +
					    '<select id="exam-result-data" class="form-control" required>' +
					    	'<option value="">- Select -</option>' +
					    	'<option value="1">Pass</option>' +
					    	'<option value="2">Fail</option>' +
					    '</select><br>' +
					    '<!--label>Result Date</label>' +
					    '<input type="date" id="exam-result-date" class="form-control" required /><br-->' +
					    '<label>Grades</label>' +
					    '<select id="exam-result-grade" class="form-control" required>' +
					    	'<option value="">- Select -</option>' +
					    	'<option value="43">First</option>' +
					    	'<option value="44">Second</option>' +
					    	'<option value="45">Third</option>' +
					    '</select>' +
				    '</div>' +
			    '</form>',
			    buttons: {
			        formSubmit: {
			            text: 'Submit',
			            btnClass: 'btn-blue',
			            action: function () {
			                var examResult = this.$content.find('#exam-result-data').val();
			                //var examResultDate = this.$content.find('#exam-result-date').val();
			                var examResultGrade = this.$content.find('#exam-result-grade').val();
			                if(!examResult){
			                    $.alert('Please select Exam Result');
			                    return false;
			                }
			                // if(!examResultDate){
			                //     $.alert('Please select Result Date');
			                //     return false;
			                // }
			                if(examResult == 1){
			                	if(!examResultGrade){
				                    $.alert('Please select Grade');
				                    return false;
				                }
			                }
			                jQuery.ajax({
								url: drupalSettings.path.baseUrl + 'bulk-student-bulk-result-update',
								type: 'POST',
								data: {examResult:examResult, examResultGrade:examResultGrade, studentNids:studentNids},
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
    // Student Bulk Raise a Machine Request
    $("button#sewing-raise-machine-request").click(function(){
        var studentNids = [];
        $.each($("input[name='check_student_nid']:checked"), function(){            
            studentNids.push($(this).val());
        });
        if (studentNids.length === 0) {
		    alert('Please select atleast One row.');
		}else {
		 	$.confirm({
			    title: 'Raise a Machine Request',
			    content: '' +
			    '<form action="" class="formName1">' +
				    '<div class="form-group">' +
					    '<label>Time to Buy</label>' +
					    '<select id="time-to-buy-machine" class="form-control" required>' +
					    	'<option value="">- Select -</option>' +
							'<option value="1">Immediate</option>' +
							'<option value="2">Within two months</option>' +
							'<option value="3">After Completing the course</option>' +
							'<option value="4">After three months</option>' +
							'<option value="5">After four months</option>' +
							'<option value="6">After six months</option>' +
							'<option value="7">After nine months</option>' +
					    '</select>' +
				    '</div>' +
			    '</form>',
			    buttons: {
			        formSubmit: {
			            text: 'Submit',
			            btnClass: 'btn-blue',
			            action: function () {
			                var timeToBuy = this.$content.find('#time-to-buy-machine').val();
			                if(!timeToBuy){
			                    $.alert('Please select Time To Buy');
			                    return false;
			                }
			                jQuery.ajax({
								url: drupalSettings.path.baseUrl + 'bulk-student-raise-machine-request',
								type: 'POST',
								data: {timeToBuy:timeToBuy, studentNids:studentNids},
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
    // Student Bulk Certificate Issued
    $("button#sewing-bulk-certificate-issued").click(function(){
        var studentNids = [];
        $.each($("input[name='check_student_nid']:checked"), function(){            
            studentNids.push($(this).val());
        });
        if (studentNids.length === 0) {
		    alert('Please select atleast One row.');
		}else {
		 	$.confirm({
			    title: 'Certificate Issued',
			    content: '' +
			    '<form action="" class="formName2">' +
				    '<div class="form-group">' +
				    	'<label>Date of Certificate Issued</label>' +
					    '<input type="date" id="certificate-issued-date" class="form-control" required /><br>' +
				    '</div>' +
			    '</form>',
			    buttons: {
			        formSubmit: {
			            text: 'Submit',
			            btnClass: 'btn-blue',
			            action: function () {
			                var certificateDate = this.$content.find('#certificate-issued-date').val();
			                if(!certificateDate){
			                    $.alert('Please select certificate issued date.');
			                    return false;
			                }
			                jQuery.ajax({
								url: drupalSettings.path.baseUrl + 'bulk-student-certificate_issued',
								type: 'POST',
								data: {certificateDate:certificateDate, studentNids:studentNids},
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
    
    // Student Bulk Certificate Print
    $("button#sewing-bulk-certificate-print").click(function(){
        var studentNids = [];
        $.each($("input[name='check_student_nid']:checked"), function(){            
            studentNids.push($(this).val());
        });
        if (studentNids.length === 0) {
		    alert('Please select atleast One row.');
		}else {
		 	$.confirm({
			    title: 'Certificate Print',
			    content: '' +
			    '<form action="" class="formName3">' +
				    '<div class="form-group">' +
				    	'<label>Date of Certificate Print</label>' +
					    '<input type="date" id="certificate-print-date" class="form-control" required /><br>' +
				    '</div>' +
			    '</form>',
			    buttons: {
			        formSubmit: {
			            text: 'Submit',
			            btnClass: 'btn-blue',
			            action: function () {
			                var certificateDate = this.$content.find('#certificate-print-date').val();
			                if(!certificateDate){
			                    $.alert('Please select certificate print date.');
			                    return false;
			                }
			                jQuery.ajax({
								url: drupalSettings.path.baseUrl + 'bulk-student-certificate_print',
								type: 'POST',
								data: {certificateDate:certificateDate, studentNids:studentNids},
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
    // School OnHold State to approve State
    jQuery('span.school_on_hold a').on('click', function(){
	    var schoolNid = $(this).attr('node-id');
		$.confirm({
		    title: 'On Hold School Approval?',
		    content: 'Are you sure to On Hold to Approved?',
		    autoClose: 'Close|10000',
		    buttons: {
		        deleteUser: {
		            text: 'Approved',
		            action: function () {
		            	jQuery.ajax({
							url: drupalSettings.path.baseUrl + 'sewing_school_onhold_to_approve/'+schoolNid,
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
	// School Terminated State to approve State
    jQuery('span.school_terminated a').on('click', function(){
	    var schoolNid = $(this).attr('node-id');
		$.confirm({
		    title: 'Terminated School Approval?',
		    content: 'Are you sure to Terminated to Approved?',
		    autoClose: 'Close|10000',
		    buttons: {
		        deleteUser: {
		            text: 'Approved',
		            action: function () {
		            	jQuery.ajax({
							url: drupalSettings.path.baseUrl + 'sewing_school_terminate_to_approve/'+schoolNid,
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
	// Sewing School Select All Function
    $('button#bulk-school-select-all').click(function(){  
	    $("input[name='check_school_nid']").prop('checked', true);
	});
	// Sewing School Deselect All Function
	$('button#bulk-school-deselect-all').click(function(){  
	    $("input[name='check_school_nid']").prop('checked', false); 
	});
	// Sewing Training Select All Function
    $('button#bulk-training-select-all').click(function(){  
	    $("input[name='check_training_nid']").prop('checked', true);
	});
	// Sewing Training Deselect All Function
	$('button#bulk-training-deselect-all').click(function(){  
	    $("input[name='check_training_nid']").prop('checked', false);
	});
	// Sewing Student Select All Function
    $('button#sewing-bulk-student-select-all').click(function(){  
	    $("input[name='check_student_nid']").prop('checked', true);
	});
	// Sewing Student Deselect All Function
	$('button#sewing-bulk-student-unselect-all').click(function(){  
	    $("input[name='check_student_nid']").prop('checked', false);
	});
	// Sewing Certificate Management Select All Function
    $('button#sewing-bulk-certificate-select-all').click(function(){  
	    $("input[name='check_student_nid']").prop('checked', true);
	});
	// Sewing Certificate Management Deselect All Function
	$('button#sewing-bulk-certificate-unselect-all').click(function(){  
	    $("input[name='check_student_nid']").prop('checked', false);
	});
});

jQuery( document ).ready(function($) {
	if($('body.path-manage-school, body.path-ssi-manage-school').is(':visible')){
		$(".bulk-school-checkbox").click();
		$('div.bulk-school-checkbox').off('click');
	}
	if($('body.path-manage-workshop-activity').is(':visible')){
		$(".bulk-training-checkbox").click();
		$('div.bulk-training-checkbox').off('click');
	}
	if($('body.path-manage-students-ssi').is(':visible')){
		$(".sewing-bulk-student-checkbox").click();
		$('div.sewing-bulk-student-checkbox').off('click');
	}
	if($('body.path-manage-certificate-ssi').is(':visible')){
		$(".sewing-bulk-certificate-checkbox").click();
		$('div.sewing-bulk-certificate-checkbox').off('click');
	}
});