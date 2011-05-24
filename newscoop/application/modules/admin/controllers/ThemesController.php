<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
use Newscoop\Controller\Action\Helper\Datatable\Adapter\Theme,
    Newscoop\Controller\Action\Helper\Datatable\Adapter\ThemeFiles,
    Newscoop\Service\IPublicationService,
    Newscoop\Service\IThemeManagementService,
    Newscoop\Service\Resource\ResourceId, 
    Newscoop\Service\IThemeService, 
    Newscoop\Service\Model\SearchTheme,
    Newscoop\Service\PublicationServiceDoctrine,
    Newscoop\Entity\Theme\Loader\LocalLoader,
    Newscoop\Service\IOutputService,
    Newscoop\Service\Exception\DuplicateNameException
    ;

/**
 * Themes Controller
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
     * @return Newscoop\Service\IThemeManagementService
     * The theme service to be used by this controller.
     */
    public function getThemeService()
    {
        if( $this->_themeService === NULL ) {
            $this->_themeService = $this->getResourceId()->getService( IThemeManagementService::NAME_1 );
        }
        return $this->_themeService;
    }
    
	/**
     * Provides the publication service
     *
     * @return Newscoop\Service\ThemeServiceLocalFileSystem
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

    public $instId = null;
    public function init()
    {
        $this->getThemeService();
        $this->view->placeholder( 'title' )->set( getGS( 'Theme management' ) );
        $this->_helper->contextSwitch
            ->addActionContext( 'index', 'json' )
            ->addActionContext( 'assign-to-publication', 'json' )
            ->initContext();
    }
    

    public function indexAction()
    {
        $datatableAdapter = new Theme( $this->getThemeService() );
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
                function( $theme, $index = null ) use ($view)
                {
                    $id = json_encode( array( "id" => $theme['id'] ) );
                    $processed[] = $id; 
                    
                    /*$imgArr = array();
                    if( @is_array( $theme['images'] ) ) {
                        foreach( $theme['images'] as $img ) {
                            $imgArr[] = " { image : " . json_encode( $img ) . " } ";
                        }
                    }
                    $processed[] = " [ " . implode( ",", $imgArr ) . " ] ";*/
                    
                    $processed[] = json_encode( array( 'images' => $theme['images'] ) );
                    $processed[] = json_encode( array( 'title' => $theme['title'], 'designer' => $theme['designer'], 'version' => $theme['version'] ) );
                    $processed[] = json_encode( array( 'compat' => $theme['subTitle'], 'text' => $theme['description'] ) );
                    $processed[] = $id; 
                    return $processed;
                } 
            )
            ->setParams( $this->_request->getParams() );
            
        if( ( $this->view->mytable = $datatable->dispatch() ) )
        {
            $this->view->publications  = $this->getPublicationService()->getEntities();
            
            $this->view->headScript()->appendFile( $this->view->baseUrl( "/js/jquery/jquery.tmpl.js" ) );
            $this->view->headLink( array
            ( 
            	'type'  =>'text/css', 
            	'href'  => $this->view->baseUrl('/admin-style/themes_list.css'),
                'media'	=> 'screen',
                'rel'	=> 'stylesheet'
            ) );
        }
    }
    
    function editAction()
    {
        $themeId = $this->_request->getParam( 'id' );
        $thmServ = $this->getThemeService();
        $theme   = $thmServ->findById( $themeId );
        $outServ = $this->getOutputService();
        foreach( ( $outputs = $outServ->getEntities() ) as $k => $output )
            $outSets[] = $thmServ->findOutputSetting( $theme, $output ); // ->toObject()
        
        $themeForm = new Admin_Form_Theme();
        $themeForm->populate( array
        ( 
        	"theme-version"    => (string) $theme->getVersion(),
        	"required-version" => (string) $theme->getMinorNewscoopVersion() 
        ) );
        
        
        $this->view->headLink( array
        ( 
        	'type'  =>'text/css', 
        	'href'  => $this->view->baseUrl('/admin-style/common.css'),
            'media'	=> 'screen',
            'rel'	=> 'stylesheet'
        ) );
        
        $this->view->themeForm      = $themeForm;
        $this->view->theme          = $theme->toObject();
        $this->view->outputs        = $outputs;
        $this->view->outputSettings = $outSets;
    }
    
    function filesAction()
    {
        $datatable = $this->_helper->genericDatatable; 
        $datatable->setAdapter
        ( 
            new ThemeFiles( $this->getThemeFileService(), $this->_request->getParam( 'id' ) ) 
        )->setOutputObject( $this->view );
    }
    
    function assignToPublicationAction()
    {
        try
        {
            $theme  = $this->getThemeService()->getById( $this->_request->getParam( 'theme-id' ) );
		    $pub    = $this->getPublicationService()->findById( $this->_request->getParam( 'pub-id' ) );
    		$this->view->response = $this->getThemeService()->assignTheme( $theme, $pub );
        }
        catch( DuplicateNameException $e )
        {
            $this->view->exception = array( "code" => $e->getCode(), "message" => getGS( 'Duplicate assignation' ) );
        }
        catch( \Exception $e )
        {
            $this->view->exception = array( "code" => $e->getCode(), "message" => getGS( 'Something broke' ) );
        }
        
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

