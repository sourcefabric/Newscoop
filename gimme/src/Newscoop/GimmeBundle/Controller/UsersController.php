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

class UsersController extends FOSRestController
{
    /**
     * @Route("/users.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View()
     */
    public function getUsersAction(Request $request)
    {
        $em = $this->container->get('em');
        $serializer = $this->get('serializer');
        $serializer->setGroups(array('list'));

        $users = $em->getRepository('Newscoop\Entity\User')
            ->getActiveUsers();

        if (!$users) {
            throw new NotFoundHttpException('Result was not found.');
        }

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $users = $paginator->paginate($users, array(
            'distinct' => false
        ));

        return $users;
    }

    /**
     * @Route("/users/{id}.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View()
     */
    public function getUserAction(Request $request, $id)
    {
        $em = $this->container->get('em');
        $serializer = $this->get('serializer');
        $serializer->setGroups(array('list'));

        $user = $em->getRepository('Newscoop\Entity\User')
            ->getOneActiveUser($id)
            ->getOneOrNullResult();

        if (!$user) {
            throw new NotFoundHttpException('Result was not found.');
        }

        return $user;
    }



}