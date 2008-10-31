// Template plugin for HTMLArea
// Implementation by Udo Schmal based on HTMLArea 3.0
// Original Author - Udo Schmal www.Schaffrath-NeueMedien.de
//
// (c) Udo Schmal & Schaffrath NeueMedien 2004
// Distributed under the same terms as HTMLArea itself.
// This notice MUST stay intact for use (see license.txt).

function Template(editor) {
        this.editor = editor;
        var cfg = editor.config;
        var self = this;
	
	// register the toolbar buttons provided by this plugin
	cfg.registerButton({
	id       : "template",
	tooltip  : HTMLArea._lc("Insert template","Template"),
	image    : editor.imgURL("ed_template.gif", "Template"),
	textMode : false,
	action   : function(editor) {
			self.buttonPress(editor);
		}
	});
	cfg.addToolbarElement("template", "inserthorizontalrule", 1);
}

Template._pluginInfo = {
	name          : "Template",
	version       : "1.0",
	developer     : "Udo Schmal",
	developer_url : "http://www.schaffrath-neuemedien.de/",
	c_owner       : "Udo Schmal & Schaffrath NeueMedien",
	license       : "htmlArea"
};

Template.prototype.onGenerate = function() {
  var style_id = "Template-style"
  var style = this.editor._doc.getElementById(style_id);
  if (style == null) {
    style = this.editor._doc.createElement("link");
    style.id = style_id;
    style.rel = 'stylesheet';
    style.href = _editor_url + 'plugins/Template/template.css';
    this.editor._doc.getElementsByTagName("HEAD")[0].appendChild(style);
  }
};

Template.prototype.buttonPress = function(editor) {
  editor._popupDialog( "plugin://Template/template", function( obj ) {
    if ( !obj ) {//user must have pressed Cancel
      return false;
    }

    var bodys = editor._doc.getElementsByTagName("body");
    var body = bodys[0];

  function getElement(x) {
    var result = editor._doc.getElementById(x);
    if (!result) {
      result = editor._doc.createElement("div");
      result.id = x;
      result.innerHTML = x;
      body.appendChild(result);
    }
    if (result.style)
      result.removeAttribute("style");
    return result;
  }
  
    var content = getElement("content");
    var menu1 = getElement("menu1");
    var menu2 = getElement("menu2");
    var menu3 = getElement("menu3");
    switch (obj["templ"]) {
	    case "1": menu1.style.position = "absolute";
                menu1.style.right = "0px";
                menu1.style.width = "28%";
                menu1.style.backgroundColor = "#e1ddd9";
                menu1.style.padding = "2px 20px";
                content.style.position = "absolute";
                content.style.left = "0px";
                content.style.width = "70%";
                content.style.backgroundColor = "#fff";
                menu2.style.visibility = "hidden";
                menu3.style.visibility = "hidden";
                break;
      case "2": menu1.style.position = "absolute";
                menu1.style.left = "0px";
                menu1.style.width = "28%";
                menu1.style.height = "100%";
                menu1.style.backgroundColor = "#e1ddd9";
                content.style.position = "absolute";
                content.style.right = "0px";
                content.style.width = "70%";
                content.style.backgroundColor = "#fff";
                menu2.style.visibility = "hidden";
                menu3.style.visibility = "hidden";
                break
      case "3": menu1.style.position = "absolute";
                menu1.style.left = "0px";
                menu1.style.width = "28%";
                menu1.style.backgroundColor = "#e1ddd9";
                menu2.style.position = "absolute";
                menu2.style.right = "0px";
                menu2.style.width = "28%";
                menu2.style.backgroundColor = "#e1ddd9";
                content.style.position = "absolute";
                content.style.right = "30%";
                content.style.width = "60%";
                content.style.backgroundColor = "#fff";
                menu3.style.visibility = "hidden";
                break
    }
  }, null);
};