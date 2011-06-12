<?php

namespace Newscoop\Controller\Action\Helper\Datatable\Adapter;

use Newscoop\Service\IThemeService,
    Newscoop\Service\Implementation\ThemeServiceLocalFileSystem;

/**
 * Datatable adapter for one theme's files
 * @author mihaibalaceanu
 */
class ThemeFiles extends AAdapter
{
    /**
     * The theme files service
     * @var Newscoop\Service\Implementation\ThemeServiceLocalFileSystem
     */
    private $_service;

    /**
     * the theme entity
     * @var Newscoop\Entity\Theme
     */
    private $_theme;

    public function __construct( IThemeService $service, $themeId )
    {
        $this->_service = $service;
        $this->_theme = $this->_service->getById( $themeId );

    }

    public function getData( array $params, array $cols )
    {
        var_dump( $this->_service->getFiles( $this->_theme ) );
    }

    public function getCount( array $params = array(), array $cols = array() )
    {

    }

    public function sort( array $cols )
    {

    }

    public function search( $query, array $cols = null )
    {

    }
}