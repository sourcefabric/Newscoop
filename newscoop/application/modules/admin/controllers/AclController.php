<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\Acl\Role,
    Newscoop\Entity\Acl\Rule,
    Newscoop\Entity\User\Staff;

/**
 * @Acl(ignore="1")
 */
class Admin_AclController extends Zend_Controller_Action
{
    /** @var Resource_Acl */
    private $acl;

    /** @var Doctrine\ORM\EntityRepository */
    private $ruleRepository;

    /** @var string */
    private $resource;

    public function init()
    {
        camp_load_translation_strings('user_types');

        $this->ruleRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Acl\Rule');

        $this->ruleTypes = array(
            'allow' => getGS('Allow'),
            'deny' => getGS('Deny'),
        );

        $this->groups = array(
            'authoring' => getGS('Authoring'),
            'structure' => getGS('Structure'),
            'layout' => getGS('Layout'),
            'users' => getGS('Users'),
            'system' => getGS('System'),
            'plugins' => getGS('Plugins'),
        );

        $this->resources = array(
            'authoring' => array(
                'article' => getGS('Articles'),
                'image' => getGS('Images'),
                'comment' => getGS('Comments'),
                'file' => getGS('Files'),
                'audioclip' => getGS('Audioclips'),
                'editor' => getGS('Rich-Text Editor Preferences'),
            ),
            'structure' => array(
                'publication' => getGS('Publications'),
                'issue' => getGS('Issues'),
                'section' => getGS('Sections'),
                'topic' => getGS('Topics'),
                'language' => getGS('Languages'),
            ),
            'users' => array(
                'user-group' => getGS('User Groups'),
                'user' => getGS('Staff'),
                'author' => getGS('Authors'),
                'subscriber' => getGS('Subscribers'),
            ),
            'layout' => array(
                'theme' => getGS('Themes'),
                'template' => getGS('Templates'),
                'article-type' => getGS('Article Types'),
            ),
            'system' => array(
                'system-preferences' => getGS('Global'),
                'indexer' => getGS('Search Indexer'),
                'country' => getGS('Countries'),
                'log' => getGS('Log'),
                'localizer' => getGS('Localizer'),
                'backup' => getGS('Backup'),
                'cache' => getGS('Cache'),
                'subscription' => getGS('Subscriptions'),
                'notification' => getGS('Notification'),
            ),
            'plugins' => array(
                'plugin' => getGS('Plugins'),
                'plugin-blog' => getGS('Blogs'),
                'pluginpoll' => getGS('Polls'),
                'plugin-interview' => getGS('Interviews'),
                'plugin-recaptcha' => getGS('ReCaptcha'),
            ),
        );

        // i18n
        $this->actions = array(
            'add' => getGS('add'),
            'admin' => getGS('admin'),
            'attach' => getGS('attach'),
            'clear' => getGS('clear'),
            'delete' => getGS('delete'),
            'edit' => getGS('edit'),
            'enable' => getGS('enable'),
            'guest' => getGS('guest'),
            'manage' => getGS('manage'),
            'moderate' => getGS('moderate'),
            'moderator' => getGS('moderate'),
            'move' => getGS('move'),
            'notify' => getGS('notify'),
            'publish' => getGS('publish'),
            'translate' => getGS('translate'),
            'view' => getGS('view'),

            // editor related
            'bold' => getGS('bold'),
            'charactermap' => getGS('character map'),
            'copycutpaste' => getGS('copy/cut/paste'),
            'enlarge' => getGS('enlarge'),
            'findreplace' => getGS('find/replace'),
            'fontcolor' => getGS('font color'),
            'fontface' => getGS('font face'),
            'fontsize' => getGS('font size'),
            'horizontalrule' => getGS('horizontal rule'),
            'image' => getGS('image'),
            'indent' => getGS('indent'),
            'italic' => getGS('italic'),
            'link' => getGS('link'),
            'listbullet' => getGS('list bullet'),
            'listnumber' => getGS('list number'),
            'sourceview' => getGS('source view'),
            'spellcheckerenabled' => getGS('spell checker enabled'),
            'statusbar' => getGS('statusbar'),
            'strikethrough' => getGS('strikethrough'),
            'subhead' => getGS('subhead'),
            'subscript' => getGS('subscript'),
            'superscript' => getGS('superscript'),
            'table' => getGS('table'),
            'textalignment' => getGS('text alignment'),
            'textdirection' => getGS('text direction'),
            'underline' => getGS('underline'),
            'undoredo' => getGS('undo/redo'),
        );

        $this->_helper->contextSwitch()
            ->addActionContext('save', 'json')
            ->initContext();

        $this->acl = Zend_Registry::get('acl');

        $this->resource = $this->_getParam('user', false) ? 'user' : 'user-group';
    }

    public function editAction()
    {
        if ($this->_getParam('user', false)) {
            $role = $this->_helper->entity->find('Newscoop\Entity\User\Staff', $this->_getParam('user'));
        } else {
            $role = $this->_helper->entity->find('Newscoop\Entity\User\Group', $this->_getParam('group'));
        }

        $this->view->role = $role;
        $this->view->roleId = $role->getRoleId();
        $this->view->groups = $this->groups;
        $this->view->resources = $this->resources;
        $this->view->actions = $this->acl->getResources();
        $this->view->actionNames = $this->actions;
        $this->view->acl = $this->getHelper('acl')->getAcl($role);
    }

    public function saveAction()
    {
        $request = $this->getRequest();
        if (!$request->isPost()) {
            return;
        }

        $values = $request->getPost();
        if ($this->isBlocker($values)) {
            $this->view->status = 'error';
            $this->view->message = getGS("You can't deny yourself to manage $1", $this->getResourceName($this->resource));
            return;
        }

        try {
            $rule = new Rule();
            $this->ruleRepository->save($rule, $values);
            $this->_helper->entity->flushManager();
            $this->view->status = 'ok';
        } catch (\Exception $e) {
            $this->view->status = 'error';
            $this->view->message = $e->getMessage();
        }
    }

    /**
     * Test if adding rule would block current user to manage users/types
     *
     * @param array $values
     * @return bool
     */
    private function isBlocker(array $values)
    {
        $user = Zend_Registry::get('user');
        $acl = $this->_helper->acl->getAcl($user);

        if (in_array($values['role'], $acl->getRoles()) && $values['type'] == 'deny') {
            $resource = empty($values['resource']) ? null : $values['resource'];
            $action = empty($values['action']) ? null : $values['action'];
            $acl->deny($values['role'], $resource, $action);

            return !$acl->isAllowed($user, $this->resource, 'manage');
        }

        return False;
    }

    /**
     * Get translated resource name
     *
     * @param string $resource
     * @return string
     */
    private function getResourceName($resource)
    {
        foreach ($this->resources as $resources) {
            if (isset($resources[$resource])) {
                return $resources[$resource];
            }
        }

        return $resource;
    }
}
