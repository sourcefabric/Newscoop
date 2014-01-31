<?php
/**
 * @package Newscoop\Newscoop
 * @author Yorick Terweijden <yorick.terweijden@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\EventListener;

use Newscoop\Services\ArticleService;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Run publication resolver on request
 */
class ArticleListener
{   
    /**
     * Article service
     * @var ArticleService
     */
    private $articleService;

    /**
     * Construct ArticleListener object
     * @param ArticleService $articleService Article service
     */
    public function __construct(ArticleService $articleService)
    {
        $this->articleService = $articleService;
    }
    
    public function onRequest(GetResponseEvent $event)
    {
        $pos = strpos($_SERVER['REQUEST_URI'], '_profiler');
        if ($pos === false) {
            $request = $event->getRequest();
            $this->articleService->articleResolver($event->getRequest());
        }
    }
}