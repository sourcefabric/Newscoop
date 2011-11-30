<?php

namespace Newscoop;

/**
 * The datetime object class used in the ArticleDatetime to pass date formats
 * @author mihaibalaceanu
 */
class ArticleDatetime
{
    private $startDate = null;

    private $endDate = null;

    private $startTime = null;

    private $endTime = null;

    private $recurring = null;

    private $spawns = array();

    /**
     * @param mixed $format
     * 		array of start_date, end_date, etc. keys
     * 		array with 1 element: start_date => end_date strings - Date and Time formats: http://php.net/manual/en/datetime.formats.php
     * 		string formatted like above and start separated by end with a - (dash) padded with spaces
     * 		also in case of array you can add recurring => [daily|weekly|etc.]
     * @param string $recurring
     * @todo check start < end
     */
    public function __construct($format, $recurring=null)
    {
        if (is_array($format))
        {
            if (array_key_exists('start_date', $format))
            {
                $this->setStartDate($format['start_date']);
                $this->setEndDate(isset($format['end_date']) ? $format['end_date'] : null);
                $this->setStartTime(isset($format['start_time']) ? $format['start_time'] : null);
                $this->setEndTime(isset($format['end_time']) ? $format['end_time'] : null);
                if (isset($format['recurring'])) {
                    $this->recurring = $format['recurring'];
                }
                return;
            }
            $start = key($format);
            $end = current($format);
        }
        elseif (is_string($format))
        {
            list($start, $end) = explode(" - ", $format);
            if (in_array( ($end = trim($end)), array( '1', '0', 'true', 'false'))) {
                $end = (bool) $end;
            }
        }

        $this->setRecurring($recurring);

        if (!( $startTimestamp = strtotime($start))) {
            return;
        }

        $parsedStart = date_parse($start);
        $startHasDate = $parsedStart['month']!==false && $parsedStart['day']!==false;
        $startHasTime = $parsedStart['hour']!==false && $parsedStart['minute']!==false;

        $startDate = strftime('%F', $startTimestamp);

        switch (true)
        {
            // TODO fix midnight for relative formats
            case is_bool($end) && $end : // full day
                $this->setStartDate($startDate);
                $this->setStartTime($startHasTime ? strftime('%T', $startTimestamp) : null);
                $this->setEndDate(null);
                $this->setEndTime(null);
                break;

            case is_string($end) : // ...until date, or just a single time value for the date

                $parsedEnd = date_parse($end);
                $endHasDate = $parsedEnd['month']!==false && $parsedEnd['day']!==false;
                $endHasTime = $parsedEnd['hour']!==false && $parsedEnd['minute']!==false;

                $end = preg_replace("/-\s*recurring:(\w+)/", "", $end);

                $this->setStartDate($startDate);

                if ($endHasDate) {
                    $this->setEndDate(strftime('%F', strtotime($end)));
                }
                if ($endHasTime && ($parsedEnd['hour']!=0 || $parsedEnd['minute']!=0 || $parsedEnd['second']!=0)) {
                    $this->setEndTime(strftime('%T', strtotime($end)));
                }

                if ($startHasTime && ($parsedStart['hour']!=0 || $parsedStart['minute']!=0 || $parsedStart['second']!=0)) {
                    $this->setStartTime(strftime('%T', strtotime($start)));
                }

                break;

            case is_array($end) : // time interval for current date

                $spawn =& $this;
                foreach ($end as $startTime => $endTime)
                {
                    if ($startTime == 'recurring' ) { // TODO dirty fix for reccuring flag take out
                        continue;
                    }
                    $spawn->setStartDate($startDate);
                    $spawn->setEndDate(null);
                    $spawn->setStartTime(strftime('%T', strtotime($startTime)));
                    $spawn->setEndTime(strftime('%T', strtotime($endTime)));

                    $this->spawns[] = clone $spawn;
                    $spawn =& $this->spawns[count($this->spawns)-1];
                };
                array_pop($this->spawns);
                break;
        }
    }

    /**
     * clears spawns
     */
    public function __clone()
    {
        $this->spawns = array();
    }

    /**
     * @param string $value date format
     */
    public function setStartDate($value)
    {
        $this->startDate = is_null($value) ? null : new \DateTime($value);
    }

    /**
     * @param string $value
     */
    public function setStartTime($value)
    {
        $this->startTime = is_null($value) ? null : new \DateTime($value);
    }

    /**
     * @param string $value
     */
    public function setEndDate($value)
    {
        $this->endDate = is_null($value) ? null : new \DateTime($value);
    }

    /**
     * @param string $value
     */
    public function setEndTime($value)
    {
        $this->endTime = is_null($value) ? null : new \DateTime($value);
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return \DateTime
     */
    public function getStartTime()
    {
        return $this->startTime;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndTime()
    {
        return $this->endTime;
    }

    /**
     * Set recurring flag
     * @param string $value daily|weekly|monthly|yearly
     */
    public function setRecurring($value)
    {
        if (!in_array( $value, array('daily', 'weekly', 'monthly', 'yearly'))) {
            return false;
        }
        $this->recurring = $value;
    }

    /**
     * Get if date object is recurring
     */
    public function getRecurring()
    {
        return $this->recurring;
    }

    /**
     * Get other spawned objects from a contruct
     */
    public function getSpawns()
    {
        return $this->spawns;
    }
}