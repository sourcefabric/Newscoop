/**
 * Remove all <p ...> and </p> tags.
 */

function RemoveParagraphs(editor, args)
{
    this.editor = editor;
    var removeparagraphs = this;
    editor.config.registerButton('removeparagraphs', this._lc("Remove paragraphs"), editor.imgURL('ed_removeparagraphs.gif', 'RemoveParagraphs'), true, function(e, objname, obj) { removeparagraphs._removeParagraphs(null, obj); });

    // Add new button to the toolbar
    editor.config.addToolbarElement("removeparagraphs", ["ultraclean","removeformat"], +1);
}

RemoveParagraphs._pluginInfo =
{
  name     : "RemoveParagraphs",
  version  : "1.0",
  developer: "Paul Baranowski",
  developer_url: "http://campware.org/",
  c_owner      : "MDLF, Inc.",
  license      : "htmlArea",
  sponsor      : "MDLF, Inc.",
  sponsor_url  : "http://mdlf.org/"
};

RemoveParagraphs.prototype._lc = function(string) {
    return HTMLArea._lc(string, 'RemoveParagraphs');
}

RemoveParagraphs.prototype._removeParagraphs = function(opts, obj)
{
    var removeparagraphs = this;
    var editor = removeparagraphs.editor;
    
    var D = editor.getInnerHTML();
    D = D.replace(/<\s*p[^>]*>/gi, '');
    D = D.replace(/<\/\s*p\s*>/gi, '');
    editor.setHTML(D);
}

