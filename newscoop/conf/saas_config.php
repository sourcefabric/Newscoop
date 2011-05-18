<?php
	$this->saasConfig = array(
	'permissions'=>array(
		/*article types*/
		'ManageArticleTypes',
		'DeleteArticleTypes',

		/*templates*/
		'ManageTempl',
		'DeleteTempl',
		'ManageIssueTemplates',
		'ManageSectionTemplates',

		/*plugins*/
		'plugin_manager',

		/*plugin blog*/
		'plugin_blog_moderator',
		'plugin_blog_admin',

		/*plugin poll*/
		'plugin_poll',

		/*plugin interview*/
		'plugin_interview_notify',
		'plugin_interview_guest',
		'plugin_interview_moderator',
		'plugin_interview_admin',

		/*countries*/
		'ManageCountries',
		'DeleteCountries',

		/*localizer*/
		'ManageLocalizer',

		/*subscriptions*/
		'ManageSubscriptions',
		'ManageSectionSubscriptions',

		/*readers*/
		'ManageReaders',

		/*publications*/
		//'ManagePub',
		'AddPub',
		'ManagePubInvalidUrlTemplate',

		/*system preferences*/
		'ManageSystemPreferences'

	),
	'privileges'=>
		array(
			array('resource'=>'staff', 'privilege'=>'add'),
			array('resource'=>'publication', 'privilege'=>'delete'),
			array('resource'=>'template', 'privilege'=>'delete'),
			array('resource'=>'template', 'privilege'=>'manage'),
			array('resource'=>'subscriber', 'privilege'=>'manage'),
			array('resource'=>'subscriber', 'privilege'=>'add'),
			array('resource'=>'country', 'privilege'=>'manage'),
			array('resource'=>'country', 'privilege'=>'delete'),
			array('resource'=>'localizer', 'privilege'=>'manage'),
			array('resource'=>'plugin', 'privilege'=>'manage'),
			array('resource'=>'pluginblog', 'privilege'=>'admin'),
			array('resource'=>'pluginblog', 'privilege'=>'moderator'),
			array('resource'=>'plugininterview', 'privilege'=>'notify'),
			array('resource'=>'plugininterview', 'privilege'=>'admin'),
			array('resource'=>'plugininterview', 'privilege'=>'guest'),

		)
	);