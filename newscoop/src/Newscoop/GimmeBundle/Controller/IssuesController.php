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

class IssuesController extends FOSRestController
{
    /**
     * Get Issues
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when issues found",
     *         404={
     *           "Returned when issues are not found",
     *         }
     *     },
     *     parameters={
     *         {"name"="publication", "dataType"="integer", "required"=true, "description"="Publication id"}
     *     }
     * )
     *
     * @Route("/issues.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     */
    public function getIssuesAction(Request $request)
    {
        $em = $this->container->get('em');
        $publication = $this->get('newscoop_newscoop.publication_service')->getPublication()->getId();

        $issues = $em->getRepository('Newscoop\Entity\Issue')
            ->getLatestByPublication($request->query->get('publication', $publication), false);

        if (!$issues) {
            throw new NotFoundHttpException('Result was not found.');
        }

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $issues = $paginator->paginate($issues, array(
            'distinct' => false
        ));

        return $issues;
    }
}
