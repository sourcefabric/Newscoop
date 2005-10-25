// GUIDO Music Notation plugin for HTMLArea
// Implementation by Richard Christophe
// Original Author - Richard Christophe cvrichard@infonie.fr
//
// Distributed under the same terms as HTMLArea itself.
// This notice MUST stay intact for use (see license.txt).

// this is a collection of JavaScript routines that
// facilitate accessing the GUIDO NoteServer.
//
// These Functions can be used within WEB-Pages
// examples can be found at
// www.noteserver.org/javascript/index.html
//

function NoteServer(editor) {
  this.editor = editor;
  var cfg = editor.config;
  var self = this;

  cfg.registerButton({
                id       : "insertscore",
                tooltip  : this._lc("Insert GUIDO Music Notation"),
                image    : editor.imgURL("note.gif", "NoteServer"),
                textMode : false,
                action   : function(editor) {
                                self.buttonPress(editor);
                           }
            })
	cfg.addToolbarElement("insertscore", "insertimage", 1);
};

NoteServer._pluginInfo = {
  name          : "NoteServer",
  version       : "1.1",
  developer     : "Richard Christophe",
  developer_url : "http://piano-go.chez.tiscali.fr/guido.html",
  c_owner       : "Richard Christophe",
  sponsor       : "",
  sponsor_url   : "",
  license       : "htmlArea"
};

NoteServer.prototype._lc = function(string) {
  return HTMLArea._lc(string, 'NoteServer');
};

NoteServer.prototype.buttonPress = function(editor) {
  editor._popupDialog( "plugin://NoteServer/codenote", function(param) {
    if (!param) {	// user must have pressed Cancel
      return false;
    } else IncludeGuido(editor,param);
  }, null);
};

// this variable is the address of the noteserver
// can be set to another address (local address if availalble) 

// var noteserveraddress = "www.noteserver.org";
// var htmlbase = "";

// alternative: specify server-adress directly:
var noteserveraddress = "clef.cs.ubc.ca"; //"www.noteserver.org"
var htmlbase = "/salieri/nview";


// this is the version of the NoteServer used.
// one of "0_4", "0_5", "0_6", or "0_7", or ""
// "" means: take the current version
// var versionstring = "0_7";
var versionstring = "";

// this functions takes a GMN-string and returns the URL
// that converts it into a GIF-file
function GetGIFURL(gmnstring,zoom,pagenum) {
  gmnstring = escape(gmnstring);
  gmnstring = gmnstring.replace(/\//g,"%2F");

  if (!zoom) {
    zoom = "1.0";
  }
  if (!pagenum) {
    pagenum = "1";
  }

  var string = "http://" + noteserveraddress +
               "/scripts/salieri" + versionstring +
               "/gifserv.pl?" +
               "pagewidth=21" +
               "&pageheight=29.7" +
               "&zoomfactor=" + zoom +
               "&pagesizeadjust=yes" +
               "&outputformat=gif87" +
               "&pagenum=" + pagenum +
               "&gmndata=" + gmnstring;

  //	document.write(string);
  return string;
}

// this functions takes a GMN-string and returns the URL
// that converts it into a MIDI-file
function GetMIDIURL(gmnstring) {
  gmnstring = escape(gmnstring);
  gmnstring = gmnstring.replace(/\//g,"%2F");

  var string = "http://" + noteserveraddress +
               "/scripts/salieri" + versionstring + 
               "/midserv.pl?" +
               "gmndata=" + gmnstring;

  return string;
}

// this functions takes a GMN-string and returns the URL
// that insert Applet
function GetAPPLETURL(gmnstring,zoom) {
  gmnstring = escape(gmnstring);
  gmnstring = gmnstring.replace(/\//g,"%2F");

  var string = '<applet ' +
               'code="NoteServerApplet" ' +
               'codebase="http://' +
               noteserveraddress + htmlbase + '/java" ' +
               ' width=700 height=300>' +
               '<param name=server value="' +
               noteserveraddress + '">' +
               '<param name=serverVersion value="' +
               versionstring + '">' +
               '<param name=zoomFactor value="' +
               zoom + '">' +
               '<param name=pageWidth value="21">' +
               '<param name=pageHeight value="29.7">' +
               '<param name=gmn value="' +
               gmnstring + '">' +
               '</applet>';

  return string;
}

// This function takes a GUIDO string, accesses the
// NoteServer (address specified as a constant above)
// and then embeds the GIF-Image in the document.


function IncludeGuido(editor,param) {
  // this  holds the URL for retrieving the picture ...

  if (!param["f_zoom"])
    zoom = "";
  //if (!pagenum)
   // pagenum = "";

  var string = GetGIFURL(param["f_code"],param["f_zoom"],"");
  var stringmidi = GetMIDIURL(param["f_code"]);
  var string2 = "<br>";

if (param["f_applet"] == false ){
  if (((navigator.userAgent.toLowerCase().indexOf("msie") != -1)
    && (navigator.userAgent.toLowerCase().indexOf("opera") == -1))) {
    editor.focusEditor();
    editor.insertHTML("<img src=" + string + ">");
  }	else {
    img = new Image();
    img.src = string;

    var doc = editor._doc;
    var sel = editor._getSelection();
    var range = editor._createRange(sel);
    editor._doc.execCommand("insertimage", false, img.src);
  }
} else {
  var stringapplet = GetAPPLETURL(param["f_code"],param["f_zoom"]);
  string2 = string2 + stringapplet + "<br>";
}

// To test code source in textarea
//if (param["f_affcode"]) string2 = string2 + HTMLArea._lc("Source Code","NoteServer") + " :" + '<br> <textarea  cols=60 rows=10 style = "background: #FFFFE6">' +  param["f_code"] + '</textarea> <br>';

if (param["f_affcode"]) string2 = string2 + HTMLArea._lc("GUIDO Code","NoteServer") + " : "  + param["f_code"] + "<br>";
if (param["f_midi"]) string2 = string2 + "<a href=" + stringmidi + ">" + HTMLArea._lc("MIDI File","NoteServer") + "</a> <br>";

  editor.focusEditor();
  editor.insertHTML(string2);

    //var html = linktext.link(stringmidi);
    //editor.insertHTML(html);
};

// this routine includes the applet-definition 
function IncludeGuidoStringAsApplet(editor, gmnstring, zoom) {
  gmnstring = escape(gmnstring);
  gmnstring = gmnstring.replace(/\//g,"%2F");

  var string = '<applet ' + 
               'codebase="http://' + noteserveraddress + htmlbase + '/java"\n' +
               'code="NoteServerApplet" width=480 height=230>' +
               "<PARAM NAME=server VALUE='" + noteserveraddress + "'>" +
               "<PARAM NAME=serverVersion VALUE='" + versionstring + "'>" +
               "<PARAM NAME=zoomFactor VALUE='"	+ zoom + "'>" +
               '<param name=pageWidth value="21">' +
               '<param name=pageHeight value="29.7">' +
               "<PARAM NAME=gmn VALUE='" + gmnstring + "'>" +
               "</applet>";
  alert(string);
  editor.focusEditor();
  editor.insertHTML(string);
};