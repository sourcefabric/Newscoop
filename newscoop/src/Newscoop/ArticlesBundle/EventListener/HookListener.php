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
    private $em;
    private $templating;
    private $clientManager;
    private $preferencesService;
    private $publicationService;

    /**
     * Construct
     *
     * @param EntityManager   $em         Entity manager
     * @param EngineInterface $templating Templating
     */
    public function __construct(EntityManager $em, EngineInterface $templating, $clientManager, $preferencesService, $publicationService)
    {
        $this->em = $em;
        $this->templating = $templating;
        $this->clientManager = $clientManager;
        $this->preferencesService = $preferencesService;
        $this->publicationService = $publicationService;
    }

    /**
     * Lists editorial comments for given article
     *
     * @param PluginHooksEvent $event Plugins hook event
     *
     * @return boolean
     */
    public function renderEditorialCommentsTemplate(PluginHooksEvent $event)
    {
        $article = $event->getArgument('article');
        $clientName = 'newscoop_'.$this->preferencesService->SiteSecretKey;
        $client = $this->em->getRepository('\Newscoop\GimmeBundle\Entity\Client')->findOneByName($clientName);

        $response = $this->templating->renderResponse('NewscoopArticlesBundle:Hook:editorialComments.html.twig', array(
            'article' => $article,
            'clientId' => $client->getPublicId(),
            'pluginName' => 'Editorial Comments',
        ));

        $event->addHookResponse($response);

        return true;
    }
}
