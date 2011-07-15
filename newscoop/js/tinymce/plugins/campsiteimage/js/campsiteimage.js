/* Functions for the campsiteimage plugin popup */

tinyMCEPopup.requireLangPack();

var CampsiteImageDialog = {
    init : function(ed) {
        tinyMCEPopup.resizeToInnerSize();
    },
    
    edit_insert : function(command) {
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
        
        if (shrinkRatio < 1 || shrinkRatio > 99) {
			shrinkRatio = '';
		}
        else {
			shrinkRatio = '_' + shrinkRatio;
		}

        if (command == 'insert') var mce_command = 'mceInsertContent';
        if (command == 'edit') var mce_command = 'mceReplaceContent';
        
        var resize_width = topDoc.getElementById('f_resize_width').value;
        var resize_height = topDoc.getElementById('f_resize_height').value;
        
        if (topDoc.getElementById('f_ratio').value != '') {
			resize_width = resize_width * topDoc.getElementById('f_ratio').value * 0.01;
			resize_height = resize_height * topDoc.getElementById('f_ratio').value * 0.01;
		}
        
        var element = dom.createHTML('img', {
            src : topDoc.getElementById('f_url').value,
            align : topDoc.getElementById('f_align').value,
            id : topDoc.getElementById('f_image_template_id').value + shrinkRatio,
            title : topDoc.getElementById('f_caption').value,
            alt : topDoc.getElementById('f_alt').value,
            width : resize_width,
            height : resize_height
        });
        
        /*
        var element_id = topDoc.getElementById('f_image_template_id').value;
        var element_align = ' align=""';
        if (topDoc.getElementById('f_align').value) element_align = ' align="' + topDoc.getElementById('f_align').value + '"';
        var element_alt = '';
        if (topDoc.getElementById('f_alt').value) element_alt = ' alt="' + topDoc.getElementById('f_alt').value + '"';
        var element_caption = '';
        if (topDoc.getElementById('f_caption').value) element_caption = ' sub="' + topDoc.getElementById('f_caption').value + '"';
        var element_width = '';
        if (topDoc.getElementById('f_resize_width').value) element_width = ' width="' + topDoc.getElementById('f_resize_width').value + '"';
        var element_height = '';
        if (topDoc.getElementById('f_resize_height').value) element_height = ' height="' + topDoc.getElementById('f_resize_height').value + '"';
        var element_ratio = '';
        if (topDoc.getElementById('f_ratio').value) element_ratio = ' ratio="' + topDoc.getElementById('f_ratio').value + '"';
        
        var element = '<!** Image ' + element_id + element_align + element_alt + element_caption + element_width + element_height + element_ratio + '>';
        //console.log(element);
        */
        
        //var element = '<img id="' + element_id + '" >';
        
        tinyMCEPopup.execCommand(mce_command, false, element);

        return(tinyMCEPopup.close());
	},

    insert : function() {
        return(CampsiteImageDialog.edit_insert('insert'));
    },

    edit : function() {
        return(CampsiteImageDialog.edit_insert('edit'));
    },

    select : function(p_image_template_id, p_filename, p_alt, p_title, p_align, p_ratio, p_width, p_height) {
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

        if (p_ratio != undefined && p_ratio != '') {
            var obj = topDoc.getElementById('f_ratio');
            obj.value = p_ratio;
        }
        if (p_width != undefined && p_width != '') {
            var obj = topDoc.getElementById('f_resize_width');
            obj.value = p_width;
        }
        if (p_height != undefined && p_height != '') {
            var obj = topDoc.getElementById('f_resize_height');
            obj.value = p_height;
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
