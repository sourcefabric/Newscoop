<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Services;

use Doctrine\ORM\EntityManager;
use Newscoop\Subscription\Subscription;
use Newscoop\Subscription\SubscriptionData;
use Symfony\Component\Yaml\Parser;

/**
 */
class SubscriptionService
{
    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /**
     * Subscriptions config
     * @var array
     */
    private $subscriptionsConfig;

    /**
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
        $subscriptionsConfigFile = __DIR__ . '/../../../application/configs/subscriptions/subscriptions.yml';

        if (file_exists($subscriptionsConfigFile)) {
            $yamlParser = new Parser();
            $this->subscriptionsConfig = $yamlParser->parse(file_get_contents($subscriptionsConfigFile));
        } else {
            $this->subscriptionsConfig = array();
        }
    }

    public function getSubscriptionsConfig() {
        return $this->subscriptionsConfig;
    }

    public function create()
    {
        $subscription = new Subscription();

        return $subscription;
    }

    public function save(Subscription $subscription) {
        $this->em->persist($subscription);
        $this->em->flush();
    }

    public function remove($id)
    {

    }

    /**
     * Remove Subscription by Id
     * @param  integer $id - user subscription id
     * @return void
     */
    public function removeById($id) {
        
        $subscription = $this->em->getRepository('Newscoop\Subscription\Subscription')
            ->findOneBy(array(
                'id' => $id
            ));
            
        if ($subscription) {
            $subscription->setActive(false);
            $this->em->flush();
        }
    }

    public function getOneById($id)
    {

        $subscription = $this->em->getRepository('Newscoop\Subscription\Subscription')->findOneBy(array(
            'id' => $id
        ));

        return $subscription;
    }

    public function getOneByUserAndPublication($userId, $publicationId)
    {
        $subscription = $this->em->getRepository('Newscoop\Subscription\Subscription')->findOneBy(array(
            'user' => $userId,
            'publication' => $publicationId
        ));

        return $subscription;
    }

    /**
     * Update Subscription according to SubscritionData class
     * @param  Subscription     $subscription
     * @param  SubscriptionData $data
     * @return Subscription
     */
    public function update(Subscription $subscription, SubscriptionData $data)
    {
        $subscription = $this->apply($subscription, $data);

        return $subscription;
    }

    private function apply(Subscription $subscription, SubscriptionData $data) 
    {
        if ($data->userId) {
            $user = $this->em->getRepository('Newscoop\Entity\User')->getOneActiveUser($data->userId, false)->getOneOrNullResult();
            if ($user) {
                $subscription->setUser($user);    
            }
        }

        if ($data->publicationId) {
            $publication = $this->em->getRepository('Newscoop\Entity\Publication')->findOneBy(array('id' => $data->publicationId));
            if ($publication) {
                $subscription->setPublication($publication);
            }
        }

        if ($data->toPay) {
            $subscription->setToPay($data->toPay);
        }

        if ($data->currency) {
            $subscription->setCurrency($data->currency);
        }

        if ($data->active) {
            $subscription->setActive($data->active);
        }

        if ($data->type) {
            $subscription->setType($data->type);
        }

        if ($data->sections) {
            $sectionsIds = array();
            foreach ($data->sections as $key => $section) {
                $subscription->addSection($section);
                $sectionsIds[] = $section->getId();
            }

            //Clean conncted sections list
            $subscription->setSections($sectionsIds);
        }

        if ($data->articles) {
            $articlesIds = array();
            foreach ($data->articles as $key => $article) {
                $subscription->addArticle($article);
                $articlesIds[] = $article->getId();
            }

            //Clean conncted sections list
            $subscription->setArticles($articlesIds);
        }

        if ($data->issues) {
            $issuesIds = array();
            foreach ($data->issues as $key => $issue) {
                $subscription->addIssue($issue);
                $issuesIds[] = $issue->getId();
            }

            //Clean conncted sections list
            $subscription->setIssues($issuesIds);
        }
        
        return $subscription;
    }

    public function getArticleRepository(){
        return $this->em->getRepository('Newscoop\Entity\Article');
    }

    public function getSectionRepository(){
        return $this->em->getRepository('Newscoop\Entity\Section');
    }

    public function getLanguageRepository(){
        return $this->em->getRepository('Newscoop\Entity\Language');
    }

    public function getIssueRepository(){
        return $this->em->getRepository('Newscoop\Entity\Issue');
    }
}
