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

    protected $factory;
    protected $container;
    protected $user;
    protected $showPublishingEnvironmentMenu;
    protected $showConfigureMenu;
    protected $showUserMenu;
    protected $menu = null;

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

    public function mainMenu($modern = false)
    {
        if ($this->menu !== null) {
            return $this->menu;
        }

        $translator = $this->container->get('translator');
        $this->user = $this->container->getService('user')->getCurrentUser();
        $this->preparePrivileges();

        $menu = $this->factory->createItem('root');

        if ($modern) {
            $menu->setChildrenAttribute('class', 'nav navbar-nav');
        } else {
            $menu->setChildrenAttribute('class', 'navigation');
        }

        // change menu for blogger
        $blogService = $this->container->get('blog');
        if ($blogService->isBlogger($this->user)) {
            $menu->addChild('Blog', array('uri' => $this->generateZendRoute('admin'), 'attributes' => array(
                'data-menu' => 'not-menu',
            )));

            if (!$modern) {
                $menu = $this->decorateMenu($menu);
            }

            return $menu;
        }

        if ($modern) {
            $menu->addChild($translator->trans('Dashboard', array(), 'home'), array('uri' => $this->generateZendRoute('admin')));

            $menu->addChild($translator->trans('Content'), array('uri' => '#'))
                ->setAttribute('dropdown', true)
                ->setLinkAttribute('data-toggle', 'dropdown');

            $this->prepareContentMenu($menu[$translator->trans('Content')], $modern);

            $menu->addChild($translator->trans('Actions'), array('uri' => '#'))
                ->setAttribute('dropdown', true)
                ->setLinkAttribute('data-toggle', 'dropdown');

            $this->prepareActionsMenu($menu[$translator->trans('Actions')]);

            if ($this->showConfigureMenu) {
                $menu->addChild($translator->trans('Configure'), array('uri' => '#'))
                    ->setAttribute('dropdown', true)
                    ->setLinkAttribute('data-toggle', 'dropdown');
                $this->prepareConfigureMenu($menu[$translator->trans('Configure')]);
            }

            if ($this->showUserMenu) {
                $menu->addChild($translator->trans('Users'), array('uri' => '#'))
                    ->setAttribute('dropdown', true)
                    ->setLinkAttribute('data-toggle', 'dropdown');
                $this->prepareUsersMenu($menu[$translator->trans('Users')]);
            }
        } else {
            $menu->addChild($translator->trans('Dashboard', array(), 'home'), array('uri' => $this->generateZendRoute('admin')));

            $menu->addChild($translator->trans('Content'), array('uri' => '#'));
            $this->prepareContentMenu($menu[$translator->trans('Content')], $modern);

            $menu->addChild($translator->trans('Actions'), array('uri' => '#'));
            $this->prepareActionsMenu($menu[$translator->trans('Actions')]);

            if ($this->showConfigureMenu) {
                $menu->addChild($translator->trans('Configure'), array('uri' => '#'));
                $this->prepareConfigureMenu($menu[$translator->trans('Configure')]);
            }

            if ($this->showUserMenu) {
                $menu->addChild($translator->trans('Users'), array('uri' => '#'));
                $this->prepareUsersMenu($menu[$translator->trans('Users')]);
            }
        }

        $this->preparePluginsMenu($menu);

        // Extend menu with events
        $this->container->get('event_dispatcher')->dispatch('newscoop_newscoop.menu_configure', new ConfigureMenuEvent(
            $this->factory,
            $menu,
            $this->container->get('router')
        ));

        if (!$modern) {
            $menu = $this->decorateMenu($menu);
        }

        $this->menu = $menu;

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

    private function decorateMenu($menu)
    {
        foreach ($menu as $key => $value) {
            $value->setLinkAttribute('class', 'fg-button fg-button-menu ui-widget fg-button-icon-right fg-button-ui-state-default fg-button-ui-corner-all');
        }

        return $menu;
    }

    private function prepareContentMenu($menu, $modern)
    {
        $translator = $this->container->get('translator');

        $this->addChild($menu, $translator->trans('Publications'), array(
            'uri' => $this->container->get('router')->generate('newscoop_newscoop_publications_index'),
            'resource' => 'publication',
            'privilege' => 'manage',
        ));

        $publicationId = $this->container->get('request')->get('id', 1);
        if (!is_numeric($publicationId)) {
            $publicationId = 1;
        }

        $this->addChild($menu[$translator->trans('Publications')], $translator->trans('publications.title.edit', array(), 'pub'), array(
            'route' => 'newscoop_newscoop_publications_edit',
            'routeParameters' => array('id' => $publicationId),
            'resource' => 'publication',
            'privilege' => 'manage',
        ))->setDisplay(false);

        $this->addChild($menu[$translator->trans('Publications')], $translator->trans('publications.title.remove', array(), 'pub'), array(
            'route' => 'newscoop_newscoop_publications_remove',
            'routeParameters' => array('id' => $publicationId),
            'resource' => 'publication',
            'privilege' => 'manage',
        ))->setDisplay(false);

        $this->addChild(
            $menu,
            $translator->trans('comments.label.menu', array(), 'new_comments'),
            array('uri' => $this->container->get('router')->generate('newscoop_newscoop_comments_index'))
        );

        $this->addChild($menu, $translator->trans('Feedback', array(), 'home'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'feedback',
                'action' => 'index',
            ),
            'resource' => 'feedback',
            'privilege' => 'manage',
        ));

        $this->addChild($menu, $translator->trans('Media Archive', array(), 'home'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'media-archive',
                'action' => 'index.php',
            ),
        ));

        $this->addChild($menu, $translator->trans('Search'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'universal-list',
                'action' => 'index.php',
            ),
        ));

        $this->addChild($menu, $translator->trans('Pending articles', array(), 'home'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'pending_articles',
                'action' => 'index.php',
            ),
        ));

        $this->addChild($menu, $translator->trans('Featured Article Lists', array(), 'home'), array(
            'uri' => $this->container->get('router')->generate('newscoop_newscoop_playlists_index'),
            'resource' => 'playlist',
            'privilege' => 'manage',
        ));

        // add content/publications
        $publicationService = $this->container->get('content.publication');
        foreach ($publicationService->getPublicationsForMenu()->getArrayResult() as $publication) {
            $pubId = $publication['id'];
            $this->addChild($menu, $publication['name'], array('uri' => $this->generateZendRoute('admin')."/issues/?Pub=$pubId"))
                ->setAttribute('rightdrop', true)
                ->setLinkAttribute('data-toggle', 'rightdrop');

            $query = $this->container->get('em')
                ->getRepository('Newscoop\Entity\Issue')
                ->getLatestByPublication($pubId, 11);

            if ($query) {
                $issues = $query->getArrayResult();
            } else {
                $issues = array();
            }

            $moreIssues = count($issues) > 10 ? true : false;
            if ($moreIssues) {
                unset($issues[10]);
            }

            // add content/publication/issue
            $latestPublished = false;
            foreach ($issues as $issue) {
                $issueId = $issue['number'];
                $languageId = $issue['language']['id'];
                $issueName = sprintf('%d. %s (%s)', $issue['number'], $issue['name'], $issue['language']['name']);
                $issueChild = $this->addChild(
                    $menu[$publication['name']],
                    $issueName,
                    array('uri' => $this->generateZendRoute('admin', array('zend_route' => array('reset_params' => true)))."/sections/?Pub=$pubId&Issue=$issueId&Language=$languageId",
                ))->setAttribute('rightdrop', true)
                ->setLinkAttribute('data-toggle', 'rightdrop');

                if (($issue['workflowStatus'] === 'Y' ? true : false) && !$latestPublished) {
                    $issueChild->setLinkAttribute('class', 'latest-published');
                    $latestPublished = true;
                }

                // add content/publication/issue/section
                    $firstSections = array();
                foreach ($issue['sections'] as $section) {
                    $firstSections[$section['number']] = $section;
                }

                ksort($firstSections);
                $sectionsCounter = 0;
                foreach ($firstSections as $section) {
                    if ($sectionsCounter < 10) {
                        $sectionId = $section['number'];
                        $sectionName = sprintf('%d. %s', $section['number'], $section['name']);
                        $this->addChild(
                                $menu[$publication['name']][$issueName],
                                $sectionName,
                                array('uri' => $this->generateZendRoute('admin', array('zend_route' => array('reset_params' => true)))."/articles/?f_publication_id=$pubId&f_issue_number=$issueId&f_language_id=$languageId&f_section_number=$sectionId",
                            ));
                        $sectionsCounter ++;
                    }
                }

                if (count($issue['sections']) > 0) {
                    if (!$modern) {
                        $this->addChild($menu[$publication['name']][$issueName], null, array())->setAttribute('class', 'divider');
                        $this->addChild(
                                $menu[$publication['name']][$issueName],
                                $translator->trans('More...'),
                                array('uri' => $this->generateZendRoute('admin', array('zend_route' => array('reset_params' => true)))."/sections/?Pub=$pubId&Issue=$issueId&Language=$languageId",
                            ));
                    } else {
                        $this->addChild(
                                $menu[$publication['name']][$issueName],
                                $translator->trans('More...'),
                                array('uri' => $this->generateZendRoute('admin', array('zend_route' => array('reset_params' => true)))."/sections/?Pub=$pubId&Issue=$issueId&Language=$languageId",
                            ))->setAttribute('divider_prepend', true);
                    }
                }
            }

            if ($moreIssues) {
                if (!$modern) {
                    $this->addChild($menu[$publication['name']], null, array())->setAttribute('class', 'divider');
                    $this->addChild(
                        $menu[$publication['name']],
                        $translator->trans('More...'),
                        array('uri' => $this->generateZendRoute('admin', array('zend_route' => array('reset_params' => true)))."/issues/?Pub=$pubId",
                    ));
                } else {
                    $this->addChild(
                        $menu[$publication['name']],
                        $translator->trans('More...'),
                        array('uri' => $this->generateZendRoute('admin', array('zend_route' => array('reset_params' => true)))."/issues/?Pub=$pubId",
                    ))->setAttribute('divider_prepend', true);
                }
            }
        }
    }

    private function prepareActionsMenu($menu)
    {
        $translator = $this->container->get('translator');

        $this->addChild($menu, $translator->trans('Add new article'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'articles',
                'action' => 'add_move.php',
            ),
            'resource' => 'article',
            'privilege' => 'add',
        ));

        $this->addChild($menu, $translator->trans('publications.title.add', array(), 'pub'), array(
            'uri' => $this->container->get('router')->generate('newscoop_newscoop_publications_add'),
            'resource' => 'publication',
            'privilege' => 'manage',
        ));

        $this->addChild($menu, $translator->trans('Add new user', array(), 'home'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'user',
                'action' => 'create',
            ),
            'resource' => 'user',
            'privilege' => 'manage',
        ));

        $this->addChild($menu, $translator->trans('Add new user type', array(), 'home'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'user-group',
                'action' => 'add',
            ),
            'resource' => 'user-group',
            'privilege' => 'manage',
        ));

        $this->addChild($menu, $translator->trans('Add new article type'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'article_types',
                'action' => 'add.php',
                'reset_params' => false,
            ),
            'resource' => 'article-type',
            'privilege' => 'manage',
        ));

        $this->addChild($menu, $translator->trans('Merge article types', array(), 'article_types'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'article_types',
                'action' => 'merge.php',
                'reset_params' => false,
            ),
            'resource' => 'article-type',
            'privilege' => 'manage',
        ));

        $status = $this->addChild($menu[$translator->trans('Merge article types', array(), 'article_types')], $translator->trans('Step 2'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'article_types',
                'action' => 'merge2.php',
                'reset_params' => false,
            ),
            'resource' => 'article-type',
            'privilege' => 'manage',
        ));

        if ($status) {
            $menu[$translator->trans('Merge article types', array(), 'article_types')][$translator->trans('Step 2', array(), 'article_types')]->setDisplay(false);
        }

        $status = $this->addChild($menu[$translator->trans('Merge article types', array(), 'article_types')], $translator->trans('Step 3', array(), 'article_types'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'article_types',
                'action' => 'merge3.php',
                'reset_params' => false,
            ),
            'resource' => 'article-type',
            'privilege' => 'manage',
        ));

        if ($status) {
            $menu[$translator->trans('Merge article types', array(), 'article_types')][$translator->trans('Step 3', array(), 'article_types')]->setDisplay(false);
        }

        if ($this->user->hasPermission('ManageCountries') || $this->user->hasPermission('DeleteCountries')) {
            $status = $this->addChild($menu, $translator->trans('Countries'), array('zend_route' => array(
                    'module' => 'admin',
                    'controller' => 'country',
                    'action' => 'index.php',
                ),
            ));

            if ($status) {
                $menu[$translator->trans('Countries')]->setDisplay(false);
            }

            $this->addChild($menu[$translator->trans('Countries')], $translator->trans('Add new country'), array('zend_route' => array(
                    'module' => 'admin',
                    'controller' => 'country',
                    'action' => 'add.php',
                ),
                'resource' => 'country',
                'privilege' => 'manage',
            ));

            $status = $this->addChild($menu[$translator->trans('Countries')], $translator->trans('Edit country'), array('zend_route' => array(
                    'module' => 'admin',
                    'controller' => 'country',
                    'action' => 'edit.php',
                    'reset_params' => false,
                ),
                'resource' => 'country',
                'privilege' => 'manage',
            ));

            if ($status) {
                $menu[$translator->trans('Countries')][$translator->trans('Edit country')]->setDisplay(false);
            }
        }

        $this->addChild($menu, $translator->trans('Edit your password', array(), 'home'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'user',
                'action' => 'edit-password',
            ),
        ));

        if ($this->user->hasPermission('ManageIssue') && $this->user->hasPermission('AddArticle')) {
            $this->addChild($menu, $translator->trans('Import XML', array(), 'home'), array('zend_route' => array(
                    'module' => 'admin',
                    'controller' => 'articles',
                    'action' => 'la_import.php',
                ),
            ));
        }

        $this->addChild($menu, $translator->trans('Backup/Restore', array(), 'home'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'backup.php',
                'action' => null,
            ),
            'resource' => 'backup',
            'privilege' => 'manage',
        ));

        if (\CampCache::IsEnabled() && $this->user->hasPermission('ClearCache')) {
            $this->addChild(
                $menu,
                $translator->trans('Clear system cache', array(), 'home'),
                array('uri' => $this->generateZendRoute('admin')."/?clear_cache=yes",
            ));
        }
    }

    private function prepareConfigureMenu($menu)
    {
        $translator = $this->container->get('translator');

        $this->addChild($menu, $translator->trans('System Preferences'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'preferences',
                'action' => 'index',
            ),
            'resource' => 'system-preferences',
            'privilege' => 'edit',
        ));

        $status = $this->addChild($menu, $translator->trans('Templates', array(), 'home'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'template',
                'action' => 'index',
            ),
        ));

        if ($status) {
            $menu[$translator->trans('Templates', array(), 'home')]->setDisplay(false);
        }

        $status = $this->addChild($menu[$translator->trans('Templates', array(), 'home')], $translator->trans('Edit'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'template',
                'action' => 'edit',
            ),
        ));

        if ($status) {
            $menu[$translator->trans('Templates', array(), 'home')][$translator->trans('Edit')]->setDisplay(false);
        }

        $status = $this->addChild($menu[$translator->trans('Templates', array(), 'home')], $translator->trans('Upload', array(), 'home'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'template',
                'action' => 'upload',
            ),
        ));

        if ($status) {
            $menu[$translator->trans('Templates', array(), 'home')][$translator->trans('Upload', array(), 'home')]->setDisplay(false);
        }

        $this->addChild($menu, $translator->trans('Themes', array(), 'home'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'themes',
                'action' => null,
            ),
            'resource' => 'theme',
            'privilege' => 'manage',
        ));

        $status = $this->addChild($menu[$translator->trans('Themes', array(), 'home')], $translator->trans('Settings', array(), 'home'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'themes',
                'action' => 'advanced-theme-settings',
                'params' => array(
                    'next' => null,
                    'file' => null,
                ),
                'reset_params' => false,
            ),
        ));

        if ($status) {
            $menu[$translator->trans('Themes', array(), 'home')][$translator->trans('Settings', array(), 'home')]->setDisplay(false);
        }

        $status = $this->addChild($menu[$translator->trans('Themes', array(), 'home')][$translator->trans('Settings', array(), 'home')], $translator->trans('Upload', array(), 'home'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'template',
                'action' => 'upload',
                'reset_params' => false,
            ),
        ));

        if ($status) {
            $menu[$translator->trans('Themes', array(), 'home')][$translator->trans('Settings', array(), 'home')][$translator->trans('Upload', array(), 'home')]->setDisplay(false);
        }

        $status = $this->addChild($menu[$translator->trans('Themes', array(), 'home')][$translator->trans('Settings', array(), 'home')], $translator->trans('Edit'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'template',
                'action' => 'edit',
                'reset_params' => false,
            ),
        ));

        if ($status) {
            $menu[$translator->trans('Themes', array(), 'home')][$translator->trans('Settings', array(), 'home')][$translator->trans('Edit')]->setDisplay(false);
        }

        if ($this->user->hasPermission('DeleteArticleTypes')) {
            $this->addChild($menu, $translator->trans('Article Types'), array('zend_route' => array(
                    'module' => 'admin',
                    'controller' => 'article_types',
                    'action' => 'index.php',
                ),
                'resource' => 'article-type',
                'privilege' => 'manage',
            ));
        }

        $this->addChild($menu, $translator->trans('Topics'), array(
            'uri' => $this->container->get('router')->generate('newscoop_newscoop_topics_index'),
            'resource' => 'topic',
            'privilege' => 'manage',
        ));

        $this->addChild($menu, $translator->trans('Languages'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'languages',
                'action' => null,
            ),
            'resource' => 'language',
            'privilege' => 'manage',
        ));

        $status = $this->addChild($menu[$translator->trans('Languages')], $translator->trans('Edit language', array(), 'home'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'languages',
                'action' => 'edit',
                'reset_params' => false,
            ),
            'resource' => 'language',
            'privilege' => 'manage',
        ));

        if ($status) {
            $menu[$translator->trans('Languages')][$translator->trans('Edit language', array(), 'home')]->setDisplay(false);
        }

        $status = $this->addChild($menu[$translator->trans('Languages')], $translator->trans('Add new language'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'languages',
                'action' => 'add',
            ),
            'resource' => 'language',
            'privilege' => 'manage',
        ));

        if ($status) {
            $menu[$translator->trans('Languages')][$translator->trans('Add new language')]->setDisplay(false);
        }

        if ($this->user->hasPermission('ManageCountries') || $this->user->hasPermission('DeleteCountries')) {
            $this->addChild($menu, $translator->trans('Countries'), array('zend_route' => array(
                    'module' => 'admin',
                    'controller' => 'country',
                    'action' => 'index.php',
                ),
            ));
        }

        if ($this->user->hasPermission('ViewLogs')) {
            $this->addChild($menu, $translator->trans('Logs'), array('zend_route' => array(
                    'module' => 'admin',
                    'controller' => 'log',
                    'action' => null,
                ),
                'resource' => 'log',
                'privilege' => 'view',
            ));
        }

        $this->addChild($menu, $translator->trans('Support', array(), 'home'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'support',
                'action' => null,
                'params' => array(
                    'id' => 'stat',
                ),
            ),
        ));

        $this->addChild($menu, $translator->trans('Image Rendering', array(), 'home'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'rendition',
                'action' => null,
            ),
        ));

        $this->addChild(
            $menu,
            $translator->trans('api.configure.menu', array(), 'api'),
            array('uri' => $this->container->get('router')->generate('configure_api'))
        );
    }

    private function prepareUsersMenu($menu)
    {
        $translator = $this->container->get('translator');
        $this->addChild(
            $menu,
            $translator->trans('users.menu.manage', array(), 'users'),
            array('uri' => $this->container->get('router')->generate('newscoop_newscoop_users_index'))
        );

        $status = $this->addChild($menu[$translator->trans('users.menu.manage', array(), 'users')], $translator->trans('Edit user', array(), 'home'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'user',
                'action' => 'edit',
                'reset_params' => false,
            ),
        ));

        if ($status) {
            $menu[$translator->trans('users.menu.manage', array(), 'users')][$translator->trans('Edit user', array(), 'home')]->setDisplay(false);
        }

        $status = $this->addChild($menu[$translator->trans('users.menu.manage', array(), 'users')][$translator->trans('Edit user', array(), 'home')], $translator->trans('Edit permissions', array(), 'home'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'acl',
                'action' => 'edit',
            ),
        ));

        if ($status) {
            $menu[$translator->trans('users.menu.manage', array(), 'users')][$translator->trans('Edit user', array(), 'home')][$translator->trans('Edit permissions', array(), 'home')]->setDisplay(false);
        }

        $status = $this->addChild($menu[$translator->trans('users.menu.manage', array(), 'users')], $translator->trans('Rename user', array(), 'home'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'user',
                'action' => 'rename',
                'reset_params' => false,
            ),
        ));

        if ($status) {
            $menu[$translator->trans('users.menu.manage', array(), 'users')][$translator->trans('Rename user', array(), 'home')]->setDisplay(false);
        }

        $status = $this->addChild($menu[$translator->trans('users.menu.manage', array(), 'users')], $translator->trans('Create new user', array(), 'home'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'user',
                'action' => 'create',
                'reset_params' => false,
            ),
        ));

        if ($status) {
            $menu[$translator->trans('users.menu.manage', array(), 'users')][$translator->trans('Create new user', array(), 'home')]->setDisplay(false);
        }

        $this->addChild($menu, $translator->trans('Manage Authors', array(), 'home'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'users',
                'action' => 'authors.php',
            ),
        ));

        $this->addChild($menu, $translator->trans('Manage Authors', array(), 'home'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'users',
                'action' => 'authors.php',
            ),
            'resource' => 'autors',
            'privilege' => 'edit',
        ));

        $this->addChild($menu, $translator->trans('Manage User Types', array(), 'home'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'user-group',
                'action' => null,
            ),
            'resource' => 'user-group',
            'privilege' => 'manage',
        ));

        $status = $this->addChild($menu[$translator->trans('Manage User Types', array(), 'home')], $translator->trans('Edit user type', array(), 'home'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'user-group',
                'action' => 'edit-access',
                'reset_params' => false,
            ),
        ));

        if ($status) {
            $menu[$translator->trans('Manage User Types', array(), 'home')][$translator->trans('Edit user type', array(), 'home')]->setDisplay(false);
        }

        $this->addChild($menu, $translator->trans('Create new account', array(), 'home'), array('zend_route' => array(
                'module' => 'admin',
                'controller' => 'user',
                'action' => 'create',
            ),
        ));
    }

    public function preparePluginsMenu($menu)
    {
        $translator = $this->container->get('translator');
        $rootMenu = false;
        $pluginInfos = \CampPlugin::GetPluginsInfo(true);
        if ($this->user->hasPermission('plugin_manager')) {
            $rootMenu = true;
        }

        foreach ($pluginInfos as $info) {
            if (isset($info['menu']['permission']) && $this->user->hasPermission($info['menu']['permission'])) {
                $rootMenu = true;
            } elseif (isset($info['menu']['sub']) && is_array($info['menu']['sub'])) {
                foreach ($info['menu']['sub'] as $menuInfo) {
                    if ($this->user->hasPermission($menuInfo['permission'])) {
                        $rootMenu = true;
                    }
                }
            }
        }

        $menu->addChild($translator->trans('Plugins'), array('uri' => '#'))
            ->setAttribute('dropdown', true)
            ->setLinkAttribute('data-toggle', 'dropdown');

        if (!$rootMenu) {
            $menu[$translator->trans('Plugins')]->setDisplay(false);

            return;
        }

        if ($this->user->hasPermission('plugin_manager')) {
            $this->addChild($menu[$translator->trans('Plugins')], $translator->trans('Manage Plugins'),  array('uri' => $this->container->get('router')->generate('newscoop_newscoop_plugins_index')));
        }

        $enabled = \CampPlugin::GetEnabled();
        $enabledIds = array();
        foreach ($enabled as $plugin) {
            $enabledIds[] = $plugin->getName();
        }

        foreach ($pluginInfos as $info) {
            if (in_array($info['name'], $enabledIds)) {
                $parentMenu = false;

                if (isset($info['menu']['permission']) && $this->user->hasPermission($info['menu']['permission'])) {
                    $parentMenu = true;
                } elseif (isset($info['menu']['sub']) && is_array($info['menu']['sub'])) {
                    foreach ($info['menu']['sub'] as $menuInfo) {
                        if ($this->user->hasPermission($menuInfo['permission'])) {
                            $parentMenu = true;
                        }
                    }
                }

                if ($parentMenu && isset($info['menu'])) {
                    $uri = '#';
                    if (isset($info['menu']['path'])) {
                        $uri = $this->generateZendRoute('admin').'/'.$info['menu']['path'];
                    }

                    $this->addChild($menu[$translator->trans('Plugins')], $translator->trans($info['menu']['label']), array(
                        'uri' => $uri,
                    ))->setLinkAttribute('data-toggle', 'rightdrop');
                }

                if (isset($info['menu']['sub']) && is_array($info['menu']['sub'])) {
                    foreach ($info['menu']['sub'] as $menuInfo) {
                        if ($this->user->hasPermission($menuInfo['permission'])) {
                            $uri = '#';
                            if (isset($menuInfo['path'])) {
                                $uri = $this->generateZendRoute('admin').'/'.$menuInfo['path'];
                            }

                            $this->addChild($menu[$translator->trans('Plugins')][$translator->trans($info['menu']['label'])], $translator->trans($menuInfo['label']), array(
                                'uri' => $uri,
                            ));
                        }
                    }
                }
            }
        }

        return $menu;
    }

    protected function addChild($menu, $name, $element)
    {
        if (array_key_exists('resource', $element) && array_key_exists('privilege', $element)) {
            if (!$this->hasPermission($element['resource'], $element['privilege'])) {
                return false;
            }
        }

        if (array_key_exists('zend_route', $element)) {
            $element['uri'] = $this->generateZendRoute($element['zend_route']['module'], $element);
        }

        if (is_object($menu)) {
            return $menu->addChild($name, $element);
        }
    }

    private function hasPermission($resource, $action)
    {
        return $this->user->hasPermission(null, $resource, $action);
    }

    private function generateZendRoute($module, $element = array())
    {
        if (!array_key_exists('zend_route', $element)) {
            $element['zend_route'] = array();
        }

        $zendRouteParams = $element['zend_route'];
        $params = array_key_exists('params', $zendRouteParams) == true ? $zendRouteParams['params'] : array();
        $controller = array_key_exists('controller', $zendRouteParams) == true ? $zendRouteParams['controller'] : null;
        $action = array_key_exists('action', $zendRouteParams) == true ? $zendRouteParams['action'] : null;
        $module = array_key_exists('module', $zendRouteParams) == true ? $zendRouteParams['module'] : $module;
        $resetParams = array_key_exists('reset_params', $zendRouteParams) == true ? $zendRouteParams['reset_params'] : true;

        $getParams = '';
        if (count($_GET) > 0 && !$resetParams) {
            $getParams = '?'.http_build_query($_GET);
        }

        return $this->container->get('zend_router')->assemble(array(
            'controller' => $controller,
            'action' => $action,
            'module' => $module,
        ) + $params, 'default', $resetParams).$getParams;
    }
}
