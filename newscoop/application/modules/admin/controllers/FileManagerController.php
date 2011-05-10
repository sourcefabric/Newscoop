<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 */
class Admin_FileManagerController extends Zend_Controller_Action
{
    const SEPARATOR = ':'; // can't user / in url, so replacing with :

    /** @var string */
    private $root;

    /** @var Newscoop\Entity\Repository\TemplateRepository */
    private $repo;

    public function init()
    {
        $this->root = realpath(APPLICATION_PATH . '/../templates');
        $this->repo = $this->_helper->entity->getRepository('Newscoop\Entity\Template');
    }

    public function indexAction()
    {
        $path = $this->parsePath($this->_getParam('path', ''));
        $manager = new Newscoop\File\Manager\LocalManager($path, $this->root,
            $this->_helper->entity->getRepository('Newscoop\Entity\Template'));
        $this->view->manager = $manager;

        // set current path
        $this->view->path = $this->formatPath($path);

        // get parents
        $parents = explode('/', $path);
        array_pop($parents);
        $this->view->parent = implode(self::SEPARATOR, $parents);

        // build breadcrubs for path
        $pages = array($this->buildBreadcrumbs(explode('/', $path)));

        $this->view->nav = new Zend_Navigation($pages);
        $this->view->dateFormat = 'Y-m-d H:i';
        $this->view->separator = self::SEPARATOR;
    }

    public function editAction()
    {
        $fileinfo = $this->getFileInfo();
        $template = $this->repo->getTemplate($fileinfo, $this->root);

        $form = new Admin_Form_Template;
        $form->setAction('')->setMethod('post');
        $form->setDefaultsFromTemplate($template);

        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            $this->repo->save($template, $form->getValues());
            $this->_helper->entity->flushManager();

            $this->_helper->flashMessenger(getGS('Template $1', getGS('saved')));
            $this->_helper->redirector('edit', 'file-manager', 'admin', array(
                'path' => $this->_getParam('path'),
                'file' => $this->_getParam('file'),
            ));
        }

        $this->view->form = $form;
        $this->view->file = $template;
    }

    public function deleteAction()
    {
        $fileinfo = $this->getFileInfo();
        $template = $this->repo->getTemplate($fileinfo, $this->root);

        try {
            $this->repo->delete($template, $this->root);
            $this->_helper->entity->flushManager();
        } catch (InvalidArgumentException $e) {
            $this->_helper->flashMessenger('e:' . getGS("Can't delete '$1'", $e->getMessage()));
            $this->_helper->redirector('index', 'file-manager', 'admin', array(
                'path' => $this->_getParam('path'),
            ));
        }

        $this->_helper->flashMessenger(getGS("'$1' $2", $fileinfo->getBasename(), getGS('deleted')));
        $this->_helper->redirector('index', 'file-manager', 'admin', array(
            'path' => $this->_getParam('path'),
        ));
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

    /**
     * Format path
     *
     * @param string $path
     * @return string
     */
    private function formatPath($path)
    {
        return str_replace('/', self::SEPARATOR, ltrim($path, '/'));
    }

    /**
     * Parse path
     *
     * @param string $path
     * @return string
     */
    private function parsePath($path)
    {
        return str_replace(self::SEPARATOR, '/', $path);
    }

    /**
     * Get FileInfo for file specified with params
     *
     * @return SplFileInfo
     */
    private function getFileInfo()
    { 
        $path = $this->parsePath($this->_getParam('path', ''));
        $file = $this->_getParam('file', '');
        $realpath = realpath("$this->root/$path/$file");
        if (!$realpath) {
            $this->_helper->flashMessenger(getGS("'$1' not found in '$2'", $file, $path));
            $this->_helper->redirector('index');
        }

        return new SplFileInfo($realpath);
    }
}

