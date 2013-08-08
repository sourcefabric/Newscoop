<?php
$auth = Zend_Auth::getInstance();
if ($auth->hasIdentity()) { // ignore for logged user
    return;
}

$f_user_name = Input::Get('f_user_name');
$f_password = Input::Get('f_password');
$f_login_language = Input::Get('f_login_language', 'string', 'en');
$f_is_encrypted = Input::Get('f_is_encrypted', 'int', '1');
$f_captcha_code = Input::Get('f_captcha_code', 'string', '', true);

$xorkey = camp_session_get('xorkey', '');
if (trim($xorkey) == '') {
    return 'xorkey';
}

if (!Input::isValid()) {
    return 'userpass';
}

$t_password = rc4($xorkey, base64ToText($f_password));

//
// Valid logins
//
// if user valid, password valid, encrypted, no CAPTCHA -> login
// if user valid, password valid, encrypted, CAPTCHA valid -> login
// if user valid, password valid, not encrypted, no CAPTCHA -> login, upgrade
// if user valid, password valid, not encrypted, CAPTCHA valid -> login, upgrade

//
// Invalid logins
//
// CAPTCHA invalid -> captcha
// If user not valid -> userpass
// password invalid, encrypted -> upgrade
// password invalid, not encrypted -> userpass

if (!$auth->hasIdentity()) {
    $adapter = $controller->_helper->service('auth.adapter');
    $adapter->setUsername($f_user_name)->setPassword($t_password)->setAdmin(TRUE);
    $result = $auth->authenticate($adapter);
    if ($result->getCode() != Zend_Auth_Result::SUCCESS) {
        LoginAttempts::RecordLoginAttempt();
        return 'userpass';
    }
}

$validateCaptcha = LoginAttempts::MaxLoginAttemptsExceeded();

//
// Valid login section
//
if ($auth->hasIdentity()) {
    if (!$validateCaptcha || PhpCaptcha::Validate($f_captcha_code, true)) {
        // if user valid, password valid, encrypted, no CAPTCHA -> login
        // if user valid, password valid, encrypted, CAPTCHA valid -> login

        LoginAttempts::ClearLoginAttemptsForIp();
        Article::UnlockByUser($auth->getIdentity());

        // next action GET/POST detection
        if (!empty($_POST['_next']) && $_POST['_next'] == 'get') {
            $_SERVER['REQUEST_METHOD'] = 'GET';
            $method = 'redirect';
        }

        // fix zend csrf protection
        foreach ($_POST as $key => $value) {
            if (strpos($key, 'csrf') === FALSE) {
                continue;
            }
            $form = new Zend_Form;
            $form->addElement('hash', $key);
            $element = $form->getElement($key);
            $session = $element->getSession();
            $request = $this->getRequest();

            $session->hash = $element->getHash();
            $_POST['csrf'] = $element->getHash();
            $request->setPost($key, $element->getHash());
        }

        // fix legacy cs
        if (!empty($_POST[SecurityToken::SECURITY_TOKEN])) {
            $_POST[SecurityToken::SECURITY_TOKEN] = SecurityToken::GetToken();
        }

        // fix uri
        $_SERVER['REQUEST_URI'] = $this->getRequest()->getRequestUri();

        // reset view
        $this->view->legacy = NULL;
        $this->_helper->layout->enableLayout();

        // redirect/forward
        if (!empty($_POST['_next']) && $_POST['_next'] == 'post') { // forward POST request
            $this->_forward($this->_getParam('action'), $this->_getParam('controller'), 'admin');
        } else { // redirect GET request
            setcookie("TOL_Language", $f_login_language);
            setcookie('NO_CACHE', '1', NULL, '/');
            if (array_key_exists(SecurityToken::SECURITY_TOKEN, $_GET)) {
                $_SERVER['REQUEST_URI'] .= sprintf('&%s=%s', SecurityToken::SECURITY_TOKEN, SecurityToken::GetToken());
            }

            $this->_redirect($_SERVER['REQUEST_URI'], array(
                'prependBase' => false,
            ));
        }

        return;
    }
}

//
// Invalid logins start here.
//

// Record the attempt
LoginAttempts::RecordLoginAttempt();

// CAPTCHA invalid -> captcha login page
if ($validateCaptcha && !PhpCaptcha::Validate($f_captcha_code, true)) {
    return 'captcha';
}

// Everything else
return 'userpass';
