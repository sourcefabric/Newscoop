<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager,
    Newscoop\Entity\UserSubscription;

/**
 * Stat service
 */
class StatService
{
    /** @var Doctrine\ORM\EntityManager */
    private $em;

    /**
     * @param Doctrine\ORM\EntityManager $em
     *
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }
    
    public function getAll()
    {
        require_once($GLOBALS['g_campsiteDir'].'/classes/SystemPref.php');
        
        $stats = array();
        $stats['installationId'] = $this->getInstallationId();
        $stats['logTime'] = $this->getLogTime();
        $stats['server'] = $this->getServer();
        $stats['ipAddress'] = $this->getIp();
        $stats['ramUsed'] = $this->getRamUsed();
        $stats['ramTotal'] = $this->getRamTotal();
        $stats['version'] = $this->getVersion();
        $stats['installMethod'] = $this->getInstallMethod();
        $stats['publications'] = $this->getPublications();
        $stats['issues'] = $this->getIssues();
        $stats['averageSections'] = $this->getAverageSections();
        $stats['articles'] = $this->getArticles();
        $stats['publishedArticles'] = $this->getArticles(true);
        $stats['languages'] = $this->getLanguages();
        $stats['authors'] = $this->getAuthors();
        $stats['subscribers'] = $this->getSubscribers();
        $stats['backendUsers'] = $this->getSubscribers(1);
        $stats['images'] = $this->getImages();
        $stats['attachments'] = $this->getAttachments();
        $stats['topics'] = $this->getTopics();
        $stats['comments'] = $this->getComments();
        $stats['hits'] = $this->getHits();
        
        return($stats);
    }
    
    public function getInstallationId()
    {
        return(\SystemPref::get('installation_id'));
    }
    
    public function getLogTime()
    {
        return(time());
    }
    
    public function getServer()
    {
        return($_SERVER['SERVER_SOFTWARE']);
    }
    
    public function getIp()
    {
        return($_SERVER['SERVER_ADDR']);
    }
    
    public function getRamUsed()
    {
        return(round((memory_get_usage()/(1024*1024)), 2));
    }
    
    public function getRamTotal()
    {
        return(str_replace('M', '', ini_get('memory_limit')));
    }
    
    public function getVersion()
    {
        return(\Newscoop\Version::VERSION);
    }
    
    public function getPublications()
    {
        $publicationRepository = $this->em->getRepository('Newscoop\Entity\Publication');
        $publications = $publicationRepository->findAll();
        return(count($publications));
    }
    
    public function getIssues()
    {
        $issues = \Issue::GetNumIssues();
        return($issues);
    }
    
    public function getAverageSections()
    {
        $averageSections = round(\Section::GetTotalSections() / $this->getIssues(), 2);
        return($averageSections);
    }
    
    public function getArticles($published = null)
    {
        $articleRepository = $this->em->getRepository('Newscoop\Entity\Article');
        $articles = $articleRepository->findAll();
        
        if ($published) {
            foreach ($articles as $key => $article) {
                if ($article->getWorkflowStatus() != 'Y') {
                    unset($articles[$key]);
                }
            }
        }
        return(count($articles));
    }
    
    public function getLanguages()
    {
        $languages = array();
        
        $articleRepository = $this->em->getRepository('Newscoop\Entity\Article');
        $articles = $articleRepository->findAll();
        
        foreach ($articles as $article) {
            $language = $article->getLanguage()->getName();
            if (!in_array($language, $languages)) {
                $languages[] = $language;
            }
        }
        
        $languages = implode(', ', $languages);
        
        return($languages);
    }
    
    public function getAuthors()
    {
        $authors = \Author::GetAuthors();
        return(count($authors));
    }
    
    public function getSubscribers($isAdmin = 0)
    {
        $userRepository = $this->em->getRepository('Newscoop\Entity\User');
        $subscribers = $userRepository->findBy(array('is_admin' => $isAdmin));
        
        return(count($subscribers));
    }
    
    public function getImages()
    {
        $images = \Image::GetTotalImages();
        return($images);
    }
    
    public function getAttachments()
    {
        $attachments = \Attachment::GetTotalAttachments();
        return($attachments);
    }
    
    public function getTopics()
    {
        $topics = \Topic::GetTopics(null, null, null, null, 5, null, null, true, false);
        return($topics['count']);
    }
    
    public function getComments()
    {
        $commentRepository = $this->em->getRepository('Newscoop\Entity\Comment');
        $comments = $commentRepository->findAll();
        
        return(count($comments));
    }
    
    public function getInstallMethod()
    {
        $installMethod = 'tarball';
        if (file_exists('debian')) {
            $installMethod = 'debian';
        }
        return($installMethod);
    }
    
    public function getHits()
    {
        global $Campsite;
        if (empty($Campsite)) {
            $Campsite = array('db' => array());
        }
        
        $newscoop_path = dirname(dirname(dirname(dirname(__FILE__))));
        require_once($newscoop_path . '/conf/database_conf.php');
        
        $dbAccess = $Campsite['db'];
        $db_host = $dbAccess['host'];
        $db_port = $dbAccess['port'];
        $db_user = $dbAccess['user'];
        $db_pwd = $dbAccess['pass'];
        $db_name = $dbAccess['name'];
        
        $dbh = new \PDO(
            "mysql:host=$db_host;port=$db_port;dbname=$db_name", 
            "$db_user",
            "$db_pwd",
            array(\PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
        );
        
        $query = "SELECT SUM(`request_count`) as 'hits' FROM `RequestObjects`";
        $sth = $dbh->prepare($query);
        $res = $sth->execute();
        $hits = $sth->fetch(\PDO::FETCH_ASSOC);
        
        return($hits['hits']);
    }
}
