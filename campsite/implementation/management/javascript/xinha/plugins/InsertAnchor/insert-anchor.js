function InsertAnchor(editor) {
  this.editor = editor;
  var cfg = editor.config;
  var self = this;

  // register the toolbar buttons provided by this plugin
  cfg.registerButton({
  id       : "insert-anchor", 
  tooltip  : this._lc("Insert Anchor"), 
  image    : editor.imgURL("insert-anchor.gif", "InsertAnchor"),
  textMode : false,
  action   : function(editor) {
               self.buttonPress(editor);
             }
  });
  cfg.addToolbarElement("insert-anchor", "createlink", 1);
}

InsertAnchor._pluginInfo = {
  name          : "InsertAnchor",
  origin        : "version: 1.0, by Andre Rabold, MR Printware GmbH, http://www.mr-printware.de",
  version       : "2.0",
  developer     : "Udo Schmal",
  developer_url : "http://www.schaffrath-neuemedien.de",
  c_owner       : "Udo Schmal",
  sponsor       : "L.N.Schaffrath NeueMedien",
  sponsor_url   : "http://www.schaffrath-neuemedien.de",
  license       : "htmlArea"
};

InsertAnchor.prototype._lc = function(string) {
    return HTMLArea._lc(string, 'InsertAnchor');
}

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

InsertAnchor.prototype.buttonPress = function(editor) {
  var outparam = null;
  var html = editor.getSelectedHTML();
  var sel  = editor._getSelection();
  var range  = editor._createRange(sel);
  var  a = editor._activeElement(sel);
  if(!(a != null && a.tagName.toLowerCase() == 'a')) {
    a = editor._getFirstAncestor(sel, 'a'); 
  }
  if (a != null && a.tagName.toLowerCase() == 'a')
    outparam = { name : a.id };
  else
    outparam = { name : '' };

  editor._popupDialog( "plugin://InsertAnchor/insert_anchor", function( param ) {
    if ( param ) {
      var anchor = param["name"];
      if (anchor == "" || anchor == null) {
        if (a) {
          var child = a.innerHTML;
          a.parentNode.removeChild(a);
          editor.insertHTML(child);
        }
        return;
      } 
      try {
        var doc = editor._doc;
        if (!a) {
//          editor.surroundHTML('<a id="' + anchor + '" name="' + anchor + '" title="' + anchor + '" class="anchor">', '</a>');
          a = doc.createElement("a");
          a.id = anchor;
          a.name = anchor;
          a.title = anchor;
          a.className = "anchor";
          a.innerHTML = html;
          if (HTMLArea.is_ie) {
            range.pasteHTML(a.outerHTML);
          } else {
            editor.insertNodeAtSelection(a);
          }
        } else {
          a.id = anchor;
          a.name = anchor;
          a.title = anchor;
          a.className = "anchor";
        }
      }
      catch (e) { }
    }
  }, outparam);
}
