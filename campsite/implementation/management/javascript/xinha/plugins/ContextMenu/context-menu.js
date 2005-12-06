// Context Menu Plugin for HTMLArea-3.0
// Sponsored by www.americanbible.org
// Implementation by Mihai Bazon, http://dynarch.com/mishoo/
//
// (c) dynarch.com 2003.
// Distributed under the same terms as HTMLArea itself.
// This notice MUST stay intact for use (see license.txt).
//
// $Id$

HTMLArea.loadStyle("menu.css", "ContextMenu");

function ContextMenu(editor) {
	this.editor = editor;
}

ContextMenu._pluginInfo = {
	name          : "ContextMenu",
	version       : "1.0",
	developer     : "Mihai Bazon",
	developer_url : "http://dynarch.com/mishoo/",
	c_owner       : "dynarch.com",
	sponsor       : "American Bible Society",
	sponsor_url   : "http://www.americanbible.org",
	license       : "htmlArea"
};

ContextMenu.prototype.onGenerate = function() {
	var self = this;
	var doc = this.editordoc = this.editor._iframe.contentWindow.document;
	HTMLArea._addEvents(doc, ["contextmenu"],
			    function (event) {
				    return self.popupMenu(HTMLArea.is_ie ? self.editor._iframe.contentWindow.event : event);
			    });
	this.currentMenu = null;
};

ContextMenu.prototype.getContextMenu = function(target) {
	var self = this;
	var editor = this.editor;
	var config = editor.config;
	var menu = [];
	var tbo = this.editor.plugins.TableOperations;
	if (tbo) tbo = tbo.instance;

	var selection = editor.hasSelectedText();
	if (selection)
		menu.push([ HTMLArea._lc("Cut", "ContextMenu"), function() { editor.execCommand("cut"); }, null, config.btnList["cut"][1] ],
			  [ HTMLArea._lc("Copy", "ContextMenu"), function() { editor.execCommand("copy"); }, null, config.btnList["copy"][1] ]);
	menu.push([ HTMLArea._lc("Paste", "ContextMenu"), function() { editor.execCommand("paste"); }, null, config.btnList["paste"][1] ]);

	var currentTarget = target;
	var elmenus = [];

	var link = null;
	var table = null;
	var tr = null;
	var td = null;
	var img = null;

	function tableOperation(opcode) {
		tbo.buttonPress(editor, opcode);
	}

	function insertPara(after) {
		var el = currentTarget;
		var par = el.parentNode;
		var p = editor._doc.createElement("p");
		p.appendChild(editor._doc.createElement("br"));
		par.insertBefore(p, after ? el.nextSibling : el);
		var sel = editor._getSelection();
		var range = editor._createRange(sel);
		if (!HTMLArea.is_ie) {
			sel.removeAllRanges();
			range.selectNodeContents(p);
			range.collapse(true);
			sel.addRange(range);
		} else {
			range.moveToElementText(p);
			range.collapse(true);
			range.select();
		}
	}

	for (; target; target = target.parentNode) {
		var tag = target.tagName;
		if (!tag)
			continue;
		tag = tag.toLowerCase();
		switch (tag) {
		    case "img":
			img = target;
			elmenus.push(null,
				     [ HTMLArea._lc("_Image Properties...", "ContextMenu"),
				       function() {
					       editor._insertImage(img);
				       },
				       HTMLArea._lc("Show the image properties dialog", "ContextMenu"),
				       config.btnList["insertimage"][1] ]
				);
			break;
		    case "a":
			link = target;
			elmenus.push(null,
				     [ HTMLArea._lc("_Modify Link...", "ContextMenu"),
               function() { editor.config.btnList['createlink'][3](editor); },
				       HTMLArea._lc("Current URL is", "ContextMenu") + ': ' + link.href,
				       config.btnList["createlink"][1] ],

				     [ HTMLArea._lc("Chec_k Link...", "ContextMenu"),
				       function() { window.open(link.href); },
				       HTMLArea._lc("Opens this link in a new window", "ContextMenu") ],

				     [ HTMLArea._lc("_Remove Link...", "ContextMenu"),
				       function() {
					       if (confirm(HTMLArea._lc("Please confirm that you want to unlink this element.", "ContextMenu") + "\n" +
							   HTMLArea._lc("Link points to:", "ContextMenu") + " " + link.href)) {
						       while (link.firstChild)
							       link.parentNode.insertBefore(link.firstChild, link);
						       link.parentNode.removeChild(link);
					       }
				       },
				       HTMLArea._lc("Unlink the current element", "ContextMenu") ]
				);
			break;
		    case "td":
			td = target;
			if (!tbo) break;
			elmenus.push(null,
				     [ HTMLArea._lc("C_ell Properties...", "ContextMenu"),
				       function() { tableOperation("TO-cell-prop"); },
				       HTMLArea._lc("Show the Table Cell Properties dialog", "ContextMenu"),
				       config.btnList["TO-cell-prop"][1] ]
				);
			break;
		    case "tr":
			tr = target;
			if (!tbo) break;
			elmenus.push(null,
				     [ HTMLArea._lc("Ro_w Properties...", "ContextMenu"),
				       function() { tableOperation("TO-row-prop"); },
				       HTMLArea._lc("Show the Table Row Properties dialog", "ContextMenu"),
				       config.btnList["TO-row-prop"][1] ],

				     [ HTMLArea._lc("I_nsert Row Before", "ContextMenu"),
				       function() { tableOperation("TO-row-insert-above"); },
				       HTMLArea._lc("Insert a new row before the current one", "ContextMenu"),
				       config.btnList["TO-row-insert-above"][1] ],

				     [ HTMLArea._lc("In_sert Row After", "ContextMenu"),
				       function() { tableOperation("TO-row-insert-under"); },
				       HTMLArea._lc("Insert a new row after the current one", "ContextMenu"),
				       config.btnList["TO-row-insert-under"][1] ],

				     [ HTMLArea._lc("_Delete Row", "ContextMenu"),
				       function() { tableOperation("TO-row-delete"); },
				       HTMLArea._lc("Delete the current row", "ContextMenu"),
				       config.btnList["TO-row-delete"][1] ]
				);
			break;
		    case "table":
			table = target;
			if (!tbo) break;
			elmenus.push(null,
				     [ HTMLArea._lc("_Table Properties...", "ContextMenu"),
				       function() { tableOperation("TO-table-prop"); },
				       HTMLArea._lc("Show the Table Properties dialog", "ContextMenu"),
				       config.btnList["TO-table-prop"][1] ],

				     [ HTMLArea._lc("Insert _Column Before", "ContextMenu"),
				       function() { tableOperation("TO-col-insert-before"); },
				       HTMLArea._lc("Insert a new column before the current one", "ContextMenu"),
				       config.btnList["TO-col-insert-before"][1] ],

				     [ HTMLArea._lc("Insert C_olumn After", "ContextMenu"),
				       function() { tableOperation("TO-col-insert-after"); },
				       HTMLArea._lc("Insert a new column after the current one", "ContextMenu"),
				       config.btnList["TO-col-insert-after"][1] ],

				     [ HTMLArea._lc("De_lete Column", "ContextMenu"),
				       function() { tableOperation("TO-col-delete"); },
				       HTMLArea._lc("Delete the current column", "ContextMenu"),
				       config.btnList["TO-col-delete"][1] ]
				);
			break;
		    case "body":
			elmenus.push(null,
				     [ HTMLArea._lc("Justify Left", "ContextMenu"),
				       function() { editor.execCommand("justifyleft"); }, null,
				       config.btnList["justifyleft"][1] ],
				     [ HTMLArea._lc("Justify Center", "ContextMenu"),
				       function() { editor.execCommand("justifycenter"); }, null,
				       config.btnList["justifycenter"][1] ],
				     [ HTMLArea._lc("Justify Right", "ContextMenu"),
				       function() { editor.execCommand("justifyright"); }, null,
				       config.btnList["justifyright"][1] ],
				     [ HTMLArea._lc("Justify Full", "ContextMenu"),
				       function() { editor.execCommand("justifyfull"); }, null,
				       config.btnList["justifyfull"][1] ]
				);
			break;
		}
	}

	if (selection && !link)
		menu.push(null, [ HTMLArea._lc("Make lin_k...", "ContextMenu"),
           function() { editor.config.btnList['createlink'][3](editor); },
				  HTMLArea._lc("Create a link", "ContextMenu"),
				  config.btnList["createlink"][1] ]);

	for (var i = 0; i < elmenus.length; ++i)
		menu.push(elmenus[i]);

	if (!/html|body/i.test(currentTarget.tagName))
		menu.push(null,
			  [ HTMLArea._lc({string: "Remove the $elem Element...", replace: {elem: "&lt;" + currentTarget.tagName + "&gt;"}}, "ContextMenu"),
			    function() {
				    if (confirm(HTMLArea._lc("Please confirm that you want to remove this element:", "ContextMenu") + " " +
						currentTarget.tagName)) {
					    var el = currentTarget;
					    var p = el.parentNode;
					    p.removeChild(el);
					    if (HTMLArea.is_gecko) {
						    if (p.tagName.toLowerCase() == "td" && !p.hasChildNodes())
							    p.appendChild(editor._doc.createElement("br"));
						    editor.forceRedraw();
						    editor.focusEditor();
						    editor.updateToolbar();
						    if (table) {
							    var save_collapse = table.style.borderCollapse;
							    table.style.borderCollapse = "collapse";
							    table.style.borderCollapse = "separate";
							    table.style.borderCollapse = save_collapse;
						    }
					    }
				    }
			    },
			    HTMLArea._lc("Remove this node from the document", "ContextMenu") ],
			  [ HTMLArea._lc("Insert paragraph before", "ContextMenu"),
			    function() { insertPara(false); },
			    HTMLArea._lc("Insert a paragraph before the current node", "ContextMenu") ],
			  [ HTMLArea._lc("Insert paragraph after", "ContextMenu"),
			    function() { insertPara(true); },
			    HTMLArea._lc("Insert a paragraph after the current node", "ContextMenu") ]
			  );
	return menu;
};

ContextMenu.prototype.popupMenu = function(ev) {
	var self = this;
	if (this.currentMenu)
		this.currentMenu.parentNode.removeChild(this.currentMenu);
	function getPos(el) {
		var r = { x: el.offsetLeft, y: el.offsetTop };
		if (el.offsetParent) {
			var tmp = getPos(el.offsetParent);
			r.x += tmp.x;
			r.y += tmp.y;
		}
		return r;
	}
	function documentClick(ev) {
		ev || (ev = window.event);
		if (!self.currentMenu) {
			alert(HTMLArea._lc("How did you get here? (Please report!)", "ContextMenu"));
			return false;
		}
		var el = HTMLArea.is_ie ? ev.srcElement : ev.target;
		for (; el != null && el != self.currentMenu; el = el.parentNode);
		if (el == null)
			self.closeMenu();
		//HTMLArea._stopEvent(ev);
		//return false;
	}
	var keys = [];
	function keyPress(ev) {
		ev || (ev = window.event);
		HTMLArea._stopEvent(ev);
		if (ev.keyCode == 27) {
			self.closeMenu();
			return false;
		}
		var key = String.fromCharCode(HTMLArea.is_ie ? ev.keyCode : ev.charCode).toLowerCase();
		for (var i = keys.length; --i >= 0;) {
			var k = keys[i];
			if (k[0].toLowerCase() == key)
				k[1].__msh.activate();
		}
	}
	self.closeMenu = function() {
		self.currentMenu.parentNode.removeChild(self.currentMenu);
		self.currentMenu = null;
		HTMLArea._removeEvent(document, "mousedown", documentClick);
		HTMLArea._removeEvent(self.editordoc, "mousedown", documentClick);
		if (keys.length > 0)
			HTMLArea._removeEvent(self.editordoc, "keypress", keyPress);
		if (HTMLArea.is_ie)
			self.iePopup.hide();
	}
	var target = HTMLArea.is_ie ? ev.srcElement : ev.target;
     var ifpos = getPos(self.editor._htmlArea);//_iframe);
	var x = ev.clientX + ifpos.x;
	var y = ev.clientY + ifpos.y;

	var div;
	var doc;
	if (!HTMLArea.is_ie) {
		doc = document;
	} else {
		// IE stinks
		var popup = this.iePopup = window.createPopup();
		doc = popup.document;
		doc.open();
		doc.write("<html><head><style type='text/css'>@import url(" + _editor_url + "plugins/ContextMenu/menu.css); html, body { padding: 0px; margin: 0px; overflow: hidden; border: 0px; }</style></head><body unselectable='yes'></body></html>");
		doc.close();
	}
	div = doc.createElement("div");
	if (HTMLArea.is_ie)
		div.unselectable = "on";
	div.oncontextmenu = function() { return false; };
	div.className = "htmlarea-context-menu";
	if (!HTMLArea.is_ie)
		div.style.left = div.style.top = "0px";
	doc.body.appendChild(div);

	var table = doc.createElement("table");
	div.appendChild(table);
	table.cellSpacing = 0;
	table.cellPadding = 0;
	var parent = doc.createElement("tbody");
	table.appendChild(parent);

	var options = this.getContextMenu(target);
	for (var i = 0; i < options.length; ++i) {
		var option = options[i];
		var item = doc.createElement("tr");
		parent.appendChild(item);
		if (HTMLArea.is_ie)
			item.unselectable = "on";
		else item.onmousedown = function(ev) {
			HTMLArea._stopEvent(ev);
			return false;
		};
		if (!option) {
			item.className = "separator";
			var td = doc.createElement("td");
			td.className = "icon";
			var IE_IS_A_FUCKING_SHIT = '>';
			if (HTMLArea.is_ie) {
				td.unselectable = "on";
				IE_IS_A_FUCKING_SHIT = " unselectable='on' style='height=1px'>&nbsp;";
			}
			td.innerHTML = "<div" + IE_IS_A_FUCKING_SHIT + "</div>";
			var td1 = td.cloneNode(true);
			td1.className = "label";
			item.appendChild(td);
			item.appendChild(td1);
		} else {
			var label = option[0];
			item.className = "item";
			item.__msh = {
				item: item,
				label: label,
				action: option[1],
				tooltip: option[2] || null,
				icon: option[3] || null,
				activate: function() {
					self.closeMenu();
					self.editor.focusEditor();
					this.action();
				}
			};
			label = label.replace(/_([a-zA-Z0-9])/, "<u>$1</u>");
			if (label != option[0])
				keys.push([ RegExp.$1, item ]);
			label = label.replace(/__/, "_");
			var td1 = doc.createElement("td");
			if (HTMLArea.is_ie)
				td1.unselectable = "on";
			item.appendChild(td1);
			td1.className = "icon";
			if (item.__msh.icon)
      {
        var t = HTMLArea.makeBtnImg(item.__msh.icon, doc);
        td1.appendChild(t);
        // td1.innerHTML = "<img align='middle' src='" + item.__msh.icon + "' />";
      }
      var td2 = doc.createElement("td");
			if (HTMLArea.is_ie)
				td2.unselectable = "on";
			item.appendChild(td2);
			td2.className = "label";
			td2.innerHTML = label;
			item.onmouseover = function() {
				this.className += " hover";
				self.editor._statusBarTree.innerHTML = this.__msh.tooltip || '&nbsp;';
			};
			item.onmouseout = function() { this.className = "item"; };
			item.oncontextmenu = function(ev) {
				this.__msh.activate();
				if (!HTMLArea.is_ie)
					HTMLArea._stopEvent(ev);
				return false;
			};
			item.onmouseup = function(ev) {
				var timeStamp = (new Date()).getTime();
				if (timeStamp - self.timeStamp > 500)
					this.__msh.activate();
				if (!HTMLArea.is_ie)
					HTMLArea._stopEvent(ev);
				return false;
			};
			//if (typeof option[2] == "string")
			//item.title = option[2];
		}
	}

	if (!HTMLArea.is_ie) {
    /* FIXME: I think this is to stop the popup from running off the bottom of the screen?
		var dx = x + div.offsetWidth - window.innerWidth + 4;
		var dy = y + div.offsetHeight - window.innerHeight + 4;
    // alert('dy= (' + y + '+' + div.offsetHeight + '-' + window.innerHeight + ' + 4 ) = ' + dy);
		if (dx > 0) x -= dx;
		if (dy > 0) y -= dy;
    */
		div.style.left = x + "px";
		div.style.top = y + "px";
	} else {
    // To get the size we need to display the popup with some width/height
    // then we can get the actual size of the div and redisplay the popup at the
    // correct dimensions.
    this.iePopup.show(ev.screenX, ev.screenY, 300,50);
		var w = div.offsetWidth;
		var h = div.offsetHeight;
		this.iePopup.show(ev.screenX, ev.screenY, w, h);
	}

	this.currentMenu = div;
	this.timeStamp = (new Date()).getTime();

	HTMLArea._addEvent(document, "mousedown", documentClick);
	HTMLArea._addEvent(this.editordoc, "mousedown", documentClick);
	if (keys.length > 0)
		HTMLArea._addEvent(this.editordoc, "keypress", keyPress);

	HTMLArea._stopEvent(ev);
	return false;
};