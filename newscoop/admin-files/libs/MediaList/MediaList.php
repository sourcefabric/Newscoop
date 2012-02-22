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
require_once WWW_DIR . '/classes/Attachment.php';

/**
 * Media list component
 */
class MediaList extends BaseList
{
    /**
     */
    public function __construct()
    {
        parent::__construct();
        
        $this->model = new Attachment;

        $this->cols = array(
            'id' => NULL,
            'file_name' => getGS('Filename'),
            'mime_type' => getGS('Type'),
            'size_in_bytes' => getGS('Size'),
            'content_disposition' => getGS('Open in browser'),
            'time_created' => getGS('Added'),
            'last_modified' => getGS('Last modified'),
            'Source' => getGS('Source'),
            'Status' => getGS('Status'),
            'InUse' => getGS('In use'),
        );
        
        $this->searchCols = array(
            'file_name', 'extension', 'mime_type',
        );

        $this->ignoredCols = array('InUse');
        $this->inUseColumn = sizeof($this->cols) - 1;

        $this->defaultSorting = 5;
        $this->defaultSortingDir = 'desc';
        $this->type = 'media';
    }

    /**
     * Process db row
     * @param array $row
     * @return array
     */
    public function processRow(array $row)
    {
        global $ADMIN;

        // edit link
        $row['file_name'] = sprintf('<a href="/%s/media-archive/edit-attachment.php?f_attachment_id=%d">%s</a>',
            $ADMIN,
            $row['id'], $row['file_name']);

        // human readable size
        $row['size_in_bytes'] = parent::FormatFileSize($row['size_in_bytes']);

        // yes/no disposition
        $row['content_disposition'] = empty($row['content_disposition']) ? getGS('Yes') : getGS('No');

        // get in use info
        $object = new Attachment($row['id']);
        $row['InUse'] = (int) $object->inUse();

        return array_values($row);
    }
}
