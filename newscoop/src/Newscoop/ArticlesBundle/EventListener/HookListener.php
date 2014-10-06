<?php

namespace Newscoop\ArticlesBundle\EventListener;

use Symfony\Bundle\FrameworkBundle\Templating\DelegatingEngine;
use Newscoop\EventDispatcher\Events\PluginHooksEvent;
use Doctrine\ORM\EntityManager;

class HookListener
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var DelegatingEngine
     */
    protected $templating;

    /**
     * Construct
     *
     * @param EntityManager    $em         Entity manager
     * @param DelegatingEngine $templating Templating
     */
    public function __construct(EntityManager $em, DelegatingEngine $templating)
    {
        $this->em = $em;
        $this->templating = $templating;
    }

    public function listEditorialComments(PluginHooksEvent $event)
    {
        $articleNumber = $event->getArgument('articleNumber');
        $editorialComments = $this->em->getRepository('Newscoop\ArticlesBundle\Entity\EditorialComment')->getAllByArticleNumber($articleNumber);

        $response = $this->templating->renderResponse(
            'NewscoopArticlesBundle:Hook:editorialComments.html.twig',
            array(
                'editorialComments' => $editorialComments,
                'articleNumber' => $articleNumber
            )
        );

        $event->addHookResponse($response);
    }
}
