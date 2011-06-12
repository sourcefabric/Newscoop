<?php

function camp_get_permission_list()
{
    $content_group = array(
        'ManagePub'=>getGS('User may add/change publications'),
	'DeletePub'=>getGS('User may delete publications'),
	'ManageIssue'=>getGS('User may add/change issues'),
	'DeleteIssue'=>getGS('User may delete issues'),
	'ManageSection'=>getGS('User may add/change sections'),
	'DeleteSection'=>getGS('User may delete sections'),
	'AddArticle'=>getGS('User may add articles'),
	'ChangeArticle'=>getGS('User may change articles'),
	'MoveArticle'=>getGS('User may move articles'),
	'TranslateArticle'=>getGS('User may translate articles'),
	'AttachImageToArticle'=>getGS('User may attach images to articles'),
	'AttachTopicToArticle'=>getGS('User may attach topics to articles'),
	'Publish'=>getGS('User may publish articles'),
	'DeleteArticle'=>getGS('User may delete articles'),
	'AddImage'=>getGS('User may add images'),
	'ChangeImage'=>getGS('User may change images'),
	'DeleteImage'=>getGS('User may delete images'),
	'AddFile'=>getGS('User may add article attachments'),
	'ChangeFile'=>getGS('User may change article attachments'),
	'DeleteFile'=>getGS('User may delete article attachments'),
	'ManageTopics'=>getGS('User may manage topics'),
	'CommentModerate'=>getGS('User may moderate comments'),
	'CommentEnable' => getGS('User may enable comments'));

    $templates_group = array(
	'ManageTempl'=>getGS('User may manage templates'),
	'DeleteTempl'=>getGS('User may delete templates'));

    $administrative_group = array(
	'ChangeSystemPreferences'=>getGS('User may change system preferences'),
	'ClearCache'=>getGS('User may clear up the system cache'),
    'ManageBackup'=>getGS('User may backup and restore the site data'));

    $users_group = array(
	'ManageUsers'=>getGS('User may add/change staff accounts and passwords'),
	'DeleteUsers'=>getGS('User may delete staff accounts'),
	'ManageReaders'=>getGS('User may add/change subscribers accounts and passwords'),
	'ManageSubscriptions'=>getGS('User may manage user subscriptions'),
	'ManageUserTypes'=>getGS('User may manage account types'),
         'EditAuthors'=>getGS('User may change authors')
        );

    $article_types_group = array(
	'ManageArticleTypes'=>getGS('User may add/change article types'),
	'DeleteArticleTypes'=>getGS('User may delete article types'));

    $languages_group = array(
        'ManageLanguages'=>getGS('User may add languages and manage language information'),
	'DeleteLanguages'=>getGS('User may delete languages'));

    $countries_group = array(
	'ManageCountries'=>getGS('User may add/change country entries'),
	'DeleteCountries'=>getGS('User may delete country entries'));

    $misc_group = array(
	'ViewLogs'=>getGS('User may view audit logs'),
	'MailNotify'=>getGS('User will be notified on several events'));

    $localizer_group = array('ManageLocalizer'=>getGS('User may manage localizer'));

    $editor_group_1 = array(
        'EditorBold'=>getGS('User may use bold'),
	'EditorItalic'=>getGS('User may use italic'),
	'EditorUnderline'=>getGS('User may use underline'),
	'EditorStrikethrough'=>getGS('User may use strikethrough'),
	'EditorTextAlignment'=>getGS('User may change text alignment'),
	'EditorCopyCutPaste'=>getGS('User may copy, cut, and paste'),
	'EditorUndoRedo'=>getGS('User may undo/redo'),
	'EditorFindReplace'=>getGS('User may find and replace'),
	'EditorCharacterMap'=>getGS('User may add special characters'),
	'EditorTextDirection'=>getGS('User may change text direction'),
	'EditorIndent'=>getGS('User may set indents'),
	'EditorLink'=>getGS('User may add links'),
	'EditorSubhead'=>getGS('User may add subheads'),
	'EditorImage'=>getGS('User may insert images'),
	'EditorSourceView'=>getGS('User may view the HTML source'),
	'EditorEnlarge'=>getGS('User may enlarge the editor'),
	'EditorStatusBar'=>getGS('User may use the editor status bar'));

    $editor_group_2 = array(
	'EditorFontFace'=>getGS('User may change the font face'),
	'EditorFontSize'=>getGS('User may change the font size'),
	'EditorListBullet'=>getGS('User may create bulleted lists'),
	'EditorListNumber'=>getGS('User may create numbered lists'));

    $editor_group_3 = array('EditorTable'=>getGS('User may insert tables'));

    $editor_group_4 = array(
	'EditorHorizontalRule'=>getGS('User may insert horizontal rules'),
	'EditorFontColor'=>getGS('User may change the font color'),
	'EditorSuperscript'=>getGS('User may use superscripts'),
	'EditorSubscript'=>getGS('User may use subscripts'),
	'EditorSpellcheckerEnabled'=>getGS('Enable Firefox spell checking by default'));

    $rights = array(getGS('Content')=>$content_group,
		    getGS('Templates')=>$templates_group,
		    getGS('Staff/Subscribers Management')=>$users_group,
		    getGS('Administrative tasks')=>$administrative_group,
		    getGS('Article Types')=>$article_types_group,
		    getGS('Languages')=>$languages_group,
		    getGS('Countries')=>$countries_group,
		    getGS('Miscellaneous')=>$misc_group,
		    getGS('Localizer')=>$localizer_group,
		    getGS('Editor Basic Settings')=>$editor_group_1,
		    getGS('Editor Advanced Font Settings')=>$editor_group_2,
		    getGS('Editor Table Settings')=>$editor_group_3,
		    getGS('Editor Miscellaneous Settings')=>$editor_group_4);

    // plugins: extend permission list
    $rights[getGS('Plugins')] = array('plugin_manager' => 'User may manage Plugins');
    foreach (CampPlugin::GetPluginsInfo(true) as $info) {
    	foreach ($info['permissions'] as $permission => $label) {
    		$rights[$info['label']][$permission] = getGS($label);
    	}
    }

    return $rights;
} // fn camp_get_permission_list

?>
