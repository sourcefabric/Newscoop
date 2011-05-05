<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Utils\PermissionToAcl;

require_once dirname(__FILE__) . '/../library/Newscoop/Utils/PermissionToAcl.php';

global $g_ado_db, $LiveUserAdmin;

$LiveUserAdmin = new LiveUserMock($g_ado_db);

/**
 * LiveUser mock object
 *  provides backward compatibility for plugins
 */
class LiveUserMock
{
    const RIGHTS = 'liveuser_rights';
    const RULES = 'acl_rule';

    /** @var ADOConnection */
    private $db;

    /**
     * @param ADOConnection $db
     */
    public function __construct(ADOConnection $db)
    {
        $this->db = $db;
    }

    /**
     * Add dynamic right
     *
     * @param array $right
     * @return void
     */
    public function addRight(array $right)
    {
        // get next id
        $sql = 'SELECT MAX(right_id) FROM ' . self::RIGHTS;
        $lastId = (int) $this->db->GetOne($sql);
        $nextId = $lastId + 1;

        $areaId = (int) $right['area_id'];
        $permission = mysql_real_escape_string($right['right_define_name']);

        $sql = 'INSERT IGNORE
                INTO ' . self::RIGHTS . " (right_id, area_id, right_define_name)
                VALUES ($nextId, $areaId, '$permission')";
        $this->db->Execute($sql);
    }

    /**
     * Get rights
     *
     * @param array $params
     * @return array
     */
    public function getRights(array $params)
    {
        $permission = $params['filters']['right_define_name'];
        $sql = 'SELECT right_id
                FROM ' . self::RIGHTS . "
                WHERE right_define_name = '" . mysql_real_escape_string($permission) . "'"; 
        return $this->db->GetAll($sql);
    }

    /**
     * Remove right
     *
     * @param array $params
     * @return void
     */
    public function removeRight(array $params)
    {
        $rightId = (int) $params['right_id'];

        // get permission
        $sql = 'SELECT right_define_name
                FROM ' . self::RIGHTS . "
                WHERE right_id = $rightId";
        $permission = $this->db->GetOne($sql);

        // remove acl rules
        list($resource,) = PermissionToAcl::translate($permission);
        $sql = 'DELETE
                FROM ' . self::RULES . "
                WHERE resource = '$resource'";
        $this->db->Execute($sql);

        // remove right
        $sql = 'DELETE
                FROM ' . self::RIGHTS . "
                WHERE right_id = $rightId";
        $this->db->Execute($sql);
    }
}
