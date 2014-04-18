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

class ArticleTypesController extends FOSRestController
{
    /**
     * Get Article Types
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when article types found",
     *         404={
     *           "Returned when article types are not found",
     *         }
     *     }
     * )
     *
     * @Route("/articleTypes.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     */
    public function getArticleTypesAction(Request $request)
    {
        $em = $this->container->get('em');

        $articleTypes = $em->getRepository('Newscoop\Entity\ArticleType')->getAllTypes();

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $articleTypes = $paginator->paginate($articleTypes, array(
            'distinct' => false
        ));

        return $articleTypes;
    }

    /**
     * Get Article Type
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404={
     *           "Returned when the article type is not found",
     *         }
     *     },
     *     parameters={
     *         {"name"="name", "dataType"="string", "required"=true, "description"="Article Type name"}
     *     },
     *     output="\Newscoop\Entity\ArticleTypeField"
     * )
     *
     * @Route("/articleTypes/{name}.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"details"})
     */
    public function getArticleTypeAction(Request $request, $name)
    {
        $em = $this->container->get('em');
        $type = $em->getRepository('Newscoop\Entity\ArticleType')->findOneByName($name);

        if (!$type) {
            throw new NotFoundHttpException('Article Type was not found');
        }

        $articleTypesFields = $em->getRepository('Newscoop\Entity\ArticleTypeField')
            ->getFieldsForType($type)
            ->getResult();

        $allItems = array(
            'name' => $type->getName(),
            'fields' => $articleTypesFields
        );

        return $allItems;
    }
}
