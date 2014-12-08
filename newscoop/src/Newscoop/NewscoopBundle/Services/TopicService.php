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
     *
     * @return boolean
     * @throws ResourcesConflictException
     */
    public function addTopicToArticle(Topic $topic, Article $article)
    {
        $topicRepository = $this->em->getRepository('Newscoop\Entity\Article');
        $result = $this->attachTopicToArticle($topic, $article);

        if (!$result) {
            throw new ResourcesConflictException("Topic already attached to article", 409);
        }

        return true;
    }

    /**
     * Removes topic from the article
     *
     * @param Topic   $topic   Topic object
     * @param Article $article Article object
     *
     * @return boolean
     * @throws ResourcesConflictException
     */
    public function removeTopicFromArticle(Topic $topic, Article $article)
    {
        $topicRepository = $this->em->getRepository('Newscoop\Entity\Article');
        $result = $this->detachTopicFromArticle($topic, $article);

        if (!$result) {
            throw new ResourcesConflictException("Topic already removed from the article", 409);
        }

        return true;
    }

    /**
     * Adds topic to the article
     *
     * @param Topic   $topic   Topic object
     * @param Article $article Article object
     *
     * @return boolean
     */
    protected function attachTopicToArticle(Topic $topic, Article $article)
    {
        $result = $article->addTopic($topic);
        if ($result) {
            $this->em->flush();
        }

        return $result;
    }

    /**
     * Removes topic from the article
     *
     * @param Topic   $topic   Topic object
     * @param Article $article Article object
     *
     * @return boolean
     */
    protected function detachTopicFromArticle(Topic $topic, Article $article)
    {
        $result = $article->removeTopic($topic);
        if ($result) {
            $this->em->flush();
        }

        return $result;
    }

    /**
     * Saves topic position when it was dragged and dropped
     *
     * @param Topic   $node     Dragged topic object
     * @param int     $parentId Parent of dragged topic
     * @param boolean $asRoot   If topic is dragged from children to root level
     * @param array   $params   Parameters with positions
     *
     * @return boolean
     */
    public function saveTopicPosition(Topic $node, $params)
    {
        if (isset($params['parent']) && $params['parent']) {
            $repository = $this->getTopicRepository();
            $parent = $repository->findOneBy(array(
                'id' => $params['parent'],
            ));

            if (!$parent) {
                return false;
            }

            $node->setOrder(null);
            foreach ($params as $key => $isSet) {
                switch ($key) {
                    case 'first':
                        if ($isSet) {
                            $repository->persistAsFirstChildOf($node, $parent);
                        }
                        break;
                    case 'last':
                        if ($isSet) {
                            $repository->persistAsLastChildOf($node, $parent);
                        }
                        break;
                    case 'middle':
                        if ($isSet) {
                            $repository->persistAsNextSiblingOf($node, $parent);
                        }
                        break;
                    default:
                        break;
                }
            }
        }

        // when dragging children to roots
        if (isset($params['asRoot']) && $params['asRoot']) {
            $node->setParent(null);
        }

        $this->em->flush();

        return true;
    }

    /**
     * Reorder root topics
     *
     * @param array $rootNodes Root topics
     * @param array $order     Topics ids in order
     *
     * @return boolean
     */
    public function reorderRootNodes($rootNodes, $order = array())
    {
        foreach ($rootNodes as $rootNode) {
            $rootNode->setOrder(null);
        }

        $this->em->flush();

        if (count($order) > 1) {
            $counter = 0;

            foreach ($order as $item) {
                foreach ($rootNodes as $rootNode) {
                    if ($rootNode->getId() == $item) {
                        $rootNode->setOrder($counter + 1);
                        $counter++;
                    }
                }
            }
        } else {
            $counter = 1;
            foreach ($rootNodes as $rootNode) {
                $rootNode->setOrder($counter);
                $counter++;
            }
        }

        $this->em->flush();

        return true;
    }

    /**
     * Saves new topic
     *
     * @param Topic       $node   Topic object
     * @param string|null $locale Language code
     *
     * @return boolean
     * @throws ResourcesConflictException When Topic already exists
     */
    public function saveNewTopic(Topic $node, $locale = null)
    {
        $node->setTranslatableLocale($locale ?: $node->getTranslatableLocale());
        $topic = $this->getTopicRepository()->findOneByTitle($node->getTitle());

        $topicTranslation = $this->getTopicRepository()->createQueryBuilder('t')
            ->join('t.translations', 'tt')
            ->where('tt.locale = :locale')
            ->andWhere('tt.content = :title')
            ->andWhere("tt.field = 'title'")
            ->setParameters(array(
                'title' => $node->getTitle(),
                'locale' => $node->getTranslatableLocale()
            ))
            ->getQuery()
            ->getOneOrNullResult();

        if ($topic || $topicTranslation) {
             throw new ResourcesConflictException("Topic already exists", 409);
        }

        if (!$node->getParent()) {
            $qb = $this->getTopicRepository()->createQueryBuilder('t');
            $maxOrderValue = $qb
                ->select('MAX(t.topicOrder)')
                ->setMaxResults(1)
                ->getQuery()
                ->getSingleScalarResult();

            $node->setOrder((int) $maxOrderValue + 1);
        }

        $this->em->persist($node);
        $this->em->flush();

        return true;
    }

    /**
     * Gets Topic Repository
     *
     * @return Newscoop\NewscoopBundle\Entity\Repository\TopicRepository
     */
    protected function getTopicRepository()
    {
        return $this->em->getRepository('Newscoop\NewscoopBundle\Entity\Topic');
    }
}
