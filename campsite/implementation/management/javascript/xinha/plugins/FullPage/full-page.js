// FullPage Plugin for HTMLArea-3.0
// Implementation by Mihai Bazon.  Sponsored by http://thycotic.com
//
// htmlArea v3.0 - Copyright (c) 2002 interactivetools.com, inc.
// This notice MUST stay intact for use (see license.txt).
//
// A free WYSIWYG editor replacement for <textarea> fields.
// For full source code and docs, visit http://www.interactivetools.com/
//
// Version 3.0 developed by Mihai Bazon for InteractiveTools.
//   http://dynarch.com/mishoo
//
// $Id$

function FullPage(editor) {
	this.editor = editor;

	var cfg = editor.config;
	cfg.fullPage = true;
	var self = this;

	cfg.registerButton("FP-docprop", this._lc("Document properties"), editor.imgURL("docprop.gif", "FullPage"), false,
			   function(editor, id) {
				   self.buttonPress(editor, id);
			   });

	// add a new line in the toolbar
	cfg.addToolbarElement(["separator","FP-docprop"],"separator",-1);
};

FullPage._pluginInfo = {
	name          : "FullPage",
	version       : "1.0",
	developer     : "Mihai Bazon",
	developer_url : "http://dynarch.com/mishoo/",
	c_owner       : "Mihai Bazon",
	sponsor       : "Thycotic Software Ltd.",
	sponsor_url   : "http://thycotic.com",
	license       : "htmlArea"
};

FullPage.prototype._lc = function(string) {
    return HTMLArea._lc(string, 'FullPage');
};

FullPage.prototype.buttonPress = function(editor, id) {
	var self = this;
	switch (id) {
	    case "FP-docprop":
		var doc = editor._doc;
		var links = doc.getElementsByTagName("link");
		var style1 = '';
		var style2 = '';
		var keywords = '';
		var description = '';
		var charset = '';
		for (var i = links.length; --i >= 0;) {
			var link = links[i];
			if (/stylesheet/i.test(link.rel)) {
				if (/alternate/i.test(link.rel))
					style2 = link.href;
				else
					style1 = link.href;
			}
		}
		var metas = doc.getElementsByTagName("meta");
		for (var i = metas.length; --i >= 0;) {
			var meta = metas[i];
			if (/content-type/i.test(meta.httpEquiv)) {
				r = /^text\/html; *charset=(.*)$/i.exec(meta.content);
				charset = r[1];
			} else if ((/keywords/i.test(meta.name)) || (/keywords/i.test(meta.id))) {
				keywords = meta.content;
			}	else if ((/description/i.test(meta.name)) || (/description/i.test(meta.id))) {
				description = meta.content;
			}
		}
		var title = doc.getElementsByTagName("title")[0];
		title = title ? title.innerHTML : '';
		var init = {
			f_doctype      : editor.doctype,
			f_title        : title,
			f_body_bgcolor : HTMLArea._colorToRgb(doc.body.style.backgroundColor),
			f_body_fgcolor : HTMLArea._colorToRgb(doc.body.style.color),
			f_base_style   : style1,
			f_alt_style    : style2,
			f_charset      : charset,
			f_keywords     : keywords,
			f_description  : description,
			editor         : editor
		};
		editor._popupDialog("plugin://FullPage/docprop", function(params) {
			self.setDocProp(params);
		}, init);
		break;
	}
};

FullPage.prototype.setDocProp = function(params) {
	var txt = "";
	var doc = this.editor._doc;
	var head = doc.getElementsByTagName("head")[0];
	var links = doc.getElementsByTagName("link");
	var metas = doc.getElementsByTagName("meta");
	var style1 = null;
	var style2 = null;
	var charset = null;
	var charset_meta = null;
	var keywords = null;
	var description = null;
	for (var i = links.length; --i >= 0;) {
		var link = links[i];
		if (/stylesheet/i.test(link.rel)) {
			if (/alternate/i.test(link.rel))
				style2 = link;
			else
				style1 = link;
		}
	}
	for (var i = metas.length; --i >= 0;) {
		var meta = metas[i];
		if (/content-type/i.test(meta.httpEquiv)) {
			r = /^text\/html; *charset=(.*)$/i.exec(meta.content);
			charset = r[1];
			charset_meta = meta;
		} else if ((/keywords/i.test(meta.name)) || (/keywords/i.test(meta.id))) {
			keywords = meta;
		}	else if ((/description/i.test(meta.name)) || (/description/i.test(meta.id))) {
			description = meta;
		}
	}
	function createLink(alt) {
		var link = doc.createElement("link");
		link.rel = alt ? "alternate stylesheet" : "stylesheet";
		head.appendChild(link);
		return link;
	};
	function createMeta(httpEquiv, name, content) {
		var meta = doc.createElement("meta");
		if (httpEquiv!="") meta.httpEquiv = httpEquiv;
		if (name!="") meta.name = name;
		if (name!="") meta.id = name;
		meta.content = content;
		head.appendChild(meta);
		return meta;
	};

	if (!style1 && params.f_base_style)
		style1 = createLink(false);
	if (params.f_base_style)
		style1.href = params.f_base_style;
	else if (style1)
		head.removeChild(style1);

	if (!style2 && params.f_alt_style)
		style2 = createLink(true);
	if (params.f_alt_style)
		style2.href = params.f_alt_style;
	else if (style2)
		head.removeChild(style2);

	if (charset_meta) {
		head.removeChild(charset_meta);
		charset_meta = null;
	}
	if (!charset_meta && params.f_charset)
		charset_meta = createMeta("Content-Type","", "text/html; charset="+params.f_charset);

	if (!keywords && params.f_keywords)
		keywords = createMeta("","keywords", params.f_keywords);
	else if (params.f_keywords)
		keywords.content = params.f_keywords;
	else if (keywords)
		head.removeChild(keywords);

	if (!description && params.f_description)
		description = createMeta("","description", params.f_description);
	else if (params.f_description)
		description.content = params.f_description;
	else if (description)
		head.removeChild(description);

  	for (var i in params) {
		var val = params[i];
		switch (i) {
		    case "f_title":
			var title = doc.getElementsByTagName("title")[0];
			if (!title) {
				title = doc.createElement("title");
				head.appendChild(title);
			} else while (node = title.lastChild)
				title.removeChild(node);
			if (!HTMLArea.is_ie)
				title.appendChild(doc.createTextNode(val));
			else
				doc.title = val;
			break;
		    case "f_doctype":
			this.editor.setDoctype(val);
			break;
		    case "f_body_bgcolor":
			doc.body.style.backgroundColor = val;
			break;
		    case "f_body_fgcolor":
			doc.body.style.color = val;
			break;
		}
	}
};