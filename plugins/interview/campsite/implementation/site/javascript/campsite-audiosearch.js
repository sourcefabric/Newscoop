function SearchForm_displayRow(row)
{
    document.getElementById('searchRow_' + row).style.display = 'inline';
    document.forms['search'].elements['row_' + Number(row) + '[active]'].value = 1
}

function SearchForm_addRow(errorMessage)
{
    if (document.forms['search'].elements['counter'].value < document.forms['search'].elements['max_rows'].value) {
        document.forms['search'].elements['counter'].value++;
        SearchForm_displayRow(document.forms['search'].elements['counter'].value);
        return true;
    } else {
        alert(errorMessage);
        return false;
    }
}

function SearchForm_hideRow(row)
{
    document.getElementById('searchRow_' + row).style.display = 'none';
    document.forms['search'].elements['row_' + Number(row) + '[0]'].options[0].selected = true;
    document.forms['search'].elements['row_' + Number(row) + '[1]'].options[0].selected = true;
    document.forms['search'].elements['row_' + Number(row) + '[2]'].value = '';
    document.forms['search'].elements['row_' + Number(row) + '[active]'].value = 0
}

function SearchForm_dropRow(row)
{
    if (document.forms['search'].elements['counter'].value <= 1)
        return false;
    var n;
    for (n = row; n < document.forms['search'].elements['counter'].value; n++) {
        document.forms['search'].elements['row_' + Number(n) + '[0]'].value = document.forms['search'].elements['row_' + (Number(n)+1) + '[0]'].value;
        document.forms['search'].elements['row_' + Number(n) + '[1]'].value = document.forms['search'].elements['row_' + (Number(n)+1) + '[1]'].value;
        document.forms['search'].elements['row_' + Number(n) + '[2]'].value = document.forms['search'].elements['row_' + (Number(n)+1) + '[2]'].value;
    }
    document.forms['search'].elements['counter'].value--;
    SearchForm_hideRow(Number(n));

}

function _hs_findOptions(ary, keys)
{
    var key = keys.shift();
    if (!key in ary) {
        return {};
    } else if (0 == keys.length) {
        return ary[key];
    } else {
        return _hs_findOptions(ary[key], keys);
    }
}

function _hs_findSelect(form, groupName, selectIndex)
{
    if (groupName+'['+ selectIndex +']' in form) {
        return form[groupName+'['+ selectIndex +']']; 
    } else {
        return form[groupName+'['+ selectIndex +'][]']; 
    }
}

function _hs_replaceOptions(ctl, optionList)
{
    var j = 0;
    ctl.options.length = 0;
    for (i in optionList) {
        ctl.options[j++] = new Option(optionList[i], i, false, false);
    }
}

function _hs_setValue(ctl, value)
{
    var testValue = {};
    if (value instanceof Array) {
        for (var i = 0; i < value.length; i++) {
            testValue[value[i]] = true;
        }
    } else {
        testValue[value] = true;
    }
    for (var i = 0; i < ctl.options.length; i++) {
        if (ctl.options[i].value in testValue) {
            ctl.options[i].selected = true;
        }
    }
}

function _hs_swapOptions(form, groupName, selectIndex)
{
    var hsValue = [];
    for (var i = 0; i <= selectIndex; i++) {
        hsValue[i] = _hs_findSelect(form, groupName, i).value;
    }

    _hs_replaceOptions(_hs_findSelect(form, groupName, selectIndex + 1), 
                       _hs_findOptions(_hs_options[groupName][selectIndex], hsValue));
    if (selectIndex + 1 < _hs_options[groupName].length) {
        _hs_swapOptions(form, groupName, selectIndex + 1);
    }
}

function _hs_onReset(form, groupNames)
{
    for (var i = 0; i < groupNames.length; i++) {
        try {
            for (var j = 0; j <= _hs_options[groupNames[i]].length; j++) {
                _hs_setValue(_hs_findSelect(form, groupNames[i], j), _hs_defaults[groupNames[i]][j]);
                if (j < _hs_options[groupNames[i]].length) {
                    _hs_replaceOptions(_hs_findSelect(form, groupNames[i], j + 1), 
                                       _hs_findOptions(_hs_options[groupNames[i]][j], _hs_defaults[groupNames[i]].slice(0, j + 1)));
                }
            }
        } catch (e) {
            if (!(e instanceof TypeError)) {
                throw e;
            }
        }
    }
}

function _hs_setupOnReset(form, groupNames)
{
    setTimeout(function() { _hs_onReset(form, groupNames); }, 25);
}

function _hs_onReload()
{
    var ctl;
    for (var i = 0; i < document.forms.length; i++) {
        for (var j in _hs_defaults) {
            if (ctl = _hs_findSelect(document.forms[i], j, 0)) {
                for (var k = 0; k < _hs_defaults[j].length; k++) {
                    _hs_setValue(_hs_findSelect(document.forms[i], j, k), _hs_defaults[j][k]);
                }
            }
        }
    }

    if (_hs_prevOnload) {
        _hs_prevOnload();
    }
}

var _hs_prevOnload = null;
if (window.onload) {
    _hs_prevOnload = window.onload;
}
window.onload = _hs_onReload;

var _hs_options = {};
var _hs_defaults = {};
