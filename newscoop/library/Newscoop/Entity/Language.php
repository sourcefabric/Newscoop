<?php
/**
 * @package Newscoop
 * @subpackage Languages
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * Language entity
 * @ORM\Entity(repositoryClass="Newscoop\Entity\Repository\LanguageRepository")
 * @ORM\Table(name="Languages")
 */
class Language
{
    /**
     * Provides the class name as a constant.
     */
    const NAME = __CLASS__;

    /**
     * @ORM\Id 
     * @ORM\GeneratedValue
     * @ORM\Column(name="Id", type="integer")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(name="Name", nullable=True)
     * @var string
     */
    protected $name;

    /**
     * @ORM\Column(name="CodePage", nullable=True)
     * @var string
     */
    protected $code_page;

    /**
     * @ORM\Column(name="OrigName", nullable=True)
     * @var string
     */
    protected $original_name;

    /**
     * @ORM\Column(name="Code", nullable=True)
     * @var string
     */
    protected $code;

    /**
     * @ORM\Column(name="RFC3066bis")
     * @var string
     */
    protected $RFC3066bis;

    /**
     * @ORM\Column(name="Month1", nullable=True)
     * @var string
     */
    protected $month1;

    /**
     * @ORM\Column(name="Month2", nullable=True)
     * @var string
     */
    protected $month2;

    /**
     * @ORM\Column(name="Month3", nullable=True)
     * @var string
     */
    protected $month3;

    /**
     * @ORM\Column(name="Month4", nullable=True)
     * @var string
     */
    protected $month4;

    /**
     * @ORM\Column(name="Month5", nullable=True)
     * @var string
     */
    protected $month5;

    /**
     * @ORM\Column(name="Month6", nullable=True)
     * @var string
     */
    protected $month6;

    /**
     * @ORM\Column(name="Month7", nullable=True)
     * @var string
     */
    protected $month7;

    /**
     * @ORM\Column(name="Month8", nullable=True)
     * @var string
     */
    protected $month8;

    /**
     * @ORM\Column(name="Month9", nullable=True)
     * @var string
     */
    protected $month9;

    /**
     * @ORM\Column(name="Month10", nullable=True)
     * @var string
     */
    protected $month10;

    /**
     * @ORM\Column(name="Month11", nullable=True)
     * @var string
     */
    protected $month11;

    /**
     * @ORM\Column(name="Month12", nullable=True)
     * @var string
     */
    protected $month12;

    /**
     * @ORM\Column(name="ShortMonth1", nullable=True)
     * @var string
     */
    protected $short_month1;

    /**
     * @ORM\Column(name="ShortMonth2", nullable=True)
     * @var string
     */
    protected $short_month2;

    /**
     * @ORM\Column(name="ShortMonth3", nullable=True)
     * @var string
     */
    protected $short_month3;

    /**
     * @ORM\Column(name="ShortMonth4", nullable=True)
     * @var string
     */
    protected $short_month4;

    /**
     * @ORM\Column(name="ShortMonth5", nullable=True)
     * @var string
     */
    protected $short_month5;

    /**
     * @ORM\Column(name="ShortMonth6", nullable=True)
     * @var string
     */
    protected $short_month6;

    /**
     * @ORM\Column(name="ShortMonth7", nullable=True)
     * @var string
     */
    protected $short_month7;

    /**
     * @ORM\Column(name="ShortMonth8", nullable=True)
     * @var string
     */
    protected $short_month8;

    /**
     * @ORM\Column(name="ShortMonth9", nullable=True)
     * @var string
     */
    protected $short_month9;

    /**
     * @ORM\Column(name="ShortMonth10", nullable=True)
     * @var string
     */
    protected $short_month10;

    /**
     * @ORM\Column(name="ShortMonth11", nullable=True)
     * @var string
     */
    protected $short_month11;

    /**
     * @ORM\Column(name="ShortMonth12", nullable=True)
     * @var string
     */
    protected $short_month12;

    /**
     * @ORM\Column(name="WDay1", nullable=True)
     * @var string
     */
    protected $day1;

    /**
     * @ORM\Column(name="WDay2", nullable=True)
     * @var string
     */
    protected $day2;

    /**
     * @ORM\Column(name="WDay3", nullable=True)
     * @var string
     */
    protected $day3;

    /**
     * @ORM\Column(name="WDay4", nullable=True)
     * @var string
     */
    protected $day4;

    /**
     * @ORM\Column(name="WDay5", nullable=True)
     * @var string
     */
    protected $day5;

    /**
     * @ORM\Column(name="WDay6", nullable=True)
     * @var string
     */
    protected $day6;

    /**
     * @ORM\Column(name="WDay7", nullable=True)
     * @var string
     */
    protected $day7;

    /**
     * @ORM\Column(name="ShortWDay1", nullable=True)
     * @var string
     */
    protected $short_day1;

    /**
     * @ORM\Column(name="ShortWDay2", nullable=True)
     * @var string
     */
    protected $short_day2;

    /**
     * @ORM\Column(name="ShortWDay3", nullable=True)
     * @var string
     */
    protected $short_day3;

    /**
     * @ORM\Column(name="ShortWDay4", nullable=True)
     * @var string
     */
    protected $short_day4;

    /**
     * @ORM\Column(name="ShortWDay5", nullable=True)
     * @var string
     */
    protected $short_day5;

    /**
     * @ORM\Column(name="ShortWDay6", nullable=True)
     * @var string
     */
    protected $short_day6;

    /**
     * @ORM\Column(name="ShortWDay7", nullable=True)
     * @var string
     */
    protected $short_day7;

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
     * Set id
     * @var int
     */
    public function setId($id)
    {
        $this->id = $id;
        return $this;
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

    /**
     * Set RFC3066bis
     *
     * @param string $RFC3066bis Language code
     *
     * @return self
     */
    public function setRFC3066bis($RFC3066bis)
    {
        $this->RFC3066bis = (string) $RFC3066bis;

        return  $this;
    }

    /**
     * Get RFC3066bis
     *
     * @return string
     */
    public function getRFC3066bis()
    {
        return $this->RFC3066bis;
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
