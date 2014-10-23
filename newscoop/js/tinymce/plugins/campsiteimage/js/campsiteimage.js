/* Functions for the campsiteimage plugin popup */

tinyMCEPopup.requireLangPack();

var CampsiteImageDialog = {
    init : function(ed) {
        if (captionsEnabled) {
            ed.windowManager.params.mce_height = ed.windowManager.params.mce_height + 100;
        }
        tinyMCEPopup.resizeToInnerSize();
    },

    edit_insert : function(command) {
        var ed = tinyMCEPopup.editor, dom = ed.dom;
        var topDoc = window.top.document;
        var re = /\"/;
        if (!captionsEnabled) {
            var alt = topDoc.getElementById('f_alt').value;
            var caption = topDoc.getElementById('f_caption').value;

            if ((alt.match(re)) || (caption.match(re))) {
                alert('Double quotes are not allowed for Alt and Caption fields.\nUse single quotes or double single quotes instead.');
                return false;
            }
        } else {
            if (!validateTinyMCEEditors()) {
                return false;
            }
        }

        var shrinkRatio = topDoc.getElementById('f_ratio').value;

        if (shrinkRatio < 1 || shrinkRatio > 99) {
            shrinkRatio = '';
        }
        else {
            shrinkRatio = '_' + shrinkRatio;
        }

        if (command == 'insert') var mce_command = 'mceInsertContent';
        if (command == 'edit') var mce_command = 'mceReplaceContent';

        var width = topDoc.getElementById('f_original_width').value;
        var height = topDoc.getElementById('f_original_height').value;

        if (topDoc.getElementById('f_ratio').value != '') {
            width = width * topDoc.getElementById('f_ratio').value * 0.01;
            height = height * topDoc.getElementById('f_ratio').value * 0.01;
        }

        if (topDoc.getElementById('f_resize_width').value != '' && topDoc.getElementById('f_resize_width').value != topDoc.getElementById('f_original_width').value && topDoc.getElementById('f_resize_width').value != width) width = topDoc.getElementById('f_resize_width').value;
        if (topDoc.getElementById('f_resize_height').value != '' && topDoc.getElementById('f_resize_height').value != topDoc.getElementById('f_original_height').value && topDoc.getElementById('f_resize_height').value != height) height = topDoc.getElementById('f_resize_height').value;

        var element = dom.createHTML('img', {
            src : topDoc.getElementById('f_url').value,
            align : topDoc.getElementById('f_align').value,
            id : topDoc.getElementById('f_image_template_id').value + shrinkRatio,
            title : captionsEnabled ? tinyMCE.editors['f_caption'].getContent({format : 'html'}) : topDoc.getElementById('f_caption').value.replace(/<\/?[^>]+(>|$)/g, ""),
            alt : topDoc.getElementById('f_alt').value.replace(/<\/?[^>]+(>|$)/g, ""),
            width : width,
            height : height
        });
        tinyMCEPopup.execCommand(mce_command, false, element);
        return(tinyMCEPopup.close());
    },

    insert : function() {
        return(CampsiteImageDialog.edit_insert('insert'));
    },

    edit : function() {
        return(CampsiteImageDialog.edit_insert('edit'));
    },

    select : function(p_image_template_id, p_filename, p_alt, p_title, p_align, p_ratio, p_width, p_height, p_original_width, p_original_height) {

        var topDoc = window.top.document;

        var obj = topDoc.getElementById('f_image_template_id');
        obj.value = p_image_template_id;

        var obj = topDoc.getElementById('f_url');
        obj.value = p_filename;

        var obj = topDoc.getElementById('f_alt');
        obj.value = p_alt.replace(/<\/?[^>]+(>|$)/g, "");

        var obj = topDoc.getElementById('f_caption');
        if (typeof(captionsEnabled) !== 'undefined' && captionsEnabled) {
            obj.value = p_title;
            tinyMCE.editors['f_caption'].setContent(p_title, {format : 'html'});
        } else {
            obj.value = p_title.replace(/<\/?[^>]+(>|$)/g, "");
        }

        var obj = topDoc.getElementById('f_align');
        obj.value = p_align;

        if (p_ratio != undefined && p_ratio != '') {
            var obj = topDoc.getElementById('f_ratio');
            obj.value = p_ratio;
        }

        if (p_width != undefined && p_width != '') {
            var obj = topDoc.getElementById('f_resize_width');
            obj.value = p_width;

            var obj = topDoc.getElementById('f_original_width');
            obj.value = p_width;
        }
        if (p_height != undefined && p_height != '') {
            var obj = topDoc.getElementById('f_resize_height');
            obj.value = p_height;

            var obj = topDoc.getElementById('f_original_height');
            obj.value = p_height;
        }

        if (p_original_width != undefined && p_original_width != '') {
            var obj = topDoc.getElementById('f_original_width');
            obj.value = p_original_width;
        }
        if (p_original_height != undefined && p_original_height != '') {
            var obj = topDoc.getElementById('f_original_height');
            obj.value = p_original_height;
        }

        var allPageTags = new Array();
        allPageTags = document.getElementsByTagName('*');
        for (i = 0; i < allPageTags.length; i++) {
            if (allPageTags[i].className == 'block') {
                allPageTags[i].style.backgroundColor='';
            }
        }

        document.getElementById('block_'+p_image_template_id).style.backgroundColor='#FFC';
    },

    close : function() {
        tinyMCEPopup.close();
    }
};

// While loading
tinyMCEPopup.onInit.add(CampsiteImageDialog.init);
