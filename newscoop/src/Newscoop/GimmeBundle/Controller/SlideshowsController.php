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

class SlideshowsController extends FOSRestController
{
    /**
     * Get slideshow items
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
     * @Route("/slideshows/{id}.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"details"})
     */
    public function getSlideshowItemsAction(Request $request, $id)
    {
        $em = $this->container->get('em');
        $paginatorService = $this->get('newscoop.paginator.paginator_service');
        $paginatorService->setUsedRouteParams(array('id' => $id));

        $package = $em->getRepository('Newscoop\Package\Package')
            ->findOneById($id);

        if (!$package) {
            throw new NotFoundHttpException('Result was not found.');
        }

        $items = $em->getRepository('Newscoop\Package\Item')
            ->getAllForPackage($id);

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $items = $paginator->paginate($items, array(
            'distinct' => false
        ));

        $allItems = array_merge(array(
            'id' => $package->getId(),
            'title' => $package->getHeadline(),
            'summary' => $package->getDescription(),
        ), $items);

        return $allItems;
    }
}
