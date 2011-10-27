<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\EntityRepository,
    Newscoop\Entity\ArticlePopularity;

/**
 * ArticlePopularity repository
 */
class ArticlePopularityRepository extends EntityRepository
{
    /** @var array */
    private $setters = array(
        'article_id' => 'setArticleId',
        'language_id' => 'setLanguageId',
        'url' => 'setURL',
        'date' => 'setDate',
        'unique_views' => 'setUniqueViews',
        'avg_time_on_page' => 'setAvgTimeOnPage',
        'tweets' => 'setTweets',
        'likes' => 'setLikes',
        'comments' => 'setComments',
        'popularity' => 'setPopularity',
    );


    /**
     * Save
     *
     * @param Newscoop\Entity\ArticlePopularity $entry
     * @param array $values
     * @return void
     */
    public function save(ArticlePopularity $entity, array $values)
    {
        $this->setProperties($entity, $values);
        if ($this->exists($entity)) {
            unset($values['article_id']);
            unset($values['language_id']);
            if ($entity->getURL() == $values['url']) {
                unset($values['url']);
            }
        }

        $entity->setDate(new \DateTime);

        $this->getEntityManager()->persist($entity);
    }

    /**
     * Delete
     *
     * @param Newscoop\Entity\ArticlePopularity $section
     * @return void
     */
    public function delete(ArticlePopularity $entry)
    {
        $em = $this->getEntityManager();
        $em->remove($entry);
    }

    /**
     * Set properties
     *
     * @param Newscoop\Entity\ArticlePopularity $entity
     * @param array $values
     * @return void
     */
    private function setProperties(ArticlePopularity $entity, array $values)
    {
        foreach ($this->setters as $property => $setter) {
            if (array_key_exists($property, $values)) {
                $entity->$setter($values[$property]);
            }
        }
    }

    /**
     * Get Article entity
     *
     * @param Newscoop\Entity\ArticlePopularity $entity
     * @return Newscoop\Entity\Article
     */
    public function getArticle(ArticlePopularity $entity)
    {   
        $article = $this->getEntityManager()->getRepository('Newscoop\Entity\Article')
            ->findOneBy(array('language' => $entity->getLanguageId(), 'number' => $entity->getArticleId(),
        )); 

        if (is_null($article)) {
            $article = new Article;
        }

        return $article;
    }

    /**
     * Check whether the entity exists or not
     *
     * @param Newscoop\Entity\ArticlePopularity $entity
     * @return bool
     */
    public function exists(ArticlePopularity $entity)
    {
        return ($entity->getArticleId()) ? true : false;
    }

    /**
     * Find max value
     *
     * @param array $fields
     * @return array
     */
    public function findMax(array $fields)
    {
        if (!is_array($fields)) {
            return null;
        }

        $what = array();
        foreach($fields as $key => $field) {
            $what[] = 'MAX(p.' . $key . ') as ' . $key;
        }

        $sql = 'SELECT ' . implode(',', $what) . ' FROM Newscoop\Entity\ArticlePopularity p';
        $query = $this->getEntityManager()->createQuery($sql);
        return $query->getSingleResult();
    }
}
