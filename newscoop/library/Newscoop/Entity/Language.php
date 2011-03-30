<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

/**
 * Language entity
 * @entity(repositoryClass="Newscoop\Entity\Repository\LanguageRepository")
 * @table(name="Languages")
 */
class Language 
{
    /**
     * @id @generatedValue
     * @column(name="Id", type="integer")
     * @var int
     */
    private $id;

    /**
     * @column(name="Name")
     * @var string
     */
    private $name;

    /**
     * @column(name="CodePage")
     * @var string
     */
    private $code_page;

    /**
     * @column(name="OrigName")
     * @var string
     */
    private $original_name;

    /**
     * @column(name="Code")
     * @var string
     */
    private $code;

    /**
     * @column(name="Month1")
     * @var string
     */
    private $month_1;

    /**
     * @column(name="Month2")
     * @var string
     */
    private $month_2;

    /**
     * @column(name="Month3")
     * @var string
     */
    private $month_3;

    /**
     * @column(name="Month4")
     * @var string
     */
    private $month_4;

    /**
     * @column(name="Month5")
     * @var string
     */
    private $month_5;

    /**
     * @column(name="Month6")
     * @var string
     */
    private $month_6;

    /**
     * @column(name="Month7")
     * @var string
     */
    private $month_7;

    /**
     * @column(name="Month8")
     * @var string
     */
    private $month_8;

    /**
     * @column(name="Month9")
     * @var string
     */
    private $month_9;

    /**
     * @column(name="Month10")
     * @var string
     */
    private $month_10;

    /**
     * @column(name="Month11")
     * @var string
     */
    private $month_11;

    /**
     * @column(name="Month12")
     * @var string
     */
    private $month_12;

    /**
     * @column(name="ShortMonth1")
     * @var string
     */
    private $short_month_1;

    /**
     * @column(name="ShortMonth2")
     * @var string
     */
    private $short_month_2;

    /**
     * @column(name="ShortMonth3")
     * @var string
     */
    private $short_month_3;

    /**
     * @column(name="ShortMonth4")
     * @var string
     */
    private $short_month_4;

    /**
     * @column(name="ShortMonth5")
     * @var string
     */
    private $short_month_5;

    /**
     * @column(name="ShortMonth6")
     * @var string
     */
    private $short_month_6;

    /**
     * @column(name="ShortMonth7")
     * @var string
     */
    private $short_month_7;

    /**
     * @column(name="ShortMonth8")
     * @var string
     */
    private $short_month_8;

    /**
     * @column(name="ShortMonth9")
     * @var string
     */
    private $short_month_9;

    /**
     * @column(name="ShortMonth10")
     * @var string
     */
    private $short_month_10;

    /**
     * @column(name="ShortMonth11")
     * @var string
     */
    private $short_month_11;

    /**
     * @column(name="ShortMonth12")
     * @var string
     */
    private $short_month_12;

    /**
     * @column(name="WDay1")
     * @var string
     */
    private $weekday_1;

    /**
     * @column(name="WDay2")
     * @var string
     */
    private $weekday_2;

    /**
     * @column(name="WDay3")
     * @var string
     */
    private $weekday_3;

    /**
     * @column(name="WDay4")
     * @var string
     */
    private $weekday_4;

    /**
     * @column(name="WDay5")
     * @var string
     */
    private $weekday_5;

    /**
     * @column(name="WDay6")
     * @var string
     */
    private $weekday_6;

    /**
     * @column(name="WDay7")
     * @var string
     */
    private $weekday_7;

    /**
     * @column(name="ShortWDay1")
     * @var string
     */
    private $short_weekday_1;

    /**
     * @column(name="ShortWDay2")
     * @var string
     */
    private $short_weekday_2;

    /**
     * @column(name="ShortWDay3")
     * @var string
     */
    private $short_weekday_3;

    /**
     * @column(name="ShortWDay4")
     * @var string
     */
    private $short_weekday_4;

    /**
     * @column(name="ShortWDay5")
     * @var string
     */
    private $short_weekday_5;

    /**
     * @column(name="ShortWDay6")
     * @var string
     */
    private $short_weekday_6;

    /**
     * @column(name="ShortWDay7")
     * @var string
     */
    private $short_weekday_7;

    /**
     * Get id
     *
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get language name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function getNativeName()
    {
        return $this->original_name;
    }

    public function getCode()
    {
        return $this->code;
    }
}