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

    public function init()
    {

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
    	if ( $this->getTime($date->startTime->getTimestamp()) == "00:00" && $this->getTime($date->endTime->getTimestamp()) == "23:59" ) {
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
    		
    	} else {
    		$startDate = $this->_request->getParam('start-date-daterange');
    		$endDate = $this->_request->getParam('end-date-daterange');
    		
    		if ($this->_request->getParam('daterange-all-day') == 1) {
    			$startTime = "00:01";
    			$endTime = "23:59";
    		} else {
    			$startTime = $this->_request->getParam('start-time-daterange');
            	$endTime = $this->_request->getParam('end-time-daterange');	
    		}
    		
            
            
    		
            $timeSet = array("$startDate $startTime" => "$endDate $endTime");	
            
    	}
    	
        $repo->add($timeSet, $articleId, 'schedule');
    	
        echo json_encode(array('code' => 200));
    	
    	die();
    }


    public function getdatesAction() 
    {
    	$articleId = $this->_request->getParam('articleId');
        $repo = $this->_helper->entity->getRepository('Newscoop\Entity\ArticleDatetime');
        
        $return = array();
        
        
        
        $dates = $repo->findDates((object) array('articleId' => "$articleId"));
        
        //echopre($dates);
        
        foreach( $dates as $date) {
        	$calDate = array();
        	$calDate['id'] = $date->id;
        	$calDate['title'] = 'Event '.$date->id;
        	$calDate['start'] = strtotime( $this->getDate($date->getStartDate()->getTimestamp()).' '.$this->getTime($date->getStartTime()->getTimestamp()) );
        	$endDate = $date->getEndDate();
        	if ( empty($endDate)) {
        		$calDate['end'] = strtotime( $this->getDate($date->getStartDate()->getTimestamp()).' '.$this->getTime($date->getEndTime()->getTimestamp()) );
        	} else {
        		$calDate['end'] = strtotime( $this->getDate($date->getEndDate()->getTimestamp()).' '.$this->getTime($date->getEndTime()->getTimestamp()) );	
        	}
        	$calDate['allDay'] = $this->isAllDay($date);
        	$return[] = $calDate;
        }
        
        echo json_encode($return);
        
        die();
        
    }
    

    public function testDatetimeAction()
    {
        $repo = $this->_helper->entity->getRepository('Newscoop\Entity\ArticleDatetime');
        /* @var $repo Newscoop\Entity\Repository\ArticleDatetimeRepository */
        $arepo = $this->_helper->entity->getRepository('Newscoop\Entity\Article');
        /* @var $arepo Newscoop\Entity\Repository\ArticleRepository */
        $timeSet = array
        (
        	"2011-11-01" => array( "20:00" => "22:00", "recurring" => "weekly" ),
        	"2011-11-02" => array( "10:00" => "11:00", "12:00" => "18:00", "20:00" => "22:00" ),
            "2011-11-03" => "11:00 - recurring:daily",
        	"2011-11-03 14:00" => "18:00",
            "2011-11-04" => "2011-11-07",
            "2011-11-08 - 2011-11-09 12:00 - recurring:weekly",
        	"2011-11-10 10:30" => "2011-11-11",
        	"2011-11-12 12:30" => "2011-11-13 13:00",
        	"2011-11-14 14:30" => "2011-11-16 17:00 - recurring:daily",
        	"2011-11-16 15:30" => "2011-11-17",
        	"August 5" => "recurring:monthly", // 'fifth of august' doesn't work
            "first day of April" => "recurring:yearly",
        	"tomorrow" => true
        );
        $article = $arepo->findOneBy(array('type'=>'news'));
        // test insert by an array of dates
        var_dump( $repo->add($timeSet, $article->getId(), 'schedule') );
        
        // with a helper object
        // daily from 18:11:31 to 22:00:00 between 24th of November and the 29th
        $dateobj = new ArticleDatetime(array('2011-11-24 18:11:31' => '2011-11-29 22:00:00'), 'daily');
        var_dump( $repo->add($dateobj, $article->getId(), 'schedule', null, false) );
        // same as above in 1 string param
        $dateobj = new ArticleDatetime('2011-11-24 18:11:31 - 2011-11-29 22:00:00');
        var_dump( $repo->add($dateobj, $article->getId(), 'schedule', null, false) );

        // test update
        $one = $repo->findAll();
        $one = current($one);
        echo 'updating: ', $one->getId(), " (it'll get another id after this)";
        $repo->update( $one->getId(), array( "2011-11-27 10:30" => "2011-11-28" ));

        // test find
        // daily from 14:30
        echo 'daily from 14:30';
        var_dump($repo->findDates((object) array('daily' => '14:30')));
        // weekly to 12:00
        echo 'weekly to 12:00';
        var_dump($repo->findDates((object) array('weekly' => 'tuesday', 'endTime' => '12:00'), true)->getFindDatesSQL("dt.id"));
        // daily from 15:00 to 15:01
        //var_dump($repo->findDates((object) array('daily' => array( '15:00' => '15:01'))));
        // yearly in april
        echo 'monthly on the 5th';
        var_dump($repo->findDates((object) array('monthly' => '2011-11-05')));
        echo 'yearly in april';
        var_dump($repo->findDates((object) array('yearly' => 'april')));
        die;
        
    }
}

