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
use Newscoop\Entity\User;

class UsersController extends Controller
{
    /**
     * @Route("admin/users/")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $userService = $this->get('user');
        $em = $this->get('em');
        $registered = $userService->countBy(array('status' => User::STATUS_ACTIVE));
        $pending = $userService->countBy(array('status' => User::STATUS_INACTIVE));
        $deleted = $userService->countBy(array('status' => User::STATUS_DELETED));
        $active = $em->getRepository('Newscoop\Entity\User')->getLatelyLoggedInUsers(14, true)->getSingleScalarResult();

        return array(
            'registered' => $registered,
            'pending' => $pending,
            'deleted' => $deleted,
            'active' => $active
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

        $criteria = $this->processRequest($request);
        $criteria->is_public = null;
        $users = $em->getRepository('Newscoop\Entity\User')->getListByCriteria($criteria);

        $pocessed = array();
        foreach ($users as $user) {
            $pocessed[] = $this->processUser($user, $zendRouter);
        }

        return new JsonResponse(array(
            'records' => $pocessed,
            'queryRecordCount' => $users->count,
            'totalRecordCount'=> count($users->items)
        ));
    }

    private function processRequest($request)
    {
        $criteria = new \Newscoop\User\UserCriteria();

        if ($request->query->has('sorts')) {
            foreach ($request->get('sorts') as $key => $value) {
                $criteria->orderBy[$key] = $value == '-1' ? 'desc' : 'asc';
            }
        }

        if ($request->query->has('queries')) {
            $queries = $request->query->get('queries');

            if (array_key_exists('search', $queries)) {
                $criteria->query = $queries['search'];
            }

            if (array_key_exists('filter', $queries)) {
                if ($queries['filter'] == 'active') {
                    $criteria->lastLoginDays = 30;
                }

                if ($queries['filter'] == 'registered') {
                    $criteria->status = User::STATUS_ACTIVE;
                }

                if ($queries['filter'] == 'pending') {
                    $criteria->status = User::STATUS_INACTIVE;
                }

                if ($queries['filter'] == 'deleted') {
                    $criteria->status = User::STATUS_DELETED;
                }
            }
        }

        $criteria->maxResults = $request->query->get('perPage', 10);
        if ($request->query->has('offset')) {
            $criteria->firstResult = $request->query->get('offset');
        }

        return $criteria;
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
