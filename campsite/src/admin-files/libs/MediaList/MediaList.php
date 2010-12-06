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
 * Article list component
 */
class MediaList extends BaseList
{
    /** @var array */
    protected $cols = array();

    /**
     * @param bool $quiet
     */
    public function __construct($quiet = FALSE)
    {
        parent::__construct($quiet);

        $this->cols = array(
            'id' => NULL,
            'file_name' => getGS('Filename'),
            'mime_type' => getGS('Type'),
            'size_in_bytes' => getGS('Size'),
            'last_modified' => getGS('Last modified'),
        );
    }

    /**
     * Data provider
     * @return array
     */
    public function doData()
    {
        global $g_ado_db;

        // get args
        $aoData = $this->getArgs();

        // order
        $dbCols = array_keys($this->cols);
        $order = array();
        for ($i = 0; $i < (int) $aoData['iSortingCols']; $i++) {
            $order[] = sprintf('%s %s',
                $dbCols[$aoData["iSortCol_$i"]],
                $aoData["sSortDir_$i"]);
        }

        // select columns
        $attachment = new Attachment;
        $queryStr = 'SELECT ' . implode(', ', array_keys($this->cols)) . '
            FROM ' . $attachment->m_dbTableName;

        // set search
        if (!empty($aoData['sSearch'])) {
            $search = array();
            foreach (array('file_name', 'extension', 'mime_type') as $col) {
                $search[] = sprintf('%s LIKE "%%%s%%"', $col,
                    mysql_real_escape_string($aoData['sSearch']));
            }
            $queryStr .= ' WHERE ' . implode(' OR ', $search);
        }

        // set order
        if (!empty($order)) {
            $queryStr .= ' ORDER BY ' . implode(', ', $order);
        }

        // add limit
        $queryStr .= sprintf(' LIMIT %d,%d',
            $aoData['iDisplayStart'],
            $aoData['iDisplayLength']);

        $attachments = array();
        $rows = (array) $g_ado_db->GetAll($queryStr);
        foreach ($rows as $row) {
            $item = (array) $row;

            // edit link
            $item['file_name'] = sprintf('<a href="edit-attachment.php?f_attachment_id=%d">%s</a>',
                $item['id'], $item['file_name']);

            // human readable size
            $item['size_in_bytes'] = self::FormatFileSize($item['size_in_bytes']);

            $attachments[] = array_values($item);
        }

        // get total count
        $queryStr = 'SELECT COUNT(*)
            FROM ' . $attachment->m_dbTableName;
        $totalCount = $g_ado_db->GetOne($queryStr);

        return array(
            'iTotalRecords' => $totalCount,
            'iTotalDisplayRecords' => sizeof($attachments),
            'sEcho' => (int) $aoData['sEcho'],
            'aaData' => $attachments,
        );
    }

    /**
     * Get human readable filesize
     * @credits joaoptm [http://php.net/manual/en/function.filesize.php]
     * @param int $size
     * @return string
     */
    public static function FormatFileSize($size)
    {
        static $units = array(' B', ' KB', ' MB', ' GB', ' TB');

        $size = (int) $size;
        for ($i = 0; $size >= 1024 && $i < 4; $i++) {
            $size /= 1024;
        }

        return round($size, 2) . $units[$i];
    }
}
