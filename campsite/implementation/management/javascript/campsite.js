function ToggleRowVisibility(id) {
	if (document.getElementById(id).style.display == "none") {
		if (document.all) {
			document.getElementById(id).style.display = "block";
		}
		else {
			document.getElementById(id).style.display = "";
		}
	}
	else {
		document.getElementById(id).style.display = "none";
	}
}

function ToggleBoolValue(element_id) {
    if (document.getElementById(element_id).value == "false") {
		document.getElementById(element_id).value = "true";
    }
    else {
	   document.getElementById(element_id).value = "false";
    }
}
