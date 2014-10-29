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

    /**
     * Lists editorial comments for given article
     *
     * @param  PluginHooksEvent $event Plugins hook event
     *
     * @return boolean
     */
    public function listEditorialComments(PluginHooksEvent $event)
    {
        $article = $event->getArgument('article');
        $editorialComments = $this->em->getRepository('Newscoop\ArticlesBundle\Entity\EditorialComment')->getAllByArticleNumber($article->getArticleNumber());

        $response = $this->templating->renderResponse('NewscoopArticlesBundle:Hook:editorialComments.html.twig', array(
            'editorialComments' => $editorialComments,
            'article' => $article,
            'pluginName' => 'Editorial Comments',
        ));

        $event->addHookResponse($response);

        return true;
    }
}
