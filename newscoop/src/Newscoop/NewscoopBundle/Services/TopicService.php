<?php

/**
 * @package Newscoop\NewscoopBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Services;

use Newscoop\NewscoopBundle\Entity\Topic;
use Newscoop\Entity\Article;
use Doctrine\ORM\EntityManager;
use Newscoop\Exception\ResourcesConflictException;

/**
 * Topcis service
 */
class TopicService
{
    /** @var Doctrine\ORM\EntityManager */
    protected $em;

    /**
     * @param Doctrine\ORM\EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Adds topic to the article
     *
     * @param Topic   $topic   Topic object
     * @param Article $article Article object
     */
    public function addTopicToArticle(Topic $topic, Article $article)
    {
        $topicRepository = $this->em->getRepository('Newscoop\Entity\Article');
        $result = $topicRepository->addTopicToArticle($topic, $article);

        if (!$result) {
            throw new ResourcesConflictException("Topic already attached to article", 409);
        }
    }

    /**
     * Removes topic from the article
     *
     * @param Topic   $topic   Topic object
     * @param Article $article Article object
     */
    public function removeTopicFromArticle(Topic $topic, Article $article)
    {
        $topicRepository = $this->em->getRepository('Newscoop\Entity\Article');
        $result = $topicRepository->removeTopicFromArticle($topic, $article);

        if (!$result) {
            throw new ResourcesConflictException("Topic already removed from the article", 409);
        }
    }
}
