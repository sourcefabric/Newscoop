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
        
        // Todo: below some hacked code to init Poll, have to be generic for all plugins
        
        $interview_id = Input::Get('interview_id', 'int');      
        		   
        $context->registerObjectType(array('interview' => 'Interview'));
        $context->registerListObject(array('interviews' => array('class' => 'Interviews', 'list' => 'interviews')));
        
        $context->registerObjectType(array('interviewitem' => 'InterviewItem'));
        $context->registerListObject(array('interviewitems' => array('class' => 'InterviewItems', 'list' => 'interviewitems')));
        
        $context->interview = new MetaInterview($interview_id);     
    }  
}

?>
