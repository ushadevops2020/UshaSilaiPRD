jQuery(function ($) {
	var state = jQuery('#edit-swr-state').val();
	var schoolCode = jQuery('#edit-swr-school-code').val();
	var studentFeestatus = jQuery('#edit-swr-student-fee').prop("checked");
	// alert(studentFeestatus);
	if(schoolCode && studentFeestatus) {
		studentDetails(schoolCode);
	}
	var feeId = jQuery('#hidden-fee-id').val();
	if(feeId != '') {
		$('#reset-revenue-type').hide();
	}	
	jQuery("#edit-swr-state").on('change', function() {
		jQuery('#edit-swr-school-code').val('');
		jQuery('#edit-swr-town').val('');
		jQuery('#edit-swr-school-type').val('');
    	jQuery('#edit-swr-school-grade').val('');
    	jQuery('#edit-swr-sap-code').val('');
    	jQuery('#edit-swr-school-admin').val('');
    	jQuery('#edit-swr-no-student').val('');		                	
    	jQuery('#edit-swr-course').val('');
    	jQuery('.revenue-head-class input:radio').prop("checked", false);
    	jQuery('.form-item-swr-revenue-head-value').hide();
    	jQuery('#edit-swr-revenue-head-value').val('');
    	jQuery("#edit-swr-student-fee").prop('checked',false);
    	jQuery('#edit-swr-student-table').hide();
    	var divContainer = document.getElementById("studentListData");
        divContainer.innerHTML = "";
    	jQuery('#edit-swr-total-fee-entry').val('');
    	jQuery('#edit-swr-total-pay-to-uil').val('');
    	jQuery('#edit-swr-tax').val('');
    	
    	
		  jQuery.ajax({
			url: drupalSettings.path.baseUrl + 'get-sewing-towen-by-state/' + this.value,
			type: 'GET',
			error: function () {
				console.log("Error occurred");
			},
			success: function (response) {
				 if (response.status == 1) {
	                if (response.data == "") {
	                    var location_select = $('#edit-swr-town');
						location_select.empty();
						location_select.html("<option value=''>- Select a value -</option>");
	                } else {
	                	var data = response.data;
	                	var location_select = jQuery('#edit-swr-town');
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

	jQuery("#edit-swr-state").on('change', function() {
		var town = jQuery('#edit-swr-town').val();
	  	jQuery.ajax({
			url: drupalSettings.path.baseUrl + 'get-sewing-school-code',
			type: 'POST',
			data: {state:this.value, town:town},
			error: function () {
				console.log("Error occurred");
			},
			success: function (response) {
				 if (response.status == 1) {
	                if (response.data == "") {
	                    var location_select = $('#edit-swr-school-code');
						location_select.empty();
						location_select.html("<option value=''>- Select a value -</option>");
	                } else {
	                	var data = response.data;
	                	var location_select = jQuery('#edit-swr-school-code');
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

	jQuery("#edit-swr-town").on('change', function() {
		jQuery('#edit-swr-school-code').val('');
		jQuery('#edit-swr-school-type').val('');
    	jQuery('#edit-swr-school-grade').val('');
    	jQuery('#edit-swr-sap-code').val('');
    	jQuery('#edit-swr-school-admin').val('');
    	jQuery('#edit-swr-no-student').val('');		                	
    	jQuery('#edit-swr-course').val('');
    	jQuery('.revenue-head-class input:radio').prop("checked", false);
    	jQuery('.form-item-swr-revenue-head-value').hide();
    	jQuery('#edit-swr-revenue-head-value').val('');
    	jQuery("#edit-swr-student-fee").prop('checked',false);
    	var divContainer = document.getElementById("studentListData");
        divContainer.innerHTML = "";
        jQuery('#edit-swr-total-fee-entry').val('');
    	jQuery('#edit-swr-total-pay-to-uil').val('');
    	jQuery('#edit-swr-tax').val('');
		var state = jQuery('#edit-swr-state').val();
	  	jQuery.ajax({
			url: drupalSettings.path.baseUrl + 'get-sewing-school-code',
			type: 'POST',
			data: {state:state, town:this.value},
			error: function () {
				console.log("Error occurred");
			},
			success: function (response) {
			 	if (response.status == 1) {
	                if (response.data == "") {
	                    var location_select = $('#edit-swr-school-code');
						location_select.empty();
						location_select.html("<option value=''>- Select a value -</option>");
	                } else {
	                	var data = response.data;
	                	var location_select = jQuery('#edit-swr-school-code');
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
	jQuery('.form-item-swr-school-type-id').hide();
	//Get school detials by school code
	jQuery("#edit-swr-school-code").on('change', function() {
		jQuery("#edit-swr-student-fee").prop('checked',false);
		var divContainer = document.getElementById("studentListData");
        divContainer.innerHTML = "";
        jQuery('#edit-swr-total-fee-entry').val('');
    	jQuery('#edit-swr-no-student').val('');
    	jQuery('#edit-swr-course').val('');
    	jQuery('#edit-swr-total-fee-entry').val('');
    	jQuery('#edit-swr-total-pay-to-uil').val('');
    	jQuery('#edit-swr-tax').val('');
    	jQuery('.revenue-head-class input:radio').prop("checked", false);
    	jQuery('.form-item-swr-revenue-head-value').hide();
    	jQuery('#edit-swr-revenue-head-value').val('');
		var schoolCode = jQuery("#edit-swr-school-code").val();
		if(schoolCode){
				jQuery.ajax({
				url : drupalSettings.path.baseUrl+'sewing-school-detials-by-school-code',
				type: 'POST',
				data: {schoolCode: schoolCode},
				success: function(response) {
					var data = response.data;
					if(response.status == 1) {
	                	jQuery('#edit-swr-school-type').empty();
	                	jQuery('#edit-swr-school-type').val(data[0]); 
	                	jQuery('#edit-swr-school-grade').empty();
	                	jQuery('#edit-swr-school-grade').val(data[1]);
	                	jQuery('#edit-swr-sap-code').empty();
	                	jQuery('#edit-swr-sap-code').val(data[2]);
	                	jQuery('#edit-swr-school-admin').empty();
	                	jQuery('#edit-swr-school-admin').val(data[3]);
	                	jQuery('#edit-swr-no-student').empty();
	                	jQuery('#edit-swr-no-student').val(data[4]);		                	
	                	jQuery('#edit-swr-course').empty();
	                	jQuery('#edit-swr-course').val(data[5]);
	                	jQuery('#edit-swr-school-type-id').empty();
	                	jQuery('#edit-swr-school-type-id').val(data['schoolTypeId']);
	                	//alert(data['schoolTypeId']);
	                	if(data['schoolTypeId']!= SCHOOL_TYPE_COMPANY_RUN){
	                		jQuery('#edit-swr-payment-type input:radio:last').parent().addClass('revenue_last_payment_type');
	                		jQuery('.revenue_last_payment_type').hide();
	                		jQuery('.form-item-swr-school-type-id').hide();
	                	}else{
	                		jQuery('.revenue_last_payment_type').show();
	                		jQuery('.form-item-swr-school-type-id').hide();
	                	}
	                	
					}
				}
			});
		} else {
			jQuery('#edit-swr-school-type').val('');
        	jQuery('#edit-swr-school-grade').val('');
        	jQuery('#edit-swr-sap-code').val('');
        	jQuery('#edit-swr-school-admin').val('');
        	jQuery('#edit-swr-no-student').val('');		                	
        	jQuery('#edit-swr-course').val('');
		}
	});

	jQuery('#edit-swr-student-fee').change(function () {
		jQuery(".revenue-head-class input:radio").prop('disabled',true);
                var sourceRecFeeObj = jQuery('.fee-received-class');
		var targetRecFeeObj = jQuery('#edit-swr-total-fee-entry');
		var sourceUILFeeObj = jQuery('.payment-to-uil-class');
		var targetUILFeeObj = jQuery('#edit-swr-total-pay-to-uil');
		calculateTotal(sourceRecFeeObj, targetRecFeeObj);
		calculateTotal(sourceUILFeeObj, targetUILFeeObj);
		calculateTotalTax(sourceUILFeeObj);
		var schoolCode = jQuery('#edit-swr-school-code').val();
        if (this.checked) {
        	if(schoolCode) {
        		studentDetails(schoolCode);
			} else {
				alert('Please select School Code.');
				jQuery("#edit-swr-student-fee").prop('checked',false);
			}	
            
        } else {
        	var divContainer = document.getElementById("studentListData");
	        divContainer.innerHTML = "";
	jQuery('#edit-swr-total-fee-entry').val('0');
    	jQuery('#edit-swr-total-pay-to-uil').val('0');
            jQuery('#edit-swr-student-table').hide();
        }
    });
	jQuery(document).delegate('#reset-revenue-type', 'click', function() {
		
		var sourceRecFeeObj = jQuery('.fee-received-class');
		var targetRecFeeObj = jQuery('#edit-swr-total-fee-entry');
		var sourceUILFeeObj = jQuery('.payment-to-uil-class');
		var targetUILFeeObj = jQuery('#edit-swr-total-pay-to-uil');
		jQuery('.form-item-swr-revenue-head-value').hide();
		jQuery('#edit-swr-revenue-head-value').val('');
		calculateTotal(sourceRecFeeObj, targetRecFeeObj);
		calculateTotal(sourceUILFeeObj, targetUILFeeObj);
		calculateTotalTax(sourceUILFeeObj);
		jQuery('.revenue-head-class input:radio').prop("checked", false);
		//alert('hello');
		jQuery(".revenue-head-class input:radio").prop('disabled',false);
		jQuery("#edit-swr-student-fee").prop('disabled',false);
		jQuery("#edit-swr-student-fee").prop('checked',false);
		var divContainer = document.getElementById("studentListData");
        divContainer.innerHTML = "";
	});	
	jQuery(document).delegate('.revenue-head-class input:radio', 'click', function() {
		jQuery("#edit-swr-student-fee").prop('disabled',true);
		jQuery('#edit-swr-revenue-head-value').val('');
		var schoolCode = jQuery('#edit-swr-school-code').val();
		var affiliationNId = jQuery('#edit-swr-affiliation-nid').val();
		var renewalNId = jQuery('#edit-swr-renewal-nid').val();
		var sourceRecFeeObj = jQuery('.fee-received-class');
		var targetRecFeeObj = jQuery('#edit-swr-total-fee-entry');
		var sourceUILFeeObj = jQuery('.payment-to-uil-class');
		var targetUILFeeObj = jQuery('#edit-swr-total-pay-to-uil');
		var select = this.value;
		if(schoolCode) {
			jQuery.ajax({
				url : drupalSettings.path.baseUrl+'sewing-school-detials-by-school-code',
				type: 'POST',
				data: {schoolCode: schoolCode, select:select},
				success: function(response) {
					if(response.status == 1) {
						var data = response.data;
						jQuery('#max-fee-amount').text(data[6]);
						jQuery('#revenue-tax').text(data[7]);
						jQuery('#revenue-student-tax').text(data[8]);
						jQuery('.form-item-swr-revenue-head-value').hide();
						if(data['affilicationCon'] == 0 && select == affiliationNId) {
							alert("Affiliation fee cannot be entered before the affiliation date "+data['affilicationDate']);
							jQuery('.revenue-head-class input:radio').prop("checked", false);
							jQuery('.form-item-swr-revenue-head-value').hide();
						} else if(data['affilicationCon'] == 2 && select == affiliationNId) {
							alert(" Affiliation Fee already exist. Affiliation Fee can be entered only once.");
							jQuery('.revenue-head-class input:radio').prop("checked", false);
							jQuery('.form-item-swr-revenue-head-value').hide();
						}/*  else if(data['renewalCon'] == 0 && select == renewalNId) {
							alert("Renewal fee cannot be entered before the renewal date "+data['renewalDate']);
							jQuery('.revenue-head-class input:radio').prop("checked", false);
							jQuery('.form-item-swr-revenue-head-value').hide();
						} */ else if(data['renewalCon'] == 2 && select == renewalNId) {
							alert("You have not paid Afiliation Fee for this school yet. Kindly first pay affiliation fee.");
							jQuery('.revenue-head-class input:radio').prop("checked", false);
							jQuery('.form-item-swr-revenue-head-value').hide();
						} else if(select == affiliationNId ||select == renewalNId ) {
							jQuery('.form-item-swr-revenue-head-value').show();
							jQuery('#edit-swr-revenue-head-value').val(data[6]);
							jQuery('#edit-swr-revenue-head-value').prop('readonly',true);
						} else {
							jQuery('.form-item-swr-revenue-head-value').show();
							jQuery('#edit-swr-revenue-head-value').prop('readonly',false);
						}
						calculateTotal(sourceRecFeeObj, targetRecFeeObj);
						calculateTotal(sourceUILFeeObj, targetUILFeeObj);
						calculateTotalTax(sourceUILFeeObj);
			        }    
				}
			});
		} else {
			alert('Please select School Code.');
			jQuery('.revenue-head-class input:radio').prop("checked", false);
		}		
	});

	jQuery(document).delegate("#edit-swr-revenue-head-value", 'change', function() {
		var value 	= jQuery(this).val();
		var maxFee 	= jQuery('#max-fee-amount').text();
		var sourceRecFeeObj = jQuery('.fee-received-class');
		var targetRecFeeObj = jQuery('#edit-swr-total-fee-entry');
		var sourceUILFeeObj = jQuery('.payment-to-uil-class');
		var targetUILFeeObj = jQuery('#edit-swr-total-pay-to-uil');
		if((parseFloat(maxFee) > 0 && parseFloat(maxFee) < parseFloat(value))) {
			var message = "<p class='ajax-msg' style='color:red;'>Revenue Fee not more than Max fee!</p>";+maxFee 
		    jQuery(this).after(message);
			jQuery(this).val('');
			setTimeout(function(){ jQuery('p.ajax-msg').remove(); }, 1000);
	        return false;
		} else {
			calculateTotal(sourceRecFeeObj, targetRecFeeObj);
			calculateTotal(sourceUILFeeObj, targetUILFeeObj);
			calculateTotalTax(sourceUILFeeObj);
		}	
	});

	jQuery(document).delegate(".fee-received-class", 'change', function() {
		var value 	= jQuery(this).val();
		var blFee 	= jQuery(this).attr('data-b-fee');
		var uilPer 	= jQuery(this).attr('data-uil-per');
		var key     = jQuery(this).attr('data-col');
		var sourceRecFeeObj = jQuery('.fee-received-class');
		var targetRecFeeObj = jQuery('#edit-swr-total-fee-entry');
		var sourceUILFeeObj = jQuery('.payment-to-uil-class');
		var targetUILFeeObj = jQuery('#edit-swr-total-pay-to-uil');
		//alert('hello');
		if(parseFloat(value) > parseFloat(blFee)) {
			var message = "<p class='ajax-msg' style='color:red;'>Fee Received not more than balance fee!</p>"; 
		    jQuery(this).after(message);
			jQuery(this).val('');
			jQuery('#edit-swr-total-fee-entry').val('');
			setTimeout(function(){ jQuery('p.ajax-msg').remove(); }, 2000);
	        return false;
		} else {


			var uilPayment = ((value * uilPer) / 100);
			uilPayment = uilPayment.toFixed(2);
			//uilPayment = value;
			jQuery('#payment_to_uil_' + key).val(uilPayment);
			calculateTotal(sourceRecFeeObj, targetRecFeeObj);
			calculateTotal(sourceRecFeeObj, targetUILFeeObj);
			calculateTotalTax( targetUILFeeObj );
		}
	});
	var paymentMode = jQuery('#payment-mode-id:checked').val();
	paymentOption(paymentMode);
	jQuery(document).delegate('#payment-mode-id', 'click', function() {
		paymentOption(this.value);		
	});
	jQuery(document).delegate('.custom-manage-teacher .form-select', 'change', function() {
		//$('#edit-field-sewing-copy-teacher-data-value').trigger('change');
		jQuery('#edit-title-0-value').val('');
		jQuery('#edit-field-sewing-teacher-email-0-value').val('');
		jQuery('#edit-field-teacher-mobile-number-0-value').val('');
		jQuery("#edit-field-sewing-copy-teacher-data-value").prop('checked',false);
		jQuery('#edit-title-0-value').prop('readonly',false);
		jQuery('#edit-field-sewing-teacher-email-0-value').prop('readonly',false);
		jQuery('#edit-field-teacher-mobile-number-0-value').prop('readonly',false);
		jQuery.ajax({
			url: drupalSettings.path.baseUrl + 'get-sewing-teacher-detail-by-schoolcode',
			type: 'POST',
			data: {schoolCode:this.value},
			beforeSend: function() {
	        	var message = "<p class='ajax-msg'><strong>Please wait...</strong></p>";
	        	jQuery('#edit-copy-school-teachet-data').after(message);
	        },
			error: function () {
				console.log("Error occurred");
			},
			success: function (response) {
				var data =  response.data;
				if (data['duplicate'] == 1) {
					setTimeout(function(){ jQuery('p.ajax-msg').remove(); }, 200);
					jQuery("#edit-field-sewing-copy-teacher-data-value").prop('disabled',true);
					// jQuery('.form-item-field-sewing-copy-teacher-data-value').hide();
				} else {
					jQuery("#edit-field-sewing-copy-teacher-data-value").prop('disabled',false);
					// jQuery('.form-item-field-sewing-copy-teacher-data-value').show();
				}  
			}
			
		});		
	});
	var copyTeacherCheck = jQuery('#edit-field-sewing-copy-teacher-data-value:checked').val();
	if(copyTeacherCheck) {
		jQuery('#edit-title-0-value').prop('readonly',true);
		jQuery('#edit-field-sewing-teacher-email-0-value').prop('readonly',true);
		jQuery('#edit-field-teacher-mobile-number-0-value').prop('readonly',true);
	}	
	jQuery(document).delegate('#edit-field-sewing-copy-teacher-data-value', 'change', function() {
		var schoolCode = jQuery('.custom-manage-teacher .form-select').val();
		var editId = jQuery('#field-hidden-tid').val();
		if (this.checked) {
			if(schoolCode) {
				jQuery.ajax({
					url: drupalSettings.path.baseUrl + 'get-sewing-teacher-detail-by-schoolcode',
					type: 'POST',
					data: {schoolCode:schoolCode, editId:editId},
					beforeSend: function() {
			        	var message = "<p class='ajax-msg'><strong>Please wait...</strong></p>";
			        	jQuery('#edit-copy-school-teachet-data').after(message);
			        },
					error: function () {
						console.log("Error occurred");
					},
					success: function (response) {
						var data =  response.data;
						if (response.status == 1) {
							setTimeout(function(){ jQuery('p.ajax-msg').remove(); }, 200);
							if (data['duplicate'] == 1) {
								alert('Alreday School Admin as a Teacher.');
								jQuery("#edit-field-sewing-copy-teacher-data-value").prop('checked',false);
								jQuery("#edit-field-sewing-copy-teacher-data-value").prop('disabled',true);
							} else {
								jQuery("#edit-field-sewing-copy-teacher-data-value").prop('disabled',false); 
								jQuery('#edit-title-0-value').val(data['name']);
								jQuery('#edit-title-0-value').prop('readonly',true);
								jQuery('#edit-field-sewing-teacher-email-0-value').val(data['email']);
								jQuery('#edit-field-sewing-teacher-email-0-value').prop('readonly',true);
								jQuery('#edit-field-teacher-mobile-number-0-value').val(data['phoneNo']);
								jQuery('#edit-field-teacher-mobile-number-0-value').prop('readonly',true);
							}		
						}   
					}
					
				});
			} else {
				alert('Please select School Code.');
				jQuery("#edit-field-sewing-copy-teacher-data-value").prop('checked',false);
			}
		} else {
			jQuery('#edit-title-0-value').val('');
			jQuery('#edit-title-0-value').prop('readonly',false);
			jQuery('#edit-field-sewing-teacher-email-0-value').val('');
			jQuery('#edit-field-sewing-teacher-email-0-value').prop('readonly',false);
			jQuery('#edit-field-teacher-mobile-number-0-value').val('');
			jQuery('#edit-field-teacher-mobile-number-0-value').prop('readonly',false);
		}			
	});

	//delete user using confirm box
	$('i.delete-sewing-teacher').on('click', function(){
	    nid = $(this).attr("data-id");
		$.confirm({
		    title: 'Delete Teacher?',
		    content: 'Are you sure to delete Teacher?',
		    autoClose: 'Close|8000',
		    buttons: {
		        deleteUser: {
		            text: 'delete ',
		            action: function () {
		            	jQuery.ajax({
							url: drupalSettings.path.baseUrl + 'delete-sewing-teacher',
							type: 'POST',
							data: {nid:nid},
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
		        	window.location.reload();
		        }
		    }
		});    	
	});

});

function studentDetails(schoolCode) {
	var feeId = jQuery('#hidden-fee-id').val();
	if(schoolCode) {
	jQuery.ajax({
		url: drupalSettings.path.baseUrl + 'get-sewing-student-by-schoolcode',
		type: 'POST',
		data: {schoolCode:schoolCode, feeId:feeId},
		beforeSend: function() {
        	var message = "<p class='ajax-msg'><strong>Please wait...</strong></p>";
        	jQuery('#edit-swr-student-fee').after(message);
        },
		error: function () {
			console.log("Error occurred");
		},
		success: function (response) {
			var data =  response.data;
			 if (response.status == 1 && data.length >0) {
			 		setTimeout(function(){ jQuery('p.ajax-msg').remove(); }, 200);
	                // EXTRACT VALUE FOR HTML HEADER. 
			        var col = [];
			        for (var i = 0; i < data.length; i++) {
			            for (var key in data[i]) {
			                if (col.indexOf(key) === -1) {
			                    col.push(key);
			                }
			            }
			        }
			        // CREATE DYNAMIC TABLE.
			        var studentList = document.createElement("thead");
			        // CREATE HTML TABLE HEADER ROW USING THE EXTRACTED HEADERS ABOVE.
			        var tr = studentList.insertRow(-1);                   // TABLE ROW.
			        for (var i = 0; i < col.length; i++) {
			            var th = document.createElement("th");      // TABLE HEADER.
			            th.innerHTML = col[i];
			            tr.appendChild(th);
			        }
			        // ADD JSON DATA TO THE TABLE AS ROWS.
			        for (var i = 0; i < data.length; i++) {
			            tr = studentList.insertRow(-1);
			            for (var j 	= 0; j < col.length; j++) {
			                var tabCell = tr.insertCell(-1);
			                tabCell.innerHTML = data[i][col[j]];
			            }
			        }
			        // FINALLY ADD THE NEWLY CREATED TABLE WITH JSON DATA TO A CONTAINER.
			        var divContainer = document.getElementById("studentListData");
			        divContainer.innerHTML = "";
			        divContainer.appendChild(studentList);
			    }
			        
		        
			}
		
	});
	}
}

function paymentOption(value) {
	var editId = jQuery('#hidden-fee-id').val();
	if(editId != '') {
		var schoolTypeID = jQuery('#edit-swr-school-type-id').val();
		if(schoolTypeID != SCHOOL_TYPE_COMPANY_RUN){
    		jQuery('#edit-swr-payment-type input:radio:last').parent().addClass('revenue_last_payment_type');
    		jQuery('.revenue_last_payment_type').hide();
    		jQuery('.form-item-swr-school-type-id').hide();
    	}else{
    		jQuery('.revenue_last_payment_type').show();
    		jQuery('.form-item-swr-school-type-id').hide();
    	}
	}
	if(value == 0) {
		jQuery('.form-item-swr-cheque-no').hide();
		jQuery('.form-item-swr-bank-drawn').hide();
		jQuery('.form-item-swr-cheque-transaction').hide();
		jQuery('.fee-date-cheque').hide();
		if(editId == '') {
			jQuery("#edit-swr-cheque-no").val('');
			jQuery("#edit-swr-bank-drawn").val('');
			jQuery("#edit-swr-cheque-transaction").val('');
			jQuery("#edit-swr-cheque-date-date").val('');
			jQuery("#edit-swr-cheque-date-time").val('');
		}	
		jQuery("#edit-swr-cheque-no").prop('required',false);
		jQuery("#edit-swr-bank-drawn").prop('required',false);
		// jQuery("#edit-swr-cheque-transaction").prop('required',false);
		// jQuery("#edit-swr-cheque-date-date").prop('required',false);

		jQuery('.form-item-swr-beneficiary').show();
		jQuery("#edit-swr-beneficiary").prop('required',true);
		jQuery('.form-item-swr-beneficiary-ac-no').show();
		jQuery("#edit-swr-beneficiary-ac-no").prop('required',true);
		jQuery('.form-item-swr-remitter').show();
		jQuery("#edit-swr-remitter").prop('required',true);
		jQuery('.form-item-swr-remitter-ac-no').show();
		jQuery("#edit-swr-remitter-ac-no").prop('required',true);
		jQuery('.form-item-swr-ifsc').show();
		jQuery("#edit-swr-ifsc").prop('required',true);
		jQuery('.form-item-swr-transaction').show();
		jQuery("#edit-swr-transaction").prop('required',true);
		jQuery('.fee-date-neft').show();
		jQuery("#edit-swr-date-date").prop('required',true);
		jQuery("#edit-swr-date-time").prop('required',true);

	} else if(value == 2){
		jQuery('.fee-date-neft').hide();
		jQuery('.fee-date-cheque').hide();

		jQuery("#edit-swr-beneficiary").prop('required',false);
		jQuery("#edit-swr-beneficiary-ac-no").prop('required',false);
		jQuery("#edit-swr-remitter").prop('required',false);
		jQuery("#edit-swr-remitter-ac-no").prop('required',false);
		jQuery("#edit-swr-ifsc").prop('required',false);
		jQuery("#edit-swr-transaction").prop('required',false);
		jQuery("#edit-swr-date-date").prop('required',false);
		jQuery("#edit-swr-date-time").prop('required',false);

		jQuery('.form-item-swr-beneficiary').hide();
		jQuery('.form-item-swr-beneficiary-ac-no').hide();
		jQuery('.form-item-swr-remitter').hide();
		jQuery('.form-item-swr-remitter-ac-no').hide();
		jQuery('.form-item-swr-ifsc').hide();
		jQuery('.form-item-swr-transaction').hide();

	} else {
		jQuery('.form-item-swr-cheque-no').show();
		jQuery("#edit-swr-cheque-no").prop('required',true);
		jQuery('.form-item-swr-bank-drawn').show();
		jQuery("#edit-swr-bank-drawn").prop('required',true);
		jQuery('.form-item-swr-cheque-transaction').show();
		// jQuery("#edit-swr-cheque-transaction").prop('required',true);
		jQuery('.fee-date-cheque').show();
		// jQuery("#edit-swr-cheque-date-date").prop('required',true);

		jQuery("#edit-swr-beneficiary").prop('required',false);
		jQuery("#edit-swr-beneficiary-ac-no").prop('required',false);
		jQuery("#edit-swr-remitter").prop('required',false);
		jQuery("#edit-swr-remitter-ac-no").prop('required',false);
		jQuery("#edit-swr-ifsc").prop('required',false);
		jQuery("#edit-swr-transaction").prop('required',false);
		jQuery("#edit-swr-date-date").prop('required',false);
		jQuery("#edit-swr-date-time").prop('required',false);

		jQuery('.form-item-swr-beneficiary').hide();
		jQuery('.form-item-swr-beneficiary-ac-no').hide();
		jQuery('.form-item-swr-remitter').hide();
		jQuery('.form-item-swr-remitter-ac-no').hide();
		jQuery('.form-item-swr-ifsc').hide();
		jQuery('.form-item-swr-transaction').hide();
		jQuery('.fee-date-neft').hide();

		if(editId == '') {
			jQuery("#edit-swr-beneficiary").val('');
			jQuery("#edit-swr-beneficiary-ac-no").val('');
			jQuery("#edit-swr-remitter").val('');
			jQuery("#edit-swr-remitter-ac-no").val('');
			jQuery("#edit-swr-ifsc").val('');
			jQuery("#edit-swr-transaction").val('');
			jQuery("#edit-swr-date-date").val('');
			jQuery("#edit-swr-date-time").val('');
		}	
	}
}

function calculateTotal(sourceObj, targetObj){
	var total = 0;
	
	var revenueFee = jQuery('#edit-swr-revenue-head-value').val();
	var studentTax = jQuery('#revenue-student-tax').val();
	jQuery(sourceObj).each(function(){
		val = jQuery.trim(jQuery(this).val());
		val = (val == '') ? 0 : val;
		total += parseFloat(val);
	});
	if(typeof total == undefined){
		total = 0;
	}
	if(typeof revenueFee == undefined){
		revenueFee = 0;
	}
	if(revenueFee) {
		total = total+ parseFloat(revenueFee);
	} else {
		total = total;
	}	
	var netstudentFee = Math.round(total / ( 1 + (studentTax / 100)));
	var studentTaxAmt = total - netstudentFee;
	jQuery(targetObj).val(netstudentFee);
	jQuery('#edit-swr-total-pay-to-uil').val(total);
	jQuery('#edit-swr-cheque-amount').val(total);
}

function calculateTotalTax(sourceObj){

	var total = 0;
	var revenueFee = jQuery('#edit-swr-revenue-head-value').val();
	var revenueTax = jQuery('#revenue-tax').text();
	var studentTax = jQuery('#revenue-student-tax').val();
	var netRevenueFee = Math.round(revenueFee / ( 1 + (revenueTax / 100)));
	//var revenueTaxAmt = ((revenueTax * revenueFee) /100);
	var revenueTaxAmt = revenueFee - netRevenueFee;
	jQuery(sourceObj).each(function(){
		val = jQuery.trim(jQuery(this).val());
		val = (val == '') ? 0 : val;
		total += parseFloat(val);
	});

	if(typeof total == undefined){
		total = 0;
	}
	var netstudentFee = Math.round(total / ( 1 + (studentTax / 100)));
	var studentTaxAmt = total - netstudentFee;
	//var studentTaxAmt = ((total * studentTax) /100);
	totalTax = parseFloat(revenueTaxAmt) + parseFloat(studentTaxAmt);
	jQuery('#edit-swr-tax').val(totalTax);
}