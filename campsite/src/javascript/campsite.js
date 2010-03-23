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
 * Clear all the tabs and set the appropiate css classes.
 *
 * @param int p_elements
 *      the total number of tabs
 */
function clearAllTabs(p_elements) {
	for(k = 1; k <= p_elements; k++) {
		document.getElementById('link' + k).className='tab_normal';
		document.getElementById('tab' + k).style.display='none';
	}
}

/**
 * Set the selected tab.
 *
 * @param int p_id
 *      the Id of the HTML element
 * @param int p_elements
 *      the total number of tabs
 */
function selectTab(p_id, p_elements) {
	clearAllTabs(p_elements);
	document.getElementById('link' + p_id).className='tab_current';
	document.getElementById('tab' + p_id).style.display='';
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

/**
 * Returns true if the given object had the given property.
 */
function element_exists(object, property) {
	for (i in object) {
		if (object[i].name == property) {
			return true
		}
	}
	return false
}

/**
 * Used in subscription form; computes the subscription cost and updates
 * the corresponding field in the form.
 */
function update_subscription_payment() {
	var sum = 0
	var i
	var my_form = document.forms["subscription_form"]
	var subs_all_lang = false
	var unitcost = my_form.unitcost.value
	var lang_count = 1
	if (element_exists(my_form.elements, "subs_all_languages")
		&& my_form.subs_all_languages.checked) {
		unitcost = my_form.unitcostalllang.value
	} else if (element_exists(my_form.elements, "subscription_language[]")) {
		lang_count = 0
		for (i=0; i<my_form["subscription_language[]"].options.length; i++) {
			if (my_form["subscription_language[]"].options[i].selected) {
				lang_count++
			}
		}
	}
	for (i = 0; i < my_form.nos.value; i++) {
		if (element_exists(my_form.elements, "by")
			&& my_form.by.value == "publication") {
			sum = parseInt(sum) + parseInt(my_form["tx_subs"].value)
			continue
		}
		if (!my_form["cb_subs[]"][i].checked) {
			continue
		}
		var section = my_form["cb_subs[]"][i].value
		var time_var_name = "tx_subs" + section
		if (element_exists(my_form.elements, time_var_name)) {
			sum = parseInt(sum) + parseInt(my_form[time_var_name].value)
		} else if (element_exists(my_form.elements, "tx_subs")) {
			sum = parseInt(sum) + parseInt(my_form["tx_subs"].value)
		}
	}
	my_form.suma.value = Math.round(100 * sum * unitcost * lang_count) / 100
}

/**
 * @param area
 * @param field
 * @param value
 * @param style
 */
function addInputTextField(area, field, value) {
    if (!document.getElementById) return;

    var field_area = document.getElementById(area);
    var all_inputs = field_area.getElementsByTagName('input');

    if (document.createElement) {
	var tr = document.createElement('tr');
	var td = document.createElement('td');
	var input = document.createElement('input');

	td = document.createElement('td');
	td.style.textAlign = 'right';
	td.appendChild(document.createTextNode(field+': '));
	tr.appendChild(td);
	input.id = field;
	input.name = field;
	input.type = 'text';
	input.size = '50';
	input.value = (value != undefined) ? value : '';
	td = document.createElement('td');
	td.appendChild(input);
	tr.appendChild(td);
	field_area.appendChild(tr);
    } else {
	field_area.innerHTML += '<tr><td align="right">'+field+': '+'</td><td><input name="'+field+'" id="'+field+'" type="text" class="input_text" /></td></tr>';
    }
}

/**
 * @param number
 * @param decimals
 * @param dec_point
 * @param thousands_sep
 */
function number_format(number, decimals, dec_point, thousands_sep) {
    var n = number, c = isNaN(decimals = Math.abs(decimals)) ? 2 : decimals;
    var d = dec_point == undefined ? "," : dec_point;
    var t = thousands_sep == undefined ? "." : thousands_sep, s = n < 0 ? "-" : "";
    var i = parseInt(n = Math.abs(+n || 0).toFixed(c)) + "", j = (j = i.length) > 3 ? j % 3 : 0;

    return s + (j ? i.substr(0, j) + t : "") + i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + t) + (c ? d + Math.abs(n - i).toFixed(c).slice(2) : "");
}

/**
 *
 *
 * @param int p_size
 *      the total number of tabs
 */
function formatBytes(p_size) {
    if (p_size >= 1073741824) {
	p_size = number_format(p_size / 1073741824, 2, '.', '') + ' GB';
    } else if (p_size >= 1048576) {
	p_size = number_format(p_size / 1048576, 2, '.', '') + ' MB';
    } else if (p_size >= 1024) {
	p_size = number_format(p_size / 1024, 0) + ' KB';
    } else {
	p_size = number_format(p_size, 0) + ' bytes';
    }
    return p_size;
}