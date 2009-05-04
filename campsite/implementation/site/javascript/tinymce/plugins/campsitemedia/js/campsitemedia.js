/* Functions for the campsitemedia plugin popup */

tinyMCEPopup.requireLangPack();

var CampsiteMediaDialog = {

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
	var ed = tinyMCEPopup.editor;
	var dom = ed.dom;
	var topDoc = window.top.document;
	var parentWin = tinyMCEPopup.getWindowArg("window");

	parentWin.document.getElementById('src').value = topDoc.getElementById('f_url').value;
	ed.selection.setContent(dom.createHTML('a', {
		    href : topDoc.getElementById('f_url').value,
		    title : topDoc.getElementById('f_description').value
	    }, ed.selection.getContent()));
	tinyMCEPopup.close();
    },

    select : function(p_attachment_id, p_filename, p_description, p_selected) {
	var topDoc = window.top.document;

	var obj = topDoc.getElementById('f_attachment_id');
	obj.value = p_attachment_id;

	var obj = topDoc.getElementById('f_url');
	obj.value = p_filename;

	var obj = topDoc.getElementById('f_description');
	obj.value = p_description;

	var allPageTags = new Array();
	allPageTags = document.getElementsByTagName('*');
	for (i = 0; i < allPageTags.length; i++) {
	    if (allPageTags[i].className == 'block') {
		allPageTags[i].style.backgroundColor='';
	    }
	}

	document.getElementById('block_'+p_selected).style.backgroundColor='#FFC';
    },

    close : function() {
	tinyMCEPopup.close();
    }
};
