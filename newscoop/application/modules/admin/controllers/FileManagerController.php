<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Entity\Template;

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

    /** @var array */
    private static $equivalentMimeTypes = array(
        'text/plain',
        'text/html',
        'application/x-php',
        'application/octet-stream',
        'application/javascript',
        'text/x-c',
        'text/css' ,
        'text/x-php',
        'application/x-httpd-php',
        'text/x-c++',
        'application/x-empty; charset=binary',
    );

    public function init()
    {
        $this->root = realpath(APPLICATION_PATH . '/../templates');
        $this->repository = $this->_helper->entity->getRepository('Newscoop\Entity\Template');
        $this->storage = new Newscoop\Storage\LocalStorage($this->root);

        $this->_helper->contextSwitch
            ->addActionContext('get-items', 'json')
            ->initContext();
    }

    public function indexAction()
    {
        // item action form
        $form = $this->getActionForm();
        $form->setAction('')->setMethod('post');
        $this->view->form = $form;

        // get items
        $path = $this->parsePath();
        $folders = $templates = array();
        foreach ($this->storage->listItems($path) as $item) {
            $form->file->addMultioption($item, $item); // add possible files

            $fileInfo = new SplFileInfo("$this->root/$path/$item");
            if ($fileInfo->isDir()) {
                $folders[] = $item;
                continue;
            }

            $template = $this->repository->getTemplate("$path/$item");
            $template->setFileInfo($fileInfo);
            $templates[] = $template;
        }

        $this->view->folders = $folders;
        $this->view->templates = $templates;

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $values = $form->getValues();
            $this->_forward($values['action']);
            return;
        }

        // set current path
        $this->view->path = $this->formatPath($path);

        // get parents
        $parents = explode('/', $path);
        array_pop($parents);
        $this->view->parent = implode(self::SEPARATOR, $parents);

        // build breadcrubs for path
        $pages = array($this->buildBreadcrumbs(explode('/', $path)));

        $this->view->moveForm = $this->getMoveForm();
        $this->view->nav = new Zend_Navigation($pages);
        $this->view->dateFormat = 'Y-m-d H:i';
        $this->view->separator = self::SEPARATOR;
        $this->view->actions = array(
            array(
                'label' => getGS('Upload'),
                'module' => 'admin',
                'controller' => 'file-manager',
                'action' => 'upload',
                'class' => 'upload',
                'reset_params' => false,
            ),
            array(
                'label' => getGS('Create folder'),
                'uri' => '#create-folder',
                'class' => 'add',
            ),
            array(
                'label' => getGS('Create file'),
                'uri' => '#create-file',
                'class' => 'add',
            ),
        );
    }

    public function getItemsAction()
    {
        $path = $this->parsePath();

        $items = array();
        foreach ($this->storage->listItems($path) as $item) {
            $fileInfo = new SplFileInfo("$this->root/$path/$item");
            $items[] = (object) array(
                'name' => basename($item),
                'isDir' => $fileInfo->isDir(),
            );
        }

        $this->view->items = $items;
    }

    public function uploadAction()
    {
        $path = $this->parsePath($this->_getParam('path', ''));
        $plupload = $this->getHelper('plupload');

        $form = new Admin_Form_Upload;
        $form->setMethod('post');

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
        $key = $this->getKey();
        $template = $this->repository->getTemplate($key);
        $fileInfo = new \SplFileInfo("$this->root/$key");
        $template->setFileInfo($fileInfo);
        $this->view->template = $template;
        $this->_setParam('template', $template);

        switch ($template->getType()) {
            case 'jpg':
            case 'png':
            case 'gif':
                $this->_forward('edit-image');
                break;

            case 'tpl':
            case 'css':
            case 'txt':
            case 'js':
                $this->_forward('edit-template');
                break;

            default:
                $this->_forward('edit-other');
                break;
        }

        $form = new Admin_Form_ReplaceTemplate;
        $form->setAction('')->setMethod('post')->setAttrib('enctype', 'multipart/form-data');

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $path = $this->parsePath();
            $file = $this->_getParam('file');
            $oldMime = $this->getMime("$path/$file");
            $newInfo = $form->file->getFileInfo();
            $newMime = $newInfo['file']['type'];

            if ($oldMime != $newMime
                && !(in_array($oldMime, self::$equivalentMimeTypes) && in_array($newMime, self::$equivalentMimeTypes))) {
                $this->_helper->flashMessenger(getGS('You can only replace a file with a file of the same type.  The original file is of type "$1", and the file you uploaded was of type "$2".', $oldMime, $newMime));
                $this->_helper->redirector('edit', 'file-manager', 'admin', array(
                    'path' => $this->_getParam('path'),
                    'file' => $this->_getParam('file'),
                ));
            }

            $form->getValues(); // upload
            if ($this->storage->storeItem($key, file_get_contents($form->file->getFileName()))) {
	            $this->_helper->flashMessenger(getGS('File "$1" replaced.', $file));
            } else {
                $this->_helper->flashMessenger(getGS("Unable to save the file '$1' to the path '$2'.", $file, $path));
            }

            $this->_helper->redirector('edit', 'file-manager', 'admin', array(
                'path' => $this->_getParam('path'),
                'file' => $this->_getParam('file'),
            ));
        }

        $this->view->replaceForm = $form;
    }

    public function editTemplateAction()
    {
        $key = $this->getKey();
        $template = $this->_getParam('template');

        $form = new Admin_Form_Template;
        $form->setAction('')->setMethod('post');

        $form->setDefaults(array(
            'content' => $this->storage->fetchItem($key),
            'cache_lifetime' => $template->getCacheLifetime(),
        ));

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
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
    }

    public function editImageAction()
    {
        $key = $this->getKey();
        $this->view->src = "$this->root/$key";
    }

    public function editOtherAction()
    {
    }

    public function moveAction()
    {
        $path = $this->parsePath();
        $name = $this->_getParam('name');

        $files = (array) $this->_getParam('file', array());
        foreach ($files as $file) {
            $fileInfo = $this->getFileInfo("$path/$file");
            if ($fileInfo->isDir()) {
                $this->_helper->flashMessenger->addMessage(getGS("Can't move directory $1.", "<strong>$file</strong>"));
                continue;
            }

            $fileInfo = $this->getFileInfo("$name/$file");
            if ($fileInfo->getRealpath()) {
                $this->_helper->flashMessenger->addMessage(getGS("Can't move file $1 to $2. It exists already.", "<strong>$file</strong>", $name));
                continue;
            }

            if ($this->storage->moveItem("$path/$file", "$name/$file")) {
                $this->repository->updateKey("$path/$file", "$name/$file");
                $this->findReplace("$path/$file", "$name/$file");
                $this->_helper->flashMessenger->addMessage(getGS("Template $1 moved", "<strong>$file</strong>"));
            } else {
                $this->_helper->flashMessenger->addMessage(getGS("Can't move file $1.", "<strong>$file</strong>"));
            }
        }

        $this->_helper->redirector('index', 'file-manager', 'admin', array(
            'path' => $this->_getParam('path'),
        ));
    }

    public function copyAction()
    {
        $path = $this->parsePath();
        $file = $this->_getParam('file');
        if (is_array($file)) {
            $file = array_shift($file);
        }

        $name = $this->formatName($this->_getParam('name'), pathinfo($file, PATHINFO_EXTENSION));

        $dest = "$path/$name";
        if (in_array($dest, $this->storage->listItems($path))) {
	        $this->_helper->flashMessenger(getGS('A file or folder having the name $1 already exists', "<strong>$name</strong>"));
            $this->_helper->redirector('index', 'file-manager', 'admin', array(
                'path' => $this->_getParam('path'),
            ));
        }

        if ($this->storage->copyItem("$path/$file", "$path/$name")) {
		    $this->_helper->flashMessenger(getGS('Template $1 was duplicated into $2', $file, $name));
        } else {
            $this->_helper->flashMessenger(getGS('The template $1 could not be created.', "<strong>$name</strong>"));
        }

        $this->_helper->redirector('index', 'file-manager', 'admin', array(
            'path' => $this->_getParam('path'),
        ));
    }

    public function renameAction()
    {
        $path = $this->parsePath();
        $file = $this->_getParam('file');
        if (is_array($file)) {
            $file = array_shift($file);
        }

        $name = $this->formatName($this->_getParam('name'), pathinfo($file, PATHINFO_EXTENSION));

        $dest = "$path/$name";
        if (in_array($dest, $this->storage->listItems($path))) { // check if exists
	        $this->_helper->flashMessenger(getGS('A file or folder having the name $1 already exists', "<strong>$name</strong>"));
            $this->_helper->redirector('index', 'file-manager', 'admin', array(
                'path' => $this->_getParam('path'),
            ));
        }

        // rename
        if ($this->storage->renameItem("$path/$file", "$path/$name")) {
            $this->repository->updateKey("$path/$file", "$path/$name");
            $this->clearCompiledTemplate("$path/$file");
            $this->findReplace("$path/$file", "$path/$name");
		    $this->_helper->flashMessenger(getGS('Template object $1 was renamed to $2', $file, $name));
        } else {
            $this->_helper->flashMessenger(getGS('The template object $1 could not be renamed.', "<strong>$name</strong>"));
        }

        $this->_helper->redirector('index', 'file-manager', 'admin', array(
            'path' => $this->_getParam('path'),
        ));
    }

    public function deleteAction()
    {
        $path = $this->parsePath();

        $files = $this->_getParam('file', array());
        foreach ((array) $files as $file) {
            $key = "$path/$file";
            $fileInfo = $this->getFileInfo($key);

            if ($fileInfo->isDir()) { // delete collection
                $items = $this->storage->listItems($key);
                if (!empty($items)) { // can't delete non empty dir
                    $this->_helper->flashMessenger->addMessage(getGS("Can't remove non empty directory '$1'", $file));
                    continue;
                }

                $this->storage->deleteItem($key);
            } else { // delete file
                $template = $this->repository->getTemplate($key);
                $inUse = $this->isUsed($template);
	            if (in_array($inUse, array(CAMP_ERROR_READ_FILE, CAMP_ERROR_READ_DIR))) {
                    $this->_helper->flashMessenger->addMessage(getGS("There are some files which can not be readed so Newscoop was not able to determine whether '$1' is in use or not. Please fix this, then try to delete the template again.", $file));
                    continue;
                } elseif ($inUse) {
		            $this->_helper->flashMessenger->addMessage(getGS("The template object $1 is in use and can not be deleted.", $key));
                    continue;
                }

                // delete
                $this->storage->deleteItem($key);
                $this->repository->delete($key);
                $this->clearCompiledTemplate($key);
            }

			$this->_helper->flashMessenger->addMessage(getGS('Template object $1 was deleted', $file));
        }

        $this->_helper->entity->flushManager();
        $this->_helper->redirector('index', 'file-manager', 'admin', array(
            'path' => $this->_getParam('path'),
        ));
    }

    public function createFolderAction()
    {
        $path = $this->parsePath();
        $name = $this->formatName($this->_getParam('name'));

        $fileInfo = $this->getFileInfo("$path/$name");
        if ($fileInfo->getRealpath()) {
	        $this->_helper->flashMessenger(getGS('A file or folder having the name $1 already exists', "<strong>$name</strong>"));
            $this->_helper->redirector('index', 'file-manager', 'admin', array(
                'path' => $this->_getParam('path'),
            ));
        }

        $this->storage->storeItem("/$path/$name/placeholder", '');
        $this->storage->deleteItem("/$path/$name/placeholder");
		$this->_helper->flashMessenger(getGS("Directory $1 created.", "<strong>$name</strong>"));
        $this->_helper->redirector('index', 'file-manager', 'admin', array(
            'path' => $this->_getParam('path'),
        ));
    }

    public function createFileAction()
    {
        $path = $this->parsePath();
        $name = $this->formatName($this->_getParam('name'));

        $fileInfo = $this->getFileInfo("$path/$name");
        if ($fileInfo->getRealpath()) {
	        $this->_helper->flashMessenger(getGS('A file or folder having the name $1 already exists', "<strong>$name</strong>"));
            $this->_helper->redirector('index', 'file-manager', 'admin', array(
                'path' => $this->_getParam('path'),
            ));
        }

        $this->storage->storeItem("$path/$name", '');
	    $this->_helper->flashMessenger(getGS('New template $1 created', "$path/$name"));
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
     * Get key
     *
     * @return string
     */
    private function getKey()
    {
        $path = $this->parsePath();
        $file = $this->_getParam('file', '');
        return trim("$path/$file", '/');
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
            return getGS("'$1' $2", current($files), $action);
        }

        return getGS("$1 files $2", $count, $action);
    }

    /**
     * Format file name
     *
     * @param string $name
     * @param string $ext
     * @return string
     */
    private function formatName($name, $ext = '')
    {
        $name = trim(strtr($name, '?~#%*&|"\'\\/<>', '_____________'), '_');
        if (!empty($ext)) {
            $current = pathinfo($name, PATHINFO_EXTENSION);
            if ($current != $ext) {
                $name .= ".{$ext}";
            }
        }

        return $name;
    }

    /**
     * Get action form
     *
     * @return Zend_Form
     */
    private function getActionForm()
    {
        $form = new Zend_Form;

        $form->addElement('hash', 'csrf');
        $form->addElement('hidden', 'name');

        $form->addElement('select', 'multiaction', array(
            'multioptions' => array(
                '' => getGS('Actions'),
                'move' => getGS('Move'),
                'delete' => getGS('Delete'),
            ),
        ));

        $form->addElement('hidden', 'action', array(
            'required' => true,
            'validators' => array(
                array('inArray', true, array(
                    array('copy', 'move', 'rename', 'delete', 'create-file', 'create-folder'),
                )),
            ),
        ));

        $form->addElement('multiCheckbox', 'file', array(
        ));

        return $form;
    }

    /**
     * Get move form
     *
     * @return Zend_Form
     */
    private function getMoveForm()
    {
        $form = new Zend_Form;

        $form->addElement('select', 'name', array(
            'multioptions' => array('/' => '/') + $this->getPaths(''),
        ));

        return $form;
    }

    /**
     * Get available paths starting from path
     *
     * @param string $path
     * @return array
     */
    private function getPaths($path)
    {
        $paths = array();
        foreach ($this->storage->listItems($path) as $item) {
            $fileInfo = $this->getFileInfo("$path/$item");
            if (!$fileInfo->isDir()) {
                continue;
            }

            $paths["$path/$item"] = "$path/$item";
            $paths += $this->getPaths("$path/$item");
        }

        return $paths;
    }

    /**
     * Get file info
     *
     * @param string $name
     * @return SplFileInfo
     */
    private function getFileInfo($name)
    {
        return new SplFileInfo("$this->root/$name");
    }

    /**
     * Test if template is used
     *
     * @param Newscoop\Entity\Template $template
     * @return mixed
     */
    private function isUsed(Template $template)
    {
        if ($this->repository->isUsed($template)) {
            return true;
        }

        $templateName = $template->getKey();
        $tplFindObj = new FileTextSearch();
        $tplFindObj->setExtensions(array('tpl','css'));
        $tplFindObj->setSearchKey($templateName);
        $result = $tplFindObj->findReplace($this->root);
        if (is_array($result) && sizeof($result) > 0) {
            return $result[0];
        }

        if (pathinfo($templateName, PATHINFO_EXTENSION) == 'tpl') {
            $templateName = ' ' . $templateName;
        }

        $tplFindObj->setSearchKey($templateName);
        $result = $tplFindObj->findReplace($this->root);
        if (is_array($result) && sizeof($result) > 0) {
            return $result[0];
        }

        if ($tplFindObj->m_totalFound > 0) {
            return true;
        }

        return false;
    }

    /**
     * Find and replace template name
     *
     * @param string $old
     * @param string $new
     * @return void
     */
    private function findReplace($old, $new)
    {
		$replaceObj = new FileTextSearch();
		$replaceObj->setExtensions(array('tpl','css'));
		$replaceObj->setSearchKey($old);
		$replaceObj->setReplacementKey($new);
		$replaceObj->findReplace($this->root);

		$tpl1_name = $old;
		$tpl2_name = $new;
		if (pathinfo($old, PATHINFO_EXTENSION) == 'tpl') {
			$tpl1_name = ' ' . $old;
			$tpl2_name = ' ' . $new;
		}

		$replaceObj->setSearchKey($tpl1_name);
		$replaceObj->setReplacementKey($tpl2_name);
		$replaceObj->findReplace($this->root);
    }

    /**
     * Clear compiled template
     *
     * @param string $filename
     * @return void
     */
    private function clearCompiledTemplate($filename)
    {
        require_once APPLICATION_PATH . '/../template_engine/classes/CampTemplate.php';

        try {
		    CampTemplate::singleton()->clear_compiled_tpl($filename);
        } catch (Exception $e) { // ignore file not found
        }
    }

    /**
     * Get mime type
     *
     * @param string $path
     * @return string
     */
    private function getMime($path)
    {
        $realpath = realpath($path);
        if (!$realpath) {
            $realpath = realpath("$this->root/$path");
            if (!$realpath) {
                throw new \InvalidArgumentException($path);
            }
        }

        $finfo = new finfo(FILEINFO_MIME);
        return $finfo->file($realpath);
    }
}
