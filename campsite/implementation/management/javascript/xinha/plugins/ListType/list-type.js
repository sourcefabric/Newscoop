// ListType Plugin for HTMLArea-3.0
// Sponsored by MEdTech Unit - Queen's University
// Implementation by Mihai Bazon, http://dynarch.com/mishoo/
//
// (c) dynarch.com 2003.
// Distributed under the same terms as HTMLArea itself.
// This notice MUST stay intact for use (see license.txt).
//
// $Id: list-type.js,v 1.1 2005/05/02 17:39:57 paul Exp $

function ListType(editor) {
	this.editor = editor;
	var cfg = editor.config;
	var toolbar = cfg.toolbar;
	var self = this;
	var options = {};
	options[this._lc("Decimal numbers")] = "decimal";
	options[this._lc("Lower roman numbers")] = "lower-roman";
	options[this._lc("Upper roman numbers")] = "upper-roman";
	options[this._lc("Lower latin letters")] = "lower-alpha";
	options[this._lc("Upper latin letters")] = "upper-alpha";
	if (!HTMLArea.is_ie)
		// IE doesn't support this property; even worse, it complains
		// with a gross error message when we tried to select it,
		// therefore let's hide it from the damn "browser".
		options[this._lc("Lower greek letters")] = "lower-greek";
	var obj = {
		id            : "ListType",
		tooltip       : this._lc("Choose list style type (for ordered lists)"),
		options       : options,
		action        : function(editor) { self.onSelect(editor, this); },
		refresh       : function(editor) { self.updateValue(editor, this); },
		context       : "ol"
	};
	cfg.registerDropdown(obj);
	var a, i, j, found = false;
	for (i = 0; !found && i < toolbar.length; ++i) {
		a = toolbar[i];
		for (j = 0; j < a.length; ++j) {
			if (a[j] == "insertorderedlist") {
				found = true;
				break;
			}
		}
	}
	if (found)
		a.splice(j+1, 0, "space", "ListType", "space");
};

ListType._pluginInfo = {
	name          : "ListType",
	version       : "1.0",
	developer     : "Mihai Bazon",
	developer_url : "http://dynarch.com/mishoo/",
	c_owner       : "dynarch.com",
	sponsor       : "MEdTech Unit - Queen's University",
	sponsor_url   : "http://www.queensu.ca/",
	license       : "htmlArea"
};

ListType.prototype._lc = function(string) {
    return HTMLArea._lc(string, 'ListType');
}

ListType.prototype.onSelect = function(editor, combo) {
	var tbobj = editor._toolbarObjects[combo.id].element;
	var parent = editor.getParentElement();
	while (!/^ol$/i.test(parent.tagName)) {
		parent = parent.parentNode;
	}
	parent.style.listStyleType = tbobj.value;
};

ListType.prototype.updateValue = function(editor, combo) {
	var tbobj = editor._toolbarObjects[combo.id].element;
	var parent = editor.getParentElement();
	while (parent && !/^ol$/i.test(parent.tagName)) {
		parent = parent.parentNode;
	}
	if (!parent) {
		tbobj.selectedIndex = 0;
		return;
	}
	var type = parent.style.listStyleType;
	if (!type) {
		tbobj.selectedIndex = 0;
	} else {
		for (var i = tbobj.firstChild; i; i = i.nextSibling) {
			i.selected = (type.indexOf(i.value) != -1);
		}
	}
};
