<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * @Acl(resource="template", action="manage")
 */
class Admin_FileManagerController extends Zend_Controller_Action
{
    const SEPARATOR = ':'; // can't use / in url, thus replace with :

    /** @var string */
    private $root;

    /** @var Newscoop\Entity\Repository\TemplateRepository */
    private $repository;

    /** @var Newscoop\Storage\LocalStorage */
    private $storage;

    public function init()
    {
        $this->root = realpath(APPLICATION_PATH . '/../templates');
        $this->repository = $this->_helper->entity->getRepository('Newscoop\Entity\Template');
        $this->storage = new Newscoop\Storage\LocalStorage($this->root);
    }

    public function indexAction()
    {
        $form = $this->getIndexForm();
        $form->setAction('')->setMethod('post');
        $this->view->dialogForm = $form;

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $values = $form->getValues();
            $this->_forward($values['action']);
            return;
        }

        $path = $this->parsePath($this->_getParam('path', ''));

        $folders = $templates = array();
        foreach ($this->storage->listItems($path) as $key) {
            $fileInfo = new SplFileInfo("$this->root/$path/$key");
            if ($fileInfo->isDir()) {
                $folders[] = $key;
                continue;
            }

            $template = $this->repository->getTemplate("$path/$key");
            $template->setFileInfo($fileInfo);
            $templates[] = $template;
        }

        $this->view->folders = $folders;
        $this->view->templates = $templates;

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

    public function uploadAction()
    {
        $path = $this->parsePath($this->_getParam('path', ''));
        $plupload = $this->getHelper('plupload');

        $form = new Admin_Form_Upload;
        $form->setAction('')->setMethod('post');

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $files = $plupload->getUploadedFiles();
            foreach ($files as $basename => $tmp) {
                $this->storage->storeItem("$path/$basename", file_get_contents($tmp));
            }

            $this->_helper->flashMessenger($this->formatMessage($files, getGS('uploaded')));
            $this->_helper->redirector('index', 'file-manager', 'admin', array(
                'path' => $this->_getParam('path'),
            ));
        }

        $this->view->form = $form;
        $this->view->destination = new SplFileInfo("$this->root/$path");
    }

    public function editAction()
    {
        $path = $this->parsePath();
        $file = $this->_getParam('file');
        $key = "$path/$file";
        $fileInfo = new \SplFileInfo("$this->root/$key");
        $template = $this->repository->getTemplate($key);
        $template->setFileInfo($fileInfo);

        $form = new Admin_Form_Template;
        $form->setAction('')->setMethod('post');
        $form->setDefaults(array(
            'content' => $this->storage->fetchItem($key),
            'cache_lifetime' => $template->getCacheLifetime(),
        ));

        if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
            $values = $form->getValues();
            $this->storage->storeItem($key, $values['content']);
            $this->repository->save($template, $values);
            $this->_helper->entity->flushManager();

            $this->_helper->flashMessenger(getGS('Template $1', getGS('saved')));
            $this->_helper->redirector('edit', 'file-manager', 'admin', array(
                'path' => $this->_getParam('path'),
                'file' => $this->_getParam('file'),
            ));
        }

        $this->view->form = $form;
        $this->view->template = $template;
    }

    public function moveAction()
    {
        $path = $this->parsePath();
        $file = $this->_getParam('file');
        $name = $this->_getParam('name');

        $next = "$path/$name";
        if (is_dir("$this->root/$next")) {
            $next .= '/' . $file; // when moving into dir, it will be name/file instead of name
        }

        if ($this->storage->moveItem("$path/$file", "$path/$name")) {

            $this->repository->updateKey("$path/$file", $next);
            $this->_helper->flashMessenger(getGS("'$1' $2", $file, getGS('moved')));
        } else {
            $this->_helper->flashMessenger(getGS("'$1' can't be $2", $file, getGS('moved')));
        }

        $this->_helper->redirector('index', 'file-manager', 'admin', array(
            'path' => $this->_getParam('path'),
        ));
    }

    public function renameAction()
    {
        $path = $this->parsePath();
        $file = $this->_getParam('file');
        $name = $this->_getParam('name');

        if ($this->storage->renameItem("$path/$file", "$path/$name")) {
            $this->repository->updateKey("$path/$file", "$path/$name");
            $this->_helper->flashMessenger(getGS("'$1' $2", $file, getGS('renamed')));
        } else {
            $this->_helper->flashMessenger(getGS("'$1' can't be $2", $file, getGS('renamed')));
        }

        $this->_helper->redirector('index', 'file-manager', 'admin', array(
            'path' => $this->_getParam('path'),
        ));
    }

    public function copyAction()
    {
        $path = $this->parsePath();
        $file = $this->_getParam('file');
        $name = $this->_getParam('name');

        if ($this->storage->copyItem("$path/$file", "$path/$name")) {
            $this->_helper->flashMessenger(getGS("'$1' $2", $file, getGS('copied')));
        } else {
            $this->_helper->flashMessenger(getGS("'$1' can't be $2", $file, getGS('copied')));
        }

        $this->_helper->redirector('index', 'file-manager', 'admin', array(
            'path' => $this->_getParam('path'),
        ));
    }

    public function deleteAction()
    {
        $path = $this->parsePath();
        $file = $this->_getParam('file');
        $key = "$path/$file";

        $this->storage->deleteItem($key);
        $this->repository->delete($key);
        $this->_helper->entity->flushManager();

        $this->_helper->flashMessenger(getGS("'$1' $2", $file, getGS('deleted')));
        $this->_helper->redirector('index', 'file-manager', 'admin', array(
            'path' => $this->_getParam('path'),
        ));
    }

    public function createFolderAction()
    {
        $path = $this->parsePath();
        $name = $this->_getParam('name');

        $this->storage->storeItem("/$path/$name/placeholder", '');
        $this->storage->deleteItem("/$path/$name/placeholder");
        $this->_helper->flashMessenger(getGS("'$1' $2", $name, getGS('created')));
        $this->_helper->redirector('index', 'file-manager', 'admin', array(
            'path' => $this->_getParam('path'),
        ));
    }

    public function createFileAction()
    {
        $path = $this->parsePath();
        $name = $this->_getParam('name');

        $this->storage->storeItem("$path/$name", '');
        $this->_helper->flashMessenger(getGS("'$1' $2", $name, getGS('created')));
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
    private function parsePath($path = NULL)
    {
        if ($path === NULL) {
            $path = $this->_getParam('path', '');
        }

        return rtrim(str_replace(self::SEPARATOR, '/', $path), '/');
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

    /**
     * Format message
     *
     * @param array|string $files
     * @param string $action
     * @return string
     */
    private function formatMessage($files, $action)
    {
        $files = (array) $files;
        $count = sizeof($files);
        if ($count == 1) {
            return getGS("'$1' $2", current(array_keys($files)), $action);
        }

        return getGS("$1 files $2", $count, $action);
    }

    /**
     * Get index form
     *
     * @return Zend_Form
     */
    private function getIndexForm()
    {
        $form = new Zend_Form;

        $form->addElement('hash', 'csrf');
        $form->addElement('hidden', 'action');
        $form->addElement('hidden', 'file');

        $form->addElement('text', 'name', array(
            'label' => getGS('Name'),
            'required' => true,
        ));

        return $form;
    }
}
