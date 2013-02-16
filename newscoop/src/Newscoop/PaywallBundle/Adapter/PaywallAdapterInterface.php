<?php
/**
 * @package Newscoop\PaywallBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\PaywallBundle\Adapter;

use Newscoop\Services\SubscriptionService;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;

interface PaywallAdapterInterface
{   
    /**
     * Apply injected services
     * @param SubscriptionService $subscriptionService 
     */
    public function __construct(SubscriptionService $subscriptionService);

    /**
     * Process callback request
     * @return Response 
     */
    public function proccess();

    /**
     * Set request to process
     * @param Request $request
     */
    public function setRequest(Request $request);
}