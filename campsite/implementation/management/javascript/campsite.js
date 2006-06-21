/**
 * Given the ID of an HTML element, toggle its visibility.
 * @param string id
 */
function ToggleRowVisibility(id) {
	element = document.getElementById(id);
	if (element) {
		if (element.style.display == "none") {
			if (document.all) {
				element.style.display = "block";
			} else {
				element.style.display = "";
			}
		} else {
			element.style.display = "none";
		}
	}
	return true;
}


function ToggleBoolValue(element_id) {
	element = document.getElementById(element_id);
	if (element) {
	    if (element.value == "false") {
			element.value = "true";
	    } else {
		   element.value = "false";
	    }
	}
}


/**
 * Given the ID of an HTML element, make it visible.
 */
function ShowElement(id)
{
	element = document.getElementById(id);
	if (element) {
		if (document.all) {
			element.style.display = "block";
		} else {
			element.style.display = "";
		}
	}
	return true;
}


/**
 * Given the ID of an HTML element, make it invisible.
 */
function HideElement(id)
{
	element = document.getElementById(id);
	if (element) {
		element.style.display = "none";
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
		element = document.getElementById(p_array[i]);
		if (element) {
			element.style.display = "none";
		}
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
		element = document.getElementById(p_array[i]);
		if (element) {
			element.style.display = "";
		}
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
	element = document.getElementById(element_id);
	if (element) {
		if (element.style.display == 'none') {
			element.style.opacity = '0';
			element.style.display = 'block';
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
}
