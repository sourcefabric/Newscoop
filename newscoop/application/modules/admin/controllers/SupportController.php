<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * @Acl(ignore="1")
 */
class Admin_SupportController extends Zend_Controller_Action
{

    public function init()
    {
        
    }

    public function indexAction()
    {
        $this->view->stats = $this->getStats();
        
        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            
            SystemPref::set('support_set', 1);
            
            SystemPref::set('support_send', $values['send_support_feedback']);
            SystemPref::set('support_promote', $values['promote']);
            if ($values['promote'] == '1') {
                SystemPref::set('support_promote_name', $values['promote_name']);
                SystemPref::set('support_promote_description', $values['promote_description']);
                SystemPref::set('support_promote_country', $values['promote_country']);
                SystemPref::set('support_promote_city', $values['promote_city']);
                SystemPref::set('support_promote_phone', $values['promote_phone']);
                SystemPref::set('support_promote_email', $values['promote_email']);
            }
            
            $this->_helper->flashMessenger(getGS('Support settings saved.'));
            $this->_helper->redirector('..');
        }
        else {
            $this->view->form = $this->getForm();
            $this->view->first = false;
            if (!SystemPref::get('support_set')) {
                $this->view->first = true;
            }
        }
    }
    
    public function getStats()
    {
        $stats = array();
        $stats['installationId'] = SystemPref::get('installation_id');
        $stats['logTime'] = time();
        $stats['server'] = $_SERVER['SERVER_SOFTWARE'];
        $stats['ipAddress'] = $_SERVER['SERVER_ADDR'];
        $stats['ramUsed'] = round((memory_get_usage()/(1024*1024)), 2);
        $stats['ramTotal'] = str_replace('M', '', ini_get('memory_limit'));
        $stats['version'] = \Newscoop\Version::VERSION;
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
    
    public function getPublications()
    {
        $publicationRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Publication');
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
        $articleRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Article');
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
        
        $articleRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Article');
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
        $userRepository = $this->_helper->entity->getRepository('Newscoop\Entity\User');
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
        $commentRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Comment');
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
        
        $newscoop_path = dirname(dirname(dirname(dirname(dirname(__FILE__)))));
        require_once($newscoop_path . '/conf/database_conf.php');
        
        $dbAccess = $Campsite['db'];
        $db_host = $dbAccess['host'];
        $db_port = $dbAccess['port'];
        $db_user = $dbAccess['user'];
        $db_pwd = $dbAccess['pass'];
        $db_name = $dbAccess['name'];
        
        $dbh = new PDO(
            "mysql:host=$db_host;port=$db_port;dbname=$db_name", 
            "$db_user",
            "$db_pwd",
            array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8')
        );
        
        $query = "SELECT SUM(`request_count`) as 'hits' FROM `RequestObjects`";
        $sth = $dbh->prepare($query);
        $res = $sth->execute();
        $hits = $sth->fetch(PDO::FETCH_ASSOC);
        
        return($hits['hits']);
    }
    
    /**
     * Get priority form
     *
     * @return \Zend_Form
     */
    private function getForm()
    {
        $supportSend = (SystemPref::get('support_send')) ? SystemPref::get('support_send') : 0;
        /*
        $supportPromote = (SystemPref::get('support_promote')) ? SystemPref::get('support_promote') : 0;
        $supportPromoteName = (SystemPref::get('support_promote_name')) ? SystemPref::get('support_promote_name') : '';
        $supportPromoteDescription = (SystemPref::get('support_promote_description')) ? SystemPref::get('support_promote_description') : '';
        $supportPromoteCountry = (SystemPref::get('support_promote_country')) ? SystemPref::get('support_promote_country') : '';
        $supportPromoteCity = (SystemPref::get('support_promote_city')) ? SystemPref::get('support_promote_city') : '';
        $supportPromotePhone = (SystemPref::get('support_promote_phone')) ? SystemPref::get('support_promote_phone') : '';
        $supportPromoteEmail = (SystemPref::get('support_promote_email')) ? SystemPref::get('support_promote_email') : '';
        */
        
        $form = new Zend_Form;

        $form->addElement('checkbox', 'send_support_feedback', array(
            //'onChange' => 'fixPromote();fixSubmit();',
            'onChange' => 'fixSubmit();',
            'value' => $supportSend
        ));
        /*
        $form->addElement('checkbox', 'promote', array(
            'onChange' => 'fixPromoteDetails();',
            'value' => $supportPromote
        ));
        
        $form->addElement('text', 'promote_name', array(
            'label' => getGS('Website name'),
            'value' => $supportPromoteName
        ));
        $form->addElement('textarea', 'promote_description', array(
            'label' => getGS('Description'),
            'class' => 'textAreaFix',
            'value' => $supportPromoteDescription
        ));
        $form->addElement('text', 'promote_country', array(
            'label' => getGS('Country'),
            'value' => $supportPromoteCountry
        ));
        $form->addElement('text', 'promote_city', array(
            'label' => getGS('City'),
            'value' => $supportPromoteCity
        ));
        $form->addElement('text', 'promote_phone', array(
            'label' => getGS('Phone').' ('.getGS('Will not be published. For verification purposes only').')',
            'value' => $supportPromotePhone
        ));
        $form->addElement('text', 'promote_email', array(
            'label' => getGS('E-mail').' ('.getGS('Will not be published. For verification purposes only').')',
            'value' => $supportPromoteEmail
        ));
        */
        $form->addElement('checkbox', 'agree_policy', array(
            'onChange' => 'fixSubmit();',
            'value' => 0
        ));
        
        $form->addElement('submit', 'save', array(
            'label' => getGS('Save')
        ));
        
        return $form;
    }
}

