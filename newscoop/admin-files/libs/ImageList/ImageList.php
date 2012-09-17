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
        $this->type = 'image';
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
        /*
        $row['Description'] = sprintf('<a href="/%s/media-archive/edit.php?f_image_id=%d">%s</a>',
            $ADMIN,
            $row['Id'],
            $row['Description']);
        */
        /*
        $row['Description'] = "
            <span style='display: inline;' id='description_view_".$row['Id']."' onClick='edit(\"description\",".$row['Id'].");'>".$row['Description']."</span>
            <span style='display: none;' id='description_edit_".$row['Id']."'><input id='description_input_".$row['Id']."'><br><button onClick='save(\"description\",".$row['Id'].");'>save</button><button onClick='view(\"description\",".$row['Id'].");'>cancel</button></span>
        ";
        
        $row['Photographer'] = "
            <span style='display: inline;' id='photographer_view_".$row['Id']."' onClick='edit(\"photographer\",".$row['Id'].");'>".$row['Photographer']."</span>
            <span style='display: none;' id='photographer_edit_".$row['Id']."'><input id='photographer_input_".$row['Id']."'><br><button onClick='save(\"photographer\",".$row['Id'].");'>save</button><button onClick='view(\"photographer\",".$row['Id'].");'>cancel</button></span>
        ";
        
        $row['Place'] = "
            <span style='display: inline;' id='place_view_".$row['Id']."' onClick='edit(\"place\",".$row['Id'].");'>".$row['Place']."</span>
            <span style='display: none;' id='place_edit_".$row['Id']."'><input id='place_input_".$row['Id']."'><br><button onClick='save(\"place\",".$row['Id'].");'>save</button><button onClick='view(\"place\",".$row['Id'].");'>cancel</button></span>
        ";
        
        $row['Date'] = "
            <span style='display: inline;' id='date_view_".$row['Id']."' onClick='edit(\"date\",".$row['Id'].");'>".$row['Date']."</span>
            <span style='display: none;' id='date_edit_".$row['Id']."'><input id='date_input_".$row['Id']."'><br><button onClick='save(\"date\",".$row['Id'].");'>save</button><button onClick='view(\"date\",".$row['Id'].");'>cancel</button></span>
        ";
        */

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
        if (!empty($args['filter']) && $args['filter'] == 'newsfeed') {
            $this->filters = array('1');
        }

        return parent::doData();
    }
}
