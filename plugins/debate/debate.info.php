<?php
use Newscoop\Entity\Acl\Rule;
$info = array(
    'name' => 'debate',
    'version' => '0.0.1',
    'label' => 'Debate',
    'description' => 'This plugin provides functionality to perform debates.',
    'menu' => array(
        'name' => 'debate',
        'label' => 'Debates',
        'icon' => 'css/debate.png',
        'permission' => 'plugin_debate_admin',
        'path' => "debate/index.php",
    ),
    'userDefaultConfig' => array(
        'plugin_debate' => 'N',
    ),
    'permissions' => array(
    /**
     * Do not remove this comment: it is needed for the localizer
     * getGS('User may manage Debates');
     *
     */
    	'plugin_debate_admin' => 'User may manage Debates'
    ),
    'no_menu_scripts' => array(
        '/debate/assign_popup.php',
        '/debate/files/popup.php',
        '/debate/files/do_add.php',
        '/debate/files/do_delete.php'
    ),
    'template_engine' => array(
        'objecttypes' => array(
            array('debate' => array('class' => 'Debate')),
            array('debateanswer' => array('class' => 'DebateAnswer')),
            array('debatedays' => array('class' => 'DebateDays') ),
            array('debatevotes' => array('class' => 'DebateVotes') ),
            array('debateanswerattachment' => array('class' => 'DebateAnswerAttachment')),
            array('debatejustvoted' => array('class' => 'DebateAnswer'))
        ),
        'listobjects' => array(
            array('debate' => array('class' => 'Debate', 'list' => 'debate', 'url_id'=>'dbs')),
            array('debateanswers' => array('class' => 'DebateAnswers', 'list' => 'debateanswers', 'url_id'=>'dbt_ans')),
            array('debatedays' => array('class' => 'DebateDays', 'list' => 'debatedays', 'url_id'=>'dbt_dy')),
            array('debatevotes' => array('class' => 'DebateVotes', 'list' => 'debatevotes', 'url_id'=>'dbt_vt')),
            array('debateanswerattachments' => array('class' => 'DebateAnswerAttachments', 'list' => 'attachments', 'url_id'=>'pl_ans_att'))
        ),
        'init' => 'plugin_debate_init'
    ),
    'localizer' => array(
        'id' => 'plugin_debate',
        'path' => '/plugins/debate/admin-files/debate/*/*',
        'screen_name' => 'Debate'
    ),
    'install' => 'plugin_debate_install',
    'enable' => 'plugin_debate_install',
    'update' => 'plugin_debate_update',
    'disable' => '',
    'uninstall' => 'plugin_debate_uninstall'
);

if (!defined('PLUGIN_DEBATE_FUNCTIONS')) {
    define('PLUGIN_DEBATE_FUNCTIONS', true);

    function plugin_debate_install()
    {
        global $LiveUserAdmin;

        $LiveUserAdmin->addRight(array('area_id' => 0, 'right_define_name' => 'plugin_debate_admin', 'has_implied' => 1));

        require_once($GLOBALS['g_campsiteDir'].'/install/classes/CampInstallationBase.php');
        $GLOBALS['g_db'] = $GLOBALS['g_ado_db'];

        $errors = CampInstallationBaseHelper::ImportDB(CS_PATH_PLUGINS.DIR_SEP.'debate/install/sql/plugin_debate.sql', $error_queries);
        unset($GLOBALS['g_db']);

        global $g_ado_db;
        $res = $g_ado_db->execute("SELECT * FROM `liveuser_groups` WHERE `group_define_name` = 'Administrator'");
        $row = $res->FetchRow();
        if ($row) {
            $g_ado_db->execute("
                INSERT IGNORE INTO `acl_rule`(`action`, `resource`, `role_id`, `type`)
                VALUES('admin', 'plugin-debate', '{$row['role_id']}', 'allow')
            ");
        }
    }

    function plugin_debate_uninstall()
    {
        global $LiveUserAdmin, $g_ado_db;

        foreach (array('plugin_debate') as $right_def_name) {
            $filter = array(
                "fields" => array("right_id"),
                "filters" => array("right_define_name" => $right_def_name)
            );
            $rights = $LiveUserAdmin->getRights($filter);
            if(!empty($rights)) {
                $LiveUserAdmin->removeRight(array('right_id' => $rights[0]['right_id']));
            }
        }

        $g_ado_db->execute('DROP TABLE plugin_debate');
        $g_ado_db->execute('DROP TABLE plugin_debate_answer');
        $g_ado_db->execute('DROP TABLE plugin_debate_article');
        $g_ado_db->execute('DROP TABLE plugin_debate_issue');
        $g_ado_db->execute('DROP TABLE plugin_debate_publication');
        $g_ado_db->execute('DROP TABLE plugin_debate_section');
        $g_ado_db->execute('DROP TABLE plugin_debateanswer_attachment');
    }

    /**
     * @param CampContext $p_context
     */
    function plugin_debate_init(&$p_context)
    {
        $debate_nr = Input::Get("f_debate_nr", "int");
        $debate_language_id = Input::Get("f_debate_language_id" ,"int");
        $p_context->debate = new MetaDebate($debate_language_id, $debate_nr, $p_context->user->identifier);
        $url = $p_context->url;
        /* @var $url MetaURL */

        //if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
        //    $p_context->url->set_parameter('f_debate_ajax_request', 1);
        //}

        // reset the context urlparameters
        foreach (array('f_debate', 'f_debate_nr', 'f_debate_language_id', 'f_debate_ajax_request') as $param)
        {
            $p_context->url->reset_parameter($param);
            $p_context->default_url->reset_parameter($param);
        }
    }

    function plugin_debate_addPermissions()
    {
        $Admin = new UserType(1);
        $ChiefEditor = new UserType(2);
        $Editor = new UserType(3);

        $Admin->setPermission('plugin_debate_admin', true);
        $ChiefEditor->setPermission('plugin_debate_admin', true);
        $Editor->setPermission('plugin_debate_admin', true);
    }

    function plugin_debate_update()
    {
        require_once $GLOBALS['g_campsiteDir'] . '/install/classes/CampInstallationBase.php';
        $GLOBALS['g_db'] = $GLOBALS['g_ado_db'];

        $errors = CampInstallationBaseHelper::ImportDB(CS_PATH_PLUGINS.DIR_SEP.'debate'.DIR_SEP.'install'.DIR_SEP.'sql'.DIR_SEP.'update.sql', $error_queries);

        unset($GLOBALS['g_db']);
    }
}
?>
