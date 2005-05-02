// Make our right side panel and insert appropriatly
function SuperClean(editor, args)
{
  this.editor = editor;
  editor._superclean_on = false;
  editor.config.registerButton('superclean', this._lc("Clean Up HTML"), editor.imgURL('ed_superclean.gif', 'SuperClean'), true, function(e, objname, obj) { e._superClean(null, obj); });

  // See if we can find 'killword' and replace it with superclean
  var t = editor.config.toolbar;
  var done = false;
  for(var i = 0; i < t.length && !done; i++)
  {
    for(var x = 0; x < t[i].length && !done; x++)
    {
      if(t[i][x] == 'killword')
      {
        t[i][x] = 'superclean';
        done = true;
      }
    }
  }

  if(!done)
  {
    t[t.length-1].push('superclean');
  }
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

HTMLArea.prototype._superClean = function(opts, obj)
{
  var editor = this;

  // Do the clean if we got options
  if(opts)
  {
    if(opts.word_clean) this._wordClean();
    var D = this.getInnerHTML();
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
    this.setHTML(D);

    if(this.config.tidy_handler && opts.tidy)
    {
      HTMLArea._postback(this.config.tidy_handler, {'content' : this.getInnerHTML()},
                         function(javascriptResponse) { eval(javascriptResponse) });
    }
    return true;
  }

  // If already cleaning, then cancel it
  if(editor._superclean_on)
  {
    editor._superclean_on.click();
    return true;
  }

  // Otherwise ask for options
  var frm = document.createElement('div');
  frm.style.backgroundColor='window';
  frm.style.width  = this._iframe.style.width;
  frm.style.height = this._iframe.style.height;

  var win = document.createElement('div');
  win.style.padding = '5px';
  frm.appendChild(win);

  win.appendChild(document.createTextNode(HTMLArea._lc("Please select from the following cleaning options...", "SuperClean")));

  if(this.config.tidy_handler)
  {
    var div = document.createElement('div');
    var lab = document.createElement('label');
    var cb  = document.createElement('input');
    cb.setAttribute('type', 'checkbox');
    cb.setAttribute('name', 'tidy');
    lab.appendChild(cb);
    lab.appendChild(document.createTextNode(HTMLArea._lc("General tidy up and correction of some problems.", "SuperClean")));
    div.appendChild(lab);
    win.appendChild(div);
  }

  var div = document.createElement('div');
  var lab = document.createElement('label');
  var cb  = document.createElement('input');
  cb.setAttribute('type', 'checkbox');
  cb.setAttribute('name', 'word_clean');
  lab.appendChild(cb);
  lab.appendChild(document.createTextNode(HTMLArea._lc("Clean bad HTML from Microsoft Word", "SuperClean")));
  div.appendChild(lab);
  win.appendChild(div);

  var div = document.createElement('div');
  var lab = document.createElement('label');
  var cb  = document.createElement('input');
  cb.setAttribute('type', 'checkbox');
  cb.setAttribute('name', 'faces');
  lab.appendChild(cb);
  lab.appendChild(document.createTextNode(HTMLArea._lc('Remove custom typefaces (font "styles").', "SuperClean")));
  div.appendChild(lab);
  win.appendChild(div);

  var div = document.createElement('div');
  var lab = document.createElement('label');
  var cb  = document.createElement('input');
  cb.setAttribute('type', 'checkbox');
  cb.setAttribute('name', 'sizes');
  lab.appendChild(cb);
  lab.appendChild(document.createTextNode(HTMLArea._lc('Remove custom font sizes.', "SuperClean")));
  div.appendChild(lab);
  win.appendChild(div);

  var div = document.createElement('div');
  var lab = document.createElement('label');
  var cb  = document.createElement('input');
  cb.setAttribute('type', 'checkbox');
  cb.setAttribute('name', 'colors');
  lab.appendChild(cb);
  lab.appendChild(document.createTextNode(HTMLArea._lc('Remove custom text colors.', "SuperClean")));
  div.appendChild(lab);
  win.appendChild(div);

  var div = document.createElement('div');
  div.style.textAlign  = 'center';
  var but = document.createElement('input');
  but.setAttribute('type',  'button');
  but.setAttribute('value', HTMLArea._lc('Go', "SuperClean"));


  // We want it in text mode when we do the clean.
  var modeWhenDone = this._editMode;
  if(this._editMode != 'textmode')
  {
    this.setMode('textmode');
  }

  // But we don't want to see the textarea
  this._textArea.style.display = 'none';

  but.onclick = function()
  {
    f = frm;
    var elms = f.getElementsByTagName('input');
    cfg = { };
    for(var i = 0; i < elms.length; i++)
    {
      if(elms[i].getAttribute('type') == 'checkbox')
      {
        cfg[elms[i].getAttribute('name')] = elms[i].checked;
      }
    }

    editor._superClean(cfg, obj);
    editor._textArea.style.display = '';
    if(editor._editMode != modeWhenDone)
    {
      editor.setMode(modeWhenDone);
    }
    editor._superclean_on = false;
    frm.parentNode.removeChild(frm);
  }
  div.appendChild(but);

  var but = document.createElement('input');
  but.setAttribute('type',  'button');
  but.setAttribute('value', HTMLArea._lc('Cancel', "SuperClean"));
  but.onclick = function()
  {
    editor._textArea.style.display = '';
    if(editor._editMode != modeWhenDone)
    {
      editor.setMode(modeWhenDone);
    }
    editor._superclean_on = false;
    frm.parentNode.removeChild(frm);

  }
  div.appendChild(but);
  win.appendChild(div);
  editor._superclean_on = but;

  this._textArea.parentNode.insertBefore( frm, this._textArea );
}

// set to the URL of a handler for html tidy, this handler
//  (see tidy.php for an example) must that a single post variable
//  "content" which contains the HTML to tidy, and return javascript like
//  editor.setHTML('<strong>Tidied Html</strong>')
// it's called through XMLHTTPRequest
//
// set to false if you need to disable this.
HTMLArea.Config.prototype.tidy_handler = _editor_url + 'plugins/SuperClean/tidy.php';



