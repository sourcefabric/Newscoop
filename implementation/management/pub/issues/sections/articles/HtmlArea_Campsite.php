<?php

/**
 * @param array p_dbColumns
 * @param User p_user
 * @return void
 */
function HtmlArea_Campsite($p_dbColumns, $p_user) {
	global $Campsite;
	global $ADMIN;
	?>	
<script type="text/javascript">
	//<![CDATA[
      _editor_url = "/javascript/htmlarea/";
      _editor_lang = "<?php p($_REQUEST['TOL_Language']); ?>";
      _campsite_article_id = <?php echo $_REQUEST['Article']; ?>;
	//]]>
</script>    

<!-- Load the HTMLArea file -->
<script type="text/javascript" src="/javascript/htmlarea/htmlarea.js"></script>

<!-- Special Campsite functionality -->
<script type="text/javascript">
function CampsiteSubhead(editor, objectName, object) {
	parent = editor.getParentElement();
	if ((parent.tagName.toLowerCase() == "span") && 
		(parent.className.toLowerCase()=="campsite_subhead")) {
		editor.selectNodeContents(parent);
		//editor._doc.execCommand("unlink", false, null);
		editor.updateToolbar();
		return false;
	}
	else {
		editor.surroundHTML('<span class="campsite_subhead">', '</span>');
	}
} // fn CampsiteSubhead

/** 
 * Handler for creating an internal campsite link.
 * This is a copy of the _createlink function, except that it calls 
 * a different popup window.
 */
function CampsiteInternalLink(editor, objectName, object, link) {
	var outparam = null;
	if (typeof link == "undefined") {
		link = editor.getParentElement();
		if (link && !/^a$/i.test(link.tagName))
			link = null;
	}
	popupWindowTarget = "campsite_internal_link.php";
	if (link) {
		outparam = {
		f_href   : HTMLArea.is_ie ? editor.stripBaseURL(link.href) : link.getAttribute("href"),
		f_title  : link.title,
		f_target : link.target
		};
		// Pass the arguments to the popup window so that it
		// can populate the dropdown controls.
		popupWindowTarget += "?" + outparam["f_href"].replace("campsite_internal_link?", "");
	}
	editor._popupDialog(popupWindowTarget, 
		function(param) {
			// This function is called when the OK button
			// is clicked in the popup window.
			if (!param)
				return false;
			var a = link;
			if (!a) try {
				// Create a link normally in the editor
				editor._doc.execCommand("createlink", false, param.f_href);
				a = editor.getParentElement();
				var sel = editor._getSelection();
				var range = editor._createRange(sel);
				if (!HTMLArea.is_ie) {
					a = range.startContainer;
					if (!/^a$/i.test(a.tagName)) {
						a = a.nextSibling;
						if (a == null)
							a = range.startContainer.parentNode;
					}
				}
			} catch(e) {}
			else {
				// Unlink the text if it is linked already
				var href = param.f_href.trim();
				editor.selectNodeContents(a);
				if (href == "") {
					editor._doc.execCommand("unlink", false, null);
					editor.updateToolbar();
					return false;
				}
				else {
					a.href = href;
				}
			}
			if (!(a && /^a$/i.test(a.tagName)))
				return false;
			a.target = param.f_target.trim();
			a.title = param.f_title.trim();
			editor.selectNodeContents(a);
			editor.updateToolbar();
		}, 
		outparam);
};

//<![CDATA[
HTMLArea.loadPlugin("ImageManager");
<?php if ($p_user->hasPermission('EditorTable')) { ?>
HTMLArea.loadPlugin("TableOperations");
<?php } ?>
<?php if ($p_user->hasPermission('EditorListNumber')) { ?>
HTMLArea.loadPlugin("ListType");
<?php } ?>

initdocument = function () {
	var editorArray = new Array();
	<?php
	$stylesheetFile = $Campsite['HTML_COMMON_DIR'] 
		.'/priv/pub/issues/sections/articles/article_stylesheet.css';
	$htmlAreaFields = array();
	foreach ($p_dbColumns as $dbColumn) {	
		if (stristr($dbColumn->getType(), "blob")) {
			$htmlAreaFields[] = $dbColumn->getName();
			?>
			var editor = new HTMLArea("<?php print $dbColumn->getName(); ?>");
			editorArray['<?php print $dbColumn->getName(); ?>'] = editor;
 			var config = editor.config;
 			// Import our custom CSS - watch out for newlines though!
 			// They will break the editor.
			config.pageStyle = "<?php echo str_replace("\n", "", file_get_contents($stylesheetFile)); ?>";
			subheadTooltip = "Subhead";
			if (typeof HTMLArea.I18N.tooltips['campsite_subhead'] != "undefined") {
				subheadTooltip = HTMLArea.I18N.tooltips['campsite_subhead'];
			}
	 		config.registerButton({
	 			// The ID of the button.
				id        : "campsite-subhead", 
				// The tooltip.
				tooltip   : subheadTooltip,
				// Image to be displayed in the toolbar.
				image     : "/javascript/xinha/images/campsite_subhead.gif",
				// TRUE = enabled in text mode
				// FALSE = disabled in text mode
				textMode  : false,
				// Called when the button is clicked.
				action    : CampsiteSubhead,
				// The button will be disabled if outside 
				// the specified element.
				context   : ''
				});
				
			internalLinkTooltip = "Internal Link";
			if (typeof HTMLArea.I18N.tooltips['campsite_internal_link'] != "undefined") {
				internalLinkTooltip = HTMLArea.I18N.tooltips['campsite_internal_link'];
			}
	 		config.registerButton({
	 			// The ID of the button.
				id        : "campsite-internal-link", 
				// The tooltip.
				tooltip   : internalLinkTooltip,
				// Image to be displayed in the toolbar.
				image     : "/javascript/xinha/images/campsite_internal_link.gif",
				// TRUE = enabled in text mode
				// FALSE = disabled in text mode
				textMode  : false,
				// Called when the button is clicked.
				action    : CampsiteInternalLink,
				// The button will be disabled if outside 
				// the specified element.
				context   : ''
				});

			config.toolbar = [
				[ 
				<?php if ($p_user->hasPermission('EditorBold')) { ?>
				  "bold", 
				<?php } ?>
				<?php if ($p_user->hasPermission('EditorItalic')) { ?>
				  "italic", 
				<?php } ?>
				<?php if ($p_user->hasPermission('EditorUnderline')) { ?>
				  "underline", 
				<?php } ?>
				<?php if ($p_user->hasPermission('EditorStrikethrough')) { ?>
				  "strikethrough", 
				<?php } ?>
				  "separator",
				<?php if ($p_user->hasPermission('EditorTextAlignment')) { ?>
				"justifyleft", "justifycenter", "justifyright", "justifyfull", "separator",
				<?php } ?>
				<?php if ($p_user->hasPermission('EditorIndent')) { ?>
				  "outdent", "indent", "separator",
				<?php } ?>
				<?php if ($p_user->hasPermission('EditorCopyCutPaste')) { ?>
				  "copy", "cut", "paste", "space", "separator", 
				<?php } ?>
				<?php if ($p_user->hasPermission('EditorUndoRedo')) { ?>				  
				  "undo", "redo", "separator", 
				<?php } ?>				  
				<?php if ($p_user->hasPermission('EditorTextDirection')) { ?>
				  "lefttoright", "righttoleft", "separator", 
				<?php } ?>
				<?php if ($p_user->hasPermission('EditorLink')) { ?>
				  "campsite-internal-link", "createlink", "separator",
				<?php } ?>
				<?php if ($p_user->hasPermission('EditorSubhead')) { ?>
				  "campsite-subhead", 
				<?php } ?>
				<?php if ($p_user->hasPermission('EditorImage')) { ?>
				  "insertimage", "separator",
				<?php } ?>
				<?php if ($p_user->hasPermission('EditorSourceView')) { ?>
				  "htmlmode", 
				<?php } ?>
				<?php if ($p_user->hasPermission('EditorEnlarge')) { ?>
				  "popupeditor",
				<?php } ?>
				],
		
				[ 
				<?php if ($p_user->hasPermission('EditorFontFace')) { ?>
				  "fontname", "space",
				<?php } ?>
				<?php if ($p_user->hasPermission('EditorFontSize')) { ?>
				  "fontsize", "space",
				<?php } ?>
				<?php if (false) { ?>
				  "formatblock", "space",
				<?php } ?>
				<?php if ($p_user->hasPermission('EditorListBullet')) { ?>
				  "unorderedlist", 
				<?php } ?>
				<?php if ($p_user->hasPermission('EditorListNumber')) { ?>
				  "orderedlist", "separator", 
				<?php } ?>
				  ],
				  [
				<?php if ($p_user->hasPermission('EditorHorizontalRule')) { ?>
				  "inserthorizontalrule", "separator",
				<?php } ?>
				<?php if ($p_user->hasPermission('EditorFontColor')) { ?>
				  "forecolor", "hilitecolor", "separator",
				<?php } ?>
				<?php if ($p_user->hasPermission('EditorSubscript')) { ?>
				  "subscript",
				<?php } ?>
				<?php if ($p_user->hasPermission('EditorSuperscript')) { ?>
				 "superscript",
				<?php } ?>
				  ]
			];
			<?php if ($p_user->hasPermission('EditorListNumber')) { ?>
			editor.registerPlugin(ListType);
			<?php } ?>
			<?php if ($p_user->hasPermission('EditorTable')) { ?>
		  	editor.registerPlugin(TableOperations);
		  	<?php } ?>
			editor.generate();
			<?php
		}
	}
	
	// Warning: you are about to witness a huge hack!
	// This quickly flips the htmlareas between text mode
	// and wysiwyg mode so that when there are more than
	// one on a page, they are all editable.
	if (count($htmlAreaFields) > 0) {
		array_pop($htmlAreaFields);
		$count = 1;
		foreach ($htmlAreaFields as $field) {
			?>
			setTimeout(function() {
					editorArray["<?php p($field) ?>"].setMode();
					editorArray["<?php p($field) ?>"].setMode();
				}, (1000-(<?php p($count++); ?>*100)));
				
			<?php
		}
	}
	?>
} // fn initDocument

function addEvent(obj, evType, fn) { 
	if (obj.addEventListener) { 
		obj.addEventListener(evType, fn, true); 
		return true; 
	} 
    else if (obj.attachEvent) {  
    	var r = obj.attachEvent("on"+evType, fn);  
    	return r;  
    } 
    else {  
    	return false; 
    } 
}  // fn addEvent

addEvent(window, 'load', initdocument);
//]]>
</script>
<?php
} // fn HtmlArea_Campsite
?>