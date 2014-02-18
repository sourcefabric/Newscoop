<?php
/**
 * @package Newscoop\Gimme
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
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
use Symfony\Component\Security\Core\Authentication\Token\AnonymousToken;

/**
 * Users Rest API Controller
 */
class UsersController extends FOSRestController
{
    /**
     * Get all users
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *     }
     * )
     *
     * @Route("/users.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     *
     * @return array
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
     * Get user by given id
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404="Returned when the user is not found",
     *     },
     *     parameters={
     *         {"name"="id", "dataType"="integer", "required"=true, "description"="User id"},
     *         {"name"="image_type", "dataType"="string", "required"=false, "description"="User image specification (e.g. crop, fit)"},
     *         {"name"="image_width", "dataType"="integer", "required"=false, "description"="User image width"},
     *         {"name"="image_height", "dataType"="integer", "required"=false, "description"="User image height"},
     *     },
     *     output="\Newscoop\Entity\User"
     * )
     *
     * @Route("/users/{id}.{_format}", defaults={"_format"="json"})
     * @Method("GET")
     * @View(serializerGroups={"list"})
     *
     * @return array
     */
    public function getUserAction(Request $request, $id)
    {
        $em = $this->container->get('em');
        $imageType = $request->get('image_type');
        $imageHeight = $request->get('image_height');
        $imageWidth = $request->get('image_width');

        $user = $em->getRepository('Newscoop\Entity\User')
            ->getOneActiveUser($id)
            ->getOneOrNullResult();

        if (!$user) {
            throw new NotFoundHttpException('Result was not found.');
        }

        $metaUser = new \MetaUser($user);
        $user->setImage($metaUser->image($imageWidth ?: 80, $imageHeight ?: 80, $imageType ?: 'crop'));

        return $user;
    }

    /**
     * Log in user
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         403="Returned when wrong password given",
     *         404="Returned when the user is not found",
     *         400="Returned when invalid arguments",
     *     },
     *     parameters={
     *         {"name"="username", "dataType"="string", "required"=true, "description"="Username or email"},
     *         {"name"="password", "dataType"="string", "required"=true, "description"="User password"}
     *     },
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
        $zendRouter = $this->container->get('zend_router');
        $userService = $this->container->get('user');
        $em = $this->container->get('em');
        $username = $request->get('username');
        $password = $request->get('password');
        $response = new Response();
        if (!$username || !$password) {
            $response->setStatusCode(400);

            return $response;
        }

        $passwordEncoder = $this->container->get('newscoop_newscoop.password_encoder');
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
            $response->setStatusCode(403);

            return $response;
        }

        $token = $userService->loginUser($user);
        $session = $request->getSession();
        $session->set('_security_frontend_area', serialize($token));

        $zendAuth = \Zend_Auth::getInstance();
        $authAdapter = $this->get('auth.adapter');
        $authAdapter->setEmail($user->getEmail())->setPassword($request->request->get('password'));
        $zendAuth->authenticate($authAdapter);

        $response->setStatusCode(200);
        $response->headers->set(
            'X-Location',
            $this->generateUrl('newscoop_gimme_users_getuser', array(
                'id' => $user->getId()
            ), true)
        );

        return $response;
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
        $token = new AnonymousToken(null, 'anon.');
        $response = new Response();
        $session = $request->getSession();
        $request->getSession()->invalidate();
        $session->set('_security_frontend_area', serialize($token));
        $this->get('security.context')->setToken($token);
        $zendAuth = \Zend_Auth::getInstance();
        $zendAuth->clearIdentity();

        return $response;
    }

    /**
     * Register user
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         409="Returned when user is already registered",
     *     },
     *     parameters={
     *         {"name"="email", "dataType"="string", "required"=true, "description"="User email"}
     *     },
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
        $emailService = $this->container->get('email');
        $zendRouter = $this->container->get('zend_router');
        $publicationMetadata = $request->attributes->get('_newscoop_publication_metadata');
        $response = new Response();
        $users = $userService->findBy(array(
            'email' => $email,
        ));

        if (count($users) > 0) {
            $user = array_pop($users);
        } else {
            $user = $userService->createPending($email);
        }

        if (!$user->isPending()) {
            $response->setStatusCode(409);
        } else {
            $emailService->sendConfirmationToken($user);
            $response->setStatusCode(200);
            $response->headers->set(
                'X-Location',
                $request->getScheme().'://'.$publicationMetadata['alias']['name'].$zendRouter->assemble(array('controller' => 'register', 'action' => 'after'))
            );
        }

        return $response;
    }

    /**
     * Restore user password
     *
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful",
     *         404="Returned when email is not found",
     *     },
     *     parameters={
     *         {"name"="email", "dataType"="string", "required"=true, "description"="User email"}
     *     },
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
        $publicationMetadata = $request->attributes->get('_newscoop_publication_metadata');
        $user = $this->container->get('user')->findOneBy(array(
            'email' => $request->get('email'),
        ));

        if (!empty($user) && $user->isActive()) {
            $this->container->get('email')->sendPasswordRestoreToken($user);
            $response->setStatusCode(200);
            $response->headers->set(
                'X-Location',
                $request->getScheme().'://'.$publicationMetadata['alias']['name'].$zendRouter->assemble(array('controller' => 'auth', 'action' => 'password-restore-after'))
            );

            return $response;
        }

        $response->setStatusCode(404);

        return $response;
    }
}