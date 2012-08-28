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
            '/articles/get/{id}' => $this->generateUrl('newscoop_gimme_articles_getarticle', array('id' => 1), true),
            '/articles/get/{id}/{language}/comments' => $this->generateUrl('newscoop_gimme_comments_getcommentsforarticle', array('id' => 1, 'language' => 'en_US'), true)
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

        /**
         * Optional parameters
         */
        $type = $request->get('type');
        $language = $request->get('language');

        /**
         * We can't use default paginator counter because composite id.
         * It must be moved to repository
         */
        $articlesCount = $em
            ->createQuery('SELECT COUNT(a) FROM Newscoop\Entity\Article a')
            ->getSingleScalarResult();

        $articles = $em->getRepository('Newscoop\Entity\Article')
            ->getArticles()
            ->setHint('knp_paginator.count', $articlesCount);

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $paginator->setDistinct(false);
        $articles = $paginator->paginate($articles);

        return $articles;
    }

    /**
     * @Route("/articles/{id}.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View()
     */
    public function getArticleAction($id)
    {

    }
}