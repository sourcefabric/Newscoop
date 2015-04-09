<?php
/**
 * @package Newscoop\GimmeBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
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
use Newscoop\NewscoopBundle\Entity\Topic;
use Newscoop\NewscoopBundle\Form\Type\TopicType;
use Symfony\Component\HttpFoundation\Response;

class TopicsController extends FOSRestController
{
    /**
     * Get all topics
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404="Returned when topics are not found"
     *     }
     * )
     *
     * @Route("/topics.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     *
     * @return array
     */
    public function getTopicsAction(Request $request)
    {
        $em = $this->container->get('em');

        $topics = $em->getRepository('Newscoop\NewscoopBundle\Entity\Topic')
            ->getTopics($request->get('language'));

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
     * Get single topic
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when topic successfully found",
     *         404="Returned when the topic is not found",
     *     },
     *     output="\Newscoop\NewscoopBundle\Entity\Topic"
     * )
     *
     * @Route("/topics/{id}.{_format}", defaults={"_format"="json"}, options={"expose"=true}, requirements={"id" = "[\d]+"})
     * @Method("GET")
     * @View()
     */
    public function getTopicByIdAction($id)
    {
        $em = $this->container->get('em');
        $topic = $em->getRepository('Newscoop\NewscoopBundle\Entity\Topic')
            ->getSingleTopicQuery($id)
            ->getOneOrNullResult();

        if (!$topic) {
            throw new NotFoundHttpException('Topic was not found.');
        }

        return $topic;
    }

    /**
     * Create new Topic
     *
     * @ApiDoc(
     *     statusCodes={
     *         201="Returned when Topic created successfully"
     *     },
     *     input="\Newscoop\NewscoopBundle\Form\Type\TopicType"
     * )
     *
     * @Route("/topics.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Route("/articles/{articleNumber}/{languageCode}/topics.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("POST")
     * @View()
     *
     * @return Form
     */
    public function createTopicAction(Request $request, $articleNumber = null, $languageCode = null)
    {
        return $this->processForm($request, null, $articleNumber, $languageCode);
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
     * @Route("/topics/{id}/{language}/articles.{_format}", defaults={"_format"="json"}, options={"expose"=true})
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

        $query = $em->getRepository('Newscoop\NewscoopBundle\Entity\Topic')->getArticlesQueryByTopicIdAndLanguage($id, $language->getCode());
        $topic = $query->getArrayResult();

        if (empty($topic)) {
            throw new NotFoundHttpException('Result was not found.');
        }

        $articles = $em->getRepository('Newscoop\Entity\Article')
            ->getArticlesForTopic($publication, $id);

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $articles = $paginator->paginate($articles, array(
            'distinct' => false
        ));

        $allItems = array_merge(array(
            'id' => $topic[0]['id'],
            'title' => $topic[0]['title'],
        ), $articles);

        return $allItems;
    }

    /**
     * Get article's topics
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when topic successfully found",
     *         404={
     *           "Returned when the topics are not found",
     *         }
     *     }
     * )
     *
     * @Route("/articles/{number}/{language}/topics.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Route("/topics/article/{number}/{language}.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     */
    public function getArticlesTopicsAction($number, $language)
    {
        $em = $this->container->get('em');
        $topics = $em->getRepository('Newscoop\NewscoopBundle\Entity\Topic')
            ->getArticleTopics($number, $language);

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $paginator->setUsedRouteParams(array('number' => $number, 'language' => $language));
        $topics = $paginator->paginate($topics);

        return $topics;
    }

    /**
     * Search for topics by title
     *
     * Get list of topics by given search query
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful"
     *     },
     *     filters={
     *          {"name"="query", "dataType"="string", "description"="search query"}
     *     },
     * )
     *
     * @Route("/search/topics.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     */
    public function searchTopicsAction(Request $request)
    {
        $em = $this->container->get('em');
        $query = $request->query->get('query', '');
        $topics = $em->getRepository('Newscoop\NewscoopBundle\Entity\Topic')
            ->searchTopics($query);

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $topics = $paginator->paginate($topics, array('distinct' => false));

        return $topics;
    }

    /**
     * Delete topic
     *
     * @ApiDoc(
     *     statusCodes={
     *         204="Returned when topic removed succesfully",
     *         404={
     *           "Returned when topic is not found",
     *         }
     *     },
     *     requirements={
     *         {"name"="id", "dataType"="integer", "required"=true, "description"="Topic id"}
     *     }
     * )
     *
     * @Route("/topics/{id}.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("DELETE")
     */
    public function deleteTopicAction(Request $request, $id)
    {
        $topicService = $this->get('newscoop_newscoop.topic_service');
        $em = $this->container->get('em');
        $topic = $em->getRepository('Newscoop\NewscoopBundle\Entity\Topic')->findOneBy(array(
            'id' => $id,
        ));

        if (!$topic) {
            throw new NotFoundHttpException('Topic was not found');
        }

        $topicService->deleteTopic($topic);
        $response = new Response();
        $response->setStatusCode(204);

        return $response;
    }

    /**
     * Process Topic form
     *
     * @param Request $request
     * @param integer $topicId
     * @param integer $articleNumber
     * @param string  $languageCode
     *
     * @return Form
     */
    private function processForm($request, $topicId = null, $articleNumber = null, $languageCode = null)
    {
        $em = $this->get('em');
        $topic = new Topic();
        $statusCode = 201;
        if ($topicId) {
            $topic = $em->getRepository('Newscoop\NewscoopBundle\Entity\Topic')->findOneById($topicId);
            $statusCode = 200;
            if (is_null($topic)) {
                throw new NotFoundHttpException("Topic with ID: '" . $topicId . "' was not found");
            }
        }

        $article = null;
        if (!is_null($articleNumber) && !is_null($languageCode)) {
            $article = $em->getRepository('Newscoop\Entity\Article')
                ->getArticle($articleNumber, $languageCode)
                ->getOneOrNullResult();

            if (!$article) {
                throw new NotFoundHttpException('Article with number:"' . $articleNumber . '" and language: "' . $languageCode . '" was not found.');
            }
        }

        $form = $this->get('form.factory')->create(new TopicType(), $topic);
        $form->handleRequest($request);

        if ($form->isValid()) {
            if ($article) {
                $topic->addArticleTopic($article);
            }

            $locale = null;
            if (!$topic->getTranslatableLocale()) {
                $locale = $request->getLocale();
            }

            $topicService = $this->get('newscoop_newscoop.topic_service');
            $topicService->saveNewTopic($topic, $locale);
            $response = new Response();
            $response->setStatusCode($statusCode);

            $response->headers->set(
                'X-Location',
                $this->generateUrl('newscoop_gimme_topics_gettopicbyid', array(
                    'id' => $topic->getId(),
                ), true)
            );

            return $response;
        }

        return $form;
    }
}
