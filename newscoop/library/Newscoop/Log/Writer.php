<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Log;

use DateTime,
    Doctrine\ORM\EntityManager,
    Zend_Log_Writer_Abstract,
    Newscoop\Entity\Log,
    Newscoop\Entity\User;

/**
 * Log Writer for Zend_Log
 */
class Writer extends Zend_Log_Writer_Abstract
{
    /** @var Doctrine\ORM\EntityManager */
    private $em = NULL;

    /** @var array */
    private static $ipKeys = array(
        'HTTP_X_FORWARDED_FOR',
        'REMOTE_ADDR',
        'SERVER_ADDR',
    );

    /**
     * @param Doctrine\ORM\EntityManager
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Store event
     *
     * @param array $event
     * @return void
     */
    protected function _write($event)
    {
        // search for ip
        foreach (self::$ipKeys as $key) {
            if (!empty($_SERVER[$key])) {
                $ip = $_SERVER[$key];
                break;
            }
        }

        if (empty($event['user'])) { // Can't store without user
            //FIXME: the user class is abstract cannot create instance $event['user'] = new User;
            $event['user'] = new User;
        }

        // create log entity
        $log = new Log;
        $log->setTimeCreated(new DateTime($event['timestamp']))
            ->setText($event['message'])
            ->setPriority($event['priority'])
            ->setUser($event['user'])
            ->setClientIP($ip ?: '');

        // store
        $this->em->persist($log);
        $this->em->flush();
    }

    /**
     * Writer factory
     *
     * @param array|Zend_Config $config
     * @return NULL
     */
    public static function factory($config)
    {
        return NULL;
    }
}
