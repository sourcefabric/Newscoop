// Unormat plugin for HTMLArea


function UnFormat(editor) {
  this.editor = editor;
  var cfg = editor.config;
  var self = this;

  cfg.registerButton({
    id       : "unformat",
    tooltip  : this._lc("Page Cleaner"),
    image    : editor.imgURL("unformat.gif", "UnFormat"),
    textMode : false,
    action   : function(editor) {
                 self.buttonPress(editor);
               }
  });

  cfg.addToolbarElement("unformat", "killword", 1);
}

UnFormat._pluginInfo = {
  name          : "UnFormat",
  version       : "1.0",
  license       : "htmlArea"
};


UnFormat.prototype._lc = function(string) {
  return HTMLArea._lc(string, 'UnFormat');
};

UnFormat.prototype.buttonPress = function(editor){
  editor._popupDialog( "plugin://UnFormat/unformat", function( param){
    if (param) {
      if (param["cleaning_area"] == "all") {
        var html = editor._doc.body.innerHTML;
      } else {
        var html = editor.getSelectedHTML();
      }

      if (param["html_all"]== true) {
        html = html.replace(/<[\!]*?[^<>]*?>/g, ""); 				
      }

      if (param["formatting"] == true) {
        html = html.replace(/style="[^"]*"/gi, "");
        html = html.replace(/<\/?font[^>]*>/gi,"");
        html = html.replace(/<\/?b>/gi,"");
        html = html.replace(/<\/?strong[^>]*>/gi,"");
        html = html.replace(/<\/?i>/gi,"");
        html = html.replace(/<\/?em[^>]*>/gi,"");
        html = html.replace(/<\/?u[^>]*>/gi,"");
        html = html.replace(/<\/?strike[^>]*>/gi,"");
        html = html.replace(/ align=[^\s|>]*/gi,"");
        html = html.replace(/ class=[^\s|>]*/gi,"");
      }
      if (param["cleaning_area"] == "all") {
        editor._doc.body.innerHTML = html;
      } else	{
        editor.insertHTML(html);
      }
    } else { return false; }
  }, null);
};