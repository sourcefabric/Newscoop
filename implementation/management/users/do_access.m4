B_HTML
INCLUDE_PHP_LIB(<*$ADMIN_DIR/users*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUsers*>)

B_HEAD
	X_EXPIRES
	X_TITLE(<*Updating user account permissions*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change user account permissions.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Updating user account permissions*>)
B_HEADER_BUTTONS
X_HBUTTON(<*Users*>, <*users/*>)
E_HEADER_BUTTONS
E_HEADER

<?php 
    todefnum('User');

todefradio('cManagePub');
todefradio('cDeletePub');
todefradio('cManageIssue');
todefradio('cDeleteIssue');
todefradio('cManageSection');
todefradio('cDeleteSection');
todefradio('cAddArticle');
todefradio('cChangeArticle');
todefradio('cDeleteArticle');
todefradio('cAddImage');
todefradio('cChangeImage');
todefradio('cDeleteImage');
todefradio('cManageTempl');
todefradio('cDeleteTempl');
todefradio('cManageUsers');
todefradio('cManageSubscriptions');
todefradio('cDeleteUsers');
todefradio('cManageUserTypes');
todefradio('cManageArticleTypes');
todefradio('cDeleteArticleTypes');
todefradio('cManageLanguages');
todefradio('cDeleteLanguages');
todefradio('cMailNotify');
todefradio('cManageClasses');
todefradio('cManageDictionary');
todefradio('cDeleteDictionary');
todefradio('cManageCountries');
todefradio('cDeleteCountries');
todefradio('cViewLogs');
todefradio('cManageLocalizer');
todefradio('cManageIndexer');
todefradio('cPublish');
todefradio('cManageTopics');

todefradio('cEditorBold');
todefradio('cEditorItalic');
todefradio('cEditorUnderline');
todefradio('cEditorUndoRedo');
todefradio('cEditorCopyCutPaste');
todefradio('cEditorImage');
todefradio('cEditorTextAlignment');
todefradio('cEditorFontColor');
todefradio('cEditorFontSize');
todefradio('cEditorFontFace');
todefradio('cEditorTable');
todefradio('cEditorSuperscript');
todefradio('cEditorSubscript');
todefradio('cEditorStrikethrough');
todefradio('cEditorIndent');
todefradio('cEditorListBullet');
todefradio('cEditorListNumber');
todefradio('cEditorHorizontalRule');
todefradio('cEditorSourceView');
todefradio('cEditorEnlarge');
todefradio('cEditorTextDirection');
todefradio('cEditorLink');
todefradio('cEditorSubhead');

    query ("SELECT * FROM Users WHERE Id=$User", 'uacc');
    if ($NUM_ROWS) {
	query ("SELECT * FROM UserPerm WHERE IdUser=$User", 'uperm');
	if ($NUM_ROWS) { 
	    fetchRow($uacc);
	    fetchRow($uperm);
	
	?>dnl

B_CURRENT
X_CURRENT(<*User account*>, <*<?php  pgetHVar($uacc,'UName'); ?>*>)
E_CURRENT

<P>
B_MSGBOX(<*Updating user account permissions*>)
<?php 
$queryStr = "UPDATE UserPerm SET "
	." ManagePub='$cManagePub', "
	." DeletePub='$cDeletePub',"
	." ManageIssue='$cManageIssue',"
	." DeleteIssue='$cDeleteIssue', "
	." ManageSection='$cManageSection', "
	." DeleteSection='$cDeleteSection', "
	." AddArticle='$cAddArticle', "
	." ChangeArticle='$cChangeArticle', "
	." DeleteArticle='$cDeleteArticle', "
	." AddImage='$cAddImage', "
	." ChangeImage='$cChangeImage', "
	." DeleteImage='$cDeleteImage', "
	." ManageTempl='$cManageTempl', "
	." DeleteTempl='$cDeleteTempl', "
	." ManageUsers='$cManageUsers', "
	." ManageSubscriptions='$cManageSubscriptions', "
	." DeleteUsers='$cDeleteUsers', "
	." ManageUserTypes='$cManageUserTypes', "
	." ManageArticleTypes='$cManageArticleTypes', "
	." DeleteArticleTypes='$cDeleteArticleTypes', "
	." ManageLanguages='$cManageLanguages', "
	." DeleteLanguages='$cDeleteLanguages', "
	." MailNotify='$cMailNotify', "
	." ManageClasses='$cManageClasses', "
	." ManageDictionary='$cManageDictionary', "
	." DeleteDictionary='$cDeleteDictionary', "
	." ManageCountries='$cManageCountries', "
	." DeleteCountries='$cDeleteCountries', "
	." ViewLogs='$cViewLogs' , "
	." ManageLocalizer = '$cManageLocalizer', "
	." ManageIndexer = '$cManageIndexer', "
	." Publish = '$cPublish', "
	." ManageTopics= '$cManageTopics', "
	." EditorImage='$cEditorImage', "
	." EditorTextAlignment='$cEditorTextAlignment', "
	." EditorFontColor='$cEditorFontColor', "
	." EditorFontSize='$cEditorFontSize', "
	." EditorFontFace='$cEditorFontFace', "
	." EditorTable='$cEditorTable', "
	." EditorSuperscript='$cEditorSuperscript', "
	." EditorSubscript='$cEditorSubscript', "
	." EditorStrikethrough='$cEditorStrikethrough', "
	." EditorIndent='$cEditorIndent', "
	." EditorListBullet='$cEditorListBullet', "
	." EditorListNumber='$cEditorListNumber', "
	." EditorHorizontalRule='$cEditorHorizontalRule', "
	." EditorSourceView='$cEditorSourceView', "
	." EditorEnlarge='$cEditorEnlarge', "
	." EditorTextDirection='$cEditorTextDirection', "
	." EditorLink='$cEditorLink', "
	." EditorSubhead='$cEditorSubhead',"
	." EditorBold='$cEditorBold',"
	." EditorItalic='$cEditorItalic',"
	." EditorUnderline='$cEditorUnderline',"
	." EditorUndoRedo='$cEditorUndoRedo',"
	." EditorCopyCutPaste='$cEditorCopyCutPaste'"
	." WHERE IdUser=$User";
query($queryStr);

	## added by sebastian
	if (function_exists ("incModFile"))
		incModFile ($User);

	if ($AFFECTED_ROWS > 0) { ?>dnl
		X_AUDIT(<*55*>, <*getGS('Permissions for $1 changed',getHVar($uacc,'UName'))*>)
		X_MSGBOX_TEXT(<*<LI><?php  putGS('User account permissions have been successfuly updated.'); ?></LI>*>)
<?php 	} else { ?>dnl
		X_MSGBOX_TEXT(<*<LI><?php  putGS('User account permissions could not be updated.'); ?></LI>
<?php 	} ?>*>)
	B_MSGBOX_BUTTONS
<?php 
    if ($AFFECTED_ROWS > 0) { ?>dnl
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/users/*>)
<?php  } else { ?>dnl
		REDIRECT(<OK**>, <*OK*>, <*X_ROOT/users/access.php?User=<?php  pencURL($User); ?>*>)
<?php  } ?>dnl
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('This user account does not have permissions information.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

<?php  } else { ?>dnl
<BLOCKQUOTE>
	<LI><?php  putGS('No such user account.'); ?></LI>
</BLOCKQUOTE>
<?php  } ?>dnl

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML
