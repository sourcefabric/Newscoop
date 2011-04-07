/* $Id: functions.js,v 1.2 2005/12/02 08:23:04 lsmith Exp $ */


/**
 * Displays an confirmation box beforme to submit a "DROP/DELETE/ALTER" query.
 * This function is called while clicking links
 *
 * @param   object   the link
 * @param   object   the sql query to submit
 *
 * @return  bool  whether to run the query or not
 */
function confirmLink(theLink, confirmMsg)
{
    // Confirmation is not required in the configuration file
    if (confirmMsg == '') {
        return true;
    }

    var is_confirmed = confirm(confirmMsg);
    if (is_confirmed) {
        theLink.href += '&is_js_confirmed=1';
    }

    return is_confirmed;
} // end of the 'confirmLink()' function


/**
 * Displays an error message if an element of a form hasn't been completed and
 * should be
 *
 * @param   object   the form
 * @param   string   the name of the form field to put the focus on
 *
 * @return  bool  whether the form field is empty or not
 */
function emptyFormElements(theForm, theFieldName)
{
    var theField = theForm.elements[theFieldName];
    // Whether the replace function (js1.2) is supported or not
    var isRegExp = (typeof(theField.value.replace) != 'undefined');

    if (!isRegExp) {
        var isEmpty  = (theField.value == '') ? 1 : 0;
    } else {
        var space_re = new RegExp('\\s+');
        var isEmpty  = (theField.value.replace(space_re, '') == '') ? 1 : 0;
    }
    if (isEmpty) {
        theForm.reset();
        theField.select();
        alert(errorMsg0);
        theField.focus();
        return false;
    }

    return true;
} // end of the 'emptyFormElements()' function


/**
 * Ensures a value submitted in a form is numeric and is in a range
 *
 * @param   object   the form
 * @param   string   the name of the form field to check
 * @param int  the minimum authorized value
 * @param int  the maximum authorized value
 *
 * @return  bool  whether a valid number has been submitted or not
 */
function checkFormElementInRange(theForm, theFieldName, min, max)
{
    var theField         = theForm.elements[theFieldName];
    var val              = parseInt(theField.value);

    if (typeof(min) == 'undefined') {
        min = 0;
    }
    if (typeof(max) == 'undefined') {
        max = Number.MAX_VALUE;
    }

    // It's not a number
    if (isNaN(val)) {
        theField.select();
        alert(errorMsg1);
        theField.focus();
        return false;
    }
    // It's a number but it is not between min and max
    else if (val < min || val > max) {
        theField.select();
        alert(val + errorMsg2);
        theField.focus();
        return false;
    }
    // It's a valid number
    else {
        theField.value = val;
    }

    return true;
} // end of the 'checkFormElementInRange()' function


/**
 * Sets/unsets the pointer in browse mode
 *
 * @param   object   the table row
 * @param   object   the color to use for this row
 *
 * @return  bool  whether pointer is set or not
 */
function setPointer(theRow, thePointerColor)
{
    if (thePointerColor == '' || typeof(theRow.style) == 'undefined') {
        return false;
    }
    if (typeof(document.getElementsByTagName) != 'undefined') {
        var theCells = theRow.getElementsByTagName('td');
    }
    else if (typeof(theRow.cells) != 'undefined') {
        var theCells = theRow.cells;
    }
    else {
        return false;
    }

    var rowCellsCnt  = theCells.length;
    for (var c = 0; c < rowCellsCnt; c++) {
        theCells[c].style.backgroundColor = thePointerColor;
    }

    return true;
} // end of the 'setPointer()' function


/**
 * Checks/unchecks all tables
 *
 * @param   string   the form name
 * @param bool  whether to check or to uncheck the element
 *
 * @return  bool  always true
 */
function setCheckboxes(the_form, do_check)
{
    var elts      = document.forms[the_form].elements['selected_tbl[]'];
    var elts_cnt  = elts.length;

    for (var i = 0; i < elts_cnt; i++) {
        elts[i].checked = do_check;
    } // end for

    return true;
} // end of the 'setCheckboxes()' function

