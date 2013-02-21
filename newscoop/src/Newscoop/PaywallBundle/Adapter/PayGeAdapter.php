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
        $subscription = $this->subscriptionService->create();

        $subscriptionData = new \Newscoop\Subscription\SubscriptionData(array(
            'userId' => 1,
            'publicationId' => 2,
            'toPay' => 30,
            'days' => 30,
            'currency' => 'PLN'
        ), $subscription);

        $article = $this->subscriptionService->getArticleRepository()->findOneByNumber(64);
        $section = $this->subscriptionService->getSectionRepository()->findOneByNumber(10);
        $issue = $this->subscriptionService->getIssueRepository()->findOneByNumber(13);
        $language = $this->subscriptionService->getLanguageRepository()->findOneById(1);
        
        $subscriptionData->addArticle($article, $language);
        $subscriptionData->addSection($section, $language);
        $subscriptionData->addIssue($issue, $language);

        $subscription = $this->subscriptionService->update($subscription, $subscriptionData);
        $this->subscriptionService->save($subscription);

        return new Response('OK');
    }
}