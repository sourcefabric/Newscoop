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
use Newscoop\Exception\InvalidParametersException;

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
     * @Route("/author/{id}.{_format}", defaults={"_format"="json"}, options={"expose"=true})
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
     * @Route("/authors/{id}.{_format}", defaults={"_format"="json"}, options={"expose"=true}, requirements={"id" = "[\d]+"})
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
     * @Route("/authors/types.{_format}", defaults={"_format"="json"}, options={"expose"=true})
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
     * @Route("/authors/types/{id}.{_format}", defaults={"_format"="json"}, options={"expose"=true})
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
     * @Route("/search/authors.{_format}", defaults={"_format"="json"}, options={"expose"=true})
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
        $authors = $paginator->paginate($authors, array('distinct' => false));

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
     * @Route("/articles/{number}/{language}/authors.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Route("/authors/article/{number}/{language}.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     */
    public function getArticleAuthorsAction($number, $language)
    {
        $em = $this->container->get('em');
        $authors = $em->getRepository('Newscoop\Entity\ArticleAuthor')
            ->getArticleAuthors($number, $language);

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $authors = $paginator->paginate($authors, array('distinct' => false));

        return $authors;
    }

    /**
     * Get single article author
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404="Returned when the article author is not found"
     *     },
     *     output="\Newscoop\Entity\ArticleAuthor"
     * )
     *
     * @Route("/articles/{number}/{language}/authors/{id}.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Route("/authors/{id}/article/{number}/{language}.{_format}", defaults={"_format"="json"}, options={"expose"=true})
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
     * Update single article author
     *
     * To update currently assigned article author you need provide his old and new article
     * type, it can be done with special ```link``` header value:
     *
     *     </{api-endpoint}/authors/types/{authorTypeId}; rel="old-author-type">,</{api-endpoint}/authors/types/{authorTypeId}; rel="new-author-type">
     *
     *  example:
     *
     *  **To update artile author with number 7 and type 1 to type 2 you need to send**:
     *
     *      [POST] /articles/{number}/{language}/authors/7
     *
     *  **With header ```link``` and his value:**
     *
     *      </{api-endpoint}/authors/types/1; rel="old-author-type">,</{api-endpoint}/authors/types/2; rel="new-author-type">
     *
     * @ApiDoc(
     *     statusCodes={
     *         201="Returned when successful",
     *         404="Returned when the article author is not found",
     *         422="Invalid parameters"
     *     },
     *     output="\Newscoop\Entity\ArticleAuthor"
     * )
     *
     * @Route("/articles/{number}/{language}/authors/{authorId}.{_format}", defaults={"_format"="json"}, options={"expose"=true},  requirements={"authorId" = "[\d]+"})
     * @Method("POST|PATCH")
     * @View(serializerGroups={"list"}, statusCode=201)
     */
    public function updateArticleAuthorAction(Request $request, $number, $language, $authorId)
    {
        $em = $this->container->get('em');
        $links = $request->attributes->get('links', array());

        $oldAuthorType = null;
        $newAuthorType = null;
        foreach ($links as $key => $objectArray) {
            if ($objectArray['object'] instanceof \Newscoop\Entity\AuthorType && $objectArray['resourceType'] == 'old-author-type') {
                $oldAuthorType = $objectArray['object'];
            }

            if ($objectArray['object'] instanceof \Newscoop\Entity\AuthorType && $objectArray['resourceType'] == 'new-author-type') {
                $newAuthorType = $objectArray['object'];
            }
        }

        if (!$oldAuthorType || !$newAuthorType) {
            return new InvalidParametersException("\"old-author-type\" and \"new-author-type\" resources are required");
        }

        $articleAuthor = $em->getRepository('Newscoop\Entity\ArticleAuthor')
            ->getArticleAuthor($number, $language, $authorId, $oldAuthorType->getId())
            ->getOneOrNullResult();

        if (!$articleAuthor) {
            throw new NotFoundHttpException('Article Author was not found.');
        }

        $articleAuthor->setType($newAuthorType);
        $em->flush();
    }

    /**
     * Set article authors order
     *
     * New order must be send with form in this format
     *
     *     order={authorId}-{authorType},{authorId}-{authorType},{authorId}-{authorType},{authorId}-{authorType}
     *
     * example:
     *
     *     order=7-1,8-1,5-2,4-2
     *
     * All article author-author type pair must be send.
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         422="Invalid parameters"
     *     }
     * )
     *
     * @Route("/articles/{number}/{language}/authors/order.{_format}", defaults={"_format"="json"}, options={"expose"=true})
     * @Method("POST")
     * @View(serializerGroups={"list"}, statusCode=200)
     */
    public function setArticleAuthorsOrderAction(Request $request, $number, $language)
    {
        $em = $this->container->get('em');
        $authorService = $this->get('author');
        $articleAuthors = $em->getRepository('Newscoop\Entity\ArticleAuthor')
            ->getArticleAuthors($number, $language)
            ->getResult();

        $order = explode(',', $request->get('order'));

        if (count($order) != count($articleAuthors)) {
            throw new InvalidParametersException("Number of sorted article authors must be this same as number of authors");
        }

        $authorService->reorderAuthors($em, $articleAuthors, $order);
    }
}
