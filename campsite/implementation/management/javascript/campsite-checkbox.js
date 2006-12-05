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
