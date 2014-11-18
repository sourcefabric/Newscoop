<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Rafał Muszyński <rafal.muszynski@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Newscoop\NewscoopBundle\Form\Type\PasswordRecoveryType;
use Newscoop\NewscoopBundle\Form\Type\PasswordCheckType;
use Newscoop\Entity\User;

class PasswordRecoveryController extends Controller
{
    /**
     * @Route("/admin/password-recovery")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $translator = $this->container->get('translator');
        $preferencesService = $this->container->get('system_preferences_service');
        $sent = false;
        $error = '';
        $disabled = false;
        $form = $this->container->get('form.factory')->create(new PasswordRecoveryType(), array(), array());

        if ($preferencesService->get("PasswordRecovery") == 'N') {
            $disabled = true;
        } else {
            if ($request->isMethod('POST')) {
                $form->handleRequest($request);
                if ($form->isValid()) {
                    $data = $form->getData();
                    $user = $this->container->get('user')->findOneBy(array(
                        'email' => $data['email'],
                        'is_admin' => true
                    ));

                    if ($user != null && is_numeric($user->getId()) && $user->getId() > 0) {
                        try {
                            $token = $this->setPasswordResetToken($user);
                            $this->sendToken($data['email'], $token);
                            $sent = true;
                        } catch (\Exception $exception) {
                            $error = $translator->trans("Fatal error occured. Please try again later.", array(), 'home');
                        }
                    } else {
                        $error = $translator->trans("No user is registered with this email.", array(), 'home');
                    }
                }
            }
        }

        return array(
            'form' => $form->createView(),
            'sent' => $sent,
            'disabled' => $disabled,
            'error' => $error
        );
    }

    /**
     * @Route("/admin/password-check-token")
     * @Template("NewscoopNewscoopBundle:PasswordRecovery:check.html.twig")
     */
    public function checkTokenAction(Request $request)
    {
        $translator = $this->container->get('translator');
        $preferencesService = $this->container->get('system_preferences_service');
        $email = $request->get('email');
        $token = $request->get('token');
        $noPassword = false;
        $success = false;
        $error = '';
        $form = $this->container->get('form.factory')->create(new PasswordCheckType(), array(), array());

        if ($preferencesService->get("PasswordRecovery") == 'N') {
            $noPassword = false;
            $error = $translator->trans('Password recovery is disabled.', array(), 'home');
        } elseif (!stristr($email, "@") == false && strlen($token) > 4) {
            $noPassword = true;
            $user = $this->container->get('user')->findOneBy(array(
                'email' => $email,
                'is_admin' => true
            ));

            if ($user != null) {
                $tokenGenerated = (int) substr($token, -10);

                // valid for 48 hours
                if ($user->getResetToken() == $token && (time() - $tokenGenerated < 48 * 3600)) {
                    if ($request->isMethod('POST')) {
                        $form->handleRequest($request);
                        if ($form->isValid()) {
                            $data = $form->getData();
                            $newPassword = $data['password'];
                            if (strlen($newPassword) >= 6) {
                                $this->setPassword($user, $newPassword);
                                $success = true;
                                $noPassword = false;
                            } else {
                                $error = $translator->trans('Your new password must have at least 6 characters.', array(), 'home');
                            }
                        }
                    }
                } else {
                    $noPassword = false;
                    $error = $translator->trans('This link is not valid.', array(), 'home');
                }
            } else {
                $noPassword = false;
                $error = $translator->trans('Bad input parameters.', array(), 'home');
            }
        } else {
            $noPassword = false;
            $error = $translator->trans('Bad input parameters.', array(), 'home');
        }

        return array(
            'form' => $form->createView(),
            'error' => $error,
            'success' => $success,
            'noPassword' => $noPassword,
            'email' => $email,
            'token' => $token
        );
    }

    /**
     * Generates token for given user
     *
     * @param  Newscoop\Entity\User $user User
     *
     * @return string
     */
    public function setPasswordResetToken(User $user)
    {   
        $token = sha1(uniqid('', TRUE)) . (string) time();
        $em = $this->container->get('em');
        $queryBuilder = $em->createQueryBuilder();
        $resetToken = $queryBuilder->update('Newscoop\Entity\User', 'u')
            ->set('u.resetToken', ':token')
            ->where('u = :user')
            ->setParameters(array(
                'token' => $token,
                'user' => $user
            ))
            ->getQuery();
        $resetToken->execute();

        return $token;
    }

    /**
     * Sends email message with password reset token
     *
     * @param  string User email
     * @param  string Token
     * @param string $token
     *
     * @return void
     */
    public function sendToken($email, $token)
    {
        $translator = $this->container->get('translator');
        $preferencesService = $this->container->get('system_preferences_service');

        $link = $this->container->get('router')->generate('newscoop_newscoop_passwordrecovery_checktoken', array(
            'token' => $token, 
            'email' => $email
        ), true);
        
        $from = $preferencesService->get('PasswordRecoveryFrom');
        if (empty($from)) {
            $from = 'no-reply@' . $this->getRequest()->getHost();
        }

        try {
            $message = \Swift_Message::newInstance()
                ->setSubject($translator->trans('Password recovery', array(), 'home'))
                ->setFrom($from)
                ->setTo($email)
                ->setBody(
                    $this->renderView(
                        'NewscoopNewscoopBundle:PasswordRecovery:email.txt.twig',
                        array('link' => urldecode($link))
                    )
                );

            $this->container->get('mailer')->send($message);
        } catch (\Exception $exception) {
            throw new \Exception("Error sending email.", 1); 
        }
    }

    /**
     * Set password
     *
     * @param  Newscoop\Entity\User $user     User
     * @param  string               $password New user password
     *
     * @return void
     */
    public function setPassword(User $user, $password)
    {
        $salt = $this->generateRandomString();
        $password = implode('$', array(
            'sha1',
            $salt,
            hash('sha1', $salt . $password),
        ));

        $em = $this->container->get('em');   
        $queryBuilder = $em->createQueryBuilder();
        $resetToken = $queryBuilder->update('Newscoop\Entity\User', 'u')
            ->set('u.password', ':password')
            ->set('u.resetToken', ':resetToken')
            ->where('u = :user')
            ->setParameters(array(
                'password' => $password,
                'resetToken' => null,
                'user' => $user
            ))
            ->getQuery();
        $resetToken->execute();
    }

    /**
     * Get random string
     *
     * @param  int    $length       String length
     * @param  string $allowedChars Chars allowed
     *
     * @return string
     */
    public function generateRandomString($length = 12, $allowedChars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
    {
        $return = '';
        for ($i = 0; $i < $length; $i++) {
            $return .= $allowedChars[mt_rand(0, strlen($allowedChars) - 1)];
        }

        return $return;
    }
}