<?php

/**
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2014 Sourcefabric z.ú.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */
namespace Newscoop\NewscoopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Newscoop\NewscoopBundle\Entity\Topic;
use Newscoop\NewscoopBundle\Form\Type\TopicType;
use Newscoop\NewscoopBundle\Form\Type\TopicTranslationType;
use Newscoop\NewscoopBundle\Entity\TopicTranslation;
use Doctrine\ORM\Query;
use Newscoop\EventDispatcher\Events\GenericEvent;

/**
 * Topic controller.
 */
class TopicsController extends Controller
{
    /**
     * @Route("/admin/topics/get", options={"expose"=true})
     */
    public function getTopicsAction(Request $request)
    {
        $term = $request->query->get('term', '');
        $limit = $request->query->get('limit', null);

        if (trim($term) === '') {
            return new JsonResponse(array());
        }

        $locale = $request->get('_code', $request->getLocale());
        $topics = $this->container->get('em')
            ->getRepository('Newscoop\NewscoopBundle\Entity\Topic')
            ->searchTopics($term, array('title' => 'asc'), $limit, $locale)
            ->getArrayResult();

        return new JsonResponse($topics);
    }

    /**
     * @Route("/admin/topics/", options={"expose"=true}, name="newscoop_newscoop_topics_index")
     * @Route("/admin/topics/view/{compactView}/{articleNumber}/{language}", options={"expose"=true}, name="newscoop_newscoop_topics_index_compact")
     */
    public function indexAction($compactView = false, $articleNumber = null, $language = null)
    {
        $assignedTopics = array();
        if ($compactView === 'compact') {
            $compactView = true;
            $entityManager = $this->get('em');
            $repository = $entityManager->getRepository('Newscoop\NewscoopBundle\Entity\Topic');
            $assignedTopics = $repository
                ->getArticleTopics($articleNumber, $language)
                ->getArrayResult();

            foreach ($assignedTopics as $key => $topic) {
                $topicObj = $entityManager->getReference('Newscoop\NewscoopBundle\Entity\Topic', $topic['id']);
                $topic['path'] = $repository->getReadablePath($topicObj, $language);
                $assignedTopics[$key] = $topic;
            }
        }

        return $this->render('NewscoopNewscoopBundle:Topics:index.html.twig', array(
            'compactView' => $compactView,
            'articleLanguage' => $language,
            'articleNumber' => $articleNumber,
            'assignedTopics' => $assignedTopics,
        ));
    }

    /**
     * @Route("/admin/topics/tree/", options={"expose"=true})
     *
     * @Method("GET")
     */
    public function treeAction(Request $request)
    {
        $em = $this->get('em');
        $locale = $request->get('_code');
        $articleNumber = $request->get('_articleNumber');
        $topicService = $this->get('newscoop_newscoop.topic_service');
        $cacheService = $this->get('newscoop.cache');
        $topicsCount = $topicService->countBy();
        $attachedCount = $topicService->countArticleTopicsBy();
        $cacheKey = $cacheService->getCacheKey(array('topics', $topicsCount, $attachedCount, $articleNumber), 'topic');
        $repository = $em->getRepository('Newscoop\NewscoopBundle\Entity\Topic');
        if ($cacheService->contains($cacheKey)) {
            $nodes = $cacheService->fetch($cacheKey);
        } else {
            $topicsQuery = $repository->getTranslatableTopics($locale);
            $nodes = $topicsQuery->getArrayResult();
            if ($articleNumber) {
                $nodes = $this->setAttachedKeysForArticleTopics($nodes, $articleNumber);
            }

            $cacheService->save($cacheKey, $nodes);
        }

        $tree = $repository->buildTreeArray($nodes);

        usort($tree, function ($node1, $node2) {
            return $node2['topicOrder'] - $node1['topicOrder'];
        });

        return new JsonResponse(array('tree' => $tree));
    }

    /**
     * Adds "attached" and "hasAttachedSubtopic" keys with values to the array of the topic;.
     *
     * @param array  $nodes         Array of topics
     * @param string $articleNumber Article number
     *
     * @return array
     */
    private function setAttachedKeysForArticleTopics($nodes, $articleNumber)
    {
        $topicsIds = $this->getArticleTopicsIds($articleNumber);
        foreach ($nodes as $key => $topic) {
            if (in_array($topic['id'], $topicsIds)) {
                $topic['attached'] = true;
                $nodes[$key] = $topic;
            }
        }

        return $nodes;
    }

    /**
     * @Route("/admin/topics/move/{id}", options={"expose"=true})
     */
    public function moveAction(Request $request, $id)
    {
        $em = $this->get('em');
        $repository = $em->getRepository('Newscoop\NewscoopBundle\Entity\Topic');
        $topicService = $this->get('newscoop_newscoop.topic_service');
        $translator = $this->get('translator');
        $cacheService = $this->get('newscoop.cache');
        $node = $this->findOr404($id);
        if (is_array($node)) {
            return new JsonResponse($node, 404);
        }

        $params = $request->request->all();
        $parent = isset($params['parent']) && $params['parent'] ? $params['parent'] : null;
        $result = $topicService->saveTopicPosition($node, $params);

        if (!$result) {
            return new JsonResponse(array(
                'status' => false,
                'message' => $translator->trans('topics.failedfind', array('%id%' => $id), 'topics'),
            ));
        }

        if (($request->get('last') || $request->get('first') || $request->get('middle')) && !$parent) {
            $rootNodes = $repository->getRootNodesQuery()->getResult();
            $order = array_reverse(explode(',', $request->get('order')));
            $topicService->reorderRootNodes($rootNodes, $order);
        }

        $cacheService->clearNamespace('topic');

        return new JsonResponse(array(
            'topic' => array(
                'id' => $node->getId(),
                'root' => $node->getRoot(),
                'parentId' => $node->getParentId()
            ),
            'status' => true,
            'message' => $translator->trans('topics.alerts.ordersaved', array(), 'topics'),
        ), 200);
    }

    /**
     * @Route("/admin/topics/add/", options={"expose"=true})
     *
     * @Method("POST")
     */
    public function addAction(Request $request)
    {
        $node = new Topic();
        $translator = $this->get('translator');
        $cacheService = $this->get('newscoop.cache');
        $form = $this->createForm(new TopicType(), $node);
        $form->handleRequest($request);
        $response = array(
            'status' => false,
            'message' => $translator->trans('topics.error', array(), 'topics'),
        );

        if (!$this->get('form.csrf_provider')->isCsrfTokenValid('default', $request->get('_csrf_token'))) {
            return new JsonResponse(array(
                'status' => false,
                'message' => $translator->trans('topics.csrfinvalid', array(), 'topics'),
            ), 403);
        }

        if ($form->isValid()) {
            $locale = $request->get('_code', $request->getLocale());
            $topicService = $this->get('newscoop_newscoop.topic_service');
            try {
                $topicService->saveNewTopic($node, $locale);
            } catch (\Exception $e) {
                $response = array(
                    'status' => false,
                    'message' => $translator->trans('topics.exists', array(), 'topics'),
                );

                return new JsonResponse($response, 409);
            }

            $response = array(
                'status' => true,
                'message' => $translator->trans('topics.added', array(), 'topics'),
                'topicId' => $node->getId(),
                'topicTitle' => $node->getTitle(),
                'locale' => $locale,
            );

            $cacheService->clearNamespace('topic');
        } else {
            $response = array(
                'status' => false,
                'message' => $form->getErrors()->getChildren()->getMessage(),
            );
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/admin/topics/add/translation/{id}", requirements={"id" = "\d+"}, options={"expose"=true})
     *
     * @Method("POST")
     */
    public function addTranslation(Request $request, $id)
    {
        $em = $this->get('em');
        $translator = $this->get('translator');
        $cacheService = $this->get('newscoop.cache');
        $form = $this->createForm(new TopicTranslationType());
        $form->handleRequest($request);
        $response = array(
            'status' => false,
            'message' => $translator->trans('topics.error', array(), 'topics'),
        );

        if (!$this->get('form.csrf_provider')->isCsrfTokenValid('default', $request->get('_csrf_token'))) {
            return new JsonResponse(array(
                'status' => false,
                'message' => $translator->trans('topics.csrfinvalid', array(), 'topics'),
            ), 403);
        }

        $node = $this->findOr404($id);
        if (is_array($node)) {
            return new JsonResponse($node, 404);
        }

        if ($form->isValid()) {
            $data = $form->getData();
            $locale = $data['locale'];
            $language = $em
                ->getRepository('Newscoop\Entity\Language')
                ->findOneByCode($locale);

            if (!$language) {
                return new JsonResponse(array(
                    'status' => false,
                    'message' => $translator->trans('topics.alerts.languagenotfound', array('%locale%' => $locale), 'topics'),
                ), 404);
            }

            try {
                $topicTranslation = new TopicTranslation($language->getCode(), 'title', $data['title']);
                $node->addTranslation($topicTranslation);
                $em->flush();
            } catch (\Exception $e) {
                return new JsonResponse(array(
                    'status' => false,
                    'message' => $translator->trans('topics.alerts.translationexists', array('%locale%' => $locale), 'topics'),
                ), 403);
            }

            $response = array(
                'status' => true,
                'message' => $translator->trans('topics.alerts.translationadded', array(), 'topics'),
                'topicTranslationId' => $topicTranslation->getId(),
                'topicTranslationTitle' => $topicTranslation->getContent(),
                'topicTranslationLocale' => $topicTranslation->getLocale(),
            );

            $cacheService->clearNamespace('topic');
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/admin/topics/delete/{id}", options={"expose"=true})
     *
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $translator = $this->get('translator');
        $cacheService = $this->get('newscoop.cache');
        $node = $this->findOr404($id);
        if (is_array($node)) {
            return new JsonResponse($node, 404);
        }

        $topicService = $this->get('newscoop_newscoop.topic_service');
        $topicService->deleteTopic($node);
        $cacheService->clearNamespace('topic');

        $this->get('dispatcher')->dispatch('topic.delete', new GenericEvent($this, array(
            'title' => $node->getTitle(),
            'id' => array(
                'id' => $id,
            ),
            'diff' => array(
                'id' => $id,
                'title' => $node->getTitle(),
            ),
        )));

        return new JsonResponse(array(
            'status' => true,
            'message' => $translator->trans('topics.removed', array('%title%' => $node->getTitle()), 'topics'),
        ));
    }

    /**
     * @Route("/admin/topics/is-attached/{id}", options={"expose"=true})
     *
     * @Method("GET")
     */
    public function isAttachedAction(Request $request, $id)
    {
        $translator = $this->get('translator');
        $topicService = $this->get('newscoop_newscoop.topic_service');
        $node = $this->findOr404($id);
        if (is_array($node)) {
            return new JsonResponse($node, 404);
        }

        $result = $topicService->isAttached($id, true);

        if ($result[1]) {
            return new JsonResponse(array(
                'status' => true,
                'message' => $translator->trans('topics.attached', array('%occurence%' => $result[0]), 'topics'),
            ));
        }

        return new JsonResponse(array(
            'status' => false,
        ));
    }

    /**
     * @Route("/admin/topics/translations/delete/{id}", options={"expose"=true})
     *
     * @Method("POST")
     */
    public function deleteTranslationAction(Request $request, $id)
    {
        $translator = $this->get('translator');
        $cacheService = $this->get('newscoop.cache');
        $em = $this->get('em');
        $topicTranslation = $em->getRepository('Newscoop\NewscoopBundle\Entity\TopicTranslation')->findOneBy(array(
            'id' => $id,
        ));

        if (!$topicTranslation) {
            return new JsonResponse(array(
                'status' => false,
                'message' => $translator->trans('topics.failedfindTranslation', array('%id%' => $id), 'topics'),
            ), 404);
        }

        if ($topicTranslation->getIsDefault()) {
            return new JsonResponse(array(
                'status' => false,
                'message' => $translator->trans('topics.failedremoveTranslation', array(), 'topics'),
            ), 403);
        }

        $em->remove($topicTranslation);
        $em->flush();

        $cacheService->clearNamespace('topic');

        return new JsonResponse(array(
            'status' => true,
            'message' => $translator->trans('topics.removedTranslation', array(), 'topics'),
        ));
    }

    /**
     * @Route("/admin/topics/edit/{id}", options={"expose"=true})
     *
     * @Method("POST")
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->get('em');
        $translator = $this->get('translator');
        $topicService = $this->get('newscoop_newscoop.topic_service');
        $cacheService = $this->get('newscoop.cache');

        if (!$this->get('form.csrf_provider')->isCsrfTokenValid('default', $request->get('_csrf_token'))) {
            return new JsonResponse(array(
                'status' => false,
                'message' => $translator->trans('topics.csrfinvalid', array(), 'topics'),
            ), 403);
        }

        $node = $this->findOr404($id);
        if (is_array($node)) {
            return new JsonResponse($node, 404);
        }

        $locale = $request->get('_code', $request->getLocale());
        $form = $this->createForm(new TopicType());
        $form->handleRequest($request);
        if ($form->isValid()) {
            $data = $form->getData();

            if ($topicService->checkTopicName($locale, $data['title'])) {
                return new JsonResponse(array(
                    'status' => false,
                    'message' => $translator->trans('topics.existsname', array(), 'topics'),
                ));
            }

            foreach ($node->getTranslations() as $translation) {
                if ($translation->getLocale() == $locale && $translation->getField() == 'title') {
                    $translation->setContent($data['title']);
                    $exists = true;
                }
            }

            $em->flush();
            $cacheService->clearNamespace('topic');

            return new JsonResponse(array(
                'status' => true,
                'message' => $translator->trans('topics.updated', array(), 'topics'),
            ));
        }

        return new JsonResponse(array(
            'status' => false,
            'message' => $translator->trans('topics.error', array(), 'topics'),
        ));
    }

    /**
     * @Route("/admin/topics/detach", options={"expose"=true})
     *
     * @Method("POST")
     */
    public function detachTopicAction(Request $request)
    {
        $translator = $this->get('translator');
        $em = $this->get('em');
        $userService = $this->get('user');
        $cacheService = $this->get('newscoop.cache');
        $user = $userService->getCurrentUser();
        $topicService = $this->get('newscoop_newscoop.topic_service');
        if (!$user->hasPermission('AttachTopicToArticle')) {
            return new JsonResponse(array(
                'status' => false,
                'message' => $translator->trans('You do not have the right to detach topics from articles.', array(), 'article_topics'),
            ), 403);
        }

        $articleNumber = $request->get('articleNumber');
        $topicId = $request->get('topicId');
        $language = $request->get('language');
        $articleObj = $em->getRepository('Newscoop\Entity\Article')->findOneBy(array(
            'number' => $articleNumber,
            'language' => $language,
        ));

        if (!$articleObj) {
            return new JsonResponse(array(
                'status' => false,
                'message' => $translator->trans('Article does not exist.'),
            ), 404);
        }

        $topicObj = $em->getRepository('Newscoop\NewscoopBundle\Entity\Topic')->getSingleTopicQuery($topicId)->getOneOrNullResult();

        if (!$topicObj) {
            return new JsonResponse(array(
                'status' => false,
                'message' => $translator->trans('Topic does not exist.'),
            ), 404);
        }

        $topicService->removeTopicFromArticle($topicObj, $articleObj);
        $cacheService->clearNamespace('topic');

        return new JsonResponse(array(
            'status' => true,
            'message' => $translator->trans('The topic $1 has been removed from article.', array('$1' => $topicObj->getTitle()), 'article_topics'),
        ));
    }

    /**
     * @Route("/admin/topics/attach", options={"expose"=true})
     *
     * @Method("POST")
     */
    public function attachTopicAction(Request $request)
    {
        $translator = $this->get('translator');
        $em = $this->get('em');
        $userService = $this->get('user');
        $user = $userService->getCurrentUser();
        $cacheService = $this->get('newscoop.cache');
        $topicService = $this->get('newscoop_newscoop.topic_service');
        if (!$user->hasPermission('AttachTopicToArticle')) {
            return new JsonResponse(array(
                'status' => false,
                'message' => $translator->trans('You do not have the right to attach topics to articles.', array(), 'article_topics'),
            ), 403);
        }

        $articleNumber = $request->get('_articleNumber');
        $languageCode = $request->get('_languageCode');
        $qb = $em->getRepository('Newscoop\Entity\Article')->createQueryBuilder('a')
            ->join('a.language', 'l')
            ->where('a.number = :number')
            ->andWhere('l.code = :code')
            ->setParameters(array(
                'number' => $articleNumber,
                'code' => $languageCode,
            ));

        $articleObj = $qb->getQuery()->getOneOrNullResult();

        if (!$articleObj) {
            return new JsonResponse(array(
                'status' => false,
                'message' => $translator->trans('Article does not exist.'),
            ), 404);
        }

        $ids = $request->get('ids');
        $topicsIds = $this->getArticleTopicsIds($articleNumber);
        $idsDiff = array_merge(array_diff($ids, $topicsIds), array_diff($topicsIds, $ids));
        foreach ($idsDiff as $key => $topicId) {
            $topicObj = $em->getReference("Newscoop\NewscoopBundle\Entity\Topic", $topicId);
            if (in_array($topicId, $topicsIds)) {
                $topicService->removeTopicFromArticle($topicObj, $articleObj);
            } else {
                $topicService->addTopicToArticle($topicObj, $articleObj);
            }
        }

        $cacheService->clearNamespace('topic');

        return new JsonResponse(array(
            'status' => true,
            'message' => $translator->trans('topics.alerts.saved', array(), 'topics'),
        ));
    }

    public function findOr404($id)
    {
        $em = $this->get('em');
        $translator = $this->get('translator');
        $node = $em->getRepository('Newscoop\NewscoopBundle\Entity\Topic')->findOneBy(array(
            'id' => $id,
        ));

        if (!$node) {
            return array(
                'status' => false,
                'message' => $translator->trans('topics.failedfind', array('%id%' => $id), 'topics'),
            );
        }

        return $node;
    }

    /**
     * Get Article Topics.
     *
     * @param string $articleNumber Article number
     *
     * @return array Ids of article topics
     */
    private function getArticleTopicsIds($articleNumber)
    {
        $em = $this->get('em');
        $query = $em->getRepository('Newscoop\Entity\ArticleTopic')->getArticleTopicsIds($articleNumber, true);
        $articleTopics = $query->getArrayResult();

        $topicsIds = array();
        foreach ($articleTopics as $articleTopic) {
            $topicsIds[] = reset($articleTopic);
        }

        return $topicsIds;
    }
}
