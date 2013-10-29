<?php
/**
 * @param array p_dbColumns
 * @param object p_user The User object
 * @param int p_editorLanguage The current or selected language
 *
 * @return void
 */
function editor_load_tinymce($p_dbColumns, $p_editorLanguage, $options=array())
{
        global $Campsite;

        $stylesheetFile = '/admin-files/articles/article_stylesheet.css';

        // Defaults, can be overridden via $options parameter
        $toolbarlength      = 33;

        // hangle options
        foreach ($options AS $option =>  $value) {
            if ($option == 'toolbar_length') {
                $toolbarlength = $value;
                unset($options[$option]);
            }
        }

        /** STEP 1 ********************************************************
         * What are the names of the textareas you will be turning
         * into editors?
         ******************************************************************/
        $editors = array();
        $editors[] = $p_dbColumns;

        $textareas = implode(",", $editors);

        /** STEP 2 ********************************************************
         * Now, what are the plugins you will be using in the editors
         * on this page.  List all the plugins you will need, even if not
         * all the editors will use all the plugins.
         ******************************************************************/
        $plugins = array();
        $plugins[] = 'paste';
        $plugins[] = 'searchreplace';
        $plugins[] = 'fullscreen';
        $plugins_list = implode(",", $plugins);
        $statusbar_location = "none";

        /** STEP 3 ********************************************************
         * We create a default configuration to be used by all the editors.
         * If you wish to configure some of the editors differently this
         * will be done in step 4.
         ******************************************************************/
        $toolbar1 = array();
        $toolbar1[] = "bold";
        $toolbar1[] = "italic";
        $toolbar1[] = "underline";
        $toolbar1[] = "strikethrough";
        $toolbar1[] = "|";
        $toolbar1[] = "outdent";
        $toolbar1[] = "indent";
        $toolbar1[] = "blockquote";
        $toolbar1[] = "|";
        $toolbar1[] = "copy";
        $toolbar1[] = "cut";
        $toolbar1[] = "paste";
        $toolbar1[] = "pastetext";
        $toolbar1[] = "pasteword";
        $toolbar1[] = "|";
        $toolbar1[] = "undo";
        $toolbar1[] = "redo";
        $toolbar1[] = "|";
        $toolbar1[] = "ltr";
        $toolbar1[] = "rtl";
        $toolbar1[] = "charmap";
        $toolbar1[] = "|";
        $toolbar1[] = "campsiteinternallink";
        $toolbar1[] = "link";
        $toolbar1[] = "anchor";
        $toolbar1[] = "campsite-subhead";
        $toolbar1[] = "campsiteimage";
        $toolbar1[] = "media";
        $toolbar1[] = "code";
        $toolbar1[] = "fullscreen";
        $toolbar1[] = "hr";
        $toolbar1[] = "forecolor";
        $toolbar1[] = "backcolor";
        $toolbar1[] = "sub";
        $toolbar1[] = "sup";
        $toolbar1[] = "|";
        $toolbar1[] = "search";
        $toolbar1[] = "replace";
        $toolbar1[] = "|";
        $toolbar1[] = "bullist";
        $toolbar1[] = "numlist";
        $toolbar1[] = "|";

        $toolbar2 = array();
        $toolbar3 = array();

        // Slice up the toolbars if they are too long.
        if (count($toolbar1) > $toolbarlength) {
            $toolbar2   = array_splice($toolbar1, $toolbarlength);
        }
        if (count($toolbar2) > $toolbarlength) {
            $toolbar3   = array_splice($toolbar2, $toolbarlength);
        }

        // Always push these elements to the last bar, but check if that is bar2 or bar3
        $lastBar = (count($toolbar3) > 0 || count($toolbar2) == $toolbarlength) ? 'toolbar3' : 'toolbar2';
        array_push($$lastBar, "styleselect", "formatselect", "fontselect");

        $theme_buttons1 = (count($toolbar1) > 0) ? implode(',', $toolbar1) : '';
        $theme_buttons2 = (count($toolbar2) > 0) ? implode(',', $toolbar2) : '';
        $theme_buttons3 = (count($toolbar3) > 0) ? implode(',', $toolbar3) : '';

        // Convert resting options to json
        $optionsAsJson = json_encode($options);

?>
<!-- TinyMCE -->
<script type="text/javascript" src="/js/tinymce/tiny_mce.js"></script>
<script type="text/javascript">
    function CampsiteSubhead(ed) {
        element = ed.dom.getParent(ed.selection.getNode(), 'span');
        if (element && ed.dom.getAttrib(element, 'class') == 'campsite_subhead') {
            return false;
        } else {
            html = ed.selection.getContent({format : 'text'});
        ed.selection.setContent('<span class="campsite_subhead">' + html + '</span>');
        }
    } // fn CampsiteSubhead
 
    var tinyMceOptions = {
        // General options
        language : "<?php p($p_editorLanguage); ?>",
        mode : "exact",
        elements : "<?php p($textareas); ?>",
        theme : "advanced",
        plugins : "<?php p($plugins_list); ?>",
        file_browser_callback : "campsitemedia",
        forced_root_block : "",
        relative_urls : false,
     
        // Theme options
        theme_advanced_buttons1 : "<?php p($theme_buttons1); ?>",
        theme_advanced_buttons2 : "<?php p($theme_buttons2); ?>",
        theme_advanced_buttons3 : "<?php p($theme_buttons3); ?>",
     
        theme_advanced_toolbar_location : "external",
        theme_advanced_toolbar_align : "left",
        theme_advanced_resizing : false,
        theme_advanced_statusbar_location: "<?php p($statusbar_location); ?>",

        // Limit characters
        max_chars : 255,
        max_chars_indicator : ".maxCharsSpan",
     
        // Example content CSS (should be your site CSS)
        content_css : "<?php echo $stylesheetFile; ?>",
     
        // Drop lists for link/image/media/template dialogs
        template_external_list_url : "lists/template_list.js",
        external_link_list_url : "lists/link_list.js",
        external_image_list_url : "lists/image_list.js",
        media_external_list_url : "lists/media_list.js",
     
        // paste options
        paste_use_dialog: false,
        paste_auto_cleanup_on_paste: true,
        paste_convert_headers_to_strong: true,
        paste_remove_spans: true,
        paste_remove_styles: true,
     
        setup : function(ed) {
             // Character limit
            wordcount = 0;
            wordCounter = function (ed, e) {
                text = ed.getContent().replace(/<[^>]*>/g, '').replace(/\s+/g, ' ');
                text = text.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
                this.wordcount = ed.getParam('max_chars') - text.length;
            };

            ed.onKeyUp.add(function(ed, e) {
                wordCounter(ed, e);
            });

            ed.onKeyDown.add(function(ed, e) {
                if(wordcount <= 0 && e.keyCode != 8 && e.keyCode != 46) {
                    tinymce.dom.Event.cancel(e);
                }
            });
        }
    };

    $.extend(tinyMceOptions, <?php echo $optionsAsJson; ?>);

    console.log(tinyMceOptions);
 
    // Default skin
    tinyMCE.init(tinyMceOptions);
 
    function tinyMCECharsValid(editor, maxChars) {
        if (maxChars == 0) return true;
        var text = editor.getContent().replace(/<[^>]*>/g, '').replace(/\s+/g, ' ');
        text = text.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
        return (text.length <= maxChars);
    }

    <?php
        if (SystemPref::Get('MediaRichTextCaptions') == 'Y') {
    ?>

    function validateTinyMCEEditors() {

        var valid = true;
        var invalidInstances = [];

        for (inst in tinyMCE.editors) {
            // Check if entry is valid tinyMCE instance and skyip numeric instances
            if (tinyMCE.editors[inst].getContent && isNaN(inst)) {
                if (!tinyMCECharsValid(tinyMCE.editors[inst], tinyMceOptions.max_chars)) {
                    valid = false;
                    invalidInstances.push(inst);
                }
            }
        }

        if (!valid) {
            alert('An image caption is too long. The maximum amount of characters is '+tinyMceOptions.max_chars+'.');
            // Focus first instance
            tinymce.execCommand('mceFocus', false, invalidInstances[0]);
        }

        return valid;
    }

    <?php
        } else {
    ?>
        function validateTinyMCEEditors() {
            return true;
        }
    <?php
        }
    ?>

    function campsitemedia(field_name, url, type, win)
    {
        topDoc = window.top.document;
        articleNo = (topDoc.getElementById('f_article_number')) ? topDoc.getElementById('f_article_number').value : 0;
        langId = (topDoc.getElementById('f_language_selected')) ? topDoc.getElementById('f_language_selected').value : 1;
     
        tinyMCE.activeEditor.windowManager.open({
            url: "/js/tinymce/plugins/campsitemedia/popup.php?article_id="+articleNo+'&language_selected='+langId,
            width: 580,
            height: 320,
            inline : "yes",
            close_previous : "no"
        },{
            window : win,
            input : field_name
        });
    }

</script>
<!-- /TinyMCE -->
<?php
    } // fn editor_load_tinymce
?>