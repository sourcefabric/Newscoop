/**
 * $Id: editor_plugin_src.js 539 2008-01-14 19:08:58Z holman $
 *
 * @author Campware
 * @copyright Copyright 2008-2009, Campware - MDLF, All rights reserved.
 */

(function() {
    tinymce.create('tinymce.plugins.CampsiteImage', {
        init : function(ed, url) {
            this.editor = ed;
	    editorId = ed.id;
	    articleNo = editorId.substring(editorId.lastIndexOf('_')+1);

	    // Register commands
	    ed.addCommand('mceCampsiteImage', function() {
		ed.windowManager.open({
                    file : url + '/popup.php?article_id=' + articleNo,
		        width : 580 + parseInt(ed.getLang('campsiteimage.delta_width', 0)),
			height : 330 + parseInt(ed.getLang('campsiteimage.delta_height', 0)),
			inline : 1
		    }, {
		    plugin_url : url
		});
	    });

	    // Register buttons
	    ed.addButton('campsiteimage', {
	        title : 'campsiteimage.image_desc',
		cmd : 'mceCampsiteImage',
		image : url + '/img/campsiteimage.gif'
	    });

	    ed.addShortcut('ctrl+g', 'campsiteimage.campsiteimage_desc', 'mceCampsiteImage');

	    ed.onNodeChange.add(function(ed, cm, n, co) {
                cm.setDisabled('link', co && n.nodeName != 'A');
		cm.setActive('link', n.nodeName == 'A' && !n.name);
	    });
	},

	getInfo : function() {
            return {
		longname : 'Campsite Image',
		author : 'Campware',
		authorurl : 'http://www.campware.org',
		infourl : 'http://code.campware.org/projects/campsite',
		version : '3.2'
	    };
	}
    });

    // Register plugin
    tinymce.PluginManager.add('campsiteimage', tinymce.plugins.CampsiteImage);
})();
