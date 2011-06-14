<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
use Newscoop\Service\IArticleTypeService,
    Newscoop\Entity\Repository\ArticleTypeRepository,
    Newscoop\Entity\ArticleType,
    Newscoop\Entity\Resource,
    Newscoop\Controller\Action\Helper\Datatable\Adapter\Theme,
    Newscoop\Controller\Action\Helper\Datatable\Adapter\ThemeFiles,
    Newscoop\Service\IPublicationService,
    Newscoop\Service\IThemeManagementService,
    Newscoop\Service\Resource\ResourceId,
    Newscoop\Service\IThemeService,
    Newscoop\Service\Model\SearchTheme,
    Newscoop\Service\PublicationServiceDoctrine,
    Newscoop\Entity\Theme\Loader\LocalLoader,
    Newscoop\Service\IOutputService,
    Newscoop\Service\Exception\DuplicateNameException,
    Newscoop\Entity\Output
    ;

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
        $this->view->placeholder( 'title' )->set( getGS( 'Theme management' ) );

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
        $datatableAdapter = new Theme( $this->getThemeService() );
        // really wierd way to bind some filtering logic right here
        // basically this is the column index we are going to look for filtering requests
        $datatableAdapter->setPublicationFilterColumn(4);

        $datatable = $this->_helper->genericDatatable;
        /* @var $datatable Action_Helper_GenericDatatable */
        $datatable->setAdapter( $datatableAdapter )->setOutputObject( $this->view );

        $view = $this->view;
        $datatable            // setting options for the datatable
            ->setCols( array
            (
                'checkbox'	   => '',
                'image'        => '',
                'name'         => getGS( 'Theme name / version' ),
                'description'  => getGS( 'Compatibility' ),
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
                'fnInitComplete' => "newscoopDatatables.callbackInit"
            ) )
            ->setWidths( array( 'checkbox' => 20, 'image' => 215, 'name' => 235, 'description' => 280, 'actions' => 115 ) )
            ->setRowHandler
            (
                function( $theme, $index = null )
                {
                    return array
                    (
                    	"id"       => $theme['id'],
                    	"images"   => $theme['images'],
                        "title"    => $theme['title'],
                        "designer" => $theme['designer'],
                        "version"  => $theme['version'],
                    	"compat"   => $theme['subTitle'],
                    	"text"     => $theme['description'],
                        "pubId"	   => $theme['pubId']
                    );
                }
            )
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

            $this->view->headScript()->appendFile( $this->view->baseUrl( "/js/jquery/jquery.tmpl.js" ) );
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
        }
    }

    public function wizardThemeSettingsAction()
    {
        $theme = $this->getThemeService()->findById( $this->_request->getParam( 'id' ) );
        // setup the theme settings form
        $themeForm = new Admin_Form_Theme();
        $themeForm->populate( array
        (
        	"theme-version"    => (string) $theme->getVersion(),
        	"required-version" => (string) $theme->getMinorNewscoopVersion()
        ) );
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
            foreach( $this->getArticleTypeService()->findFields( $at ) as $atf )
                $articleTypes[$atName][] = $atf->getName();
        }

        $this->view->theme            = $theme->toObject();
        $this->view->articleTypes     = (object) $articleTypes;
        $this->view->articleTypeNames = array_keys( $articleTypes );

        $this->view->jQueryUtils()->registerVar( 'articleTypes', $articleTypes );
        $this->view->jQueryUtils()->registerVar( 'themeArticleTypes', $themeArticleTypes );
    }

    public function wizardThemeFilesAction()
    {
        $themeId          = $this->_request->getParam( 'id' );
        $datatableAdapter = new ThemeFiles( $this->getThemeService(), $themeId );
        $datatable        = $this->_helper->genericDatatable;
        /* @var $datatable Action_Helper_GenericDatatable */
        $datatable
            ->setAdapter( $datatableAdapter )
            ->setOutputObject( $this->view )
            ->setCols( array
            (
                'checkbox'	=> '',
                'name'      => getGS( 'Name' ),
                'id'        => getGS( 'ID' ),
                'type'	    => getGS( 'Type' ),
                'cache'     => getGS( 'Cache lifetime, sec.' ),
                'modified'	=> getGS( 'Modified' ),
                'actions'	=> getGS( 'Action' )
            ))
            ->buildColumnDefs()
            ->setOptions( array
            (
                'sAjaxSource' => $this->view->url( array( 'action' => 'wizard-theme-files', 'format' => 'json') ),
            	'sPaginationType' => 'full_numbers',
            	'bServerSide'    => true,
            	'bJQueryUI'      => true,
            	'bAutoWidth'     => false,
                'sDom'		     => 'tiprl',
            	'iDisplayLength' => 25,
            	'bLengthChange'  => false,
                'fnRowCallback'	 => "newscoopDatatables.callbackRow",
                'fnDrawCallback' => "newscoopDatatables.callbackDraw",
                'fnInitComplete' => "newscoopDatatables.callbackInit"
            ) )
            ->setWidths( array
            (
            	'checkbox'  => 20,
            	'name'      => 150,
            	'id'        => 150,
            	'type'      => 150,
                'cache'	    => 150,
            	'modified'  => 150,
                'actions'	=> 150
            ) )
            ->setRowHandler
            (
                function( $theme, $index = null )
                {
                    return array
                    (

                    );
                }
            )
            ->setParams( $this->_request->getParams() );

        if( ( $this->view->mytable = $datatable->dispatch() ) )
        {
            $this->view->publications  = $this->getPublicationService()->getEntities();
            $this->view->uploadForm    = new Admin_Form_Theme_Upload();
            $this->view->themesPath    = $this->view->baseUrl( '/themes' );
            $this->view->headScript()->appendFile( $this->view->baseUrl( "/js/jquery/jquery.tmpl.js" ) );
        }
    }

    public function advancedThemeSettingsAction()
    {
        $this->view->themeId = $this->_request->getParam( 'id' );
        $this->view->headLink( array
        (
        	'type'  =>'text/css',
        	'href'  => $this->view->baseUrl('/admin-style/common.css'),
            'media'	=> 'screen',
            'rel'	=> 'stylesheet'
        ) );

        $params = $this->getRequest()->getParams();
        $this->view->templatesParams = $params;
    }

    /**
     *
     * called by wizard template action
     */
    public function outputEditAction()
    {
        $thmServ    = $this->getThemeService();

        // getting the theme entity
        $themeId    = $this->_request->getParam( 'themeid' );
        $theme      = $thmServ->findById( $themeId );

        // getting selected output
        $outputId   = $this->_request->getParam( 'outputid' );
        $output     = $this->getOutputService()->getById( $outputId );
        /* @var $settings Newscoop\Entity\Output */

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
        	"frontpage"   => $settings->getFrontPage(),
        	"articlepage" => $settings->getArticlePage(),
        	"sectionpage" => $settings->getSectionPage(),
        	"errorpage"   => $settings->getErrorPage(),
            "outputid"	  => $outputId,
            "themeid"	  => $themeId
        );
        $outputForm->setValues( $templates, $settingVals );

        try // @todo maybe implement this a little smarter, little less code?
        {
            if( $this->_request->isPost() ) {
                if( $outputForm->isValid( $this->_request->getPost() ) )
                {
                    $settings->setFrontPage( new Resource( $outputForm->getValue( 'frontpage' ) ) );
                    $settings->setSectionPage( new Resource( $outputForm->getValue( 'sectionpage' ) ) );
                    $settings->setArticlePage( new Resource( $outputForm->getValue( 'articlepage' ) ) );
                    $settings->setErrorPage( new Resource( $outputForm->getValue( 'errorpage' ) ) );

                    $this->getThemeService()->assignOutputSetting( $settings, $theme );

                    $this->_helper->flashMessenger( ( $this->view->success = getGS( 'Settings saved.' ) ) );
                }
                else
                {
                    throw new \Exception();
                }
            }
        }
        catch( \Exception $e )
        {
            $this->_helper->flashMessenger( ( $this->view->error = getGS( 'Saving settings failed.' ) ) );
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
        $themeArticleTypeFields = $this->_request->getPost( 'themeArticleTypeFields' );
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

        //print '===create===';
        //var_dump( $createArticleTypes );

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

        //print '===update===';
        //var_dump( $updateArticleTypes );
        //exit;

        $this->view->response = $thmServ->assignArticleTypes( $updateArticleTypes, $theme );

    }

    public function deleteAction()
    {
        $this->_forward( 'unassign' );
    }

    public function unassignAction()
    {
        if( ( $themeId = $this->_getParam( 'id', null ) ) ) {
            $this->view->response = $this->getThemeService()->removeTheme($themeId);
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
        $this->_helper->layout()->disableLayout();
        $this->_helper->viewRenderer->setNoRender();
        if( ( $themeId = $this->_getParam( 'id', null ) ) )
        {
            $exportPath = $this->getThemeService()
                ->exportTheme( ( $themeEntity = $this->getThemeService()->findById( $themeId ) ) );

            $this->getResponse()
                ->setHeader( 'Content-type', 'application/zip' )
                ->setHeader( 'Content-Disposition', 'attachment; filename="'.$themeEntity->getName().'.zip"' )
                ->setHeader( 'Content-length', filesize( $exportPath ) )
                ->setHeader( 'Cache-control', 'private' );
            readfile( $exportPath );
            $this->getResponse()->sendResponse();
        }
    }

    function assignToPublicationAction()
    {
        try
        {
            $theme  = $this->getThemeService()->getById( $this->_request->getParam( 'theme-id' ) );
		    $pub    = $this->getPublicationService()->findById( $this->_request->getParam( 'pub-id' ) );

		    if( $this->getThemeService()->assignTheme( $theme, $pub ) ) {
		        $this->view->response =  getGS( 'Assigned successfully' );
		    } else {
    		    throw new Exception();
		    }
        }
        catch( DuplicateNameException $e ) {
            $this->view->exception = array( "code" => $e->getCode(), "message" => getGS( 'Duplicate assignation' ) );
        }
        catch( \Exception $e ) {
            $this->view->exception = array( "code" => $e->getCode(), "message" => getGS( 'Something broke' ) );
        }

    }

    public function testAction()
    {
        $theme = $this->getThemeService()->findById( $this->_request->getParam( 'id' ) );
        //$this->getThemeFileService();
        var_dump( $this->getThemeService()->getArticleTypes($theme ) );die;
        $artServ = $this->getArticleTypeService();
        var_dump( $artServ->findTypeByName( 'news' ) );
        /*$k=1;
        foreach( $artServ->findAllTypes() as $at )
        {
            echo $at->getName(),($k++)," <br />";
            foreach( $artServ->findFields( $at ) as $af )

              echo $af->getName(), " type: ", $at->getName(), " ~= type from field: ", $af->getType()->getName(), "<br />";
        } */
        die;
    }

    public function installAction()
    {
        $this->_repository->install( $this->_getParam( 'offset' ) );
        $this->_helper->entity->flushManager();

        $this->_helper->flashMessenger( getGS( 'Theme $1', getGS( 'installed' ) ) );
        $this->_helper->redirector( 'index' );
    }

    public function uninstallAction()
    {
        $this->_repository->uninstall( $this->_getParam( 'id' ) );
        $this->_helper->entity->flushManager();

        $this->_helper->flashMessenger( getGS( 'Theme $1', getGS( 'deleted' ) ) );
        $this->_helper->redirector( 'index' );
    }

}

