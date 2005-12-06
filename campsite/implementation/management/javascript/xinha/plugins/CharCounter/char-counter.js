// Charcounter for HTMLArea-3.0
// (c) Udo Schmal & L.N.Schaffrath NeueMedien 
// Distributed under the same terms as HTMLArea itself.
// This notice MUST stay intact for use (see license.txt).

function CharCounter(editor) {
    this.editor = editor;
}

CharCounter._pluginInfo = {
    name : "CharCounter",
    version : "1.0",
    developer : "Udo Schmal",
    developer_url : "http://www.schaffrath-neuemedien.de",
    sponsor       : "L.N.Schaffrath NeueMedien",
    sponsor_url   : "http://www.schaffrath-neuemedien.de",
    c_owner : "Udo Schmal & L.N.Schaffrath NeueMedien",
    license : "htmlArea"
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

CharCounter.prototype.onUpdateToolbar = function() {
    this.updateCharCount();
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

CharCounter.prototype.updateCharCount = function(ev) {
    editor = this.editor;
    var contents = editor.getHTML();
    contents = contents.replace(/<(.+?)>/g, '');//Don't count HTML tags
    contents = contents.replace(/([\n\r\t])/g, ' ');//convert newlines and tabs into space
    contents = contents.replace(/(  +)/g, ' ');//count spaces only once
    contents = contents.replace(/&(.*);/g, ' ');//Count htmlentities as one keystroke
    contents = contents.replace(/^\s*|\s*$/g, '');//trim
//    var words=0;
//    for (var x=0;x<contents.length;x++) {
//      if (contents.charAt(x) == " " )  {words++}
//    }
//    this.charCount.innerHTML = this._lc("Words") + ": " + words + " | " + this._lc("Chars") + ": " + contents.length;
    this.charCount.innerHTML = this._lc("Chars") + ": " + contents.length;
    return(contents.length);
};