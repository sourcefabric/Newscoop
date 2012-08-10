<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository\Ingest\Feed;

use DateTime;
use Doctrine\ORM\EntityRepository;

/**
 * Ingest/Feed/Entry repository
 */
class EntryRepository extends EntityRepository
{
    /**
     * Lift embargo if expired
     */
    public function liftEmbargo()
    {
        $query = $this->getEntityManager()
            ->createQuery("SELECT e.id, e.embargoed FROM {$this->getEntityName()} e WHERE e.status = 'Embargoed' AND DATE_DIFF(CURRENT_DATE(), e.embargoed) >= 0");

        $embargoed = $query->getResult();
        if (empty($embargoed)) {
            return;
        }

        $now = new DateTime();
        $updated = array_filter($embargoed, function($entry) use ($now) {
            $liftEmbargo = $entry['embargoed'] instanceof DateTime ? $entry['embargoed'] : new DateTime($entry['embargoed']);
            return $now->getTimestamp() >= $liftEmbargo->getTimestamp();
        });

        $ids = array_map(function($entry) {
            return $entry['id'];
        }, $updated);

        if (empty($ids)) {
            return;
        }

        $query = $this->getEntityManager()
            ->createQuery("UPDATE {$this->getEntityName()} e SET e.status = 'Usable' WHERE e.id IN (" . implode(',', $ids) . ")");
        $query->getResult();
    }
}
