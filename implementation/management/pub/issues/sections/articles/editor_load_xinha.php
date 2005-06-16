<?php

/**
 * @param array p_dbColumns
 * @param User p_user
 * @return void
 */
function editor_load_xinha($p_dbColumns, $p_user) {
	global $Campsite;
	global $ADMIN;
	$stylesheetFile = $Campsite['HTML_COMMON_DIR'] 
		.'/priv/pub/issues/sections/articles/article_stylesheet.css';
	?>	
<script type="text/javascript">
	//<![CDATA[
      _editor_url = "/javascript/xinha/";
      _editor_lang = "<?php p($_REQUEST['TOL_Language']); ?>";
      _campsite_article_id = <?php echo $_REQUEST['Article']; ?>;
	//]]>
</script>    

<!-- Load the HTMLArea file -->
<script type="text/javascript" src="/javascript/xinha/htmlarea.js"></script>

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
		if (link) {
			if (/^img$/i.test(link.tagName)) {
				link = link.parentNode;
			}
			if (!/^a$/i.test(link.tagName)) {
				link = null;
			}
		}
	}
	popupWindowTarget = "campsite_internal_link.php?TOL_Language=<?php p($_REQUEST["TOL_Language"]); ?>";
	if (!link) {
    	var sel = editor._getSelection();
    	var range = editor._createRange(sel);
    	var compare = 0;
    	if (HTMLArea.is_ie) {
      		compare = range.compareEndPoints("StartToEnd", range);
    	} 
    	else {
      		compare = range.compareBoundaryPoints(range.START_TO_END, range);
    	}
    	if (compare == 0) {
      		alert(HTMLArea._lc("You need to select some text before creating a link"));
      		return;
    	}
	    outparam = {
		      f_href : '',
		      f_title : '',
		      f_target : '',
		      f_usetarget : editor.config.makeLinkShowsTarget
		    };
	} 
	else {
	    outparam = {
	      f_href   : HTMLArea.is_ie ? editor.stripBaseURL(link.href) : link.getAttribute("href"),
	      f_title  : link.title,
	      f_target : link.target,
	      f_usetarget : editor.config.makeLinkShowsTarget
	    };
		// Pass the arguments to the popup window so that it
		// can populate the dropdown controls.
		popupWindowTarget += "&" + outparam["f_href"].replace("campsite_internal_link?", "");
	}
	editor._popupDialog(popupWindowTarget, function(param) {
    	if (!param) {
      		return false;
    	}
    	var a = link;
    	if (!a) {
    		try {
      			editor._doc.execCommand("createlink", false, param.f_href);
      			a = editor.getParentElement();
      			var sel = editor._getSelection();
      			var range = editor._createRange(sel);
      			if (!HTMLArea.is_ie) {
        			a = range.startContainer;
        			if (!/^a$/i.test(a.tagName)) {
          				a = a.nextSibling;
          				if (a == null) {
            				a = range.startContainer.parentNode;
          				}
        			}
      			}
    		} 
    		catch(e) {}
    	}
    	else {
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
    	if (!(a && /^a$/i.test(a.tagName))) {
      		return false;
    	}
    	a.target = param.f_target.trim();
    	a.title = param.f_title.trim();
    	editor.selectNodeContents(a);
    	editor.updateToolbar();
  	}, outparam);
};
	
xinha_editors = null;
xinha_init    = null;
xinha_config  = null;
xinha_plugins = null;

// This contains the names of textareas we will make into Xinha editors
xinha_init = xinha_init ? xinha_init : function()
{
  /** STEP 1 ***************************************************************
   * First, what are the plugins you will be using in the editors on this
   * page.  List all the plugins you will need, even if not all the editors
   * will use all the plugins.
   ************************************************************************/

  xinha_plugins = xinha_plugins ? xinha_plugins :
  [
	'ImageManager',
	<?php if ($p_user->hasPermission('EditorTable')) { ?>
	'TableOperations',
	<?php } ?>
	<?php if ($p_user->hasPermission('EditorListNumber')) { ?>
	'ListType',
	<?php } ?>
    'FullScreen',
    'UltraClean',
    'CharacterMap',
    'FindReplace',
    'RemoveParagraphs'
  ];
	// THIS BIT OF JAVASCRIPT LOADS THE PLUGINS, NO TOUCHING  :)
	if(!HTMLArea.loadPlugins(xinha_plugins, xinha_init)) return;

  /** STEP 2 ***************************************************************
   * Now, what are the names of the textareas you will be turning into
   * editors?
   ************************************************************************/

  xinha_editors = xinha_editors ? xinha_editors :
  [
  	<?php
	foreach ($p_dbColumns as $dbColumn) {	
		if (stristr($dbColumn->getType(), "blob")) {
			$xinhaEditors[] = "'".$dbColumn->getName()."'";
		}
	}
	echo implode(",", $xinhaEditors);
	?>
  ];

  /** STEP 3 ***************************************************************
   * We create a default configuration to be used by all the editors.
   * If you wish to configure some of the editors differently this will be
   * done in step 4.
   *
   * If you want to modify the default config you might do something like this.
   *
   *   xinha_config = new HTMLArea.Config();
   *   xinha_config.width  = 640;
   *   xinha_config.height = 420;
   *
   *************************************************************************/

   xinha_config = xinha_config ? xinha_config : new HTMLArea.Config();
   xinha_config.statusBar = false;
   xinha_config.htmlareaPaste =  HTMLArea.is_gecko ? false : true;
   xinha_config.flowToolbars = false;
   xinha_config.mozParaHandler = "built-in";
   // Change the built-in icon for "web link"
   linkIcon = _editor_url + xinha_config.imgURL + "ed_campsite_link.gif";
   xinha_config.btnList["createlink"] = [ "Insert Web Link", linkIcon, false, function(e) {e._createLink();} ],
   // Change the removeformat button to work in text mode.
   xinha_config.btnList["removeformat"] = [ "Remove formatting", ["ed_buttons_main.gif",4,4], true, function(e) {e.execCommand("removeformat");} ],
   // Put the "find-replace" plugin in a better location
   //xinha_config.addToolbarElement([], ["FR-findreplace"], 0);
   xinha_config.addToolbarElement(["FR-findreplace"], ["paste","cut","copy","redo","undo"], +1);

   // Add in our style sheet for the "subheads".
   xinha_config.pageStyle = "<?php echo str_replace("\n", "", file_get_contents($stylesheetFile)); ?>";
   subheadTooltip = HTMLArea._lc('Subhead', 'Campsite');
   xinha_config.registerButton({
       // The ID of the button.
       id        : "campsite-subhead",
       // The tooltip.
       tooltip   : subheadTooltip,
       // Image to be displayed in the toolbar.
       image     : "/javascript/htmlarea/images/campsite_subhead.gif",
       // TRUE = enabled in text mode
       // FALSE = disabled in text mode
       textMode  : false,
       // Called when the button is clicked.
       action    : CampsiteSubhead,
       // The button will be disabled if outside
       // the specified element.
       context   : ''
   });

   internalLinkTooltip = HTMLArea._lc('Insert Internal Link', 'Campsite');
   xinha_config.registerButton({
       // The ID of the button.
       id        : "campsite-internal-link",
       // The tooltip.
       tooltip   : internalLinkTooltip,
       // Image to be displayed in the toolbar.
       image     : "/javascript/htmlarea/images/campsite_internal_link.gif",
       // TRUE = enabled in text mode
       // FALSE = disabled in text mode
       textMode  : false,
       // Called when the button is clicked.
       action    : CampsiteInternalLink,
       // The button will be disabled if outside
       // the specified element.
       context   : ''
   });

   xinha_config.toolbar = [
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
		  	//"separator",
		<?php if ($p_user->hasPermission('EditorTextAlignment')) { ?>
			"justifyleft", 
			"justifycenter", 
			"justifyright", 
			"justifyfull", 
			//"separator",
		<?php } ?>
		<?php if ($p_user->hasPermission('EditorIndent')) { ?>
		  	"outdent", 
		  	"indent", 
		  	//"separator",
		<?php } ?>
		<?php if ($p_user->hasPermission('EditorCopyCutPaste')) { ?>
		  	"copy", 
		  	"cut", 
		  	"paste", 
		  	"space", 
		  	//"separator", 
		<?php } ?>
		<?php if ($p_user->hasPermission('EditorUndoRedo')) { ?>				  
			"undo", 
			"redo", 
			//"separator", 
		<?php } ?>				  
		<?php if ($p_user->hasPermission('EditorTextDirection')) { ?>
		  	"lefttoright", 
		  	"righttoleft", 
		  	//"separator", 
		<?php } ?>
		<?php if ($p_user->hasPermission('EditorLink')) { ?>
		  	"campsite-internal-link", 
		  	"createlink", 
		  	//"separator",
		<?php } ?>
		<?php if ($p_user->hasPermission('EditorSubhead')) { ?>
		  	"campsite-subhead", 
		<?php } ?>
		<?php if ($p_user->hasPermission('EditorImage')) { ?>
		  	"insertimage", 
		  	//"separator",
		<?php } ?>
			//"killword",
			"removeformat",
		<?php if ($p_user->hasPermission('EditorSourceView')) { ?>
		  	"htmlmode", 
		<?php } ?>
		<?php if ($p_user->hasPermission('EditorEnlarge')) { ?>
		  	"popupeditor",
		<?php } ?>
			"linebreak",
		],

		[ 
		<?php if ($p_user->hasPermission('EditorFontFace')) { ?>
		  	//"fontname", 
		  	//"space",
		<?php } ?>
		<?php if ($p_user->hasPermission('EditorFontSize')) { ?>
		  	//"fontsize", 
		  	//"space",
		<?php } ?>
		<?php if (false) { ?>
		  	"formatblock", 
		  	"space",
		<?php } ?>
		<?php if ($p_user->hasPermission('EditorListBullet')) { ?>
		  	"insertunorderedlist", 
		<?php } ?>
		<?php if ($p_user->hasPermission('EditorListNumber')) { ?>
		  	"insertorderedlist", 
		  	//"separator", 
		<?php } ?>
		<?php if ($p_user->hasPermission('EditorHorizontalRule')) { ?>
		  	"inserthorizontalrule", 
		  	//"separator",
		<?php } ?>
		<?php if ($p_user->hasPermission('EditorFontColor')) { ?>
		  	"forecolor", 
		  	"hilitecolor", 
		  	//"separator",
		<?php } ?>
		<?php if ($p_user->hasPermission('EditorSubscript')) { ?>
		  	"subscript",
		<?php } ?>
		<?php if ($p_user->hasPermission('EditorSuperscript')) { ?>
		 	"superscript",
		<?php } ?>
		  ],
		  
		<?php if ($p_user->hasPermission('EditorTable')) { ?>
		  [ "linebreak", "inserttable" ],
		<?php } ?>
	];

  /** STEP 3 ***************************************************************
   * We first create editors for the textareas.
   *
   * You can do this in two ways, either
   *
   *   xinha_editors   = HTMLArea.makeEditors(xinha_editors, xinha_config, xinha_plugins);
   *
   * if you want all the editor objects to use the same set of plugins, OR;
   *
   *   xinha_editors = HTMLArea.makeEditors(xinha_editors, xinha_config);
   *   xinha_editors['myTextArea'].registerPlugins(['Stylist','FullScreen']);
   *   xinha_editors['anotherOne'].registerPlugins(['CSS','SuperClean']);
   *
   * if you want to use a different set of plugins for one or more of the
   * editors.
   ************************************************************************/

  xinha_editors   = HTMLArea.makeEditors(xinha_editors, xinha_config, xinha_plugins);
	
  /** STEP 4 ***************************************************************
   * If you want to change the configuration variables of any of the
   * editors,  this is the place to do that, for example you might want to
   * change the width and height of one of the editors, like this...
   *
   *   xinha_editors.myTextArea.config.width  = 640;
   *   xinha_editors.myTextArea.config.height = 480;
   *
   ************************************************************************/


  /** STEP 5 ***************************************************************
   * Finally we "start" the editors, this turns the textareas into
   * Xinha editors.
   ************************************************************************/
  HTMLArea.startEditors(xinha_editors);
  	<?php
  	// Warning: you are about to witness a huge hack!
	// This quickly flips the htmlareas between text mode
	// and wysiwyg mode so that when there are more than
	// one on a page, they are all editable.
//	if (count($xinhaEditors) > 0) {
//		$firstEditor = array_shift($xinhaEditors);
//		?>
//		xinha_editors["<?php p(str_replace("'", "", $firstEditor)); ?>"].generate();		
//		<?php
//		$count = 1;
//		foreach ($xinhaEditors as $field) {
//			?>
//			//setTimeout(function() {
//					//xinha_editors["<?php p(str_replace("'", "", $field)); ?>"].generate();
//				//}, <?php p($count++*300); ?>);
//				
//			<?php
//		}
//	}
	?>  
}

window.onload = xinha_init;
</script>
  <!--<link href="/javascript/xinha/skins/xp-blue/skin.css" rel="Stylesheet" />-->
<?php
} // fn editor_load_xinha
?>
