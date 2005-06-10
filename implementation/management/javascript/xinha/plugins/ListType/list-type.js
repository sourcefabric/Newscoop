// ListType Plugin for HTMLArea-3.0
// Sponsored by MEdTech Unit - Queen's University
// Implementation by Mihai Bazon, http://dynarch.com/mishoo/
//
// (c) dynarch.com 2003.
// Distributed under the same terms as HTMLArea itself.
// This notice MUST stay intact for use (see license.txt).
//
// $Id: list-type.js,v 1.2 2005/06/10 15:35:40 paul Exp $

function ListType(editor) {
	this.editor = editor;
	var cfg = editor.config;
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
		id            : "listtype",
		tooltip       : this._lc("Choose list style type (for ordered lists)"),
		options       : options,
		action        : function(editor) { self.onSelect(editor, this); },
		refresh       : function(editor) { self.updateValue(editor, this); },
		context       : "ol"
	};
	cfg.registerDropdown(obj);
	cfg.addToolbarElement("listtype", ["insertorderedlist","orderedlist"], 1);
}	

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
