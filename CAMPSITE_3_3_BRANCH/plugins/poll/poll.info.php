<?php
$info = array( 
    'name' => 'poll',
    'version' => '3.3.x-0.2.1',
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
        'plugin_poll' => 'User may manage Polls',
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
    'update' => '',
    'disable' => '',
    'uninstall' => 'plugin_poll_uninstall'
);

if (!defined('PLUGIN_POLL_FUNCTIONS')) {
    define('PLUGIN_POLL_FUNCTIONS', true);

    function plugin_poll_install()
    {
        global $LiveUserAdmin;
        
        $LiveUserAdmin->addRight(array('area_id' => 0, 'right_define_name' => 'plugin_poll', 'has_implied' => 1));  
        
        require_once($GLOBALS['g_campsiteDir'].'/install/classes/CampInstallationBase.php');
        $GLOBALS['g_db'] = $GLOBALS['g_ado_db'];
        
        $errors = CampInstallationBaseHelper::ImportDB(CS_PATH_PLUGINS.DIR_SEP.'poll/install/sql/plugin_poll.sql', $error_queries);
        
        unset($GLOBALS['g_db']);       
    }
    
    function plugin_poll_uninstall()
    {
        global $LiveUserAdmin, $g_ado_db;
        
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
        
        $g_ado_db->execute('DROP TABLE plugin_poll');
        $g_ado_db->execute('DROP TABLE plugin_poll_answer');
        $g_ado_db->execute('DROP TABLE plugin_poll_article');
        $g_ado_db->execute('DROP TABLE plugin_poll_issue');
        $g_ado_db->execute('DROP TABLE plugin_poll_publication');
        $g_ado_db->execute('DROP TABLE plugin_poll_section');
        $g_ado_db->execute('DROP TABLE plugin_pollanswer_attachment');
        
        
        system('rm -rf '.CS_PATH_PLUGINS.DIR_SEP.'/poll');    
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
}
?>