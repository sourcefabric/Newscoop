<?php

namespace Newscoop\Controller\Action\Helper\Datatable\Adapter;

use Newscoop\Service\IThemeManagementService,
    Newscoop\Service\Model\SearchTheme,
    Newscoop\Service\Model\Search as Search,
    Newscoop\Service\Implementation\ThemeManagementServiceLocal,
    Newscoop\Controller\Action\Helper\Datatable\Row,
    Newscoop\Service,
    Newscoop\Entity\Publication;

class Theme extends AAdapter
{
    /**
     *
     * Theme service
     * @var ThemeManagementServiceLocal
     */
    private $_service;

    /**
     * The search object
     * @var SearchTheme
     */
    private $_search;

    /**
     * Search column index used for filtering per publication
     * @var int $_pubColFilterIdx
     */
    private $_pubColFilterIdx;

    public function __construct( IThemeManagementService $service )
    {
        $this->_service = $service;
    }

    public function getData( array $p_params, array $p_cols )
    {
        $p_params = (object) $p_params;
        $dataCollection = null;
        if( isset( $p_params->search ) )
        {
            /*
             * some sort of search management
            foreach( $p_params as $searchCol => $searchVal )
            {
                if( @trim( $searchVal ) != "" )
                    switch( $searchCol )
                    {
                    };
            }

            */
            if( @trim( $p_params->search[ $this->_pubColFilterIdx ] ) != "" )
            {
                $p = new Publication();
                $p->setId( intval( $p_params->search[ $this->_pubColFilterIdx ] ) );
                $dataCollection = $this->_service->getThemes( $p );
                //var_dump( $dataCollection );
            }

            $this->search( $p_params->search );
        }
        if( isset( $p_params->sort ) ) {
            $this->sort( ( !is_array( $p_params->sort ) ? array( $p_params->sort ) : $p_params->sort ) );
        }

        $retThemes = array();
        if( is_null( $dataCollection ) )
            $dataCollection = $this->_service->getUnassignedThemes( $this->getSearchObject() );

        foreach( $dataCollection as $theme )
        {
            $images = array();
            foreach( $this->_service->getPresentationImages( $theme ) as $img ) {
                $images[] = (string) $img->getPath(); // @todo some sorting
            }
            $retThemes[] = array
            (
                'id'          => (string) $theme->getId(),
            	'title'       => (string) $theme->getName(),
                'designer'    => (string) $theme->getDesigner(),
                'version'     => (string) $theme->getVersion(),
                'subTitle'    => (string) $theme->getMinorNewscoopVersion(),
                'description' => (string) $theme->getDescription(),
                'images'	  => $images,
                'pubId'       => isset( $p ) ? $p->getId() : null
            );
        }

        return $retThemes;
    }

    public function getCount( array $p_params = array(), array $cols = array() )
    {
        $search = $this->getSearchObject();
        $p_params = (object) $p_params;
        if( isset( $p_params->search ) )
        {
            if( @trim( $p_params->search[ $this->_pubColFilterIdx ] ) != "" )
            {
                $p = new Publication();
                $p->setId( intval( $p_params->search[ $this->_pubColFilterIdx ] ) );
                try
                {
                    return $this->_service->getCountThemes( $p, $search );
                }
                catch( \Exception $e )
                {
                    return 0;
                }
            }

            $this->search( $p_params->search );
        }

        try
        {
            return $this->_service->getCountUnassignedThemes( $search );
        }
        catch( \Exception $e )
        {
            return 0;
        }
    }

    /**
     * Define in what column number to look for the publication filter search
     * @param int $column
     */
    public function setPublicationFilterColumn( $column )
    {
        $this->_pubColFilterIdx = $column;
        return $this;
    }

    public function search( $query, array $cols = null )
    {
        return;
    }

    public function sort( array $p_params )
    {
        $search = $this->getSearchObject();
        foreach( $p_params as $k => $v )
        {
            switch( $k )
            {
                case 1 :
                case 2 : $colName = 'NAME'; break;
                case 3 : $colName = 'MINOR_NEWSCOOP_VERSION'; break;
                default : continue 2;
            }
            $sortMethod = ( $v == 'asc' ? 'orderAscending' : 'orderDescending' );
            $search->$colName->$sortMethod();
        }
    }

    /**
     * Get/set search object
     * @return SearchTheme
     */
    public function getSearchObject()
    {
        if( is_null( $this->_search ) )
            $this->_search = new SearchTheme;
        return $this->_search;
    }
}