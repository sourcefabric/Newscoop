<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Subscription;

use Newscoop\Entity\Language;

/**
 */
class SectionFacadeTest extends \TestCase
{
    const SECTION = 10;
    const DATE = '2011-11-11';
    const DAYS = 7;
    const PAID_DAYS = 5;

    public function setUp() {
        $this->orm = $this->setUpOrm('Newscoop\Subscription\Subscription', 'Newscoop\Subscription\Section', 'Newscoop\Entity\Language');
        $this->facade = new SectionFacade($this->orm);

        $this->subscription = new Subscription();
        $this->orm->persist($this->subscription);

        $this->language = new Language();
        $this->orm->persist($this->language);

        $this->orm->flush();

        $this->values = array(
            'subscription' => $this->subscription->getId(),
            'section' => array('number' => self::SECTION),
            'startDate' => self::DATE,
            'days' => self::DAYS,
            'paidDays' => self::PAID_DAYS,
        );
    }

    public function tearDown()
    {
        $this->tearDownOrm($this->orm);
    }

    public function testInstance()
    {
        $this->assertInstanceOf('Newscoop\Subscription\SectionFacade', $this->facade);
    }

    public function testSave()
    {
        $section = $this->facade->save($this->values);
        $this->assertInstanceOf('Newscoop\Subscription\Section', $section);
        $this->assertNotNull($section->getId());
        $this->assertEquals($this->subscription, $section->getSubscription());
        $this->assertEquals(self::SECTION, $section->getSectionNumber());
        $this->assertEquals(self::DATE, $section->getStartDate()->format('Y-m-d'));
        $this->assertEquals(self::DAYS, $section->getDays());
        $this->assertEquals(self::PAID_DAYS, $section->getPaidDays());
        $this->assertFalse($section->hasLanguage());
    }

    public function testSaveLanguage()
    {
        $section = $this->facade->save(array_merge($this->values, array(
            'language' => array('id' => $this->language->getId()),
        )));

        $this->assertTrue($section->hasLanguage());
        $this->assertEquals($this->language, $section->getLanguage());
    }

    public function testSaveUpdate()
    {
        $section = $this->facade->save($this->values);

        $this->facade->save(array(
            'days' => 128,
            'paidDays' => 120,
            'startDate' => '2012-12-12',
        ), $section);

        $this->assertEquals(128, $section->getDays());
        $this->assertEquals(120, $section->getPaidDays());
        $this->assertEquals('2012-12-12', $section->getStartDate()->format('Y-m-d'));
    }

    public function testDelete()
    {
        $section = $this->facade->save($this->values);
        $id = $section->getId();

        $this->assertNotNull($this->facade->find($id));
        $this->facade->delete($id);
        $this->assertNull($this->facade->find($id));
    }
}
