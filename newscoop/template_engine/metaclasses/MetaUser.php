<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\User;

require_once($GLOBALS['g_campsiteDir'].'/classes/User.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Language.php');
require_once($GLOBALS['g_campsiteDir'].'/classes/Country.php');
require_once($GLOBALS['g_campsiteDir'].'/template_engine/metaclasses/MetaDbObject.php');
require_once($GLOBALS['g_campsiteDir'].'/include/get_ip.php');

/**
 */
final class MetaUser
{
    /** @var Newscoop\Entity\User */
    protected $entity;

    /** @var array */
    protected $callbacks = array();

    /** @var int */
    public $identifier;

    /** @var string */
    public $uname;

    /** @var string */
    public $name;

    /** @var string */
    public $email;

    /** @var bool */
    public $defined;

    /**
     * @param Newscoop\Entity\User $user
     */
    public function __construct(User $user = NULL)
    {
        if (!$user) {
            return;
        }

        $this->entity = $user;

        $this->identifier = $user->getId();
        $this->name = trim($user->getFirstName() . ' ' . $user->getLastName());
        $this->uname = $user->getUsername();
        $this->email = $user->getEmail();
        $this->defined = $user->getId() > 0;
        $this->is_admin = true; // TODO add permissions based check

        $this->callbacks = array(
            'country' => array($this, 'getCountry'),
            'logged_in' => array($this, 'isLoggedIn'),
            'blocked_from_comments' => array($this, 'isBlockedFromComments'),
            'subscription' => array($this, 'getSubscription'),
            'is_admin' => array($this, 'isAdmin'),
        );
    }

    /**
     * @param string $property
     */
    public function __get($property)
    {
        if (empty($this->callbacks[$property])) {
            throw new InvalidArgumentException("Property '$property' not defined");
        }

        if (empty($this->entity)) {
            return;
        }

        return call_user_func($this->callbacks[$property]);
    }

    /**
     * Returns the name of the country of the registered user.
     *
     * @return string
     */
    protected function getCountry()
    {
        $countryCode = $this->entity->getAttribute('CountryCode');
        $smartyObj = CampTemplate::singleton();
        $contextObj = $smartyObj->get_template_vars('gimme');
        $country = new Country($countryCode, $contextObj->language->number);
        if (!$country->exists()) {
            return null;
        }
        return $country->getName();
    }


    /**
     * Request an user permission indicated by permission name
     *
     * @param string $p_permission permission name
     * @return boolean
     */
    public function has_permission($p_permission)
    {
        return $this->entity->hasPermission($p_permission);
    }


    /**
     * Returns true if the user had adminstration rights
     *
     * @return bool
     */
    protected function isAdmin()
    {
        // TODO use acl
        return True;
    }


    /**
     * Returns true of the user was authenticated, false if not
     *
     * @return bool
     */
    protected function isLoggedIn()
    {
        $auth = Zend_Auth::getInstance();
        return $auth->hasIdentity() && $auth->getIdentity() == $this->entity->getId();
    }

    protected function isBlockedFromComments()
    {
        global $controller;

        $publication_id = CampTemplate::singleton()->context()->publication->identifier;
        $userIp = getIp();
        $repositoryAcceptance = $controller->getHelper('entity')->getRepository('Newscoop\Entity\Comment\Acceptance');
        $repository = $controller->getHelper('entity')->getRepository('Newscoop\Entity\Comment');
        return (int) $repositoryAcceptance->checkParamsBanned($this->name, $this->email, $userIp, $publication_id);
    }


    protected function getSubscription()
    {
        if (empty($this->entity)) {
            return;
        }

        $publicationId = CampTemplate::singleton()->context()->publication->identifier;
        $subscriptions = Subscription::GetSubscriptions($publicationId, $this->entity->getId());
        if (empty($subscriptions)) {
            return new MetaSubscription();
        }
        return new MetaSubscription($subscriptions[0]->getSubscriptionId());
    }
}
