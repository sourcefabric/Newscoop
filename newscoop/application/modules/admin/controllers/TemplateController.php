<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Annotations\Acl;
use Newscoop\Service\Template;
use Newscoop\Storage;
use Newscoop\Service\Resource\ResourceId;
use Newscoop\Service\IThemeService;

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
        /* @var $themeService Newscoop\Service\Implementation\ThemeServiceLocalFileSystem */
        $theme = $themeService->findById($this->_getParam('id'));
        /* @var $theme Newscoop\Entity\Theme */
        $this->view->themeId = $this->_getParam('id');

        $path = $theme->getPath();
        $fullPath = $themeService->toFullPath($theme);
        $root = str_replace($path, '', $fullPath);

        $storage = new Storage($fullPath);
        $repository = $this->_helper->entity->getRepository('Newscoop\Entity\Template')
            ->setBasePath($path);
        $this->service = new Template($storage, $repository);
        $this->service->setTheme($theme);

        $this->_helper->contextSwitch
            ->addActionContext('get-items', 'json')
            ->addActionContext('cache-templates', 'json')
            ->initContext();

        $this->view->basePath = $path;
    }

    public function indexAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $resource = new ResourceId(__CLASS__);
        $themeService = $resource->getService(IThemeService::NAME);

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

                if (!isset($item->size)) {
                    $folders[] = $item;
                    continue;
                }

                if ($item->name == $themeService->themeConfigFileName) {
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
        if ($path == '') {
            $this->view->paths = $this->reqCachePaths; // don't move form here
            $this->view->doCache = true;
        }

        $this->view->nav = new Zend_Navigation($pages);
        $this->view->dateFormat = 'Y-m-d H:i';
        $this->view->separator = self::SEPARATOR;

        // redirect parameter in session
        $nextUrl = new Zend_Session_Namespace('upload-next');

        $nextUrl->setExpirationHops(7, 'next', true);
        $nextUrl->next = $this->_request->getParams();

        $this->view->actions = array(
            array(
                'label' => $translator->trans('Upload', array(), 'themes'),
                'module' => 'admin',
                'controller' => 'template',
                'action' => 'upload',
                'class' => 'upload',
                'reset_params' => false
            ),
            array(
                'label' => $translator->trans('Create folder', array(), 'themes'),
                'uri' => '#create-folder',
                'class' => 'add',
            ),
            array(
                'label' => $translator->trans('Create file', array(), 'themes'),
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
        $translator = \Zend_Registry::get('container')->getService('translator');
        // get next redirect param
        $nextRedirect = new Zend_Session_Namespace('upload-next');

        $path = $this->parsePath($this->_getParam('path', ''));
        $plupload = $this->getHelper('plupload');

        $form = new Admin_Form_Upload;
        $form->setMethod('post');
        $form->getElement('submit')->setLabel($translator->trans('Done uploading', array(), 'themes'));

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {

            $files = $plupload->getUploadedFiles();
            foreach ($files as $basename => $tmp) {
                $this->service->storeItem("$path/$basename", file_get_contents($tmp));
            }
            $this->_helper->flashMessenger($this->formatMessage(array_keys($files), $translator->trans('uploaded', array(), 'themes')));

            // redirect by next parameter
            if(!is_null($nextRedirect->next)) {
                $this->_helper->redirector->gotoRouteAndExit($nextRedirect->next);
            }
            else {
                $this->_helper->redirector->gotoSimple("index", "themes", "admin");
            }
        }

        // prelong next parameter
        $nextRedirect->setExpirationHops(7, 'next', true);

        $this->view->form = $form;
        $this->view->path = $this->view->basePath . $path;
        $this->view->isWritable = $this->service->isWritable($path);
    }

    public function editAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $key = $this->getKey();
        $item = $this->service->fetchMetadata($key);
        $this->view->item = $item;
        $this->view->placeholder('title')
            ->set($translator->trans("Edit template: $1", array('$1' => $item->name), 'themes'));

        switch ($item->type) {
            case 'jpg':
            case 'png':
            case 'gif':
                $this->_forward('edit-image');
                break;

            case 'css':
            case 'txt':
            case 'html':
            case 'js':
            case 'tpl':
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
	            $this->_helper->flashMessenger($translator->trans("File $1 was replaced.", array('$1' => basename($key)), 'themes'));
            } catch (\InvalidArgumentException $e) {
                $this->_helper->flashMessenger(array('error', $e->getMessage()));
            }

            $this->_helper->redirector('edit', 'template', 'admin', array(
                'path' => $this->_getParam('path'),
                'file' => $this->_getParam('file'),
                'next' => urlencode($this->_getParam('next')),
                'id' => $this->_getParam('id'),
            ));
        }

        $this->view->replaceForm = $form;
    }

    public function editTemplateAction()
    {
        $key = $this->getKey();

        $translator = \Zend_Registry::get('container')->getService('translator');
        $form = new Admin_Form_Template;
        $form->setMethod('post');

        $metadata = $this->service->fetchMetadata($key);
        $form->setDefaults(array(
            'content' => $this->service->fetchItem($key),
            'cache_lifetime' => $metadata->type == 'tpl' ? $metadata->ttl : 0,
        ));

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {
            $values = $form->getValues();
            $this->service->storeItem($key, $values['content']);
            if ($metadata->type == 'tpl') {
                $this->service->storeMetadata($key, $values);
                $this->_helper->entity->flushManager();
            }

            $cacheService = \Zend_Registry::get('container')->getService('newscoop.cache');
            $cacheService->clearNamespace('template');

            $this->_helper->flashMessenger($translator->trans("Template $1 $2.", array('$1' => basename($key), '$2' => $translator->trans('updated', array(), 'themes')), 'themes'));
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
        $translator = \Zend_Registry::get('container')->getService('translator');
        $path = $this->parsePath();
        $dest = $this->_getParam('name');
        try {
            $files = (array) $this->_getParam('file', array());
            foreach ($files as $file) {
                $s = $this->service->moveItem("$path/$file", $dest);
                $this->_helper->flashMessenger->addMessage($translator->trans("Template $1 $2.", array('$1' => $file, '$2' => $translator->trans('moved', array(), 'themes')), 'themes'));
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
        $translator = \Zend_Registry::get('container')->getService('translator');
        $path = $this->parsePath();
        $file = $this->_getParam('file');
        if (is_array($file)) {
            $file = array_shift($file);
        }

        try {
            $nameExt = pathinfo($this->_getParam('name'),PATHINFO_EXTENSION);
            $name = $this->formatName($this->_getParam('name'), ($nameExt==''?pathinfo($file, PATHINFO_EXTENSION):null) );
            $this->service->copyItem("$path/$file", $name);
		    $this->_helper->flashMessenger($translator->trans("Template $1 was duplicated into $2.", array('$1' => $file, '$2' => $name), 'themes'));
        } catch (\InvalidArgumentException $e) {
            $this->_helper->flashMessenger(array('error', $e->getMessage()));
        }

        $this->_redirect(urldecode($this->_getParam('next')), array(
            'prependBase' => false,
        ));
    }

    public function renameAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $path = $this->parsePath();
        $file = $this->_getParam('file');
        if (is_array($file)) {
            $file = array_shift($file);
        }

        try {
            $name = $this->formatName($this->_getParam('name'), null);
            $this->service->renameItem("$path/$file", $name);
            $this->clearCompiledTemplate("$path/$file");
		    $this->_helper->flashMessenger($translator->trans("Template object $1 was renamed to $2.", array('$1' => $file, '$2' => $name), 'themes'));
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
        $translator = \Zend_Registry::get('container')->getService('translator');
        $path = $this->parsePath();
        $files = $this->_getParam('file', array());
        try {
            foreach ((array) $files as $file) {
                $key = "$path/$file";
                $this->service->deleteItem($key);
                $this->_helper->entity->flushManager();
                $this->clearCompiledTemplate($key);
			    $this->_helper->flashMessenger($translator->trans("Template object $1 was deleted.", array('$1' => $file), 'themes'));
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
        $translator = \Zend_Registry::get('container')->getService('translator');
        $path = $this->parsePath();
        $name = $this->formatName($this->_getParam('name'));

        try {
            $this->service->createFolder(ltrim("$path/$name", ' /'));
		    $this->_helper->flashMessenger($translator->trans("Directory $1 created.", array('$1' => $name), 'themes'));
        } catch (\InvalidArgumentException $e) {
	        $this->_helper->flashMessenger(array('error', $e->getMessage()));
        }

        $this->_redirect(urldecode($this->_getParam('next')), array(
            'prependBase' => false,
        ));
    }

    public function createFileAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $path = $this->parsePath();
        $name = $this->formatName($this->_getParam('name'));

        try {
            $this->service->createFile(ltrim("$path/$name", ' /'));
	        $this->_helper->flashMessenger($translator->trans("New template $1 created.", array('$1' => $name), 'themes'));
        } catch (\InvalidArgumentException $e) {
            $this->_helper->flashMessenger(array('error', $e->getMessage()));
        }

        $this->_redirect(urldecode($this->_getParam('next')), array(
            'prependBase' => false,
        ));
    }

    public function cacheTemplatesAction()
    {
        $path = $this->parsePath();
        $this->service->cacheTemplates($path);
        $this->view->success = true;
    }

    /**
     * Build pages tree for navigation
     *
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
        $translator = \Zend_Registry::get('container')->getService('translator');
        $files = (array) $files;
        $count = sizeof($files);
        if ($count == 1) {
            return $translator->trans("$1 $2", array('$1' => current($files), '$2' => $action), 'themes');
        }

        return $translator->trans("$1 files $2", array('$1' => $count, '$2' => $action), 'themes');
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
        $name = strtr(basename($name), '?~#%*&|"\'\\/<>', '_____________');
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
        $translator = \Zend_Registry::get('container')->getService('translator');
        $form = new Zend_Form;

        $form->addElements( array
        (
            new Zend_Form_Element_Hash('csrf', array('decorators' => array( 'ViewHelper' )) ),
            new Zend_Form_Element_Hidden('name', array('decorators' => array( 'ViewHelper' )) ),
            new Zend_Form_Element_Select('multiaction', array
            (
            	'multioptions' => array
                (
                	'' => $translator->trans('Actions'),
                	'move' => $translator->trans('Move'),
                	'delete' => $translator->trans('Delete'),
                ),
                'decorators' => array( 'ViewHelper' )
            )),
            new Zend_Form_Element_Hidden('action', array
            (
            	'required' => true,
            	'validators' => array
                (
                    array('inArray', true, array
                    (
                        array('copy', 'move', 'rename', 'delete', 'create-file', 'create-folder'),
                    )),
                ),
                'decorators' => array( 'ViewHelper' )
            )),
            new Zend_Form_Element_MultiCheckbox('file')
        ));

        foreach( $form->getElements() as $elem ) {
            $elem->removeDecorator( 'DtDdWrapper' )->removeDecorator( 'Label' );
        }

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
        $multioptionPaths = array();
        foreach (($paths = $this->getPaths('')) as $v)
            $multioptionPaths[$v] = $v;
        $form->addElement('select', 'name', array(
            'multioptions' => array('/' => '/') + $multioptionPaths,
        ));

        return $form;
    }

    private $reqCachePaths = array();

    /**
     * Get available paths starting from path
     *
     * @param string $path
     * @return array
     */
    private function getPaths($path)
    {
        return ( $this->reqCachePaths = $this->service->listPaths($path) );
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
		    CampTemplate::singleton()->clearCompiledTemplate($filename);
        } catch (Exception $e) { // ignore file not found
        }
    }
}
