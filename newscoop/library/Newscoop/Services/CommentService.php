<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager;

/**
 * User service
 */
class CommentService
{
    /** @var Doctrine\ORM\EntityManager */
    private $em;

    /**
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    private function comment_create($params)
    {
        $comment = $this->find($params['id']);

        $commenter = $comment->getCommenter();
        $user = $commenter->getUser();

        if (!isset($user)) {
            return;
        }

        $attribute_value = $user->getAttribute("comment_delivered");
        $attribute_value = isset($attribute_value) ? ($attribute_value + 1) : 1;

        $user->addAttribute("comment_delivered", $attribute_value);

        $points_action = $this->em->getRepository('Newscoop\Entity\UserPoints')
                    ->getPointValueForAction("comment_delivered");

        $points = $user->getPoints();

        $user->setPoints($points+$points_action);
    }

    private function comment_update($params)
    {
        $comment = $this->find($params['id']);
    }

    private function comment_delete($params)
    {
        $comment = $this->find($params['id']);

        $commenter = $comment->getCommenter();
        $user = $commenter->getUser();

        if (!isset($user)) {
            return;
        }

        $attribute_value = $user->getAttribute("comment_deleted");
        $attribute_value = isset($attribute_value) ? ($attribute_value + 1) : 1;

        $user->addAttribute("comment_deleted", $attribute_value);

        //have to remove points for a deleted comment.
        $points_action = $this->em->getRepository('Newscoop\Entity\UserPoints')
                    ->getPointValueForAction("comment_delivered");

        $points = $user->getPoints();

        $user->setPoints($points-$points_action);
    }


    /**
     * Receives notifications of points events.
     *
     * @param sfEvent $event
     * @return void
     */
    public function update(\sfEvent $event)
    {
        $params = $event->getParameters();
        list($resource, $action) = explode('.', $event->getName());

        $method = $resource."_".$action;
        $this->$method($params);

        $this->em->flush();
    }

    /**
     * Find a comment by its id.
     *
     * @param int $id
     * @return Newscoop\Entity\Comment
     *
     */
    public function find($id)
    {
        return $this->em->getRepository('Newscoop\Entity\Comment')
            ->find($id);
    }

    /**
     * Find records by set of criteria
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @param int|null $offset
     * @return array
     */
    public function findBy(array $criteria, $orderBy = null, $limit = null, $offset = null)
    {
        return $this->em->getRepository('Newscoop\Entity\Comment')
            ->findBy($criteria, $orderBy, $limit, $offset);
    }
}