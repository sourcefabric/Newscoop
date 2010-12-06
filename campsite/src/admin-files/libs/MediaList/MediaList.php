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
            'time_created' => getGS('Added'),
            'last_modified' => getGS('Last modified'),
        );
        
        $this->searchCols = array(
            'file_name', 'extension', 'mime_type',
        );

        $this->defaultSorting = 4;
        $this->defaultSortingDir = 'desc';
    }

    /**
     * Process db row
     * @param array $row
     * @return array
     */
    public function processRow(array $row)
    {
        // edit link
        $row['file_name'] = sprintf('<a href="edit-attachment.php?f_attachment_id=%d">%s</a>',
            $row['id'], $row['file_name']);

        // human readable size
        $row['size_in_bytes'] = parent::FormatFileSize($row['size_in_bytes']);

        return array_values($row);
    }
}
