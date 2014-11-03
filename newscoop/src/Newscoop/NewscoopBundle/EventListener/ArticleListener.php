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
    protected $articleService;

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
        $request = $event->getRequest();
        $pos = strpos($request->getRequestUri(), '/admin');
        if ($pos === false) {
            $this->articleService->articleResolver($request);
        }
    }
}
