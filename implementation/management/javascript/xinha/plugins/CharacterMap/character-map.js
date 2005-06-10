// Character Map plugin for HTMLArea
// Sponsored by http://www.systemconcept.de
// Implementation by Holger Hees based on HTMLArea XTD 1.5 (http://mosforge.net/projects/htmlarea3xtd/)
// Original Author - Bernhard Pfeifer novocaine@gmx.net 
//
// (c) systemconcept.de 2004
// Distributed under the same terms as HTMLArea itself.
// This notice MUST stay intact for use (see license.txt).

function CharacterMap(editor) {
  this.editor = editor;
	var cfg = editor.config;
	var self = this;
  cfg.registerButton({
                id       : "insertcharacter",
                tooltip  : this._lc("Insert special character"),
                image    : editor.imgURL("ed_charmap.gif", "CharacterMap"),
                textMode : false,
                action   : function(editor) {
                                self.buttonPress(editor);
                           }
            })
	cfg.addToolbarElement("insertcharacter", "inserthorizontalrule", 1);
};

CharacterMap._pluginInfo = {
	name          : "CharacterMap",
	version       : "1.0",
	developer     : "Holger Hees & Bernhard Pfeifer",
	developer_url : "http://www.systemconcept.de/",
	c_owner       : "Holger Hees & Bernhard Pfeifer",
	sponsor       : "System Concept GmbH & Bernhard Pfeifer",
	sponsor_url   : "http://www.systemconcept.de/",
	license       : "htmlArea"
};

CharacterMap.prototype._lc = function(string) {
    return HTMLArea._lc(string, 'CharacterMap');
}

CharacterMap.prototype.buttonPress = function(editor) {
    editor._popupDialog( "plugin://CharacterMap/select_character", function( entity ) 
    {
        if ( !entity ) 
        {  
            //user must have pressed Cancel
            return false;
        }
        
        editor.insertHTML( entity );
               
    }, null);
}

