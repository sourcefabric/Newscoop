/**
 * $Id: editor_plugin_src.js 539 2008-01-14 19:08:58Z holman $
 *
 * @author Campware
 * @copyright Copyright 2008-2009, Campware - MDLF, All rights reserved.
 */

(function() {
    tinymce.PluginManager.requireLangPack('campsiteimage');

    tinymce.create('tinymce.plugins.campsiteimage', {
        init : function(ed, url) {
            this.editor = ed;
            editorId = ed.id;
            articleNo = editorId.substring(editorId.lastIndexOf('_')+1);

            // Register commands
            ed.addCommand('mcecampsiteimage', function() {
                var se = ed.selection;
                var url_params = '';

                if (!se.isCollapsed() || ed.dom.getParent(se.getNode(), 'IMG')) {
                    var action = '';
                    var elm = se.getNode();
                    elm = ed.dom.getParent(elm, "IMG");
                    if (elm != null && elm.nodeName == "IMG")
                        action = "update";

                    if (action == 'update') {
                        var elmId = ed.dom.getAttrib(elm, 'id');
                        url_params = '&image_id=' + elmId;
                        if (ed.dom.getAttrib(elm, 'alt') !== null)
                            url_params += '&image_alt=' + encodeURIComponent(ed.dom.getAttrib(elm, 'alt'));
                        if (ed.dom.getAttrib(elm, 'title') !== null)
                            url_params += '&image_title=' + encodeURIComponent(ed.dom.getAttrib(elm, 'title'));
                        if (ed.dom.getAttrib(elm, 'align') !== null)
                            url_params += '&image_alignment=' + escape(ed.dom.getAttrib(elm, 'align'));
                        if (ed.dom.getAttrib(elm, 'width') !== null && ed.dom.getAttrib(elm, 'width') != '')
                            url_params += '&image_resize_width=' + escape(ed.dom.getAttrib(elm, 'width'));
                        if (ed.dom.getAttrib(elm, 'height') !== null && ed.dom.getAttrib(elm, 'height') != '')
                            url_params += '&image_resize_height=' + escape(ed.dom.getAttrib(elm, 'height'));
                        if (ed.dom.getAttrib(elm, 'ratio') !== null && ed.dom.getAttrib(elm, 'ratio') != '')
                            url_params += '&image_ratio=' + escape(ed.dom.getAttrib(elm, 'ratio'));
                        else
                            if (elmId.lastIndexOf('_') > 0)
                                url_params += '&image_ratio=' + elmId.substring(elmId.lastIndexOf('_')+1);
                    }
                }

                ed.windowManager.open({
                    file : url + '/popup.php?article_id=' + articleNo + url_params,
                    width : 580 + parseInt(ed.getLang('campsiteimage.delta_width', 0)),
                    height : 430 + parseInt(ed.getLang('campsiteimage.delta_height', 0)),
                    inline : 1
                }, {
                    plugin_url : url
                });
            });

            // Register buttons
            ed.addButton('campsiteimage', {
                title : 'campsiteimage.campsiteimage_desc',
                cmd : 'mcecampsiteimage',
                image : url + '/img/campsiteimage.gif'
            });

            ed.addShortcut('ctrl+g', 'campsiteimage.campsiteimage_desc', 'mcecampsiteimage');

            ed.onNodeChange.add(function(ed, cm, n, co) {
                cm.setDisabled('link', co && n.nodeName != 'A');
                cm.setActive('link', n.nodeName == 'A' && !n.name);
            });
        },

        getInfo : function() {
            return {
                longname : 'campsiteimage',
                author : 'Sourcefabric',
                authorurl : 'http://www.sourcefabric.org',
                infourl : 'http://dev.sourcefabric.org/browse/CS',
                version : '3.4'
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('campsiteimage', tinymce.plugins.campsiteimage);
})();
