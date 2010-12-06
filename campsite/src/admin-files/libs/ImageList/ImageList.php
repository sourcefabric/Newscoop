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
        );

        $this->searchCols = array(
            'Description',
            'Photographer',
            'Place',
        );

        $this->notSortable[] = 1;
    }

    /**
     * Process row
     * @param array $row
     * @return array
     */
    public function processRow(array $row)
    {
        global $Campsite;

        // set thumbnail
        $row['ThumbnailFileName'] = sprintf('<img src="%s" alt="%s" />',
            $Campsite['THUMBNAIL_BASE_URL'] . $row['ThumbnailFileName'],
            $row['Description']);

        // create link for desc
        $row['Description'] = sprintf('<a href="edit.php?f_image_id=%d">%s</a>',
            $row['Id'],
            $row['Description']);

        return array_values($row);
    }
}
