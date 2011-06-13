<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Entity\Repository;

/**
 */
class TemplateRepositoryIsUsedTest extends \RepositoryTestCase
{
    /** @var string */
    protected $entity = 'Newscoop\Entity\Template';

    /** @var Newscoop\Entity\Repository\TemplateRepository */
    protected $repository;

    /** @var Newscoop\Entity\Issue */
    protected $issue;

    /** @var Newscoop\Entity\Template */
    protected $template;

    public function setUp()
    {
        parent::setUp(array(
            'Newscoop\Entity\Issue',
            'Newscoop\Entity\Section',
            $this->entity,
        ));

        $this->repository = $this->em->getRepository($this->entity);

        $this->issue = new \Newscoop\Entity\Issue(1);
        $this->em->persist($this->issue);

        $this->section = new \Newscoop\Entity\Section(1, 'test');
        $this->em->persist($this->section);

        $this->template = $this->repository->getTemplate('key');
    }

    public function testIsNotUsed()
    {
        $this->assertFalse($this->repository->isUsed('non-existing-key'));
        $this->assertFalse($this->repository->isUsed($this->template->getKey()));
    }

    public function testIsUsedIssueTemplate()
    {
        $this->issue->setTemplate($this->template);
        $this->flushAssertTrue();
    }

    public function testIsUsedIssueSectionTemplate()
    {
        $this->issue->setSectionTemplate($this->template);
        $this->flushAssertTrue();
    }

    public function testIsUsedIssueArticleTemplate()
    {
        $this->issue->setArticleTemplate($this->template);
        $this->flushAssertTrue();
    }

    public function testIsUsedSectionTemplate()
    {
        $this->section->setTemplate($this->template);
        $this->flushAssertTrue();
    }

    public function testIsUsedSectionArticleTemplate()
    {
        $this->section->setArticleTemplate($this->template);
        $this->flushAssertTrue();
    }

    private function flushAssertTrue()
    {
        $this->em->flush();
        $this->assertTrue($this->repository->isUsed($this->template->getKey()));
    }
}
