<?php
/**
 * @package Newscoop\ArticlesBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\ArticlesBundle\EventListener;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Newscoop\EventDispatcher\Events\PluginHooksEvent;
use Doctrine\ORM\EntityManager;

class HookListener
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @var EngineInterface
     */
    protected $templating;

    /**
     * Construct
     *
     * @param EntityManager    $em         Entity manager
     * @param EngineInterface  $templating Templating
     */
    public function __construct(EntityManager $em, EngineInterface $templating)
    {
        $this->em = $em;
        $this->templating = $templating;
    }

    public function listEditorialComments(PluginHooksEvent $event)
    {
        $articleNumber = $event->getArgument('articleNumber');
        $editorialComments = $this->em->getRepository('Newscoop\ArticlesBundle\Entity\EditorialComment')->getAllByArticleNumber($articleNumber);

        $response = $this->templating->renderResponse('NewscoopArticlesBundle:Hook:editorialComments.html.twig', array(
            'editorialComments' => $editorialComments,
            'articleNumber' => $articleNumber
        ));

        $event->addHookResponse($response);

        return true;
    }
}
