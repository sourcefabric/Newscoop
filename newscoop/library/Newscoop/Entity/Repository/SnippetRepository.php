<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl.txt
 */

namespace Newscoop\Entity\Repository;

use Doctrine\ORM\Query\Expr;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Newscoop\Entity\Snippet;
// use Newscoop\Entity\Snippet\Commenter;
use Newscoop\Datatable\Source as DatatableSource;
use Newscoop\Entity\User;
use Newscoop\Search\RepositoryInterface;
use Newscoop\NewscoopException\IndexException;

/**
 * Snippet repository
 */
class SnippetRepository extends EntityRepository
{

    /**
     * Get new instance of the Snippet
     *
     * @return \Newscoop\Entity\Snippet
     */
    public function getPrototype()
    {
        return new Snippet;
    }

    /**
     * Get Snippets for article
     *
     * @param  int $article  Article number
     * @param  string $language Language code in format "en" for example.
     *
     * @return Doctrine\ORM\Query           Query
     */
    public function getArticleSnippets($article, $language)
    {
        $em = $this->getEntityManager();
        $languageId = $em->getRepository('Newscoop\Entity\Language')
                ->findOneByCode($language);

        $article = $em->getRepository('Newscoop\Entity\Article')
                ->getArticle($article, $languageId);

        $queryBuilder = $em->getRepository('Newscoop\Entity\Snippet')
                ->createQueryBuilder('snippet');

        $query = $queryBuilder->getQuery();
        return $query;

        // $article
        // $queryBuilder = $em->getRepository('Newscoop\Entity\Snippet')
        //     ->createQueryBuilder('c')
        //     ->where('c.thread = :thread')
        //     ->andWhere('c.language = :language')
        //     ->setParameters(array(
        //         'thread' => $article,
        //         'language' => $languageId->getId()
        //     ));
        // if ($recommended) {
        //     $queryBuilder->andWhere('c.recommended = 1');
        // }

        // if (!$getDeleted) {
        //     $queryBuilder->andWhere('c.status != :status')
        //         ->setParameter('status', Snippet::STATUS_DELETED);
        // }

        // $query = $queryBuilder->getQuery();

        // return $query;
    }

    /**
     * Get all Snippets query
     *
     * @return Query
     */
    public function getSnippets($getDeleted = true)
    {
        $em = $this->getEntityManager();

        $queryBuilder = $em->getRepository('Newscoop\Entity\Snippet')
            ->createQueryBuilder('c');

        if (!$getDeleted) {
            $queryBuilder->andWhere('c.status != :status')
                ->setParameter('status', Snippet::STATUS_DELETED);
        }

        $query = $queryBuilder->getQuery();

        return $query;
    }

    /**
     * Get single Snippet query
     *
     * @param int $id
     *
     * @return Query
     */
    // public function getSnippet($id, $getDeleted = true)
    // {
    //     $em = $this->getEntityManager();

    //     $queryBuilder = $em->getRepository('Newscoop\Entity\Snippet')
    //         ->createQueryBuilder('c')
    //         ->andWhere('c.id = :id')
    //         ->setParameter('id', $id);

    //     if (!$getDeleted) {
    //         $queryBuilder->andWhere('c.status != :status')
    //             ->setParameter('status', Snippet::STATUS_DELETED);
    //     }

    //     $query = $queryBuilder->getQuery();

    //     return $query;
    // }

    /**
     * Method for update a Snippet
     *
     * @param Snippet $entity
     * @param array   $values
     *
     * @return Snippet $enitity
     */
    // public function update(Snippet $Snippet, $values)
    // {
    //     // get the enitity manager
    //     $em = $this->getEntityManager();
    //     if (array_key_exists('subject', $values) && !is_null($values['subject'])) {
    //         $Snippet->setSubject($values['subject']);
    //     }
    //     if (array_key_exists('message', $values) && !is_null($values['message'])) {
    //         $Snippet->setMessage($values['message']);
    //     }
    //     if (array_key_exists('recommended', $values) && !is_null($values['recommended'])) {
    //         $Snippet->setRecommended($values['recommended']);
    //     }
    //     if (array_key_exists('status', $values) && !is_null($values['status'])) {
    //         $Snippet->setStatus($values['status']);
    //     }
    //     $Snippet->setTimeUpdated(new \DateTime());

    //     return $Snippet;
    // }

    /**
     * Method for saving a Snippet
     *
     * @param Snippet $entity
     * @param array   $values
     *
     * @return Snippet
     */
    // public function save(Snippet $entity, $values)
    // {
    //     $values += array('recommended' => false);
    //     $em = $this->getEntityManager();

    //     $commenterRepository = $em->getRepository('Newscoop\Entity\Snippet\Commenter');

    //     $commenter = new Commenter();
    //     $commenter = $commenterRepository->save($commenter, $values);

    //     $entity->setCommenter($commenter)
    //         ->setSubject($values['subject'])
    //         ->setMessage($values['message'])
    //         ->setStatus($values['status'])
    //         ->setIp($values['ip'])
    //         ->setTimeCreated($values['time_created'])
    //         ->setRecommended($values['recommended']);

    //     if (array_key_exists('source', $values)) {
    //         $entity->setSource($values['source']);
    //     }

    //     $threadLevel = 0;

    //     if (!empty($values['parent']) && (0 != $values['parent'])) {
    //         $parent = $this->find($values['parent']);
    //         // set parent of the Snippet
    //         $entity
    //             ->setParent($parent)
    //             ->setLanguage($parent->getLanguage())
    //             ->setForum($parent->getForum())
    //             ->setThread($parent->getThread());
    //         /**
    //          * get the maximum thread order from the current parent
    //          */
    //         $qb = $this->createQueryBuilder('c');
    //         $threadOrder =
    //         $qb->select('MAX(c.thread_order)')
    //                 ->andwhere('c.parent = :parent')
    //                 ->andWhere('c.thread = :thread')
    //                 ->andWhere('c.language = :language')
    //                 ->setParameter('parent', $parent)
    //                 ->setParameter('thread', $parent->getThread()->getId())
    //                 ->setParameter('language', $parent->getLanguage()->getId());

    //         $threadOrder = $threadOrder->getQuery()->getSingleScalarResult();
    //         // if the Snippet parent doesn't have children then use the parent thread order
    //         if (empty($threadOrder)) {
    //             $threadOrder = $parent->getThreadOrder();
    //         }
    //         $threadOrder += 1;
    //         *
    //          * update all the Snippet for the thread where thread order is less or equal
    //          * of the current thread_order
             
    //         $qb = $this->createQueryBuilder('c');
    //         $qb->update()
    //            ->set('c.thread_order',  'c.thread_order+1')
    //            ->andwhere('c.thread_order >= :thread_order')
    //            ->andWhere('c.thread = :thread')
    //            ->andWhere('c.language = :language')
    //                 ->setParameter('language', $parent->getLanguage()->getId())
    //                 ->setParameter('thread', $parent->getThread()->getId())
    //                 ->setParameter('thread_order', $threadOrder);
    //         $qb->getQuery()->execute();
    //         // set the thread level the thread level of the parent plus one the current level
    //         $threadLevel = $parent->getThreadLevel() + 1;
    //     } else {
    //         $languageRepository = $em->getRepository('Newscoop\Entity\Language');

    //         if (is_numeric($values['language'])) {
    //             $language = $languageRepository->findOneById($values['language']);
    //         } else {
    //             $language = $languageRepository->findOneByCode($values['language']);
    //         }

    //         $articleRepository = $em->getRepository('Newscoop\Entity\Article');
    //         $thread = $articleRepository->find(array('number' => $values['thread'], 'language' => $language->getId()));

    //         $query = $this->createQueryBuilder('c')
    //             ->select('MAX(c.thread_order)')
    //             ->where('c.thread = :thread')
    //             ->andWhere('c.language = :language')
    //             ->setParameter('thread', $thread->getNumber())
    //             ->setParameter('language', $language->getId())
    //             ->getQuery();

    //         // increase by one of the current Snippet
    //         $threadOrder = $query->getSingleScalarResult() + 1;

    //         $entity
    //             ->setLanguage($language)
    //             ->setForum($thread->getPublication())
    //             ->setThread($thread);
    //     }

    //     $entity->setThreadOrder($threadOrder)->setThreadLevel($threadLevel);
    //     $em->persist($entity);

    //     return $entity;
    // }

    /**
     * Delete article Snippets
     *
     * @param Newscoop\Entity\Article  $article
     * @param Newscoop\Entity\Language $language
     */
    // public function deleteArticle($article, $language = null)
    // {
    //     $em = $this->getEntityManager();
    //     $params = array('thread' => $article);
    //     if (!is_null($language)) {
    //         $params['language'] = $language;
    //     }
    //     $Snippets = $this->findBy($params);
    //     foreach ($Snippets as $Snippet) {
    //         $Snippet->setParent();
    //     }
    //     foreach ($Snippets as $Snippet) {
    //         $this->setSnippetstatus($Snippet, 'deleted');
    //     }
    // }

    /**
     * Flush method
     */
    public function flush()
    {
        $this->getEntityManager()->flush();
    }
}
