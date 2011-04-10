<?php
/**
 * @param array p_dbColumns
 * @param object p_user The User object
 * @param int p_editorLanguage The current or selected language
 *
 * @return void
 */
function editor_load_tinymce($p_dbColumns, $p_user, $p_articleNumber,
                             $p_editorLanguage, $p_objectType = 'article')
{
    global $Campsite;

    $stylesheetFile = $Campsite['WEBSITE_URL'] . '/admin/articles/article_stylesheet.css';

	/** STEP 1 ********************************************************
	 * What are the names of the textareas you will be turning
	 * into editors?
	 ******************************************************************/
	$editors = array();
	if (is_array($p_dbColumns)) {
	    foreach ($p_dbColumns as $dbColumn) {
	        if ($dbColumn->getType() == ArticleTypeField::TYPE_BODY) {
                if ($p_articleNumber > 0) {
                    $editors[] = $dbColumn->getName().'_'.$p_articleNumber;
                } else {
                    $editors[] = $dbColumn->getName();
                }
            }
        }
	} else {
	    if ($p_articleNumber > 0) {
	        $editors[] = $p_dbColumns.'_'.$p_articleNumber;
	    } else {
	        $editors[] = $p_dbColumns;
	    }
    }
    $textareas = implode(",", $editors);

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
	if ($p_user->hasPermission('EditorTable')) {
	    $plugins[] = 'table';
	}
	if ($p_user->hasPermission('EditorLink')) {
	    $plugins[] = 'campsiteinternallink';
	    if ($p_objectType == 'article') {
	        $plugins[] = 'campsiteattachment';
	    }
	}
	if ($p_user->hasPermission('EditorImage') && $p_objectType == 'article') {
	        $plugins[] = 'campsiteimage';
            $plugins[] = 'media';
	}
	$plugins[] = 'iframe';
	$plugins[] = 'codehighlighting';
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
	$toolbar1 = array();
	if ($p_user->hasPermission('EditorBold')) {
	    $toolbar1[] = "bold";
	}
	if ($p_user->hasPermission('EditorItalic')) {
	    $toolbar1[] = "italic";
	}
	if ($p_user->hasPermission('EditorUnderline')) {
	    $toolbar1[] = "underline";
	}
	if ($p_user->hasPermission('EditorStrikethrough')) {
	    $toolbar1[] = "strikethrough";
	}
	if ($p_user->hasPermission('EditorTextAlignment')) {
	    $toolbar1[] = "|";
	    $toolbar1[] = "justifyleft";
	    $toolbar1[] = "justifycenter";
	    $toolbar1[] = "justifyright";
	    $toolbar1[] = "justifyfull";
	}
	if ($p_user->hasPermission('EditorIndent')) {
	    $toolbar1[] = "|";
	    $toolbar1[] = "outdent";
	    $toolbar1[] = "indent";
	    $toolbar1[] = "blockquote";
	}
	if ($p_user->hasPermission('EditorCopyCutPaste')) {
	    $toolbar1[] = "|";
	    $toolbar1[] = "copy";
	    $toolbar1[] = "cut";
	    $toolbar1[] = "paste";
	    $toolbar1[] = "pastetext";
	    $toolbar1[] = "pasteword";
	}
	if ($p_user->hasPermission('EditorUndoRedo')) {
	    $toolbar1[] = "|";
	    $toolbar1[] = "undo";
	    $toolbar1[] = "redo";
	}
	if ($p_user->hasPermission('EditorTextDirection')) {
	    $toolbar1[] = "|";
	    $toolbar1[] = "ltr";
	    $toolbar1[] = "rtl";
	    $toolbar1[] = "charmap";
	}
	if ($p_user->hasPermission('EditorLink')) {
	    $toolbar1[] = "|";
	    $toolbar1[] = "campsiteinternallink";
	    $toolbar1[] = "link";
	    $toolbar1[] = "anchor";
	    if ($p_objectType == 'article') {
	        $toolbar1[] = "campsiteattachment";
	    }
	}
	if ($p_user->hasPermission('EditorSubhead')) {
	    $toolbar1[] = "campsite-subhead";
	}
	if ($p_user->hasPermission('EditorImage') && $p_objectType == 'article') {
	        $toolbar1[] = "campsiteimage";
		    $toolbar1[] = "media";
	}
	if ($p_user->hasPermission('EditorSourceView')) {
	    $toolbar1[] = "code";
	}
	if ($p_user->hasPermission('EditorEnlarge')) {
	    $toolbar1[] = "fullscreen";
	}
	if ($p_user->hasPermission('EditorHorizontalRule')) {
	    $toolbar1[] = "hr";
	}
	if ($p_user->hasPermission('EditorFontColor')) {
	    $toolbar1[] = "forecolor";
	    $toolbar1[] = "backcolor";
	}
	if ($p_user->hasPermission('EditorSubscript')) {
	    $toolbar1[] = "sub";
	}
	if ($p_user->hasPermission('EditorSuperscript')) {
	    $toolbar1[] = "sup";
	}
	if ($p_user->hasPermission('EditorFindReplace')) {
	    $toolbar1[] = "|";
	    $toolbar1[] = "search";
	    $toolbar1[] = "replace";
	}

	$toolbar2 = array();
	// Slice up the first toolbar if it is too long.
	if (count($toolbar1) > 34) {
		$toolbar2 = array_splice($toolbar1, 34);
	}

	// This is to put the bulleted and numbered list controls
	// on the most appropriate line of the toolbar.
	if ($p_user->hasPermission('EditorListBullet') && $p_user->hasPermission('EditorListNumber') && count($toolbar1) < 19) {
	    $toolbar1[] = "|";
	    $toolbar1[] = "bullist";
	    $toolbar1[] = "numlist";
	} elseif ($p_user->hasPermission('EditorListBullet') && !$p_user->hasPermission('EditorListNumber') && count($toolbar1) < 34) {
	    $toolbar1[] = "|";
	    $toolbar1[] = "bullist";
	} elseif (!$p_user->hasPermission('EditorListBullet') && $p_user->hasPermission('EditorListNumber') && count($toolbar1) < 20) {
	    $toolbar1[] = "|";
	    $toolbar1[] = "numlist";
	} else {
	    $hasSeparator = false;
	    if ($p_user->hasPermission('EditorListBullet')) {
	        $toolbar2[] = "|";
	        $toolbar2[] = "bullist";
            $hasSeparator = true;
	    }
	    if ($p_user->hasPermission('EditorListNumber')) {
	        if (!$hasSeparator) {
                $toolbar2[] = "|";
            }
	        $toolbar2[] = "numlist";
	    }
	}

	if ($p_user->hasPermission('EditorFontFace')) {
	    $toolbar2[] = "|";
	    $toolbar2[] = "styleselect";
	    $toolbar2[] = "formatselect";
	    $toolbar2[] = "fontselect";
	}
	if ($p_user->hasPermission('EditorFontSize')) {
	    $toolbar2[] = "fontsizeselect";
	}

	if ($p_user->hasPermission('EditorTable')) {
	    $toolbar3[] = "tablecontrols";
	}
	$toolbar3[] = 'iframe';
	$toolbar3[] = 'codehighlighting';

	$theme_buttons1 = (count($toolbar1) > 0) ? implode(',', $toolbar1) : '';
	$theme_buttons2 = (count($toolbar2) > 0) ? implode(',', $toolbar2) : '';
	$theme_buttons3 = (count($toolbar3) > 0) ? implode(',', $toolbar3) : '';

    $localeFile = $Campsite['CAMPSITE_DIR'] . '/js/tinymce/langs/' . $p_editorLanguage . '.js';
	if (!file_exists($localeFile)) {
	    $p_editorLanguage = 'en';
	}
?>

<!-- Load TinyMCE -->
<script type="text/javascript" src="<?php echo $Campsite['WEBSITE_URL']; ?>/js/tinymce/jquery.tinymce.js"></script>
<script type="text/javascript">
<?php if ($p_objectType == 'article') { ?>
function CampsiteSubhead(ed) {
    element = ed.dom.getParent(ed.selection.getNode(), 'span');
    if (element && ed.dom.getAttrib(element, 'class') == 'campsite_subhead') {
        return false;
    } else {
        html = ed.selection.getContent({format : 'text'});
        ed.selection.setContent('<span class="campsite_subhead">' + html + '</span>');
    }
} // fn CampsiteSubhead
<?php } ?>

$().ready(function() {
    $('textarea.tinymce').tinymce({

		
    	// Location of TinyMCE script
        script_url : '<?php echo $Campsite['WEBSITE_URL']; ?>/js/tinymce/tiny_mce.js',

     	// General options
        language : "<?php p($p_editorLanguage); ?>",
        theme : "advanced",
        plugins : "<?php p($plugins_list); ?>",
        
        file_browser_callback : "campsitemedia",
        forced_root_block : "",
        relative_urls : false,
        onchange_callback : function() { $('form#article-main').change(); },
        extended_valid_elements : "iframe[src|width|height|name|align|frameborder|scrolling|marginheight|marginwidth]",

        
        // Theme options
        theme_advanced_buttons1 : "<?php p($theme_buttons1); ?>",
        theme_advanced_buttons2 : "<?php p($theme_buttons2); ?>",
        theme_advanced_buttons3 : "<?php p($theme_buttons3); ?>",

        theme_advanced_toolbar_location : "top",
        theme_advanced_toolbar_align : "left",
        theme_advanced_resizing : false,
        theme_advanced_statusbar_location: "<?php p($statusbar_location); ?>",

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

        // not escaping greek characters
        entity_encoding: 'raw',

        <?php if ($p_user->hasPermission('EditorSpellcheckerEnabled')): ?>
        gecko_spellcheck : true,
        <?php endif; ?>

        <?php if ($p_user->hasPermission('EditorSubhead') && $p_objectType == 'article') { ?>
        setup : function(ed) {
            ed.onInit.add(function(){ed.controlManager.setDisabled('pasteword', true);});
            ed.onNodeChange.add(function(){ed.controlManager.setDisabled('pasteword', true);});

            ed.onKeyUp.add(function(ed, l) {
                var idx = ed.id.lastIndexOf('_');
                var buttonId = ed.id.substr(0, idx);
            });

            ed.addButton('campsite-subhead', {
                title : '<?php putGS("Newscoop Subhead"); ?>',
                image : website_url + '/js/tinymce/themes/advanced/img/campsite_subhead.gif',
                onclick : function() {
                    CampsiteSubhead(ed);
                }
            });
        },
        <?php } ?>

    });
});

<?php if ($p_objectType == 'article') { ?>
function campsitemedia(field_name, url, type, win)
{
    topDoc = window.top.document;
    articleNo = topDoc.getElementById('f_article_number').value;
    langId = topDoc.getElementById('f_language_selected').value;

    tinyMCE.activeEditor.windowManager.open({
        url: website_url + "/js/tinymce/plugins/campsitemedia/popup.php?article_id="+articleNo+'&language_selected='+langId,
        width: 580,
        height: 320,
        inline : "yes",
        close_previous : "no"
    },{
        window : win,
        input : field_name
    });
}
<?php } ?>
</script>
<!-- /TinyMCE -->
<?php
} // fn editor_load_tinymce
?>
