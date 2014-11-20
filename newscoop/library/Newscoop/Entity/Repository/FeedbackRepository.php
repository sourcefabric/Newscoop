<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\Query\Expr;
use Newscoop\Entity\Feedback;
use Newscoop\Entity\User;
use Newscoop\Entity\Article;
use Newscoop\Entity\Section;
use Newscoop\Entity\Publication;
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
     * @param \Newscoop\Entity\Feedback $entity
     * @param array $values
     * @return Feedback \Newscoop\Entity\Feedback
     */
    public function save(Feedback $entity, $values)
    {
        // get the entity manager
        $em = $this->getEntityManager();
        if (array_key_exists('user', $values)) {
			$entity->setUser($values['user']);
		}

        if (!empty($values['publication'])) {
			$publication = $em->getReference('Newscoop\Entity\Publication', $values['publication']);
			$entity->setPublication($publication);
		}
		if (!empty($values['section'])) {
			$section = $em->getReference('Newscoop\Entity\Section', $values['section']);
			$entity->setSection($section);
		}
		if (!empty($values['language']) && !empty($values['article'])) {
			$article = $em->getReference('Newscoop\Entity\Article', array(
				'language' => $values['language'],
				'number' => $values['article']
			));
			$entity->setArticle($article);
		}

        if (isset($values['subject'])) $entity->setSubject($values['subject']);
		if (isset($values['message'])) $entity->setMessage($values['message']);
        if (isset($values['url'])) $entity->setUrl($values['url']);
        if (isset($values['time_created'])) $entity->setTimeCreated($values['time_created']);
        if (isset($values['status'])) $entity->setStatus($values['status']);
        if (isset($values['attachment_type'])) $entity->setAttachmentType($values['attachment_type']);
        if (isset($values['attachment_id'])) $entity->setAttachmentId($values['attachment_id']);
        
        $em->persist($entity);
        return $entity;
    }

    /**
     * Method for setting status
     *
     * @param array $feedbacks Feedback identifiers
     * @param string $status
     * @return void
     */
    public function setStatus(array $feedbacks, $status)
    {
        foreach ($feedbacks as $feedback) {
            $this->setFeedbackStatus($this->find($feedback), $status);
        }
    }

    /**
     * Method for setting status for a feedback message
     *
     * @param \Newscoop\Entity\Feedback $feedback
     * @param  string $status
     * @return void
     */
    private function setFeedbackStatus(Feedback $feedback, $status)
    {
        $em = $this->getEntityManager();
        if ($status == 'deleted') {
            $em->remove($feedback);
        } else {
            $feedback->setStatus($status);
            $em->persist($feedback);
        }
    }

    /**
     * Get data for table
     *
     * @param array $params
     * @param array $cols
     * @return Comment[]
     */
    public function getData(array $params, array $cols)
    {
        $qb = $this->createQueryBuilder('e');
        $qb->from('Newscoop\Entity\User', 's');
        $andx = $qb->expr()->andx();
        $andx->add($qb->expr()->eq('e.user', new Expr\Literal('s.id')));

        if (!empty($params['sSearch'])) {
            $this->buildWhere($cols, $params['sSearch'], $qb, $andx);
        }

        if (!empty($params['sFilter'])) {
            $this->buildFilter($cols, $params['sFilter'], $qb, $andx);
        }

        // sort
        if (isset($params['iSortCol_0'])) {
            $colsIndex = array_keys($cols);
            $sortId = $params['iSortCol_0'];
            $sortBy = $colsIndex[$sortId];
            $dir = $params['sSortDir_0'] ? : 'asc';
            switch ($sortBy) {
                case 'user':
                    $qb->orderBy('s.username', $dir);
                    break;
                case 'message':
                    $qb->orderBy('e.time_created', $dir);
                    break;
                case 'url':
                    $qb->orderBy('e.url', $dir);
                    break;
                case 'index':
                    $qb->orderBy('e.time_created', $dir);
                    break;
                default:
                    $qb->orderBy('e.' . $sortBy, $dir);
            }
        }

        $qb->where($andx);

        // limit
        if (isset($params['iDisplayLength'])) {
            $qb->setFirstResult((int)$params['iDisplayStart'])->setMaxResults((int)$params['iDisplayLength']);
        }

        return $qb->getQuery()->getResult();
    }

    /**
     * Build where condition
     *
     * @param array $cols
     * @param string $search
     * @return Doctrine\ORM\Query\Expr
     */
    protected function buildWhere(array $cols, $search, $qb = null, $andx = null)
    {
        $orx = $qb->expr()->orx();
        $orx->add($qb->expr()->like('s.username', $qb->expr()->literal("%{$search}%")));
        $orx->add($qb->expr()->like('e.subject', $qb->expr()->literal("%{$search}%")));
        $orx->add($qb->expr()->like('e.message', $qb->expr()->literal("%{$search}%")));
        return $andx->add($orx);
    }

    /**
     * Build filter condition
     *
     * @param array $cols
     * @param array $filter
     * @param $qb
     * @param $andx
     * @return Doctrine\ORM\Query\Expr
     */
    protected function buildFilter(array $cols, array $filter, $qb, $andx)
    {
        foreach ($filter as $key => $values) {
            if (!is_array($values)) {
                $values = array($values);
            }
            $orx = $qb->expr()->orx();
            switch ($key) {
                case 'status':
                    $mapper = array_flip(Feedback::$status_enum);
                    foreach ($values as $value) {
                        $orx->add($qb->expr()->eq('e.status', $mapper[$value]));
                    }
                    break;
                case 'attachmentType':
					$mapper = array_flip(Feedback::$attachment_type_enum);
					foreach ($values as $value) {
                        $orx->add($qb->expr()->eq('e.attachment_type', $mapper[$value]));
                    }
            }
            $andx->add($orx);
        }
        return $andx;
    }
    
    public function getByUser($p_user_id) {
		$em = $this->getEntityManager();
		$qb = $em->createQueryBuilder();
		$qb->add('select', 'f.id')
            ->add('from', 'Newscoop\Entity\Feedback f')
            ->add('where', 'f.user = :p_user_id')
            ->setParameter('p_user_id', $p_user_id);
        $query = $qb->getQuery();
        $feedbackIds = $query->getArrayResult();
        
        $clearFeedbackIds = array();
        foreach($feedbackIds as $key => $value) {
        	$clearFeedbackIds[] = $value['id'];
        }
        return $clearFeedbackIds;
	}

    /**
     * Flush method
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
    }

    /**
     * Get feedbacks count for user
     *
     * @param Newscoop\Entity\User $user
     * @return int
     */
    public function countByUser(User $user)
    {
        return (int) $this->getEntityManager()
            ->createQuery("SELECT COUNT(feedback) FROM Newscoop\Entity\Feedback feedback WHERE feedback.user = :user")
            ->setParameter('user', $user->getId())
            ->getSingleScalarResult();
    }
}
