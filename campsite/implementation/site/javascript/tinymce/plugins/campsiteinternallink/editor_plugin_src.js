/**
 * $Id: editor_plugin_src.js 539 2008-01-14 19:08:58Z holman $
 *
 * @author Campware
 * @copyright Copyright 2008-2009, Campware - MDLF, All rights reserved.
 */

(function() {
    tinymce.create('tinymce.plugins.CampsiteInternalLinkPlugin', {
        init : function(ed, url) {
            this.editor = ed;

	    // Register commands
	    ed.addCommand('mceCampsiteInternalLink', function() {
                var se = ed.selection;

		// No selection and not in link
		if (se.isCollapsed() && !ed.dom.getParent(se.getNode(), 'A'))
		    return;

		ed.windowManager.open({
                    file : url + '/link.php',
			    width : 480 + parseInt(ed.getLang('campsiteinternallink.delta_width', 0)),
			    height : 300 + parseInt(ed.getLang('campsiteinternallink.delta_height', 0)),
			    inline : 1
		    }, {
		    plugin_url : url
		});
	    });

	    // Register buttons
	    ed.addButton('campsiteinternallink', {
	        title : 'campsiteinternallink.link_desc',
		cmd : 'mceCampsiteInternalLink',
		image : url + '/img/example.gif'
	    });

	    ed.addShortcut('ctrl+k', 'campsiteinternallink.campsiteinternallink_desc', 'mceCampsiteInternalLink');

	    ed.onNodeChange.add(function(ed, cm, n, co) {
                cm.setDisabled('link', co && n.nodeName != 'A');
		cm.setActive('link', n.nodeName == 'A' && !n.name);
	    });
	},

	getInfo : function() {
            return {
		longname : 'Campsite Internal Link',
		author : 'Campware',
		authorurl : 'http://www.campware.org',
		infourl : 'http://code.campware.org/projects/campsite',
		version : '3.2'
	    };
	}
    });

    // Register plugin
    tinymce.PluginManager.add('campsiteinternallink', tinymce.plugins.CampsiteInternalLinkPlugin);
})();
