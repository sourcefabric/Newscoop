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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArticlesController extends FOSRestController
{
	/**
     * @Route("/articles.{_format}", defaults={"_format"="json"})
     * @Method("OPTION")
     * @View()
     */
    public function optionsArticlesAction()
    {
        return array(
            '/articles' => $this->generateUrl('newscoop_gimme_articles_getarticles', array(), true),
            '/articles/{number}' => $this->generateUrl('newscoop_gimme_articles_getarticle', array('number' => 1), true),
            '/articles/{number}/{language}/comments' => $this->generateUrl('newscoop_gimme_comments_getcommentsforarticle', array('number' => 1, 'language' => 'en'), true)
        );
    }

    /**
     * @Route("/articles.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View()
     */
    public function getArticlesAction(Request $request)
    {
        $em = $this->container->get('em');
        $publication = $this->get('newscoop.publication_service')->getPublication()->getId();
        $serializer = $this->get('serializer');
        $serializer->setGroups(array('list'));

        $articles = $em->getRepository('Newscoop\Entity\Article')
            ->getArticles($publication, $request->get('type', null), $request->get('language', null));

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $articles = $paginator->paginate($articles, array(
            'distinct' => false
        ));

        return $articles;
    }

    /**
     * @Route("/articles/{number}.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View()
     */
    public function getArticleAction(Request $request, $number)
    {
        $em = $this->container->get('em');
        $serializer = $this->get('serializer');
        $serializer->setGroups(array('details'));

        $article = $em->getRepository('Newscoop\Entity\Article')
            ->getArticle($number, $request->get('language', null))
            ->getOneOrNullResult();

        if (!$article) {
            throw new NotFoundHttpException('Result was not found.');
        }

        return $article;
    }
}