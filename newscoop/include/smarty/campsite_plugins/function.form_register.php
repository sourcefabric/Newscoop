<?php

use Newscoop\EventDispatcher\Events\GenericEvent;

// todo fix autoloading error
require_once APPLICATION_PATH . '/forms/Register.php';
require_once APPLICATION_PATH . '/forms/Confirm.php';

/**
 * Render widget
 *
 * @param array $params
 * @param array $smarty
 * @return void
 */
function smarty_function_form_register($params, $smarty)
{
    global $controller;

    $formRegister = new Form_Register();
    $formConfirm = new Form_Confirm();
    $userService = $controller->getHelper('service')->getService('user');
    $session = new Zend_Session_Namespace('Form_Register');

    $request = $controller->getRequest();
    if ($request->isPost()) {
        $request->setPost('password', $session->password);
        if ($formRegister->isValid($request->getPost())) { // handle confirm form
            if ($request->has('username') && $formConfirm->isValid($request->getPost())) {
                $values = $formConfirm->getValues();
                $values['password'] = empty($values['password_change']) ? $session->password : $values['password_change'];
                $user = $userService->create($values);

                $dispatcher = $controller->getHelper('service')->getService('dispatcher');
                $dispatcher->dispatch('user.register', new GenericEvent($smarty, array(
                    'user' => $user,
                )));

                $controller->getHelper('redirector')->gotoSimple('index', 'index', 'default');
            } elseif (!$request->has('username')) { // init confirm form
                $values = $formRegister->getValues();
                $session->password = $values['password'];
                $values['username'] = $userService->generateUsername($values['first_name'], $values['last_name']);
                $formConfirm->setDefaults($values + array(
                    'terms_of_services' => 'Terms of services text',
                ));
            }

            echo $formConfirm;
            return;
        }
    } else {
        $session->password = null;
    }

    echo $formRegister;
}
