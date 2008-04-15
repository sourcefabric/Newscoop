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
        Poll::registerVoting();
        
        $poll_nr = Input::Get('f_poll_nr', 'int');
        $poll_language_id = Input::Get('f_poll_language_id' ,'int');
        
        		   
        $context->registerObjectType(array('poll' => array('class' => 'Poll')));
        $context->registerListObject(array('polls' => array('class' => 'Polls', 'list' => 'polls')));
    
        $context->registerObjectType(array('pollanswer' => array('class' => 'PollAnswer')));
        $context->registerListObject(array('pollanswers' => array('class' => 'PollAnswers', 'list' => 'pollanswers'))); 
        
        $context->registerObjectType(array('pollanswerattachment' => array('class' => 'PollAnswerAttachment')));
        $context->registerListObject(array('pollanswerattachments' => array('class' => 'PollAnswerAttachments', 'list' => 'attachments')));

        
        $context->poll = new MetaPoll($poll_language_id, $poll_nr);
           
        // reset the context urlparameters
        $context->default_url->reset_parameter('f_poll_nr');
        $context->url->reset_parameter('f_poll_language_id');
       
    }  
}

?>
