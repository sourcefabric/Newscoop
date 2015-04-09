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
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;

class OauthController extends Controller
{
    /**
     * Login user with oauth v2
     *
     * Find out more informations about Newscoop REST API authentiocation here: [click me][1]
     * 
     * [1]: http://docs.sourcefabric.org/projects/newscoop-restful-api/en/master/tutorial.html
     * @ApiDoc(
     *     statusCodes={
     *         200="Returned when successful"
     *     },
     *     parameters={
     *         {"name"="client_id", "dataType"="integer", "required"=true, "description"="Your client id, for example 9_1irxa0qcy3ms48c8c8wsgcgsc04k0s0w0g0sg4cco4kocoowoo"},
     *         {"name"="redirect_uri", "dataType"="string", "required"=true, "description"="The uri of your client web application, for example http://myapp.example.com/. This must match the URI you added in the Newscoop Admin Interface above. Remember to encode the URI."},
     *         {"name"="response_type", "dataType"="string", "required"=true, "description"="Value must be: 'token'"},
     *     }
     * )
     */
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

        return new Response($templatesService->fetchTemplate('oauth_login.tpl'), 200, array('Content-Type' => 'text/html'));
    }

    /**
     * @Route("/oauth/authentication/result", defaults={"_format"="json"}, options={"expose"=true}, name="oauth_authentication_result")
     * @Method("GET")
     */
    public function defaultOauthRedirectAction(Request $request)
    {
        $templatesService = $this->get('newscoop.templates.service');
        $smarty = $templatesService->getSmarty();

        return new Response($templatesService->fetchTemplate('oauth_result.tpl'), 200, array('Content-Type' => 'text/html'));
    }
}
