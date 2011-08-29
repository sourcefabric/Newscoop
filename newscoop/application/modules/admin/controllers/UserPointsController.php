<?php
/**
 * @package Newscoop
 * @copyright 2011 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

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
        camp_load_translation_strings('api');
        camp_load_translation_strings('users');

        $this->userPointsService = $this->_helper->service('user_points');
    }

    public function indexAction()
    {
        $form = new Admin_Form_UserPoints();

        $points_settings = $this->userPointsService->getPointOptions();
        $form->setDefaults($points_settings);

        $request = $this->getRequest();
        if ($request->isPost() && $form->isValid($request->getPost())) {

            $values = $form->getValues();
            $this->userPointsService->updateEntries($values);
        }

        $this->view->form = $form;
    }
}