<?php
/**
 * @package Campsite
 *
 * @author Petr Jasek <petr.jasek@sourcefabric.org>
 * @copyright 2010 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 * @link http://www.sourcefabric.org
 */

require_once dirname(__FILE__) . '/../BaseList/BaseList.php';

/**
 * Image list component
 */
class ImageList extends BaseList
{
    /** @var array */
    protected $filters = array(
        "Source <> 'newsfeed'",
    );

    /**
     */
    public function __construct()
    {
        parent::__construct();

        $this->model = new Image;

        $this->cols = array(
            'Id' => NULL,
            'ThumbnailFileName' => getGS('Thumbnail'),
            'Description' => getGS('Description'),
            'Photographer' => getGS('Photographer'),
            'Place' => getGS('Place'),
            'Date' => getGS('Date'),
            'TimeCreated' => getGS('Added'),
            'LastModified' => getGS('Last modified'),
            'Source' => getGS('Source'),
            'Status' => getGS('Status'),
            'InUse' => getGS('In use')
        );

        $this->searchCols = array(
            'Description',
            'Photographer',
            'Place',
        );

        $this->ignoredCols = array('InUse');
        $this->inUseColumn = sizeof($this->cols) - 1;

        // set sorting
        $this->defaultSorting = 6;
        $this->defaultSortingDir = 'desc';
        $this->notSortable[] = 1;
    }

    /**
     * Process row
     * @param array $row
     * @return array
     */
    public function processRow(array $row)
    {
        global $Campsite, $ADMIN;
        $Campsite['THUMBNAIL_BASE_URL'] . $row['ThumbnailFileName'];

        // set thumbnail
        $row['ThumbnailFileName'] = sprintf('<a href="/%s/media-archive/edit.php?f_image_id=%d"><img src="%s" alt="%s" /></a>',
            $ADMIN,
            $row['Id'],
            $Campsite['THUMBNAIL_BASE_URL'] . $row['ThumbnailFileName'],
            $row['Description']);

        // create link for desc
        $row['Description'] = sprintf('<a href="/%s/media-archive/edit.php?f_image_id=%d">%s</a>',
            $ADMIN,
            $row['Id'],
            $row['Description']);

        // get in use info
        $image = new Image($row['Id']);
        $image->fixMissingThumbnail();
        $row['InUse'] = (int) $image->inUse();

        return array_values($row);
    }

    /**
     * @see BaseList
     */
    public function doData()
    {
        $args = $this->getArgs();
        if (!empty($args['filter']) && $args['filter'] == 'sda') {
            $this->filters = array('1');
        }

        return parent::doData();
    }
}
