/**
 * $Id: editor_plugin_src.js 539 2008-01-14 19:08:58Z holman $
 *
 * @author Campware
 * @copyright Copyright 2008-2009, Campware - MDLF, All rights reserved.
 */

(function() {
    tinymce.PluginManager.requireLangPack('campsiteinternallink');

    tinymce.create('tinymce.plugins.campsiteinternallink', {
        init : function(ed, url) {
            this.editor = ed;

    	    // Register commands
    	    ed.addCommand('mcecampsiteinternallink', function() {
    	        var se = ed.selection;
    	        var url_params = '';

                // No selection and not in link
                if (se.isCollapsed() && !ed.dom.getParent(se.getNode(), 'A')) {
                    alert(ed.getLang('You need to select some text before creating a link'));
                    return;
                }

                var action = '';
                var elm = se.getNode();
                elm = ed.dom.getParent(elm, "A");
                if (elm != null && elm.nodeName == "A") {
                    action = "update";
                }

                if (action == 'update') {
                    var href = ed.dom.getAttrib(elm, 'href');

                    publication = getURLVar('IdPublication', href);
                    language = getURLVar('IdLanguage', href);
                    issue = getURLVar('NrIssue', href);
                    section = getURLVar('NrSection', href);
                    article = getURLVar('NrArticle', href);

                    url_params = '?IdLanguage=' + language
                        + '&IdPublication=' + publication
                        + '&NrIssue=' + issue
                        + '&NrSection=' + section
                        + '&NrArticle=' + article;
                }

                ed.windowManager.open({
                    file : url + '/link.php' + url_params,
                    width : 480 + parseInt(ed.getLang('campsiteinternallink.delta_width', 0)),
                    height : 360 + parseInt(ed.getLang('campsiteinternallink.delta_height', 0)),
                    inline : 1
                }, {
                    plugin_url : url
                });
            });

            // Register buttons
            ed.addButton('campsiteinternallink', {
                title : 'campsiteinternallink.campsiteinternallink_desc',
                cmd : 'mcecampsiteinternallink',
                image : url + '/img/campsiteinternallink.gif'
            });

            ed.addShortcut('ctrl+k', 'campsiteinternallink.campsiteinternallink_desc', 'mcecampsiteinternallink');

            ed.onNodeChange.add(function(ed, cm, n, co) {
                cm.setDisabled('link', co && n.nodeName != 'A');
                cm.setActive('link', n.nodeName == 'A' && !n.name);
            });
        },

        getInfo : function() {
            return {
                longname : 'campsiteinternallink',
                author : 'Sourcefabric',
                authorurl : 'http://www.sourcefabric.org',
                infourl : 'http://dev.sourcefabric.org/browse/CS',
                version : '3.2'
            };
        }
    });

    // Register plugin
    tinymce.PluginManager.add('campsiteinternallink', tinymce.plugins.campsiteinternallink);
})();


function getURLVar(urlVarName, href) {
    //divide the URL in half at the '?'
    var urlHalves = String(href).split('?');
    var urlVarValue = '';

    if (urlHalves[1]) {
        //load all the name/value pairs into an array
        var urlVars = urlHalves[1].split('&');
        //loop over the list, and find the specified url variable
        for(i=0; i<=(urlVars.length); i++){
            if(urlVars[i]){
                //load the name/value pair into an array
                var urlVarPair = urlVars[i].split('=');
                if (urlVarPair[0] && urlVarPair[0] == urlVarName) {
                    //I found a variable that matches, load it's value into the return variable
                    urlVarValue = urlVarPair[1];
                }
            }
        }
    }
    return urlVarValue;
}