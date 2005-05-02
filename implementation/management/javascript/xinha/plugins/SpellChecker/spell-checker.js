// Spell Checker Plugin for HTMLArea-3.0
// Sponsored by www.americanbible.org
// Implementation by Mihai Bazon, http://dynarch.com/mishoo/
//
// (c) dynarch.com 2003.
// Distributed under the same terms as HTMLArea itself.
// This notice MUST stay intact for use (see license.txt).
//
// $Id: spell-checker.js,v 1.1 2005/05/02 17:39:57 paul Exp $

HTMLArea.Config.prototype.SpellChecker = { 'backend': 'php', 'personalFilesDir' : '', 'defaultDictionary' : 'en_GB' };

function SpellChecker(editor) {
  this.editor = editor;

  var cfg = editor.config;
  var bl = SpellChecker.btnList;
  var self = this;

  // see if we can find the mode switch button, insert this before that
  var id = "SC-spell-check";
  cfg.registerButton(id, this._lc("Spell-check"), editor.imgURL("spell-check.gif", "SpellChecker"), false,
             function(editor, id) {
               // dispatch button press event
               self.buttonPress(editor, id);
             });

  var done = false;
  for(var i = 0; i < cfg.toolbar.length; i++)
  {
    if(cfg.toolbar[i].contains('htmlmode'))
    {
      var j = cfg.toolbar[i].indexOf('htmlmode');
      cfg.toolbar[i].splice(j,0,id);
      done = true;
      break;
    }
  }

  if(!done)
  {
    cfg.toolbar[0].push(id);
  }

  /*
  // register the toolbar buttons provided by this plugin
  var toolbar = [];
  for (var i = 0; i < bl.length; ++i)
  {
    var btn = bl[i];
    if (!btn)
    { // toolbar.push("separator");
    } else {
      var id = "SC-" + btn[0];
      cfg.registerButton(id, tt[id], editor.imgURL(btn[0] + ".gif", "SpellChecker"), false,
             function(editor, id) {
               // dispatch button press event
               self.buttonPress(editor, id);
             }, btn[1]);
      toolbar.push(id);
    }
  }

  for (var i = 0; i < toolbar.length; ++i) {
    cfg.toolbar[0].push(toolbar[i]);
  }
  */
};

SpellChecker._pluginInfo = {
  name          : "SpellChecker",
  version       : "1.0",
  developer     : "Mihai Bazon",
  developer_url : "http://dynarch.com/mishoo/",
  c_owner       : "Mihai Bazon",
  sponsor       : "American Bible Society",
  sponsor_url   : "http://www.americanbible.org",
  license       : "htmlArea"
};

SpellChecker.prototype._lc = function(string) {
    return HTMLArea._lc(string, 'SpellChecker');
}

SpellChecker.btnList = [
  null, // separator
  ["spell-check"]
  ];

SpellChecker.prototype.buttonPress = function(editor, id) {
  switch (id) {
      case "SC-spell-check":
    SpellChecker.editor = editor;
    SpellChecker.init = true;
    var uiurl = _editor_url + "plugins/SpellChecker/spell-check-ui.html";
    var win;
    if (HTMLArea.is_ie) {
      win = window.open(uiurl, "SC_spell_checker",
            "toolbar=no,location=no,directories=no,status=no,menubar=no," +
            "scrollbars=no,resizable=yes,width=600,height=450");
    } else {
      win = window.open(uiurl, "SC_spell_checker",
            "toolbar=no,menubar=no,personalbar=no,width=600,height=450," +
            "scrollbars=no,resizable=yes");
    }
    win.focus();
    break;
  }
};

// this needs to be global, it's accessed from spell-check-ui.html
SpellChecker.editor = null;
