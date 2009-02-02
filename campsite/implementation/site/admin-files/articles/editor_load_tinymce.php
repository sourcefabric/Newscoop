<?php

/**
 * @param array p_dbColumns
 * @param object p_user The User object
 * @param int p_editorLanguage The current or selected language
 *
 * @return void
 */
function editor_load_tinymce($p_dbColumns, $p_user,
			     $p_articleNumber, $p_editorLanguage)
{
	global $Campsite;

	$stylesheetFile = '/admin/articles/article_stylesheet.css';

	/** STEP 1 ********************************************************
	 * What are the names of the textareas you will be turning
	 * into editors?
	 ******************************************************************/
	$editors = array();
	if (is_array($p_dbColumns)) {
	    foreach ($p_dbColumns as $dbColumn) {
	        if (stristr($dbColumn->getType(), "blob")) {
		    $editors[] = $dbColumn->getName().'_'.$p_articleNumber;
		}
	    }
	} else {
	    $editors[] = $p_dbColumns.'_'.$p_articleNumber;
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
	}
	$plugins[] = 'campsiteimage';
	$plugins_list = implode(",", $plugins);

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
	}
	if ($p_user->hasPermission('EditorSubhead')) {
	    $toolbar1[] = "campsite-subhead";
	}
	if ($p_user->hasPermission('EditorImage')) {
	    $toolbar1[] = "campsiteimage";
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
	if (count($toolbar1) > 31) {
		$toolbar2 = array_splice($toolbar1, 31);
	}

	// This is to put the bulleted and numbered list controls
	// on the most appropriate line of the toolbar.
	if ($p_user->hasPermission('EditorListBullet') && $p_user->hasPermission('EditorListNumber') && count($toolbar1) < 19) {
	    $toolbar1[] = "|";
	    $toolbar1[] = "bullist";
	    $toolbar1[] = "numlist";
	} elseif ($p_user->hasPermission('EditorListBullet') && !$p_user->hasPermission('EditorListNumber') && count($toolbar1) < 31) {
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

	$theme_buttons1 = (count($toolbar1) > 0) ? implode(',', $toolbar1) : '';
	$theme_buttons2 = (count($toolbar2) > 0) ? implode(',', $toolbar2) : '';
	$theme_buttons3 = (count($toolbar3) > 0) ? implode(',', $toolbar3) : '';
?>
<!-- TinyMCE -->
<script type="text/javascript" src="/javascript/tinymce/tiny_mce.js"></script>
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


// Default skin
tinyMCE.init({
    // General options
    language : "<?php p($p_editorLanguage); ?>",
    mode : "exact",
    elements : "<?php p($textareas); ?>",
    theme : "advanced",
    plugins : "<?php p($plugins_list); ?>",
    forced_root_block : "",

    // Theme options
    theme_advanced_buttons1 : "<?php p($theme_buttons1); ?>",
    theme_advanced_buttons2 : "<?php p($theme_buttons2); ?>",
    theme_advanced_buttons3 : "<?php p($theme_buttons3); ?>",

    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_resizing : false,

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
        ed.onChange.add(function(ed, l) {
	    var idx = ed.id.lastIndexOf('_');
	    var buttonId = ed.id.substr(0, idx);
	    buttonEnable('save_' + buttonId);
	});

    <?php if ($p_user->hasPermission('EditorSubhead')) { ?>
        ed.addButton('campsite-subhead', {
        title : 'Subhead',
        image : '/javascript/tinymce/themes/advanced/img/campsite_subhead.gif',
        onclick : function() {
                      CampsiteSubhead(ed);
                  }
        });
    <?php } ?>
    }
});
</script>
<!-- /TinyMCE -->
<?php
} // fn editor_load_tinymce
?>
