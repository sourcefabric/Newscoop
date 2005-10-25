// BackgroundImage plugin for Xinha
// Sponsored by http://www.schaffrath-neuemedien.de
// Implementation by Udo Schmal
// based on TinyMCE (http://tinymce.moxiecode.com/) Distributed under LGPL by Moxiecode Systems AB
//
// Distributed under the same terms as HTMLArea itself.
// This notice MUST stay intact for use (see license.txt).

function BackgroundImage(editor) {
  this.editor = editor;
	var cfg = editor.config;
	var self = this;
  cfg.registerButton({
                id       : "bgImage",
                tooltip  : this._lc("Set page background image"),
                image    : editor.imgURL("ed_bgimage.gif", "BackgroundImage"),
                textMode : false,
                action   : function(editor) {
                                self.buttonPress(editor);
                           }
            })
	cfg.addToolbarElement("bgImage", "inserthorizontalrule", 1);
};

BackgroundImage._pluginInfo = {
	name          : "BackgroundImage",
	version       : "1.0",
	developer     : "Udo Schmal",
	developer_url : "http://www.schaffrath-neuemedien.de/",
	c_owner       : "Udo Schmal & Schaffrath NeueMedien",
	sponsor       : "L.N.Schaffrath NeueMedien",
	sponsor_url   : "http://www.schaffrath-neuemedien.de.de/",
	license       : "htmlArea"
};

BackgroundImage.prototype._lc = function(string) {
    return HTMLArea._lc(string, 'BackgroundImage');
};

BackgroundImage.prototype.buttonPress = function(editor) {
		//var doc = this.editor._doc;
    editor._popupDialog( "plugin://BackgroundImage/bgimage", function( bgImage ) {
        if(bgImage) {
					if(HTMLArea.is_ie) editor.focusEditor();
					if(bgImage=="*") {
						editor._doc.body.background = "";
					} else {
					  editor._doc.body.background = bgImage;
					}
				}	
    }, null);
};