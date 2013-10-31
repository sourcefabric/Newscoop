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
        
        $translator = \Zend_Registry::get('container')->getService('translator');

        $this->model = new Attachment;

        $this->cols = array(
            'id' => NULL,
            'file_name' => $translator->trans('Filename', array(), 'library'),
            'mime_type' => $translator->trans('Type'),
            'size_in_bytes' => $translator->trans('Size'),
            'content_disposition' => $translator->trans('Open in browser', array(), 'library'),
            'time_created' => $translator->trans('Added', array(), 'library'),
            'last_modified' => $translator->trans('Last modified', array(), 'library'),
            'Source' => $translator->trans('Source', array(), 'library'),
            'Status' => $translator->trans('Status'),
            'InUse' => $translator->trans('In use'),
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

        $translator = \Zend_Registry::get('container')->getService('translator');
        // edit link
        $row['file_name'] = sprintf('<a href="/%s/media-archive/edit-attachment.php?f_attachment_id=%d">%s</a>',
            $ADMIN,
            $row['id'], $row['file_name']);

        // human readable size
        $row['size_in_bytes'] = parent::FormatFileSize($row['size_in_bytes']);

        // yes/no disposition
        $row['content_disposition'] = empty($row['content_disposition']) ? $translator->trans('Yes') : $translator->trans('No');

        // get in use info
        $object = new Attachment($row['id']);
        $row['InUse'] = (int) $object->inUse();

        return array_values($row);
    }
}
