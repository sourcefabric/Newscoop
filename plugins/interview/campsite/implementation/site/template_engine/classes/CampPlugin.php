<?php
/**
 * @package Campsite
 *
 * @author Sebastian Goebel <devel@yellowsunshine.de>
 * @copyright 2007 MDLF, Inc.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @version $Revision$
 * @link http://www.campware.org
 */


/**
 * Class CampPlugin
 */

class CampPlugin {
    private static $m_userDefaultConfig = array(
        'plugin_interview_notify'=>'N',
        'plugin_interview_admin'=>'N',
        'plugin_interview_moderator'=>'N',
        'plugin_interview_guest'=>'N',
    );
        
    private static $m_no_menu_scripts = array(
    	'/interview/admin/edit.php',
    	'/interview/admin/edit_item.php',
    	'/interview/moderator/edit.php',
    	'/interview/moderator/edit_item.php',
    	'/interview/guest/edit.php',
    	'/interview/guest/edit_item.php',
    	'/interview/admin/invitation.php'
	);
        
    static public function initPlugins4TemplateEngine()
    {
        User::$m_defaultConfig += self::$m_userDefaultConfig;
        
        $context = CampTemplate::singleton()->context();
        
        // Todo: below some hacked code to init Interview, have to be generic for all plugins   
        		   
        $context->registerObjectType(array('interview' => array('class' => 'Interview')));
        $context->registerListObject(array('interviews' => array('class' => 'Interviews', 'list' => 'interviews')));
        
        $context->registerObjectType(array('interviewitem' => array('class' => 'InterviewItem')));
        $context->registerListObject(array('interviewitems' => array('class' => 'InterviewItems', 'list' => 'interviewitems')));
           
        $interview_id = Input::Get('f_interview_id', 'int');     
        $interviewitem_id = Input::Get('f_interviewitem_id', 'int');
        
        $context->interviewitem = new MetaInterviewItem($interviewitem_id, $interview_id);
        
        if ($context->interviewitem->defined) {
            $context->interview = new MetaInterview($context->interviewitem->interview_id);       
        } else {
            $context->interview = new MetaInterview($interview_id);  
        }
    }
    
    static public function initPlugins4Admin()
    {
        global $no_menu_scripts;
        $no_menu_scripts = array_merge($no_menu_scripts, self::$m_no_menu_scripts);
        
        User::$m_defaultConfig += self::$m_userDefaultConfig;
    }     
}

?>
