<?php
/**
 * @package Newscoop
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Install;

use RuntimeException;

/**
 * Config Writer
 */
class ConfigWriter
{
    const TPL = <<<EOF
<?php

global \$Campsite;

\$Campsite['DATABASE_NAME'] = '%s';
\$Campsite['DATABASE_SERVER_ADDRESS'] = '%s';
\$Campsite['DATABASE_SERVER_PORT'] = %d;
\$Campsite['DATABASE_USER'] = '%s';
\$Campsite['DATABASE_PASSWORD'] = '%s';

/** Database settings **/
\$Campsite['db']['type'] = 'mysql';
\$Campsite['db']['host'] = \$Campsite['DATABASE_SERVER_ADDRESS'];
\$Campsite['db']['port'] = \$Campsite['DATABASE_SERVER_PORT'];
\$Campsite['db']['name'] = \$Campsite['DATABASE_NAME'];
\$Campsite['db']['user'] = \$Campsite['DATABASE_USER'];
\$Campsite['db']['pass'] = \$Campsite['DATABASE_PASSWORD'];

EOF;

    /**
     * Write config in given filename
     *
     * @param Newscoop\Install\InstallConfig $config
     * @param string $filename
     */
    public function write(InstallConfig $config, $filename)
    {
        if ((file_exists($filename) && !is_writable($filename))
            || !is_writable(dirname($filename))) {
            throw new RuntimeException;
        }

        $code = sprintf(
            self::TPL,
            $this->escape($config->db['dbname']),
            $this->escape($config->db['host']),
            $config->db['port'],
            $this->escape($config->db['user']),
            $this->escape($config->db['password'])
        );

        file_put_contents($filename, $code);
        chmod($filename, 0600);
    }

    /**
     * Escape string
     *
     * @param string $input
     * @return string
     */
    public function escape($input)
    {
        return addslashes($input);
    }
}
