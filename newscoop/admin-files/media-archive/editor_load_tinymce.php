<?php
/**
 * @param mixed p_dbColumns
 * @param object p_user The User object
 * @param int p_editorLanguage The current or selected language
 * @param array options Override tinyMCE options
 *
 * @return void
 */
function editor_load_tinymce($p_dbColumns, $p_user, $p_editorLanguage, $options=array())
{
    global $Campsite;

    $stylesheetFile = $Campsite['WEBSITE_URL'] . '/admin/articles/article_stylesheet.css';

    // Defaults, can be overridden via $options parameter
    $toolbarlength      = 33;

    // hangle options
    if (array_key_exists('toolbar_length', $options)) {
        $toolbarlength = $options['toolbar_length'];
        unset($options['toolbar_length']);
    }

    /** STEP 1 ********************************************************
     * What are the names of the textareas you will be turning
     * into editors?
     ******************************************************************/
    $editors = array();
    if (is_array($p_dbColumns)) {
        $editors = $p_dbColumns;
    } else {
        $editors[] = $p_dbColumns;
    }
    $textareas = implode(',', $editors);

    /** STEP 2 ********************************************************
     * Now, what are the plugins you will be using in the editors
     * on this page.  List all the plugins you will need, even if not
     * all the editors will use all the plugins.
     ******************************************************************/
    $plugins = array();
    if ($p_user->hasPermission('EditorCopyCutPaste')) {
        $plugins[] = 'paste';
    }
    if ($p_user->hasPermission('EditorFindReplace')) {
        $plugins[] = 'searchreplace';
    }
    if ($p_user->hasPermission('EditorEnlarge')) {
        $plugins[] = 'fullscreen';
    }
    if ($p_user->hasPermission('EditorLink')) {
        $plugins[] = 'campsiteinternallink';
    }
    if ($p_user->hasPermission('EditorFontColor')) {
        $plugins[] = 'codehighlighting';
    }
    if ($p_user->hasPermission('EditorTextDirection')) {
        $plugins[] = 'directionality';
    }

    $plugins_list = implode(",", $plugins);

    $statusbar_location = "none";
    if ($p_user->hasPermission('EditorStatusBar')) {
        $statusbar_location = "bottom";
    }

    /** STEP 3 ********************************************************
     * We create a default configuration to be used by all the editors.
     * If you wish to configure some of the editors differently this
     * will be done in step 4.
     ******************************************************************/
    $toolbar = array();
    if ($p_user->hasPermission('EditorBold')) {
        $toolbar[] = "bold";
    }
    if ($p_user->hasPermission('EditorItalic')) {
        $toolbar[] = "italic";
    }
    if ($p_user->hasPermission('EditorUnderline')) {
        $toolbar[] = "underline";
    }
    if ($p_user->hasPermission('EditorStrikethrough')) {
        $toolbar[] = "strikethrough";
        $toolbar[] = "blockquote";
    }
    if ($p_user->hasPermission('EditorTextAlignment')) {
        $toolbar[] = "|";
        $toolbar[] = "justifyleft";
        $toolbar[] = "justifycenter";
        $toolbar[] = "justifyright";
        $toolbar[] = "justifyfull";
    }
    if ($p_user->hasPermission('EditorIndent')) {
        $toolbar[] = "|";
        $toolbar[] = "outdent";
        $toolbar[] = "indent";
    }
    if ($p_user->hasPermission('EditorCopyCutPaste')) {
        $toolbar[] = "|";
        $toolbar[] = "copy";
        $toolbar[] = "cut";
        $toolbar[] = "paste";
        $toolbar[] = "pastetext";
        $toolbar[] = "pasteword";
    }
    if ($p_user->hasPermission('EditorUndoRedo')) {
        $toolbar[] = "|";
        $toolbar[] = "undo";
        $toolbar[] = "redo";
    }
    if ($p_user->hasPermission('EditorTextDirection')) {
        $toolbar[] = "|";
        $toolbar[] = "ltr";
        $toolbar[] = "rtl";
    }

    if ($p_user->hasPermission('EditorLink')) {
        $toolbar[] = "|";
        $toolbar[] = "campsiteinternallink";
        $toolbar[] = "link";
    }
    if ($p_user->hasPermission('EditorSourceView')) {
        $toolbar[] = "code";
    }
    if ($p_user->hasPermission('EditorEnlarge')) {
        $toolbar[] = "fullscreen";
    }
    if ($p_user->hasPermission('EditorHorizontalRule')) {
        $toolbar[] = "hr";
    }
    if ($p_user->hasPermission('EditorFontColor')) {
        $toolbar[] = "forecolor";
        $toolbar[] = "backcolor";
        $toolbar[] = 'codehighlighting';
    }
    if ($p_user->hasPermission('EditorSubscript')) {
        $toolbar[] = "sub";
    }
    if ($p_user->hasPermission('EditorSuperscript')) {
        $toolbar[] = "sup";
    }
    if ($p_user->hasPermission('EditorCharacterMap')) {
        $toolbar[] = "charmap";
    }
    if ($p_user->hasPermission('EditorListBullet') && $p_user->hasPermission('EditorListNumber')) {
        $toolbar[] = "|";
        $toolbar[] = "bullist";
        $toolbar[] = "numlist";
    } elseif ($p_user->hasPermission('EditorListBullet') && !$p_user->hasPermission('EditorListNumber')) {
        $toolbar[] = "|";
        $toolbar[] = "bullist";
    } elseif (!$p_user->hasPermission('EditorListBullet') && $p_user->hasPermission('EditorListNumber')) {
        $toolbar[] = "|";
        $toolbar[] = "numlist";
    }

    // Create toolbar rows base on toolbarlength option
    $toolbarRow = 0;
    $toolbarCollector = array();
    while (count($toolbar) > 0) {
        $tmpArray = array_splice($toolbar, 0, $toolbarlength);

        if (in_array('forecolor', $tmpArray) && in_array('backcolor', $tmpArray)) {
            $button = array_pop($tmpArray);
            array_unshift($toolbar, $button);
        }

        if ($tmpArray[0] == '|') {
            $tmpArray = array_splice($tmpArray, 1);
        }
        if ($tmpArray[count($tmpArray)-1] == '|') {
            $tmpArray = array_splice($tmpArray, 0, count($tmpArray)-1);
        }

        $toolbarCollector[$toolbarRow] = $tmpArray;
        $toolbarRow++;
    }

    // Add fontface and fontsize select fields on logical positions
    if ($p_user->hasPermission('EditorFontFace')) {

        $lastIndex = (count($toolbarCollector) - 1);
        $lastRow = $toolbarCollector[$lastIndex];

        if (($toolbarlength - count($toolbarCollector[$lastIndex])) > 5) {
            $toolbarCollector[$lastIndex][] = "|";
            $toolbarCollector[$lastIndex][] = "formatselect";
        } else {
            $toolbarCollector[++$lastIndex][] = "formatselect";
        }
    }
    if ($p_user->hasPermission('EditorFontSize')) {

        $lastIndex = (count($toolbarCollector) - 1);
        $lastRow = $toolbarCollector[$lastIndex];

        if (($toolbarlength - count($toolbarCollector[$lastIndex])) > 5) {
            $toolbarCollector[$lastIndex][] = "|";
            $toolbarCollector[$lastIndex][] = "fontsizeselect";
        } else {
            $toolbarCollector[++$lastIndex][] = "fontsizeselect";
        }
    }

    // Make sure there are always 3 toolbars, tinymce requires 3 to be defined
    if (count($toolbarCollector) < 3) {
        $toolbarCollector = array_pad($toolbarCollector, 3, array());
    }

    $localeFile = $Campsite['CAMPSITE_DIR'] . '/js/tinymce/langs/' . $p_editorLanguage . '.js';
    if (!file_exists($localeFile)) {
        $p_editorLanguage = 'en';
    }
    // Convert resting options to json
    $optionsAsJson = json_encode($options);
?>

<!-- Load TinyMCE -->
<script type="text/javascript" src="/js/tinymce/tiny_mce.js"></script>
<script type="text/javascript">

var validateTinyMCEEditors = function() { return true; };

var tinyMceOptions = {

    // General options
    language : "<?php p($p_editorLanguage); ?>",
    mode : "exact",
    elements : "<?php p($textareas); ?>",
    theme : "advanced",
    plugins : "<?php p($plugins_list); ?>",

    forced_root_block : "p",
    relative_urls : false,

    <?php
        // Print toolbars
        foreach ($toolbarCollector as $row => $buttons) {
            echo sprintf('theme_advanced_buttons%d : "%s",%s', ++$row, implode(',', $buttons), "\n\t");
        }
    ?>

    theme_advanced_toolbar_location : "external",
    theme_advanced_toolbar_align : "left",
    theme_advanced_resizing : false,
    theme_advanced_statusbar_location: "<?php p($statusbar_location); ?>",

    // Restrict usage of certain elements
    invalid_elements: "html,head,body,title,base,link,meta,style,script,"+
        "noscript,template,section,nav,article,aside,header,footer,"+
        "address,main,figure,figcaption,div,data,time,code,car,samp,kbd,"+
        "mark,ruby,rt,rp,ins,del,img,map,area,svg,math,table,caption,"+
        "colgroup,col,tbody,thead,tfoor,tr,td,th,form,fieldset,legend,"+
        "label,input,button,select,datalist,optgroup,option,textarea,"+
        "keygen,output,progress,meter,details,summary,menuitem,menu",

    // Limit characters
    max_chars : 0,
    max_chars_indicator : ".maxCharsSpan",

    // Example content CSS (should be your site CSS)
    content_css : "<?php echo $stylesheetFile; ?>",

    // Drop lists for link/image/media/template dialogs
    template_external_list_url : "lists/template_list.js",
    external_link_list_url : "lists/link_list.js",
    external_image_list_url : "lists/image_list.js",
    media_external_list_url : "lists/media_list.js",

    // paste options
    paste_auto_cleanup_on_paste: true,
    paste_convert_headers_to_strong: true,
    paste_remove_spans: true,
    paste_remove_styles: true,
    paste_block_drop: true,

    // not escaping greek characters
    entity_encoding: 'raw',

    <?php if ($p_user->hasPermission('EditorSpellcheckerEnabled')) { ?>
    gecko_spellcheck : true,
    <?php } ?>

    <?php
    $translator = \Zend_Registry::get('container')->getService('translator');
    ?>
    setup : function(ed) {
        var wordcount = false;

        ed.onKeyUp.add(function(ed, l) {
            var row = tinymce.DOM.get(tinyMCE.activeEditor.id + '_path_row');
            if (!wordcount) {
                tinymce.DOM.add(row.parentNode, 'div', {'style': 'float: right'}, '<?php echo $translator->trans("Characters", array(), 'articles'); ?>: ' + '<span id="' + tinyMCE.activeEditor.id + '-wordcount">0</span>');
                wordcount = true;
            }
            var strip = (tinyMCE.activeEditor.getContent()).replace(/(<([^>]+)>)/ig,"");
            tinymce.DOM.setHTML(tinyMCE.activeEditor.id + '-wordcount', strip.length);
        });
    }
};

$.extend(tinyMceOptions, <?php echo $optionsAsJson; ?>);

// Remove option when value is  '0'. '0' indicates no character limit but
// plugin doesn't support this functionality.
if (tinyMceOptions.max_chars == 0) {
    delete tinyMceOptions.max_chars;
}

// Default skin
tinyMCE.init(tinyMceOptions);

function tinyMCECharsValid(editor, maxChars) {
    if (maxChars == 0) return true;
    var text = editor.getContent().replace(/<[^>]*>/g, '').replace(/\s+/g, ' ');
    text = text.replace(/^\s\s*/, '').replace(/\s\s*$/, '');
    return (text.length <= maxChars);
}

<?php
    $prefService = \Zend_Registry::get('container')->getService('preferences');
    if ($prefService->MediaRichTextCaptions == 'Y') {
?>

validateTinyMCEEditors = function() {

    if (typeof(tinyMceOptions.max_chars) == 'undefined') {
        return true;
    }

    var valid = true;
    var invalidInstances = [];

    for (inst in tinyMCE.editors) {
        // Check if entry is valid tinyMCE instance and skip numeric instances
        if (tinyMCE.editors[inst].getContent && isNaN(inst)) {
            if (!tinyMCECharsValid(tinyMCE.editors[inst], tinyMceOptions.max_chars)) {
                valid = false;
                invalidInstances.push(inst);
            }
        }
    }

    if (!valid) {
        // TODO: Translate this
        alert('<?php echo $translator->trans('An image caption is too long. The character limit is $1.', array(), 'media_archive'); ?>'.replace('$1', tinyMceOptions.max_chars));
        // Focus first instance
        tinymce.execCommand('mceFocus', false, invalidInstances[0]);
    }

    return valid;
}

<?php
    }
?>

</script>
<!-- /TinyMCE -->
<?php
} // fn editor_load_tinymce
?>
