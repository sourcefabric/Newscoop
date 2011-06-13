<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Service\Template,
    Newscoop\Storage,
    Newscoop\Service\Resource\ResourceId,
    Newscoop\Service\IThemeService;

/**
 * @Acl(resource="template", action="manage")
 */
class Admin_TemplateController extends Zend_Controller_Action
{
    const SEPARATOR = ':'; // can't use / in url, thus replace with :

    /** @var Newscoop\Service\Template */
    private $service;

    public function init()
    {
        $resource = new ResourceId(__CLASS__);
        $themeService = $resource->getService(IThemeService::NAME);
        $theme = $themeService->findById($this->_getParam('id'));

        $path = $theme->getPath();
        $fullPath = $themeService->toFullPath($theme);
        $root = str_replace($path, '', $fullPath);

        $storage = new Storage($fullPath);
        $repository = $this->_helper->entity->getRepository('Newscoop\Entity\Template')
            ->setBasePath($path);
        $this->service = new Template($storage, $repository); 

        $this->_helper->contextSwitch
            ->addActionContext('get-items', 'json')
            ->initContext();

        $this->view->basePath = $path;
    }

    public function indexAction()
    {
        // item action form
        $form = $this->getActionForm();
        $form->setMethod('post');
        $this->view->form = $form;

        try {
            // get items
            $path = $this->parsePath();
            $folders = $templates = array();
            foreach ($this->service->listItems($path) as $item) {
                $form->file->addMultioption($item->name, $item->name); // add possible files

                if (!isset($item->id)) {
                    $folders[] = $item;
                    continue;
                }

                $templates[] = $item;
            }
        } catch (\InvalidArgumentException $e) {
            $this->_helper->flashMessenger(array('error', $e->getMessage()));
            $this->_helper->redirector('index');
        }

        $this->view->folders = $folders;
        $this->view->templates = $templates;

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($_POST)) {
            $values = $form->getValues();
            $this->_forward($values['action']);
            return;
        }

        // set current path
        $this->view->path = $this->formatPath($path);

        // get parents
        $parents = explode('/', dirname($path));
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
                'controller' => 'template',
                'action' => 'upload',
                'class' => 'upload',
                'reset_params' => false,
                'params' => array(
                    'next' => urlencode($this->view->url()),
                ),
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
        $this->view->items = $this->service->listItems($path);
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
                $this->service->storeItem("$path/$basename", file_get_contents($tmp));
            }

            $this->_helper->flashMessenger($this->formatMessage(array_keys($files), getGS('uploaded')));
            $this->_helper->log($this->formatMessage(array_keys($files), getGS('uploaded')));
            $this->_redirect(urldecode($this->_getParam('next')), array(
                'prependBase' => false,
            ));
        }

        $this->view->form = $form;
        $this->view->isWritable = $this->service->isWritable($path);
    }

    public function editAction()
    {
        $key = $this->getKey();
        $item = $this->service->fetchMetadata($key);
        $this->view->item = $item;
        $this->view->placeholder('title')
            ->set(getGS("Edit template: $1 (Template ID: $2)", $item->name, $item->id));

        switch ($item->type) {
            case 'jpg':
            case 'png':
            case 'gif':
                $this->_forward('edit-image');
                break;

            case 'tpl':
            case 'css':
            case 'txt':
            case 'html':
            case 'js':
                $this->_forward('edit-template');
                break;

            default:
                $this->_forward('edit-other');
                break;
        }

        $form = new Admin_Form_ReplaceTemplate;
        $form->setMethod('post')->setAttrib('enctype', 'multipart/form-data');

        $request = $this->getRequest();
        if ($request->isPost() && $request->getParam('file', false) && $form->isValid($request->getPost())) {
            try {
                $form->getValues(); // upload
                $this->service->replaceItem($key, $form->file);
	            $this->_helper->flashMessenger(getGS("File '$1' was replaced.", basename($key)));
	            $this->_helper->log(getGS("File '$1' was replaced.", basename($key)));
            } catch (\InvalidArgumentException $e) {
                $this->_helper->flashMessenger(array('error', $e->getMessage()));
            }

            $this->_helper->redirector('edit', 'template', 'admin', array(
                'path' => $this->_getParam('path'),
                'file' => $this->_getParam('file'),
                'next' => $this->_getParam('next'),
                'id' => $this->_getParam('id'),
            ));
        }

        $this->view->replaceForm = $form;
    }

    public function editTemplateAction()
    {
        $key = $this->getKey();

        $form = new Admin_Form_Template;
        $form->setMethod('post');

        $form->setDefaults(array(
            'content' => $this->service->fetchItem($key),
            'cache_lifetime' => $this->service->fetchMetadata($key)->ttl,
        ));

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $values = $form->getValues();
            $this->service->storeItem($key, $values['content']);
            $this->service->storeMetadata($key, $values);
            $this->_helper->entity->flushManager();

            $this->_helper->flashMessenger(getGS("Template '$1' $2.", basename($key), getGS('updated')));
            $this->_helper->log(getGS("Template '$1' $2.", basename($key), getGS('updated')));
            $this->_redirect(urldecode($this->_getParam('next')), array(
                'prependBase' => false,
            ));
        }

        $this->view->form = $form;
    }

    public function editImageAction()
    {
        $key = $this->getKey();
        $this->view->item = $this->service->fetchMetadata($key);
    }

    public function editOtherAction()
    {
        // pass
    }

    public function moveAction()
    {
        $path = $this->parsePath();
        $dest = $this->_getParam('name');

        try {
            $files = (array) $this->_getParam('file', array());
            foreach ($files as $file) {
                $this->service->moveItem("$path/$file", $dest);
                $this->_helper->flashMessenger->addMessage(getGS("Template '$1' $2.", $file, getGS('moved')));
                $this->_helper->log(getGS("Template '$1' $2.", $file, getGS('moved')));
            }
        } catch (\InvalidArgumentException $e) {
            $this->_helper->flashMessenger->addMessage(array('error', $e->getMessage()));
        }

        $this->_redirect(urldecode($this->_getParam('next')), array(
            'prependBase' => false,
        ));
    }

    public function copyAction()
    {
        $path = $this->parsePath();
        $file = $this->_getParam('file');
        if (is_array($file)) {
            $file = array_shift($file);
        }

        try {
            $name = $this->formatName($this->_getParam('name'), pathinfo($file, PATHINFO_EXTENSION));
            $this->service->copyItem("$path/$file", $name);
		    $this->_helper->flashMessenger(getGS("Template '$1' was duplicated into '$2'.", $file, $name));
		    $this->_helper->log(getGS("Template '$1' was duplicated into '$2'.", $file, $name));
        } catch (\InvalidArgumentException $e) {
            $this->_helper->flashMessenger(array('error', $e->getMessage()));
        }

        $this->_redirect(urldecode($this->_getParam('next')), array(
            'prependBase' => false,
        ));
    }

    public function renameAction()
    {
        $path = $this->parsePath();
        $file = $this->_getParam('file');
        if (is_array($file)) {
            $file = array_shift($file);
        }

        try {
            $name = $this->formatName($this->_getParam('name'), pathinfo($file, PATHINFO_EXTENSION));
            $this->service->renameItem("$path/$file", $name);
            $this->clearCompiledTemplate("$path/$file");
		    $this->_helper->flashMessenger(getGS("Template object '$1' was renamed to '$2'.", $file, $name));
		    $this->_helper->log(getGS("Template object '$1' was renamed to '$2'.", $file, $name));
        } catch (\InvalidArgumentException $e) {
            $this->_helper->flashMessenger(array('error', $e->getMessage()));
        }

        $this->_redirect(urldecode($this->_getParam('next')), array(
            'prependBase' => false,
        ));
    }

    /**
     * @Acl(action="delete")
     */
    public function deleteAction()
    {
        $path = $this->parsePath();
        $files = $this->_getParam('file', array());
        try {
            foreach ((array) $files as $file) {
                $key = "$path/$file";
                $this->service->deleteItem($key);
                $this->_helper->entity->flushManager();
                $this->clearCompiledTemplate($key);
			    $this->_helper->flashMessenger(getGS("Template object '$1' was deleted.", $file));
			    $this->_helper->log(getGS("Template object '$1' was deleted.", $file));
            }
        } catch (\InvalidArgumentException $e) {
            $this->_helper->flashMessenger(array('error', $e->getMessage()));
        }

        $this->_redirect(urldecode($this->_getParam('next')), array(
            'prependBase' => false,
        ));
    }

    public function createFolderAction()
    {
        $path = $this->parsePath();
        $name = $this->formatName($this->_getParam('name'));

        try {
            $this->service->createFolder(ltrim("$path/$name", ' /'));
		    $this->_helper->flashMessenger(getGS("Directory '$1' created.", $name));
		    $this->_helper->log(getGS("Directory '$1' created.", $name));
        } catch (\InvalidArgumentException $e) {
	        $this->_helper->flashMessenger(array('error', $e->getMessage()));
        }

        $this->_redirect(urldecode($this->_getParam('next')), array(
            'prependBase' => false,
        ));
    }

    public function createFileAction()
    {
        $path = $this->parsePath();
        $name = $this->formatName($this->_getParam('name'));

        try {
            $this->service->createFile(ltrim("$path/$name", ' /'));
	        $this->_helper->flashMessenger(getGS("New template '$1' created.", $name));
	        $this->_helper->log(getGS("New template '$1' created.", $name));
        } catch (\InvalidArgumentException $e) {
            $this->_helper->flashMessenger(array('error', $e->getMessage()));
        }

        $this->_redirect(urldecode($this->_getParam('next')), array(
            'prependBase' => false,
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
            'controller' => $this->_getParam('controller'),
            'action' => $this->_getParam('action'),
            'params' => array(
                'id' => $this->_getParam('id'),
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
        $name = trim(strtr(basename($name), '?~#%*&|"\'\\/<>', '_____________'), '_');
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

        $form->addElement('multiCheckbox', 'file', array());

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
        foreach ($this->service->listItems($path) as $item) {
            if (isset($item->id)) { // skip template
                continue;
            }

            $paths["$path/$item->name"] = "$path/$item->name";
            $paths += $this->getPaths("$path/$item->name");
        }

        return $paths;
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
}
