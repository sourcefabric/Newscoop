<?php
$info = array( 
    'name' => 'interview',
    'version' => '0.1',
    'label' => 'Interview',
    'description' => 'This plugin provides functionality to perform online interviews.',  
    'menu' => array(
        'name' => 'interview',
        'label' => 'Interview',
        'icon' => 'interview.png',
        'sub' => array(
            array(
                'permission' => 'plugin_interview_admin',
                'path' => "interview/admin/index.php",
                'label' => 'Administrate Interviews',
                'icon' => 'interview.png',
            ),
            array(
                'permission' => 'plugin_interview_moderator',
                'path' => "interview/moderator/index.php",
                'label' => 'Moderate Interviews',
                'icon' => 'interview.png',
            ),
            array(
                'permission' => 'plugin_interview_guest',
                'path' => "interview/guest/index.php",
                'label' => 'Interview Guest',
                'icon' => 'interview.png',
            ),
        ),
    ),
    'userDefaultConfig' => array(
        'plugin_manager' => 'N',
        'plugin_interview_notify'=>'N',
        'plugin_interview_admin'=>'N',
        'plugin_interview_moderator'=>'N',
        'plugin_interview_guest'=>'N',
    ),
    'permissions' => array(
        'plugin_interview_notify' => 'User recives notification about new interviews',
        'plugin_interview_admin' => 'User is interview admin',
        'plugin_interview_moderator' => 'User is interview moderator',
        'plugin_interview_guest' => 'User is interview guest'
    ),
    'no_menu_scripts' => array(
    	'/interview/admin/edit.php',
    	'/interview/admin/edit_item.php',
    	'/interview/moderator/edit.php',
    	'/interview/moderator/edit_item.php',
    	'/interview/guest/edit.php',
    	'/interview/guest/edit_item.php',
    	'/interview/admin/invitation.php'
    ),
    'template_engine' => array(
        'objecttypes' => array(
            array('interview' => array('class' => 'Interview')),
            array('interviewitem' => array('class' => 'InterviewItem')),
        ),
        'listobjects' => array(
            array('interviews' => array('class' => 'Interviews', 'list' => 'interviews')),
            array('interviewitems' => array('class' => 'InterviewItems', 'list' => 'interviewitems')),
        ),
        'init_eval_code' => ' $interview_id = Input::Get("f_interview_id", "int");
                    $interviewitem_id = Input::Get("f_interviewitem_id", "int");
            
                    $context->interviewitem = new MetaInterviewItem($interviewitem_id, $interview_id);
            
                    if ($context->interviewitem->defined) {
                        $context->interview = new MetaInterview($context->interviewitem->interview_id);
                    } else {
                        $context->interview = new MetaInterview($interview_id);
                    }'
    ),
    'enable' => 'plugin_interview_enable',
    'update' => '',
    'disable' => '',
);

function plugin_interview_enable()
{
    global $LiveUserAdmin;
    
    $LiveUserAdmin->addRight(array('area_id' => 0, 'right_define_name' => 'plugin_interview_notify', 'has_implied' => 1));
    $LiveUserAdmin->addRight(array('area_id' => 0, 'right_define_name' => 'plugin_interview_guest', 'has_implied' => 1));
    $LiveUserAdmin->addRight(array('area_id' => 0, 'right_define_name' => 'plugin_interview_moderator', 'has_implied' => 1));
    $LiveUserAdmin->addRight(array('area_id' => 0, 'right_define_name' => 'plugin_interview_admin', 'has_implied' => 1)); 
}
?>