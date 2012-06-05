<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop;

use Newscoop\Entity\Article,
    Newscoop\Entity\Language;

/**
 */
class WebcodeFacadeTest extends \TestCase
{
    public function setUp()
    {
        $this->em = $this->setUpOrm('Newscoop\Entity\Webcode', 'Newscoop\Entity\Language', 'Newscoop\Entity\Article');
        $this->random = $this->getMock('Newscoop\Random');
        $this->facade = new WebcodeFacade($this->em, $this->random);

        $this->language = new Language();
        $this->em->persist($this->language);
        $this->em->flush();

        $this->article = new Article(1, $this->language);
        $this->em->persist($this->article);
        $this->em->flush();
    }

    public function tearDown()
    {
        $this->tearDownOrm($this->em);
    }

    public function testSetGetWebcode()
    {
        $webcode = 'test';
        $this->facade->setArticleWebcode($this->article, $webcode);

        $this->assertEquals($webcode, $this->facade->getArticleWebcode($this->article));
        $this->assertEquals($this->article, $this->facade->findArticleByWebcode($webcode));
    }

    public function testGenerateWebcode()
    {
        $webcode = 'random';
        $this->random->expects($this->once())
            ->method('getRandomString')
            ->will($this->returnValue($webcode));

        $this->facade->setArticleWebcode($this->article);
        $this->assertEquals($webcode, $this->facade->getArticleWebcode($this->article));
    }

    public function testGenerateWebcodeDuplicate()
    {
        $this->random->expects($this->any())
            ->method('getRandomString')
            ->will($this->onConsecutiveCalls('tic', 'tic', 'toc'));

        $this->facade->setArticleWebcode($this->article);
        $this->facade->setArticleWebcode($this->article);

        $this->assertEquals('toc', $this->facade->getArticleWebcode($this->article));
        $this->assertEquals($this->article, $this->facade->findArticleByWebcode('tic'));
    }

    /**
     * @expectedException InvalidArgumentException
     */
    public function testSetDuplicateWebcode()
    {
        $this->facade->setArticleWebcode($this->article, 'tic');
        $this->facade->setArticleWebcode($this->article, 'tic');
    }

    public function testGenerateArticleWebcodeIfNotSet()
    {
        $webcode = 'new';

        $this->random->expects($this->once())
            ->method('getRandomString')
            ->will($this->returnValue($webcode));

        $this->assertEquals($webcode, $this->facade->getArticleWebcode($this->article));
    }
}
