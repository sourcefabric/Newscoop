/* Functions for the campsiteattachment plugin popup */

tinyMCEPopup.requireLangPack();

var CampsiteAttachmentDialog = {

    init : function(ed) {
	tinyMCEPopup.resizeToInnerSize();

	var formObj = document.forms[0];
	var inst = tinyMCEPopup.editor;
	var elm = inst.selection.getNode();
	var action = "insert";
	var html;

	elm = inst.dom.getParent(elm, "A");
	if (elm != null && elm.nodeName == "A")
	    action = "update";

	formObj.insert.value = tinyMCEPopup.getLang(action, 'Insert', true); 

	if (action == "update")
	    var href = inst.dom.getAttrib(elm, 'href');
    },

    insert : function() {
	var ed = tinyMCEPopup.editor, dom = ed.dom;
	var topDoc = window.top.document;

	tinyMCEPopup.execCommand('mceInsertContent', false, dom.createHTML('img', {
	    src : topDoc.getElementById('f_url').value,
	    id : topDoc.getElementById('f_attachment_id').value
	}));

	tinyMCEPopup.close();
    },

    select : function(p_attachment_id, p_filename) {
	var topDoc = window.top.document;

	var obj = topDoc.getElementById('f_attachment_id');
	obj.value = p_attachment_id;

	var obj = topDoc.getElementById('f_url');
	obj.value = p_filename;
    },

    close : function() {
	tinyMCEPopup.close();
    }
};

// While loading
tinyMCEPopup.onInit.add(init);
