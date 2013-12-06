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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ArticleTypesController extends FOSRestController
{
    /**
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
     * @Route("/articleTypes/{name}.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"details"})
     */
    public function getArticleTypeAction(Request $request, $name)
    {
        $em = $this->container->get('em');
        $type = $em->getRepository('Newscoop\Entity\ArticleType')->findOneByName($name);
        $articleTypesFields = $em->getRepository('Newscoop\Entity\ArticleTypeField')->getFieldsForType($type)->getResult();

        $allItems = array(
            'name' => $type->getName(),
            'fields' => $articleTypesFields
        );

        return $allItems;
    }
}