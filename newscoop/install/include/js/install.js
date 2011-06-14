
function onLicensePage() 
{
	if($("#license_agreement").length > 0) {
		return true;
	} else {
		return false;
	}
}

function toggleNextButton() 
{
	if(! $('#license_agreement').attr('checked')) {
		$("#license_error_message").html("<p>You must accept the terms of the License Agreement!</p>");
	} else {
		$("#license_error_message").html("<p></p>");
	}
}

/**
 * Generic submit form
 */
function submitForm(frm, step)
{
	if( onLicensePage() && step == 'database') {
		toggleNextButton();
		if( $('#license_agreement').attr('checked') ) {
			frm.step.value = step;
		    frm.submit();
		} else {
			//show error message
			$("#license_error_message").html("<p>You must accept the terms of the License Agreement!</p>");
		}
	} else {
		frm.step.value = step;
	    frm.submit();
	}
		
    
} // fn submitForm


$(function() 
{
	if(onLicensePage) {
		$('#license_agreement').click(function() {
			toggleNextButton();
		});
	}
	
});