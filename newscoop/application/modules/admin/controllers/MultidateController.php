<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Annotations\Acl;
use Newscoop\ArticleDatetime;

/**
 * @Acl(resource="article", action="edit")
 */
class Admin_MultidateController extends Zend_Controller_Action
{
    
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
        if ( ($this->getTime( is_null($date->getStartTime()) ? $this->tz : $date->getStartTime()->getTimestamp() ) == '00:00') && ($date->endTime === null) ) {
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
                        'start_date' => $startDate,
                        'end_date' => $startDate,
                        'start_time' => $startTime,
                    );
                break;
                case 'start-and-end':
                    $timeSet = array(
                        'start_date' => $startDate,
                        'end_date' => $startDate,
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                    );
                    break;
            case 'all-day':
                    $timeSet = array(
                        'start_date' => $startDate,
                        'end_date' => $startDate,
                    );
                    break;
    		}
    		
            $timeSet = new ArticleDatetime($timeSet);

    		if ( $multidateId > 0) {
                $repo->update( $multidateId, $timeSet, null, $multidateField, null, array('eventComment' => $eventComment) );
    		} else {
                $repo->add($timeSet, $articleId, $multidateField, null, false, array('eventComment' => $eventComment) );
    		}
    		
    		
    	} else {
    		
    		$startDate = $this->_request->getParam('start-date-daterange');
    		if ($this->_request->getParam('cycle-ends') == 'never') {
    			$endDate = null;
    		} else {
    			$endDate = $this->_request->getParam('end-date-daterange');
    		}
    		if ($this->_request->getParam('daterange-all-day') == 1) {
    			$startTime = '00:00';
    			$endTime = null;
    		} else {
    			$startTime = $this->_request->getParam('start-time-daterange');
            	$endTime = $this->_request->getParam('end-time-daterange');	
    		}
    		$recurring = $this->_request->getParam('repeats-cycle');
            if (($endDate !== null) && ('weekly' == $recurring)) {
                $start_week_day = date('w', date_timestamp_get(date_create($startDate)));
                $end_week_day = date('w', date_timestamp_get(date_create($endDate)));
                if ($start_week_day != $end_week_day) {
                    $days_sub = (7 + $end_week_day - $start_week_day) % 7;
                    $end_date_new = date_create($endDate);
                    $sub_interval = new \DateInterval('P'.$days_sub.'D');
                    $end_date_new->sub($sub_interval);
                    $endDate = date('Y-m-d', date_timestamp_get($end_date_new));
                }
            }
            if (($endDate !== null) && ('monthly' == $recurring)) {
                $start_month_day = date('j', date_timestamp_get(date_create($startDate)));
                $orig_end_date = date_create($endDate);
                $end_month_day = date('j', date_timestamp_get($orig_end_date));
                if ($start_month_day != $end_month_day) {
                    $end_month_month = date('n', date_timestamp_get($orig_end_date));
                    $end_month_year = date('Y', date_timestamp_get($orig_end_date));

                    if ($start_month_day < $end_month_day) {
                        $end_month_day = $start_month_day;
                    }
                    else {
                        $end_month_day = $start_month_day;
                        while (true) {
                            $end_month_month -= 1;
                            if (0 == $end_month_month) {
                                $end_month_month = 12;
                                $end_month_year -= 1;
                            }
                            if (checkdate($end_month_month, $end_month_day, $end_month_year)) {
                                break;
                            }
                        }
                    }
                    $new_end_date = mktime(0, 0, 0, $end_month_month, $end_month_day, $end_month_year);
                    $endDate = date('Y-m-d', $new_end_date);
                }
            }

            $timeSet = array(
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
        $repo = $this->_helper->entity->getRepository('Newscoop\Entity\ArticleDatetime');
        $jsEvent = array();
        $event = $repo->findDates((object) array('id' => "$articleDateTimeId"));
        if (is_array($event) && isset($event[0]) && (!empty($event[0]))) {
        	$date = $event[0];
        	$jsEvent['id'] = $date->id;
        	$jsEvent['startDate'] = $this->getDate($date->getStartDate()->getTimestamp());
        	$jsEvent['startTime'] = $this->getTime(is_null($date->getStartTime()) ? $this->tz : $date->getStartTime()->getTimestamp());
        	
	        $endDate = $date->getEndDate();
	        if ( empty($endDate)) {
	        	$jsEvent['endDate'] = null;
	        } else {
	        	$jsEvent['endDate'] = $this->getDate($date->getEndDate()->getTimestamp());
	        }
	        $jsEvent['endTime'] = $this->getTime(is_null($date->getEndTime()) ? $this->tz : $date->getEndTime()->getTimestamp());
	        $jsEvent['allDay'] = $this->isAllDay($date);
            $jsEvent['restOfDay'] = false;
	        $jsEvent['isRecurring'] = $date->getRecurring();
            if ((!$jsEvent['isRecurring']) && (!$jsEvent['allDay'])) {
                if (is_null($date->getEndTime())) {
                    $jsEvent['restOfDay'] = true;
                }
            }
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
    	$repo = $this->_helper->entity->getRepository('Newscoop\Entity\ArticleDatetime');
    	
    	$repo->deleteById($articleDateTimeId);
    	
    	echo json_encode(array('code' => 200));
        die();
    }


    public function getdatesAction() 
    {

        require_once($GLOBALS['g_campsiteDir'].'/classes/Article.php');
        require_once($GLOBALS['g_campsiteDir'].'/classes/ArticleTypeField.php');

        $field_ranks = array();
        $field_infos = array();

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
            $itemRank = 0;
            $itemHidden = false;

            if (array_key_exists($itemField, $field_ranks)) {
                $itemRank = $field_ranks[$itemField];
            }
            else {
                $field_obj = new \ArticleTypeField($article_type, $itemField);
                $allItemRanks = $field_obj->getOrders();
                foreach ($allItemRanks as $one_weight => $one_field) {
                    $field_ranks[$one_field] = $one_weight;
                    if ($one_field == $itemField) {
                        $itemRank = $one_weight;
                    }
                }
            }

            if (array_key_exists($itemField, $field_infos)) {
                $itemColor = $field_infos[$itemField]['background_color'];
                $itemHidden = $field_infos[$itemField]['hidden_status'];
            }
            else {
                $field_obj = new \ArticleTypeField($article_type, $itemField);
                $itemColor = $field_obj->getColor();
                $itemHidden = $field_obj->isHidden();
                $field_infos[$itemField] = array('background_color' => $itemColor, 'hidden_status' => $itemHidden);
            }

            if ($itemHidden) {
                continue;
            }

        	if (strlen($recurring) > 1) {
        		//daterange
        		$start = strtotime( $this->getDate($date->getStartDate()->getTimestamp()).' '.$this->getTime(is_null($date->getStartTime()) ? $this->tz : $date->getStartTime()->getTimestamp()) . ' UTC');
        		$end = strtotime( $this->getDate(is_null($date->getEndDate()) ? ($this->distant) : $date->getEndDate()->getTimestamp()).' '.$this->getTime(is_null($date->getEndTime()) ? ($this->tz + 86399) : $date->getEndTime()->getTimestamp()) . ' UTC');
        		$itemStart = $start;
        		$itemEnd = strtotime( $this->getDate($date->getStartDate()->getTimestamp()).' '.$this->getTime(is_null($date->getEndTime()) ? ($this->tz + 86399) : $date->getEndTime()->getTimestamp()) . ' UTC');
        		
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
                    $calDate['title'] = $itemField;
                    $calDate['start_utc'] = $itemStart;
                    $calDate['start'] = gmdate('Y-m-d\TH:i:s\Z', $itemStart);
                    $calDate['start_day'] = gmdate('m-d', $itemStart);
                    $calDate['end_utc'] = $itemEnd;
                    $calDate['end'] = gmdate('Y-m-d\TH:i:s\Z', $itemEnd);
                    $calDate['allDay'] = $this->isAllDay($date);
                    $calDate['field_name'] = $itemField;
                    $calDate['backgroundColor'] = $itemColor;
                    $calDate['textColor'] = '#000000';
                    $calDate['event_comment'] = $event_comment;
                    $calDate['weight'] = $itemRank;
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
                $calDate['title'] = $itemField;
                $itemStart = strtotime( $this->getDate($date->getStartDate()->getTimestamp()).' '.$this->getTime( is_null($date->getStartTime()) ? $this->tz : $date->getStartTime()->getTimestamp() ) . ' UTC');
	        	$calDate['start_utc'] = $itemStart;
                $calDate['start'] = gmdate('Y-m-d\TH:i:s\Z', $itemStart);
                $calDate['start_day'] = gmdate('m-d', $itemStart);

	        	$endDate = $date->getEndDate();
                // TODO: at this moment, specific dates without end dates are taken as single-date dates, even though they should be taken as never-ending continuous events
                $itemEnd = 0;
	        	if ( empty($endDate)) {
	        		$itemEnd = strtotime( $this->getDate($date->getStartDate()->getTimestamp()).' '.$this->getTime(is_null($date->getEndTime()) ? ($this->tz + 86399) : $date->getEndTime()->getTimestamp()) . ' UTC');
	        	} else {
	        		$itemEnd = strtotime( $this->getDate($date->getEndDate()->getTimestamp()).' '.$this->getTime(is_null($date->getEndTime()) ? ($this->tz + 86399) : $date->getEndTime()->getTimestamp()) . ' UTC');	
	        	}
                $calDate['end_utc'] = $itemEnd;
                $calDate['end'] = gmdate('Y-m-d\TH:i:s\Z', $itemEnd);
                $calDate['allDay'] = $this->isAllDay($date);
                $calDate['restOfDay'] = false;
                if (!$calDate['allDay']) {
                    if (is_null($date->getEndTime())) {
                        $calDate['restOfDay'] = true;
                    }
                }

                $calDate['field_name'] = $itemField;
                $calDate['backgroundColor'] = $itemColor;
                $calDate['textColor'] = '#000000';
                if (in_array($itemColor, $dark_blues)) {
                    $calDate['textColor'] = $yellow;
                }
                $calDate['event_comment'] = $event_comment;
                $calDate['weight'] = $itemRank;
                $return[] = $calDate;
            }
        }

        $res = usort($return, 'self::EventOrder');
        echo json_encode($return);
        die();
    }

    public static function EventOrder($a, $b) {
        if (isset($a['start_day']) && isset($b['start_day'])) {
            if ($a['start_day'] > $b['start_day']) {
                return 1;
            }
            if ($a['start_day'] < $b['start_day']) {
                return -1;
            }
        }

        if (isset($a['weight']) && isset($b['weight'])) {
            if ($a['weight'] > $b['weight']) {
                return 1;
            }
            if ($a['weight'] < $b['weight']) {
                return -1;
            }
        }

        if (isset($a['start']) && isset($b['start'])) {
            if ($a['start'] > $b['start']) {
                return 1;
            }
            if ($a['start'] < $b['start']) {
                return -1;
            }
        }

        return 0;
    }

}

