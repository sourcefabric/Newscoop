<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Log;

use DateTime,
    Zend_Registry,
    Zend_Log_Writer_Abstract,
    Doctrine\ORM\EntityManager,
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

        // set user if any
        if (!isset($event['user'])) {
            $event['user'] = null;
            $auth = \Zend_Auth::getInstance();
            if ($auth->hasIdentity()) {
                $event['user'] = $this->em->getReference('Newscoop\Entity\User\Staff', $auth->getIdentity());
            }
        }

        // create log entity
        $log = new Log;
        $log->setTimeCreated(new DateTime($event['timestamp']))
            ->setText($event['message'])
            ->setPriority($event['priority'])
            ->setClientIP(isset($ip) ? $ip : '')
            ->setUser($event['user']);

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
