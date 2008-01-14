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

class CampPlugin{
    static public function initPlugins()
    {
        $context = CampTemplate::singleton()->context();
        
        // Todo: below some hacked code to init Interview, have to be generic for all plugins   
        		   
        $context->registerObjectType(array('interview' => array('class' => 'Interview')));
        $context->registerListObject(array('interviews' => array('class' => 'Interviews', 'list' => 'interviews')));
        
        $context->registerObjectType(array('interviewitem' => array('class' => 'InterviewItem')));
        $context->registerListObject(array('interviewitems' => array('class' => 'InterviewItems', 'list' => 'interviewitems')));
        
        $interview_id = Input::Get('f_interview_id', 'int');
        $context->interview = new MetaInterview($interview_id);
        
        $interviewitem_id = Input::Get('f_interviewitem_id', 'int');
        $context->interviewitem = new MetaInterviewItem($interviewitem_id, $interview_id);
        
        if (Interview::IsInvitationTriggered()) {
            Interview::SendInvitation();   
        }
    }  
}

?>
