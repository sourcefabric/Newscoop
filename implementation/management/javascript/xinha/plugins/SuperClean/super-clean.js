// Make our right side panel and insert appropriatly
function SuperClean(editor, args)
{
  this.editor = editor;
  var superclean = this;
  editor._superclean_on = false;
  editor.config.registerButton('superclean', this._lc("Clean Up HTML"), editor.imgURL('ed_superclean.gif', 'SuperClean'), true, function(e, objname, obj) { superclean._superClean(null, obj); });

  // See if we can find 'killword' and replace it with superclean
  editor.config.addToolbarElement("superclean", "killword", 0);
}

SuperClean._pluginInfo =
{
  name     : "SuperClean",
  version  : "1.0",
  developer: "James Sleeman",
  developer_url: "http://www.gogo.co.nz/",
  c_owner      : "Gogo Internet Services",
  license      : "htmlArea",
  sponsor      : "Gogo Internet Services",
  sponsor_url  : "http://www.gogo.co.nz/"
};

SuperClean.prototype._lc = function(string) {
    return HTMLArea._lc(string, 'SuperClean');
}

/** superClean combines HTMLTidy, Word Cleaning and font stripping into a single function
 *  it works a bit differently in how it asks for parameters */

SuperClean.prototype._superClean = function(opts, obj)
{
  var superclean = this;

  // Do the clean if we got options
  var doOK = function()
  {
    var opts = superclean._dialog.hide();
    var editor = superclean.editor;

    if(opts.word_clean) editor._wordClean();
    var D = editor.getInnerHTML();
    if(opts.faces)
    {
      D = D.replace(/face="[^"]*"/gi, '');
      // { (stops jedit's fold breaking)
      D = D.replace(/font-family:[^;}"']+;?/gi, '');
    }

    if(opts.sizes)
    {
      D = D.replace(/size="[^"]*"/gi, '');
      // { (stops jedit's fold breaking)
      D = D.replace(/font-size:[^;}"']+;?/gi, '');
    }

    if(opts.colors)
    {
      D = D.replace(/color="[^"]*"/gi, '');
      // { (stops jedit's fold breaking)
      D = D.replace(/([^-])color:[^;}"']+;?/gi, '$1');
    }

    D = D.replace(/(style|class)="\s*"/gi, '');
    D = D.replace(/<(font|span)\s*>/gi, '');
    editor.setHTML(D);

    if(editor.config.tidy_handler && opts.tidy)
    {
      HTMLArea._postback(editor.config.tidy_handler, {'content' : editor.getInnerHTML()},
                         function(javascriptResponse) { eval(javascriptResponse) });
    }
    return true;
  }
  var inputs = {};
  this._dialog.show(inputs, doOK);
}

// set to the URL of a handler for html tidy, this handler
//  (see tidy.php for an example) must that a single post variable
//  "content" which contains the HTML to tidy, and return javascript like
//  editor.setHTML('<strong>Tidied Html</strong>')
// it's called through XMLHTTPRequest
//
// set to false if you need to disable this.
HTMLArea.Config.prototype.tidy_handler = _editor_url + 'plugins/SuperClean/tidy.php';


SuperClean.prototype.onGenerate = function()
{
  this._dialog = new SuperClean.Dialog(this);
}
// Inline Dialog for SuperClean


SuperClean.Dialog = function (SuperClean)
{
  var  lDialog = this;
  this.Dialog_nxtid = 0;
  this.SuperClean = SuperClean;
  this.id = { }; // This will be filled below with a replace, nifty

  this.ready = false;
  this.files  = false;
  this.html   = false;
  this.dialog = false;

  // load the dTree script
  this._prepareDialog();

}

SuperClean.Dialog.prototype._prepareDialog = function()
{
  var lDialog = this;
  var SuperClean = this.SuperClean;

  if(this.html == false)
  {
    HTMLArea._getback(_editor_url + 'plugins/SuperClean/dialog.html', function(txt) { lDialog.html = txt; lDialog._prepareDialog(); });
    return;
  }
  var html = this.html;

  // Now we have everything we need, so we can build the dialog.
  var dialog = this.dialog = new HTMLArea.Dialog(SuperClean.editor, this.html, 'SuperClean');

  this.ready = true;
}

SuperClean.Dialog.prototype._lc = SuperClean.prototype._lc;

SuperClean.Dialog.prototype.show = function(inputs, ok, cancel)
{
  if(!this.ready)
  {
    var lDialog = this;
    window.setTimeout(function() {lDialog.show(inputs,ok,cancel);},100);
    return;
  }

  if(!this.SuperClean.editor.config.tidy_handler) {
    this.dialog.getElementById('divTidy').style.display = 'none';
  }

  // Connect the OK and Cancel buttons
  var dialog = this.dialog;
  var lDialog = this;
  if(ok)
  {
    this.dialog.getElementById('ok').onclick = ok;
  }
  else
  {
    this.dialog.getElementById('ok').onclick = function() {lDialog.hide();};
  }

  if(cancel)
  {
    this.dialog.getElementById('cancel').onclick = cancel;
  }
  else
  {
    this.dialog.getElementById('cancel').onclick = function() { lDialog.hide()};
  }

  // Show the dialog
  this.SuperClean.editor.disableToolbar(['fullscreen','SuperClean']);

  this.dialog.show(inputs);

  // Init the sizes
  this.dialog.onresize();
}

SuperClean.Dialog.prototype.hide = function()
{
  this.SuperClean.editor.enableToolbar();
  return this.dialog.hide();
}
