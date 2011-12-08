<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\DBAL\SQLParserUtils,
    Nette\InvalidArgumentException,
    Newscoop\Utils\Exception,
    Doctrine\ORM\Query,
    Doctrine\ORM\Configuration,
    Newscoop\Entity\ArticleDatetime,
    Doctrine\ORM\EntityRepository,
    Newscoop\ArticleDatetime as ArticleDatetimeHelper,
    Newscoop\Entity\Article;

class ArticleDatetimeRepository extends EntityRepository
{

    const RECURRING_NONE = 'NULL';
    const RECURRING_DAILY = 'daily';
    const RECURRING_WEEKLY = 'weekly';
    const RECURRING_MONTHLY = 'monthly';
    const RECURRING_YEARLY = 'yearly';

    private $lastQb;
    private $lastQParams;

    /**
     * @return array
     */
    private function buildInsertValues($timeSet, $recurring)
    {
        $insertValues = array();
        if (is_array($timeSet) || is_string($timeSet))
        {
            $timeSet = (array) $timeSet;
            foreach ($timeSet as $start => $end )
            {
            	if (!is_string($start) && !is_array($end)) {
                    list($start, $end) = explode(' - ', $end, 2);
                }
                $insertValues[] = new ArticleDatetimeHelper // some logic to capture the recurring also included
                (
                    array( $start => $end ),
                    is_array($end) && isset($end['recurring'])
                        ? $end['recurring']
                        : (!is_array($end) && ($x = preg_grep('/recurring:\w+/i', explode('-', $end))) && count($x) ?
                            next(preg_split('/\s*:\s*/', current($x))) : $recurring)
                );
            }
        }
        if ($timeSet instanceof ArticleDatetimeHelper) {
            $insertValues[] = $timeSet;
        }
        return $insertValues;
    }

    public function deleteByArticle($article)
    {
        $em = $this->getEntityManager();
        if(is_numeric($article)) {
            foreach ($this->findBy(array('articleId' => $article)) as $entry) {
                $em->remove($entry);
            }
        }
        elseif ($article instanceof Article) {
            $em->remove($article);
        }
    }
    
    public function deleteById($id)
    {
        $em = $this->getEntityManager();
        if(is_numeric($id)) {
            //$entry = $this->findBy(array('id' => $id));
            $entry = $this->find($id);
            $em->remove($entry); 
            $em->flush();
        }
        
    }

    /**
     * Adds time intervals
     * @param array|ArticleDatetime $timeSet
     * 		Complex set of intervals
     *		{
     *			"2011-11-02" = { "12:00" => "18:00", "20:00" => "22:00", [ "recurring" => true|false ] } - between these hours on 11-02
     *			"2011-11-03" = "11:00 - recurring:weekly" - at 11:00 this day, and recurring weekly
     *			"2011-11-03 14:00" = "18:00" - from 3rd nov 14:00 until 18:00
	 *			"2011-11-04" = "2011-11-07" - from 4th till 7th nov
	 *			"2011-11-08" = "2011-11-09 12:00" - from 8th till 12:00 9th
	 * 			"2011-11-10 10:30" = "2011-11-11" - from 10th 10:40 until the end of the day
     *			"2011-11-12 12:30" = "2011-11-13 13:00" - self explanatory
     *			"2011-11-14 14:30" = "2011-11-15 15:00" - self explanatory
     *			"2011-11-15 15:30" = "2011-11-17" - self explanatory
     *			"2011-11-30" = true - on the 30th full day
     *		}
     * @param int|Article $articleId
     * @param string $fieldName
     * @param string $recurring
     * @param bool $overwrite
     */
    public function add( $timeSet, $articleId, $fieldName = null, $recurring = null, $overwrite=false )
    {
        $insertValues = $this->buildInsertValues($timeSet, $recurring);

        $em = $this->getEntityManager();
        // check article
        if (is_numeric($articleId)) {
            $article = $em->getRepository('Newscoop\Entity\Article')->findOneBy(array('number' => $articleId));
            /* @var $article Newscoop\Entity\Article */
        }
        elseif ($articleId instanceof Article) {
            $article = $articleId;
        }
        if (is_null($article)) {
            return false;
        };

        try // delete all entries and add new ones
        {
            $em->getConnection()->beginTransaction();
            if ($overwrite)
            {
                $this->deleteByArticle($articleId);
            }
            foreach ($insertValues as $dateValue) {
                foreach (array_merge(array($dateValue), $dateValue->getSpawns()) as $dateValue)
                {
                    $articleDatetime = new ArticleDatetime();
                    $articleDatetime->setValues($dateValue, $article, $fieldName);
                    $em->persist($articleDatetime);
                }
            }
            $em->flush();
            $em->getConnection()->commit();
        }
        catch (\Exception $e) // rollback on commit
        {
            $em->getConnection()->rollback();
            $em->close();
            return $e;
        }
    }

    /**
     * Update entry by id
     * @param int $id
     * @param array $timeSet
     * @param int $articleId
     * @param string $fieldName
     * @param string $recurring
     */
    public function update($id, $timeSet, $articleId=null, $fieldName=null, $recurring=null)
    {
        $em = $this->getEntityManager();

        $entry = $this->find($id);
        if (!$entry) {
            return false;
        }
        if (is_null($articleId)) {
            $articleId = $entry->getArticleId();
        }
        if (is_null($fieldName)) {
            $fieldName = $entry->getFieldName();
        }

        $insertValues = $this->buildInsertValues($timeSet, $recurring);

        try
        {
            $em->getConnection()->beginTransaction();
            $em->remove($entry);
            foreach ($insertValues as $dateValue) {
                foreach (array_merge(array($dateValue), $dateValue->getSpawns()) as $dateValue)
                {
                    $articleDatetime = new ArticleDatetime();
                    $articleDatetime->setValues($dateValue, $articleId, $fieldName, $entry->getArticleType());
                    $em->persist($articleDatetime);
                }
            }
            $em->flush();
            $em->getConnection()->commit();
        }
        catch(\Exception $e)
        {
            $em->getConnection()->rollback();
            $em->close();
            return $e;
        }
    }

    /**
     * Find dates
     * @param object $search
     * 		{
     * 			startDate : dateFormat, - passing only startDate will compare to entries with exactly (=) this value
     * 			endDate : dateFormat,
     * 			startTime : dateFormat,
     * 			endTime : dateFormat,
     * 			daily : bool|dateFormat,
     * 			weekly : dateFormat,
     *			monthly : dateFormat,
     *			yearly : dateFormat
     *		}
     * @param $dontExecute if true, store query builder object and params in $this->lastQb and $this->lastQParams for later use
     */
    public function findDates($search, $dontExecute=false)
    {
        $qb = $this->createQueryBuilder('dt');

        // just one day
        if (isset($search->startDate) && isset($search->endDate))
        {
            $qb->add('where',  $qb->expr()->andx('dt.startDate = :startDate', 'dt.endDate is null'));
            $qb->setParameter('startDate', new \DateTime($search->startDate));
        }
        // date interval
        if (isset($search->startDate) && isset($search->endDate))
        {
            $qb->add('where',
                $qb->expr()->andx
                (
					'dt.startDate <= :startDate',
                    $qb->expr()->orx('dt.endDate >= :endDate', 'dt.endDate is null')
                ));
            $qb->setParameter('startDate', new \DateTime($search->startDate));
            $qb->setParameter('endDate', new \DateTime($search->endDate));
        }
        $hasStartTimeQuery = false;
        if (isset($search->startTime))
        {
            $qb->andWhere('dt.startTime <= :startTime');
            $qb->setParameter('startTime', new \DateTime($search->startTime));
            $hasStartTimeQuery = true;
        }
        if (isset($search->endTime))
        {
            $qb->andWhere('dt.endTime >= :endTime');
            $qb->setParameter('endTime', new \DateTime($search->endTime));
        }
        if (isset($search->daily))
        {
            $qb->andWhere('dt.recurring = :recurringDaily');
            $qb->setParameter('recurringDaily', self::RECURRING_DAILY);

            if (is_string($search->daily)) // replace start time with daily string value
            {
                if (!$hasStartTimeQuery) {
                    $qb->andWhere('dt.startTime <= :startTime');
                }
                $qb->setParameter('startTime', new \DateTime($search->daily));
            }
            if (is_array($search->daily)) // replace time with daily key values
            {
                $paraCount = 11;
                $orSqlParts = array();
                foreach ($search->daily as $startTime => $endTime)
                {
                    $orSqlParts[] = "( dt.startTime <= ?".($paraCount+1)." and (dt.endTime >= ?".($paraCount+2)." or dt.endTime is null) )";
                    $qb->setParameter(++$paraCount, new \DateTime($startTime));
                    $qb->setParameter(++$paraCount, new \DateTime($endTime));
                }
                $qb->andWhere(implode(" or ", $orSqlParts));
            }
        }
        if (isset($search->weekly))
        {
            $qb->andWhere('DAYOFWEEK(dt.startDate) = :dayOfWeek');
        	$qb->andWhere('dt.recurring = :recurringWeekly');
            $qb->setParameter('recurringWeekly', self::RECURRING_WEEKLY);
            if (is_string($search->weekly))
            {
                $dayOfWeek = new \DateTime($search->weekly);
                $dayOfWeek = $dayOfWeek->format('w')+1;
                $qb->setParameter('dayOfWeek', $dayOfWeek);
            }
            else {
                throw new \InvalidArgumentException('Parameter "weekly" must have a date-like formated value');
            }
        }
        if (isset($search->monthly))
        {
            $qb->andWhere('DAYOFMONTH(dt.startDate) = :dayOfMonth');
            $qb->andWhere('dt.recurring = :recurringMonthly');
            $qb->setParameter('recurringMonthly', self::RECURRING_MONTHLY);
            if (is_string($search->monthly))
            {
                $dayOfMonth = new \DateTime($search->monthly);
                $dayOfMonth = $dayOfMonth->format('d');
                $qb->setParameter('dayOfMonth', $dayOfMonth);
            }
            else {
                throw new \InvalidArgumentException('Parameter "monthly" must have a date-like formated value');
            }
        }
        if (isset($search->yearly))
        {
            $qb->andWhere('DAYOFYEAR(dt.startDate) <= :dayOfYear');
            $qb->andWhere('dt.recurring = :recurringYearly');
            $qb->setParameter('recurringYearly', self::RECURRING_YEARLY);
            if (is_string($search->yearly))
            {
                $dayOfYear = new \DateTime($search->yearly);
                $dayOfYear = $dayOfYear->format('z');
                $qb->setParameter('dayOfYear', $dayOfYear);
            }
            else {
                throw new \InvalidArgumentException('Parameter "yearly" must have a date-like formated value');
            }
        }
        // article field name query
        if (isset($search->fieldName))
        {
            $qb->andWhere('dt.fieldName = :fieldName');
            $qb->setParameter('fieldName', $search->fieldName);
        }
        // search for article id
        if (isset($search->articleId))
        {
            $qb->andWhere('dt.articleId= :articleId');
            $qb->setParameter('articleId', $search->articleId);
        }
        // search for article datetime id
        if (isset($search->id))
        {
            $qb->andWhere('dt.id= :id');
            $qb->setParameter('id', $search->id);
        }
        // store query and return $this
        if ($dontExecute)
        {
            $this->lastQb = $qb;
            $this->lastQParams = $qb->getParameters();
            return $this;
        }
        return $qb->getQuery()->getResult();
    }

    /**
     * Get the sql used for find method
     * @param array|string $cols columns to select
     */
    public function getFindDatesSQL($cols=null)
    {
        $conn = $this->getEntityManager()->getConnection();
        if (!is_null($cols)) {
            $this->lastQb->add('select', implode(",", (array) $cols));
        }
        $lastDQL = $this->lastQb->getDQL();
        foreach ($this->lastQParams as $paramName => $paramValue)
        {
            if (in_array($paramName, array('startTime', 'endTime'))) {
                $paramValue = $conn->convertToDatabaseValue($paramValue, 'time');
            }
            if (in_array($paramName, array('startDate', 'endDate'))) {
                $paramValue = $conn->convertToDatabaseValue($paramValue, 'date');
            }
            $lastDQL = preg_replace("/:{$paramName}/", "'".addslashes($paramValue)."'", $lastDQL);
        }
        $qb = $this->getEntityManager()->createQuery($lastDQL);
        return $qb->getSQL();
    }
}
