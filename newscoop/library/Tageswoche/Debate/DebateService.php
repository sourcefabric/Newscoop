<?php
/**
 * @package Tageswoche
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Tageswoche\Debate;

use InvalidArgumentException;
use Doctrine\ORM\EntityManager;
use Newscoop\Entity\Article;

/**
 */
class DebateService
{
    const LIMIT = 30;

    const PRO_VOTE = 1;
    const CON_VOTE = 2;

    /**
     * @var array
     */
    private $criteria = array(
        'type' => 'deb_moderator',
        'publication' => 1,
        'language' => 5,
        'section' => 81,
        'workflowStatus' => Article::STATUS_PUBLISHED,
    );

    /**
     * @var Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Find debate by given id
     *
     * @param int $debateId
     * @return Newscoop\Entity\Article
     */
    public function findPublished($debateId)
    {
        if (empty($debateId)) {
            return null;
        }

        return $this->getArticleRepository()
            ->findOneBy(array_merge($this->criteria, array(
                'number' => $debateId,
            )));
    }

    /**
     * Find current debate id
     *
     * @return int
     */
    public function findCurrentDebateId()
    {
        $debates = $this->getArticleRepository()
            ->findBy($this->criteria, array('published' => 'desc'), 1);

        foreach ($debates as $debate) {
            return $debate->getNumber();
        }
    }

    /**
     * Find all published debates
     *
     * @return array
     */
    public function findAllPublished()
    {
        return $this->getArticleRepository()
            ->findBy($this->criteria, array('published' => 'desc'), self::LIMIT);
    }

    /**
     * Get statement article
     *
     * @param Newscoop\Entity\Article $debate
     * @param bool $pro
     */
    public function findStatement(Article $debate, $pro)
    {
        $criteria = array_merge($this->criteria, array(
            'type' => 'deb_statement',
            'section' => $debate->getSectionNumber(),
        ));

        $statements = $this->getArticleRepository()
            ->findBy($criteria);

        foreach ($statements as $statement) {
            if ($statement->getData('contra') == ! $pro) {
                return $statement;
            }
        }
    }

    /**
     * Gets relative number of votes for the given debate.
     *
     * @param Newscoop\Entity\Article $article
     * @param int $stage
     * @return array
     */
    public function getVotes($article, $stage = null)
    {
        $debateQuery = $this->getDebateRepository()
            ->createQueryBuilder('d')
            ->join('d.articles', 'a')
            ->where('a.number = :number')
            ->getQuery();

        $debateQuery->setParameter('number', $article->getNumber());
        $debate = $debateQuery->getSingleResult();

        list($from, $to) = $this->getFromTo($article, $stage);
        $votes = array(self::PRO_VOTE => 0, self::CON_VOTE => 0);
        foreach ($debate->getVotes() as $vote) {
            if (!$vote->isWithin($from, $to)) {
                continue;
            }

            $votes[$vote->getAnswerId()]++;
        }

        $total = array_sum($votes);
        if (empty($total)) {
            return array(0, 0);
        }

        return array(
            ceil($votes[self::PRO_VOTE] * 100.0 / $total),
            floor($votes[self::CON_VOTE] * 100.0 / $total),
        );
    }

    /**
     * Get debate repository
     *
     * @return Doctrine\ORM\EntityRepository
     */
    private function getArticleRepository()
    {
        return $this->em->getRepository('Newscoop\Entity\Article');
    }

    /**
     * Get debate repository
     *
     * @return Doctrine\ORM\EntityRepository
     */
    private function getDebateRepository()
    {
        return $this->em->getRepository('Tageswoche\Entity\Debate');
    }

    /**
     * Get from to dates for debate and stage
     *
     * @param Newscoop\Entity\Article $article
     * @param int $stage
     * @return array
     */
    private function getFromTo($article, $stage)
    {
        if (empty($stage)) {
            return array(null, null);
        }

        $stages = array(
            'date_opening',
            'date_rebuttal',
            'date_final',
            'date_closing',
        );

        if ($stage < 1 || $stage - 1 > count($stages)) {
            throw new InvalidArgumentException("Stage out of range");
        }

        return array(
            $article->getData($stages[$stage - 1]),
            $article->getData($stages[$stage])
        );
    }
}
