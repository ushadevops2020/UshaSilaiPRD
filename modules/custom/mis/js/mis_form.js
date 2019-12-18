jQuery(function ($) {
$(document).delegate('.alphanumeric','keypress', function(e) {
	    var regex = /^[a-z\d\-_\s]+$/i;
	    var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
	    if (regex.test(str)) {
	        return true;
	    }

	    e.preventDefault();
	    return false;
	});
});