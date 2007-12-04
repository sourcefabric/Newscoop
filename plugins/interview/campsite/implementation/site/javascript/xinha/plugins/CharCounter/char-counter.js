// Charcounter for HTMLArea-3.0
// (c) Udo Schmal & L.N.Schaffrath NeueMedien
// Distributed under the same terms as HTMLArea itself.
// This notice MUST stay intact for use (see license.txt).

function CharCounter(editor) {
  this.editor = editor;
}

HTMLArea.Config.prototype.CharCounter =
{
  'showChar': true, // show the characters count,
  'showWord': true, // show the words count,
  'showHtml': true, // show the exact html count
  'separator': ' | ' // separator used to join informations
};

CharCounter._pluginInfo = {
  name          : "CharCounter",
  version       : "1.2",
  developer     : "Udo Schmal",
  developer_url : "http://www.schaffrath-neuemedien.de",
  sponsor       : "L.N.Schaffrath NeueMedien",
  sponsor_url   : "http://www.schaffrath-neuemedien.de",
  c_owner       : "Udo Schmal & L.N.Schaffrath NeueMedien",
  license       : "htmlArea"
};

CharCounter.prototype._lc = function(string) {
    return HTMLArea._lc(string, "CharCounter");
};


CharCounter.prototype.onGenerate = function() {
  var self = this;
  if (this.charCount==null) {
    var charCount = document.createElement("span");
    charCount.style.padding = "2px 5px";
    if(HTMLArea.is_ie) {
      charCount.style.styleFloat = "right";
    } else {
      charCount.style.cssFloat = "right";
    }
    var brk = document.createElement('div');
    brk.style.height =
    brk.style.width =
    brk.style.lineHeight =
    brk.style.fontSize = '1px';
    brk.style.clear = 'both';
    if(HTMLArea.is_ie) {
      this.editor._statusBarTree.style.styleFloat = "left";
    } else {
      this.editor._statusBarTree.style.cssFloat = "left";
    }
    this.editor._statusBar.appendChild(charCount);
    this.editor._statusBar.appendChild(brk);
    this.charCount = charCount;
  }
};

CharCounter.prototype._updateCharCount = function() {
  var editor = this.editor;
  var cfg = editor.config;
  var contents = editor.getHTML();
  var string = new Array();
  if (cfg.CharCounter.showHtml) {
    string[string.length] = this._lc("HTML") + ": " + contents.length;
  }
  if (cfg.CharCounter.showWord || cfg.CharCounter.showChar) {
    contents = contents.replace(/<\/?\s*!--[^-->]*-->/gi, "" );
    contents = contents.replace(/<(.+?)>/g, '');//Don't count HTML tags
    contents = contents.replace(/&nbsp;/gi, ' ');
    contents = contents.replace(/([\n\r\t])/g, ' ');//convert newlines and tabs into space
    contents = contents.replace(/(  +)/g, ' ');//count spaces only once
    contents = contents.replace(/&(.*);/g, ' ');//Count htmlentities as one keystroke
    contents = contents.replace(/^\s*|\s*$/g, '');//trim
  }
  if (cfg.CharCounter.showWord) {
    var words=0;
    for (var x=0;x<contents.length;x++)
    {
      if (contents.charAt(x) == " " ) {words++;}
    }
    if (words>=1) { words++; }
    string[string.length] = this._lc("Words") + ": " + words;
  }

  if (cfg.CharCounter.showChar) {
    string[string.length] = this._lc("Chars") + ": " + contents.length;
  }

  this.charCount.innerHTML = string.join(cfg.CharCounter.separator);
};

CharCounter.prototype.onUpdateToolbar = function() {
  this.charCount.innerHTML = this._lc("... in progress");
  if(this._timeoutID) {
    window.clearTimeout(this._timeoutID);
  }
  var e = this;
  this._timeoutID = window.setTimeout(function() {e._updateCharCount();}, 1000);
};

CharCounter.prototype.onMode = function (mode)
{
  //Hide Chars in statusbar when switching into textmode
  switch (mode)
  {
    case "textmode":
      this.charCount.style.display = "none";
      break;
    case "wysiwyg":
      this.charCount.style.display = "";
      break;
    default:
      alert("Mode <" + mode + "> not defined!");
      return false;
  }
};
