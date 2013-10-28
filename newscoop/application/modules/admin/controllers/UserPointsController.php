<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

use Newscoop\Annotations\Acl;

/**
 * UserPoints controller
 *
 * @Acl(resource="user-points", action="manage")
 */
class Admin_UserPointsController extends Zend_Controller_Action
{
    /** @var Newscoop\Services\UserPointsService */
    private $userPointsService;

    public function init()
    {
        $this->userPointsService = $this->_helper->service('user_points');
    }

    public function indexAction()
    {
        $all_actions = $this->userPointsService->findAll();
        $form = new Admin_Form_UserPoints($all_actions);

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {

            $values = $form->getValues();
            $this->userPointsService->updateEntries($values);
        }

        $this->view->form = $form;
    }
}
