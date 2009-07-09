/**
 * $Id: editor_plugin_src.js 520 2008-01-07 16:30:32Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	tinymce.create('tinymce.plugins.CampsitebreakPlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mceCampsitebreak', function() {
				ed.windowManager.open({
					file : url + '/campsitebreak.htm',
					width : 250 + parseInt(ed.getLang('campsitebreak.delta_width', 0)),
					height : 160 + parseInt(ed.getLang('campsitebreak.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('campsitebreak', {title : 'campsitebreak.campsitebreak_desc', cmd : 'mceCampsitebreak', image : url + '/img/drupalbreak.gif'});
		},

		getInfo : function() {
			return {
				longname : 'Campsitebreak',
				author : 'campware.org',
				authorurl : 'http://campware.org',
				infourl : 'http://campware.org',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('campsitebreak', tinymce.plugins.CampsitebreakPlugin);
})();