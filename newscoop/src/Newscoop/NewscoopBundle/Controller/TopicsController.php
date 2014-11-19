<?php
/**
 * @package   Newscoop\NewscoopBundle
 * @author    Mischa Gorinskat <mischa.gorinskat@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Gedmo\Translatable\TranslatableListener;
use Doctrine\ORM\Query;
use Newscoop\NewscoopBundle\Entity\Topic;
use Newscoop\NewscoopBundle\Form\Type\TopicType;

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

        $topics = $this->container->get('em')
            ->getRepository('Newscoop\Entity\Topic')
            ->getTopicsByName($term, $limit)
            ->getArrayResult();

        return new JsonResponse($topics);
    }

    /**
     * @Route("/admin/new-topics/", options={"expose"=true})
     * @Template
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/admin/topics/tree/", options={"expose"=true})
     * @Method("GET")
     */
    public function treeAction()
    {
        $em = $this->get('em');
        $tree = $em->getRepository('Newscoop\NewscoopBundle\Entity\Topic')->childrenHierarchy();

        return new JsonResponse(array('tree' => $tree));
    }

    /**
     * @Route("/admin/topics/add/", options={"expose"=true})
     * @Method("POST")
     */
    public function addAction(Request $request)
    {
        $node = new Topic();
        $translator = $this->get('translator');
        $form = $this->createForm(new TopicType());
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
            $em = $this->get('em');
            $data = $form->getData();
            $parent = $em->getRepository('Newscoop\NewscoopBundle\Entity\Topic')->findOneBy(array(
                'id' => $data['parent']
            ));
            $node->setTitle($data['title']);
            if ($parent) {
                $node->setParent($parent);
            }

            $em->persist($node);
            $em->flush();

            $response = array(
                'status' => true,
                'message' => $translator->trans('topics.added', array(), 'topics'),
                'topicId' => $node->getId(),
                'topicTitle' => $node->getTitle()
            );
        } else {
            $response = array(
                'status' => false,
                'message' => $form->getErrors()->getChildren()->getMessage(),
            );
        }

        return new JsonResponse($response);
    }

    /**
     * @Route("/admin/topics/delete/{id}", options={"expose"=true})
     * @Method("POST")
     */
    public function deleteAction(Request $request, $id)
    {
        $translator = $this->get('translator');
        $em = $this->get('em');
        $node = $em->getRepository('Newscoop\NewscoopBundle\Entity\Topic')->findOneBy(array(
            'id' => $id,
        ));

        if (!$node) {
            return new JsonResponse(array(
                'status' => false,
                'message' => $translator->trans('topics.failedfind', array('%id%' => $id), 'topics')
            ), 404);
        }

        $em->remove($node);
        $em->flush();

        return new JsonResponse(array(
            'status' => true,
            'message' => $translator->trans('topics.removed', array('%title%' => $node->getTitle()), 'topics')
        ));
    }

    /**
     * @Route("/admin/topics/edit/{id}", options={"expose"=true})
     * @Method("POST")
     */
    public function editAction(Request $request, $id)
    {
        $em = $this->get('em');
        $translator = $this->get('translator');

        if (!$this->get('form.csrf_provider')->isCsrfTokenValid('default', $request->get('_csrf_token'))) {
            return new JsonResponse(array(
                'status' => false,
                'message' => $translator->trans('topics.csrfinvalid', array(), 'topics'),
            ), 403);
        }

        $node = $em->getRepository('Newscoop\NewscoopBundle\Entity\Topic')->findOneBy(array(
            'id' => $id,
        ));

        if (!$node) {
            return new JsonResponse(array(
                'status' => false,
                'message' => $translator->trans('topics.failedfind', array('%id%' => $id), 'topics')
            ), 404);
        }

        $form = $this->createForm(new TopicType(), $node);

        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->flush();

            return new JsonResponse(array(
                'status' => true,
                'message' => $translator->trans('topics.updated', array(), 'topics')
            ));
        }

        return new JsonResponse(array(
            'status' => false,
            'message' => $translator->trans('topics.error', array(), 'topics')
        ));
    }

    /**
     * Set translatable hints
     *
     * @param Query $query Query object
     */
    public function setTranslatableHints(Query $query)
    {
        $query->setHint(
            Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        );
        $query->setHint(
            TranslatableListener::HINT_INNER_JOIN,
            $this->get('session')->get('gedmo.trans.inner_join', false)
        );
        $query->setHint(
            TranslatableListener::HINT_TRANSLATABLE_LOCALE,
            $this->get('request')->get('_locale', 'en')
        );
        $query->setHint(
            TranslatableListener::HINT_FALLBACK,
            $this->get('session')->get('gedmo.trans.fallback', false)
        );
    }
}
