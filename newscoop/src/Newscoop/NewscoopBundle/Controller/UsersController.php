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

class UsersController extends Controller
{
    /**
     * @Route("admin/users/")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        return array();
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
        foreach ($users as $key => $user) {
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


            $pocessed[] = array(
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
                        'rel' => 'delete',
                        'href' => $zendRouter->assemble(array(
                            'module' => 'admin',
                            'controller' => 'user',
                            'action' => 'delete',
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
                    )
                )
            );
        }

        return new JsonResponse(array(
            'records' => $pocessed,
            'queryRecordCount' => count($users->items),
            'totalRecordCount'=> $users->count
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
            $criteria->query = $queries['search'];
        }

        return $criteria;
    }
}
