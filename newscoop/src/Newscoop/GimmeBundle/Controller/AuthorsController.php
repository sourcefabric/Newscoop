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
     * This route will be removed in 4.4
     *
     * @deprecated
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
     * Get author
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
     * @Route("/authors/{id}.{_format}", defaults={"_format"="json"}, requirements={"id" = "^[0-9]"})
     * @Method("GET")
     * @View()
     */
    public function getAuthorByIdAction($id)
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
     * @View(serializerGroups={"list"})
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
     * @View(serializerGroups={"list"})
     */
    public function getAuthorsTypesAction()
    {
        $em = $this->container->get('em');
        $authorsTypes = $em->getRepository('Newscoop\Entity\AuthorType')
            ->getAuthorsTypes();

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $authorsTypes = $paginator->paginate($authorsTypes);

        return $authorsTypes;
    }

    /**
     * Get author type
     *
     * Get single author type
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the author type is not found",
     *         }
     *     },
     *     output="\Newscoop\Entity\AuthorType"
     * )
     *
     * @Route("/authors/types/{id}.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"details"})
     */
    public function getAuthorTypeAction($id)
    {
        $em = $this->container->get('em');
        $authorType = $em->getRepository('Newscoop\Entity\AuthorType')
            ->getAuthorType($id)
            ->getOneOrNullResult();

        if (!$authorType) {
            throw new NotFoundHttpException('Author Type was not found.');
        }

        return $authorType;
    }

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
     * @Route("/search/authors.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     */
    public function searchAuthorsAction(Request $request)
    {
        $em = $this->container->get('em');
        $query = $request->query->get('query', '');
        $authors = $em->getRepository('Newscoop\Entity\Author')
            ->searchAuthors($query);

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $authors = $paginator->paginate($authors);

        return $authors;
    }

    /**
     * Get article authors
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
     * @Route("/articles/{number}/{language}/authors.{_format}", defaults={"_format"="json"})
     * @Route("/authors/article/{number}/{language}.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View()
     */
    public function getArticleAuthorsAction($number, $language, $id)
    {}

    /**
     * Get single article author
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the article author is not found",
     *         }
     *     },
     *     output="\Newscoop\Entity\ArticleAuthor"
     * )
     *
     * @Route("/articles/{number}/{language}/authors/{id}.{_format}", defaults={"_format"="json"})
     * @Route("/authors/{id}/article/{number}/{language}.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     */
    public function getArticleAuthorAction($number, $language, $id)
    {
        $em = $this->container->get('em');
        $articleAuthor = $em->getRepository('Newscoop\Entity\ArticleAuthor')
            ->getArticleAuthor($number, $language, $id)
            ->getOneOrNullResult();

        if (!$articleAuthor) {
            throw new NotFoundHttpException('Article Author was not found.');
        }

        return $articleAuthor;
    }

    /**
     * Set article authors order
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the article authors are not found",
     *         },
     *         400="Returned when data are invalid"
     *     }
     * )
     *
     * @Route("/articles/{number}/{language}/authors/order.{_format}", defaults={"_format"="json"})
     * @Method("POST|PATCH")
     * @View()
     */
    public function setArticleAuthorsOrderAction($number, $language, $id)
    {}
}
