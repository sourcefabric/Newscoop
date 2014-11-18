<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Annotations\Acl;
use Newscoop\Entity\Acl\Rule;
use Newscoop\Entity\User;

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
        $translator = \Zend_Registry::get('container')->getService('translator');

        $this->ruleRepository = $this->_helper->entity->getRepository('Newscoop\Entity\Acl\Rule');

        $this->ruleTypes = array(
            'allow' => $translator->trans('Allow', array(), 'user_types'),
            'deny' => $translator->trans('Deny', array(), 'user_types'),
        );

        $this->groups = array(
            'authoring' => $translator->trans('Authoring', array(), 'user_types'),
            'structure' => $translator->trans('Structure', array(), 'user_types'),
            'layout' => $translator->trans('Layout', array(), 'user_types'),
            'users' => $translator->trans('Users'),
            'system' => $translator->trans('System', array(), 'user_types'),
            'plugins' => $translator->trans('Plugins'),
        );

        $this->resources = array(
            'authoring' => array(
                'article' => $translator->trans('Articles'),
                'image' => $translator->trans('Images'),
                'comment' => $translator->trans('Comments'),
                'feedback' => $translator->trans('Feedback Messages', array(), 'user_types'),
                'file' => $translator->trans('Files', array(), 'user_types'),
                'editor' => $translator->trans('Rich-Text Editor Preferences', array(), 'user_types'),
            ),
            'structure' => array(
                'publication' => $translator->trans('Publications'),
                'issue' => $translator->trans('Issues'),
                'section' => $translator->trans('Sections'),
                'topic' => $translator->trans('Topics'),
                'language' => $translator->trans('Languages'),
                'playlist' => $translator->trans('Article Playlists', array(), 'user_types')
            ),
            'users' => array(
                'user-group' => $translator->trans('User Groups', array(), 'user_types'),
                'user' => $translator->trans('Staff'),
                'author' => $translator->trans('Authors'),
                'subscriber' => $translator->trans('Subscribers'),
                'subscription' => $translator->trans('Subscriptions'),
            ),
            'layout' => array(
                'theme' => $translator->trans('Themes'),
                'template' => $translator->trans('Templates', array(), 'user_types'),
                'article-type' => $translator->trans('Article Types', array(), 'user_types'),
            ),
            'system' => array(
                'system-preferences' => $translator->trans('Global', array(), 'user_types'),
                'indexer' => $translator->trans('Search Indexer', array(), 'user_types'),
                'country' => $translator->trans('Countries'),
                'log' => $translator->trans('Log', array(), 'user_types'),
                'backup' => $translator->trans('Backup', array(), 'user_types'),
                'cache' => $translator->trans('Cache', array(), 'user_types'),
                'notification' => $translator->trans('Notification'),
            ),
            'plugins' => array(
                'plugin' => $translator->trans('Plugins'),
                'pluginpoll' => $translator->trans('Polls', array(), 'user_types'),
                'plugin-recaptcha' => $translator->trans('ReCaptcha', array(), 'user_types'),
                'plugin-soundcloud' => $translator->trans('Soundcloud', array(), 'user_types'),
            ),
        );

        // i18n
        $this->actions = array(
            'add' => $translator->trans('add', array(), 'user_types'),
            'admin' => $translator->trans('admin', array(), 'user_types'),
            'attach' => $translator->trans('attach', array(), 'user_types'),
            'clear' => $translator->trans('clear', array(), 'user_types'),
            'delete' => $translator->trans('delete', array(), 'user_types'),
            'edit' => $translator->trans('edit', array(), 'user_types'),
            'enable' => $translator->trans('enable', array(), 'user_types'),
            'get' => $translator->trans('get', array(), 'user_types'),
            'guest' => $translator->trans('guest', array(), 'user_types'),
            'manage' => $translator->trans('manage', array(), 'user_types'),
            'moderate' => $translator->trans('moderate', array(), 'user_types'),
            'moderate-comment' => $translator->trans('moderate', array(), 'user_types'),
            'moderator' => $translator->trans('moderate', array(), 'user_types'),
            'move' => $translator->trans('move', array(), 'user_types'),
            'notify' => $translator->trans('notify', array(), 'user_types'),
            'publish' => $translator->trans('publish', array(), 'user_types'),
            'translate' => $translator->trans('translate', array(), 'user_types'),
            'view' => $translator->trans('view', array(), 'user_types'),

            // editor related
            'bold' => $translator->trans('bold', array(), 'user_types'),
            'charactermap' => $translator->trans('character map', array(), 'user_types'),
            'copycutpaste' => $translator->trans('copy/cut/paste', array(), 'user_types'),
            'enlarge' => $translator->trans('enlarge', array(), 'user_types'),
            'findreplace' => $translator->trans('find/replace', array(), 'user_types'),
            'fontcolor' => $translator->trans('font color', array(), 'user_types'),
            'fontface' => $translator->trans('font face', array(), 'user_types'),
            'fontsize' => $translator->trans('font size', array(), 'user_types'),
            'horizontalrule' => $translator->trans('horizontal rule', array(), 'user_types'),
            'image' => $translator->trans('image', array(), 'user_types'),
            'indent' => $translator->trans('indent', array(), 'user_types'),
            'italic' => $translator->trans('italic', array(), 'user_types'),
            'link' => $translator->trans('link', array(), 'user_types'),
            'listbullet' => $translator->trans('list bullet', array(), 'user_types'),
            'listnumber' => $translator->trans('list number', array(), 'user_types'),
            'sourceview' => $translator->trans('source view', array(), 'user_types'),
            'spellcheckerenabled' => $translator->trans('spell checker enabled', array(), 'user_types'),
            'statusbar' => $translator->trans('statusbar', array(), 'user_types'),
            'strikethrough' => $translator->trans('strikethrough', array(), 'user_types'),
            'subhead' => $translator->trans('subhead', array(), 'user_types'),
            'subscript' => $translator->trans('subscript', array(), 'user_types'),
            'superscript' => $translator->trans('superscript', array(), 'user_types'),
            'table' => $translator->trans('table', array(), 'user_types'),
            'textalignment' => $translator->trans('text alignment', array(), 'user_types'),
            'textdirection' => $translator->trans('text direction', array(), 'user_types'),
            'underline' => $translator->trans('underline', array(), 'user_types'),
            'undoredo' => $translator->trans('undo/redo', array(), 'user_types'),
        );

        // register new plugins permissions
        $pluginsService = \Zend_Registry::get('container')->get('newscoop.plugins.service');
        $collectedPermissionsData = $pluginsService->collectPermissions();
        foreach ($collectedPermissionsData as $pluginLabel => $permissions) {
            foreach ($permissions as $permissionKey => $permissionName) {
                $pluginNameArray = explode('_', $permissionKey);
                $this->resources['plugins'][$pluginNameArray[0] . '-' . $pluginNameArray[1]] = $pluginLabel;
                break;
            }
        }

        $this->_helper->contextSwitch()
            ->addActionContext('edit', 'json')
            ->initContext();

        $this->acl = Zend_Registry::get('acl');

        $this->resource = $this->_getParam('user', false) ? 'user' : 'user-group';
    }

    public function editAction()
    {
        $translator = \Zend_Registry::get('container')->getService('translator');

        $role = $this->_getParam('user', false)
            ? $this->_helper->entity->find('Newscoop\Entity\User', $this->_getParam('user'))
            : $this->_helper->entity->find('Newscoop\Entity\User\Group', $this->_getParam('group'));

        if ($this->getRequest()->isPost()) {
            $values = $this->getRequest()->getPost();
            if ($this->isBlocker($values)) {
                $this->view->error = $translator->trans("You cant deny yourself to manage $1", array('$1' => $this->getResourceName($this->resource)), 'user_types');

                return;
            }

            try {
                $this->ruleRepository->save($values, $this->_getParam('user', false));
            } catch (\Exception $e) {
                $this->view->error = $e->getMessage();
            }

            return;
        }

        $this->view->role = $role;
        $this->view->groups = $this->groups;
        $this->view->resources = $this->resources;
        $this->view->actions = $this->acl->getResources();
        $this->view->actionNames = $this->actions;
        $this->view->acl = $this->getHelper('acl')->getAcl($role);
    }

    /**
     * Test if adding rule would block current user to manage users/types
     *
     * @param  array $values
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
     * @param  string $resource
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
