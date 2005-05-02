function InsertAnchor(editor) {
  this.editor = editor;
  var cfg = editor.config;
  var bl = InsertAnchor.btnList;
  var self = this;

  // register the toolbar buttons provided by this plugin
  var toolbar = [];
  for (var i in bl) {
    if(typeof bl[i] == 'function') continue;
    var btn = bl[i];
    if (!btn) {
      toolbar.push("separator");
    }
    else {
      var id = "IA-" + btn[0];
      cfg.registerButton(id, this._lc("Insert Anchor"), editor.imgURL(btn[0] + ".gif", "InsertAnchor"), false,
             function(editor, id) {
               // dispatch button press event
               self.buttonPress(editor, id);
             }, btn[1]);
      toolbar.push(id);
    }
  }

  for (var i in toolbar) {
    cfg.toolbar[0].push(toolbar[i]);
  }
}

InsertAnchor._pluginInfo = {
  name          : "InsertAnchor",
  version       : "1.0",
  developer     : "Andre Rabold",
  developer_url : "http://www.mr-printware.de",
  c_owner       : "Andre Rabold",
  sponsor       : "MR Printware GmbH",
  sponsor_url   : "http://www.mr-printware.de",
  license       : "htmlArea"
};

InsertAnchor.prototype._lc = function(string) {
    return HTMLArea._lc(string, 'InsertAnchor');
}

InsertAnchor.btnList = [
  null, // separator
  ["insert-anchor"]
];

InsertAnchor.prototype.onGenerate = function() {
  var style_id = "IA-style"
  var style = this.editor._doc.getElementById(style_id);
  if (style == null) {
    style = this.editor._doc.createElement("link");
    style.id = style_id;
    style.rel = 'stylesheet';
    style.href = _editor_url + 'plugins/InsertAnchor/insert-anchor.css';
    this.editor._doc.getElementsByTagName("HEAD")[0].appendChild(style);
  }
}

InsertAnchor.prototype.buttonPress = function(editor, id) {
  var anchor;
  anchor = prompt(this._lc("Anchor name"), "");
  if (anchor == "" || anchor == null) {
    return;
  }

  try {
    var doc = editor._doc;
    var alink = doc.createElement("a");
    alink.id = anchor;
    alink.name = anchor;
    alink.title = anchor;
    alink.className = "anchor";
    if (HTMLArea.is_ie) {
      var sel = editor._getSelection();
      var range = editor._createRange(sel);
      range.pasteHTML(alink.outerHTML);
    } else {
      editor.insertNodeAtSelection(alink);
    }
  }
  catch (e) { }
}
