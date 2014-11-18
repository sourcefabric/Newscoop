<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

use Nette\InvalidArgumentException;
use Newscoop\Utils\Exception;
use Newscoop\Entity\ArticleDatetime;
use Doctrine\ORM\EntityRepository;
use Newscoop\ArticleDatetime as ArticleDatetimeHelper;
use Newscoop\Entity\Article;

class ArticleDatetimeRepository extends EntityRepository
{

    const RECURRING_NONE = 'NULL';
    const RECURRING_DAILY = 'daily';
    const RECURRING_WEEKLY = 'weekly';
    const RECURRING_MONTHLY = 'monthly';
    const RECURRING_YEARLY = 'yearly';

    protected $lastQb;
    protected $lastQParams;

    /**
     * @param string|null $recurring
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
    public function add( $timeSet, $articleId, $fieldName = null, $recurring = null, $overwrite=false, $otherInfo=null )
    {
        $insertValues = $this->buildInsertValues($timeSet, $recurring);
        $article = null;

        $em = $this->getEntityManager();
        // check article
        if (is_numeric($articleId)) {
            $article = $em->getRepository('Newscoop\Entity\Article')->findOneBy(array('number' => $articleId));
            /* @var $article Newscoop\Entity\Article */
        }
        elseif ($articleId instanceof \Article) {
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
                    $articleDatetime->setValues($dateValue, $article, $fieldName, null, $otherInfo);
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
    
    public function getEmpty() {
        $articleDatetime = new ArticleDatetime();
        return($articleDatetime);
    }

    /**
     * Update entry by id
     * @param int $id
     * @param array $timeSet
     * @param int $articleId
     * @param string $fieldName
     * @param string $recurring
     */
    public function update($id, $timeSet, $articleId=null, $fieldName=null, $recurring=null, $otherInfo=null)
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
                    $articleDatetime->setValues($dateValue, $articleId, $fieldName, $entry->getArticleType(), $otherInfo);
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
/* Notices:
 *  by now, 'NULL' means 'no end', i.e. till the end of day for end_time, forever for end_date
 *  when no recurring, then it is continuously from start_date/start_time till end_date/end_time
 *      start_date and date_end are usually the same then ... must be for a single day event.
 *
 *  this search would be wrong on situations where we search for a short time interval and an recurring event starts before and ends after,
 *      but does not recur at the specified (short) interval, like:
 *          a) having an event yearly from 2000, each January 1st
 *          b) searching for events 2012-04-01 till 2012-04-30
 *          c) without specifying any recurrence
 *      the search would take that event even though it does not occur at the specified interval
 *      thus the addition below, at part where just start-end is set
 */

        $qb = $this->createQueryBuilder('dt');

        // interval from a date till infinity
        if (isset($search->startDate) && !isset($search->endDate))
        {
            $qb->andWhere('dt.endDate >= :startDate');
            $qb->setParameter('startDate', new \DateTime($search->startDate));
        }

        // date interval
        if (isset($search->startDate) && isset($search->endDate))
        {

            $qb->add('where',
                $qb->expr()->andx
                (
                    'dt.startDate <= :endDate',
                    $qb->expr()->orx('dt.endDate >= :startDate', 'dt.endDate is null')
                ));
            $qb->setParameter('startDate', new \DateTime($search->startDate));
            $qb->setParameter('endDate', new \DateTime($search->endDate));

            if (!isset($search->daily) && !isset($search->weekly) && !isset($search->monthly) && !isset($search->yearly) && ($search->startDate <= $search->endDate)) {

                $interval_one_day = new \DateInterval('P1D');
                $start_date = new \DateTime($search->startDate);
                $end_date = new \DateTime($search->endDate);
                $end_date_plus = clone $end_date;
                $end_date_plus->add($interval_one_day);

                $weeks_to_check = true;
                $months_to_check = true;
                $years_to_check = true;

                $yearly_days_check_str = '1 = 1';

                // taking covered days of year

                $start_year = $start_date->format('Y');
                $start_month_day = $start_date->format('m-d');

                $end_year = $end_date->format('Y');
                $end_month_day = $end_date->format('m-d');

                $yearly_checking = null;

                if (($start_year + 2) <= $end_year) {
                    $weeks_to_check = false;
                    $months_to_check = false;
                    $years_to_check = false;
                }
                elseif (($start_year + 1) == $end_year) {
                    if ($start_month_day <= $end_month_day) {
                        $weeks_to_check = false;
                        $months_to_check = false;
                        $years_to_check = false;
                    }
                    else {
                        $yearly_checking = $qb->expr()->andx();
                        $yearly_checking->add('dt.recurring = :recurring_yearly');
                        $yearly_checking->add($qb->expr()->orx(
                            $qb->expr()->gte('DATE_FORMAT(dt.startDate, "%m-%d")', '"' . $start_month_day . '"'),
                            $qb->expr()->lte('DATE_FORMAT(dt.startDate, "%m-%d")', '"' . $end_month_day . '"')
                        ));

                        //$yearly_days_check_str = 'dt.recurring = :recurring_yearly AND (DATE_FORMAT(dt.startDate, "%m-%d") >= "' . $start_month_day . '") OR (DATE_FORMAT(dt.startDate, "%m-%d") <= "' . $end_month_day . '")';
                    }
                }
                else {
                    $yearly_checking = $qb->expr()->andx();
                    $yearly_checking->add('dt.recurring = :recurring_yearly');
                    $yearly_checking->add($qb->expr()->gte('DATE_FORMAT(dt.startDate, "%m-%d")', '"' . $start_month_day . '"'));
                    $yearly_checking->add($qb->expr()->lte('DATE_FORMAT(dt.startDate, "%m-%d")', '"' . $end_month_day . '"'));

                    //$yearly_days_check_str = 'dt.recurring = :recurring_yearly AND (DATE_FORMAT(dt.startDate, "%m-%d") >= "' . $start_month_day . '") AND (DATE_FORMAT(dt.startDate, "%m-%d") <= "' . $end_month_day . '")';
                }

                // taking covered days of month

                if ($months_to_check) {
                    $start_end_period = new \DatePeriod($start_date, $interval_one_day, $end_date_plus);

                    $allowed_month_days = array();

                    foreach($start_end_period as $one_day_in) {

                        $one_day_in_month = $one_day_in->format('j');
                        $allowed_month_days[$one_day_in_month] = $one_day_in_month;
                    }

                    if (31 <= count($allowed_month_days)) {
                        $months_to_check = false;
                    }
                }

                // taking covered days of week

                if ($weeks_to_check) {

                    $start_day_of_week = $start_date->format('w') + 1;
                    $end_day_of_week = $end_date->format('w') + 1;

                    foreach (array(1,2,3,4,5,6,7) as $one_week_day) {
                        if (($one_week_day >= $start_day_of_week) && ($one_week_day <= $end_day_of_week)) {
                            $allowed_week_days[] = $one_week_day;
                            continue;
                        }
                        if ($start_day_of_week > $end_day_of_week) {
                            if (($one_week_day >= $start_day_of_week) || ($one_week_day <= $end_day_of_week)) {
                                $allowed_week_days[] = $one_week_day;
                                continue;
                            }
                        }
                    }

                    if (7 <= count($allowed_week_days)) {
                        $weeks_to_check = false;
                    }

                }

                // put the check parts in
/*
                // current doctrine is broken on 'IN' statement
                $qb->andWhere(
                    $qb->expr()->orx
                    (
                        'dt.recurring IS NULL',
                        'dt.recurring = 0',
                        'dt.recurring = :recurring_daily' // it is ok for daily repeating events; and if time specified, it is set below
                        $qb->expr()->andx('dt.recurring = :recurring_weekly', $qb->expr()->in('DAYOFWEEK(dt.startDate)', ':allowed_week_days')),
                        $qb->expr()->andx('dt.recurring = :recurring_monthly', $qb->expr()->in('DAYOFMONTH(dt.startDate)', ':allowed_month_days')),
                        $qb->expr()->andx('dt.recurring = :recurring_yearly', $yearly_days_check_str)* /
                    )
                );
*/
                $outerOr = $qb->expr()->orx();
                $outerOr->add('dt.recurring IS NULL');
                $outerOr->add('dt.recurring = 0');
                $outerOr->add('dt.recurring = :recurring_daily');
                $useOuter = true;

                if ($weeks_to_check) {
                    $innerWeekOr = $qb->expr()->orx();
                    foreach ($allowed_week_days as $one_allowed_week_day) {
                        $innerWeekOr->add($qb->expr()->eq('DAYOFWEEK(dt.startDate)', $one_allowed_week_day));
                    }

                    $outerWeekAnd = $qb->expr()->andx();
                    $outerWeekAnd->add('dt.recurring = :recurring_weekly');
                    $outerWeekAnd->add($innerWeekOr);

                    $outerOr->add($outerWeekAnd);
                    $useOuter = true;
                }

                if ($months_to_check) {
                    $innerMonthOr = $qb->expr()->orx();
                    foreach ($allowed_month_days as $one_allowed_month_day) {
                        $innerMonthOr->add($qb->expr()->eq('DAYOFMONTH(dt.startDate)', $one_allowed_month_day));
                    }

                    $outerMonthAnd = $qb->expr()->andx();
                    $outerMonthAnd->add('dt.recurring = :recurring_monthly');
                    $outerMonthAnd->add($innerMonthOr);

                    $outerOr->add($outerMonthAnd);
                    $useOuter = true;
                }

/*
                // TODO: doctrine do not work with date_format, even when the function is user created and set in resources
                //       but we do not have support for year-repeating events in UI anyway
                if ($years_to_check) {
                    $outerOr->add($yearly_checking);
                    $useOuter = true;
                }
*/

                if ($useOuter) {
                    $qb->andWhere($outerOr);
                    //$qb->where($outerOr);
                }

                $qb->setParameter('recurring_daily', self::RECURRING_DAILY);
                $qb->setParameter('recurring_weekly', self::RECURRING_WEEKLY);
                $qb->setParameter('recurring_monthly', self::RECURRING_MONTHLY);
                $qb->setParameter('recurring_yearly', self::RECURRING_YEARLY);

                //var_dump($qb->getDQL());
            }

        }
        $hasStartTimeQuery = false;
        if (isset($search->startTime))
        {
            $qb->andWhere('dt.startTime >= :startTime');
            $qb->setParameter('startTime', new \DateTime($search->startTime));
            $hasStartTimeQuery = true;
        }
        if (isset($search->endTime))
        {
            $qb->andWhere('dt.endTime <= :endTime');
            $qb->setParameter('endTime', new \DateTime($search->endTime));
        }
        if (isset($search->daily))
        {
            $qb->andWhere('dt.recurring = :recurringDaily');
            $qb->setParameter('recurringDaily', self::RECURRING_DAILY);

            if (is_string($search->daily)) // replace start time with daily string value
            {
                if (!$hasStartTimeQuery) {
                    $qb->andWhere('dt.startTime >= :startTime');
                }
                $qb->setParameter('startTime', new \DateTime($search->daily));
            }
            if (is_array($search->daily)) // replace time with daily key values
            {
                $paraCount = 11;
                $orSqlParts = array();
                foreach ($search->daily as $startTime => $endTime)
                {
                    $orSqlParts[] = "( dt.startTime >= ?".($paraCount+1)." and (dt.startTime <= ?".($paraCount+2).") )";
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
                $dayOfMonth = $dayOfMonth->format('j');
                $qb->setParameter('dayOfMonth', $dayOfMonth);
            }
            else {
                throw new \InvalidArgumentException('Parameter "monthly" must have a date-like formated value');
            }
        }
        if (isset($search->yearly))
        {
            $qb->andWhere('DATE_FORMAT(dt.startDate, "%m-%d") = :dayOfYear');
            $qb->andWhere('dt.recurring = :recurringYearly');
            $qb->setParameter('recurringYearly', self::RECURRING_YEARLY);
            if (is_string($search->yearly))
            {
                $dayOfYear = new \DateTime($search->yearly);
                $dayOfYear = $dayOfYear->format('m-d');
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

    public function renameField($p_articleType, $p_fieldNames) {
        $qb = $this->createQueryBuilder('dt')
            ->update()
            ->set('dt.fieldName', ':fieldNameNew')
            ->setParameter('fieldNameNew', $p_fieldNames['new'])
            ->andWhere('dt.articleType = :articleType')
            ->setParameter('articleType', $p_articleType)
            ->andWhere('dt.fieldName = :fieldNameOld')
            ->setParameter('fieldNameOld', $p_fieldNames['old']);

        $q = $qb->getQuery();
        $q->execute();

    }

    public function deleteField($p_articleType, $p_fieldNames) {
        $qb = $this->createQueryBuilder('dt')
            ->delete()
            ->andWhere('dt.articleType = :articleType')
            ->setParameter('articleType', $p_articleType)
            ->andWhere('dt.fieldName = :fieldNameOld')
            ->setParameter('fieldNameOld', $p_fieldNames['old']);

        $q = $qb->getQuery();
        $q->execute();
    }


}
