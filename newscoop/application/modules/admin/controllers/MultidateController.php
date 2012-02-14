<?php

use Newscoop\ArticleDatetime;
use Doctrine\Common\Util\Debug;
//use Newscoop\Service\IThemeManagementService;
//use Newscoop\Service\IOutputService;
//use Newscoop\Service\ILanguageService;
//use Newscoop\Service\ISyncResourceService;
//use Newscoop\Service\IPublicationService;
//use Newscoop\Service\IThemeService;
//use Newscoop\Service\IOutputSettingIssueService;
//use Newscoop\Service\IOutputSettingSectionService;
//use Newscoop\Service\IIssueService;
//use Newscoop\Service\ISectionService;
//use Newscoop\Service\ITemplateSearchService;
//use Newscoop\Entity\Publication;
//use Newscoop\Entity\Theme;
//use Newscoop\Entity\Resource;

/**
 * @Acl(resource="article", action="edit")
 */
class Admin_MultidateController extends Zend_Controller_Action
{

    /** @var Newscoop\Services\Resource\ResourceId */
//    private $resourceId = NULL;
    /** @var Newscoop\Service\IThemeService */
//    private $themeService = NULL;
    /** @var Newscoop\Service\IThemeManagementService */
//    private $themeManagementService = NULL;
    /** @var Newscoop\Service\IPublicationService */
//    private $publicationService = NULL;
    /** @var Newscoop\Service\ILanguageService */
//    private $languageService = NULL;
    /** @var Newscoop\Service\IIssueService */
//    private $issueService = NULL;
    /** @var Newscoop\Service\ISectionService */
//    private $sectionService = NULL;
    /** @var Newscoop\Service\IOutputService */
//    private $outputService = NULL;
    /** @var Newscoop\Service\IOutputSettingSectionService */
//    private $outputSettingSectionService = NULL;
    /** @var Newscoop\Service\IOutputSettingIssueService */
//    private $outputSettingIssueService = NULL;
    /** @var Newscoop\Service\ITemplateSearchService */
//    private $templateSearchService = NULL;
    /** @var Newscoop\Service\ISyncResourceService */
//    private $syncResourceService = NULL;
    
    /** @var variable set to a timestamp with 0 h and 0 m */
    public $tz;
    /** @var variable set to a timestamp with a distant future date */
    public $distant;

    public function init()
    {
        $this->tz = mktime(0, 0, 0, 1, 1, 98);
        $current_year = date('Y');
        $this->distant = mktime(0, 0, 0, 12, 31, $current_year + 10); // it must be at most 2037, on 32bit systems
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
//return false;

    	//if ( $this->getTime( is_null($date->getStartTime()) ? $this->tz : $date->getStartTime()->getTimestamp() ) == "00:00" && $this->getTime($date->endTime->getTimestamp()) == "23:59" ) {
        if ( $this->getTime( is_null($date->getStartTime()) ? $this->tz : $date->getStartTime()->getTimestamp() ) == '00:00' && $date->endTime === null ) {
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
    	$multidateField = $this->_request->getParam('multidatefield');
    	$eventComment = $this->_request->getParam('event-comment');
    	
        $timeSet = array();
    	if ($date_type == 'specific') {
    		//single date
    		$startDate = $this->_request->getParam('start-date-specific');
    		$startTime = $this->_request->getParam('start-time-specific');
    		$endTime = $this->_request->getParam('end-time-specific');
    		

            $type = $this->_request->getParam('specific-radio');
            switch($type) {
                case 'start-only':
                    $timeSet = array(
                        //"$startDate" => array( "$startTime" => "23:59" )
                        //$startDate => array( $startTime => null )
                        'start_date' => $startDate,
                        'end_date' => $startDate,
                        'start_time' => $startTime,
                    );
                break;
                case 'start-and-end':
                    $timeSet = array(
                        //"$startDate" => array("$startTime" => "$endTime")
                        'start_date' => $startDate,
                        'end_date' => $startDate,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                    );
                    break;
            case 'all-day':
                    $timeSet = array(
                        //"$startDate" => array( "00:00" => "23:59" )
                        //$startDate => array( '00:00' => null )
                        'start_date' => $startDate,
                        'end_date' => $startDate,
                    );
                    break;
    		}
    		
            $timeSet = new ArticleDatetime($timeSet);

    		if ( $multidateId > 0) {
//echo json_encode(array('code' => 200, 'content' => 'www1112222wwwwwwwwwwwwwwwwww'));
//die();
                $repo->update( $multidateId, $timeSet, null, $multidateField, null, array('eventComment' => $eventComment) );
    		} else {
    			//add
//echo json_encode(array('code' => 200, 'timeSet' => $timeSet, 'articleId' => $articleId, 'multidateField' => $multidateField));
//die();
                $repo->add($timeSet, $articleId, $multidateField, null, false, array('eventComment' => $eventComment) );
    		}
    		
    		
    	} else {
    		
    		$startDate = $this->_request->getParam('start-date-daterange');
    		if ($this->_request->getParam('cycle-ends') == 'never') {
    			//$endDate = '2130-12-31';
    			$endDate = null;
    		} else {
    			$endDate = $this->_request->getParam('end-date-daterange');
    		}
    		if ($this->_request->getParam('daterange-all-day') == 1) {
    			$startTime = '00:00';
    			//$endTime = "23:59";
    			$endTime = null;
    		} else {
    			$startTime = $this->_request->getParam('start-time-daterange');
            	$endTime = $this->_request->getParam('end-time-daterange');	
    		}
    		$recurring = $this->_request->getParam('repeats-cycle');
            //$timeSet = array("$startDate $startTime" => "$endDate $endTime - $recurring");
            $timeSet = array(
                //"$startDate" => array("$startTime" => "$endTime")
                'start_date' => $startDate,
                'end_date' => $endDate,
                'start_time' => $startTime,
                'end_time' => $endTime,
                'recurring' => $recurring,
            );
            $timeSet = new ArticleDatetime($timeSet);

            if ( $multidateId > 0) {
                $repo->update( $multidateId, $timeSet, null, $multidateField, null, array('eventComment' => $eventComment) );
            } else {
                $repo->add($timeSet, $articleId, $multidateField, null, false, array('eventComment' => $eventComment) );
            }
    	}
        echo json_encode(array('code' => 200));
    	die();
    }
    
      
    
    public function geteventAction() 
    {
    	$articleDateTimeId = $this->_request->getParam('id');
//var_dump($articleDateTimeId);
        $repo = $this->_helper->entity->getRepository('Newscoop\Entity\ArticleDatetime');
        $jsEvent = array();
        $event = $repo->findDates((object) array('id' => "$articleDateTimeId"));
        if (is_array($event) && isset($event[0]) && (!empty($event[0]))) {
//var_dump($event);
//die();
        	$date = $event[0];
//var_dump($date);
//die();
        	$jsEvent['id'] = $date->id;
        	$jsEvent['startDate'] = $this->getDate($date->getStartDate()->getTimestamp());
        	$jsEvent['startTime'] = $this->getTime(is_null($date->getStartTime()) ? $this->tz : $date->getStartTime()->getTimestamp());
        	
	        $endDate = $date->getEndDate();
	        if ( empty($endDate)) {
	        	//$jsEvent['endDate'] = $this->getDate($date->getStartDate()->getTimestamp());
	        	$jsEvent['endDate'] = null;
	        } else {
	        	$jsEvent['endDate'] = $this->getDate($date->getEndDate()->getTimestamp());
	        }
	        $jsEvent['endTime'] = $this->getTime(is_null($date->getEndTime()) ? $this->tz : $date->getEndTime()->getTimestamp());
	        $jsEvent['allDay'] = $this->isAllDay($date);
	        $jsEvent['isRecurring'] = $date->getRecurring();
        	//if ($jsEvent['endDate'] == '2130-12-31') {}
        	if ($jsEvent['endDate'] === null) {
	        	$jsEvent['neverEnds'] = 1;
	        } else {
	        	$jsEvent['neverEnds'] = 0;
	        }

            $jsEvent['field_name'] = $date->getFieldName();
            $jsEvent['event_comment'] = $date->getEventComment();
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
//echo '[]';
//die();
    	
    	//is_null($date->getStartTime()) ? $this->tz : $date->getStartTime()->getTimestamp();

        require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
        require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleTypeField.php');
        $field_background_colors = array();

        $dark_blues = array('#4040ff', '#8040ff');
        $yellow = '#ffff40';

    	
    	$articleId = $this->_request->getParam('articleId');
    	$languageId = $this->_request->getParam('languageId');

        $article_obj = new \Article($languageId, $articleId);
        $article_type = $article_obj->getType();

        $repo = $this->_helper->entity->getRepository('Newscoop\Entity\ArticleDatetime');
        $return = array();
        $dates = $repo->findDates((object) array('articleId' => "$articleId"));
        foreach( $dates as $date) {

            $recurring = $date->getRecurring();

            $event_comment = $date->getEventComment();

            $itemField = $date->getFieldName();
            $itemColor = '#';
            if (array_key_exists($itemField, $field_background_colors)) {
                $itemColor = $field_background_colors[$itemField];
            }
            else {
                $field_obj = new \ArticleTypeField($article_type, $itemField);
                $itemColor = $field_obj->getColor();
                $field_background_colors[$itemField] = $itemColor;
            }

        	//if (strlen($recurring) > 1 && $recurring != 'daily') {}
        	if (strlen($recurring) > 1) {
        		//daterange
        		$start = strtotime( $this->getDate($date->getStartDate()->getTimestamp()).' '.$this->getTime(is_null($date->getStartTime()) ? $this->tz : $date->getStartTime()->getTimestamp()) );
        		$end = strtotime( $this->getDate(is_null($date->getEndDate()) ? ($this->distant) : $date->getEndDate()->getTimestamp()).' '.$this->getTime(is_null($date->getEndTime()) ? ($this->tz + 86399) : $date->getEndTime()->getTimestamp()) );
        		$itemStart = $start;
        		$itemEnd = strtotime( $this->getDate($date->getStartDate()->getTimestamp()).' '.$this->getTime(is_null($date->getEndTime()) ? ($this->tz + 86399) : $date->getEndTime()->getTimestamp()) );
        		
                $step = "+1 day";
        		switch($recurring) {
        			case 'weekly':
        				$step = "+1 week";
        				break;
        			case 'monthly':
        				$step = "+1 month";
        				break;
        		}

                while($itemStart <= $end) {
                    $calDate = array();
                    $calDate['id'] = $date->id;
                    //$calDate['title'] = 'Event ';
                    $calDate['title'] = $itemField;
                    $calDate['start'] = $itemStart;
                    $calDate['end'] = $itemEnd;
                    $calDate['allDay'] = $this->isAllDay($date);
                    $calDate['field_name'] = $itemField;
                    $calDate['backgroundColor'] = $itemColor;
                    $calDate['textColor'] = '#000000';
                    $calDate['event_comment'] = $event_comment;
                    $return[] = $calDate;

                    if ('+1 month' == $step) {
                        $curr_start_year = date('Y', $itemStart);
                        $curr_start_month = date('n', $itemStart);
                        $curr_start_day = date('j', $itemStart);
                        while (true) {
                            $curr_start_month += 1;
                            if (13 == $curr_start_month) {
                                $curr_start_month = 1;
                                $curr_start_year += 1;
                            }
                            if (checkdate($curr_start_month, $curr_start_day, $curr_start_year)) {
                                $itemStart = mktime(date('G', $itemStart), 0 + ltrim(date('i', $itemStart), '0'), 0, $curr_start_month, $curr_start_day, $curr_start_year);
                                $itemEnd = mktime(date('G', $itemEnd), 0 + ltrim(date('i', $itemEnd), '0'), 0, $curr_start_month, $curr_start_day, $curr_start_year);
                                break;
                            }
                        }
                    }
                    else {
                        $itemStart = strtotime($step, $itemStart);
                        $itemEnd = strtotime($step, $itemEnd);
                    }
                }

            } else {
        		//specific
        		$calDate = array();
	        	$calDate['id'] = $date->id;
	        	//$calDate['title'] = 'Event ';
                $calDate['title'] = $itemField;
	        	$calDate['start'] = strtotime( $this->getDate($date->getStartDate()->getTimestamp()).' '.$this->getTime( is_null($date->getStartTime()) ? $this->tz : $date->getStartTime()->getTimestamp() ));
	        	//$calDate['start'] = strtotime( $this->getDate($date->getStartDate()->getTimestamp()).' ');
	        	$endDate = $date->getEndDate();
                // TODO: at this moment, specific dates without end dates are taken as single-date dates, even though they should be taken as never-ending continuous events
	        	if ( empty($endDate)) {
	        		$calDate['end'] = strtotime( $this->getDate($date->getStartDate()->getTimestamp()).' '.$this->getTime(is_null($date->getEndTime()) ? $this->tz : $date->getEndTime()->getTimestamp()) );
	        		//$calDate['end'] = strtotime( $this->getDate($date->getStartDate()->getTimestamp()).' ' );
	        	} else {
	        		$calDate['end'] = strtotime( $this->getDate($date->getEndDate()->getTimestamp()).' '.$this->getTime(is_null($date->getEndTime()) ? $this->tz : $date->getEndTime()->getTimestamp()) );	
	        		//$calDate['end'] = strtotime( $this->getDate($date->getEndDate()->getTimestamp()).' ' );	
	        	}
                $calDate['allDay'] = $this->isAllDay($date);
                //$calDate['allDay'] = false;
                $calDate['field_name'] = $itemField;
                $calDate['backgroundColor'] = $itemColor;
                $calDate['textColor'] = '#000000';
                if (in_array($itemColor, $dark_blues)) {
                    $calDate['textColor'] = $yellow;
                }
                $calDate['event_comment'] = $event_comment;
                //$calDate['className'] = 'event_type_' . substr($itemField, 0);
                $return[] = $calDate;
            }
        }

        $res = usort($return, 'self::EventOrder');
        echo json_encode($return);
        die();
    }

    public static function EventOrder($a, $b) {
        if (isset($a['start']) && isset($b['start'])) {
            if ($a['start'] > $b['start']) {
                return 1;
            }
            if ($a['start'] < $b['start']) {
                return -1;
            }
        }

        if (isset($a['title']) && isset($b['title'])) {
            if ($a['title'] > $b['title']) {
                return 1;
            }
            if ($a['title'] < $b['title']) {
                return -1;
            }
        }

        return 0;
    }

/*
    public function setfieldcolorAction () {

        require_once($GLOBALS['g_campsiteDir'].DIRECTORY_SEPARATOR.'classes'.DIRECTORY_SEPARATOR.'ArticleTypeField.php');

        $article_type = $this->_request->getParam('f_article_type');
        $field_name = $this->_request->getParam('f_field_name');
        $color_value = $this->_request->getParam('f_color_value');

        $atf = new \ArticleTypeField($article_type, $field_name);
        $atf->setColor($color_value);

        //ArticleType::setFieldColor($article_type, $field_name, $color_value);
        echo "saved";
        die();
    }
*/
}

