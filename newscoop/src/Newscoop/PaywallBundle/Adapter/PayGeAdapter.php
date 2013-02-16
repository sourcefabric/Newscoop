<?php
/**
 * @package Newscoop\PaywallBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\PaywallBundle\Adapter;

use Newscoop\Services\SubscriptionService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Newscoop\PaywallBundle\Adapter\PaywallAdapterInterface;

class PayGeAdapter implements PaywallAdapterInterface
{   
    private $subscriptionService;

    private $request;

    public function setRequest(Request $request) {
        $this->request = $request;
    }

    public function __construct(SubscriptionService $subscriptionService) {
        $this->subscriptionService = $subscriptionService;
    }

    public function proccess() {
        // do all what you need with request and subscription service
        $subscriptionData = new \Newscoop\Subscription\SubscriptionData(array(
            'userId' => 1,
            'publicationId' => 2
        ));

        $this->subscriptionService->create($subscriptionData);

        return new Response('OK');
    }
}