<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
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
use Newscoop\Criteria\SlideshowCriteria;

class SlideshowsController extends FOSRestController
{
    /**
     * Get slideshow and its items
     *
     * Returns array with items under "items" key and requested slideshow "id", "title" and "summary"
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the slideshow is not found",
     *         }
     *     }
     * )
     *
     * @Route("/slideshows/{id}.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("GET")
     * @View(serializerGroups={"details"})
     */
    public function getSlideshowItemsAction(Request $request, $id)
    {
        $em = $this->container->get('em');
        $package = $em->getRepository('Newscoop\Package\Package')->findOneBy(array(
            'id' => $id,
        ));

        if (!$package) {
            throw new NotFoundHttpException('Result was not found.');
        }

        return $package;
    }

    /**
     * Get article slideshows
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the articles slideshows are not found"
     *         }
     *     }
     * )
     *
     * @Route("/articles/{number}/slideshows.{_format}", defaults={"_format"="json"}, options={"expose"=true}, name="newscoop_gimme_articles_slideshows_default_lang")
     * @Route("/articles/{number}/{language}/slideshows.{_format}", defaults={"_format"="json"}, options={"expose"=true}, name="newscoop_gimme_articles_slideshows")
     * @Method("GET")
     * @View(serializerGroups={"list"})
     *
     * @return array
     */
    public function getArticleSlideshowsAction(Request $request, $number, $language = null)
    {
        $em = $this->container->get('em');
        $publication = $this->get('newscoop.publication_service')->getPublication();
        $article = $em->getRepository('Newscoop\Entity\Article')
            ->getArticle($number, $request->request->get('language', $publication->getLanguage()->getCode()))
            ->getOneOrNullResult();

        if (!$article) {
            throw new NotFoundHttpException('Article was not found');
        }

        $criteria = new SlideshowCriteria();
        $criteria->articleNumber = $article->getNumber();
        $criteria->articleLanguage = $article->getLanguageId();
        $slideshows = $em->getRepository('Newscoop\Package\Package')->getListByCriteria($criteria);
        $paginator = $this->get('newscoop.paginator.paginator_service');
        $paginator->setUsedRouteParams(array('number' => $number, 'language' => $article->getLanguage()->getCode()));
        $slideshows = $paginator->paginate($slideshows);

        return $slideshows;
    }
}
