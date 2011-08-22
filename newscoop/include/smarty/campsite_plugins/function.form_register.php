<?php

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

    $request = $controller->getRequest();
    if ($request->isPost()) {
        if ($formRegister->isValid($request->getPost())) { // handle confirm form
            if ($request->has('username') && $formConfirm->isValid($request->getPost())) {
                $values = $formConfirm->getValues();
                $userService->create($values);
                $controller->_redirect($controller->getRequest()->getRequestUri());
                // todo add info message
            } elseif (!$request->has('username')) { // init confirm form
                $values = $formRegister->getValues();
                $values['username'] = $userService->generateUsername($values['first_name'], $values['last_name']);
                $formConfirm->setDefaults($values + array(
                    'terms_of_services' => 'Terms of services text',
                ));
            }

            echo $formConfirm;
            return;
        }
    }

    echo $formRegister;
}
