<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
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

class PublicationsController extends FOSRestController
{
    /**
     * Get Publications
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when publications found",
     *         404={
     *           "Returned when publications are not found",
     *         }
     *     }
     * )
     *
     * @Route("/publications.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     */
    public function getPublicationsAction(Request $request)
    {
        $em = $this->container->get('em');

        $publications = $em->getRepository('Newscoop\Entity\Publication')
            ->getPublications();

        if (!$publications) {
            throw new NotFoundHttpException('Result was not found.');
        }

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $publications = $paginator->paginate($publications, array(
            'distinct' => false
        ));

        return $publications;
    }
}
