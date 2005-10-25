// htmlArea v3.0 - Copyright (c) 2002, 2003 interactivetools.com, inc.
// This copyright notice MUST stay intact for use (see license.txt).
//
// Portions (c) dynarch.com, 2003
//
// A free WYSIWYG editor replacement for <textarea> fields.
// For full source code and docs, visit http://www.interactivetools.com/
//
// Version 3.0 developed by Mihai Bazon.
//   http://dynarch.com/mishoo
//
// $Id$
HTMLArea = window.opener.HTMLArea;

function getAbsolutePos(el) {
	var r = { x: el.offsetLeft, y: el.offsetTop };
	if (el.offsetParent) {
		var tmp = getAbsolutePos(el.offsetParent);
		r.x += tmp.x;
		r.y += tmp.y;
	}
	return r;
};

function comboSelectValue(c, val) {
	var ops = c.getElementsByTagName("option");
	for (var i = ops.length; --i >= 0;) {
		var op = ops[i];
		op.selected = (op.value == val);
	}
	c.value = val;
};

function __dlg_onclose() {
	opener.Dialog._return(null);
};

function __dlg_init(bottom) {
  if(window.opener._editor_skin != "") {
    var head = document.getElementsByTagName("head")[0];
    var link = document.createElement("link");
    link.type = "text/css";
    link.href = window.opener._editor_url + 'skins/' + window.opener._editor_skin + '/skin.css';
    link.rel = "stylesheet";
    head.appendChild(link);
  }
	var body = document.body;
	var body_height = 0;
	if (typeof bottom == "undefined") {
		var div = document.createElement("div");
		body.appendChild(div);
		var pos = getAbsolutePos(div);
		body_height = pos.y;
	} else {
		var pos = getAbsolutePos(bottom);
		body_height = pos.y + bottom.offsetHeight;
	}
	window.dialogArguments = opener.Dialog._arguments;
	if (!document.all) {
		window.sizeToContent();
		window.sizeToContent();	// for reasons beyond understanding,
					// only if we call it twice we get the
					// correct size.
		window.addEventListener("unload", __dlg_onclose, true);
		window.innerWidth = body.offsetWidth + 5;
		window.innerHeight = body_height + 2;
		// center on parent
		var x = opener.screenX + (opener.outerWidth - window.outerWidth) / 2;
		var y = opener.screenY + (opener.outerHeight - window.outerHeight) / 2;
		window.moveTo(x, y);
	} else {
		// window.dialogHeight = body.offsetHeight + 50 + "px";
		// window.dialogWidth = body.offsetWidth + "px";
		window.resizeTo(body.offsetWidth, body_height);
		var ch = body.clientHeight;
		var cw = body.clientWidth;
		window.resizeBy(body.offsetWidth - cw, body_height - ch);
		var W = body.offsetWidth;
		var H = 2 * body_height - ch;
		var x = (screen.availWidth - W) / 2;
		var y = (screen.availHeight - H) / 2;
		window.moveTo(x, y);
	}
	HTMLArea.addDom0Event(document.body, 'keypress', __dlg_close_on_esc);
};

function __dlg_translate(context) {
	var types = ["input", "select", "legend", "span", "option", "td", "button", "div", "label"];
	for (var type = 0; type < types.length; ++type) {
		var spans = document.getElementsByTagName(types[type]);
		for (var i = spans.length; --i >= 0;) {
			var span = spans[i];
			if (span.firstChild && span.firstChild.data) {
				var txt = HTMLArea._lc(span.firstChild.data, context);
				if (txt)
					span.firstChild.data = txt;
			}
                        if (span.title) {
				var txt = HTMLArea._lc(span.title, context);
				if (txt)
					span.title = txt;
                        }
		}
	}
    document.title = HTMLArea._lc(document.title, context);
};

// closes the dialog and passes the return info upper.
function __dlg_close(val) {
	opener.Dialog._return(val);
	window.close();
};

function __dlg_close_on_esc(ev) {
	ev || (ev = window.event);
	if (ev.keyCode == 27) {
		window.close();
		return false;
	}
	return true;
};
