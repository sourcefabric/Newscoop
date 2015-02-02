<?php
/**
 * @package Newscoop\Newscoop
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\EventListener;

use Newscoop\Services\IssueService;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Issue listener to identify current issue
 */
class IssueListener
{
    /**
     * Issue service
     * @var IssueService
     */
    protected $issueService;

    /**
     * Contruct
     *
     * @param IssueService $issueService Issue service
     */
    public function __construct(IssueService $issueService)
    {
        $this->issueService = $issueService;
    }

    /**
     * Resolve issue on request
     *
     * @param GetResponseEvent $event GetResponseEvent event
     *
     * @return void
     */
    public function onRequest(GetResponseEvent $event)
    {
        $onAdminInterface = strpos($event->getRequest()->getRequestUri(), '/admin');
        if ($onAdminInterface === false) {
            $this->issueService->issueResolver($event->getRequest());
        }
    }
}
