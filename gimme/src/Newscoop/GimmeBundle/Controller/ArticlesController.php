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

class ArticlesController extends FOSRestController
{
	/**
     * @Route("/articles.{_format}", defaults={"_format"="json"})
     * @Method("OPTION")
     * @View()
     */
	public function optionsArticlesAction()
    {}

    /**
     * @Route("/articles.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View()
     */
    public function getArticlesAction()
    {}

    /**
     * @Route("/articles/{id}.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View()
     */
    public function getArticleAction($id)
    {}
}