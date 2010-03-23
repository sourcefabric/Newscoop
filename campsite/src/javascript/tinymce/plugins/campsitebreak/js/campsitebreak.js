tinyMCEPopup.requireLangPack();

var CampsitebreakDialog = {
	init : function(ed) {
		tinyMCEPopup.resizeToInnerSize();
	},

	insert : function(string) {
		var ed = tinyMCEPopup.editor, dom = ed.dom;

		tinyMCEPopup.execCommand('mceInsertContent', false, dom.createHTML('dummy', {}, string));

		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(CampsitebreakDialog.init, CampsitebreakDialog);
