<?php
/**
 * @package Newscoop\NewscoopBundle
 * @author Paweł Mikołajczuk <pawel.mikolajczuk@sourcefabric.org>
 * @copyright 2013 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\NewscoopBundle\EventListener;

use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpFoundation\Cookie;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Doctrine\ORM\EntityManager;
use Newscoop\Services\CacheService;

/**
 * Save locale cookie.
 */
class LocaleListener
{
    protected $cacheService;

    protected $em;

    protected $translator;

    public function __construct(CacheService $cacheService, EntityManager $em, $translator)
    {
        $this->cacheService = $cacheService;
        $this->em = $em;
        $this->translator = $translator;
    }

    public function onResponse(FilterResponseEvent $event)
    {
        if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
            return;
        }

        $request = $event->getRequest();
        if ($request->request->has('login_language')) {
            $response = $event->getResponse();
            $response->headers->setCookie(new Cookie('TOL_Language', $request->request->get('login_language'), 2147483647, '/admin'));
            $event->setResponse($response);
        }
    }

    public function onRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        $isAdmin = (strpos($request->server->get('REQUEST_URI'), '/admin') !== false);
        $cookies = $request->cookies;
        $session = $request->getSession();
        $languageCode = null;

        // Backend cookie
        if ($isAdmin) {
            if ($cookies->has('TOL_Language')) {
                $languageCode = $cookies->get("TOL_Language");
            }

        } else {
            // Session language
            if ($session && $session->has('languageCode')) {
                $languageCode = $session->get('languageCode');
            }

            // Allow for overriding locale with get parameter
            if ($request->query->has('__set_lang')) {

                try {
                    $language = $this->em->getRepository('Newscoop\Entity\Language')
                        ->findByRFC3066bis($request->query->get('__set_lang'), true);

                    if ($language) {
                        $languageCode = $language->getCode();
                    }
                } catch (\Exception $e) {}
            }

            // Try to get locale from issue
            $issueMetadata = $request->attributes->get('_newscoop_issue_metadata');
            $issueLanguageCode = $issueMetadata['code_default_language'];
            if ($issueLanguageCode) {
                $languageCode = $issueLanguageCode;
            }

            // Determine language from URL
            $locale = $this->extractLocaleFromUri($request->getRequestUri());
            if ($locale) {
                $language = $this->em->getRepository('Newscoop\Entity\Language')
                        ->findOneByCode($locale);
                if ($language) {
                    $languageCode = $language->getCode();
                }
            }

            // Last fallback is to use default publication language
            if (is_null($languageCode)) {
                $publicationMetadata = $request->attributes->get('_newscoop_publication_metadata');
                $languageCode = $publicationMetadata['publication']['code_default_language'];
            }

            if ($session) {
                $session->set('languageCode', $languageCode);
            }
        }

        if (!is_null($languageCode)) {
            $request->setLocale($languageCode);
            $this->translator->setLocale($languageCode);
        }
    }

    private function extractLocaleFromUri($requestUri)
    {
        if ($requestUri !== "/") {
            $requestUri = str_replace("?", "", $requestUri);
            $extractedUri = array_filter(explode("/", $requestUri));
            if (isset($extractedUri[1])) {
                return $extractedUri[1];
            }
        }
    }
}
