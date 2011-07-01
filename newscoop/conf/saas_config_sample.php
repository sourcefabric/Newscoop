<?php
/*
 * configuration file for the Saas feature
 * to disable the Saas feature just rename the file to something other then saas_config.php
 *
*/
    $this->saasConfig = array(
        'permissions' => array(
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

        /*plugin recaptcha*/
        'plugin_recaptcha_admin',

        /*plugin soundclound*/
        'plugin_soundcloud_preferences',
        'plugin_soundcloud_browser',
        'plugin_soundcloud_upload',
        'plugin_soundcloud_update',
        'plugin_soundcloud_delete',


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
        'DeletePub',
        'ManagePublicationSubscriptions',

        /*system preferences*/
        'ManageSystemPreferences'

    ),
    'privileges' =>
    array(
        array('resource' => 'staff', 'privilege' => 'add'),
        array('resource' => 'publication', 'privilege' => 'delete'),
        array('resource' => 'template', 'privilege' => 'delete'),
        array('resource' => 'template', 'privilege' => 'manage'),
        array('resource' => 'themes', 'privilege' => '*'),
        array('resource' => 'theme', 'privilege' => '*'),
        array('resource' => 'subscriber', 'privilege' => 'manage'),
        array('resource' => 'subscriber', 'privilege' => 'add'),
        array('resource' => 'country', 'privilege' => 'manage'),
        array('resource' => 'country', 'privilege' => 'delete'),
        array('resource' => 'localizer', 'privilege' => 'manage'),
        array('resource' => 'plugin', 'privilege' => 'manage'),
        array('resource' => 'plugin-blog', 'privilege' => 'admin'),
        array('resource' => 'plugin-blog', 'privilege' => 'moderator'),
        array('resource' => 'plugin-interview', 'privilege' => 'notify'),
        array('resource' => 'plugin-interview', 'privilege' => 'admin'),
        array('resource' => 'plugin-interview', 'privilege' => 'guest'),
        array('resource' => 'plugin-recaptcha', 'privilege' => 'admin'),

    )
);