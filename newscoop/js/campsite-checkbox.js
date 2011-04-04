/**
 * This array is used to remember mark status of rows in browse mode
 */
var marked_row = new Array;
var default_class = new Array;
var defaultRowPrefix = "row_";
var defaultCheckboxPrefix = "checkbox_";

function checkAll(numItems, rowPrefix, checkboxPrefix)
{
	if (rowPrefix == null) {
		rowPrefix = defaultRowPrefix;
	}
	if (checkboxPrefix == null) {
		checkboxPrefix = defaultCheckboxPrefix;
	}
	for (i = 0; i < numItems; i++) {
		document.getElementById(rowPrefix+i).className = 'list_row_click';
		document.getElementById(checkboxPrefix+i).checked = true;
                marked_row[i] = true;
	}
} // fn checkAll


function uncheckAll(numItems, rowPrefix, checkboxPrefix)
{
	if (rowPrefix == null) {
		rowPrefix = defaultRowPrefix;
	}
	if (checkboxPrefix == null) {
		checkboxPrefix = defaultCheckboxPrefix;
	}
	for (i = 0; i < numItems; i++) {
		document.getElementById(rowPrefix+i).className = default_class[i];
		document.getElementById(checkboxPrefix+i).checked = false;
                marked_row[i] = false;
	}
} // fn uncheckAll


function checkRestore(numItems, checkedItems, rowPrefix, checkboxPrefix)
{
    if (rowPrefix == null) {
        rowPrefix = defaultRowPrefix;
    }
    if (checkboxPrefix == null) {
        checkboxPrefix = defaultCheckboxPrefix;
    }

    for (i = 0; i < numItems; i++) {
       if (checkedItems.indexOf(checkboxPrefix+i) >= 0) {
           document.getElementById(rowPrefix+i).className = 'list_row_click';
           document.getElementById(checkboxPrefix+i).checked = true;
           marked_row[i] = true;
       } else {
           document.getElementById(rowPrefix+i).className = default_class[i];
           document.getElementById(checkboxPrefix+i).checked = false;
           marked_row[i] = false;
	}
    }
} // fn checkRestore

/**
 * Sets/unsets the pointer and marker in browse mode
 *
 * @param   object    the table row
 * @param   integer  the row number
 * @param   string    the action calling this script (over, out or click)
 * @param   string    the default class
 *
 * @return  boolean  whether pointer is set or not
 */
function setPointer(theRow, theRowNum, theAction)
{
	newClass = null;
    // 4. Defines the new class
    // 4.1 Current class is the default one
    if (theRow.className == default_class[theRowNum]) {
        if (theAction == 'over') {
            newClass = 'list_row_hover';
        }
    }
    // 4.1.2 Current color is the hover one
    else if (theRow.className == 'list_row_hover'
             && (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])) {
        if (theAction == 'out') {
            newClass = default_class[theRowNum];
        }
    }

    if (newClass != null) {
    	theRow.className = newClass;
    }
    return true;
} // end of the 'setPointer()' function

/**
 * Change the color of the row when the checkbox is selected.
 *
 * @param object  The checkbox object.
 * @param int     The row number.
 */
function checkboxClick(theCheckbox, theRowNum, rowPrefix)
{
	if (rowPrefix == null) {
		rowPrefix = defaultRowPrefix;
	}
	if (theCheckbox.checked) {
        newClass = 'list_row_click';
        marked_row[theRowNum] = (typeof(marked_row[theRowNum]) == 'undefined' || !marked_row[theRowNum])
                              ? true
                              : null;
	} else {
        newClass = 'list_row_hover';
        marked_row[theRowNum] = false;
	}
   	row = document.getElementById(rowPrefix+theRowNum);
   	row.className = newClass;
} // fn checkboxClick

/**
 * Returns true if at least minChecked and at most maxChecked checkboxes were checked,
 * otherwise returns false. You may pass the wildcard * for maxChecked
 *
 * @param string formName      The name of the form containing the checkboxes
 * @param string fieldName     The name of the checkbox field
 * @param int minChecked       The minimum number of checkboxes that must be checked
 * @param int maxChecked       The maximum number of checkboxes that must be checked
 * @param string errorMessage  The error message to be displayed if the checkbox 
 *                             selection was not valid
 */
function validateCheckboxes(formName, fieldName, minChecked, maxChecked, errorMessage)
{
    // Verify that at least one checkbox has been selected.
    checkboxes = document.forms[formName][fieldName];
    if (checkboxes) {
        isValid = false;
        numCheckboxesChecked = 0;
        // Special case for single checkbox
        // (when there is only one article in the section).
        if (!checkboxes.length) {
            numCheckboxesChecked = checkboxes.checked ? 1 : 0;
        } else {
            // Multiple checkboxes
            for (var index = 0; index < checkboxes.length; index++) {
                if (checkboxes[index].checked) {
                    numCheckboxesChecked++;
                }
            }
        }
        isValid = numCheckboxesChecked >= minChecked;
        if (maxChecked != "*") {
        	isValid = isValid && numCheckboxesChecked <= maxChecked;
        }
        if (!isValid) {
            alert(errorMessage);
        }
        return isValid;
    } else {
        return true;
    }
} // fn validateCheckboxes
