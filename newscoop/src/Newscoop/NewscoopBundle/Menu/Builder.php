<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Menu;

use Knp\Menu\FactoryInterface;
use Newscoop\NewscoopBundle\Event\ConfigureMenuEvent;

class Builder
{

    private $factory;
    private $container;
    private $user;
    private $showPublishingEnvironmentMenu;
    private $showConfigureMenu;
    private $showUserMenu;

    private function preparePrivileges()
    {
        $this->showPublishingEnvironmentMenu = (
            $this->user->hasPermission('ManageTempl') || 
            $this->user->hasPermission('DeleteTempl') ||
            $this->user->hasPermission('ManageArticleTypes') ||
            $this->user->hasPermission('DeleteArticleTypes') ||
            $this->user->hasPermission('ManageTopics') ||
            $this->user->hasPermission('ManageLanguages') ||
            $this->user->hasPermission('DeleteLanguages') ||
            $this->user->hasPermission('ManageCountries') ||
            $this->user->hasPermission('DeleteCountries')
        );

        $this->showConfigureMenu = (
            $this->showPublishingEnvironmentMenu || 
            $this->user->hasPermission('ManageLocalizer') || 
            $this->user->hasPermission('ViewLogs')
        );

        $this->showUserMenu = (
            $this->user->hasPermission('ManageUsers') || 
            $this->user->hasPermission('DeleteUsers') || 
            $this->user->hasPermission('ManageSubscriptions') || 
            $this->user->hasPermission('ManageUserTypes') || 
            $this->user->hasPermission('ManageReaders') || 
            $this->user->hasPermission('EditAuthors')
        );
    }

    public function __construct(FactoryInterface $factory, $container)
    {
        $this->factory = $factory;
        $this->container = $container;
    }

    public function mainMenu()
    {
        camp_load_translation_strings('home');
        $this->user  = $this->container->getService('user')->getCurrentUser();
        $this->preparePrivileges();

        $menu = $this->factory->createItem('root');
        $menu->setChildrenAttribute('class', 'navigation');

        // change menu for blogger
        $blogService = $this->container->get('blog');
        if ($blogService->isBlogger($this->user)) {
             $menu->addChild('Blog', array('uri' => $this->generateZendRoute('admin'), 'attributes' => array(
                'data-menu' => 'not-menu'
            )));

            $menu = $this->decorateMenu($menu);
            return $menu;
        }

        $menu->addChild(getGS('Dashboard'), array('uri' => $this->generateZendRoute('admin'), 'attributes' => array(
            'data-menu' => 'not-menu'
        )));

        $menu->addChild(getGS('Content'), array('uri' => '#'));
        $this->prepareContentMenu($menu[getGS('Content')]);

        $menu->addChild(getGS('Actions'), array('uri' => '#'));
        $this->prepareActionsMenu($menu[getGS('Actions')]);

        if ($this->showConfigureMenu) {
            $menu->addChild(getGS('Configure'), array('uri' => '#'));
            $this->prepareConfigureMenu($menu[getGS('Configure')]);
        }

        if ($this->showUserMenu) {
            $menu->addChild(getGS('Users'), array('uri' => '#'));
            $this->prepareUsersMenu($menu[getGS('Users')]);
        }


        $this->preparePluginsMenu($menu);

        // Extend menu with events
        $this->container->get('event_dispatcher')->dispatch('newscoop_newscoop.menu_configure', new ConfigureMenuEvent(
            $this->factory, 
            $menu, 
            $this->container->get('router')
        ));

        $menu = $this->decorateMenu($menu);

        return $menu;
    }

    public function mainBreadcrumb()
    {
        $menu = $this->container->get('newscoop_newscoop.menu.main');

        $matcher = new \Knp\Menu\Matcher\Matcher();
        $matcher->addVoter(new \Knp\Menu\Matcher\Voter\UriVoter($_SERVER['REQUEST_URI']));

        $treeIterator = new \RecursiveIteratorIterator(
            new \Knp\Menu\Iterator\RecursiveItemIterator(
                new \ArrayIterator(array($menu))
            ),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        $iterator = new \Knp\Menu\Iterator\CurrentItemFilterIterator($treeIterator, $matcher);

        // Set Current as an empty Item in order to avoid exceptions on knp_menu_get
        $current = new \Knp\Menu\MenuItem('', $this->factory);

        foreach ($iterator as $item) {
            $item->setCurrent(true);
            $current = $item;
            break;
        }

        return $current;
    }

    private function decorateMenu($menu) {
        foreach ($menu as $key => $value) {
            $value->setLinkAttribute('class', 'fg-button ui-widget fg-button-icon-right fg-button-ui-state-default fg-button-ui-corner-all');
        }

        return $menu;
    }

    private function prepareContentMenu($menu) {
       $this->addChild($menu, getGS('Publications'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'pub',
                'action' => 'index.php',
            ),
            'resource' => 'publication',
            'privilege' => 'manage',
        ));

        $this->addChild($menu, getGS('Comments'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'comment',
                'action' => 'index',
            ),
            'resource' => 'comment',
            'privilege' => 'moderate',
        ));

        $this->addChild($menu, getGS('Feedback'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'feedback',
                'action' => 'index',
            ),
            'resource' => 'feedback',
            'privilege' => 'manage',
        ));

        $this->addChild($menu, getGS('Media Archive'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'media-archive',
                'action' => 'index.php',
            )
        ));

        $this->addChild($menu, getGS('Search'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'universal-list',
                'action' => 'index.php',
            )
        ));

        $this->addChild($menu, getGS('Pending articles'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'pending_articles',
                'action' => 'index.php',
            )
        ));

        $this->addChild($menu, getGS('Featured Article Lists'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'playlist',
                'action' => 'index',
            ),
            'resource' => 'playlist',
            'privilege' => 'manage'
        ));

        // add content/publications
        $publicationService = $this->container->get('content.publication');
        foreach ($publicationService->findAll() as $publication) {
            $pubId = $publication->getId();
            $this->addChild($menu, $publication->getName(), array('uri' => $this->generateZendRoute('admin') . "/issues/?Pub=$pubId"));

            // add content/publication/issue
            foreach ($publication->getIssues() as $issue) {
                $issueId = $issue->getNumber();
                $languageId = $issue->getLanguage()->getId();
                $issueName = sprintf('%d. %s (%s)', $issue->getNumber(), $issue->getName(), $issue->getLanguage()->getName());
                $this->addChild(
                    $menu[$publication->getName()], 
                    $issueName, 
                    array('uri' => $this->generateZendRoute('admin', array('zend_route' => array('reset_params' => true))) . "/sections/?Pub=$pubId&Issue=$issueId&Language=$languageId"
                ));

                // add content/publication/issue/section
                    foreach ($issue->getSections() as $section) {
                        $sectionId = $section->getNumber();
                        $sectionName = sprintf('%d. %s', $section->getNumber(), $section->getName());
                        $this->addChild(
                            $menu[$publication->getName()][$issueName],
                            $sectionName, 
                            array('uri' => $this->generateZendRoute('admin', array('zend_route' => array('reset_params' => true))) . "/articles/?f_publication_id=$pubId&f_issue_number=$issueId&f_language_id=$languageId&f_section_number=$sectionId"
                        ));
                    }
                    if (count($issue->getSections()) > 0) {
                        $this->addChild(
                            $menu[$publication->getName()][$issueName],
                            getGS('More...'), 
                            array('uri' => $this->generateZendRoute('admin', array('zend_route' => array('reset_params' => true))) . "/sections/?Pub=$pubId&Issue=$issueId&Language=$languageId"
                        ));
                    }
            }
                
            if (count($publication->getIssues()) > 0) {
                $this->addChild(
                    $menu[$publication->getName()],
                    getGS('More...'), 
                    array('uri' => $this->generateZendRoute('admin', array('zend_route' => array('reset_params' => true))) . "/issues/?Pub=$pubId"
                ));
            }
        }    
    }

    private function prepareActionsMenu($menu) 
    {

        $this->addChild($menu, getGS('Add new article'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'articles',
                'action' => 'add_move.php',
            ),
            'resource' => 'article',
            'privilege' => 'add',
        ));

        $this->addChild($menu, getGS('Add new publication'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'pub',
                'action' => 'add.php',
            ),
            'resource' => 'publication',
            'privilege' => 'manage',
        ));

        $this->addChild($menu, getGS('Add new user'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'user',
                'action' => 'create'
            ),
            'resource' => 'user',
            'privilege' => 'manage',
        ));

        $this->addChild($menu, getGS('Add new user type'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'user-group',
                'action' => 'add'
            ),
            'resource' => 'user-group',
            'privilege' => 'manage',
        ));

        $this->addChild($menu, getGS('Add new article type'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'article_types',
                'action' => 'add.php',
                'reset_params' => false
            ),
            'resource' => 'article-type',
            'privilege' => 'manage',
        ));

        $this->addChild($menu, getGS('Merge article types'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'article_types',
                'action' => 'merge.php',
                'reset_params' => false
            ),
            'resource' => 'article-type',
            'privilege' => 'manage',
        ));

        $this->addChild($menu[getGS('Merge article types')], getGS('Step 2'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'article_types',
                'action' => 'merge2.php',
                'reset_params' => false
            ),
            'resource' => 'article-type',
            'privilege' => 'manage',
        ));
        $menu[getGS('Merge article types')][getGS('Step 2')]->setDisplay(false);

        $this->addChild($menu[getGS('Merge article types')], getGS('Step 3'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'article_types',
                'action' => 'merge3.php',
                'reset_params' => false
            ),
            'resource' => 'article-type',
            'privilege' => 'manage',
        ));
        $menu[getGS('Merge article types')][getGS('Step 3')]->setDisplay(false);

        if($this->user->hasPermission('ManageCountries') || $this->user->hasPermission('DeleteCountries')) {
            $this->addChild($menu, getGS('Countries'), array('zend_route' => array(
                    'module' => 'admin',
                    'controller' => 'country',
                    'action' => 'index.php',
                )
            ));
            $menu[getGS('Countries')]->setDisplay(false);

            $this->addChild($menu[getGS('Countries')], getGS('Add new country'), array('zend_route' => array(
                    'module' => 'admin',
                    'controller' => 'country',
                    'action' => 'add.php'
                ),
                'resource' => 'country',
                'privilege' => 'manage',
            ));

            $this->addChild($menu[getGS('Countries')], getGS('Edit country'), array('zend_route' => array(
                    'module' => 'admin',
                    'controller' => 'country',
                    'action' => 'edit.php',
                    'reset_params' => false
                ),
                'resource' => 'country',
                'privilege' => 'manage',
            ));
            $menu[getGS('Countries')][getGS('Edit country')]->setDisplay(false);
        }

        $this->addChild($menu, getGS('Edit your password'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'user',
                'action' => 'edit-password'
            )
        ));

        if ($this->user->hasPermission('ManageIssue') && $this->user->hasPermission('AddArticle')) {
            $this->addChild($menu, getGS('Import XML'), array('zend_route' => array(
                    'module' => 'admin',
                    'controller' => 'articles',
                    'action' => 'la_import.php'
                )
            ));
        }

        $this->addChild($menu, getGS('Backup/Restore'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'backup.php',
                'action' => null
            ),
            'resource' => 'backup',
            'privilege' => 'manage',
        ));

        if (\CampCache::IsEnabled() && $this->user->hasPermission('ClearCache')) {
            $this->addChild(
                $menu,
                getGS('Clear system cache'), 
                array('uri' => $this->generateZendRoute('admin') . "/?clear_cache=yes"
            ));
        }
    }

    private function prepareConfigureMenu($menu) 
    {
        $this->addChild($menu, getGS('System Preferences'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'preferences',
                'action' => 'index'
            ),
            'resource' => 'system-preferences',
            'privilege' => 'edit',
        ));

        $this->addChild($menu, getGS('Templates'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'template',
                'action' => 'index'
            )
        ));
        $menu[getGS('Templates')]->setDisplay(false);

        $this->addChild($menu[getGS('Templates')], getGS('Edit'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'template',
                'action' => 'edit'
            )
        ));
        $menu[getGS('Templates')][getGS('Edit')]->setDisplay(false);

        $this->addChild($menu[getGS('Templates')], getGS('Upload'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'template',
                'action' => 'upload'
            )
        ));
        $menu[getGS('Templates')][getGS('Upload')]->setDisplay(false);

        $this->addChild($menu, getGS('Themes'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'themes',
                'action' => null
            ),
            'resource' => 'theme',
            'privilege' => 'manage'
        ));

        $this->addChild($menu[getGS('Themes')], getGS('Settings'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'themes',
                'action' => 'advanced-theme-settings',
                'params' => array(
                    'next' => null,
                    'file' => null,
                ),
                'reset_params' => false
            )
        ));
        $menu[getGS('Themes')][getGS('Settings')]->setDisplay(false);

        $this->addChild($menu[getGS('Themes')][getGS('Settings')], getGS('Upload'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'template',
                'action' => 'upload',
                'reset_params' => false
            )
        ));
        $menu[getGS('Themes')][getGS('Settings')][getGS('Upload')]->setDisplay(false);

        $this->addChild($menu[getGS('Themes')][getGS('Settings')], getGS('Edit'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'template',
                'action' => 'edit',
                'reset_params' => false
            )
        ));
        $menu[getGS('Themes')][getGS('Settings')][getGS('Edit')]->setDisplay(false);

        if($this->user->hasPermission('DeleteArticleTypes')) {
            $this->addChild($menu, getGS('Article Types'), array('zend_route' => array(
                    'module' => 'admin',
                    'controller' => 'article_types',
                    'action' => 'index.php',
                ),
                'resource' => 'article-type',
                'privilege' => 'manage'
            ));
        }

        $this->addChild($menu, getGS('Topics'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'topics',
                'action' => 'index.php',
            ),
            'resource' => 'topic',
            'privilege' => 'manage'
        ));

        $this->addChild($menu, getGS('Languages'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'languages',
                'action' => null,
            ),
            'resource' => 'language',
            'privilege' => 'manage'
        ));

        $this->addChild($menu[getGS('Languages')], getGS('Edit language'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'languages',
                'action' => 'edit',
                'reset_params' => false
            ),
            'resource' => 'language',
            'privilege' => 'manage'
        ));
        $menu[getGS('Languages')][getGS('Edit language')]->setDisplay(false);

        $this->addChild($menu[getGS('Languages')], getGS('Add new language'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'languages',
                'action' => 'add',
            ),
            'resource' => 'language',
            'privilege' => 'manage'
        ));
        $menu[getGS('Languages')][getGS('Add new language')]->setDisplay(false);

        if($this->user->hasPermission('ManageCountries') || $this->user->hasPermission('DeleteCountries')) {
            $this->addChild($menu, getGS('Countries'), array('zend_route' => array(
                    'module' => 'admin',
                    'controller' => 'country',
                    'action' => 'index.php',
                )
            ));
        }

        if($this->user->hasPermission('ManageLocalizer')) {
            $this->addChild($menu, getGS('Localizer'), array('zend_route' => array(
                    'module' => 'admin',
                    'controller' => 'localizer',
                    'action' => 'index.php',
                )
            ));
        }

        if($this->user->hasPermission('ViewLogs')) {
            $this->addChild($menu, getGS('Logs'), array('zend_route' => array(
                    'module' => 'admin',
                    'controller' => 'log',
                    'action' => null
                ),
                'resource' => 'log',
                'privilege' => 'view'
            ));
        }

        $this->addChild($menu, getGS('Support'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'support',
                'action' => null,
                'params' => array(
                    'id' => 'stat',
                )
            )
        ));

        $this->addChild($menu, getGS('Image Rendering'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'rendition',
                'action' => null
            )
        ));
    }

    private function prepareUsersMenu($menu) 
    {
        $this->addChild($menu, getGS('Manage Users'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'user',
                'action' => 'index',
                'params' => array('user' => null),
            )
        ));

        $this->addChild($menu[getGS('Manage Users')], getGS('Edit user'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'user',
                'action' => 'edit',
                'reset_params' => false
            )
        ));
        $menu[getGS('Manage Users')][getGS('Edit user')]->setDisplay(false);

        $this->addChild($menu[getGS('Manage Users')][getGS('Edit user')], getGS('Edit permissions'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'acl',
                'action' => 'edit'
            )
        ));
        $menu[getGS('Manage Users')][getGS('Edit user')][getGS('Edit permissions')]->setDisplay(false);

        $this->addChild($menu[getGS('Manage Users')], getGS('Rename user'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'user',
                'action' => 'rename',
                'reset_params' => false
            )
        ));
        $menu[getGS('Manage Users')][getGS('Rename user')]->setDisplay(false);

        $this->addChild($menu[getGS('Manage Users')], getGS('Create new user'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'user',
                'action' => 'create',
                'reset_params' => false
            )
        ));
        $menu[getGS('Manage Users')][getGS('Create new user')]->setDisplay(false);

        $this->addChild($menu, getGS('Manage Authors'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'users',
                'action' => 'authors.php',
            )
        ));

        $this->addChild($menu, getGS('Manage Authors'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'users',
                'action' => 'authors.php',
            ),
            'resource' => 'autors',
            'privilege' => 'edit',
        ));

        $this->addChild($menu, getGS('Manage User Types'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'user-group',
                'action' => null,
            ),
            'resource' => 'user-group',
            'privilege' => 'manage',
        ));

        $this->addChild($menu[getGS('Manage User Types')], getGS('Add new user type'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'user-group',
                'action' => 'add',
            )
        ));
        $menu[getGS('Manage User Types')][getGS('Add new user type')]->setDisplay(false);

        $this->addChild($menu[getGS('Manage User Types')], getGS('Edit user type'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'user-group',
                'action' => 'edit-access',
                'reset_params' => false
            )
        ));
        $menu[getGS('Manage User Types')][getGS('Edit user type')]->setDisplay(false);

        $this->addChild($menu, getGS('Create new account'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'user',
                'action' => 'create',
            )
        ));
    }

    public function preparePluginsMenu($menu)
    {
        $root_menu = false;
        $plugin_infos = \CampPlugin::GetPluginsInfo(true);
        if ($this->user->hasPermission('plugin_manager')) {
            $root_menu = true;
        }

        foreach ($plugin_infos as $info) {
            if (isset($info['menu']['permission']) && $this->user->hasPermission($info['menu']['permission'])) {
                $root_menu = true;
            } elseif (isset($info['menu']['sub']) && is_array($info['menu']['sub'])) {
                foreach ($info['menu']['sub'] as $menu_info) {
                    if ($this->user->hasPermission($menu_info['permission'])) {
                        $root_menu = true;
                    }
                }
            }
        }

        if (!$root_menu) {
            return;
        }

        $menu->addChild(getGS('Plugins'), array('uri' => '#'));

        if ($this->user->hasPermission('plugin_manager')) {
            $this->addChild($menu[getGS('Plugins')], getGS('Manage Plugins'), array('zend_route' => array(
                    'module' => 'admin',
                    'controller' => 'plugins',
                    'action' => 'manage.php',
                )
            ));
        }
        
        foreach ($plugin_infos as $info) {
            if (\CampPlugin::IsPluginEnabled($info['name'])) {
                $parent_menu = false;

                $Plugin = new \CampPlugin($info['name']);

                if (isset($info['menu']['permission']) && $this->user->hasPermission($info['menu']['permission'])) {
                    $parent_menu = true;
                } elseif (isset($info['menu']['sub']) && is_array($info['menu']['sub'])) {
                    foreach ($info['menu']['sub'] as $menu_info) {
                        if ($this->user->hasPermission($menu_info['permission'])) {
                            $parent_menu = true;
                        }
                    }
                }

                if ($parent_menu && isset($info['menu'])) {
                    $uri = '#';
                    if (isset($info['menu']['path'])) {
                        $uri = $this->generateZendRoute('admin') .'/'. $info['menu']['path'];
                    }

                    $this->addChild($menu[getGS('Plugins')], getGS($info['menu']['label']), array(
                        'uri' => $uri
                    ));
                }

                if (isset($info['menu']['sub']) && is_array($info['menu']['sub'])) {
                    foreach ($info['menu']['sub'] as $menu_info) {
                        if ($this->user->hasPermission($menu_info['permission'])) {
                            $uri = '#';
                            if (isset($menu_info['path'])) {
                                $uri = $this->generateZendRoute('admin') .'/'. $menu_info['path'];
                            }

                            $this->addChild($menu[getGS('Plugins')][getGS($info['menu']['label'])], getGS($menu_info['label']), array(
                                'uri' => $uri
                            ));
                        }
                    }
                }
            }
        }

        return $menu;
    }

    private function addChild($menu, $name, $element) {
        if(array_key_exists('resource', $element) && array_key_exists('privilege', $element)) {
            if (!$this->hasPermission($element['resource'], $element['privilege'])) {
                return;
            }
        }

        if(array_key_exists('zend_route', $element)) {
            $element['uri'] = $this->generateZendRoute($element['zend_route']['module'], $element); 
        }

        return $menu->addChild($name, $element);
    }

    private function hasPermission($resource, $action) 
    {
        return $this->user->hasPermission(null, $resource, $action);
    }

    private function generateZendRoute($module, $element = array()) 
    {
        if(!array_key_exists('zend_route', $element)) {
            $element['zend_route'] = array();
        }

        $zendRouteParams = $element['zend_route'];
        $params = array_key_exists('params', $zendRouteParams) == true ? $zendRouteParams['params'] : array();
        $controller = array_key_exists('controller', $zendRouteParams) == true ? $zendRouteParams['controller'] : null;
        $action = array_key_exists('action', $zendRouteParams) == true ? $zendRouteParams['action'] : null;
        $module = array_key_exists('module', $zendRouteParams) == true ? $zendRouteParams['module'] : $module;
        $reset_params = array_key_exists('reset_params', $zendRouteParams) == true ? $zendRouteParams['reset_params'] : true;

        $get_params = '';
        if (count($_GET) > 0 && !$reset_params) {
            $get_params = '?'.http_build_query($_GET);
        }

        return $this->container->get('zend_router')->assemble(array(
            'controller' => $controller,
            'action' => $action,
            'module' => $module
        ) + $params, 'default', $reset_params).$get_params;
    }
}
