<?php

namespace spec\Newscoop\Services;

use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpKernel\Debug\TraceableEventDispatcher;
use Doctrine\ORM\EntityManager;
use Newscoop\Entity\Article;
use Newscoop\Entity\Language;
use Newscoop\EventDispatcher\Events\GenericEvent;
use Prophecy\Argument;

class EditorServiceSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Newscoop\Services\EditorService');
        $this->shouldImplement('Newscoop\EditorInterface');
    }

    public function let(TraceableEventDispatcher $dispatcher, EntityManager $em, Article $article)
    {
        $article->getNumber()->willReturn(10);
        $article->getName()->willReturn("test article");
        $article->getPublicationId()->willReturn(2);
        $article->getLanguageId()->willReturn(1);
        $article->getIssueId()->willReturn(20);
        $article->getSectionId()->willReturn(30);

        $this->beConstructedWith($dispatcher, $em);
    }

    public function it_should_get_editor_link(
        Article $article,
        Language $language,
        EntityManager $em,
        TraceableEventDispatcher $dispatcher,
        GenericEvent $event)
    {

        $em
            ->getReference('Newscoop\Entity\Language', 1)
            ->willReturn($language);

        $language->getCode()->willReturn('en');
        $dispatcher->dispatch(
             'newscoop_admin.editor',
             Argument::type('Newscoop\EventDispatcher\Events\GenericEvent')
        )->shouldBeCalled()->willReturn($event);

        $event->getArgument('link')->willReturn(null);
        $params = "f_publication_id=2&f_issue_number=20&f_section_number=30&f_article_number=10&f_language_id=1&f_language_selected=1";

        $this->getLink($article)->shouldReturn('/admin/articles/edit.php?'. $params);
    }

    public function it_should_return_default_editor_parameters(Article $article)
    {
        $params = "?f_publication_id=2&f_issue_number=20&f_section_number=30&f_article_number=10&f_language_id=1&f_language_selected=1";
        $this->getLinkParameters($article)->shouldReturn($params);
    }
}
