<?php
/**
 * @package Newscoop\GimmeBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class OauthController extends Controller
{
    public function loginAction(Request $request)
    {
        $session = $request->getSession();
        $templatesService = $this->get('newscoop.templates.service');

        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        } elseif (null !== $session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
            $session->remove(SecurityContext::AUTHENTICATION_ERROR);
        } else {
            $error = false;
        }

        if ($error) {
            $error = $error->getMessage();
        }

        $lastUsername = (null === $session) ? '' : $session->get(SecurityContext::LAST_USERNAME);

        $smarty = $templatesService->getSmarty();
        $smarty->assign('lastUsername', $lastUsername);
        $smarty->assign('error', $error);
        $smarty->assign('targetPath', $request->getSession()->get('_security.oauth_authorize.target_path'));

        return new Response($templatesService->fetchTemplate('oauth_login.tpl'));
    }
}
