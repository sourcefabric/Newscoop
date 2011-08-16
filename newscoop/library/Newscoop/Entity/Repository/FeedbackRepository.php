<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Newscoop\Entity\Feedback;
use Newscoop\Entity\User;
use Newscoop\Datatable\Source as DatatableSource;

/**
 * Feedback repository
 */
class FeedbackRepository extends DatatableSource
{

    /**
     * Get new instance of the comment
     *
     * @return \Newscoop\Entity\Feedback
     */
    public function getPrototype()
    {
        return new Feedback;
    }

    /**
     * Method for saving a feedback
     *
     * @param Feedback $p_entity
     * @param array $params
     * @return Feedback $p_entity
     */
    public function save(Feedback $p_entity, $p_values)
    {
		// get the entity manager
        $em = $this->getEntityManager();
        
        $subscriber = $em->getReference('Newscoop\Entity\User\Subscriber', $p_values['subscriber']);
        
        $p_entity->setSubscriber($subscriber);
        $p_entity->setMessage($p_values['message']);
        $p_entity->setUrl($p_values['url']);
        $p_entity->setTimeCreated($p_values['time_created']);

        $em->persist($p_entity);
        return $p_entity;
    }

    /**
     * Flush method
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
    }
}
