/*---------------------------------------*\
 Insert Smiley Plugin for HTMLArea-3.0
 -----------------------------------------
 author: Ki Master George 
 e-mail: kimastergeorge@gmail.com
\*---------------------------------------*/

function InsertSmiley(editor) {
	this.editor = editor;

	var cfg = editor.config;
	var self = this;
	
	// register the toolbar buttons provided by this plugin
	cfg.registerButton({
	id       : "insertsmiley",
	tooltip  : this._lc("Insert Smiley"),
	image    : editor.imgURL("ed_smiley.gif", "InsertSmiley"),
	textMode : false,
	action   : function(editor) {
			self.buttonPress(editor);
		}
	})
	cfg.addToolbarElement("insertsmiley", "inserthorizontalrule", 1);
};

InsertSmiley._pluginInfo = {
  name          : "InsertSmiley",
  version       : "1.0",
  developer     : "Ki Master George",
  developer_url : "http://kimastergeorge.i4host.com/",
  c_owner       : "Ki Master George",
  sponsor       : "Ki Master George",
  sponsor_url   : "http://kimastergeorge.i4host.com/",
  license       : "htmlArea"
};

InsertSmiley.prototype._lc = function(string) {
    return HTMLArea._lc(string, 'InsertSmiley');
}

InsertSmiley.prototype.buttonPress = function(editor) {
	var self = this;
	var sel = editor.getSelectedHTML().replace(/(<[^>]*>|&nbsp;|\n|\r)/g,"");
	var param = new Object();
	param.editor = editor;
	param.editor_url = _editor_url;
	if(param.editor_url == "../") {
		param.editor_url = document.URL;
		param.editor_url = param.editor_url.replace(/^(.*\/).*\/.*$/g, "$1");
	}
	editor._popupDialog("plugin://InsertSmiley/insertsmiley", function(param) {
		editor.insertHTML("<img src=\"" + param.imgURL + "\" alt=\"Smiley\" />");
	}, param);
};
