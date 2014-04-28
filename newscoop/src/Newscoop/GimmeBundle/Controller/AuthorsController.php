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

class AuthorsController extends FOSRestController
{
    /**
     * Get author
     *
     * @deprecated route author/{id}.{_format} will be removed in 4.4
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the author is not found",
     *         }
     *     },
     *     output="\Newscoop\Entity\Author"
     * )
     *
     * @Route("/author/{id}.{_format}", defaults={"_format"="json"})
     * @Route("/authors/{id}.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View()
     */
    public function getAuthorAction($id)
    {
        $em = $this->container->get('em');
        $author = $em->getRepository('Newscoop\Entity\Author')
            ->getAuthor($id)
            ->getOneOrNullResult();

        if (!$author) {
            throw new NotFoundHttpException('Author was not found.');
        }

        return $author;
    }

    /**
     * Get authors
     *
     * Get list of Author resources
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the authors are not found",
     *         }
     *     }
     * )
     *
     * @Route("/authors.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View()
     */
    public function getAuthorsAction()
    {}

    /**
     * Get authors types
     *
     * Get list of authors types
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the authors types are not found",
     *         }
     *     }
     * )
     *
     * @Route("/authors/types.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View()
     */
    public function getAuthorsTypesAction()
    {}

    /**
     * Search for authors
     *
     * Get list of authors for search query
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the authors are not found",
     *         }
     *     },
     *     filters={
     *          {"name"="query", "dataType"="string", "description"="search query"}
     *     },
     * )
     *
     * @Route("/authors/search.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View()
     */
    public function searchAuthorsAction()
    {}
}
