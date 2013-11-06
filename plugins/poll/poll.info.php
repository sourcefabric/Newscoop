<?php

require_once $GLOBALS['g_campsiteDir'] . '/classes/LiveUserMock.php';

$info = array(
    'name' => 'poll',
    'version' => '0.3.1',
    'label' => 'Polls',
    'description' => 'This plugin provides functionality to perform polls (standard and advanced).',
    'menu' => array(
        'name' => 'poll',
        'label' => 'Polls',
        'icon' => 'css/poll.png',
        'permission' => 'plugin_poll',
        'path' => "poll/index.php",
    ),
    'userDefaultConfig' => array(
        'plugin_poll' => 'N',
    ),
    'permissions' => array(
    /**
     * Do not remove this comment: it is needed for the localizer
     * getGS('User may manage Polls');
     *
     */
        'plugin_poll' => 'User may manage Polls'
    ),
    'no_menu_scripts' => array(
        '/poll/assign_popup.php',
        '/poll/files/popup.php',
        '/poll/files/do_add.php',
        '/poll/files/do_delete.php'
    ),
    'template_engine' => array(
        'objecttypes' => array(
            array('poll' => array('class' => 'Poll')),
            array('pollanswer' => array('class' => 'PollAnswer')),
            array('pollanswerattachment' => array('class' => 'PollAnswerAttachment'))
        ),
        'listobjects' => array(
            array('polls' => array('class' => 'Polls', 'list' => 'polls', 'url_id'=>'pls')),
            array('pollanswers' => array('class' => 'PollAnswers', 'list' => 'pollanswers', 'url_id'=>'pl_ans')),
            array('pollanswerattachments' => array('class' => 'PollAnswerAttachments', 'list' => 'attachments', 'url_id'=>'pl_ans_att'))
        ),
        'init' => 'plugin_poll_init'
    ),
    'localizer' => array(
        'id' => 'plugin_poll',
        'path' => '/plugins/poll/admin-files/poll/*/*',
        'screen_name' => 'Polls'
    ),
    'install' => 'plugin_poll_install',
    'enable' => 'plugin_poll_install',
    'update' => 'plugin_poll_update',
    'disable' => '',
    'uninstall' => 'plugin_poll_uninstall'
);

if (!defined('PLUGIN_POLL_FUNCTIONS')) {
    define('PLUGIN_POLL_FUNCTIONS', true);

    function plugin_poll_install()
    {
        global $LiveUserAdmin;

        $LiveUserAdmin->addRight(array('area_id' => 0, 'right_define_name' => 'plugin_poll', 'has_implied' => 1));

        $container = \Zend_Registry::get('container');
        $databaseConnection = $container->get('database_connection');

        $installerDatabaseService = new \Newscoop\Installer\Services\DatabaseService($container->get('logger'));
        $installerDatabaseService->importDB(CS_PATH_PLUGINS.DIR_SEP.'poll/install/sql/plugin_poll.sql', $databaseConnection);
    }

    function plugin_poll_uninstall()
    {
        global $LiveUserAdmin;

        foreach (array('plugin_poll') as $right_def_name) {
            $filter = array(
                "fields" => array("right_id"),
                "filters" => array("right_define_name" => $right_def_name)
            );
            $rights = $LiveUserAdmin->getRights($filter);
            if(!empty($rights)) {
                $LiveUserAdmin->removeRight(array('right_id' => $rights[0]['right_id']));
            }
        }

        $container = \Zend_Registry::get('container');
        $databaseConnection = $container->get('database_connection');

        $databaseConnection->executeQuery('DROP TABLE plugin_poll');
        $databaseConnection->executeQuery('DROP TABLE plugin_poll_answer');
        $databaseConnection->executeQuery('DROP TABLE plugin_poll_article');
        $databaseConnection->executeQuery('DROP TABLE plugin_poll_issue');
        $databaseConnection->executeQuery('DROP TABLE plugin_poll_publication');
        $databaseConnection->executeQuery('DROP TABLE plugin_poll_section');
        $databaseConnection->executeQuery('DROP TABLE plugin_pollanswer_attachment');
    }

    function plugin_poll_init(&$p_context)
    {
        $poll_nr = Input::Get("f_poll_nr", "int");
        $poll_language_id = Input::Get("f_poll_language_id" ,"int");
        $p_context->poll = new MetaPoll($poll_language_id, $poll_nr);

        // reset the context urlparameters
        foreach (array( 'f_poll',
                        'f_poll_nr',
                        'f_poll_language_id',
                        'f_poll_ajax_request'
            ) as $param) {

            $p_context->url->reset_parameter($param);
            $p_context->default_url->reset_parameter($param);
        }
    }

    function plugin_poll_addPermissions()
    {
        $Admin = new UserType(1);
        $ChiefEditor = new UserType(2);
        $Editor = new UserType(3);

        $Admin->setPermission('plugin_poll', true);
        $ChiefEditor->setPermission('plugin_poll', true);
        $Editor->setPermission('plugin_poll', true);
    }

    function plugin_poll_update()
    {
        $container = \Zend_Registry::get('container');
        $databaseConnection = $container->get('database_connection');

        $installerDatabaseService = new \Newscoop\Installer\Services\DatabaseService($container->get('logger'));
        $installerDatabaseService->importDB(CS_PATH_PLUGINS.DIR_SEP.'poll'.DIR_SEP.'install'.DIR_SEP.'sql'.DIR_SEP.'update.sql', $databaseConnection);
    }
}
