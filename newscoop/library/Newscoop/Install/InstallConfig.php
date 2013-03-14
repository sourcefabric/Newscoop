<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Install;

/**
 */
class InstallConfig
{
    /**
     * @var string
     */
    public $site_name = 'Newscoop';

    /**
     * @var string
     */
    public $admin_password = 'admin';

    /**
     * @var string
     */
    public $admin_email = 'root@localhost';

    /**
     * @var string
     */
    public $template_set = 'the_new_custodian';

    /**
     * @var string
     */
    public $alias = 'localhost:9000';

    /**
     * @var bool
     */
    public $overwrite_database = false;

    /**
     * @var array
     */
    public $db = array(
        'driver' => 'pdo_mysql',
        'dbname' => 'newscoop',
        'user' => 'root',
        'password' => '',
        'host' => 'localhost',
        'port' => 3306,
        'charset' => 'utf8',
    );
}
