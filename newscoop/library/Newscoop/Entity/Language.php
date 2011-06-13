<?php
/**
 * @package Newscoop
 * @subpackage Languages
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
	 * Provides the class name as a constant. 
	 */
	const NAME = __CLASS__;
	
	/* --------------------------------------------------------------- */
	
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
    private $month1;

    /**
     * @column(name="Month2")
     * @var string
     */
    private $month2;

    /**
     * @column(name="Month3")
     * @var string
     */
    private $month3;

    /**
     * @column(name="Month4")
     * @var string
     */
    private $month4;

    /**
     * @column(name="Month5")
     * @var string
     */
    private $month5;

    /**
     * @column(name="Month6")
     * @var string
     */
    private $month6;

    /**
     * @column(name="Month7")
     * @var string
     */
    private $month7;

    /**
     * @column(name="Month8")
     * @var string
     */
    private $month8;

    /**
     * @column(name="Month9")
     * @var string
     */
    private $month9;

    /**
     * @column(name="Month10")
     * @var string
     */
    private $month10;

    /**
     * @column(name="Month11")
     * @var string
     */
    private $month11;

    /**
     * @column(name="Month12")
     * @var string
     */
    private $month12;

    /**
     * @column(name="ShortMonth1")
     * @var string
     */
    private $short_month1;

    /**
     * @column(name="ShortMonth2")
     * @var string
     */
    private $short_month2;

    /**
     * @column(name="ShortMonth3")
     * @var string
     */
    private $short_month3;

    /**
     * @column(name="ShortMonth4")
     * @var string
     */
    private $short_month4;

    /**
     * @column(name="ShortMonth5")
     * @var string
     */
    private $short_month5;

    /**
     * @column(name="ShortMonth6")
     * @var string
     */
    private $short_month6;

    /**
     * @column(name="ShortMonth7")
     * @var string
     */
    private $short_month7;

    /**
     * @column(name="ShortMonth8")
     * @var string
     */
    private $short_month8;

    /**
     * @column(name="ShortMonth9")
     * @var string
     */
    private $short_month9;

    /**
     * @column(name="ShortMonth10")
     * @var string
     */
    private $short_month10;

    /**
     * @column(name="ShortMonth11")
     * @var string
     */
    private $short_month11;

    /**
     * @column(name="ShortMonth12")
     * @var string
     */
    private $short_month12;

    /**
     * @column(name="WDay1")
     * @var string
     */
    private $day1;

    /**
     * @column(name="WDay2")
     * @var string
     */
    private $day2;

    /**
     * @column(name="WDay3")
     * @var string
     */
    private $day3;

    /**
     * @column(name="WDay4")
     * @var string
     */
    private $day4;

    /**
     * @column(name="WDay5")
     * @var string
     */
    private $day5;

    /**
     * @column(name="WDay6")
     * @var string
     */
    private $day6;

    /**
     * @column(name="WDay7")
     * @var string
     */
    private $day7;

    /**
     * @column(name="ShortWDay1")
     * @var string
     */
    private $short_day1;

    /**
     * @column(name="ShortWDay2")
     * @var string
     */
    private $short_day2;

    /**
     * @column(name="ShortWDay3")
     * @var string
     */
    private $short_day3;

    /**
     * @column(name="ShortWDay4")
     * @var string
     */
    private $short_day4;

    /**
     * @column(name="ShortWDay5")
     * @var string
     */
    private $short_day5;

    /**
     * @column(name="ShortWDay6")
     * @var string
     */
    private $short_day6;

    /**
     * @column(name="ShortWDay7")
     * @var string
     */
    private $short_day7;

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
     * Set name
     *
     * @param string $name
     * @return Newscoop\Entity\Language
     */
    public function setName($name)
    {
        $this->name = (string) $name;
        return $this;
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

    /**
     * Set original/native name
     *
     * @param string $name
     * @return Newscoop\Entity\Language
     */
    public function setNativeName($native_name)
    {
        $this->original_name = (string) $native_name;
        return $this;
    }

    public function getNativeName()
    {
        return $this->original_name;
    }

    /**
     * Set code page
     *
     * @param string $name
     * @return Newscoop\Entity\Language
     */
    public function setCodePage($code_page)
    {
        $this->code_page = (string) $code_page;
        return $this;
    }

    public function getCodePage()
    {
        return $this->code_page;
    }

    /**
     * Set code
     *
     * @param string $name
     * @return Newscoop\Entity\Language
     */
    public function setCode($code)
    {
        $this->code = (string) $code;
        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function setMonth1($month)
    {
        $this->month1 = (string) $month;
        return $this;
    }    

    public function getMonth1()
    {
        return $this->month1;
    }

    public function setMonth2($month)
    {
        $this->month2 = (string) $month;
        return $this;
    }

    public function getMonth2()
    {
        return $this->month2;
    }

    public function setMonth3($month)
    {
        $this->month3 = (string) $month;
        return $this;
    }

    public function getMonth3()
    {
        return $this->month3;
    }

    public function setMonth4($month)
    {
        $this->month4 = (string) $month;
        return $this;
    }

    public function getMonth4()
    {
        return $this->month4;
    }

    public function setMonth5($month)
    {
        $this->month5 = (string) $month;
        return $this;
    }

    public function getMonth5()
    {
        return $this->month5;
    }

    public function setMonth6($month)
    {
        $this->month6 = (string) $month;
        return $this;
    }

    public function getMonth6()
    {
        return $this->month6;
    }

    public function setMonth7($month)
    {
        $this->month7 = (string) $month;
        return $this;
    }

    public function getMonth7()
    {
        return $this->month7;
    }

    public function setMonth8($month)
    {
        $this->month8 = (string) $month;
        return $this;
    }

    public function getMonth8()
    {
        return $this->month8;
    }

    public function setMonth9($month)
    {
        $this->month9 = (string) $month;
        return $this;
    }

    public function getMonth9()
    {
        return $this->month9;
    }

    public function setMonth10($month)
    {
        $this->month10 = (string) $month;
        return $this;
    }

    public function getMonth10()
    {
        return $this->month10;
    }

    public function setMonth11($month)
    {
        $this->month11 = (string) $month;
        return $this;
    }

    public function getMonth11()
    {
        return $this->month11;
    }

    public function setMonth12($month)
    {
        $this->month12 = (string) $month;
        return $this;
    }

    public function getMonth12()
    {
        return $this->month12;
    }

    public function setShortMonth1($month)
    {
        $this->short_month1 = (string) $month;
        return $this;
    }

    public function getShortMonth1()
    {
        return $this->short_month1;
    }

    public function setShortMonth2($month)
    {
        $this->short_month2 = (string) $month;
        return $this;
    }

    public function getShortMonth2()
    {
        return $this->short_month2;
    }

    public function setShortMonth3($month)
    {
        $this->short_month3 = (string) $month;
        return $this;
    }

    public function getShortMonth3()
    {
        return $this->short_month3;
    }

    public function setShortMonth4($month)
    {
        $this->short_month4 = (string) $month;
        return $this;
    }

    public function getShortMonth4()
    {
        return $this->short_month4;
    }

    public function setShortMonth5($month)
    {
        $this->short_month5 = (string) $month;
        return $this;
    }

    public function getShortMonth5()
    {
        return $this->short_month5;
    }

    public function setShortMonth6($month)
    {
        $this->short_month6 = (string) $month;
        return $this;
    }

    public function getShortMonth6()
    {
        return $this->short_month6;
    }

    public function setShortMonth7($month)
    {
        $this->short_month7 = (string) $month;
        return $this;
    }

    public function getShortMonth7()
    {
        return $this->short_month7;
    }

    public function setShortMonth8($month)
    {
        $this->short_month8 = (string) $month;
        return $this;
    }

    public function getShortMonth8()
    {
        return $this->short_month8;
    }

    public function setShortMonth9($month)
    {
        $this->short_month9 = (string) $month;
        return $this;
    }

    public function getShortMonth9()
    {
        return $this->short_month9;
    }

    public function setShortMonth10($month)
    {
        $this->short_month10 = (string) $month;
        return $this;
    }

    public function getShortMonth10()
    {
        return $this->short_month10;
    }

    public function setShortMonth11($month)
    {
        $this->short_month11 = (string) $month;
        return $this;
    }

    public function getShortMonth11()
    {
        return $this->short_month11;
    }

    public function setShortMonth12($month)
    {
        $this->short_month12 = (string) $month;
        return $this;
    }

    public function getShortMonth12()
    {
        return $this->short_month12;
    }

    public function setDay1($day)
    {
        $this->day1 = (string) $day;
        return $this;
    }

    public function getDay1()
    {
        return $this->day1;
    }

    public function setDay2($day)
    {
        $this->day2 = (string) $day;
        return $this;
    }

    public function getDay2()
    {
        return $this->day2;
    }

    public function setDay3($day)
    {
        $this->day3 = (string) $day;
        return $this;
    }

    public function getDay3()
    {
        return $this->day3;
    }

    public function setDay4($day)
    {
        $this->day4 = (string) $day;
        return $this;
    }

    public function getDay4()
    {
        return $this->day4;
    }

    public function setDay5($day)
    {
        $this->day5 = (string) $day;
        return $this;
    }

    public function getDay5()
    {
        return $this->day5;
    }

    public function setDay6($day)
    {
        $this->day6 = (string) $day;
        return $this;
    }

    public function getDay6()
    {
        return $this->day6;
    }

    public function setDay7($day)
    {
        $this->day7 = (string) $day;
        return $this;
    }

    public function getDay7()
    {
        return $this->day7;
    }

    public function setShortDay1($day)
    {
        $this->short_day1 = (string) $day;
        return $this;
    }

    public function getShortDay1()
    {
        return $this->short_day1;
    }

    public function setShortDay2($day)
    {
        $this->short_day2 = (string) $day;
        return $this;
    }

    public function getShortDay2()
    {
        return $this->short_day2;
    }

    public function setShortDay3($day)
    {
        $this->short_day3 = (string) $day;
        return $this;
    }

    public function getShortDay3()
    {
        return $this->short_day3;
    }

    public function setShortDay4($day)
    {
        $this->short_day4 = (string) $day;
        return $this;
    }

    public function getShortDay4()
    {
        return $this->short_day4;
    }

    public function setShortDay5($day)
    {
        $this->short_day5 = (string) $day;
        return $this;
    }

    public function getShortDay5()
    {
        return $this->short_day5;
    }

    public function setShortDay6($day)
    {
        $this->short_day6 = (string) $day;
        return $this;
    }

    public function getShortDay6()
    {
        return $this->short_day6;
    }

    public function setShortDay7($day)
    {
        $this->short_day7 = (string) $day;
        return $this;
    }

    public function getShortDay7()
    {
        return $this->short_day7;
    }
}
