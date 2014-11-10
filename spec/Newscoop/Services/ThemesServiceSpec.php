<?php

namespace spec\Newscoop\Services;

use PhpSpec\ObjectBehavior;
use Newscoop\IssueServiceInterface;
use Newscoop\Services\CacheService;
use Newscoop\Services\PublicationService;
use Newscoop\Entity\Issue;
use Newscoop\Entity\Publication;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\Bundle\DoctrineBundle\Registry;
use Newscoop\Entity\Output;
use Newscoop\Entity\Output\OutputSettingsIssue;
use Prophecy\Argument;
use Newscoop\Entity\Resource;

class ThemesServiceSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\Services\ThemesService');
        $this->shouldImplement('Newscoop\ThemesServiceInterface');
    }

    public function let(
        IssueServiceInterface $issueService,
        Issue $issue,
        CacheService $cacheService,
        PublicationService $publicationService,
        Publication $publication,
        EntityManager $em,
        Registry $doctrine,
        EntityRepository $repository,
        Output $output,
        OutputSettingsIssue $issueOutput
    )
    {
        $issueService->getIssue()->willReturn($issue);
        $publicationService->getPublication()->willReturn($publication);
        $doctrine->getManager()->willReturn($em);

        $em->getRepository(Argument::exact('Newscoop\Entity\Output'))->willReturn($repository);
        $repository->findBy(array('name' => 'Web'))->willReturn(array($output));

        $em->getRepository(Argument::exact('Newscoop\Entity\Output\OutputSettingsIssue'))->willReturn($repository);
        $repository->findBy(array('issue' => 1, 'output' => 1))->willReturn(array($issueOutput));

        $issue->getId()->willReturn(1);
        $issue->getNumber()->willReturn(10);
        $issue->getName()->willReturn("May 2014");
        $issue->getShortName()->willReturn("may2014");
        $issue->getLanguageId()->willReturn(1);
        $output->getId()->willReturn(1);
        $output->getName()->willReturn('Web');

        $this->beConstructedWith($issueService, $cacheService, $publicationService, $em);
    }

    public function it_gets_weboutput_by_name(Output $output)
    {
        $this->findByName('Web')->shouldReturn($output);
    }

    public function it_finds_by_issue_and_output(Issue $issue, Output $output, OutputSettingsIssue $issueOutput)
    {
        $this->findByIssueAndOutput($issue, $output)->shouldReturn($issueOutput);
    }

    public function it_should_throw_exception_when_name_param_is_empty_or_null()
    {
        $this
            ->shouldThrow('Exception')
            ->during('findByName', array(null));

        $this
            ->shouldThrow('Exception')
            ->during('findByName', array(''));
    }

    public function it_gets_theme_path(OutputSettingsIssue $issueOutput, Resource $resource)
    {
        $resource->getPath()->willReturn('publication_1/theme_1/');
        $issueOutput->getThemePath()->willReturn($resource);
        $this->getThemePath()->shouldReturn('publication_1/theme_1/');
    }
}
