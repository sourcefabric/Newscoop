<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Annotations\Acl;
use Newscoop\Service\Exception\RemoveThemeException;
use Newscoop\Entity\OutputSettings;
use Newscoop\Service\IArticleTypeService;
use Newscoop\Entity\Repository\ArticleTypeRepository;
use Newscoop\Entity\ArticleType;
use Newscoop\Entity\Resource;
use Newscoop\Controller\Action\Helper\Datatable\Adapter\Theme;
use Newscoop\Controller\Action\Helper\Datatable\Adapter\ThemeFiles;
use Newscoop\Service\IPublicationService;
use Newscoop\Service\IThemeManagementService;
use Newscoop\Service\Resource\ResourceId;
use Newscoop\Service\IThemeService;
use Newscoop\Service\Model\SearchTheme;
use Newscoop\Service\PublicationServiceDoctrine;
use Newscoop\Entity\Theme\Loader\LocalLoader;
use Newscoop\Service\IOutputService;
use Newscoop\Service\Exception\DuplicateNameException;
use Newscoop\Entity\Output;

/**
 * Themes Controller
 * @Acl(resource="theme", action="manage")
 */
class Admin_ThemesController extends Zend_Controller_Action
{

    /**
     * No idea what this should be
     * @var unknown_type
     */
    private $_repository;

    /**
     * @var Newscoop\Services\Resource\ResourceId
     */
    private $_resourceId = NULL;

    /**
     * @var Newscoop\Service\IThemeManagementService
     */
    private $_themeService = NULL;

    /**
     * @var Newscoop\Service\IPublicationService
     */
    private $_publicationService = NULL;

    /**
     * @var Newscoop\Service\ThemeServiceLocalFileSystem
     */
    private $_themeFileService = NULL;

    /**
     * @var Newscoop\Service\IOutputService
     */
    private $_outputService = NULL;

    /**
     * @var Newscoop\Service\IArticleTypeService
     */
    private $_articleTypeService = NULL;


    /**
     * Provides the controller resource id.
     *
     * @return Newscoop\Services\Resource\ResourceId
     * The controller resource id.
     */
    public function getResourceId()
    {
        if( $this->_resourceId === NULL ) {
            $this->_resourceId = new ResourceId( __CLASS__ );
        }
        return $this->_resourceId;
    }

    /**
     * Provides the theme service.
     *
     * @return Newscoop\Service\Implementation\ThemeManagementServiceLocal
     */
    public function getThemeService()
    {
        if( $this->_themeService === NULL ) {
            $this->_themeService = $this->getResourceId()->getService( IThemeManagementService::NAME_1 );
        }
        return $this->_themeService;
    }

	/**
     * Provides the theme files service
     *
     * @return Newscoop\Service\Implementation\ThemeServiceLocalFileSystem
     * The publication service to be used by this controller.
     */
    public function getThemeFileService( )
    {
        if( $this->_themeFileService === NULL ) {
            $this->_themeFileService = $this->getResourceId()->getService( IThemeService::NAME );
        }
        return $this->_themeFileService;
    }

	/**
	 * Provides the ouput service.
	 *
	 * @return Newscoop\Service\IOutputService
	 *		The service service to be used by this controller.
	 */
	public function getOutputService()
	{
		if ($this->_outputService === NULL) {
			$this->_outputService = $this->getResourceId()->getService( IOutputService::NAME );
		}
		return $this->_outputService;
	}

	/**
     * Provides the publication service
     *
     * @return Newscoop\Service\IPublicationService
     * The publication service to be used by this controller.
     */
    public function getPublicationService()
    {
        if( $this->_publicationService === NULL ) {
            $this->_publicationService = $this->getResourceId()->getService( IPublicationService::NAME );
        }
        return $this->_publicationService;
    }

	/**
     * Provides the theme service.
     *
     * @return Newscoop\Service\Implementation\ArticleTypeServiceDoctrine
     */
    public function getArticleTypeService()
    {
        if( $this->_articleTypeService === NULL ) {
            $this->_articleTypeService = $this->getResourceId()->getService( IArticleTypeService::NAME );
        }
        return $this->_articleTypeService;
    }

    public $instId = null;
    public function init()
    {
        $this->getThemeService();

        // TODO move this + callbacks from here to a higher level
        if( !$this->_helper->contextSwitch->hasContext( 'adv' ) )
        {
            $this->_helper->contextSwitch->addContext( 'adv', array
            (
            	'suffix' => 'adv',
                'callbacks' => array
                (
                	'init' => array( $this, 'initAdvContext' ),
                	'post' => array( $this, 'postAdvContext' )
                )
            ) );
        }

        // init ajax contexts.. actually json contexts
        // TODO see why ajax context is not working
        $this->_helper->contextSwitch
            ->addActionContext( 'index', 'json' )
            ->addActionContext( 'assign-to-publication', 'json' )
            ->addActionContext( 'copy-to-available', 'json' )
            ->addActionContext( 'output-edit', 'json' )
            ->addActionContext( 'article-types-edit', 'json' )
            ->addActionContext( 'unassign', 'json' )
            ->addActionContext( 'wizard-theme-settings', 'adv' )
            ->addActionContext( 'wizard-theme-template-settings', 'adv' )
            ->addActionContext( 'wizard-theme-article-types', 'adv' )
            ->addActionContext( 'wizard-theme-files', 'adv' )
            ->initContext();
    }

    public function initAdvContext()
    {
        $this->_helper->layout()->enableLayout();
    }

    public function postAdvContext()
    {

    }

    public function indexAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $datatableAdapter = new Theme( $this->getThemeService() );
        // really wierd way to bind some filtering logic right here
        // basically this is the column index we are going to look for filtering requests
        $datatableAdapter->setPublicationFilterColumn(0);

        $datatable = $this->_helper->genericDatatable;
        /* @var $datatable Action_Helper_GenericDatatable */
        $datatable->setAdapter( $datatableAdapter )->setOutputObject( $this->view );

        $view = $this->view;
        $datatable            // setting options for the datatable
            ->setCols( array
            (
                'image'        => '',
                'name'         => $translator->trans( 'Theme name / version' , array(), 'themes'),
                'description'  => $translator->trans( 'Compatibility' , array(), 'themes'),
                'actions'      => ''
            ))
            ->buildColumnDefs()
            ->setOptions( array
            (
                'sAjaxSource' => $this->view->url( array( 'action' => 'index', 'format' => 'json') ),
            	'sPaginationType' => 'full_numbers',
            	'bServerSide'    => true,
            	'bJQueryUI'      => true,
            	'bAutoWidth'     => false,
                'sDom'		     => 'tiprl',
            	'iDisplayLength' => 25,
            	'bLengthChange'  => false,
                'fnRowCallback'	 => "newscoopDatatables.callbackRow",
                'fnDrawCallback' => "newscoopDatatables.callbackDraw",
                'fnServerData'	 => "newscoopDatatables.callbackServerData"
            ) )
            ->setWidths( array( 'image' => 215, 'name' => 235, 'description' => 280, 'actions' => 115 ) )
            ->setRowHandler(function($theme, $index = null) {
                return array(
                    "id" => $theme['id'],
                    "images" => $theme['images'],
                    "title" => htmlspecialchars($theme['title']),
                    "designer" => htmlspecialchars($theme['designer']),
                    "version" => htmlspecialchars($theme['version']),
                    "compat" => htmlspecialchars($theme['subTitle']),
                    "text" => htmlspecialchars($theme['description']),
                    "pubId" => $theme['pubId'],
                );
            })
            ->setParams( $this->_request->getParams() );

        if( ( $this->view->mytable = $datatable->dispatch() ) )
        {
            $this->view->publications  = $this->getPublicationService()->getEntities();
            $this->view->themesPath    = $this->view->baseUrl( '/themes' );

            $uploadForm    = new Admin_Form_Theme_Upload();
            $uploadForm // set some specific stuff for this page
                ->setAction( $this->view->url( array( 'action' => 'upload' ) ) )
                ->addElement( 'hidden', 'format', array( 'value' => 'json', 'decorators' => array( 'ViewHelper' ) ) )
                ->getElement( 'submit-button' )->clearDecorators()->addDecorator( 'ViewHelper' )->setAttrib( 'style', 'display:none' );
            $this->view->uploadForm = $uploadForm;

            $this->view->headScript()->appendFile( $this->view->baseUrl( "/js/jquery/doT.js" ) );
            $this->view->headLink( array
            (
            	'type'  =>'text/css',
            	'href'  => $this->view->baseUrl('/admin-style/themes_list.css'),
                'media'	=> 'screen',
                'rel'	=> 'stylesheet'
            ) );
            $this->view->headLink( array
            (
            	'type'  =>'text/css',
            	'href'  => $this->view->baseUrl('/admin-style/action_buttons.css'),
                'media'	=> 'screen',
                'rel'	=> 'stylesheet'
            ) );
            $this->view->placeholder( 'title' )->set( $translator->trans( 'Theme management' , array(), 'themes') );
        }
    }

    public function wizardThemeSettingsAction()
    {
        $theme = $this->getThemeService()->findById( $this->_request->getParam( 'id' ) );
        // setup the theme settings form
        $themeForm = new Admin_Form_Theme();
        $themeForm->setDefaults(array(
            'name' => $theme->getName(),
            'theme-version' => (string) $theme->getVersion(),
            'required-version' => (string) $theme->getMinorNewscoopVersion(),
        ));

        $request = $this->getRequest();
        if ($request->isPost() && $themeForm->isValid($request->getPost())) {
            $values = $themeForm->getValues();
            $theme->setName($values['name']);
            $this->getThemeService()->updateTheme($theme);
        }

        $this->view->themeForm = $themeForm;
    }

    /**
     * see Admin_ThemesController::outputEditAction()
     */
    public function wizardThemeTemplateSettingsAction()
    {
        $themeId = $this->_request->getParam( 'id' );
        $thmServ = $this->getThemeService();
        $theme   = $thmServ->findById( $themeId );
        $outServ = $this->getOutputService();
        foreach( ( $outputs = $outServ->getEntities() ) as $k => $output )
            $outSets[] = $thmServ->findOutputSetting( $theme, $output );

        $this->view->jQueryUtils()
            ->registerVar
            (
                'load-output-settings-url',
                $this->_helper->url->url( array
                (
                	'action' => 'output-edit',
                	'controller' => 'themes',
                    'module' => 'admin',
                    'themeid' => '$1',
                    'outputid' => '$2'
                ), null, true, false )
            );
        $this->view->theme          = $theme->toObject();
        $this->view->outputs        = $outputs;
        $this->view->outputSettings = $outSets;
    }

    public function wizardThemeArticleTypesAction()
    {
        $theme = $this->getThemeService()->findById( $this->_request->getParam( 'id' ) );
        $themeArticleTypes = $this->getThemeService()->getArticleTypes( $theme );
        $this->view->themeArticleTypes = $themeArticleTypes;
        $articleTypes = array();
        foreach( $this->getArticleTypeService()->findAllTypes() as $at )
        {
            $atName = $at->getName();
            $articleTypes[$atName] = array();
            foreach( $this->getArticleTypeService()->findFields( $at ) as $atf ) {
                $articleTypes[$atName][] = $atf->getName();
            }
        }

        $this->view->theme            = $theme->toObject();
        $this->view->articleTypes     = (object) $articleTypes;
        $this->view->articleTypeNames = array_keys( $articleTypes );

        $this->view->jQueryUtils()->registerVar( 'articleTypes', $articleTypes );
        $this->view->jQueryUtils()->registerVar( 'themeArticleTypes', $themeArticleTypes );
    }

    public function wizardThemeFilesAction()
    {

    }

    public function advancedThemeSettingsAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->view->themeId = ( $themeId = $this->_request->getParam( 'id' ) );
        $this->view->headLink( array
        (
        	'type'  =>'text/css',
        	'href'  => $this->view->baseUrl('/admin-style/common.css'),
            'media'	=> 'screen',
            'rel'	=> 'stylesheet'
        ) );

        $params = $this->getRequest()->getParams();
        $this->view->templatesParams = $params;

        $this->view->placeholder( 'title' )->set( $translator->trans( 'Theme management' , array(), 'themes') );

        $themeMngService = $this->getThemeService();
        /* @var $themeMngService Newscoop\Service\Implementation\ThemeManagementServiceLocal */
        $theme = $themeMngService->getById($themeId);
        $this->view->placeholder( 'title' )->append( ": " . $this->view->escape($theme->getName()) );
        if (($publication = $themeMngService->getThemePublication($theme))) {
            $this->view->placeholder( 'title' )->append( " - ".$this->view->escape($publication->getName()) );
        }
    }

    /**
     *
     * called by wizard template action
     */
    public function outputEditAction()
    {
        $thmServ    = $this->getThemeService();
        $translator = \Zend_Registry::get('container')->getService('translator');

        // getting the theme entity
        $themeId    = $this->_request->getParam( 'themeid' );
        $theme      = $thmServ->findById( $themeId );

        // getting selected output
        $outputId   = $this->_request->getParam( 'outputid' );
        $output     = $this->getOutputService()->getById( $outputId );
        /* @var $settings Newscoop\Entity\Output */

        $templates = array();
        // getting all available templates
        foreach( $thmServ->getTemplates( $theme ) as $tpl ) {
        	/* @var $tpl Newscoop\Entity\Resource */
            $templates[ $tpl->getPath() ] = $tpl->getName(); // couldn't get id cause it's null :) :) :)
        }

        // making the form
        $outputForm = new Admin_Form_Theme_OutputSettings();
        $outputForm->setAction( $this->_helper->url( 'output-edit' ) );

        // getting theme's output settings
        $settings   = $thmServ->findOutputSetting( $theme, $output );
        /* @var $settings Newscoop\Entity\OutputSettings */

        $settingVals = array
        (
            "frontpage"	  => null,
        	"articlepage" => null,
        	"sectionpage" => null,
        	"errorpage"	  => null,
            "outputid"	  => $outputId,
            "themeid"	  => $themeId
        );
        if( $settings instanceof  OutputSettings )
        {
            $settingVals["frontpage"]  = $settings->getFrontPage();
        	$settingVals["articlepage"] = $settings->getArticlePage();
        	$settingVals["sectionpage"] = $settings->getSectionPage();
        	$settingVals["errorpage"]   = $settings->getErrorPage();
        }
        $outputForm->setValues( $templates, $settingVals );

        try // @todo maybe implement this a little smarter, little less code?
        {
            if( $this->_request->isPost() ) {
                if( $outputForm->isValid( $this->_request->getPost() ) ) {
                    $settings->setFrontPage( new Resource( $outputForm->getValue( 'frontpage' ) ) );
                    $settings->setSectionPage( new Resource( $outputForm->getValue( 'sectionpage' ) ) );
                    $settings->setArticlePage( new Resource( $outputForm->getValue( 'articlepage' ) ) );
                    $settings->setErrorPage( new Resource( $outputForm->getValue( 'errorpage' ) ) );

                    $this->getThemeService()->assignOutputSetting( $settings, $theme );

                    $msg = $translator->trans( 'Theme settings saved.' , array(), 'themes') ;
                    $this->view->success = $msg;
                    $this->_helper->flashMessenger( $msg );
                }
                else {
                    throw new \Exception();
                }
            }
        }
        catch( \Exception $e )
        {
//            $this->_helper->flashMessenger( ( $this->view->error = $translator->trans( 'Saving settings failed.' ) ) );
        }
        $this->view->outputForm = $outputForm;

        // disabling layout for ajax and hide the submit button
        if( $this->_request->isXmlHttpRequest() )
        {
            $this->_helper->layout->disableLayout();
            $outputForm->getElement( 'submit' )
                ->clearDecorators()
                ->setAttrib( 'style', 'display:none' );
        }
    }

    public function articleTypesEditAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $thmServ                = $this->getThemeService();

        // getting the theme entity
        $themeId                = $this->_request->getParam( 'id' );
        $theme                  = $thmServ->findById( $themeId );

        $updateArticleTypes     = array(); // for xml updating
        $createArticleTypes     = array(); // for db updating

        // process the request matching
        $articleTypeIgnore      = $this->_request->getPost( 'articleTypeIgnore' );
        $articleTypeCreate      = $this->_request->getPost( 'articleTypeCreate' );
        $articleTypeFieldIgnore = $this->_request->getPost( 'articleTypeFieldIgnore' );
        $articleTypeFieldCreate = $this->_request->getPost( 'articleTypeFieldCreate' );

        $articleTypes           = $this->_request->getPost( 'articleTypes' );
        $articleTypeFields      = $this->_request->getPost( 'articleTypeFields' );
        $themeArticleTypeFields = $this->_request->getPost( 'themeArticleTypeFields', array() );
        $themeArticleTypes      = $this->_request->getPost( 'themeArticleTypes' );

        // complex logic for matching
        foreach( $themeArticleTypeFields as $typeName => $fields )
        {
            $updateArticleTypes[ $typeName ] = array( 'name' => '', 'ignore' => false, 'fields' => array() );

            if( intval( $articleTypeIgnore[ $typeName ] ) == 0 ) // replace type with new one
            {
                if( isset( $articleTypes[ $typeName ] ) ) // type from db system to xml
                {
                    $updateArticleTypes[ $typeName ]['name'] = $articleTypes[ $typeName ];
                }
                if( intval( $articleTypeCreate[ $typeName ] ) == 1 ) // create article type in the system
                {
                    $createArticleTypes[ $typeName ]['name'] =
                        $updateArticleTypes[ $typeName ]['name'] = $typeName;
                }
            }
            else // leave it as it is
            {
                $updateArticleTypes[ $typeName ] = array( 'name' => $typeName, 'ignore' => true, 'fields' => array() );
            }

            foreach( $fields as $fieldName ) // process fields, same as above
            {
                // need to pass article type value for matching with the system
                if( intval( $articleTypeFieldIgnore[ $typeName ][ $fieldName ] ) == 0 )
                {
                    if( isset( $articleTypeFields[ $typeName ][ $fieldName ] ) )
                    {
                        $updateArticleTypes[ $typeName ]['fields'][$fieldName] =
                            array
                            (
                            	'name' => $articleTypeFields[ $typeName ][ $fieldName ],
                            	'parentType' => $articleTypes[ $typeName ],
                            	'ignore' => false
                            );
                    }
                    if( intval( $articleTypeFieldCreate[ $typeName ][ $fieldName ] ) == 1 )
                    {
                        $updateArticleTypes[ $typeName ]['fields'][$fieldName] =
                            $createArticleTypes[ $typeName ]['fields'][] =
                                array
                                (
                                	'name' => $fieldName,
                                	'parentType' => $articleTypes[ $typeName ],
                                	'ignore' => false
                                );
                    }
                }
                else
                {
                    $updateArticleTypes[ $typeName ][ 'fields' ][$fieldName]
                        = array( 'name' => $fieldName, 'parentType' => $articleTypes[ $typeName ], 'ignore' => true );
                }
            }

        }

        $artServ = $this->getArticleTypeService();
        $themeArticleTypes = (array) $this->getThemeService()->getArticleTypes( $theme );
        foreach( $createArticleTypes as $typeName => $type )
        {
            // TODO pass if not found in xml?
            if( !isset($themeArticleTypes[$typeName]) )
            {
                unset( $createArticleTypes[$typeName] );
                continue;
            }
            if( isset( $type['fields'] ) && is_array( $type['fields'] ) ) {
                foreach( $type['fields'] as $k => $field )
                {
                    $createArticleTypes[$typeName]['fields'][$k]['props']
                        = (array) $themeArticleTypes[$typeName]->{$field['name']};
                }
            }
        }

        $artServ->createMany( $createArticleTypes );

        $this->view->response = $thmServ->assignArticleTypes( $updateArticleTypes, $theme );

        $this->_helper->flashMessenger($translator->trans('Theme settings updated.', array(), 'themes'));

    }

    public function deleteAction()
    {
        $this->_forward( 'unassign', null, null, array('p_forwardedFrom' => 'deleteAction'));
    }

    public function unassignAction()
    {
        $translator = \Zend_Registry::get('container')->getService('translator');
        $p_forwardedFrom = $this->_request->getParam( 'p_forwardedFrom' );
        if( ( $themeId = $this->_getParam( 'id', null ) ) ) {
            try
            {
            	if($p_forwardedFrom == 'deleteAction') {
                    global $Campsite;
                    $themeIsAssigned = FALSE;
                    $theme = $this->getThemeService()->getById($themeId);
                    $themeName = $theme->getName();
                    foreach($Campsite['publications'] as $publication) {
                    	$pub = $this->getPublicationService()->findById( $publication->getPublicationId() );
	                    foreach($this->getThemeService()->getThemes($pub) as $th){
				            if( trim($th->getName()) === trim($themeName) ){
                                $themeIsAssigned = TRUE;
                                break 2;
				            }
				        }
                    }
                    if($themeIsAssigned) {
                       throw new Exception('Theme is assigned and can not be deleted', 500);
                    }
            	}

                $this->getThemeService()->removeTheme($themeId);
                $this->_helper->service('image.rendition')->reloadRenditions();
                $this->view->status = true;
                $this->view->response = $translator->trans( "Unassign successful" , array(), 'themes');
            }
            catch( RemoveThemeException $e )
            {
                $this->view->status = false;
                $this->view->response = $translator->trans("The theme can not be unassigned because it is in use by issues ($1) in this publication", array('$1' => $e->getMessage()), 'themes');
            }
            catch( Exception $e )
            {
                $this->view->status = false;
                if( $e->getCode() == 500) {
                    $this->view->response = $translator->trans( "Theme is assigned and can not be deleted" , array(), 'themes');
                } else {
                    $this->view->response = $translator->trans( "Failed unassigning theme" , array(), 'themes');
                }

            }
        }
    }

    public function uploadAction()
    {
        try
        {
            $this->view->response = $this->getThemeService()->installTheme( $_FILES['browse']['tmp_name'] );
        }
        catch( \Exception $e )
        {
            $this->view->response = false;
        }
        if( $this->_getParam( 'format' ) == 'json' )
        {
            $this->_helper->layout()->disableLayout();
            $this->_helper->viewRenderer->setNoRender();
            $vars = Zend_Json::encode($this->view->getVars());
            $this->getResponse()->setBody($vars);
        }
    }

    public function exportAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $erro_msg = $translator->trans('Theme export was not successful. Check please that the server is not out of disk space.', array(), 'themes');

        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        if( ( $themeId = $this->_getParam( 'id', null ) ) )
        {
            // it looks that a problem could happen here if the server is out of its disk space
            try {
                $exportPath = $this->getThemeService()
                    ->exportTheme( ( $themeEntity = $this->getThemeService()->findById( $themeId ) ), $erro_msg );
                if (false === $exportPath) {
                    die($erro_msg);
                }

                // the Content-Type header would be set wrongly at ContentType.php
                $GLOBALS['header_content_type_set'] = true;

                // Chrome complains when the Content-Disposition header is set by Zend.
                $theme_name = trim(str_replace(array('"', ' '), array('_', '_'), $themeEntity->getName()));
                header('Content-Disposition: attachment; filename="' . $theme_name . '.zip"');

                $this->getResponse()
                    ->setHeader( 'Content-type', 'application/zip' )
                    //->setHeader( 'Content-Disposition', 'attachment; filename="'.$themeEntity->getName().'.zip"' )
                    ->setHeader( 'Content-length', filesize( $exportPath ) )
                    ->setHeader( 'Cache-control', 'private' );

                if(!@readfile( $exportPath )) {
                    die($erro_msg);
                }
                $this->getResponse()->sendResponse();
            }
            catch (Exception $exc) {
                echo $erro_msg;
            }
        }
    }

    function assignToPublicationAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        try
        {
            $theme  = $this->getThemeService()->getById( $this->_request->getParam( 'theme-id' ) );
		    $pub    = $this->getPublicationService()->findById( $this->_request->getParam( 'pub-id' ) );

		    if( $this->getThemeService()->assignTheme( $theme, $pub ) ) {
                $this->_helper->service('image.rendition')->reloadRenditions();
		        $this->view->response =  $translator->trans( 'Assigned successfully' , array(), 'themes');
		    } else {
    		    throw new Exception();
		    }
        }
        catch( DuplicateNameException $e ) {
            $this->view->exception = array( "code" => $e->getCode(), "message" => $translator->trans( 'Duplicate assignment' , array(), 'themes') );
        }
        catch( \Exception $e ) {
            $this->view->exception = array( "code" => $e->getCode(), "message" => $translator->trans( 'Something broke' , array(), 'themes') );
        }
    }

    function copyToAvailableAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        try {
            $theme = $this->getThemeService()->getById($this->_getParam('theme-id'));
            $this->getThemeService()->copyToUnassigned($theme);
            $this->view->response = $translator->trans('Copied successfully', array(), 'themes');
        } catch (DuplicateNameException $e) {
            $this->view->exception = array(
                'code' => $e->getCode(),
                'message' => $translator->trans('Duplicate assignment', array(), 'themes'),
            );
        } catch(\Exception $e) {
            $this->view->exception = array(
                'code' => $e->getCode(),
                'message' => $translator->trans('Something broke', array(), 'themes')
            );
        }
    }

    public function installAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->_repository->install( $this->_getParam( 'offset' ) );
        $this->_helper->entity->flushManager();

        $this->_helper->flashMessenger( $translator->trans( 'Theme $1', array('$1' => $translator->trans('installed' , array(), 'themes')), 'themes') );
        $this->_helper->redirector( 'index' );
    }

    public function uninstallAction()
    {   
        $translator = \Zend_Registry::get('container')->getService('translator');
        $this->_repository->uninstall( $this->_getParam( 'id' ) );
        $this->_helper->entity->flushManager();

        $this->_helper->flashMessenger( $translator->trans( 'Theme $1', array('$1' => $translator->trans( 'deleted' , array(), 'themes')), 'themes') );
        $this->_helper->redirector( 'index' );
    }

}

