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
     * @param array $p_values
     * @return boolean
     */
    public function create($p_values = null, $p_allNamesRequired = false)
    {
        if ((!isset($p_values['names'])) || (!is_array($p_values['names']))) {
            return false;
        }

        $names_available = array();
        $names_occupied = array();
        foreach ($p_values['names'] as $check_lang_id => $check_topic_name) {
            $check_name_obj = $this->getTopic($check_topic_name, $check_lang_id, $this->em);

            if ($check_name_obj) {
                $names_occupied[$check_lang_id] = $check_topic_name;
            }
            else {
                $names_available[$check_lang_id] = $check_topic_name;
            }
            unset($check_name_obj);
        }

        if (empty($names_available)) {
            return false;
        }

        if ($p_allNamesRequired) {
            if (!empty($names_occupied)) {
                return false;
            }
        }

        $p_values['names'] = $names_available;
        $parentLeft = 0;
        $this->updateTopicNodes($parentLeft);
        $lastTopicId = $this->insertNodes($parentLeft);
        $some_name_set = false;
        $some_name_not_set = false;
        $success = false;
        if (is_numeric($lastTopicId)) {
            $success = true; 
        }

        if ($success) {
            foreach ($p_values['names'] as $languageId => $name) {
                try {
                    $language = $this->em->getRepository('Newscoop\Entity\Language')->findOneBy(array(
                        'id' => $languageId
                    ));
                    $newTopic = new Topic($lastTopicId, $language, $name);
                    $this->em->persist($newTopic);
                    $this->em->flush();
                    $some_name_set = true;
                } catch (\Exception $exception) {
                    $some_name_not_set = true;
                }
            }

            if ($p_allNamesRequired && $some_name_not_set) {
                $this->deleteTopicNames($lastTopicId);
                $success = false;
            }

            if (!$some_name_set) {
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
     * @param  Newscoop\Entity\Topic   $topic   Topic
     * @param  Newscoop\Entity\Article $article Article
     *
     * @return void
     */
    public function AddTopicToArticle($topic, $article)
    {
        try
        {   
            $articleTopic = new ArticleTopic();
            $topicObj = $this->em->getRepository('Newscoop\Entity\Topic')
                ->findOneBy(array(
                    'id' => $topic,
            ));

            $articleObj = $this->em->getRepository('Newscoop\Entity\Article')
                ->findOneBy(array(
                    'number' => $article,
            ));
            
            $articleTopic->setTopic($topicObj);
            $articleTopic->setArticle($articleObj);
            $this->em->persist($articleTopic);
            $this->em->flush();
        }
        catch(\Exception $e)
        {
            throw new \Exception('Fatal error occured. Try again!');
        }
    }

    /**
     * Get topic by id or name
     *
     * @param  int|null $p_idOrName          Topic Id or Name
     * @param  int|string|null $f_language_selected Topic language
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
     * @param $p_idOrName    Topic Id or Name
     * @param $check_lang_id Topic language
     *
     * @return object
     */
    public function getTopic($check_topic_name, $check_lang_id) 
    {
        $check_name_obj = null;
        if (is_numeric($check_topic_name) && $check_lang_id > 0) {
            $check_name_obj = $this->em->getRepository('Newscoop\Entity\Topic')->findOneBy(array(
                'id' => $check_topic_name,
                'language' =>  $check_lang_id
            ));
        } elseif (!empty($check_topic_name) && $check_lang_id > 0) {
            $check_name_obj = $this->em->getRepository('Newscoop\Entity\Topic')->findOneBy(array(
                'name' => $check_topic_name,
                'language' =>  $check_lang_id
            ));
        }

        return $check_name_obj;
    }

    /**
     * Insert Topic nodes
     *
     * @param int       $parentLeft Parent topic
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
     * @param int   $topicId Topic id
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
     * @param int   $parentLeft Parent topic
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
     * @param int   $parentLeft Parent Topic
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
     * @param int   $topicId Topic id
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