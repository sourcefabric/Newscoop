/* Functions for the campsiteimage plugin popup */

tinyMCEPopup.requireLangPack();

var CampsiteImageDialog = {

    init : function(ed) {
	tinyMCEPopup.resizeToInnerSize();
    },

    insert : function() {
	var ed = tinyMCEPopup.editor, dom = ed.dom;
	var topDoc = window.top.document;

	tinyMCEPopup.execCommand('mceInsertContent', false, dom.createHTML('img', {
	    src : topDoc.getElementById('f_url').value,
	    align : topDoc.getElementById('f_align').value,
            id : topDoc.getElementById('f_image_template_id').value + '_' + topDoc.getElementById('f_ratio').value,
	    alt : topDoc.getElementById('f_alt').value,
	    title : topDoc.getElementById('f_caption').value
	}));

	tinyMCEPopup.close();
    },

    select : function(p_image_template_id, p_filename, p_alt, p_title) {
	var topDoc = window.top.document;

	var obj = topDoc.getElementById('f_image_template_id');
	obj.value = p_image_template_id;

	var obj = topDoc.getElementById('f_url');
	obj.value = p_filename;

	var obj = topDoc.getElementById('f_alt');
	obj.value = p_alt;

	var obj = topDoc.getElementById('f_caption');
	obj.value = p_title;
    },

    close : function() {
	tinyMCEPopup.close();
    }
};

// While loading
tinyMCEPopup.onInit.add(init);
