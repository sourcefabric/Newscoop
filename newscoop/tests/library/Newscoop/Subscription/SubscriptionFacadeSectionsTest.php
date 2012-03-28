<?php
/**
 * @package Newscoop
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Subscription;

use Newscoop\Entity\User,
    Newscoop\Entity\Publication,
    Newscoop\Entity\Issue,
    Newscoop\Entity\Section,
    Newscoop\Entity\Language;

/**
 */
class SubscriptionFacadeSectionsTest extends \TestCase
{
    const DATE = '2011-01-01';
    const DAYS = 7;

    public function setUp()
    {
        $this->orm = $this->setUpOrm('Newscoop\Subscription\Subscription', 'Newscoop\Entity\User', 'Newscoop\Entity\Acl\Role', 'Newscoop\Entity\Publication', 'Newscoop\Entity\Issue', 'Newscoop\Entity\Section', 'Newscoop\Subscription\Section', 'Newscoop\Entity\Language');
        $this->facade = new SubscriptionFacade($this->orm);

        $this->user = new User('test');
        $this->publication = new Publication();
        $this->language = new Language();
        $this->language->setName('lang1');
        $this->anotherLanguage = new Language();

        $this->orm->persist($this->anotherLanguage);
        $this->orm->persist($this->language);
        $this->orm->persist($this->user);
        $this->orm->persist($this->publication);
        $this->orm->flush();

        $issue = new Issue(1, $this->publication, $this->language);
        $this->orm->persist($issue);
        $this->orm->persist(new Section(1, 'sec1', $issue));
        $this->orm->persist(new Section(2, 'sec2', $issue));
        $this->orm->flush();

        $issue2 = new Issue(2, $this->publication, $this->anotherLanguage);
        $this->orm->persist($issue2);
        $this->orm->persist(new Section(21, 'sec21', $issue2));
        $this->orm->flush();

        $this->assertEquals(2, count($this->publication->getIssues()));
        $this->assertEquals(2, count($issue->getSections()));
        $this->assertEquals($this->language, $issue->getLanguage());

        $this->values = array(
            'user' => $this->user,
            'publication' => $this->publication,
            'add_sections' => true,
            'start_date' => self::DATE,
            'days' => self::DAYS,
        );
    }

    public function tearDown()
    {
        $this->tearDownOrm($this->orm);
    }

    public function testSaveSectionsAnyLanguageTrial()
    {
        $subscription = $this->facade->save(array_merge($this->values, array(
            'type' => Subscription::TYPE_TRIAL,
        )));

        $this->assertEquals(3, count($subscription->getSections()));
        foreach ($subscription->getSections() as $section) {
            $this->assertEquals(self::DAYS, $section->getDays());
            $this->assertEquals(self::DAYS, $section->getPaidDays());
            $this->assertEquals(self::DATE, $section->getStartDate()->format('Y-m-d'));
            $this->assertFalse($section->hasLanguage());
            $this->assertContains($section->getName(), array('sec1', 'sec2', 'sec21'));
        }
    }

    public function testSaveSectionsAnyLanguagePayNow()
    {
        $subscription = $this->facade->save(array_merge($this->values, array(
            'type' => Subscription::TYPE_PAID_NOW,
        )));

        $this->assertEquals(3, count($subscription->getSections()));

        foreach ($subscription->getSections() as $section) {
            $this->assertEquals(self::DAYS, $section->getPaidDays());
        }
    }

    public function testSaveSectionsAnyLanguagePayLater()
    {
        $subscription = $this->facade->save(array_merge($this->values, array(
            'type' => Subscription::TYPE_PAID,
        )));

        $this->assertEquals(3, count($subscription->getSections()));

        foreach ($subscription->getSections() as $section) {
            $this->assertEquals(0, $section->getPaidDays());
            $this->assertEquals(self::DAYS, $section->getDays());
        }
    }

    public function testSaveSectionsGivenLanguage()
    {
        $subscription = $this->facade->save(array_merge($this->values, array(
            'type' => Subscription::TYPE_PAID,
            'individual_languages' => true,
            'languages' => array($this->language->getId()),
        )));

        $this->assertEquals(2, count($subscription->getSections()));

        foreach ($subscription->getSections() as $section) {
            $this->assertEquals($this->language->getName(), $section->getLanguageName());
        }
    }
}
