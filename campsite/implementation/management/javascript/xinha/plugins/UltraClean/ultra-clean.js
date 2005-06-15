// Make our right side panel and insert appropriatly
function UltraClean(editor, args)
{
    this.editor = editor;
    var ultraclean = this;
    editor.config.registerButton('ultraclean', this._lc("Clean Up HTML"), editor.imgURL('ed_rmformat.gif', 'UltraClean'), true, function(e, objname, obj) { ultraclean._ultraClean(null, obj); });

    // See if we can find 'killword' and replace it with ultraclean
    editor.config.addToolbarElement("ultraclean", "removeformat", 0);
}

UltraClean._pluginInfo =
{
  name     : "UltraClean",
  version  : "1.0",
  developer: "Paul Baranowski",
  developer_url: "http://campware.org/",
  c_owner      : "MDLF, Inc.",
  license      : "htmlArea",
  sponsor      : "MDLF, Inc.",
  sponsor_url  : "http://mdlf.org/"
};

UltraClean.prototype._lc = function(string) {
    return HTMLArea._lc(string, 'UltraClean');
}

/** UltraClean combines Word Cleaning and strips all span and font tags */
UltraClean.prototype._ultraClean = function(opts, obj)
{
    var ultraclean = this;
    var editor = ultraclean.editor;
    editor._wordClean();
    
    var D = editor.getInnerHTML();
    D = D.replace(/(lang|dir)="[^"]*"/gi, '');
    D = D.replace(/(style|class)\s*=\s*"[^"]*"/gi, '');
    D = D.replace(/<\s*(font|span)\s*>/gi, '');
    D = D.replace(/<\/\s*(font|span)\s*>/gi, '');
    editor.setHTML(D);
}

