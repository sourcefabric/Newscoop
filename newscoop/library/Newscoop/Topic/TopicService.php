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
    private $em;

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
     * - id - topic identifier; if not supplied generated automatically
     * - node_left
     * - node_right
     * - names - array of topic translations of the form: language_id => name
     *
     * @param  array   $values
     * @return boolean
     */
    public function create($values = null, $allNamesRequired = false)
    {
        if ((!isset($values['names'])) || (!is_array($values['names']))) {
            return false;
        }

        $namesAvailable = array();
        $namesOccupied = array();
        foreach ($values['names'] as $checkLangId => $checkTopicName) {
            $checkNameObj = $this->getTopic($checkTopicName, $checkLangId);

            if ($checkNameObj) {
                $namesOccupied[$checkLangId] = $checkTopicName;
            }
            else {
                $namesAvailable[$checkLangId] = $checkTopicName;
            }
            unset($checkNameObj);
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
        $this->updateTopicNodes($parentLeft);
        $lastTopicId = $this->insertNodes($parentLeft);
        $someNameSet = false;
        $someNameNotSet = false;
        $success = false;
        if (is_numeric($lastTopicId)) {
            $success = true; 
        }

        if ($success) {
            foreach ($values['names'] as $languageId => $name) {
                try {
                    $language = $this->em->getRepository('Newscoop\Entity\Language')->findOneBy(array(
                        'id' => $languageId
                    ));
                    $newTopic = new Topic($lastTopicId, $language, $name);
                    $this->em->persist($newTopic);
                    $this->em->flush();
                    $someNameSet = true;
                } catch (\Exception $exception) {
                    $someNameNotSet = true;
                }
            }

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
    }

    /**
     * Link a topic to an article.
     *
     * @param  int $topicId   Topic id
     * @param  int $articleId Article id
     *
     * @return void
     */
    public function addTopicToArticle($topicId, $articleId)
    {
        try
        {   
            $articleTopic = new ArticleTopic();
            $topicObj = $this->em->getRepository('Newscoop\Entity\Topic')
                ->findOneBy(array(
                    'id' => $topicId,
            ));

            $articleObj = $this->em->getRepository('Newscoop\Entity\Article')
                ->findOneBy(array(
                    'number' => $articleId,
            ));
            
            $articleTopic->setTopic($topicObj);
            $articleTopic->setArticle($articleObj);
            $this->em->persist($articleTopic);
            $this->em->flush();
        } catch(\Exception $e) {
            throw new \Exception('Fatal error occured. Try again!');
        }
    }

    /**
     * Get topic by id or name
     *
     * @param  int|string|null $p_idOrName          Topic Id or Name
     * @param  int|null        $f_language_selected Topic language
     *
     * @return object
     */
    public function getTopicByIdOrName($p_idOrName = null, $f_language_selected = null)
    {
        if (preg_match('/^[\d]+$/', $p_idOrName) > 0) {
            $topic = $this->em->getRepository('Newscoop\Entity\Topic')
                ->findOneBy(array(
                    'id' => $p_idOrName,
            ));
        } elseif (is_string($p_idOrName) && !empty($p_idOrName)) {
            $topic = $this->em->getRepository('Newscoop\Entity\Topic')
                ->findOneBy(array(
                    'name' => $p_idOrName,
                    'language' => $f_language_selected
            ));
        }
            
        return $topic;
    }

    /**
     * Get topic
     *
     * @param  $p_idOrName    Topic Id or Name
     * @param  $checkLangId Topic language
     *
     * @return object
     */
    public function getTopic($checkTopicName, $checkLangId) 
    {
        $checkNameObj = null;
        if (is_numeric($checkTopicName) && $checkLangId > 0) {
            $checkNameObj = $this->em->getRepository('Newscoop\Entity\Topic')->findOneBy(array(
                'id' => $checkTopicName,
                'language' =>  $checkLangId
            ));
        } elseif (!empty($checkTopicName) && $checkLangId > 0) {
            $checkNameObj = $this->em->getRepository('Newscoop\Entity\Topic')->findOneBy(array(
                'name' => $checkTopicName,
                'language' =>  $checkLangId
            ));
        }

        return $checkNameObj;
    }

    /**
     * Insert Topic nodes
     *
     * @param  int      $parentLeft Parent topic
     *
     * @return int|bool
     */
    public function insertNodes($parentLeft) 
    {
        try {
            $newTopicNodes = new TopicNodes();
            $newTopicNodes->setLeftNode($parentLeft + 1);
            $newTopicNodes->setRightNode($parentLeft + 2);
            $this->em->persist($newTopicNodes);
            $this->em->flush();

            return $newTopicNodes->getId();
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete topic name by given topic id
     *
     * @param  int  $topicId Topic id
     *
     * @return void
     */
    public function deleteTopicNames($topicId)
    {
        $topic = $this->em->getRepository('Newscoop\Entity\Topic')->findOneBy(array(
            'id' => $topicId,
        ));
        $this->em->remove($topic);
        $this->em->flush();
    }

    /**
     * Update topic nodes by given topic id
     *
     * @param  int  $parentLeft Parent topic
     *
     * @return void
     */
    public function updateTopicNodes($parentLeft) 
    {
        $queryBuilder = $this->em->createQueryBuilder();
        $leftNodes = $queryBuilder->update('Newscoop\Entity\TopicNodes', 'tn')
            ->set('tn.leftNode', 'tn.leftNode + 2')
            ->where('tn.leftNode > ?1')
            ->setParameter(1, $parentLeft)
            ->getQuery();
        $leftNodes->execute();

        $rightNodes = $queryBuilder->update('Newscoop\Entity\TopicNodes', 'tn')
            ->set('tn.rightNode', 'tn.rightNode + 2')
            ->where('tn.rightNode > ?1')
            ->setParameter(1, $parentLeft)
            ->getQuery();
        $rightNodes->execute();
    }

    /**
     * Delete topic nodes by parent topic
     *
     * @param  int  $parentLeft Parent Topic
     *
     * @return void
     */
    public function deleteTopicNodes($parentLeft)
    {   
        $queryBuilder = $this->em->createQueryBuilder();
        $leftNodes = $queryBuilder->update('Newscoop\Entity\TopicNodes', 'tn')
            ->set('tn.leftNode', 'tn.leftNode - 2')
            ->where('tn.leftNode > ?1')
            ->setParameter(1, $parentLeft + 2)
            ->getQuery();
        $leftNodes->execute();

        $rightNodes = $queryBuilder->update('Newscoop\Entity\TopicNodes', 'tn')
            ->set('tn.rightNode', 'tn.rightNode - 2')
            ->where('tn.rightNode > ?1')
            ->setParameter(1, $parentLeft + 2)
            ->getQuery();
        $rightNodes->execute();
    }

    /**
     * Delete topic nodes by given topic id
     *
     * @param  int  $topicId Topic id
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