<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * Includes
 */
require_once($GLOBALS['g_campsiteDir'].'/template_engine/metaclasses/MetaDbObject.php');

use Newscoop\Entity\User;

/**
 * Template user
 */
final class MetaUser extends MetaDbObject
{
    /** @var Newscoop\Entity\User */
    protected $user;

    /** @var bool */
    //public $defined;

    /**
     * @param Newscoop\Entity\User $user
     */
    public function __construct(User $user = NULL)
    {
        $this->user = $user;
        if (!$user) {
            return;
        }

        $this->m_dbObject = $user;

        $this->m_properties['id'] = 'getId';
        $this->m_properties['first_name'] = 'getFirstName';
        $this->m_properties['last_name'] = 'getLastName';
        $this->m_properties['uname'] = 'getUsername';
        $this->m_properties['email'] = 'getEmail';

        $this->m_customProperties['name'] = 'getDisplayName';
        $this->m_customProperties['created'] = 'getCreated';
        $this->m_customProperties['country'] = 'getCountry';
        $this->m_customProperties['subscription'] = 'getSubscription';
        $this->m_customProperties['logged_in'] = 'isLoggedIn';
        $this->m_customProperties['topics'] = 'getTopics';
        $this->m_customProperties['is_blocked_from_comments'] = 'isBlockedFromComments';
        $this->m_customProperties['is_admin'] = 'isAdmin';

        $this->m_skipFilter[] = "name";
        //$this->defined = $user->getId() > 0;
    }


    /**
     * @return string
     */
    protected function getDisplayName()
    {
        $url = $GLOBALS['controller']->view->url(array('username' => $this->user->getUsername()), 'user');

        $name = trim($this->user->getFirstName() . ' ' . $this->user->getLastName());

        if ($this->user->isPublic()) {
            return "<a href='{$url}'>{$name}</a>";
        }
        else {
            return $name;
        }
    }

    protected function getCreated()
    {
        $date = $this->user->getCreated();
        return $date->format('d.m.Y');
    }

    /**
     * Get subscription
     *
     * @return MetaSubscription
     */
    protected function getSubscription()
    {
        if (empty($this->user)) {
            return new MetaSubscription();
        }

        $publicationId = CampTemplate::singleton()->context()->publication->identifier;
        $subscriptions = Subscription::GetSubscriptions($publicationId, $this->user->getId());
        return empty($subscriptions) ? new MetaSubscription() : new MetaSubscription($subscriptions[0]->getSubscriptionId());
    }

    /**
     * Get the name of the country of the registered user
     *
     * @return string
     */
    protected function getCountry()
    {
        require_once dirname(__FILE__) . '/../../classes/Country.php';
        require_once dirname(__FILE__) . '/../../classes/Language.php';

        $countryCode = $this->user->getAttribute('country_code');
        $smartyObj = CampTemplate::singleton();
        $contextObj = $smartyObj->get_template_vars('gimme');
        $country = new Country($countryCode, $contextObj->language->number);
        return !$country->exists() ? '' : $country->getName();
    }

    /**
     * Test if user has permission
     *
     * @param string $permission
     * @return boolean
     */
    public function has_permission($permission)
    {
        return $this->user->hasPermission($permission);
    }

    /**
     * Test if user is admin
     *
     * @return bool
     */
    protected function isAdmin()
    {
        return $this->user->isAdmin();
    }

    /**
     * Test if user is logged in
     *
     * @return bool
     */
    protected function isLoggedIn()
    {
        $auth = Zend_Auth::getInstance();
        return $auth->hasIdentity() && $auth->getIdentity() == $this->user->getId();
    }

    /**
     * Test if user is blocked from commenting
     *
     * @return bool
     */
    protected function isBlockedFromComments()
    {
        require_once dirname(__FILE__) . '/../../include/get_ip.php';

        global $controller;

        $userIp = getIp();
        $publication_id = CampTemplate::singleton()->context()->publication->identifier;
        $repositoryAcceptance = $controller->getHelper('user')->getRepository('Newscoop\user\Comment\Acceptance');
        $repository = $controller->getHelper('user')->getRepository('Newscoop\user\Comment');
        return (int) $repositoryAcceptance->checkParamsBanned($this->name, $this->email, $userIp, $publication_id);
    }

    /**
     * Get image src
     *
     * @param int $width
     * @param int $height
     * @return string
     */
    public function image($width = 80, $height = 80)
    {
        if (!$this->user->getImage()) {
            return '';
        }

        return $GLOBALS['controller']->getHelper('service')->getService('image')
            ->getSrc($this->user->getImage(), $width, $height);
    }

    /**
     * Get topics
     *
     * @return array
     */
    protected function getTopics()
    {
        if (!$this->user->getId()) {
            return array();
        }

        $service = $GLOBALS['controller']->getHelper('service')->getService('user.topic');
        $topics = array();
        foreach ($service->getTopics($this->user) as $topic) {
            $topics[$topic->getTopicId()] = $topic->getName();
        }

        return $this->topics = $topics;
    }
}
