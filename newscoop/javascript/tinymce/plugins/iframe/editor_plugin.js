(function() {
	tinymce.PluginManager.requireLangPack('iframe');
	tinymce.create('tinymce.plugins.iframe', {
		init : function(ed, url) {

			ed.addCommand('mce_iframe', function() {
				ed.windowManager.open({
					file : url + '/window.html',
					width : 360 + ed.getLang('iframe.delta_width', 0),
					height : 210 + ed.getLang('iframe.delta_height', 0),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			ed.addButton('iframe', {
				title : 'iframe.desc',
				cmd : 'mce_iframe',
				image : url + '/iframe.gif'
			});
		},
		
		getInfo : function() {
			return {
					longname  : 'iframe',
					author 	  : 'Duwial Hakvoort',
					authorurl : 'http://www.exed.nl',
					infourl   : 'http://www.exed.nl',
					version   : "1.0"
			};
		}
	});

	tinymce.PluginManager.add('iframe', tinymce.plugins.iframe);
})();


