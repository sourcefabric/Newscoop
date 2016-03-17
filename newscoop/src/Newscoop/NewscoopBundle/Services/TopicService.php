<?php

/**
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\NewscoopBundle\Services;

use Newscoop\NewscoopBundle\Entity\Topic;
use Newscoop\Entity\Article;
use Doctrine\ORM\EntityManager;
use Newscoop\Exception\ResourcesConflictException;
use Doctrine\ORM\Query;
use Newscoop\EventDispatcher\Events\GenericEvent;
use Newscoop\NewscoopBundle\Entity\TopicTranslation;

/**
 * Topcis service.
 */
class TopicService
{
    protected $em;
    protected $dispatcher;

    /**
     * @param EntityManager   $em
     * @param EventDispatcher $dispatcher
     */
    public function __construct(EntityManager $em, $dispatcher)
    {
        $this->em = $em;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Adds topic to the article.
     *
     * @param Topic   $topic   Topic object
     * @param Article $article Article object
     *
     * @return bool
     *
     * @throws ResourcesConflictException
     */
    public function addTopicToArticle(Topic $topic, Article $article)
    {
        $result = $this->attachTopicToArticle($topic, $article);

        if (!$result) {
            throw new ResourcesConflictException('Topic already attached to article', 409);
        }

        return true;
    }

    /**
     * Removes topic from the article.
     *
     * @param Topic   $topic   Topic object
     * @param Article $article Article object
     *
     * @return bool
     *
     * @throws ResourcesConflictException
     */
    public function removeTopicFromArticle(Topic $topic, Article $article)
    {
        $result = $this->detachTopicFromArticle($topic, $article);

        if (!$result) {
            throw new ResourcesConflictException('Topic already removed from the article', 409);
        }

        return true;
    }

    /**
     * Adds topic to the article.
     *
     * @param Topic   $topic   Topic object
     * @param Article $article Article object
     *
     * @return bool
     */
    protected function attachTopicToArticle(Topic $topic, Article $article)
    {
        $result = $article->addTopic($topic);
        if ($result) {
            $this->em->flush();

            $this->dispatcher->dispatch('article-topic.attach', new GenericEvent($this, $this->getLogArray($topic, $article)));
        }

        return $result;
    }

    /**
     * Removes topic from the article.
     *
     * @param Topic   $topic   Topic object
     * @param Article $article Article object
     *
     * @return bool
     */
    protected function detachTopicFromArticle(Topic $topic, Article $article)
    {
        $result = $article->removeTopic($topic);
        if ($result) {
            $this->em->flush();

            $this->dispatcher->dispatch('article-topic.detach', new GenericEvent($this, $this->getLogArray($topic, $article)));
        }

        return $result;
    }

    private function getLogArray(Topic $topic, Article $article)
    {
        return array(
            'title' => $topic->getTitle(),
            'id' => array(
                'Title' => $article->getName(),
                'Number' => $article->getNumber(),
                'IdLanguage' => $article->getLanguageId(),
            ),
            'diff' => array(
                'id' => $topic->getId(),
                'title' => $topic->getTitle(),
            ),
        );
    }

    /**
     * Removes topic from all articles it is attached to.
     *
     * @param string|int $topicId Topic id
     *
     * @return bool
     */
    public function removeTopicFromAllArticles($topicId)
    {
        $updateDateTime = new \DateTime();
        $qb = $this->em->createQueryBuilder();
        $topic = $this->em->getReference('Newscoop\NewscoopBundle\Entity\Topic', $topicId);
        $children = $this->getTopicRepository()->childrenQuery($topic)->getArrayResult();
        $attachedTopics = array();
        foreach ($children as $key => $child) {
            if ($this->isAttached($child['id'])) {
                $attachedTopics[] = $child['id'];
            }
        }

        $attachedTopics[] = $topicId;

        $articles = $this->em->createQueryBuilder()
            ->select('a')
            ->from('Newscoop\Entity\Article', 'a')
            ->innerJoin('Newscoop\Entity\ArticleTopic', 'at')
            ->where('at.topic IN (:topic_ids)')
            ->setParameter('topic_ids', $attachedTopics)
            ->getQuery()
            ->getResult();

        foreach ($articles as $article) {
            $article->setUpdated($updateDateTime);
            $this->em->persist($article);
        }
        $this->em->flush();

        $topicsQuery = $qb->delete('Newscoop\Entity\ArticleTopic', 'at')
            ->where('at.topic IN (?1)')
            ->setParameter(1, $attachedTopics)
            ->getQuery();

        $topicsQuery->execute();

        return true;
    }

    /**
     * Saves topic position when it was dragged and dropped.
     *
     * @param Topic $node   Dragged topic object
     * @param array $params Parameters with positions
     *
     * @return Topic
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

        $metadata = $this->em->getClassMetaData(get_class($node));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $this->em->flush();

        return $node;
    }

    /**
     * Reorder root topics.
     *
     * @param array $rootNodes Root topics
     * @param array $order     Topics ids in order
     *
     * @return bool
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
                        ++$counter;
                    }
                }
            }
        } else {
            $counter = 1;
            foreach ($rootNodes as $rootNode) {
                $rootNode->setOrder($counter);
                ++$counter;
            }
        }

        $this->em->flush();

        return true;
    }

    /**
     * Saves new topic. Possibility to overwrite AUTO strategy (set custom ids).
     *
     * @param Topic       $node   Topic object
     * @param string|null $locale Language code
     *
     * @return bool
     *
     * @throws ResourcesConflictException When Topic already exists
     */
    public function saveNewTopic(Topic $node, $locale = null)
    {
        $node->setTranslatableLocale($locale ?: $node->getTranslatableLocale());
        $topicTranslation = $this->getTopicRepository()->createQueryBuilder('t')
            ->join('t.translations', 'tt')
            ->where('tt.locale = :locale')
            ->andWhere('tt.content = :title')
            ->andWhere("tt.field = 'title'")
            ->setParameters(array(
                'title' => $node->getTitle(),
                'locale' => $node->getTranslatableLocale(),
            ))
            ->getQuery()
            ->getOneOrNullResult();

        if ($topicTranslation) {
            throw new ResourcesConflictException('Topic already exists', 409);
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

        $node->addTranslation(new TopicTranslation($locale ?: $node->getTranslatableLocale(), 'title', $node->getTitle(), true));

        $this->em->persist($node);
        $metadata = $this->em->getClassMetaData(get_class($node));
        $metadata->setIdGeneratorType(\Doctrine\ORM\Mapping\ClassMetadata::GENERATOR_TYPE_NONE);
        $this->em->flush();

        $this->dispatcher->dispatch('topic.create', new GenericEvent($this, array(
            'title' => $node->getTitle(),
            'id' => array('id' => $node->getId()),
            'diff' => (array) $node,
        )));

        return true;
    }

    /**
     * Checks if topic is attached to any article.
     *
     * If $attachedCount is set to yes, returns an array with the number of topics attached to articles,
     * else returns boolean. By default set to false.
     *
     * @param string|int $topicId       Topic id
     * @param bool       $attachedCount Switch to include/exclude number of topics
     *
     * @return bool|array
     */
    public function isAttached($topicId, $attachedCount = false)
    {
        $topic = $this->em->getRepository('Newscoop\Entity\ArticleTopic')
            ->getTheOccurrenceOfTheTopic($topicId)
            ->getSingleScalarResult();

        $count = (int) $topic;
        if ($attachedCount) {
            if ($count > 0) {
                return array($count, true);
            }

            return array($count, false);
        }

        if ($count > 0) {
            return true;
        }

        return false;
    }

    /**
     * Returns a topic object identified by the full name in the
     * format topic_name:language_code.
     *
     * @param string $fullName Topic's full name
     *
     * @return Topic|null object
     */
    public function getTopicByFullName($fullName)
    {
        $extractedData = $this->extractNameAndLanguage($fullName);
        if (empty($extractedData)) {
            return;
        }

        $name = $extractedData['name'];
        $languageCode = $extractedData['locale'];

        $topicTranslation = $this->em->getRepository('Newscoop\NewscoopBundle\Entity\TopicTranslation')->findOneBy(array(
            'content' => $name,
            'locale' => $languageCode,
            'field' => 'title',
        ));

        if (!$topicTranslation) {
            return;
        }

        return $topicTranslation->getObject();
    }

    /**
     * Returns a topic as an array identified by the full name in the
     * format topic_name:language_code.
     *
     * @param string $fullName Topic's full name
     *
     * @return array
     */
    public function getTopicByFullNameAsArray($fullName)
    {
        $extractedData = $this->extractNameAndLanguage($fullName);
        if (empty($extractedData)) {
            return;
        }

        $name = $extractedData['name'];
        $languageCode = $extractedData['locale'];

        $topicTranslation = $this->em->getRepository('Newscoop\NewscoopBundle\Entity\Topic')
            ->getOneByExtractedFullName($name, $languageCode)
            ->getArrayResult();

        if (empty($topicTranslation)) {
            return;
        }

        $topicTranslation[0]['object']['title'] = $topicTranslation[0]['title'];

        return $topicTranslation[0]['object'];
    }

    private function extractNameAndLanguage($fullName)
    {
        $fullName = trim($fullName);
        $lastColon = strrpos($fullName, ':');
        if (!$lastColon) {
            return;
        }

        $name = substr($fullName, 0, $lastColon);
        $languageCode = substr($fullName, $lastColon + 1);

        return array(
            'name' => $name,
            'locale' => $languageCode,
        );
    }

    /**
     * Gets the topic by id, its title or title
     * combined with the language and language code.
     * $string parameter value can be: "test", 20, "test:en".
     *
     * @param string      $string Topic search phrase
     * @param string|null $locale Locale
     *
     * @return Topic|null
     */
    public function getTopicBy($string, $locale = null)
    {
        $topic = $this->getTopicRepository()
            ->getTopicByIdOrName($string, $locale)
            ->getOneOrNullResult();

        if (!$topic) {
            $topic = $this->getTopicByFullName($string);
            if (!$topic) {
                return;
            }
        }

        return $topic;
    }

    /**
     * Wrapper method for setting translatable hint.
     * When for instance getting topic with German name
     * $locale should be set to "de".
     *
     * @param Query  $query  Query object
     * @param string $locale Locale
     *
     * @return Query
     */
    public function setTranslatableHint(Query $query, $locale = null)
    {
        return $this->getTopicRepository()->setTranslatableHint($query, $locale);
    }

    /**
     * Wrapper method for getting readable topic path.
     *
     * @param Topic       $topic  Topic object
     * @param string|null $locale Locale e.g. "en"
     *
     * @return string Topic's readable path
     */
    public function getReadablePath(Topic $topic, $locale = null)
    {
        return $this->getTopicRepository()->getReadablePath($topic, $locale);
    }

    /**
     * Count topics by given criteria.
     *
     * @param array $criteria
     *
     * @return int
     */
    public function countBy(array $criteria = array())
    {
        return $this->getTopicRepository()->countBy($criteria);
    }

    /**
     * Count article topics by given criteria.
     *
     * @param array $criteria
     *
     * @return int
     */
    public function countArticleTopicsBy(array $criteria = array())
    {
        return $this->getArticleTopicRepository()->countBy($criteria);
    }

    /**
     * Check if topic name already exists by given locale.
     *
     * @param string $locale Locale
     * @param string $title  Topic name
     *
     * @return bool
     */
    public function checkTopicName($locale, $title)
    {
        $topicTranslation = $this->getTopicRepository()->createQueryBuilder('t')
            ->select('count(t)')
            ->join('t.translations', 'tt')
            ->where('tt.locale = :locale')
            ->andWhere('tt.content = :title')
            ->andWhere("tt.field = 'title'")
            ->setParameters(array(
                'title' => $title,
                'locale' => $locale,
            ))
            ->getQuery()
            ->getSingleScalarResult();

        if ((int) $topicTranslation > 0) {
            return true;
        }

        return false;
    }

    /**
     * Deletes the topic. If topic is attached to any article
     * it is first detached and deleted.
     *
     * @param Topic $topic Topic
     *
     * @return bool
     */
    public function deleteTopic(Topic $topic)
    {
        if ($this->isAttached($topic->getId())) {
            $this->removeTopicFromAllArticles($topic->getId());
        }

        $this->removeTopicFromAllUsers($topic->getId());
        $this->em->remove($topic);
        $this->em->flush();
        $this->em->clear();

        return true;
    }

    /**
     * Removes topic from all users it is followed by.
     *
     * @param string|int $topicId Topic id
     *
     * @return bool
     */
    public function removeTopicFromAllUsers($topicId)
    {
        $qb = $this->em->createQueryBuilder();
        $topicsQuery = $qb->delete('Newscoop\Entity\UserTopic', 'ut')
            ->where('ut.topic = :topicId')
            ->setParameter('topicId', $topicId)
            ->getQuery();

        $topicsQuery->execute();

        return true;
    }

    /**
     * Checks if topic is attached to any article.
     *
     * If $attachedCount is set to yes, returns an array with the number of topics attached to articles,
     * else returns boolean. By default set to false.
     *
     * @param string|int $topicId       Topic id
     * @param bool       $attachedCount Switch to include/exclude number of topics
     *
     * @return bool|array
     */
    public function isFollowed($topicId)
    {
        $topic = $this->em->getRepository('Newscoop\Entity\UserTopic')
            ->getTheOccurrenceOfTheUserTopic($topicId)
            ->getSingleScalarResult();

        $count = (int) $topic;
        if ($count > 0) {
            return true;
        }

        return false;
    }

    /**
     * Get options for forms.
     *
     * @return array
     */
    public function getMultiOptions()
    {
        return $this->getTopicRepository()->findOptions();
    }

    /**
     * Gets Topic Repository.
     *
     * @return Newscoop\NewscoopBundle\Entity\Repository\TopicRepository
     */
    protected function getTopicRepository()
    {
        return $this->em->getRepository('Newscoop\NewscoopBundle\Entity\Topic');
    }

    /**
     * Gets article topic Repository.
     *
     * @return Newscoop\Entity\Repository\ArticleTopicRepository
     */
    protected function getArticleTopicRepository()
    {
        return $this->em->getRepository('Newscoop\Entity\ArticleTopic');
    }
}
