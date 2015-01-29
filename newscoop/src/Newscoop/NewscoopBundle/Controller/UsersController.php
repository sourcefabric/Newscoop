<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Newscoop\Entity\User;

class UsersController extends Controller
{
    /**
     * @Route("admin/users/")
     */
    public function indexAction(Request $request)
    {
        $userService = $this->get('user');
        $blogService = $this->get('blog');
        $user = $userService->getCurrentUser();
        if ($blogService->isBlogger($user)) {
            throw new AccessDeniedException();
        }

        $em = $this->get('em');

        $registered = $userService->countBy(array('status' => User::STATUS_ACTIVE));
        $pending = $userService->countBy(array('status' => User::STATUS_INACTIVE));
        $deleted = $userService->countBy(array('status' => User::STATUS_DELETED));
        $active = $em->getRepository('Newscoop\Entity\User')->getLatelyLoggedInUsers(14, true)->getSingleScalarResult();
        $userGroups = $em->getRepository('Newscoop\Entity\User\Group')->findAll();

        return $this->render(
            'NewscoopNewscoopBundle:Users:index.html.twig',
            array(
                'registered' => $registered,
                'pending' => $pending,
                'deleted' => $deleted,
                'active' => $active,
                'userGroups' => $userGroups,
                'active_logins' => array(
                    'newscoop' => $em->getRepository('Newscoop\Entity\User')->getNewscoopLoginCount(),
                    'external' => $em->getRepository('Newscoop\Entity\User')->getExternalLoginCount(),
                )
            )
        );
    }

    /**
     * @Route("admin/users/load/", options={"expose"=true})
     * @Template()
     */
    public function loadUsersAction(Request $request)
    {
        $em = $this->get('em');
        $zendRouter = $this->get('zend_router');
        $userService = $this->get('user');
        $cacheService = $this->get('newscoop.cache');

        $criteria = $userService->extractCriteriaFromRequest($request);
        $criteria->is_public = null;
        $registered = $userService->countBy(array('status' => User::STATUS_ACTIVE));
        $pending = $userService->countBy(array('status' => User::STATUS_INACTIVE));

        $cacheKey = array('users__'.md5(serialize($criteria)), $registered, $pending);

        if ($cacheService->contains($cacheKey)) {
            $responseArray =  $cacheService->fetch($cacheKey);
        } else {
            $users = $em->getRepository('Newscoop\Entity\User')->getListByCriteria($criteria);

            $pocessed = array();
            foreach ($users as $user) {
                $pocessed[] = $this->processUser($user, $zendRouter);
            }

            $responseArray = array(
                'records' => $pocessed,
                'queryRecordCount' => $users->count,
                'totalRecordCount'=> count($users->items)
            );

            $cacheService->save($cacheKey, $responseArray);
        }

        return new JsonResponse($responseArray);
    }

    private function processUser($user, $zendRouter)
    {
        switch ($user->getStatus()) {
            case '0':
                $status = 'Inactive';
                break;
            case '1':
                $status = 'Active';
                break;
            case '2':
                $status = 'Banned';
                break;
            case '3':
                $status = 'Deleted';
                break;
        }

        $types = array();
        foreach ($user->getUserTypes() as $type) {
            $types[] = $type->getName();
        }

        return array(
            'id' => $user->getId(),
            'email' => $user->getEmail(),
            'username' => $user->getUsername(),
            'first_name' => $user->getFirstName(),
            'last_name' => $user->getLastName(),
            'created' => $user->getCreated(),
            'updated' => $user->getUpdated(),
            'is_verified' => (bool) $user->getAttribute(\Newscoop\Entity\UserAttribute::IS_VERIFIED),
            'status' => $status,
            'types' => implode(', ', $types),
            'links' => array(
                array(
                    'rel' => 'edit',
                    'href' => $zendRouter->assemble(array(
                        'module' => 'admin',
                        'controller' => 'user',
                        'action' => 'edit',
                        'user' => $user->getId(),
                    ), 'default', true)
                ),
                array(
                    'rel' => 'token',
                    'href' => $zendRouter->assemble(array(
                        'module' => 'admin',
                        'controller' => 'user',
                        'action' => 'send-confirm-email',
                        'user' => $user->getId(),
                    ), 'default', true)
                ),
                array(
                    'rel' => 'rename',
                    'href' => $zendRouter->assemble(array(
                        'module' => 'admin',
                        'controller' => 'user',
                        'action' => 'rename',
                        'user' => $user->getId(),
                    ), 'default', true),
                ),
                array(
                    'rel' => 'delete',
                    'href' => $zendRouter->assemble(array(
                        'module' => 'admin',
                        'controller' => 'user',
                        'action' => 'delete',
                        'user' => $user->getId(),
                    ), 'default', true)
                ),
            )
        );
    }
}
