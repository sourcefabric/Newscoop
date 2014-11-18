<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\GimmeBundle\Form\Handler;

use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\Request;
use Newscoop\GimmeBundle\Form\Model\Authorize;
use Symfony\Component\Security\Core\SecurityContextInterface;
use OAuth2\OAuth2;
use OAuth2\OAuth2ServerException;
use FOS\OAuthServerBundle\Form\Handler\AuthorizeFormHandler as BaseAuthorizeFormHandler;

class AuthorizeFormHandler extends BaseAuthorizeFormHandler
{
    protected $request;
    protected $form;
    protected $context;
    protected $oauth2;

    public function __construct(Form $form, Request $request, SecurityContextInterface $context, OAuth2 $oauth2)
    {
        parent::__construct($form, $request);
        $this->context = $context;
        $this->oauth2 = $oauth2;
    }

    public function isAccepted()
    {
        return $this->form->getData()->getAllowAccess();
    }

    public function isRejected()
    {
        return !$this->form->getData()->getAllowAccess();
    }

    public function process()
    {
        $this->form->setData(new Authorize(
            $this->request->request->has('accepted'),
            $this->request->query->all()
        ));

        if ($this->request->getMethod() == 'POST') {
            $this->form->handleRequest($this->request);
            if ($this->form->isValid()) {
                try {
                    $user = $this->context->getToken()->getUser();

                    return $this->oauth2->finishClientAuthorization(true, $user, $this->request, null);
                } catch (OAuth2ServerException $e) {
                    return $e->getHttpResponse();
                }
            }
        }

        return false;
    }
}
