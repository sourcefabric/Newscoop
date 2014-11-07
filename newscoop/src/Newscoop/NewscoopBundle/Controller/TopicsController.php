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
        $em = $this->get('em');
        $repo = $em->getRepository('Newscoop\NewscoopBundle\Entity\Topic');
        $self = &$this;
        $options = array(
            'decorate' => true,
        );
        $query = $em
            ->createQueryBuilder()
            ->select('node')
            ->from('Newscoop\NewscoopBundle\Entity\Topic', 'node')
            ->orderBy('node.root, node.lft', 'ASC')
            ->getQuery();

        $this->setTranslatableHints($query);
        $nodes = $query->getArrayResult();
        $tree = $repo->buildTree($nodes, $options);

        return array('tree' => $tree);
    }

    /**
     * @Route("/admin/topics/tree/", options={"expose"=true})
     * @Method("GET")
     */
    public function treeAction()
    {
        $em = $this->get('em');
        $tree = $em->getRepository('Newscoop\NewscoopBundle\Entity\Topic')->childrenHierarchy();

        return new JsonResponse(compact('tree'));
    }

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
