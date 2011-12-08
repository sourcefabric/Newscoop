<?php

use Newscoop\ArticleDatetime;
use Doctrine\Common\Util\Debug;
use Newscoop\Service\IThemeManagementService;
use Newscoop\Service\IOutputService;
use Newscoop\Service\ILanguageService;
use Newscoop\Service\ISyncResourceService;
use Newscoop\Service\IPublicationService;
use Newscoop\Service\IThemeService;
use Newscoop\Service\IOutputSettingIssueService;
use Newscoop\Service\IOutputSettingSectionService;
use Newscoop\Service\IIssueService;
use Newscoop\Service\ISectionService;
use Newscoop\Service\ITemplateSearchService;
use Newscoop\Entity\Publication;
use Newscoop\Entity\Theme;
use Newscoop\Entity\Resource;


/**
 * @Acl(resource="theme", action="manage")
 */
class Admin_MultidateController extends Zend_Controller_Action
{

    /** @var Newscoop\Services\Resource\ResourceId */
    private $resourceId = NULL;
    /** @var Newscoop\Service\IThemeService */
    private $themeService = NULL;
    /** @var Newscoop\Service\IThemeManagementService */
    private $themeManagementService = NULL;
    /** @var Newscoop\Service\IPublicationService */
    private $publicationService = NULL;
    /** @var Newscoop\Service\ILanguageService */
    private $languageService = NULL;
    /** @var Newscoop\Service\IIssueService */
    private $issueService = NULL;
    /** @var Newscoop\Service\ISectionService */
    private $sectionService = NULL;
    /** @var Newscoop\Service\IOutputService */
    private $outputService = NULL;
    /** @var Newscoop\Service\IOutputSettingSectionService */
    private $outputSettingSectionService = NULL;
    /** @var Newscoop\Service\IOutputSettingIssueService */
    private $outputSettingIssueService = NULL;
    /** @var Newscoop\Service\ITemplateSearchService */
    private $templateSearchService = NULL;
    /** @var Newscoop\Service\ISyncResourceService */
    private $syncResourceService = NULL;
    
    /** @var variable set to a timestamp with 0 h and 0 m */
    public $tz;

    public function init()
    {
        $this->tz = mktime(0, 0, 0, 1, 1, 98);
    }

    /* --------------------------------------------------------------- */
    
    public function getDate($full)
    {
    	return date("Y-m-d", $full);
    }
    
    public function getTime($full)
    {
    	return date("H:i",$full);
    }

    public function isAllDay($date)
    {
    	if ( $this->getTime( is_null($date->getStartTime()) ? $this->tz : $date->getStartTime()->getTimestamp() ) == "00:00" && $this->getTime($date->endTime->getTimestamp()) == "23:59" ) {
    		return true;
    	} else {
    		return false;
    	}
    }

    
    public function addAction() 
    {	
    	$date_type = $this->_request->getParam('date-type');
    	$articleId = $this->_request->getParam('article-number');
    	$repo = $this->_helper->entity->getRepository('Newscoop\Entity\ArticleDatetime');
    	$multidateId = $this->_request->getParam('multidateId');
    	
    	//print_r($_REQUEST);
    	
    	if ($date_type == 'specific') {
    		//single date
    		$startDate = $this->_request->getParam('start-date-specific');
    		$startTime = $this->_request->getParam('start-time-specific');
    		$endTime = $this->_request->getParam('end-time-specific');

    		$type = $this->_request->getParam('specific-radio');
    		switch($type) {
    			case 'start-only':
    				$timeSet = array(
    				    "$startDate" => array( "$startTime" => "23:59" )
    				);
    				break;
    			case 'start-and-end':
    				$timeSet = array(
                        "$startDate" => array("$startTime" => "$endTime")
                    );
                    break;
    			case 'all-day':
    				$timeSet = array(
                        "$startDate" => array( "00:00" => "23:59" )
                    );
                    break;
    		}
    		
    		if ( $multidateId > 0) {
    			$repo->update( $multidateId, $timeSet);
    		} else {
    			//add
    			$repo->add($timeSet, $articleId, 'schedule');	
    		}
    		
    		
    	} else {
    		
    		$startDate = $this->_request->getParam('start-date-daterange');
    		if ($this->_request->getParam('cycle-ends') == 'never') {
    			$endDate = "2030-12-31";
    		} else {
    			$endDate = $this->_request->getParam('end-date-daterange');
    		}
    		if ($this->_request->getParam('daterange-all-day') == 1) {
    			$startTime = "00:00";
    			$endTime = "23:59";
    		} else {
    			$startTime = $this->_request->getParam('start-time-daterange');
            	$endTime = $this->_request->getParam('end-time-daterange');	
    		}
    		$recurring = $this->_request->getParam('repeats-cycle');
            $timeSet = array("$startDate $startTime" => "$endDate $endTime - $recurring");
            
            if ( $multidateId > 0) {
            	$repo->update( $multidateId, $timeSet );
            } else {
            	$repo->add($timeSet, $articleId, 'schedule');	
            }
    	}
        echo json_encode(array('code' => 200));
    	die();
    }
    
      
    
    public function geteventAction() 
    {
    	$articleDateTimeId = $this->_request->getParam('id');
        $repo = $this->_helper->entity->getRepository('Newscoop\Entity\ArticleDatetime');
        $jsEvent = array();
        $event = $repo->findDates((object) array('id' => "$articleDateTimeId"));
        if (is_array($event)) {
        	$date = $event[0];
        	$jsEvent['id'] = $date->id;
        	$jsEvent['startDate'] = $this->getDate($date->getStartDate()->getTimestamp());
        	$jsEvent['startTime'] = $this->getTime(is_null($date->getStartTime()) ? $this->tz : $date->getStartTime()->getTimestamp());
        	
	        $endDate = $date->getEndDate();
	        if ( empty($endDate)) {
	        	$jsEvent['endDate'] = $this->getDate($date->getStartDate()->getTimestamp());
	        } else {
	        	$jsEvent['endDate'] = $this->getDate($date->getEndDate()->getTimestamp());
	        }
	        $jsEvent['endTime'] = $this->getTime(is_null($date->getEndTime()) ? $this->tz : $date->getEndTime()->getTimestamp());
	        $jsEvent['allDay'] = $this->isAllDay($date);
	        $jsEvent['isRecurring'] = $date->getRecurring();
        	if ($jsEvent['endDate'] == "2030-12-31") {
	        	$jsEvent['neverEnds'] = 1;
	        } else {
	        	$jsEvent['neverEnds'] = 0;
	        }
        }
        echo json_encode($jsEvent);
        die();
    }
    
    public function removeAction() 
    {
    	
    	
    	
    	$articleDateTimeId = $this->_request->getParam('id');
    	//echo "|".$articleDateTimeId."|";
    	$repo = $this->_helper->entity->getRepository('Newscoop\Entity\ArticleDatetime');
    	
    	$repo->deleteById($articleDateTimeId);
    	
    	echo json_encode(array('code' => 200));
        die();
    }


    public function getdatesAction() 
    {
    	
    	//is_null($date->getStartTime()) ? $this->tz : $date->getStartTime()->getTimestamp();
    	
    	$articleId = $this->_request->getParam('articleId');
        $repo = $this->_helper->entity->getRepository('Newscoop\Entity\ArticleDatetime');
        $return = array();
        $dates = $repo->findDates((object) array('articleId' => "$articleId"));
        foreach( $dates as $date) {
        	
        	$recurring = $date->getRecurring();
        	if (strlen($recurring) > 1 && $recurring != 'daily') {
        		//daterange
        		$start = strtotime( $this->getDate($date->getStartDate()->getTimestamp()).' '.$this->getTime(is_null($date->getStartTime()) ? $this->tz : $date->getStartTime()->getTimestamp()) );
        		$end = strtotime( $this->getDate($date->getEndDate()->getTimestamp()).' '.$this->getTime(is_null($date->getEndTime()) ? $this->tz : $date->getEndTime()->getTimestamp()) );
        		$itemStart = $start;
        		$itemEnd = strtotime( $this->getDate($date->getStartDate()->getTimestamp()).' '.$this->getTime(is_null($date->getEndTime()) ? $this->tz : $date->getEndTime()->getTimestamp()) );
        		
        		switch($recurring) {
        			case 'weekly':
        				$step = "+1 week";
        				break;
        			case 'monthly':
        				$step = "+1 month";
        				break;
        		}
        		
        		while($itemStart < $end) {
        			$calDate = array();
		        	$calDate['id'] = $date->id;
		        	$calDate['title'] = 'Event ';
		        	$calDate['start'] = $itemStart;
		        	$calDate['end'] = $itemEnd;
		        	$calDate['allDay'] = $this->isAllDay($date);
	        		$return[] = $calDate;
		        	
		        	$itemStart = strtotime($step, $itemStart);
		        	$itemEnd = strtotime($step, $itemEnd);
        		}
        		
        	} else {
        		//specific
        		$calDate = array();
	        	$calDate['id'] = $date->id;
	        	$calDate['title'] = 'Event ';
	        	$calDate['start'] = strtotime( $this->getDate($date->getStartDate()->getTimestamp()).' '.$this->getTime( is_null($date->getStartTime()) ? $this->tz : $date->getStartTime()->getTimestamp() ));
	        	$endDate = $date->getEndDate();
	        	if ( empty($endDate)) {
	        		$calDate['end'] = strtotime( $this->getDate($date->getStartDate()->getTimestamp()).' '.$this->getTime(is_null($date->getEndTime()) ? $this->tz : $date->getEndTime()->getTimestamp()) );
	        	} else {
	        		$calDate['end'] = strtotime( $this->getDate($date->getEndDate()->getTimestamp()).' '.$this->getTime(is_null($date->getEndTime()) ? $this->tz : $date->getEndTime()->getTimestamp()) );	
	        	}
	        	$calDate['allDay'] = $this->isAllDay($date);
	        	$return[] = $calDate;
        	}
        	
        }
        echo json_encode($return);
        die();
    }    
}

