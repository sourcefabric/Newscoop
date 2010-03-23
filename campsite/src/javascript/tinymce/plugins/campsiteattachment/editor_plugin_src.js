/**
 * $Id: editor_plugin_src.js 539 2009-04-06 14:39:58Z holman $
 *
 * @author Campware
 * @copyright Copyright 2009, Campware - MDLF, All rights reserved.
 */

(function() {
    tinymce.PluginManager.requireLangPack('campsiteattachment');
    tinymce.create('tinymce.plugins.CampsiteAttachment', {
        init : function(ed, url) {
            this.editor = ed;
	    editorId = ed.id;
	    articleNo = editorId.substring(editorId.lastIndexOf('_')+1);
	    topDoc = window.top.document;
	    langId = topDoc.getElementById('f_language_selected').value;

	    // Register commands
	    ed.addCommand('mceCampsiteAttachment', function() {
		var se = ed.selection;

		// No selection and not in link
		if (se.isCollapsed() && !ed.dom.getParent(se.getNode(), 'A')) {
		    alert(ed.getLang('campsiteattachment.select_to_link'));
		    return;
		}

		var action = '';
		var elm = se.getNode();
		elm = ed.dom.getParent(elm, "A");
		if (elm != null && elm.nodeName == "A")
		    action = "update";

		if (action == 'update') {
		    var href = ed.dom.getAttrib(elm, 'href');
		}

		ed.windowManager.open({
                    file : url + '/popup.php?article_id=' + articleNo + '&language_selected=' + langId,
		        width : 580 + parseInt(ed.getLang('campsiteattachment.delta_width', 0)),
			height : 330 + parseInt(ed.getLang('campsiteattachment.delta_height', 0)),
			inline : 1
		    }, {
		    plugin_url : url
		});
	    });

	    // Register buttons
	    ed.addButton('campsiteattachment', {
	        title : 'campsiteattachment.campsiteattachment_desc',
		cmd : 'mceCampsiteAttachment',
		image : url + '/img/campsiteattachment.gif'
	    });

	    ed.addShortcut('ctrl+h', 'campsiteattachment.campsiteattachment_desc', 'mceCampsiteAttachment');

	    ed.onNodeChange.add(function(ed, cm, n, co) {
                cm.setDisabled('link', co && n.nodeName != 'A');
		cm.setActive('link', n.nodeName == 'A' && !n.name);
	    });
	},

	getInfo : function() {
            return {
		longname : 'Campsite Attachment',
		author : 'Campware',
		authorurl : 'http://www.campware.org',
		infourl : 'http://code.campware.org/projects/campsite',
		version : '3.2'
	    };
	}
    });

    // Register plugin
    tinymce.PluginManager.add('campsiteattachment', tinymce.plugins.CampsiteAttachment);
})();
