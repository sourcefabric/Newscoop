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
}

function comboSelectValue(c, val) {
	var ops = c.getElementsByTagName("option");
	for (var i = ops.length; --i >= 0;) {
		var op = ops[i];
		op.selected = (op.value == val);
	}
	c.value = val;
}

function __dlg_onclose() {
	opener.Dialog._return(null);
}

function __dlg_init(bottom, win_dim) {
  if(window.__dlg_init_done) return true;
  
  if(window.opener._editor_skin != "") {
    var head = document.getElementsByTagName("head")[0];
    var link = document.createElement("link");
    link.type = "text/css";
    link.href = window.opener._editor_url + 'skins/' + window.opener._editor_skin + '/skin.css';
    link.rel = "stylesheet";
    head.appendChild(link);
  }
	window.dialogArguments = opener.Dialog._arguments;

  var body        = document.body;
  
  if(win_dim)
  {
    window.resizeTo(win_dim.width, win_dim.height);
    if(win_dim.top && win_dim.left)
    {
      window.moveTo(win_dim.left,win_dim.top);
    }
    else
    {
      var x = opener.screenX + (opener.outerWidth - win_dim.width) / 2;
      var y = opener.screenY + (opener.outerHeight - win_dim.height) / 2;
      window.moveTo(x,y);
    }
  }
  else if (window.sizeToContent) {
		window.sizeToContent();
		window.sizeToContent();	// for reasons beyond understanding,
					// only if we call it twice we get the
					// correct size.
		window.addEventListener("unload", __dlg_onclose, true);
		window.innerWidth = body.offsetWidth + 5;
		window.innerHeight = body.scrollHeight + 2;
		// center on parent
		var x = opener.screenX + (opener.outerWidth - window.outerWidth) / 2;
		var y = opener.screenY + (opener.outerHeight - window.outerHeight) / 2;
		window.moveTo(x, y);
	} else {
		var docElm      = document.documentElement ? document.documentElement : null;    
		var body_height = body.scrollHeight;
    
		window.resizeTo(body.scrollWidth, body_height);
		var ch = docElm && docElm.clientHeight ? docElm.clientHeight : body.clientHeight;
		var cw = docElm && docElm.clientWidth  ? docElm.clientWidth  : body.clientWidth;
		
		window.resizeBy(body.offsetWidth - cw, body_height - ch);
		var W = body.offsetWidth;
		var H = 2 * body_height - ch;
		var x = (screen.availWidth - W) / 2;
		var y = (screen.availHeight - H) / 2;
		window.moveTo(x, y);
	}
	HTMLArea.addDom0Event(document.body, 'keypress', __dlg_close_on_esc);
  window.__dlg_init_done = true;
}

function __dlg_translate(context) {
	var types = ["input", "select", "legend", "span", "option", "td", "button", "div", "label", "a", "img"];
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
}

// closes the dialog and passes the return info upper.
function __dlg_close(val) {
	opener.Dialog._return(val);
	window.close();
}

function __dlg_close_on_esc(ev) {
	ev || (ev = window.event);
	if (ev.keyCode == 27) {
		window.close();
		return false;
	}
	return true;
}
