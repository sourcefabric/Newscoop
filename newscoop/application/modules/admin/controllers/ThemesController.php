<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
use Newscoop\Service\IPublicationService;
use Newscoop\Service\IThemeManagementService;
use Newscoop\Controller\Action\Helper\Datatable\Adapter\Theme,
    Newscoop\Entity\Theme\Loader\LocalLoader, 
    Newscoop\Service\Resource\ResourceId, 
    Newscoop\Service\IThemeService, 
    Newscoop\Service\Model\SearchTheme,
    Newscoop\Service\PublicationServiceDoctrine;

/**
 */
class Admin_ThemesController extends Zend_Controller_Action
{

    private $repository;

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
        $datatable
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
                'fnDrawCallback' => "newscoopDatatables.callbackDraw"
            ) )
            ->setWidths( array( 'checkbox' => 20, 'image' => 215, 'name' => 235, 'description' => 280, 'actions' => 115 ) )
            ->setRowHandler
            ( 
                function( $theme, $index = null ) use ($view)
                {
                    $processed[] = "null"; 
                    $imgArr = array();

                    if( @is_array( $theme['images'] ) ) {
                        foreach( $theme['images'] as $img ) {
                            $imgArr[] = " { image : " . json_encode( $img ) . " } ";
                        }
                    }
                    $processed[] = " [ " . implode( ",", $imgArr ) . " ] ";

                    $processed[] = json_encode( array( array( 'title' => $theme['title'], 'designer' => $theme['designer'], 'version' => $theme['version'] ) ) );
                    $processed[] = json_encode( array( array( 'compat' => $theme['subTitle'], 'text' => $theme['description'] ) ) );
                    $processed[] = "null"; 
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

    public function installAction()
    {
        $this->repository->install( $this->_getParam( 'offset' ) );
        $this->_helper->entity->flushManager();
        
        $this->_helper->flashMessenger( getGS( 'Theme $1', getGS( 'installed' ) ) );
        $this->_helper->redirector( 'index' );
    }

    public function uninstallAction()
    {
        $this->repository->uninstall( $this->_getParam( 'id' ) );
        $this->_helper->entity->flushManager();
        
        $this->_helper->flashMessenger( getGS( 'Theme $1', getGS( 'deleted' ) ) );
        $this->_helper->redirector( 'index' );
    }
}

