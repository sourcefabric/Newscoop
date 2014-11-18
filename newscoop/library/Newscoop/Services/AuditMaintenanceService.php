<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager;
use Newscoop\Entity\AuditEvent;

/**
 * Audit events maintenance service
 */
class AuditMaintenanceService
{
    const LOG_FILE = 'log/newscoop-audit.log';
    const LOG_LIFETIME = 'P7D';

    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /**
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Flush audit data
     *
     * @return void
     */
    public function flush()
    {
        $events = $this->getEvents();
        try {
            $this->writeCsv($events);
        } catch (\Exception $e) {
            exit;
        }

        foreach ($events as $event) {
            $this->em->remove($event);
        }
        $this->em->flush();
    }

    /**
     * Get events to be flushed
     *
     * @return array
     */
    public function getEvents()
    {
        $date = new \DateTime();
        $date->sub(new \DateInterval(self::LOG_LIFETIME));

        return (array) $this->getRepository()->getFlushableEvents($date);
    }

    /**
     * Write audit data in CSV format to output file
     *
     * @param array $events
     */
    public function writeCsv(array $events)
    {
        $fp = fopen(APPLICATION_PATH . '/../' . self::LOG_FILE, 'a');
        if ($fp == FALSE) {
            throw new \Exception('Couldn\'t open the log file');
        }

        foreach ($events as $event) {
            $e = array(
                $event->getId(),
                $event->getUser() ? $event->getUser()->getId() : NULL,
                (string) $event->getResourceType(),
                addslashes(json_encode($event->getResourceId())),
                (string) $event->getResourceTitle(),
                addslashes(json_encode($event->getResourceDiff())),
                (string) $event->getAction(),
                $event->getCreated()->format('Y-m-d H:i:s'),
                0
            );

            fputcsv($fp, $e, ',', '"');
        }

        fclose($fp);
    }

    /**
     * Get repository
     *
     * @return Doctrine\ORM\EntityRepository
     */
    private function getRepository()
    {
        return $this->em->getRepository('Newscoop\Entity\AuditEvent');
    }
}
