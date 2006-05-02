/**
 * Given the ID of an HTML element, toggle its visibility.
 * @param string id
 */
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
	return true;
}


function ToggleBoolValue(element_id) {
    if (document.getElementById(element_id).value == "false") {
		document.getElementById(element_id).value = "true";
    }
    else {
	   document.getElementById(element_id).value = "false";
    }
}


/**
 * Given the ID of an HTML element, make it visible.
 */
function ShowElement(id)
{
	if (document.all) {
		document.getElementById(id).style.display = "block";
	}
	else {
		document.getElementById(id).style.display = "";
	}
	return true;
}


/**
 * Given an array of element IDs, make all of the
 * elements invisible.
 *
 * @param array p_array
 */
function HideAll(p_array)
{
	for (i = 0; i < p_array.length; i++) {
		document.getElementById(p_array[i]).style.display = "none";
	}
	return true;
}


/**
 * Given an array of element IDs, make all of the
 * elements visible.
 *
 * @param array p_array
 */
function ShowAll(p_array)
{
	for (i = 0; i < p_array.length; i++) {
		document.getElementById(p_array[i]).style.display = "";
	}
	return true;
}

