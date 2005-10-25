// Paste Plain Text plugin for HTMLArea

// Distributed under the same terms as HTMLArea itself.
// This notice MUST stay intact for use (see license.txt).

function PasteText(editor) {
  this.editor = editor;
	var cfg = editor.config;
	var self = this;
        
	cfg.registerButton({
                id       : "pastetext",
                tooltip  : this._lc("Paste as Plain Text"),
                image    : editor.imgURL("ed_paste_text.gif", "PasteText"),
                textMode : false,
                action   : function(editor) {
                             self.buttonPress(editor);
                           }
            })

  cfg.addToolbarElement("pastetext", ["paste", "killword"], 1);

};

PasteText._pluginInfo = {
	name          : "PasteText",
	version       : "1.0",
	developer     : "Michael Harris",
	developer_url : "http://www.jonesadvisorygroup.com",
	c_owner       : "Jones Advisory Group",
	sponsor       : "Jones International University",
	sponsor_url   : "http://www.jonesinternational.edu",
	license       : "htmlArea"
};

PasteText.prototype._lc = function(string) {
    return HTMLArea._lc(string, 'PasteText');
};

PasteText.prototype.buttonPress = function(editor) {

	outparam = {
		
	}; 
	html=" ";
	editor._popupDialog( "plugin://PasteText/paste_text", function( html ) {
		html = html.replace(/\t/g,"&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;");
		html = html.replace(/\n/g,"</p><p>");
		html="<p>"+html;
		editor.insertHTML(html);
	}, outparam);
};