<?php

function camp_get_permission_list()
{
    $translator = \Zend_Registry::get('container')->getService('translator');
    
    $content_group = array(
        'ManagePub'=>$translator->trans('User may add/change publications', array(), 'users'),
	'DeletePub'=>$translator->trans('User may delete publications', array(), 'users'),
	'ManageIssue'=>$translator->trans('User may add/change issues', array(), 'users'),
	'DeleteIssue'=>$translator->trans('User may delete issues', array(), 'users'),
	'ManageSection'=>$translator->trans('User may add/change sections', array(), 'users'),
	'DeleteSection'=>$translator->trans('User may delete sections', array(), 'users'),
	'AddArticle'=>$translator->trans('User may add articles', array(), 'users'),
	'ChangeArticle'=>$translator->trans('User may change articles', array(), 'users'),
	'MoveArticle'=>$translator->trans('User may move articles', array(), 'users'),
	'TranslateArticle'=>$translator->trans('User may translate articles', array(), 'users'),
	'AttachImageToArticle'=>$translator->trans('User may attach images to articles', array(), 'users'),
	'AttachTopicToArticle'=>$translator->trans('User may attach topics to articles', array(), 'users'),
	'Publish'=>$translator->trans('User may publish articles', array(), 'users'),
	'DeleteArticle'=>$translator->trans('User may delete articles', array(), 'users'),
	'AddImage'=>$translator->trans('User may add images', array(), 'users'),
	'ChangeImage'=>$translator->trans('User may change images', array(), 'users'),
	'DeleteImage'=>$translator->trans('User may delete images', array(), 'users'),
	'AddFile'=>$translator->trans('User may add article attachments', array(), 'users'),
	'ChangeFile'=>$translator->trans('User may change article attachments', array(), 'users'),
	'DeleteFile'=>$translator->trans('User may delete article attachments', array(), 'users'),
	'ManageTopics'=>$translator->trans('User may manage topics', array(), 'users'),
	'CommentModerate'=>$translator->trans('User may moderate comments', array(), 'users'),
	'CommentEnable' => $translator->trans('User may enable comments', array(), 'users'));

    $templates_group = array(
	'ManageTempl'=>$translator->trans('User may manage templates', array(), 'users'),
	'DeleteTempl'=>$translator->trans('User may delete templates', array(), 'users'));

    $administrative_group = array(
	'ChangeSystemPreferences'=>$translator->trans('User may change system preferences', array(), 'users'),
	'ClearCache'=>$translator->trans('User may clear up the system cache', array(), 'users'),
    'ManageBackup'=>$translator->trans('User may backup and restore the site data', array(), 'users'));

    $users_group = array(
	'ManageUsers'=>$translator->trans('User may add/change staff accounts and passwords', array(), 'users', array(), 'users'),
	'DeleteUsers'=>$translator->trans('User may delete staff accounts', array(), 'users', array(), 'users'),
	'ManageReaders'=>$translator->trans('User may add/change subscribers accounts and passwords', array(), 'users', array(), 'users'),
	'ManageSubscriptions'=>$translator->trans('User may manage user subscriptions', array(), 'users', array(), 'users'),
	'ManageUserTypes'=>$translator->trans('User may manage account types', array(), 'users', array(), 'users'),
         'EditAuthors'=>$translator->trans('User may change authors', array(), 'users', array(), 'users')
        );

    $article_types_group = array(
	'ManageArticleTypes'=>$translator->trans('User may add/change article types', array(), 'users'),
	'DeleteArticleTypes'=>$translator->trans('User may delete article types', array(), 'users'));

    $languages_group = array(
        'ManageLanguages'=>$translator->trans('User may add languages and manage language information', array(), 'users'),
	'DeleteLanguages'=>$translator->trans('User may delete languages', array(), 'users'));

    $countries_group = array(
	'ManageCountries'=>$translator->trans('User may add/change country entries', array(), 'users'),
	'DeleteCountries'=>$translator->trans('User may delete country entries', array(), 'users'));

    $misc_group = array(
	'ViewLogs'=>$translator->trans('User may view audit logs', array(), 'users'),
	'MailNotify'=>$translator->trans('User will be notified on several events', array(), 'users'));

    $localizer_group = array('ManageLocalizer'=>$translator->trans('User may manage localizer', array(), 'users'));

    $editor_group_1 = array(
        'EditorBold'=>$translator->trans('User may use bold', array(), 'users'),
	'EditorItalic'=>$translator->trans('User may use italic', array(), 'users'),
	'EditorUnderline'=>$translator->trans('User may use underline', array(), 'users'),
	'EditorStrikethrough'=>$translator->trans('User may use strikethrough', array(), 'users'),
	'EditorTextAlignment'=>$translator->trans('User may change text alignment', array(), 'users'),
	'EditorCopyCutPaste'=>$translator->trans('User may copy, cut, and paste', array(), 'users'),
	'EditorUndoRedo'=>$translator->trans('User may undo/redo', array(), 'users'),
	'EditorFindReplace'=>$translator->trans('User may find and replace', array(), 'users'),
	'EditorCharacterMap'=>$translator->trans('User may add special characters', array(), 'users'),
	'EditorTextDirection'=>$translator->trans('User may change text direction', array(), 'users'),
	'EditorIndent'=>$translator->trans('User may set indents', array(), 'users'),
	'EditorLink'=>$translator->trans('User may add links', array(), 'users'),
	'EditorSubhead'=>$translator->trans('User may add subheads', array(), 'users'),
	'EditorImage'=>$translator->trans('User may insert images', array(), 'users'),
	'EditorSourceView'=>$translator->trans('User may view the HTML source', array(), 'users'),
	'EditorEnlarge'=>$translator->trans('User may enlarge the editor', array(), 'users'),
	'EditorStatusBar'=>$translator->trans('User may use the editor status bar', array(), 'users'));

    $editor_group_2 = array(
	'EditorFontFace'=>$translator->trans('User may change the font face', array(), 'users'),
	'EditorFontSize'=>$translator->trans('User may change the font size', array(), 'users'),
	'EditorListBullet'=>$translator->trans('User may create bulleted lists', array(), 'users'),
	'EditorListNumber'=>$translator->trans('User may create numbered lists', array(), 'users'));

    $editor_group_3 = array('EditorTable'=>$translator->trans('User may insert tables', array(), 'users'));

    $editor_group_4 = array(
	'EditorHorizontalRule'=>$translator->trans('User may insert horizontal rules', array(), 'users'),
	'EditorFontColor'=>$translator->trans('User may change the font color', array(), 'users'),
	'EditorSuperscript'=>$translator->trans('User may use superscripts', array(), 'users'),
	'EditorSubscript'=>$translator->trans('User may use subscripts', array(), 'users'),
	'EditorSpellcheckerEnabled'=>$translator->trans('Enable Firefox spell checking by default', array(), 'users'));

    $rights = array($translator->trans('Content')=>$content_group,
		    $translator->trans('Templates', array(), 'users')=>$templates_group,
		    $translator->trans('Staff/Subscribers Management', array(), 'users')=>$users_group,
		    $translator->trans('Administrative tasks', array(), 'users')=>$administrative_group,
		    $translator->trans('Article Types')=>$article_types_group,
		    $translator->trans('Languages')=>$languages_group,
		    $translator->trans('Countries')=>$countries_group,
		    $translator->trans('Miscellaneous', array(), 'users')=>$misc_group,
		    $translator->trans('Localizer')=>$localizer_group,
		    $translator->trans('Editor Basic Settings', array(), 'users')=>$editor_group_1,
		    $translator->trans('Editor Advanced Font Settings', array(), 'users')=>$editor_group_2,
		    $translator->trans('Editor Table Settings', array(), 'users')=>$editor_group_3,
		    $translator->trans('Editor Miscellaneous Settings', array(), 'users')=>$editor_group_4);

    // plugins: extend permission list
    $rights[$translator->trans('Plugins')] = array('plugin_manager' => 'User may manage Plugins');
    foreach (CampPlugin::GetPluginsInfo(true) as $info) {
    	foreach ($info['permissions'] as $permission => $label) {
    		$rights[$info['label']][$permission] = $translator->trans($label);
    	}
    }

    $pluginsService = \Zend_Registry::get('container')->get('newscoop.plugins.service');
    $collectedPermissionsData = $pluginsService->collectPermissions();
    $rights = array_merge($collectedPermissionsData, $rights);

    return $rights;
} // fn camp_get_permission_list

?>
