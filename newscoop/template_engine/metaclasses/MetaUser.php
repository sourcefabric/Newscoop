<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\User;

require_once __DIR__ . '/MetaDbObject.php';

/**
 * Template user
 */
final class MetaUser extends MetaDbObject implements ArrayAccess
{

    /**
     * @param Newscoop\Entity\User $user
     */
    public function __construct(User $user = NULL)
    {
        if (is_null($user)) {
            $user = new User;
        }
        $this->m_dbObject = $user;

        $this->m_properties = array();

        $this->m_customProperties['identifier'] = 'getId';
        $this->m_customProperties['first_name'] = 'getFirstName';
        $this->m_customProperties['last_name'] = 'getLastName';
        $this->m_customProperties['uname'] = 'getUsername';
        $this->m_customProperties['email'] = 'getEmail';
        $this->m_customProperties['subscriber'] = 'getSubscriber';

        $this->m_customProperties['name'] = 'getDisplayName';
        $this->m_customProperties['created'] = 'getCreated';
        $this->m_customProperties['country'] = 'getCountry';
        $this->m_customProperties['subscription'] = 'getSubscription';
        $this->m_customProperties['logged_in'] = 'isLoggedIn';
        $this->m_customProperties['topics'] = 'getTopics';
        $this->m_customProperties['is_blocked_from_comments'] = 'isBlockedFromComments';
        $this->m_customProperties['is_admin'] = 'isAdmin';
        $this->m_customProperties['defined'] = 'isDefined';
        $this->m_customProperties['posts_count'] = 'getPostsCount';
        $this->m_customProperties['is_author'] = 'isAuthor';
        $this->m_customProperties['is_active'] = 'isActive';
        $this->m_customProperties['is_blogger'] = 'isBlogger';
        $this->m_customProperties['author'] = 'getAuthor';
        $this->m_customProperties['image'] = 'image';

        $this->m_skipFilter[] = "name";
    }

    protected function getFirstName()
    {
        return $this->m_dbObject->getFirstName();
    }

    protected function getLastName()
    {
        return $this->m_dbObject->getLastName();
    }

    protected function getUsername()
    {
        return $this->m_dbObject->getusername();
    }

    protected function getEmail()
    {
        return $this->m_dbObject->getEmail();
    }
    
    protected function getSubscriber()
    {
        return $this->m_dbObject->getSubscriber();
    }

    /**
     * @return string
     */
    protected function getDisplayName()
    {
        $url = $GLOBALS['controller']->view->url(array('username' => $this->m_dbObject->getUsername()), 'user');

        $name = trim($this->m_dbObject->getFirstName() . ' ' . $this->m_dbObject->getLastName());

        if ($this->m_dbObject->isPublic()) {
            return "<a href='{$url}'>{$name}</a>";
        }
        else {
            return $name;
        }
    }

    /**
     * Get user id
     * @author Mihai Balaceanu
     * @return int
     */
    protected function getId()
    {
        return $this->m_dbObject->getId();
    }

    protected function isDefined()
    {
        return $this->m_dbObject->getId() > 0;
    }

    protected function getCreated()
    {
        $date = $this->m_dbObject->getCreated();
        return $date->format('d.m.Y');
    }

    /**
     * Get subscription
     *
     * @return MetaSubscription
     */
    protected function getSubscription()
    {
        if (empty($this->m_dbObject)) {
            return new MetaSubscription();
        }

        $publicationId = CampTemplate::singleton()->context()->publication->identifier;
        $subscriptions = Subscription::GetSubscriptions($publicationId, $this->m_dbObject->getId());
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

        $countryCode = $this->m_dbObject->getAttribute('country_code');
        $smartyObj = CampTemplate::singleton();
        $contextObj = $smartyObj->getTemplateVars('gimme');
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
        return $this->m_dbObject->hasPermission($permission);
    }

    /**
     * Test if user is admin
     *
     * @return bool
     */
    public function isAdmin()
    {
        return $this->m_dbObject->isAdmin() && !$this->isBlogger();
    }

    /**
     * Test if user is blogger
     *
     * @return bool
     */
    public function isBlogger()
    {
        return (bool) \Zend_Registry::get('container')->getService('blog')
            ->isBlogger($this->m_dbObject);
    }

    /**
     * Test if user is logged in
     *
     * @return bool
     */
    protected function isLoggedIn()
    {
        $auth = Zend_Auth::getInstance();
        return $auth->hasIdentity() && $auth->getIdentity() == $this->m_dbObject->getId();
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
        global $controller;

        if (!$this->m_dbObject->getImage()) {
            return '';
        }

        return $controller->view->url(array(
            'src' => $controller->getHelper('service')->getService('image')->getSrc('images/' . $this->m_dbObject->getImage(), $width, $height),
        ), 'image', false, false);
    }

    /**
     * Get topics
     *
     * @return array
     */
    protected function getTopics()
    {
        if (!$this->m_dbObject->getId()) {
            return array();
        }

        $service = $GLOBALS['controller']->getHelper('service')->getService('user.topic');
        $topics = array();
        foreach ($service->getTopics($this->m_dbObject) as $topic) {
            $topics[$topic->getTopicId()] = $topic->getName();
        }

        return $topics;
    }

    /**
     * Get posts count
     *
     * @return int
     */
    protected function getPostsCount()
    {
        if (!$this->m_dbObject->getId()) {
            return 0;
        }

        $sum = 0;
        $sum += $GLOBALS['controller']->getHelper('entity')->getRepository('Newscoop\Entity\Comment')
            ->countByUser($this->m_dbObject);

        $sum += $GLOBALS['controller']->getHelper('entity')->getRepository('Newscoop\Entity\Feedback')
            ->countByUser($this->m_dbObject);

        return $sum;
    }

    /**
     * @see ArrayAccess
     * @todo
     */
    public function offsetExists($offset)
    {
        $offset = $this->m_dbObject->getAttribute($offset);
        return isset($offset);
    }

    /**
     * @see ArrayAccess
     */
    public function offsetGet($offset)
    {
        return $this->m_dbObject->getAttribute($offset);
    }

    /**
     * @see ArrayAccess
     * @todo
     */
    public function offsetSet($offset, $value)
    {
    }

    /**
     * @see ArrayAccess
     * @todo
     */
    public function offsetUnset($offset)
    {
    }

    /**
     * Test if user is author
     *
     * @return bool
     */
    public function isAuthor()
    {
        return $this->m_dbObject->getAuthorId() && $this->isAdmin();
    }

    /**
     * Get user related author
     *
     * @return MetaAuthor
     */
    public function getAuthor()
    {
        try {
            return new \MetaAuthor($this->m_dbObject->getAuthorId());
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Test if user is active
     *
     * @return bool
     */
    public function isActive()
    {
        return $this->m_dbObject->isActive();
    }
}
