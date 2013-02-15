<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\PaywallBundle\Adapter;

use Newscoop\Services\SubscriptionsService;
use Symfony\Component\HttpFoundation\Request;

class PayGeAdapter
{   
    private $subscriptionService;

    private $request;

    public function __construct(Request $request, SubscriptionsService $subscriptionService) {
        $this->request = $request;
        $this->subscriptionService = $subscriptionService;
    }

    public function proccess() {
        // do all what you need with request and subscription service
    }
}