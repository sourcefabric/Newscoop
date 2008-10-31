// Mask Language plugin for HTMLArea
// Implementation by Udo Schmal
//
// (c) Udo Schmal & Schaffrath NeueMedien 2004
// Distributed under the same terms as HTMLArea itself.
// This notice MUST stay intact for use (see license.txt).

function LangMarks(editor, args) {
	this.editor = editor;
	var cfg = editor.config;
	var self = this;
	var options = {};
	options[this._lc("&mdash; language &mdash;")] = "";
	options[this._lc("Greek")] = "el";
	options[this._lc("English")] = "en";
	options[this._lc("French")] = "fr";
	options[this._lc("Latin")] = "la";

	cfg.registerDropdown({
		id	: "langmarks",
		tooltip	: this._lc("language select"),
		options	: options,
		action	: function(editor) { self.onSelect(editor, this); },
		refresh	: function(editor) { self.updateValue(editor, this); }
	});
	cfg.addToolbarElement("langmarks", "inserthorizontalrule", 1);
}

LangMarks._pluginInfo = {
	name          : "LangMarks",
	version       : "1.0",
	developer     : "Udo Schmal",
	developer_url : "",
	sponsor       : "L.N.Schaffrath NeueMedien",
	sponsor_url   : "http://www.schaffrath-neuemedien.de/",	
	c_owner       : "Udo Schmal & Schaffrath NeueMedien",
	license       : "htmlArea"
};

LangMarks.prototype._lc = function(string) {
    return HTMLArea._lc(string, 'LangMarks');
};

LangMarks.prototype.onGenerate = function() {
  var style_id = "LM-style"
  var style = this.editor._doc.getElementById(style_id);
  if (style == null) {
    style = this.editor._doc.createElement("link");
    style.id = style_id;
    style.rel = 'stylesheet';
    style.href = _editor_url + 'plugins/LangMarks/lang-marks.css';
    this.editor._doc.getElementsByTagName("HEAD")[0].appendChild(style);
  }
};

LangMarks.prototype.onSelect = function(editor, obj, context, updatecontextclass) {
	var tbobj = editor._toolbarObjects[obj.id];
	var index = tbobj.element.selectedIndex;
	var className = tbobj.element.value;

	// retrieve parent element of the selection
	var parent = editor.getParentElement();
	var surround = true;

	var is_span = (parent && parent.tagName.toLowerCase() == "span");
	var update_parent = (context && updatecontextclass && parent && parent.tagName.toLowerCase() == context);

	if (update_parent) {
		parent.className = className;
		parent.lang = className;
		editor.updateToolbar();
		return;
	}

	if (is_span && index == 0 && !/\S/.test(parent.style.cssText)) {
		while (parent.firstChild) {
			parent.parentNode.insertBefore(parent.firstChild, parent);
		}
		parent.parentNode.removeChild(parent);
		editor.updateToolbar();
		return;
	}

	if (is_span) {
		// maybe we could simply change the class of the parent node?
		if (parent.childNodes.length == 1) {
			parent.className = className;
			parent.lang = className;
			surround = false;
			// in this case we should handle the toolbar updation
			// ourselves.
			editor.updateToolbar();
		}
	}

	// Other possibilities could be checked but require a lot of code.  We
	// can't afford to do that now.
	if (surround) {
		// shit happens ;-) most of the time.  this method works, but
		// it's dangerous when selection spans multiple block-level
		// elements.
		editor.surroundHTML('<span lang="' + className + '" class="' + className + '">', '</span>');
	}
};

LangMarks.prototype.updateValue = function(editor, obj) {
	var select = editor._toolbarObjects[obj.id].element;
	var parent = editor.getParentElement();
	if (typeof parent.className != "undefined" && /\S/.test(parent.className)) {
		var options = select.options;
		var value = parent.className;
		for (var i = options.length; --i >= 0;) {
			var option = options[i];
			if (value == option.value) {
				select.selectedIndex = i;
				return;
			}
		}
	}
	select.selectedIndex = 0;
};