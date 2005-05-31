INCLUDE_PHP_LIB(<*$ADMIN_DIR/u_types*>)
B_DATABASE

CHECK_BASIC_ACCESS
CHECK_ACCESS(<*ManageUserTypes*>)

B_HEAD
	X_TITLE(<*Updating user types permissions*>)
<?php  if ($access == 0) { ?>dnl
	X_AD(<*You do not have the right to change user type permissions.*>)
<?php  } ?>dnl
E_HEAD

<?php  if ($access) { ?>dnl
B_STYLE
E_STYLE

B_BODY

B_HEADER(<*Updating user type permissions*>)
B_HEADER_BUTTONS
X_HBUTTON(<*User Types*>, <*u_types/*>)
E_HEADER_BUTTONS
E_HEADER
<?php 
todef('UType');

todef('cName');

todefradio('cReader');
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
todefradio('cManageReaders');
todefradio('cManageSubscriptions');
todefradio('cDeleteUsers');
todefradio('cManageUserTypes');
todefradio('cManageArticleTypes');
todefradio('cDeleteArticleTypes');
todefradio('cManageLanguages');
todefradio('cDeleteLanguages');
todefradio('cMailNotify');
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
?>


<P>
B_MSGBOX(<*Updating user type permissions*>)
<?php  if ($cName != "") {
	if ($UType != $cName) {
		query ("SELECT COUNT(*) FROM UserTypes WHERE Name='$cName'", 'c');
		fetchRowNum($c);
		$ok = (getNumVar($c,0) == 0);
	}
	else
		$ok= 1;
	if ($ok) {
		$queryStr = "UPDATE UserTypes SET "
			." Name='$cName', "
			." Reader='$cReader',"
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
			." ManageReaders='$cManageReaders', "
			." ManageSubscriptions='$cManageSubscriptions', "
			." DeleteUsers='$cDeleteUsers', "
			." ManageUserTypes='$cManageUserTypes', "
			." ManageArticleTypes='$cManageArticleTypes', "
			." DeleteArticleTypes='$cDeleteArticleTypes', "
			." ManageLanguages='$cManageLanguages', "
			." DeleteLanguages='$cDeleteLanguages', "
			." MailNotify='$cMailNotify', "
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
			." EditorSubhead='$cEditorSubhead', "
			." EditorBold='$cEditorBold',"
			." EditorItalic='$cEditorItalic',"
			." EditorUnderline='$cEditorUnderline',"
			." EditorUndoRedo='$cEditorUndoRedo',"
			." EditorCopyCutPaste='$cEditorCopyCutPaste'"
			." WHERE Name='$UType'";
		query($queryStr);
		$ok= $AFFECTED_ROWS > 0;
	}
} else
	$ok= 0;
if ($ok) { ?>
	X_MSGBOX_TEXT(<*<LI><?php  putGS('User type permissions have been successfuly updated.'); ?></LI>*>)
X_AUDIT(<*123*>, <*getGS('User type $1 changed permissions',$cName)*>)
<?php  } else { ?>dnl
	X_MSGBOX_TEXT(<*<LI><?php  putGS('User type permissions could not be updated.'); ?></LI>
<?php  if ($cName == "") { ?>dnl
	<LI><?php  putGS('You must complete the $1 field.','<B>'.getGS('Name').'</B>'); ?><LI>
<?php  } ?>dnl
	*>)
<?php  } ?>dnl
	B_MSGBOX_BUTTONS
		REDIRECT(<*Done*>, <*Done*>, <*X_ROOT/u_types/*>)
	E_MSGBOX_BUTTONS
E_MSGBOX
<P>

X_COPYRIGHT
E_BODY
<?php  } ?>dnl

E_DATABASE
E_HTML

