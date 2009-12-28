/* Functions for the campsiteimage plugin popup */

tinyMCEPopup.requireLangPack();

var CampsiteImageDialog = {
    init : function(ed) {
        tinyMCEPopup.resizeToInnerSize();
    },

    insert : function() {
        var ed = tinyMCEPopup.editor, dom = ed.dom;
        var topDoc = window.top.document;
        var re = /\"/;
        var alt = topDoc.getElementById('f_alt').value;
        var caption = topDoc.getElementById('f_caption').value;
        
        if ((alt.match(re)) || (caption.match(re))) {
            alert('Double quotes are not allowed for Alt and Caption fields.\nUse single quotes or double single quotes instead.');
            return false;
        }
        
        var shrinkRatio = topDoc.getElementById('f_ratio').value;
        if (shrinkRatio < 1 || shrinkRatio > 99)
            shrinkRatio = '';
        else
            shrinkRatio = '_' + shrinkRatio;

        tinyMCEPopup.execCommand('mceInsertContent', false, dom.createHTML('img', {
            src : topDoc.getElementById('f_url').value,
            align : topDoc.getElementById('f_align').value,
            id : topDoc.getElementById('f_image_template_id').value + shrinkRatio,
            title : topDoc.getElementById('f_caption').value,
            alt : topDoc.getElementById('f_alt').value
        }));

        tinyMCEPopup.close();
    },

    edit : function() {
        var ed = tinyMCEPopup.editor, dom = ed.dom;
        var topDoc = window.top.document;
        var re = /\"/;
        var alt = topDoc.getElementById('f_alt').value;
        var caption = topDoc.getElementById('f_caption').value;
        if ((alt.match(re)) || (caption.match(re))) {
            alert('Double quotes are not allowed for Alt and Caption fields.\nUse single quotes or double single quotes instead.');
            return false;
        }
        
        var shrinkRatio = topDoc.getElementById('f_ratio').value;
        if (shrinkRatio < 1 || shrinkRatio > 99)
            shrinkRatio = '';
        else
            shrinkRatio = '_' + shrinkRatio;

        tinyMCEPopup.execCommand('mceReplaceContent', false, dom.createHTML('img', {
            src : topDoc.getElementById('f_url').value,
            align : topDoc.getElementById('f_align').value,
            id : topDoc.getElementById('f_image_template_id').value + shrinkRatio,
            title : topDoc.getElementById('f_caption').value,
            alt : topDoc.getElementById('f_alt').value
        }));

        tinyMCEPopup.close();
    },

    select : function(p_image_template_id, p_filename, p_alt, p_title, p_align, p_ratio) {
        var topDoc = window.top.document;

        var obj = topDoc.getElementById('f_image_template_id');
        obj.value = p_image_template_id;

        var obj = topDoc.getElementById('f_url');
        obj.value = p_filename;

        var obj = topDoc.getElementById('f_alt');
        obj.value = p_alt;

        var obj = topDoc.getElementById('f_caption');
        obj.value = p_title;

        var obj = topDoc.getElementById('f_align');
        obj.value = p_align;

        if (p_ratio != undefined) {
            var obj = topDoc.getElementById('f_ratio');
            obj.value = p_ratio;
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
