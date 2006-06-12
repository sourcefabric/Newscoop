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
 * Given the ID of an HTML element, make it invisible.
 */
function HideElement(id)
{
	document.getElementById(id).style.display = "none";
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


/**
 * Fade an element by adjusting its opacity.
 *
 * @param string element_id
 * 		ID of the HTML element.
 *
 * @param int milliseconds_between_changes
 *		How fast between each transition change.  For smooth transitions
 *		You want to use a value under 100.
 *
 * @param int maxFade
 *		The min opacity to fade to, this can be between 0 and 10.
 *
 * @param boolean disappear
 *		Whether to make the element disappear when it becomes completely
 *		transparent.
 *
 * @param int delay_in_milliseconds
 *		Time to wait before the fade begins.
 */
function fade(element_id, milliseconds_between_changes, maxFade, disappear, delay_in_milliseconds)
{
	setTimeout('do_fade(\''+element_id+'\', '+milliseconds_between_changes+', '+maxFade+', '+disappear+');',
			   delay_in_milliseconds);
}

/**
 * Slightly modified form of the function found here:
 * http://www.bigbold.com/snippets/posts/show/1843
 */
function do_fade(element_id, milliseconds_between_changes, maxFade, disappear)
{
	var opacs = ["0",".1",".2",".3",".4",".5",".6",".7",".8",".9","1"];
	if (document.getElementById(element_id).style.display == 'none') {
		document.getElementById(element_id).style.opacity = '0';
		document.getElementById(element_id).style.display = 'block';
		for (var i = 0; i < maxFade; i++) {
			setTimeout('document.getElementById(\''+element_id+'\').style.opacity = "'+opacs[i]+'";', i * milliseconds_between_changes);
		}
	} else {
		opacs.reverse();
		for (var i = 0; i < maxFade; i++) {
		    setTimeout('document.getElementById(\''+element_id+'\').style.opacity = "'+opacs[i]+'";', i * milliseconds_between_changes);
		}
		if (disappear) {
			setTimeout('document.getElementById(\''+element_id+'\').style.display = "none";', i * milliseconds_between_changes);
		}
	}
}
