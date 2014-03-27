<?php
/**
 * @package Newscoop
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\Topic;

use Doctrine\ORM\EntityManager;
use Newscoop\Entity\ArticleTopic;
use Newscoop\Entity\TopicNodes;
use Newscoop\Entity\Topic;

/**
 * Topic Service
 */
class TopicService
{
    /**
     * @var EntityManager
     */
    protected $em;

    /**
     * @param EntityManager $em
     */
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Create a new topic.
     *
     * The values array may have the following keys:
     * - parent_id - parent topic identifier
     * - names - array of topic translations of the form: language_id => name
     *
     * @param array $values
     * @param bool  $allNamesRequired
     *
     * @return boolean
     */
    public function create($values = null, $allNamesRequired = false)
    {
        try {
            if ((!isset($values['names'])) || (!is_array($values['names']))) {
                return false;
            }

            $namesAvailable = array();
            $namesOccupied = array();
            foreach ($values['names'] as $checkLangId => $checkTopicName) {
                $topicObj = $this->getTopic($checkTopicName, $checkLangId);

                if ($topicObj) {
                    $namesOccupied[$checkLangId] = $checkTopicName;
                } else {
                    $namesAvailable[$checkLangId] = $checkTopicName;
                }
                unset($topicObj);
            }

            if (empty($namesAvailable)) {
                return false;
            }

            if ($allNamesRequired) {
                if (!empty($namesOccupied)) {
                    return false;
                }
            }

            $values['names'] = $namesAvailable;
            $parentLeft = 0;
            if (isset($values['parent_id']) && !empty($values['parent_id'])) {
                $parent = new \Topic($values['parent_id']);
                if (!$parent->exists()) {
                    return false;
                }

                $parentLeft = (int) $parent->getLeft();
            }

            $this->updateTopicNodes($parentLeft);
            $lastTopic = $this->insertNodes($parentLeft);
            $someNameSet = false;
            $someNameNotSet = false;
            $success = false;
            if (is_numeric($lastTopic->getId())) {
                $success = true;
            }

            if ($success) {
                foreach ($values['names'] as $languageId => $name) {
                    try {
                        $language = $this->em->getRepository('Newscoop\Entity\Language')->findOneBy(array(
                            'id' => $languageId
                        ));

                        $newTopic = new Topic();
                        $newTopic->setTopicId($lastTopic->getId());
                        $newTopic->setLanguage($language);
                        $newTopic->setName($name);
                        $this->em->persist($newTopic);
                        $someNameSet = true;
                    } catch (\Exception $exception) {
                        $someNameNotSet = true;
                    }
                }

                $this->em->flush();

                if ($allNamesRequired && $someNameNotSet) {
                    $this->deleteTopicNames($lastTopicId);
                    $success = false;
                }

                if (!$someNameSet) {
                    $success = false;
                }

                if (!$success) {
                    $this->deleteTopicById($lastTopicId);
                }
            }

            if (!$success) {
               $this->deleteTopicNodes($parentLeft);
            }

            return $success;
        } catch (\Exception $e) {
            throw $e;
        }
    }

    /**
     * Link a topic to an article.
     *
     * @param int $topicId   Topic id
     * @param int $articleId Article id
     *
     * @return void
     */
    public function addTopicToArticle($topicId, $articleId)
    {
        try {
            $articleTopic = new ArticleTopic();
            $topicObj = $this->em->getRepository('Newscoop\Entity\Topic')
                ->findOneBy(array(
                    'id' => $topicId,
            ));

            $articleObj = $this->em->getRepository('Newscoop\Entity\Article')
                ->findOneBy(array(
                    'number' => $articleId,
            ));

            $qb = $this->em->getRepository('Newscoop\Entity\ArticleTopic')
                ->createQueryBuilder('at');

            $currentTopic = $qb
                ->select('count(at)')
                ->leftJoin('at.topic', 't')
                ->leftJoin('at.article', 'a')
                ->where('t.id = :topicId')
                ->andWhere('a.number = :articleId')
                ->setParameters(array(
                    'topicId' => $topicId,
                    'articleId' => $articleId
                ))
                ->getQuery()
                ->getSingleScalarResult();

            if ((int) $currentTopic == 0) {
                $articleTopic->setTopic($topicObj);
                $articleTopic->setArticle($articleObj);
                $this->em->persist($articleTopic);
                $this->em->flush();
            }
        } catch(\Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }

    /**
     * Get topic by id or name
     *
     * @param int|string|null $idOrName         Topic Id or Name
     * @param int|null        $languageSelected Topic language
     *
     * @return object
     */
    public function getTopicByIdOrName($idOrName = null, $languageSelected = null)
    {
        if (preg_match('/^[\d]+$/', $idOrName) > 0) {
            $topic = $this->em->getRepository('Newscoop\Entity\Topic')
                ->findOneBy(array(
                    'id' => $idOrName,
            ));
        } elseif (is_string($idOrName) && !empty($idOrName)) {
            $topic = $this->em->getRepository('Newscoop\Entity\Topic')
                ->findOneBy(array(
                    'name' => $idOrName,
                    'language' => $languageSelected
            ));
        }

        return $topic;
    }

    /**
     * Get topic
     *
     * @param string $checkTopicName Topic Name
     * @param int    $checkLangId    Topic language
     *
     * @return object
     */
    public function getTopic($checkTopicName, $checkLangId)
    {
        $topicObj = null;
        if (is_numeric($checkTopicName) && $checkLangId > 0) {
            $topicObj = $this->em->getRepository('Newscoop\Entity\Topic')->findOneBy(array(
                'id' => $checkTopicName,
                'language' =>  $checkLangId
            ));
        } elseif (!empty($checkTopicName) && $checkLangId > 0) {
            $topicObj = $this->em->getRepository('Newscoop\Entity\Topic')->findOneBy(array(
                'name' => $checkTopicName,
                'language' =>  $checkLangId
            ));
        }

        return $topicObj;
    }

    /**
     * Insert Topic nodes
     *
     * @param int $parentLeft Parent topic
     *
     * @return TopicNodes
     */
    public function insertNodes($parentLeft)
    {
        try {
            $newTopicNodes = new TopicNodes();
            $newTopicNodes->setLeftNode($parentLeft + 1);
            $newTopicNodes->setRightNode($parentLeft + 2);
            $this->em->persist($newTopicNodes);
            $this->em->flush();

            return $newTopicNodes;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete topic name by given topic id
     *
     * @param int $topicId Topic id
     *
     * @return void
     */
    public function deleteTopicNames($topicId)
    {
        $topic = $this->em->getRepository('Newscoop\Entity\Topic')->findOneBy(array(
            'id' => $topicId,
        ));

        if ($topic) {
            $this->em->remove($topic);
            $this->em->flush();
        }
    }

    /**
     * Update topic nodes by given topic id
     *
     * @param int $parentLeft Parent topic
     *
     * @return void
     */
    public function updateTopicNodes($parentLeft)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $nodes = $queryBuilder->update('Newscoop\Entity\TopicNodes', 'tn')
            ->set('tn.leftNode', 'tn.leftNode + 2')
            ->set('tn.rightNode', 'tn.rightNode + 2')
            ->where('tn.leftNode > ?1')
            ->where('tn.rightNode > ?1')
            ->setParameter(1, $parentLeft)
            ->getQuery();

        $nodes->execute();
    }

    /**
     * Delete topic nodes by parent topic
     *
     * @param int $parentLeft Parent Topic
     *
     * @return void
     */
    public function deleteTopicNodes($parentLeft)
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $nodes = $queryBuilder->update('Newscoop\Entity\TopicNodes', 'tn')
            ->set('tn.leftNode', 'tn.leftNode - 2')
            ->set('tn.rightNode', 'tn.rightNode - 2')
            ->where('tn.leftNode > ?1')
            ->setParameter(1, $parentLeft + 2)
            ->getQuery();
        $nodes->execute();
    }

    /**
     * Delete topic nodes by given topic id
     *
     * @param int $topicId Topic id
     *
     * @return void
     */
    public function deleteTopicById($topicId)
    {
        $topic = $this->em->getRepository('Newscoop\Entity\TopicNodes')->findOneBy(array(
            'id' => $topicId,
        ));

        $this->em->remove($topic);
        $this->em->flush();
    }

    /**
     * Get options for forms
     *
     * @return array
     */
    public function getMultiOptions()
    {
        return $this->em->getRepository('Newscoop\Entity\Topic')->findOptions();
    }
}
