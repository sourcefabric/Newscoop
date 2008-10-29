<?php
$info = array( 
    'name' => 'blog',
    'version' => '0.1',
    'label' => 'Blogs',
    'description' => 'This plugin provides blogs.',
    'menu' => array(
        'name' => 'blog',
        'label' => 'Blog',
        'icon' => '/css/blog.png',
        'sub' => array(
            array(
                'permission' => 'plugin_blog_admin',
                'path' => "blog/admin/index.php",
                'label' => 'Administrate Blogs',
                'icon' => 'css/blog.png',
            ),
            array(
                'permission' => 'plugin_blog_moderator',
                'path' => "blog/moderator/index.php",
                'label' => 'Moderate Blogs',
                'icon' => 'css/blog.png',
            ),
        ),
    ),
    'userDefaultConfig' => array(
        'plugin_blog' => 'N',
    ),
    'permissions' => array(
        'plugin_blog_admin' => 'User may manage Blogs',
    ),
    'template_engine' => array(
        'objecttypes' => array(
            array('blog' => array('class' => 'Blog')),
            array('blogentry' => array('class' => 'BlogEntry')),
            array('blogcomment' => array('class' => 'BlogComment'))
        ),
        'listobjects' => array(
            array('blogs' => array('class' => 'Blogs', 'list' => 'blogs')),
            array('blogentries' => array('class' => 'BlogEntries', 'list' => 'blogentries')),
            array('blogcomments' => array('class' => 'BlogComments', 'list' => 'blogcomments'))
        ),
        'init' => 'plugin_blog_init'
    ),
    'localizer' => array(
        'id' => 'plugin_blog',
        'path' => '/plugins/blog/admin-files/blog/',
        'screen_name' => 'Blogs'
    ),
    'install' => 'plugin_blog_install',
    'enable' => 'plugin_blog_install',
    'update' => '',
    'disable' => '',
    'uninstall' => 'plugin_blog_uninstall'
);

if (!defined('PLUGIN_BLOG_FUNCTIONS')) {
    define('PLUGIN_BLOG_FUNCTIONS', true);

    function plugin_blog_install()
    {
        global $LiveUserAdmin, $g_documentRoot;
        
        $LiveUserAdmin->addRight(array('area_id' => 0, 'right_define_name' => 'plugin_blog', 'has_implied' => 1));  
        
        require_once($g_documentRoot.'/install/classes/CampInstallationBase.php');
        $GLOBALS['g_db'] =& $GLOBALS['g_ado_db'];
        
        $errors = CampInstallationBaseHelper::ImportDB(CS_PATH_PLUGINS.DIR_SEP.'blog'.DIR_SEP.'install'.DIR_SEP.'sql'.DIR_SEP.'plugin_blog.sql', $error_queries);
        
        unset($GLOBALS['g_db']);       
    }
    
    function plugin_blog_uninstall()
    {
        global $LiveUserAdmin, $g_documentRoot, $g_ado_db;
        
        foreach (array('plugin_blog') as $right_def_name) {
            $filter = array(
                "fields" => array("right_id"),
                "filters" => array("right_define_name" => $right_def_name)
            );
            $rights = $LiveUserAdmin->getRights($filter);
            if(!empty($rights)) {
                $LiveUserAdmin->removeRight(array('right_id' => $rights[0]['right_id']));
            }
        }
        
        $g_ado_db->execute('DROP TABLE plugin_blog_blog');
        $g_ado_db->execute('DROP TABLE plugin_blog_entry');
        $g_ado_db->execute('DROP TABLE plugin_blog_comment');        
        
        system('rm -rf '.$g_documentRoot.DIR_SEP.PLUGINS_DIR.DIR_SEP.'blog');    
    }
    
    function plugin_blog_init(&$p_context)
    {      
        $blog_id = Input::Get("f_blog_id", "int");
        $p_context->blog = new MetaBlog($blog_id);
        
        $entry_id = Input::Get('f_blogentry_id', 'int');
        $p_context->blogentry = new MetaBlogEntry($entry_id);
        
        $comment_id = Input::Get('f_blogcomment_id', 'int');
        $p_context->blogcomment = new MetaBlogComment($comment_id);
        
        foreach (array('f_blog', 
                       'f_blog_action',
                       
                       'f_blog_id', 
                       'f_blog_title',
                       'f_blog_info',
                       'f_blog_request_text',
                       'f_blog_action',
                       'f_blogentry_action',
                       'f_blogcomment_action',
                       
                       'f_blogentry_id',
                       'f_blogentry_title',
                       'f_blogentry_content',
                       'f_blogentry_mood',
                       
                       'f_blogcomment_id',
                       'f_blogcomment_title',
                       'f_blogcomment_content',
                       'f_blogcomment_mood',
                       'f_preview_blogcomment',
                       'f_submit_blogcomment'
                   ) as $v) {
                       
            $p_context->url->reset_parameter($v);
            $p_context->default_url->reset_parameter($v);   
        }
    }
}
?>