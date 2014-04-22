<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2012 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Controller;

use FOS\RestBundle\Controller\FOSRestController;
use FOS\RestBundle\Controller\Annotations\View;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TopicsController extends FOSRestController
{
    /**
     * Get Topics
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when succesfull",
     *         404={
     *           "Returned when topics are not found",
     *         }
     *     }
     * )
     *
     * @Route("/topics.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     */
    public function getTopicsAction(Request $request)
    {
        $em = $this->container->get('em');

        $topics = $em->getRepository('Newscoop\Entity\Topic')
            ->getTopics();

        if (!$topics) {
            throw new NotFoundHttpException('Result was not found.');
        }

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $topics = $paginator->paginate($topics, array(
            'distinct' => false
        ));

        return $topics;
    }

    /**
     * Get topic articles
     *
     * Returns array with articles under "items" key and requested topic "id" and "title"
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the topic is not found",
     *         }
     *     }
     * )
     *
     * @Route("/topics/{id}/{language}/articles.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     */
    public function getTopicsArticlesAction(Request $request, $id, $language)
    {
        $em = $this->container->get('em');
        $publication = $this->get('newscoop_newscoop.publication_service')->getPublication()->getId();

        $paginatorService = $this->get('newscoop.paginator.paginator_service');
        $paginatorService->setUsedRouteParams(array('id' => $id, 'language' => $language));

        $language = $em->getRepository('Newscoop\Entity\Language')
                ->findOneByCode($language);

        $topic = $em->getRepository('Newscoop\Entity\Topic')
            ->findOneBy(array(
                'id' => $id, 
                'language' => $language->getId()
            ));

        if (!$topic) {
            throw new NotFoundHttpException('Result was not found.');
        }

        $articles = $em->getRepository('Newscoop\Entity\Article')
            ->getArticlesForTopic($publication, $id);

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $articles = $paginator->paginate($articles, array(
            'distinct' => false
        ));

        $allItems = array_merge(array(
            'id' => $topic->getTopicId(),
            'title' => $topic->getName(),
        ), $articles);

        return $allItems;
    }
}
