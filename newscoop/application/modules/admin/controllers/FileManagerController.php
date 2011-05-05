<?php

class Admin_FileManagerController extends Zend_Controller_Action
{
    const SEPARATOR = ':';

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $root = realpath(APPLICATION_PATH . '/../templates');
        $path = str_replace(self::SEPARATOR, '/', $this->_getParam('path', ''));
        $rootpath = realpath("$root/$path");
        $manager = new Newscoop\File\Manager\LocalManager($rootpath, $root);
        $this->view->manager = $manager;

        // remove .. from path
        $path = ltrim(str_replace($root, '', $rootpath), '/');
        $this->view->path = str_replace('/', self::SEPARATOR, $path);

        // get parent
        $parents = explode('/', $path);
        array_pop($parents);
        $this->view->parent = implode(self::SEPARATOR, $parents);

        // build breadcrubs for path
        $pages = array($this->buildBreadcrumbs(explode('/', $path)));

        $this->view->nav = new Zend_Navigation($pages);
        $this->view->dateFormat = 'Y-m-d H:i';
        $this->view->separator = self::SEPARATOR;
    }

    /**
     * Build pages tree for navigation
     *
     * @param string $path
     * @return array
     */
    private function buildBreadcrumbs(array $pieces, $level = 0)
    {
        if ($level >= sizeof($pieces)) {
            return array('uri' => '#');
        }

        return array(
            'label' => $pieces[$level],
            'module' => 'admin',
            'controller' => 'file-manager',
            'action' => 'index',
            'params' => array(
                'path' => implode(self::SEPARATOR, array_slice($pieces, 0, $level + 1)),
            ),
            'pages' => array(
                $this->buildBreadcrumbs($pieces, $level + 1),
            ),
        );
    }
}

