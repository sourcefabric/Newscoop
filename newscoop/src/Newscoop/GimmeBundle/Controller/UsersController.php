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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\Response;
use Doctrine\ORM\EntityNotFoundException;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class UsersController extends FOSRestController
{
    /**
     * @Route("/users.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     */
    public function getUsersAction(Request $request)
    {
        $em = $this->container->get('em');

        $users = $em->getRepository('Newscoop\Entity\User')
            ->getActiveUsers();

        $paginator = $this->get('newscoop.paginator.paginator_service');
        $users = $paginator->paginate($users, array(
            'distinct' => false
        ));

        return $users;
    }

    /**
     * @Route("/users/{id}.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     */
    public function getUserAction(Request $request, $id)
    {
        $em = $this->container->get('em');

        $user = $em->getRepository('Newscoop\Entity\User')
            ->getOneActiveUser($id)
            ->getOneOrNullResult();

        if (!$user) {
            throw new NotFoundHttpException('Result was not found.');
        }

        return $user;
    }

    /**
     * Log in user
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         403="Returned when wrong password given.",
     *         404="Returned when the user is not found",
     *     }
     * )
     *
     * @Route("/users/login.{_format}", defaults={"_format"="json"})
     * @Method("POST")
     * @View(statusCode=200)
     *
     * @return array
     */
    public function loginAction(Request $request)
    {
        $request = $this->getRequest();
        $username = $request->get('username');
        $password = $request->get('password');
        $em = $this->container->get('em');
        $passwordEncoder = $this->container->get('newscoop_newscoop.password_encoder');
        $userService = $this->container->get('user');
        $user = $em->getRepository('Newscoop\Entity\User')
            ->findOneBy(array(
                'username' => $username
            ));

        if (!$user) {
            $user = $user = $em->getRepository('Newscoop\Entity\User')
                ->findOneBy(array(
                    'email' => $username
                ));
        }

        if (!$user instanceof \Newscoop\Entity\User) {
            throw new NotFoundHttpException("User not found");
        }

        if (!$passwordEncoder->isPasswordValid($user->getPassword(), $password, $user->getSalt())) {
            throw new AccessDeniedException("Wrong password");
        }

        $userService->loginUser($user);

        return array(
            'success' => true,
            'user' => $user
        );
    }

    /**
     * Logout user
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful"
     *     }
     * )
     *
     * @Route("/users/logout.{_format}", defaults={"_format"="json"})
     * @Method("POST")
     * @View(statusCode=200)
     */
    public function logoutAction(Request $request)
    {
        $userService = $this->container->get('user');
        $userService->logoutUser();
    }

    /**
     * Register user
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404="Returned when the user is not found",
     *         409="Returned when user is already registered",
     *     }
     * )
     *
     * @Route("/users/register.{_format}", defaults={"_format"="json"})
     * @Method("POST")
     * @View()
     *
     * @return Response|Exception
     */
    public function registerAction(Request $request)
    {
        $email = $request->get('email');
        $userService = $this->container->get('user');
        $emailService = $this->container->get('sendemail');
        $zendRouter = $this->container->get('zend_router');
        $response = new Response();
        $users = $userService->findBy(array(
            'email' => $email,
        ));

        if (count($users) > 0) {
            $user = array_pop($users);
        } else {
            if (!$users) {
                throw new EntityNotFoundException('Result was not found.');
            }

            $user = $userService->createPending($email);
        }

        if (!$user->isPending()) {
            $response->setStatusCode(409);

            return $response;
        } else {
            $emailService->sendConfirmationToken($user, $this->getRequest()->getHost());
            $response->setStatusCode(200);
            $response->headers->set(
                'X-Location',
                $zendRouter->assemble(array('controller' => 'register', 'action' => 'after'))
            );

            return $response;
        }
    }

    /**
     * Restore user password
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404="Returned when email is not found",
     *     }
     * )
     *
     * @Route("/users/restore-password.{_format}", defaults={"_format"="json"})
     * @Method("POST")
     * @View()
     *
     * @return Response
     */
    public function restorePasswordAction(Request $request)
    {
        $response = new Response();
        $zendRouter = $this->container->get('zend_router');
        $user = $this->container->get('user')->findOneBy(array(
            'email' => $request->get('email'),
        ));

        if (!empty($user) && $user->isActive()) {
            $this->container->get('sendemail')->sendPasswordRestoreToken($user, $this->getRequest()->getHost());
            $response->setStatusCode(200);
            $response->headers->set(
                'X-Location',
                $zendRouter->assemble(array('controller' => 'auth', 'action' => 'password-restore-after'))
            );

            return $response;
        } else if (empty($user)) {
            $response->setStatusCode(404);

            return $response;
        }
    }
}